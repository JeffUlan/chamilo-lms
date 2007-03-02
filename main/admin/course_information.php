<?php
// $Id: course_information.php 11354 2007-03-02 23:06:28Z yannoo $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
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

// name of the language file that needs to be included 
$language_file = 'admin';
$cidReset = true;
require ('../inc/global.inc.php');
require_once(api_get_path(LIBRARY_PATH).'sortabletable.class.php');
$this_section=SECTION_PLATFORM_ADMIN;

api_protect_admin_script();
/**
 * 
 */
function get_course_usage($course_code)
{
	$table = Database::get_main_table(TABLE_MAIN_COURSE);
	$sql = "SELECT * FROM $table WHERE code='".$course_code."'";
	$res = api_sql_query($sql,__FILE__,__LINE__);
	$course = mysql_fetch_object($res);
	// Learnpaths
	$table = Database :: get_course_table(TABLE_LP_MAIN, $course->db_name);
	$usage[] = array (get_lang(ucfirst(TOOL_LEARNPATH)), Database::count_rows($table));
	// Forums
	$table = Database :: get_course_table(TABLE_FORUM, $course->db_name);
	$usage[] = array (get_lang('Forums'), Database::count_rows($table));
	// Quizzes
	$table = Database :: get_course_table(TABLE_QUIZ_TEST, $course->db_name);
	$usage[] = array (get_lang(ucfirst(TOOL_QUIZ)), Database::count_rows($table));
	// Documents
	$table = Database :: get_course_table(TABLE_DOCUMENT, $course->db_name);
	$usage[] = array (get_lang(ucfirst(TOOL_DOCUMENT)), Database::count_rows($table));
	// Groups
	$table = Database :: get_course_table(TABLE_GROUP, $course->db_name);
	$usage[] = array (get_lang(TOOL_GROUP), Database::count_rows($table));
	// Calendar
	$table = Database :: get_course_table(TABLE_AGENDA, $course->db_name);
	$usage[] = array (get_lang(ucfirst(TOOL_CALENDAR_EVENT)), Database::count_rows($table));
	// Link
	$table = Database::get_course_table(TABLE_LINK, $course->db_name);
	$usage[] = array(get_lang(ucfirst(TOOL_LINK)), Database::count_rows($table));
	// Announcements
	$table = Database::get_course_table(TABLE_ANNOUNCEMENT, $course->db_name);
	$usage[] = array(get_lang(ucfirst(TOOL_ANNOUNCEMENT)), Database::count_rows($table));
	return $usage;
}
/*****************************************************************/
if (!isset ($_GET['code']))
{
	api_not_allowed();
}
$interbreadcrumb[] = array ("url" => 'index.php', "name" => get_lang('PlatformAdmin'));
$interbreadcrumb[] = array ("url" => 'course_list.php', "name" => get_lang('Courses'));
$table_course = Database :: get_main_table(TABLE_MAIN_COURSE);
$code = $_GET['code'];
$sql = "SELECT * FROM $table_course WHERE code = '".$code."'";
$res = api_sql_query($sql,__FILE__,__LINE__);
$course = mysql_fetch_object($res);
$tool_name = $course->title.' ('.$course->code.')';
Display::display_header($tool_name);
//api_display_tool_title($tool_name);
?>
<p>
<a href="<?php echo api_get_path(WEB_COURSE_PATH).$course->directory; ?>"><img src="../img/course_home.gif" border="0" /> <?php echo api_get_path(WEB_COURSE_PATH).$course->directory; ?></a>
<br/>
<?php
if( get_setting('server_type') == 'test')
{
	?>
	<a href="course_create_content.php?course_code=<?php echo $course->code ?>"><?php echo get_lang('AddDummyContentToCourse') ?></a>
	<?php
}
?>
</p>
<?php

echo '<h4>'.get_lang('CourseUsage').'</h4>';
echo '<blockquote>';
$table = new SortableTableFromArray(get_course_usage($course->code),0,20,'usage_table');
$table->set_additional_parameters(array ('code' => $_GET['code']));
$table->set_other_tables(array('user_table','class_table'));
$table->set_header(0,get_lang('Tool'), true);
$table->set_header(1,get_lang('NumberOfItems'), true);
$table->display();
echo '</blockquote>';
/**
 * Show all users subscribed in this course
 */
echo '<h4>'.get_lang('Users').'</h4>';
echo '<blockquote>';
$table_course_user = Database :: get_main_table(TABLE_MAIN_COURSE_USER);
$table_user = Database :: get_main_table(TABLE_MAIN_USER);
$sql = 'SELECT *,cu.status as course_status FROM '.$table_course_user.' cu, '.$table_user." u WHERE cu.user_id = u.user_id AND cu.course_code = '".$code."' ";
$res = api_sql_query($sql,__FILE__,__LINE__);
if (mysql_num_rows($res) > 0)
{
	$users = array ();
	while ($obj = mysql_fetch_object($res))
	{
		$user = array ();
		$user[] = $obj->official_code;
		$user[] = $obj->firstname;
		$user[] = $obj->lastname;
		$user[] = Display :: encrypted_mailto_link($obj->email, $obj->email);
		$user[] = $obj->course_status == 5 ? get_lang('Student') : get_lang('Teacher');
		$user[] = '<a href="user_information.php?user_id='.$obj->user_id.'"><img src="../img/synthese_view.gif" border="0" /></a>';
		$users[] = $user;
	}
	$table = new SortableTableFromArray($users,0,20,'user_table');
	$table->set_additional_parameters(array ('code' => $_GET['code']));
	$table->set_other_tables(array('usage_table','class_table'));
	$table->set_header(0,get_lang('OfficialCode'), true);
	$table->set_header(1,get_lang('FirstName'), true);
	$table->set_header(2,get_lang('LastName'), true);
	$table->set_header(3,get_lang('Email'), true);
	$table->set_header(4,get_lang('Status'), true);
	$table->set_header(5,'', false);
	$table->display();
}
else
{
	echo get_lang('NoUsersInCourse');
}
echo '</blockquote>';
/**
 * Show all classes subscribed in this course
 */
$table_course_class = Database :: get_main_table(TABLE_MAIN_COURSE_CLASS);
$table_class = Database :: get_main_table(TABLE_MAIN_CLASS);
$sql = 'SELECT * FROM '.$table_course_class.' cc, '.$table_class.' c WHERE cc.class_id = c.id AND cc.course_code = '."'".$_GET['code']."'";
$res = api_sql_query($sql,__FILE__,__LINE__);
if (mysql_num_rows($res) > 0)
{
	$data = array ();
	while ($class = mysql_fetch_object($res))
	{
		$row = array ();
		$row[] = $class->name;
		$row[] = '<a href="class_information.php?id='.$class->id.'"><img src="../img/synthese_view.gif" border="0" /></a>';
		$data[] = $row;
	}
	echo '<p><b>'.get_lang('AdminClasses').'</b></p>';
	echo '<blockquote>';
	$table = new SortableTableFromArray($data,0,20,'class_table');
	$table->set_additional_parameters(array ('code' => $_GET['code']));
	$table->set_other_tables(array('usage_table','user_table'));
	$table->set_header(0,get_lang('Title'));
	$table->set_header(1,'');
	$table->display();
	echo '</blockquote>';
}
else
{
	echo '<p>'.get_lang('NoClassesForThisCourse').'</p>';
}
/*
==============================================================================
		FOOTER 
==============================================================================
*/ 
Display::display_footer();
?>