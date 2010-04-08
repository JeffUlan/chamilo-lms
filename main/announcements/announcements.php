<?php //$Id: announcements.php 2009-11-13 18:56:45Z aportugal $
/* For licensing terms, see /license.txt */
/**
 * @author Frederik Vermeire <frederik.vermeire@pandora.be>, UGent Internship
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University: code cleaning
 * @abstract The task of the internship was to integrate the 'send messages to specific users' with the
 *			 Announcements tool and also add the resource linker here. The database also needed refactoring
 *			 as there was no title field (the title was merged into the content field)
 * @package dokeos.announcements
 * @todo make AWACS out of the configuration settings
 * @todo this file is 1200+ lines without any functions -> needs to be split into
 * multiple functions
*/
/*
		INIT SECTION
*/
// name of the language file that needs to be included
$language_file[] = 'announcements';
$language_file[] = 'group';
$language_file[] = 'survey';

// use anonymous mode when accessing this course tool
$use_anonymous = true;

// setting the global file that gets the general configuration, the databases, the languages, ...
require_once '../inc/global.inc.php';
$this_section=SECTION_COURSES;

$nameTools = get_lang('ToolAnnouncement');


//session
if(isset($_GET['id_session'])) {
	$_SESSION['id_session'] = intval($_GET['id_session']);
}

/* ACCESS RIGHTS */
// notice for unauthorized people.
api_protect_course_script();

/*
	Constants and variables
*/
// Configuration settings
$display_announcement_list	 = true;
$display_form				 = false;
$display_title_list 		 = true;

// Maximum title messages to display
$maximum 	= '12';

// Length of the titles
$length 	= '36';

// Database Table Definitions
$tbl_course_user   		= Database::get_main_table(TABLE_MAIN_COURSE_USER);
$tbl_user          		= Database::get_main_table(TABLE_MAIN_USER);
$tbl_courses			= Database::get_main_table(TABLE_MAIN_COURSE);
$tbl_sessions			= Database::get_main_table(TABLE_MAIN_SESSION);
$tbl_session_course_user= Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER);
$tbl_group     			= Database::get_course_table(TABLE_GROUP);
$tbl_groupUser  		= Database::get_course_table(TABLE_GROUP_USER);
$tbl_announcement		= Database::get_course_table(TABLE_ANNOUNCEMENT);
$tbl_announcement_attachment = Database::get_course_table(TABLE_ANNOUNCEMENT_ATTACHMENT);
$tbl_item_property  	= Database::get_course_table(TABLE_ITEM_PROPERTY);

/*
	Resource linker
*/
$_SESSION['source_type']="Ad_Valvas";
include '../resourcelinker/resourcelinker.inc.php';

if (!empty($_POST['addresources'])) // When the "Add Resource" button is clicked we store all the form data into a session
{
	include('announcements.inc.php');

    $form_elements= array ('emailTitle'=>stripslashes($emailTitle), 'newContent'=>stripslashes($newContent), 'id'=>$id, 'to'=>$selectedform, 'emailoption'=>$email_ann);
    $_SESSION['formelements']=$form_elements;

    if($id) // this is to correctly handle edits
	{
		  $action="edit";
    }else
    {
		  $action="add";
    }

	// ============== //
	// 7 = Ad_Valvas	//
	// ============== //
	if($surveyid)
	{
		header("Location: ../resourcelinker/resourcelinker.php?source_id=7&action=$action&id=$id&originalresource=no&publish_survey=$surveyid&db_name=$db_name&cidReq=$cidReq");
		exit;
	}
	else
	{
		header("Location: ../resourcelinker/resourcelinker.php?source_id=7&action=$action&id=$id&originalresource=no");
		exit;
	}
	exit;
}

/*
	Tracking
*/
event_access_tool(TOOL_ANNOUNCEMENT);


/*
	Libraries
*/
$lib = api_get_path(LIBRARY_PATH); //avoid useless function calls
require_once $lib.'groupmanager.lib.php';
require_once $lib.'mail.lib.inc.php';
require_once $lib.'debug.lib.inc.php';
require_once $lib.'tracking.lib.php';
require_once $lib.'fckeditor/fckeditor.php';
require_once $lib.'fileUpload.lib.php';
require_once 'announcements.inc.php';
/*
	POST TO
*/

$safe_emailTitle = $_POST['emailTitle'];
$safe_newContent = $_POST['newContent'];

if (!empty($_POST['To']))
{
	if (api_get_session_id()!=0 && api_is_allowed_to_session_edit(false,true)==false) {
		api_not_allowed();
	}
	$display_form = true;

	$form_elements = array ('emailTitle'=>$safe_emailTitle, 'newContent'=>$safe_newContent, 'id'=>Security::remove_XSS($_POST['id']), 'emailoption'=>Security::remove_XSS($_POST['email_ann']));
    $_SESSION['formelements'] = $form_elements;

    $form_elements            	= $_SESSION['formelements'];
	$title_to_modify            = $form_elements["emailTitle"];
	$content_to_modify          = $form_elements["newContent"];
	$announcement_to_modify     = $form_elements["id"];
}

/*
	Show/hide user/group form
*/

$setting_select_groupusers = true;
if (empty($_POST['To']) and !$_SESSION['select_groupusers'])
{
	$_SESSION['select_groupusers'] = "hide";
}
$select_groupusers_status=$_SESSION['select_groupusers'];
if (!empty($_POST['To']) and ($select_groupusers_status=="hide"))
{
	$_SESSION['select_groupusers'] = "show";
}
if (!empty($_POST['To']) and ($select_groupusers_status=="show"))
{
	$_SESSION['select_groupusers'] = "hide";
}

/*
	Action handling
*/

// display the form
if (((!empty($_GET['action']) && $_GET['action'] == 'add') && $_GET['origin'] == "") || (!empty($_GET['action']) && $_GET['action'] == 'edit') || !empty($_POST['To']))
{
	if (api_get_session_id()!=0 && api_is_allowed_to_session_edit(false,true)==false) {
		api_not_allowed();
	}
	$display_form = true;
}

// clear all resources
if ((empty($originalresource) || ($originalresource!=='no')) and (!empty($action) && $action=='add'))
{
	$_SESSION['formelements']=null;
	//unset($_SESSION['formelements']);
	unset_session_resources();
}

/*
	Javascript
*/

$htmlHeadXtra[] = to_javascript();
$htmlHeadXtra[] = '<script src="'.api_get_path(WEB_LIBRARY_PATH).'javascript/jquery.js" type="text/javascript" language="javascript"></script>'; //jQuery
$htmlHeadXtra[] = '<script type="text/javascript">
function setFocus(){
$("#emailTitle").focus();
}
$(document).ready(function () {
  setFocus();
});
</script>';

/*
	Filter user/group
*/

if(!empty($_GET['toolgroup'])){
	if($_GET['toolgroup'] == strval(intval($_GET['toolgroup']))){ //check is integer
		$toolgroup = $_GET['toolgroup'];
		$_SESSION['select_groupusers'] = 'hide';
	}else{
		$toolgroup = 0;
	}
	api_session_register("toolgroup");
}


/*
	Sessions
*/

$ctok = $_SESSION['sec_token'];
$stok = Security::get_token();

if (!empty($_SESSION['formelements']) and !empty($_GET['originalresource']) and $_GET['originalresource'] == 'no')
{
	$form_elements			= $_SESSION['formelements'];
	$title_to_modify		= $form_elements['emailTitle'];
	$content_to_modify		= $form_elements['newContent'];
	$announcement_to_modify	= $form_elements['id'];
	$to						= $form_elements['to'];
	//load_edit_users('announcement',$announcement_to_modify);

	$email_ann				= $form_elements['emailoption'];
}
if(!empty($_GET['remind_inactive']))
{
	$to[] = 'USER:'.intval($_GET['remind_inactive']);
}
/*
	Survey
*/
$surveyid = 0;
if(!empty($_REQUEST['publish_survey']))
{
	$surveyid=Database::escape_string(Security::remove_XSS($_REQUEST['publish_survey']));
}
$cidReq=Database::escape_string($_REQUEST['cidReq']);
if($surveyid)
{
	$db_name=Database::escape_string($_REQUEST['db_name']);
	$sql_temp = "SELECT * FROM $db_name.survey WHERE survey_id='$surveyid'";
	$res_temp = Database::query($sql_temp);
	$obj=@Database::fetch_object($res_temp);
	$template=$obj->template;
}

