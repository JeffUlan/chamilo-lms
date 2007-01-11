<?php
/*
    DOKEOS - elearning and course management software

    For a full list of contributors, see documentation/credits.html
   
    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.
    See "documentation/licence.html" more details.
 
    Contact: 
		Dokeos
		Rue des Palais 44 Paleizenstraat
		B-1030 Brussels - Belgium
		Tel. +32 (2) 211 34 56
*/

/**
*	@package dokeos.survey
* 	@author 
* 	@version $Id: question_edit.php 10680 2007-01-11 21:26:23Z pcool $
*/

/*
==============================================================================
		INIT SECTION
==============================================================================
*/
// name of the language file that needs to be included 
$language_file = 'survey';

require ('../inc/global.inc.php');
require_once (api_get_path(LIBRARY_PATH)."/surveymanager.lib.php");
require_once (api_get_path(LIBRARY_PATH)."/course.lib.php");

/** @todo replace this with the correct code */
/*
$status = surveymanager::get_status();
api_protect_course_script();
if($status==5)
{
	api_protect_admin_script();
}
*/
/** @todo this has to be moved to a more appropriate place (after the display_header of the code)*/
if (!api_is_allowed_to_edit())
{
	Display :: display_header();
	Display :: display_error_message(get_lang('NotAllowedHere'));
	Display :: display_footer();
	exit;
}

$groupid=$_REQUEST['groupid'];
$surveyid=$_REQUEST['surveyid'];
$qid=$_REQUEST['qid'];
$qtype=$_REQUEST['qtype'];

$table_survey 			= Database :: get_course_table(TABLE_SURVEY);
$table_group 			= Database :: get_course_table(TABLE_SURVEY_GROUP);
$table_survey_question 	= Database :: get_course_table(TABLE_SURVEY_QUESTION);

if($qtype=="Yes/No")
{
	header("location:yesno_edit.php?qid=$qid&qtype=$qtype&groupid=$groupid&surveyid=$surveyid&curr_dbname=$curr_dbname");
	exit;
}
if($qtype=="Numbered")
{
	header("location:numbered_edit.php?qid=$qid&qtype=$qtype&groupid=$groupid&surveyid=$surveyid&curr_dbname=$curr_dbname");
	exit;
}

if($qtype=="Multiple Choice (single answer)")
{
	header("location:mcsa_edit.php?qid=$qid&qtype=$qtype&groupid=$groupid&surveyid=$surveyid&curr_dbname=$curr_dbname");
	exit;
}

if($qtype=="Multiple Choice (multiple answer)")
{
	header("location:mcma_edit.php?qid=$qid&qtype=$qtype&groupid=$groupid&surveyid=$surveyid&curr_dbname=$curr_dbname");
	exit;
}

if($qtype=="Open Answer")
{
 header("location:open_edit.php?qid=$qid&qtype=$qtype&groupid=$groupid&surveyid=$surveyid&curr_dbname=$curr_dbname");
	exit;
}

Display :: display_footer();
?>