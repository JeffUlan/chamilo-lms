<?php

/* For licensing terms, see /license.txt */
/**
 * Search user certificates if them are publics
 * @author Angel Fernando Quiroz Campos <angel.quiroz@beeznest.com>
 * @package chamilo.gradebook
 */
use \ChamiloSession as Session;

$cidReset = true;

require_once '../inc/global.inc.php';

if (api_get_setting('allow_public_certificates') != 'true') {
    api_not_allowed(
        true,
        Display::return_message(get_lang('CertificatesNotPublic'), 'warning')
    );
}

$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$userList = $userInfo = $courseList = $sessionList = [];

$searchForm = new FormValidator('search_form', 'post', null, null);
$searchForm->addText('firstname', get_lang('Firstname'));
$searchForm->addText('lastname', get_lang('Lastname'));
$searchForm->addButtonSearch();

if ($searchForm->validate()) {
    $firstname = $searchForm->getSubmitValue('firstname');
    $lastname = $searchForm->getSubmitValue('lastname');

    $userList = UserManager::getUserByName($firstname, $lastname);

    if (empty($userList)) {
        Session::write('message', Display::return_message(get_lang('NoResults'), 'warning'));

        Header::location(api_get_self());
    }
} elseif ($userId > 0) {
    $userInfo = api_get_user_info($userId);

    if (empty($userInfo)) {
        Session::write('message', Display::return_message(get_lang('NoUser'), 'warning'));

        Header::location(api_get_self());
    }

    $courseList = GradebookUtils::getUserCertificatesInCourses($userId, false);
    $sessionList = GradebookUtils::getUserCertificatesInSessions($userId, false);

    if (empty($courseList) && empty($sessionList)) {
        Session::write(
            'message',
            Display::return_message(
                sprintf(get_lang('TheUserXNotYetAchievedCertificates'), $userInfo['complete_name']),
                'warning'
            )
        );

        Header::location(api_get_self());
    }
}

$template = new Template(get_lang('SearchCertificates'));

$template->assign('search_form', $searchForm->returnForm());
$template->assign('user_list', $userList);
$template->assign('user_info', $userInfo);
$template->assign('course_list', $courseList);
$template->assign('session_list', $sessionList);

if (Session::has('message')) {
    $template->assign('message', Session::read('message'));
    Session::erase('message');
}

$content = $template->fetch('default/gradebook/search.tpl');

$template->assign('header', get_lang('SearchCertificates'));
$template->assign('content', $content);

$template->display_one_col_template();