if (!empty($_SESSION['toolgroup'])){
	$_clean_toolgroup=intval($_SESSION['toolgroup']);
	$group_properties  = GroupManager :: get_group_properties($_clean_toolgroup);
	$interbreadcrumb[] = array ("url" => "../group/group.php", "name" => get_lang('Groups'));
	$interbreadcrumb[] = array ("url"=>"../group/group_space.php?gidReq=".$_clean_toolgroup, "name"=> get_lang('GroupSpace').' ('.$group_properties['name'].')');
} else {
	if($surveyid) {
		$interbreadcrumb[] = array ("url" => "../survey/survey_list.php?cidReq=$cidReq", "name" => get_lang('ToolSurvey'));
		$nameTools = get_lang('PublishSurvey');
	} else {
		$nameTools = get_lang('ToolAnnouncement');
		$nameTools12 = get_lang('PublishSurvey');
	}
}



/*
	Learning path & css
*/
// showing the header if we are not in the learning path, if we are in
// the learning path, we do not include the banner so we have to explicitly
// include the stylesheet, which is normally done in the header
if (empty($_GET['origin']) or $_GET['origin'] !== 'learnpath')
{
	//we are not in the learning path
	Display::Display_header($nameTools,"Announcements");
} else {
	//we are in the learning path, only display central data and change css
	$display_title_list = false;
	$display_announcement_list = false;
	$display_specific_announcement = true;
	$announcement_id = $_REQUEST['ann_id'];
	?> <link rel="stylesheet" type="text/css" href="<?php echo api_get_path(WEB_CODE_PATH).'css/'.$my_style; ?>/default.css">
	<!-- css file for announcements -->
	<link href="../css/<?php echo $my_style; ?>/announcements.css" rel="stylesheet" type="text/css">
	<?php
}

// inserting an anchor (top) so one can jump back to the top of the page
echo "<a name=\"top\"></a>";

/*
		ACTION HANDLING
*/

