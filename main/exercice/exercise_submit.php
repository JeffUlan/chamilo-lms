<?php
/* For licensing terms, see /license.txt */
/**
*	Exercise submission
* 	This script allows to run an exercise. According to the exercise type, questions
* 	can be on an unique page, or one per page with a Next button.
*
* 	One exercise may contain different types of answers (unique or multiple selection,
* 	matching, fill in blanks, free answer, hot-spot).
*
* 	Questions are selected randomly or not.
*
* 	When the user has answered all questions and clicks on the button "Ok",
* 	it goes to exercise_result.php
*
* 	Notice : This script is also used to show a question before modifying it by
* 	the administrator
*	@package chamilo.exercise
* 	@author Olivier Brouckaert
* 	@author Julio Montoya <gugli100@gmail.com>
* 			Fill in blank option added (2008)
* 			Cleaning exercises (2010),
* 			Adding hotspot delineation support (2011)
* 			Adding reminder + ajax support (2011)
*   Modified by hubert.borderiou (2011-10-21 question category)
*/
/**
 * Code
 */

use \ChamiloSession as Session;

require_once 'exercise.class.php';
require_once 'question.class.php';
require_once 'answer.class.php';

// name of the language file that needs to be included
$language_file = 'exercice';

require_once '../inc/global.inc.php';
$current_course_tool  = TOOL_QUIZ;
require_once 'exercise.lib.php';

$nameTools = get_lang('Quiz');

$this_section = SECTION_COURSES;

if ($debug) { error_log('--- Enter to the exercise_submit.php ---- '); error_log('0. POST variables : '.print_r($_POST,1)); }

// Notice for unauthorized people.
api_protect_course_script(true);

$is_allowedToEdit = api_is_allowed_to_edit(null,true);

if (api_get_setting('show_glossary_in_extra_tools') == 'true') {
    $htmlHeadXtra[] = api_get_js('glossary.js'); //Glossary
    $htmlHeadXtra[] = api_get_js('jquery.   highlight.js'); //highlight
}

$htmlHeadXtra[] = api_get_js('jquery.jsPlumb.all.js');

//This library is necessary for the time control feature
$htmlHeadXtra[]= api_get_css(api_get_path(WEB_LIBRARY_PATH).'javascript/epiclock/stylesheet/jquery.epiclock.css');
$htmlHeadXtra[]= api_get_css(api_get_path(WEB_LIBRARY_PATH).'javascript/epiclock/renderers/minute/epiclock.minute.css');
$htmlHeadXtra[]= api_get_js('epiclock/javascript/jquery.dateformat.min.js');
$htmlHeadXtra[]= api_get_js('epiclock/javascript/jquery.epiclock.min.js');
$htmlHeadXtra[]= api_get_js('epiclock/renderers/minute/epiclock.minute.js');
$htmlHeadXtra[] = api_get_js('d3/jquery.xcolor.js');

$htmlHeadXtra[]= '
<script>

var recycle_icon = "<a href=\'#\' class=\'ui-icon ui-icon-refresh\'>Recycle image</a>";
var trash_icon = "<a href=\'#\' class=\'ui-icon ui-icon-trash\'>Delete image</a>";

function deleteItem($item, $insertHere) {

        if ($insertHere.find(".question_draggable").length > 0) {
            return false;
        }

        $item.fadeOut(function() {
            /*var $list = $( "ul", $trash ).length ?
              $( "ul", $trash ) :
              $( "<ul class=\'gallery ui-helper-reset\'/>" ).appendTo( $trash );*/

            $list =  $( "<div class=\'gallery ui-helper-reset\'/>" ).appendTo($insertHere);
            $item.find( "a.ui-icon-trash" ).remove();

             var droppedId = $item.attr("id");
             var dropedOnId = $insertHere.attr("id");

             var originSelectId = "window_"+droppedId+"_select";

             value = dropedOnId.split("_")[2];

             //console.log(originSelectId);             console.log(value);

             $("#"+originSelectId +" option").filter(function() {
                    return $(this).val() == value;
            }).attr("selected", true);

            //console.log(droppedId);
            //console.log(dropedOnId);

            $item.append( recycle_icon ).appendTo( $list  ).fadeIn(function() {
            //$item.animate({ width: "48px" }).find( "img" ).animate({ height: "36px" });
            });

      });
    }

// Draggable js script in order to use jsplumb
var oldPositionArray;

$(function() {

    var $gallery = $( ".drag_question" );
    var $trash = $( ".droppable" );

    // let the questions items be draggable
    $("li", $gallery).draggable({
        cancel: "a.ui-icon", // clicking an icon wont initiate dragging
        revert: "invalid", // when not dropped, the item will revert back to its initial position
        containment: "document",
        helper: "clone",
        cursor: "move"
    });

    // let the "droppable" be droppable, accepting the questions items
    $trash.droppable({
        accept: ".drag_question > li",
        hoverClass: "ui-state-active",
        //activeClass: "ui-state-highlight",
        drop: function(event, ui) {
                /*
            var dropped = ui.draggable;
            var droppedId = dropped.attr("id");

            var droppedOn = $(this);
            var droppedOnId = droppedOn.attr("id");

            $(this).html(dropped.html());
            $( this ).addClass("ui-state-highlight");
            */
            deleteItem(ui.draggable,  $(this));
        }
    });

    // let the question handler be droppable as well, accepting items from the trash

    $gallery.droppable({
       // accept: ".droppable",
       // activeClass: "custom-state-active",
        hoverClass: "ui-state-active",
        drop: function( event, ui ) {
            recycleItem( ui.draggable, $(this));
        }
    });



    function recycleItem( $item, droppedOn) {
        //console.log("recycleItem");
        $item.fadeOut(function() {
        $item
          .find( "a.ui-icon-refresh" )
            .remove()
          .end()
          .css( "width", "96px")
          //.append( trash_icon )
          .find( "img" )
            .css( "height", "72px" )
          .end()
          .appendTo( $gallery )
          .fadeIn();
        });
        var droppedId = $item.attr("id");
        var originSelectId = "window_"+droppedId+"_select";
        $("#"+originSelectId+" option:first").attr("selected","selected");
    }



    $( "ul.drag_question > li" ).click(function( event ) {
        var $item = $( this ),
            $target = $( event.target );

        if ( $target.is( "a.ui-icon-trash" ) ) {
            deleteItem( $item );
        } else if ( $target.is( "a.ui-icon-zoomin" ) ) {
        } else if ( $target.is( "a.ui-icon-refresh" ) ) {
            recycleItem( $item );
        }

        return false;
    });

    /*$( ".drag_question" ).sortable({

        start: function(e, ui) {
            // Getting old positions into array before sorting
            oldPositionArray = $(this).sortable("toArray");
            ui.item.startPos = ui.item.index();
        },
        stop: function( event, ui ) {
            $(this).data("new_position", $(this).sortable("toArray"));
            var newPositionArray = $(this).sortable("toArray");

            var oldPosition = ui.item.startPos;
            var newPosition = ui.item.index();

            var oldId = "window_"+newPositionArray[oldPosition]+"_select";
            var newId = "window_"+newPositionArray[newPosition]+"_select";

            var counter = 0;
            $.each(newPositionArray, function(index, value){
                newIdValue = "window_"+value+"_select";
                $("#"+newIdValue +" option").filter(function() {
                    return $(this).val() == counter+1;
                }).attr("selected", true);
                counter++;
            });
        }
    });*/
});

