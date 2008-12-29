<?php // $id: $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2008 Dokeos S.A.

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
*	@package dokeos.admin
*	@author Carlos Vargas
*	This file is the calendar/agenda.inc.php 
==============================================================================
*/

/*
-----------------------------------------------------------
	Constants and variables
-----------------------------------------------------------
*/
// the variables for the days and the months
// Defining the shorts for the days
$DaysShort = array (get_lang("SundayShort"), get_lang("MondayShort"), get_lang("TuesdayShort"), get_lang("WednesdayShort"), get_lang("ThursdayShort"), get_lang("FridayShort"), get_lang("SaturdayShort"));
// Defining the days of the week to allow translation of the days
$DaysLong = array (get_lang("SundayLong"), get_lang("MondayLong"), get_lang("TuesdayLong"), get_lang("WednesdayLong"), get_lang("ThursdayLong"), get_lang("FridayLong"), get_lang("SaturdayLong"));
// Defining the months of the year to allow translation of the months
$MonthsLong = array (get_lang("JanuaryLong"), get_lang("FebruaryLong"), get_lang("MarchLong"), get_lang("AprilLong"), get_lang("MayLong"), get_lang("JuneLong"), get_lang("JulyLong"), get_lang("AugustLong"), get_lang("SeptemberLong"), get_lang("OctoberLong"), get_lang("NovemberLong"), get_lang("DecemberLong"));

/*
==============================================================================
		FUNCTIONS
==============================================================================
*/

/**
* Retrieves all the agenda items from the table
* @author: Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @author Yannick Warnier <yannick.warnier@dokeos.com> - cleanup
* @param integer $month: the integer value of the month we are viewing
* @param integer $year: the 4-digit year indication e.g. 2005
* @return array
*/

/**
* show the mini calender of the given month
* @author: Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @param array an array containing all the agenda items for the given month
* @param integer $month: the integer value of the month we are viewing
* @param integer $year: the 4-digit year indication e.g. 2005
* @param string $monthName: the language variable for the mont name
* @return html code
* @todo refactor this so that $monthName is no longer needed as a parameter
*/
function display_minimonthcalendar($agendaitems, $month, $year, $monthName)
{
	global $DaysShort;
	//Handle leap year
	$numberofdays = array (0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	if (($year % 400 == 0) or ($year % 4 == 0 and $year % 100 <> 0))
		$numberofdays[2] = 29;
	//Get the first day of the month
	$dayone = getdate(mktime(0, 0, 0, $month, 1, $year));
	//Start the week on monday
	$startdayofweek = $dayone['wday'] <> 0 ? ($dayone['wday'] - 1) : 6;
	$backwardsURL = api_get_self()."?".api_get_cidreq()."&amp;coursePath=".(empty($_GET['coursePath'])?'':$_GET['coursePath'])."&amp;courseCode=".(empty($_GET['courseCode'])?'':$_GET['courseCode'])."&amp;month=". ($month == 1 ? 12 : $month -1)."&amp;year=". ($month == 1 ? $year -1 : $year);
	$forewardsURL = api_get_self()."?".api_get_cidreq()."&amp;coursePath=".(empty($_GET['coursePath'])?'':$_GET['coursePath'])."&amp;courseCode=".(empty($_GET['courseCode'])?'':$_GET['courseCode'])."&amp;month=". ($month == 12 ? 1 : $month +1)."&amp;year=". ($month == 12 ? $year +1 : $year);

	echo 	"<table class=\"data_table\">\n",
			"<tr>\n",
			"<th width=\"10%\"><a href=\"", $backwardsURL, "\"> &laquo; </a></th>\n",
			"<th width=\"80%\" colspan=\"5\">", $monthName, " ", $year, "</th>\n",
			"<th  width=\"10%\"><a href=\"", $forewardsURL, "\"> &raquo; </a></th>\n", "</tr>\n";
	echo "<tr>\n";
	for ($ii = 1; $ii < 8; $ii ++)
	{
		echo "<td class=\"weekdays\">", $DaysShort[$ii % 7], "</td>\n";
	}
	echo "</tr>\n";
	$curday = -1;
	$today = getdate();
	while ($curday <= $numberofdays[$month])
	{
		echo "<tr>\n";
		for ($ii = 0; $ii < 7; $ii ++)
		{
			if (($curday == -1) && ($ii == $startdayofweek))
			{
				$curday = 1;
			}
			if (($curday > 0) && ($curday <= $numberofdays[$month]))
			{
				$bgcolor = $ii < 5 ? $class="class=\"days_week\"" : $class="class=\"days_weekend\"";
				$dayheader = "$curday";
				if (($curday == $today['mday']) && ($year == $today['year']) && ($month == $today['mon']))
				{
					$dayheader = "$curday";
					$class = "class=\"days_today\"";
				}
				echo "\t<td ".$class.">";
				if (!empty($agendaitems[$curday]))
				{
					echo "<a href=\"".api_get_self()."?".api_get_cidreq()."&amp;action=view&amp;view=day&amp;day=".$curday."&amp;month=".$month."&amp;year=".$year."\">".$dayheader."</a>";
				}
				else
				{
					echo $dayheader;
				}
				// "a".$dayheader." <span class=\"agendaitem\">".$agendaitems[$curday]."</span>\n";
				echo "</td>\n";
				$curday ++;
			}
			else
			{
				echo "<td>&nbsp;</td>\n";
			}
		}
		echo "</tr>\n";
	}
	echo "</table>\n";
}
/**
* show the calender of the given month
* @author: Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @param integer $month: the integer value of the month we are viewing
* @param integer $year: the 4-digit year indication e.g. 2005
* @return html code
*/
/**
* returns all the javascript that is required for easily selecting the target people/groups this goes into the $htmlHeadXtra[] array
* @author: Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @return javascript code
*/
function to_javascript()
{
$Send2All=get_lang("Send2All");


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
	//if (cbList.length <	1) {
	//	alert(\"$Send2All\");
	//	return;
	//}
	for	(var i=0; i<cbList.length; i++)
		cbList[i].selected = cbList[i].checked = bSelect
}

function reverseAll(cbList)
{
	for	(var i=0; i<cbList.length; i++)
	{
		cbList[i].checked  = !(cbList[i].checked)
		cbList[i].selected = !(cbList[i].selected)
	}
}
//	End	-->
</script>";
}


