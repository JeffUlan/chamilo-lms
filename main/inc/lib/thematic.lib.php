<?php
/* For licensing terms, see /license.txt */

/**
 * This file contains class used like library, provides functions for thematic option inside attendance tool. It's also used like model to thematic_controller (MVC pattern)
 * @author Christian Fasanando <christian1827@gmail.com>
 * @author Julio Montoya 	<gugli100@gmail.com> SQL fixes + improvements
 * @package chamilo.attendance
 */

/**
 * Thematic class can be used to instanciate objects or as a library for thematic control
 * @package chamilo.attendance
 */
class Thematic
{	
	private $session_id;
	private $thematic_id;
	private $thematic_title;
	private $thematic_content;
	private $thematic_plan_id;
	private $thematic_plan_title;
	private $thematic_plan_description;
	private $thematic_plan_description_type;	
	private $thematic_advance_id;
	private $attendance_id;
	private $thematic_advance_content;
	private	$start_date;
	private $duration;

	public function __construct() {}
	
	
	/**
	 * Get the total number of thematic inside current course and current session
	 * @see SortableTable#get_total_number_of_items()
	 */
	public function get_number_of_thematics() {	
		$tbl_thematic = Database :: get_course_table(TABLE_THEMATIC);
		$session_id = api_get_session_id();
		$condition_session = api_get_session_condition($session_id);
		$sql = "SELECT COUNT(id) AS total_number_of_items FROM $tbl_thematic WHERE active = 1 $condition_session ";
		$res = Database::query($sql);
		$res = Database::query($sql);
		$obj = Database::fetch_object($res);		
		return $obj->total_number_of_items;
	}
	
	
	/**
	 * Get the thematics to display on the current page (fill the sortable-table)
	 * @param   int     offset of first user to recover
	 * @param   int     Number of users to get
	 * @param   int     Column to sort on
	 * @param   string  Order (ASC,DESC)
	 * @see SortableTable#get_table_data($from)
	 */
	public function get_thematic_data($from, $number_of_items, $column, $direction) {
		$tbl_thematic = Database :: get_course_table(TABLE_THEMATIC);
		$session_id = api_get_session_id();
		$condition_session = api_get_session_condition($session_id);
	    $column = intval($column);
	    $from = intval($from);
	    $number_of_items = intval($number_of_items);
		if (!in_array($direction, array('ASC','DESC'))) {
	    	$direction = 'ASC';
	    }
		$sql = "SELECT
				id AS col0,
				title AS col1,
				display_order AS col2								
				FROM $tbl_thematic
				WHERE active = 1 $condition_session
				ORDER BY col2 LIMIT $from,$number_of_items ";
		$res = Database::query($sql);
		$thematics = array ();

		$param_gradebook = '';
		if (isset($_SESSION['gradebook'])) {
			$param_gradebook = '&gradebook='.$_SESSION['gradebook'];
		}

		while ($thematic = Database::fetch_row($res)) {			
			$thematic[1] = '<a href="index.php?'.api_get_cidreq().'&action=thematic_details&thematic_id='.$thematic[0].$param_gradebook.'">'.Security::remove_XSS($thematic[1], STUDENT).'</a>';			
			if (api_is_allowed_to_edit(null, true)) {
				$actions  = '';						
				$actions .= '<center><a href="index.php?'.api_get_cidreq().'&action=thematic_plan_list&thematic_id='.$thematic[0].$param_gradebook.'">'.Display::return_icon('info.gif',get_lang('ThematicPlan')).'</a>&nbsp;';
				$actions .= '<a href="index.php?'.api_get_cidreq().'&action=thematic_advance_list&thematic_id='.$thematic[0].$param_gradebook.'">'.Display::return_icon('porcent.png',get_lang('ThematicAdvance')).'</a>&nbsp;';
				
				if ($thematic[2] > 1) {

					$actions .= '<a href="'.api_get_self().'?action=moveup&'.api_get_cidreq().'&thematic_id='.$thematic[0].$param_gradebook.'">'.Display::return_icon('up.gif', get_lang('Up')).'</a>';
				} else {
					$actions .= Display::return_icon('up_na.gif','&nbsp;');
	
				}
				if ($thematic[2] < self::get_max_thematic_item()) {
					$actions .= '<a href="'.api_get_self().'?action=movedown&a'.api_get_cidreq().'&thematic_id='.$thematic[0].$param_gradebook.'">'.Display::return_icon('down.gif',get_lang('Down')).'</a>';
				} else {
					$actions .= Display::return_icon('down_na.gif','&nbsp;');
				}
				
				$actions .= '<a href="index.php?'.api_get_cidreq().'&action=thematic_edit&thematic_id='.$thematic[0].$param_gradebook.'">'.Display::return_icon('edit.gif',get_lang('Edit')).'</a>&nbsp;';
				$actions .= '<a onclick="javascript:if(!confirm(\''.get_lang('AreYouSureToDelete').'\')) return false;" href="index.php?'.api_get_cidreq().'&action=thematic_delete&thematic_id='.$thematic[0].$param_gradebook.'">'.Display::return_icon('delete.gif',get_lang('Delete')).'</a></center>';
				$thematics[] = array($thematic[0], $thematic[1], $actions);
			} 
		}
		return $thematics;
	}
	
