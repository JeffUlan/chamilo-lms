<?php
/* For licensing terms, see /license.txt */

use Chamilo\UserBundle\Entity\User;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Wav;

$cidReset = true;

require_once __DIR__.'/../../../main/inc/global.inc.php';

$action = isset($_POST['action']) ? $_POST['action'] : 'enrollment';
$isEnrollment = 'enrollment' === $action;
$isAuthentify = 'authentify' === $action;

$isAllowed = true;

if ($isEnrollment) {
    api_block_anonymous_users(false);

    $isAllowed = !empty($_FILES['audio']);
} elseif ($isAuthentify) {
    $isAllowed = !empty($_POST['username']) && !empty($_FILES['audio']);
}

if (!$isAllowed) {
    echo Display::return_message(get_lang('NotAllowed'), 'error');

    exit;
}

$plugin = WhispeakAuthPlugin::create();

if ($isAuthentify) {
    $em = Database::getManager();
    /** @var User|null $user */
    $user = $em->getRepository('ChamiloUserBundle:User')->findOneBy(['username' => $_POST['username']]);
} else {
    /** @var User $user */
    $user = api_get_user_entity(api_get_user_id());
}

if (empty($user)) {
    echo Display::return_message(get_lang('NoUser'), 'error');

    exit;
}

$path = api_upload_file('whispeakauth', $_FILES['audio'], $user->getId());

if (false === $path) {
    echo Display::return_message(get_lang('UploadError'), 'error');

    exit;
}

$originFullPath = api_get_path(SYS_UPLOAD_PATH).'whispeakauth'.$path['path_to_save'];
$directory = dirname($originFullPath);
$newFullPath = $directory.'/audio.wav';

try {
    $ffmpeg = FFMpeg::create();

    $audio = $ffmpeg->open($originFullPath);
    $audio->save(new Wav(), $newFullPath);
} catch (Exception $exception) {
    echo Display::return_message($exception->getMessage(), 'error');

    exit;
}

if ($isEnrollment) {
    $result = $plugin->requestEnrollment($user, $newFullPath);

    if (empty($result)) {
        echo Display::return_message($plugin->get_lang('EnrollmentFailed'));

        exit;
    }

    $plugin->saveEnrollment($user, $result['uid']);

    echo Display::return_message($plugin->get_lang('EnrollmentSuccess'), 'success');

    exit;
}

if ($isAuthentify) {
    $result = $plugin->requestAuthentify($user, $newFullPath);

    if (empty($result)) {
        echo Display::return_message($plugin->get_lang('AuthentifyFailed'), 'error');

        exit;
    }

    $success = (bool) $result['audio'][0]['result'];

    if (!$success) {
        echo Display::return_message($plugin->get_lang('TryAgain'), 'warning');

        exit;
    }

    $loggedUser = [
        'user_id' => $user->getId(),
        'status' => $user->getStatus(),
        'uidReset' => true,
    ];

    ChamiloSession::write('_user', $loggedUser);
    Login::init_user($user->getId(), true);

    echo Display::return_message($plugin->get_lang('AuthentifySuccess'), 'success');
    echo '<script>window.location.href = "'.api_get_path(WEB_PATH).'";</script>';

    exit;
}