// Matching js script in order to use jsplumb
var colorDestination = "#316b31";
var curvinessValue = 0;
var connectorType = "Straight";

;(function() {
    window.jsPlumbDemo = {
	    init : function(questionId) {

	        var windowQuestion = ".window"+ questionId+"_question";
	        var countConnections = $(windowQuestion).size();

            if (countConnections && countConnections > 0) {
                var colorArray = $.xcolor.analogous("#da0", countConnections);
                var colorArrayDestination = $.xcolor.analogous("#51a351", countConnections);
            } else {
                var colorArray = $.xcolor.analogous("#da0", 10);
                var colorArrayDestination = $.xcolor.analogous("#51a351", 10);
            }

	        jsPlumb.importDefaults({
				DragOptions : { cursor: "pointer", zIndex:2000 },
				PaintStyle : { strokeStyle:"#000" },
				EndpointStyle : { strokeStyle:"#316b31" },
				Endpoint : "Rectangle",
				Anchors : ["TopCenter", "TopCenter"]
			});

            var exampleDropOptions = {
                tolerance: "touch",
                hoverClass: "dropHover",
                activeClass: "dragActive"
            };

            var destinationEndPoint = {
                endpoint:["Dot", { radius: 15 }],
                paintStyle:{ fillStyle:colorDestination },
                isSource:false,
                connectorStyle:{ strokeStyle:colorDestination, lineWidth:8 },
                connector: [connectorType, { curviness: curvinessValue } ],
                maxConnections: 1000,
                isTarget:true,
                dropOptions : exampleDropOptions,
                beforeDrop:function(params) {
                    var connections = jsPlumb.getConnections({source: params.sourceId});
                    jsPlumb.select({source:params.sourceId}).each(function(connection) {
                        jsPlumb.detach(connection);
                    });
                    var selectId = params.sourceId + "_select";
                    var value = params.targetId.split("_")[2];

                    $("#" +selectId +" option").filter(function() {
                        return $(this).val() == value;
                    }).attr("selected", true);
                    return true;
                }
            };

            var count = 0;
            var sourceDestinationArray = Array;

            $(windowQuestion).each(function(index) {
                var windowId = $(this).attr("id");
                var scope = windowId + "scope";
                var destinationColor = colorArray[count].getHex();

                var sourceEndPoint = {
                    endpoint:["Dot", { radius:15 }],
                    paintStyle:{ fillStyle: destinationColor },
                    isSource:true,
                    connectorStyle:{ strokeStyle:"#8a8888" , lineWidth:8 },
                    connector: [connectorType, { curviness: curvinessValue } ],
                    maxConnections: 1,
                    isTarget:false,
                    dropOptions : exampleDropOptions,
                    scope: scope
                };
                sourceDestinationArray[count+1] = sourceEndPoint;

                count++;
                jsPlumb.addEndpoint(windowId, { anchor:[ "RightMiddle","RightMiddle","RightMiddle","RightMiddle" ] }, sourceEndPoint);
                var destinationCount = 0;
                $(windowQuestion).each(function( index ) {
                    var windowDestinationId = $(this).attr("id");
                    destinationEndPoint.scope = scope;
                    destinationEndPoint.paintStyle.fillStyle = colorArrayDestination[destinationCount].getHex();
                    destinationCount++;
                    jsPlumb.addEndpoint(windowDestinationId+"_answer", { anchor:[ "LeftMiddle","LeftMiddle","LeftMiddle","LeftMiddle" ]  }, destinationEndPoint);
                });
            });


            //var divsWithWindowClass = jsPlumb.CurrentLibrary.getSelector("#"+questionId+" .window");
			//jsPlumb.draggable(divsWithWindowClass);
			jsPlumbDemo.attachBehaviour();
        }
    }
})();

;(function() {
    var _initialised = false;

    jsPlumbDemo.attachBehaviour = function() {
        if (!_initialised) {
            /*$(".hide").click(function() {
                jsPlumb.toggle($(this).attr("rel"));
            });

            $(".drag").click(function() {
                var s = jsPlumb.toggleDraggable($(this).attr("rel"));
                $(this).html(s ? "disable dragging" : "enable dragging");
                if (!s) $("#" + $(this).attr("rel")).addClass("drag-locked"); else $("#" + $(this).attr("rel")).removeClass("drag-locked");
                $("#" + $(this).attr("rel")).css("cursor", s ? "pointer" : "default");
            });

            $(".detach").click(function() {
                jsPlumb.detachAllConnections($(this).attr("rel"));
            });

            $("#clear").click(function() {
                jsPlumb.detachEveryConnection(); jsPlumbDemo.showConnectionInfo("");
            });*/

            _initialised = true;
        }
    };
})();

jsPlumb.ready(function() {
    if ($(".drag_question").length > 0) {
	    jsPlumbDemo.init();

	     $(document).scroll(function() {
            jsPlumb.repaintEverything();
        });

        $(window).resize(function() {
            jsPlumb.repaintEverything();
        });
	}
});


$(function(){
    $(".highlight_image").on("click", function() {
        $(this).parent().find(".highlight_image").each(function(index){
            $(this).removeClass("highlight_image_selected");
            $(this).addClass("highlight_image_default");
            $(this).find("label").find("input").attr("checked", false);
        });
        $(this).removeClass("highlight_image_default");
        $(this).addClass("highlight_image_selected");
        $(this).find("label").find("input").attr("checked", "checked");
    });
});

</script>';

// General parameters passed via POST/GET

$learnpath_id 			= isset($_REQUEST['learnpath_id']) ? intval($_REQUEST['learnpath_id']) : 0;
$learnpath_item_id 		= isset($_REQUEST['learnpath_item_id']) ? intval($_REQUEST['learnpath_item_id']) : 0;
$learnpath_item_view_id	= isset($_REQUEST['learnpath_item_view_id']) ? intval($_REQUEST['learnpath_item_view_id']) : 0;
$origin 				= isset($_REQUEST['origin']) ? Security::remove_XSS($_REQUEST['origin']) : '';
$reminder 				= isset($_REQUEST['reminder']) ? intval($_REQUEST['reminder']) : 0;
$remind_question_id 	= isset($_REQUEST['remind_question_id']) ? intval($_REQUEST['remind_question_id']) : 0;
$exerciseId				= isset($_REQUEST['exerciseId']) ? intval($_REQUEST['exerciseId']) : 0;

$formSent = isset($_REQUEST['formSent']) ? $_REQUEST['formSent'] : null;
$exerciseResult = isset($_REQUEST['exerciseResult']) ? $_REQUEST['exerciseResult'] : null;
$exerciseResultCoordinates = isset($_REQUEST['exerciseResultCoordinates']) ? $_REQUEST['exerciseResultCoordinates'] : null;

$choice = isset($_REQUEST['choice']) ? $_REQUEST['choice'] : null;
$choice = empty($choice) ? isset($_REQUEST['choice2']) ? $_REQUEST['choice2'] : null : null;

//From submit modal
$current_question = isset($_REQUEST['num']) ? intval($_REQUEST['num']) : null;

//Error message
$error = '';

//Table calls
$exercice_attemp_table = Database :: get_statistic_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);

/*  Teacher takes an exam and want to see a preview, we delete the objExercise from the session in order to get the latest changes in the exercise */
if (api_is_allowed_to_edit(null,true) && isset($_GET['preview']) && $_GET['preview'] == 1 ) {
    Session::erase('objExercise');
}

// 1. Loading the $objExercise variable