	/**
	 * Get the maximum display order of the thematic item
	 * @return int	Maximum display order
	 */
	public function get_max_thematic_item() {
		// Database table definition
		$tbl_thematic = Database :: get_course_table(TABLE_THEMATIC);
		$session_id = api_get_session_id();
		$condition_session = api_get_session_condition($session_id);
		$sql = "SELECT MAX(display_order) FROM $tbl_thematic WHERE active = 1 $condition_session";
		$rs = Database::query($sql);
		$dsp=0;
		$row = Database::fetch_array($rs);
		return $row[0];
	}
	
	/**
	 * Move a thematic
	 *
	 * @param string	Direction (up, down)
	 * @param int		Thematic id
	 */
	public function move_thematic($direction, $thematic_id) {
		// Database table definition
		$tbl_thematic = Database :: get_course_table(TABLE_THEMATIC);

		// sort direction
		if ($direction == 'up') {
			$sortorder = 'DESC';
		} else {
			$sortorder = 'ASC';
		}

		$session_id = api_get_session_id();
		$condition_session = api_get_session_condition($session_id);
		
		$sql = "SELECT id, display_order FROM $tbl_thematic WHERE active = 1 $condition_session ORDER BY display_order $sortorder";
		$res = Database::query($sql);
		$found = false;
		
		//Variable definition 
		$current_id = 0;
		$next_id 	= 0;
		
		while ($row = Database::fetch_array($res)) {
			if ($found == true && empty($next_id)) {
				$next_id = intval($row['id']);
				$next_display_order = intval($row['display_order']);
			}

			if ($row['id'] == $thematic_id) {
				$current_id = intval($thematic_id);
				$current_display_order = intval($row['display_order']);
				$found = true;
			}
		}

		// get last done thematic advance before move thematic list
		$last_done_thematic_advance = $this->get_last_done_thematic_advance();
		
		if (!empty($next_display_order) && !empty($current_id)) {
			$sql = "UPDATE $tbl_thematic SET display_order = $next_display_order WHERE id = $current_id ";
			Database::query($sql);
		}
		if (!empty($current_display_order) && !empty($next_id)) {
			$sql = "UPDATE $tbl_thematic SET display_order = $current_display_order WHERE id = $next_id ";
			Database::query($sql);
		}
		
		// update done advances with de current thematic list
		$update_done_advances = $this->update_done_thematic_advances($last_done_thematic_advance);

	}
	
	/**
	 * get thematic list
	 * @param	int		Thematic id (optional), get list by id
	 * @return	array	Thematic data
	 */	 
	 public function get_thematic_list($thematic_id = null, $course_code = null, $session_id = null) {
	 	
	 	// set current course and session
	 	if (isset($course_code)) {
	 		$course_info = api_get_course_info($course_code);
	 		$tbl_thematic = Database :: get_course_table(TABLE_THEMATIC, $course_info['dbName']);
	 	} else {
	 		$tbl_thematic = Database :: get_course_table(TABLE_THEMATIC);	
	 	}
	 	
	 	if (isset($session_id)) {
	 		$session_id = intval($session_id);
	 	} else {
	 		$session_id = api_get_session_id();
	 	}

	    $data = array();
	    $condition = '';	    	    
	    if (isset($thematic_id)) {
	    	$thematic_id = intval($thematic_id);
	    	$condition = " WHERE id = $thematic_id ";
	    } else {	    	
	    	$condition_session = api_get_session_condition($session_id);
	    	$condition = " WHERE active = 1 $condition_session ";
	    }
		$sql = "SELECT * FROM $tbl_thematic $condition ORDER BY display_order ";
		$res = Database::query($sql);
		if (Database::num_rows($res) > 0) {
			if (!empty($thematic_id)) {
				$data = Database::fetch_array($res);	
			} else {
				while ($row = Database::fetch_array($res)) {
					$data[$row['id']] = $row;	
				}
			}
		}
		return $data;

	 }
	
