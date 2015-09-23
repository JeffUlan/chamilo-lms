<?php
/* For licensing terms, see /license.txt */
require_once '../../../global.inc.php';

if (api_is_anonymous()) {
    api_not_allowed(true);
}

$roomId = isset($_GET['room']) ? $_GET['room'] : null;

$entityManager = Database::getManager();

$chatVideo = $entityManager->find('ChamiloCoreBundle:ChatVideo', $roomId);

if (!$chatVideo) {
    header('Location: '.api_get_path(WEB_PATH));
    exit;
}

$friend_html = SocialManager::listMyFriendsBlock($user_id, '', false);
$isSender = $chatVideo->getFromUser() === api_get_user_id();
$isReceiver = $chatVideo->getToUser() === api_get_user_id();

if (!$isSender && !$isReceiver) {
    header('Location: '.api_get_path(WEB_PATH));
    exit;
}

if ($isSender) {
    $chatUser = api_get_user_info($chatVideo->getToUser());
} elseif ($isReceiver) {
    $chatUser = api_get_user_info($chatVideo->getFromUser());
}
$idUserLocal = api_get_user_id();
$userLocal = api_get_user_info($idUserLocal, true);
$htmlHeadXtra[] = '<script type="text/javascript" src="'
    . api_get_path(WEB_PATH) . 'web/assets/simplewebrtc/latest.js'
    . '"></script>' . "\n";

$template = new Template();
$template->assign('chat_user', $chatUser);
$template->assign('user_local', $userLocal);
$template->assign('block_friends', $friend_html);

$content = $template->fetch('default/chat/video.tpl');

$templateHeader = Display::returnFontAswesomeIcon('video-camera', true, 'lg')
    . $chatVideo->getRoomName();

$template->assign('header', $templateHeader);
$template->assign('content', $content);
$template->assign(
    'message',
    Display::return_message(get_lang('YourBroswerDoesNotSupportWebRTC'), 'warning')
);
$template->display_one_col_template();
//$template->display_no_layout_template();