if (!isset($_SESSION['objExercise']) || $_SESSION['objExercise']->id != $_REQUEST['exerciseId']) {
    // Construction of Exercise
    $objExercise = new Exercise();
    if ($debug) {error_log('1. Setting the $objExercise variable'); };
    unset($_SESSION['questionList']);

    // if the specified exercise doesn't exist or is disabled
    if (!$objExercise->read($exerciseId) || (!$objExercise->selectStatus() && !$is_allowedToEdit && ($origin != 'learnpath'))) {
    	if ($debug) {error_log('1.1. Error while reading the exercise'); };
        unset ($objExercise);
        $error = get_lang('ExerciseNotFound');
    } else {
        // Saves the object into the session
        Session::write('objExercise',$objExercise);
        if ($debug) {error_log('1.1. $_SESSION[objExercise] was unset - set now - end'); };
    }
}

//2. Checking if $objExercise is set
if (!isset($objExercise) && isset($_SESSION['objExercise'])) {
	if ($debug) { error_log('2. Loading $objExercise from session'); };
    $objExercise = $_SESSION['objExercise'];
}


//3. $objExercise is not set, then return to the exercise list
if (!is_object($objExercise)) {
	if ($debug) {error_log('3. $objExercise was not set, kill the script'); };
    header('Location: exercice.php');
    exit;
}

//if reminder ends we jump to the exercise_reminder
if ($objExercise->review_answers) {
	if ($remind_question_id == -1) {
		header('Location: exercise_reminder.php?origin='.$origin.'&exerciseId='.$exerciseId);
		exit;
	}
}

$current_timestamp 	= time();
$my_remind_list 	= array();

$time_control = false;
if ($objExercise->expired_time != 0) {
	$time_control = true;
}

//Generating the time control key for the user
$current_expired_time_key = get_time_control_key($objExercise->id, $learnpath_id, $learnpath_item_id);

$_SESSION['duration_time'][$current_expired_time_key] = $current_timestamp;

if ($time_control) {
	//Get the expired time of the current exercice in track_e_exercices
	$total_seconds = $objExercise->expired_time*60;
}

$show_clock = true;
$user_id = api_get_user_id();
if ($objExercise->selectAttempts() > 0) {
	$attempt_html = '';
	$attempt_count = get_attempt_count($user_id, $exerciseId, $learnpath_id, $learnpath_item_id, $learnpath_item_view_id);

	if ($attempt_count >= $objExercise->selectAttempts()) {
		$show_clock = false;
		if (!api_is_allowed_to_edit(null,true)) {

			if ($objExercise->results_disabled == 0 && $origin != 'learnpath') {

				//Showing latest attempt according with task BT#1628
				$exercise_stat_info = get_exercise_results_by_user($user_id, $exerciseId, api_get_course_id(), api_get_session_id());

				if (!empty($exercise_stat_info)) {
					$max_exe_id = max(array_keys($exercise_stat_info));
					$last_attempt_info = $exercise_stat_info[$max_exe_id];
					$attempt_html .= Display::div(get_lang('Date').': '.api_get_local_time($last_attempt_info['exe_date']), array('id'=>''));

					$attempt_html .= Display::return_message(sprintf(get_lang('ReachedMaxAttempts'), $exercise_title, $objExercise->selectAttempts()), 'warning', false);

					if (!empty($last_attempt_info['question_list'])) {
						foreach($last_attempt_info['question_list'] as $question_data) {
							$question_id = $question_data['question_id'];
							$marks       = $question_data['marks'];

							$question_info = Question::read($question_id);
							$attempt_html .= Display::div($question_info->question, array('class'=>'question_title'));
							$attempt_html .= Display::div(get_lang('Score').' '.$marks, array('id'=>'question_score'));
						}
					}
					$score =  show_score($last_attempt_info['exe_result'], $last_attempt_info['exe_weighting']);
					$attempt_html .= Display::div(get_lang('YourTotalScore').' '.$score, array('id'=>'question_score'));
				} else {
					$attempt_html .= Display::return_message(sprintf(get_lang('ReachedMaxAttempts'), $exercise_title, $objExercise->selectAttempts()), 'warning', false);
				}
			} else {
				$attempt_html .= Display::return_message(sprintf(get_lang('ReachedMaxAttempts'), $exercise_title, $objExercise->selectAttempts()), 'warning', false);
			}
		} else {
			$attempt_html .= Display :: return_message(sprintf(get_lang('ReachedMaxAttempts'), $exercise_title, $objExercise->selectAttempts()), 'warning', false);
			//Display :: display_warning_message(sprintf(get_lang('ReachedMaxAttemptsAdmin'), $exercise_title, $objExercise->selectAttempts()), false);
		}

		if ($origin == 'learnpath') {
			Display :: display_reduced_header();
		} else {
			Display :: display_header($nameTools,'Exercises');
		}

		echo $attempt_html;
		if ($origin != 'learnpath')
			Display :: display_footer();
		exit;
	}
}


if ($debug) { error_log("4. Setting the exe_id: $exe_id");} ;

//5. Getting user exercise info (if the user took the exam before) - generating exe_id

$exercise_stat_info = $objExercise->get_stat_track_exercise_info($learnpath_id, $learnpath_item_id, $learnpath_item_view_id);

$clock_expired_time = null;
if (empty($exercise_stat_info)) {
    if ($debug)  error_log("learnpath_id: $learnpath_id, learnpath_item_id: $learnpath_item_id, learnpath_item_view_id: $learnpath_item_view_id");
    if ($debug)  error_log('5  $exercise_stat_info is empty ');
	$total_weight = 0;
	$questionList = $objExercise->get_question_list(true);

	foreach ($questionList as $question_id) {
		$objQuestionTmp = Question::read($question_id);
		$total_weight += floatval($objQuestionTmp->weighting);
	}

	if ($time_control) {
		$expected_time = $current_timestamp + $total_seconds;

		if ($debug)  error_log('5.1. $current_timestamp '.$current_timestamp);
		if ($debug)  error_log('5.2. $expected_time '.$expected_time);

		$clock_expired_time 	= api_get_utc_datetime($expected_time);
		if ($debug) error_log('5.3. $expected_time '.$clock_expired_time);

		//Sessions  that contain the expired time
		$_SESSION['expired_time'][$current_expired_time_key] 	 = $clock_expired_time;
		if ($debug) { error_log('5.4. Setting the $_SESSION[expired_time]: '.$_SESSION['expired_time'][$current_expired_time_key] ); };
	}
	$exe_id = $objExercise->save_stat_track_exercise_info($clock_expired_time, $learnpath_id, $learnpath_item_id, $learnpath_item_view_id, $questionList, $total_weight);
	$exercise_stat_info = $objExercise->get_stat_track_exercise_info($learnpath_id, $learnpath_item_id, $learnpath_item_view_id);
    if ($debug)  error_log("5.5  exercise_stat_info[] created. exe_id: $exe_id");
} else {
	$exe_id = $exercise_stat_info['exe_id'];
    if ($debug)  error_log("5  exercise_stat_info[] exists getting exe_id $exe_id ");
}

//Array to check in order to block the chat
create_chat_exercise_session($exe_id);

if ($debug) { error_log('6. $objExercise->get_stat_track_exercise_info function called::  '.print_r($exercise_stat_info, 1)); };

if (!empty($exercise_stat_info['questions_to_check'])) {
	$my_remind_list = $exercise_stat_info['questions_to_check'];
	$my_remind_list = explode(',', $my_remind_list);
	$my_remind_list = array_filter($my_remind_list);
}

