<?php
/* For licensing terms, see /license.txt */

/**
*	Exercise result
*	This script gets information from the script "exercise_submit.php",
*	through the session, and calculates the score of the student for
*	that exercise.
*	Then it shows the results on the screen.
*	@package chamilo.exercise
*	@author Olivier Brouckaert, main author
*	@author Roan Embrechts, some refactoring
* 	@author Julio Montoya switchable fill in blank option added
*
*	@todo	split more code up in functions, move functions to library?
*/

use \ChamiloSession as Session;

$debug = false;
require_once '../inc/global.inc.php';

$this_section = SECTION_COURSES;

/* 	ACCESS RIGHTS  */
api_protect_course_script(true);

if ($debug) {
    error_log('Entering exercise_result.php: '.print_r($_POST, 1));
}

// general parameters passed via POST/GET
if (empty($origin)) {
    $origin = Security::remove_XSS($_REQUEST['origin']);
}
if (empty($objExercise)) {
    $objExercise = $_SESSION['objExercise'];
}
if (empty($remind_list)) {
    $remind_list = isset($_REQUEST['remind_list']) ? $_REQUEST['remind_list'] : null;
}

$exe_id = isset($_REQUEST['exe_id']) ? intval($_REQUEST['exe_id']) : 0;

if (empty($objExercise)) {
    // Redirect to the exercise overview
    // Check if the exe_id exists
    $objExercise = new Exercise();
    $exercise_stat_info = $objExercise->get_stat_track_exercise_info_by_exe_id($exe_id);
    if (!empty($exercise_stat_info) && isset($exercise_stat_info['exe_exo_id'])) {
        header("Location: overview.php?exerciseId=".$exercise_stat_info['exe_exo_id']);
        exit;
    }
    api_not_allowed();
}

$gradebook = '';
if (isset($_SESSION['gradebook'])) {
	$gradebook=	$_SESSION['gradebook'];
}
if (!empty($gradebook) && $gradebook=='view') {
	$interbreadcrumb[]= array ('url' => '../gradebook/'.$_SESSION['gradebook_dest'], 'name' => get_lang('ToolGradebook'));
}

$nameTools = get_lang('Exercice');

$interbreadcrumb[]= array("url" => "exercice.php?gradebook=$gradebook","name" => get_lang('Exercices'));

if ($origin != 'learnpath') {
	// So we are not in learnpath tool
	Display::display_header($nameTools,get_lang('Exercise'));
} else {
    Display::display_reduced_header();
}

/* DISPLAY AND MAIN PROCESS */

// I'm in a preview mode as course admin. Display the action menu.
if (api_is_course_admin() && $origin != 'learnpath') {
	echo '<div class="actions">';
	echo '<a href="admin.php?'.api_get_cidreq().'&exerciseId='.$objExercise->id.'">'.Display::return_icon('back.png', get_lang('GoBackToQuestionList'), array(), 32).'</a>';
	echo '<a href="exercise_admin.php?'.api_get_cidreq().'&modifyExercise=yes&exerciseId='.$objExercise->id.'">'.Display::return_icon('edit.png', get_lang('ModifyExercise'), array(), 32).'</a>';
	echo '</div>';
}

$feedback_type = $objExercise->feedback_type;
$exercise_stat_info = $objExercise->get_stat_track_exercise_info_by_exe_id($exe_id);

if (!empty($exercise_stat_info['data_tracking'])) {
	$question_list = explode(',', $exercise_stat_info['data_tracking']);
}

$learnpath_id = $exercise_stat_info['orig_lp_id'];
$learnpath_item_id = $exercise_stat_info['orig_lp_item_id'];
$learnpath_item_view_id = $exercise_stat_info['orig_lp_item_view_id'];

if ($origin == 'learnpath') {
?>
	<form method="GET" action="exercice.php?<?php echo api_get_cidreq() ?>">
	<input type="hidden" name="origin" 					value="<?php echo $origin; ?>" />
    <input type="hidden" name="learnpath_id" 			value="<?php echo $learnpath_id; ?>" />
    <input type="hidden" name="learnpath_item_id" 		value="<?php echo $learnpath_item_id; ?>" />
    <input type="hidden" name="learnpath_item_view_id"  value="<?php echo $learnpath_item_view_id; ?>" />
<?php
}

$i = $total_score = $total_weight = 0;

//We check if the user attempts before sending to the exercise_result.php
if ($objExercise->selectAttempts() > 0) {
    $attempt_count = Event::get_attempt_count(
        api_get_user_id(),
        $objExercise->id,
        $learnpath_id,
        $learnpath_item_id,
        $learnpath_item_view_id
    );
    if ($attempt_count >= $objExercise->selectAttempts()) {
        Display :: display_warning_message(
            sprintf(get_lang('ReachedMaxAttempts'), $objExercise->selectTitle(), $objExercise->selectAttempts()),
            false
        );
        if ($origin != 'learnpath') {
            //we are not in learnpath tool
            Display::display_footer();
        }
        exit;
    }
}

Display :: display_normal_message(get_lang('Saved').'<br />',false);

// Display and save questions
ExerciseLib::display_question_list_by_attempt($objExercise, $exe_id, true);

//If is not valid

/*
$session_control_key = ExerciseLib::get_session_time_control_key($objExercise->id, $learnpath_id, $learnpath_item_id);
if (isset($session_control_key) && !ExerciseLib::exercise_time_control_is_valid($objExercise->id, $learnpath_id, $learnpath_item_id)) {
	$TBL_TRACK_ATTEMPT		= Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
	$sql_fraud = "UPDATE $TBL_TRACK_ATTEMPT SET answer = 0, marks = 0, position = 0 WHERE exe_id = $exe_id ";
	Database::query($sql_fraud);
}*/

//Unset session for clock time
ExerciseLib::exercise_time_control_delete($objExercise->id, $learnpath_id, $learnpath_item_id);
ExerciseLib::delete_chat_exercise_session($exe_id);

if ($origin != 'learnpath') {
    echo '<hr>';
    echo Display::url(get_lang('ReturnToCourseHomepage'), api_get_course_url(), array('class' => 'btn btn-large'));

    if (api_is_allowed_to_session_edit()) {
        Session::erase('objExercise');
        Session::erase('exe_id');
    }

	Display::display_footer();
} else {
	$lp_mode = isset($_SESSION['lp_mode']) ? $_SESSION['lp_mode'] : null;
	$url = '../newscorm/lp_controller.php?cidReq='.api_get_course_id().'&action=view&lp_id='.$learnpath_id.'&lp_item_id='.$learnpath_item_id.'&exeId='.$exercise_stat_info['exe_id'].'&fb_type='.$objExercise->feedback_type;
	$href = ($lp_mode == 'fullscreen')?' window.opener.location.href="'.$url.'" ':' top.location.href="'.$url.'"';

    if (api_is_allowed_to_session_edit()) {
        Session::erase('objExercise');
        Session::erase('exe_id');
    }

	// Record the results in the learning path, using the SCORM interface (API)
	echo "<script>window.parent.API.void_save_asset('$total_score', '$total_weight', 0, 'completed');</script>";
    echo '<script type="text/javascript">'.$href.'</script>';
	echo '</body></html>';
}
