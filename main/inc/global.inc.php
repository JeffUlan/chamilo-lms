<?php
/**
==============================================================================
* It is recommended that ALL dokeos scripts include this important file.
* This script manages
* - http get, post, post_files, session, server-vars extraction into global namespace;
*   (which doesn't occur anymore when servertype config setting is set to test,
*    and which will disappear completely in Dokeos 1.6.1)
* - include of /conf/configuration.php;
* - include of several libraries: main_api, database, display, text, security;
* - selecting the main database;
* - include of language files.
*
* @package dokeos.include
* @todo isn't configuration.php renamed to configuration.inc.php yet?
* @todo use the $_configuration array for all the needed variables
* @todo remove the code that displays the button that links to the install page
* 		but use a redirect immediately. By doing so the $already_installed variable can be removed.
* @todo make it possible to enable / disable the tracking through the Dokeos config page.
*
==============================================================================
*/

// Determine the directory path where this current file lies
// This path will be useful to include the other intialisation files

$includePath = dirname(__FILE__);

// @todo isn't this file renamed to configuration.inc.php yet?
// include the main Dokeos platform configuration file
$main_configuration_file_path = $includePath . "/conf/configuration.php";

$already_installed = false;

if(file_exists($main_configuration_file_path))
{
	require_once($main_configuration_file_path);
	$already_installed = true;
}
else
{
	$_configuration = array();
}

// include the main Dokeos platform library file
require_once($includePath.'/lib/main_api.lib.php');

// Start session

api_session_start($already_installed);


$error_message_not_installed = <<<EOM
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
		<head>
			<title>Dokeos not installed!</title>
			<style type="text/css" media="screen, projection">
				/*<![CDATA[*/
				@import "main/css/public_admin/default.css";
				/*]]>*/
			</style>
		</head>
		<body>
			<div id="header">
				<div id="header1"><a href="http://www.dokeos.com">Dokeos Homepage</a></div>
				<div class="clear"></div>
				<div id="header2">&nbsp;</div>
				<div id="header3">&nbsp;</div>
			</div>

			<div style="text-align: center;"><br /><br />
					<form action="main/install/index.php" method="get"><input type="submit" value="&nbsp;&nbsp; Click to INSTALL DOKEOS &nbsp;&nbsp;" /></form><br />
					or <a href="documentation/installation_guide.html" target="_blank">read the installation guide</a><br /><br />
			</div>

			<div id="footer">
				<div class="copyright">Platform <a href="http://www.dokeos.com"> Dokeos </a> &copy; 2008 </div>
				&nbsp;
			</div>
		</body>
</html>
EOM;

$error_message_db_problem = <<<EOM
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
		<head>
			<title>Dokeos database unavailable!</title>
			<style type="text/css" media="screen, projection">
				/*<![CDATA[*/
				@import "main/css/public_admin/default.css";
				/*]]>*/
			</style>
		</head>
		<body>
			<div id="header">
				<div id="header1"><a href="http://www.dokeos.com">Dokeos Homepage</a></div>
				<div class="clear"></div>
				<div id="header2">&nbsp;</div>
				<div id="header3">&nbsp;</div>
			</div>
EOM;
$error_message_db_problem .= '
			<div style="text-align: center; font-size: large; margin-bottom: 2em;"><br /><br />
					This portal is currently experiencing database issues. Please report this to the portal administrator. Thank you for your help.</a>
			</div>
			<div id="footer">
				<div class="copyright">Platform <a href="http://www.dokeos.com"> Dokeos </a> &copy; 2008 </div>
				&nbsp;
			</div>
		</body>
</html>';


if (!$already_installed)
{
	//require('installedVersion.inc.php');
	die($error_message_not_installed);
}

//Assigning a variable to avoid several useless calls to the database setting. 
// Do not over-user. This is only for this script's local use.
$lib_path = api_get_path(LIBRARY_PATH); 
// Add the path to the pear packages to the include path
ini_set('include_path',ini_get('include_path').PATH_SEPARATOR.$lib_path.'pear');

// Include the libraries that are necessary everywhere
require_once($lib_path.'database.lib.php');
require_once($lib_path.'display.lib.php');
require_once($lib_path.'text.lib.php');
require_once($lib_path.'security.lib.php');

// @todo: this shouldn't be done here. It should be stored correctly during installation
if(empty($_configuration['statistics_database']) && $already_installed)
{
	$_configuration['statistics_database'] = $_configuration['main_database'];
}

// connect to the server database and select the main dokeos database

