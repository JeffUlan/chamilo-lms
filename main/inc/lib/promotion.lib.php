<?php
/* For licensing terms, see /license.txt */

/**
*	This class provides methods for the promotion management.
*	Include/require it in your code to use its features.
*	@package chamilo.library
*/

require_once 'model.lib.php';
require_once 'career.lib.php';
require_once 'fckeditor/fckeditor.php';

define ('PROMOTION_STATUS_ACTIVE',  1);
define ('PROMOTION_STATUS_INACTIVE',0);   

class Promotion extends Model {
    
    var $table;
    var $columns = array('id','name','description','career_id','status','created_at','updated_at');
    
	public function __construct() {
        $this->table =  Database::get_main_table(TABLE_PROMOTION);
	}
    
    /**
     * Gets all promotions by career id
     * @param   int     career id
     * @return  array   results
     */
    public function get_all_promotions_by_career_id($career_id, $order = false) {        
        return Database::select('*', $this->table, array('where'=>array('career_id = ?'=>$career_id),'order' =>$order));
    }
    
    public function get_status_list() {
    	return array(PROMOTION_STATUS_ACTIVE => get_lang('Active'), PROMOTION_STATUS_INACTIVE => get_lang('Inactive'));
    } 
   
    /**
     * Displays the title + grid
     * @return  string  html code
     */
	function display() {
		// action links
		echo '<div class="actions" style="margin-bottom:20px">';
        echo '<a href="career_dashboard.php">'.Display::return_icon('back.png',get_lang('Back'),'','32').'</a>';
		echo '<a href="'.api_get_self().'?action=add">'.Display::return_icon('new_promotion.png',get_lang('Add'),'','32').'</a>';			
		echo '<a href="'.api_get_path(WEB_CODE_PATH).'admin/session_add.php">'.Display::return_icon('new_session.png',get_lang('AddSession'),'','32').'</a>';			
		echo '</div>';
        echo Display::grid_html('promotions');  
	}
    
    /**
     * Update all session status by promotion
     * @param   int     promotion id
     * @param   int     status (1, 0)
    */
    public function update_all_sessions_status_by_promotion_id($promotion_id, $status) { 
        require_once api_get_path(LIBRARY_PATH).'sessionmanager.lib.php';               
        $session_list   = SessionManager::get_all_sessions_by_promotion($promotion_id);    
        if (!empty($session_list)) {
            foreach($session_list  as $item) {
                SessionManager::set_session_status($item['id'], $status);            
            }
        }
    }
    
        
    /**
     * Returns a Form validator Obj
     * @todo the form should be auto generated
     * @param   string  url
     * @param   string  header name
     * @return  obj     form validator obj 
     */
     
    function return_form($url, $action = 'add') {
    	
		$oFCKeditor = new FCKeditor('description') ;
				$oFCKeditor->ToolbarSet = 'careers';
				$oFCKeditor->Width		= '100%';
				$oFCKeditor->Height		= '200';
				$oFCKeditor->Value		= $message;
				$oFCKeditor->CreateHtml();		
		
		$form = new FormValidator('promotion', 'post', $url);
        // Settting the form elements
        $header = get_lang('Add');
        if ($action == 'edit') {
        	$header = get_lang('Modify');
        }
        $form->addElement('header', '', $header);
        $form->addElement('hidden', 'id', intval($_GET['id']));
        $form->addElement('text', 'name', get_lang('Name'), array('size' => '70','id' => 'name'));        
        $form->add_html_editor('description', get_lang('Description'), false, false, array('ToolbarSet' => 'careers','Width' => '100%', 'Height' => '250'));       
        $career = new Career();
        $careers = $career->get_all();
        $career_list = array();    
        foreach($careers as $item) {        
            $career_list[$item['id']] = $item['name'];
        }
        $form->addElement('select', 'career_id', get_lang('Career'), $career_list);
        
        $status_list = $this->get_status_list();         
        $form->addElement('select', 'status', get_lang('Status'), $status_list);
        if ($action == 'edit') {
            $form->addElement('text', 'created_at', get_lang('CreatedAt'));
            $form->freeze('created_at');
        }         
        $form->addElement('style_submit_button', 'submit', get_lang('Modify'), 'class="save"');
    
        // Setting the defaults
        $defaults = $this->get($_GET['id']);
        $defaults['created_at'] = api_convert_and_format_date($defaults['created_at']);
        $defaults['updated_at'] = api_convert_and_format_date($defaults['updated_at']);            
        $form->setDefaults($defaults);
    
        // Setting the rules
        $form->addRule('name', '<div class="required">'.get_lang('ThisFieldIsRequired'), 'required');
        
        return $form;
    }
    /**
     * Copies the promotion to a new one
     * @param   integer     Promotion ID
     * @param   integer     Career ID, in case we want to change it
     * @param   boolean     Whether or not to copy the sessions inside 
     * @return  integer     New promotion ID on success, false on failure
     */
    public function copy($id, $career_id = null, $copy_sessions = false) {
        $promotion = $this->get($id);
        $new = array();
        foreach ($promotion as $key => $val) {
            switch ($key) {
            	case 'id':
                case 'updated_at':
                    break;
                case 'name':
                    $val .= ' '.get_lang('Copy');
                    $new[$key] = $val;
                    break;
                case 'created_at':
                    $val = api_get_utc_datetime();
                    $new[$key] = $val;
                    break;
                case 'career_id':
                    if (!empty($career_id)) {
                    	$val = (int)$career_id;
                    }
                    $new[$key] = $val;
                default:
                    $new[$key] = $val;
                    break;
            }
        }     
        $pid = false;   
        if ($copy_sessions) {
            /**
             * When copying a session we do:
             * 1. Copy a new session from the source
             * 2. Copy all courses from the session (no user data, no user list) 
             * 3. Create the promotion           
             */
            require_once api_get_path(LIBRARY_PATH).'sessionmanager.lib.php';
            $session_list   = SessionManager::get_all_sessions_by_promotion($id); 
        
            if (!empty($session_list)) {
                foreach($session_list  as $item) {
                    $sid = SessionManager::copy_session($item['id'], true, false, true, true);                    
                    if ($sid != 0) {
                        $pid = $this->save($new);
                        SessionManager::suscribe_sessions_to_promotion($pid,array($sid));
                    }
                }
            }
        } else {
            $pid = $this->save($new);
        }        
    	return $pid;
    }
}