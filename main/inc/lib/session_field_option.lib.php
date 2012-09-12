<?php
/* For licensing terms, see /license.txt */

class SessionFieldOption extends Model {
     public $columns = array('id', 'field_id', 'option_value', 'option_display_text', 'option_order', 'tms');
     
     public function __construct() {
        $this->table = Database::get_main_table(TABLE_MAIN_SESSION_FIELD_OPTIONS);        
     }
     
    public function get_count() {
        $row = Database::select('count(*) as count', $this->table, array(), 'first');
        return $row['count'];
    }    
    
    public function get_field_options_to_string($field_id) {
        $options = self::get_field_options_by_field($field_id);
        $new_options = array();        
        if (!empty($options)) {
            foreach ($options as $option) {
                $new_options[] = $option['option_value'].':'.$option['option_value'];
            }
            $string = implode(';', $new_options);
            return $string;
        }
    }
    
    public function delete_all_options_by_field_id($field_id) {
        $field_id = intval($field_id);
        $sql = "DELETE FROM  {$this->table} WHERE field_id = $field_id";
        Database::query($sql);
    }
    
    public function save($params, $show_query = false) {
        $field_id = intval($params['field_id']);
        
        if (empty($field_id)) {
            return false;
        }
        
        $time = api_get_utc_datetime();     
        if (!empty($params['field_options']) && 
            in_array($params['field_type'], array(
                UserManager::USER_FIELD_TYPE_RADIO, 
                UserManager::USER_FIELD_TYPE_SELECT, 
                UserManager::USER_FIELD_TYPE_SELECT_MULTIPLE, 
                UserManager::USER_FIELD_TYPE_DOUBLE_SELECT))
            ) {
            if ($params['field_type'] == UserManager::USER_FIELD_TYPE_DOUBLE_SELECT) {
                //$params['field_options'] = France:Paris;Bretagne;Marseilles;Lyon|Belgique:Bruxelles;Namur;Liège;Bruges|Peru:Lima;Piura;                
                $options_parsed = UserManager::extra_field_double_select_convert_string_to_array($params['field_options']);                
                
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
    
    
    
    public function get_field_options_by_field($field_id) {
        $field_id = intval($field_id);
        $option_value = Database::escape_string($option_value);
        
        $sql = "SELECT * FROM {$this->table} WHERE field_id = $field_id ";
        $result = Database::query($sql);
        if (Database::num_rows($result) > 0) {
            return Database::store_result($result, 'ASSOC');
        }
        return false;        
    }
    
    public function get_second_select_field_options_by_field($field_id, $option_value_id, $to_json = false) {
        $field_id = intval($field_id);
        $option_value_id = intval($option_value_id);        
        $options = array();
        $sql = "SELECT * FROM {$this->table} WHERE field_id = $field_id AND option_value = $option_value_id ";
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
    
    public function get_field_options_by_field_to_string($field_id) {
        $session_field = new SessionField();
        $field_info = $session_field->get($field_id);
        
        $options = self::get_field_options_by_field($field_id);
        $elements = array();
        if (!empty($options)) {
            switch ($field_info['field_type']) {
                case UserManager::USER_FIELD_TYPE_DOUBLE_SELECT:                    
                    $html = UserManager::extra_field_double_select_convert_array_to_string($options);                    
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
    
    public function update($params) {
        parent::update($params);
    }     
}