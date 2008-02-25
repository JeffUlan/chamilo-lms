<?php
// $Id: infocours.php 14371 2008-02-25 22:28:33Z yannoo $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Hugues Peeters
	Copyright (c) Roan Embrechts (Vrije Universiteit Brussel)
	Copyright (c) Olivier Brouckaert
	Copyright (c) Bart Mollet, Hogeschool Gent

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
*	Code to display the course settings form (for the course admin)
*	and activate the changes.
*
*	See ./inc/conf/course_info.conf.php for settings
* @todo: Move $canBeEmpty from course_info.conf.php to config-settings
* @todo: Take those config settings into account in this script
* @author Patrick Cool <patrick.cool@UGent.be>
* @author Roan Embrechts, refactoring
* and improved course visibility|subscribe|unsubscribe options
* @package dokeos.course_info
==============================================================================
*/
/*
==============================================================================
	   INIT SECTION
==============================================================================
*/
// name of the language file that needs to be included
$language_file = array ('create_course', 'course_info');
include ('../inc/global.inc.php');
$this_section = SECTION_COURSES;

$nameTools = get_lang("ModifInfo");

/*
-----------------------------------------------------------
	Libraries
-----------------------------------------------------------
*/
require_once (api_get_path(LIBRARY_PATH).'course.lib.php');
require_once (api_get_path(INCLUDE_PATH)."conf/course_info.conf.php");
require_once (api_get_path(INCLUDE_PATH)."lib/debug.lib.inc.php");
require_once (api_get_path(LIBRARY_PATH).'formvalidator/FormValidator.class.php');
/*
-----------------------------------------------------------
	Constants and variables
-----------------------------------------------------------
*/
define("MODULE_HELP_NAME", "Settings");
define("COURSE_CHANGE_PROPERTIES", "COURSE_CHANGE_PROPERTIES");
$TABLECOURSE 				= Database :: get_main_table(TABLE_MAIN_COURSE);
$TABLEFACULTY 				= Database :: get_main_table(TABLE_MAIN_CATEGORY);
$TABLECOURSEHOME 			= Database :: get_course_table(TABLE_TOOL_LIST);
$TABLELANGUAGES 			= Database :: get_main_table(TABLE_MAIN_LANGUAGE);
$TABLEBBCONFIG 				= Database :: get_course_table(TOOL_FORUM_CONFIG_TABLE);
$currentCourseID 			= $_course['sysCode'];
$currentCourseRepository 	= $_course["path"];
$is_allowedToEdit 			= $is_courseAdmin || $is_platformAdmin;
$course_setting_table 		= Database::get_course_table(TABLE_COURSE_SETTING);
/*
==============================================================================
		LOGIC FUNCTIONS
==============================================================================
*/
function is_settings_editable()
{
	return $GLOBALS["course_info_is_editable"];
}
$course_code = $_course["sysCode"];
$course_access_settings = CourseManager :: get_access_settings($course_code);

/*
==============================================================================
		MAIN CODE
==============================================================================
*/

if (!$is_allowedToEdit)
{
	api_not_allowed(true);
}

$table_course_category = Database :: get_main_table(TABLE_MAIN_CATEGORY);
$tbl_user = Database :: get_main_table(TABLE_MAIN_USER);
$tbl_admin = Database :: get_main_table(TABLE_MAIN_ADMIN);
$tbl_course_user = Database :: get_main_table(TABLE_MAIN_COURSE_USER);
$tbl_course = Database :: get_main_table(TABLE_MAIN_COURSE);

// Get all course categories
$sql = "SELECT code,name FROM ".$table_course_category." WHERE auth_course_child ='TRUE'  OR code = '".mysql_real_escape_string($_course['categoryCode'])."'  ORDER BY tree_pos";
$res = api_sql_query($sql, __FILE__, __LINE__);

$s_select_course_tutor_name="SELECT tutor_name FROM $tbl_course WHERE code='$course_code'";
$q_tutor=api_sql_query($s_select_course_tutor_name, __FILE__, __LINE__);
$s_tutor=mysql_result($q_tutor,0,"tutor_name");

$s_sql_course_titular="SELECT DISTINCT username, lastname, firstname FROM $tbl_user as user, $tbl_course_user as course_rel_user WHERE (course_rel_user.status='1') AND user.user_id=course_rel_user.user_id AND course_code='".$course_code."'";
$q_result_titulars=api_sql_query($s_sql_course_titular, __FILE__, __LINE__);

if(mysql_num_rows($q_result_titulars)==0){
	$sql="SELECT username, lastname, firstname FROM $tbl_user as user, $tbl_admin as admin WHERE admin.user_id=user.user_id ORDER BY lastname ASC";
	$q_result_titulars=api_sql_query($sql, __FILE__, __LINE__);
}

