<?php
/* For licensing terms, see /license.txt */

/**
 *	@package chamilo.tracking
 */

$pathopen = isset($_REQUEST['pathopen']) ? $_REQUEST['pathopen'] : null;
// Including the global initialization file
require_once __DIR__.'/../inc/global.inc.php';
$current_course_tool = TOOL_TRACKING;
$course_info = api_get_course_info();
$from_myspace = false;
$from = isset($_GET['from']) ? $_GET['from'] : null;

if ($from == 'myspace') {
    $from_myspace = true;
    $this_section = "session_my_space";
} else {
    $this_section = SECTION_COURSES;
}

// Access restrictions.
$is_allowedToTrack = api_is_platform_admin() || api_is_allowed_to_create_course() || api_is_session_admin() || api_is_drh() || api_is_course_tutor();

if (!$is_allowedToTrack) {
    api_not_allowed();
    exit;
}

// Starting the output buffering when we are exporting the information.
$export_csv = isset($_GET['export']) && $_GET['export'] == 'csv' ? true : false;
$exportXls = isset($_GET['export']) && $_GET['export'] == 'xls' ? true : false;
$session_id = intval($_REQUEST['id_session']);

if ($export_csv || $exportXls) {
    $csvData = TrackingCourseLog::get_item_resources_data(0, 0, '', '');
    array_walk(
        $csvData,
        function (&$item) {
            $item[0] = strip_tags($item[0]);
            $item[2] = strip_tags(preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $item[2]));
            $item[3] = strip_tags($item[3]);
            $item[4] = strip_tags($item[4]);

            unset(
                $item['col0'],
                $item['col1'],
                $item['ref'],
                $item['col3'],
                $item['col6'],
                $item['user_id'],
                $item['col7']
            );
        }
    );

    array_unshift(
        $csvData,
        [
            get_lang('Tool'),
            get_lang('EventType'),
            get_lang('Session'),
            get_lang('UserName'),
            get_lang('IPAddress'),
            get_lang('Document'),
            get_lang('Date')
        ]
    );

    if ($export_csv) {
        Export::arrayToCsv($csvData);
    }
    if ($exportXls) {
        Export::arrayToXls($csvData);
    }
    die;
}

if (empty($session_id)) {
    $session_id = api_get_session_id();
}

// Breadcrumbs.
if (isset($_GET['origin']) && $_GET['origin'] == 'resume_session') {
    $interbreadcrumb[] = array('url' => api_get_path(WEB_CODE_PATH).'admin/index.php', 'name' => get_lang('PlatformAdmin'));
    $interbreadcrumb[] = array('url' => api_get_path(WEB_CODE_PATH).'session/session_list.php', 'name' => get_lang('SessionList'));
    $interbreadcrumb[] = array('url' => api_get_path(WEB_CODE_PATH).'session/resume_session.php?id_session='.api_get_session_id(), 'name' => get_lang('SessionOverview'));
}

$nameTools = get_lang('Tracking');

// Display the header.
Display::display_header($nameTools, 'Tracking');

/* MAIN CODE */

echo '<div class="actions">';
echo Display::url(
    Display::return_icon('user.png', get_lang('StudentsTracking'), array(), ICON_SIZE_MEDIUM),
    api_get_path(WEB_CODE_PATH).'tracking/courseLog.php?'.api_get_cidreq()
);

echo Display::url(
    Display::return_icon('group.png', get_lang('GroupReporting'), array(), ICON_SIZE_MEDIUM),
    'course_log_groups.php?'.api_get_cidreq(true, false)
);

echo Display::url(
    Display::return_icon('course.png', get_lang('CourseTracking'), array(), ICON_SIZE_MEDIUM),
    api_get_path(WEB_CODE_PATH).'tracking/course_log_tools.php?'.api_get_cidreq()
);

echo Display::return_icon('tools_na.png', get_lang('ResourcesTracking'), array(), ICON_SIZE_MEDIUM);
echo '<span style="float:right; padding-top:0px;">';
echo '<a href="javascript: void(0);" onclick="javascript: window.print();">'.
    Display::return_icon('printer.png', get_lang('Print'), '', ICON_SIZE_MEDIUM).
'</a>';

$addional_param = '';
if (isset($_GET['additional_profile_field'])) {
    $addional_param = 'additional_profile_field='.intval($_GET['additional_profile_field']);
}

$users_tracking_per_page = '';
if (isset($_GET['users_tracking_per_page'])) {
    $users_tracking_per_page = '&users_tracking_per_page='.intval($_GET['users_tracking_per_page']);
}

echo '<a href="'.api_get_self().'?'.api_get_cidreq().'&export=csv&'.$addional_param.$users_tracking_per_page.'">
'.Display::return_icon('export_csv.png', get_lang('ExportAsCSV'), '', ICON_SIZE_MEDIUM).'</a>';
echo '<a href="'.api_get_self().'?'.api_get_cidreq().'&export=xls&'.$addional_param.$users_tracking_per_page.'">
'.Display::return_icon('export_excel.png', get_lang('ExportAsXLS'), '', ICON_SIZE_MEDIUM).'</a>';

echo '</span>';
echo '</div>';

// Create a search-box.
$form = new FormValidator(
    'search_simple',
    'GET',
    api_get_path(WEB_CODE_PATH).'tracking/course_log_resources.php?'.api_get_cidreq().'&id_session'.$session_id,
    '',
    array('class' => 'form-search'),
    false
);
$renderer = $form->defaultRenderer();
$renderer->setCustomElementTemplate('<span>{element}</span>');
$form->addElement('text', 'keyword', get_lang('Keyword'));
$form->addElement('hidden', 'cidReq', api_get_course_id());
$form->addElement('hidden', 'id_session', $session_id);
$form->addButtonSearch(get_lang('SearchUsers'), 'submit');
echo '<div class="actions">';
$form->display();
echo '</div>';

$table = new SortableTable(
    'resources',
    array('TrackingCourseLog', 'count_item_resources'),
    array('TrackingCourseLog', 'get_item_resources_data'),
    6,
    20,
    'DESC'
);

$parameters = array(
    //'keyword' => Security::remove_XSS($_GET['keyword']),
    'id_session' => $session_id,
    'cidReq' => api_get_course_id()
);

$table->set_additional_parameters($parameters);
$table->set_header(0, get_lang('Tool'));
$table->set_header(1, get_lang('EventType'));
$table->set_header(2, get_lang('Session'), false);
$table->set_header(3, get_lang('UserName'), true, 'width=65px');
$table->set_header(4, get_lang('IPAddress'), true, 'width=100px');
$table->set_header(5, get_lang('Document'), false);
$table->set_header(6, get_lang('Date'), true, 'width=190px');
$table->display();

Display::display_footer();
