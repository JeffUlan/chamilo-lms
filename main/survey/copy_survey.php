<?php
/* For licensing terms, see /license.txt */
/**
 * @package chamilo.backup
 */
/**
 * Code
 */
// Setting the global file that gets the general configuration, the databases, the languages, ...
require_once '../inc/global.inc.php';
$current_course_tool  = TOOL_COURSE_MAINTENANCE;
api_protect_course_script(true);

// Notice for unauthorized people.
if (!api_is_allowed_to_edit()) {
    api_not_allowed(true);
}

// Breadcrumbs
$interbreadcrumb[] = array('url' => '../course_info/maintenance.php', 'name' => get_lang('Maintenance'));

// The section (for the tabs)
$this_section = SECTION_COURSES;

// Display the header
Display::display_header(get_lang('CopySurvey'));
echo Display::page_header(get_lang('CopySurvey'));

/* MAIN CODE */

// If a CourseSelectForm is posted or we should copy all resources, then copy them
if (Security::check_token('post')) {
    // Clear token
    Security::clear_token();
    $surveyId = intval($_POST['surveys']);
    $courseId = Security::remove_XSS($_POST['destination_course']);
    $surveyCopyId = survey_manager::copy_survey($surveyId, null, $courseId);
    // Copy the survey to the target course
    survey_manager::empty_survey($surveyCopyId, $courseId);
    // Empty the copied survey
    Display::display_confirmation_message(get_lang('SurveyCopied'));
}

$surveys = survey_manager::get_surveys(api_get_course_id(), api_get_session_id());
$courses = CourseManager::get_courses_list();
$form = new FormValidator('copy_survey', 'post', 'copy_survey.php?'.api_get_cidreq());
if (!$surveys) {
    Display::display_error_message(get_lang('NoSurveysAvailable'));
}
if (count($courses) <= 1) {
    Display::display_error_message(get_lang('NoCoursesAvailable'));
}
if ($surveys && count($courses) > 1) {
    // Surveys select
    $options = array();
    foreach ($surveys as $survey) {
        $options[$survey['survey_id']] = $survey['title'];
    }
    $form->addElement('select', 'surveys', get_lang('SelectSurvey'), $options);
    // All-courses-but-current select
    $currentCourseId = api_get_course_int_id();
    $options = array();
    foreach ($courses as $course) {
        if ($course['id'] != $currentCourseId) {
            $options[$course['id']] = $course['title'];
        }
    }
    $form->addElement('select', 'destination_course', get_lang('SelectDestinationCourse'), $options);
    $form->addElement('style_submit_button', 'submit', get_lang('CopySurvey'), 'class="save"');
}

// Add Security token
$token = Security::get_token();
$form->addElement('hidden', 'sec_token');
$form->setConstants(array('sec_token' => $token));

$form->display();

Display::display_footer();
