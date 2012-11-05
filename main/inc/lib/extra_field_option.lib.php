<?php
/* For licensing terms, see /license.txt */
/**
 * Declaration of the ExtraFieldOption class
 */
/**
 * Handles the extra fields for various objects (users, sessions, courses)
 */
class ExtraFieldOption extends Model {
    public $columns = array('id', 'field_id', 'option_value', 'option_display_text', 'option_order', 'tms');

    /**
     * Gets the table for the type of object for which we are using an extra field
     * @param string Type of object (course, user or session)
     */
    public function __construct($type) {
        $this->type = $type;
        switch ($this->type) { 
           case 'course':            
                $this->table = Database::get_main_table(TABLE_MAIN_COURSE_FIELD_OPTIONS);
                break;
            case 'user':            
                $this->table = Database::get_main_table(TABLE_MAIN_USER_FIELD_OPTIONS);
                break;
            case 'session':
                $this->table = Database::get_main_table(TABLE_MAIN_SESSION_FIELD_OPTIONS);                        
            break;   
        }
    }
    /**
     * Gets the number of options already available in the table for this item type
     * @return int Number of options available
     */
    public function get_count() {
        $row = Database::select('count(*) as count', $this->table, array(), 'first');
        return $row['count'];
    }  
    /**
     * Gets the number of options available for this field
     * @param int Field ID
     * @return int Number of options
     */
    public function get_count_by_field_id($field_id) {
        $row = Database::select('count(*) as count', $this->table, array('where' => array('field_id = ?' => $field_id)), 'first');           
        return $row['count'];
    }
    /**
     * Returns a list of options for a specific field, separated by ";"
     * @param int Field ID
     * @param bool Indicates whether we want the results to be given with their id
     * @param string Order by clause (without the "order by") to be added to the SQL query
     * @return string List of options separated by ;
     */
    public function get_field_options_to_string($field_id,  $add_id_in_array = false, $ordered_by = null) {
        $options = self::get_field_options_by_field($field_id, $add_id_in_array, $ordered_by);
        $new_options = array();        
        if (!empty($options)) {
            foreach ($options as $option) {
                $new_options[] = $option['option_value'].':'.$option['option_display_text'];
            }
            $string = implode(';', $new_options);
            return $string;
        }
    }
    /**
     * Delete all the options of a specific field
     * @param int Field ID
     * @result void
     */
    public function delete_all_options_by_field_id($field_id) {
        $field_id = intval($field_id);
        $sql = "DELETE FROM  {$this->table} WHERE field_id = $field_id";
        Database::query($sql);
    }
    /**
     * Saves an option into the corresponding *_field_options table
     * @param array Parameters to be considered for the insertion
     * @param bool Whether to show the query (sent to the parent save() method)
     * @return bool True on success, false on error 
     */
    public function save($params, $show_query = false) {
        $field_id = intval($params['field_id']);
        
        if (empty($field_id)) {
            return false;
        }
        
        $time = api_get_utc_datetime();     
        if (!empty($params['field_options']) && 
            in_array($params['field_type'], array(
                ExtraField::FIELD_TYPE_RADIO, 
                ExtraField::FIELD_TYPE_SELECT, 
                ExtraField::FIELD_TYPE_SELECT_MULTIPLE, 
                ExtraField::FIELD_TYPE_DOUBLE_SELECT))
            ) {
            if ($params['field_type'] == ExtraField::FIELD_TYPE_DOUBLE_SELECT) {
                //$params['field_options'] = France:Paris;Bretagne;Marseilles;Lyon|Belgique:Bruxelles;Namur;Liège;Bruges|Peru:Lima;Piura;                
                $options_parsed = ExtraField::extra_field_double_select_convert_string_to_array($params['field_options']);                
                
                if (!empty($options_parsed)) {
                    foreach ($options_parsed as $key => $option) {                        
                        $sub_options = $option['options'];

                        $new_params = array(
                            'field_id' => $field_id,                            
                            'option_value' => 0,
                            'option_display_text' => $option['label'],
                            'option_order' => 0,
                            'tms' => $time,
                        );
                        //Looking if option already exists:
                        $option_info = self::get_field_option_by_field_id_and_option_display_text($field_id, $option['label']);
                        
                        if (empty($option_info)) {
                            $sub_id = parent::save($new_params, $show_query);
                        } else {
                            $sub_id = $option_info['id'];
                            $new_params['id'] = $sub_id;
                            parent::update($new_params, $show_query);                            
                        }                        
                        foreach ($sub_options as $sub_option) {                            
                            if (!empty($sub_option)) {
                                $new_params = array(
                                    'field_id' => $field_id,                            
                                    'option_value' => $sub_id,
                                    'option_display_text' => $sub_option,
                                    'option_order' => 0,
                                    'tms' => $time,
                                );
                                $option_info = self::get_field_option_by_field_id_and_option_display_text_and_option_value($field_id, $sub_option, $sub_id);                                                                
                                if (empty($option_info)) {
                                    parent::save($new_params, $show_query);                                     
                                } else {                                    
                                    $new_params['id'] = $option_info['id'];
                                    parent::update($new_params, $show_query);    
                                }                                
                            }
                            
                        }
                    }
                }
                $list = array();
            } else {
                $list = explode(';', $params['field_options']);
            }
            
            if (!empty($list)) {
                foreach ($list as $option) {
                    $option_info = self::get_field_option_by_field_and_option($field_id, $option);
                    
                    if ($option_info == false) {
                        $order = self::get_max_order($field_id);                        
                        $new_params = array(
                            'field_id' => $field_id,                            
                            'option_value' => $option,
                            'option_display_text' => $option,
                            'option_order' => $order,
                            'tms' => $time,
                        );
                        parent::save($new_params, $show_query);                         
                    }
                }
            }
        }
        return true;    
    }
    /**
     * Save one option item at a time
     * @param array Parameters specific to the option
     * @param bool Whether to show the query (sent to parent save() method)
     * @param bool Whether to insert even if the option already exists
     * @return bool True on success, false on failure
     */
    public function save_one_item($params, $show_query = false, $insert_repeated = true) {
        $field_id = intval($params['field_id']);        
        if (empty($field_id)) {
            return false;
        }
        $params['tms'] = api_get_utc_datetime();   
        if (empty($params['option_order'])) {
            $order = self::get_max_order($field_id);
            $params['option_order'] = $order;
        }
        if ($insert_repeated) {
            parent::save($params, $show_query); 
        } else {
            
            $check = self::get_field_option_by_field_and_option($field_id, $params['option_value']);
            if ($check == false) {
                parent::save($params, $show_query);    
            }
        }
        return true;
    }
    /**
     * Get the complete row of a specific option of a specific field
     * @param int Field ID
     * @param string Value of the option
     * @return mixed The row on success or false on failure
     */ 
    public function get_field_option_by_field_and_option($field_id, $option_value) {
        $field_id = intval($field_id);
        $option_value = Database::escape_string($option_value);
        
        $sql = "SELECT * FROM {$this->table} WHERE field_id = $field_id AND option_value = '".$option_value."'";
        $result = Database::query($sql);
        if (Database::num_rows($result) > 0) {
            return Database::store_result($result, 'ASSOC');
        }
        return false;        
    }
    /**
     * Get the complete row of a specific option's display text of a specific field
     * @param int Field ID
     * @param string Display value of the option
     * @return mixed The row on success or false on failure
     */
    public function get_field_option_by_field_id_and_option_display_text($field_id, $option_display_text) {
        $field_id = intval($field_id);
        $option_display_text = Database::escape_string($option_display_text);
        
        $sql = "SELECT * FROM {$this->table} WHERE field_id = $field_id AND option_display_text = '".$option_display_text."'";
        $result = Database::query($sql);
        if (Database::num_rows($result) > 0) {
            return Database::fetch_array($result, 'ASSOC');
        }
        return false;        
    }
    /**
     * Get the complete row of a specific option's display text of a specific field
     * @param int Field ID
     * @param string Display value of the option
     * @param string Value of the option
     * @return mixed The row on success or false on failure
     */
    public function get_field_option_by_field_id_and_option_display_text_and_option_value($field_id, $option_display_text, $option_value) {
        $field_id = intval($field_id);
        $option_display_text = Database::escape_string($option_display_text);
        $option_value = Database::escape_string($option_value);
        
        $sql = "SELECT * FROM {$this->table} WHERE field_id = $field_id AND option_display_text = '".$option_display_text."' AND option_value = '$option_value'";        
        $result = Database::query($sql);
        if (Database::num_rows($result) > 0) {
            return Database::fetch_array($result, 'ASSOC');
        }
        return false;        
    }
    /**
     * Gets an array of options for a specific field
     * @param int The field ID
     * @param bool Whether to add the row ID in the result
     * @param string Extra ordering query bit
     * @result mixed Row on success, false on failure
     */
    public function get_field_options_by_field($field_id, $add_id_in_array = false, $ordered_by = null) {
        $field_id = intval($field_id);
        $option_value = Database::escape_string($option_value);
        
        $sql = "SELECT * FROM {$this->table} 
                WHERE field_id = $field_id ";
        
        if (!empty($ordered_by)) {
            $sql .= " ORDER BY $ordered_by ";
        }
        
        $result = Database::query($sql);
        if (Database::num_rows($result) > 0) {
            if ($add_id_in_array) {
                $options = array();
                while ($row = Database::fetch_array($result, 'ASSOC')) {                    
                    $options[$row['id']] = $row;
                }
                return $options;
            } else {
                return Database::store_result($result, 'ASSOC');
            }
        }
        return false;        
    }
    /**
     * Get options for a specific field as array or in JSON format suited for the double-select format
     * @param int Field ID
     * @param int Option value ID
     * @param bool Return format (whether it should be formatted to JSON or not)
     * @return mixed Row/JSON on success
     */
    public function get_second_select_field_options_by_field($field_id, $option_value_id, $to_json = false) {
        $field_id = intval($field_id);
        $option_value_id = intval($option_value_id);        
        $options = array();
        $sql = "SELECT * FROM {$this->table} 
                WHERE field_id = $field_id AND option_value = $option_value_id 
                ORDER BY option_display_text";
        $result = Database::query($sql);
        if (Database::num_rows($result) > 0) {
            $options = Database::store_result($result, 'ASSOC');
        }
        
        if ($to_json) {
            $string = null;
            if (!empty($options)) {
                $array = array();
                foreach ($options as $option) {                    
                    $array[$option['id']] = $option['option_display_text'];
                }                
                $string = json_encode($array);                
            }
            return $string;
            
        }        
        return $options;    
    }
    
