<?php
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Olivier Brouckaert

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
==============================================================================
*/
/**
============================================================================== 
	@author Bart Mollet
*	@package dokeos.admin
============================================================================== 
*/
/*
==============================================================================
		INIT SECTION
==============================================================================
*/
$langFile = 'survey';

require ('../inc/global.inc.php');
require_once (api_get_path(LIBRARY_PATH)."/surveymanager.lib.php");
$status = surveymanager::get_status();
if($status==5)
{
api_protect_admin_script();
}
//api_protect_admin_script();
require_once (api_get_path(LIBRARY_PATH)."/course.lib.php");
$cidReq = $_REQUEST['cidReq'];
$db_name = $_REQUEST['db_name'];
$table_survey = Database :: get_course_table('survey');
//$interbredcrump[] = array ("url" => "index.php", "name" => get_lang('AdministrationTools'));
//$interbredcrump[] = array ("url" => "survey.php", "name" => get_lang('a_survey'));
$interbredcrump[] = array ("url" => "survey_list.php", "name" => get_lang('Survey'));
$n='e';
$tool_name = get_lang('CreateFromExistingSurveys');
$surveyid=$_GET['surveyid'];
if(isset($_POST['importexistingsurvey']))
{
	$db_name = $_POST['db_name'];
	$cidReq = $_REQUEST['cidReq'];
	$surveyid=$_REQUEST['surveyid'];
    header("location:attach_existingsurvey.php?cidReq=$cidReq&surveyid=$surveyid&n=$n&db_name=$db_name");
	exit;
}
if(isset($_POST['back']))
{
	$db_name = $_POST['db_name'];
	$cidReq = $_REQUEST['cidReq'];
	header("location:survey_all_courses.php?cidReq=$cidReq&db_name=$db_name&n=$n");
	exit;
}
if(isset($_POST['import']))
{
  $cidReq = $_REQUEST['cidReq'];
  $surveyid=$_REQUEST['surveyid'];
  $selectcount=count($_POST['course']);	
  if($selectcount<=0)
   {
	  	 $error_message=get_lang("PleaseSelectAChoice");		
   }
 else
  {
	$db_name = $_POST['db_name'];
	$cidReq = $_REQUEST['cidReq'];
	$gid_arr = $_REQUEST['course']; 
	$gids = implode(",",$gid_arr);	
    header("location:attach_survey.php?cidReq=$cidReq&surveyid=$surveyid&gids=$gids&db_name=$db_name");
	exit;
  }
}
if(isset($_POST['view']))
{
 $db_name = $_POST['db_name'];
 $cidReq = $_REQUEST['cidReq'];
 $surveyid=$_REQUEST['surveyid'];
 $selectcount=count($_POST['course']);	
  if($selectcount<=0)
   {
	  	 $error_message=get_lang("PleaseSelectAChoice");		
   }
 else
  {    
   $course=implode(",",$_REQUEST['course']);
   header("location:question_list.php?cidReq=$cidReq&surveyid=$surveyid&course=$course&n=$n&db_name=$db_name");
   exit;
  }
}
Display :: display_header($tool_name);
api_display_tool_title($tool_name);
if( isset($error_message) )
{
	Display::display_error_message($error_message);	
}

?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>?cidReq=<?php echo $cidReq; ?>" method="POST" name="frm1">
<input type="submit" name="importexistingsurvey" value="<?php echo get_lang('ImportExistingSurvey'); ?>">
<input type="hidden" name="surveyid" value="<?php echo $surveyid; ?>">
<input type="hidden" name="db_name" value="<?php echo $db_name; ?>">
</form>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>?cidReq=<?php echo $cidReq; ?>" name="frm2">
<input type="hidden" name="action" value="add_survey">
<input type="hidden" name="surveyid" value="<?php echo $surveyid; ?>">
<input type="hidden" name="db_name" value="<?php echo $db_name; ?>">
<?php 	
   		$nameTools=get_lang('CreateFromExistingSurveys');
		$table_group = Database :: get_course_table('survey_group');
		$sql = "SELECT * FROM $db_name.survey_group WHERE survey_id='$surveyid'";
		$parameters = array ();
			$parameters['surveyid']=$surveyid;
			$parameters['newgroupid']=$groupid;
			$parameters['cidReq']=$cidReq;
			$parameters['db_name']=$db_name;

		$res = api_sql_query($sql,__FILE__,__LINE__);	
	if (mysql_num_rows($res) > 0)
	{		
		$surveys = array ();
		while ($obj = mysql_fetch_object($res))
		{
			$survey = array ();
			$survey[] = '<input type="checkbox" name="course[]" value="'.$obj->group_id.'">';
			$survey[] = $obj->groupname;
			$groupid=$obj->group_id;
			//$surveyid=surveymanager::get_surveyid($groupid);
			$authorid=surveymanager::get_author($db_name,$surveyid);
			$author=surveymanager::get_survey_author($authorid);
			$NoOfQuestion=surveymanager::no_of_question($db_name,$groupid);
			$survey[] = $NoOfQuestion;
			$survey[] = $author;
			$surveys[] = $survey;
		}
		$table_header[] = array (' ', false);
		$table_header[] = array (get_lang('QuesGroup'), true);
		$table_header[] = array (get_lang('NoOfQuestions'), true);
		$table_header[] = array (get_lang('author'), true);
		Display :: display_sortable_table($table_header, $surveys, array (), array (), $parameters);
		?>		
		<table>
		<tr>
		<td><input type="submit" name="back" value="<?php  echo get_lang('back');?>"></td>
		<td><input type="submit" name="view" value="<?php echo get_lang('ViewQues');?>"></td>
		<td><input type="submit" name="import" value="<?php echo get_lang('ImportGroups');?>"></td>
		</tr>
		</table>
		</form>
<?	
    }
	else
	{
		echo get_lang('NoSearchResults');
	}
Display :: display_footer();
?> 




