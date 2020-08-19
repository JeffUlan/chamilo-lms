<?php
/* For licensing terms, see /license.txt */

use Chamilo\PluginBundle\WhispeakAuth\Controller\AuthenticationController;
use Chamilo\PluginBundle\WhispeakAuth\Controller\EnrollmentController;

require_once __DIR__.'/../../../main/inc/global.inc.php';

$action = isset($_POST['action']) ? $_POST['action'] : 'enrollment';
$isEnrollment = 'enrollment' === $action;
$isAuthentify = 'authentify' === $action;

$isAllowed = false;

if ($isEnrollment) {
    api_block_anonymous_users(false);

    $controller = new EnrollmentController();

    try {
        $controller->ajax();
    } catch (Exception $exception) {
        WhispeakAuthPlugin::displayNotAllowedMessage(
            $exception->getMessage()
        );
    }
    die;
}

if ($isAuthentify) {
    $controller = new AuthenticationController();

    try {
        $controller->ajax();
    } catch (Exception $exception) {
        echo Display::return_message($exception->getMessage(), 'error', false);
    }
}