	/**
	 * insert or update a thematic
	 * @return int last thematic id
	 */
	public function thematic_save() {
		global $_course;
		// definition database table		
		$tbl_thematic = Database::get_course_table(TABLE_THEMATIC);
				
		// protect data
		$id = intval($this->thematic_id);
		$title = Database::escape_string($this->thematic_title);
		$content = Database::escape_string($this->thematic_content);
		$session_id = intval($this->session_id);
		$user_id = api_get_user_id();
		
		// get the maximum display order of all the glossary items
		$max_thematic_item = $this->get_max_thematic_item();		
		
		if (empty($id)) {			
			// insert
			$sql = "INSERT INTO $tbl_thematic(title, content, active, display_order, session_id) VALUES ('$title', '$content', 1, ".(intval($max_thematic_item)+1).", $session_id) ";
			Database::query($sql);
			$last_id = Database::insert_id();			
			if (Database::affected_rows()) {
				// save inside item property table
				$last_id = Database::insert_id();
				api_item_property_update($_course, TOOL_COURSE_PROGRESS, $last_id,"CourseProgressAdded", $user_id);
			}			
		} else {
			// update
			$sql = "UPDATE $tbl_thematic SET title = '$title', content = '$content', session_id = $session_id WHERE id = $id ";
			Database::query($sql);
			$last_id = $id;
			if (Database::affected_rows()) {
				// save inside item property table
				$last_id = Database::insert_id();
				api_item_property_update($_course, TOOL_COURSE_PROGRESS, $last_id,"CourseProgressUpdated", $user_id);
			}
		}		
		return $last_id;			
	}
	
	/**
	 * Delete logically (set active field to 0) a thematic
	 * @param	int|array	One or many thematic ids
	 * @return	int			Affected rows
	 */
	public function thematic_destroy($thematic_id) {
		global $_course;
		$tbl_thematic = Database::get_course_table(TABLE_THEMATIC);
		$affected_rows = 0;
		$user_id = api_get_user_id();
		if (is_array($thematic_id)) {
			foreach ($thematic_id as $id) {
				$id	= intval($id);
				$sql = "UPDATE $tbl_thematic SET active = 0 WHERE id = $id";
				Database::query($sql);
				$affected_rows += Database::affected_rows();
				if (!empty($affected_rows)) {
					// update row item property table
					api_item_property_update($_course, TOOL_COURSE_PROGRESS, $id,"delete", $user_id);
				}
			}
		} else  {
			$thematic_id	= intval($thematic_id);
			$sql = "UPDATE $tbl_thematic SET active = 0 WHERE id = $thematic_id";
			Database::query($sql);
			$affected_rows = Database::affected_rows();
			if (!empty($affected_rows)) {
				// update row item property table
				api_item_property_update($_course, TOOL_COURSE_PROGRESS, $thematic_id,"delete", $user_id);
			}
		}
		return $affected_rows;				
	}
	
