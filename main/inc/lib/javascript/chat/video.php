<?php
/* For licensing terms, see /license.txt */
require_once '../../../global.inc.php';

if (api_is_anonymous()) {
    api_not_allowed(true);
}

$roomName = isset($_GET['room']) ? $_GET['room'] : null;

$room = VideoChat::getChatRoomByName($roomName);

if ($room === false) {
    header('Location: '.api_get_path(WEB_PATH));
    exit;
}

$friend_html = SocialManager::listMyFriendsBlock($user_id, '', false);

$isSender = $room['from_user'] == api_get_user_id();
$isReceiver = $room['to_user'] == api_get_user_id();

if (!$isSender && !$isReceiver) {
    header('Location: '.api_get_path(WEB_PATH));
    exit;
}

if ($isSender) {
    $chatUser = api_get_user_info($room['to_user']);
} elseif ($isReceiver) {
    $chatUser = api_get_user_info($room['from_user']);
}
$idUserLocal = api_get_user_id();
$userLocal = api_get_user_info($idUserLocal, true);
$htmlHeadXtra[] = '<script type="text/javascript" src="'
    . api_get_path(WEB_PATH) . 'web/assets/simplewebrtc/latest.js'
    . '"></script>' . "\n";

$template = new Template();
$template->assign('room', $room);
$template->assign('chat_user', $chatUser);
$template->assign('user_local', $userLocal);
$template->assign('block_friends', $friend_html);

$content = $template->fetch('default/chat/video.tpl');

$templateHeader = Display::returnFontAswesomeIcon('video-camera', true, 'lg')
    . $room['room_name'];

$template->assign('header', $templateHeader);
$template->assign('content', $content);
$template->assign(
    'message',
    Display::return_message(get_lang('YourBroswerDoesNotSupportWebRTC'), 'warning')
);
$template->display_one_col_template();
//$template->display_no_layout_template();
