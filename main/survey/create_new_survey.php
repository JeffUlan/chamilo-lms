<?php
/* For licensing terms, see /license.txt */

/**
 * @package chamilo.survey
 *
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University: cleanup,
 *  refactoring and rewriting large parts (if not all) of the code
 * @author Julio Montoya Armas <gugli100@gmail.com>, Chamilo: Personality
 * Test modification and rewriting large parts of the code
 *
 * @version $Id: create_new_survey.php 22297 2009-07-22 22:08:30Z cfasanando $
 *
 * @todo only the available platform languages should be used => need an
 *  api get_languages and and api_get_available_languages (or a parameter)
 */
require_once __DIR__.'/../inc/global.inc.php';

$this_section = SECTION_COURSES;

$allowSurveyAvailabilityDatetime = api_get_configuration_value('allow_survey_availability_datetime');

// Database table definitions
$table_survey = Database::get_course_table(TABLE_SURVEY);
$table_user = Database::get_main_table(TABLE_MAIN_USER);
$table_course = Database::get_main_table(TABLE_MAIN_COURSE);
$table_gradebook_link = Database::get_main_table(TABLE_MAIN_GRADEBOOK_LINK);

/** @todo this has to be moved to a more appropriate place (after the display_header of the code) */
// If user is not teacher or if he's a coach trying to access an element out of his session
if (!api_is_allowed_to_edit()) {
    if (!api_is_session_general_coach() ||
        (!empty($_GET['survey_id']) &&
        !api_is_element_in_the_session(TOOL_SURVEY, $_GET['survey_id']))
    ) {
        api_not_allowed(true);
        exit;
    }
}

// Getting the survey information
$survey_id = isset($_GET['survey_id']) ? (int) $_GET['survey_id'] : null;
$survey_data = SurveyManager::get_survey($survey_id);

// Additional information
$course_id = api_get_course_id();
$session_id = api_get_session_id();
$gradebook_link_type = 8;
$urlname = isset($survey_data['title']) ? strip_tags($survey_data['title']) : null;

// Breadcrumbs
if ($_GET['action'] == 'add') {
    $interbreadcrumb[] = [
        'url' => api_get_path(WEB_CODE_PATH).'survey/survey_list.php?'.api_get_cidreq(),
        'name' => get_lang('SurveyList'),
    ];
    $tool_name = get_lang('CreateNewSurvey');
}
if ($_GET['action'] == 'edit' && is_numeric($survey_id)) {
    $interbreadcrumb[] = [
        'url' => api_get_path(WEB_CODE_PATH).'survey/survey_list.php?'.api_get_cidreq(),
        'name' => get_lang('SurveyList'),
    ];
    $interbreadcrumb[] = [
        'url' => api_get_path(WEB_CODE_PATH).'survey/survey.php?survey_id='.$survey_id.'&'.api_get_cidreq(),
        'name' => Security::remove_XSS($urlname),
    ];
    $tool_name = get_lang('EditSurvey');
}
$gradebook_link_id = null;
// Getting the default values
if ($_GET['action'] == 'edit' && isset($survey_id) && is_numeric($survey_id)) {
    $defaults = $survey_data;
    $defaults['survey_id'] = $survey_id;
    $defaults['anonymous'] = $survey_data['anonymous'];

    if ($allowSurveyAvailabilityDatetime) {
        $defaults['avail_from'] = api_get_local_time($defaults['avail_from'], null, 'UTC');
        $defaults['avail_till'] = api_get_local_time($defaults['avail_till'], null, 'UTC');
        $defaults['start_date'] = $defaults['avail_from'];
        $defaults['end_date'] = $defaults['avail_till'];
    }

    $link_info = GradebookUtils::isResourceInCourseGradebook(
        $course_id,
        $gradebook_link_type,
        $survey_id,
        $session_id
    );
    $gradebook_link_id = $link_info['id'];

    if ($link_info) {
        $defaults['category_id'] = $link_info['category_id'];
        $gradebook_link_id = (int) $gradebook_link_id;
        $sql = "SELECT weight FROM $table_gradebook_link WHERE id = $gradebook_link_id";
        $result = Database::query($sql);
        $gradeBookData = Database::fetch_array($result);
        if ($gradeBookData) {
            $defaults['survey_qualify_gradebook'] = $gradebook_link_id;
            $defaults['survey_weight'] = number_format($gradeBookData['weight'], 2, '.', '');
        }
    }
} else {
    $defaults['survey_language'] = $_course['language'];
    $defaults['start_date'] = date(
        $allowSurveyAvailabilityDatetime ? 'Y-m-d 00:00:00' : 'Y-m-d',
        api_strtotime(api_get_local_time())
    );
    $startdateandxdays = time() + 864000; // today + 10 days
    $defaults['end_date'] = date(
        $allowSurveyAvailabilityDatetime ? 'Y-m-d 23:59:59' : 'Y-m-d',
        $startdateandxdays
    );
    //$defaults['survey_share']['survey_share'] = 0;
    //$form_share_value = 1;
    $defaults['anonymous'] = 0;
}