if (api_is_allowed_to_edit(false,true) OR (api_get_course_setting('allow_user_edit_announcement') && !api_is_anonymous())) {
/*
	Change visibility of announcement
*/
	// $_GET['isStudentView']<>"false" is added to prevent that the visibility
	// is changed after you do the following:
	// change visibility -> studentview -> course manager view
	if (!isset($_GET['isStudentView']) || $_GET['isStudentView']!='false') {
		if (isset($_GET['id']) AND $_GET['id'] AND isset($_GET['action']) AND $_GET['action']=="showhide") {
			if (api_get_session_id()!=0 && api_is_allowed_to_session_edit(false,true)==false) {
				api_not_allowed();
			}

			$id=intval($_GET['id']);
			if (!api_is_course_coach() || api_is_element_in_the_session(TOOL_ANNOUNCEMENT, $id)) {
				if ($ctok == $_GET['sec_token']) {
					change_visibility_announcement(TOOL_ANNOUNCEMENT,$id);
					$message = get_lang("VisibilityChanged");
				}
			}
		}
	}

/*
	Delete announcement
*/
	if (!empty($_GET['action']) AND $_GET['action']=='delete' AND isset($_GET['id'])) {
		//Database::query("DELETE FROM  $tbl_announcement WHERE id='$delete'");
		$id=intval(addslashes($_GET['id']));
		if (api_get_session_id()!=0 && api_is_allowed_to_session_edit(false,true)==false) {
			api_not_allowed();
		}

		if (!api_is_course_coach() || api_is_element_in_the_session(TOOL_ANNOUNCEMENT, $id)) {

			// tooledit : visibility = 2 : only visibile for platform administrator
			if ($ctok == $_GET['sec_token']) {
				Database::query("UPDATE $tbl_item_property SET visibility='2' WHERE tool='".TOOL_ANNOUNCEMENT."' and ref='".$id."'");

				delete_added_resource("Ad_Valvas", $delete);

				$id = null;
				$emailTitle = null;
				$newContent = null;

				$message = get_lang("AnnouncementDeleted");
			}
		}
	}

/*
	Delete all announcements
*/
	if (!empty($_GET['action']) and $_GET['action']=='delete_all') {

		//Database::query("DELETE FROM $tbl_announcement");
		if (api_is_allowed_to_edit()) {
			Database::query("UPDATE $tbl_item_property SET visibility='2' WHERE tool='".TOOL_ANNOUNCEMENT."'");

			delete_all_resources_type("Ad_Valvas");

			$id = null;
			$emailTitle = null;
			$newContent = null;

			$message = get_lang("AnnouncementDeletedAll");
		}
	}

/*
	Modify announcement
*/
	if (!empty($_GET['action']) and $_GET['action']=='modify' AND isset($_GET['id'])) {
		if (api_get_session_id()!=0 && api_is_allowed_to_session_edit(false,true)==false) {
			api_not_allowed();
		}

		$display_form = true;

		// RETRIEVE THE CONTENT OF THE ANNOUNCEMENT TO MODIFY
		$id = intval($_GET['id']);

		if (!api_is_course_coach() || api_is_element_in_the_session(TOOL_ANNOUNCEMENT, $id)) {
			$sql="SELECT * FROM  $tbl_announcement WHERE id='$id'";
			$rs = Database::query($sql);
			$myrow = Database::fetch_array($rs);
			$last_id = $id;
			$edit_attachment = edit_announcement_attachment_file($last_id, $_FILES['user_upload'], $file_comment);
			if ($myrow) {
				$announcement_to_modify 	= $myrow['id'];
				$content_to_modify 			= $myrow['content'];
				$title_to_modify 			= $myrow['title'];

				if ($originalresource!=="no")  {
					//unset_session_resources();
					edit_added_resources("Ad_Valvas", $announcement_to_modify);
					$to=load_edit_users("announcement", $announcement_to_modify);
				}

				$display_announcement_list = false;
			}

			if ($to=="everyone" OR !empty($_SESSION['toolgroup'])) {
				$_SESSION['select_groupusers']="hide";
			} else {
				$_SESSION['select_groupusers']="show";
			}
		}

	}

/*
	Move announcement up/down
*/
	if ($ctok == $_GET['sec_token']) {
		if (!empty($_GET['down'])) {
			$thisAnnouncementId = intval($_GET['down']);
			$sortDirection = "DESC";
		}

		if (!empty($_GET['up'])) {
			$thisAnnouncementId = intval($_GET['up']);
			$sortDirection = "ASC";
		}
	}

	if (!empty($sortDirection)) {
		if (!in_array(trim(strtoupper($sortDirection)), array('ASC', 'DESC'))) {
			$sortDirection='ASC';
		}
		$my_sql = "SELECT announcement.id, announcement.display_order " .
				"FROM $tbl_announcement announcement, " .
				"$tbl_item_property itemproperty " .
				"WHERE itemproperty.ref=announcement.id " .
				"AND itemproperty.tool='".TOOL_ANNOUNCEMENT."' " .
				"AND itemproperty.visibility<>2 " .
				"ORDER BY display_order $sortDirection";
		$result = Database::query($my_sql);

		while (list ($announcementId, $announcementOrder) = Database::fetch_row($result)) {
			// STEP 2 : FOUND THE NEXT ANNOUNCEMENT ID AND ORDER.
			//          COMMIT ORDER SWAP ON THE DB

			if (isset ($thisAnnouncementOrderFound) && $thisAnnouncementOrderFound == true) {
				$nextAnnouncementId = $announcementId;
				$nextAnnouncementOrder = $announcementOrder;
				Database::query("UPDATE $tbl_announcement " .
						"SET display_order = '$nextAnnouncementOrder' " .
						"WHERE id =  '$thisAnnouncementId'");
				Database::query("UPDATE $tbl_announcement " .
						"SET display_order = '$thisAnnouncementOrder' " .
						"WHERE id =  '$nextAnnouncementId.'");

				break;
			}

			// STEP 1 : FIND THE ORDER OF THE ANNOUNCEMENT

			if ($announcementId == $thisAnnouncementId) {
				$thisAnnouncementOrder = $announcementOrder;
				$thisAnnouncementOrderFound = true;
			}
		}
		// show message
		$message = get_lang('AnnouncementMoved');
	}

/*
	Submit announcement
*/
	//if (api_is_allowed_to_edit(false,true) OR (api_get_course_setting('allow_user_edit_announcement') && !api_is_anonymous())) {

	$emailTitle=(!empty($_POST['emailTitle'])?$safe_emailTitle:'');
	$newContent=(!empty($_POST['newContent'])?$safe_newContent:'');

	$submitAnnouncement=isset($_POST['submitAnnouncement'])?$_POST['submitAnnouncement']:0;

	$id = 0;
	if (!empty($_POST['id'])) {
		$id=intval($_POST['id']);
	}

	if ($submitAnnouncement && empty($emailTitle)) {
		$error_message = get_lang('TitleIsRequired');
		$content_to_modify = $newContent;
	} else if ($submitAnnouncement) {

		if (isset($id)&&$id) {
			// there is an Id => the announcement already exists => update mode
			if ($ctok == $_POST['sec_token']) {
				$file_comment = $_POST['file_comment'];
				$file = $_FILES['user_upload'];
				$edit_id = edit_advalvas_item($id,$emailTitle,$newContent,$_POST['selectedform'],$file,$file_comment);
				if (!$delete) {
				    update_added_resources("Ad_Valvas", $id);
				}
				$message = get_lang('AnnouncementModified');
			}
		} else {
			//insert mode
			if ($ctok == $_POST['sec_token']) {

				if (!$surveyid) {
					$result = Database::query("SELECT MAX(display_order) FROM $tbl_announcement WHERE session_id=".intval($_SESSION['id_session'])." OR session_id=0");
					list($orderMax) = Database::fetch_row($result);
					$order = $orderMax + 1;
					$file = $_FILES['user_upload'];
					$file_comment = $_POST['file_comment'];
					if (!empty($_SESSION['toolgroup'])) {
						$insert_id=store_advalvas_group_item($safe_emailTitle,$safe_newContent,$order,array('GROUP:'.$_SESSION['toolgroup']),$_POST['selectedform'],$file,$file_comment);
					} else {
						$insert_id=store_advalvas_item($safe_emailTitle,$safe_newContent,$order,$_POST['selectedform'],$file,$file_comment);
					}
				    store_resources($_SESSION['source_type'],$insert_id);
				    $_SESSION['select_groupusers']="hide";
				    $message = get_lang('AnnouncementAdded');
				}

/*
		MAIL WHEN USER COMES FROM SURVEY
*/

				if ($_POST['emailsAdd']) {

					$to_email_address =$_POST['emailsAdd'];
					$to_email_to = explode(',', $to_email_address);
					$to_email = array_unique($to_email_to);
					$db_name = $_REQUEST['db_name'];

					for ($i=0;$i<count($to_email);$i++) {

						$to= trim($to_email[$i]);
						$db_name = $_REQUEST['db_name'];
						$newContentone=str_replace("#page#","choose_language.php",$newContent);
						$newContenttwo=str_replace("#temp#",$template,$newContentone);
						$newContentthree=str_replace("#sid#",$surveyid,$newContenttwo);
						$newContentfour=str_replace("#mail#",$to,$newContentthree);
					    $newContentfive=str_replace("#db_name#",$db_name,$newContentfour);
						$newContentsix=str_replace("#uid#","",$newContentfive);

						if (eregi('^[0-9a-z_\.-]+@(([0-9]{1,3}\.){3}[0-9]{1,3}|([0-9a-z][0-9a-z-]*[0-9a-z]\.)+[a-z]{2,3})$', $to )) {
							$subject=stripslashes($emailTitle);
							$message=stripslashes($newContentsix);

						    $sender_name = api_get_person_name($_SESSION['_user']['lastName'], $_SESSION['_user']['firstName'], null, PERSON_NAME_EMAIL_ADDRESS);
						    $email = $_SESSION['_user']['mail'];
							$headers="From:$sender_name\r\nReply-to: $email";
							//@mail($to,$subject,$message,$headers);
							//api_send_mail($to,$subject,$message,$headers);
							@api_mail('',$to,$subject,$message,$sender_name,$email,$headers);
							$sql_date="SELECT * FROM $db_name.survey WHERE survey_id='$surveyid'";
							$res_date=Database::query($sql_date);
							$obj_date=Database::fetch_object($res_date);
							$end_date=$obj_date->avail_till;
							$table_reminder = Database :: get_main_table(TABLE_MAIN_SURVEY_REMINDER); // TODO: To be checked. TABLE_MAIN_SURVEY_REMINDER has not been defined.
							if ($_REQUEST['reminder']=="1") {
								$time=getdate();
								$time = $time['yday'];
								$time = $time+7;
								$sql_insert="INSERT INTO $table_reminder(sid,db_name,email,subject,content,reminder_choice,reminder_time,avail_till) values('$surveyid','$db_name','$to','".addslashes($subject)."','".addslashes($message)."','1','$time','$end_date')";
								Database::query($sql_insert);
							} else if ($_REQUEST['reminder']=="2") {
								$time=getdate();
								$time = $time['yday'];
								$time = $time+14;
								$sql_insert="INSERT INTO $table_reminder(sid,db_name,email,subject,content,reminder_choice,reminder_time,avail_till) values('$surveyid','$db_name','$to','".addslashes($subject)."','".addslashes($message)."','1','$time','$end_date')";
								Database::query($sql_insert);
							} else if($_REQUEST['reminder']=="3") {
								$time=getdate();
								$time = $time['yday'];
								$time = $time+30;
								$sql_insert="INSERT INTO $table_reminder(sid,db_name,email,subject,content,reminder_choice,reminder_time,avail_till) values('$surveyid','$db_name','$to','".addslashes($subject)."','".addslashes($message)."','1','$time','$end_date')";
								Database::query($sql_insert);
							}
						}
					}
				}

/*
		MAIL FUNCTION
*/

				if ($_POST['email_ann'] && empty($_POST['onlyThoseMails'])) {

				  	$sent_to=sent_to("announcement", $insert_id);
				    $userlist   = $sent_to['users'];
				    $grouplist  = $sent_to['groups'];

			        // groepen omzetten in users
			        if ($grouplist) {

						$grouplist = "'".implode("', '",$grouplist)."'";	//protect individual elements with surrounding quotes
						$sql = "SELECT user_id
								FROM $tbl_groupUser gu
								WHERE gu.group_id IN (".$grouplist.")";


						$groupMemberResult = Database::query($sql);


						if ($groupMemberResult) {
							while ($u = Database::fetch_array($groupMemberResult)) {
								$userlist [] = $u ['user_id']; // complete the user id list ...
							}
						}
					}


				    if (is_array($userlist)) {
				    	$userlist = "'".implode("', '", array_unique($userlist) )."'";

				    	// send to the created 'userlist'
					    $sqlmail = "SELECT user_id, lastname, firstname, email
						       					 FROM $tbl_user
						       					 WHERE user_id IN (".$userlist.")";
				    } else if (empty($_POST['not_selected_form'])) {
			    		if(empty($_SESSION['id_session']) || api_get_setting('use_session_mode')=='false') {
				    		// send to everybody
				    		$sqlmail = "SELECT user.user_id, user.email, user.lastname, user.firstname
					                     FROM $tbl_course_user, $tbl_user
					                     WHERE course_code='".Database::escape_string($_course['sysCode'])."'
					                     AND course_rel_user.user_id = user.user_id AND relation_type <>".COURSE_RELATION_TYPE_RRHH." ";
			    		} else {
			    			$sqlmail = "SELECT user.user_id, user.email, user.lastname, user.firstname
					                     FROM $tbl_user
										 INNER JOIN $tbl_session_course_user
										 	ON $tbl_user.user_id = $tbl_session_course_user.id_user
											AND $tbl_session_course_user.course_code = '".$_course['id']."'
											AND $tbl_session_course_user.id_session = ".intval($_SESSION['id_session']);

			    		}
			    	}

					if ($sqlmail != '') {
						$rs_mail = Database::query($sqlmail);
				    	/*=================================================================================
							    				send email one by one to avoid antispam
					    =================================================================================*/
						$db_name = Database::get_course_table(TABLE_MAIN_SURVEY);
						while ($myrow = Database::fetch_array($rs_mail)) {
							/*    Header : Bericht van uw lesgever - GES ($_cid)

								  Body :   John Doe (prenom + nom) <john_doe@hotmail.com> (email)

								  		   Morgen geen les!! (emailTitle)

								  		   Morgen is er geen les, de les wordt geschrapt wegens vergadering (newContent)
						    */

							$emailSubject = "[" . $_course['official_code'] . "] " . $emailTitle;

	                        if ($surveyid) {
	                        	$newContentone=str_replace("#page#","choose_language.php",$newContent);
								$newContenttwo=str_replace("#temp#",$template,$newContentone);
								$newContentthree=str_replace("#sid#",$surveyid,$newContenttwo);
								$newContentfour=str_replace("#mail#",$myrow["email"],$newContentthree);
	                            $newContentfive=str_replace("#db_name#",$db_name,$newContentfour);
								$newContentsix=str_replace("#uid#",$myrow["user_id"],$newContentfive);
	                			$message=stripslashes($newContentsix);
							    $sender_name = api_get_person_name($_SESSION['_user']['lastName'], $_SESSION['_user']['firstName'], null, PERSON_NAME_EMAIL_ADDRESS);
							    $email = $_SESSION['_user']['mail'];
								$headers="From:$sender_name\r\nReply-to: $email";
								//@mail($myrow["email"],stripslashes($emailTitle),$message,$headers);
								@api_mail('',$myrow["email"],stripslashes($emailTitle),$message,$sender_name,$email);
	                        } else {
	                            // intro of the email: receiver name and subject
								$mail_body = api_get_person_name($myrow["lastname"], $myrow["firstname"], null, PERSON_NAME_EMAIL_ADDRESS)."<br />\n".stripslashes($emailTitle)."<br />";
								// make a change for absolute url
	        					$newContent = str_replace('src=\"../../','src=\"'.api_get_path(WEB_PATH).'', $newContent);
	                            // main part of the email
	                            $mail_body .= trim(stripslashes($newContent));
	                            // signature of email: sender name and course URL after -- line
	                            $mail_body .= "<br />-- <br />";
	                            $mail_body .= api_get_person_name($_user['firstName'], $_user['lastName'], null, PERSON_NAME_EMAIL_ADDRESS)." \n";
	                            $mail_body .= "<br /> \n<a href=\"".api_get_path(WEB_CODE_PATH).'announcements/announcements.php?'.api_get_cidreq()."\">";
	                            $mail_body .= $_course['official_code'].' '.$_course['name'] . "</a>";

								$recipient_name	= api_get_person_name($myrow["firstname"], $myrow["lastname"], null, PERSON_NAME_EMAIL_ADDRESS);
		                        $mailid = $myrow["email"];

		                        $sender_name = api_get_person_name($_SESSION['_user']['firstName'], $_SESSION['_user']['lastName'], null, PERSON_NAME_EMAIL_ADDRESS);
		                        $sender_email = $_SESSION['_user']['mail'];

								// send attachment file
								$data_file = array();
								$sql = 'SELECT path, filename FROM '.$tbl_announcement_attachment.' WHERE announcement_id = "'.$insert_id.'"';
								$rs_attach = Database::query($sql);
								if (Database::num_rows($rs_attach) > 0) {
									$row_attach  = Database::fetch_array($rs_attach);
									$path_attach = api_get_path(SYS_COURSE_PATH).$_course['path'].'/upload/announcements/'.$row_attach['path'];
									$filename_attach = $row_attach['filename'];
									$data_file = array('path' => $path_attach,'filename' => $filename_attach);
								}

								@api_mail_html($recipient_name, $mailid, stripslashes($emailSubject), $mail_body, $sender_name, $sender_email, null, $data_file);
	                        }

							$sql_date="SELECT * FROM $db_name WHERE survey_id='$surveyid'";
							$res_date=Database::query($sql_date);
							$obj_date=Database::fetch_object($res_date);
							$end_date=$obj_date->avail_till;
							$table_reminder = Database :: get_main_table(TABLE_MAIN_SURVEY_REMINDER); // TODO: To be checked. TABLE_MAIN_SURVEY_REMINDER has not been defined.

							if ($_REQUEST['reminder']=="1") {
								$time=getdate();
								$time = $time['yday'];
								$time = $time+7;
								$sql="INSERT INTO $table_reminder(sid,db_name,email,subject,content,reminder_choice,reminder_time,avail_till) values('$surveyid','$db_name','$mailid','".addslashes($emailSubject)."','".addslashes($mail_body)."','1','$time','$end_date')";
								Database::query($sql);
							} else if ($_REQUEST['reminder']=="2") {
								$time=getdate();
								$time = $time['yday'];
								$time = $time+14;
								$sql="INSERT INTO $table_reminder(sid,db_name,email,subject,content,reminder_choice,reminder_time,avail_till) values('$surveyid','$db_name','$mailid','".addslashes($emailSubject)."','".addslashes($mail_body)."','1','$time','$end_date')";
								Database::query($sql);

							} else if ($_REQUEST['reminder']=="3") {
								$time=getdate();
								$time = $time['yday'];
								$time = $time+30;
								$sql="INSERT INTO $table_reminder(sid,db_name,email,subject,content,reminder_choice,reminder_time,avail_till) values('$surveyid','$db_name','$mailid','".addslashes($emailSubject)."','".addslashes($mail_body)."','1','$time','$end_date')";
								Database::query($sql);
							}
						}
						update_mail_sent($insert_id);
						$message = $added_and_sent;
					}

				} // $email_ann*/
			} // end condition token
		}	// isset

		// UNSET VARIABLES
		unset_session_resources();
		unset($form_elements);
		$_SESSION['formelements']=null;

		$newContent = null;
		$emailTitle = null;

		unset($emailTitle);
		unset($newContent);
		unset($content_to_modify);
		unset($title_to_modify);

		if($_REQUEST['publish_survey']) {

		 $surveyid=$_REQUEST['surveyid'];
		 $cidReq = $_REQUEST['cidReq'];
		 ?>
		<script>
			window.location.href="../survey/survey_list.php?<?php echo  api_get_cidreq(); ?>&published=published&surveyid=<?php echo Security::remove_XSS($_REQUEST['publish_survey']); ?>";
		</script>
		<?php
		}
	}	// if $submit Announcement
}


