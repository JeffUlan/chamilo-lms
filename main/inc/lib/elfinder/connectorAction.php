<?php
/* For licensing terms, see /license.txt */

require_once '../../global.inc.php';

use Chamilo\CoreBundle\Component\Editor\Connector;
use Chamilo\CoreBundle\Component\Editor\Finder;

error_reporting(-1);
/** @var Connector $connector */
$connector = new Connector();
$driverList = array(
    'PersonalDriver',
    'CourseDriver',
    //'CourseUserDriver',
    //'HomeDriver'
);
$connector->setDriverList($driverList);

$operations = $connector->getOperations();

// Run elFinder
$finder = new Finder($operations);
$elFinderConnector = new \elFinderConnector($finder);
$elFinderConnector->run();