// Initialize the object
$form = new FormValidator(
    'survey',
    'post',
    api_get_self().'?action='.Security::remove_XSS($_GET['action']).'&survey_id='.$survey_id.'&'.api_get_cidreq()
);

$form->addElement('header', $tool_name);

// Setting the form elements
if ($_GET['action'] == 'edit' && isset($survey_id) && is_numeric($survey_id)) {
    $form->addElement('hidden', 'survey_id');
}

$survey_code = $form->addElement(
    'text',
    'survey_code',
    get_lang('SurveyCode'),
    ['size' => '20', 'maxlength' => '20', 'autofocus' => 'autofocus']
);

if ($_GET['action'] == 'edit') {
    $survey_code->freeze();
    $form->applyFilter('survey_code', 'api_strtoupper');
}

$form->addElement(
    'html_editor',
    'survey_title',
    get_lang('SurveyTitle'),
    null,
    ['ToolbarSet' => 'Survey', 'Width' => '100%', 'Height' => '200']
);
$form->addElement(
    'html_editor',
    'survey_subtitle',
    get_lang('SurveySubTitle'),
    null,
    [
        'ToolbarSet' => 'Survey',
        'Width' => '100%',
        'Height' => '100',
        'ToolbarStartExpanded' => false,
    ]
);

// Pass the language of the survey in the form
$form->addElement('hidden', 'survey_language');

if ($allowSurveyAvailabilityDatetime) {
    $form->addDateTimePicker('start_date', get_lang('StartDate'));
    $form->addDateTimePicker('end_date', get_lang('EndDate'));
} else {
    $form->addElement('date_picker', 'start_date', get_lang('StartDate'));
    $form->addElement('date_picker', 'end_date', get_lang('EndDate'));
}

$form->addElement('checkbox', 'anonymous', null, get_lang('Anonymous'));
$visibleResults = [
    SURVEY_VISIBLE_TUTOR => get_lang('Coach'),
    SURVEY_VISIBLE_TUTOR_STUDENT => get_lang('CoachAndStudent'),
    SURVEY_VISIBLE_PUBLIC => get_lang('Everyone'),
];

if (api_get_configuration_value('hide_survey_reporting_button')) {
    $form->addLabel(get_lang('ResultsVisibility'), get_lang('FeatureDisabledByAdministrator'));
} else {
    $form->addElement('select', 'visible_results', get_lang('ResultsVisibility'), $visibleResults);
}
//$defaults['visible_results'] = 0;
$form->addElement(
    'html_editor',
    'survey_introduction',
    get_lang('SurveyIntroduction'),
    null,
    ['ToolbarSet' => 'Survey', 'Width' => '100%', 'Height' => '130', 'ToolbarStartExpanded' => false]
);
$form->addElement(
    'html_editor',
    'survey_thanks',
    get_lang('SurveyThanks'),
    null,
    ['ToolbarSet' => 'Survey', 'Width' => '100%', 'Height' => '130', 'ToolbarStartExpanded' => false]
);

$extraField = new ExtraField('survey');
$extraField->addElements($form, $survey_id);

// Additional Parameters
$form->addButtonAdvancedSettings('advanced_params');
$form->addElement('html', '<div id="advanced_params_options" style="display:none">');

