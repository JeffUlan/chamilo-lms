<?php
/* For licensing terms, see /license.txt */
/**
 *     Exercise administration
 *     This script allows to manage (create, modify) an exercise and its questions
 *
 *      Following scripts are includes for a best code understanding :
 *
 *     - exercise.class.php : for the creation of an Exercise object
 *     - question.class.php : for the creation of a Question object
 *     - answer.class.php : for the creation of an Answer object
 *     - exercise.lib.php : functions used in the exercise tool
 *     - exercise_admin.inc.php : management of the exercise
 *     - question_admin.inc.php : management of a question (statement & answers)
 *     - statement_admin.inc.php : management of a statement
 *     - answer_admin.inc.php : management of answers
 *     - question_list_admin.inc.php : management of the question list
 *
 *     Main variables used in this script :
 *
 *     - $is_allowedToEdit : set to 1 if the user is allowed to manage the exercise
 *     - $objExercise : exercise object
 *     - $objQuestion : question object
 *     - $objAnswer : answer object
 *     - $aType : array with answer types
 *     - $exerciseId : the exercise ID
 *     - $picturePath : the path of question pictures
 *     - $newQuestion : ask to create a new question
 *     - $modifyQuestion : ID of the question to modify
 *     - $editQuestion : ID of the question to edit
 *     - $submitQuestion : ask to save question modifications
 *     - $cancelQuestion : ask to cancel question modifications
 *     - $deleteQuestion : ID of the question to delete
 *     - $moveUp : ID of the question to move up
 *     - $moveDown : ID of the question to move down
 *     - $modifyExercise : ID of the exercise to modify
 *     - $submitExercise : ask to save exercise modifications
 *     - $cancelExercise : ask to cancel exercise modifications
 *     - $modifyAnswers : ID of the question which we want to modify answers for
 *     - $cancelAnswers : ask to cancel answer modifications
 *     - $buttonBack : ask to go back to the previous page in answers of type "Fill in blanks"
 *
 * @package chamilo.exercise
 * @author Olivier Brouckaert
 * Modified by Hubert Borderiou 21-10-2011 Question by category
 */

/**
 * Code
 */
use \ChamiloSession as Session;

require_once 'exercise.class.php';
require_once 'question.class.php';
require_once 'answer.class.php';

// Name of the language file that needs to be included
$language_file = 'exercice';

require_once '../inc/global.inc.php';
$urlMainExercise = api_get_path(WEB_CODE_PATH).'exercice/';

$current_course_tool = TOOL_QUIZ;
$this_section = SECTION_COURSES;

// Access control
api_protect_course_script(true);

$is_allowedToEdit = api_is_allowed_to_edit(null, true);

if (!$is_allowedToEdit) {
    api_not_allowed(true);
}

/*  stripslashes POST data  */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST as $key => $val) {
        if (is_string($val)) {
            $_POST[$key] = stripslashes($val);
        } elseif (is_array($val)) {
            foreach ($val as $key2 => $val2) {
                $_POST[$key][$key2] = stripslashes($val2);
            }
        }
        $GLOBALS[$key] = $_POST[$key];
    }
}

// Get vars from GET
if (empty($exerciseId)) {
    $exerciseId = isset($_GET['exerciseId']) ? intval($_GET['exerciseId']):'0';
}
if (empty($newQuestion)) {
    $newQuestion = isset($_GET['newQuestion']) ? $_GET['newQuestion'] : 0;
}
if (empty($modifyAnswers)) {
    $modifyAnswers = isset($_GET['modifyAnswers']) ? $_GET['modifyAnswers'] : 0;
}
if (empty($editQuestion)) {
    $editQuestion = isset($_GET['editQuestion']) ? $_GET['editQuestion'] : 0;
}
if (empty($modifyQuestion)) {
    $modifyQuestion = isset($_GET['modifyQuestion']) ? $_GET['modifyQuestion'] : 0;
}
if (empty($deleteQuestion)) {
    $deleteQuestion = isset($_GET['deleteQuestion']) ? $_GET['deleteQuestion'] : 0;
}
$clone_question = isset($_REQUEST['clone_question']) ? $_REQUEST['clone_question'] : 0;
if (empty($questionId)) {
    $questionId = isset($_SESSION['questionId']) ? $_SESSION['questionId'] : 0;
}

/* Cleaning all incomplete attempts of the admin/teacher to avoid weird problems
    when changing the exercise settings, number of questions, etc */

