<?php
/* For licensing terms, see /license.txt */
/**
 * Index page of the admin tools
 * @package chamilo.admin
 */
/**
 * Code
 */

// Language files that need to be included.
$language_file = array('admin', 'tracking','coursebackup');

// Resetting the course id.
$cidReset = true;

// Including some necessary chamilo files.
require_once '../inc/global.inc.php';
require_once api_get_path(SYS_CODE_PATH).'admin/statistics/statistics.lib.php';

// Setting the section (for the tabs).
$this_section = SECTION_PLATFORM_ADMIN;

// Access restrictions.
api_protect_admin_script(true);

$nameTools = get_lang('PlatformAdmin');

// Displaying the header
$message = '';

if (api_is_platform_admin()) {
    /* deprecated since 2014-10-30 (all main/install/ files were checked against direct execution)
    if (is_dir(api_get_path(SYS_CODE_PATH).'install/') && is_readable(api_get_path(SYS_CODE_PATH).'install/index.php')) {
        $message = Display::return_message(get_lang('InstallDirAccessibleSecurityThreat'),'warning');
    }
    */
    if (is_dir(api_get_path(SYS_ARCHIVE_PATH)) && !is_writable(api_get_path(SYS_ARCHIVE_PATH))) {
        $message = Display::return_message(get_lang('ArchivesDirectoryNotWriteableContactAdmin'), 'warning');
    }

    /* ACTION HANDLING */
    if (!empty($_POST['Register'])) {
        api_register_campus(!$_POST['donotlistcampus']);
        $message = Display :: return_message(get_lang('VersionCheckEnabled'), 'confirmation');
    }
    $keyword_url = Security::remove_XSS((empty($_GET['keyword']) ? '' : $_GET['keyword']));
}

if (isset($_GET['msg']) && isset($_GET['type'])) {
    if (in_array($_GET['msg'], array('ArchiveDirCleanupSucceeded', 'ArchiveDirCleanupFailed'))) {
        switch ($_GET['type']) {
            case 'error':
                $message = Display::return_message(get_lang($_GET['msg']), 'error');
                break;
            case 'confirmation':
                $message = Display::return_message(get_lang($_GET['msg']), 'confirm');
        }
    }
}

$blocks = array();

/* Users */

$blocks['users']['icon']  = Display::return_icon('members.gif', get_lang('Users'), array(), ICON_SIZE_SMALL, false);
$blocks['users']['label'] = api_ucfirst(get_lang('Users'));
$blocks['users']['class'] = 'block-admin-users';

if (api_is_platform_admin()) {
    $search_form = '
            <form method="get" class="form-search" action="user_list.php">
                <input class="span3" type="text" name="keyword" value="">
                <button class="btn" type="submit">'.get_lang('Search').'</button>
            </form>';
    $blocks['users']['search_form'] = $search_form;
    $items = array(
        array('url'=>'user_list.php', 	'label' => get_lang('UserList')),
        array('url'=>'user_add.php', 	'label' => get_lang('AddUsers')),
        array('url'=>'user_export.php', 'label' => get_lang('ExportUserListXMLCSV')),
        array('url'=>'user_import.php', 'label' => get_lang('ImportUserListXMLCSV')),
        array('url'=>'user_update_import.php', 'label' => get_lang('EditUserListCSV')),
    );

    if (api_get_setting('allow_social_tool') == 'true') {
        $items[] = array('url'=>'group_add.php', 	'label' => get_lang('AddGroups'));
        $items[] = array('url'=>'group_list.php', 	'label' => get_lang('GroupList'));
    }
    if (isset($extAuthSource) && isset($extAuthSource['ldap']) && count($extAuthSource['ldap']) > 0) {
        $items[] = array('url'=>'ldap_users_list.php', 	'label' => get_lang('ImportLDAPUsersIntoPlatform'));
    }
    $items[] = array('url'=>'user_fields.php', 	'label' => get_lang('ManageUserFields'));
} else {
    $items = array(
        array('url'=>'user_list.php', 	'label' => get_lang('UserList')),
        array('url'=>'user_add.php', 	'label' => get_lang('AddUsers')),
        array('url'=>'user_import.php', 'label' => get_lang('ImportUserListXMLCSV')),
    );
}

$items[] = array('url'=>'usergroups.php', 	'label' => get_lang('Classes'));

$blocks['users']['items'] = $items;
$blocks['users']['extra'] = null;

