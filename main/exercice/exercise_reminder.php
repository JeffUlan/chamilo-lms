<?php
/* For licensing terms, see /license.txt */
/**
*	Exercise reminder overview
*	Then it shows the results on the screen.
*	@package chamilo.exercise
* 	@author Julio Montoya Armas switchable fill in blank option added
*/
/**
 *	INIT SECTION
 */
require_once 'exercise.class.php';
require_once 'question.class.php';
require_once 'answer.class.php';

$language_file = 'exercice';
require_once '../inc/global.inc.php';
require_once 'exercise.lib.php';

if ($_GET['origin'] == 'learnpath') {
    require_once '../newscorm/learnpath.class.php';
    require_once '../newscorm/learnpathItem.class.php';
    require_once '../newscorm/scorm.class.php';
    require_once '../newscorm/scormItem.class.php';
    require_once '../newscorm/aicc.class.php';
    require_once '../newscorm/aiccItem.class.php';
}

$this_section = SECTION_COURSES;

/* 	ACCESS RIGHTS  */
// notice for unauthorized people.
api_protect_course_script(true);

if($debug>0){error_log('Entered exercise_result.php: '.print_r($_POST,1));}

// general parameters passed via POST/GET
if ( empty ( $origin ) ) {                  $origin                 = Security::remove_XSS($_REQUEST['origin']);}
if ( empty ( $learnpath_id ) ) {            $learnpath_id           = intval($_REQUEST['learnpath_id']);}
if ( empty ( $learnpath_item_id ) ) {       $learnpath_item_id      = intval($_REQUEST['learnpath_item_id']);}
if ( empty ( $learnpath_item_view_id ) ) {  $learnpath_item_view_id = intval($_REQUEST['learnpath_item_view_id']);}

if ( empty ($exerciseId)) {  $exerciseId = intval($_REQUEST['exerciseId']);}

if ( empty ($objExercise)) { $objExercise = $_SESSION['objExercise'];}

if (!$objExercise) {
	//Redirect to the exercise overview
	//Check if the exe_id exists
	header("Location: overview.php?exerciseId=".$exerciseId);
	exit;
}

$time_control = false;
$clock_expired_time = get_session_time_control_key($objExercise->id, $learnpath_id, $learnpath_item_id);

if ($objExercise->expired_time != 0 && !empty($clock_expired_time)) {
	$time_control = true;
}

if ($time_control) {
    // Get time left for exipiring time
    $time_left = api_strtotime($clock_expired_time,'UTC') - time();
	$htmlHeadXtra[] = api_get_css(api_get_path(WEB_LIBRARY_PATH).'javascript/epiclock/stylesheet/jquery.epiclock.css');
    $htmlHeadXtra[] = api_get_css(api_get_path(WEB_LIBRARY_PATH).'javascript/epiclock/renderers/minute/epiclock.minute.css');
    $htmlHeadXtra[] = api_get_js('epiclock/javascript/jquery.dateformat.min.js');
    $htmlHeadXtra[] = api_get_js('epiclock/javascript/jquery.epiclock.min.js');
    $htmlHeadXtra[] = api_get_js('epiclock/renderers/minute/epiclock.minute.js');
	$htmlHeadXtra[] = $objExercise->show_time_control_js($time_left);
}

if (isset($_SESSION['exe_id'])) {
	$exe_id = intval($_SESSION['exe_id']);
}
$exercise_stat_info	= $objExercise->get_stat_track_exercise_info_by_exe_id($exe_id);
if (!empty($exercise_stat_info['data_tracking'])) {
	$question_list = explode(',', $exercise_stat_info['data_tracking']);
}

if (empty($exercise_stat_info) || empty($question_list)) {
	api_not_allowed();
}
$gradebook = isset($_SESSION['greadebook']) ? Security::remove_XSS($_SESSION['greadebook']) : null;

$nameTools = get_lang('Exercice');
$interbreadcrumb[] = array("url" => "exercice.php?gradebook=$gradebook","name" => get_lang('Exercices'));