$dokeos_database_connection = @mysql_connect($_configuration['db_host'], $_configuration['db_user'], $_configuration['db_password']) or die ($error_message_db_problem);

if (! $_configuration['db_host'])
{
	die($error_message_db_problem);
}


unset($error_message_db_problem);
unset($error_message_not_installed);

$selectResult = mysql_select_db($_configuration['main_database'],$dokeos_database_connection) or die ('<center>WARNING ! SYSTEM UNABLE TO SELECT THE MAIN DOKEOS DATABASE</center>');

/*
--------------------------------------------
  RETRIEVING ALL THE DOKEOS CONFIG SETTINGS
--------------------------------------------
*/	
if(!empty($_configuration['multiple_access_urls']))
{
	$_configuration['access_url'] = 1;
	$access_urls = api_get_access_urls();
	$protocol =  ((!empty($_SERVER['HTTPS']) && strtoupper($_SERVER['HTTPS'])!='OFF')?'https':'http').'://';
	$request_url1 = $protocol.$_SERVER['SERVER_NAME'].'/';
	$request_url2 = $protocol.$_SERVER['HTTP_HOST'].'/';

	foreach($access_urls as $details)
	{
		if($request_url1 == $details['url'] or $request_url2 == $details['url'])
		{
			$_configuration['access_url'] = $details['id'];
		}
	}
}
else
{
	$_configuration['access_url'] = 1;
}

//$sql="SELECT * FROM settings_current";
//$result=mysql_query($sql) or die(mysql_error());
$result = api_get_settings(null,'list',$_configuration['access_url']);
//while ($row=mysql_fetch_array($result))
foreach($result as $row)
{
	if ($row['subkey']==NULL)
	{
		$_setting[$row['variable']]=$row['selected_value'];
	}
	else
	{
		$_setting[$row['variable']][$row['subkey']]=$row['selected_value'];
	}
}
// we have to store the settings for the plugins differently because it expects an array
//$sql="SELECT * FROM settings_current WHERE category='plugins'";
//$result=mysql_query($sql) or die(mysql_error());
$result = api_get_settings('Plugins','list',$_configuration['access_url']);
$_plugins=array();
//while ($row=mysql_fetch_array($result))
foreach($result as $row)
{
	$key= $row['variable'];
	if (is_string($_setting[$key]))
	{
		$_setting[$key]=array();
	}
	$_setting[$key][]=$row['selected_value'];
	$_plugins[$key][]=$row['selected_value'];
}


// include the local (contextual) parameters of this course or section
require($includePath."/local.inc.php");

// ===== "who is logged in?" module section =====

include_once($includePath."/lib/online.inc.php");
// check and modify the date of user in the track.e.online table
if (!$x=strpos($_SERVER['PHP_SELF'],'whoisonline.php'))
{
	LoginCheck(isset($_user['user_id']) ? $_user['user_id'] : '',$_configuration['statistics_database']);
}

// ===== end "who is logged in?" module section =====

if(get_setting('server_type') == 'test')
{
	/*
	--------------------------------------------
	Server type is test
	- high error reporting level
	- only do addslashes on $_GET and $_POST
	--------------------------------------------
	*/
	error_reporting(E_ALL & ~E_NOTICE);
	//error_reporting(E_ALL);

	//Addslashes to all $_GET variables
	foreach($_GET as $key=>$val)
	{
		if(!ini_get('magic_quotes_gpc'))
		{
			if(is_string($val))
			{
				$_GET[$key]=addslashes($val);
			}
		}
	}

	//Addslashes to all $_POST variables
	foreach($_POST as $key=>$val)
	{
		if(!ini_get('magic_quotes_gpc'))
		{
			if(is_string($val))
			{
				$_POST[$key]=addslashes($val);
			}
		}
	}
}
else
{
	/*
	--------------------------------------------
	Server type is not test
	- normal error reporting level
	- full fake register globals block
	--------------------------------------------
	*/
	error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);

	if(!isset($HTTP_GET_VARS)) { $HTTP_GET_VARS=$_GET; }
	if(!isset($HTTP_POST_VARS)) { $HTTP_POST_VARS=$_POST; }
	if(!isset($HTTP_POST_FILES)) { $HTTP_POST_FILES=$_FILES; }
	if(!isset($HTTP_SESSION_VARS)) { $HTTP_SESSION_VARS=$_SESSION; }
	if(!isset($HTTP_SERVER_VARS)) { $HTTP_SERVER_VARS=$_SERVER; }

	// Register SESSION variables into $GLOBALS
	if(sizeof($HTTP_SESSION_VARS))
	{
		if(!is_array($_SESSION))
		{
			$_SESSION=array();
		}

		foreach($HTTP_SESSION_VARS as $key=>$val)
		{
			$_SESSION[$key]=$HTTP_SESSION_VARS[$key];
			$GLOBALS[$key]=$HTTP_SESSION_VARS[$key];
		}
	}

	// Register SERVER variables into $GLOBALS
	if(sizeof($HTTP_SERVER_VARS))
	{
		$_SERVER=array();
		foreach($HTTP_SERVER_VARS as $key=>$val)
		{
			$_SERVER[$key]=$HTTP_SERVER_VARS[$key];

			if(!isset($_SESSION[$key]) && $key != 'includePath' && $key != 'rootSys' && $key!= 'clarolineRepositorySys' && $key!= 'lang_path' && $key!= 'extAuthSource' && $key!= 'thisAuthSource' && $key!= 'main_configuration_file_path' && $key!= 'phpDigIncCn' && $key!= 'drs')
			{
				$GLOBALS[$key]=$HTTP_SERVER_VARS[$key];
			}
		}
	}
}



