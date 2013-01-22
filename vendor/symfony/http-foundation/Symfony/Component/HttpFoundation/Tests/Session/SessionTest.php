<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests\Session;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * SessionTest
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Robert Schönthal <seroscho@googlemail.com>
 * @author Drak <drak@zikula.org>
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface
     */
    protected $storage;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    protected function setUp()
    {
        $this->storage = new MockArraySessionStorage();
        $this->session = new Session($this->storage, new AttributeBag(), new FlashBag());
    }

    protected function tearDown()
    {
        $this->storage = null;
        $this->session = null;
    }

    public function testStart()
    {
        $this->assertEquals('', $this->session->getId());
        $this->assertTrue($this->session->start());
        $this->assertNotEquals('', $this->session->getId());
    }

    public function testGet()
    {
        // tests defaults
        $this->assertNull($this->session->get('foo'));
        $this->assertEquals(1, $this->session->get('foo', 1));
    }

    /**
     * @dataProvider setProvider
     */
    public function testSet($key, $value)
    {
        $this->session->set($key, $value);
        $this->assertEquals($value, $this->session->get($key));
    }

    /**
     * @dataProvider setProvider
     */
    public function testHas($key, $value)
    {
        $this->session->set($key, $value);
        $this->assertTrue($this->session->has($key));
        $this->assertFalse($this->session->has($key.'non_value'));
    }

    public function testReplace()
    {
        $this->session->replace(array('happiness' => 'be good', 'symfony' => 'awesome'));
        $this->assertEquals(array('happiness' => 'be good', 'symfony' => 'awesome'), $this->session->all());
        $this->session->replace(array());
        $this->assertEquals(array(), $this->session->all());
    }

    /**
     * @dataProvider setProvider
     */
    public function testAll($key, $value, $result)
    {
        $this->session->set($key, $value);
        $this->assertEquals($result, $this->session->all());
    }

    /**
     * @dataProvider setProvider
     */
    public function testClear($key, $value)
    {
        $this->session->set('hi', 'fabien');
        $this->session->set($key, $value);
        $this->session->clear();
        $this->assertEquals(array(), $this->session->all());
    }

    public function setProvider()
    {
        return array(
            array('foo', 'bar', array('foo' => 'bar')),
            array('foo.bar', 'too much beer', array('foo.bar' => 'too much beer')),
            array('great', 'symfony2 is great', array('great' => 'symfony2 is great')),
        );
    }

    /**
     * @dataProvider setProvider
     */
    public function testRemove($key, $value)
    {
        $this->session->set('hi.world', 'have a nice day');
        $this->session->set($key, $value);
        $this->session->remove($key);
        $this->assertEquals(array('hi.world' => 'have a nice day'), $this->session->all());
    }

    public function testInvalidate()
    {
        $this->session->set('invalidate', 123);
        $this->session->invalidate();
        $this->assertEquals(array(), $this->session->all());
    }

    public function testMigrate()
    {
        $this->session->set('migrate', 321);
        $this->session->migrate();
        $this->assertEquals(321, $this->session->get('migrate'));
    }

    public function testMigrateDestroy()
    {
        $this->session->set('migrate', 333);
        $this->session->migrate(true);
        $this->assertEquals(333, $this->session->get('migrate'));
    }

    public function testSave()
    {
        $this->session->start();
        $this->session->save();
    }

    public function testGetId()
    {
        $this->assertEquals('', $this->session->getId());
        $this->session->start();
        $this->assertNotEquals('', $this->session->getId());
    }

    public function testGetFlashBag()
    {
        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\Session\\Flash\\FlashBagInterface', $this->session->getFlashBag());
    }

    // deprecated since 2.1, will be removed from 2.3

    public function testGetSetFlashes()
    {
        $array = array('notice' => 'hello', 'error' => 'none');
        $this->assertEquals(array(), $this->session->getFlashes());
        $this->session->setFlashes($array);
        $this->assertEquals($array, $this->session->getFlashes());
        $this->assertEquals(array(), $this->session->getFlashes());
        $this->session->getFlashBag()->add('notice', 'foo');

        // test that BC works by only retrieving the first added.
        $this->session->getFlashBag()->add('notice', 'foo2');
        $this->assertEquals(array('notice' => 'foo'), $this->session->getFlashes());
    }

    public function testGetFlashesWithArray()
    {
        $array = array('notice' => 'hello', 'error' => 'none');
        $this->assertEquals(array(), $this->session->getFlashes());
        $this->session->setFlash('foo', $array);
        $this->assertEquals(array('foo' => $array), $this->session->getFlashes());
        $this->assertEquals(array(), $this->session->getFlashes());

        $array = array('hello', 'foo');
        $this->assertEquals(array(), $this->session->getFlashes());
        $this->session->setFlash('foo', $array);
        $this->assertEquals(array('foo' => 'hello'), $this->session->getFlashes());
        $this->assertEquals(array(), $this->session->getFlashes());
    }

    public function testGetSetFlash()
    {
        $this->assertNull($this->session->getFlash('notice'));
        $this->assertEquals('default', $this->session->getFlash('notice', 'default'));
        $this->session->getFlashBag()->add('notice', 'foo');
        $this->session->getFlashBag()->add('notice', 'foo2');

        // test that BC works by only retrieving the first added.
        $this->assertEquals('foo', $this->session->getFlash('notice'));
        $this->assertNull($this->session->getFlash('notice'));
    }

    public function testHasFlash()
    {
        $this->assertFalse($this->session->hasFlash('notice'));
        $this->session->setFlash('notice', 'foo');
        $this->assertTrue($this->session->hasFlash('notice'));
    }

    public function testRemoveFlash()
    {
        $this->session->setFlash('notice', 'foo');
        $this->session->setFlash('error', 'bar');
        $this->assertTrue($this->session->hasFlash('notice'));
        $this->session->removeFlash('error');
        $this->assertTrue($this->session->hasFlash('notice'));
        $this->assertFalse($this->session->hasFlash('error'));
    }

    public function testClearFlashes()
    {
        $this->assertFalse($this->session->hasFlash('notice'));
        $this->assertFalse($this->session->hasFlash('error'));
        $this->session->setFlash('notice', 'foo');
        $this->session->setFlash('error', 'bar');
        $this->assertTrue($this->session->hasFlash('notice'));
        $this->assertTrue($this->session->hasFlash('error'));
        $this->session->clearFlashes();
        $this->assertFalse($this->session->hasFlash('notice'));
        $this->assertFalse($this->session->hasFlash('error'));
    }

    /**
     * @covers Symfony\Component\HttpFoundation\Session\Session::getIterator
     */
    public function testGetIterator()
    {
        $attributes = array('hello' => 'world', 'symfony2' => 'rocks');
        foreach ($attributes as $key => $val) {
            $this->session->set($key, $val);
        }

        $i = 0;
        foreach ($this->session as $key => $val) {
            $this->assertEquals($attributes[$key], $val);
            $i++;
        }

        $this->assertEquals(count($attributes), $i);
    }

    /**
     * @covers \Symfony\Component\HttpFoundation\Session\Session::count
     */
    public function testGetCount()
    {
        $this->session->set('hello', 'world');
        $this->session->set('symfony2', 'rocks');

        $this->assertEquals(2, count($this->session));
    }

    public function testGetMeta()
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\MetadataBag', $this->session->getMetadataBag());
    }
}
