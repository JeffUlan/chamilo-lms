<?php
/* For licensing terms, see /license.txt */

/**
 * 	@package chamilo.survey
 * 	@author Patrick Cool <patrick.cool@UGent.be>, Ghent University: cleanup,
 *  refactoring and rewriting large parts (if not all) of the code
 * 	@author Julio Montoya Armas <gugli100@gmail.com>, Chamilo: Personality
 * Test modification and rewriting large parts of the code
 * 	@version $Id: create_new_survey.php 22297 2009-07-22 22:08:30Z cfasanando $
 *
 * 	@todo only the available platform languages should be used => need an
 *  api get_languages and and api_get_available_languages (or a parameter)
 */
// Language file that needs to be included
$language_file = 'survey';

// Including the global initialization file
require_once '../inc/global.inc.php';

$this_section = SECTION_COURSES;

// Including additional libraries

$htmlHeadXtra[] = '<script>
    function advanced_parameters() {
        if (document.getElementById(\'options\').style.display == \'none\') {
            document.getElementById(\'options\').style.display = \'block\';
            document.getElementById(\'plus_minus\').innerHTML=\'&nbsp;'.Display::return_icon('div_hide.gif', get_lang('Hide'), array('style' => 'vertical-align:middle')).'&nbsp;'.get_lang('AdvancedParameters').'\';
        } else {
            document.getElementById(\'options\').style.display = \'none\';
            document.getElementById(\'plus_minus\').innerHTML=\'&nbsp;'.Display::return_icon('div_show.gif', get_lang('Show'), array('style' => 'vertical-align:middle')).'&nbsp;'.get_lang('AdvancedParameters').'\';
        }
    }

    function setFocus(){
        $("#surveycode_title").focus();
    }

    $(document).ready(function () {
        setFocus();
    });
</script>';

// Database table definitions
$table_survey = Database :: get_course_table(TABLE_SURVEY);
$table_user = Database :: get_main_table(TABLE_MAIN_USER);
$table_course = Database :: get_main_table(TABLE_MAIN_COURSE);
$table_gradebook_link = Database :: get_main_table(TABLE_MAIN_GRADEBOOK_LINK);

/** @todo this has to be moved to a more appropriate place (after the display_header of the code) */
// If user is not teacher or if he's a coach trying to access an element out of his session
if (!api_is_allowed_to_edit()) {
    if (!api_is_course_coach() ||
        (!empty($_GET['survey_id']) &&
        !api_is_element_in_the_session(TOOL_SURVEY, $_GET['survey_id']))
    ) {
        api_not_allowed(true);
        exit;
    }
}

// Getting the survey information
$survey_id = isset($_GET['survey_id']) ? intval($_GET['survey_id']) : null;
$survey_data = survey_manager::get_survey($survey_id);

// Additional information
$course_id = api_get_course_id();
$session_id = api_get_session_id();
$gradebook_link_type = 8;
$urlname = isset($survey_data['title']) ? strip_tags($survey_data['title']) : null;

// Breadcrumbs
if ($_GET['action'] == 'add') {
    $interbreadcrumb[] = array(
        'url' => api_get_path(WEB_CODE_PATH).'survey/survey_list.php?'.api_get_cidreq(),
        'name' => get_lang('SurveyList')
    );
    $tool_name = get_lang('CreateNewSurvey');
}
if ($_GET['action'] == 'edit' && is_numeric($survey_id)) {
    $interbreadcrumb[] = array(
        'url' => api_get_path(WEB_CODE_PATH).'survey/survey_list.php?'.api_get_cidreq(),
        'name' => get_lang('SurveyList')
    );
    $interbreadcrumb[] = array(
        'url' => api_get_path(WEB_CODE_PATH).'survey/survey.php?survey_id='.$survey_id.'&'.api_get_cidreq(),
        'name' => Security::remove_XSS($urlname)
    );
    $tool_name = get_lang('EditSurvey');
}
$gradebook_link_id = null;
// Getting the default values
if ($_GET['action'] == 'edit' && isset($survey_id) && is_numeric($survey_id)) {
    $defaults = $survey_data;
    $defaults['survey_id'] = $survey_id;
    $defaults['anonymous'] = $survey_data['anonymous'];

    $link_info = GradebookUtils::is_resource_in_course_gradebook($course_id, $gradebook_link_type, $survey_id, $session_id);
    $gradebook_link_id = $link_info['id'];

    if ($link_info) {
        if ($sql_result_array = Database::fetch_array(Database::query('SELECT weight FROM '.$table_gradebook_link.' WHERE id='.$gradebook_link_id))) {
            $defaults['survey_qualify_gradebook'] = $gradebook_link_id;
            $defaults['survey_weight'] = number_format($sql_result_array['weight'], 2, '.', '');
        }
    }
} else {
    $defaults['survey_language'] = $_course['language'];
    $defaults['start_date'] = date('Y-m-d', api_strtotime(api_get_local_time()));
    $startdateandxdays = time() + 864000; // today + 10 days
    $defaults['end_date'] = date('Y-m-d', $startdateandxdays);
    //$defaults['survey_share']['survey_share'] = 0;
    //$form_share_value = 1;
    $defaults['anonymous'] = 0;
}

// Initialize the object
$form = new FormValidator('survey', 'post', api_get_self().'?action='.Security::remove_XSS($_GET['action']).'&survey_id='.$survey_id);

$form->addElement('header', '', $tool_name);

// Setting the form elements
if ($_GET['action'] == 'edit' && isset($survey_id) && is_numeric($survey_id)) {
    $form->addElement('hidden', 'survey_id');
}

$survey_code = $form->addElement('text', 'survey_code', get_lang('SurveyCode'), array('size' => '20', 'maxlength' => '20', 'id' => 'surveycode_title'));

if ($_GET['action'] == 'edit') {
    //$survey_code->freeze();
    $form->applyFilter('survey_code', 'api_strtoupper');
}

$form->addElement('html_editor', 'survey_title', get_lang('SurveyTitle'), null, array('ToolbarSet' => 'Survey', 'Width' => '100%', 'Height' => '200'));
$form->addElement('html_editor', 'survey_subtitle', get_lang('SurveySubTitle'), null, array('ToolbarSet' => 'Survey', 'Width' => '100%', 'Height' => '100', 'ToolbarStartExpanded' => false));

// Pass the language of the survey in the form
$form->addElement('hidden', 'survey_language');
$form->addElement('date_picker', 'start_date', get_lang('StartDate'));
$form->addElement('date_picker', 'end_date', get_lang('EndDate'));

$form->addElement('checkbox', 'anonymous', null, get_lang('Anonymous'));
$visibleResults = array(
    SURVEY_VISIBLE_TUTOR => get_lang('Coach'),
    SURVEY_VISIBLE_TUTOR_STUDENT => get_lang('CoachAndStudent'),
    SURVEY_VISIBLE_PUBLIC => get_lang('Everyone')
);
$form->addElement('select', 'visible_results', get_lang('ResultsVisibility'), $visibleResults);
//$defaults['visible_results'] = 0;
$form->addElement('html_editor', 'survey_introduction', get_lang('SurveyIntroduction'), null, array('ToolbarSet' => 'Survey', 'Width' => '100%', 'Height' => '130', 'ToolbarStartExpanded' => false));
$form->addElement('html_editor', 'survey_thanks', get_lang('SurveyThanks'), null, array('ToolbarSet' => 'Survey', 'Width' => '100%', 'Height' => '130', 'ToolbarStartExpanded' => false));

// Additional Parameters
$form->addElement(
    'advanced_settings',
    '<a href="javascript: void(0);" onclick="javascript: advanced_parameters();">
        <span id="plus_minus">&nbsp;'.
        Display::return_icon('div_show.gif', null, array('style' => 'vertical-align:middle')).'&nbsp;'.get_lang('AdvancedParameters').'</span></a>'
);

$form->addElement('html', '<div id="options" style="display: none;">');

if (Gradebook::is_active()) {
    // An option: Qualify the fact that survey has been answered in the gradebook
    $form->addElement('checkbox', 'survey_qualify_gradebook', null, get_lang('QualifyInGradebook'), 'onclick="javascript: if (this.checked) { document.getElementById(\'gradebook_options\').style.display = \'block\'; } else { document.getElementById(\'gradebook_options\').style.display = \'none\'; }"');
    $form->addElement('html', '<div id="gradebook_options"'.($gradebook_link_id ? '' : ' style="display:none"').'>');
    $form->addElement('text', 'survey_weight', get_lang('QualifyWeight'), 'value="0.00" style="width: 40px;" onfocus="javascript: this.select();"');
    $form->applyFilter('survey_weight', 'html_filter');
    $form->addElement('html', '</div>');
}

// Personality/Conditional Test Options
$surveytypes[0] = get_lang('Normal');
$surveytypes[1] = get_lang('Conditional');

if ($_GET['action'] == 'add') {
    $form->addElement('hidden', 'survey_type', 0);
    require_once api_get_path(LIBRARY_PATH).'surveymanager.lib.php';
    $survey_tree = new SurveyTree();
    $list_surveys = $survey_tree->createList($survey_tree->surveylist);
    $list_surveys[0] = '';
    $form->addElement('select', 'parent_id', get_lang('ParentSurvey'), $list_surveys);
    $defaults['parent_id'] = 0;
}

if (isset($survey_data['survey_type']) && $survey_data['survey_type'] == 1 || $_GET['action'] == 'add') {
    $form->addElement('checkbox', 'one_question_per_page', null, get_lang('OneQuestionPerPage'));
    $form->addElement('checkbox', 'shuffle', null, get_lang('ActivateShuffle'));
}
$input_name_list = null;

if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($survey_id)) {
    if ($survey_data['anonymous'] == 0) {
        $form->addElement('checkbox', 'show_form_profile', null, get_lang('ShowFormProfile'), 'onclick="javascript: if(this.checked){document.getElementById(\'options_field\').style.display = \'block\';}else{document.getElementById(\'options_field\').style.display = \'none\';}"');

        if ($survey_data['show_form_profile'] == 1) {
            $form->addElement('html', '<div id="options_field" style="display:block">');
        } else {
            $form->addElement('html', '<div id="options_field" style="display:none">');
        }

        $field_list = SurveyUtil::make_field_list();

        if (is_array($field_list)) {
            // TODO hide and show the list in a fancy DIV
            foreach ($field_list as $key => & $field) {
                if ($field['visibility'] == 1) {
                    $form->addElement('checkbox', 'profile_'.$key, ' ', '&nbsp;&nbsp;'.$field['name']);
                    $input_name_list.= 'profile_'.$key.',';
                }
            }

            // Necessary to know the fields
            $form->addElement('hidden', 'input_name_list', $input_name_list);

            // Set defaults form fields
            if ($survey_data['form_fields']) {
                $form_fields = explode('@', $survey_data['form_fields']);
                foreach ($form_fields as & $field) {
                    $field_value = explode(':', $field);
                    if ($field_value[0] != '' && $field_value[1] != '') {
                        $defaults[$field_value[0]] = $field_value[1];
                    }
                }
            }
        }

        $form->addElement('html', '</div>');
    }
}

$form->addElement('html', '</div><br />');

if (isset($_GET['survey_id']) && $_GET['action'] == 'edit') {
    $class = 'save';
    $text = get_lang('ModifySurvey');
} else {
    $class = 'add';
    $text = get_lang('CreateSurvey');
}
$form->addElement('style_submit_button', 'submit_survey', $text, 'class="'.$class.'"');

// Setting the rules
if ($_GET['action'] == 'add') {
    $form->addRule('survey_code', get_lang('ThisFieldIsRequired'), 'required');
    $form->addRule('survey_code', '', 'maxlength', 20);
}
$form->addRule('survey_title', get_lang('ThisFieldIsRequired'), 'required');
$form->addRule('start_date', get_lang('InvalidDate'), 'date');
$form->addRule('end_date', get_lang('InvalidDate'), 'date');
$form->addRule(array('start_date', 'end_date'), get_lang('StartDateShouldBeBeforeEndDate'), 'date_compare', 'lte');

// Setting the default values
$form->setDefaults($defaults);

// The validation or display
if ($form->validate()) {
    // Exporting the values
    $values = $form->exportValues();
    // Storing the survey
    $return = survey_manager::store_survey($values);

    /* // Deleting the shared survey if the survey is getting unshared (this only happens when editing)
      if (is_numeric($survey_data['survey_share']) && $values['survey_share']['survey_share'] == 0 && $values['survey_id'] != '') {
      survey_manager::delete_survey($survey_data['survey_share'], true);
      }
      // Storing the already existing questions and options of a survey that gets shared (this only happens when editing)
      if ($survey_data['survey_share'] == 0 && $values['survey_share']['survey_share'] !== 0 && $values['survey_id'] != '') {
      survey_manager::get_complete_survey_structure($return['id']);
      }
     */
    if ($return['type'] == 'error') {
        // Display the error
        Display::display_error_message(get_lang($return['message']), false);

        // Displaying the header
        Display::display_header($tool_name);

        // Display the form
        $form->display();
    } else {
        $gradebook_option = false;
        if (isset($values['survey_qualify_gradebook'])) {
            $gradebook_option = $values['survey_qualify_gradebook'] > 0;
        }

        if ($gradebook_option) {
            $survey_id = intval($return['id']);
            if ($survey_id > 0) {
                $title_gradebook = ''; // Not needed here.
                $description_gradebook = ''; // Not needed here.
                $survey_weight = floatval($_POST['survey_weight']);
                $max_score = 1;
                $date = time(); // TODO: Maybe time zones implementation is needed here.
                $visible = 1; // 1 = visible

                $link_info = GradebookUtils::is_resource_in_course_gradebook(
                    $course_id,
                    $gradebook_link_type,
                    $survey_id,
                    $session_id
                );
                $gradebook_link_id = $link_info['id'];
                if (!$gradebook_link_id) {
                    GradebookUtils::add_resource_to_course_gradebook(
                        $course_id,
                        $gradebook_link_type,
                        $survey_id,
                        $title_gradebook,
                        $survey_weight,
                        $max_score,
                        $description_gradebook,
                        1,
                        $session_id
                    );
                } else {
                    Database::query('UPDATE '.$table_gradebook_link.' SET weight='.$survey_weight.' WHERE id='.$gradebook_link_id);
                }
            }
        }
    }

    Display::addFlash(Display::return_message($return['message'], false));
    // Redirecting to the survey page (whilst showing the return message)
    header('location: '.api_get_path(WEB_CODE_PATH).'survey/survey.php?survey_id='.$return['id'].'&message='.$return['message'].'&'.api_get_cidreq());
    exit;
} else {
    // Displaying the header
    Display::display_header($tool_name);

    $form->display();
}

// Footer
Display :: display_footer();
