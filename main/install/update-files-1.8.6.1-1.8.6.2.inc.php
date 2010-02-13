<?php
/* See license terms in /dokeos_license.txt */
/**
==============================================================================
* Updates the Dokeos files from version 1.8.6.1 to version 1.8.6.2
* This script operates only in the case of an update, and only to change the
* active version number (and other things that might need a change) in the
* current configuration file.
* @package dokeos.install
==============================================================================
*/
require_once '../inc/lib/main_api.lib.php';
require_once '../inc/lib/fileUpload.lib.php';
require_once '../inc/lib/database.lib.php';

if (defined('DOKEOS_INSTALL') || defined('DOKEOS_COURSE_UPDATE')) {
	// Edit the Dokeos config file
	$file = file('../inc/conf/configuration.php');
	$fh = fopen('../inc/conf/configuration.php', 'w');
	$found_version = false;
	$found_stable = false;
	foreach ($file as $line) {
		$ignore = false;
		if (stristr($line, '$_configuration[\'dokeos_version\']')) {
			$found_version = true;
			$line = '$_configuration[\'dokeos_version\'] = \''.$new_version.'\';'."\r\n";
		} elseif(stristr($line, '$_configuration[\'dokeos_stable\']')) {
			$found_stable = true;
			$line = '$_configuration[\'dokeos_stable\'] = '.($new_version_stable?'true':'false').';'."\r\n";
		} elseif(stristr($line,'$userPasswordCrypted')) {
			$line = '$userPasswordCrypted 									= \''.($userPasswordCrypted).'\';'."\r\n";
		} elseif(stristr($line, '?>')) {
			//ignore the line
			$ignore = true;
		}
		if (!$ignore) {
			fwrite($fh, $line);
		}
	}
	if (!$found_version) {
		fwrite($fh, '$_configuration[\'dokeos_version\'] = \''.$new_version.'\';'."\r\n");
	}
	if (!$found_stable) {
		fwrite($fh, '$_configuration[\'dokeos_stable\'] = '.($new_version_stable?'true':'false').';'."\r\n");
	}
	fwrite($fh, '?>');
	fclose($fh);

	$sys_course_path = $pathForm.'courses/';

	$perm = api_get_permissions_for_new_directories();

	//$old_umask = umask(0); // This function is not thread-safe.

	$link = mysql_connect($dbHostForm, $dbUsernameForm, $dbPassForm);
	mysql_select_db($dbNameForm, $link);
	$db_name = $dbNameForm;
	$sql = "SELECT * FROM $db_name.course";
	error_log('Getting courses for files updates: '.$sql, 0);
	$result = mysql_query($sql);

	while ($courses_directories = mysql_fetch_array($result)) {
		$currentCourseRepositorySys = $sys_course_path.$courses_directories['directory'].'/';
		//upload > announcements
		if (!is_dir($currentCourseRepositorySys."upload/announcements")){
			mkdir($currentCourseRepositorySys."upload/announcements", $perm);
		}

		//upload > announcements > images
		if (!is_dir($currentCourseRepositorySys."upload/announcements/images")) {
			mkdir($currentCourseRepositorySys."upload/announcements/images", $perm);
		}
	}

	////create a specific directory for global thumbails
	//home > default_platform_document > template_thumb
	if (!is_dir($pathForm.'home/default_platform_document/template_thumb')) {
		mkdir($pathForm.'home/default_platform_document/template_thumb', $perm);
	}

} else {

	echo 'You are not allowed here !';

}
