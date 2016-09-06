<?php
/* For licensing terms, see /license.txt */

require_once '../../inc/global.inc.php';

$hash = isset($_REQUEST['hash']) ? $_REQUEST['hash'] : null;

if ($hash) {
    $hashParams = Rest::decodeParams($hash);

    foreach ($hashParams as $key => $value) {
        $_REQUEST[$key] = $value;
    }
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
$username = isset($_REQUEST['username']) ? Security::remove_XSS($_REQUEST['username']) : null;
$apiKey = isset($_REQUEST['api_key']) ? Security::remove_XSS($_REQUEST['api_key']) : null;

$restResponse = new RestResponse();

try {
    /** @var Rest $restApi */
    $restApi = $apiKey ? Rest::validate($username, $apiKey) : null;

    switch ($action) {
        case Rest::ACTION_AUTH:
            Rest::init();

            $password = isset($_POST['password']) ? $_POST['password'] : null;

            $isValid = Rest::isValidUser($username, $password);

            if (!$isValid) {
                throw new Exception(get_lang('InvalideUserDetected'));
            }

            $restResponse->setData([
                'url' => api_get_path(WEB_PATH),
                'apiKey' => Rest::findUserApiKey($username, Rest::SERVIVE_NAME),
                'gcmSenderId' => api_get_setting('messaging_gdc_project_number')
            ]);
            break;

        case Rest::ACTION_GCM_ID:
            $gcmId = isset($_POST['registration_id']) ? Security::remove_XSS($_POST['registration_id']) : null;

            $restApi->setGcmId($gcmId);

            $restResponse->setData(['status' => true]);
            break;

        case Rest::ACTION_USER_MESSAGES:
            $lastMessageId = isset($_POST['last']) ? intval($_POST['last']) : 0;

            $messages = $restApi->getUserMessages($lastMessageId);

            $restResponse->setData($messages);
            break;

        case Rest::ACTION_USER_COURSES:
            $courses = $restApi->getUserCourses();

            $restResponse->setData($courses);
            break;

        case Rest::ACTION_COURSE_INFO:
            $courseId = isset($_POST['c_id']) ? Security::remove_XSS($_POST['c_id']) : 0;
            $courseInfo = $restApi->getCourseInfo($courseId);

            $restResponse->setData($courseInfo);
            break;

        case Rest::ACTION_COURSE_DESCRIPTIONS:
            $courseId = isset($_POST['c_id']) ? Security::remove_XSS($_POST['c_id']) : 0;

            $descriptions = $restApi->getCourseDescriptions($courseId);

            $restResponse->setData($descriptions);
            break;

        case Rest::ACTION_COURSE_DOCUMENTS:
            $courseId = isset($_POST['c_id']) ? Security::remove_XSS($_POST['c_id']) : 0;
            $directoryId = isset($_POST['dir_id']) ? Security::remove_XSS($_POST['dir_id']) : null;

            $documents = $restApi->getCourseDocuments($courseId, $directoryId);

            $restResponse->setData($documents);
            break;

        case Rest::ACTION_COURSE_ANNOUNCEMENTS:
            $courseId = isset($_POST['c_id']) ? Security::remove_XSS($_POST['c_id']) : 0;

            $announcements = $restApi->getCourseAnnouncements($courseId);

            $restResponse->setData($announcements);
            break;

        case Rest::ACTION_COURSE_ANNOUNCEMENT:
            $courseId = isset($_POST['c_id']) ? Security::remove_XSS($_POST['c_id']) : 0;
            $announcementId = isset($_POST['a_id']) ? Security::remove_XSS($_POST['a_id']) : 0;

            $announcement = $restApi->getCourseAnnouncement($announcementId, $courseId);

            $restResponse->setData($announcement);
            break;

        case Rest::ACTION_COURSE_AGENDA:
            $courseId = isset($_POST['c_id']) ? Security::remove_XSS($_POST['c_id']) : 0;

            $agenda = $restApi->getCourseAgenda($courseId);

            $restResponse->setData($agenda);
            break;

        case Rest::ACTION_COURSE_NOTEBOOKS:
            $courseId = isset($_POST['c_id']) ? Security::remove_XSS($_POST['c_id']) : 0;

            $notebooks = $restApi->getCourseNotebooks($courseId);

            $restResponse->setData($notebooks);
            break;

        case Rest::ACTION_COURSE_FORUM_CATEGORIES:
            $courseId = isset($_POST['c_id']) ? Security::remove_XSS($_POST['c_id']) : 0;

            $forums = $restApi->getCourseForumCategories($courseId);

            $restResponse->setData($forums);
            break;

        case Rest::ACTION_COURSE_FORUM:
            $forumId = isset($_POST['forum']) ? Security::remove_XSS($_POST['forum']) : 0;

            $forum = $restApi->getCourseForum($forumId);

            $restResponse->setData($forum);
            break;

        case Rest::ACTION_COURSE_FORUM_THREAD:
            $threadId = isset($_POST['thread']) ? Security::remove_XSS($_POST['thread']) : 0;

            $thread = $restApi->getCourseForumThread($threadId);

            $restResponse->setData($thread);
            break;

        case Rest::ACTION_PROFILE:
            $userInfo = $restApi->getUserProfile();

            $restResponse->setData($userInfo);
            break;

        case Rest::ACTION_COURSE_LEARNPATHS:
            $courseId = isset($_POST['c_id']) ? Security::remove_XSS($_POST['c_id']) : 0;

            $data = $restApi->getCourseLearnPaths($courseId);

            $restResponse->setData($data);
            break;

        case Rest::ACTION_COURSE_LEARNPATH:
            $lpId = isset($_REQUEST['lp_id']) ? intval($_REQUEST['lp_id']) : 0;
            $cidReq = isset($_REQUEST['cidReq']) ? Security::remove_XSS($_REQUEST['cidReq']) : 0;

            $restApi->showLearningPath($lpId, $cidReq);
            break;

        case Rest::ACTION_SAVE_FORUM_POST:
            if (
                empty($_POST['title']) || empty($_POST['text']) || empty($_POST['thread']) || empty($_POST['forum']) ||
                empty($_POST['notify']) || empty($_POST['parent']) || empty($_POST['course'])
            ) {
                throw new Exception(get_lang('NoData'));
            }

            $courseId = intval($_POST['course']);

            $postValues = [
                'post_title' => $_POST['title'],
                'post_text' => nl2br($_POST['text']),
                'thread_id' => $_POST['thread'],
                'forum_id' => $_POST['forum'],
                'post_notification' => $_POST['notify'],
                'post_parent_id' => $_POST['parent']
            ];

            $data = $restApi->saveForumPost($postValues, $forumId, $courseId);

            $restResponse->setData($data);
            break;

        default:
            throw new Exception(get_lang('InvalidAction'));
    }
} catch (Exception $exeption) {
    $restResponse->setErrorMessage(
        $exeption->getMessage()
    );

}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

echo $restResponse->format();
