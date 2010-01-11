<?php //$Id: announcements.inc.php 21903 2009-07-08 17:28:02Z juliomontoya $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2008 Dokeos SPRL
	Copyright (c) various contributors

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, rue du Corbeau, 108, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
/**
==============================================================================
* Include file with functions for the announcements module.
* @package dokeos.announcements
==============================================================================
*/

$tbl_announcement_attachment = Database::get_course_table(TABLE_ANNOUNCEMENT_ATTACHMENT);

/*
==============================================================================
		DISPLAY FUNCTIONS
==============================================================================
*/
/**
* displays one specific announcement
* @param $announcement_id, the id of the announcement you want to display
* @todo remove globals
* @todo more security checking
*/
function display_announcement($announcement_id)
{
	global $_user, $dateFormatLong;

	if ($announcement_id != strval(intval($announcement_id))) { return false; } // potencial sql injection

	$tbl_announcement 	= Database::get_course_table(TABLE_ANNOUNCEMENT);
	$tbl_item_property	= Database::get_course_table(TABLE_ITEM_PROPERTY);

	if ($_user['user_id'])
	{
		$sql_query = "	SELECT announcement.*, toolitemproperties.*
						FROM $tbl_announcement announcement, $tbl_item_property toolitemproperties
						WHERE announcement.id = toolitemproperties.ref
						AND announcement.id = '$announcement_id'
						AND toolitemproperties.tool='announcement'
						AND (toolitemproperties.to_user_id='".$_user['user_id']."' OR toolitemproperties.to_group_id='0')
						AND toolitemproperties.visibility='1'
						ORDER BY display_order DESC";

	}
	else
	{
		$sql_query = "	SELECT announcement.*, toolitemproperties.*
						FROM $tbl_announcement announcement, $tbl_item_property toolitemproperties
						WHERE announcement.id = toolitemproperties.ref
						AND announcement.id = '$announcement_id'
						AND toolitemproperties.tool='announcement'
						AND toolitemproperties.to_group_id='0'
						AND toolitemproperties.visibility='1'";
	}
	$sql_result = Database::query($sql_query,__FILE__,__LINE__);
	$result = Database::fetch_array($sql_result);

	if ($result !== false) // A sanity check.
	{
		$title		 = $result['title'];
		$content	 = $result['content'];
		$content     = make_clickable($content);
		$content     = text_filter($content);
		$last_post_datetime = $result['insert_date'];// post time format  datetime de mysql
		list($last_post_date, $last_post_time) = split(" ", $last_post_datetime);
	}

	echo "<table height=\"100\" width=\"100%\" border=\"1\" cellpadding=\"5\" cellspacing=\"0\" id=\"agenda_list\">\n";
	echo "<tr class=\"data\"><td>" . $title . "</td></tr>\n";
	echo "<tr><td class=\"announcements_datum\">" . get_lang('AnnouncementPublishedOn') . " : " . api_ucfirst(format_locale_date($dateFormatLong,strtotime($last_post_date) ) ) . "</td></tr>\n";
	echo "<tr class=\"text\"><td>$content</td></tr>\n";
	echo "</table>";
}

/*======================================
	          SHOW_TO_FORM
======================================*/
/**
* this function shows the form for sending a message to a specific group or user.
*/
function show_to_form($to_already_selected)
{
	$user_list=get_course_users();
	$group_list=get_course_groups();

	if ($to_already_selected == "")
          $to_already_selected = array();

	echo "\n<table id=\"recipient_list\" style=\"display: none;\">\n";
	echo "\t<tr>\n";

	// the form containing all the groups and all the users of the course
	echo "\t\t<td>\n";
	echo "<strong>".get_lang('Users')."</strong><br />";
	construct_not_selected_select_form($group_list,$user_list,$to_already_selected);
	echo "\t\t</td>\n";

	// the buttons for adding or removing groups/users
	echo "\n\t\t<td valign=\"middle\">\n";
	/*echo "\t\t<input	type=\"button\"	",
				"onClick=\"javascript: move(this.form.elements[0],this.form.elements[3])\" ",// 7 & 4 : fonts
				"value=\"   >>   \">",

				"\n\t\t<p>&nbsp;</p>",

				"\n\t\t<input	type=\"button\"",
				"onClick=\"javascript: move(this.form.elements[3],this.form.elements[0])\" ",
				"value=\"   <<   \">";*/
				
?>
<button class="arrowr" type="button" onClick="javascript: move(this.form.elements[0], this.form.elements[3])" onClick="javascript: move(this.form.elements[0], this.form.elements[3])"></button>	
<br /> <br />
<button class="arrowl" type="button" onClick="javascript: move(this.form.elements[3], this.form.elements[0])" onClick="javascript: move(this.form.elements[3], this.form.elements[0])"></button>
<?php
	echo "\t\t</td>\n";
	echo "\n\t\t<td>\n";

	// the form containing the selected groups and users
	echo "<strong>".get_lang('DestinationUsers')."</strong><br />";
	construct_selected_select_form($group_list,$user_list,$to_already_selected);
	echo "\t\t</td>\n";
	echo "\t</tr>\n";
	echo "</table>";
}


