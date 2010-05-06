<?php //$Id: myallagendas.php 21102 2009-05-30 14:58:16Z iflorespaz $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2008 Dokeos SPRL
	Copyright (c) 2003 Ghent University (UGent)

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, rue du Corbeau, 108, B-1030 Brussels, Belgium, info@dokeos.com
==============================================================================
	@author: Patrick Cool <patrick.cool@UGent.be>, Ghent University
	@author: Toon Van Hoecke <toon.vanhoecke@ugent.be>, Ghent University
	@author: Eric Remy (initial version)
	@version: 2.2 alpha
	@description: 	this file generates a general agenda of all items of the
					courses the user is registered for
==============================================================================
	version info:
	-------------
	-> version 2.3 : Yannick Warnier, yannick.warnier@dokeos.com 2008
	Added repeated events
	-> version 2.2 : Patrick Cool, patrick.cool@ugent.be, november 2004
	Personal Agenda added. The user can add personal agenda items. The items
	are stored in a dokeos_user database because it is not course or platform
	based. A personal agenda view was also added. This lists all the personal
	agenda items of that user.

	-> version 2.1 : Patrick Cool, patrick.cool@ugent.be, , oktober 2004
	This is the version that works with the Group based Agenda tool.

	-> version 2.0 (alpha): Patrick Cool, patrick.cool@ugent.be, , oktober 2004
	The 2.0 version introduces besides the month view also a week- and day view.
	In the 2.5 (final) version it will be possible for the student to add his/her
	own agenda items. The platform administrator can however decide if the students
	are allowed to do this or not.
	The alpha version only contains the three views. The personal agenda feature is
	not yet completely finished. There are however already some parts of the code
	for adding a personal agenda item present.
	this code was not released in an official dokeos but was only used in the offical
	server of the Ghent University where it underwent serious testing

	-> version 1.5: Toon Van Hoecke, toon.vanhoecke@ugent.be, december 2003

	-> version 1.0: Eric Remy, eremy@rmwc.edu, 6 Oct 2003
	The tool was initially called master-calendar as it collects all the calendar
	items of all the courses one is subscribed to. It was very soon integrated in
	Dokeos as this was a really basic and very usefull tool.

/* ==============================================================================
				  			HEADER
============================================================================== */


// name of the language file that needs to be included
$language_file = 'agenda';
// we are not inside a course, so we reset the course id
$cidReset = true;
// setting the global file that gets the general configuration, the databases, the languages, ...
require_once '../inc/global.inc.php';
$this_section = SECTION_MYAGENDA;
api_block_anonymous_users();
require_once api_get_path(LIBRARY_PATH).'groupmanager.lib.php';
require_once 'agenda.inc.php';
require_once 'myagenda.inc.php';
// setting the name of the tool
$nameTools = get_lang('MyAgenda');

// if we come from inside a course and click on the 'My Agenda' link we show a link back to the course
// in the breadcrumbs
//remove this if cause it was showing in agenda general
/*if(!empty($_GET['coursePath'])) {
	$course_path = api_htmlentities(strip_tags($_GET['coursePath']),ENT_QUOTES,$charset);
	$course_path = str_replace(array('../','..\\'),array('',''),$course_path);
}
*/
if (!empty ($course_path)) {
	$interbreadcrumb[] = array ('url' => api_get_path(WEB_COURSE_PATH).urlencode($course_path).'/index.php', 'name' => Security::remove_XSS($_GET['courseCode']));
}
// this loads the javascript that is needed for the date popup selection
$htmlHeadXtra[] = "<script src=\"tbl_change.js\" type=\"text/javascript\" language=\"javascript\"></script>";
// showing the header
Display::display_header(get_lang('MyAgenda'));

