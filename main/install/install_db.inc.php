<?php
/* For licensing terms, see /license.txt */
/**
 *    Install the Chamilo database
 *    Notice : This script has to be included by index.php
 *
 * @package chamilo.install
 */

/* This page is called only during a NEW chamilo installation */
/* This page can only be access through including from the install script.  */

/**
 * Init checks
 */
if (!defined('SYSTEM_INSTALLATION')) {
    echo 'You are not allowed here!';
    exit;
}

$urlForm = api_add_trailing_slash($urlForm);

switch ($encryptPassForm) {
    case 'md5' :
        $passToStore = md5($passForm);
        break;
    case 'sha1' :
        $passToStore = sha1($passForm);
        break;
    case 'none' :
        $passToStore = $passForm;
        break;
}

$dbPrefixForm = preg_replace('/[^a-zA-Z0-9_\-]/', '', $dbPrefixForm);

$dbNameForm = preg_replace('/[^a-zA-Z0-9_\-]/', '', $dbNameForm);
if (!empty($dbPrefixForm) && strpos($dbNameForm, $dbPrefixForm) !== 0) {
    $dbNameForm = $dbPrefixForm . $dbNameForm;
}

$dbStatsForm = preg_replace('/[^a-zA-Z0-9_\-]/', '', $dbStatsForm);
if (!empty($dbPrefixForm) && strpos($dbStatsForm, $dbPrefixForm) !== 0) {
    $dbStatsForm = $dbPrefixForm . $dbStatsForm;
}

$dbUserForm = preg_replace('/[^a-zA-Z0-9_\-]/', '', $dbUserForm);
if (!empty($dbPrefixForm) && strpos($dbUserForm, $dbPrefixForm) !== 0) {
    $dbUserForm = $dbPrefixForm . $dbUserForm;
}

$mysqlMainDb = $dbNameForm;
if (empty($mysqlMainDb) || $mysqlMainDb == 'mysql' || $mysqlMainDb == $dbPrefixForm) {
    $mysqlMainDb = $dbPrefixForm . 'main';
}

//This parameter is needed to run a command line to install Chamilo using BNPanel + ISPConfig see #1799
if (!defined('CLI_INSTALLATION')) {

    $result = Database::query("SHOW VARIABLES LIKE 'datadir'") or die(Database::error());

    $mysqlRepositorySys = Database::fetch_array($result);
    $mysqlRepositorySys = $mysqlRepositorySys['Value'];

    $create_database = true;
    /** @var \Doctrine\ORM\EntityManager $manager */
    global $manager;
    $databases = $manager->getConnection()->getSchemaManager()->listDatabases();

    if (in_array($mysqlMainDb, $databases)) {
        $create_database = false;
    }

    // Create database
    if ($create_database) {
        $manager->getConnection()->getSchemaManager()->createDatabase($mysqlMainDb);
        /*$sql = "CREATE DATABASE IF NOT EXISTS `$mysqlMainDb`";
        Database::query($sql) or die(Database::error());*/
    }
}

// This parameter is needed to run a command line install of Chamilo (needed for Phing)
if (!defined('CLI_INSTALLATION')) {
    include_once api_get_path(SYS_LANG_PATH) . 'english/trad4all.inc.php';

    if ($languageForm != 'english') {
        include_once api_get_path(SYS_LANG_PATH) . $languageForm . '/trad4all.inc.php';
    }
}

$installation_settings = array();
$installation_settings['{ORGANISATIONNAME}'] = $institutionForm;
$installation_settings['{ORGANISATIONURL}'] = $institutionUrlForm;
$installation_settings['{CAMPUSNAME}'] = $campusForm;
$installation_settings['{PLATFORMLANGUAGE}'] = $languageForm;
$installation_settings['{ALLOWSELFREGISTRATION}'] = trueFalse($allowSelfReg);
$installation_settings['{ALLOWTEACHERSELFREGISTRATION}'] = trueFalse($allowSelfRegProf);
$installation_settings['{ADMINLASTNAME}'] = $adminLastName;
$installation_settings['{ADMINFIRSTNAME}'] = $adminFirstName;
$installation_settings['{ADMINLOGIN}'] = $loginForm;
$installation_settings['{ADMINPASSWORD}'] = $passToStore;
$installation_settings['{ADMINEMAIL}'] = $emailForm;
$installation_settings['{ADMINPHONE}'] = $adminPhoneForm;
$installation_settings['{PLATFORM_AUTH_SOURCE}'] = PLATFORM_AUTH_SOURCE;
$installation_settings['{ADMINLANGUAGE}'] = $languageForm;
$installation_settings['{HASHFUNCTIONMODE}'] = $encryptPassForm;

AddCourse::drop_course_tables();

load_main_database($installation_settings);

locking_settings();

update_dir_and_files_permissions();
