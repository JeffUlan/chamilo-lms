<?php
/**
 * Generated by PHPUnit_SkeletonGenerator on 2013-02-17 at 00:37:09.
 */
class AccessTokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AccessToken
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new AccessToken;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * Generated from @assert () !== null.
     *
     * @covers AccessToken::empty_token
     */
    public function testEmpty_token()
    {
        $this->assertNotSame(
          null,
          AccessToken::empty_token()
        );
    }

    /**
     * Generated from @assert ('') !== null.
     *
     * @covers AccessToken::parse
     */
    public function testParse()
    {
        $this->assertNotSame(
          null,
          AccessToken::parse('')
        );
    }

    /**
     * Generated from @assert (base64_encode('a/b')) !== null.
     *
     * @covers AccessToken::parse
     */
    public function testParse2()
    {
        $this->assertNotSame(
          null,
          AccessToken::parse(base64_encode('a/b'))
        );
    }

    /**
     * Generated from @assert (base64_encode('a/b/c')) !== null.
     *
     * @covers AccessToken::parse
     */
    public function testParse3()
    {
        $this->assertNotSame(
          null,
          AccessToken::parse(base64_encode('a/b/c'))
        );
    }

    /**
     * Generated from @assert (0, 0, 'a') !== null.
     *
     * @covers AccessToken::create
     */
    public function testCreate()
    {
        $this->assertNotSame(
          null,
          AccessToken::create(0, 0, 'a')
        );
    }

    /**
     * Generated from @assert () > 0.
     *
     * @covers AccessToken::get_id
     */
    public function testGet_id()
    {
        $this->assertGreaterThan(
          0,
          $this->object->get_id()
        );
    }

    /**
     * Generated from @assert () > 0.
     *
     * @covers AccessToken::get_user_id
     */
    public function testGet_user_id()
    {
        $this->assertGreaterThan(
          0,
          $this->object->get_user_id()
        );
    }

    /**
     * Generated from @assert () !== null.
     *
     * @covers AccessToken::get_key
     */
    public function testGet_key()
    {
        $this->assertNotSame(
          null,
          $this->object->get_key()
        );
    }

    /**
     * Generated from @assert () === true.
     *
     * @covers AccessToken::is_empty
     */
    public function testIs_empty()
    {
        $this->assertSame(
          true,
          $this->object->is_empty()
        );
    }

    /**
     * Generated from @assert () === false.
     *
     * @covers AccessToken::is_valid
     */
    public function testIs_valid()
    {
        $this->assertSame(
          false,
          $this->object->is_valid()
        );
    }

    /**
     * Generated from @assert () !== null.
     *
     * @covers AccessToken::__toString
     */
    public function test__toString()
    {
        $this->assertNotSame(
          null,
          $this->object->__toString()
        );
    }
}