/*
	Tool introduction
*/

if (empty($_GET['origin']) || $_GET['origin'] !== 'learnpath') {
	//api_display_tool_title($nameTools);
	Display::display_introduction_section(TOOL_ANNOUNCEMENT);
}


/*
		MAIN SECTION
*/

/* DISPLAY LEFT COLUMN */

//condition for the session
$session_id = api_get_session_id();
$condition_session = api_get_session_condition($session_id);

if(api_is_allowed_to_edit(false,true))  {
 	// check teacher status
  	if (empty($_GET['origin']) or $_GET['origin'] !== 'learnpath') {

		$sql="SELECT
				announcement.*, ip.visibility, ip.to_group_id, ip.insert_user_id
				FROM $tbl_announcement announcement, $tbl_item_property ip
				WHERE announcement.id = ip.ref
				AND ip.tool='announcement'
				AND ip.visibility<>'2'
				$condition_session
				GROUP BY ip.ref
				ORDER BY display_order DESC
				LIMIT 0,$maximum";
	}
} else {
	// students only get to see the visible announcements


		if (empty($_GET['origin']) or $_GET['origin'] !== 'learnpath') {
			$group_memberships=GroupManager::get_group_ids($_course['dbName'], $_user['user_id']);

			if ((api_get_course_setting('allow_user_edit_announcement') && !api_is_anonymous())) {
				$cond_user_id = " AND (ip.lastedit_user_id = '".api_get_user_id()."' OR ( ip.to_user_id='".$_user['user_id']."'" .
						"OR ip.to_group_id IN (0, ".implode(", ", $group_memberships)."))) ";
			} else {
				$cond_user_id = " AND ( ip.to_user_id='".$_user['user_id']."'" .
						"OR ip.to_group_id IN (0, ".implode(", ", $group_memberships).")) ";
			}

			// the user is member of several groups => display personal announcements AND his group announcements AND the general announcements
			if (is_array($group_memberships) && count($group_memberships)>0) {
				$sql="SELECT
					announcement.*, ip.visibility, ip.to_group_id, ip.insert_user_id
					FROM $tbl_announcement announcement, $tbl_item_property ip
					WHERE announcement.id = ip.ref
					AND ip.tool='announcement'
					AND ip.visibility='1'
					$cond_user_id
					$condition_session
					GROUP BY ip.ref
					ORDER BY display_order DESC
					LIMIT 0,$maximum";
			} else {
				// the user is not member of any group
				// this is an identified user => show the general announcements AND his personal announcements
				if ($_user['user_id']) {

					if ((api_get_course_setting('allow_user_edit_announcement') && !api_is_anonymous())) {
						$cond_user_id = " AND (ip.lastedit_user_id = '".api_get_user_id()."' OR ( ip.to_user_id='".$_user['user_id']."' OR ip.to_group_id='0')) ";
					} else {
						$cond_user_id = " AND ( ip.to_user_id='".$_user['user_id']."' OR ip.to_group_id='0') ";
					}


					$sql="SELECT
						announcement.*, ip.visibility, ip.to_group_id, ip.insert_user_id
						FROM $tbl_announcement announcement, $tbl_item_property ip
						WHERE announcement.id = ip.ref
						AND ip.tool='announcement'
						AND ip.visibility='1'
						$cond_user_id
						$condition_session
						GROUP BY ip.ref
						ORDER BY display_order DESC
						LIMIT 0,$maximum";
				} else {

					if (api_get_course_setting('allow_user_edit_announcement')) {
						$cond_user_id = " AND (ip.lastedit_user_id = '".api_get_user_id()."' OR ip.to_group_id='0') ";
					} else {
						$cond_user_id = " AND ip.to_group_id='0' ";
					}

					// the user is not identiefied => show only the general announcements
					$sql="SELECT
						announcement.*, ip.visibility, ip.to_group_id, ip.insert_user_id
						FROM $tbl_announcement announcement, $tbl_item_property ip
						WHERE announcement.id = ip.ref
						AND ip.tool='announcement'
						AND ip.visibility='1'
						AND ip.to_group_id='0'
						$condition_session
						GROUP BY ip.ref
						ORDER BY display_order DESC
						LIMIT 0,$maximum";
				}
			}
		}


}

