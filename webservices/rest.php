<?php
/* For licensing terms, see /license.txt */
/**
 * Controller for REST request
 * @author Angel Fernando Quiroz Campos <angel.quiroz@beeznest.com>
 * @package chamilo.plugin.tour
 */
/* Require libs and classes */
require_once '../main/inc/global.inc.php';

/* Manage actions */
$json = array();

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'nothing';
$username = isset($_POST['username']) ? Security::remove_XSS($_POST['username']) : null;
$apiKey = isset($_POST['api_key']) ? Security::remove_XSS($_POST['api_key']) : null;

switch ($action) {
    case 'loginNewMessages':
        $password = isset($_POST['password']) ? Security::remove_XSS($_POST['password']) : null;

        if (MessagesWebService::isValidUser($username, $password)) {
            $webService = new MessagesWebService();

            $apiKey = $webService->getApiKey($username);

            $json = array(
                'status' => true,
                'apiKey' => $apiKey
            );
        } else {
            $json = array(
                'status' => false
            );
        }
        break;
    case 'countNewMessages':
        if (MessagesWebService::isValidApiKey($username, $apiKey)) {
            $webService = new MessagesWebService();
            $webService->setApiKey($apiKey);

            $lastId = isset($_POST['last']) ? $_POST['last'] : 0;

            $count = $webService->countNewMessages($username, $lastId);

            $json = array(
                'status' => true,
                'count' => $count
            );
        } else {
            $json = array(
                'status' => false
            );
        }
        break;
    case 'getNewMessages':
        if (MessagesWebService::isValidApiKey($username, $apiKey)) {
            $webService = new MessagesWebService();
            $webService->setApiKey($apiKey);

            $lastId = isset($_POST['last']) ? $_POST['last'] : 0;

            $messages = $webService->getNewMessages($username, $lastId);

            $json = $messages;
        } else {
            $json = array(
                'status' => true,
                'status' => false
            );
        }
        break;
    default:
}

/* View */
echo json_encode($json);