delete_all_incomplete_attempts(api_get_user_id(), $exerciseId, api_get_course_int_id(), api_get_session_id());

/** @var Exercise $objExercise */
$objExercise = Session::read('objExercise');
/** @var Question $objQuestion */
$objQuestion = Session::read('objQuestion');
/** @var Answer $objAnswer */
$objAnswer = Session::read('objAnswer');

// document path
$documentPath = api_get_path(SYS_COURSE_PATH).$_course['path'].'/document';

// picture path
$picturePath = $documentPath.'/images';

// audio path
$audioPath = $documentPath.'/audio';

// the 5 types of answers
$aType = array(
    get_lang('UniqueSelect'),
    get_lang('MultipleSelect'),
    get_lang('FillBlanks'),
    get_lang('Matching'),
    get_lang('FreeAnswer')
);

// tables used in the exercise tool

if (!empty($_GET['action']) && $_GET['action'] == 'exportqti2' && !empty($_GET['questionId'])) {
    require_once 'export/qti2/qti2_export.php';
    $export = export_question($_GET['questionId'], true);
    $qid = (int)$_GET['questionId'];
    $archive_path = api_get_path(SYS_ARCHIVE_PATH);
    $temp_dir_short = uniqid();
    $temp_zip_dir = $archive_path."/".$temp_dir_short;
    if (!is_dir($temp_zip_dir)) {
        mkdir($temp_zip_dir, api_get_permissions_for_new_directories());
    }
    $temp_zip_file = $temp_zip_dir."/".api_get_unique_id().".zip";
    $temp_xml_file = $temp_zip_dir."/qti2export_".$qid.'.xml';
    file_put_contents($temp_xml_file, $export);
    $zip_folder = new PclZip($temp_zip_file);
    $zip_folder->add($temp_xml_file, PCLZIP_OPT_REMOVE_ALL_PATH);
    $name = 'qti2_export_'.$qid.'.zip';

    DocumentManager::file_send_for_download($temp_zip_file, true, $name);
    unlink($temp_zip_file);
    unlink($temp_xml_file);
    rmdir($temp_zip_dir);
    //DocumentManager::string_send_for_download($export,true,'qti2export_q'.$_GET['questionId'].'.xml');
    exit; //otherwise following clicks may become buggy
}

// Initializes the Exercise object.
if (!is_object($objExercise)) {
    // construction of the Exercise object
    $objExercise = new Exercise();
    // creation of a new exercise if wrong or not specified exercise ID
    if ($exerciseId) {
        $objExercise->read($exerciseId, true);
    }
    // saves the object into the session
    Session::write('objExercise', $objExercise);
}

if ($objExercise->fastEdition) {
    $htmlHeadXtra[] = api_get_jqgrid_js();
}

$nbrQuestions = $objExercise->getQuestionCount();

// Initializes the Question object
if ($editQuestion || $newQuestion || $modifyQuestion || $modifyAnswers) {
    if ($editQuestion || $newQuestion) {

        // reads question data
        if ($editQuestion) {
            // question not found
            if (!$objQuestion = Question::read($editQuestion, null, $objExercise)) {
                api_not_allowed();
            }
            // saves the object into the session
            Session::write('objQuestion', $objQuestion);
        }
    }

    // Checks if the object exists.
    if (is_object($objQuestion)) {
        // gets the question ID
        $questionId = $objQuestion->selectId();
    }
}

// If cancelling an exercise.
if (!empty($cancelExercise)) {
    // existing exercise
    if ($exerciseId) {
        unset($modifyExercise);
    } else {
        // new exercise
        // goes back to the exercise list
        header('Location: '.$urlMainExercise.'exercice.php');
        exit();
    }
}

// if cancelling question creation/modification
if (!empty($cancelQuestion)) {
    // if we are creating a new question from the question pool
    if (!$exerciseId && !$questionId) {
        // goes back to the question pool
        header('Location: '.$urlMainExercise.'question_pool.php');
        exit();
    } else {
        // goes back to the question viewing
        $editQuestion = $modifyQuestion;
        unset($newQuestion, $modifyQuestion);
    }
}