	/**
	 * Get the total number of thematic advance inside current course
	 * @see SortableTable#get_total_number_of_items()
	 */
	public function get_number_of_thematic_advances() {	
		global $thematic_id;	
		$tbl_thematic_advance = Database :: get_course_table(TABLE_THEMATIC_ADVANCE);		
		$sql = "SELECT COUNT(id) AS total_number_of_items FROM $tbl_thematic_advance WHERE thematic_id = $thematic_id ";		
		$res = Database::query($sql);
		$res = Database::query($sql);
		$obj = Database::fetch_object($res);		
		return $obj->total_number_of_items;
	}
	
	
	/**
	 * Get the thematic advances to display on the current page (fill the sortable-table)
	 * @param   int     offset of first user to recover
	 * @param   int     Number of users to get
	 * @param   int     Column to sort on
	 * @param   string  Order (ASC,DESC)
	 * @see SortableTable#get_table_data($from)
	 */
	public function get_thematic_advance_data($from, $number_of_items, $column, $direction) {
		global $thematic_id;		
		$tbl_thematic_advance = Database :: get_course_table(TABLE_THEMATIC_ADVANCE);			
		$thematic_data = self::get_thematic_list($thematic_id);
	    $column = intval($column);
	    $from = intval($from);
	    $number_of_items = intval($number_of_items);
		if (!in_array($direction, array('ASC','DESC'))) {
	    	$direction = 'ASC';
	    }
		$sql = "SELECT
				id AS col0,
				start_date AS col1,
				duration AS col2,
				content AS col3							
				FROM $tbl_thematic_advance
				WHERE thematic_id = $thematic_id
				ORDER BY col$column $direction LIMIT $from,$number_of_items ";
		$res = Database::query($sql);
		$data = array ();		
		$i = 1;
		while ($thematic_advance = Database::fetch_row($res)) {		
			$thematic_advance[1] = api_get_local_time($thematic_advance[1]);
			$thematic_advance[1] = api_format_date($thematic_advance[1], DATE_TIME_FORMAT_LONG);
			if (api_is_allowed_to_edit(null, true)) {
				$actions  = '';						
				$actions .= '<a href="index.php?'.api_get_cidreq().'&action=thematic_advance_edit&thematic_id='.$thematic_id.'&thematic_advance_id='.$thematic_advance[0].'">'.Display::return_icon('edit.gif',get_lang('Edit')).'</a>&nbsp;';
				$actions .= '<a onclick="javascript:if(!confirm(\''.get_lang('AreYouSureToDelete').'\')) return false;" href="index.php?'.api_get_cidreq().'&action=thematic_advance_delete&thematic_id='.$thematic_id.'&thematic_advance_id='.$thematic_advance[0].'">'.Display::return_icon('delete.gif',get_lang('Delete')).'</a></center>';
				$data[] = array($i, $thematic_advance[1], $thematic_advance[2], $thematic_advance[3], $actions);
			}
			$i++;	 
		}
		return $data;
	}
	
	/**
	 * get thematic advance data by tematic id
	 * @param	int		Thematic id
	 * @param	string	Course code (optional)
	 * @return	array	data
	 */	 
	 public function get_thematic_advance_by_thematic_id($thematic_id, $course_code = null) {	 	
	 	
	 	// set current course
	 	if (isset($course_code)) {
	 		$course_info = api_get_course_info($course_code);
	 		$tbl_thematic_advance = Database::get_course_table(TABLE_THEMATIC_ADVANCE, $course_info['dbName']);
	 	} else {
	 		$tbl_thematic_advance = Database::get_course_table(TABLE_THEMATIC_ADVANCE);	
	 	}
	 				    
	    $thematic_id = intval($thematic_id);
	    $data = array();	    
		$sql = "SELECT * FROM $tbl_thematic_advance WHERE thematic_id = $thematic_id ";
		$res = Database::query($sql);
		if (Database::num_rows($res) > 0) {
			while ($row = Database::fetch_array($res)) {
				$data[] = $row;	
			}			
		}
		return $data;
	 }
	 
	 /**
	 * get thematic advance list
	 * @param	int		Thematic advance id (optional), get data by thematic advance list
	 * @param	string	Course code (optional)
	 * @return	array	data
	 */
	 public function get_thematic_advance_list($thematic_advance_id = null, $course_code = null) {	 	
	 	// set current course
	 	if (isset($course_code)) {
	 		$course_info = api_get_course_info($course_code);
	 		$tbl_thematic_advance = Database::get_course_table(TABLE_THEMATIC_ADVANCE, $course_info['dbName']);	 		
	 	} else {
	 		$tbl_thematic_advance = Database::get_course_table(TABLE_THEMATIC_ADVANCE);	
	 	}
	 	
	 	$data = array();
	 			
	 	$condition = '';
	 	if (isset($thematic_advance_id)) {
	 		$thematic_advance_id = intval($thematic_advance_id);
	 		$condition = " WHERE id = $thematic_advance_id ";	
	 	}

		$sql = "SELECT * FROM $tbl_thematic_advance $condition ORDER BY start_date ";
		$res = Database::query($sql);
		if (Database::num_rows($res) > 0) {			
			if (!empty($thematic_advance_id)) {		
				$data = Database::fetch_array($res);
			} else {
				// group all data group by thematic id
				$tmp = array();
				while ($row = Database::fetch_array($res)) {					
					$tmp[] = $row['thematic_id'];
					if (in_array($row['thematic_id'], $tmp)) {
						$data[$row['thematic_id']][$row['id']] = $row;
					}
				}
			}										
		}
		
		return $data;
	 }
	
