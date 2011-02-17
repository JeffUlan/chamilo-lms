<?php
/* For licensing terms, see /license.txt */
/**
*	Code for Qti2 import integration.
*	@package chamilo.exercise
* 	@author Ronny Velasquez
* 	@version $Id: qti2.php  2010-03-12 12:14:25Z $
*/

// name of the language file that needs to be included
$language_file = 'exercice';

// including the global Chamilo file
require_once '../inc/global.inc.php';
require_once api_get_path(LIBRARY_PATH).'document.lib.php';

// including additional libraries
require_once 'exercise.class.php';
require_once 'question.class.php';
require_once 'answer.class.php';

// section (for the tabs)
$this_section = SECTION_COURSES;

// access restriction: only teachers are allowed here
if (!api_is_allowed_to_edit(null, true)) {
	api_not_allowed();
}

// the breadcrumbs
$interbreadcrumb[]= array ("url"=>"./exercice.php", "name"=> get_lang('Exercices'));
$is_allowedToEdit = api_is_allowed_to_edit(null, true);

/**
 * This function displays the form for import of the zip file with qti2
 */
function ch_qti2_display_form() {
	$name_tools = get_lang('ImportQtiQuiz');
	$form  = '<div class="actions">';
	$form .= '<a href="exercice.php?show=test">' . Display :: return_icon('back.png', get_lang('BackToExercisesList'),'','32').'</a>';
	$form .= '</div>';
	$form .= '<form action='.api_get_self()."?".api_get_cidreq().' method="post" enctype="multipart/form-data">';
	$form .= '<div class="row"><div class="form_header">'.$name_tools.'</div></div>';
	$form .= '<div class="row">';
	$form .= '<div class="label" style="padding:10px">';
	$form .= '<span class="form_required">*</span>';
	$form .= get_lang('DownloadFile').' :';
	$form .= '</div>';
	$form .= '<div class="formw">';
	$form .= '<div style="padding:10px">
			  <input type="file" name="userFile"><br /><br />
			  <button type="submit" class="upload" name="submit" value="'.get_lang('Send').'">'.get_lang('SendFile').'</button>
		 	  </div>';
	$form .= '</div></div>';
	echo $form;
}

/**
 * This function will import the zip file with the respective qti2
 * @param array $uploaded_file ($_FILES)
 */
function ch_qti2_import_file($array_file) {
	$unzip = 0;
	$lib_path = api_get_path(LIBRARY_PATH);
	require_once $lib_path.'fileUpload.lib.php';
	require_once $lib_path.'fileManage.lib.php';
	$process = process_uploaded_file($array_file);
	if (preg_match('/\.zip$/i', $array_file['name'])) {
		// if it's a zip, allow zip upload
		$unzip = 1;
	}
	if ($process && $unzip == 1) {
		$main_path = api_get_path(SYS_CODE_PATH);
		require_once $main_path.'exercice/export/exercise_import.inc.php';
        require_once $main_path.'exercice/export/qti2/qti2_classes.php';
        $imported = import_exercise($array_file['name']);
        if ($imported) {
        	header('Location: exercice.php?' . Security::remove_XSS(api_get_cidreq()) .'');
        } else {
            Display::display_error_message(get_lang('The import was not performed'));
            return false;
        }
	}
}

// import file
if ((api_is_allowed_to_edit(null, true))) {
	if (isset($_POST['submit'])) {
		ch_qti2_import_file($_FILES['userFile']);
	}
}

// display header
Display::display_header($name_tools, get_lang('Exercise'));

// display qti form
ch_qti2_display_form();

// display the footer
Display::display_footer();
?>