/*===========================================
	  CONSTRUCT_NOT_SELECT_SELECT_FORM
===========================================*/
/**
* this function shows the form for sending a message to a specific group or user.
*/
function construct_not_selected_select_form($group_list=null, $user_list=null,$to_already_selected)
{

	echo "\t\t<select name=\"not_selected_form[]\" size=5 style=\"width:200px\" multiple>\n";
	// adding the groups to the select form
	if ($group_list)
	{
		foreach($group_list as $this_group)
		{
			if (is_array($to_already_selected))
			{
				if (!in_array("GROUP:".$this_group['id'],$to_already_selected)) // $to_already_selected is the array containing the groups (and users) that are already selected
				{
					echo	"\t\t<option value=\"GROUP:".$this_group['id']."\">",
					"G: ",$this_group['name']," - " . $this_group['userNb'] . " " . get_lang('Users') .
					"</option>\n";
				}
			}
		}
		// a divider
		echo	"\t\t<option value=\"\">---------------------------------------------------------</option>\n";
	}
	// adding the individual users to the select form
	foreach($user_list as $this_user)
	{
		if (is_array($to_already_selected)) {
			if (!(in_array("USER:".$this_user["user_id"],$to_already_selected))) // $to_already_selected is the array containing the users (and groups) that are already selected
			{
				echo	"\t\t<option value=\"USER:",$this_user["user_id"],"\">",
					"", api_get_person_name($this_user['firstname'], $this_user['lastname']),
					"</option>\n";
			}
		}

	}
	echo "\t\t</select>\n";
}



/*==========================================
	   CONSTRUCT_SELECTED_SELECT_FORM
==========================================*/
/**
* this function shows the form for sending a message to a specific group or user.
*/
function construct_selected_select_form($group_list=null, $user_list=null,$to_already_selected)
{
	// we separate the $to_already_selected array (containing groups AND users into
	// two separate arrays
	$groupuser = array();
	if (is_array($to_already_selected))
	{
		$groupuser=separate_users_groups($to_already_selected);
	}
	$groups_to_already_selected=$groupuser['groups'];
	$users_to_already_selected=$groupuser['users'];

	// we load all the groups and all the users into a reference array that we use to search the name of the group / user
	$ref_array_groups=get_course_groups();
	$ref_array_users=get_course_users();

	// we construct the form of the already selected groups / users
	echo "\t\t<select name=\"selectedform[]\" size=\"5\" multiple style=\"width:200px\" width=\"200px\">";
	if (is_array($to_already_selected)) {
		foreach($to_already_selected as $groupuser)
		{
			list($type,$id)=explode(":",$groupuser);
			if ($type=="GROUP")
			{
				echo "\t\t<option value=\"".$groupuser."\">G: ".$ref_array_groups[$id]['name']."</option>";
			}
			else
			{
				foreach($ref_array_users as $key=>$value){
					if($value['user_id']==$id){
						echo "\t\t<option value=\"".$groupuser."\">".api_get_person_name($value['firstname'], $value['lastname'])."</option>";
						break;
					}
				}
			}
		}
	} else {
		if ($to_already_selected=='everyone') {
			// adding the groups to the select form
			if (is_array($ref_array_groups))
			{
				foreach($ref_array_groups as $this_group)
				{
					//api_display_normal_message("group " . $thisGroup[id] . $thisGroup[name]);
					if (!is_array($to_already_selected) || !in_array("GROUP:".$this_group['id'],$to_already_selected)) // $to_already_selected is the array containing the groups (and users) that are already selected
					{
						echo	"\t\t<option value=\"GROUP:".$this_group['id']."\">",
							"G: ",$this_group['name']," &ndash; " . $this_group['userNb'] . " " . get_lang('Users') .
							"</option>\n";
					}
				}
			}
			// adding the individual users to the select form
			foreach($ref_array_users as $this_user)
			{
				if (!is_array($to_already_selected) || !in_array("USER:".$this_user['user_id'],$to_already_selected)) // $to_already_selected is the array containing the users (and groups) that are already selected
				{
					echo	"\t\t<option value=\"USER:",$this_user['user_id'],"\">",
						"", api_get_person_name($this_user['lastname'], $this_user['firstname']),
						"</option>\n";
				}
			}
		}
	}
	echo "</select>\n";
}