    /**
     * Get options for a specific field as string split by ;
     * @param int Field ID
     * @param string Extra query bit for reordering
     * @return string HTML string of options
     */
    public function get_field_options_by_field_to_string($field_id, $ordered_by = null) {        
        $field = new ExtraField($this->type);
        $field_info = $field->get($field_id);                
        $options = self::get_field_options_by_field($field_id, false, $ordered_by);        
        $elements = array();
        if (!empty($options)) {
            switch ($field_info['field_type']) {
                case ExtraField::FIELD_TYPE_DOUBLE_SELECT:                    
                    $html = ExtraField::extra_field_double_select_convert_array_to_string($options);                    
                    break;
                default:
                    foreach ($options as $option) {
                        $elements[]= $option['option_value'];
                    }
                    $html = implode(';', $elements);
                    break;                
            }
            
            return $html;
        }
        return null;
        
    }
    /**
     * Get the maximum order value for a specific field
     * @param int Field ID
     * @return int Current max ID + 1 (we start from 0)
     */
    public function get_max_order($field_id) {
        $field_id = intval($field_id);
        $sql = "SELECT MAX(option_order) FROM {$this->table} WHERE field_id = $field_id";
        $res = Database::query($sql);
        $max = 1;
        if (Database::num_rows($res) > 0) {
            $row = Database::fetch_array($res);
            $max = $row[0] + 1;
        }
        return $max;
    }
    /**
     * Update the option using the given params
     * @param array Parameters with the data to be saved
     */
    public function update($params) {
        parent::update($params);
    }
    
