<?php

/**
 * Generated by PHPUnit_SkeletonGenerator on 2013-01-07 at 09:32:19.
 */
class UserManagerTest extends BaseWebTestCase
{

    /**
     * @var UserManager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
        $this->obj = $this->app['usermanager'];
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {

    }

    /**
     * @return array
     */
    public function customUser()
    {
        $unique_id = uniqid();
        return array(
            array('julio', 'montoya', 5, 'julio@example.com', 'jmontoya1_'.$unique_id, 'jmontoya1_'.$unique_id),
            array('julio', 'montoya', 5, 'julio@example.com', 'jmontoya2_'.$unique_id, 'jmontoya2_'.$unique_id)
        );
    }

    /**
     *
     * @covers  UserManager::create_user
     * @depends testIsUsernameAvailableFlow
     */
    public function testCreateUser()
    {
        $user_id = UserManager::create_user('julio', 'montoya', 5, 'julio@example.com', 'jmontoya666', 'jmontoya666');
        $this->assertGreaterThan(
            1,
            $user_id
        );
        return $user_id;
    }

    /**
     * @dataProvider customUser
     * @covers       UserManager::create_user
     */
    public function testCreateUser2($firstname, $lastname, $status, $email, $username, $password)
    {

        $this->assertGreaterThan(
            1,
            $this->obj->create_user($firstname, $lastname, $status, $email, $username, $password)
        );
        exit;
    }

    /**
     *
     */
    public function testIsUsernameAvailableFlow()
    {
        $this->assertSame(
            true,
            UserManager::is_username_available('jmontoya666')
        );
    }

    /**
     * Test multiple URL case with users hosting limit
     */
    public function testCreateUser3()
    {
        $this->app['configuration'][1]['hosting_limit_users'] = 50;
        $this->assertSame(
            false,
            UserManager::create_user('Merry', 'Brandybuck', null, null, 'jo', 'jo')
        );
    }

    /**
     * Test teachers hosting limit
     */
    public function testCreateUser4()
    {
        $this->app['configuration'][1]['hosting_limit_teachers'] = 50;
        $this->assertSame(
            false,
            UserManager::create_user('Pippin', 'Took', 1, null, 'jo', 'jo')
        );
    }

    /**
     * Test language non-existence
     */
    public function testCreateUser5()
    {
        $this->assertSame(
            false,
            UserManager::create_user('Pippin', 'Tooky', null, null, 'jo', 'jo', null, 'spaniard')
        );
    }

    /**
     *
     * @covers UserManager::is_username_available
     */
    public function testIsUsernameAvailable()
    {
        $this->assertSame(
            false,
            UserManager::is_username_available('')
        );

        $unique_id = uniqid();
        $this->assertSame(
            true,
            UserManager::is_username_available($unique_id)
        );
    }


    /**
     * @covers  UserManager::get_user_info_by_id
     * @depends testCreateUser
     */
    public function testGet_user_info_by_id($user_id)
    {
        $user_info = UserManager::get_user_info_by_id($user_id);
        $this->assertGreaterThan(1, $user_info['user_id']);
        return $user_info;
    }




    /**
     * @covers  UserManager::get_user_info
     * @depends testGet_user_info_by_id
     */
    public function testGet_user_info($user_info)
    {
        $user_info = UserManager::get_user_info($user_info['username']);
        $this->assertGreaterThan(
            1,
            $user_info['user_id']
        );
    }

    /**
     * @covers UserManager::delete_user
     */
    public function testDelete_user()
    {
        $this->assertSame(
            false,
            UserManager::delete_user(null)
        );

        $this->assertSame(
            false,
            UserManager::delete_user('abc')
        );

        $this->assertSame(
            false,
            UserManager::delete_users(-1)
        );

        $this->assertSame(
            false,
            UserManager::delete_users(array(-1))
        );

        $this->assertSame(
            false,
            UserManager::delete_users(null)
        );
    }

    /**
     * @depends testCreateUser
     */
    public function testDelete_user2()
    {
        $user_info = UserManager::get_user_info('jmontoya666');
        $this->assertSame(
            false,
            UserManager::delete_user($user_info['user_id'])
        );
    }

    /**
     * Generated from @assert (null) === false.
     *
     * @covers UserManager::delete_users
     */
    public function testDelete_users()
    {
        $this->assertSame(
            false,
            UserManager::delete_users(null)
        );
    }