$a_profs[0] = '-- '.get_lang('NoManager').' --';
while($a_titulars=mysql_fetch_array($q_result_titulars)){
		$s_username=$a_titulars["username"];
		$s_lastname=$a_titulars["lastname"];
		$s_firstname=$a_titulars["firstname"];

		if($s_firstname.' '.$s_lastname==$s_tutor){
			$s_selected_tutor=$s_firstname.' '.$s_lastname;
		}
		$s_disabled_select_titular="";
		if(!$is_courseAdmin){
			$s_disabled_select_titular="disabled=disabled";
		}
		$a_profs[$s_firstname.' '.$s_lastname]="$s_lastname $s_firstname ($s_username)";
}

while ($cat = mysql_fetch_array($res))
{
	$categories[$cat['code']] = '('.$cat['code'].') '.$cat['name'];
	ksort($categories);
}

$linebreak = '<div class="row"><div class="label"></div><div class="formw" style="border-bottom:1px dashed"></div></div>';

// Build the form
$form = new FormValidator('update_course');
$visual_code=$form->addElement('text','visual_code', get_lang('Code'));
	$visual_code->freeze();
$form->applyFilter('visual_code', 'strtoupper');
//$form->add_textfield('tutor_name', get_lang('Professors'), true, array ('size' => '60'));
$prof = &$form->addElement('select', 'tutor_name', get_lang('Professors'), $a_profs);
$prof -> setSelected($s_selected_tutor);
$form->add_textfield('title', get_lang('Title'), true, array ('size' => '60'));
$form->addElement('select', 'category_code', get_lang('Fac'), $categories);
$form->add_textfield('department_name', get_lang('Department'), false, array ('size' => '60'));
$form->add_textfield('department_url', get_lang('DepartmentUrl'), false, array ('size' => '60'));
$form->addRule('tutor_name', get_lang('ThisFieldIsRequired'), 'required');
$form->addElement('select_language', 'course_language', get_lang('Ln'));
$form->addElement('static', null, null, get_lang("TipLang"));
$form -> addElement('html',$linebreak);

$form->addElement('radio', 'visibility', get_lang("CourseAccess"), get_lang('OpenToTheWorld'), COURSE_VISIBILITY_OPEN_WORLD);
$form->addElement('radio', 'visibility', null, get_lang('OpenToThePlatform'), COURSE_VISIBILITY_OPEN_PLATFORM);
$form->addElement('radio', 'visibility', null, get_lang('Private'), COURSE_VISIBILITY_REGISTERED);
$form->addElement('radio', 'visibility', null, get_lang('CourseVisibilityClosed'), COURSE_VISIBILITY_CLOSED);
$form -> addElement('html',$linebreak);

$form->addElement('radio', 'subscribe', get_lang('Subscription'), get_lang('Allowed'), 1);
$form->addElement('radio', 'subscribe', null, get_lang('Denied'), 0);
$form -> addElement('html',$linebreak);

$form->addElement('radio', 'unsubscribe', get_lang('Unsubscription'), get_lang('AllowedToUnsubscribe'), 1);
$form->addElement('radio', 'unsubscribe', null, get_lang('NotAllowedToUnsubscribe'), 0);
$form -> addElement('html',$linebreak);

$form->addElement('radio', 'email_alert_manager_on_new_doc', get_lang('WorkEmailAlert'), get_lang('WorkEmailAlertActivate'), 1);
$form->addElement('radio', 'email_alert_manager_on_new_doc', null, get_lang('WorkEmailAlertDeactivate'), 0);
$form -> addElement('html',$linebreak);

$form->addElement('radio', 'email_alert_on_new_doc_dropbox', get_lang('DropboxEmailAlert'), get_lang('DropboxEmailAlertActivate'), 1);
$form->addElement('radio', 'email_alert_on_new_doc_dropbox', null, get_lang('DropboxEmailAlertDeactivate'), 0);
$form -> addElement('html',$linebreak);

$form->addElement('radio', 'email_alert_manager_on_new_quiz', get_lang('QuizEmailAlert'), get_lang('QuizEmailAlertActivate'), 1);
$form->addElement('radio', 'email_alert_manager_on_new_quiz', null, get_lang('QuizEmailAlertDeactivate'), 0);
$form -> addElement('html',$linebreak);

$form->addElement('radio', 'allow_user_edit_agenda', get_lang('AllowUserEditAgenda'), get_lang('AllowUserEditAgendaActivate'), 1);
$form->addElement('radio', 'allow_user_edit_agenda', null, get_lang('AllowUserEditAgendaDeactivate'), 0);
$form -> addElement('html',$linebreak);

