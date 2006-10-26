<?php //$id: $
/**
 * Script managing the learnpath upload. To best treat the uploaded file, make sure we can identify it.
 * @package dokeos.learnpath
 * @author Yannick Warnier <ywarnier@beeznest.org>
 */
/**
 * Script initialisations
 */
//$langFile = "scormdocument";

require_once('back_compat.inc.php');
$course_dir  = api_get_course_path().'/scorm';
$course_sys_dir = api_get_path(SYS_COURSE_PATH).$course_dir;
$current_dir = replace_dangerous_char(trim($_POST['current_dir']),'strict');
$uncompress  = 1;

//error_log('New LP - lp_upload.php',0);
/*
 * check the request method in place of a variable from POST
 * because if the file size exceed the maximum file upload
 * size set in php.ini, all variables from POST are cleared !
 */

if ($_SERVER['REQUEST_METHOD'] == 'POST' 
	&& count($_FILES)>0
	&& empty($_POST['file_name'])
	)
{

	// A file upload has been detected, now deal with the file...
	//directory creation

	$stopping_error = false;

	$s=$_FILES['user_file']['name'];
	//get name of the zip file without the extension
	$info = pathinfo($s);
	$filename = $info['basename'];
	$extension = $info['extension'];
	$file_base_name = str_replace('.'.$extension,'',$filename);
	
	$new_dir = replace_dangerous_char(trim($file_base_name),'strict');

	require_once('learnpath.class.php');
	require_once('scorm.class.php');
	$oScorm = new scorm();
	$manifest = $oScorm->import_package($_FILES['user_file'],$current_dir);
	if(!empty($manifest)){
		$oScorm->parse_manifest($manifest);
		$oScorm->import_manifest(api_get_course_id());
	}

	$proximity = '';
	if(!empty($_REQUEST['content_proximity'])){$proximity = mysql_real_escape_string($_REQUEST['content_proximity']);}
	$maker = '';
	if(!empty($_REQUEST['content_maker'])){$maker = mysql_real_escape_string($_REQUEST['content_maker']);}
	$oScorm->set_proximity($proximity);
	$oScorm->set_maker($maker);
	$oScorm->set_jslib('scorm_api.php');
} // end if is_uploaded_file
elseif($_SERVER['REQUEST_METHOD'] == 'POST')
{
	//if file name given to get in claroline/upload/, try importing this way
	
	// A file upload has been detected, now deal with the file...
	//directory creation

	$stopping_error = false;

	//escape path with basename so it can only be directly into the claroline/upload directory
	$s=api_get_path(SYS_CODE_PATH).'garbage/'.basename($_POST['file_name']);
	//get name of the zip file without the extension
	$info = pathinfo($s);
	$filename = $info['basename'];
	$extension = $info['extension'];
	$file_base_name = str_replace('.'.$extension,'',$filename);
	$new_dir = replace_dangerous_char(trim($file_base_name),'strict');

	require_once('learnpath.class.php');
	require_once('scorm.class.php');
	$oScorm = new scorm();
	$manifest = $oScorm->import_local_package($s,$current_dir);
	if(!empty($manifest)){
		$oScorm->parse_manifest($manifest);
		$oScorm->import_manifest(api_get_course_id());
	}

	$proximity = '';
	if(!empty($_REQUEST['content_proximity'])){$proximity = mysql_real_escape_string($_REQUEST['content_proximity']);}
	$maker = '';
	if(!empty($_REQUEST['content_maker'])){$maker = mysql_real_escape_string($_REQUEST['content_maker']);}
	$oScorm->set_proximity($proximity);
	$oScorm->set_maker($maker);
	$oScorm->set_jslib('scorm_api.php');
	
}
?>