    /**
     * Generated from @assert (-1) === false.
     *
     * @covers UserManager::delete_users
     */
    public function testDelete_users2()
    {
        $this->assertSame(
            false,
            UserManager::delete_users(-1)
        );
    }

    /**
     * Generated from @assert (array(-1)) === false.
     *
     * @covers UserManager::delete_users
     */
    public function testDelete_users3()
    {
        $this->assertSame(
            false,
            UserManager::delete_users(array(-1))
        );
    }


    /**
     * Generated from @assert (null) === false.
     *
     * @covers UserManager::deactivate_users
     */
    public function testDeactivate_users()
    {
        $this->assertSame(
            false,
            UserManager::deactivate_users(null)
        );
    }

    /**
     * Generated from @assert (array(-1)) === false.
     *
     * @covers UserManager::deactivate_users
     */
    public function testDeactivate_users2()
    {
        $this->assertSame(
            false,
            UserManager::deactivate_users(array(-1))
        );
    }

    /**
     * Generated from @assert (null) === false.
     *
     * @covers UserManager::activate_users
     */
    public function testActivate_users()
    {
        $this->assertSame(
            false,
            UserManager::activate_users(null)
        );
    }

    /**
     * Generated from @assert (array(-1)) === false.
     *
     * @covers UserManager::activate_users
     */
    public function testActivate_users2()
    {
        $this->assertSame(
            false,
            UserManager::activate_users(array(-1))
        );
    }

    /**
     * Generated from @assert (false,'') === false.
     *
     * @covers UserManager::update_openid
     */
    public function testUpdate_openid()
    {
        $this->assertSame(
            false,
            UserManager::update_openid(false, '')
        );
    }

    /**
     * Generated from @assert (-1,'') === false.
     *
     * @covers UserManager::update_openid
     */
    public function testUpdate_openid2()
    {
        $this->assertSame(
            false,
            UserManager::update_openid(-1, '')
        );
    }

    /**
     * Generated from @assert (false, false, false, false, false, false, false, false, false, false, false, false, false) === false.
     *
     * @covers UserManager::update_user
     */
    public function testUpdate_user()
    {
        var_dump($this->app);exit;
        $this->assertSame(
            false,
            UserManager::update_user(
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false
            )
        );
    }

    /**
     * Generated from @assert (0) === false.
     *
     * @covers UserManager::disable
     */
    public function testDisable()
    {
        $this->assertSame(
            false,
            UserManager::disable(0)
        );
    }

    /**
     * Generated from @assert (0) === false.
     *
     * @covers UserManager::enable
     */
    public function testEnable()
    {
        $this->assertSame(
            false,
            UserManager::enable(0)
        );
    }

    /**
     * Generated from @assert ('0','---') === 0.
     *
     * @covers UserManager::get_user_id_from_original_id
     */
    public function testGet_user_id_from_original_id()
    {
        $this->assertSame(
            0,
            UserManager::get_user_id_from_original_id('0', '---')
        );
    }

    /**
     * Generated from @assert ('') === false.
     *
     * @covers UserManager::is_username_available
     */
    public function testIs_username_available()
    {
        $this->assertSame(
            false,
            UserManager::is_username_available('')
        );
    }

    /**
     * Generated from @assert ('xyzxyzxyz') === true.
     *
     * @covers UserManager::is_username_available
     */
    public function testIs_username_available2()
    {
        $this->assertSame(
            true,
            UserManager::is_username_available('xyzxyzxyz')
        );
    }

    /**
     * Generated from @assert ('','') === false.
     *
     * @covers UserManager::create_username
     */
    public function testCreateUsername()
    {
        $this->assertSame(
            false,
            UserManager::create_username('', '')
        );
    }

    /**
     * Generated from @assert ('a','b') === 'ab'.
     *
     * @covers UserManager::create_username
     */
    public function testCreateUsername2()
    {
        $this->assertSame(
            'ab',
            UserManager::create_username('a', 'b')
        );
    }

