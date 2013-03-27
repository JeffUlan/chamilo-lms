<?php
/**
 * Generated by PHPUnit_SkeletonGenerator on 2013-02-17 at 00:38:16.
 */
class AccessurleditcoursestourlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Accessurleditcoursestourl
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        ob_start();
        require_once dirname(__FILE__).'/../../../main/inc/lib/access_url_edit_courses_to_url_functions.lib.php';
        require_once dirname(__FILE__).'/../../../main/inc/lib/main_api.lib.php';
        $this->object = new Accessurleditcoursestourl;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        ob_end_clean();
    }

    /**
     * Generated from @assert () === false.
     *
     * @covers Accessurleditcoursestourl::search_courses
     */
    public function testSearch_courses()
    {
        $this->assertThat(
          false,
          $this->logicalNot(
            $this->equalTo($this->object->search_courses(null, null))
          )
        );
    }
}