/**
* this function shows the form for sending a message to a specific group or user.
*/
function show_to_form_group($group_id)
{

	echo "\n<table id=\"recipient_list\" style=\"display: none;\">\n";
	echo "\t<tr>\n";

	echo "\t\t<td>\n";

	echo "\t\t<select name=\"not_selected_form[]\" size=5 style=\"width:200px\" multiple>\n";
	$group_users = GroupManager::get_subscribed_users($group_id);
	foreach ($group_users as $user){
		echo '<option value="'.$user['user_id'].'">'.api_get_person_name($user['firstname'], $user['lastname']).'</option>';
	}
	echo '</select>';

	echo "\t\t</td>\n";

	// the buttons for adding or removing groups/users
	echo "\n\t\t<td valign=\"middle\">\n";
	/*echo "\t\t<input	type=\"button\"	",
				"onClick=\"move(this.form.elements[1],this.form.elements[4])\" ",// 7 & 4 : fonts
				"value=\"   >>   \">",

				"\n\t\t<p>&nbsp;</p>",

				"\n\t\t<input	type=\"button\"",
				"onClick=\"move(this.form.elements[4],this.form.elements[1])\" ",
				"value=\"   <<   \">";*/
				
?>
<button class="arrowr" type="button" onClick="javascript: move(this.form.elements[1], this.form.elements[4])" onClick="javascript: move(this.form.elements[1], this.form.elements[4])"></button>	
<br /> <br />
<button class="arrowl" type="button" onClick="javascript: move(this.form.elements[4], this.form.elements[1])" onClick="javascript: move(this.form.elements[4], this.form.elements[1])"></button>
<?php				
	echo "\t\t</td>\n";
	echo "\n\t\t<td>\n";

	echo "\t\t<select name=\"selectedform[]\" size=5 style=\"width:200px\" multiple>\n";
	echo '</select>';

	echo "\t\t</td>\n";
	echo "\t</tr>\n";
	echo "</table>";
}


/*
==============================================================================
		DATA FUNCTIONS
==============================================================================
*/

/**
* this function gets all the users of the course,
* including users from linked courses
*/
function get_course_users()
{
	//this would return only the users from real courses:
	//$user_list = CourseManager::get_user_list_from_course_code(api_get_course_id());

	$user_list = CourseManager::get_real_and_linked_user_list(api_get_course_id(), true, $_SESSION['id_session']);
	return $user_list;
}

/**
* this function gets all the groups of the course,
* not including linked courses
*/
function get_course_groups()
{
	$new_group_list = CourseManager::get_group_list_of_course(api_get_course_id(), intval($_SESSION['id_session']));
	return $new_group_list;
}

/*======================================
	          LOAD_EDIT_USERS
======================================*/
/**
* This tools loads all the users and all the groups who have received
* a specific item (in this case an announcement item)
*/
function load_edit_users($tool, $id)
{
	global $_course;
	global $tbl_item_property;

	$tool = Database::escape_string($tool);
	$id = Database::escape_string($id);

	$sql="SELECT * FROM $tbl_item_property WHERE tool='$tool' AND ref='$id'";
	$result=Database::query($sql,__FILE__,__LINE__) or die (mysql_error());
	while ($row=Database::fetch_array($result))
	{
		$to_group=$row['to_group_id'];
		switch ($to_group)
		{
			// it was send to one specific user
			case null:
				$to[]="USER:".$row['to_user_id'];
				break;
			// it was sent to everyone
			case 0:
				 return "everyone";
				 exit;
				 break;
			default:
				$to[]="GROUP:".$row['to_group_id'];
		}
	}
	return $to;
}



