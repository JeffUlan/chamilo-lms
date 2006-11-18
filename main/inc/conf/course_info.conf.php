<?php // $Id $
/**
==============================================================================
*	This file holds the configuration constants and variables
*	for the course info tool.
*
*	@package dokeos.configuration
==============================================================================
*/

/**
 * @todo check if these are used. If this is the case then they should be changed into dokeos config settings and stored in the database.
 * 
 */

$course_info_is_editable = true;

//if (basename($_SERVER["SCRIPT_FILENAME"])==basename(__FILE__)) die("Va voir ailleurs");
$showLinkToDeleteThisCourse = TRUE;
$showLinkToExportThisCourse = TRUE;
$showLinkToBackupThisCourse = TRUE;
$showLinkToRecycleThisCourse = TRUE;
$showLinkToRestoreCourse	= TRUE;
$showLinkToCopyThisCourse 	= TRUE; 

// If true, these fileds  keep the previous content.
$canBeEmpty["screenCode"] 	= FALSE;
$canBeEmpty["course_title"] 			= FALSE;
$canBeEmpty["course_category"] 		= TRUE;
$canBeEmpty["description"] 	= TRUE;
$canBeEmpty["visibility"]	= FALSE;
$canBeEmpty["titulary"] 	= FALSE;
$canBeEmpty["course_language"]= FALSE;
$canBeEmpty["department_name"]	= TRUE;
$canBeEmpty["department_url"] 	= TRUE;

$showDiskQuota									= TRUE;
$showDiskUse									= TRUE;
$showLinkToChangeDiskQuota						= TRUE;
$showExpirationDate 							= TRUE;
$showCreationDate 								= TRUE;
$showLastEdit 									= TRUE;
$showLastVisit 									= TRUE;
$canReportExpirationDate 						= TRUE; // need to be true 
														// if ScriptToReportExpirationDate 
														// is not automaticly called
$linkToChangeDiskQuota							= "changeQuota.php";
$urlScriptToReportExpirationDate 				= "postpone.php"; // external script to postpone the expiration of course.
?>
