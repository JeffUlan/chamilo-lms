<?php
/* For licensing terms, see /dokeos_license.txt */

/**
 * This library provides functions for course description tool. It's also used like model to course_description_controller (MVC pattern)
 * @package dokeos.library
 * @author Christian Fasanando <christian1827@gmail.com>
 */

class CourseDescription
{	
	private $id;
	private $title;
	private $content;
    private $session_id;
    private $description_type;
    private $progress;
   
   	/**
	 * Constructor
	 */
	public function __construct() {}
   
    /**
     * Get all data of course description by session id, 
     * first you must set session_id property with the object CourseDescription
     * @return array 
     */
	public function get_description_data() {				
		$tbl_course_description = Database::get_course_table(TABLE_COURSE_DESCRIPTION);
		$sql = "SELECT * FROM $tbl_course_description WHERE session_id = '".$this->session_id."' ORDER BY id ";
		$rs = Database::query($sql, __FILE__, __LINE__);		
		$data = array();		
		while ($description = Database::fetch_array($rs)) {
			if ($description['description_type'] == THEMATIC_ADVANCE) {
				$description['progress_icon'] = $this->get_progress_porcent();
			}
			$data['descriptions'][$description['description_type']] = $description;			
			//reload titles to ensure we have the last version (after edition)
			$data['default_description_titles'][$description['description_type']] = $description['title'];
		}				
		return $data;		
	}
	
	/**
     * Get all data of course description by session id, 
     * first you must set session_id property with the object CourseDescription
     * @return array 
     */
	public function get_description_history($description_type) {				
		$tbl_stats_item_property = Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_ITEM_PROPERTY);
		$tbl_item_property = Database::get_course_table(TABLE_ITEM_PROPERTY);
		$course_id = api_get_real_course_id();

		$description_id = $this->get_id_by_description_type($description_type);		
		$item_property_id = api_get_item_property_id($course_id, TOOL_COURSE_DESCRIPTION, $description_id);
							
		$sql = "SELECT * FROM $tbl_stats_item_property tip
				INNER JOIN $tbl_item_property ip ON ip.tool = '".TOOL_COURSE_DESCRIPTION."' AND ip.id = tip.item_property_id
				WHERE tip.course_id = '$course_id' AND tip.session_id = '".intval($this->session_id)."'";