	/**
	 * insert or update a thematic advance
	 * @return int last thematic advance id
	 */
	public function thematic_advance_save() {
		
		// definition database table		
		$tbl_thematic_advance = Database::get_course_table(TABLE_THEMATIC_ADVANCE);
		
		// protect data
		$id = intval($this->thematic_advance_id);
		$tematic_id = intval($this->thematic_id);
		$attendance_id = intval($this->attendance_id);
		$content = Database::escape_string($this->thematic_advance_content);
		$start_date = Database::escape_string($this->start_date);
		$duration	= intval($this->duration);

		if (empty($id)) {			
			// insert
			$sql = "INSERT INTO $tbl_thematic_advance (thematic_id, attendance_id, content, start_date, duration) VALUES ($tematic_id, $attendance_id, '$content', '".api_get_utc_datetime($start_date)."', $duration) ";
			Database::query($sql);
			$last_id = Database::insert_id();
		} else {
			// update
			$sql = "UPDATE $tbl_thematic_advance SET thematic_id = $tematic_id, attendance_id = $attendance_id, content = '$content', start_date = '".api_get_utc_datetime($start_date)."', duration = $duration WHERE id = $id ";
			Database::query($sql);
			$last_id = $id;
		}
		
		return $last_id;
			
	}
	
	/**
	 * delete  thematic advance
	 * @param	int		Thematic advance id
	 * @return	int		Affected rows
	 */
	public function thematic_advance_destroy($thematic_advance_id) {
		
		// definition database table		
		$tbl_thematic_advance = Database::get_course_table(TABLE_THEMATIC_ADVANCE);		
		// protect data
		$thematic_advance_id = intval($thematic_advance_id);				
		$sql = "DELETE FROM $tbl_thematic_advance WHERE id = $thematic_advance_id ";
		Database::query($sql);
		$affected_rows = Database::affected_rows();
		
		return $affected_rows;
	}
	
	/**
	 * get thematic plan data
	 * @param	int		Thematic id (optional), get data by thematic id
	 * @param	int		Thematic plan description type (optional), get data by description type
	 * @return 	array	Thematic plan data
	 */
	public function get_thematic_plan_data($thematic_id = null, $description_type = null) {
		
		// definition database table		
		$tbl_thematic_plan = Database::get_course_table(TABLE_THEMATIC_PLAN);
		$data = array();
		$condition = '';
		if (isset($thematic_id)) {
			$thematic_id = intval($thematic_id);
			$condition .= " AND thematic_id = $thematic_id ";
		}
		if (isset($description_type)) {
			$description_type = intval($description_type);
			$condition .= " AND description_type = $description_type ";
		}
		
		$sql = "SELECT * FROM $tbl_thematic_plan WHERE 1 $condition";
		$rs	 = Database::query($sql);
		if (Database::num_rows($rs) > 0) {			
			if (!isset($thematic_id) && !isset($description_type)) {				
				// group all data group by thematic id
				$tmp = array();
				while ($row = Database::fetch_array($rs)) {					
					$tmp[] = $row['thematic_id'];
					if (in_array($row['thematic_id'], $tmp)) {
						$data[$row['thematic_id']][$row['id']] = $row;
					}
				}				
			} else {
				while ($row = Database::fetch_array($rs)) {
					$data[] = $row;
				}	
			}
		}		
		return $data;
	}
	
	/**
	 * insert or update a thematic plan
	 * @return int affected rows
	 */
	public function thematic_plan_save() {
		
		// definition database table		
		$tbl_thematic_plan = Database::get_course_table(TABLE_THEMATIC_PLAN);
		
		// protect data
		$thematic_id = intval($this->thematic_id);
		$title 		 = Database::escape_string($this->thematic_plan_title);
		$description = Database::escape_string($this->thematic_plan_description);
		$description_type = intval($this->thematic_plan_description_type);		
		
		// check thematic plan type already exists
		$sql = "SELECT id FROM $tbl_thematic_plan WHERE thematic_id = $thematic_id AND description_type = $description_type ";
		$rs	 = Database::query($sql);
		
		$affected_rows = 0;
		if (Database::num_rows($rs) > 0) {
			// update
			$upd = "UPDATE $tbl_thematic_plan SET title = '$title', description = '$description' WHERE thematic_id = $thematic_id AND description_type = $description_type ";
			Database::query($upd);
			$affected_rows = Database::affected_rows();
		} else {
			// insert
			$ins = "INSERT INTO $tbl_thematic_plan(thematic_id, title, description, description_type) VALUES($thematic_id, '$title', '$description', $description_type) ";
			Database::query($ins);
			$affected_rows = Database::affected_rows();
		} 
				
		return $affected_rows;			
	}
	