if (api_is_platform_admin()) {
    /* Courses */
    $blocks['courses']['icon']  = Display::return_icon('course.gif', get_lang('Courses'), array(), ICON_SIZE_MEDIUM, false);
    $blocks['courses']['label'] = api_ucfirst(get_lang('Courses'));
    $blocks['courses']['class'] = 'block-admin-courses';

    $search_form = ' <form method="get" class="form-search" action="course_list.php">
							<input class="span3" type="text" name="keyword" value="">
							<button class="btn" type="submit">'.get_lang('Search').'</button>
	            		</form>';
    $blocks['courses']['search_form'] = $search_form;

    $items = array();
    $items[] = array('url'=>'course_list.php', 	'label' => get_lang('CourseList'));
    $items[] = array('url'=>'course_add.php', 	'label' => get_lang('AddCourse'));

    if (api_get_setting('course_validation') == 'true') {

        $items[] = array('url'=>'course_request_review.php', 	'label' => get_lang('ReviewCourseRequests'));
        $items[] = array('url'=>'course_request_accepted.php', 	'label' => get_lang('AcceptedCourseRequests'));
        $items[] = array('url'=>'course_request_rejected.php', 	'label' => get_lang('RejectedCourseRequests'));
    }

    $items[] = array('url'=>'course_export.php', 			'label' => get_lang('ExportCourses'));
    $items[] = array('url'=>'course_import.php', 			'label' => get_lang('ImportCourses'));
    $items[] = array('url'=>'course_category.php', 			'label' => get_lang('AdminCategories'));
    $items[] = array('url'=>'subscribe_user2course.php', 	'label' => get_lang('AddUsersToACourse'));
    $items[] = array('url'=>'course_user_import.php', 		'label' => get_lang('ImportUsersToACourse'));
    //$items[] = array('url'=>'course_intro_pdf_import.php', 	'label' => get_lang('ImportPDFIntroToCourses'));

    if (api_get_setting('gradebook_enable_grade_model') == 'true') {
        $items[] = array('url'=>'grade_models.php', 'label' => get_lang('GradeModel'));
    }

    if (isset($extAuthSource) && isset($extAuthSource['ldap']) && count($extAuthSource['ldap']) > 0) {
        $items[] = array('url'=>'ldap_import_students.php', 'label' => get_lang('ImportLDAPUsersIntoCourse'));
    }

    $items[] = array('url'=>'course_fields.php', 	'label' => get_lang('ManageCourseFields'));

    $blocks['courses']['items'] = $items;
    $blocks['courses']['extra'] = null;

    /* Platform */
    $blocks['platform']['icon']  = Display::return_icon('platform.png', get_lang('Platform'), array(), ICON_SIZE_MEDIUM, false);
    $blocks['platform']['label'] = api_ucfirst(get_lang('Platform'));
    $blocks['platform']['class'] = 'block-admin-platform';


    $search_form = ' <form method="get" action="settings.php" class="form-search">
							<input class="span3" type="text" name="search_field" value="" >
                            <input type="hidden" value="search_setting" name="category">
							<button class="btn" type="submit">'.get_lang('Search').'</button>
	            		</form>';
	$blocks['platform']['search_form'] = $search_form;


    $items = array();
    $items[] = array('url'=>'settings.php', 				'label' => get_lang('PlatformConfigSettings'));
    $items[] = array('url'=>'settings.php?category=Plugins','label' => get_lang('Plugins'));
    $items[] = array('url'=>'settings.php?category=Regions','label' => get_lang('Regions'));
    $items[] = array('url'=>'system_announcements.php', 	'label' => get_lang('SystemAnnouncements'));
    $items[] = array('url'=> api_get_path(WEB_CODE_PATH).'calendar/agenda_js.php?type=admin', 'label' => get_lang('GlobalAgenda'));
    $items[] = array('url'=>'configure_homepage.php', 		'label' => get_lang('ConfigureHomePage'));
    $items[] = array('url'=>'configure_inscription.php', 	'label' => get_lang('ConfigureInscription'));
    $items[] = array('url'=>'statistics/index.php', 		'label' => get_lang('Statistics'));
    $items[] = array('url'=> api_get_path(WEB_CODE_PATH).'mySpace/company_reports.php',          'label' => get_lang('Reports'));

    /* Event settings */

    if (api_get_setting('activate_email_template') == 'true') {
        $items[] = array('url'=>'event_controller.php?action=listing', 		'label' => get_lang('EventMessageManagement'));
    }

    if (!empty($_configuration['multiple_access_urls'])) {
        if (api_is_global_platform_admin()) {
            $items[] = array('url'=>'access_urls.php', 	'label' => get_lang('ConfigureMultipleAccessURLs'));
        }
    }

    if (api_get_setting('allow_reservation') == 'true') {
        $items[] = array('url'=>'../reservation/m_category.php', 	'label' => get_lang('BookingSystem'));
    }
    if (api_get_setting('allow_terms_conditions') == 'true') {
        $items[] = array('url'=>'legal_add.php', 	'label' => get_lang('TermsAndConditions'));
    }
    $blocks['platform']['items'] = $items;
    $blocks['platform']['extra'] = null;
}

