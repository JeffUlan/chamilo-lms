<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Test\Json;

use Seld\JsonLint\ParsingException;
use Composer\Json\JsonFile;

class JsonFileTest extends \PHPUnit_Framework_TestCase
{
    public function testParseErrorDetectExtraComma()
    {
        $json = '{
        "foo": "bar",
}';
        $this->expectParseException('Parse error on line 2', $json);
    }

    public function testParseErrorDetectExtraCommaInArray()
    {
        $json = '{
        "foo": [
            "bar",
        ]
}';
        $this->expectParseException('Parse error on line 3', $json);
    }

    public function testParseErrorDetectUnescapedBackslash()
    {
        $json = '{
        "fo\o": "bar"
}';
        $this->expectParseException('Parse error on line 1', $json);
    }

    public function testParseErrorSkipsEscapedBackslash()
    {
        $json = '{
        "fo\\\\o": "bar"
        "a": "b"
}';
        $this->expectParseException('Parse error on line 2', $json);
    }

    public function testParseErrorDetectSingleQuotes()
    {
        $json = '{
        \'foo\': "bar"
}';
        $this->expectParseException('Parse error on line 1', $json);
    }

    public function testParseErrorDetectMissingQuotes()
    {
        $json = '{
        foo: "bar"
}';
        $this->expectParseException('Parse error on line 1', $json);
    }

    public function testParseErrorDetectArrayAsHash()
    {
        $json = '{
        "foo": ["bar": "baz"]
}';
        $this->expectParseException('Parse error on line 2', $json);
    }

    public function testParseErrorDetectMissingComma()
    {
        $json = '{
        "foo": "bar"
        "bar": "foo"
}';
        $this->expectParseException('Parse error on line 2', $json);
    }

    public function testSchemaValidation()
    {
        $json = new JsonFile(__DIR__.'/../../../../composer.json');
        $this->assertTrue($json->validateSchema());
    }

    public function testParseErrorDetectMissingCommaMultiline()
    {
        $json = '{
        "foo": "barbar"

        "bar": "foo"
}';
        $this->expectParseException('Parse error on line 2', $json);
    }

    public function testParseErrorDetectMissingColon()
    {
        $json = '{
        "foo": "bar",
        "bar" "foo"
}';
        $this->expectParseException('Parse error on line 3', $json);
    }

    public function testSimpleJsonString()
    {
        $data = array('name' => 'composer/composer');
        $json = '{
    "name": "composer/composer"
}';
        $this->assertJsonFormat($json, $data);
    }

    public function testTrailingBackslash()
    {
        $data = array('Metadata\\' => 'src/');
        $json = '{
    "Metadata\\\\": "src/"
}';
        $this->assertJsonFormat($json, $data);
    }

    public function testFormatEmptyArray()
    {
        $data = array('test' => array(), 'test2' => new \stdClass);
        $json = '{
    "test": [

    ],
    "test2": {

    }
}';
        $this->assertJsonFormat($json, $data);
    }

    public function testEscape()
    {
        $data = array("Metadata\\\"" => 'src/');
        $json = '{
    "Metadata\\\\\\"": "src/"
}';

        $this->assertJsonFormat($json, $data);
    }

    public function testUnicode()
    {
        if (!function_exists('mb_convert_encoding') && version_compare(PHP_VERSION, '5.4', '<')) {
            $this->markTestSkipped('Test requires the mbstring extension');
        }

        $data = array("Žluťoučký \" kůň" => "úpěl ďábelské ódy za €");
        $json = '{
    "Žluťoučký \" kůň": "úpěl ďábelské ódy za €"
}';

        $this->assertJsonFormat($json, $data);
    }

    public function testOnlyUnicode()
    {
        if (!function_exists('mb_convert_encoding') && version_compare(PHP_VERSION, '5.4', '<')) {
            $this->markTestSkipped('Test requires the mbstring extension');
        }

        $data = "\\/ƌ";

        $this->assertJsonFormat('"\\\\\\/ƌ"', $data, JsonFile::JSON_UNESCAPED_UNICODE);
    }

    public function testEscapedSlashes()
    {
        $data = "\\/foo";

        $this->assertJsonFormat('"\\\\\\/foo"', $data, 0);
    }

    public function testEscapedBackslashes()
    {
        $data = "a\\b";

        $this->assertJsonFormat('"a\\\\b"', $data, 0);
    }

    public function testEscapedUnicode()
    {
        $data = "ƌ";

        $this->assertJsonFormat('"\\u018c"', $data, 0);
    }

    private function expectParseException($text, $json)
    {
        try {
            JsonFile::parseJson($json);
            $this->fail();
        } catch (ParsingException $e) {
            $this->assertContains($text, $e->getMessage());
        }
    }

    private function assertJsonFormat($json, $data, $options = null)
    {
        $file = new JsonFile('composer.json');

        if (null === $options) {
            $this->assertEquals($json, $file->encode($data));
        } else {
            $this->assertEquals($json, $file->encode($data, $options));
        }
    }

}