$result = Database::query($sql);
$announcement_number = Database::num_rows($result);

/*
		ADD ANNOUNCEMENT / DELETE ALL
*/

if (!$surveyid) {
	$show_actions = false;
	if ((api_is_allowed_to_edit(false,true) OR (api_get_course_setting('allow_user_edit_announcement') && !api_is_anonymous())) and (empty($_GET['origin']) or $_GET['origin'] !== 'learnpath')) {
		echo '<div class="actions">';
		echo "<a href='".api_get_self()."?".api_get_cidreq()."&action=add&origin=".(empty($_GET['origin'])?'':$_GET['origin'])."'>".Display::return_icon('announce_add.gif',get_lang('AddAnnouncement')).get_lang('AddAnnouncement')."</a>";
		$show_actions = true;
	}

	if (api_is_allowed_to_edit() && $announcement_number > 1) {
		if (!$show_actions)
			echo '<div class="actions">';
		echo "<a href=\"".api_get_self()."?".api_get_cidreq()."&action=delete_all\" onclick=\"javascript:if(!confirm('".get_lang("ConfirmYourChoice")."')) return false;\">".Display::return_icon('valvesdelete.gif',get_lang('AnnouncementDeleteAll')).get_lang('AnnouncementDeleteAll')."</a>\n";	}	// if announcementNumber > 1
	if ($show_actions)
		echo '</div>';
}

if (empty($_GET['origin']) OR $_GET['origin'] !== 'learnpath') {
	echo "\n\n<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
	echo "\t<tr>\n";
	echo "\t\t<td width=\"20%\" valign=\"top\">\n";
}

/*
		ANNOUNCEMENTS LIST
*/
if (!$surveyid) {
	if ($display_title_list == true) {
		echo "\t\t\t<table>\n";
		while ($myrow = Database::fetch_array($result)) {
			$title = $myrow['title'];
			$title = Security::remove_XSS($title);
			echo "\t\t\t\t<tr>\n";
			echo "\t\t\t\t\t<td width=\"15%\">\n";
			if ($myrow['visibility']==0) {
				$class="class=\"invisible\"";
			} else {
				$class="";
			}
			//validation when belongs to a session
			$session_img = api_get_session_image($myrow['session_id'], $_user['status']);

			echo "\t\t\t\t\t\t".Display::return_icon('lp_announcement.png', api_convert_and_format_date($myrow['end_date'], DATE_FORMAT_LONG), array('align' => 'absmiddle', 'Width' => '10', 'Height' => '10'))."  <a style=\"text-decoration:none\" href=\"announcements.php?".api_get_cidreq()."#".$myrow['id']."\" ".$class.">" . api_trunc_str($title, $length) . "</a>\n" . $session_img;
			echo "\t\t\t\t\t</td>\n\t\t\t\t</tr>\n";
		}
		echo "\t\t\t</table>\n";
	} // end $display_title_list == true
}

if (empty($_GET['origin']) or $_GET['origin'] !== 'learnpath') {
	echo   "\t\t</td>\n";
	echo "\t\t<td width=\"20\" background=\"../img/verticalruler.gif\">&nbsp;</td>\n";
	// START RIGHT PART
	echo	"\t\t<td valign=\"top\">\n";
}

/*
		DISPLAY ACTION MESSAGE
*/

if (isset($message) && $message == true) {
	Display::display_confirmation_message($message);
	$display_announcement_list = true;
	$display_form             = false;
}
if (!empty($error_message)) {
	Display::display_error_message($error_message);
	$display_announcement_list = false;
	$display_form             = true;
}

/*
		DISPLAY FORM
*/