/**
* returns the javascript for setting a filter. This is a jump menu
* @author: Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @return javascript code
*/
function user_group_filter_javascript()
{
return "<script language=\"JavaScript\" type=\"text/JavaScript\">
<!--
function MM_jumpMenu(targ,selObj,restore){
  eval(targ+\".location='\"+selObj.options[selObj.selectedIndex].value+\"'\");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>
";
}
/**
* this function gets all the users of the current course
* @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @return array: associative array where the key is the id of the user and the value is an array containing
			the first name, the last name, the user id
*/
function get__users()
{/*
global $tbl_user;
global $tbl_courseUser, $tbl_session_course_user;
global $_cid;

// not 100% if this is necessary, this however prevents a notice
if (!isset($courseadmin_filter))
	{$courseadmin_filter='';}

$sql = "SELECT u.user_id uid, u.lastname lastName, u.firstname firstName
		FROM $tbl_user as u, $tbl_courseUser as cu
		WHERE cu.course_code = '".$_cid."'
			AND cu.user_id = u.user_id $courseadmin_filter
		ORDER BY u.lastname, u.firstname";
$result = api_sql_query($sql,__FILE__,__LINE__);
while($user=Database::fetch_array($result)){
	$users[$user[0]] = $user;
}

if(!empty($_SESSION['id_session'])){
	$sql = "SELECT u.user_id uid, u.lastname lastName, u.firstName firstName
			FROM $tbl_session_course_user AS session_course_user
			INNER JOIN $tbl_user u
				ON u.user_id = session_course_user.id_user
			WHERE id_session='".$_SESSION['id_session']."'
			AND course_code='$_cid'";

	$result = api_sql_query($sql,__FILE__,__LINE__);
	while($user=Database::fetch_array($result)){
		$users[$user[0]] = $user;
	}

}

return $users;
*/
}

/**
* this function gets all the groups of the course
* @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @return array
*/
function get_course_groups()
{/*
	global $tbl_group;
	global $tbl_groupUser;
	$group_list = array();
	
	$sql = "SELECT g.id, g.name, COUNT(gu.id) userNb
			        FROM ".$tbl_group." AS g LEFT JOIN ".$tbl_groupUser." gu
			        ON g.id = gu.group_id
			        GROUP BY g.id";
	
	$result = api_sql_query($sql,__FILE__,__LINE__);
	while ($group_data = Database::fetch_array($result))
	{
		$group_list [$group_data['id']] = $group_data;
	}
	return $group_list;*/
}
/**
* this function shows the form for sending a message to a specific group or user.
* @author: Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @return html code
*/
function show_to_form($to_already_selected)
{
/*$user_list=get_course_users();
$group_list=get_course_groups();

echo "\n<table id=\"recipient_list\" style=\"display: none;\">\n";
	echo "\t<tr>\n";
	// the form containing all the groups and all the users of the course
	echo "\t\t<td>\n";
		construct_not_selected_select_form($group_list,$user_list,$to_already_selected);
	echo "\t\t</td>\n";
	// the buttons for adding or removing groups/users
	echo "\n\t\t<td valign=\"middle\">\n";
	echo "\t\t<input type=\"button\" ",
				"onclick=\"move(this.form.elements[2],this.form.elements[5])\" ",
				"value=\"   &gt;&gt;   \" />",

				"\n\t\t<p>&nbsp;</p>",

				"\n\t\t<input type=\"button\" ",
				"onclick=\"move(this.form.elements[5],this.form.elements[2])\" ",
				"value=\"   &lt;&lt;   \" />";
	echo "\t\t</td>\n";
	echo "\n\t\t<td>\n";
		construct_selected_select_form($group_list,$user_list,$to_already_selected);
	echo "\t\t</td>\n";
	echo "\t</tr>\n";
echo "</table>";*/
}

function display_monthcalendar($month, $year)
{
	global $MonthsLong;
	global $DaysShort;
	global $origin;

	// grabbing all the calendar items for this year and storing it in a array
	$data=get_calendar_items($month,$year);


	//Handle leap year
	$numberofdays = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
	if (($year%400 == 0) or ($year%4==0 and $year%100<>0)) $numberofdays[2] = 29;

	//Get the first day of the month
	$dayone = getdate(mktime(0,0,0,$month,1,$year));
  	//Start the week on monday
	$startdayofweek = $dayone['wday']<>0 ? ($dayone['wday']-1) : 6;

	$backwardsURL = api_get_self()."?".api_get_cidreq()."&amp;origin=$origin&amp;month=".($month==1 ? 12 : $month-1)."&amp;year=".($month==1 ? $year-1 : $year);
	$forewardsURL = api_get_self()."?".api_get_cidreq()."&amp;origin=$origin&amp;month=".($month==12 ? 1 : $month+1)."&amp;year=".($month==12 ? $year+1 : $year);

	   $maand_array_maandnummer=$month-1;

	echo "<table class=\"data_table\">\n",
		"<tr>\n",
		"<th width=\"10%\"><a href=\"",$backwardsURL,"\"> &laquo; </a></th>\n",
		"<th width=\"80%\" colspan=\"5\">",$MonthsLong[$maand_array_maandnummer]," ",$year,"</th>\n",
		"<th width=\"10%\"><a href=\"",$forewardsURL,"\"> &raquo; </a></th>\n",
		"</tr>\n";

	echo "<tr>\n";

	for ($ii=1;$ii<8; $ii++)
	{
	echo "<td class=\"weekdays\" width=\"14%\">",$DaysShort[$ii%7],"</td>\n";
  }

	echo "</tr>\n";
	$curday = -1;
	$today = getdate();
	while ($curday <=$numberofdays[$month])
  	{
	echo "<tr>\n";
    	for ($ii=0; $ii<7; $ii++)
	  	{
	  		if (($curday == -1)&&($ii==$startdayofweek))
			{
	    		$curday = 1;
			}
			if (($curday>0)&&($curday<=$numberofdays[$month]))
			{
				$bgcolor = $ii<5 ? "class=\"row_odd\"" : "class=\"row_even\"";

				$dayheader = "$curday";
				if (key_exists($curday,$data))
				{
					$dayheader="<a href='".api_get_self()."?".api_get_cidreq()."&amp;view=list&amp;origin=$origin&amp;month=$month&amp;year=$year&amp;day=$curday#$curday'>".$curday."</a>";
					foreach ($data[$curday] as $key=>$agenda_item)
					{
						foreach ($agenda_item as $key=>$value)
						{
							$dayheader .= '<br /><b>'.substr($value['start_date'],11,8).'</b>';
							$dayheader .= ' - ';
							$dayheader .= $value['title'];
						}
					}
				}

				if (($curday==$today['mday'])&&($year ==$today['year'])&&($month == $today['mon']))
				{
			echo "<td id=\"today\" ",$bgcolor,"\">".$dayheader." \n";
      }
				else
				{
			echo "<td id=\"days\" ",$bgcolor,"\">".$dayheader." \n";
				}
			echo "</td>\n";

	      		$curday++;
	    }
	  		else
	    {
	echo "<td>&nbsp;</td>";

	    }
		}
	echo "</tr>";
    }
echo "</table>";
}
/**
* this function shows the form with the user that were not selected
* @author: Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @return html code
*/
function construct_not_selected_select_form($group_list=null, $user_list=null,$to_already_selected=array())
{
	/*echo "\t\t<select name=\"not_selected_form[]\" size=\"5\" multiple=\"multiple\" style=\"width:200px\">\n";

	// adding the groups to the select form
	if (is_array($group_list))
	{
		foreach($group_list as $this_group)
		{
			//api_display_normal_message("group " . $thisGroup[id] . $thisGroup[name]);
			if (!is_array($to_already_selected) || !in_array("GROUP:".$this_group['id'],$to_already_selected)) // $to_already_selected is the array containing the groups (and users) that are already selected
				{
				echo	"\t\t<option value=\"GROUP:".$this_group['id']."\">",
					"G: ",$this_group['name']," &ndash; " . $this_group['userNb'] . " " . get_lang('Users') .
					"</option>\n";
			}
		}
		// a divider
		echo	"\t\t<option value=\"\">----------------------------------</option>\n";
	}

	// adding the individual users to the select form
	foreach($user_list as $this_user)
	{
		if (!is_array($to_already_selected) || !in_array("USER:".$this_user['uid'],$to_already_selected)) // $to_already_selected is the array containing the users (and groups) that are already selected
		{
			echo	"\t\t<option value=\"USER:",$this_user['uid'],"\">",
				"",$this_user['lastName']," ",$this_user['firstName'],
				"</option>\n";
		}
	}
	echo "\t\t</select>\n";*/
}
/**
* This function shows the form with the user that were selected
* @author: Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @return html code
*/
function construct_selected_select_form($group_list=null, $user_list=null,$to_already_selected)
{
/*	// we separate the $to_already_selected array (containing groups AND users into
	// two separate arrays
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
	echo "\t\t<select name=\"selectedform[]\" size=\"5\" multiple=\"multiple\" style=\"width:200px\">";
	if(is_array($to_already_selected))
	{
		foreach($to_already_selected as $groupuser)
		{
			list($type,$id)=explode(":",$groupuser);
			if ($type=="GROUP")
			{
				echo "\t\t<option value=\"".$groupuser."\">G: ".$ref_array_groups[$id]['name']."</option>";
			}
			else
			{
				echo "\t\t<option value=\"".$groupuser."\">".$ref_array_users[$id]['lastName']." ".$ref_array_users[$id]['firstName']."</option>";
			}
		}
	}
	echo "</select>\n";*/
}
/**
* This function stores the Agenda Item in the table calendar_event and updates the item_property table also
* @author: Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @return integer the id of the last added agenda item
*/
function store_new_agenda_item()
{
	global $_user /*, $_course*/;
	$TABLEAGENDA = Database::get_main_table(TABLE_MAIN_SYSTEM_CALENDAR);
    //$t_agenda_repeat = Database::get_course_Table(TABLE_AGENDA_REPEAT);
	
	// some filtering of the input data
	$title=strip_tags(trim($_POST['title'])); // no html allowed in the title
	$content=trim($_POST['content']);
	$start_date=(int)$_POST['fyear']."-".(int)$_POST['fmonth']."-".(int)$_POST['fday']." ".(int)$_POST['fhour'].":".(int)$_POST['fminute'].":00";
    $end_date=(int)$_POST['end_fyear']."-".(int)$_POST['end_fmonth']."-".(int)$_POST['end_fday']." ".(int)$_POST['end_fhour'].":".(int)$_POST['end_fminute'].":00";
	 
	// store in the table calendar_event
	 $sql = "INSERT INTO ".$TABLEAGENDA."
					        (title,content, start_date, end_date)
					        VALUES
					        ('".$title."','".$content."', '".$start_date."','".$end_date."')";
	
	$result = api_sql_query($sql,__FILE__,__LINE__) or die (Database::error());
	$last_id=Database::insert_id();
	
	// store in last_tooledit (first the groups, then the users
	$to=$_POST['selectedform'];
	/*	if ((!is_null($to))or (!empty($_SESSION['toolgroup']))) // !is_null($to): when no user is selected we send it to everyone
	{
		$send_to=separate_users_groups($to);
		// storing the selected groups
		if (is_array($send_to['groups']))
		{
			foreach ($send_to['groups'] as $group)
			{
				api_item_property_update($_course, TOOL_CALENDAR_EVENT, $last_id,"AgendaAdded", $_user['user_id'], $group,'',$start_date, $end_date);
			}
		}
		// storing the selected users
		if (is_array($send_to['users']))
		{
			foreach ($send_to['users'] as $user)
			{
				api_item_property_update($_course, TOOL_CALENDAR_EVENT, $last_id,"AgendaAdded", $_user['user_id'],'',$user, $start_date,$end_date);
			}
		}
	}
	else // the message is sent to everyone, so we set the group to 0
	{
		api_item_property_update($_course, TOOL_CALENDAR_EVENT, $last_id,"AgendaAdded", $_user['user_id'], '','',$start_date,$end_date);
	}*/
	
// storing the resources
	//store_resources($_SESSION['source_type'],$last_id);
    
    //if repetitive, insert element into agenda_repeat table
/*    if(!empty($_POST['repeat']) && !empty($_POST['repeat_type']))
    {
    	if(!empty($_POST['repeat_end_year']) && !empty($_POST['repeat_end_month']) && !empty($_POST['repeat_end_day']))
        {
        	$end_y = intval($_POST['repeat_end_year']);
            $end_m = intval($_POST['repeat_end_month']);
            $end_d = intval($_POST['repeat_end_day']);
            $end = mktime((int)$_POST['fhour'],(int)$_POST['fminute'],0,$end_m,$end_d,$end_y);
            $now = time();
            $type = Database::escape_string($_POST['repeat_type']);
        
        	if($end > $now 
                && in_array($type,array('daily','weekly','monthlyByDate','monthlyByDay','monthlyByDayR','yearly')))
            {
        	   $sql = "INSERT INTO $t_agenda_repeat (cal_id, cal_type, cal_end)" .
                    " VALUES ($last_id,'$type',$end)";
               $res = Database::query($sql,__FILE__,__LINE__);
            }
        }
    }
	return $last_id;*/
}
/**
 * Stores the given agenda item as an announcement (unlinked copy)
 * @param	integer		Agenda item's ID
 * @return	integer		New announcement item's ID
 */
function store_agenda_item_as_announcement($item_id){/*
	$table_agenda = Database::get_main_table(TABLE_MAIN_SYSTEM_CALENDAR);
	$table_ann = Database::get_course_table(TABLE_ANNOUNCEMENT);
	//check params
	if(empty($item_id) or $item_id != strval(intval($item_id))){return -1;}
	//get the agenda item
	$sql = "SELECT * FROM $table_agenda WHERE id = '".$item_id."'";
	$res = api_sql_query($sql,__FILE__,__LINE__);
	if(Database::num_rows($res)>0){
		$row = Database::fetch_array($res);
		//we have the agenda event, copy it
		//get the maximum value for display order in announcement table
		$sql_max = "SELECT MAX(display_order) FROM $table_ann";
		$res_max = api_sql_query($sql_max,__FILE__,__LINE__);
		$row_max = Database::fetch_array($res_max);
		$max = $row_max[0]+1;
		//build the announcement text
		$content = $row['start_date']." - ".$row['end_date']."\n\n".$row['content'];
		//insert announcement

		$sql_ins = "INSERT INTO $table_ann (title,content,end_date,display_order) " .
				"VALUES ('".$row['title']."','$content','".$row['end_date']."','$max')";
		$res_ins = api_sql_query($sql_ins,__FILE__,__LINE__);
		if($res > 0)
		{
			$ann_id = Database::get_last_insert_id();
			//Now also get the list of item_properties rows for this agenda_item (calendar_event)
			//and copy them into announcement item_properties
			$table_props = Database::get_course_table(TABLE_ITEM_PROPERTY);
			$sql_props = "SELECT * FROM $table_props WHERE tool = 'calendar_event' AND ref='$item_id'";
			$res_props = api_sql_query($sql_props,__FILE__,__LINE__);
			if(Database::num_rows($res_props)>0)
			{
				while($row_props = Database::fetch_array($res_props))
				{
					//insert into announcement item_property
					$time = date("Y-m-d H:i:s", time());
					$sql_ins_props = "INSERT INTO $table_props " .
							"(tool, insert_user_id, insert_date, " .
							"lastedit_date, ref, lastedit_type," .
							"lastedit_user_id, to_group_id, to_user_id, " .
							"visibility, start_visible, end_visible)" .
							" VALUES " .
							"('announcement','".$row_props['insert_user_id']."','".$time."'," .
							"'$time','$ann_id','AnnouncementAdded'," .
							"'".$row_props['last_edit_user_id']."','".$row_props['to_group_id']."','".$row_props['to_user_id']."'," .
							"'".$row_props['visibility']."','".$row_props['start_visible']."','".$row_props['end_visible']."')";
					$res_ins_props = api_sql_query($sql_ins_props,__FILE__,__LINE__);
					if($res_ins_props <= 0){
						error_log('SQL Error in '.__FILE__.' at line '.__LINE__.': '.$sql_ins_props);
					}else{
						//copy was a success
						return $ann_id;
					}
				}
			}
		}else{
			return -1;
		}
	}
	return -1;*/
}

/**
* This function separates the users from the groups
* users have a value USER:XXX (with XXX the dokeos id
* groups have a value GROUP:YYY (with YYY the group id)
* @author: Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @return array
*/
function separate_users_groups($to)
{
/*	$grouplist = array();
    $userlist  = array();
	if(is_array($to) && count($to)>0)
    {
        foreach($to as $to_item)
    	{
    	list($type, $id) = explode(':', $to_item);
    
    	switch($type)
    		{
    		case 'GROUP':
    			$grouplist[] =$id;
    			break;
    		case 'USER':
    			$userlist[] =$id;
    			break;
    		}
    	}
        $send_to['groups']=$grouplist;
        $send_to['users']=$userlist;
    }
    return $send_to;*/
}

/**
* returns all the users and all the groups a specific Agenda item has been sent to
* @author: Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @return array
*/
function sent_to($tool, $id)
{
}

function load_edit_users($tool, $id)
{}
/**
* This functions swithes the visibility a course resource using the visible field in 'last_tooledit' values: 0 = invisible
* @author: Patrick Cool <patrick.cool@UGent.be>, Ghent University
*/
function change_visibility($tool,$id)
{}
/**
* The links that allows the student AND course administrator to show all agenda items and sort up/down
* @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
*/
function display_student_links()
{
	global $show;
	if ($_SESSION['sort'] == 'DESC')
	{
		echo "<a href='".api_get_self()."?sort=asc&amp;origin=".$_GET['origin']."'>".Display::return_icon('calendar_up.gif',get_lang('AgendaSortChronologicallyUp')).' '.get_lang("AgendaSortChronologicallyUp")."</a>";
	}
	else
	{
		echo "<a href='".api_get_self()."?sort=desc&amp;origin=".$_GET['origin']."'>".Display::return_icon('calendar_down.gif',get_lang('AgendaSortChronologicallyDown')).' '.get_lang("AgendaSortChronologicallyDown")."</a>";
	}	
	if ($_SESSION['view'] <> 'month')
	{
		echo "\t<a href=\"".api_get_self()."?action=view&amp;view=month\"><img src=\"../img/calendar_month.gif\" border=\"0\" alt=\"".get_lang('MonthView')."\" /> ".get_lang('MonthView')."</a>\n";
	}
	else 
	{
		echo "\t<a href=\"".api_get_self()."?action=view&amp;view=list\"><img src=\"../img/calendar_select.gif\" border=\"0\" alt=\"".get_lang('ListView')."\" /> ".get_lang('ListView')."</a>\n";
	}
}
/**
* get all the information of the agenda_item from the database
* @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @param integer the id of the agenda item we are getting all the information of
* @return an associative array that contains all the information of the agenda item. The keys are the database fields
*/
function get_agenda_item($id)
{
	$TABLEAGENDA = Database::get_main_table(TABLE_MAIN_SYSTEM_CALENDAR);
    //$t_agenda_repeat = Database::get_course_table(TABLE_AGENDA_REPEAT);
    $item = array();
	if(empty($id))
    {
        $id=(int)addslashes($_GET['id']);
    }
    else
    {
    	$id = (int) $id;
    }
    if(empty($id)){return $item;}
	$sql 					= "SELECT * FROM ".$TABLEAGENDA." WHERE id='".$id."'";
	$result					= api_sql_query($sql,__FILE__,__LINE__);
	$entry_to_edit 			= Database::fetch_array($result);
	$item['title']			= $entry_to_edit["title"];
	$item['content']		= $entry_to_edit["content"];
	$item['start_date']		= $entry_to_edit["start_date"];
	$item['end_date']		= $entry_to_edit["end_date"];
	$item['to']				== "everyone";
	// if the item has been sent to everybody then we show the compact to form
	if ($item['to']=="everyone")
	{
		$_SESSION['allow_individual_calendar']="hide";
	}
	else
	{
		$_SESSION['allow_individual_calendar']="show";
	}

	return $item;
}
/**
* This is the function that updates an agenda item. It does 3 things
* 1. storethe start_date, end_date, title and message in the calendar_event table
* 2. store the groups/users who this message is meant for in the item_property table
* 3. modify the attachments (if needed)
* @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
*/
function store_edited_agenda_item()
{

	// STEP 1: editing the calendar_event table
	// 1.a.  some filtering of the input data
	$id=(int)$_POST['id'];
	$title=strip_tags(trim($_POST['title'])); // no html allowed in the title
	$content=trim($_POST['content']);
	$start_date=(int)$_POST['fyear']."-".(int)$_POST['fmonth']."-".(int)$_POST['fday']." ".(int)$_POST['fhour'].":".(int)$_POST['fminute'].":00";
	$end_date=(int)$_POST['end_fyear']."-".(int)$_POST['end_fmonth']."-".(int)$_POST['end_fday']." ".(int)$_POST['end_fhour'].":".(int)$_POST['end_fminute'].":00";
	$to=$_POST['selectedform'];
	// 1.b. the actual saving in calendar_event table
	$edit_result=save_edit_agenda_item($id,$title,$content,$start_date,$end_date);

	echo '<br />';
	Display::display_normal_message(get_lang("EditSuccess"));

}

/**
* This function stores the Agenda Item in the table calendar_event and updates the item_property table also (after an edit)
* @author: Patrick Cool <patrick.cool@UGent.be>, Ghent University
*/
function save_edit_agenda_item($id,$title,$content,$start_date,$end_date)
{
	$TABLEAGENDA = Database::get_main_table(TABLE_MAIN_SYSTEM_CALENDAR);
		$id=Database::escape_string($id);
		$title=Database::escape_string($title);
		$content=Database::escape_string($content);
		$start_date=Database::escape_string($start_date);
		$end_date=Database::escape_string($end_date);
					

	// store the modifications in the table calendar_event
	$sql = "UPDATE ".$TABLEAGENDA."
								SET title='".$title."',
									content='".$content."',
									start_date='".$start_date."',
									end_date='".$end_date."'
								WHERE id='".$id."'";
	$result = api_sql_query($sql,__FILE__,__LINE__) or die (Database::error());
	return true;
}

/**
* This is the function that deletes an agenda item.
* The agenda item is no longer fycically deleted but the visibility in the item_property table is set to 2
* which means that it is invisible for the student AND course admin. Only the platform administrator can see it.
* This will in a later stage allow the platform administrator to recover resources that were mistakenly deleted
* by the course administrator
* @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @param integer the id of the agenda item wa are deleting
*/
function delete_agenda_item($id)
{
	global $_course;
	$id=Database::escape_string($id);
	if (is_allowed_to_edit()  && !api_is_anonymous())
	{
		if (!empty($_GET['id']) && isset($_GET['action']) && $_GET['action']=="delete")
		{
		    $t_agenda     = Database::get_main_table(TABLE_MAIN_SYSTEM_CALENDAR);
            $id=(int)addslashes($_GET['id']);
            $sql = "SELECT * FROM $t_agenda WHERE id = $id";
            $res = Database::query($sql,__FILE__,__LINE__);
            if(Database::num_rows($res)>0)
            {
                $sql = "DELETE FROM ".$t_agenda." WHERE id='$id'";
                $result = api_sql_query($sql,__FILE__,__LINE__) or die (Database::error());
            }
			api_item_property_update($_course,TOOL_CALENDAR_EVENT,$id,'delete',api_get_user_id());
			$id=null;
			echo '<br />';
			Display::display_normal_message(get_lang("AgendaDeleteSuccess"));
		}
	} 

}
/**
* Makes an agenda item visible or invisible for a student
* @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @param integer id the id of the agenda item we are changing the visibility of
*/
function showhide_agenda_item($id)
{
	global $nameTools;
	/*==================================================
				SHOW / HIDE A CALENDAR ITEM
	  ==================================================*/
	//  and $_GET['isStudentView']<>"false" is added to prevent that the visibility is changed after you do the following:
	// change visibility -> studentview -> course manager view
	if ((is_allowed_to_edit() && !api_is_anonymous()) and $_GET['isStudentView']<>"false")
	{
		if (isset($_GET['id'])&&$_GET['id']&&isset($_GET['action'])&&$_GET['action']=="showhide")
		{
			$id=(int)addslashes($_GET['id']);
			change_visibility($nameTools,$id);
			Display::display_normal_message(get_lang("VisibilityChanged"));
		}
	}
}
/**
* Displays all the agenda items
* @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @author Yannick Warnier <yannick.warnier@dokeos.com> - cleanup
*/
function display_agenda_items()
{
	$TABLEAGENDA = Database::get_main_table(TABLE_MAIN_SYSTEM_CALENDAR);
	global $select_month, $select_year;
	global $DaysShort, $DaysLong, $MonthsLong;
	global $is_courseAdmin;
	global $dateFormatLong, $timeNoSecFormat,$charset, $_user, $_course;

	// getting the group memberships
	//$group_memberships=GroupManager::get_group_ids($_course['dbName'],$_user['user_id']);

	// getting the name of the groups
	//$group_names=get_course_groups();

	/*--------------------------------------------------
			CONSTRUCT THE SQL STATEMENT
	  --------------------------------------------------*/

    $start = 0;
    $stop = 0;
	// this is to make a difference between showing everything (all months) or only the current month)
	// $show_all_current is a part of the sql statement
	if ($_SESSION['show']!=="showall")
	{
		$show_all_current=" AND MONTH(start_date)=$select_month AND year(start_date)=$select_year";
        $start = mktime(0,0,0,$select_month,1,$select_year);
        $stop = 0;
        if(empty($select_year)){$select_year = date('Y');}
        if(empty($select_month)){$select_month = date('m');}
        if($select_month==12)
        {
            $stop = mktime(0,0,0,1,1,$select_year+1)-1;
        }
        else
        {
            $stop = mktime(0,0,0,$select_month+1,1,$select_year)-1;
        }
	}
	else
	{
		$show_all_current="";
        $start = time();
        $stop = mktime(0,0,0,1,1,2038);//by default, set year to maximum for mktime()
	}

	// by default we use the id of the current user. The course administrator can see the agenda of other users by using the user / group filter
    
    $repeats = array(); //placeholder for repeated events

	if (is_allowed_to_edit() && !api_is_anonymous())
	{
			$sql="SELECT * FROM ".$TABLEAGENDA.' ORDER BY start_date '.$_SESSION['sort'];
		
	} // you are a student

//echo "<pre>".$sql."</pre>";
	$result=api_sql_query($sql,__FILE__,__LINE__) or die(Database::error());
	$number_items=Database::num_rows($result);

	/*--------------------------------------------------
			DISPLAY: NO ITEMS
	  --------------------------------------------------*/
	if ($number_items==0)
	{
        echo "<table class=\"data_table\" ><tr><td>".get_lang("NoAgendaItems")."</td></tr></table>";
	}

	/*--------------------------------------------------
			DISPLAY: THE ITEMS
	  --------------------------------------------------*/

    $month_bar="";
    $event_list="";
    $counter=0;
    $export_icon = 'export.png';
    $export_icon_low = 'export_low_fade.png';
    $export_icon_high = 'export_high_fade.png';

    while($myrow=Database::fetch_array($result))
    {
    	$is_repeated = !empty($myrow['parent_event_id']);
	    echo '<table class="data_table">',"\n";
        /*--------------------------------------------------
        		display: the month bar
         --------------------------------------------------*/
        // Make the month bar appear only once.
        if ($month_bar != date("m",strtotime($myrow["start_date"])).date("Y",strtotime($myrow["start_date"])))
		{
            $month_bar = date("m",strtotime($myrow["start_date"])).date("Y",strtotime($myrow["start_date"]));
			echo "\t<tr>\n\t\t<td class=\"agenda_month_divider\" colspan=\"3\" valign=\"top\">".
			ucfirst(format_locale_date("%B %Y",strtotime($myrow["start_date"]))).
			"</td>\n\t</tr>\n";
		}

        /*--------------------------------------------------
         display: the icon, title, destinees of the item
         -------------------------------------------------*/
    	echo '<tr>';

    	// highlight: if a date in the small calendar is clicked we highlight the relevant items
    	$db_date=(int)date("d",strtotime($myrow["start_date"])).date("n",strtotime($myrow["start_date"])).date("Y",strtotime($myrow["start_date"]));
    	if ($_GET["day"].$_GET["month"].$_GET["year"] <>$db_date)
    	{
    		if ($myrow['visibility']=='0')
    		{
    			$style="data_hidden";
    			$stylenotbold="datanotbold_hidden";
    			$text_style="text_hidden";
    		}
    		else
    		{
    			$style="data";
    			$stylenotbold="datanotbold";
    			$text_style="text";
    		}

    	}
    	else
    	{
    		$style="datanow";
    		$stylenotbold="datanotboldnow";
    		$text_style="textnow";
    	}

    	echo "\t\t<th>\n";
    	// adding an internal anchor
    	echo "\t\t\t<a name=\"".(int)date("d",strtotime($myrow["start_date"]))."\"></a>";
    	// the icons. If the message is sent to one or more specific users/groups
    	// we add the groups icon
    	// 2do: if it is sent to groups we display the group icon, if it is sent to a user we show the user icon
    	Display::display_icon('agenda.gif', get_lang('Agenda'));
    	if ($myrow['to_group_id']!=='0')
    	{
    		echo Display::return_icon('group.gif', get_lang('Group'));
    	}
    	echo " ".$myrow['title']."\n";
    	echo "\t\t</th>\n";

    	// the message has been sent to
    	echo "\t\t<th>".get_lang("SentTo").": ".get_lang('AllUsersOfThePlatform');
    	//$sent_to=sent_to(TOOL_CALENDAR_EVENT, $myrow["ref"]);
    	//$sent_to_form=sent_to_form($sent_to);
    //	echo $sent_to_form;
    	echo "</th>";

    	if (!$is_repeated && (api_is_allowed_to_edit(false,true) OR (api_get_course_setting('allow_user_edit_agenda') && !api_is_anonymous())))
    	{
    		if( ! (api_is_course_coach() && !api_is_element_in_the_session(TOOL_AGENDA, $myrow['id'] ) ) )
			{ // a coach can only delete an element belonging to his session
	    		echo '<th>'.get_lang('Modify');
	    		echo '</th></tr>';
			}
    	}

        /*--------------------------------------------------
     			display: the title
         --------------------------------------------------*/
    	echo "<tr class='row_odd'>";
    	echo "\t\t<td>".get_lang("StartTimeWindow").": ";
    	echo ucfirst(format_locale_date($dateFormatLong,strtotime($myrow["start_date"])))."&nbsp;&nbsp;&nbsp;";
    	echo ucfirst(strftime($timeNoSecFormat,strtotime($myrow["start_date"])))."";
    	echo "</td>\n";
    	echo "\t\t<td>";
    	if ($myrow["end_date"]<>"0000-00-00 00:00:00")
    	{
    		echo get_lang("EndTimeWindow").": ";
    		echo ucfirst(format_locale_date($dateFormatLong,strtotime($myrow["end_date"])))."&nbsp;&nbsp;&nbsp;";
    		echo ucfirst(strftime($timeNoSecFormat,strtotime($myrow["end_date"])))."";
    	}
    	echo "</td>\n";

    	// attachment list
	    	//$attachment_list=get_attachment($myrow['id']);

        /*--------------------------------------------------
    	 display: edit delete button (course admin only)
         --------------------------------------------------*/


    	if (!$is_repeated && (api_is_allowed_to_edit(false,true) OR (api_get_course_setting('allow_user_edit_agenda') && !api_is_anonymous())))
    	{
    		if( ! (api_is_course_coach() && !api_is_element_in_the_session(TOOL_AGENDA, $myrow['id'] ) ) )
			{ // a coach can only delete an element belonging to his session
				$mylink = api_get_self().'?'.api_get_cidreq().'&amp;origin='.Security::remove_XSS($_GET['origin']).'&amp;id='.$myrow['id'];
	    		echo '<td align="center">';
	    		// edit
    			echo '<a href="'.$mylink.'&amp;action=edit&amp;title="'.get_lang("ModifyCalendarItem").'">';
	    		echo Display::return_icon('edit.gif', get_lang('ModifyCalendarItem'))."</a>";

    			echo "<a href=\"".$mylink."&amp;action=delete\" onclick=\"javascript:if(!confirm('".addslashes(htmlentities(get_lang("ConfirmYourChoice"),ENT_QUOTES,$charset))."')) return false;\"  title=\"".get_lang("Delete")."\"> ";
	    		echo Display::return_icon('delete.gif', get_lang('Delete'))."</a>";
			}

    	if (!$is_repeated && (api_is_allowed_to_edit(false,true) OR (api_get_course_setting('allow_user_edit_agenda') && !api_is_anonymous())))
    	{
    		if( ! (api_is_course_coach() && !api_is_element_in_the_session(TOOL_AGENDA, $myrow['id'] ) ) )
			{ // a coach can only delete an element belonging to his session
    			$td_colspan= '<td colspan="3">';
			}
			else
			{
				$td_colspan= '<td colspan="2">';
			}
    	}
    	else
    	{
    		$td_colspan= '<td colspan="2">';
    	}
    	$mylink = 'calendar_ical_export.php?'.api_get_cidreq().'&amp;type=course&amp;id='.$myrow['id'];
		echo '<a class="ical_export" href="'.$mylink.'&amp;class=confidential" title="'.get_lang('ExportiCalConfidential').'">'.Display::return_icon($export_icon_high, get_lang('ExportiCalConfidential')).'</a> ';
    	echo '<a class="ical_export" href="'.$mylink.'&amp;class=private" title="'.get_lang('ExportiCalPrivate').'">'.Display::return_icon($export_icon_low, get_lang('ExportiCalPrivate')).'</a> ';
    	echo '<a class="ical_export" href="'.$mylink.'&amp;class=public" title="'.get_lang('ExportiCalPublic').'">'.Display::return_icon($export_icon, get_lang('ExportiCalPublic')).'</a> ';
	    echo '<a href="#" onclick="javascript:win_print=window.open(\'calendar_view_print.php?id='.$myrow['id'].'\',\'popup\',\'left=100,top=100,width=700,height=500,scrollbars=1,resizable=0\'); win_print.focus(); return false;">'.Display::return_icon('print.gif', get_lang('Print')).'</a>&nbsp;';
    	echo '</td>';
    	echo '</tr>';
}

        /*--------------------------------------------------
     			display: the content
         --------------------------------------------------*/
    	$content = $myrow['content'];
    	$content = make_clickable($content);
    	$content = text_filter($content);
    	echo "<tr class='row_even'>";
    	echo "<td colspan='3'>";

    	echo $content;
    	// show attachment list
			if (!empty($attachment_list)) {

				$realname=$attachment_list['path'];
				$user_filename=$attachment_list['filename'];
				$full_file_name = 'download.php?file='.$realname;
				echo Display::return_icon('attachment.gif',get_lang('Attachment'));
				echo '<a href="'.$full_file_name.'';
				echo ' "> '.$user_filename.' </a>';
				echo '<span class="forum_attach_comment" >'.$attachment_list['comment'].'</span>';
				if (api_is_allowed_to_edit()) {
					echo '&nbsp;&nbsp;<a href="'.api_get_self().'?'.api_get_cidreq().'&amp;origin='.Security::remove_XSS($_GET['origin']).'&amp;action=delete_attach&amp;id_attach='.$attachment_list['id'].'" onclick="javascript:if(!confirm(\''.addslashes(htmlentities(get_lang("ConfirmYourChoice"),ENT_QUOTES,$charset)).'\')) return false;">'.Display::return_icon('delete.gif',get_lang('Delete')).'</a><br />';	
				}				

			}

	    echo '</td></tr>';


        /*--------------------------------------------------
     			display: the added resources
         --------------------------------------------------
    	if (check_added_resources("Agenda", $myrow["id"]))
    	{

    		echo '<tr>';
    		echo '<td colspan="3">';
    		echo "<i>".get_lang("AddedResources")."</i><br/>";
    		if ($myrow['visibility']==0)
    		{
    			$addedresource_style="invisible";
    		}
    		display_added_resources("Agenda", $myrow["id"], $addedresource_style);
    		echo "</td></tr>";
    	}*/


    	$event_list.=$myrow['id'].',';

    	$counter++;
        /*--------------------------------------------------
    	 display: jump-to-top icon
         --------------------------------------------------*/
    	echo '<tr>';
        echo '<td colspan="3">';
        if($is_repeated){echo get_lang('RepeatedEvent'),'<a href="',api_get_self(),'?',api_get_cidreq,'&amp;agenda_id=',$myrow['parent_event_id'],'" alt="',get_lang('RepeatedEventViewOriginalEvent'),'">',get_lang('RepeatedEventViewOriginalEvent'),'</a>';}
    	echo "<a href=\"#top\">".Display::return_icon('top.gif', get_lang('Top'))."</a></td></tr>";
    	echo "</table><br /><br />";
    } // end while ($myrow=Database::fetch_array($result))

    if(!empty($event_list))
    {
    	$event_list=substr($event_list,0,-1);
    }
    else
    {
    	$event_list='0';
    }

    echo "<form name=\"event_list_form\"><input type=\"hidden\" name=\"event_list\" value=\"$event_list\" /></form>";

    // closing the layout table
    echo "</td>",
    	"</tr>",
    	"</table>";
}

/**
* Displays only 1 agenda item. This is used when an agenda item is added to the learning path.
* @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
*/
function display_one_agenda_item($agenda_id)
{
	$TABLEAGENDA = Database::get_main_table(TABLE_MAIN_SYSTEM_CALENDAR);
	global $TABLE_ITEM_PROPERTY;
	global $select_month, $select_year;
	global $DaysShort, $DaysLong, $MonthsLong;
	global $is_courseAdmin;
	global $dateFormatLong, $timeNoSecFormat, $charset;
	global $_user;
	$agenda_id=Database::escape_string($agenda_id);
	//echo "displaying agenda items";

	// getting the name of the groups
	//$group_names=get_course_groups();

	/*--------------------------------------------------
			CONSTRUCT THE SQL STATEMENT
	  --------------------------------------------------*/

	$sql="SELECT *	FROM ".$TABLEAGENDA;
	$result=api_sql_query($sql,__FILE__,__LINE__) or die(Database::error());
	$number_items=Database::num_rows($result);
	$myrow=Database::fetch_array($result); // there should be only one item so no need for a while loop
    
    $sql_rep = "SELECT * FROM $TABLEAGENDA WHERE id = $agenda_id";
    $res_rep = Database::query($sql_rep,__FILE__,__LINE__);
    $repeat = false;
    $repeat_id = 0;
    if(Database::num_rows($res_rep)>0)
    {
        $repeat=true;
        $row_rep = Database::fetch_array($res_rep);
        //$repeat_id = $row_rep['parent_event_id']; 
    }
    
	/*--------------------------------------------------
			DISPLAY: NO ITEMS
	  --------------------------------------------------*/
	if ($number_items==0)
	{
		echo "<table id=\"data_table\" ><tr><td>".get_lang("NoAgendaItems")."</td></tr></table>";
	}

	/*--------------------------------------------------
			DISPLAY: THE ITEMS
	  --------------------------------------------------*/
	echo "<table id=\"data_table\">\n";

	/*--------------------------------------------------
	 DISPLAY : the icon, title, destinees of the item
	  --------------------------------------------------*/
	echo "\t<tr>\n";

	// highlight: if a date in the small calendar is clicked we highlight the relevant items
	$db_date=(int)date("d",strtotime($myrow["start_date"])).date("n",strtotime($myrow["start_date"])).date("Y",strtotime($myrow["start_date"]));
	if ($_GET["day"].$_GET["month"].$_GET["year"] <>$db_date)
	{
		if ($myrow['visibility']=='0')
		{
			$style="data_hidden";
			$stylenotbold="datanotbold_hidden";
			$text_style="text_hidden";
		}
		else
		{
			$style="data";
			$stylenotbold="datanotbold";
			$text_style="text";
		}
	}
	else
	{
		$style="datanow";
		$stylenotbold="datanotboldnow";
		$text_style="textnow";
	}


	echo "\t\t<td class=\"".$style."\">\n";

	// adding an internal anchor
	echo "\t\t\t<a name=\"".(int)date("d",strtotime($myrow["start_date"]))."\"></a>";

	// the icons. If the message is sent to one or more specific users/groups
	// we add the groups icon
	// 2do: if it is sent to groups we display the group icon, if it is sent to a user we show the user icon
	echo Display::return_icon('agenda.gif');
	if ($myrow['to_group_id']!=='0')
	{
		echo Display::return_icon('group.gif');
	}
	echo " ".$myrow['title']."\n";
	echo "\t\t</td>\n";

	// the message has been sent to
	echo "\t\t<td class=\"".$stylenotbold."\">".get_lang("SentTo").": ".get_lang('AllUsersOfThePlatform');
	$sent_to=sent_to(TOOL_CALENDAR_EVENT, $myrow["ref"]);
	$sent_to_form=sent_to_form($sent_to);
	echo $sent_to_form;
	echo "</td>\n\t</tr>\n";

	/*--------------------------------------------------
	 			DISPLAY: the title
	  --------------------------------------------------*/
	echo "\t<tr class=\"".$stylenotbold."\">\n";
	echo "\t\t<td>".get_lang("StartTime").": ";
	echo ucfirst(format_locale_date($dateFormatLong,strtotime($myrow["start_date"])))."&nbsp;&nbsp;&nbsp;";
	echo ucfirst(strftime($timeNoSecFormat,strtotime($myrow["start_date"])))."";
	echo "</td>\n";
	echo "\t\t<td>".get_lang("EndTime").": ";
	echo ucfirst(format_locale_date($dateFormatLong,strtotime($myrow["end_date"])))."&nbsp;&nbsp;&nbsp;";
	echo ucfirst(strftime($timeNoSecFormat,strtotime($myrow["end_date"])))."";
	echo "</td>\n";
	echo "\n\t</tr>\n";

	/*--------------------------------------------------
	 			DISPLAY: the content
	  --------------------------------------------------*/
    $export_icon = '../img/export.png';
    $export_icon_low = '../img/export_low_fade.png';
    $export_icon_high = '../img/export_high_fade.png';

	$content = $myrow['content'];
	$content = make_clickable($content);
	$content = text_filter($content);
	//echo "\t<tr>\n\t\t<td class=\"".$text_style."\" colspan='2'>";
	//echo $content;
	//echo "</td></tr>";
    echo "<tr class='row_even'>";
    echo '<td colspan="2">';
    echo $content;
    echo '</td></tr>';

	/*--------------------------------------------------
	 			DISPLAY: the added resources
	  --------------------------------------------------*/
	if (check_added_resources("Agenda", $myrow["id"]))
		{
		echo "<tr><td colspan='2'>";
		echo "<i>".get_lang("AddedResources")."</i><br/>";
		if ($myrow['visibility']==0)
		{
			$addedresource_style="invisible";
		}
		display_added_resources("Agenda", $myrow["id"], $addedresource_style);
		echo "</td></tr>";
		}

	/*--------------------------------------------------
		DISPLAY: edit delete button (course admin only)
	  --------------------------------------------------*/
	echo '<tr><td colspan="2">';
	if (!$repeat && api_is_allowed_to_edit(false,true))	{
		// edit
		$mylink = api_get_self()."?".api_get_cidreq()."&amp;origin=".Security::remove_XSS($_GET['origin'])."&amp;id=".$myrow['id'];
		echo 	"<a href=\"".$mylink."&amp;action=edit\">",
				Display::return_icon('edit.gif', get_lang('ModifyCalendarItem')), "</a>",
				"<a href=\"".$mylink."&amp;action=delete\" onclick=\"javascript:if(!confirm('".addslashes(htmlentities(get_lang("ConfirmYourChoice"),ENT_QUOTES,$charset))."')) return false;\">",
				Display::return_icon('delete.gif', get_lang('Delete')),"</a>";
		if ($myrow['visibility']==1) {
			$image_visibility="visible";
		} else {
			$image_visibility="invisible";
		}
		echo 	'<a href="'.$mylink.'&amp;action=showhide">',Display::return_icon($image_visibility, get_lang('Visible')),'</a><br /><br />';
	}
   	$mylink = 'calendar_ical_export.php?'.api_get_cidreq().'&amp;type=course&amp;id='.$myrow['id'];
    echo '<a class="ical_export" href="'.$mylink.'&amp;class=confidential" title="'.get_lang('ExportiCalConfidential').'">'.Display::return_icon($export_icon_high, get_lang('ExportiCalConfidential')).'</a> ';
    	echo '<a class="ical_export" href="'.$mylink.'&amp;class=private" title="'.get_lang('ExportiCalPrivate').'">'.Display::return_icon($export_icon_low, get_lang('ExportiCalPrivate')).'</a> ';
    	echo '<a class="ical_export" href="'.$mylink.'&amp;class=public" title="'.get_lang('ExportiCalPublic').'">'.Display::return_icon($export_icon, get_lang('ExportiCalPublic')).'</a> ';
    echo '<a href="#" onclick="javascript:win_print=window.open(\'calendar_view_print.php?id='.$myrow['id'].'\',\'popup\',\'left=100,top=100,width=700,height=500,scrollbars=1,resizable=0\'); win_print.focus(); return false;">'.Display::return_icon('print.gif', get_lang('Print')).'</a>&nbsp;';
	echo "</td>";
    if($repeat) {
    	echo '<tr>';
    	echo '<td colspan="2">',get_lang('RepeatedEvent'),'<a href="',api_get_self(),'?',api_get_cidreq(),'&amp;agenda_id=',$repeat_id,'" alt="',get_lang('RepeatedEventViewOriginalEvent'),'">',get_lang('RepeatedEventViewOriginalEvent'),'</a></td>';
        echo '</tr>';
    }
	echo "</table>";

	// closing the layout table
	echo "</td>",
		"</tr>",
		"</table>";
}
/**
* Show the form for adding a new agenda item. This is the same function that is used whenever we are editing an
* agenda item. When the id parameter is empty (default behaviour), then we show an empty form, else we are editing and
* we have to retrieve the information that is in the database and use this information in the forms.
* @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @param integer id, the id of the agenda item we are editing. By default this is empty which means that we are adding an
*		 agenda item.
*/
function show_group_filter_form()
{
$group_list=get_course_groups();

echo "<select name=\"select\" onchange=\"MM_jumpMenu('parent',this,0)\">";
echo "<option value=\"agenda.php?group=none\">show all groups</option>";
/*foreach($group_list as $this_group)
	{
	// echo "<option value=\"agenda.php?isStudentView=true&amp;group=".$this_group['id']."\">".$this_group['name']."</option>";
	echo "<option value=\"agenda.php?group=".$this_group['id']."\" ";
	echo ($this_group['id']==$_SESSION['group'])? " selected":"" ;
	echo ">".$this_group['name']."</option>";
	}*/
echo "</select>";
}

function show_user_filter_form()
{
$user_list=get_course_users();

//echo "<select name=\"select\" onchange=\"MM_jumpMenu('parent',this,0)\">";
//echo "<option value=\"agenda.php?user=none\">show all users</option>";
/*foreach($user_list as $this_user)
	{
	// echo "<option value=\"agenda.php?isStudentView=true&amp;user=".$this_user['uid']."\">".$this_user['lastName']." ".$this_user['firstName']."</option>";
	echo "<option value=\"agenda.php?user=".$this_user['uid']."\" ";
	echo ($this_user['uid']==$_SESSION['user'])? " selected":"" ;
	echo ">".$this_user['lastName']." ".$this_user['firstName']."</option>";
	}*/
echo "</select>";
}

function show_user_group_filter_form()
{
	echo "\n<select name=\"select\" onchange=\"MM_jumpMenu('parent',this,0)\">";
	echo "\n\t<option value=\"agenda.php?user=none\">".get_lang("ShowAll")."</option>";

	// Groups
	echo "\n\t<optgroup label=\"".get_lang("Groups")."\">";
	$group_list=get_course_groups();
/*	foreach($group_list as $this_group)
	{
		// echo "<option value=\"agenda.php?isStudentView=true&amp;group=".$this_group['id']."\">".$this_group['name']."</option>";
		echo "\n\t\t<option value=\"agenda.php?group=".$this_group['id']."\" ";
		echo ($this_group['id']==$_SESSION['group'])? " selected":"" ;
		echo ">".$this_group['name']."</option>";
	}*/
	echo "\n\t</optgroup>";

	// Users
	echo "\n\t<optgroup label=\"".get_lang("Users")."\">";
	$user_list=get_course_users();
/*	foreach($user_list as $this_user)
		{
		// echo "<option value=\"agenda.php?isStudentView=true&amp;user=".$this_user['uid']."\">".$this_user['lastName']." ".$this_user['firstName']."</option>";
		echo "\n\t\t<option value=\"agenda.php?user=".$this_user['uid']."\" ";
		echo ($this_user['uid']==$_SESSION['user'])? " selected":"" ;
		echo ">".$this_user['lastName']." ".$this_user['firstName']."</option>";
		}*/
	echo "\n\t</optgroup>";
	echo "</select>";
}

function show_add_form($id = '')
{

	global $MonthsLong;

	// the default values for the forms
	if ($_GET['originalresource'] !== 'no')
	{
		$day	= date('d');
		$month	= date('m');
		$year	= date('Y');
		$hours	= 9;
		$minutes= '00';

		$end_day	= date('d');
		$end_month	= date('m');
		$end_year	= date('Y');
		$end_hours	= 17;
		$end_minutes= '00';
        $repeat = false;
	}
	else
	{

		// we are coming from the resource linker so there might already have been some information in the form.
		// When we clicked on the button to add resources we stored every form information into a session and now we
		// are doing the opposite thing: getting the information out of the session and putting it into variables to
		// display it in the forms.
		$form_elements=$_SESSION['formelements'];
		$day=$form_elements['day'];
		$month=$form_elements['month'];
		$year=$form_elements['year'];
		$hours=$form_elements['hour'];
		$minutes=$form_elements['minutes'];
		$end_day=$form_elements['end_day'];
		$end_month=$form_elements['end_month'];
		$end_year=$form_elements['end_year'];
		$end_hours=$form_elements['end_hours'];
		$end_minutes=$form_elements['end_minutes'];
		$title=$form_elements['title'];
		$content=$form_elements['content'];
		$id=$form_elements['id'];
		$to=$form_elements['to'];
        $repeat = $form_elements['repeat'];
	}


	//	switching the send to all/send to groups/send to users
	if ($_POST['To'])
	{
			$day			= $_POST['fday'];
			$month			= $_POST['fmonth'];
			$year			= $_POST['fyear'];
			$hours			= $_POST['fhour'];
			$minutes		= $_POST['fminute'];
			$end_day		= $_POST['end_fday'];
			$end_month		= $_POST['end_fmonth'];
			$end_year		= $_POST['end_fyear'];
			$end_hours		= $_POST['end_fhour'];
			$end_minutes	= $_POST['end_fminute'];
			$title 			= $_POST['title'];
			$content		= $_POST['content'];
			// the invisible fields
			$action			= $_POST['action'];
			$id				= $_POST['id'];
            $repeat         = !empty($_POST['repeat'])?true:false;
		}


	// if the id is set then we are editing an agenda item
	if (is_int($id))
	{
		//echo "before get_agenda_item".$_SESSION['allow_individual_calendar'];
		$item_2_edit=get_agenda_item($id);
		$title	= $item_2_edit['title'];
		$content= $item_2_edit['content'];
		// start date
		list($datepart, $timepart) = split(" ", $item_2_edit['start_date']);
		list($year, $month, $day) = explode("-", $datepart);
		list($hours, $minutes, $seconds) = explode(":", $timepart);
		// end date
		list($datepart, $timepart) = split(" ", $item_2_edit['end_date']);
		list($end_year, $end_month, $end_day) = explode("-", $datepart);
		list($end_hours, $end_minutes, $end_seconds) = explode(":", $timepart);
		// attachments
		//edit_added_resources("Agenda", $id);
		$to=$item_2_edit['to'];
		//echo "<br />after get_agenda_item".$_SESSION['allow_individual_calendar'];
	}
	$content=stripslashes($content);
	$title=stripslashes($title);
	// we start a completely new item, we do not come from the resource linker
	if ($_GET['originalresource']!=="no" and $_GET['action']=="add")
	{

		$_SESSION["formelements"]=null;
		//unset_session_resources();//--------------------------------------------------------------------------------------
	}
?>

<!-- START OF THE FORM  -->
<form enctype="multipart/form-data" action="<?php echo api_get_self().'?origin='.$_GET['origin'].'&amp;action='.$_GET['action']; ?>" method="post" name="new_calendar_item">
<input type="hidden" name="id" value="<?php if (isset($id)) echo $id; ?>" />
<input type="hidden" name="action" value="<?php if (isset($_GET['action'])) echo $_GET['action']; ?>" />
<table border="0" cellpadding="5" cellspacing="0" width="100%" id="newedit_form">
	<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
	<tr class="title">
	
	
	
		<td colspan="2" align="left">
		<span style="font-weight: bold;"><?php echo (isset($id) AND $id<>'')?get_lang('ModifyCalendarItem'):get_lang("AddCalendarItem"); ?></span>
		</td>
	</tr>

	<!-- START date and time -->
<tr>
<div>
<table>

				<td >	
					<!-- date: 1 -> 31 -->
					<?php echo get_lang('StartDate').": \n"; ?>
				</td>
				<td >
					<select name="fday" onchange="javascript:document.new_calendar_item.end_fday.value=this.value;">
							<?php
							// small loop for filling all the dates
							// 2do: the available dates should be those of the selected month => february is from 1 to 28 (or 29) and not to 31
							echo "\n";
							foreach (range(1, 31) as $i)
											{
											// values have to have double digits
											$value = ($i <= 9 ? '0'.$i : $i );
											// the current day is indicated with [] around the date
											if ($value==$day)
											{
												echo "\t\t\t\t <option value=\"".$value."\" selected> ".$i." </option>\n";
											}
											else
											{
												echo "\t\t\t\t<option value=\"$value\">$i</option>\n";
											}
										}
										 ?>
					</select>
					<!-- month: january -> december -->
					<select name="fmonth" onchange="javascript:document.new_calendar_item.end_fmonth.value=this.value;">
					<?php
											echo "\n";
											for ($i=1; $i<=12; $i++)
											{
												// values have to have double digits
												if ($i<=9)
												{
													$value="0".$i;
												}
												else
												{
													$value=$i;
												}
												if ($value==$month)
												{
													echo "\t\t\t\t <option value=\"".$value."\" selected>".$MonthsLong[$i-1]."</option>\n";
												}
												else
												{
													echo "\t\t\t\t <option value=\"".$value."\">".$MonthsLong[$i-1]."</option>\n";
												}
											} ?>
					</select>
					<select name="fyear" onchange="javascript:document.new_calendar_item.end_fyear.value=this.value;">
										<option value="<?php echo ($year-1); ?>"><?php echo ($year-1); ?></option>
											<option value="<?php echo $year; ?>" selected="selected"><?php echo $year; ?></option>
											<?php
												echo "\n";
												for ($i=1; $i<=5; $i++)
												{
													$value=$year+$i;
													echo "\t\t\t\t<option value=\"$value\">$value</option>\n";
												} ?>
					</select>
					<a href="javascript:openCalendar('new_calendar_item', 'f')"><?php Display::display_icon('calendar_select.gif'); ?></a>
				</td>
				<td >
						<!-- date: 1 -> 31 -->
						<?php echo get_lang('StartTime').": "; ?>
				</td>
				<td >

						<select name="fhour" onchange="javascript:document.new_calendar_item.end_fhour.value=this.value;">
							<!-- <option value="--">--</option> -->
							<?php
								echo "\n";
								foreach (range(0, 23) as $i)
								{
									// values have to have double digits
									$value = ($i <= 9 ? '0'.$i : $i );
									// the current hour is indicated with [] around the hour
									if ($hours==$value)
									{
										echo "\t\t\t\t<option value=\"".$value."\" selected> ".$value." </option>\n";
									}
									else
									{
										echo "\t\t\t\t<option value=\"$value\">$value</option>\n";
									}
								} ?>
						</select>
						
						<select name="fminute" onchange="javascript:document.new_calendar_item.end_fminute.value=this.value;">
							<!-- <option value="<?php echo $minutes ?>"><?php echo $minutes; ?></option> -->
							<!-- <option value="--">--</option> -->
							<?php
								foreach (range(0, 59) as $i)
								{
									// values have to have double digits
									$value = ($i <= 9 ? '0'.$i : $i );
									echo "\t\t\t\t<option value=\"$value\">$value</option>\n";
								} ?>
						</select>
					</td>

</div>
</tr>
			<!-- END date and time -->
<tr>
<div>

					<td >			
							<!-- date: 1 -> 31 -->
							<?php echo get_lang('EndDate').": "; ?>
					</td>
					<td  >
						<select name="end_fday">
							<?php
								// small loop for filling all the dates
								// 2do: the available dates should be those of the selected month => february is from 1 to 28 (or 29) and not to 31
								echo "\n";
								foreach (range(1, 31) as $i)
								{
									// values have to have double digits
									$value = ($i <= 9 ? '0'.$i : $i );
									// the current day is indicated with [] around the date
									if ($value==$end_day)
										{ echo "\t\t\t\t <option value=\"".$value."\" selected> ".$i." </option>\n";}
									else
										{ echo "\t\t\t\t <option value=\"".$value."\">".$i."</option>\n"; }
									}?>
						</select>
							<!-- month: january -> december -->
						<select name="end_fmonth">
								<?php
								echo "\n";
								foreach (range(1, 12) as $i)
								{
									// values have to have double digits
									$value = ($i <= 9 ? '0'.$i : $i );
									if ($value==$end_month)
										{ echo "\t\t\t\t <option value=\"".$value."\" selected>".$MonthsLong[$i-1]."</option>\n"; }
									else
										{ echo "\t\t\t\t <option value=\"".$value."\">".$MonthsLong[$i-1]."</option>\n"; }
									}?>
						</select>			
						<select name="end_fyear">
								<option value="<?php echo ($end_year-1) ?>"><?php echo ($end_year-1) ?></option>
								<option value="<?php echo $end_year ?>" selected> <?php echo $end_year ?> </option>
								<?php
								echo "\n";
								for ($i=1; $i<=5; $i++)
								{
									$value=$end_year+$i;
									echo "\t\t\t\t<option value=\"$value\">$value</option>\n";
								} ?>
						</select>
						<a href="javascript:openCalendar('new_calendar_item', 'end_f')"><?php Display::display_icon('calendar_select.gif'); ?></a>
					</td>

				<td >
							<!-- date: 1 -> 31 -->
							<?php echo get_lang('EndTime').": \n"; ?>
				</td >
				<td >
						<select name="end_fhour">
							<!-- <option value="--">--</option> -->
							<?php
								echo "\n";
								foreach (range(0, 23) as $i)
								{
									// values have to have double digits
									$value = ($i <= 9 ? '0'.$i : $i );
									// the current hour is indicated with [] around the hour
									if ($end_hours==$value)
										{ echo "\t\t\t\t<option value=\"".$value."\" selected> ".$value." </option>\n"; }
									else
										{ echo "\t\t\t\t<option value=\"".$value."\"> ".$value." </option>\n"; }
								} ?>
						</select>
						
						<select name="end_fminute">
							<!-- <option value="<?php echo $end_minutes; ?>"><?php echo $end_minutes; ?></option> -->
							<!-- <option value="--">--</option> -->
							<?php
								foreach (range(0, 59) as $i)
								{
									// values have to have double digits
									$value = ($i <= 9 ? '0'.$i : $i );
									echo "\t\t\t\t<option value=\"$value\">$value</option>\n";
								} ?>
						</select>
						
						<br>
				</td>
</div>
</tr>

	<tr class="subtitle">
		<hr noshade="noshade" color="#cccccc" />
		<td colspan="3" valign="top"><?php echo get_lang('ItemTitle'); ?> :
			<!--<div style='margin-left: 80px'><textarea name="title" cols="50" rows="2" wrap="virtual" style="vertical-align:top; width:75%; height:50px;"><?php  if (isset($title)) echo $title; ?></textarea></div>-->
			<input type="text" size="60" name="title" value="<?php  if (isset($title)) echo $title; ?>" />
		</td>
	</tr>

	<tr>
		<td colspan="3">

			<?php
			require_once(api_get_path(LIBRARY_PATH) . "/fckeditor/fckeditor.php");

			$oFCKeditor = new FCKeditor('content') ;
			$oFCKeditor->BasePath	= api_get_path(WEB_PATH) . 'main/inc/lib/fckeditor/' ;
			$oFCKeditor->Height		= '175';
			$oFCKeditor->Width		= '100%';
			$oFCKeditor->Value		= $content;
			$oFCKeditor->Config['CustomConfigurationsPath'] = api_get_path(REL_PATH)."main/inc/lib/fckeditor/myconfig.js";
			$oFCKeditor->ToolbarSet = 'Agenda';

			$TBL_LANGUAGES = Database::get_main_table(TABLE_MAIN_LANGUAGE);
			$sql="SELECT isocode FROM ".$TBL_LANGUAGES." WHERE english_name='".$_SESSION["_course"]["language"]."'";
			$result_sql=api_sql_query($sql);
			//$isocode_language=Database::result($result_sql,0,0);
			$oFCKeditor->Config['DefaultLanguage'] = $isocode_language;

			$return =	$oFCKeditor->CreateHtml();

			echo $return;

 ?>
		</td>
	</tr>
	<!--<?php /* ADDED BY UGENT, Patrick Cool, march 2004 */ ?>
	<tr>
		<td colspan="7">
	    <?php
			//onclick="selectAll(this.form.elements[6],true)"

//				show_addresource_button('onclick="selectAll(this.form.elements[6],true)"');

		?>
		</td>
	</tr>-->
	<?php
	   //if ($_SESSION['addedresource'])
	   echo "\t<tr>\n";
	   echo "\t\t<td colspan=\"3\">\n";
	   //echo display_resources(0);//--------------------------------------------------------
	   $test=$_SESSION['addedresource'];
	   echo "\t\t</td>\n\t</tr>\n";
	   /* END ADDED BY UGENT, Patrick Cool, march 2004 */
    if(empty($id)) //only show repeat fields when adding the first time
    {
	?>
    
    <tr>
      <td colspan="2" />      
    </tr>
    <?php
    }//only show repeat fields if adding, not if editing
    ?>
	<tr>
		<td colspan="3">
		<input type="submit" name="submit_event" value="<?php echo get_lang('Ok'); ?>" />
		</td>
	</tr>
</table>
</form>
<?php
}

function get_agendaitems($month, $year)
{
	global $_user;
	global $_configuration;
	$month=Database::escape_string($month);
	$year=Database::escape_string($year);

	$items = array ();

	//databases of the courses
	$TABLEAGENDA = Database :: get_course_table(TABLE_MAIN_SYSTEM_CALENDAR);
	//$TABLE_ITEMPROPERTY = Database :: get_course_table(TABLE_ITEM_PROPERTY);

	//$group_memberships = GroupManager :: get_group_ids(Database::get_current_course_database(), $_user['user_id']);
	// if the user is administrator of that course we show all the agenda items
	//if (api_is_allowed_to_edit())
	//{
		//echo "course admin";
		$sqlquery = "SELECT
						DISTINCT *
						FROM ".$TABLEAGENDA."							 
						WHERE 
						MONTH(start_date)='".$month."'
						AND YEAR(start_date)='".$year."'
						GROUP BY id
						ORDER BY start_date ";
	//}
	// if the user is not an administrator of that course
/*	else
	{
		//echo "GEEN course admin";
		if (is_array($group_memberships) && count($group_memberships)>0)
		{
			$sqlquery = "SELECT
							agenda.*
							FROM ".$TABLEAGENDA."
							WHERE
							MONTH(agenda.start_date)='".$month."'
							AND YEAR(agenda.start_date)='".$year."'
							ORDER BY start_date ";
		}
		else
		{
			$sqlquery = "SELECT
							agenda.*, item_property.*
							FROM ".$TABLEAGENDA."
							WHERE 
							MONTH(agenda.start_date)='".$month."'
							AND YEAR(agenda.start_date)='".$year."'
							ORDER BY start_date ";
		}
	}*/

	//$mycourse = api_get_course_info();
    $result = api_sql_query($sqlquery, __FILE__, __LINE__);
	while ($item = Database::fetch_array($result))
	{
		$agendaday = date('j',strtotime($item['start_date']));
		$time= date('H:i',strtotime($item['start_date']));
		$URL = $_configuration['root_web'].'main/admin/agenda.php?day='.$agendaday."&amp;month=".$month."&amp;year=".$year; // RH  //Patrick Cool: to highlight the relevant agenda item
		$items[$agendaday][$item['start_time']] .= '<i>'.$time.'</i> <a href="'.$URL.'" title="'.$item['title'].'<br />';
	}
		
	// sorting by hour for every day
	$agendaitems = array ();
	while (list ($agendaday, $tmpitems) = each($items))
	{
		sort($tmpitems);
		while (list ($key, $val) = each($tmpitems))
		{
			$agendaitems[$agendaday] .= $val;
		}
	}
	return $agendaitems;
}

function display_upcoming_events()
{
	echo '<b>'.get_lang('UpcomingEvent').'</b><br />';
	$number_of_items_to_show = (int)api_get_setting('number_of_upcoming_events');
	
	//databases of the courses
	$TABLEAGENDA = Database :: get_main_table(TABLE_MAIN_SYSTEM_CALENDAR);
	//$TABLE_ITEMPROPERTY = Database :: get_course_table(TABLE_ITEM_PROPERTY);
    //$mycourse = api_get_course_info();
    //$myuser = api_get_user_info();

	//$group_memberships = GroupManager :: get_group_ids($mycourse['dbName'], $myuser['user_id']);
	// if the user is administrator of that course we show all the agenda items
	/*if (api_is_allowed_to_edit())
	{*/
		//echo "course admin";
		$sqlquery = "SELECT
						DISTINCT *
						FROM ".$TABLEAGENDA."
						ORDER BY start_date ";
	//}
	// if the user is not an administrator of that course
	$result = api_sql_query($sqlquery, __FILE__, __LINE__);
	$counter = 0;
	while ($item = Database::fetch_array($result,'ASSOC'))	
	{
		if ($counter < $number_of_items_to_show)
		{
			echo $item['start_date'],' - ',$item['title'],'<br />';
			$counter++;
		}
	}
}
/**
 * This function calculates the startdate of the week (monday)
 * and the enddate of the week (sunday)
 * and returns it as an array
 */
function calculate_start_end_of_week($week_number, $year)
{
	// determine the start and end date
	// step 1: we calculate a timestamp for a day in this week
	$random_day_in_week = mktime(0, 0, 0, 1, 1, $year) + ($week_number-1) * (7 * 24 * 60 * 60); // we calculate a random day in this week
	// step 2: we which day this is (0=sunday, 1=monday, ...)
	$number_day_in_week = date('w', $random_day_in_week);
	// step 3: we calculate the timestamp of the monday of the week we are in
	$start_timestamp = $random_day_in_week - (($number_day_in_week -1) * 24 * 60 * 60);
	// step 4: we calculate the timestamp of the sunday of the week we are in
	$end_timestamp = $random_day_in_week + ((7 - $number_day_in_week +1) * 24 * 60 * 60) - 3600;
	// step 5: calculating the start_day, end_day, start_month, end_month, start_year, end_year
	$start_day = date('j', $start_timestamp);
	$start_month = date('n', $start_timestamp);
	$start_year = date('Y', $start_timestamp);
	$end_day = date('j', $end_timestamp);
	$end_month = date('n', $end_timestamp);
	$end_year = date('Y', $end_timestamp);
	$start_end_array['start']['day'] = $start_day;
	$start_end_array['start']['month'] = $start_month;
	$start_end_array['start']['year'] = $start_year;
	$start_end_array['end']['day'] = $end_day;
	$start_end_array['end']['month'] = $end_month;
	$start_end_array['end']['year'] = $end_year;
	return $start_end_array;
}
/**
 * Show the mini calendar of the given month
 */
function display_daycalendar($agendaitems, $day, $month, $year, $weekdaynames, $monthName)
{
	global $DaysShort, $DaysLong, $course_path;
	global $MonthsLong;
	global $query;

	// timestamp of today
	$today = mktime();
	$nextday = $today + (24 * 60 * 60);
	$previousday = $today - (24 * 60 * 60);
	// the week number of the year
	$week_number = date("W", $today);
	// if we moved to the next / previous day we have to recalculate the $today variable
	if ($_GET['day'])
	{
		$today = mktime(0, 0, 0, $month, $day, $year);
		$nextday = $today + (24 * 60 * 60);
		$previousday = $today - (24 * 60 * 60);
		$week_number = date("W", $today);
	}
	// calculating the start date of the week
	// the date of the monday of this week is the timestamp of today minus
	// number of days that have already passed this week * 24 hours * 60 minutes * 60 seconds
	$current_day = date("j", $today); // Day of the month without leading zeros (1 to 31) of today
	$day_of_the_week = date("w", $today); // Numeric representation of the day of the week	0 (for Sunday) through 6 (for Saturday) of today
	//$timestamp_first_date_of_week=$today-(($day_of_the_week-1)*24*60*60); // timestamp of the monday of this week
	//$timestamp_last_date_of_week=$today+((7-$day_of_the_week)*24*60*60); // timestamp of the sunday of this week
	// we are loading all the calendar items of all the courses for today
	echo "<table class=\"data_table\">\n";
	// the forward and backwards url
	$backwardsURL = api_get_self()."?coursePath=".urlencode($course_path)."&amp;courseCode=".Security::remove_XSS($_GET['courseCode'])."&amp;action=view&amp;view=day&amp;day=".date("j", $previousday)."&amp;month=".date("n", $previousday)."&amp;year=".date("Y", $previousday);
	$forewardsURL = api_get_self()."?coursePath=".urlencode($course_path)."&amp;courseCode=".Security::remove_XSS($_GET['courseCode'])."&amp;action=view&amp;view=day&amp;day=".date("j", $nextday)."&amp;month=".date("n", $nextday)."&amp;year=".date("Y", $nextday);
	// The title row containing the day
	echo "<tr>\n", "<th width=\"10%\"><a href=\"", $backwardsURL, "\">&#171;</a></th>\n", "<th>";
	echo $DaysLong[$day_of_the_week]." ".date("j", $today)." ".$MonthsLong[date("n", $today) - 1]." ".date("Y", $today);
	echo "</th>";
	echo "<th width=\"10%\"><a href=\"", $forewardsURL, "\">&#187;</a></th>\n";
	echo "</tr>\n";
	// the rows for each half an hour
	for ($i = 10; $i < 48; $i ++)
	{
		if ($i % 2 == 0)
		{
			$class = "class=\"row_even\"";
		}
		else
		{
			$class = "class=\"row_odd\"";
		}
		echo "<tr $class>\n";
		echo "\t";
		if ($i % 2 == 0)
		{
			echo ("<td valign=\"top\" width=\"75\">". (($i) / 2)." ".get_lang("HourShort")." 00</td>\n");
		}
		else
		{
			echo ("<td valign=\"top\" width=\"75\">". ((($i) / 2) - (1 / 2))." ".get_lang("HourShort")." 30</td>\n");
		}
		echo "\t<td $class valign=\"top\" colspan=\"2\">\n";
		if (is_array($agendaitems[$i]))
		{
			foreach ($agendaitems[$i] as $key => $value)
			{
				echo $value;
			}
		}
		else
		{
			echo $agendaitems[$i];
		}
		echo "\t</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
}
/**
 *	Display the weekly view of the calendar
 */ 
function display_weekcalendar($agendaitems, $month, $year, $weekdaynames, $monthName)
{
	global $DaysShort,$course_path;
	global $MonthsLong;
	// timestamp of today
	$today = time();
	$day_of_the_week = date("w", $today);
	$thisday_of_the_week = date("w", $today);
	// the week number of the year
	$week_number = date("W", $today);
	$thisweek_number = $week_number;
	// if we moved to the next / previous week we have to recalculate the $today variable
	if ($_GET['week'])
	{
		$today = mktime(0, 0, 0, 1, 1, $year);
		$today = $today + (((int)$_GET['week']-1) * (7 * 24 * 60 * 60));
		$week_number = date("W", $today);
	}
	// calculating the start date of the week
	// the date of the monday of this week is the timestamp of today minus
	// number of days that have already passed this week * 24 hours * 60 minutes * 60 seconds
	$current_day = date("j", $today); // Day of the month without leading zeros (1 to 31) of today
	$day_of_the_week = date("w", $today); // Numeric representation of the day of the week	0 (for Sunday) through 6 (for Saturday) of today
	$timestamp_first_date_of_week = $today - (($day_of_the_week -1) * 24 * 60 * 60); // timestamp of the monday of this week
	$timestamp_last_date_of_week = $today + ((7 - $day_of_the_week) * 24 * 60 * 60); // timestamp of the sunday of this week
	$backwardsURL = api_get_self()."?coursePath=".urlencode($course_path)."&amp;courseCode=".Security::remove_XSS($_GET['courseCode'])."&amp;action=view&amp;view=week&amp;week=". ($week_number -1);
	$forewardsURL = api_get_self()."?coursePath=".urlencode($course_path)."&amp;courseCode=".Security::remove_XSS($_GET['courseCode'])."&amp;action=view&amp;view=week&amp;week=". ($week_number +1);
	echo "<table id=\"agenda_list\">\n";
	// The title row containing the the week information (week of the year (startdate of week - enddate of week)
	echo "<tr class=\"title\">\n";
	echo "<td width=\"10%\"><a href=\"", $backwardsURL, "\">&#171;</a></td>\n";
	echo "<td colspan=\"5\">".get_lang("Week")." ".$week_number;
	echo " (".$DaysShort['1']." ".date("j", $timestamp_first_date_of_week)." ".$MonthsLong[date("n", $timestamp_first_date_of_week) - 1]." ".date("Y", $timestamp_first_date_of_week)." - ".$DaysShort['0']." ".date("j", $timestamp_last_date_of_week)." ".$MonthsLong[date("n", $timestamp_last_date_of_week) - 1]." ".date("Y", $timestamp_last_date_of_week).')';
	echo "</td>";
	echo "<td width=\"10%\"><a href=\"", $forewardsURL, "\">&#187;</a></td>\n", "</tr>\n";
	// The second row containing the short names of the days of the week
	echo "<tr>\n";
	// this is the Day of the month without leading zeros (1 to 31) of the monday of this week
	$tmp_timestamp = $timestamp_first_date_of_week;
	for ($ii = 1; $ii < 8; $ii ++)
	{
		$is_today = ($ii == $thisday_of_the_week AND (!isset($_GET['week']) OR $_GET['week']==$thisweek_number));
		echo "\t<td class=\"weekdays\">";
		if ($is_today)
		{
			echo "<font color=#CC3300>";
		}
		echo $DaysShort[$ii % 7]." ".date("j", $tmp_timestamp)." ".$MonthsLong[date("n", $tmp_timestamp) - 1];
		if ($is_today)
		{
			echo "</font>";
		}
		echo "</td>\n";
		// we 24 hours * 60 minutes * 60 seconds to the $tmp_timestamp
		$array_tmp_timestamp[] = $tmp_timestamp;
		$tmp_timestamp = $tmp_timestamp + (24 * 60 * 60);
	}
	echo "</tr>\n";
	// the table cells containing all the entries for that day
	echo "<tr>\n";
	$counter = 0;
	foreach ($array_tmp_timestamp as $key => $value)
	{
		if ($counter < 5)
		{
			$class = "class=\"days_week\"";
		}
		else
		{
			$class = "class=\"days_weekend\"";
		}
		if ($counter == $thisday_of_the_week -1 AND (!isset($_GET['week']) OR $_GET['week']==$thisweek_number))
		{
			$class = "class=\"days_today\"";
		}

		echo "\t<td ".$class.">";
		echo "<span class=\"agendaitem\">".$agendaitems[date('j', $value)]."&nbsp;</span> ";
		echo "</td>\n";
		$counter ++;
	}
	echo "</tr>\n";
	echo "</table>\n";
}
/**
 * Show the monthcalender of the given month
 */
function get_day_agendaitems($courses_dbs, $month, $year, $day)
{
	global $_user;
	global $_configuration;
	global $setting_agenda_link;

	$items = array ();

	// get agenda-items for every course
	//$query=api_sql_query($sql_select_courses);
	foreach ($courses_dbs as $key => $array_course_info)
	{
		//databases of the courses
		$TABLEAGENDA = Database :: get_main_table(TABLE_MAIN_SYSTEM_CALENDAR);
		//$TABLE_ITEMPROPERTY = Database :: get_course_table(TABLE_ITEM_PROPERTY, $array_course_info['db']);

		// getting all the groups of the user for the current course
		//$group_memberships = GroupManager :: get_group_ids($array_course_info['db'], $_user['user_id']);
		// if the user is administrator of that course we show all the agenda items
		if ($array_course_info['status'] == '1')
		{
			//echo "course admin";
			$sqlquery = "SELECT DISTINCT *
										FROM ".$TABLEAGENDA." 
										WHERE 
										DAYOFMONTH(start_date)='".$day."' AND MONTH(start_date)='".$month."' AND YEAR(start_date)='".$year."'
										GROUP BY agenda.id
										ORDER BY start_date ";
		}
		// if the user is not an administrator of that course
		else
		{
			//echo "GEEN course admin";
			if (is_array($group_memberships) && count($group_memberships)>0)
			{
				$sqlquery = "SELECT
													agenda.*
													FROM ".$TABLEAGENDA." 
													WHERE 
													DAYOFMONTH(start_date)='".$day."' AND MONTH(start_date)='".$month."' AND YEAR(start_date)='".$year."'
													ORDER BY start_date ";
			}
			else
			{
				$sqlquery = "SELECT
													agenda.*
													FROM ".$TABLEAGENDA." 
													WHERE 
													DAYOFMONTH(start_date)='".$day."' AND MONTH(start_date)='".$month."' AND YEAR(start_date)='".$year."'
													ORDER BY start_date ";
			}
		}
		//$sqlquery = "SELECT * FROM $agendadb WHERE DAYOFMONTH(day)='$day' AND month(day)='$month' AND year(day)='$year'";
		//echo "abc";
		//echo $sqlquery;
		$result = api_sql_query($sqlquery, __FILE__, __LINE__);
		//echo Database::num_rows($result);
		while ($item = Database::fetch_array($result))
		{
			// in the display_daycalendar function we use $i (ranging from 0 to 47) for each halfhour
			// we want to know for each agenda item for this day to wich halfhour it must be assigned
			list ($datepart, $timepart) = split(" ", $item['start_date']);
			list ($year, $month, $day) = explode("-", $datepart);
			list ($hours, $minutes, $seconds) = explode(":", $timepart);

			$halfhour = 2 * $hours;
			if ($minutes >= '30')
			{
				$halfhour = $halfhour +1;
			}

			if ($setting_agenda_link == 'coursecode')
			{
				$title=$array_course_info['title'];
				$agenda_link = substr($title, 0, 14);
			}
			else 
			{
				$agenda_link = Display::return_icon('course_home.gif');
			}			
			
			$URL = $_configuration['root_web'].$mycours["dir"]."/";
			$URL = $_configuration['root_web'].'main/admin/agenda.php?cidReq='."&amp;day=$day&amp;month=$month&amp;year=$year#$day"; // RH  //Patrick Cool: to highlight the relevant agenda item
			$items[$halfhour][] .= "<i>".$hours.":".$minutes."</i> <a href=\"$URL\" title=\"".$array_course_info['name']."\">".$agenda_link."</a>  ".$item['title']."<br />";
		}
	}
	// sorting by hour for every day
	$agendaitems = array();
	while (list($agendaday, $tmpitems) = each($items))
	{
		sort($tmpitems);
		while (list($key,$val) = each($tmpitems))
		{
			$agendaitems[$agendaday].=$val;
		}
	}
	$agendaitems = $items;
	//print_r($agendaitems);
	return $agendaitems;
}

/**
 * Return agenda items of the week
 */
function get_week_agendaitems($courses_dbs, $month, $year, $week = '')
{
	$TABLEAGENDA = Database :: get_main_table(TABLE_MAIN_SYSTEM_CALENDAR);
	global $_user;
	global $_configuration;
	global $setting_agenda_link;

	$items = array ();
	// The default value of the week
	if ($week == '')
	{
		$week_number = date("W", time());
	}
	else
	{
		$week_number = $week;
	}
	$start_end = calculate_start_end_of_week($week_number, $year);
	$start_filter = $start_end['start']['year']."-".$start_end['start']['month']."-".$start_end['start']['day'];
	$end_filter = $start_end['end']['year']."-".$start_end['end']['month']."-".$start_end['end']['day'];
	// get agenda-items for every course
	foreach ($courses_dbs as $key => $array_course_info)
	{
		//databases of the courses
		$TABLEAGENDA = Database :: get_main_table(TABLE_MAIN_SYSTEM_CALENDAR);
		//$TABLE_ITEMPROPERTY = Database :: get_course_table(TABLE_ITEM_PROPERTY, $array_course_info["db"]);

		// getting all the groups of the user for the current course
	//	$group_memberships = GroupManager :: get_group_ids($array_course_info["db"], $_user['user_id']);

		// if the user is administrator of that course we show all the agenda items
		//if ($array_course_info['status'] == '1')
		//{
			//echo "course admin";
			$sqlquery = "SELECT DISTINCT * FROM ".$TABLEAGENDA." ORDER BY start_date";
		//}
		// if the user is not an administrator of that course
		//else
		//{
			//echo "GEEN course admin";
/*			if (is_array($group_memberships) && count($group_memberships)>0)
			{
				$sqlquery = "SELECT *  FROM ".$TABLEAGENDA." ORDER BY a.start_date";
			}
			else*/
			//{
			//	$sqlquery = "SELECT * FROM ".$TABLEAGENDA." ORDER BY a.start_date";
			//}
		//}
		//echo "<pre>".$sqlquery."</pre>";
		// $sqlquery = "SELECT * FROM $agendadb WHERE (DAYOFMONTH(day)>='$start_day' AND DAYOFMONTH(day)<='$end_day')
		//				AND (MONTH(day)>='$start_month' AND MONTH(day)<='$end_month')
		//				AND (YEAR(day)>='$start_year' AND YEAR(day)<='$end_year')";
		$result = api_sql_query($sqlquery, __FILE__, __LINE__);
		while ($item = Database::fetch_array($result))
		{
			$agendaday = date("j",strtotime($item['start_date']));
			$time= date("H:i",strtotime($item['start_date']));

			if ($setting_agenda_link == 'coursecode')
			{				
				$title=$array_course_info['title'];
				$agenda_link = substr($title, 0, 14);
			}
			else 
			{
				$agenda_link = Display::return_icon('course_home.gif');
			}			
			
			$URL = $_configuration['root_web']."main/admin/agenda.php?cidReq=".urlencode($array_course_info["code"])."&amp;day=$agendaday&amp;month=$month&amp;year=$year#$agendaday"; // RH  //Patrick Cool: to highlight the relevant agenda item
			$items[$agendaday][$item['start_time']] .= "<i>$time</i> <a href=\"$URL\" title=\"".$array_course_info["name"]."\">".$agenda_link."</a>  ".$item['title']."<br />";
		}
	}
	// sorting by hour for every day
	$agendaitems = array ();
	while (list ($agendaday, $tmpitems) = each($items))
	{
		sort($tmpitems);
		while (list ($key, $val) = each($tmpitems))
		{
			$agendaitems[$agendaday] .= $val;
		}
	}
	//print_r($agendaitems);
	return $agendaitems;
}
/**
 * Get repeated events of a course between two dates (timespan of a day).
 * Returns an array containing the events
 * @param   string  Course info array (as returned by api_get_course_info())
 * @param	int		UNIX timestamp of span start. Defaults 0, later transformed into today's start
 * @param	int		UNIX timestamp. Defaults to 0, later transformed into today's end
 * @param   array   A set of parameters to alter the SQL query
 * @return	array	[int] => [course_id,parent_event_id,start_date,end_date,title,description]
 */
function get_repeated_events_day_view($course_info,$start=0,$end=0,$params)
{
	$events = array();
	//initialise all values
	$y=0;
	$m=0;
	$d=0;
    //block $end if higher than 2038 -- PHP doesn't go past that
    if($end>2145934800){$end = 2145934800;}
	if($start == 0 or $end == 0)
	{	
		$y=date('Y');
		$m=date('m');
		$d=date('j');
	}
	if($start==0)
	{
		$start = mktime(0,0,0,$m,$d,$y);
	}
	$db_start = date('Y-m-d H:i:s',$start);
	if($end==0)
	{
		$end = mktime(23,59,59,$m,$d,$y);
	}
	//$db_end = date('Y-m-d H:i:s',$end);
	
	$t_cal = Database::get_main_table(TABLE_MAIN_SYSTEM_CALENDAR,$course_info['dbName']);
	//$t_cal_repeat = Database::get_course_table(TABLE_AGENDA_REPEAT,$course_info['dbName']);
    $t_ip = Database::get_course_table(TABLE_ITEM_PROPERTY,$course_info['dbName']);
	$sql = "SELECT c.id, c.title, c.content, " .
			" UNIX_TIMESTAMP(c.start_date) as orig_start, UNIX_TIMESTAMP(c.end_date) as orig_end, " .
			" cr.cal_type, cr.cal_end " .
			" FROM $t_cal c, $t_cal_repeat cr, $t_ip as item_property " .
			" WHERE cr.cal_end >= $start " .
			" AND cr.cal_id = c.id " .
            " AND item_property.ref = c.id ".
            " AND item_property.tool = '".TOOL_CALENDAR_EVENT."' ".
			" AND c.start_date <= '$db_start' "
            .(!empty($params['conditions'])?$params['conditions']:'')
            .(!empty($params['groupby'])?' GROUP BY '.$params['groupby']:'')
            .(!empty($params['orderby'])?' ORDER BY '.$params['orderby']:'');
	$res = api_sql_query($sql,__FILE__,__LINE__);
	if(Database::num_rows($res)>0)
	{
		while($row = Database::fetch_array($res))
		{
			$orig_start = $row['orig_start'];
			$orig_end = $row['orig_end'];
			$repeat_type = $row['cal_type'];
			switch($repeat_type)
			{
				case 'daily':
					//we are in the daily view, so if this element is repeated daily and
					//the repetition is still active today (which is a condition of the SQL query)
					//then the event happens today. Just build today's timestamp for start and end
					$time_orig_h = date('H',$orig_start);
					$time_orig_m = date('i',$orig_start);
					$time_orig_s = date('s',$orig_start);
					$int_time = (($time_orig_h*60)+$time_orig_m)*60+$time_orig_s; //time in seconds since 00:00:00
					$span = $orig_end - $orig_start; //total seconds between start and stop of original event
					$current_start =$start + $int_time; //unixtimestamp start of today's event 
					$current_stop = $start+$int_time+$span; //unixtimestamp stop of today's event
					$events[] = array($course_info['id'],$row['id'],$current_start,$current_stop,$row['title'],$row['content']);
					break;
				case 'weekly':
					$time_orig = date('Y/n/W/j/N/G/i/s',$orig_start);
					list($y_orig,$m_orig,$w_orig,$d_orig,$dw_orig,$h_orig,$n_orig,$s_orig) = split('/',$time_orig);
					$time_now = date('Y/n/W/j/N/G/i/s',$end);
					list($y_now,$m_now,$w_now,$d_now,$dw_now,$h_now,$n_now,$s_now) = split('/',$time_now);
					if((($y_now>$y_orig) OR (($y_now == $y_orig) && ($w_now>$w_orig))) && ($dw_orig == $dw_now))
					{ //if the event is after the original (at least one week) and the day of the week is the same
					  $time_orig_end = date('Y/n/W/j/N/G/i/s',$orig_end);
					  list($y_orig_e,$m_orig_e,$w_orig_e,$d_orig_e,$dw_orig_e,$h_orig_e,$n_orig_e,$s_orig_e) = split('/',$time_orig_end);
					  $events[] = array($course_info['id'],$row['id'],mktime($h_orig,$n_orig,$s_orig,$m_now,$d_orig,$y_now),mktime($h_orig_e,$n_orig_e,$s_orig_e,$m_now,$d_orig_e,$y_now),$row['title'],$row['content']);
					}
					break;
				case 'monthlyByDate':
					$time_orig = date('Y/n/j/G/i/s',$orig_start);
					list($y_orig,$m_orig,$d_orig,$h_orig,$n_orig,$s_orig) = split('/',$time_orig);
					$time_now = date('Y/n/j/G/i/s',$end);
					list($y_now,$m_now,$d_now,$h_now,$n_now,$s_now) = split('/',$time_now);
					if((($y_now>$y_orig) OR (($y_now == $y_orig) && ($m_now>$m_orig))) && ($d_orig == $d_now))
					{
					  $time_orig_end = date('Y/n/j/G/i/s',$orig_end);
					  list($y_orig_e,$m_orig_e,$d_orig_e,$h_orig_e,$n_orig_e,$s_orig_e) = split('/',$time_orig_end);
					  $events[] = array($course_info['id'],$row['id'],mktime($h_orig,$n_orig,$s_orig,$m_now,$d_orig,$y_now),mktime($h_orig_e,$n_orig_e,$s_orig_e,$m_now,$d_orig_e,$y_now),$row['title'],$row['content']);
					}
					break;
				case 'monthlyByDayR':
					//not implemented yet
					break;
				case 'monthlyByDay':
					//not implemented yet
					break;
				case 'yearly':
					$time_orig = date('Y/n/j/z/G/i/s',$orig_start);
					list($y_orig,$m_orig,$d_orig,$dy_orig,$h_orig,$n_orig,$s_orig) = split('/',$time_orig);
					$time_now = date('Y/n/j/z/G/i/s',$end);
					list($y_now,$m_now,$d_now,$dy_now,$h_now,$n_now,$s_now) = split('/',$time_now);
					if(($y_now>$y_orig) && ($dy_orig == $dy_now))
					{
					  $time_orig_end = date('Y/n/j/G/i/s',$orig_end);
					  list($y_orig_e,$m_orig_e,$d_orig_e,$dy_orig_e,$h_orig_e,$n_orig_e,$s_orig_e) = split('/',$time_orig_end);
					  $events[] = array($course_info['id'],$row['id'],mktime($h_orig,$n_orig,$s_orig,$m_now,$d_orig,$y_now),mktime($h_orig_e,$n_orig_e,$s_orig_e,$m_now,$d_orig_e,$y_now),$row['title'],$row['content']);
					}
					break;
				default:
					break;
			}
		}
	}
	return $events;
}
/**
 * Get repeated events of a course between two dates (timespan of a week).
 * Returns an array containing the events
 * @param	string	Course info array (as returned by api_get_course_info())
 * @param	int		UNIX timestamp of span start. Defaults 0, later transformed into today's start
 * @param	int		UNIX timestamp. Defaults to 0, later transformed into today's end
 * @param   array   A set of parameters to alter the SQL query
 * @return	array	[int] => [course_id,parent_event_id,start_date,end_date,title,description]
 */
function get_repeated_events_week_view($course_info,$start=0,$end=0,$params)
{
	$events = array();
    //block $end if higher than 2038 -- PHP doesn't go past that
    if($end>2145934800){$end = 2145934800;}
	//initialise all values
	$y=0;
	$m=0;
	$d=0;
	if($start == 0 or $end == 0)
	{
		$time = time();
		$dw = date('w',$time);
		$week_start = $time - (($dw-1)*86400);
		$y = date('Y',$week_start);
		$m = date('m',$week_start);
		$d = date('j',$week_start);
		$w = date('W',$week_start);
	}
	if($start==0)
	{
		$start = mktime(0,0,0,$m,$d,$y);
	}
	$db_start = date('Y-m-d H:i:s',$start);
	if($end==0)
	{
		$end = $start+(86400*7)-1; //start of week, more 7 days, minus 1 second to get back to the previoyus day
	}
	//$db_end = date('Y-m-d H:i:s',$end);
	
	$t_cal = Database::get_main_table(TABLE_MAIN_SYSTEM_CALENDAR);
	//$t_cal_repeat = Database::get_course_table(TABLE_AGENDA_REPEAT,$course_info['dbName']);
    //$t_ip = Database::get_course_table(TABLE_ITEM_PROPERTY,$course_info['dbName']);
    $sql = "SELECT c.id, c.title, c.content " .
            " UNIX_TIMESTAMP(c.start_date) as orig_start, UNIX_TIMESTAMP(c.end_date) as orig_end, " .
            " FROM". $t_cal ."
             WHERE c.start_date <= '$db_start' "
            .(!empty($params['conditions'])?$params['conditions']:'')
            .(!empty($params['groupby'])?' GROUP BY '.$params['groupby']:'')
            .(!empty($params['orderby'])?' ORDER BY '.$params['orderby']:'');
	$res = api_sql_query($sql,__FILE__,__LINE__);
	if(Database::num_rows($res)>0)
	{
		while($row = Database::fetch_array($res))
		{
			$orig_start = $row['orig_start'];
			$orig_end = $row['orig_end'];
			$repeat_type = $row['cal_type'];
			switch($repeat_type)
			{
				case 'daily':
					$time_orig_h = date('H',$orig_start);
					$time_orig_m = date('i',$orig_start);
					$time_orig_s = date('s',$orig_start);
					$int_time = (($time_orig_h*60)+$time_orig_m)*60+$time_orig_s; //time in seconds since 00:00:00
					$span = $orig_end - $orig_start; //total seconds between start and stop of original event
					for($i=0;$i<7;$i++)
					{
						$current_start = $start + ($i*86400) + $int_time; //unixtimestamp start of today's event 
						$current_stop = $start + ($i*86400) + $int_time + $span; //unixtimestamp stop of today's event
						$events[] = array($course_info['id'],$row['id'],$current_start,$current_stop,$row['title'],$row['content']);
					}
					break;
				case 'weekly':
					$time_orig = date('Y/n/W/j/N/G/i/s',$orig_start);
					list($y_orig,$m_orig,$w_orig,$d_orig,$dw_orig,$h_orig,$n_orig,$s_orig) = split('/',$time_orig);
					$time_now = date('Y/n/W/j/N/G/i/s',$end);
					list($y_now,$m_now,$w_now,$d_now,$dw_now,$h_now,$n_now,$s_now) = split('/',$time_now);
					if((($y_now>$y_orig) OR (($y_now == $y_orig) && ($w_now>$w_orig))))
					{ //if the event is after the original (at least one week) and the day of the week is the same
					  $time_orig_end = date('Y/n/W/j/N/G/i/s',$orig_end);
					  list($y_orig_e,$m_orig_e,$w_orig_e,$d_orig_e,$dw_orig_e,$h_orig_e,$n_orig_e,$s_orig_e) = split('/',$time_orig_end);
					  $events[] = array($course_info['id'],$row['id'],mktime($h_orig,$n_orig,$s_orig,$m_now,$d_orig,$y_now),mktime($h_orig_e,$n_orig_e,$s_orig_e,$m_now,$d_orig_e,$y_now),$row['title'],$row['content']);
					}
					break;
				case 'monthlyByDate':
					$time_orig = date('Y/n/W/j/G/i/s',$orig_start);
					list($y_orig,$m_orig,$w_orig,$d_orig,$h_orig,$n_orig,$s_orig) = split('/',$time_orig);
					$time_now = date('Y/n/W/j/G/i/s',$end);
					list($y_now,$m_now,$w_now,$d_now,$h_now,$n_now,$s_now) = split('/',$time_now);
					$event_repetition_time = mktime($h_orig,$n_orig,$s_orig,$m_now,$d_orig,$y_now); 
					if((($y_now>$y_orig) OR (($y_now == $y_orig) && ($m_now>$m_orig))) && ($start<$event_repetition_time && $event_repetition_time<$end))
					{ //if the event is after the original (at least one month) and the original event's day is between the first day of the week and the last day of the week
					  $time_orig_end = date('Y/n/j/G/i/s',$orig_end);
					  list($y_orig_e,$m_orig_e,$d_orig_e,$h_orig_e,$n_orig_e,$s_orig_e) = split('/',$time_orig_end);
					  $events[] = array($course_info['id'],$row['id'],mktime($h_orig,$n_orig,$s_orig,$m_now,$d_orig,$y_now),mktime($h_orig_e,$n_orig_e,$s_orig_e,$m_now,$d_orig_e,$y_now),$row['title'],$row['content']);
					}
					break;
				case 'monthlyByDayR':
					//not implemented yet
					break;
				case 'monthlyByDay':
					//not implemented yet
					break;
				case 'yearly':
					$time_orig = date('Y/n/j/z/G/i/s',$orig_start);
					list($y_orig,$m_orig,$d_orig,$dy_orig,$h_orig,$n_orig,$s_orig) = split('/',$time_orig);
					$time_now = date('Y/n/j/z/G/i/s',$end);
					list($y_now,$m_now,$d_now,$dy_now,$h_now,$n_now,$s_now) = split('/',$time_now);
					$event_repetition_time = mktime($h_orig,$n_orig,$s_orig,$m_orig,$d_orig,$y_now); 
					if((($y_now>$y_orig) && ($start<$event_repetition_time && $event_repetition_time<$end)))
					{
					  $time_orig_end = date('Y/n/j/G/i/s',$orig_end);
					  list($y_orig_e,$m_orig_e,$d_orig_e,$dy_orig_e,$h_orig_e,$n_orig_e,$s_orig_e) = split('/',$time_orig_end);
					  $events[] = array($course_info['id'],$row['id'],mktime($h_orig,$n_orig,$s_orig,$m_now,$d_orig,$y_now),mktime($h_orig_e,$n_orig_e,$s_orig_e,$m_now,$d_orig_e,$y_now),$row['title'],$row['content']);
					}
					break;
				default:
					break;
			}
		}
	}
	return $events;
}
/**
 * Get repeated events of a course between two dates (timespan of a month).
 * Returns an array containing the events
 * @param   string  Course info array (as returned by api_get_course_info())
 * @param	int		UNIX timestamp of span start. Defaults 0, later transformed into today's start
 * @param	int		UNIX timestamp. Defaults to 0, later transformed into today's end
 * @param   array   A set of parameters to alter the SQL query
 * @return	array	[int] => [course_id,parent_event_id,start_date,end_date,title,description]
 */
function get_repeated_events_month_view($course_info,$start=0,$end=0,$params)
{
	$events = array();
    //block $end if higher than 2038 -- PHP doesn't go past that
    if($end>2145934800){$end = 2145934800;}
	//initialise all values
	$y=0;
	$m=0;
	$d=0;
	if($start == 0 or $end == 0)
	{
		$time = time();
		$y = date('Y');
		$m = date('m');
	}
	if($start==0)
	{
		$start = mktime(0,0,0,$m,1,$y);
	}
	$db_start = date('Y-m-d H:i:s',$start);
	if($end==0)
	{
		if($m==12)
		{
			$end = mktime(0,0,0,1,1,$y+1)-1; //start of next month, minus 1 second to get back to the previoyus day
		}
		else
		{
			$end = mktime(0,0,0,$m+1,1,$y)-1;
		}
	}
	//$db_end = date('Y-m-d H:i:s',$end);
	
	$t_cal = Database::get_main_table(TABLE_MAIN_SYSTEM_CALENDAR);
	//$t_cal_repeat = Database::get_course_table(TABLE_AGENDA_REPEAT,$course_info['dbName']);
    //$t_ip = Database::get_course_table(TABLE_ITEM_PROPERTY,$course_info['dbName']);
    $sql = "SELECT c.id, c.title, c.content, " .
            " UNIX_TIMESTAMP(c.start_date) as orig_start, UNIX_TIMESTAMP(c.end_date) as orig_end, " .
            " cr.cal_type, cr.cal_end " .
            " FROM $t_cal c, $t_cal_repeat cr, $t_ip as item_property " .
            " WHERE cr.cal_end >= $start " .
            " AND cr.cal_id = c.id " .
            " AND item_property.ref = c.id ".
            " AND item_property.tool = '".TOOL_CALENDAR_EVENT."' ".
            " AND c.start_date <= '$db_start' "
            .(!empty($params['conditions'])?$params['conditions']:'')
            .(!empty($params['groupby'])?' GROUP BY '.$params['groupby']:'')
            .(!empty($params['orderby'])?' ORDER BY '.$params['orderby']:'');
	$res = api_sql_query($sql,__FILE__,__LINE__);
	if(Database::num_rows($res)>0)
	{
		while($row = Database::fetch_array($res))
		{
			$orig_start = $row['orig_start'];
			$orig_end = $row['orig_end'];
			$repeat_type = $row['cal_type'];
			switch($repeat_type)
			{
				case 'daily':
					$time_orig_h = date('H',$orig_start);
					$time_orig_m = date('i',$orig_start);
					$time_orig_s = date('s',$orig_start);
					$month_last_day = date('d',$end);
					$int_time = (($time_orig_h*60)+$time_orig_m)*60+$time_orig_s; //time in seconds since 00:00:00
					$span = $orig_end - $orig_start; //total seconds between start and stop of original event
					for($i=0;$i<$month_last_day;$i++)
					{
						$current_start = $start + ($i*86400) + $int_time; //unixtimestamp start of today's event 
						$current_stop = $start + ($i*86400) + $int_time + $span; //unixtimestamp stop of today's event
						$events[] = array($course_info['id'],$row['id'],$current_start,$current_stop,$row['title'],$row['content']);
					}
					break;
				case 'weekly':
					//A weekly repeated event is very difficult to catch in a month view,
					//because weeks start before or at the same time as the first day of the month
					//The same can be said for the end of the month.
					// The idea is thus to get all possible events by enlarging the scope of
					// the month to get complete weeks covering the complete month, and then take out
					// the events that start before the 1st ($start) or after the last day of the month ($end) 
					$time_orig = date('Y/n/W/j/N/G/i/s',$orig_start);
					list($y_orig,$m_orig,$w_orig,$d_orig,$dw_orig,$h_orig,$n_orig,$s_orig) = split('/',$time_orig);
                    $time_orig_end = date('Y/n/W/j/N/G/i/s',$orig_end);
                    list($y_orig_e,$m_orig_e,$w_orig_e,$d_orig_e,$dw_orig_e,$h_orig_e,$n_orig_e,$s_orig_e) = split('/',$time_orig_end);

					$time_now = date('Y/n/W/j/N/G/i/s',$end);
					list($y_now,$m_now,$w_now,$d_now,$dw_now,$h_now,$n_now,$s_now) = split('/',$time_now);

					$month_first_week = date('W',$start);
					$month_last_week = date('W',$end);

					if(($y_now>$y_orig) OR (($y_now == $y_orig) && ($w_now>$w_orig)))
					{ //if the event is after the original (at least one week) and the day of the week is the same
						for($i=$month_first_week;$i<=$month_last_week;$i++)
						{
						  //the "day of the week" of repetition is the same as the $dw_orig,
                          //so to get the "day of the month" from the "day of the week", we have
                          //to get the first "day of the week" for this week and add the number
                          //of days (in seconds) to reach the $dw_orig
                          //example: the first week spans between the 28th of April (Monday) to the
                          // 4th of May (Sunday). The event occurs on the 2nd day of each week.
                          // This means the event occurs on 29/4, 6/5, 13/5, 20/5 and 27/5.
                          // We want to get all of these, and then reject 29/4 because it is out
                          // of the month itself.
                          
                          //First, to get the start time of the first day of the month view (even if
                          // the day is from the past month), we get the month start date (1/5) and
                          // see which day of the week it is, and subtract the number of days necessary
                          // to get back to the first day of the week.
                          $month_first_day_weekday = date('N',$start);
                          $first_week_start = $start - (($month_first_day_weekday-1)*86400); 
                          
                          //Second, we add the week day of the original event, so that we have an
                          // absolute time that represents the first repetition of the event in
                          // our 4- or 5-weeks timespan
                          $first_event_repeat_start = $first_week_start + (($dw_orig-1)*86400) + ($h_orig*3600) + ($n_orig*60) + $s_orig;

                          //Third, we start looping through the repetitions and see if they are between
                          // $start and $end
					      for($i = $first_event_repeat_start; $i<=$end; $i+=604800)
                          {
                          	if($start<$i && $i<$end)
                            {
                               list($y_repeat,$m_repeat,$d_repeat,$h_repeat,$n_repeat,$s_repeat) = split('/',date('Y/m/j/H/i/s',$i));
                               $events[] = array($course_info['id'],$row['id'],mktime($h_orig,$n_orig,$s_orig,$m_now,$d_orig,$y_now),mktime($h_orig_e,$n_orig_e,$s_orig_e,$m_now,$d_orig_e,$y_now),$row['title'],$row['content']);
                            }
                          }
						}
					}
					break;
				case 'monthlyByDate':
					$time_orig = date('Y/n/W/j/G/i/s',$orig_start);
					list($y_orig,$m_orig,$w_orig,$d_orig,$h_orig,$n_orig,$s_orig) = split('/',$time_orig);
					$time_now = date('Y/n/W/j/G/i/s',$end);
					list($y_now,$m_now,$w_now,$d_now,$h_now,$n_now,$s_now) = split('/',$time_now);
					$event_repetition_time = mktime($h_orig,$n_orig,$s_orig,$m_now,$d_orig,$y_now); 
					if(($y_now>$y_orig) OR (($y_now == $y_orig) && ($m_now>$m_orig)))
					{ //if the event is after the original (at least one month) and the original event's day is between the first day of the week and the last day of the week
					  $time_orig_end = date('Y/n/j/G/i/s',$orig_end);
					  list($y_orig_e,$m_orig_e,$d_orig_e,$h_orig_e,$n_orig_e,$s_orig_e) = split('/',$time_orig_end);
					  $events[] = array($course_info['id'],$row['id'],mktime($h_orig,$n_orig,$s_orig,$m_now,$d_orig,$y_now),mktime($h_orig_e,$n_orig_e,$s_orig_e,$m_now,$d_orig_e,$y_now),$row['title'],$row['content']);
					}
					break;
				case 'monthlyByDayR':
					//not implemented yet
					break;
				case 'monthlyByDay':
					//not implemented yet
					break;
				case 'yearly':
					$time_orig = date('Y/n/j/z/G/i/s',$orig_start);
					list($y_orig,$m_orig,$d_orig,$dy_orig,$h_orig,$n_orig,$s_orig) = split('/',$time_orig);
					$time_now = date('Y/n/j/z/G/i/s',$end);
					list($y_now,$m_now,$d_now,$dy_now,$h_now,$n_now,$s_now) = split('/',$time_now);
					$event_repetition_time = mktime($h_orig,$n_orig,$s_orig,$m_orig,$d_orig,$y_now); 
					if((($y_now>$y_orig) && ($start<$event_repetition_time && $event_repetition_time<$end)))
					{
					  $time_orig_end = date('Y/n/j/G/i/s',$orig_end);
					  list($y_orig_e,$m_orig_e,$d_orig_e,$dy_orig_e,$h_orig_e,$n_orig_e,$s_orig_e) = split('/',$time_orig_end);
					  $events[] = array($course_info['id'],$row['id'],mktime($h_orig,$n_orig,$s_orig,$m_now,$d_orig,$y_now),mktime($h_orig_e,$n_orig_e,$s_orig_e,$m_now,$d_orig_e,$y_now),$row['title'],$row['content']);
					}
					break;
				default:
					break;
			}
		}
	}
	return $events;
}
/**
 * Get repeated events of a course between two dates (1 year timespan). Used for the list display.
 * This is virtually unlimited but by default it shortens to 100 years from now (even a birthday shouldn't be useful more than this time - except for turtles)
 * Returns an array containing the events
 * @param   string  Course info array (as returned by api_get_course_info())
 * @param   int     UNIX timestamp of span start. Defaults 0, later transformed into today's start
 * @param   int     UNIX timestamp. Defaults to 0, later transformed into today's end
 * @param   array   A set of parameters to alter the SQL query
 * @return  array   [int] => [course_id,parent_event_id,start_date,end_date,title,description]
 */
function get_repeated_events_list_view($course_info,$start=0,$end=0,$params)
{
    $events = array();
    //block $end if higher than 2038 -- PHP doesn't go past that
    if($end>2145934800){$end = 2145934800;}
    //initialise all values
    $y=0;
    $m=0;
    $d=0;
    if(empty($start) or empty($end))
    {
        $time = time();
        $y = date('Y');
        $m = date('m');
    }
    if(empty($start))
    {
        $start = mktime(0, 0, 0, $m, 1, $y);
    }
    $db_start = date('Y-m-d H:i:s', $start);
    if(empty($end))
    {
        $end = mktime(0, 0, 0, 1, 1, 2037);
    }
    //$db_end = date('Y-m-d H:i:s',$end);
    
    $t_cal = Database::get_main_table(TABLE_MAIN_SYSTEM_CALENDAR,$course_info['dbName']);
    //$t_cal_repeat = Database::get_course_table(TABLE_AGENDA_REPEAT,$course_info['dbName']);
    //$t_ip = Database::get_course_table(TABLE_ITEM_PROPERTY,$course_info['dbName']);
    $sql = "SELECT c.id, c.title, c.content, " .
            " UNIX_TIMESTAMP(c.start_date) as orig_start, UNIX_TIMESTAMP(c.end_date) as orig_end, " .
            " cr.cal_type, cr.cal_end " .
            " FROM $t_cal c, $t_cal_repeat cr, $t_ip as item_property " .
            " WHERE cr.cal_end >= $start " .
            " AND cr.cal_id = c.id " .
            " AND item_property.ref = c.id ".
            " AND item_property.tool = '".TOOL_CALENDAR_EVENT."' ".
            " AND c.start_date <= '$db_start' "
            .(!empty($params['conditions'])?$params['conditions']:'')
            .(!empty($params['groupby'])?' GROUP BY '.$params['groupby']:'')
            .(!empty($params['orderby'])?' ORDER BY '.$params['orderby']:'');
    $res = api_sql_query($sql,__FILE__,__LINE__);
    if(Database::num_rows($res)>0)
    {
        while($row = Database::fetch_array($res))
        {
            $orig_start = $row['orig_start'];
            $orig_end = $row['orig_end'];
            $repeat_type = $row['cal_type'];
            $repeat_end = $row['cal_end'];
            switch($repeat_type)
            {
                case 'daily':
                    $time_orig_h = date('H',$orig_start);
                    $time_orig_m = date('i',$orig_start);
                    $time_orig_s = date('s',$orig_start);
                    $span = $orig_end - $orig_start; //total seconds between start and stop of original event
                    for($i=$orig_start+86400;($i<$end && $i<=$repeat_end);$i+=86400)
                    {
                        $current_start = $i; //unixtimestamp start of today's event 
                        $current_stop = $i + $span; //unixtimestamp stop of today's event
                        $events[] = array($course_info['id'],$row['id'],$current_start,$current_stop,$row['title'],$row['content']);
                    }
                    break;
                case 'weekly':
                    //A weekly repeated event is very difficult to catch in a month view,
                    // because weeks start before or at the same time as the first day of the month
                    //The same can be said for the end of the month.
                    // The idea is thus to get all possible events by enlarging the scope of
                    // the month to get complete weeks covering the complete month, and then take out
                    // the events that start before the 1st ($start) or after the last day of the month ($end) 
                    $time_orig = date('Y/n/W/j/N/G/i/s',$orig_start);
                    list($y_orig,$m_orig,$w_orig,$d_orig,$dw_orig,$h_orig,$n_orig,$s_orig) = split('/',$time_orig);
                    $time_orig_end = date('Y/n/W/j/N/G/i/s',$orig_end);
                    list($y_orig_e,$m_orig_e,$w_orig_e,$d_orig_e,$dw_orig_e,$h_orig_e,$n_orig_e,$s_orig_e) = split('/',$time_orig_end);

                    $time_now = date('Y/n/W/j/N/G/i/s',$end);
                    list($y_now,$m_now,$w_now,$d_now,$dw_now,$h_now,$n_now,$s_now) = split('/',$time_now);
                    if($w_now==52)
                    {
                        ++$y_now;
                        $w_now=1;	
                    }
                    else
                    {
                        ++$w_now;
                    }
                    $month_first_week = date('W',$start);
                    $total_weeks = ((date('Y',$end)-$y_orig)-1)*52;
                    $month_last_week = $month_first_week + $total_weeks;

                    if(($y_now>$y_orig) OR (($y_now == $y_orig) && ($w_now>$w_orig)))
                    { //if the event is after the original (at least one week) and the day of the week is the same
                        //for($i=$month_first_week;($i<=$month_last_week && $i<1000);$i++)
                        //{
                        	
                        
                          /*
                           The "day of the week" of repetition is the same as the $dw_orig,
                           so to get the "day of the month" from the "day of the week", we have
                           to get the first "day of the week" for this week and add the number
                           of days (in seconds) to reach the $dw_orig
                          example: the first week spans between the 28th of April (Monday) to the
                           4th of May (Sunday). The event occurs on the 2nd day of each week.
                           This means the event occurs on 29/4, 6/5, 13/5, 20/5 and 27/5.
                           We want to get all of these, and then reject 29/4 because it is out
                           of the month itself.
                          First, to get the start time of the first day of the month view (even if
                           the day is from the past month), we get the month start date (1/5) and
                           see which day of the week it is, and subtract the number of days necessary
                           to get back to the first day of the week.
                          */
                          $month_first_day_weekday = date('N',$start);
                          $first_week_start = $start - (($month_first_day_weekday-1)*86400); 
                          
                          //Second, we add the week day of the original event, so that we have an
                          // absolute time that represents the first repetition of the event in
                          // our 4- or 5-weeks timespan
                          $first_event_repeat_start = $first_week_start + (($dw_orig-1)*86400) + ($h_orig*3600) + ($n_orig*60) + $s_orig;

                          //Third, we start looping through the repetitions and see if they are between
                          // $start and $end
                          for($i = $first_event_repeat_start; ($i<=$end && $i<=$repeat_end); $i+=604800)
                          {
                            if($start<$i && $i<=$end && $i<=$repeat_end)
                            {
                               list($y_repeat,$m_repeat,$d_repeat,$h_repeat,$n_repeat,$s_repeat) = split('/',date('Y/m/j/H/i/s',$i));
                               $new_start_time = mktime($h_orig,$n_orig,$s_orig,$m_now,$d_orig,$y_now);
                               $new_stop_time = mktime($h_orig_e,$n_orig_e,$s_orig_e,$m_now,$d_orig_e,$y_now);
                               $events[] = array($course_info['id'], $row['id'], $new_start_time, $new_stop_time, $row['title'], $row['content']);
                            }
                            $time_now = date('Y/n/W/j/N/G/i/s',$i+604800);
                            list($y_now,$m_now,$w_now,$d_now,$dw_now,$h_now,$n_now,$s_now) = split('/',$time_now);
                          }
                        //}
                    }
                    break;
                case 'monthlyByDate':
                    $time_orig = date('Y/n/W/j/G/i/s',$orig_start);
                    list($y_orig,$m_orig,$w_orig,$d_orig,$h_orig,$n_orig,$s_orig) = split('/',$time_orig);
                    
                    $time_now = date('Y/n/W/j/G/i/s',$start);
                    list($y_now,$m_now,$w_now,$d_now,$h_now,$n_now,$s_now) = split('/',$time_now);
                    //make sure we are one month ahead (to avoid being the same month as the original event)
                    if($m_now==12)
                    {
                        ++$y_now;
                        $m_now = 1;
                    }
                    else
                    {
                        ++$m_now;
                    }
                    
                    $time_orig_end = date('Y/n/j/G/i/s',$orig_end);
                    list($y_orig_e,$m_orig_e,$d_orig_e,$h_orig_e,$n_orig_e,$s_orig_e) = split('/',$time_orig_end);
 
                    $event_repetition_time = mktime($h_orig,$n_orig,$s_orig,$m_now,$d_orig,$y_now); 
                    $diff = $orig_end - $orig_start;
                    while((($y_now>$y_orig) OR (($y_now == $y_orig) && ($m_now>$m_orig))) && ($event_repetition_time < $end) && ($event_repetition_time < $repeat_end))
                    { //if the event is after the original (at least one month) and the original event's day is between the first day of the week and the last day of the week
                      $new_start_time = mktime($h_orig,$n_orig,$s_orig,$m_now,$d_orig,$y_now);
                      $new_stop_time = mktime($h_orig_e,$n_orig_e,$s_orig_e,$m_now,$d_orig_e,$y_now);
                      $events[] = array($course_info['id'],$row['id'],$new_start_time,$new_stop_time,$row['title'],$row['content']);
                      if($m_now==12)
                      {
                      	++$y_now;
                        $m_now = 1;
                      }
                      else
                      {
                        ++$m_now;
                      }
                      $event_repetition_time = mktime($h_orig,$n_orig,$s_orig,$m_now,$d_orig,$y_now); 
                    }
                    break;
                case 'monthlyByDayR':
                    //not implemented yet
                    break;
                case 'monthlyByDay':
                    //not implemented yet
                    break;
                case 'yearly':
                    $time_orig = date('Y/n/j/z/G/i/s',$orig_start);
                    list($y_orig,$m_orig,$d_orig,$dy_orig,$h_orig,$n_orig,$s_orig) = split('/',$time_orig);
                    $time_now = date('Y/n/j/z/G/i/s',$end);
                    list($y_now,$m_now,$d_now,$dy_now,$h_now,$n_now,$s_now) = split('/',$time_now);
                    $event_repetition_time = mktime($h_orig,$n_orig,$s_orig,$m_orig,$d_orig,$y_now); 
                    while((($y_now>$y_orig) && ($start<$event_repetition_time && $event_repetition_time<$end && $event_repetition_time<$repeat_end)))
                    {
                      $time_orig_end = date('Y/n/j/G/i/s',$orig_end);
                      list($y_orig_e,$m_orig_e,$d_orig_e,$dy_orig_e,$h_orig_e,$n_orig_e,$s_orig_e) = split('/',$time_orig_end);
                      $events[] = array($course_info['id'],$row['id'],mktime($h_orig,$n_orig,$s_orig,$m_now,$d_orig,$y_now),mktime($h_orig_e,$n_orig_e,$s_orig_e,$m_now,$d_orig_e,$y_now),$row['title'],$row['content']);
                      ++$y_now;
                      $event_repetition_time = mktime($h_orig,$n_orig,$s_orig,$m_orig,$d_orig,$y_now); 
                    }
                    break;
                default:
                    break;
            }
        }
    }
    return $events;
}
/**
 * Tells if an agenda item is repeated
 * @param   string  Course database
 * @param   int     The agenda item
 * @return  boolean True if repeated, false otherwise
 */
function is_repeated_event($id,$course=null)
{
	if(empty($course))
    {
    	$course_info = api_get_course_info();
        $course = $course_info['dbName']; 
    }
    $id = (int) $id;
	//$t_agenda_repeat = Database::get_course_table(TABLE_AGENDA_REPEAT,$course);
    $sql = "SELECT * FROM $t_agenda_repeat WHERE cal_id = $id";
    $res = Database::query($sql,__FILE__,__LINE__);
    if(Database::num_rows($res)>0)
    {
    	return true;
    }
    return false;
}
/**
 * Adds x weeks to a UNIX timestamp
 * @param   int     The timestamp
 * @param   int     The number of weeks to add
 * @return  int     The new timestamp
 */
function add_week($timestamp,$num=1)
{
    return $timestamp + $num*604800;
}
/**
 * Adds x months to a UNIX timestamp
 * @param   int     The timestamp
 * @param   int     The number of years to add
 * @return  int     The new timestamp
 */
function add_month($timestamp,$num=1)
{
	list($y, $m, $d, $h, $n, $s) = split('/',date('Y/m/d/h/i/s',$timestamp));
    if($m+$num>12)
    {
    	$y += floor($num/12);
        $m += $num%12;
    }
    else
    {
        $m += $num;
    }
    return mktime($h, $n, $s, $m, $d, $y);
}
/**
 * Adds x years to a UNIX timestamp
 * @param   int     The timestamp
 * @param   int     The number of years to add
 * @return  int     The new timestamp
 */
function add_year($timestamp,$num=1)
{
    list($y, $m, $d, $h, $n, $s) = split('/',date('Y/m/d/h/i/s',$timestamp));
    return mktime($h, $n, $s, $m, $d, $y+$num);
}
/**
 * Adds an agenda item in the database. Similar to store_new_agenda_item() except it takes parameters
 * @param   array   Course info
 * @param   string  Event title
 * @param   string  Event content/description
 * @param   string  Start date
 * @param   string  End date
 * @param   array   List of groups to which this event is added
 * @param   int     Parent id (optional)
 * @return  int     The new item's DB ID
 */
function agenda_add_item($course_info, $title, $content, $db_start_date, $db_end_date, $to=array(), $parent_id=null)
{
  global $_course;
    $user_id    = api_get_user_id();
    $t_agenda   = Database::get_main_table(TABLE_MAIN_SYSTEM_CALENDAR);
    
    // some filtering of the input data
    $title      = Database::escape_string($title); // no html allowed in the title
    $content    = Database::escape_string($content);
    $start_date = Database::escape_string($db_start_date);
    $end_date   = Database::escape_string($db_end_date);
     
     isset($_SESSION['id_session'])?$id_session=intval($_SESSION['id_session']):$id_session=null;
    // store in the table calendar_event

    // check if exists in calendar_event table
    $sql = "SELECT * FROM $t_agenda WHERE title='$title' AND content = '$content' AND start_date = '$start_date'
    		AND end_date = '$end_date' ".(!empty($parent_id)? "AND parent_event_id = '$parent_id'":"");
    $result = api_sql_query($sql,__FILE__,__LINE__);
    $count = Database::num_rows($result);
    if ($count > 0) {
    	return false;
    }

    $sql = "INSERT INTO ".$t_agenda."
                            (title,content, start_date, end_date)
                            VALUES
                            ('".$title."','".$content."', '".$start_date."','".$end_date."')";

    $result = api_sql_query($sql,__FILE__,__LINE__) or die (Database::error());
    $last_id=Database::insert_id();

    // add a attachment file in agenda

   // add_agenda_attachment_file($file_comment,$last_id);

    // store in last_tooledit (first the groups, then the users
    $done = false;
    if ((!is_null($to))or (!empty($_SESSION['toolgroup']))) // !is_null($to): when no user is selected we send it to everyone
    {
        $send_to=separate_users_groups($to);
        // storing the selected groups
        if (is_array($send_to['groups']))
        {
            foreach ($send_to['groups'] as $group)
            {
                api_item_property_update($course_info, TOOL_CALENDAR_EVENT, $last_id, "AgendaAdded", $user_id, $group,'',$start_date, $end_date);
                $done = true;
            }
        }
        // storing the selected users
        if (is_array($send_to['users']))
        {
            foreach ($send_to['users'] as $user)
            {
                api_item_property_update($course_info, TOOL_CALENDAR_EVENT, $last_id, "AgendaAdded", $user_id,'',$user, $start_date,$end_date);
                $done = true;
            }
        }
    }

    if(!$done) // the message is sent to everyone, so we set the group to 0
    {
        api_item_property_update($course_info, TOOL_CALENDAR_EVENT, $last_id, "AgendaAdded", $user_id, $start_date,$end_date);
    }
    // storing the resources
    if (!empty($_SESSION['source_type']) && !empty($last_id)) {
    //store_resources($_SESSION['source_type'],$last_id);
    }
    return $last_id;
}
/**
 * Adds a repetitive item to the database
 * @param   array   Course info
 * @param   int     The original event's id
 * @param   string  Type of repetition
 * @param   int     Timestamp of end of repetition (repeating until that date)
 * @param   array   Original event's destination
 * @return  boolean False if error, True otherwise
 */
 function get_calendar_items($month, $year)
{
	global $_user, $_course;
	global $is_allowed_to_edit;
		$month=Database::escape_string($month);
		$year=Database::escape_string($year);

	// database variables
	$TABLEAGENDA = Database::get_main_table(TABLE_MAIN_SYSTEM_CALENDAR);
	//$TABLE_ITEM_PROPERTY=Database::get_course_table(TABLE_ITEM_PROPERTY);

    $month_first_day = mktime(0,0,0,$month,1,$year);
    $month_last_day  = mktime(0,0,0,$month+1,1,$year)-1;
    if($month==12) {
        $month_last_day = mktime(0,0,0,1,1,$year+1)-1;
    }
    $repeats = array();
	if (is_allowed_to_edit()) {
		$sql="SELECT
			DISTINCT *
			FROM ".$TABLEAGENDA." agenda
			WHERE MONTH(start_date)='".$month."' AND YEAR(start_date)='".$year."'
			GROUP BY id ".
			"ORDER BY  start_date ";
	}
	$result=api_sql_query($sql,__FILE__,__LINE__);
	$data=array();
	while ($row=Database::fetch_array($result)) {
		$datum_item=(int)substr($row["start_date"],8,2);
		$data[$datum_item][intval($datum_item)][] = $row;
	}
	return $data;
}
 
function agenda_add_repeat_item($course_info,$orig_id,$type,$end,$orig_dest)
{/*
	$t_agenda   = Database::get_main_table(TABLE_MAIN_SYSTEM_CALENDAR,$course_info['dbName']);
   // $t_agenda_r = Database::get_course_table(TABLE_AGENDA_REPEAT,$course_info['dbName']);
    //$sql = "SELECT title, content, UNIX_TIMESTAMP(start_date) as sd, UNIX_TIMESTAMP(end_date) as ed FROM $t_agenda WHERE id = $orig_id";
    $sql = "SELECT title, content, start_date as sd, end_date as ed FROM $t_agenda WHERE id = $orig_id";
    $res = Database::query($sql,__FILE__,__LINE__);
    if(Database::num_rows($res)!==1){return false;}
    $row = Database::fetch_array($res);
    //$orig_start = $row['sd'];
    $orig_start = mktime(substr($row['sd'],11,2),substr($row['sd'],14,2),substr($row['sd'],17,2),substr($row['sd'],5,2),substr($row['sd'],8,2),substr($row['sd'],0,4));
    //$orig_end   = $row['ed'];
    $orig_end   = mktime(substr($row['ed'],11,2),substr($row['ed'],14,2),substr($row['ed'],17,2),substr($row['ed'],5,2),substr($row['ed'],8,2),substr($row['ed'],0,4));
    $diff = $orig_end - $orig_start;
    $orig_title = $row['title'];
    $orig_content = $row['content'];
    $now = time();
    $type = Database::escape_string($type);
    $end = (int) $end;
    if(1<=$end && $end<=500)
    {
    	//we assume that, with this type of value, the user actually gives a count of repetitions
        //and that he wants us to calculate the end date with that (particularly in case of imports from ical)
        switch($type)
        {
            case 'daily':
                $end = $orig_start + (86400*$end);
                break;
            case 'weekly':
                $end = add_week($orig_start,$end);
                break;
            case 'monthlyByDate':
                $end = add_month($orig_start,$end);
                break;
            case 'monthlyByDay':
                //TODO
                break;
            case 'monthlyByDayR':
                //TODO
                break;
            case 'yearly':
                $end = add_year($orig_start,$end);
                break;
        }
    }
    if($end > $now 
        && in_array($type,array('daily','weekly','monthlyByDate','monthlyByDay','monthlyByDayR','yearly')))
    {
       $sql = "INSERT INTO $t_agenda_r (cal_id, cal_type, cal_end)" .
            " VALUES ($orig_id,'$type',$end)";
       $res = Database::query($sql,__FILE__,__LINE__);
        switch($type)
        {
            case 'daily':
                for($i = $orig_start + 86400; ($i <= $end); $i += 86400)
                {
                    $res = agenda_add_item($course_info, $orig_title, $orig_content, date('Y-m-d H:i:s', $i), date('Y-m-d H:i:s', $i+$diff), $orig_dest, $orig_id);
                }
                break;
            case 'weekly':
                for($i = $orig_start + 604800; ($i <= $end); $i += 604800)
                {
                    $res = agenda_add_item($course_info, $orig_title, $orig_content, date('Y-m-d H:i:s', $i), date('Y-m-d H:i:s', $i+$diff), $orig_dest, $orig_id);
                }
                break;
            case 'monthlyByDate':
                $next_start = add_month($orig_start);
                while($next_start <= $end)
                {
                    $res = agenda_add_item($course_info, $orig_title, $orig_content, date('Y-m-d H:i:s', $next_start), date('Y-m-d H:i:s', $next_start+$diff), $orig_dest, $orig_id);
                    $next_start = add_month($next_start);
                }
                break;
            case 'monthlyByDay':
                //not yet implemented
                break;
            case 'monthlyByDayR':
                //not yet implemented
                break;
            case 'yearly':
                $next_start = add_year($orig_start);
                while($next_start <= $end)
                {
                    $res = agenda_add_item($course_info, $orig_title, $orig_content, date('Y-m-d H:i:s', $next_start), date('Y-m-d H:i:s', $next_start+$diff), $orig_dest, $orig_id);
                    $next_start = add_year($next_start);
                }
                break;
        }
    }
	return true;*/
}
/**
 * Import an iCal file into the database
 * @param   array   Course info
 * @return  boolean True on success, false otherwise
 */
function display_courseadmin_links()
{
	echo "<a href='".api_get_self()."?".api_get_cidreq()."&action=add&amp;origin=".Security::remove_XSS($_GET['origin'])."'>".Display::return_icon('calendar_add.gif', get_lang('AgendaAdd'))." ".get_lang('AgendaAdd')."</a>";
/*
	if (empty ($_SESSION['toolgroup']))
	{
		echo "<li>".get_lang('UserGroupFilter')."<br/>";
		echo "<form name=\"filter\">";
		show_user_group_filter_form();
		echo "</form>";
	}
*/

}
function agenda_import_ical($course_info,$file)
{
	require_once(api_get_path(LIBRARY_PATH).'fileUpload.lib.php');
    $charset = api_get_setting('platform_charset');
    $filepath = api_get_path(GARBAGE_PATH).$file['name'];
    if(!@move_uploaded_file($file['tmp_name'],$filepath))
    {
    	error_log('Problem moving uploaded file: '.$file['error'].' in '.__FILE__.' line '.__LINE__);
    	return false;
    }
    require_once (api_get_path(LIBRARY_PATH).'icalcreator/iCalcreator.class.php');
    $ical = new vcalendar();
    $ical->setConfig( 'directory', dirname($filepath) );
    $ical->setConfig( 'filename', basename($filepath) );
    $ical->parse();
    //we need to recover: summary, description, dtstart, dtend, organizer, attendee, location (=course name), 
    // rrule
    $ve = $ical->getComponent(0);
    //print_r($ve);
    $ttitle = $ve->getProperty('summary');
    //print_r($ttitle);
    $title = mb_convert_encoding($ttitle,$charset,'UTF-8');
    $tdesc = $ve->getProperty('description');
    $desc = mb_convert_encoding($tdesc,$charset,'UTF-8');
    $ts = $ve->getProperty('dtstart');
    $start_date = $ts['year'].'-'.$ts['month'].'-'.$ts['day'].' '.$ts['hour'].':'.$ts['min'].':'.$ts['sec'];
    $ts = $ve->getProperty('dtend');
    $end_date = $ts['year'].'-'.$ts['month'].'-'.$ts['day'].' '.$ts['hour'].':'.$ts['min'].':'.$ts['sec'];
    //echo $start_date.' - '.$end_date;
    $organizer = $ve->getProperty('organizer');
    $attendee = $ve->getProperty('attendee');
    $course_name = $ve->getProperty('location');
    //insert the event in our database
    $id = agenda_add_item($course_info,$title,$desc,$start_date,$end_date,$_POST['selectedform']);
    
    $repeat = $ve->getProperty('rrule');
    if(is_array($repeat) && !empty($repeat['FREQ']))
    {
    	$trans = array('DAILY'=>'daily','WEEKLY'=>'weekly','MONTHLY'=>'monthlyByDate','YEARLY'=>'yearly');
        $freq = $trans[$repeat['FREQ']];
        $interval = $repeat['INTERVAL'];
        if(isset($repeat['UNTIL']) && is_array($repeat['UNTIL']))
        {
            $until = mktime(23,59,59,$repeat['UNTIL']['month'],$repeat['UNTIL']['day'],$repeat['UNTIL']['year']);
            $res = agenda_add_repeat_item($course_info,$id,$freq,$until,$_POST['selectedform']);
        }
        //TODO: deal with count
        if(!empty($repeat['COUNT']))
        {
            $count = $repeat['COUNT'];
            $res = agenda_add_repeat_item($course_info,$id,$freq,$count,$_POST['selectedform']);            
        }
    }
    return true;
}
?>