		$rs = Database::query($sql, __FILE__, __LINE__);		
		$data = array();		
		while ($description = Database::fetch_array($rs)) {
			$data['descriptions'][] = $description;
		}				
		return $data;		
	}
	
	/**
     * Get all data by description and session id, 
     * first you must set session_id property with the object CourseDescription
     * @param 	int	description type
     * @return array
     */
	public function get_data_by_description_type($description_type) {		
		$tbl_course_description = Database::get_course_table(TABLE_COURSE_DESCRIPTION);
		$sql = "SELECT * FROM $tbl_course_description WHERE description_type='$description_type' AND session_id='".$this->session_id."'";
		$rs = Database::query($sql, __FILE__, __LINE__);				
		$data = array();		
		if ($description = Database::fetch_array($rs)) {
			$data['description_title']	 = $description['title'];
			$data['description_content'] = $description['content'];
			$data['progress'] 			 = $description['progress'];
		}						
		return $data;		
	}
	
	/**
     * Get maximum description type by session id, first you must set session_id properties with the object CourseDescription
     * @return  int  maximum description time adding one
     */
	public function get_max_description_type() {
		$tbl_course_description = Database::get_course_table(TABLE_COURSE_DESCRIPTION);
		$sql = "SELECT MAX(description_type) as MAX FROM $tbl_course_description WHERE session_id='".$this->session_id."'";
		$rs  = Database::query($sql, __FILE__, __LINE__);
		$max = Database::fetch_array($rs);	
		$description_type = $max['MAX']+1;
		if ($description_type < ADD_BLOCK) {
			$description_type = ADD_BLOCK;
		}		
		return $description_type;				
	}
	
	/**
     * Insert a description to the course_description table, 
     * first you must set description_type, title, content, progress and session_id properties with the object CourseDescription
     * @return  int  affected rows 
     */
	public function insert() {
		$tbl_course_description = Database::get_course_table(TABLE_COURSE_DESCRIPTION);		
		$sql = "INSERT IGNORE INTO $tbl_course_description SET description_type='".intval($this->description_type)."', title = '".Database::escape_string($this->title)."', content = '".Database::escape_string($this->content)."', progress = '".intval($this->progress)."', session_id = '".intval($this->session_id)."' ";
		Database::query($sql, __FILE__, __LINE__);
		$last_id = Database::insert_id();
		$affected_rows = Database::affected_rows();
		if ($last_id > 0) {
			//insert into item_property
			api_item_property_update(api_get_course_info(), TOOL_COURSE_DESCRIPTION, $last_id, 'CourseDescriptionAdded', api_get_user_id());
		}
		return $affected_rows;
							
	}
	
	/**
     * Insert a row like history inside track_e_item_property table  
     * first you must set description_type, title, content properties with the object CourseDescription
     * @param 	int 	description type
     * @return  int		affected rows 
     */
	public function insert_stats($description_type) {		
		$tbl_stats_item_property = Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_ITEM_PROPERTY);		
		$description_id = $this->get_id_by_description_type($description_type);
		$course_id = api_get_real_course_id();						
		$course_code = api_get_course_id();
		$item_property_id = api_get_item_property_id($course_code, TOOL_COURSE_DESCRIPTION, $description_id);		
		$sql = "INSERT IGNORE INTO $tbl_stats_item_property SET
				course_id = '$course_id',
			 	item_property_id = '$item_property_id', 			 	
			 	title = '".Database::escape_string($this->title)."', 
			 	content = '".Database::escape_string($this->content)."',
			 	lastedit_date = '".date('Y-m-d H:i:s')."',
			 	lastedit_user_id = '".api_get_user_id()."',
			 	session_id	= '".intval($this->session_id)."'";
		Database::query($sql, __FILE__, __LINE__);	
		$affected_rows = Database::affected_rows();
		return $affected_rows;			
	}
	
	/**
     * Update a description, first you must set description_type, title, content, progress 
     * and session_id properties with the object CourseDescription
     * @return int	affected rows 
     */
	public function update() {		
		$tbl_course_description = Database::get_course_table(TABLE_COURSE_DESCRIPTION);		
		$sql = "UPDATE $tbl_course_description SET  title = '".Database::escape_string($this->title)."', content = '".Database::escape_string($this->content)."', progress = '".$this->progress."' WHERE description_type='".intval($this->description_type)."' AND session_id = '".$this->session_id."'";		
		Database::query($sql, __FILE__, __LINE__);
		$affected_rows = Database::affected_rows();		
		$description_id = $this->get_id_by_description_type($this->description_type);
		if ($description_id > 0) {
			//insert into item_property
			api_item_property_update(api_get_course_info(), TOOL_COURSE_DESCRIPTION, $description_id, 'CourseDescriptionUpdated', api_get_user_id());
		}
		return $affected_rows;
	}
	
	/**
     * Delete a description, first you must set description_type and session_id properties with the object CourseDescription
     * @return int	affected rows
     */
	public function delete() {		
		$tbl_course_description = Database::get_course_table(TABLE_COURSE_DESCRIPTION);
		$description_id = $this->get_id_by_description_type($this->description_type);		
		$sql = "DELETE FROM $tbl_course_description WHERE description_type = '".intval($this->description_type)."' AND session_id = '".intval($this->session_id)."'";				
		Database::query($sql, __FILE__, __LINE__);
		$affected_rows = Database::affected_rows();		
		if ($description_id > 0) {
			//insert into item_property
			api_item_property_update(api_get_course_info(), TOOL_COURSE_DESCRIPTION, $description_id, 'CourseDescriptionDeleted', api_get_user_id());
		}
		return $affected_rows;
	}
	
	/**
	 * Get description id by description type
	 * @param int description type
	 * @return int description id
	 */
	public function get_id_by_description_type($description_type) {		
		$tbl_course_description = Database::get_course_table(TABLE_COURSE_DESCRIPTION);
		$sql = "SELECT id FROM $tbl_course_description WHERE description_type = '".intval($description_type)."'";
		$rs  = Database::query($sql, __FILE__, __LINE__);
		$row = Database::fetch_array($rs);
		$description_id = $row['id']; 	
		return $description_id;		
	}
	
	/**
	 * get thematic progress in porcent for a course, 
	 * first you must set session_id property with the object CourseDescription
	 * @param bool		true for showing a icon about the progress, false otherwise (optional)
	 * @param int		Description type (optional)
	 * @return string   img html
	 */
	 public function get_progress_porcent($with_icon = false, $description_type = THEMATIC_ADVANCE) {
	 	$tbl_course_description = Database::get_course_table(TABLE_COURSE_DESCRIPTION);
	 	$session_id = intval($session_id);
		$sql = "SELECT progress FROM $tbl_course_description WHERE description_type = '".intval($description_type)."' AND session_id = '".intval($this->session_id)."' ";
		$rs  = Database::query($sql, __FILE__, __LINE__);
		$progress = '';
		$img = '';
		$title = '0%';
		$image = 'level_0.png';
		if (Database::num_rows($rs) > 0) {
			$row = Database::fetch_array($rs);		
			$progress = $row['progress'].'%';
			$image = 'level_'.$row['progress'].'.png';				
		}		
		if ($with_icon) {
			$img = Display::return_icon($image,get_lang('ThematicAdvance'),array('style'=>'vertical-align:middle'));	
		}					
		$progress = $img.$progress;
		return $progress;
	 }
	
	/**
	 * Get description titles by default
	 * @return array 
	 */
	public function get_default_description_title() {		
		$default_description_titles = array();
		$default_description_titles[1]= get_lang('GeneralDescription');
		$default_description_titles[2]= get_lang('Objectives');
		$default_description_titles[3]= get_lang('Topics');
		$default_description_titles[4]= get_lang('Methodology');
		$default_description_titles[5]= get_lang('CourseMaterial');
		$default_description_titles[6]= get_lang('HumanAndTechnicalResources');
		$default_description_titles[7]= get_lang('Assessment');
		$default_description_titles[8]= get_lang('ThematicAdvance');
		$default_description_titles[9]= get_lang('Other');
		return $default_description_titles;
	}
	
	/**
	 * Get description titles editable by default
	 * @return array 
	 */
	public function get_default_description_title_editable() {
		$default_description_title_editable = array();
		$default_description_title_editable[1] = true;
		$default_description_title_editable[2] = true;
		$default_description_title_editable[3] = true;
		$default_description_title_editable[4] = true;
		$default_description_title_editable[5] = true;
		$default_description_title_editable[6] = true;
		$default_description_title_editable[7] = true;
		$default_description_title_editable[8] = true;
		return $default_description_title_editable;		
	}
	
	/**
	 * Get description icons by default
	 * @return array 
	 */
	public function get_default_description_icon() {		
		$default_description_icon = array();
		$default_description_icon[1]= 'edu_miscellaneous.gif';
		$default_description_icon[2]= 'spire.gif';
		$default_description_icon[3]= 'kcmdf_big.gif';
		$default_description_icon[4]= 'misc.gif';
		$default_description_icon[5]= 'laptop.gif';
		$default_description_icon[6]= 'personal.gif';
		$default_description_icon[7]= 'korganizer.gif';
		$default_description_icon[8]= 'porcent.png';
		$default_description_icon[9]= 'ktip.gif';
		return $default_description_icon;
	}
	
	/**
	 * Get questions by default for help 
	 * @return array 
	 */
	public function get_default_question() {		
		$question = array();
		$question[1]= get_lang('GeneralDescriptionQuestions');
		$question[2]= get_lang('ObjectivesQuestions');
		$question[3]= get_lang('TopicsQuestions');
		$question[4]= get_lang('MethodologyQuestions');
		$question[5]= get_lang('CourseMaterialQuestions');
		$question[6]= get_lang('HumanAndTechnicalResourcesQuestions');
		$question[7]= get_lang('AssessmentQuestions');
		$question[8]= get_lang('thematicAdvanceQuestions');
		return $question;
	}
	
	/**
	 * Get informations by default for help 
	 * @return array 
	 */
	public function get_default_information() {
		$information = array();
		$information[1]= get_lang('GeneralDescriptionInformation');
		$information[2]= get_lang('ObjectivesInformation');
		$information[3]= get_lang('TopicsInformation');
		$information[4]= get_lang('MethodologyInformation');
		$information[5]= get_lang('CourseMaterialInformation');
		$information[6]= get_lang('HumanAndTechnicalResourcesInformation');
		$information[7]= get_lang('AssessmentInformation');		
		$information[8]= get_lang('ThematicAdvanceInformation');
		return $information;
	}
	
	/**
	 * Set description id
	 * @return void
	 */
	public function set_id($id) {
		$this->id = $id;
	}
   
   	/**
	 * Set description title
	 * @return void
	 */
	public function set_title($title) {
		$this->title = $title;
	}
   
    /**
	 * Set description content
	 * @return void
	 */
	public function set_content($content) {
		$this->content = $content;
	}
	
	/**
	 * Set description session id
	 * @return void
	 */
	public function set_session_id($session_id) {
		$this->session_id = $session_id;
	}
   
   	/**
	 * Set description type
	 * @return void
	 */
	public function set_description_type($description_type) {
		$this->description_type = $description_type;
	}
	
	/**
	 * Set progress of a description
	 * @return void
	 */
	public function set_progress($progress) {
		$this->progress = $progress;
	}
	
	/**
	 * get description id
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}
	
   	/**
	 * get description title
	 * @return string 
	 */
	public function get_title() {
		return $this->title;
	}
   
   	/**
	 * get description content
	 * @return string
	 */
	public function get_content() {
		return $this->content;
	}
	
	/**
	 * get session id
	 * @return int
	 */
	public function get_session_id() {
		return $this->session_id;
	}
   
   	/**
	 * get description type
	 * @return int
	 */
	public function get_description_type() {
		return $this->description_type;
	}
	
	/**
	 * get progress of a description
	 * @return int
	 */
	public function get_progress() {
		return $this->progress;
	}
		
}
?>