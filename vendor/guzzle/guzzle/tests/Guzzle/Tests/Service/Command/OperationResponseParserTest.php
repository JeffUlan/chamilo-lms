<?php

namespace Guzzle\Tests\Service\Command;

use Guzzle\Http\Message\Response;
use Guzzle\Service\Client;
use Guzzle\Service\Command\OperationResponseParser;
use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Description\Operation;
use Guzzle\Service\Description\ServiceDescription;
use Guzzle\Service\Command\LocationVisitor\Response\StatusCodeVisitor;
use Guzzle\Service\Command\LocationVisitor\Response\ReasonPhraseVisitor;
use Guzzle\Service\Command\LocationVisitor\Response\JsonVisitor;
use Guzzle\Service\Command\LocationVisitor\Response\BodyVisitor;
use Guzzle\Service\Command\LocationVisitor\VisitorFlyweight;

/**
 * @covers Guzzle\Service\Command\OperationResponseParser
 */
class OperationResponseParserTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testHasVisitors()
    {
        $p = new OperationResponseParser(new VisitorFlyweight(array()));
        $visitor = new BodyVisitor();
        $p->addVisitor('foo', $visitor);
        $this->assertSame($visitor, $this->readAttribute($p, 'factory')->getResponseVisitor('foo'));
    }

    public function testUsesParentParser()
    {
        $p = new OperationResponseParser(new VisitorFlyweight());
        $operation = new Operation();
        $operation->setServiceDescription(new ServiceDescription());
        $op = new OperationCommand(array(), $operation);
        $op->setResponseParser($p)->setClient(new Client());
        $op->prepare()->setResponse(new Response(200, array('Content-Type' => 'application/xml'), '<F><B>C</B></F>'), true);
        $this->assertInstanceOf('SimpleXMLElement', $op->execute());
    }

    public function testVisitsLocations()
    {
        $parser = new OperationResponseParser(new VisitorFlyweight(array()));
        $parser->addVisitor('statusCode', new StatusCodeVisitor());
        $parser->addVisitor('reasonPhrase', new ReasonPhraseVisitor());
        $parser->addVisitor('json', new JsonVisitor());
        $op = new OperationCommand(array(), $this->getDescription()->getOperation('test'));
        $op->setResponseParser($parser)->setClient(new Client());
        $op->prepare()->setResponse(new Response(201), true);
        $result = $op->execute();
        $this->assertEquals(201, $result['code']);
        $this->assertEquals('Created', $result['phrase']);
    }

    public function testVisitsLocationsForJsonResponse()
    {
        $parser = OperationResponseParser::getInstance();
        $operation = $this->getDescription()->getOperation('test');
        $op = new OperationCommand(array(), $operation);
        $op->setResponseParser($parser)->setClient(new Client());
        $op->prepare()->setResponse(new Response(200, array(
            'Content-Type' => 'application/json'
        ), '{"baz":"bar","enigma":"123"}'), true);
        $result = $op->execute();
        $this->assertEquals(array(
            'baz'    => 'bar',
            'enigma' => '123',
            'code'   => 200,
            'phrase' => 'OK'
        ), $result->toArray());
    }

    public function testSkipsUnkownModels()
    {
        $parser = OperationResponseParser::getInstance();
        $operation = $this->getDescription()->getOperation('test');
        $operation->setResponseClass('Baz')->setResponseType('model');
        $op = new OperationCommand(array(), $operation);
        $op->setResponseParser($parser)->setClient(new Client());
        $op->prepare()->setResponse(new Response(201), true);
        $this->assertInstanceOf('Guzzle\Http\Message\Response', $op->execute());
    }

    public function testAllowsModelProcessingToBeDisabled()
    {
        $parser = OperationResponseParser::getInstance();
        $operation = $this->getDescription()->getOperation('test');
        $op = new OperationCommand(array('command.response_processing' => 'native'), $operation);
        $op->setResponseParser($parser)->setClient(new Client());
        $op->prepare()->setResponse(new Response(200, array(
            'Content-Type' => 'application/json'
        ), '{"baz":"bar","enigma":"123"}'), true);
        $result = $op->execute();
        $this->assertInstanceOf('Guzzle\Service\Resource\Model', $result);
        $this->assertEquals(array(
            'baz'    => 'bar',
            'enigma' => '123'
        ), $result->toArray());
    }

    public function testDoesNotParseXmlWhenNotUsingXmlVisitor()
    {
        $parser = OperationResponseParser::getInstance();
        $description = ServiceDescription::factory(array(
            'operations' => array('test' => array('responseClass' => 'Foo')),
            'models' => array(
                'Foo' => array(
                    'type'       => 'object',
                    'properties' => array('baz' => array('location' => 'body'))
                )
            )
        ));
        $operation = $description->getOperation('test');
        $op = new OperationCommand(array(), $operation);
        $op->setResponseParser($parser)->setClient(new Client());
        $brokenXml = '<broken><><><<xml>>>>>';
        $op->prepare()->setResponse(new Response(200, array(
            'Content-Type' => 'application/xml'
        ), $brokenXml), true);
        $result = $op->execute();
        $this->assertEquals(array('baz'), $result->getKeys());
        $this->assertEquals($brokenXml, (string) $result['baz']);
    }

    public function testVisitsAdditionalProperties()
    {
        $parser = OperationResponseParser::getInstance();
        $description = ServiceDescription::factory(array(
            'operations' => array('test' => array('responseClass' => 'Foo')),
            'models' => array(
                'Foo' => array(
                    'type' => 'object',
                    'properties' => array(
                        'code' => array('location' => 'statusCode')
                    ),
                    'additionalProperties' => array(
                        'location' => 'json',
                        'type' => 'object',
                        'properties' => array(
                            'a' => array(
                                'type' => 'string',
                                'filters' => 'strtoupper'
                            )
                        )
                    )
                )
            )
        ));

        $operation = $description->getOperation('test');
        $op = new OperationCommand(array(), $operation);
        $op->setResponseParser($parser)->setClient(new Client());
        $json = '[{"a":"test"},{"a":"baz"}]';
        $op->prepare()->setResponse(new Response(200, array('Content-Type' => 'application/json'), $json), true);
        $result = $op->execute()->toArray();
        $this->assertEquals(array(
            'code' => 200,
            array('a' => 'TEST'),
            array('a' => 'BAZ')
        ), $result);
    }

    public function testCreatesCustomResponseClassInterface()
    {
        $parser = OperationResponseParser::getInstance();
        $description = ServiceDescription::factory(array(
            'operations' => array('test' => array('responseClass' => 'Guzzle\Tests\Mock\CustomResponseModel'))
        ));
        $operation = $description->getOperation('test');
        $op = new OperationCommand(array(), $operation);
        $op->setResponseParser($parser)->setClient(new Client());
        $op->prepare()->setResponse(new Response(200, array('Content-Type' => 'application/json'), 'hi!'), true);
        $result = $op->execute();
        $this->assertInstanceOf('Guzzle\Tests\Mock\CustomResponseModel', $result);
        $this->assertSame($op, $result->command);
    }

    /**
     * @expectedException \Guzzle\Service\Exception\ResponseClassException
     * @expectedExceptionMessage does not exist
     */
    public function testEnsuresResponseClassExists()
    {
        $parser = OperationResponseParser::getInstance();
        $description = ServiceDescription::factory(array(
            'operations' => array('test' => array('responseClass' => 'Foo\Baz\Bar'))
        ));
        $operation = $description->getOperation('test');
        $op = new OperationCommand(array(), $operation);
        $op->setResponseParser($parser)->setClient(new Client());
        $op->prepare()->setResponse(new Response(200, array('Content-Type' => 'application/json'), 'hi!'), true);
        $op->execute();
    }

    /**
     * @expectedException \Guzzle\Service\Exception\ResponseClassException
     * @expectedExceptionMessage must implement
     */
    public function testEnsuresResponseClassImplementsResponseClassInterface()
    {
        $parser = OperationResponseParser::getInstance();
        $description = ServiceDescription::factory(array(
            'operations' => array('test' => array('responseClass' => __CLASS__))
        ));
        $operation = $description->getOperation('test');
        $op = new OperationCommand(array(), $operation);
        $op->setResponseParser($parser)->setClient(new Client());
        $op->prepare()->setResponse(new Response(200, array('Content-Type' => 'application/json'), 'hi!'), true);
        $op->execute();
    }

    protected function getDescription()
    {
        return ServiceDescription::factory(array(
            'operations' => array('test' => array('responseClass' => 'Foo')),
            'models' => array(
                'Foo' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'baz'    => array('type' => 'string', 'location' => 'json'),
                        'code'   => array('location' => 'statusCode'),
                        'phrase' => array('location' => 'reasonPhrase'),
                    )
                )
            )
        ));
    }
}
