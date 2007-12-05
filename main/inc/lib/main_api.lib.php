<?php
/*
===============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Hugues Peeters
	Copyright (c) Christophe Gesche
	Copyright (c) Roan Embrechts (Vrije Universiteit Brussel)
	Copyright (c) Patrick Cool
	Copyright (c) Olivier Brouckaert
	Copyright (c) Toon Van Hoecke
	Copyright (c) Denes Nagy

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
===============================================================================
*/
/**
==============================================================================
*	This is a code library for Dokeos.
*	It is included by default in every Dokeos file
*	(through including the global.inc.php)
*
*	@package dokeos.library
==============================================================================
*/
/*
==============================================================================
		CONSTANTS
==============================================================================
*/

//USER STATUS CONSTANTS
/** global status of a user: student */
define('STUDENT', 5);
/** global status of a user: course manager */
define('COURSEMANAGER', 1);

//COURSE VISIBILITY CONSTANTS
/** only visible for course admin */
define('COURSE_VISIBILITY_CLOSED', 0);
/** only visible for users registered in the course*/
define('COURSE_VISIBILITY_REGISTERED', 1);
/** open for all registered users on the platform */
define('COURSE_VISIBILITY_OPEN_PLATFORM', 2);
/** open for the whole world */
define('COURSE_VISIBILITY_OPEN_WORLD', 3);

define('SUBSCRIBE_ALLOWED', 1);
define('SUBSCRIBE_NOT_ALLOWED', 0);
define('UNSUBSCRIBE_ALLOWED', 1);
define('UNSUBSCRIBE_NOT_ALLOWED', 0);

//CONSTANTS FOR api_get_path FUNCTION
define('WEB_PATH', 'WEB_PATH');
define('SYS_PATH', 'SYS_PATH');
define('REL_PATH', 'REL_PATH');
define('WEB_COURSE_PATH', 'WEB_COURSE_PATH');
define('SYS_COURSE_PATH', 'SYS_COURSE_PATH');
define('REL_COURSE_PATH', 'REL_COURSE_PATH');
define('REL_CLARO_PATH', 'REL_CLARO_PATH');
define('WEB_CODE_PATH', 'WEB_CODE_PATH');
define('SYS_CODE_PATH', 'SYS_CODE_PATH');
define('SYS_LANG_PATH', 'SYS_LANG_PATH');
define('WEB_IMG_PATH', 'WEB_IMG_PATH');
define('WEB_CSS_PATH', 'WEB_CSS_PATH');
define('GARBAGE_PATH', 'GARBAGE_PATH');
define('SYS_PLUGIN_PATH', 'SYS_PLUGIN_PATH');
define('PLUGIN_PATH', 'PLUGIN_PATH');
define('WEB_PLUGIN_PATH', 'WEB_PLUGIN_PATH');
define('SYS_ARCHIVE_PATH', 'SYS_ARCHIVE_PATH');
define('INCLUDE_PATH', 'INCLUDE_PATH');
define('LIBRARY_PATH', 'LIBRARY_PATH');
define('CONFIGURATION_PATH', 'CONFIGURATION_PATH');

//CONSTANTS defining all tools, using the english version
define('TOOL_DOCUMENT', 'document');
define('TOOL_HOTPOTATOES', 'hotpotatoes');
define('TOOL_CALENDAR_EVENT', 'calendar_event');
define('TOOL_LINK', 'link');
define('TOOL_COURSE_DESCRIPTION', 'course_description');
define('TOOL_LEARNPATH', 'learnpath');
define('TOOL_ANNOUNCEMENT', 'announcement');
define('TOOL_FORUM', 'forum');
define('TOOL_THREAD', 'thread');
define('TOOL_POST', 'post');
define('TOOL_DROPBOX', 'dropbox');
define('TOOL_QUIZ', 'quiz');
define('TOOL_USER', 'user');
define('TOOL_GROUP', 'group');
define('TOOL_BLOGS', 'blog_management'); // Smartblogs (Kevin Van Den Haute :: kevin@develop-it.be)
define('TOOL_CHAT', 'chat');
define('TOOL_CONFERENCE', 'conference');
define('TOOL_STUDENTPUBLICATION', 'student_publication');
define('TOOL_TRACKING', 'tracking');
define('TOOL_HOMEPAGE_LINK', 'homepage_link');
define('TOOL_COURSE_SETTING', 'course_setting');
define('TOOL_BACKUP', 'backup');
define('TOOL_COPY_COURSE_CONTENT', 'copy_course_content');
define('TOOL_RECYCLE_COURSE', 'recycle_course');
define('TOOL_COURSE_HOMEPAGE', 'course_homepage');
define('TOOL_COURSE_RIGHTS_OVERVIEW', 'course_rights');
define('TOOL_UPLOAD','file_upload');
define('TOOL_COURSE_MAINTENANCE','course_maintenance');
define('TOOL_VISIO','visio');
define('TOOL_VISIO_CONFERENCE','visio_conference');
define('TOOL_VISIO_CLASSROOM','visio_classroom');
define('TOOL_SURVEY','survey');

// CONSTANTS defining dokeos sections
define('SECTION_CAMPUS', 'mycampus');
define('SECTION_COURSES', 'mycourses');
define('SECTION_MYPROFILE', 'myprofile');
define('SECTION_MYAGENDA', 'myagenda');
define('SECTION_COURSE_ADMIN', 'course_admin');
define('SECTION_PLATFORM_ADMIN', 'platform_admin');

// CONSTANT name for local authentication source
define('PLATFORM_AUTH_SOURCE', 'platform');

//CONSTANT defining the default HotPotatoes files directory
define('DIR_HOTPOTATOES','/HotPotatoes_files');

/*
==============================================================================
		PROTECTION FUNCTIONS
		use these to protect your scripts
==============================================================================
*/
/**
* Function used to protect a course script.
* The function blocks access when
* - there is no $_SESSION["_course"] defined; or
* - $is_allowed_in_course is set to false (this depends on the course
* visibility and user status).
*
* This is only the first proposal, test and improve!
* @param	boolean	Option to print headers when displaying error message. Default: false
* @todo replace global variable
* @author Roan Embrechts
*/
function api_protect_course_script($print_headers=false)
{
	global $is_allowed_in_course;
	if (!isset ($_SESSION["_course"]) || !$is_allowed_in_course)
	{
		api_not_allowed($print_headers);
	}
}




/**
* Function used to protect an admin script.
* The function blocks access when the user has no platform admin rights.
* This is only the first proposal, test and improve!
*
* @author Roan Embrechts
*/
function api_protect_admin_script()
{
	if (!api_is_platform_admin())
	{
		include (api_get_path(INCLUDE_PATH)."header.inc.php");
		api_not_allowed();
	}
}

/**
* Function used to prevent anonymous users from accessing a script.
*
* @author Roan Embrechts
*/
function api_block_anonymous_users()
{
	global $_user;

	if (!(isset ($_user['user_id']) && $_user['user_id']) || api_is_anonymous($_user['user_id'],true))
	{
		include (api_get_path(INCLUDE_PATH)."header.inc.php");
		api_not_allowed();
	}
}

