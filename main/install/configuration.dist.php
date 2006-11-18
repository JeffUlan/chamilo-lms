<?php
# DOKEOS version {DOKEOS_VERSION}
# File generated by /install/index.php script - {DATE_GENERATED}
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
/*
==============================================================================
		Configuration of virtual campus

This file contains a list of variables that can be modified by the campus
site administrator. Pay attention when changing these variables, some changes
can cause Dokeos to stop working.
If you changed some settings and want to restore them, please have a look at
claro_main.conf.dist.php. That file is an exact copy of the config file at
install time.
==============================================================================
*/

/**
 * @todo change these into a $_configuration array. $_configuration will use only the bare essential variables
 * 		for configuring the platform (paths, database connections, ...). Changing a $_configuration variable
 * 		CAN break the installation.
 * 		Besides the $_configuration array there is also a $_settings array that contains variables that 
 * 		can be changed and will not break the platform. 
 * 		Some of the variables that are used here can move to the $_settings array (and thus be stored in the database)
 * 		example: $is_trackingEnabled (assuming that the install script creates the necessary tables anyway.
 * 				 $phpMyAdminPath
 * 	
 * 		@todo use more obvious names for the variables and respect the code guidelines
 */

//============================================================================
//   MYSQL connection settings
//============================================================================
// Your MySQL server
$dbHost         = '{DATABASE_HOST}'; 		
// Your MySQL username
$dbLogin        = '{DATABASE_USER}'; 		
// Your MySQL password
$dbPass         = '{DATABASE_PASSWORD}'; 	

//============================================================================
//   Database settings
//============================================================================
// Is tracking enabled?
$is_trackingEnabled = {TRACKING_ENABLED};
// Is single database enabled (DO NOT MODIFY THIS)
$singleDbEnabled    = {SINGLE_DATABASE}; 		
// Prefix for course tables (IF NOT EMPTY, can be replaced by another prefix, 
// else leave empty)
$courseTablePrefix  = '{COURSE_TABLE_PREFIX}'; 	
// Separator between database and table name (DO NOT MODIFY THIS)
$dbGlu              = '{DATABASE_GLUE}'; 
// prefix all created bases (for courses) with this string		
$dbNamePrefix       = '{DATABASE_PREFIX}'; 		
// main Dokeos database
$mainDbName         = '{DATABASE_MAIN}'; 
// stats Dokeos database
$statsDbName        ='{DATABASE_STATS}'; 
// Scorm Dokeos database
$scormDbName        ='{DATABASE_SCORM}'; 
// User Personal Database (where all the personal stuff of the user is stored 
// (personal agenda items, course sorting)
$user_personal_database   ='{DATABASE_PERSONAL}'; 

//============================================================================
//   Directory settings
//============================================================================
// URL to the root of your Dokeos installation
$rootWeb                     = '{ROOT_WEB}';
// Path to the root of your Dokeos installation
$rootSys                     = '{ROOT_SYS}';
// Path from your WWW-root to the root of your Dokeos installation
$urlAppend                   = '{URL_APPEND_PATH}';
// Directory of the Dokeos code
$clarolineRepositoryAppend   = "main/";
// Directory to store all course-related files
$coursesRepositoryAppend     = "courses/";
// Directory of the admin-area
$rootAdminAppend             = "admin/";
// Do not change the following values
// @todo should be moved to api_get_path
$clarolineRepositorySys      = $rootSys.$clarolineRepositoryAppend;
$clarolineRepositoryWeb      = $rootWeb.$clarolineRepositoryAppend;
$coursesRepositorySys        = $rootSys.$coursesRepositoryAppend;
$coursesRepositoryWeb        = $rootWeb.$coursesRepositoryAppend;
$rootAdminSys                = $clarolineRepositorySys.$rootAdminAppend;
$rootAdminWeb                = $clarolineRepositoryWeb.$rootAdminAppend;
// directory to store archived courses
$archiveDirName              = "archive";
// change this to a place out of web if you can
$garbageRepositorySys        = '{GARBAGE_DIR}'; 
// URL to your phpMyAdmin installation. 
// If not empty, a link will be available in the Platform Administration
$phpMyAdminPath              = '';

//============================================================================
//   Login modules settings
//============================================================================
// For new login module
// Uncomment these lines to activate ldap
// $extAuthSource["ldap"]["login"]=$clarolineRepositorySys."auth/ldap/login.php";
// $extAuthSource["ldap"]["newUser"]=$clarolineRepositorySys."auth/ldap/newUser.php";

//============================================================================
//   Language settings
//============================================================================
// Available Languages : look at the "lang" directory
$platformLanguage   = '{PLATFORM_LANGUAGE}';
$language           = $platformLanguage;

//============================================================================
//   Misc. settings
//============================================================================
// Verbose backup
$verboseBackup      = false;
// security word for password recovery
$security_key       = '{SECURITY_KEY}';
// Settings for new and future features
$userPasswordCrypted          = {ENCRYPT_PASSWORD};
// You may have to restart your web server if you change this
$storeSessionInDb             = false; 


$openoffice_conf = array();
$openoffice_conf['javacommand'] = 'java';
$openoffice_conf['host'] = 'ns6077.ovh.net';
$openoffice_conf['port'] = '2002';
$openoffice_conf['ftpuser'] = '****';
$openoffice_conf['ftppasswd'] = '****';




//============================================================================
//   Plugin settings
//============================================================================
// plugins arrays
// @todo remove this because this is now handled through the dokeos config settings
$plugins["main_menu"]        = array();
$plugins["main_menu_logged"] = array();
$plugins["banner"]           = array();
// To load a new plugin, add a line like this
//     $plugins["main_menu"][] = "my_plugin";
// where "my_plugin" is the directory where your plugin is in main/plugin
// main_menu_logged is the same as main_menu for when a user is logged in

// PLUGINS INCLUDED BY DEFAULT
$plugins["banner"][] = 'messages';
?>