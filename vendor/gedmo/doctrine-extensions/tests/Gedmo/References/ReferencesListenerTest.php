<?php

namespace Gedmo\References;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver as MongoDBAnnotationDriver;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver as ORMAnnotationDriver;
use References\Fixture\ODM\MongoDB\Product;
use References\Fixture\ORM\StockItem;
use Tool\BaseTestCaseOM;

class ReferencesListenerTest extends BaseTestCaseOM
{
    private $em;
    private $dm;

    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Mongo')) {
            $this->markTestSkipped('Missing Mongo extension.');
        }

        $reader = new AnnotationReader();

        $this->dm = $this->getMockDocumentManager('test', new MongoDBAnnotationDriver($reader, __DIR__ . '/Fixture/ODM/MongoDB'));

        $listener = new ReferencesListener(array(
            'document' => $this->dm
        ));

        $this->evm->addEventSubscriber($listener);

        $reader = new AnnotationReader();

        $this->em = $this->getMockSqliteEntityManager(array('References\Fixture\ORM\StockItem'), new ORMAnnotationDriver($reader, __DIR__ . '/Fixture/ORM'));

        $listener->registerManager('entity', $this->em);
    }

    public function testShouldPersistReferencedIdentifiersIntoIdentifierField()
    {
        $stockItem = new StockItem();
        $stockItem->setName('Apple TV');
        $stockItem->setSku('APP-TV');
        $stockItem->setQuantity(25);

        $product = new Product();
        $product->setName('Apple TV');

        $this->dm->persist($product);
        $this->dm->flush();

        $stockItem->setProduct($product);

        $this->em->persist($stockItem);

        $this->assertEquals($product->getId(), $stockItem->getProductId());
    }

    public function testShouldPopulateReferenceOneWithProxyFromIdentifierField()
    {
        $product = new Product();
        $product->setName('Apple TV');

        $this->dm->persist($product);
        $this->dm->flush();

        $stockItem = new StockItem();
        $stockItem->setName('Apple TV');
        $stockItem->setSku('APP-TV');
        $stockItem->setQuantity(25);
        $stockItem->setProductId($product->getId());

        $this->em->persist($stockItem);
        $this->em->flush();
        $this->em->clear();

        $stockItem = $this->em->find(get_class($stockItem), $stockItem->getId());

        $this->assertSame($product, $stockItem->getProduct());
    }

    public function testShouldPopulateReferenceManyWithLazyCollectionInstance()
    {
        $product = new Product();
        $product->setName('Apple TV');

        $this->dm->persist($product);
        $this->dm->flush();
        $this->dm->clear();

        $stockItem = new StockItem();
        $stockItem->setName('Apple TV');
        $stockItem->setSku('APP-TV');
        $stockItem->setQuantity(25);
        $stockItem->setProductId($product->getId());

        $this->em->persist($stockItem);

        $stockItem = new StockItem();
        $stockItem->setName('Apple TV');
        $stockItem->setSku('AMZN-APP-TV');
        $stockItem->setQuantity(25);
        $stockItem->setProductId($product->getId());

        $this->em->persist($stockItem);
        $this->em->flush();

        $product = $this->dm->find(get_class($product), $product->getId());

        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $product->getStockItems());
        $this->assertEquals(2, $product->getStockItems()->count());

        $first = $product->getStockItems()->first();

        $this->assertInstanceOf(get_class($stockItem), $first);
        $this->assertEquals('APP-TV', $first->getSku());

        $last = $product->getStockItems()->last();

        $this->assertInstanceOf(get_class($stockItem), $last);
        $this->assertEquals('AMZN-APP-TV', $last->getSku());
    }
}