	/**
	 * delete a thematic plan description
	 * @param	int		Thematic id
	 * @param	int		Description type
	 * @return	int		Affected rows
	 */
	public function thematic_plan_destroy($thematic_id, $description_type) {
		
		// definition database table		
		$tbl_thematic_plan = Database::get_course_table(TABLE_THEMATIC_PLAN);
		
		// protect data
		$thematic_id = intval($thematic_id);		
		$description_type = intval($description_type);
		
		$sql = "DELETE FROM $tbl_thematic_plan WHERE thematic_id = $thematic_id AND description_type = $description_type ";
		Database::query($sql);
		$affected_rows = Database::affected_rows();
		
		return $affected_rows;
	}
	
	/**
	 * Get next description type for a new thematic plan description (option 'others')
	 * @param	int		Thematic id
	 * @return 	int		New Description type 
	 */
	public function get_next_description_type($thematic_id) {
		
		// definition database table		
		$tbl_thematic_plan = Database::get_course_table(TABLE_THEMATIC_PLAN);
		
		// protect data
		$thematic_id = intval($thematic_id);		
		$description_type = intval($description_type);
		$next_description_type = 0;
		
		$sql = "SELECT MAX(description_type) as max FROM $tbl_thematic_plan WHERE thematic_id = $thematic_id AND description_type >= ".ADD_THEMATIC_PLAN." ";
		$rs = Database::query($sql);
		$row = Database::fetch_array($rs);
		$last_description_type = $row['max'];
		
		if (isset($last_description_type)) {
			$row = Database::fetch_array($rs);
			
			$next_description_type = $last_description_type + 1;
		} else {
			$next_description_type = ADD_THEMATIC_PLAN;
		}
		
		return $next_description_type;
	}
	
	
	/**
	 * update done thematic advances from thematic details interface
	 * @param 	int		Thematic id
	 * @return	int		Affected rows
	 */
	public function update_done_thematic_advances ($thematic_advance_id) {
		
		$thematic_data = $this->get_thematic_list();
		$thematic_advance_data = $this->get_thematic_advance_list();
		$tbl_thematic_advance = Database::get_course_table(TABLE_THEMATIC_ADVANCE);
		$affected_rows = 0;
		
		$a_thematic_advance_ids = array();
		if (!empty($thematic_data)) {
			foreach ($thematic_data as $thematic) {			
				$thematic_id = $thematic['id'];
				if (!empty($thematic_advance_data[$thematic['id']])) {										
					foreach ($thematic_advance_data[$thematic['id']] as $thematic_advance) {												 
						$a_thematic_advance_ids[] = $thematic_advance['id'];					
						// update done thematic for previous advances ((done_advance = 1))
						$upd = "UPDATE $tbl_thematic_advance set done_advance = 1 WHERE id = ".$thematic_advance['id']." ";
						Database::query($upd);
						$affected_rows += Database::affected_rows();			
						if ($thematic_advance['id'] == $thematic_advance_id) {
							break 2;
						}				
					}					
				}			
			}
		}
		
		// Update done thematic for others advances (done_advance = 0)		
		if (!empty($a_thematic_advance_ids) && count($a_thematic_advance_ids) > 0) {
			$upd = "UPDATE $tbl_thematic_advance set done_advance = 0 WHERE id NOT IN(".implode(',',$a_thematic_advance_ids).") ";
			Database::query($upd);
		}
		return $affected_rows;
				
	}
		
	/**
	 * Get last done thematic advance from thematic details interface
	 * @return	int		Last done thematic advance id
	 */
	public function get_last_done_thematic_advance() {
		
		$thematic_data = $this->get_thematic_list();
		$thematic_advance_data = $this->get_thematic_advance_list();		
		$a_thematic_advance_ids = array();
		$last_done_advance_id = 0;
		if (!empty($thematic_data)) {
			foreach ($thematic_data as $thematic) {			
				$thematic_id = $thematic['id'];
				if (!empty($thematic_advance_data[$thematic['id']])) {										
					foreach ($thematic_advance_data[$thematic['id']] as $thematic_advance) {						
						if ($thematic_advance['done_advance'] == 1) {
							$a_thematic_advance_ids[] = $thematic_advance['id'];
						}									
					}					
				}			
			}
		}
		
		if (!empty($a_thematic_advance_ids)) {
			$last_done_advance_id = array_pop($a_thematic_advance_ids);
			$last_done_advance_id = intval($last_done_advance_id);
		}
		
		return $last_done_advance_id;		
	}