if ($origin != 'learnpath') {
	//so we are not in learnpath tool
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
echo Display::page_header(get_lang('QuestionsToReview'));

if ($time_control) {
    echo $objExercise->return_time_left_div();
}

echo Display::div('', array('id' => 'message'));

echo '<script>
		lp_data = $.param({"learnpath_id": '.$learnpath_id.', "learnpath_item_id" : '.$learnpath_item_id.', "learnpath_item_view_id": '.$learnpath_item_view_id.'});

        function final_submit() {
        	//Normal inputs
        	window.location = "exercise_result.php?origin='.$origin.'&exe_id='.$exe_id.'&" + lp_data;
		}

		function review_questions() {
			var is_checked = 1;
			$("input[type=checkbox]").each(function () {
			    if ($(this).attr("checked") == "checked") {
			    	is_checked = 2;
			    	return false;
			    }
			});

			if (is_checked == 1) {
				$("#message").addClass("warning-message");
				$("#message").html("'.addslashes(get_lang('SelectAQuestionToReview')).'");
			}
			window.location = "exercise_submit.php?'.api_get_cidreq().'&exerciseId='.$objExercise->id.'&reminder=2&origin='.$origin.'&" + lp_data;
		}

		function save_remind_item(obj, question_id) {
			var action = "";
			if ($(obj).is(\':checked\')) {
				action = "add";
			} else {
				action = "delete";
			}
			$.ajax({
				url: "'.api_get_path(WEB_AJAX_PATH).'exercise.ajax.php?a=add_question_to_reminder",
				data: "question_id="+question_id+"&exe_id='.$exe_id.'&action="+action,
				success: function(return_value) {
				},
			});
		}
</script>';

$exercise_result = get_answered_questions_from_attempt($exe_id, $objExercise);

$remind_list = $exercise_stat_info['questions_to_check'];
$remind_list = explode(',', $remind_list);


echo Display::label(get_lang('QuestionWithNoAnswer'), 'warning');
echo '<div class="clear"></div><br />';

$table = '<div class="row">';

$counter = 0;
$split_by = 25;

//$count_cols = round(count($question_list)/$split_by);
$count_cols = 3;
$span_size = 12/$count_cols;
$span_class = "span$span_size";

$table .= '<div class="'.$span_class.'">';

$cols = 1;

// Loop over all question to show results for each of them, one by one
foreach ($question_list as $questionId) {

    // creates a temporary Question object
    $objQuestionTmp = Question::read($questionId);
    // initialize question information
    $check_id = 'remind_list['.$questionId.']';
    $attributes = array('id' => $check_id, 'onclick' => "save_remind_item(this, '$questionId');");

    if (in_array($questionId, $remind_list)) {
        $attributes['checked'] = 1;
    }
    $label_attributes = array();
    $label_attributes['class'] = 'checkbox';
    $label_attributes['for'] = $check_id;
    $label_attributes['class'] = "checkbox";

    if (in_array($objQuestionTmp->type, Question::question_type_no_review())) {
        $attributes['disabled'] = 'disabled';
    }

    $checkbox = Display::input('checkbox', 'remind_list['.$questionId.']', '', $attributes);
    $url = 'exercise_submit.php?exerciseId='.$objExercise->id.'&num='.$counter.'&reminder=1';

    $counter++;
    if ($objExercise->type == ONE_PER_PAGE) {
        $question_title = Display::url($counter.'. '.Text::cut($objQuestionTmp->selectTitle(), 40), $url);
        $question_title = $counter.'. '.Text::cut($objQuestionTmp->selectTitle(), 40);
    } else {
        $question_title = $counter.'. '.Text::cut($objQuestionTmp->selectTitle(), 40);
    }
    //Check if the question doesn't have an answer
    if (!in_array($questionId, $exercise_result)) {
        $question_title = Display::label($question_title, 'warning');
    }
    $question_title = Display::tag('label', $checkbox.$question_title, $label_attributes);
    $table .= Display::div($question_title, array('class' => 'exercise_reminder_item'));

    if (($counter % $split_by) == 0) {
        if ($counter > 1 ) {
            $table .= '</div>';
            if ($cols % $count_cols == 0) {
                $table .= '</div>';
                $table .= '<hr>';
                $table .= '<div class="row">';
            }
            $cols++;
        }
        $table .= '<div class="'.$span_class.'">';
    }
}
$table .= "</div>";
$table .= "</div>";

echo Display::div($table, array('class' => 'span12'));

$exercise_actions = Display::url(get_lang('EndTest'), 'javascript://', array('onclick' => 'final_submit();', 'class' => 'btn btn-warning'));
$exercise_actions .= '&nbsp;'.Display::url(get_lang('ReviewQuestions'), 'javascript://', array('onclick' => 'review_questions();', 'class' => 'btn btn-success'));

echo Display::div('', array('class' => 'clear'));
echo Display::div($exercise_actions, array('class' => 'form-actions'));

if ($origin != 'learnpath') {
    //we are not in learnpath tool
    Display::display_footer();
}