$params = "exe_id=$exe_id&exerciseId=$exerciseId&origin=$origin&learnpath_id=$learnpath_id&learnpath_item_id=$learnpath_item_id&learnpath_item_view_id=$learnpath_item_view_id&".api_get_cidreq();
if ($debug) { error_log("6.1 params: ->  $params"); };

if ($reminder == 2 && empty($my_remind_list)) {
    if ($debug) { error_log("6.2 calling the exercise_reminder.php "); };
	header('Location: exercise_reminder.php?'.$params);
	exit;
}

/*
 * 7. Loading Time control parameters
 * If the expired time is major that zero(0) then the expired time is compute on this time.
 */

if ($time_control) {
	if ($debug) error_log('7.1. Time control is enabled');
	if ($debug) error_log('7.2. $current_expired_time_key  '.$current_expired_time_key);
	if ($debug) error_log('7.3. $_SESSION[expired_time][$current_expired_time_key]  '.$_SESSION['expired_time'][$current_expired_time_key]);

    if (!isset($_SESSION['expired_time'][$current_expired_time_key])) {
        //Timer - Get expired_time for a student
        if (!empty($exercise_stat_info)) {
        	if ($debug) {error_log('7.4 Seems that the session ends and the user want to retake the exam'); };
	        $expired_time_of_this_attempt = $exercise_stat_info['expired_time_control'];
			if ($debug) {error_log('7.5 $expired_time_of_this_attempt: '.$expired_time_of_this_attempt); }
	        //Get the last attempt of an exercice
	    	$last_attempt_date = get_last_attempt_date_of_exercise($exercise_stat_info['exe_id']);

	    	//This means that the user enters the exam but do not answer the first question we get the date from the track_e_exercises not from the track_et_attempt see #2069
	    	if (empty($last_attempt_date)) {
	    		$diff = $current_timestamp - api_strtotime($exercise_stat_info['start_date'], 'UTC');
	    		$last_attempt_date = api_get_utc_datetime(api_strtotime($exercise_stat_info['start_date'],'UTC') + $diff);
	    	} else {
	    		//Recalculate the time control due #2069
	    		$diff = $current_timestamp - api_strtotime($last_attempt_date,'UTC');
	    		$last_attempt_date = api_get_utc_datetime(api_strtotime($last_attempt_date,'UTC') + $diff);
	    	}
	        if ($debug) {error_log('7.6. $last_attempt_date: '.$last_attempt_date); }

	        //New expired time - it is due to the possible closure of session
	        $new_expired_time_in_seconds = api_strtotime($expired_time_of_this_attempt, 'UTC') - api_strtotime($last_attempt_date,'UTC');
	        if ($debug) {error_log('7.7. $new_expired_time_in_seconds: '.$new_expired_time_in_seconds); }

	        $expected_time	= $current_timestamp + $new_expired_time_in_seconds;
	        if ($debug) {error_log('7.8. $expected_time1: '.$expected_time); }

	        $clock_expired_time  = api_get_utc_datetime($expected_time);
	        if ($debug) {error_log('7.9. $clock_expired_time: '.$clock_expired_time); }

			// First we update the attempt to today
			// How the expired time is changed into "track_e_exercices" table,then the last attempt for this student should be changed too,so
	        $sql_track_e_exe = "UPDATE $exercice_attemp_table SET tms = '".api_get_utc_datetime()."' WHERE exe_id = '".$exercise_stat_info['exe_id']."' AND tms = '".$last_attempt_date."' ";
	        if ($debug) {error_log('7.10. $sql_track_e_exe2: '.$sql_track_e_exe); }
	        Database::query($sql_track_e_exe);

	        //Sessions  that contain the expired time
	        $_SESSION['expired_time'][$current_expired_time_key] 		= $clock_expired_time;
	        if ($debug) {error_log('7.11. Setting the $_SESSION[expired_time]: '.$_SESSION['expired_time'][$current_expired_time_key] ); };
        }
    } else {
        $clock_expired_time =  $_SESSION['expired_time'][$current_expired_time_key];
    }
} else {
    if ($debug) { error_log("7 No time control"); };
}

// Get time left for exipiring time
$time_left = api_strtotime($clock_expired_time,'UTC') - time();

/*
 * The time control feature is enable here - this feature is enable for a jquery plugin called epiclock
 * for more details of how it works see this link : http://eric.garside.name/docs.html?p=epiclock
 */
if ($time_control) { //Sends the exercice form when the expired time is finished
	$htmlHeadXtra[] = $objExercise->show_time_control_js($time_left);
}

// if the user has submitted the form

$exercise_title	= $objExercise->selectTitle();
$exercise_sound = $objExercise->selectSound();

//Media questions
$media_questions = $objExercise->get_media_list();
$media_is_activated = $objExercise->media_is_activated($media_questions);


//in LP's is enabled the "remember question" feature?

if (!isset($_SESSION['questionList'])) {
    // selects the list of question ID
    $questionList = $objExercise->get_question_list(false);
    //Getting order from random
    if ($media_is_activated == false && $objExercise->isRandom() && !empty($exercise_stat_info['data_tracking'])) {
    	$questionList = explode(',', $exercise_stat_info['data_tracking']);
    }
    Session::write('questionList', $questionList);
    if ($debug > 0) { error_log('$_SESSION[questionList] was set'); }
} else {
	if (isset($objExercise) && isset($_SESSION['objExercise'])) {
        $questionList = Session::read('questionList');
	}
}

if ($debug) error_log('8. Question list loaded '.print_r($questionList, 1));
if ($debug) error_log('8.1 Media list loaded '.print_r($media_questions, 1));

$question_count = $objExercise->get_count_question_list();

