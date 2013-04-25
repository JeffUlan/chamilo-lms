<?php
/* For licensing terms, see /license.txt */
/**
 * Classic global.inc.php file now returns a Application object
 * Make sure you read the documentation/installation_guide.html to learn how
 * to configure your VirtualHost to allow for overrides.
 */
/**
 * Inclusion of main setup script
 */
$app = require_once '../main/inc/global.inc.php';
/**
 * In order to execute Chamilo, you need a call to $app->run().
 * $app->run(); shows a page depending of the URL for example when entering 
 * in "/web/index"
 * Chamilo will render the controller IndexController->indexAction(). This is 
 * because a router was assigned at the end of global.inc.php:
 *   $app->get('/index', 'index.controller:indexAction')->bind('index');
 * 
 * The "index.controller:indexAction" string is transformed (due a 
 * controller - service approach) into 
 * ChamiloLMS\Controller\IndexController->indexAction() see more 
 * at: http://silex.sensiolabs.org/doc/providers/service_controller.html
 * The class is loaded automatically (no require_once needed) thanks to the 
 * namespace ChamiloLMS added in Composer.
 * The location of the file is src\ChamiloLMS\Controller\IndexController.php
 * following the PSR-1 standards.
 */
/** @var Application */
$app->run();
//$app['http_cache']->run();