if (!empty($clone_question) && !empty($objExercise->id)) {

    $old_question_obj = Question::read($clone_question, api_get_course_int_id());
    $old_question_obj->question = $old_question_obj->question.' - '.get_lang('Copy');

    $new_id = $old_question_obj->duplicate();
    $new_question_obj = Question::read($new_id, api_get_course_int_id());
    $new_question_obj->addToList($objExercise->id);

    // This should be moved to the duplicate function
    $new_answer_obj = new Answer($clone_question, null, $objExercise);
    $new_answer_obj->read();
    $new_answer_obj->duplicate($new_id);

    // Reloading tne $objExercise obj
    $objExercise->read($objExercise->id);

    header('Location: '.$urlMainExercise.'admin.php?'.api_get_cidreq().'&exerciseId='.$objExercise->id);
    exit;
}

// if cancelling answer creation/modification
if (!empty($cancelAnswers)) {
    // goes back to the question viewing
    $editQuestion = $modifyAnswers;
    unset($modifyAnswers);
}

$nameTools = get_lang('ExerciseManagement');

// modifies the query string that is used in the link of tool name
if ($editQuestion || $modifyQuestion || $newQuestion || $modifyAnswers) {
    $nameTools = get_lang('QuestionManagement');
}

if (isset($_SESSION['gradebook'])) {
    $gradebook = $_SESSION['gradebook'];
}

if (!empty($gradebook) && $gradebook == 'view') {
    $interbreadcrumb[] = array(
        'url' => '../gradebook/'.$_SESSION['gradebook_dest'],
        'name' => get_lang('ToolGradebook')
    );
}

$interbreadcrumb[] = array("url" => $urlMainExercise."exercice.php", "name" => get_lang('Exercices'));
if (isset($_GET['newQuestion']) || isset($_GET['editQuestion'])) {
    $interbreadcrumb[] = array("url" => $urlMainExercise."admin.php?exerciseId=".$objExercise->id, "name" => $objExercise->name);
} else {
    $interbreadcrumb[] = array("url" => "#", "name" => $objExercise->name);
}

// if the question is duplicated, disable the link of tool name
if (!empty($modifyIn) && $modifyIn == 'thisExercise') {
    if ($buttonBack) {
        $modifyIn = 'allExercises';
    } else {
        $noPHP_SELF = true;
    }
}

$htmlHeadXtra[] = '<script>

function multiple_answer_true_false_onchange(variable) {
    var result = variable.checked;
    var id = variable.id;
    var weight_id = "weighting_" + id;
    var array_result=new Array();
    array_result[1]="1";
    array_result[0]= "-0.50";
    array_result[-1]= "0";
    if (result) {
        result = 1;
    } else {
        result = 0;
    }
    document.getElementById(weight_id).value = array_result[result];
}
$(function() {

    $( "#dialog:ui-dialog" ).dialog( "destroy" );
    $( "#dialog-confirm" ).dialog({
        autoOpen: false,
        show: "blind",
        resizable: false,
        height:150,
        modal: false
     });


    $(".opener").click(function() {
        var targetUrl = $(this).attr("href");
        $( "#dialog-confirm" ).dialog({
            modal: true,
            buttons: {
                "'.get_lang("Yes").'": function() {
                    location.href = targetUrl;
                    $( this ).dialog( "close" );
                },
                "'.get_lang("No").'": function() {
                    $( this ).dialog( "close" );
                }
            }
        });
        $( "#dialog-confirm" ).dialog("open");
        return false;
    });
});

</script>';

