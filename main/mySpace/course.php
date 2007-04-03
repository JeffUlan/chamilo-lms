<?php
/*
 * Created on 28 juil. 2006 by Elixir Interactive http://www.elixir-interactive.com
 */
 ob_start();
 $nameTools= 'Cours';
 // name of the language file that needs to be included 
$language_file = array ('registration', 'index','trad4all', 'tracking');
 $cidReset=true;
 require ('../inc/global.inc.php');
 require_once (api_get_path(LIBRARY_PATH).'tracking.lib.php');
 require_once (api_get_path(LIBRARY_PATH).'export.lib.inc.php');
 require_once (api_get_path(LIBRARY_PATH).'course.lib.php');
 
 $this_section = "session_my_space";
 
 api_block_anonymous_users();
 $interbreadcrumb[] = array ("url" => "index.php", "name" => get_lang('MySpace'));
 
 if(isset($_GET["id_session"]) && $_GET["id_session"]!=""){
	$interbreadcrumb[] = array ("url" => "session.php", "name" => get_lang('Sessions'));
 }
 
 if(isset($_GET["user_id"]) && $_GET["user_id"]!="" && isset($_GET["type"]) && $_GET["type"]=="coach"){
 	 $interbreadcrumb[] = array ("url" => "coaches.php", "name" => get_lang('Tutors'));
 }
 
 if(isset($_GET["user_id"]) && $_GET["user_id"]!="" && isset($_GET["type"]) && $_GET["type"]=="student"){
 	 $interbreadcrumb[] = array ("url" => "student.php", "name" => get_lang('Students'));
 }
 
 if(isset($_GET["user_id"]) && $_GET["user_id"]!="" && !isset($_GET["type"])){
 	 $interbreadcrumb[] = array ("url" => "teachers.php", "name" => get_lang('Teachers'));
 }
 
 Display :: display_header($nameTools);



function count_courses()
{
	global $nb_courses;
	return $nb_courses;
}


// Database Table Definitions 
$tbl_course 				= Database :: get_main_table(TABLE_MAIN_COURSE);
$tbl_user_course 			= Database :: get_main_table(TABLE_MAIN_COURSE_USER);
$tbl_user 					= Database :: get_main_table(TABLE_MAIN_USER);
$tbl_session_course 		= Database :: get_main_table(TABLE_MAIN_SESSION_COURSE);
$tbl_session 				= Database :: get_main_table(TABLE_MAIN_SESSION);
$tbl_session_course_user 	= Database :: get_main_table(TABLE_MAIN_SESSION_COURSE_USER);
 
$id_session = intval($_GET['id_session']);

$a_courses = Tracking :: get_courses_followed_by_coach($_user['user_id'], $id_session);
$nb_courses = count($a_courses);

$table = new SortableTable('tracking_list_course', 'count_courses');
$table -> set_header(0, get_lang('CourseTitle'), false, 'align="center"');
$table -> set_header(1, get_lang('NbStudents'), false);
$table -> set_header(2, get_lang('TimeSpentInTheCourse'), false);
$table -> set_header(3, get_lang('AvgStudentsProgress'), false);
$table -> set_header(4, get_lang('AvgStudentsScore'), false);
$table -> set_header(5, get_lang('AvgMessages'), false);
$table -> set_header(6, get_lang('AvgAssignments'), false);
$table -> set_header(7, get_lang('Details'), false);

$csv_content[] = array(
				get_lang('CourseTitle'),
				get_lang('NbStudents'),
				get_lang('TimeSpentInTheCourse'),
				get_lang('AvgStudentsProgress'),
				get_lang('AvgStudentsScore'),
				get_lang('AvgMessages'),
				get_lang('AvgAssignments')
				);


	
foreach($a_courses as $course_code)
{
	$nb_students_in_course = 0;
	$a_students = array();
	$course = CourseManager :: get_course_information($course_code);
	$avg_assignments_in_course = $avg_messages_in_course = $avg_progress_in_course = $avg_score_in_course = $avg_time_spent_in_course = 0;
	
	// students subscribed to the course throw a session
	if(api_get_setting('use_session_mode') == 'true')
	{
		$sql = 'SELECT id_user as user_id
				FROM '.$tbl_session_course_user.'
				WHERE course_code="'.Database :: escape_string($course_code).'"
				AND id_session='.$id_session;
		$rs = api_sql_query($sql, __FILE__, __LINE__);
		
		while($row = mysql_fetch_array($rs))
		{
			if(!in_array($row['user_id'], $a_students))
			{
				$nb_students_in_course++;
				
				// tracking datas
				$avg_progress_in_course += Tracking :: get_avg_student_progress ($row['user_id'], $course_code);
				$avg_score_in_course += Tracking :: get_avg_student_score ($row['user_id'], $course_code);
				$avg_time_spent_in_course += Tracking :: get_time_spent_on_the_course ($row['user_id'], $course_code);
				$avg_messages_in_course += Tracking :: count_student_messages ($row['user_id'], $course_code);
				$avg_assignments_in_course += Tracking :: count_student_assignments ($row['user_id'], $course_code);
				$a_students[] = $row['user_id'];
			}
		}
	}
	if($nb_students_in_course>0)
	{
		$avg_time_spent_in_course = api_time_to_hms($avg_time_spent_in_course / $nb_students_in_course);
		$avg_progress_in_course = round($avg_progress_in_course / $nb_students_in_course,2).' %';
		$avg_score_in_course = round($avg_score_in_course / $nb_students_in_course,2).' %';
		$avg_messages_in_course = round($avg_messages_in_course / $nb_students_in_course,2);
		$avg_assignments_in_course = round($avg_assignments_in_course / $nb_students_in_course,2);
	}
	
	$table_row = array();
	$table_row[] = $course['title'];
	$table_row[] = $nb_students_in_course;
	$table_row[] = $avg_time_spent_in_course;
	$table_row[] = $avg_progress_in_course;
	$table_row[] = $avg_score_in_course;
	$table_row[] = $avg_messages_in_course;
	$table_row[] = $avg_assignments_in_course;
	$table_row[] = '<a href="../tracking/courseLog.php?cidReq='.$course_code.'&studentlist=true&id_session='.$id_session.'"><img src="'.api_get_path(WEB_IMG_PATH).'2rightarrow.gif" border="0" /></a>';
	
	$csv_content[] = array(
						$course['title'],
						$nb_students_in_course,
						$avg_time_spent_in_course,
						$avg_progress_in_course,
						$avg_score_in_course,
						$avg_messages_in_course,
						$avg_assignments_in_course,
						);
	
	$table -> addRow($table_row, 'align="right"');
	
}
$table -> setColAttributes(0,array('align'=>'left'));
$table -> setColAttributes(7,array('align'=>'center'));
$table -> display();
	
 
/*
 ==============================================================================
		FOOTER
 ==============================================================================
 */

 Display :: display_footer();
?>
