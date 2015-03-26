<?php
/* For licensing terms, see /license.txt */

use ChamiloSession as Session;

require_once '../inc/global.inc.php';
$current_course_tool  = TOOL_STUDENTPUBLICATION;

api_protect_course_script(true);

// Including necessary files
require_once 'work.lib.php';

$this_section = SECTION_COURSES;

$work_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : null;

$is_allowed_to_edit = api_is_allowed_to_edit();
$course_id = api_get_course_int_id();
$user_id = api_get_user_id();
$userInfo = api_get_user_info();
$session_id = api_get_session_id();
$course_code = api_get_course_id();
$course_info = api_get_course_info();
$group_id = api_get_group_id();

if (empty($work_id)) {
    api_not_allowed(true);
}

$workInfo = get_work_data_by_id($work_id);

if (empty($workInfo)) {
    api_not_allowed(true);
}

if ($workInfo['active'] != 1) {
    api_not_allowed(true);
}

allowOnlySubscribedUser($user_id, $work_id, $course_id);

$is_course_member = CourseManager::is_user_subscribed_in_real_or_linked_course($user_id, $course_code, $session_id);
$is_course_member = $is_course_member || api_is_platform_admin();

if ($is_course_member == false || api_is_invitee()) {
    api_not_allowed(true);
}

$check = Security::check_token('post');
$token = Security::get_token();

$student_can_edit_in_session = api_is_allowed_to_session_edit(false, true);

//  @todo add an option to allow/block multiple attempts.
/*
if (!empty($workInfo) && !empty($workInfo['qualification'])) {
    $count =  get_work_count_by_student($user_id, $work_id);
    if ($count >= 1) {
        Display::display_header();
        if (api_get_course_setting('student_delete_own_publication') == '1') {
            Display::display_warning_message(get_lang('CantUploadDeleteYourPaperFirst'));
        } else {
            Display::display_warning_message(get_lang('YouAlreadySentAPaperYouCantUpload'));
        }
        Display::display_footer();
        exit;
    }
}*/

$homework = get_work_assignment_by_id($workInfo['id']);
$validationStatus = getWorkDateValidationStatus($homework);

$interbreadcrumb[] = array('url' => api_get_path(WEB_CODE_PATH).'work/work.php?'.api_get_cidreq(), 'name' => get_lang('StudentPublications'));
$interbreadcrumb[] = array('url' => api_get_path(WEB_CODE_PATH).'work/work_list.php?'.api_get_cidreq().'&id='.$work_id, 'name' =>  $workInfo['title']);
$interbreadcrumb[] = array('url' => '#', 'name'  => get_lang('UploadADocument'));

$form = new FormValidator('form', 'POST', api_get_self()."?".api_get_cidreq()."&id=".$work_id, '', array('enctype' => "multipart/form-data"));
setWorkUploadForm($form, $workInfo['allow_text_assignment']);
$form->addElement('hidden', 'id', $work_id);
$form->addElement('hidden', 'sec_token', $token);

$error_message = null;

$succeed = false;
if ($form->validate()) {

    if ($student_can_edit_in_session && $check) {
        $values = $form->getSubmitValues();
        // Process work
        $error_message = processWorkForm(
            $workInfo,
            $values,
            $course_info,
            $session_id,
            $group_id,
            $user_id
        );
        $script = 'work_list.php';
        if ($is_allowed_to_edit) {
            $script = 'work_list_all.php';
        }
        if (!empty($error_message)) {
            Session::write('error_message', $error_message);
        }
        header('Location: '.api_get_path(WEB_CODE_PATH).'work/'.$script.'?'.api_get_cidreq().'&id='.$work_id);
        exit;
    } else {
        // Bad token or can't add works
        $error_message = Display::return_message(get_lang('IsNotPosibleSaveTheDocument'), 'error');
    }
}

$htmlHeadXtra[] = to_javascript_work();
Display :: display_header(null);

if (!empty($work_id)) {
    echo $validationStatus['message'];
    if ($is_allowed_to_edit) {
        if (api_resource_is_locked_by_gradebook($work_id, LINK_STUDENTPUBLICATION)) {
            echo Display::display_warning_message(get_lang('ResourceLockedByGradebook'));
        } else {
            $form->display();
        }
    } elseif ($student_can_edit_in_session && $validationStatus['has_ended'] == false) {
        $form->display();
    } else {
        Display::display_error_message(get_lang('ActionNotAllowed'));
    }
} else {
    Display::display_error_message(get_lang('ActionNotAllowed'));
}

Display :: display_footer();
