<?php
/* For licensing terms, see /license.txt */

/**
 *   HOME PAGE FOR EACH COURSE
 *
 *	This page, included in every course's index.php is the home
 *	page. To make administration simple, the teacher edits his
 *	course from the home page. Only the login detects that the
 *	visitor is allowed to activate, deactivate home page links,
 *	access to the teachers tools (statistics, edit forums...).
 *
 *	@package chamilo.course_home
 */

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$course_id = api_get_course_int_id();
$session_id = api_get_session_id();

//	MAIN CODE

if (api_is_allowed_to_edit(null, true)) {
    // HIDE
    if (!empty($_GET['hide'])) {
        $sql = "UPDATE $tool_table SET visibility=0 WHERE c_id = $course_id AND id=".$id;
        Database::query($sql);
        $show_message = Display::return_message(get_lang('ToolIsNowHidden'), 'confirmation');
    } elseif (!empty($_GET['restore'])) {
        // visibility 0,2 -> 1
        // REACTIVATE
        $sql = "UPDATE $tool_table SET visibility=1 WHERE c_id = $course_id AND id=".$id;
        Database::query($sql);
        //$show_message = Display::return_message(get_lang('ToolIsNowVisible'),'confirmation');
    }
}

// Work with data post askable by admin of course
if (api_is_platform_admin()) {
    // Show message to confirm that a tool it to be hidden from available tools
    // visibility 0,1->2
    if (!empty($_GET['askDelete'])) {
        $content .='<div id="toolhide">'.get_lang('DelLk').'<br />&nbsp;&nbsp;&nbsp;
            <a href="'.api_get_self().'">'.get_lang('No').'</a>&nbsp;|&nbsp;
            <a href="'.api_get_self().'?delete=yes&id='.$id.'">'.get_lang('Yes').'</a>
        </div>';
    } elseif (isset($_GET['delete']) && $_GET['delete']) {
        /*
        * Process hiding a tools from available tools.
        */
        //where $id is set?
        $id = intval($id);
        Database::query("DELETE FROM $tool_table WHERE c_id = $course_id AND id='$id' AND added_tool=1");
    }
}

// Course legal
$enabled = api_get_plugin_setting('courselegal', 'tool_enable');
$pluginExtra = null;
if ($enabled == 'true') {
    require_once api_get_path(SYS_PLUGIN_PATH).'courselegal/config.php';
    $plugin = CourseLegalPlugin::create();
    $pluginExtra = $plugin->getTeacherLink();
}

//	COURSE ADMIN ONLY VIEW

// Start of tools for CourseAdmins (teachers/tutors)

if ($session_id == 0 && api_is_course_admin() && api_is_allowed_to_edit(null, true)) {
    $content .=  '<div class="courseadminview" style="border:0px; margin-top: 0px;padding:0px;">
		<div class="normal-message" id="id_normal_message" style="display:none">';
    $content .=  '<img src="'.api_get_path(WEB_PATH).'main/inc/lib/javascript/indicator.gif"/>&nbsp;&nbsp;';
    $content .=  get_lang('PleaseStandBy');
    $content .=  '</div>
		<div class="confirmation-message" id="id_confirmation_message" style="display:none"></div>
	</div>';

    $content .= $pluginExtra;

    if (api_get_setting('show_session_data') == 'true' && $session_id > 0) {
        $content .= '<div class="courseadminview">
            <span class="viewcaption">'.get_lang('SessionData').'</span>
            <table class="course_activity_home">'.CourseHome::show_session_data($session_id).'
            </table>
        </div>';
    }
    $my_list = CourseHome::get_tools_category(TOOL_AUTHORING);
    $items = CourseHome::show_tools_category($my_list);
    $content .= return_block(get_lang('Authoring'),  $items, 'course-tools-author');

    $my_list = CourseHome::get_tools_category(TOOL_INTERACTION);
    $list2 = CourseHome::get_tools_category(TOOL_COURSE_PLUGIN);

    $my_list = array_merge($my_list,$list2);
    $items =  CourseHome::show_tools_category($my_list);
    $content .= return_block(get_lang('Interaction'),  $items, 'course-tools-interaction');

    $my_list = CourseHome::get_tools_category(TOOL_ADMIN_PLATFORM);
    $items = CourseHome::show_tools_category($my_list);
    $content .= return_block(get_lang('Administration'),  $items , 'course-tools-administration');

} elseif (api_is_coach()) {
    $content .= $pluginExtra;
    if (api_get_setting('show_session_data') == 'true' && $session_id > 0) {
        $content .= '<div class="row">
			<span class="viewcaption">'.get_lang('SessionData').'</span>
			<table class="course_activity_home">';
        $content .= CourseHome::show_session_data($session_id);
        $content .=  '</table></div>';
    }

    $content .=  '<div class="row">';
    $my_list = CourseHome::get_tools_category(TOOL_STUDENT_VIEW);
    $content .= CourseHome::show_tools_category($my_list);
    $content .= '</div>';

    if (isset($_configuration['allow_session_course_copy_for_teachers'])) {
        if ($_configuration['allow_session_course_copy_for_teachers']) {
            // Adding only maintenance for coaches.
            $myList = CourseHome::get_tools_category(TOOL_ADMIN_PLATFORM);
            $onlyMaintenanceList = array();

            foreach ($myList as $item) {
                if ($item['name'] == 'course_maintenance') {
                    $item['link'] = 'course_info/maintenance_coach.php';

                    $onlyMaintenanceList[] = $item;
                }
            }

            $items = CourseHome::show_tools_category($onlyMaintenanceList);
            $content .= return_block(get_lang('Administration'), $items);
        }
    }
} else {
    $my_list = CourseHome::get_tools_category(TOOL_STUDENT_VIEW);
    if (count($my_list) > 0) {
        $content .= '<div class="row">';
        $content .= CourseHome::show_tools_category($my_list);
        $content .= '</div>';
    }
}

function return_block($title, $content, $class) {
    $html = '<div class="row course-title-tools"><div class="span12"><div class="page-header"><h3>'.$title.'</h3></div></div></div><div class="row '.$class.'">'.$content.'</div>';
    return $html;
}