$form->addElement('radio', 'allow_user_edit_announcement', get_lang('AllowUserEditAnnouncement'), get_lang('AllowUserEditAnnouncementActivate'), 1);
$form->addElement('radio', 'allow_user_edit_announcement', null, get_lang('AllowUserEditAnnouncementDeactivate'), 0);
$form -> addElement('html',$linebreak);

$form->addElement('radio', 'allow_user_image_forum', get_lang('AllowUserImageForum'), get_lang('AllowUserImageForumActivate'), 1);
$form->addElement('radio', 'allow_user_image_forum', null, get_lang('AllowUserImageForumDeactivate'), 0);
$form -> addElement('html',$linebreak);


$form->addElement('static', null, null, get_lang("ConfTip"));
$form->add_textfield('course_registration_password', get_lang('CourseRegistrationPassword'), false, array ('size' => '60'));
if (is_settings_editable())
	{
	$form->addElement('submit', null, get_lang('Ok'));
	}
else
{
	// is it allowed to edit the course settings?
	if (!is_settings_editable())
		$disabled_output = "disabled";
	$form->freeze();
	}

// get all the course information
$all_course_information =  CourseManager::get_course_information($_course['sysCode']);


// Set the default values of the form

$values['title'] = $_course['name'];
$values['visual_code'] = $_course['official_code'];
$values['category_code'] = $_course['categoryCode'];
//$values['tutor_name'] = $_course['titular'];
$values['course_language'] = $_course['language'];
$values['department_name'] = $_course['extLink']['name'];
$values['department_url'] = $_course['extLink']['url'];
$values['visibility'] = $_course['visibility'];
$values['subscribe'] = $course_access_settings['subscribe'];
$values['unsubscribe'] = $course_access_settings['unsubscribe'];
$values['course_registration_password'] =  $all_course_information['registration_code'];
// get send_mail_setting (work)from table
$values['email_alert_manager_on_new_doc'] = api_get_course_setting('email_alert_manager_on_new_doc');
// get send_mail_setting (dropbox) from table
$values['email_alert_on_new_doc_dropbox'] = api_get_course_setting('email_alert_on_new_doc_dropbox');
// get send_mail_setting (work)from table
$values['email_alert_manager_on_new_quiz'] = api_get_course_setting('email_alert_manager_on_new_quiz');
// get allow_user_edit_agenda from table
$values['allow_user_edit_agenda'] = api_get_course_setting('allow_user_edit_agenda');
// get allow_user_edit_announcement from table
$values['allow_user_edit_announcement'] = api_get_course_setting('allow_user_edit_announcement');
// get allow_user_image_forum from table
$values['allow_user_image_forum'] = api_get_course_setting('allow_user_image_forum');

$form->setDefaults($values);
// Validate form
if ($form->validate() && is_settings_editable())
	{
	$update_values = $form->exportValues();
	foreach ($update_values as $index => $value)
		{
		$update_values[$index] = mysql_real_escape_string($value);
		}
	$table_course = Database :: get_main_table(TABLE_MAIN_COURSE);
	$sql = "UPDATE $table_course SET title 			= '".$update_values['title']."',
										 visual_code 	= '".$update_values['visual_code']."',
										 course_language = '".$update_values['course_language']."',
										 category_code  = '".$update_values['category_code']."',
										 department_name  = '".$update_values['department_name']."',
										 department_url  = '".$update_values['department_url']."',
										 visibility  = '".$update_values['visibility']."',
										 subscribe  = '".$update_values['subscribe']."',
										 unsubscribe  = '".$update_values['unsubscribe']."',
										 tutor_name     = '".$update_values['tutor_name']."',
										 registration_code = '".$update_values['course_registration_password']."'
									WHERE code = '".$course_code."'";
	api_sql_query($sql, __FILE__, __LINE__);

	//update course_settings table - this assumes those records exist, otherwise triggers an error
	$table_course_setting = Database::get_course_table(TABLE_COURSE_SETTING);
	if($update_values['email_alert_manager_on_new_doc'] != $values['email_alert_manager_on_new_doc']){
		$sql = "UPDATE $table_course_setting SET value = ".(int)$update_values['email_alert_manager_on_new_doc']." WHERE variable = 'email_alert_manager_on_new_doc' ";
		api_sql_query($sql,__FILE__,__LINE__);
	}
	if($update_values['email_alert_on_new_doc_dropbox'] != $values['email_alert_on_new_doc_dropbox']){
		$sql = "UPDATE $table_course_setting SET value = ".(int)$update_values['email_alert_on_new_doc_dropbox']." WHERE variable = 'email_alert_on_new_doc_dropbox' ";
		api_sql_query($sql,__FILE__,__LINE__);
	}
	if($update_values['email_alert_manager_on_new_quiz'] != $values['email_alert_manager_on_new_quiz']){
		$sql = "UPDATE $table_course_setting SET value = ".(int)$update_values['email_alert_manager_on_new_quiz']." WHERE variable = 'email_alert_manager_on_new_quiz' ";
		api_sql_query($sql,__FILE__,__LINE__);
	}
	if($update_values['allow_user_edit_agenda'] != $values['allow_user_edit_agenda']){
		$sql = "UPDATE $table_course_setting SET value = ".(int)$update_values['allow_user_edit_agenda']." WHERE variable = 'allow_user_edit_agenda' ";
		api_sql_query($sql,__FILE__,__LINE__);
	}
	if($update_values['allow_user_edit_announcement'] != $values['allow_user_edit_announcement']){
		$sql = "UPDATE $table_course_setting SET value = ".(int)$update_values['allow_user_edit_announcement']." WHERE variable = 'allow_user_edit_announcement' ";
		api_sql_query($sql,__FILE__,__LINE__);
	}	
	if($update_values['allow_user_image_forum'] != $values['allow_user_image_forum']){
		$sql = "UPDATE $table_course_setting SET value = ".(int)$update_values['allow_user_image_forum']." WHERE variable = 'allow_user_image_forum' ";
		api_sql_query($sql,__FILE__,__LINE__);
	}	

	$cidReset = true;
	$cidReq = $course_code;
	include ('../inc/local.inc.php');
	header('Location: infocours.php?action=show_message&amp;cidReq='.$course_code);
	exit;
}
/*
-----------------------------------------------------------
	Header
-----------------------------------------------------------
*/

