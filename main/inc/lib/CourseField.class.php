<?php
/* For licensing terms, see /license.txt */
/**
 * Manage the course extra fields
 */
class CourseField extends ExtraField {

    /**
     * Special Course extra field
     */
    const SPECIAL_COURSE_FIELD = 'special_course';

    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct('course');
    }

    /**
     * Show the course extra fields
     */
    function display() {
        // action links
        echo '<div class="actions">';
        echo '<a href="../admin/index.php">' . Display::return_icon('back.png', get_lang('BackTo') . ' ' . get_lang('PlatformAdmin'), '', ICON_SIZE_MEDIUM) . '</a>';
        echo '<a href="' . api_get_self() . '?action=add">' . Display::return_icon('add_user_fields.png', get_lang('Add'), '', ICON_SIZE_MEDIUM) . '</a>';

        echo '</div>';
        echo Display::grid_html('course_fields');
    }

    /**
     * Generate a form
     * @param string $url the The form action param
     * @param string $action The action. Add or edit
     * @return FormValidator The form
     */
    public function return_form($url, $action) {
        $form = new FormValidator('course_field', 'post', $url);
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;
        $form->addElement('hidden', 'id', $id);
        
        // Settting the form elements
        $header = get_lang('Add');        
        $defaults = array();
        
        if ($action == 'edit') {
            $header = get_lang('Modify');
            // Setting the defaults
            $defaults = $this->get($id);            
        }

        $form->addElement('header', $header);
        $form->addElement('text', 'field_display_text', get_lang('Name'), array('class' => 'span5'));

        // Field type
        $types = self::get_field_types();

        $form->addElement('select', 'field_type', get_lang('FieldType'), $types, array('id' => 'field_type', 'class' => 'chzn-select', 'data-placeholder' => get_lang('Select')));
        $form->addElement('label', get_lang('Example'), '<div id="example">-</div>');

        //$form->addElement('advanced_settings','<a class="btn btn-show" id="advanced_parameters" href="javascript://">'.get_lang('AdvancedParameters').'</a>');
        //$form->addElement('html','<div id="options" style="display:none">');

        $form->addElement('text', 'field_variable', get_lang('FieldLabel'), array('class' => 'span5'));
        $form->addElement('text', 'field_options', get_lang('FieldPossibleValues'), array('id' => 'field_options', 'class' => 'span6'));
        if ($action == 'edit') {            
            if (in_array($defaults['field_type'], array(ExtraField::FIELD_TYPE_SELECT, ExtraField::FIELD_TYPE_DOUBLE_SELECT))) {
                $url = Display::url(get_lang('EditExtraFieldOptions'), 'extra_field_options.php?type=course&field_id=' . $id);
                $form->addElement('label', null, $url);
                $form->freeze('field_options');
            }
        }
        $form->addElement('text', 'field_default_value', get_lang('FieldDefaultValue'), array('id' => 'field_default_value', 'class' => 'span5'));

        $group = array();
        $group[] = $form->createElement('radio', 'field_visible', null, get_lang('Yes'), 1);
        $group[] = $form->createElement('radio', 'field_visible', null, get_lang('No'), 0);
        $form->addGroup($group, '', get_lang('Visible'), '', false);

        $group = array();
        $group[] = $form->createElement('radio', 'field_changeable', null, get_lang('Yes'), 1);
        $group[] = $form->createElement('radio', 'field_changeable', null, get_lang('No'), 0);
        $form->addGroup($group, '', get_lang('FieldChangeability'), '', false);

        $group = array();
        $group[] = $form->createElement('radio', 'field_filter', null, get_lang('Yes'), 1);
        $group[] = $form->createElement('radio', 'field_filter', null, get_lang('No'), 0);
        $form->addGroup($group, '', get_lang('FieldFilter'), '', false);        
        
        $form->addElement('text', 'field_order', get_lang('FieldOrder'), array('class' => 'span1'));

        if ($action == 'edit') {            
            $option = new ExtraFieldOption('course');
            if ($defaults['field_type'] == ExtraField::FIELD_TYPE_DOUBLE_SELECT) {
                $form->freeze('field_options');
            }
            $defaults['field_options'] = $option->get_field_options_by_field_to_string($id);
            $form->addElement('button', 'submit', get_lang('Modify'), 'class="save"');
        } else {
            $defaults['field_visible'] = 0;
            $defaults['field_changeable'] = 0;
            $defaults['field_filter'] = 0;
            $form->addElement('button', 'submit', get_lang('Add'), 'class="save"');
        }

        /*if (!empty($defaults['created_at'])) {
            $defaults['created_at'] = api_convert_and_format_date($defaults['created_at']);
        }
        if (!empty($defaults['updated_at'])) {
            $defaults['updated_at'] = api_convert_and_format_date($defaults['updated_at']);
        }*/
        $form->setDefaults($defaults);

        // Setting the rules
        $form->addRule('field_display_text', get_lang('ThisFieldIsRequired'), 'required');
        //$form->addRule('field_variable', get_lang('ThisFieldIsRequired'), 'required');
        $form->addRule('field_type', get_lang('ThisFieldIsRequired'), 'required');

        return $form;
    }

    /**
     * Add elements to a form
     * @param FormValidator $form the form
     * @param string $courseCode The course code
     * @return array The extra data. Otherwise return false
     */
    public function addElements($form, $courseCode = null)
    {
        if (empty($form)) {
            return false;
        }

        $extra_data = false;
        if (!empty($courseCode)) {
            $extra_data = self::get_handler_extra_data($courseCode);

            if ($form) {
                $form->setDefaults($extra_data);
            }
        }

        $extra_fields = $this->get_all(null, 'option_order');

        $specilCourseFieldId = -1;

        foreach ($extra_fields as $id => $extraField) {
            if ($extraField['field_variable'] === self::SPECIAL_COURSE_FIELD) {
                $specilCourseFieldId = $id;
            }
        }

        if (isset($extra_fields[$specilCourseFieldId])) {
            unset($extra_fields[$specilCourseFieldId]);
        }

        $extra = $this->set_extra_fields_in_form(
            $form,
            $extra_data,
            $this->type.'_field',
            false,
            false,
            $extra_fields,
            $courseCode
        );

        return $extra;
    }
}