if (Gradebook::is_active()) {
    // An option: Qualify the fact that survey has been answered in the gradebook
    $form->addElement(
        'checkbox',
        'survey_qualify_gradebook',
        null,
        get_lang('QualifyInGradebook'),
        'onclick="javascript: if (this.checked) { document.getElementById(\'gradebook_options\').style.display = \'block\'; } else { document.getElementById(\'gradebook_options\').style.display = \'none\'; }"'
    );
    $form->addElement(
        'html',
        '<div id="gradebook_options"'.($gradebook_link_id ? '' : ' style="display:none"').'>'
    );
    $form->addElement(
        'text',
        'survey_weight',
        get_lang('QualifyWeight'),
        'value="0.00" style="width: 40px;" onfocus="javascript: this.select();"'
    );
    $form->applyFilter('survey_weight', 'html_filter');

    // Loading Gradebook select
    GradebookUtils::load_gradebook_select_in_tool($form);

    if ($_GET['action'] == 'edit') {
        $element = $form->getElement('category_id');
        $element->freeze();
    }
    $form->addElement('html', '</div>');
}

// Personality/Conditional Test Options
$surveytypes[0] = get_lang('Normal');
$surveytypes[1] = get_lang('Conditional');

if ($_GET['action'] == 'add') {
    $form->addElement('hidden', 'survey_type', 0);
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
        $form->addElement(
            'checkbox',
            'show_form_profile',
            null,
            get_lang('ShowFormProfile'),
            'onclick="javascript: if(this.checked){document.getElementById(\'options_field\').style.display = \'block\';}else{document.getElementById(\'options_field\').style.display = \'none\';}"'
        );

        if ($survey_data['show_form_profile'] == 1) {
            $form->addElement('html', '<div id="options_field" style="display:block">');
        } else {
            $form->addElement('html', '<div id="options_field" style="display:none">');
        }

        $field_list = SurveyUtil::make_field_list();

        if (is_array($field_list)) {
            // TODO hide and show the list in a fancy DIV
            foreach ($field_list as $key => &$field) {
                if ($field['visibility'] == 1) {
                    $form->addElement('checkbox', 'profile_'.$key, ' ', '&nbsp;&nbsp;'.$field['name']);
                    $input_name_list .= 'profile_'.$key.',';
                }
            }

            // Necessary to know the fields
            $form->addElement('hidden', 'input_name_list', $input_name_list);

            // Set defaults form fields
            if ($survey_data['form_fields']) {
                $form_fields = explode('@', $survey_data['form_fields']);
                foreach ($form_fields as &$field) {
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

$skillList = Skill::addSkillsToForm($form, ITEM_TYPE_SURVEY, $survey_id);

$form->addElement('html', '</div><br />');

if (isset($_GET['survey_id']) && $_GET['action'] == 'edit') {
    $form->addButtonUpdate(get_lang('ModifySurvey'), 'submit_survey');
} else {
    $form->addButtonCreate(get_lang('CreateSurvey'), 'submit_survey');
}

// Setting the rules
if ($_GET['action'] == 'add') {
    $form->addRule('survey_code', get_lang('ThisFieldIsRequired'), 'required');
    $form->addRule('survey_code', '', 'maxlength', 20);
}
$form->addRule('survey_title', get_lang('ThisFieldIsRequired'), 'required');
$form->addRule('start_date', get_lang('InvalidDate'), $allowSurveyAvailabilityDatetime ? 'datetime' : 'date');
$form->addRule('end_date', get_lang('InvalidDate'), $allowSurveyAvailabilityDatetime ? 'datetime' : 'date');
$form->addRule(
    ['start_date', 'end_date'],
    get_lang('StartDateShouldBeBeforeEndDate'),
    'date_compare',
    'lte'
);

$defaults['skills'] = array_keys($skillList);

// Setting the default values
$form->setDefaults($defaults);

// The validation or display
if ($form->validate()) {
    // Exporting the values
    $values = $form->getSubmitValues();
    // Storing the survey
    $return = SurveyManager::store_survey($values);
    Skill::saveSkills($form, ITEM_TYPE_SURVEY, $return['id']);

    $values['item_id'] = $return['id'];
    $extraFieldValue = new ExtraFieldValue('survey');
    $extraFieldValue->saveFieldValues($values);

    // Redirecting to the survey page (whilst showing the return message)
    header('Location: '.api_get_path(WEB_CODE_PATH).'survey/survey.php?survey_id='.$return['id'].'&'.api_get_cidreq());
    exit;
} else {
    // Displaying the header
    Display::display_header($tool_name);
    $form->display();
}

Display::display_footer();
