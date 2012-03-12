<?php
/* For licensing terms, see /license.txt */
/**
 * @package chamilo.calendar
 */
/**
 * INIT SECTION
 */

// name of the language file that needs to be included
$language_file = array('agenda', 'group', 'announcements');

// use anonymous mode when accessing this course tool
$use_anonymous = true;

require_once '../inc/global.inc.php';
require_once 'agenda.lib.php';
require_once 'agenda.inc.php';

$htmlHeadXtra[] = api_get_jquery_libraries_js(array('jquery-ui','jquery-ui-i18n'));
$htmlHeadXtra[] = api_get_js('qtip2/jquery.qtip.min.js');
$htmlHeadXtra[] = api_get_js('fullcalendar/fullcalendar.min.js');
$htmlHeadXtra[] = api_get_js('fullcalendar/gcal.js');
$htmlHeadXtra[] = api_get_css(api_get_path(WEB_LIBRARY_PATH).'javascript/fullcalendar/fullcalendar.css');
$htmlHeadXtra[] = api_get_css(api_get_path(WEB_LIBRARY_PATH).'javascript/qtip2/jquery.qtip.min.css');

$type  	= isset($_REQUEST['type']) && in_array($_REQUEST['type'], array('personal', 'course', 'admin')) ?  $_REQUEST['type'] : 'personal';
if (api_is_platform_admin() && $type == 'admin') {	
	$type = 'admin';
}
//if (api_get_course_id() != -1 && $type == 'course') {
if (isset($_REQUEST['cidReq']) && !empty($_REQUEST['cidReq'])) {	
	$type = 'course';
}

$tpl	= new Template(get_lang('Agenda'));

$tpl->assign('use_google_calendar', 0);

switch($type) {
	case 'admin':		
		$this_section = SECTION_PLATFORM_ADMIN;
		break;
	case 'course':
		$this_section = SECTION_COURSES;
		break;
	case 'personal':
        if (api_is_anonymous()) {
            api_not_allowed();
        }
        $extra_field_data = UserManager::get_extra_user_data_by_field(api_get_user_id(), 'google_calendar_url');
        if (!empty($extra_field_data) && isset($extra_field_data['google_calendar_url']) && !empty($extra_field_data['google_calendar_url'])) {            
            $tpl->assign('use_google_calendar', 1);
            $tpl->assign('google_calendar_url', $extra_field_data['google_calendar_url']);
        }
		$this_section = SECTION_MYAGENDA;
		break;
}

$can_add_events = 0;
if (api_is_platform_admin() && $type == 'admin') {
	$can_add_events = 1;
}
if (api_is_allowed_to_edit() && $type == 'course') {
	$can_add_events = 1;	
}
if (!api_is_anonymous() && $type == 'personal') {
	$can_add_events = 1;
}

//Setting translations
$day_short 		= api_get_week_days_short();
$days 			= api_get_week_days_long();
$months 		= api_get_months_long();
$months_short 	= api_get_months_short();

//Setting calendar translations
$tpl->assign('month_names', 		json_encode($months));
$tpl->assign('month_names_short', 	json_encode($months_short));
$tpl->assign('day_names', 			json_encode($days));
$tpl->assign('day_names_short', 	json_encode($day_short));
$tpl->assign('button_text', 		json_encode(array(	'today'	=> get_lang('Today'), 
														'month'	=> get_lang('Month'), 
														'week'	=> get_lang('Week'), 
														'day'	=> get_lang('Day'))));

//see http://docs.jquery.com/UI/Datepicker/$.datepicker.formatDate

$tpl->assign('js_format_date', 	'D d M yy');
$region_value = api_get_language_isocode();

if ($region_value == 'en') {
    $region_value = 'en-GB';
}
$tpl->assign('region_value', 	$region_value);


if (api_is_allowed_to_edit(false,true) OR (api_get_course_setting('allow_user_edit_agenda') && !api_is_anonymous()) && api_is_allowed_to_session_edit(false,true)) {
    if ($type == 'course') {
        $actions = display_courseadmin_links();
    }
	$tpl->assign('actions', $actions);
}

//Calendar Type : course, admin, personal
$tpl->assign('type', $type);
//Calendar type label

$tpl->assign('type_label', get_lang(ucfirst($type).'Calendar'));

//Current user can add event?
$tpl->assign('can_add_events', $can_add_events);

//Setting AJAX caller
$agenda_ajax_url = api_get_path(WEB_AJAX_PATH).'agenda.ajax.php?type='.$type;
$tpl->assign('web_agenda_ajax_url', $agenda_ajax_url);

$course_code  = api_get_course_id();

if (api_is_allowed_to_edit() && $course_code != '-1' && $type == 'course') {
    $order = 'lastname';
    if (api_is_western_name_order) {
        $order = 'firstname';    
    }    
    $user_list  = CourseManager::get_user_list_from_course_code(api_get_course_id(), api_get_session_id(), null, $order);
    $group_list = CourseManager::get_group_list_of_course(api_get_course_id(), api_get_session_id());

    $agenda = new Agenda();
    $select = $agenda->construct_not_selected_select_form($group_list, $user_list);
    $tpl->assign('visible_to', $select);
}

//Loading Agenda template
//$content .= gettext('Hello');
//$content .= gettext('Admin');
$content .= $tpl->fetch('default/agenda/month.tpl');

$tpl->assign('content', $content);

//Loading main Chamilo 1 col template
$tpl->display_one_col_template();