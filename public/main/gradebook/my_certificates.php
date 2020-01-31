<?php
/* For licensing terms, see /license.txt */

/**
 * List of achieved certificates by the current user.
 *
 * @author Angel Fernando Quiroz Campos <angel.quiroz@beeznest.com>
 */
$cidReset = true;

require_once __DIR__.'/../inc/global.inc.php';

$logInfo = [
    'tool' => 'MyCertificates',
    'tool_id' => 0,
    'tool_id_detail' => 0,
    'action' => '',
    'action_details' => '',
];
Event::registerLog($logInfo);

if (api_is_anonymous()) {
    api_not_allowed(true);
}

$userId = api_get_user_id();

$courseList = GradebookUtils::getUserCertificatesInCourses($userId);
$sessionList = GradebookUtils::getUserCertificatesInSessions($userId);

if (empty($courseList) && empty($sessionList)) {
    Display::addFlash(
        Display::return_message(get_lang('You have not achieved any certificate just yet. Continue on your learning path to get one!'), 'warning')
    );
}

$template = new Template(get_lang('My certificates'));

$template->assign('course_list', $courseList);
$template->assign('session_list', $sessionList);
$templateName = $template->get_template('gradebook/my_certificates.tpl');
$content = $template->fetch($templateName);

if ('true' === api_get_setting('allow_public_certificates')) {
    $template->assign(
        'actions',
        Display::toolbarButton(
            get_lang('Search certificates'),
            api_get_path(WEB_CODE_PATH).'gradebook/search.php',
            'search',
            'info'
        )
    );
}

$template->assign('content', $content);
$template->display_one_col_template();