if ($formSent && isset($_POST)) {
    if ($debug) { error_log('9. $formSent was set'); }

    // Initializing
    if (!is_array($exerciseResult)) {
        $exerciseResult = array();
        $exerciseResultCoordinates = array();
    }

    //Only for hotspot
    if (!isset($choice) && isset($_REQUEST['hidden_hotspot_id'])) {
        $hotspot_id = (int)($_REQUEST['hidden_hotspot_id']);
        $choice     = array($hotspot_id => '');
    }

    // Filling array exercise result

    // if the user has answered at least one question
    if (is_array($choice)) {
        if ($debug) { error_log('9.1. $choice is an array '.print_r($choice, 1)); }
        // Also store hotspot spots in the session ($exerciseResultCoordinates
        // will be stored in the session at the end of this script)

        if (isset($_POST['hotspot'])) {
            $exerciseResultCoordinates = $_POST['hotspot'];
            if ($debug) { error_log('9.2. $_POST[hotspot] data '.print_r($exerciseResultCoordinates, 1)); }
        }
        if ($objExercise->type == ALL_ON_ONE_PAGE) {
            // $exerciseResult receives the content of the form.
            // Each choice of the student is stored into the array $choice
            $exerciseResult = $choice;
        } else {
            // gets the question ID from $choice. It is the key of the array
            list ($key) = array_keys($choice);
            // if the user didn't already answer this question
            if (!isset($exerciseResult[$key])) {
                // stores the user answer into the array
                $exerciseResult[$key] = $choice[$key];
                //saving each question
                if ($objExercise->feedback_type != EXERCISE_FEEDBACK_TYPE_DIRECT) {
                    $nro_question = $current_question; // - 1;
                 	$questionId   = $key;
                    // gets the student choice for this question
                    $choice = $exerciseResult[$questionId];
                    if (isset($exe_id)) {
                    	//Manage the question and answer attempts
                        if ($debug) { error_log('8.3. manage_answer exe_id: '.$exe_id.' - $questionId: '.$questionId.' Choice'.print_r($choice,1)); }
                    	$objExercise->manage_answer($exe_id, $questionId, $choice,'exercise_show',$exerciseResultCoordinates, true, false,false, $objExercise->propagate_neg);
                    }
                    //END of saving and qualifying
                }
            }
        }
        if ($debug) { error_log('9.3.  $choice is an array - end'); }
        if ($debug) { error_log('9.4.  $exerciseResult '.print_r($exerciseResult,1)); }
    }


    // the script "exercise_result.php" will take the variable $exerciseResult from the session
    Session::write('exerciseResult',$exerciseResult);
    Session::write('remind_list', $remind_list);
    Session::write('exerciseResultCoordinates',$exerciseResultCoordinates);

    // if all questions on one page OR if it is the last question (only for an exercise with one question per page)

    if (($objExercise->type == ALL_ON_ONE_PAGE || $current_question >= $question_count)) {
        if (api_is_allowed_to_session_edit()) {
            // goes to the script that will show the result of the exercise
            if ($objExercise->type == ALL_ON_ONE_PAGE) {
                if ($debug) { error_log('10. Exercise ALL_ON_ONE_PAGE -> Redirecting to exercise_result.php'); }

                //We check if the user attempts before sending to the exercise_result.php
                if ($objExercise->selectAttempts() > 0) {
                    $attempt_count = get_attempt_count(api_get_user_id(), $exerciseId, $learnpath_id, $learnpath_item_id, $learnpath_item_view_id);
                    if ($attempt_count >= $objExercise->selectAttempts()) {
                        Display :: display_warning_message(sprintf(get_lang('ReachedMaxAttempts'), $exercise_title, $objExercise->selectAttempts()), false);
                        if ($origin != 'learnpath') {
                            //so we are not in learnpath tool
                            echo '</div>'; //End glossary div
                            Display :: display_footer();
                        } else {
                            echo '</body></html>';
                        }
                    }
                }
                header("Location: exercise_result.php?".api_get_cidreq()."&exe_id=$exe_id&origin=$origin&learnpath_id=$learnpath_id&learnpath_item_id=$learnpath_item_id&learnpath_item_view_id=$learnpath_item_view_id");
                exit;
            } else {
                //Time control is only enabled for ONE PER PAGE
                if (!empty($exe_id) && is_numeric($exe_id)) {
                    //Verify if the current test is fraudulent
                    if (exercise_time_control_is_valid($exerciseId, $learnpath_id, $learnpath_item_id)) {
                    	$sql_exe_result = "";
                        if ($debug) { error_log('exercise_time_control_is_valid is valid'); }
                    } else {
                    	$sql_exe_result = ", exe_result = 0";
                        if ($debug) { error_log('exercise_time_control_is_valid is NOT valid then exe_result = 0 '); }
                    }
                }
                if ($debug) { error_log('10. Redirecting to exercise_show.php'); }
                header("Location: exercise_result.php?".api_get_cidreq()."&exe_id=$exe_id&origin=$origin&learnpath_id=$learnpath_id&learnpath_item_id=$learnpath_item_id&learnpath_item_view_id=$learnpath_item_view_id");
                exit;
            }
        } else {
            if ($debug) { error_log('10. Redirecting to exercise_submit.php'); }
            header("Location: exercise_submit.php?".api_get_cidreq()."&exerciseId=$exerciseId&origin=$origin");
            exit;
        }
    }
    if ($debug) { error_log('11. $formSent was set - end'); }
} else {
    if ($debug) { error_log('9. $formSent was NOT sent'); }
}

// if questionNum comes from POST and not from GET

if (!$current_question || $_REQUEST['num']) {
    if (!$current_question) {
        $current_question = 1;
    } else {
        $current_question++;
    }
}

if ($question_count != 0) {
	if (($objExercise->type == ALL_ON_ONE_PAGE || $current_question > $question_count)) {
	    if (api_is_allowed_to_session_edit()) {
	        // goes to the script that will show the result of the exercise
	        if ($objExercise->type == ALL_ON_ONE_PAGE) {
	            if ($debug) { error_log('12. Exercise ALL_ON_ONE_PAGE -> Redirecting to exercise_result.php'); }

	            //We check if the user attempts before sending to the exercise_result.php
	            if ($objExercise->selectAttempts() > 0) {
	                $attempt_count = get_attempt_count(api_get_user_id(), $exerciseId, $learnpath_id, $learnpath_item_id, $learnpath_item_view_id);
	                if ($attempt_count >= $objExercise->selectAttempts()) {
	                    Display :: display_warning_message(sprintf(get_lang('ReachedMaxAttempts'), $exercise_title, $objExercise->selectAttempts()), false);
	                    if ($origin != 'learnpath') {
	                        //so we are not in learnpath tool
	                        echo '</div>'; //End glossary div
	                        Display :: display_footer();
	                    } else {
	                        echo '</body></html>';
	                    }
	                    exit;
	                }
	            }
	        } else {
	            //Time control is only enabled for ONE PER PAGE
	            if (!empty($exe_id) && is_numeric($exe_id)) {
	                //Verify if the current test is fraudulent
	            	$check = exercise_time_control_is_valid($exerciseId, $learnpath_id, $learnpath_item_id);

	                if ($check) {
	                	$sql_exe_result = "";
	                    if ($debug) { error_log('12. exercise_time_control_is_valid is valid'); }
	                } else {
	                	$sql_exe_result = ", exe_result = 0";
	                    if ($debug) { error_log('12. exercise_time_control_is_valid is NOT valid then exe_result = 0 '); }
	                }
	            }
	            if ($objExercise->review_answers) {
	            	header('Location: exercise_reminder.php?'.$params);
	            	exit;
	            } else {
	            	header("Location: exercise_result.php?".api_get_cidreq()."&exe_id=$exe_id&origin=$origin&learnpath_id=$learnpath_id&learnpath_item_id=$learnpath_item_id&learnpath_item_view_id=$learnpath_item_view_id");
                    exit;
	            }
	        }
	    } else {
	        if ($debug) { error_log('Redirecting to exercise_submit.php'); }
	        exit;
	    }
	}
} else {
	$error = get_lang('ThereAreNoQuestionsForThisExercise');
	// if we are in the case where user select random by category, but didn't choose the number of random question
	if ($objExercise->selectRandomByCat() > 0 && $objExercise->random <= 0) {
		$error .= "<br/>".get_lang('PleaseSelectSomeRandomQuestion');
	}
}

if (!empty ($_GET['gradebook']) && $_GET['gradebook'] == 'view') {
    $_SESSION['gradebook'] = Security :: remove_XSS($_GET['gradebook']);
    $gradebook = $_SESSION['gradebook'];
} elseif (empty ($_GET['gradebook'])) {
    unset ($_SESSION['gradebook']);
    $gradebook = '';
}

if (!empty ($gradebook) && $gradebook == 'view') {
    $interbreadcrumb[]= array ('url' => '../gradebook/' . Security::remove_XSS($_SESSION['gradebook_dest']),'name' => get_lang('ToolGradebook'));
}