if ($display_form == true) {

	$content_to_modify = stripslashes($content_to_modify);
	$title_to_modify = stripslashes($title_to_modify);

	// DISPLAY ADD ANNOUNCEMENT COMMAND
	echo '<form method="post" name="f1" enctype = "multipart/form-data" action="'.api_get_self().'?publish_survey='.Security::remove_XSS($surveyid).'&id='.Security::remove_XSS($_GET['id']).'&db_name='.$db_name.'&cidReq='.Security::remove_XSS($_GET['cidReq']).'" style="margin:0px;">'."\n";
	if (empty($_GET['id'])) {
		$form_name = get_lang('AddAnnouncement');
	} else {
		$form_name = get_lang('ModifyAnnouncement');
	}
	echo '<div class="row"><div class="form_header">'.$form_name.'</div></div>';

	//this variable defines if the course administrator can send a message to a specific user / group or not
	if (empty($_SESSION['toolgroup'])) {
		echo '	<div class="row">
					<div class="label">'.
						Display::return_icon('group.gif', get_lang('ModifyRecipientList'), array ('align' => 'absmiddle')).'<a href="#" onclick="if(document.getElementById(\'recipient_list\').style.display==\'none\') document.getElementById(\'recipient_list\').style.display=\'block\'; else document.getElementById(\'recipient_list\').style.display=\'none\';">'.get_lang('SentTo').'</a>
					</div>
					<div class="formw">';
		if (isset($_GET['id']) && is_array($to)) {
			echo '&nbsp;';
		} elseif (isset($_GET['remind_inactive'])) {
			$email_ann = '1';
			$_SESSION['select_groupusers']="show";
			$content_to_modify = sprintf(get_lang('RemindInactiveLearnersMailContent'),api_get_setting('siteName'), 7);
			$title_to_modify = sprintf(get_lang('RemindInactiveLearnersMailSubject'),api_get_setting('siteName'));
		} elseif (isset($_GET['remindallinactives']) && $_GET['remindallinactives']=='true') {
			// we want to remind inactive users. The $_GET['since'] parameter determines which users have to be warned (i.e the users who have been inactive for x days or more
			$since = isset($_GET['since']) ? intval($_GET['since']) : 6;
			// getting the users who have to be reminded
			$to = Tracking :: get_inactives_students_in_course($_course['id'],$since, $_SESSION['id_session']);
			// setting the variables for the form elements: the users who need to receive the message
			foreach($to as &$user) {
				$user = 'USER:'.$user;
			}
			// setting the variables for the form elements: the 'visible to' form element has to be expanded
			$_SESSION['select_groupusers']="show";
			// setting the variables for the form elements: the message has to be sent by email
			$email_ann = '1';
			// setting the variables for the form elements: the title of the email
			//$title_to_modify = sprintf(get_lang('RemindInactiveLearnersMailSubject'), api_get_setting('siteName'),' > ',$_course['name']);
			$title_to_modify = sprintf(get_lang('RemindInactiveLearnersMailSubject'), api_get_setting('siteName'));
			// setting the variables for the form elements: the message of the email
			//$content_to_modify = sprintf(get_lang('RemindInactiveLearnersMailContent'),api_get_setting('siteName'),' > ',$_course['name'],$since);
			$content_to_modify = sprintf(get_lang('RemindInactiveLearnersMailContent'),api_get_setting('siteName'),$since);
			// when we want to remind the users who have never been active then we have a different subject and content for the announcement
			if ($_GET['since'] == 'never') {
				$title_to_modify = sprintf(get_lang('RemindInactiveLearnersMailSubject'), api_get_setting('siteName'));
				$content_to_modify = get_lang('YourAccountIsActiveYouCanLoginAndCheckYourCourses');				
			}
		} else {
			echo get_lang('Everybody');
		}

		show_to_form($to);
		echo '		</div>
					</div>';

		if (!isset($announcement_to_modify) ) $announcement_to_modify ='';
		if ($announcement_to_modify=='') {
			($email_ann=='1')?$checked='checked':$checked='';
			echo '	<div class="row">
						<div class="label">
							<input class="checkbox" type="checkbox" value="1" name="email_ann" checked>
						</div>
						<div class="formw">'.get_lang('EmailOption').'
						</div>
					</div>';

		}
	} else {

		if (!isset($announcement_to_modify) ) {
			$announcement_to_modify ="";
		}
		if ($announcement_to_modify=='') {
			($email_ann=='1' || !empty($surveyid))?$checked='checked':$checked='';
			echo '<div class="row">
				  <div class="label">
					<input class="checkbox" type="checkbox" value="1" name="email_ann" '.$checked.'>
				  </div>
				  <div class="formw">'.
					get_lang('EmailOption').': '.get_lang('MyGroup').'&nbsp;&nbsp;<a href="#" onclick="if(document.getElementById(\'recipient_list\').style.display==\'none\') document.getElementById(\'recipient_list\').style.display=\'block\'; else document.getElementById(\'recipient_list\').style.display=\'none\';">'.get_lang('ModifyRecipientList').'</a>';
			show_to_form_group($_SESSION['toolgroup']);
			echo '</div></div>';
		}

	}

	if ($surveyid) {
		echo '	<div class="row">
					<div class="label">
						'.get_lang('EmailAddress').'
					</div>
					<div class="formw">
						<input type="text" name="emailsAdd" value="'.Security::remove_XSS($emails_add).'" size="52">(Comma separated for multiple)
					</div>
				</div>';
		echo '	<div class="row">
					<div class="label">
						'.get_lang('OnlyThoseAddresses').'
					</div>
					<div class="formw">
						<input type="checkbox" name="onlyThoseMails">
					</div>
				</div>';
	}

	// the announcement title
	echo '	<div class="row">
				<div id="msg_error" style="display:none;color:red;margin-left:20%"></div>
				<div class="label">
					<span class="form_required">*</span> '.get_lang('EmailTitle').'
				</div>
				<div class="formw">

					<input type="text" id="emailTitle" name="emailTitle" value="'.Security::remove_XSS($title_to_modify).'" size="60">
				</div>
			</div>';

	unset($title_to_modify);
	$title_to_modify = null;

	if (!isset($announcement_to_modify) ) $announcement_to_modify ="";
	if (!isset($content_to_modify) ) 		$content_to_modify ="";
	if (!isset($title_to_modify)) 		$title_to_modify = "";

	echo '<input type="hidden" name="id" value="'.$announcement_to_modify.'" />';

	if ($surveyid) {
	$content_to_modify='<br /><a href="'.api_get_path(WEB_CODE_PATH).'/survey/#page#?temp=#temp#&surveyid=#sid#&uid=#uid#&mail=#mail#&db_name=#db_name">'.get_lang('ClickHereToOpenSurvey').'</a><br />
									'.get_lang('OrCopyPasteUrl').' <br />
									'.api_get_path(WEB_CODE_PATH).'/survey/#page#?temp=#temp#&surveyid=#sid#&uid=#uid#&mail=#mail#&db_name=#db_name&nbsp;';
	}

    $oFCKeditor = new FCKeditor('newContent') ;

	$oFCKeditor->Width		= '100%';
	$oFCKeditor->Height		= '300';

	if(!api_is_allowed_to_edit()) {
		$oFCKeditor->ToolbarSet = "AnnouncementsStudent";
	} else {
		$oFCKeditor->ToolbarSet = "Announcements";
	}

	$oFCKeditor->Value		= $content_to_modify;

	echo $oFCKeditor->CreateHtml();

	//File attachment

	echo '	<div style="float:left; padding:5px 5px;" >
				<div class="label">
					<a href="javascript://" onclick="return plus_attachment();"><span id="plus"><img style="vertical-align:middle;" src="../img/div_show.gif" alt="" />&nbsp;'.get_lang('AddAnAttachment').'</span></a>
				</div>
				<div class="formw">
					<table id="options" style="display: none;">
					<tr>
						<td colspan="2">
					        <label for="file_name">'.get_lang('FileName').'&nbsp;</label>
					        <input type="file" name="user_upload"/>
					    </td>
					 </tr>
					 <tr>
					    <td colspan="2">
					    	<label for="comment">'.get_lang('FileComment').'</label><br />
					    	<textarea name="file_comment" rows ="4" cols = "34" ></textarea>
					    </td>
				    </tr>
			    </table>
			 </div>
			</div>';



	echo'<br />';
	if (empty($_SESSION['toolgroup'])) {
		echo '<input type="hidden" name="submitAnnouncement" value="OK">';
		echo '<input type="hidden" name="sec_token" value="'.$stok.'" />';
		echo '<button class="save" type="button"  value="'.'  '.get_lang('Send').'  '.'" onclick="selectAll(this.form.elements[3],true)" >'.get_lang('ButtonPublishAnnouncement').'</button><br /><br />';
	} else {
		echo '<input type="hidden" name="submitAnnouncement" value="OK">';
		echo '<input type="hidden" name="sec_token" value="'.$stok.'" />';
		echo '<button class="save" type="button"  value="'.'  '.get_lang('Send').'  '.'" onclick="selectAll(this.form.elements[4],true)" >'.get_lang('ButtonPublishAnnouncement').'</button><br /><br />';
	}
	echo '</form><br />';

	if ((isset($_GET['action']) && isset($_GET['id']) && is_array($to))||isset($_GET['remindallinactives'])||isset($_GET['remind_inactive'])) {
		echo '<script>document.getElementById(\'recipient_list\').style.display=\'block\';</script>';
	}

} // displayform



