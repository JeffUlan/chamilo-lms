<?php
/* For licensing terms, see /dokeos_license.txt */
/*
 * Created on 28 juil. 2006 by Elixir Interactive http://www.elixir-interactive.com
 * Somes fixes by Julio Montoya
 */
ob_start();
$nameTools= 'Sessions';
// name of the language file that needs to be included
$language_file = array ('registration', 'index', 'trad4all', 'tracking');
$cidReset = true;

require '../inc/global.inc.php';
require_once api_get_path(LIBRARY_PATH).'tracking.lib.php';
require_once api_get_path(LIBRARY_PATH).'export.lib.inc.php';

api_block_anonymous_users();

$this_section = "session_my_space";

api_block_anonymous_users();
$interbreadcrumb[] = array ("url" => "index.php", "name" => get_lang('MySpace'));
Display :: display_header($nameTools);

// Database Table Definitions
$tbl_course_user 		= Database :: get_main_table(TABLE_MAIN_COURSE_USER);
$tbl_sessions 			= Database :: get_main_table(TABLE_MAIN_SESSION);
$tbl_session_course 	= Database :: get_main_table(TABLE_MAIN_SESSION_COURSE);
$tbl_course 			= Database :: get_main_table(TABLE_MAIN_COURSE);

$export_csv = false;

if (isset($_GET['export']) && $_GET['export'] == 'csv') {
	$export_csv = true;
}


/*
===============================================================================
	FUNCTION
===============================================================================
*/

function count_sessions_coached() {
	global $nb_sessions;
	return $nb_sessions;
}

function sort_sessions($a, $b) {
	global $tracking_column;
	if ($a[$tracking_column] > $b[$tracking_column]) {
		return 1;
	} else {
		return -1;
	}
}

function rsort_sessions($a, $b) {
	global $tracking_column;
	if ($b[$tracking_column] > $a[$tracking_column]) {
		return 1;
	} else {
		return -1;
	}
}


/*
===============================================================================
	MAIN CODE
===============================================================================
*/

if (isset($_GET['id_coach']) && $_GET['id_coach'] != '') {
	$id_coach = intval($_GET['id_coach']);
} else {
	$id_coach = $_user['user_id'];
}

$a_sessions = Tracking :: get_sessions_coached_by_user($id_coach);
$nb_sessions = count($a_sessions);

if ($export_csv) {
	$csv_content = array();
}

if ($nb_sessions > 0) {
	echo '<div align="right">
			<a href="javascript: void(0);" onclick="javascript: window.print();"><img align="absbottom" src="../img/printmgr.gif">&nbsp;'.get_lang('Print').'</a>
			<a href="'.api_get_self().'?export=csv"><img align="absbottom" src="../img/excel.gif">&nbsp;'.get_lang('ExportAsCSV').'</a>
		  </div>';
	$table = new SortableTable('tracking', 'count_sessions_coached');
	$table -> set_header(0, get_lang('Title'));
	$table -> set_header(1, get_lang('Status'));
	$table -> set_header(2, get_lang('Date'));
	$table -> set_header(3, get_lang('Details'), false);

	$all_data = array();
	foreach ($a_sessions as $session) {
		$row = array();
		$row[] = $session['name'];
		$row[] = $session['status'];

		if ($session['date_start'] != '0000-00-00' && $session['date_end'] != '0000-00-00') {
			$row[] = get_lang('From').' '.format_locale_date(get_lang('DateFormatLongWithoutDay'), strtotime($session['date_start'])).' '.get_lang('To').' '.format_locale_date(get_lang('DateFormatLongWithoutDay'), strtotime($session['date_end']));
		} else {
			$row[] = ' - ';
		}

		if ($export_csv) {
			$csv_content[] = $row;
		}

		if (isset($_GET['id_coach']) && $_GET['id_coach'] != '') {
			$row[] = '<a href="student.php?id_session='.$session['id'].'&id_coach='.intval($_GET['id_coach']).'"><img src="'.api_get_path(WEB_IMG_PATH).'2rightarrow.gif" border="0" /></a>';
		} else {
			$row[] = '<a href="course.php?id_session='.$session['id'].'"><img src="'.api_get_path(WEB_IMG_PATH).'2rightarrow.gif" border="0" /></a>';
		}
		$all_data[] = $row;
	}

	if (!isset($tracking_column)) {
		$tracking_column = 0;
	}

	if ($_GET['tracking_direction'] == 'DESC') {
		usort($all_data, 'rsort_sessions');
	} else {
		usort($all_data, 'sort_sessions');
	}

	if ($export_csv) {
		usort($csv_content, 'sort_sessions');
	}

	foreach ($all_data as $row) {
		$table -> addRow($row);
	}

	$table -> setColAttributes(3, array('align' => 'center'));
	$table -> display();

	if ($export_csv) {
		ob_end_clean();
		Export :: export_table_csv($csv_content, 'reporting_student_list');
	}
} else {
	get_lang('NoSession');
}

/*
==============================================================================
	FOOTER
==============================================================================
*/

Display::display_footer();