$interbreadcrumb[]= array ("url" => "exercice.php?gradebook=$gradebook",	"name" => get_lang('Exercices'));
$interbreadcrumb[]= array ("url" => "#","name" => $objExercise->name);

if ($origin != 'learnpath') { //so we are not in learnpath tool
    Display :: display_header($nameTools,'Exercises');
    if (!api_is_allowed_to_session_edit()) {
        Display :: display_warning_message(get_lang('SessionIsReadOnly'));
    }
} else {
    Display::display_reduced_header();
    echo '<div style="height:10px">&nbsp;</div>';
}

$show_quiz_edition = $objExercise->added_in_lp();

// I'm in a preview mode
if (api_is_course_admin() && $origin != 'learnpath') {
    echo '<div class="actions">';
    if ($show_quiz_edition == false) {
    	echo '<a href="exercise_admin.php?' . api_get_cidreq() . '&modifyExercise=yes&exerciseId=' . $objExercise->id . '">'.Display :: return_icon('settings.png', get_lang('ModifyExercise'),'',ICON_SIZE_MEDIUM).'</a>';
    } else {
    	echo '<a href="#">'.Display::return_icon('settings_na.png', get_lang('ModifyExercise'),'',ICON_SIZE_MEDIUM).'</a>';
    }
    echo '</div>';
}

$is_visible_return = $objExercise->is_visible($learnpath_id, $learnpath_item_id, $learnpath_item_view_id);
if ($is_visible_return['value'] == false) {
    echo $is_visible_return['message'];
    if ($origin != 'learnpath') {
        Display :: display_footer();
    }
    exit;
}

$limit_time_exists = (($objExercise->start_time != '0000-00-00 00:00:00') || ($objExercise->end_time != '0000-00-00 00:00:00')) ? true : false;

if ($limit_time_exists) {
    $exercise_start_time 	= api_strtotime($objExercise->start_time, 'UTC');
    $exercise_end_time 		= api_strtotime($objExercise->end_time, 'UTC');
    $time_now 				= time();

    if ($objExercise->start_time != '0000-00-00 00:00:00') {
        $permission_to_start = (($time_now - $exercise_start_time) > 0) ? true : false;
    } else {
        $permission_to_start = true;
    }
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        if ($objExercise->end_time != '0000-00-00 00:00:00') {
            $exercise_timeover = (($time_now - $exercise_end_time) > 0) ? true : false;
        } else {
            $exercise_timeover = false;
        }
    }

    if (!$permission_to_start || $exercise_timeover) {
        if (!api_is_allowed_to_edit(null,true)) {
            $message_warning = $permission_to_start ? get_lang('ReachedTimeLimit') : get_lang('ExerciseNoStartedYet');
            Display :: display_warning_message(sprintf($message_warning, $exercise_title, $objExercise->selectAttempts()));
            if ($origin != 'learnpath') {
            	Display :: display_footer();
            }
            exit;
        } else {
            $message_warning = $permission_to_start ? get_lang('ReachedTimeLimitAdmin') : get_lang('ExerciseNoStartedAdmin');
            Display :: display_warning_message(sprintf($message_warning, $exercise_title, $objExercise->selectAttempts()));
        }
    }
}

// Blocking empty start times see BT#2800
global $_custom;
if (isset($_custom['exercises_hidden_when_no_start_date']) && $_custom['exercises_hidden_when_no_start_date']) {
	if (empty($objExercise->start_time) || $objExercise->start_time == '0000-00-00 00:00:00') {
		Display :: display_warning_message(sprintf(get_lang('ExerciseNoStartedYet'), $exercise_title, $objExercise->selectAttempts()));
		if ($origin != 'learnpath') {
			Display :: display_footer();
		}
	}
}

//Timer control
if ($time_control) {
    echo $objExercise->return_time_left_div();
	echo '<div style="display:none" class="warning-message" id="expired-message-id">'.get_lang('ExerciceExpiredTimeMessage').'</div>';
}


if (!empty($objExercise->description)) {
    echo "<script>
        $(function() {
            $('#description_content').accordion({
                changestart: function(event, ui) {
                    //var clicked = $(this).find('.ui-state-active').attr('id');
                    //$('#'+clicked).load('/widgets/'+clicked);
                    $('#collapse1').html('".addslashes($objExercise->description)."');
                }
            });
         });
        </script>";
    echo Display::generate_accordion(array(array('title' => get_lang('ExerciseDescriptionLabel'), 'content' => null)), 'jquery', 'description_content');
}

if ($origin != 'learnpath') {
   echo '<div id="highlight-plugin" class="glossary-content">';
}

if ($reminder == 2)  {
    if ($debug) { error_log(' $reminder == 2'); }

    $data_tracking  = $exercise_stat_info['data_tracking'];
    $data_tracking  = explode(',', $data_tracking);

    $current_question = 1; //set by default the 1st question

    if (!empty($my_remind_list)) {
    	//Checking which questions we are going to call from the remind list
		for ($i = 0; $i < count($data_tracking); $i++) {
			for($j = 0; $j < count($my_remind_list); $j++) {

				if (!empty($remind_question_id)) {
					if ($remind_question_id == $my_remind_list[$j]) {

			        	if ($remind_question_id == $data_tracking[$i]) {
			        		if (isset($my_remind_list[$j+1])) {
			        			$remind_question_id = $my_remind_list[$j+1];
			        			$current_question = $i + 1;
			        		} else {
			        			$remind_question_id = -1; //We end the remind list we go to the exercise_reminder.php please
			        			$current_question = $i + 1; // last question
			        		}
			        		break 2;
			            }
					}
				} else {
					if ($my_remind_list[$j] == $data_tracking[$i]) {
						if (isset($my_remind_list[$j+1])) {
							$remind_question_id = $my_remind_list[$j+1];
							$current_question = $i + 1; // last question
						} else {
							$remind_question_id = -1; //We end the remind list we go to the exercise_reminder.php please
							$current_question = $i + 1; // last question
						}
						break 2;
					}
				}
			}
        }
    } else {
    	if ($objExercise->review_answers) {
            if ($debug) { error_log('. redirecting to exercise_reminder.php '); }
	    	header("Location: exercise_reminder.php?$params");
	    	exit;
    	}
    }
}

if ($objExercise->review_answers) {
	$script_php = 'exercise_reminder.php';
} else {
	$script_php = 'exercise_result.php';
}