	/**
	 * Get next thematic advance not done from thematic details interface
	 * @return	int		next thematic advance not done
	 */
	public function get_next_thematic_advance_not_done() {
		
		$thematic_data = $this->get_thematic_list();
		$thematic_advance_data = $this->get_thematic_advance_list();		
		$a_thematic_advance_ids = array();
		$next_advance_not_done = 0;
		if (!empty($thematic_data)) {
			foreach ($thematic_data as $thematic) {			
				$thematic_id = $thematic['id'];
				if (!empty($thematic_advance_data[$thematic['id']])) {										
					foreach ($thematic_advance_data[$thematic['id']] as $thematic_advance) {						
						if ($thematic_advance['done_advance'] == 0) {
							$a_thematic_advance_ids[] = $thematic_advance['id'];
						}									
					}					
				}			
			}
		}

		if (!empty($a_thematic_advance_ids)) {
			$next_advance_not_done = array_shift($a_thematic_advance_ids);
			$next_advance_not_done = intval($next_advance_not_done);
		}
				
		return $next_advance_not_done;		
	}
	
	/**
	 * Get total average of thematic advances
	 * @param	string	Course code (optional)
	 * @param	int		Session id	(optional)
	 * @return 	float	Average of thematic advances
	 */
	public function get_total_average_of_thematic_advances($course_code = null, $session_id = null) {
		
		$thematic_data = $this->get_thematic_list(null, $course_code, $session_id);
		$thematic_advance_data = $this->get_thematic_advance_list(null, $course_code);		
		$a_average_of_advances_by_thematic = array();
		$total_average = 0;
		if (!empty($thematic_data)) {
			foreach ($thematic_data as $thematic) {			
				$thematic_id = $thematic['id'];			
				$a_average_of_advances_by_thematic[$thematic_id] = $this->get_average_of_advances_by_thematic($thematic_id, $course_code);					
			}
		}

		// calculate total average
		if (!empty($a_average_of_advances_by_thematic)) {
			$count_tematics = count($thematic_data);
			$score = array_sum($a_average_of_advances_by_thematic);
			$total_average = round(($score*100)/($count_tematics*100));			
		}
		
		return $total_average;
		
	}
	
	
	/**
	 * Get average of advances by thematic 
	 * @param	int		Thematic id
	 * @param	string	Course code (optional)
	 * @return 	float	Average of thematic advances
	 */
	public function get_average_of_advances_by_thematic($thematic_id, $course_code = null) {

		$thematic_advance_data = $this->get_thematic_advance_by_thematic_id($thematic_id, $course_code);	
		$average = 0;						
		if (!empty($thematic_advance_data)) {
			// get all done advances by thematic
			$advances = array();
			$count_done_advances = 0;
			$average = 0;			
			foreach ($thematic_advance_data as $thematic_advance) {								
				if ($thematic_advance['done_advance'] == 1) {
					$count_done_advances++;
				}											
				$advances[] = $thematic_advance['done_advance'];
			}						
			// calculate average by thematic					
			$count_total_advances = count($advances);					
			$average = round(($count_done_advances*100)/$count_total_advances);											
		}
		
		return $average;
		
	}
	
	/**
	 * set attributes for fields of thematic table
	 * @param	int		Thematic id
	 * @param	string	Thematic title
	 * @param	string	Thematic content
	 * @param	int		Session id
	 * @return void
	 */
	 public function set_thematic_attributes($id = null, $title = '', $content = '', $session_id = 0) {	 	
	 	$this->thematic_id = $id;
	 	$this->thematic_title = $title;
	 	$this->thematic_content = $content;
	 	$this->session_id = $session_id;	 	 	
	 }