/*======================================
	 USER_GROUP_FILTER_JAVASCRIPT
======================================*/
/**
* returns the javascript for setting a filter
* this goes into the $htmlHeadXtra[] array
*/
function user_group_filter_javascript()
{
	return "<script language=\"JavaScript\" type=\"text/JavaScript\">
	<!--
	function jumpMenu(targ,selObj,restore)
	{
	  eval(targ+\".location='\"+selObj.options[selObj.selectedIndex].value+\"'\");
	  if (restore) selObj.selectedIndex=0;
	}
	//-->
	</script>
	";
}


/*======================================
	         TO_JAVASCRIPT
========================================*/
/**
* returns all the javascript that is required for easily
* setting the target people/groups
* this goes into the $htmlHeadXtra[] array
*/
function to_javascript()
{
	return "<script type=\"text/javascript\" language=\"JavaScript\">

	<!-- Begin javascript menu swapper

	function move(fbox,	tbox)
	{
		var	arrFbox	= new Array();
		var	arrTbox	= new Array();
		var	arrLookup =	new	Array();

		var	i;
		for	(i = 0;	i <	tbox.options.length; i++)
		{
			arrLookup[tbox.options[i].text]	= tbox.options[i].value;
			arrTbox[i] = tbox.options[i].text;
		}

		var	fLength	= 0;
		var	tLength	= arrTbox.length;

		for(i =	0; i < fbox.options.length;	i++)
		{
			arrLookup[fbox.options[i].text]	= fbox.options[i].value;

			if (fbox.options[i].selected &&	fbox.options[i].value != \"\")
			{
				arrTbox[tLength] = fbox.options[i].text;
				tLength++;
			}
			else
			{
				arrFbox[fLength] = fbox.options[i].text;
				fLength++;
			}
		}

		arrFbox.sort();
		arrTbox.sort();

		var arrFboxGroup = new Array();
		var arrFboxUser = new Array();
		var prefix_x;

		for (x = 0; x < arrFbox.length; x++) {
			prefix_x = arrFbox[x].substring(0,2);
			if (prefix_x == 'G:') {
				arrFboxGroup.push(arrFbox[x]);
			} else {
				arrFboxUser.push(arrFbox[x]);
			}
		}

		arrFboxGroup.sort();
		arrFboxUser.sort();
		arrFbox = arrFboxGroup.concat(arrFboxUser);

		var arrTboxGroup = new Array();
		var arrTboxUser = new Array();
		var prefix_y;

		for (y = 0; y < arrTbox.length; y++) {
			prefix_y = arrTbox[y].substring(0,2);
			if (prefix_y == 'G:') {
				arrTboxGroup.push(arrTbox[y]);
			} else {
				arrTboxUser.push(arrTbox[y]);
			}
		}

		arrTboxGroup.sort();
		arrTboxUser.sort();
		arrTbox = arrTboxGroup.concat(arrTboxUser);

		fbox.length	= 0;
		tbox.length	= 0;

		var	c;
		for(c =	0; c < arrFbox.length; c++)
		{
			var	no = new Option();
			no.value = arrLookup[arrFbox[c]];
			no.text	= arrFbox[c];
			fbox[c]	= no;
		}
		for(c =	0; c < arrTbox.length; c++)
		{
			var	no = new Option();
			no.value = arrLookup[arrTbox[c]];
			no.text	= arrTbox[c];
			tbox[c]	= no;
		}
	}

	function validate()
	{
		var	f =	document.new_calendar_item;
		f.submit();
		return true;
	}


	function selectAll(cbList,bSelect,showwarning)
	{

		if (document.getElementById('emailTitle').value==''){
			document.getElementById('msg_error').innerHTML='".get_lang('FieldRequired')."';
			document.getElementById('msg_error').style.display='block';
			document.getElementById('emailTitle').focus();
		}else {
			if (cbList.length <	1) {
				if (!confirm(\"".get_lang('Send2All')."\")) {
					return false;
				}
			}
			for	(var i=0; i<cbList.length; i++)
			cbList[i].selected = cbList[i].checked = bSelect;
			document.f1.submit();
		}

	}

	function reverseAll(cbList)
	{
		for	(var i=0; i<cbList.length; i++)
		{
			cbList[i].checked  = !(cbList[i].checked)
			cbList[i].selected = !(cbList[i].selected)
		}
	}
	
	
function plus_attachment() {
				if (document.getElementById('options').style.display == 'none') {
					document.getElementById('options').style.display = 'block';
					document.getElementById('plus').innerHTML='&nbsp;<img style=\"vertical-align:middle;\" src=\"../img/div_hide.gif\" alt=\"\" />&nbsp;".get_lang('AddAnAttachment')."';
				} else {
				document.getElementById('options').style.display = 'none';
				document.getElementById('plus').innerHTML='&nbsp;<img style=\"vertical-align:middle;\" src=\"../img/div_show.gif\" alt=\"\" />&nbsp;".get_lang('AddAnAttachment')."';
				}
}
	
	
	
	
	//	End	-->
	</script>";
}


/*======================================
			SENT_TO_FORM
======================================*/
/**
* constructs the form to display all the groups and users the message has been sent to
* input: 	$sent_to_array is a 2 dimensional array containing the groups and the users
*			the first level is a distinction between groups and users:
*			$sent_to_array['groups'] * and $sent_to_array['users']
*			$sent_to_array['groups'] (resp. $sent_to_array['users']) is also an array
*			containing all the id's of the groups (resp. users) who have received this message.
* @author Patrick Cool <patrick.cool@>
*/
function sent_to_form($sent_to_array)
{
	// we find all the names of the groups
	$group_names=get_course_groups();

	count($sent_to_array);

	// we count the number of users and the number of groups
	if (isset($sent_to_array['users']))
	{
		$number_users=count($sent_to_array['users']);
	}
	else
	{
		$number_users=0;
	}
	if (isset($sent_to_array['groups']))
	{
		$number_groups=count($sent_to_array['groups']);
	}
	else
	{
			$number_groups=0;
	}
	$total_numbers=$number_users+$number_groups;

	// starting the form if there is more than one user/group
	if ($total_numbers >1)
	{
		$output="<select name=\"sent to\">\n";
		$output.="<option>".get_lang("SentTo")."</option>";
		// outputting the name of the groups
		if (is_array($sent_to_array['groups']))
		{
			foreach ($sent_to_array['groups'] as $group_id)
			{
				$output.="\t<option value=\"\">G: ".$group_names[$group_id]['name']."</option>\n";
			}
		}

		if (isset($sent_to_array['users']))
		{
			if (is_array($sent_to_array['users']))
			{
				foreach ($sent_to_array['users'] as $user_id)
				{
					$user_info = api_get_user_info($user_id);
					$output.="\t<option value=\"\">".api_get_person_name($user_info['firstname'], $user_info['lastname'])."</option>\n";
				}
			}
		}

		// ending the form
		$output.="</select>\n";
	}
	else // there is only one user/group
	{
		if (isset($sent_to_array['users']) and is_array($sent_to_array['users']))
		{
			$user_info = api_get_user_info($sent_to_array['users'][0]);
			echo api_get_person_name($user_info['firstName'], $user_info['lastName']);
		}
		if (isset($sent_to_array['groups']) and is_array($sent_to_array['groups']) and $sent_to_array['groups'][0]!==0)
		{
			$group_id=$sent_to_array['groups'][0];
			echo $group_names[$group_id]['name'];
		}
		if (isset($sent_to_array['groups']) and is_array($sent_to_array['groups']) and $sent_to_array['groups'][0]==0)
		{
			echo get_lang("Everybody");
		}
	}
	if(!empty($output))
	{
		echo $output;
	}
}


/*======================================
			SEPARATE_USERS_GROUPS
	======================================*/
/**
* This function separates the users from the groups
* users have a value USER:XXX (with XXX the dokeos id
* groups have a value GROUP:YYY (with YYY the group id)
* @param    array   Array of strings that define the type and id of each destination
* @return   array   Array of groups and users (each an array of IDs)
*/
function separate_users_groups($to)
{
	foreach($to as $to_item) {
		list($type, $id) = explode(':', $to_item);
		switch($type) {
			case 'GROUP':
				$grouplist[] = intval($id);
				break;
			case 'USER':
				$userlist[] = intval($id);
				break;
		}
	}

	$send_to['groups']=$grouplist;
	$send_to['users']=$userlist;
	return $send_to;
}



/*======================================
	 			SENT_TO()
  ======================================*/
/**
* Returns all the users and all the groups a specific announcement item
* has been sent to
* @param    string  The tool (announcement, agenda, ...)
* @param    int     ID of the element of the corresponding type
* @return   array   Array of users and groups to whom the element has been sent 
*/
function sent_to($tool, $id)
{
	global $_course;
	global $tbl_item_property;

	$tool = Database::escape_string($tool);
	$id = Database::escape_string($id);

	$sent_to_group = array();
	$sent_to = array();

	$sql="SELECT * FROM $tbl_item_property WHERE tool='$tool' AND ref='".$id."'";
	$result = Database::query($sql,__FILE__,__LINE__);


	while ($row=Database::fetch_array($result)) {
		// if to_group_id is null then it is sent to a specific user
		// if to_group_id = 0 then it is sent to everybody
		if ($row['to_group_id'] != 0)
		{
			$sent_to_group[]=$row['to_group_id'];
		}
		// if to_user_id <> 0 then it is sent to a specific user
		if ($row['to_user_id'] <> 0)
		{
			$sent_to_user[]=$row['to_user_id'];
		}
	}
	if (isset($sent_to_group))
	{
		$sent_to['groups']=$sent_to_group;
	}
	if (isset($sent_to_user))
	{
		$sent_to['users']=$sent_to_user;
	}
	return $sent_to;
}


/*===================================================
	           CHANGE_VISIBILITY($tool,$id)
  =================================================*/
/**
* This functions swithes the visibility a course resource
* using the visibility field in 'item_property'
* values: 0 = invisibility for
* @param    string  The tool (announcement, agenda, ...)
* @param    int     ID of the element of the corresponding type
* @return   bool    False on failure, True on success
*/
function change_visibility_announcement($tool,$id)
{
	global $_course;
	global $tbl_item_property;

	$tool = Database::escape_string($tool);
	$id = Database::escape_string($id);

	$sql="SELECT * FROM $tbl_item_property WHERE tool='$tool' AND ref='$id'";

	$result=Database::query($sql,__FILE__,__LINE__) or die (mysql_error());
	$row=Database::fetch_array($result);

	if ($row['visibility']=='1')
	{
		$sql_visibility="UPDATE $tbl_item_property SET visibility='0' WHERE tool='$tool' AND ref='$id'";
	}
	else
	{
		$sql_visibility="UPDATE $tbl_item_property SET visibility='1' WHERE tool='$tool' AND ref='$id'";
	}
    $result=Database::query($sql_visibility,__FILE__,__LINE__);
    if ($result === false) { 
        return false;
    }
    return true;
}

/**
 * Store an announcement in the database (including its attached file if any)
 * @param string    Announcement title (pure text)
 * @param string    Content of the announcement (can be HTML)
 * @param int       Display order in the list of announcements
 * @param array     Array of users and groups to send the announcement to 
 * @param string    Comment describing the attachment
 * @return int      false on failure, ID of the announcement on success 
 */
function store_advalvas_item($emailTitle, $newContent, $order, $to, $file_comment='') {

	global $_course;
	global $nameTools;
	global $_user;

	$tbl_announcement = Database::get_course_table(TABLE_ANNOUNCEMENT);
	$tbl_item_property = Database::get_course_table(TABLE_ITEM_PROPERTY);

	// filter data
	$emailTitle = Database::escape_string($emailTitle);
	$newContent = Database::escape_string($newContent);
	$order = intval($order);
	
	// store in the table announcement
	$sql = "INSERT INTO $tbl_announcement SET content = '$newContent', title = '$emailTitle', end_date = NOW(), display_order ='$order', session_id=".intval($_SESSION['id_session']);
	$result = Database::query($sql,__FILE__,__LINE__);
	if ($result === false) {
		return false;
	}
	
	//store the attach file
	$last_id = Database::insert_id();
	$save_attachment = add_announcement_attachment_file($last_id, $file_comment, $_FILES['user_upload']);
	
	// store in item_property (first the groups, then the users
	if (!is_null($to)) // !is_null($to): when no user is selected we send it to everyone
	{
		$send_to=separate_users_groups($to);
		// storing the selected groups
		if (is_array($send_to['groups']))
		{
			foreach ($send_to['groups'] as $group)
			{
				api_item_property_update($_course, TOOL_ANNOUNCEMENT, $last_id, "AnnouncementAdded", $_user['user_id'], $group);
			}
		}

		// storing the selected users
		if (is_array($send_to['users']))
		{
			foreach ($send_to['users'] as $user)
			{
				api_item_property_update($_course, TOOL_ANNOUNCEMENT, $last_id, "AnnouncementAdded", $_user['user_id'], '', $user);
			}
		}
	}
	else // the message is sent to everyone, so we set the group to 0
	{
		api_item_property_update($_course, TOOL_ANNOUNCEMENT, $last_id, "AnnouncementAdded", $_user['user_id'], '0');
	}
	return $last_id;
}


function store_advalvas_group_item($emailTitle,$newContent, $order, $to, $to_users, $file_comment='')
{
	global $_course;
	global $nameTools;
	global $_user;
	
	// database definitions
	$tbl_announcement = Database::get_course_table(TABLE_ANNOUNCEMENT);
	$tbl_item_property = Database::get_course_table(TABLE_ITEM_PROPERTY);
	
	$newContent=stripslashes($newContent);
	$emailTitle = Database::escape_string($emailTitle);
	$newContent = Database::escape_string($newContent,COURSEMANAGERLOWSECURITY);
	$order = intval($order);
	
	// store in the table announcement
	$sql = "INSERT INTO $tbl_announcement SET content = '$newContent', title = '$emailTitle', end_date = NOW(), display_order ='$order', session_id=".intval($_SESSION['id_session']);
	$result = Database::query($sql,__FILE__,__LINE__) or die (mysql_error());
	if ($result === false) {
		return false;
	}
	
	//store the attach file
	$last_id = Database::insert_id();
	$save_attachment = add_announcement_attachment_file($last_id, $file_comment, $_FILES['user_upload']);
	
	// store in item_property (first the groups, then the users
	if (!isset($to_users)) // !isset($to): when no user is selected we send it to everyone
	{
		$send_to=separate_users_groups($to);
		// storing the selected groups
		if (is_array($send_to['groups']))
		{
			foreach ($send_to['groups'] as $group)
			{
				api_item_property_update($_course, TOOL_ANNOUNCEMENT, $last_id, "AnnouncementAdded", $_user['user_id'], $group);
			}
		}
	}
	else // the message is sent to everyone, so we set the group to 0
	{
		// storing the selected users
		if (is_array($to_users))
		{
			foreach ($to_users as $user)
			{
				api_item_property_update($_course, TOOL_ANNOUNCEMENT, $last_id, "AnnouncementAdded", $_user['user_id'], '', $user);
			}
		}
	}

	return $last_id;

}


/*==================================================
	           EDIT_VALVAS_ITEM
==================================================*/
/**
* This function stores the announcement Item in the table announcement
* and updates the item_property also
*/
function edit_advalvas_item($id,$emailTitle,$newContent,$to, $file_comment='')
{

	global $_course;
	global $nameTools;
	global $_user;

	global $tbl_announcement;
	global $tbl_item_property;

	$newContent=stripslashes($newContent);
	$emailTitle = Database::escape_string($emailTitle);
	$newContent = Database::escape_string($newContent,COURSEMANAGERLOWSECURITY);
	$order = intval($order);
	
	// store the modifications in the table announcement
 	$sql = "UPDATE $tbl_announcement SET content='$newContent', title = '$emailTitle' WHERE id='$id'";
	$result = Database::query($sql,__FILE__,__LINE__) or die (mysql_error());
	
	if(empty($last_id)){
		$last_id = $id;
	$save_attachment = add_announcement_attachment_file($last_id, $file_comment, $_FILES['user_upload']);
	
	}
	
	$last_id = $id;
	$edit_attachment = edit_announcement_attachment_file($last_id, $_FILES['user_upload'], $file_comment);

	// we remove everything from item_property for this
	$sql_delete="DELETE FROM $tbl_item_property WHERE ref='$id' AND tool='announcement'";
	$result = Database::query($sql_delete,__FILE__,__LINE__) or die (mysql_error());
	
	// store in item_property (first the groups, then the users
	if (!is_null($to)) // !is_null($to): when no user is selected we send it to everyone
	{
		$send_to=separate_users_groups($to);
		// storing the selected groups
		if (is_array($send_to['groups']))
		{
			foreach ($send_to['groups'] as $group)
			{
				api_item_property_update($_course, TOOL_ANNOUNCEMENT, $id, "AnnouncementUpdated", $_user['user_id'], $group);
			}
		}
		// storing the selected users
		if (is_array($send_to['users']))
		{
			foreach ($send_to['users'] as $user)
			{
					api_item_property_update($_course, TOOL_ANNOUNCEMENT, $id, "AnnouncementUpdated", $_user['user_id'], '', $user);
			}
		}
	}
	else // the message is sent to everyone, so we set the group to 0
	{
		api_item_property_update($_course, TOOL_ANNOUNCEMENT, $id, "AnnouncementUpdated", $_user['user_id'], '0');
	}
}


/*
==============================================================================
		MAIL FUNCTIONS
==============================================================================
*/

/**
* Sends an announcement by email to a list of users.
* Emails are sent one by one to try to avoid antispam.
*/
function send_announcement_email($user_list, $course_code, $_course, $mail_title, $mail_content)
{
	global $charset;
	global $_user;

	foreach ($user_list as $this_user) {
		/*  Header : Bericht van uw lesgever - GES ($course_code) - Morgen geen les! ($mail_title)
			Body :  John Doe (prenom + nom) <john_doe@hotmail.com> (email)
					Morgen geen les! ($mail_title)
					Morgen is er geen les, de les wordt geschrapt wegens vergadering (newContent)
		*/
		$mail_subject = get_lang('professorMessage').' - '.$_course['official_code'].' - '.$mail_title;

		$mail_body = '['.$_course['official_code'].'] - ['.$_course['name']."]\n";
		$mail_body .= api_get_person_name($this_user['firstname'], $this_user['lastname'], null, PERSON_NAME_EMAIL_ADDRESS).' <'.$this_user["email"]."> \n\n".stripslashes($mail_title)."\n\n".trim(stripslashes(api_html_entity_decode(strip_tags(str_replace(array('<p>','</p>','<br />'),array('',"\n","\n"),$mail_content)), ENT_QUOTES, $charset)))." \n\n-- \n";
		$mail_body .= api_get_person_name($_user['firstName'], $_user['lastName'], null, PERSON_NAME_EMAIL_ADDRESS).' ';
		$mail_body .= '<'.$_user['mail'].">\n";
		$mail_body .= $_course['official_code'].' '.$_course['name'];

		//set the charset and use it for the encoding of the email - small fix, not really clean (should check the content encoding origin first)
		//here we use the encoding used for the webpage where the text is encoded (ISO-8859-1 in this case)
		if(empty($charset)){$charset='ISO-8859-1';}
		$encoding = 'Content-Type: text/plain; charset='. $charset;

		$newmail = api_mail(api_get_person_name($this_user['firstname'], $this_user['lastname'], null, PERSON_NAME_EMAIL_ADDRESS), $this_user['email'], $mail_subject, $mail_body, api_get_person_name($_SESSION['_user']['firstName'], $_SESSION['_user']['lastName'], null, PERSON_NAME_EMAIL_ADDRESS), $_SESSION['_user']['mail'], $encoding);
	}
}

function update_mail_sent($insert_id)
{
	global $_course;
	global $tbl_announcement;
	if ($insert_id != strval(intval($insert_id))) { return false; }
	$insert_id = Database::escape_string($insert_id);
	// store the modifications in the table tbl_annoucement
	$sql = "UPDATE $tbl_announcement SET email_sent='1' WHERE id='$insert_id'";
	Database::query($sql,__FILE__,__LINE__);
}

/**
 * Gets all announcements from a user by course
 * @param	string course db
 * @param	int user id
 * @return	string an html with the content
 */
function get_all_annoucement_by_user_course($course_db, $user_id)
{
	$tbl_announcement		= Database::get_course_table(TABLE_ANNOUNCEMENT, $course_db);
	$tbl_item_property  	= Database::get_course_table(TABLE_ITEM_PROPERTY, $course_db);
	if (!empty($user_id) && is_numeric($user_id)) {
		$user_id = Database::escape_string($user_id);
		$sql="SELECT announcement.*, toolitemproperties.*
						FROM $tbl_announcement announcement, $tbl_item_property toolitemproperties
						WHERE announcement.id = toolitemproperties.ref
						AND toolitemproperties.tool='announcement'
						AND (toolitemproperties.insert_user_id='".$user_id."' AND toolitemproperties.to_group_id='0')
						AND toolitemproperties.visibility='1'
						AND announcement.session_id  = 0
						ORDER BY display_order DESC";
		$result = Database::query($sql,__FILE__,__LINE__);
		$num_rows = Database::num_rows($result);
		$content = '';
		$i=0;
		if (Database::num_rows($result)>0) {
			while ($myrow = Database::fetch_array($result)) {
				if ($i<=4) {
					$content.= '<strong>'.$myrow['title'].'</strong><br /><br />';
					$content.= $myrow['content'];
				} else {
					break;
				}
				$i++;
			}
			return $content;
		} else {
			return $content;
		}
	} else {
		return '';
	}
}

/*
==============================================================================
		ATTACHMENT FUNCTIONS
==============================================================================
*/

/**
 * Show a list with all the attachments according to the post's id
 * @param the post's id
 * @return array with the post info
 * @author Arthur Portugal
 * @version November 2009, dokeos 1.8.6.2
 */
 
function get_attachment($announcement_id) {
	
	$tbl_announcement_attachment = Database::get_course_table(TABLE_ANNOUNCEMENT_ATTACHMENT);
	$announcement_id=Database::escape_string($announcement_id);
	$row=array();
	$sql = 'SELECT id,path, filename,comment FROM '. $tbl_announcement_attachment.' WHERE announcement_id = '.(int)$announcement_id.'';
	$result=Database::query($sql, __FILE__, __LINE__);
	if (Database::num_rows($result)!=0) {
		$row=Database::fetch_array($result,ASSOC);
	}
	return $row;
}

/**
 * This function add a attachment file into announcement
 * @param string  a comment about file
 * @param int last id from announcement table
 * @return int  -1 if failed, 0 if unknown (should not happen), 1 if success
 */

function add_announcement_attachment_file($last_id, $file_comment, $file = array()) {
	global $_course;
	$tbl_announcement_attachment = Database::get_course_table(TABLE_ANNOUNCEMENT_ATTACHMENT);
	$return = 0;
	$last_id = intval($last_id);
	
	if (is_array($file) && $file['error'] == 0 ) {
		$courseDir   = $_course['path'].'/upload/announcements';
		$sys_course_path = api_get_path(SYS_COURSE_PATH);
		$updir = $sys_course_path.$courseDir;
		
		// Try to add an extension to the file if it hasn't one
		$new_file_name = add_ext_on_mime(stripslashes($_FILES['user_upload']['name']), $_FILES['user_upload']['type']);
		// user's file name
		$file_name = $_FILES['user_upload']['name'];

		if (!filter_extension($new_file_name))  {
			$return = -1;
			Display :: display_error_message(get_lang('UplUnableToSaveFileFilteredExtension'));
		} else {
			$new_file_name = uniqid('');
			$new_path           = $updir.'/'.$new_file_name;
			$result             = @move_uploaded_file($_FILES['user_upload']['tmp_name'], $new_path);
			$safe_file_comment  = Database::escape_string($file_comment);
			$safe_file_name     = Database::escape_string($file_name);
			$safe_new_file_name = Database::escape_string($new_file_name);
			// Storing the attachments if any
			//if ($result) {
				$sql = 'INSERT INTO '.$tbl_announcement_attachment.'(filename, comment, path, announcement_id, size) '.
					   "VALUES ( '$safe_file_name', '$file_comment', '$safe_new_file_name' , '$last_id', '".intval($_FILES['user_upload']['size'])."' )";
				$result = Database::query($sql, __LINE__, __FILE__);
				//$message .= ' / '.get_lang('FileUploadSucces').'<br />';
                $return = 1;
				//$last_id_file=Database::insert_id();
				//api_item_property_update($_course, 'announcement_attachment', $last_id_file ,'AnnouncementAttachmentAdded', api_get_user_id());

			//}
		}
	}
	return $return;
}

/**
 * This function edit a attachment file into announcement
 * @param string  a comment about file
 * @param int Agenda Id
 * @param int attachment file Id
 */
function edit_announcement_attachment_file($last_id, $file = array(), $file_comment) {
	global $_course;
	$tbl_announcement_attachment = Database::get_course_table(TABLE_ANNOUNCEMENT_ATTACHMENT);
    $return = 0;
	// Storing the attachments

    if(!empty($_FILES['user_upload'])) {
		$upload_ok = process_uploaded_file($_FILES['user_upload']);
	}

	if (!empty($upload_ok)) {
		$courseDir   = $_course['path'].'/upload/announcements';
		$sys_course_path = api_get_path(SYS_COURSE_PATH);
		$updir = $sys_course_path.$courseDir;

		// Try to add an extension to the file if it hasn't one
		$new_file_name = add_ext_on_mime(stripslashes($_FILES['user_upload']['name']), $_FILES['user_upload']['type']);
		// user's file name
		$file_name =$_FILES['user_upload'] ['name'];
		if (!filter_extension($new_file_name)) {
			$return -1;
			Display :: display_error_message(get_lang('UplUnableToSaveFileFilteredExtension'));
		} else {
			$new_file_name = uniqid('');
			$new_path = $updir.'/'.$new_file_name;
			$result = @move_uploaded_file($_FILES['user_upload']['tmp_name'], $new_path);
			$safe_file_comment = Database::escape_string($file_comment);
			$safe_file_name = Database::escape_string($file_name);
			$safe_new_file_name = Database::escape_string($new_file_name);	
			$sql = "UPDATE $tbl_announcement_attachment SET filename = '$safe_file_name', comment = '$safe_file_comment', path = '$safe_new_file_name', announcement_id = '$last_id', size ='".intval($_FILES['user_upload']['size'])."'
				 WHERE announcement_id = '$last_id'";
			$result = Database::query($sql, __FILE__,__LINE__);
			if ($result === false) {
				$return = -1;
                Display :: display_error_message(get_lang('UplUnableToSaveFile'));
			} else {
                $return = 1;
			}
			//$message .= ' / '.get_lang('FileUploadSucces').'<br />';
		}
	}
	return $return;
}

/**
 * This function delete a attachment file by id
 * @param integer attachment file Id
 *
 */
function delete_announcement_attachment_file($id) {

	global $_course;
	$tbl_announcement_attachment = Database::get_course_table(TABLE_ANNOUNCEMENT_ATTACHMENT);
	$id=Database::escape_string($id);	
	$sql="DELETE FROM $tbl_announcement_attachment WHERE id = $id";
	error_log($sql);
	$result=Database::query($sql, __FILE__,__LINE__);
	// update item_property
	//api_item_property_update($_course, 'announcement_attachment',  $id,'AnnouncementAttachmentDeleted', api_get_user_id());
}