/* Sessions */
$blocks['sessions']['icon']  = Display::return_icon('session.png', get_lang('Sessions'), array(), ICON_SIZE_SMALL, false);
$blocks['sessions']['label'] = api_ucfirst(get_lang('Sessions'));
$blocks['sessions']['class'] = 'block-admin-sessions';


$search_form = ' <form method="GET" class="form-search" action="session_list.php">
                    <input class="span3" type="text" name="keyword" value="">
                    <button class="btn" type="submit">'.get_lang('Search').'</button>
                </form>';
$blocks['sessions']['search_form'] = $search_form;
$items = array();
$items[] = array('url'=>'session_list.php', 	'label' => get_lang('ListSession'));
$items[] = array('url'=>'session_add.php', 	'label' => get_lang('AddSession'));
$items[] = array('url'=>'session_category_list.php', 	'label' => get_lang('ListSessionCategory'));
$items[] = array('url'=>'session_import.php', 	'label' => get_lang('ImportSessionListXMLCSV'));
$items[] = array('url'=>'session_import_drh.php', 	'label' => get_lang('ImportSessionDrhList'));
if (isset($extAuthSource) && isset($extAuthSource['ldap']) && count($extAuthSource['ldap']) > 0) {
    $items[] = array('url'=>'ldap_import_students_to_session.php', 	'label' => get_lang('ImportLDAPUsersIntoSession'));
}
$items[] = array('url'=>'session_export.php', 	'label' => get_lang('ExportSessionListXMLCSV'));
$items[] = array('url'=>'../coursecopy/copy_course_session.php', 	'label' => get_lang('CopyFromCourseInSessionToAnotherSession'));

if (api_is_platform_admin()) {
    if (is_dir(api_get_path(SYS_TEST_PATH).'datafiller/')) { // option only visible in development mode. Enable through code if required
        $items[] = array('url'=>'user_move_stats.php', 	'label' => get_lang('MoveUserStats'));
    }
    $items[] = array('url'=>'career_dashboard.php', 	'label' => get_lang('CareersAndPromotions'));
}

$items[] = array('url'=>'session_fields.php', 	'label' => get_lang('ManageSessionFields'));

$blocks['sessions']['items'] = $items;
$blocks['sessions']['extra'] = null;