	/**
	 * set attributes for fields of thematic_plan table	 
	 * @param	int		Thematic id
	 * @param	string	Thematic plan title	 
	 * @param	string	Thematic plan description
	 * @param	int		Thematic plan description type
	 * @return void
	 */
	 public function set_thematic_plan_attributes($thematic_id = 0, $title = '', $description = '', $description_type = 0) {
	 	$this->thematic_id = $thematic_id;
	 	$this->thematic_plan_title = $title;
	 	$this->thematic_plan_description = $description;
	 	$this->thematic_plan_description_type = $description_type;	 	 	
	 }
	 
	 /**
	 * set attributes for fields of thematic_advance table	 
	 * @param	int		Thematic advance id
	 * @param	int		Thematic id	 
	 * @param	int		Attendance id
	 * @param	string	Content
	 * @param	string	Date and time
	 * @param	int		Duration in hours
	 * @return void
	 */
	 public function set_thematic_advance_attributes($id = null, $thematic_id = 0,  $attendance_id = 0, $content = '', $start_date = '0000-00-00 00:00:00', $duration = 0) {
	 	$this->thematic_advance_id = $id;
	 	$this->thematic_id = $thematic_id;
	 	$this->attendance_id = $attendance_id;
	 	$this->thematic_advance_content = $content;
	 	$this->start_date = $start_date;
	 	$this->duration = $duration;	 	 	
	 }
	 
	 /**
	  * set thematic id
	  * @param	int	 Thematic id
	  * @return void
	  */
	 public function set_thematic_id($thematic_id) {
	 	$this->thematic_id = $thematic_id;
	 }
	 
	 /**
	  * get thematic id
	  * @return void
	  */
	 public function get_thematic_id() {
	 	return $this->thematic_id; 
	 }
	 
	/**
	 * Get thematic plan titles by default
	 * @return array
	 */
	public function get_default_thematic_plan_title() {
		$default_thematic_plan_titles = array();
		$default_thematic_plan_titles[1]= get_lang('Objectives');
		$default_thematic_plan_titles[2]= get_lang('SkillToAcquire');
		$default_thematic_plan_titles[3]= get_lang('Infrastructure');
		$default_thematic_plan_titles[4]= get_lang('Methodology');
		$default_thematic_plan_titles[5]= get_lang('AditionalNotes');
		$default_thematic_plan_titles[6]= get_lang('Others');		
		return $default_thematic_plan_titles;
	}

	/**
	 * Get thematic plan icons by default
	 * @return array
	 */
	public function get_default_thematic_plan_icon() {
		$default_thematic_plan_icon = array();
		$default_thematic_plan_icon[1]= 'spire.gif';
		$default_thematic_plan_icon[2]= 'korganizer.gif';
		$default_thematic_plan_icon[3]= 'kcmdf_big.gif';		
		$default_thematic_plan_icon[4]= 'misc.gif';		
		$default_thematic_plan_icon[5]= 'ktip.gif';
		$default_thematic_plan_icon[6]= 'new_test.gif';
		return $default_thematic_plan_icon;
	}

	/**
	 * Get questions by default for help
	 * @return array
	 */
	public function get_default_question() {
		$question = array();
		$question[1]= get_lang('ObjectivesQuestions');
		$question[2]= get_lang('SkillToAcquireQuestions');
		$question[3]= get_lang('InfrastructureQuestions');
		$question[4]= get_lang('MethodologyQuestions');
		$question[5]= get_lang('AditionalNotesQuestions');		
		return $question;
	}
	
	/**
	 * buid a string datetime from array
	 * @param	array	array containing data e.g: $array('Y'=>'2010',  'F' => '02', 'd' => '10', 'H' => '12', 'i' => '30')
	 * @return	string	date and time e.g: '2010-02-10 12:30:00'
	 */
	public function build_datetime_from_array($array) {
		$year	 = '0000';
		$month = $day = $hours = $minutes = $seconds = '00';
		if (isset($array['Y']) && isset($array['F']) && isset($array['d']) && isset($array['H']) && isset($array['i'])) {
			$year = $array['Y'];
			$month = $array['F'];
			if (intval($month) < 10 ) $month = '0'.$month;
			$day = $array['d'];
			if (intval($day) < 10 ) $day = '0'.$day;
			$hours = $array['H'];
			if (intval($hours) < 10 ) $hours = '0'.$hours;
			$minutes = $array['i'];
			if (intval($minutes) < 10 ) $minutes = '0'.$minutes;
		}
		if (checkdate($month,$day,$year)) {
			$datetime = $year.'-'.$month.'-'.$day.' '.$hours.':'.$minutes.':'.$seconds;
		}
		return $datetime;
	}
	
}
?>