/*
-----------------------------------------------------------
	LOAD LANGUAGE FILES SECTION
-----------------------------------------------------------
*/

// if we use the javascript version (without go button) we receive a get
// if we use the non-javascript version (with the go button) we receive a post
$user_language = '';
if(!empty($_GET['language']))
{
	$user_language = $_GET["language"];
}

if (!empty($_POST["language_list"]))
{
	$user_language = str_replace("index.php?language=","",$_POST["language_list"]);
}

// Checking if we have a valid language. If not we set it to the platform language.
$valid_languages=api_get_languages();
if (!in_array($user_language,$valid_languages['folder']))
{
	$user_language=get_setting('platformLanguage');
}

if (in_array($user_language,$valid_languages['folder']) and (isset($_GET['language']) OR isset($_POST['language_list'])))
{
	$user_selected_language = $user_language; // $_GET["language"];
	$_SESSION["user_language_choice"] = $user_selected_language;
	$platformLanguage = $user_selected_language;
}else{
	$platformLanguage = get_setting('platformLanguage');
}


if (isset($_SESSION["user_language_choice"]))
{
	$language_interface = $_SESSION["user_language_choice"];
}
else
{
	$language_interface = get_setting('platformLanguage');
}

if (isset($_user['language']))
{
	$language_interface = $_user['language'];
}

if ($_course['language'])
{
	$language_interface = $_course['language'];
}



/*
 * Include all necessary language files
 * - trad4all
 * - notification
 * - custom tool language files
 */
$language_files = array();
$language_files[] = 'trad4all';
$language_files[] = 'notification';
$language_files[] = 'accessibility';
if( isset($language_file) )
{
	if( !is_array($language_file))
	{
		$language_files[] = $language_file;
	}
	else
	{
		$language_files = array_merge($language_files,$language_file);
	}
}
// Include all files (first english and then current interface language)
foreach($language_files as $index => $language_file)
{
	include(api_get_path(SYS_CODE_PATH).'lang/english/'.$language_file.'.inc.php');
	include(api_get_path(SYS_CODE_PATH).'lang/'.$language_interface.'/'.$language_file.'.inc.php');
}

//load the charset param after langs because the $charset variable in 
//trad4all.inc.php might have set it and we don't want that
$charset = api_get_setting('platform_charset');

//Update of the logout_date field in the table track_e_login (needed for the calculation of the total connection time)

if($_configuration['tracking_enabled'] && !isset($_SESSION['login_as']) && isset($_user))
{ // if $_SESSION['login_as'] is set, then the user is an admin logged as the user

	$tbl_track_login = Database :: get_statistic_table(TABLE_STATISTIC_TRACK_E_LOGIN);
	
	$sql_last_connection="SELECT login_id, login_date FROM $tbl_track_login WHERE login_user_id='".$_user["user_id"]."' ORDER BY login_date DESC LIMIT 0,1";
	
	$q_last_connection=mysql_query($sql_last_connection);
	if(mysql_num_rows($q_last_connection) > 0)
	{
		$i_id_last_connection=mysql_result($q_last_connection,0,"login_id");
		$s_sql_update_logout_date="UPDATE $tbl_track_login SET logout_date=NOW() WHERE login_id='$i_id_last_connection'";
		api_sql_query($s_sql_update_logout_date);
	}
	
}
?>