if (!empty($error)) {
    Display :: display_error_message($error, false);
} else {
    if (!empty ($exercise_sound)) {
        echo "<a href=\"../document/download.php?doc_url=%2Faudio%2F" . Security::remove_XSS($exercise_sound) . "\" target=\"_blank\">", "<img src=\"../img/sound.gif\" border=\"0\" align=\"absmiddle\" alt=", get_lang('Sound') . "\" /></a>";
    }
    // Get number of hotspot questions for javascript validation
    $number_of_hotspot_questions = 0;
    $onsubmit = '';
    $i = 0;

    if (!empty($questionList)) {
        foreach ($questionList as $questionId) {
            $i++;
            $objQuestionTmp = Question::read($questionId);
            // for sequential exercises
            if ($objExercise->type == ONE_PER_PAGE) {
                // if it is not the right question, goes to the next loop iteration
                if ($current_question != $i) {
                    continue;
                } else {
                    if ($objQuestionTmp->selectType() == HOT_SPOT || $objQuestionTmp->selectType() == HOT_SPOT_DELINEATION) {
                        $number_of_hotspot_questions++;
                    }
                    break;
                }
            } else {
                if ($objQuestionTmp->selectType() == HOT_SPOT || $objQuestionTmp->selectType() == HOT_SPOT_DELINEATION) {
                    $number_of_hotspot_questions++;
                }
            }
        }
    }

    if ($number_of_hotspot_questions > 0) {
        $onsubmit = "onsubmit=\"return validateFlashVar('" . $number_of_hotspot_questions . "', '" . get_lang('HotspotValidateError1') . "', '" . get_lang('HotspotValidateError2') . "');\"";
    }

    echo '<script>
            $(function() {
    			//$(".exercise_save_now_button").hide();
    		    $(".main_question").mouseover(function() {
    		    	//$(this).find(".exercise_save_now_button").show();
    		    	//$(this).addClass("question_highlight");
                });

                $(".main_question").mouseout(function() {
                	//$(this).find(".exercise_save_now_button").hide();
                	$(this).removeClass("question_highlight");
                });

                $(".no_remind_highlight").hide();
    		});

			function previous_question(question_num) {
				url = "exercise_submit.php?'.$params.'&num="+question_num;
				window.location = url;
			}

            function previous_question_and_save(previous_question_id, question_id_to_save) {
                url = "exercise_submit.php?'.$params.'&num="+previous_question_id;
                //Save the current question
                save_now(question_id_to_save, url);
            }

            function save_question_list(question_list) {
                $.each(question_list, function(key, question_id) {
                    save_now(question_id, null, false);
                });

                var url = "";
                if ('.$reminder.' == 1 ) {
                    url = "exercise_reminder.php?'.$params.'&num='.$current_question.'";
                } else if ('.$reminder.' == 2 ) {
                    url = "exercise_submit.php?'.$params.'&num='.$current_question.'&remind_question_id='.$remind_question_id.'&reminder=2";
                } else {
                    url = "exercise_submit.php?'.$params.'&num='.$current_question.'&remind_question_id='.$remind_question_id.'";
                }
                //$("#save_for_now_"+question_id).html("'.addslashes(Display::return_icon('save.png', get_lang('Saved'), array(), ICON_SIZE_SMALL)).'");
                window.location = url;
            }

            function save_now(question_id, url_extra, redirect) {
                if (redirect == undefined) {
                    redirect = true;
                }

           		//1. Normal choice inputs
           		var my_choice = $(\'*[name*="choice[\'+question_id+\']"]\').serialize();

           		//2. Reminder checkbox
           		var remind_list = $(\'*[name*="remind_list"]\').serialize();

           		//3. Hotspots
           		var hotspot = $(\'*[name*="hotspot[\'+question_id+\']"]\').serialize();

           		//Checking FCK
           		if (typeof(FCKeditorAPI) !== "undefined") {
    				var oEditor = FCKeditorAPI.GetInstance("choice["+question_id+"]") ;
    				var fck_content = "";
    				if (oEditor) {
               			fck_content = oEditor.GetHTML();
               			my_choice = {};
               			my_choice["choice["+question_id+"]"] = fck_content;
               			my_choice = $.param(my_choice);
               		}
                }

                if ($(\'input[name="remind_list[\'+question_id+\']"]\').is(\':checked\')) {
                	$("#question_div_"+question_id).addClass("remind_highlight");
                } else {
                	$("#question_div_"+question_id).removeClass("remind_highlight");
                }

           		// Only for the first time

          		$("#save_for_now_"+question_id).html("  '.addslashes(Display::return_icon('loading1.gif')).'");

                $.ajax({
                    type:"post",
                    async: false,
                    url: "'.api_get_path(WEB_AJAX_PATH).'exercise.ajax.php?a=save_exercise_by_now",
                    data: "'.$params.'&type=simple&question_id="+question_id+"&"+my_choice+"&"+hotspot+"&"+remind_list,
                    success: function(return_value) {
                        if (return_value == "ok") {
                            $("#save_for_now_"+question_id).html("'.addslashes(Display::return_icon('save.png', get_lang('Saved'), array(), ICON_SIZE_SMALL)).'");
                        } else if (return_value == "error") {
                            $("#save_for_now_"+question_id).html("'.addslashes(Display::return_icon('error.png', get_lang('Error'), array(), ICON_SIZE_SMALL)).'");
                        } else if (return_value == "one_per_page") {

                            var url = "";
                            if ('.$reminder.' == 1 ) {
                                url = "exercise_reminder.php?'.$params.'&num='.$current_question.'";
                            } else if ('.$reminder.' == 2 ) {
                                url = "exercise_submit.php?'.$params.'&num='.$current_question.'&remind_question_id='.$remind_question_id.'&reminder=2";
                            } else {
                                url = "exercise_submit.php?'.$params.'&num='.$current_question.'&remind_question_id='.$remind_question_id.'";
                            }

                            if (url_extra) {
                                url = url_extra;
                            }

                            $("#save_for_now_"+question_id).html("'.addslashes(Display::return_icon('save.png', get_lang('Saved'), array(), ICON_SIZE_SMALL)).'");

                            if (redirect) {
                                window.location = url;
                            }
                        }
                    },
                    error: function() {
                        $("#save_for_now_"+question_id).html("'.addslashes(Display::return_icon('error.png', get_lang('Error'), array(), ICON_SIZE_SMALL)).'");
                    }
                    });
                return false;
            }

            function save_now_all(validate) {
            	//1. Input choice
           		var my_choice = $(\'*[name*="choice"]\').serialize();

           		//2. Reminder
           		var remind_list = $(\'*[name*="remind_list"]\').serialize();

           		//3. Hotspots
           		var hotspot = $(\'*[name*="hotspot"]\').serialize();

           		//Question list
           		var question_list = ['.implode(',', $questionList).'];

           		var free_answers = {};

           		$.each(question_list, function(index, my_question_id) {
           			//Checking FCK
           			if (typeof(FCKeditorAPI) !== "undefined") {
               			var oEditor = FCKeditorAPI.GetInstance("choice["+my_question_id+"]") ;
        				var fck_content = "";
        				if (oEditor) {
                   			fck_content = oEditor.GetHTML();
                   			//alert(index + "  " +my_question_id + " " +fck_content);
                   		 	free_answers["free_choice["+my_question_id+"]"] = fck_content;
                   		}
               		}
           		});

           		//lok+(fgt)= data base
           		free_answers = $.param(free_answers);

          		$("#save_all_reponse").html("'.addslashes(Display::return_icon('loading1.gif')).'");

                $.ajax({
                    type:"post",
                    async: false,
                    url: "'.api_get_path(WEB_AJAX_PATH).'exercise.ajax.php?a=save_exercise_by_now",
                    data: "'.$params.'&type=all&"+my_choice+"&"+hotspot+"&"+free_answers+"&"+remind_list,
                    success: function(return_value) {
                    	if (return_value == "ok") {
                    		//$("#save_all_reponse").html("'.addslashes(Display::return_icon('accept.png')).'");
                    		if (validate == "validate") {
                            	window.location = "'.$script_php.'?'.$params.'";
                            } else {
                            	$("#save_all_reponse").html("'.addslashes(Display::return_icon('accept.png')).'");
                            }
                    	} else {
                        	$("#save_all_reponse").html("'.addslashes(Display::return_icon('wrong.gif')).'");
                        }
                    }
                });
                return false;
            }

            function validate_all() {
   				save_now_all("validate");
                return false;
            }
		</script>';

    echo '<form id="exercise_form" method="post" action="'.api_get_self().'?'.api_get_cidreq().'&autocomplete=off&gradebook='.$gradebook."&exerciseId=" . $exerciseId .'" name="frm_exercise" '.$onsubmit.'>
         <input type="hidden" name="formSent"				value="1" />
         <input type="hidden" name="exerciseId" 			value="'.$exerciseId . '" />
         <input type="hidden" name="num" 					value="'.$current_question.'" id="num_current_id" />
         <input type="hidden" name="exe_id" 				value="'.$exe_id . '" />
         <input type="hidden" name="origin" 				value="'.$origin . '" />
         <input type="hidden" name="learnpath_id" 			value="'.$learnpath_id . '" />
         <input type="hidden" name="learnpath_item_id" 		value="'.$learnpath_item_id . '" />
         <input type="hidden" name="learnpath_item_view_id" value="'.$learnpath_item_view_id . '" />';

	//Show list of questions
    $i = 1;

    $attempt_list = array();
    if (isset($exe_id)) {
        $attempt_list = get_all_exercise_event_by_exe_id($exe_id);
    }
    $remind_list  = array();
    if (isset($exercise_stat_info['questions_to_check']) && !empty($exercise_stat_info['questions_to_check'])) {
        $remind_list = explode(',', $exercise_stat_info['questions_to_check']);
    }
    render_question_list($objExercise, $questionList, $current_question, $exerciseResult, $attempt_list, $remind_list, $media_questions);
    echo '</form>';
}

function render_question_list($objExercise, $questionList, $current_question, $exerciseResult, $attempt_list, $remind_list, $media_questions = array()) {
    global $origin;

    $i = 1;
    //Normal question list render
    foreach ($questionList as $questionId) {
        // for sequential exercises
        if ($objExercise->type == ONE_PER_PAGE) {
            // if it is not the right question, goes to the next loop iteration
            if ($current_question != $i) {
                $i++;
                continue;
            } else {
                if ($objExercise->feedback_type != EXERCISE_FEEDBACK_TYPE_DIRECT) {
                    // if the user has already answered this question
                    if (isset($exerciseResult[$questionId])) {
                        // construction of the Question object
                        $objQuestionTmp = Question::read($questionId);
                        // destruction of the Question object
                        unset ($objQuestionTmp);
                        Display :: display_normal_message(get_lang('AlreadyAnswered'));
                        $i++;
                        break;
                    }
                }
            }
        }

        //Medias question render
        if (isset($media_questions) && !empty($media_questions) && isset($media_questions[$questionId])) {
            $media_question_list = $media_questions[$questionId];
            $objQuestionTmp = Question::read($questionId);

            $counter = 1;
            if ($objQuestionTmp->type == MEDIA_QUESTION) {
                echo $objQuestionTmp->show_media_content();

                $count_of_questions_inside_media = count($media_question_list);

                //Show questions that belongs to a media
                if (!empty($media_question_list)) {
                    foreach ($media_question_list as $my_question_id) {
                        if ($counter == $count_of_questions_inside_media) {
                            $last_question_in_media = true;
                        }
                        render_question($objExercise, $my_question_id, $attempt_list, $remind_list, $i, $current_question, $media_question_list, $last_question_in_media);
                        $counter++;
                    }
                }
            } else {
                render_question($objExercise, $questionId, $attempt_list, $remind_list, $i, $current_question);
            }
        } else {
            //Normal question render
            render_question($objExercise, $questionId, $attempt_list, $remind_list, $i, $current_question);
        }

        $i++;
        // for sequential exercises
        if ($objExercise->type == ONE_PER_PAGE) {
            // quits the loop
            break;
        }
    }

    // end foreach()
    if ($objExercise->type == ALL_ON_ONE_PAGE) {
        $exercise_actions =  $objExercise->show_button($questionId, $current_question);
        echo Display::div($exercise_actions, array('class'=>'exercise_actions'));
    }
}

function render_question($objExercise, $questionId, $attempt_list, $remind_list, $i, $current_question, $questions_in_media = array(), $last_question_in_media = false) {
    global $origin;
    $user_choice = isset($attempt_list[$questionId]) ? $attempt_list[$questionId] : null;

    $remind_highlight = null;

    //Hides questions when reviewing a ALL_ON_ONE_PAGE exercise see #4542 no_remind_highlight class hide with jquery
    if ($objExercise->type == ALL_ON_ONE_PAGE && isset($_GET['reminder']) && $_GET['reminder'] == 2) {
        $remind_highlight = 'no_remind_highlight';
    }

    $is_remind_on = false;

    $attributes = array('id' =>'remind_list['.$questionId.']');

    if (in_array($questionId, $remind_list)) {
        $is_remind_on = true;
        $attributes['checked'] = 1;
        $remind_highlight = ' remind_highlight ';
    }

    //Showing the question

    $exercise_actions  = null;

    echo '<div id="question_div_'.$questionId.'" class="main_question '.$remind_highlight.'" >';

        //Shows the question and possible answers

        showQuestion($questionId, false, $origin, $i, true, false, $user_choice, false);

        //Button save and continue
        switch ($objExercise->type) {
            case ONE_PER_PAGE:
                $exercise_actions .= $objExercise->show_button($questionId, $current_question);
                break;
            case ALL_ON_ONE_PAGE :
                $button  = '<a href="javascript://" class="btn" onclick="save_now(\''.$questionId.'\'); ">'.get_lang('SaveForNow').'</a>';
                $button .= '<span id="save_for_now_'.$questionId.'" class="exercise_save_mini_message"></span>&nbsp;';
                $exercise_actions  .= Display::div($button, array('class'=>'exercise_save_now_button'));
                break;
        }

        if (!empty($questions_in_media)) {
            /*$button  = '<a href="javascript://" class="btn" onclick="save_now(\''.$questionId.'\'); ">'.get_lang('SaveForNow').'</a>';
            $button .= '<span id="save_for_now_'.$questionId.'"></span>&nbsp;';
            $exercise_actions  = Display::div($button, array('class'=>'exercise_save_now_button'));
            $exercise_actions .= $objExercise->show_button($questionId, $current_question);*/
            $count_of_questions_inside_media = count($questions_in_media);
            if ($count_of_questions_inside_media > 1) {
                $button  = '<a href="javascript://" class="btn" onclick="save_now(\''.$questionId.'\', false, false); ">'.get_lang('SaveForNow').'</a>';
                $button .= '<span id="save_for_now_'.$questionId.'" class="exercise_save_mini_message"></span>&nbsp;';
                $exercise_actions  = Display::div($button, array('class'=>'exercise_save_now_button'));
            }

            if ($last_question_in_media) {
                $exercise_actions = $objExercise->show_button($questionId, $current_question, $questions_in_media);
            }
        }

        //Checkbox review answers
        if ($objExercise->review_answers) {
            $remind_question_div = Display::tag('label', Display::input('checkbox', 'remind_list['.$questionId.']', '', $attributes).get_lang('ReviewQuestionLater'), array('class' => 'checkbox', 'for' =>'remind_list['.$questionId.']'));
            $exercise_actions   .= Display::div($remind_question_div, array('class'=>'exercise_save_now_button'));
        }
        echo Display::div(' ', array('class'=>'clear'));
        echo Display::div($exercise_actions, array('class'=>'form-actions'));
    echo '</div>';
}

if ($origin != 'learnpath') {
    //so we are not in learnpath tool
    echo '</div>'; //End glossary div
    Display :: display_footer();
} else {
    echo '</body></html>';
}