/*
		DISPLAY ANNOUNCEMENT LIST
*/


if ($display_announcement_list && !$surveyid) {
	// by default we use the id of the current user. The course administrator can see the announcement of other users by using the user / group filter
	$user_id=$_user['user_id'];
	if (isset($_SESSION['user'])) {
		$user_id=$_SESSION['user'];
	}
	if (isset($_SESSION['group'])) {
		$group_id=$_SESSION['group'];
	}

	//$group_memberships=GroupManager::get_group_ids($_course['dbName'], $_user['user_id']);
	$group_memberships=GroupManager::get_group_ids($_course['dbName'],$_user['user_id']);

	if (api_is_allowed_to_edit(false,true)) {
		// A.1. you are a course admin with a USER filter
		// => see only the messages of this specific user + the messages of the group (s)he is member of.
		if (isset($_SESSION['user'])) {
			if (is_array($group_memberships) && count($group_memberships)>0) {
				$sql="SELECT
					announcement.*, ip.visibility, ip.to_group_id, ip.insert_user_id
					FROM $tbl_announcement announcement, $tbl_item_property ip
					WHERE announcement.id = ip.ref
					AND ip.tool='announcement'
					AND	(ip.to_user_id=$user_id OR ip.to_group_id IN (0, ".implode(", ", $group_memberships).") )
					$condition_session
					ORDER BY display_order DESC";

			} else {
				$sql="SELECT
					announcement.*, ip.visibility, ip.to_group_id, ip.insert_user_id
					FROM $tbl_announcement announcement, $tbl_item_property ip
					WHERE announcement.id = ip.ref
					AND ip.tool='announcement'
					AND (ip.to_user_id=$user_id OR ip.to_group_id='0')
					AND ip.visibility='1'
					$condition_session
					ORDER BY display_order DESC";

			}
		} elseif (isset($_SESSION['group'])) {
			// A.2. you are a course admin with a GROUP filter
			// => see only the messages of this specific group

			$sql="SELECT
				announcement.*, ip.visibility, ip.to_group_id, ip.insert_user_id
				FROM $tbl_announcement announcement, $tbl_item_property ip
				WHERE announcement.id = ip.ref
				AND ip.tool='announcement'
				AND (ip.to_group_id=$group_id OR ip.to_group_id='0')
				$condition_session
				GROUP BY ip.ref
				ORDER BY display_order DESC";
		} else {
			// A.3 you are a course admin without any group or user filter
			// A.3.a you are a course admin without user or group filter but WITH studentview
			// => see all the messages of all the users and groups without editing possibilities

			if (isset($isStudentView) and $isStudentView=="true") {

				$sql="SELECT
					announcement.*, ip.visibility, ip.to_group_id, ip.insert_user_id
					FROM $tbl_announcement announcement, $tbl_item_property ip
					WHERE announcement.id = ip.ref
					AND ip.tool='announcement'
					AND ip.visibility='1'
					$condition_session
					GROUP BY ip.ref
					ORDER BY display_order DESC";
			} else {
				// A.3.a you are a course admin without user or group filter and WTIHOUT studentview (= the normal course admin view)
				// => see all the messages of all the users and groups with editing possibilities
				$sql="SELECT
					announcement.*, ip.visibility, ip.to_group_id, ip.insert_user_id
					FROM $tbl_announcement announcement, $tbl_item_property ip
					WHERE announcement.id = ip.ref
					AND ip.tool='announcement'
					AND (ip.visibility='0' or ip.visibility='1')
					$condition_session
					GROUP BY ip.ref
					ORDER BY display_order DESC";

			}
		}
	} else {
	//STUDENT
			if (is_array($group_memberships) && count($group_memberships)>0) {

				if ((api_get_course_setting('allow_user_edit_announcement') && !api_is_anonymous())) {
					$cond_user_id = " AND (ip.lastedit_user_id = '".api_get_user_id()."' OR (ip.to_user_id=$user_id OR ip.to_group_id IN (0, ".implode(", ", $group_memberships).") )) ";
				} else {
					$cond_user_id = " AND (ip.to_user_id=$user_id OR ip.to_group_id IN (0, ".implode(", ", $group_memberships).")) ";
				}

				$sql="SELECT
					announcement.*, ip.visibility, ip.to_group_id, ip.insert_user_id
					FROM $tbl_announcement announcement, $tbl_item_property ip
					WHERE announcement.id = ip.ref
					AND ip.tool='announcement'
					$cond_user_id
					$condition_session
					AND ip.visibility='1'
					ORDER BY display_order DESC";
			} else {

				if ($_user['user_id']) {

					if ((api_get_course_setting('allow_user_edit_announcement') && !api_is_anonymous())) {
						$cond_user_id = " AND (ip.lastedit_user_id = '".api_get_user_id()."' OR (ip.to_user_id='".$_user['user_id']."' OR ip.to_group_id='0')) ";
					} else {
						$cond_user_id = " AND (ip.to_user_id='".$_user['user_id']."' OR ip.to_group_id='0') ";
					}

					$sql="SELECT
						announcement.*, ip.visibility, ip.to_group_id, ip.insert_user_id
						FROM $tbl_announcement announcement, $tbl_item_property ip
						WHERE announcement.id = ip.ref
						AND ip.tool='announcement'
						$cond_user_id
						$condition_session
						AND ip.visibility='1'
						AND announcement.session_id IN(0,".intval($_SESSION['id_session']).")
						ORDER BY display_order DESC";
				} else {

					if ((api_get_course_setting('allow_user_edit_announcement') && !api_is_anonymous())) {
						$cond_user_id = " AND (ip.lastedit_user_id = '".api_get_user_id()."' OR ip.to_group_id='0' ) ";
					} else {
						$cond_user_id = " AND ip.to_group_id='0' ";
					}

					$sql="SELECT
						announcement.*, ip.visibility, ip.to_group_id, ip.insert_user_id
						FROM $tbl_announcement announcement, $tbl_item_property ip
						WHERE announcement.id = ip.ref
						AND ip.tool='announcement'
						$cond_user_id
						$condition_session
						AND ip.visibility='1'
						AND announcement.session_id IN(0,".intval($_SESSION['id_session']).")";
				}
			}

	}

	$result = Database::query($sql);
	$num_rows = Database::num_rows($result);

/*
		DISPLAY: NO ITEMS
*/

	if ($num_rows == 0) {
		echo get_lang('NoAnnouncements');;
	}

	$iterator = 1;
	$bottomAnnouncement = $announcement_number;

	echo "\t\t\t<table width=\"100%\" class=\"data_table\">\n";

	$displayed=array();

	while ($myrow = Database::fetch_array($result)) {

		if (!in_array($myrow['id'], $displayed)) {
			$title		 = $myrow['title'];
			$content	 = $myrow['content'];

			$content     = make_clickable($content);
			$content     = text_filter($content);

			/* DATE */

			$last_post_datetime = $myrow['end_date'];

			list($last_post_date, $last_post_time) = split(" ", $last_post_datetime);
			list($year, $month, $day) = explode("-", $last_post_date);
			list($hour, $min) = explode(":", $last_post_time);
			$announceDate = mktime((int)$hour, (int)$min, 0, (int)$month, (int)$day, (int)$year);

			// the styles
			if ($myrow['visibility']=='0') {
				$style='invisible';
			} else {
				$style = '';
			}

			echo	"\t\t\t\t<tr class=\"".$style."\">";

			/* THE ICONS */

			echo "\t\t\t\t\t<th>\n";
			// anchoring
			echo "<a name=\"".(int)($myrow["id"])."\"></a>\n";
			// User or group icon
			if ($myrow['to_group_id']!== '0' and $myrow['to_group_id']!== 'NULL') {
				echo "\t\t\t\t\t\t".Display::return_icon('group.gif', get_lang('AnnounceSentToUserSelection'))."\n";
			}
			// the email icon
			if ($myrow['email_sent'] == '1') {
				echo "\t\t\t\t\t\t".Display::return_icon('email.gif', get_lang('AnnounceSentByEmail'))."\n";
			}
			echo "\t\t\t\t\t</th>\n";

			/* TITLE */

			echo "\t\t\t\t\t<th>".Security::remove_XSS($title)."</th>\n";
			echo "\t\t\t\t\t<th>" . get_lang("SentTo") . " : &nbsp; ";

			$sent_to=sent_to("announcement", $myrow['id']);
			$sent_to_form=sent_to_form($sent_to);
			$user_info=api_get_user_info($myrow['insert_user_id']);

			echo '&nbsp;&nbsp;&nbsp;'.get_lang('By').' : &nbsp;'.str_replace(' ', '&nbsp;', api_get_person_name($user_info['firstName'], $user_info['lastName']));
			echo "\t\t\t\t\t</th>\n","\t\t\t\t</tr>\n";
			echo "\t\t\t\t<tr class='row_odd'>\n\t\t\t\t\t<td class=\"announcements_datum\" colspan=\"3\">";
			echo get_lang('AnnouncementPublishedOn')," : ", api_convert_and_format_date($last_post_datetime, DATE_FORMAT_LONG, date_default_timezone_get());
			echo "</td>\n\t\t\t\t</tr>\n";

			/* CONTENT */

			echo "\t\t\t\t<tr class=\"$text_style\">\n\t\t\t\t\t<td colspan=\"3\">\n";
			echo $content."\t\t\t\t\t</td>\n\t\t\t\t</tr>\n";

			/* RESOURCES */

			echo "<tr class='row_odd'>\n<td colspan=\"3\">\n";

			if (check_added_resources("Ad_Valvas", $myrow["id"])) {
				echo "<i>".get_lang('AddedResources')."</i><br />";
				display_added_resources("Ad_Valvas", $myrow["id"]);
			}

			// we can edit if : we are the teacher OR the element belongs to the session we are coaching OR the option to allow users to edit is on
			if (api_is_allowed_to_edit() OR (api_is_course_coach() && api_is_element_in_the_session(TOOL_ANNOUNCEMENT,$myrow['id'])) OR (api_get_course_setting('allow_user_edit_announcement') && !api_is_anonymous())) {

				/* SHOW MOD/DEL/VIS FUNCTIONS */
				echo	"<a href=\"".api_get_self()."?".api_get_cidreq()."&action=modify&id=".$myrow['id']."\">",
						Display::return_icon('edit.gif', get_lang('Edit')),
						"</a>";

				if (api_is_allowed_to_edit(false,true)) {
					echo "<a href=\"".api_get_self()."?".api_get_cidreq()."&action=delete&id=".$myrow['id']."&sec_token=".$stok."\" onclick=\"javascript:if(!confirm('".addslashes(api_htmlentities(get_lang('ConfirmYourChoice'),ENT_QUOTES,$charset))."')) return false;\">",
						Display::return_icon('delete.gif', get_lang('Delete')),
						"</a>";
				}

				if ($myrow['visibility']==1) {
						$image_visibility="visible";
						$alt_visibility=get_lang('Hide');
				} else {
						$image_visibility="invisible";
						$alt_visibility=get_lang('Visible');
				}

				echo 	"<a href=\"".api_get_self()."?".api_get_cidreq()."&origin=".(!empty($_GET['origin'])?Security::remove_XSS($_GET['origin']):'')."&action=showhide&id=".$myrow['id']."&sec_token=".$stok."\">".
						Display::return_icon($image_visibility.'.gif', $alt_visibility)."</a>";

				// DISPLAY MOVE UP COMMAND only if it is not the top announcement
				if ($iterator != 1) {
					echo	"<a href=\"".api_get_self()."?".api_get_cidreq()."&up=".$myrow["id"]."&sec_token=".$stok."\">",
							Display::return_icon('up.gif', get_lang('Up'))."</a>";
				}

				if ($iterator < $bottomAnnouncement) {
					echo	"<a href=\"".api_get_self()."?".api_get_cidreq()."&down=".$myrow["id"]."&sec_token=".$stok."\">".
							Display::return_icon('down.gif', get_lang('Down'))."</a>";
				}

				//delete attachment file
				if($_GET['action'] == 'delete') {
					$id = $_GET['id_attach'];
					delete_announcement_attachment_file($id);
				}

				// show attachment list
				$attachment_list = array();
				$attachment_list = get_attachment($myrow['id']);

				if (count($attachment_list)>0) {
					$realname=$attachment_list['path'];
					$user_filename=$attachment_list['filename'];
					$full_file_name = 'download.php?file='.$realname;
					echo '<br/>';
					echo '<br/>';
					echo Display::return_icon('attachment.gif',get_lang('Attachment'));
					echo '<a href="'.$full_file_name.'';
					echo ' "> '.$user_filename.' </a>';
					echo '<span class="forum_attach_comment" >'.$attachment_list['comment'].'</span>';

					if (api_is_allowed_to_edit()) {
						echo '&nbsp;&nbsp;<a href="'.api_get_self().'?'.api_get_cidreq().'&action=delete&id_attach='.$attachment_list['id'].'" onclick="javascript:if(!confirm(\''.addslashes(api_htmlentities(get_lang("ConfirmYourChoice"),ENT_QUOTES,$charset)).'\')) return false;">'.Display::return_icon('delete.gif',get_lang('Delete')).'</a><br />';
					}
				}


				echo "</td>\n</tr>\n";

				$iterator ++;
			} else { // end of is_allowed_to_edit
				//students view
				// show attachment list
				$attachment_list = array();
				$attachment_list = get_attachment($myrow['id']);

				if (count($attachment_list)>0) {
					$realname=$attachment_list['path'];
					$user_filename=$attachment_list['filename'];
					$full_file_name = 'download.php?file='.$realname;
					echo '<br/>';
					echo '<br/>';
					echo Display::return_icon('attachment.gif',get_lang('Attachment'));
					echo '<a href="'.$full_file_name.'';
					echo ' "> '.$user_filename.' </a>';
					echo '<span class="forum_attach_comment" >'.$attachment_list['comment'].'</span>';
				}
			}

			echo "<tr><td width=\"100%\" colspan=\"3\"><a href=\"#top\">".Display::return_icon('top.gif', get_lang('Top'))."</a></td></tr>";

		}
		$displayed[]=$myrow['id'];
	}	// end while ($myrow = Database::fetch_array($result))

	echo "</table>";

}	// end: if ($displayAnnoucementList)

echo "</table>";
if (!empty($display_specific_announcement)) display_announcement($announcement_id);


/*
		FOOTER
*/
if (empty($_GET['origin']) or $_GET['origin'] !== 'learnpath') {
	//we are not in learnpath tool
	Display::display_footer();
}