function display_mymonthcalendar_2($agendaitems, $month, $year, $weekdaynames=array(), $monthName, $gradoo2)
{
	
	global $DaysShort,$course_path;
	//Handle leap year
	$numberofdays = array (0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	if (($year % 400 == 0) or ($year % 4 == 0 and $year % 100 <> 0))
		$numberofdays[2] = 29;
	//Get the first day of the month
	$dayone = getdate(mktime(0, 0, 0, $month, 1, $year));
	//Start the week on monday
	$startdayofweek = $dayone['wday'] <> 0 ? ($dayone['wday'] - 1) : 6;
	$g_cc = (isset($_GET['courseCode'])?$_GET['courseCode']:'');
	$backwardsURL = api_get_self()."?coursePath=".urlencode($course_path)."&amp;gradoo=".Security::remove_XSS($gradoo2)."&amp;courseCode=".Security::remove_XSS($g_cc)."&amp;action=view&amp;view=month&amp;month=". ($month == 1 ? 12 : $month -1)."&amp;year=". ($month == 1 ? $year -1 : $year);
	$forewardsURL = api_get_self()."?coursePath=".urlencode($course_path)."&amp;gradoo=".Security::remove_XSS($gradoo2)."&amp;courseCode=".Security::remove_XSS($g_cc)."&amp;action=view&amp;view=month&amp;month=". ($month == 12 ? 1 : $month +1)."&amp;year=". ($month == 12 ? $year +1 : $year);

	echo "<table class=\"data_table\">\n", "<tr>\n", "<th width=\"10%\"><a href=\"", $backwardsURL, "\">&#171;</a></th>\n", "<th width=\"80%\" colspan=\"5\">", $monthName, " ", $year, "</th>\n", "<th width=\"10%\"><a href=\"", $forewardsURL, "\">&#187;</a></th>\n", "</tr>\n";

	echo "<tr>\n";
	for ($ii = 1; $ii < 8; $ii ++)
	{
		echo "<td class=\"weekdays\">", $DaysShort[$ii % 7], "</td>\n";
	}
	echo "</tr>\n";
	$curday = -1;
	$today = getdate();
	while ($curday <= $numberofdays[$month]) {
		echo "<tr>\n";
		for ($ii = 0; $ii < 7; $ii ++) {
			if (($curday == -1) && ($ii == $startdayofweek)) {
				$curday = 1;
			}
			if (($curday > 0) && ($curday <= $numberofdays[$month])) {
				$bgcolor = $ii < 5 ? $class = "class=\"days_week\" style=\"width:10%;\"" : $class = "class=\"days_weekend\" style=\"width:10%;\"";
				$dayheader = "<b>$curday</b><br />";
				if (($curday == $today['mday']) && ($year == $today['year']) && ($month == $today['mon'])) {
					$dayheader = "<b>$curday - ".get_lang("Today")."</b><br />";
					$class = "class=\"days_today\" style=\"width:10%;\"";
				}
				echo "<td ".$class.">", "".$dayheader;
				if (!empty($agendaitems[$curday])) {
					echo "<span class=\"agendaitem\">".$agendaitems[$curday]."</span>";
				}
				echo "</td>\n";
				$curday ++;
			} else {
				echo "<td>&nbsp;</td>\n";
			}
		}
		echo "</tr>\n";
	}
	echo "</table>\n";
}

function get_allagendaitems($grado, $month, $year)
{
	global $_user;
	global $_configuration;
	global $setting_agenda_link;

	$link = mysql_connect('localhost', 'user', 'password');
	if (!$link) {
		    die('Error al conectar: ' . mysql_error());
	}

	$sql  = 'SELECT name FROM chamilo_main.class WHERE name = "'.$grado.'" ORDER BY name ASC';
	$result = mysql_query($sql);

	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{	
		$class_name = $row['name'];
		
		echo "<center><h2>".$class_name."</h2></center>";		

		$sql2  = 'SELECT code, db_name, title FROM chamilo_main.course WHERE category_code = "'.$class_name.'" ';
		$courses_dbs = mysql_query($sql2);
			

	$items = array ();
	// $courses_dbs = array();
	// get agenda-items for every course
	while($row2 = mysql_fetch_array($courses_dbs, MYSQL_ASSOC))
	{
		$db_name = $row2['db_name'];
		$code = $row2['code'];
		$title = $row2['title'];
		//echo "<center><h2>".$db_name."</h2></center>";
		//echo "<center><h2>".$code."aa</h2></center>";
		//databases of the courses
		$TABLEAGENDA = Database :: get_course_table(TABLE_AGENDA, $db_name);
		$TABLE_ITEMPROPERTY = Database :: get_course_table(TABLE_ITEM_PROPERTY, $db_name);

		//$group_memberships = GroupManager :: get_group_ids($array_course_info["db"], $_user['user_id']);
		// if the user is administrator of that course we show all the agenda items
		
			$sqlquery = "SELECT
										DISTINCT agenda.*, item_property.*
										FROM ".$TABLEAGENDA." agenda,
											 ".$TABLE_ITEMPROPERTY." item_property
										WHERE agenda.id = item_property.ref
										AND MONTH(agenda.start_date)='".$month."'
										AND YEAR(agenda.start_date)='".$year."'
										AND item_property.tool='".TOOL_CALENDAR_EVENT."'
										AND item_property.visibility='1'
										GROUP BY agenda.id
										ORDER BY start_date ";
		
		

		$result = Database::query($sqlquery, __FILE__, __LINE__);
		while ($item = Database::fetch_array($result)) {
			$agendaday = date("j",strtotime($item['start_date']));
			if(!isset($items[$agendaday])){$items[$agendaday]=array();}

			$time= date("H:i",strtotime($item['start_date']));
			$end_time= date("H:i",strtotime($item['end_date']));
			$URL = api_get_path(WEB_PATH)."main/calendar/allagendas.php?cidReq=".urlencode($code)."&amp;sort=asc&amp;view=list&amp;day=$agendaday&amp;month=$month&amp;year=$year#$agendaday"; // RH  //Patrick Cool: to highlight the relevant agenda item
			if ($setting_agenda_link == 'coursecode') {
				//$title=$array_course_info['title'];
				$agenda_link = api_substr($title, 0, 14);
			}
			else {
				$agenda_link = Display::return_icon('course_home.gif');
			}
			if(!isset($items[$agendaday][$item['start_date']])) {
				$items[$agendaday][$item['start_date']] = '';
			}
			$items[$agendaday][$item['start_date']] .= "".get_lang('StartTimeWindow')."&nbsp;<i>".$time."</i>"."&nbsp;-&nbsp;".get_lang("EndTimeWindow")."&nbsp;<i>".$end_time."</i>&nbsp;";
			$items[$agendaday][$item['start_date']] .= '<br />'."<b><a href=\"$URL\" title=\"".Security::remove_XSS($title)."\">".$agenda_link."</a> </b> ".Security::remove_XSS($item['title'])."<br /> ";
			$items[$agendaday][$item['start_date']] .= '<br/>';
		}

	}
	}
	// sorting by hour for every day
	$agendaitems = array ();
	while (list ($agendaday, $tmpitems) = each($items)) {
		if(!isset($agendaitems[$agendaday])) {
			$agendaitems[$agendaday] = '';
		}
		sort($tmpitems);
		while (list ($key, $val) = each($tmpitems)) {
			$agendaitems[$agendaday] .= $val;
		}
	}
	//print_r($agendaitems);
	return $agendaitems;
}




/* ==============================================================================
  						SETTING SOME VARIABLES
============================================================================== */
// setting the database variables
$TABLECOURS = Database :: get_main_table(TABLE_MAIN_COURSE);
$TABLECOURSUSER = Database :: get_main_table(TABLE_MAIN_COURSE_USER);
$TABLEAGENDA = Database :: get_course_table(TABLE_AGENDA);
$TABLE_ITEMPROPERTY = Database :: get_course_table(TABLE_ITEM_PROPERTY);
$tbl_personal_agenda = Database :: get_user_personal_table(TABLE_PERSONAL_AGENDA);

// the variables for the days and the months
// Defining the shorts for the days
$DaysShort = api_get_week_days_short();
// Defining the days of the week to allow translation of the days
$DaysLong = api_get_week_days_long();
// Defining the months of the year to allow translation of the months
$MonthsLong = api_get_months_long();

/*==============================================================================
  			TREATING THE URL PARAMETERS
			1. The default values
			2. storing it in the session
			3. possible view
				3.a Month view
				3.b Week view
				3.c day view
				3.d personal view (only the personal agenda items)
			4. add personal agenda
			5. edit personal agenda
			6. delete personal agenda
  ============================================================================== */
// 1. The default values. if there is no session yet, we have by default the month view
if (empty($_SESSION['view']))
{
	$_SESSION['view'] = "month";
}
// 2. Storing it in the session. If we change the view by clicking on the links left, we change the session
if (!empty($_GET['view'])) {
	$_SESSION['view'] = Security::remove_XSS($_GET['view']);

}
// 3. The views: (month, week, day, personal)
if ($_SESSION['view'])
{
	switch ($_SESSION['view'])
	{
		// 3.a Month view
		case "month" :
			$process = "month_view";
			break;
			// 3.a Week view
		case "week" :
			$process = "week_view";
			break;
			// 3.a Day view
		case "day" :
			$process = "day_view";
			break;
			// 3.a Personal view
		case "personal" :
			$process = "personal_view";
			break;
	}
}
// 4. add personal agenda
if (!empty($_GET['action']) && $_GET['action'] == "add_personal_agenda_item" and !$_POST['Submit'])
{
	$process = "add_personal_agenda_item";
}
if (!empty($_GET['action']) && $_GET['action'] == "add_personal_agenda_item" and $_POST['Submit'])
{
	$process = "store_personal_agenda_item";
}
// 5. edit personal agenda
if (!empty($_GET['action']) && $_GET['action'] == "edit_personal_agenda_item" and !$_POST['Submit'])
{
	$process = "edit_personal_agenda_item";
}
if (!empty($_GET['action']) && $_GET['action'] == "edit_personal_agenda_item" and $_POST['Submit'])
{
	$process = "store_personal_agenda_item";
}
// 6. delete personal agenda
if (!empty($_GET['action']) && $_GET['action'] == "delete" AND $_GET['id'])
{
	$process = "delete_personal_agenda_item";
}
/* ==============================================================================
  						OUTPUT
============================================================================== */
if (isset ($_user['user_id']))
{
	// getting all the courses that this user is subscribed to
	$courses_dbs = get_all_courses_of_user();
	if (!is_array($courses_dbs)) // this is for the special case if the user has no courses (otherwise you get an error)
	{
		$courses_dbs = array ();
	}
	// setting and/or getting the year, month, day, week
	$today = getdate();
	$year = (!empty($_GET['year'])? (int)$_GET['year'] : NULL);
	if ($year == NULL)
	{
		$year = $today['year'];
	}
	$month = (!empty($_GET['month'])? (int)$_GET['month']:NULL);
	if ($month == NULL)
	{
		$month = $today['mon'];
	}
	$day = (!empty($_GET['day']) ? (int)$_GET['day']:NULL);
	if ($day == NULL)
	{
		$day = $today['mday'];
	}
	$week = (!empty($_GET['week']) ?(int)$_GET['week']:NULL);
	if ($week == NULL)
	{
		$week = date("W");
	}
	// The name of the current Month
	$monthName = $MonthsLong[$month -1];
	// Starting the output

	

	echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
	echo "<tr>";
	// output: the small calendar item on the left and the view / add links
	echo "<td width=\"220\" valign=\"top\">";

		echo '<br />';
		echo '<br />';
		echo '<h1>Seleccione un grado</h1>';
		echo '<a href="?gradoo=1">I SNIPE</a><br />';
		echo '<a href="?gradoo=2">II SNIPE</a><br />';
		echo '<a href="?gradoo=3">III SNIPE</a><br />';
		echo '<a href="?gradoo=4">IV SNIPE</a><br />';
		echo '<a href="?gradoo=5">V SNIPE</a><br />';
		echo '<br />';
		echo '<a href="?gradoo=6">II PAI</a><br />';
		echo '<a href="?gradoo=I">III PAI</a><br />';
		echo '<a href="?gradoo=II">IV PAI</a><br />';
		echo '<a href="?gradoo=III">V PAI</a><br />';
		echo '<br />';
		echo '<a href="?gradoo=IV">I BI</a><br />';
		echo '<a href="?gradoo=V">II BI</a><br />';

	echo "</td>";
	$gradoo = $_GET['gradoo'];
	// the divider
	// OlivierB : the image has a white background, which causes trouble if the portal has another background color. Image should be transparent. ----> echo "<td width=\"20\" background=\"../img/verticalruler.gif\">&nbsp;</td>";
	echo "<td width=\"20\">&nbsp;</td>";
	// the main area: day, week, month view
	echo "<td valign=\"top\">";
	switch ($process)
	{
		case "month_view" :

			$grado1 = $gradoo."A";
			$grado2 = $gradoo."B";
			$grado3 = $gradoo."C";
			$agendaitems = get_allagendaitems($grado1, $month, $year);
			$agendaitems = get_global_agenda_items($agendaitems, $day, $month, $year, $week, "month_view");
			display_mymonthcalendar_2($agendaitems, $month, $year, array(), $monthName, $gradoo);

			
			$agendaitems = get_allagendaitems($grado2, $month, $year);
			$agendaitems = get_global_agenda_items($agendaitems, $day, $month, $year, $week, "month_view");
			display_mymonthcalendar_2($agendaitems, $month, $year, array(), $monthName, $gradoo);

			
			$agendaitems = get_allagendaitems($grado3, $month, $year);
			$agendaitems = get_global_agenda_items($agendaitems, $day, $month, $year, $week, "month_view");
			display_mymonthcalendar_2($agendaitems, $month, $year, array(), $monthName, $gradoo);
			

			break;
		case "week_view" :
			$agendaitems = get_week_agendaitems($courses_dbs, $month, $year, $week);
			$agendaitems = get_global_agenda_items($agendaitems, $day, $month, $year, $week, "week_view");
			if (api_get_setting("allow_personal_agenda") == "true")
			{
				$agendaitems = get_personal_agenda_items($agendaitems, $day, $month, $year, $week, "week_view");
			}
			display_weekcalendar($agendaitems, $month, $year, array(), $monthName);
			break;
		case "day_view" :
			$agendaitems = get_day_agendaitems($courses_dbs, $month, $year, $day);
			$agendaitems = get_global_agenda_items($agendaitems, $day, $month, $year, $week, "day_view");
			if (api_get_setting("allow_personal_agenda") == "true")
			{
				$agendaitems = get_personal_agenda_items($agendaitems, $day, $month, $year, $week, "day_view");
			}
			display_daycalendar($agendaitems, $day, $month, $year, array(), $monthName);
			break;
		case "personal_view" :
			show_personal_agenda();
			break;
		case "add_personal_agenda_item" :
			show_new_personal_item_form();
			break;
		case "store_personal_agenda_item" :
			store_personal_item($_POST['frm_day'], $_POST['frm_month'], $_POST['frm_year'], $_POST['frm_hour'], $_POST['frm_minute'], $_POST['frm_title'], $_POST['frm_content'], (int)$_GET['id']);
			if ($_GET['id'])
			{
				echo '<br />';
				Display :: display_normal_message(get_lang("PeronalAgendaItemEdited"));
			}
			else
			{
				echo '<br />';
				Display :: display_normal_message(get_lang("PeronalAgendaItemAdded"));
			}
			show_personal_agenda();
			break;
		case "edit_personal_agenda_item" :
			show_new_personal_item_form((int)$_GET['id']);
			break;
		case "delete_personal_agenda_item" :
			delete_personal_agenda((int)$_GET['id']);
			echo '<br />';
			Display :: display_normal_message(get_lang('PeronalAgendaItemDeleted'));
			show_personal_agenda();
			break;
	}
}
echo "</td></tr></table>";
Display :: display_footer();
?>