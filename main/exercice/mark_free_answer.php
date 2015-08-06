<?php
/* For licensing terms, see /license.txt */
/** @deprecated seems not to be used*/
exit;

/**
*	Free answer marking script
* 	This script allows a course tutor to mark a student's free answer.
*	@package chamilo.exercise
* 	@author Yannick Warnier <yannick.warnier@beeznest.com>
* 	@version $Id: admin.php 10680 2007-01-11 21:26:23Z pcool $
*
* 	@todo respect coding guidelines
*/

require_once '../inc/global.inc.php';

// debug param. 0: no display - 1: debug display
$debug = 0;

// general parameters passed via POST/GET
$my_course_code = $_GET['cid'];
if (!empty($_REQUEST['exe'])) {
	$my_exe = $_REQUEST['exe'];
} else {
	$my_exe = null;
}
if (!empty($_REQUEST['qst'])) {
	$my_qst = $_REQUEST['qst'];
} else {
	$my_qst = null;
}
if (!empty($_REQUEST['usr'])) {
	$my_usr = $_REQUEST['usr'];
} else {
	$my_usr = null;
}
if (!empty($_REQUEST['cidReq'])) {
	$my_cid = $_REQUEST['cidReq'];
} else {
	$my_cid = null;
}
if (!empty($_POST['action'])) {
	$action = $_POST['action'];
} else {
	$action = '';
}

if (empty($my_qst) or empty($my_usr) or empty($my_cid) or empty($my_exe)){
	header('Location: exercise.php');
	exit();
}

if(!$is_courseTutor)
{
	api_not_allowed();
}

$obj_question = Question:: read($my_qst);

if (isset($_SESSION['gradebook'])) {
	$gradebook = $_SESSION['gradebook'];
}

if (!empty($gradebook) && $gradebook == 'view') {
	$interbreadcrumb[] = array(
		'url' => '../gradebook/'.$_SESSION['gradebook_dest'],
		'name' => get_lang('ToolGradebook'),
	);
}

$nameTools = get_lang('Exercises');

$interbreadcrumb[] = array(
    "url" => "exercise.php",
    "name" => get_lang('Exercises'),
);

$my_msg = 'No change.';

if ($action == 'mark') {
	if (!empty($_POST['score']) && $_POST['score'] < $obj_question->selectWeighting() && $_POST['score'] >= 0) {
		//mark the user mark into the database using something similar to the following function:
		$my_int_cid = api_get_course_int_id($my_cid);
		$exercise_table = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCISES);
		#global $origin, $tbl_learnpath_user, $learnpath_id, $learnpath_item_id;
		$sql = "SELECT * FROM $exercise_table
			    WHERE exe_user_id = ".intval($my_usr)." AND c_id = $my_int_cid AND exe_exo_id = ".intval($my_exe)."
			    ORDER BY exe_date DESC";
		#echo $sql;
		$res = Database::query($sql);
		if (Database::num_rows($res)>0){
			$row = Database::fetch_array($res);
			//@todo Check that just summing past score and the new free answer mark doesn't come up
			// with a score higher than the possible score for that exercise
			$my_score = $row['exe_result'] + $_POST['score'];
			$sql = "UPDATE $exercise_table SET exe_result = '$my_score'
				    WHERE exe_id = '".$row['exe_id']."'";
			$res = Database::query($sql);
			$my_msg = get_lang('MarkIsUpdated');
		} else {
			$my_score = $_POST['score'];
			$reallyNow = time();
			$sql = "INSERT INTO $exercise_table (
					   exe_user_id,
					   c_id,
					   exe_exo_id,
					   exe_result,
					   exe_weighting,
					   exe_date
					  ) VALUES (
					   ".intval($my_usr).",
					   $my_int_cid,
					   ".intval($my_exe).",
					   '".Database::escape_string($my_score)."',
					   '".Database::escape_string($obj_question->selectWeighting())."',
					   FROM_UNIXTIME(".$reallyNow.")
					  )";
			$res = Database::query($sql);
			$my_msg = get_lang('MarkInserted');
		}
		//Database::query($sql);
		//return 0;
	} else {
		$my_msg .= get_lang('TotalScoreTooBig');
	}
}

Display::display_header($nameTools,"Exercise");

// Display simple marking interface

// 1a - result of previous marking then exit suggestion
// 1b - user answer and marking box + submit button
$objAnswerTmp = new Answer($my_qst);
$objAnswerTmp->selectAnswer($answerId);

if($action == 'mark'){
	echo $my_msg.'<br />
		<a href="exercise.php?cidReq='.$cidReq.'">'.get_lang('Back').'</a>';
} else {
	echo '<h2>'.$obj_question->question .':</h2>
		'.$obj_question->selectTitle().'<br /><br />
		'.get_lang('PleaseGiveAMark').
		"<form action='' method='POST'>\n"
		."<input type='hidden' name='exe' value='$my_exe'>\n"
		."<input type='hidden' name='usr' value='$my_usr'>\n"
		."<input type='hidden' name='cidReq' value='$my_cid'>\n"
		."<input type='hidden' name='action' value='mark'>\n"
		."<select name='score'>\n";
		for($i=0 ; $i<$obj_question->selectWeighting() ; $i++){
			echo '<option>'.$i.'</option>';
		}
		echo "</select>".
		"<input type='submit' name='submit' value='".get_lang('Ok')."'>\n"
		."</form>";
}
Display::display_footer();