Display :: display_header($nameTools, MODULE_HELP_NAME);

//api_display_tool_title($nameTools);
if (isset ($_GET['action']) && $_GET['action'] == 'show_message')
	{
	Display :: display_normal_message(get_lang('ModifDone'));
	}
// Display the form
$form->display();
	if ($showDiskQuota && $currentCourseDiskQuota != "")
	{
?>
<table>
	<tr>
	<td><?php echo get_lang("DiskQuota"); ?>&nbsp;:</td>
	<td><?php echo $currentCourseDiskQuota; ?> <?php echo $byteUnits[0] ?></td>
	</tr>
	<?php

	}
	if ($showLastEdit && $currentCourseLastEdit != "" && $currentCourseLastEdit != "0000-00-00 00:00:00")
	{
?>
	<tr>
	<td><?php echo get_lang('LastEdit'); ?>&nbsp;:</td>
	<td><?php echo format_locale_date($dateTimeFormatLong,strtotime($currentCourseLastEdit)); ?></td>
	</tr>
	<?php

	}
	if ($showLastVisit && $currentCourseLastVisit != "" && $currentCourseLastVisit != "0000-00-00 00:00:00")
	{
?>
	<tr>
	<td><?php echo get_lang('LastVisit'); ?>&nbsp;:</td>
	<td><?php echo format_locale_date($dateTimeFormatLong,strtotime($currentCourseLastVisit)); ?></td>
	</tr>
	<?php

	}
	if ($showCreationDate && $currentCourseCreationDate != "" && $currentCourseCreationDate != "0000-00-00 00:00:00")
	{
?>
	<tr>
	<td><?php echo get_lang('CreationDate'); ?>&nbsp;:</td>
	<td><?php echo format_locale_date($dateTimeFormatLong,strtotime($currentCourseCreationDate)); ?></td>
	</tr>
	<?php

	}
	if ($showExpirationDate && $currentCourseExpirationDate != "" && $currentCourseExpirationDate != "0000-00-00 00:00:00")
	{
?>
	<tr>
	<td><?php echo get_lang('ExpirationDate'); ?>&nbsp;:</td>
	<td>
	<?php

		echo format_locale_date($dateTimeFormatLong, strtotime($currentCourseExpirationDate));
		echo "<br />".get_lang('OrInTime')." : ";
		$nbJour = (strtotime($currentCourseExpirationDate) - time()) / (60 * 60 * 24);
		$nbAnnees = round($nbJour / 365);
		$nbJour = round($nbJour - $nbAnnees * 365);
		switch ($nbAnnees)
		{
			case "1" :
				echo $nbAnnees, " an ";
				break;
			case "0" :
				break;
			default :
				echo $nbAnnees, " ans ";
		};
		switch ($nbJour)
		{
			case "1" :
				echo $nbJour, " jour ";
				break;
			case "0" :
				break;
			default :
				echo $nbJour, " jours ";
		}
		if ($canReportExpirationDate)
		{
			echo " -&gt; <a href=\"".$urlScriptToReportExpirationDate."\">".get_lang('PostPone')."</a>";
		}
?>
</td>
</tr>
</table>
<?php

	}

/*
==============================================================================
		FOOTER
==============================================================================
*/
Display::display_footer();
?>