    /**
     * @covers UserManager::create_unique_username
     * @todo   Implement testCreate_unique_username().
     */
    public function testCreate_unique_username()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::purify_username
     * @todo   Implement testPurify_username().
     */
    public function testPurify_username()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::is_user_id_valid
     * @todo   Implement testIs_user_id_valid().
     */
    public function testIs_user_id_valid()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::is_username_valid
     * @todo   Implement testIs_username_valid().
     */
    public function testIs_username_valid()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::is_username_empty
     * @todo   Implement testIs_username_empty().
     */
    public function testIs_username_empty()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::is_username_too_long
     * @todo   Implement testIs_username_too_long().
     */
    public function testIs_username_too_long()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_user_list_by_ids
     * @todo   Implement testGet_user_list_by_ids().
     */
    public function testGet_user_list_by_ids()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_user_list
     * @todo   Implement testGet_user_list().
     */
    public function testGet_user_list()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_user_list_like
     * @todo   Implement testGet_user_list_like().
     */
    public function testGet_user_list_like()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_teacher_list
     * @todo   Implement testGet_teacher_list().
     */
    public function testGet_teacher_list()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_user_picture_path_by_id
     * @todo   Implement testGet_user_picture_path_by_id().
     */
    public function testGet_user_picture_path_by_id()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::update_user_picture
     * @todo   Implement testUpdate_user_picture().
     */
    public function testUpdate_user_picture()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::delete_user_picture
     * @todo   Implement testDelete_user_picture().
     */
    public function testDelete_user_picture()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::build_production_list
     * @todo   Implement testBuild_production_list().
     */
    public function testBuild_production_list()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_user_productions
     * @todo   Implement testGet_user_productions().
     */
    public function testGet_user_productions()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::remove_user_production
     * @todo   Implement testRemove_user_production().
     */
    public function testRemove_user_production()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::update_extra_field
     * @todo   Implement testUpdate_extra_field().
     */
    public function testUpdate_extra_field()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::update_extra_field_value
     * @todo   Implement testUpdate_extra_field_value().
     */
    public function testUpdate_extra_field_value()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_extra_fields
     * @todo   Implement testGet_extra_fields().
     */
    public function testGet_extra_fields()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_extra_field_options
     * @todo   Implement testGet_extra_field_options().
     */
    public function testGet_extra_field_options()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_number_of_extra_fields
     * @todo   Implement testGet_number_of_extra_fields().
     */
    public function testGet_number_of_extra_fields()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::create_extra_field
     * @todo   Implement testCreate_extra_field().
     */
    public function testCreate_extra_field()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::save_extra_field_changes
     * @todo   Implement testSave_extra_field_changes().
     */
    public function testSave_extra_field_changes()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::is_extra_field_available
     * @todo   Implement testIs_extra_field_available().
     */
    public function testIs_extra_field_available()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_extra_user_data
     * @todo   Implement testGet_extra_user_data().
     */
    public function testGet_extra_user_data()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_extra_user_data_by_field
     * @todo   Implement testGet_extra_user_data_by_field().
     */
    public function testGet_extra_user_data_by_field()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_extra_field_information_by_name
     * @todo   Implement testGet_extra_field_information_by_name().
     */
    public function testGet_extra_field_information_by_name()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_all_extra_field_by_type
     * @todo   Implement testGet_all_extra_field_by_type().
     */
    public function testGet_all_extra_field_by_type()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_extra_field_information
     * @todo   Implement testGet_extra_field_information().
     */
    public function testGet_extra_field_information()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_extra_user_data_by_value
     * @todo   Implement testGet_extra_user_data_by_value().
     */
    public function testGet_extra_user_data_by_value()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_extra_user_data_by_field_variable
     * @todo   Implement testGet_extra_user_data_by_field_variable().
     */
    public function testGet_extra_user_data_by_field_variable()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_sessions_by_category
     * @todo   Implement testGet_sessions_by_category().
     */
    public function testGet_sessions_by_category()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_personal_session_course_list
     * @todo   Implement testGet_personal_session_course_list().
     */
    public function testGet_personal_session_course_list()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_courses_list_by_session
     * @todo   Implement testGet_courses_list_by_session().
     */
    public function testGet_courses_list_by_session()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_user_id_from_username
     * @todo   Implement testGet_user_id_from_username().
     */
    public function testGet_user_id_from_username()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_user_upload_files_by_course
     * @todo   Implement testGet_user_upload_files_by_course().
     */
    public function testGet_user_upload_files_by_course()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_api_keys
     * @todo   Implement testGet_api_keys().
     */
    public function testGet_api_keys()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::add_api_key
     * @todo   Implement testAdd_api_key().
     */
    public function testAdd_api_key()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::delete_api_key
     * @todo   Implement testDelete_api_key().
     */
    public function testDelete_api_key()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::update_api_key
     * @todo   Implement testUpdate_api_key().
     */
    public function testUpdate_api_key()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_api_key_id
     * @todo   Implement testGet_api_key_id().
     */
    public function testGet_api_key_id()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::is_admin
     * @todo   Implement testIs_admin().
     */
    public function testIs_admin()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_number_of_users
     * @todo   Implement testGet_number_of_users().
     */
    public function testGet_number_of_users()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::resize_picture
     * @todo   Implement testResize_picture().
     */
    public function testResize_picture()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_picture_user
     * @todo   Implement testGet_picture_user().
     */
    public function testGet_picture_user()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::send_message_in_outbox
     * @todo   Implement testSend_message_in_outbox().
     */
    public function testSend_message_in_outbox()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_tags
     * @todo   Implement testGet_tags().
     */
    public function testGet_tags()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_top_tags
     * @todo   Implement testGet_top_tags().
     */
    public function testGet_top_tags()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_user_tags
     * @todo   Implement testGet_user_tags().
     */
    public function testGet_user_tags()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_user_tags_to_string
     * @todo   Implement testGet_user_tags_to_string().
     */
    public function testGet_user_tags_to_string()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_tag_id
     * @todo   Implement testGet_tag_id().
     */
    public function testGet_tag_id()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_tag_id_from_id
     * @todo   Implement testGet_tag_id_from_id().
     */
    public function testGet_tag_id_from_id()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::add_tag
     * @todo   Implement testAdd_tag().
     */
    public function testAdd_tag()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::delete_user_tags
     * @todo   Implement testDelete_user_tags().
     */
    public function testDelete_user_tags()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::process_tags
     * @todo   Implement testProcess_tags().
     */
    public function testProcess_tags()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_all_administrators
     * @todo   Implement testGet_all_administrators().
     */
    public function testGet_all_administrators()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_all_user_tags
     * @todo   Implement testGet_all_user_tags().
     */
    public function testGet_all_user_tags()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_search_form
     * @todo   Implement testGet_search_form().
     */
    public function testGet_search_form()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::show_menu
     * @todo   Implement testShow_menu().
     */
    public function testShow_menu()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_special_course_list
     * @todo   Implement testGet_special_course_list().
     */
    public function testGet_special_course_list()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::relate_users
     * @todo   Implement testRelate_users().
     */
    public function testRelate_users()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::remove_user_rel_user
     * @todo   Implement testRemove_user_rel_user().
     */
    public function testRemove_user_rel_user()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_users_followed_by_drh
     * @todo   Implement testGet_users_followed_by_drh().
     */
    public function testGet_users_followed_by_drh()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::suscribe_users_to_hr_manager
     * @todo   Implement testSuscribe_users_to_hr_manager().
     */
    public function testSuscribe_users_to_hr_manager()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::is_user_followed_by_drh
     * @todo   Implement testIs_user_followed_by_drh().
     */
    public function testIs_user_followed_by_drh()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_user_id_of_course_admin_or_session_admin
     * @todo   Implement testGet_user_id_of_course_admin_or_session_admin().
     */
    /*
      public function testGet_user_id_of_course_admin_or_session_admin()
      {
      // Remove the following lines when you implement this test.
      $this->markTestIncomplete(
      'This test has not been implemented yet.'
      );
      }
     */

