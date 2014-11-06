<?php
/* For licensing terms, see /license.txt */
/**
 * Create a backup.
 *
 * @author Bart Mollet <bart.mollet@hogent.be>
 * @package chamilo.backup
 */
/**
 * Code
 */
// Language files that need to be included
$language_file = array('exercice', 'admin', 'coursebackup');

// Including the global initialization file
require_once '../inc/global.inc.php';
$current_course_tool = TOOL_COURSE_MAINTENANCE;

api_protect_course_script(true);

api_check_archive_dir();

// Check access rights (only teachers are allowed here)
if (!api_is_allowed_to_edit()) {
    api_not_allowed(true);
}

// Remove memory and time limits as much as possible as this might be a long process...
if (function_exists('ini_set')) {
    api_set_memory_limit('256M');
    ini_set('max_execution_time', 1800);
}

// Section for the tabs
$this_section = SECTION_COURSES;

// Breadcrumbs
$interbreadcrumb[] = array(
    'url' => '../course_info/maintenance.php',
    'name' => get_lang('Maintenance')
);

// Displaying the header
$nameTools = get_lang('CreateBackup');
Display::display_header($nameTools);

// Include additional libraries
require_once api_get_path(LIBRARY_PATH) . 'fileManage.lib.php';
require_once 'classes/CourseBuilder.class.php';
require_once 'classes/CourseArchiver.class.php';
require_once 'classes/CourseRestorer.class.php';
require_once 'classes/CourseSelectForm.class.php';

// Display the tool title
echo Display::page_header($nameTools);

/*	MAIN CODE */

if (Security::check_token('post') && (
        (
            isset($_POST['action']) &&
            $_POST['action'] == 'course_select_form'
        ) || (
            isset($_POST['backup_option']) &&
            $_POST['backup_option'] == 'full_backup'
        )
    )
) {
    // Clear token
    Security::clear_token();

    if (isset($_POST['action']) && $_POST['action'] == 'course_select_form') {
        $course = CourseSelectForm::get_posted_course();
    } else {
        $cb = new CourseBuilder();
        $course = $cb->build();
    }

    $zip_file = CourseArchiver::write_course($course);
    Display::display_confirmation_message(get_lang('BackupCreated'));
    echo '<br /><a class="btn btn-primary btn-large" href="' . api_get_path(WEB_CODE_PATH) . 'course_info/download.php?archive=' . $zip_file . '&' . api_get_cidreq() . '">
    ' . get_lang('Download') . '</a>';

} elseif (Security::check_token('post') && (
        isset($_POST['backup_option']) &&
        $_POST['backup_option'] == 'select_items'
    )
) {
    // Clear token
    Security::clear_token();

    $cb = new CourseBuilder('partial');
    $course = $cb->build();
    // Add token to Course select form
    $hiddenFields['sec_token'] = Security::get_token();
    CourseSelectForm::display_form($course, $hiddenFields);

} else {
    $cb = new CourseBuilder();
    $course = $cb->build();
    if (!$course->has_resources()) {
        echo get_lang('NoResourcesToBackup');
    } else {
        $form = new FormValidator('create_backup_form', 'post', api_get_self() . '?' . api_get_cidreq());
        $form->addElement('header', get_lang('SelectOptionForBackup'));
        $form->addElement('radio', 'backup_option', '', get_lang('CreateFullBackup'), 'full_backup');
        $form->addElement('radio', 'backup_option', '', get_lang('LetMeSelectItems'), 'select_items');
        $form->addElement('style_submit_button', null, get_lang('CreateBackup'), 'class="save"');
        $form->add_progress_bar();
        // When progress bar appears we have to hide the title "Please select a backup-option".
        $form->updateAttributes(
            array(
                'onsubmit' => str_replace(
                    'javascript: ',
                    'javascript: page_title = getElementById(\'page_title\'); if (page_title) { setTimeout(\'page_title.style.display = \\\'none\\\';\', 2000); } ',
                    $form->getAttribute('onsubmit')
                )
            )
        );
        $values['backup_option'] = 'full_backup';
        $form->setDefaults($values);

        // Add Security token
        $token = Security::get_token();
        $form->addElement('hidden', 'sec_token');
        $form->setConstants(array('sec_token' => $token));

        $form->display();
    }
}

Display::display_footer();
