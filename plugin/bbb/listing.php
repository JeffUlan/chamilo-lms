<?php
/**
 * This script initiates a video conference session, calling the BigBlueButton API
 * @package chamilo.plugin.bigbluebutton
 */

$course_plugin = 'bbb'; //needed in order to load the plugin lang variables
require_once __DIR__.'/config.php';

$plugin = BBBPlugin::create();
$tool_name = $plugin->get_lang('Videoconference');
$tpl = new Template($tool_name);

$bbb = new bbb();
$action = isset($_GET['action']) ? $_GET['action'] : null;

$teacher = $bbb->isTeacher();
$courseInfo = api_get_course_info();

api_protect_course_script(true);
$message = null;

if ($teacher) {
    switch ($action) {
        case 'add_to_calendar':
            $course_info = api_get_course_info();
            $agenda = new Agenda();
            $agenda->type = 'course';

            $id = intval($_GET['id']);
            $title = sprintf(get_lang('VideoConferenceXCourseX'), $id, $course_info['name']);
            $content = Display::url(get_lang('GoToTheVideoConference'), $_GET['url']);

            $eventId = $agenda->addEvent(
                $_REQUEST['start'],
                null,
                'true',
                $title,
                $content,
                array('everyone')
            );
            if (!empty($eventId)) {
                $message = Display::return_message(get_lang('VideoConferenceAddedToTheCalendar'), 'success');
            } else {
                $message = Display::return_message(get_lang('Error'), 'error');
            }
            break;
        case 'copy_record_to_link_tool':
            $result = $bbb->copyRecordToLinkTool($_GET['id']);
            if ($result) {
                $message = Display::return_message(get_lang('VideoConferenceAddedToTheLinkTool'), 'success');
            } else {
                $message = Display::return_message(get_lang('Error'), 'error');
            }
            break;
        case 'delete_record':
            $bbb->deleteRecord($_GET['id']);
            if ($result) {
                $message = Display::return_message(get_lang('Deleted'), 'success');
            } else {
                $message = Display::return_message(get_lang('Error'), 'error');
            }
            break;
        case 'end':
            $bbb->endMeeting($_GET['id']);
            $message = Display::return_message(
                get_lang('MeetingClosed') . '<br />' . get_lang(
                    'MeetingClosedComment'
                ),
                'success',
                false
            );

            if (file_exists(__DIR__ . '/config.vm.php')) {
                require __DIR__ . '/../../vendor/autoload.php';

                require __DIR__ . '/lib/vm/AbstractVM.php';
                require __DIR__ . '/lib/vm/VMInterface.php';
                require __DIR__ . '/lib/vm/DigitalOceanVM.php';
                require __DIR__ . '/lib/VM.php';

                $config = require __DIR__ . '/config.vm.php';

                $vm = new VM($config);
                $vm->resizeToMinLimit();
            }

            break;
        case 'publish':
            $result = $bbb->publishMeeting($_GET['id']);
            break;
        case 'unpublish':
            $result = $bbb->unpublishMeeting($_GET['id']);
            break;
        default:
            break;
    }
}

$meetings = $bbb->getCourseMeetings();
if (!empty($meetings)) {
    $meetings = array_reverse($meetings);
}
$users_online = $bbb->getUsersOnlineInCurrentRoom();
$status = $bbb->isServerRunning();
$meeting_exists = $bbb->meetingExists(api_get_course_id().'-'.api_get_session_id());
$show_join_button = false;
if ($meeting_exists || $teacher) {
    $show_join_button = true;
}

$tpl->assign('allow_to_edit', $teacher);
$tpl->assign('meetings', $meetings);
$conferenceUrl = $bbb->getConferenceUrl();
$tpl->assign('conference_url', $conferenceUrl);
$tpl->assign('users_online', $users_online);
$tpl->assign('bbb_status', $status);
$tpl->assign('show_join_button', $show_join_button);

$tpl->assign('message', $message);
$listing_tpl = 'bbb/listing.tpl';
$content = $tpl->fetch($listing_tpl);
$tpl->assign('content', $content);$tpl->display_one_col_template();