$htmlHeadXtra[] = '<script src="'.api_get_path(WEB_CODE_PATH).'plugin/hotspot/JavaScriptFlashGateway.js"></script>';
$htmlHeadXtra[] = '<script src="'.api_get_path(WEB_CODE_PATH).'plugin/hotspot/hotspot.js"></script>';
$htmlHeadXtra[] = "<script>
<!--
// Globals
// Major version of Flash required
var requiredMajorVersion = 7;
// Minor version of Flash required
var requiredMinorVersion = 0;
// Minor version of Flash required
var requiredRevision = 0;
// the version of javascript supported
var jsVersion = 1.0;
// -->
</script>
<script language=\"VBScript\" type=\"text/vbscript\">
<!-- // Visual basic helper required to detect Flash Player ActiveX control version information
Function VBGetSwfVer(i)
  on error resume next
  Dim swControl, swVersion
  swVersion = 0

  set swControl = CreateObject(\"ShockwaveFlash.ShockwaveFlash.\" + CStr(i))
  if (IsObject(swControl)) then
    swVersion = swControl.GetVariable(\"\$version\")
  end if
  VBGetSwfVer = swVersion
End Function
// -->
</script>

<script language=\"JavaScript1.1\" type=\"text/javascript\">
<!-- // Detect Client Browser type
var isIE  = (navigator.appVersion.indexOf(\"MSIE\") != -1) ? true : false;
var isWin = (navigator.appVersion.toLowerCase().indexOf(\"win\") != -1) ? true : false;
var isOpera = (navigator.userAgent.indexOf(\"Opera\") != -1) ? true : false;
jsVersion = 1.1;
// JavaScript helper required to detect Flash Player PlugIn version information
function JSGetSwfVer(i){
	// NS/Opera version >= 3 check for Flash plugin in plugin array
	if (navigator.plugins != null && navigator.plugins.length > 0) {
		if (navigator.plugins[\"Shockwave Flash 2.0\"] || navigator.plugins[\"Shockwave Flash\"]) {
			var swVer2 = navigator.plugins[\"Shockwave Flash 2.0\"] ? \" 2.0\" : \"\";
      		var flashDescription = navigator.plugins[\"Shockwave Flash\" + swVer2].description;
			descArray = flashDescription.split(\" \");
			tempArrayMajor = descArray[2].split(\".\");
			versionMajor = tempArrayMajor[0];
			versionMinor = tempArrayMajor[1];
			if ( descArray[3] != \"\" ) {
				tempArrayMinor = descArray[3].split(\"r\");
			} else {
				tempArrayMinor = descArray[4].split(\"r\");
			}
      		versionRevision = tempArrayMinor[1] > 0 ? tempArrayMinor[1] : 0;
            flashVer = versionMajor + \".\" + versionMinor + \".\" + versionRevision;
      	} else {
			flashVer = -1;
		}
	}
	// MSN/WebTV 2.6 supports Flash 4
	else if (navigator.userAgent.toLowerCase().indexOf(\"webtv/2.6\") != -1) flashVer = 4;
	// WebTV 2.5 supports Flash 3
	else if (navigator.userAgent.toLowerCase().indexOf(\"webtv/2.5\") != -1) flashVer = 3;
	// older WebTV supports Flash 2
	else if (navigator.userAgent.toLowerCase().indexOf(\"webtv\") != -1) flashVer = 2;
	// Can't detect in all other cases
	else {

		flashVer = -1;
	}
	return flashVer;
}
// When called with reqMajorVer, reqMinorVer, reqRevision returns true if that version or greater is available
function DetectFlashVer(reqMajorVer, reqMinorVer, reqRevision)
{
 	reqVer = parseFloat(reqMajorVer + \".\" + reqRevision);
   	// loop backwards through the versions until we find the newest version
	for (i=25;i>0;i--) {
		if (isIE && isWin && !isOpera) {
			versionStr = VBGetSwfVer(i);
		} else {
			versionStr = JSGetSwfVer(i);
		}
		if (versionStr == -1 ) {
			return false;
		} else if (versionStr != 0) {
			if(isIE && isWin && !isOpera) {
				tempArray         = versionStr.split(\" \");
				tempString        = tempArray[1];
				versionArray      = tempString .split(\",\");
			} else {
				versionArray      = versionStr.split(\".\");
			}
			versionMajor      = versionArray[0];
			versionMinor      = versionArray[1];
			versionRevision   = versionArray[2];

			versionString     = versionMajor + \".\" + versionRevision;   // 7.0r24 == 7.24
			versionNum        = parseFloat(versionString);
        	// is the major.revision >= requested major.revision AND the minor version >= requested minor
			if ( (versionMajor > reqMajorVer) && (versionNum >= reqVer) ) {
				return true;
			} else {
				return ((versionNum >= reqVer && versionMinor >= reqMinorVer) ? true : false );
			}
		}
	}
}
// -->
</script>";

Display::display_header($nameTools, 'Exercise');

if ($objExercise->exercise_was_added_in_lp) {
    if ($objExercise->force_edit_exercise_in_lp == true) {
        Display::display_warning_message(get_lang('ForceEditingExerciseInLPWarning'));
    } else {
        Display::display_warning_message(get_lang('EditingExerciseCauseProblemsInLP'));
    }
}

// If we are in a test
$inATest = isset($exerciseId) && $exerciseId > 0;
if ($inATest) {
    echo '<div class="actions">';
    if (isset($_GET['hotspotadmin']) || isset($_GET['newQuestion']) || isset($_GET['myid'])) {
        echo '<a href="'.$urlMainExercise.'admin.php?exerciseId='.$exerciseId.'">'.Display::return_icon(
            'back.png',
            get_lang('GoBackToQuestionList'),
            '',
            ICON_SIZE_MEDIUM
        ).'</a>';
    }

    if (!isset($_GET['hotspotadmin']) && !isset($_GET['newQuestion']) && !isset($_GET['myid']) && !isset($_GET['editQuestion'])) {
        echo '<a href="'.$urlMainExercise.'exercice.php?'.api_get_cidReq().'">'.Display::return_icon(
            'back.png',
            get_lang('BackToExercisesList'),
            '',
            ICON_SIZE_MEDIUM
        ).'</a>';
    }
    echo '<a href="'.$urlMainExercise.'overview.php?'.api_get_cidreq().'&exerciseId='.$objExercise->id.'&preview=1">'.Display::return_icon(
        'preview_view.png',
        get_lang('Preview'),
        '',
        ICON_SIZE_MEDIUM
    ).'</a>';

    echo Display::url(
        Display::return_icon('test_results.png', get_lang('Results'), '', ICON_SIZE_MEDIUM),
        $urlMainExercise.'exercise_report.php?'.api_get_cidReq().'&exerciseId='.$objExercise->id
    );

    if ($objExercise->edit_exercise_in_lp == false) {
        echo '<a href="">'.Display::return_icon(
            'settings_na.png',
            get_lang('ModifyExercise'),
            '',
            ICON_SIZE_MEDIUM
        ).'</a>';
    } else {
        echo '<a href="'.$urlMainExercise.'exercise_admin.php?'.api_get_cidreq().'&modifyExercise=yes&exerciseId='.$objExercise->id.'">'.
            Display::return_icon('settings.png', get_lang('ModifyExercise'), '', ICON_SIZE_MEDIUM ).'</a>';
    }

    // @todo if you have 5000 questions this will slow down everything
    /*
    $maxScoreAllQuestions = 0;
    if (!empty($objExercise->questionList)) {
        foreach ($objExercise->questionList as $q) {
            $question = Question::read($q);
            if ($question) {
                $maxScoreAllQuestions += $question->selectWeighting();
            }
        }
    }
    echo '<span style="float:right">'.sprintf(get_lang('XQuestionsWithTotalScoreY'), $objExercise->selectNbrQuestions(), $maxScoreAllQuestions ).'</span>';*/
    echo '</div>';
} else {
    // we are in create a new question from question pool not in a test
    echo '<div class="actions">';
    echo '<a href="'.$urlMainExercise.'admin.php?exerciseId='.$objExercise->id.'&'.api_get_cidreq().'">'.
          Display::return_icon('back.png', get_lang('GoBackToQuestionList'),'', ICON_SIZE_MEDIUM ).'</a>';
    echo '</div>';
}


if (isset($_GET['message'])) {
    if (in_array($_GET['message'], array('ExerciseStored', 'ItemUpdated', 'ItemAdded'))) {
        Display::display_confirmation_message(get_lang($_GET['message']));
    }
}

if ($newQuestion || $editQuestion) {
    // statement management

    if ($editQuestion) {
        $type = $objQuestion->selectType();
    } else {
        $type = Security::remove_XSS($_REQUEST['answerType']);
    }
    echo '<input type="hidden" name="Type" value="'.$type.'" />';
    // Create/Edit question.
    require 'question_admin.inc.php';
}

if (isset($_GET['hotspotadmin'])) {
    if (!is_object($objQuestion)) {
        $objQuestion = Question :: read($_GET['hotspotadmin']);
    }
    if (!$objQuestion) {
        api_not_allowed();
    }
    require 'hotspot_admin.inc.php';
}

if (!$newQuestion && !$modifyQuestion && !$editQuestion && !isset($_GET['hotspotadmin'])) {

    if ($objExercise->randomByCat == EXERCISE_CATEGORY_RANDOM_SHUFFLED || EXERCISE_CATEGORY_RANDOM_ORDERED) {
        Display::display_normal_message(get_lang('AllQuestionsMustHaveACategory'));
    }
    // Question list (drag n drop view)
    // @todo this bad do not require files like this
    if ($objExercise->fastEdition) {
        require 'question_list_pagination_admin.inc.php';
    } else {
        require 'question_list_admin.inc.php';
    }
}

Session::write('objExercise', $objExercise);
Session::write('objQuestion', $objQuestion);
Session::write('objAnswer', $objAnswer);
Display::display_footer();