    /**
     * @covers UserManager::is_user_certified
     * @todo   Implement testIs_user_certified().
     */
    public function testIs_user_certified()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_info_gradebook_certificate
     * @todo   Implement testGet_info_gradebook_certificate().
     */
    public function testGet_info_gradebook_certificate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_user_path_certificate
     * @todo   Implement testGet_user_path_certificate().
     */
    public function testGet_user_path_certificate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::is_session_course_coach
     * @todo   Implement testIs_session_course_coach().
     */
    public function testIs_session_course_coach()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_favicon_from_url
     * @todo   Implement testGet_favicon_from_url().
     */
    public function testGet_favicon_from_url()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::delete_inactive_student
     * @todo   Implement testDelete_inactive_student().
     */
    public function testDelete_inactive_student()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::set_extra_fields_in_form
     * @todo   Implement testSet_extra_fields_in_form().
     */
    public function testSet_extra_fields_in_form()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::get_user_field_types
     * @todo   Implement testGet_user_field_types().
     */
    public function testGet_user_field_types()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::add_user_as_admin
     * @todo   Implement testAdd_user_as_admin().
     */
    public function testAdd_user_as_admin()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::remove_user_admin
     * @todo   Implement testRemove_user_admin().
     */
    public function testRemove_user_admin()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers UserManager::update_all_user_languages
     * @todo   Implement testUpdate_all_user_languages().
     */
    public function testUpdate_all_user_languages()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

}