    /**
     * Display a form with the options for the field_id given in REQUEST
     * @return void Prints output
     */
    function display() {
        // action links
        echo '<div class="actions">';
       	//echo  '<a href="../admin/index.php">'.Display::return_icon('back.png', get_lang('BackTo').' '.get_lang('PlatformAdmin'),'', ICON_SIZE_MEDIUM).'</a>';	   
        $field_id = isset($_REQUEST['field_id']) ? intval($_REQUEST['field_id']) : null;
        echo '<a href="'.api_get_self().'?action=add&type='.$this->type.'&field_id='.$field_id.'">'.Display::return_icon('add_user_fields.png',get_lang('Add'),'', ICON_SIZE_MEDIUM).'</a>';               
        echo '</div>';
        echo Display::grid_html('extra_field_options');
    }
    /**
     * Returns an HTML form for the current field
     * @param string URL to send the form to (action=...)
     * @param string Type of action to offer through the form (edit, usually)
     * @return string HTML form
     */
    public function return_form($url, $action) {
		$form_name = $this->type.'_field';
        $form = new FormValidator($form_name, 'post', $url);
        // Settting the form elements
        $header = get_lang('Add');        
        if ($action == 'edit') {
            $header = get_lang('Modify');
        }
        
        $form->addElement('header', $header);
        $id = isset($_GET['id']) ? intval($_GET['id']) : '';
        
        $form->addElement('hidden', 'id', $id);       
        $form->addElement('hidden', 'type', $this->type);
        $form->addElement('hidden', 'field_id', $this->field_id);       
        
        $form->addElement('text', 'option_display_text', get_lang('Name'), array('class' => 'span5'));  
        $form->addElement('text', 'option_value', get_lang('Value'), array('class' => 'span5'));
        $form->addElement('text', 'option_order', get_lang('Order'), array('class' => 'span2'));
        
        $defaults = array();
        
        if ($action == 'edit') {
            // Setting the defaults
            $defaults = $this->get($id);
            $form->freeze('option_value');
        	$form->addElement('button', 'submit', get_lang('Modify'), 'class="save"');
        } else {            
        	$form->addElement('button', 'submit', get_lang('Add'), 'class="save"');
        }
        
        $form->setDefaults($defaults);
    
        // Setting the rules
        $form->addRule('option_display_text', get_lang('ThisFieldIsRequired'), 'required');        
        //$form->addRule('field_variable', get_lang('ThisFieldIsRequired'), 'required');
        $form->addRule('option_value', get_lang('ThisFieldIsRequired'), 'required');        
		return $form;
    }  
}