/* Settings */
if (api_is_platform_admin()) {

    $blocks['settings']['icon']  = Display::return_icon('settings.png', get_lang('System'), array(), ICON_SIZE_SMALL, false);
    $blocks['settings']['label'] = api_ucfirst(get_lang('System'));
    $blocks['settings']['class'] = 'block-admin-settings';


    $items = array();
    $items[] = array('url'=>'special_exports.php', 	'label' => get_lang('SpecialExports'));
    if (!empty($_configuration['db_admin_path'])) {
        $items[] = array('url'=>$_configuration['db_admin_path'], 	'label' => get_lang('AdminDatabases').' ('.get_lang('DBManagementOnlyForServerAdmin').') ');
    }
    $items[] = array('url'=>'system_status.php', 	'label' => get_lang('SystemStatus'));
    if (is_dir(api_get_path(SYS_TEST_PATH).'datafiller/')) {
        $items[] = array('url'=>'filler.php', 	'label' => get_lang('DataFiller'));
    }
    $items[] = array('url'=>'archive_cleanup.php', 	'label' => get_lang('ArchiveDirCleanup'));
    if (api_get_setting('server_type') === 'test') {
        $items[] = array('url'=>'system_management.php', 'label' => get_lang('SystemManagement'));
    }

    if (isset($_configuration['db_manager_enabled']) &&
        $_configuration['db_manager_enabled'] == true &&
        api_is_global_platform_admin()
    ) {
        $host = $_configuration['db_host'];
        $username = $_configuration['db_user'];
        $databaseName = $_configuration['main_database'];

        $items[] = array('url'=>"db.php?username=$username&db=$databaseName&server=$host", 'label' => get_lang('Database Manager'));
    }

    $blocks['settings']['items'] = $items;
    $blocks['settings']['extra'] = null;

    $blocks['settings']['search_form'] = null;

    /* Extensions */
    /*
    $blocks['extensions']['icon']  = Display::return_icon('visio_meeting.gif', get_lang('ConfigureExtensions'), array(), ICON_SIZE_SMALL, false);
    $blocks['extensions']['label'] = api_ucfirst(get_lang('ConfigureExtensions'));
    $blocks['extensions']['class'] = 'block-admin-extensions';


    $items = array();
    $items[] = array('url'=>'configure_extensions.php?display=visio', 	'label' => get_lang('Visioconf'));
    $items[] = array('url'=>'configure_extensions.php?display=ppt2lp', 	'label' => get_lang('Ppt2lp'));
    //$items[] = array('url'=>'configure_extensions.php?display=ephorus', 	'label' => get_lang('EphorusPlagiarismPrevention'));
    $items[] = array('url'=>'configure_extensions.php?display=search', 	'label' => get_lang('SearchEngine'));
    $items[] = array('url'=>'configure_extensions.php?display=serverstats', 	'label' => get_lang('ServerStatistics'));
    $items[] = array('url'=>'configure_extensions.php?display=bandwidthstats', 	'label' => get_lang('BandWidthStatistics'));
    $blocks['extensions']['items'] = $items;
    */

    //Skills
    if (api_get_setting('allow_skills_tool') == 'true') {
        $blocks['skills']['icon']  = Display::return_icon('logo.png', get_lang('Skills'), array(), ICON_SIZE_SMALL, false);
        $blocks['skills']['label'] = get_lang('Skills');
        $blocks['skills']['class'] = 'block-admin-skills';


        $items = array();
        //$items[] = array('url'=>'skills.php',           'label' => get_lang('SkillsTree'));
        $items[] = array('url'=>'skills_wheel.php',     'label' => get_lang('SkillsWheel'));
        $items[] = array('url'=>'skills_import.php',    'label' => get_lang('SkillsImport'));
        //$items[] = array('url'=>'skills_profile.php',   'label' => get_lang('SkillsProfile'));
        $items[] = array('url'=>api_get_path(WEB_CODE_PATH).'social/skills_ranking.php',   'label' => get_lang('SkillsRanking'));
        $items[] = array('url'=>'skills_gradebook.php', 'label' => get_lang('SkillsAndGradebooks'));
        $blocks['skills']['items'] = $items;
        $blocks['skills']['extra'] = null;
        $blocks['skills']['search_form'] = null;
    }

    /* Chamilo.org */

    $blocks['chamilo']['icon']  = Display::return_icon('logo.png', 'Chamilo.org', array(), ICON_SIZE_SMALL, false);
    $blocks['chamilo']['label'] = 'Chamilo.org';
    $blocks['chamilo']['class'] = 'block-admin-chamilo';


    $items = array();
    $items[] = array('url'=>'http://www.chamilo.org/', 	'label' => get_lang('ChamiloHomepage'));
    $items[] = array('url'=>'http://www.chamilo.org/forum', 	'label' => get_lang('ChamiloForum'));

    $items[] = array('url'=>'../../documentation/installation_guide.html', 	'label' => get_lang('InstallationGuide'));
    $items[] = array('url'=>'../../documentation/changelog.html', 	'label' => get_lang('ChangesInLastVersion'));
    $items[] = array('url'=>'../../documentation/credits.html', 	'label' => get_lang('ContributorsList'));
    $items[] = array('url'=>'../../documentation/security.html', 	'label' => get_lang('SecurityGuide'));
    $items[] = array('url'=>'../../documentation/optimization.html', 	'label' => get_lang('OptimizationGuide'));
    $items[] = array('url'=>'http://www.chamilo.org/extensions', 	'label' => get_lang('ChamiloExtensions'));
    $items[] = array('url'=>'http://www.chamilo.org/en/providers', 	'label' => get_lang('ChamiloOfficialServicesProviders'));

    $blocks['chamilo']['items'] = $items;
    $blocks['chamilo']['extra'] = null;
    $blocks['chamilo']['search_form'] = null;

    // Try to display a maximum before we check the chamilo version and all that.
    //session_write_close(); //close session to avoid blocking concurrent access
    //flush(); //send data to client as much as allowed by the web server
    //ob_flush();

    //Version check
    $blocks['version_check']['icon']  = Display::return_icon('logo.png', 'Chamilo.org', array(), ICON_SIZE_SMALL, false);
    $blocks['version_check']['label'] = get_lang('VersionCheck');
    $blocks['version_check']['extra'] = '<div class="admin-block-version"></div>';
    $blocks['version_check']['search_form'] = null;
    $blocks['version_check']['items'] = null;
    $blocks['version_check']['class'] = 'block-admin-version_check';

}
$admin_ajax_url = api_get_path(WEB_AJAX_PATH).'admin.ajax.php';

$tpl = new Template();
$tpl->assign('web_admin_ajax_url', $admin_ajax_url);
$tpl->assign('blocks', $blocks);
// The template contains the call to the AJAX version checker
$admin_template = $tpl->get_template('admin/settings_index.tpl');
$content = $tpl->fetch($admin_template);
$tpl->assign('content', $content);
$tpl->assign('message', $message);
$tpl->display_one_col_template();

// Note: version checking mechanism has now been moved to main/inc/ajax/admin.ajax.php