/*
==============================================================================
		ACCESSOR FUNCTIONS
		don't access kernel variables directly,
		use these functions instead
==============================================================================
*/
/**
*	@return an array with the navigator name and version
*/
function api_get_navigator()
{
	$navigator = 'Unknown';
	$version = 0;
	if (strstr($_SERVER['HTTP_USER_AGENT'], 'Opera'))
	{
		$navigator = 'Opera';
		list (, $version) = explode('Opera', $_SERVER['HTTP_USER_AGENT']);
	}
	elseif (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
	{
		$navigator = 'Internet Explorer';
		list (, $version) = explode('MSIE', $_SERVER['HTTP_USER_AGENT']);
	}
	elseif (strstr($_SERVER['HTTP_USER_AGENT'], 'Gecko'))
	{
		$navigator = 'Mozilla';
		list (, $version) = explode('; rv:', $_SERVER['HTTP_USER_AGENT']);
	}
	elseif (strstr($_SERVER['HTTP_USER_AGENT'], 'Netscape'))
	{
		$navigator = 'Netscape';
		list (, $version) = explode('Netscape', $_SERVER['HTTP_USER_AGENT']);
	}
	$version = doubleval($version);
	if (!strstr($version, '.'))
	{
		$version = number_format(doubleval($version), 1);
	}
	return array ('name' => $navigator, 'version' => $version);
}
/**
*	@return True if user selfregistration is allowed, false otherwise.
*/
function api_is_self_registration_allowed()
{
	return $GLOBALS["allowSelfReg"];
}
/**
*	Returns a full path to a certain Dokeos area, which you specify
*	through a parameter.
*
*	See $_configuration['course_folder'] in the configuration.php
*	to alter the WEB_COURSE_PATH and SYS_COURSE_PATH parameters.
*
*	@param one of the following constants:
*	WEB_PATH, SYS_PATH, REL_PATH, WEB_COURSE_PATH, SYS_COURSE_PATH,
*	REL_COURSE_PATH, REL_CLARO_PATH, WEB_CODE_PATH, SYS_CODE_PATH,
*	SYS_LANG_PATH, WEB_IMG_PATH, GARBAGE_PATH, PLUGIN_PATH, SYS_ARCHIVE_PATH,
*	INCLUDE_PATH, LIBRARY_PATH, CONFIGURATION_PATH
*
* 	@example assume that your server root is /var/www/ dokeos is installed in a subfolder dokeos/ and the URL of your campus is http://www.mydokeos.com
* 	The other configuration paramaters have not been changed.
* 	The different api_get_paths will give
* 	WEB_PATH			http://www.mydokeos.com
* 	SYS_PATH			/var/www/
* 	REL_PATH			dokeos/
* 	WEB_COURSE_PATH		http://www.mydokeos.com/courses/
* 	SYS_COURSE_PATH		/var/www/dokeos/courses/
*	REL_COURSE_PATH
* 	REL_CLARO_PATH
* 	WEB_CODE_PATH
* 	SYS_CODE_PATH
* 	SYS_LANG_PATH
* 	WEB_IMG_PATH
* 	GARBAGE_PATH
* 	PLUGIN_PATH
* 	SYS_ARCHIVE_PATH
*	INCLUDE_PATH
* 	LIBRARY_PATH
* 	CONFIGURATION_PATH
*/
function api_get_path($path_type)
{
	global $_configuration;

	switch ($path_type)
	{
		case WEB_PATH :
			// example: http://www.mydokeos.com/ or http://www.mydokeos.com/portal/ if you're using
			// a subdirectory of your document root for Dokeos
			if(substr($_configuration['root_web'],-1) == '/')
			{
				return $_configuration['root_web'];
			}
			else
			{
				return $_configuration['root_web'].'/';
			}
			break;

		case SYS_PATH :
			// example: /var/www/
			if(substr($_configuration['root_sys'],-1) == '/')
			{
				return $_configuration['root_sys'];
			}
			else
			{
				return $_configuration['root_sys'].'/';				
			}
			break;

		case REL_PATH :
			// example: dokeos/
			if (substr($_configuration['url_append'], -1) === '/')
			{
				return $_configuration['url_append'];
			}
			else
			{
				return $_configuration['url_append'].'/';
			}
			break;

		case WEB_COURSE_PATH :
			// example: http://www.mydokeos.com/courses/
			return $_configuration['root_web'].$_configuration['course_folder'];
			break;

		case SYS_COURSE_PATH :
			// example: /var/www/dokeos/courses/
			return $_configuration['root_sys'].$_configuration['course_folder'];
			break;

		case REL_COURSE_PATH :
			// example: courses/ or dokeos/courses/
			return api_get_path(REL_PATH).$_configuration['course_folder'];
			break;
		case REL_CLARO_PATH :
			// example: main/ or dokeos/main/
			return api_get_path(REL_PATH).$_configuration['code_append'];
			break;
		case WEB_CODE_PATH :
			// example: http://www.mydokeos.com/main/
			return $GLOBALS['clarolineRepositoryWeb'];
			break;
		case SYS_CODE_PATH :
			// example: /var/www/dokeos/main/
			return $GLOBALS['clarolineRepositorySys'];
			break;
		case SYS_LANG_PATH :
			// example: /var/www/dokeos/main/lang/
			return api_get_path(SYS_CODE_PATH).'lang/';
			break;
		case WEB_IMG_PATH :
			// example: http://www.mydokeos.com/main/img/
			return api_get_path(WEB_CODE_PATH).'img/';
			break;
		case GARBAGE_PATH :
			// example: /var/www/dokeos/main/garbage/
			return $GLOBALS['garbageRepositorySys'];
			break;
		case SYS_PLUGIN_PATH :
			// example: /var/www/dokeos/plugin/
			return api_get_path(SYS_PATH).'plugin/';
			break;
		case WEB_PLUGIN_PATH :
			// example: http://www.mydokeos.com/plugin/
			return api_get_path(WEB_PATH).'plugin/';
			break;
		case SYS_ARCHIVE_PATH :
			// example: /var/www/dokeos/archive/
			return api_get_path(SYS_PATH).'archive/';
			break;
		case INCLUDE_PATH :
			// Generated by main/inc/global.inc.php 
			// example: /var/www/dokeos/main/inc/
			return str_replace('\\', '/', $GLOBALS['includePath']).'/';
			break;
		case LIBRARY_PATH :
			// example: /var/www/dokeos/main/inc/lib/
			return api_get_path(INCLUDE_PATH).'lib/';
			break;
		case WEB_LIBRARY_PATH :
			// example: http://www.mydokeos.com/main/inc/lib/
			return api_get_path(WEB_CODE_PATH).'inc/lib/';
			break;
		case CONFIGURATION_PATH :
			// example: /var/www/dokeos/main/inc/conf/
			return api_get_path(INCLUDE_PATH).'conf/';
			break;
		default :
			return;
			break;
	}
}

/**
* This function returns the id of the user which is stored in the $_user array.
*
* @example The function can be used to check if a user is logged in
* 			if (api_get_user_id())
* @return integer the id of the current user
*/
function api_get_user_id()
{
	if(empty($GLOBALS['_user']['user_id']))
	{
		return 0;
	}
	return $GLOBALS['_user']['user_id'];
}
/**
 * @param $user_id (integer): the id of the user
 * @return $user_info (array): user_id, lastname, firstname, username, email, ...
 * @author Patrick Cool <patrick.cool@UGent.be>
 * @version 21 September 2004
 * @desc find all the information about a user. If no paramater is passed you find all the information about the current user.
*/
function api_get_user_info($user_id = '')
{
	global $tbl_user;
	if ($user_id == '')
	{
		return $GLOBALS["_user"];
	}
	else
	{
		$sql = "SELECT * FROM ".Database :: get_main_table(TABLE_MAIN_USER)." WHERE user_id='".mysql_real_escape_string($user_id)."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		if(mysql_num_rows($result) > 0)
		{
			$result_array = mysql_fetch_array($result);
			// this is done so that it returns the same array-index-names
			// ideally the names of the fields of the user table are renamed so that they match $_user (or vice versa)
			// $_user should also contain every field of the user table (except password maybe). This would make the
			// following lines obsolete (and the code cleaner and slimmer !!!
			$user_info['firstName'] = $result_array['firstname'];
			$user_info['lastName'] = $result_array['lastname'];
			$user_info['mail'] = $result_array['email'];
			$user_info['picture_uri'] = $result_array['picture_uri'];
			$user_info['user_id'] = $result_array['user_id'];
			$user_info['official_code'] = $result_array['official_code'];
			$user_info['status'] = $result_array['status'];
			$user_info['auth_source'] = $result_array['auth_source'];
			$user_info['username'] = $result_array['username'];
			return $user_info;
		}
		return false;
	}
}
/**
 * Returns the current course id (integer)
*/
function api_get_course_id()
{
	return $GLOBALS["_cid"];
}
/**
 * Returns the current course directory
 *
 * This function relies on api_get_course_info()
 * @return	string	The directory where the course is located inside the Dokeos "courses" directory
 * @author	Yannick Warnier <yannick.warnier@dokeos.com>
*/
function api_get_course_path()
{
	$info = api_get_course_info();
	return $info['path'];
}
/**
 * Gets a course setting from the current course_setting table. Try always using integer values.
 * @param	string	The name of the setting we want from the table
 * @return	mixed	The value of that setting in that table. Return -1 if not found.
 */
function api_get_course_setting($setting_name)
{
	$table = Database::get_course_table(TABLE_COURSE_SETTING);
	$setting_name = mysql_real_escape_string($setting_name);
	$sql = "SELECT * FROM $table WHERE variable = '$setting_name'";
	$res = api_sql_query($sql,__FILE__,__LINE__);
	if(Database::num_rows($res)==1){
		$row = Database::fetch_array($res);
		return $row['value'];
	}
	return -1;
}
/**
 * Gets an anonymous user ID
 * 
 * For some tools that need tracking, like the learnpath tool, it is necessary
 * to have a usable user-id to enable some kind of tracking, even if not
 * perfect. An anonymous ID is taken from the users table by looking for a
 * status of "6" (anonymous).
 * @return	int	User ID of the anonymous user, or O if no anonymous user found
 */
function api_get_anonymous_id()
{
	$table = Database::get_main_table(TABLE_MAIN_USER);
	$sql = "SELECT user_id FROM $table WHERE status = 6";
	$res = api_sql_query($sql,__FILE__,__LINE__);
	if(Database::num_rows($res)>0)
	{
		$row = Database::fetch_array($res);
		//error_log('api_get_anonymous_id() returns '.$row['user_id'],0);
		return $row['user_id'];
	}
	else //no anonymous user was found
	{
		return 0;
	}
}

/**
 * Returns the cidreq parameter name + current course id
*/
function api_get_cidreq()
{
	if (!empty ($GLOBALS["_cid"]))
	{
		return 'cidReq='.htmlspecialchars($GLOBALS["_cid"]);
	}
	return '';
}
/**
*	Returns the current course info array.
*	Note: this array is only defined if the user is inside a course.
*	Array elements:
*	['name']
*	['official_code']
*	['sysCode']
*	['path']
*	['dbName']
*	['dbNameGlu']
*	['titular']
*	['language']
*	['extLink']['url' ]
*	['extLink']['name']
*	['categoryCode']
*	['categoryName']
*	Now if the course_code is given, the returned array gives info about that
*   particular course, not specially the current one.
* @todo	same behaviour as api_get_user_info so that api_get_course_id becomes absolete too
*/
function api_get_course_info($course_code=null)
{
	if(!empty($course_code))
	{
		$course_code = Database::escape_string($course_code);
		$course_table = Database::get_main_table(TABLE_MAIN_COURSE);
    	$course_cat_table = Database::get_main_table(TABLE_MAIN_CATEGORY);
        $sql =    "SELECT `course`.*, `course_category`.`code` `faCode`, `course_category`.`name` `faName`
                 FROM $course_table
                 LEFT JOIN $course_cat_table
                 ON `course`.`category_code` =  `course_category`.`code`
                 WHERE `course`.`code` = '$course_code'";
        $result = api_sql_query($sql,__FILE__,__LINE__);
        $_course = array();
        if (Database::num_rows($result)>0)
        {
        	global $_configuration;
            $cData = Database::fetch_array($result);
			$_course['id'          ]         = $cData['code'             ]; //auto-assigned integer
			$_course['name'        ]         = $cData['title'         ];
            $_course['official_code']         = $cData['visual_code'        ]; // use in echo
            $_course['sysCode'     ]         = $cData['code'             ]; // use as key in db
            $_course['path'        ]         = $cData['directory'        ]; // use as key in path
            $_course['dbName'      ]         = $cData['db_name'           ]; // use as key in db list
            $_course['dbNameGlu'   ]         = $_configuration['table_prefix'] . $cData['db_name'] . $_configuration['db_glue']; // use in all queries
            $_course['titular'     ]         = $cData['tutor_name'       ];
            $_course['language'    ]         = $cData['course_language'   ];
            $_course['extLink'     ]['url' ] = $cData['department_url'    ];
            $_course['extLink'     ]['name'] = $cData['department_name'];
            $_course['categoryCode']         = $cData['faCode'           ];
            $_course['categoryName']         = $cData['faName'           ];

            $_course['visibility'  ]         = $cData['visibility'];
            $_course['subscribe_allowed']    = $cData['subscribe'];
			$_course['unubscribe_allowed']   = $cData['unsubscribe'];
        }
        return $_course;			
	}
	else
	{
		global $_course;
		return $_course;
	}
}

/*
==============================================================================
		DATABASE QUERY MANAGEMENT
==============================================================================
*/
/**
 * Executes an SQL query
 * You have to use addslashes() on each value that you want to record into the database
 *
 * @author Olivier Brouckaert
 * @param  string $query - SQL query
 * @param  string $file - optional, the file path and name of the error (__FILE__)
 * @param  string $line - optional, the line of the error (__LINE__)
 * @return resource - the return value of the query
 */
function api_sql_query($query, $file = '', $line = 0)
{
	$result = mysql_query($query);

	if ($line && !$result)
	{
			$info = '<pre>';
			$info .= '<b>MYSQL ERROR :</b><br/> ';
			$info .= mysql_error();
			$info .= '<br/>';
			$info .= '<b>QUERY       :</b><br/> ';
			$info .= $query;
			$info .= '<br/>';
			$info .= '<b>FILE        :</b><br/> ';
			$info .= ($file == '' ? ' unknown ' : $file);
			$info .= '<br/>';
			$info .= '<b>LINE        :</b><br/> ';
			$info .= ($line == 0 ? ' unknown ' : $line);
			$info .= '</pre>';
			@ mysql_close();
			die($info);
	}
	return $result;
}
/**
 * Store the result of a query into an array
 *
 * @author Olivier Brouckaert
 * @param  resource $result - the return value of the query
 * @return array - the value returned by the query
 */
function api_store_result($result)
{
	$tab = array ();
	while ($row = mysql_fetch_array($result))
	{
		$tab[] = $row;
	}
	return $tab;
}

/*
==============================================================================
		SESSION MANAGEMENT
==============================================================================
*/
/**
 * Start the Dokeos session.
 * 
 * The default lifetime for session is set here. It is not possible to have it
 * as a database setting as it is used before the database connection has been made.
 * It is taken from the configuration file, and if it doesn't exist there, it is set
 * to 360000 seconds
 *
 * @author Olivier Brouckaert
 * @param  string variable - the variable name to save into the session
 */
function api_session_start($already_installed = true)
{
	global $storeSessionInDb;
	global $_configuration;
	
	/* causes too many problems and is not configurable dynamically
	 * 
	if($already_installed){
		$session_lifetime = 360000;
		if(isset($_configuration['session_lifetime']))
		{
			$session_lifetime = $_configuration['session_lifetime'];	
		}
		session_set_cookie_params($session_lifetime,api_get_path(REL_PATH));
		
	}*/
	if (is_null($storeSessionInDb))
	{
		$storeSessionInDb = false;
	}
	if ($storeSessionInDb && function_exists('session_set_save_handler'))
	{
		include_once (api_get_path(LIBRARY_PATH).'session_handler.class.php');
		$session_handler = new session_handler();
		@ session_set_save_handler(array (& $session_handler, 'open'), array (& $session_handler, 'close'), array (& $session_handler, 'read'), array (& $session_handler, 'write'), array (& $session_handler, 'destroy'), array (& $session_handler, 'garbage'));
	}
	session_name('dk_sid');
	session_start();
	if ($already_installed)
	{
		if (empty ($_SESSION['checkDokeosURL']))
		{
			$_SESSION['checkDokeosURL'] = api_get_path(WEB_PATH);
		}
		elseif ($_SESSION['checkDokeosURL'] != api_get_path(WEB_PATH))
		{
			api_session_clear();
		}
	}
}
/**
 * save a variable into the session
 *
 * BUG: function works only with global variables
 *
 * @author Olivier Brouckaert
 * @param  string variable - the variable name to save into the session
 */
function api_session_register($variable)
{
	global $$variable;
	session_register($variable);
	$_SESSION[$variable] = $$variable;
}
/**
 * Remove a variable from the session.
 *
 * @author Olivier Brouckaert
 * @param  string variable - the variable name to remove from the session
 */
function api_session_unregister($variable)
{
	if(isset($_SESSION[$variable]))
	{
		session_unregister($variable);
		$_SESSION[$variable] = null;
	}
	unset ($GLOBALS[$variable]);
}
/**
 * Clear the session
 *
 * @author Olivier Brouckaert
 */
function api_session_clear()
{
	session_regenerate_id();
	session_unset();
	$_SESSION = array ();
}
/**
 * Destroy the session
 *
 * @author Olivier Brouckaert
 */
function api_session_destroy()
{
	session_unset();
	$_SESSION = array ();
	session_destroy();
}

/*
==============================================================================
		STRING MANAGEMENT
==============================================================================
*/
function api_add_url_param($url, $param)
{
	if (empty ($param))
	{
		return $url;
	}
	if (strstr($url, '?'))
	{
		if ($param[0] != '&')
		{
			$param = '&'.$param;
		}
		list (, $query_string) = explode('?', $url);
		$param_list1 = explode('&', $param);
		$param_list2 = explode('&', $query_string);
		$param_list1_keys = $param_list1_vals = array ();
		foreach ($param_list1 as $key => $enreg)
		{
			list ($param_list1_keys[$key], $param_list1_vals[$key]) = explode('=', $enreg);
		}
		$param_list1 = array ('keys' => $param_list1_keys, 'vals' => $param_list1_vals);
		foreach ($param_list2 as $enreg)
		{
			$enreg = explode('=', $enreg);
			$key = array_search($enreg[0], $param_list1['keys']);
			if (!is_null($key) && !is_bool($key))
			{
				$url = str_replace($enreg[0].'='.$enreg[1], $enreg[0].'='.$param_list1['vals'][$key], $url);
				$param = str_replace('&'.$enreg[0].'='.$param_list1['vals'][$key], '', $param);
			}
		}
		$url .= $param;
	}
	else
	{
		$url = $url.'?'.$param;
	}
	return $url;
}
/**
* Returns a difficult to guess password.
* @param int $length, the length of the password
* @return string the generated password
*/
function api_generate_password($length = 8)
{
	$characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
	if ($length < 2)
	{
		$length = 2;
	}
	$password = '';
	for ($i = 0; $i < $length; $i ++)
	{
		$password .= $characters[rand() % strlen($characters)];
	}
	return $password;
}
/**
* Checks a password to see wether it is OK to use.
* @param string $password
* @return true if the password is acceptable, false otherwise
*/
function api_check_password($password)
{
	$lengthPass = strlen($password);
	if ($lengthPass < 5)
	{
		return false;
	}
	$passLower = strtolower($password);
	$cptLettres = $cptChiffres = 0;
	$consecutif = 0;
	$codeCharPrev = 0;
	for ($i = 0; $i < $lengthPass; $i ++)
	{
		$codeCharCur = ord($passLower[$i]);
		if ($i && abs($codeCharCur - $codeCharPrev) <= 1)
		{
			$consecutif ++;
			if ($consecutif == 3)
			{
				return false;
			}
		}
		else
		{
			$consecutif = 1;
		}
		if ($codeCharCur >= 97 && $codeCharCur <= 122)
		{
			$cptLettres ++;
		}
		elseif ($codeCharCur >= 48 && $codeCharCur <= 57)
		{
			$cptChiffres ++;
		}
		else
		{
			return false;
		}
		$codeCharPrev = $codeCharCur;
	}
	return ($cptLettres >= 3 && $cptChiffres >= 2) ? true : false;
}
/**
 * Clear the user ID from the session if it was the anonymous user. Generally
 * used on out-of-tools pages to remove a user ID that could otherwise be used
 * in the wrong context.
 * This function is to be used in conjunction with the api_set_anonymous()
 * function to simulate the user existence in case of an anonymous visit.
 * @param	bool	database check switch - passed to api_is_anonymous()
 * @return	bool	true if succesfully unregistered, false if not anonymous. 
 */
function api_clear_anonymous($db_check=false)
{
	global $_user;
	if(api_is_anonymous($_user['user_id'],$db_check))
	{
		unset($_user['user_id']);
		api_session_unregister('_uid');
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * truncates a string
 *
 * @author Brouckaert Olivier
 * @param  string text - text to truncate
 * @param  integer length - length of the truncated text
 * @param  string endStr - suffix
 * @param  boolean middle - if true, truncates on string middle
 */
function api_trunc_str($text, $length = 30, $endStr = '...', $middle = false)
{
	if (strlen($text) <= $length)
	{
		return $text;
	}
	if ($middle)
	{
		$text = rtrim(substr($text, 0, round($length / 2))).$endStr.ltrim(substr($text, -round($length / 2)));
	}
	else
	{
		$text = rtrim(substr($text, 0, $length)).$endStr;
	}
	return $text;
}
// deprecated, use api_trunc_str() instead
function shorten($input, $length = 15)
{
	$length = intval($length);
	if (!$length)
	{
		$length = 15;
	}
	return api_trunc_str($input, $length);
}
/**
 * handling simple and double apostrofe in order that strings be stored properly in database
 *
 * @author Denes Nagy
 * @param  string variable - the variable to be revised
 */
function domesticate($input)
{
	$input = stripslashes($input);
	$input = str_replace("'", "''", $input);
	$input = str_replace('"', "''", $input);
	return ($input);
}

/*
==============================================================================
		FAILURE MANAGEMENT
==============================================================================
*/

/*
 * The Failure Management module is here to compensate
 * the absence of an 'exception' device in PHP 4.
 */
/**
 * $api_failureList - array containing all the failure recorded
 * in order of arrival.
 */
$api_failureList = array ();
/**
 * Fills a global array called $api_failureList
 * This array collects all the failure occuring during the script runs
 * The main purpose is allowing to manage the display messages externaly
 * from the functions or objects. This strengthens encupsalation principle
 *
 * @author Hugues Peeters <peeters@ipm.ucl.ac.be>
 * @param  string $failureType - the type of failure
 * @global array $api_failureList
 * @return bolean false to stay consistent with the main script
 */
function api_set_failure($failureType)
{
	global $api_failureList;
	$api_failureList[] = $failureType;
	return false;
}

/**
 * Sets the current user as anonymous if it hasn't been identified yet. This 
 * function should be used inside a tool only. The function api_clear_anonymous()
 * acts in the opposite direction by clearing the anonymous user's data every
 * time we get on a course homepage or on a neutral page (index, admin, my space)
 * @return	bool	true if set user as anonymous, false if user was already logged in or anonymous id could not be found
 */
function api_set_anonymous()
{
	global $_user;
	if(!empty($_user['user_id']))
	{
		return false;
	}
	else
	{
		$user_id = api_get_anonymous_id();
		if($user_id == 0)
		{
			return false;
		}
		else
		{
			api_session_unregister('_user');
			$_user['user_id'] = $user_id;
			$_user['is_anonymous'] = true;
			api_session_register('_user');
			$GLOBALS['_user'] = $_user;
			return true;
		}
	}
}

/**
 * get the last failure stored in $api_failureList;
 *
 * @author Hugues Peeters <hugues.peeters@claroline.net>
 * @param void
 * @return string - the last failure stored
 */
function api_get_last_failure()
{
	global $api_failureList;
	return $api_failureList[count($api_failureList) - 1];
}
/**
 * collects and manage failures occuring during script execution
 * The main purpose is allowing to manage the display messages externaly
 * from functions or objects. This strengthens encupsalation principle
 *
 * @author Hugues Peeters <hugues.peeters@claroline.net>
 * @package dokeos.library
 */
class api_failure
{
	/*
	 * IMPLEMENTATION NOTE : For now the $api_failureList list is set to the
	 * global scope, as PHP 4 is unable to manage static variable in class. But
	 * this feature is awaited in PHP 5. The class is already written to minize
	 * the change when static class variable will be possible. And the API won't
	 * change.
	 */
	public $api_failureList = array ();
	/**
	 * Pile the last failure in the failure list
	 *
	 * @author Hugues Peeters <peeters@ipm.ucl.ac.be>
	 * @param  string $failureType - the type of failure
	 * @global array  $api_failureList
	 * @return bolean false to stay consistent with the main script
	 */
	function set_failure($failureType)
	{
		$this->api_failureList[] = $failureType;
		return false;
	}
	/**
	 * get the last failure stored
	 *
	 * @author Hugues Peeters <hugues.peeters@claroline.net>
	 * @param void
	 * @return string - the last failure stored
	 */
	function get_last_failure()
	{
		return $this->api_failureList[count($this->api_failureList) - 1];
	}
}

/*
==============================================================================
		CONFIGURATION SETTINGS
==============================================================================
*/
/**
* DEPRECATED, use api_get_setting instead
*/
function get_setting($variable, $key = NULL)
{
	global $_setting;
	return is_null($key) ? $_setting[$variable] : $_setting[$variable][$key];
}

/**
* Returns the value of a setting from the web-adjustable admin config settings.
*
* WARNING true/false are stored as string, so when comparing you need to check e.g.
* if(api_get_setting("show_navigation_menu") == "true") //CORRECT
* instead of
* if(api_get_setting("show_navigation_menu") == true) //INCORRECT
*
* @author Rene Haentjens
* @author Bart Mollet
*/
function api_get_setting($variable, $key = NULL)
{
	global $_setting;
	return is_null($key) ? $_setting[$variable] : $_setting[$variable][$key];
}

/**
 * Returns an escaped version of $_SERVER['PHP_SELF'] to avoid XSS injection
 * @return	string	Escaped version of $_SERVER['PHP_SELF']
 */
function api_get_self()
{
	return htmlentities($_SERVER['PHP_SELF']);
}

/*
==============================================================================
		LANGUAGE SUPPORT
==============================================================================
*/

/**
* Whenever the server type in the Dokeos Config settings is
* set to test/development server you will get an indication that a language variable
* is not translated and a link to a suggestions form of DLTT.
*
* @return language variable '$lang'.$variable or language variable $variable.
*
* @author Roan Embrechts
* @author Patrick Cool
*/
function get_lang($variable, $notrans = 'DLTT')
{
	$ot = '[='; //opening tag for missing vars
	$ct = '=]'; //closing tag for missing vars
	if(api_get_setting('hide_dltt_markup') == 'true')
	{
		$ot = '';
		$ct = '';
	}
	if (api_get_setting('server_type') != 'test')
	{
		$lvv = isset ($GLOBALS['lang'.$variable]) ? $GLOBALS['lang'.$variable] : (isset ($GLOBALS[$variable]) ? $GLOBALS[$variable] : $ot.$variable.$ct);
		if (!is_string($lvv))
			return $lvv;
		return str_replace("\\'", "'", $lvv);
	}
	if (!is_string($variable))
		return $ot.'get_lang(?)'.$ct;
	global $language_interface, $language_files;
	//language file specified in tool
	if (isset ($language_files))
	{
		if (!is_array($language_files))
		{
			include (api_get_path(SYS_CODE_PATH)."lang/".$language_interface."/".$language_files.".inc.php");
		}
		else
		{
			foreach ($language_files as $index => $language_file)
			{
				include (api_get_path(SYS_CODE_PATH)."lang/".$language_interface."/".$language_file.".inc.php");
			}
		}
	}
	@ eval ('$langvar = $'.$variable.';'); // Note (RH): $$var doesn't work with arrays, see PHP doc
	if (isset ($langvar) && is_string($langvar) && strlen($langvar) > 0)
	{
		return str_replace("\\'", "'", $langvar);
	}
	@ eval ('$langvar = $lang'.$variable.';');
	if (isset ($langvar) && is_string($langvar) && strlen($langvar) > 0)
	{
		return str_replace("\\'", "'", $langvar);
	}
	if ($notrans != 'DLTT')
		return $ot.$variable.$ct;
	if (!is_array($language_files))
	{
		$language_file = $language_files;
	}
	else
	{
		$language_file = implode('.inc.php',$language_files);
	}
	return $ot.$variable.$ct."<a href=\"http://www.dokeos.com/DLTT/suggestion.php?file=".$language_file.".inc.php&amp;variable=$".$variable."&amp;language=".$language_interface."\" style=\"color:#FF0000\"><strong>#</strong></a>";
}

/**
 * Gets the current interface language
 * @return string The current language of the interface
 */
function api_get_interface_language()
{
	global 	$language_interface;
	return $language_interface;
}

/*
==============================================================================
		USER PERMISSIONS
==============================================================================
*/
/**
* Check if current user is a platform administrator
* @return boolean True if the user has platform admin rights,
* false otherwise.
*/
function api_is_platform_admin()
{
	return $_SESSION["is_platformAdmin"];
}
/**
 * Check if current user is allowed to create courses
* @return boolean True if the user has course creation rights,
* false otherwise.
*/
function api_is_allowed_to_create_course()
{
	return $_SESSION["is_allowedCreateCourse"];
}
/**
 * Check if the current user is a course administrator
 * @return boolean True if current user is a course administrator
 */
function api_is_course_admin()
{
	return $_SESSION["is_courseAdmin"];
}
/**
 * Check if the current user is a course coach
 * @return	bool	True if current user is a course coach
 */
function api_is_course_coach()
{
	return $_SESSION['is_courseCoach'];
}
/**
 * Check if the current user is a course tutor
 * @return 	bool	True if current user is a course tutor
 */
function api_is_course_tutor()
{
	return $_SESSION['is_courseTutor'];
}
/**
 * Check if the current user is a course or session coach
 * @return boolean True if current user is a course or session coach
 */
function api_is_coach()
{
	global $_user;
	global $sessionIsCoach;

	$sql = "SELECT DISTINCT id, name, date_start, date_end
							FROM session
							INNER JOIN session_rel_course
								ON session_rel_course.id_coach = '".mysql_real_escape_string($_user['user_id'])."'
							ORDER BY date_start, date_end, name";
	$result = api_sql_query($sql,__FILE__,__LINE__);
	$sessionIsCoach = api_store_result($result);

	$sql = "SELECT DISTINCT id, name, date_start, date_end
							FROM session
							WHERE session.id_coach =  '".mysql_real_escape_string($_user['user_id'])."'
							ORDER BY date_start, date_end, name";
	$result = api_sql_query($sql,__FILE__,__LINE__);
	$sessionIsCoach = array_merge($sessionIsCoach , api_store_result($result));

	if(count($sessionIsCoach) > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
/*
==============================================================================
		DISPLAY OPTIONS
		student view, title, message boxes,...
==============================================================================
*/
/**
 * Displays the title of a tool.
 * Normal use: parameter is a string:
 * api_display_tool_title("My Tool")
 *
 * Optionally, there can be a subtitle below
 * the normal title, and / or a supra title above the normal title.
 *
 * e.g. supra title:
 * group
 * GROUP PROPERTIES
 *
 * e.g. subtitle:
 * AGENDA
 * calender & events tool
 *
 * @author Hugues Peeters <hugues.peeters@claroline.net>
 * @param  mixed $titleElement - it could either be a string or an array
 *                               containing 'supraTitle', 'mainTitle',
 *                               'subTitle'
 * @return void
 */
function api_display_tool_title($titleElement)
{
	if (is_string($titleElement))
	{
		$tit = $titleElement;
		unset ($titleElement);
		$titleElement['mainTitle'] = $tit;
	}
	echo '<h3>';
	if ($titleElement['supraTitle'])
	{
		echo '<small>'.$titleElement['supraTitle'].'</small><br>';
	}
	if ($titleElement['mainTitle'])
	{
		echo $titleElement['mainTitle'];
	}
	if ($titleElement['subTitle'])
	{
		echo '<br><small>'.$titleElement['subTitle'].'</small>';
	}
	echo '</h3>';
}
/**
*	Display options to switch between student view and course manager view
*
*	Changes in version 1.2 (Patrick Cool)
*	Student view switch now behaves as a real switch. It maintains its current state until the state
*	is changed explicitly
*
*	Changes in version 1.1 (Patrick Cool)
*	student view now works correctly in subfolders of the document tool
*	student view works correctly in the new links tool
*
*	Example code for using this in your tools:
*	//if ( $is_courseAdmin && api_get_setting('student_view_enabled') == 'true' )
*	//{
*	//	display_tool_view_option($isStudentView);
*	//}
*	//and in later sections, use api_is_allowed_to_edit()
*
	@author Roan Embrechts
*	@author Patrick Cool
*	@version 1.2
*	@todo rewrite code so it is easier to understand
*/
function api_display_tool_view_option()
{
	if (api_get_setting('student_view_enabled') != "true")
	{
		return '';
	}
	if (strpos($_SERVER['REQUEST_URI'],'chat/chat_banner.php')!==false)
	{	//the chat is a multiframe bit that doesn't work too well with the student_view, so do not show the link
		return '';
	}
	$output_string='';
	// check if the $_SERVER['REQUEST_URI'] contains already url parameters (thus a questionmark)
	if (!strstr($_SERVER['REQUEST_URI'], "?"))
	{
		$sourceurl = api_get_self()."?".api_get_cidreq();
	}
	else
	{
		$sourceurl = $_SERVER['REQUEST_URI'];
		//$sourceurl = str_replace('&', '&amp;', $sourceurl);
	}
	if(!empty($_SESSION['studentview']))
	{
		if ($_SESSION['studentview']=='studentview')
		{
			// we have to remove the isStudentView=true from the $sourceurl
			$sourceurl = str_replace('&isStudentView=true', '', $sourceurl);
			$sourceurl = str_replace('&isStudentView=false', '', $sourceurl);
			$output_string .= '<a href="'.$sourceurl.'&isStudentView=false" target="_top">'.get_lang("CourseManagerview").'</a>';
		}
		elseif ($_SESSION['studentview']=='teacherview')
		{
			//switching to teacherview
			$sourceurl = str_replace('&isStudentView=true', '', $sourceurl);
			$sourceurl = str_replace('&isStudentView=false', '', $sourceurl);
			$output_string .= '<a href="'.$sourceurl.'&isStudentView=true" target="_top">'.get_lang("StudentView").'</a>';
		}
	}
	else
	{
		$output_string .= '<a href="'.$sourceurl.'&isStudentView=true" target="_top">'.get_lang("StudentView").'</a>';
	}
	echo $output_string;
}
/**
 * Displays the contents of an array in a messagebox.
 * @param array $info_array An array with the messages to show
 */
function api_display_array($info_array)
{
	foreach ($info_array as $element)
	{
		$message .= $element."<br>";
	}
	Display :: display_normal_message($message);
}
/**
*	Displays debug info
* @param string $debug_info The message to display
*	@author Roan Embrechts
*	@version 1.1, March 2004
*/
function api_display_debug_info($debug_info)
{
	$message = "<i>Debug info</i><br>";
	$message .= $debug_info;
	Display :: display_normal_message($message);
}
/**
*	@deprecated, use api_is_allowed_to_edit() instead
*/
function is_allowed_to_edit()
{
	return api_is_allowed_to_edit();
}

/**
*	Function that removes the need to directly use is_courseAdmin global in
*	tool scripts. It returns true or false depending on the user's rights in
*	this particular course.
*	Optionally checking for tutor and coach roles here allows us to use the
*	student_view feature altogether with these roles as well.
*	@param	bool	Whether to check if the user has the tutor role
*	@param	bool	Whether to check if the user has the coach role
*
*	@author Roan Embrechts
*	@author Patrick Cool
*	@version 1.1, February 2004
*	@return boolean, true: the user has the rights to edit, false: he does not
*/
function api_is_allowed_to_edit($tutor=false,$coach=false)
{
	$is_courseAdmin = api_is_course_admin() || api_is_platform_admin();
	if(!$is_courseAdmin && $tutor == true)
	{	//if we also want to check if the user is a tutor...
		$is_courseAdmin = $is_courseAdmin || api_is_course_tutor();
	}
	if(!$is_courseAdmin && $coach == true)
	{	//if we also want to check if the user is a coach...
		$is_courseAdmin = $is_courseAdmin || api_is_course_coach();
	}	
	if(api_get_setting('student_view_enabled') == 'true')
	{	//check if the student_view is enabled, and if so, if it is activated
		$is_allowed = $is_courseAdmin && $_SESSION['studentview'] != "studentview";
		return $is_allowed;
	}
	else
		return $is_courseAdmin;
}

/**
* this fun
* @param $tool the tool we are checking ifthe user has a certain permission
* @param $action the action we are checking (add, edit, delete, move, visibility)
* @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @version 1.0
*/
function api_is_allowed($tool, $action, $task_id = 0)
{
	global $_course;
	global $_user;

	if(api_is_course_admin())
		return true;

	//if(!$_SESSION['total_permissions'][$_course['code']] and $_course)
	if($_course)
	{
		require_once(api_get_path(SYS_CODE_PATH) . 'permissions/permissions_functions.inc.php');
		require_once(api_get_path(LIBRARY_PATH) . "/groupmanager.lib.php");

		// getting the permissions of this user
		if($task_id == 0)
		{
			$user_permissions = get_permissions('user', $_user['user_id']);
			$_SESSION['total_permissions'][$_course['code']] = $user_permissions;
		}

		// getting the permissions of the task
		if($task_id != 0)
		{
			$task_permissions = get_permissions('task', $task_id);
			/* !!! */$_SESSION['total_permissions'][$_course['code']] = $task_permissions;
		}
		//print_r($_SESSION['total_permissions']);

		// getting the permissions of the groups of the user
		//$groups_of_user = GroupManager::get_group_ids($_course['db_name'], $_user['user_id']);

		//foreach($groups_of_user as $group)
			//$this_group_permissions = get_permissions('group', $group);

		// getting the permissions of the courseroles of the user
		$user_courserole_permissions = get_roles_permissions('user', $_user['user_id']);

		// getting the permissions of the platformroles of the user
		//$user_platformrole_permissions = get_roles_permissions('user', $_user['user_id'], ', platform');

		// getting the permissions of the roles of the groups of the user
		//foreach($groups_of_user as $group)
			//$this_group_courserole_permissions = get_roles_permissions('group', $group);

		// getting the permissions of the platformroles of the groups of the user
		//foreach($groups_of_user as $group)
			//$this_group_platformrole_permissions = get_roles_permissions('group', $group, 'platform');
	}

	// ifthe permissions are limited we have to map the extended ones to the limited ones
	if(api_get_setting('permissions') == 'limited')
	{
		if($action == 'Visibility')
			$action = 'Edit';

		if($action == 'Move')
			$action = 'Edit';
	}

	// the session that contains all the permissions already exists for this course
	// so there is no need to requery everything.
	//my_print_r($_SESSION['total_permissions'][$_course['code']][$tool]);
	if(in_array($action, $_SESSION['total_permissions'][$_course['code']][$tool]))
		return true;
	else
		return false;
}

/**
 * Tells whether this user is an anonymous user
 * @param	int		User ID (optional, will take session ID if not provided)
 * @param	bool	Whether to check in the database (true) or simply in the session (false) to see if the current user is the anonymous user
 * @return	bool	true if this user is anonymous, false otherwise
 */
function api_is_anonymous($user_id=null,$db_check=false)
{
	if(!isset($user_id))
	{
		$user_id = api_get_user_id();
	}
	if($db_check)
	{
		$info = api_get_user_info($user_id);
		if($info['status'] == 6)
		{
			return true;
		}
	}
	else
	{
		global $_user;
		if(isset($_user['is_anonymous']) and $_user['is_anonymous'] === true)
		{
			return true;
		}
	}
	return false;
}

/**
 * Displays message "You are not allowed here..." and exits the entire script.
 * @param	bool	Whether or not to print headers (default = false -> does not print them)
 *
 * @author Roan Embrechts
 * @author Yannick Warnier
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
*
 * @version 1.0, February 2004
 * @version dokeos 1.8, August 2006
*/
function api_not_allowed($print_headers = false)
{
	$home_url = api_get_path(WEB_PATH);
	$user = api_get_user_id();
	$course = api_get_course_id();
	if(isset($user) && !isset($course) && empty($_GET['cidReq']))
	{//if the access is not authorized and there is some login information 
	 // but the cidReq is not found, assume we are missing course data and send the user
	 // to the user_portal
		if(!headers_sent())
		{
			header('location: '.$home_url.'user_portal.php');
		}else{
			echo '<div align="center">';
			Display :: display_error_message('<p>'.get_lang('NotAllowed').'<br/><br/><a href="'.$home_url.'">'.get_lang('PleaseLoginAgainFromHomepage').'</a><br/>',false);
			echo '</div>';
		}
		die();
	}
	elseif(!empty($_SERVER['REQUEST_URI']) && !empty($_GET['cidReq'])){
		//only display form and return to the previous URL if there was a course ID included
		include_once (api_get_path(LIBRARY_PATH).'formvalidator/FormValidator.class.php');$form = new FormValidator('formLogin','post',api_get_self().'?'.$_SERVER['QUERY_STRING']);
		$form->addElement('static',null,null,'Username');
		$form->addElement('text','login','',array('size'=>15));
		$form->addElement('static',null,null,'Password');
		$form->addElement('password','password','',array('size'=>15));
		$form->addElement('submit','submitAuth',get_lang('Ok'));
		$test = $form->return_form();
		if(!headers_sent() or $print_headers){Display::display_header('');}
		echo '<div align="center">';
		Display :: display_error_message('<p>'.get_lang('NotAllowed').'<br/><br/>'.get_lang('PleaseLoginAgainFromFormBelow').'<br/>'.$test,false);
		echo '</div>';
		$_SESSION['request_uri'] = $_SERVER['REQUEST_URI'];
		if($print_headers){Display::display_footer();}
		die();
	}else{
		//if no course ID was included in the requested URL, redirect to homepage
		if($print_headers){Display::display_header('');}
		echo '<div align="center">';
		Display :: display_error_message('<p>'.get_lang('NotAllowed').'<br/><br/><a href="'.$home_url.'">'.get_lang('PleaseLoginAgainFromHomepage').'</a><br/>',false);
		echo '</div>';
		if($print_headers){Display::display_footer();}
		die();
	}
}

/*
==============================================================================
		WHAT'S NEW
		functions for the what's new icons
		in the user course list
==============================================================================
*/
/**
 * @param $last_post_datetime standard output date in a sql query
 * @return unix timestamp
 * @author Toon Van Hoecke <Toon.VanHoecke@UGent.be>
 * @version October 2003
 * @desc convert sql date to unix timestamp
*/
function convert_mysql_date($last_post_datetime)
{
	list ($last_post_date, $last_post_time) = split(" ", $last_post_datetime);
	list ($year, $month, $day) = explode("-", $last_post_date);
	list ($hour, $min, $sec) = explode(":", $last_post_time);
	$announceDate = mktime($hour, $min, $sec, $month, $day, $year);
	return $announceDate;
}

/**
 * Gets item visibility from the item_property table
 * @param	array	Course properties array (result of api_get_course_info())
 * @param	string	Tool (learnpath, document, etc)
 * @param	int		The item ID in the given tool
 * @return	int		-1 on error, 0 if invisible, 1 if visible 
 */
function api_get_item_visibility($_course,$tool,$id)
{
	if(!is_array($_course) or count($_course)==0 or empty($tool) or empty($id)) return -1;
	$tool = Database::escape_string($tool);
	$id = Database::escape_string($id);
	$TABLE_ITEMPROPERTY = Database :: get_course_table(TABLE_ITEM_PROPERTY,$_course['dbName']);
	$sql = "SELECT * FROM $TABLE_ITEMPROPERTY WHERE tool = '$tool' AND ref = $id";
	$res = api_sql_query($sql);
	if($res === false or Database::num_rows($res)==0) return -1;
	$row = Database::fetch_array($res);
	return $row['visibility'];  	
}

/**
 * Updates or adds item properties to the Item_propetry table
 * Tool and lastedit_type are language independant strings (langvars->get_lang!)
 *
 * @param $_course : array with course properties
 * @param $tool : tool id, linked to 'rubrique' of the course tool_list (Warning: language sensitive !!)
 * @param $item_id : id of the item itself, linked to key of every tool ('id', ...), "*" = all items of the tool
 * @param $lastedit_type : add or update action (1) message to be translated (in trad4all) : e.g. DocumentAdded, DocumentUpdated;
 * 												(2) "delete"; (3) "visible"; (4) "invisible";
 * @param $user_id : id of the editing/adding user
 * @param $to_group_id : id of the intended group ( 0 = for everybody), only relevant for $type (1)
 * @param $to_user_id : id of the intended user (always has priority over $to_group_id !), only relevant for $type (1)
 * @param string $start_visible 0000-00-00 00:00:00 format
 * @param unknown_type $end_visible 0000-00-00 00:00:00 format
 * @return boolean False if update fails.
 * @author Toon Van Hoecke <Toon.VanHoecke@UGent.be>, Ghent University
 * @version January 2005
 * @desc update the item_properties table (if entry not exists, insert) of the course
 */
function api_item_property_update($_course, $tool, $item_id, $lastedit_type, $user_id, $to_group_id = 0, $to_user_id = NULL, $start_visible = 0, $end_visible = 0)
{
	$time = time();
	$time = date("Y-m-d H:i:s", $time);
	$TABLE_ITEMPROPERTY = Database :: get_course_table(TABLE_ITEM_PROPERTY,$_course['dbName']);
	if ($to_user_id <= 0)
		$to_user_id = NULL; //no to_user_id set
	$start_visible = ($start_visible == 0) ? "0000-00-00 00:00:00" : $start_visible;
	$end_visible = ($end_visible == 0) ? "0000-00-00 00:00:00" : $end_visible;
	// set filters for $to_user_id and $to_group_id, with priority for $to_user_id
	$filter = "tool='$tool' AND ref='$item_id'";
	if ($item_id == "*")
		$filter = "tool='$tool' AND visibility<>'2'"; // for all (not deleted) items of the tool
	// check if $to_user_id and $to_group_id are passed in the function call
	// if both are not passed (both are null) then it is a message for everybody and $to_group_id should be 0 !
	if (is_null($to_user_id) && is_null($to_group_id))
		$to_group_id = 0;
	if (!is_null($to_user_id))
		$to_filter = " AND to_user_id='$to_user_id'"; // set filter to intended user
	else
		if (!is_null($to_group_id))
			$to_filter = " AND to_group_id='$to_group_id'"; // set filter to intended group
	// update if possible
	$set_type = "";
	switch ($lastedit_type)
	{
		case "delete" : // delete = make item only visible for the platform admin
			$visibility = '2';
			$sql = "UPDATE $TABLE_ITEMPROPERTY
										SET lastedit_date='$time', lastedit_user_id='$user_id', visibility='$visibility' $set_type
										WHERE $filter";
			break;
		case "visible" : // change item to visible
			$visibility = '1';
			$sql = "UPDATE $TABLE_ITEMPROPERTY
										SET lastedit_date='$time', lastedit_user_id='$user_id', visibility='$visibility' $set_type
										WHERE $filter";
			break;
		case "invisible" : // change item to invisible
			$visibility = '0';
			$sql = "UPDATE $TABLE_ITEMPROPERTY
										SET lastedit_date='$time', lastedit_user_id='$user_id', visibility='$visibility' $set_type
										WHERE $filter";
			break;
		default : // item will be added or updated
			$set_type = ", lastedit_type='$lastedit_type' ";
			$visibility = '1';
			$filter .= $to_filter;
			$sql = "UPDATE $TABLE_ITEMPROPERTY
										SET lastedit_date='$time', lastedit_user_id='$user_id' $set_type
										WHERE $filter";
	}

	$res = mysql_query($sql);
	// insert if no entries are found (can only happen in case of $lastedit_type switch is 'default')
	if (mysql_affected_rows() == 0)
	{
		if (!is_null($to_user_id)) // $to_user_id has more priority than $to_group_id
		{
			$to_field = "to_user_id";
			$to_value = $to_user_id;
		}
		else // $to_user_id is not set
			{
			$to_field = "to_group_id";
			$to_value = $to_group_id;
		}
		$sql = "INSERT INTO $TABLE_ITEMPROPERTY
						   		  			(tool,   ref,       insert_date,insert_user_id,lastedit_date,lastedit_type,   lastedit_user_id,$to_field,  visibility,   start_visible,   end_visible)
						         	VALUES 	('$tool','$item_id','$time',    '$user_id',	   '$time',		 '$lastedit_type','$user_id',	   '$to_value','$visibility','$start_visible','$end_visible')";
		$res = mysql_query($sql);
		if (!$res)
			return FALSE;
	}
	return TRUE;
}

/*
==============================================================================
		Language Dropdown
==============================================================================
*/
/**
*	Displays a form (drop down menu) so the user can select his/her preferred language.
*	The form works with or without javascript
*/
function api_display_language_form()
{
	$platformLanguage = api_get_setting('platformLanguage');
	$dirname = api_get_path(SYS_PATH)."main/lang/"; // this line is probably no longer needed
	// retrieve a complete list of all the languages.
	$language_list = api_get_languages();
	// the the current language of the user so that his/her language occurs as selected in the dropdown menu
	$user_selected_language = $_SESSION["user_language_choice"];
	if (!isset ($user_selected_language))
		$user_selected_language = $platformLanguage;
	$original_languages = $language_list['name'];
	$folder = $language_list['folder']; // this line is probably no longer needed
?>
	<script language="JavaScript" type="text/JavaScript">
	<!--
	function jumpMenu(targ,selObj,restore){ //v3.0
	  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
	  if (restore) selObj.selectedIndex=0;
	}
	//-->
	</script>
	<?php


	echo "<form id=\"lang_form\" name=\"lang_form\" method=\"post\" action=\"".api_get_self()."\">", "<select name=\"language_list\"  onchange=\"jumpMenu('parent',this,0)\">";
	foreach ($original_languages as $key => $value)
	{
		if ($folder[$key] == $user_selected_language)
			$option_end = " selected=\"selected\" >";
		else
			$option_end = ">";
		echo "<option value=\"".api_get_self()."?language=".$folder[$key]."\"$option_end";
		#echo substr($value,0,16); #cut string to keep 800x600 aspect
		echo $value;
		echo "</option>\n";
	}
	echo "</select>";
	echo "<noscript><input type=\"submit\" name=\"user_select_language\" value=\"".get_lang("Ok")."\" /></noscript>";
	echo "</form>";
}
/**
* Return a list of all the languages that are made available by the admin.
* @return array An array with all languages. Structure of the array is
*  array['name'] = An array with the name of every language
*  array['folder'] = An array with the corresponding dokeos-folder
*/
function api_get_languages()
{
	$tbl_language = Database :: get_main_table(TABLE_MAIN_LANGUAGE);
	;
	$sql = "SELECT * FROM $tbl_language WHERE available='1' ORDER BY original_name ASC";
	$result = api_sql_query($sql, __FILE__, __LINE__);
	while ($row = mysql_fetch_array($result))
	{
		$language_list['name'][] = $row['original_name'];
		$language_list['folder'][] = $row['dokeos_folder'];
	}
	return $language_list;
}

/*
==============================================================================
		WYSIWYG HTML AREA
		functions for the WYSIWYG html editor, TeX parsing...
==============================================================================
*/
/**
* Displays the FckEditor WYSIWYG editor for online editing of html
* @param string $name The name of the form-element
* @param string $content The default content of the html-editor
* @param int $height The height of the form element
* @param int $width The width of the form element
* @param string $optAttrib optional attributes for the form element
*/
function api_disp_html_area($name, $content = '', $height = '', $width = '100%', $optAttrib = '')
{
	global $_configuration, $_course, $fck_attribute;
	require_once(dirname(__FILE__).'/formvalidator/Element/html_editor.php');
	$editor = new HTML_QuickForm_html_editor($name);
	$editor->setValue($content);
	if( $height != '')
	{
		$fck_attribute['Height'] = $height;
	}
	if( $width != '')
	{
		$fck_attribute['Width'] = $width;
	}
	echo $editor->toHtml();
}
function api_return_html_area($name, $content = '', $height = '', $width = '100%', $optAttrib = '')
{
	global $_configuration, $_course, $fck_attribute;
	require_once(dirname(__FILE__).'/formvalidator/Element/html_editor.php');
	$editor = new HTML_QuickForm_html_editor($name);
	$editor->setValue($content);
	if( $height != '')
	{
		$fck_attribute['Height'] = $height;
	}
	if( $width != '')
	{
		$fck_attribute['Width'] = $width;
	}
	return $editor->toHtml();
}

/**
 * Send an email.
 *
 * Wrapper function for the standard php mail() function. Change this function
 * to your needs. The parameters must follow the same rules as the standard php
 * mail() function. Please look at the documentation on http: //www. php.
 * net/manual/en/function. mail.php
 * @param string $to
 * @param string $subject
 * @param string $message
 * @param string $additional_headers
 * @param string $additional_parameters
 */
function api_send_mail($to, $subject, $message, $additional_headers = null, $additional_parameters = null)
{
	return mail($to, $subject, $message, $additional_headers, $additional_parameters);
}

/**
 * Find the largest sort value in a given user_course_category
 * This function is used when we are moving a course to a different category
 * and also when a user subscribes to a courses (the new courses is added to the end
 * of the main category
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @param int $user_course_category: the id of the user_course_category
 * @return int the value of the highest sort of the user_course_category
*/
function api_max_sort_value($user_course_category, $user_id)
{

	$tbl_course_user = Database :: get_main_table(TABLE_MAIN_COURSE_USER);

	$sql_max = "SELECT max(sort) as max_sort FROM $tbl_course_user WHERE user_id='".$user_id."' AND user_course_cat='".$user_course_category."'";
	$result_max = mysql_query($sql_max) or die(mysql_error());
	if (mysql_num_rows($result_max) == 1)
	{
		$row_max = mysql_fetch_array($result_max);
		$max_sort = $row_max['max_sort'];
	}
	else
	{
		$max_sort = 0;
	}

	return $max_sort;
}

/**
 * This function converts the string "true" or "false" to a boolean true or false.
 * This function is in the first place written for the Dokeos Config Settings (also named AWACS)
 * @param string "true" or "false"
 * @return boolean true or false
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 */
function string_2_boolean($string)
{
	if ($string == "true")
	{
		return true;
	}
	if ($string == "false")
	{
		return false;
	}
}

/**
 * Determines the number of plugins installed for a given location
 */
function api_number_of_plugins($location)
{
	global $_plugins;
	if (is_array($_plugins[$location]))
	{
		return count($_plugins[$location]);
	}
	return 0;
}

/**
 * including the necessary plugins
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
*/
function api_plugin($location)
{
	global $_plugins;

	if (is_array($_plugins[$location]))
	{
		foreach ($_plugins[$location] as $this_plugin)
		{
			include (api_get_path(SYS_PLUGIN_PATH)."$this_plugin/index.php");
		}
	}
}

/**
* Checks to see wether a certain plugin is installed.
* @return boolean true if the plugin is installed, false otherwise.
*/
function api_is_plugin_installed($plugin_list, $plugin_name)
{
	foreach ($plugin_list as $plugin_location)
	{
		if ( array_search($plugin_name, $plugin_location) !== false ) return true;
	}
	return false;
}

/**
 * Apply parsing to content to parse tex commandos that are seperated by [tex]
 * [/tex] to make it readable for techexplorer plugin.
 * @param string $text The text to parse
 * @return string The text after parsing.
 * @author Patrick Cool <patrick.cool@UGent.be>
 * @version June 2004
*/
function api_parse_tex($textext)
{
	if (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
	{
		$textext = str_replace(array ("[tex]", "[/tex]"), array ("<object classid=\"clsid:5AFAB315-AD87-11D3-98BB-002035EFB1A4\"><param name=\"autosize\" value=\"true\" /><param name=\"DataType\" value=\"0\" /><param name=\"Data\" value=\"", "\" /></object>"), $textext);
	}
	else
	{
		$textext = str_replace(array ("[tex]", "[/tex]"), array ("<embed type=\"application/x-techexplorer\" texdata=\"", "\" autosize=\"true\" pluginspage=\"http://www.integretechpub.com/techexplorer/\">"), $textext);
	}
	return $textext;
}


/**
 * Transform a number of seconds in hh:mm:ss format
 * @author Julian Prud'homme
 * @param integer the number of seconds
 * @return string the formated time
 */
function api_time_to_hms($seconds)
{

  //How many hours ?
  $hours = floor($seconds / 3600);

  //How many minutes ?
  $min = floor(($seconds - ($hours * 3600)) / 60);

  //How many seconds
  $sec = floor($seconds - ($hours * 3600) - ($min * 60));

  if ($sec < 10)
    $sec = "0".$sec;

  if ($min < 10)
    $min = "0".$min;

  return $hours.":".$min.":".$sec;

}


/**
 * function adapted from a php.net comment
 * copy recursively a folder
 * @param the source folder
 * @param the dest folder
 * @param an array of excluded file_name (without extension)
 * @param copied_files the returned array of copied files
 */

function copyr($source, $dest, $exclude=array(), $copied_files=array())
{
    // Simple copy for a file
    if (is_file($source)) {
    	$path_infos = pathinfo($source);
    	if(!in_array($path_infos['filename'], $exclude))
       		copy($source, $dest);
       	return;
    }

 
    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest);
    }
 
    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }
 
        // Deep copy directories
        if ($dest !== "$source/$entry") {
            $zip_files = copyr("$source/$entry", "$dest/$entry", $exclude, $copied_files);
        }
    }
    // Clean up
    $dir->close();
    return $zip_files;
}


function api_chmod_R($path, $filemode) { 
    if (!is_dir($path))
       return chmod($path, $filemode);

    $dh = opendir($path);
    while ($file = readdir($dh)) {
        if($file != '.' && $file != '..') {
            $fullpath = $path.'/'.$file;
            if(!is_dir($fullpath)) {
              if (!chmod($fullpath, $filemode))
                 return FALSE;
            } else {
              if (!api_chmod_R($fullpath, $filemode))
                 return FALSE;
            }
        }
    }
 
    closedir($dh);
    
    if(chmod($path, $filemode))
      return TRUE;
    else 
      return FALSE;
}

?>