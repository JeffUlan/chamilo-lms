<?php
/* For licensing terms, see /license.txt */
/**
 * Class FlatViewDataGenerator
 * Class to select, sort and transform object data into array data,
 * used for the teacher's flat view
 * @author Bert Steppé
 *
 * @package chamilo.gradebook
 */
class FlatViewDataGenerator
{
	// Sorting types constants
	const FVDG_SORT_LASTNAME = 1;
	const FVDG_SORT_FIRSTNAME = 2;
	const FVDG_SORT_ASC = 4;
	const FVDG_SORT_DESC = 8;

	private $users;
	private $evals;
	private $links;
	private $evals_links;	
    public $params;
	public  $category = array();
    private $mainCourseCategory;

	/**
	 * Constructor
	 */
    public function FlatViewDataGenerator(
        $users = array(),
        $evals = array(),
        $links = array(),
        $params = array(),
        $mainCourseCategory = null
    ) {
		$this->users = (isset($users) ? $users : array());
		$this->evals = (isset($evals) ? $evals : array());
		$this->links = (isset($links) ? $links : array());
		$this->evals_links = array_merge($this->evals, $this->links);
        $this->params = $params;
        $this->mainCourseCategory = $mainCourseCategory;
    }

	/**
     * @return Category
     */
    public function getMainCourseCategory()
    {
        return $this->mainCourseCategory;
    }
    /**
	 * Get total number of users (rows)
	 */
    public function get_total_users_count()
    {
		return count($this->users);
	}

	/**
	 * Get total number of evaluations/links (columns) (the 2 users columns not included)
	 */
    public function get_total_items_count()
    {
		return count($this->evals_links);
	}

	/**
	 * Get array containing column header names (incl user columns)
     * @param int $items_start Start item offset
     * @param int $items_count Number of items to get
     * @param bool $show_detail whether to show the details or not
     * @return array List of headers 
	 */
    public function get_header_names($items_start = 0, $items_count = null, $show_detail = false)
    {
		$headers = array();        
                
        if (isset($this->params['show_official_code']) && $this->params['show_official_code']) {
            $headers[] = get_lang('OfficialCode');
        }        
        if (isset($this->params['join_firstname_lastname']) && $this->params['join_firstname_lastname']) {
            if (api_is_western_name_order()) {
                $headers[] = get_lang('FirstnameAndLastname');	
            } else {
                $headers[] = get_lang('LastnameAndFirstname');	
            }
        } else {
            if (api_is_western_name_order()) {
                $headers[] = get_lang('FirstName');
                $headers[] = get_lang('LastName');                
            } else {
                $headers[] = get_lang('LastName');
                $headers[] = get_lang('FirstName');                
            }
        }
		if (!isset($items_count)) {
			$items_count = count($this->evals_links) - $items_start;
		}
        
        $parent_id = $this->category->get_parent_id();
        
        if ($parent_id == 0 or
            $this->params['only_subcat'] == $this->category->get_id()
        ) {
            $main_weight  = $this->category->get_weight();
            $grade_model_id = $this->category->get_grade_model_id();
        } else {
            $main_cat  = Category::load($parent_id, null, null);
            $main_weight = $main_cat[0]->get_weight();
            $grade_model_id = $main_cat[0]->get_grade_model_id();
        }  
        
        $use_grade_model = true;
        if (empty($grade_model_id) || $grade_model_id == -1) {
            $use_grade_model = false;    
        }
		        
		//@todo move these in a function
		$sum_categories_weight_array = array();
        $mainCategoryId = null;
        $mainCourseCategory = $this->getMainCourseCategory();

        if (!empty($mainCourseCategory)) {
            $mainCategoryId = $mainCourseCategory->get_id();
        }
		if (isset($this->category) && !empty($this->category)) {
			$categories = Category::load(null, null, null, $this->category->get_id());            
			if (!empty($categories)) {
			    foreach ($categories as $category) {			         
				    $sum_categories_weight_array[$category->get_id()] = $category->get_weight();
                }
			} else {
			    $sum_categories_weight_array[$this->category->get_id()] = $this->category->get_weight();                
			}
		}
        
        //No category was added
        
        $course_code 	= api_get_course_id();            
        $session_id		= api_get_session_id();

        $allcat = $this->category->get_subcategories(
            null,
            $course_code,
            $session_id,
            'ORDER BY id'
        );
        $evaluationsAdded = array();
                    
        if ($parent_id == 0 && !empty($allcat)) {             
            
            //Means there are any subcategory
            foreach ($allcat as $sub_cat) {
                $sub_cat_weight = round(100*$sub_cat->get_weight()/$main_weight,1);
                $add_weight = " $sub_cat_weight %";
                /*if (isset($this->params['export_pdf']) && $this->params['export_pdf']) {
                   $add_weight = null;
                }*/
                $headers[] = Display::url(
                    $sub_cat->get_name(),
                    api_get_self().'?selectcat='.$sub_cat->get_id()
                ).$add_weight;
            }
        } else {
            if (!isset($this->params['only_total_category']) ||
                (isset($this->params['only_total_category']) &&
                $this->params['only_total_category'] == false)
            ) {
                for ($count=0; ($count < $items_count ) && ($items_start + $count < count($this->evals_links)); $count++) {
                    $item = $this->evals_links[$count + $items_start];                    
                    $weight = round(100*$item->get_weight()/$main_weight,1);
                    $headers[] = $item->get_name().' '.$weight.' % ';                
                    $evaluationsAdded[] = $item->get_id();
                }
            }            
        }
        if (!empty($mainCategoryId)) {
            for ($count = 0; ($count < $items_count) && ($items_start + $count < count($this->evals_links)); $count++) {
                /** @var AbstractLink $item */
                $item = $this->evals_links[$count + $items_start];
                if ($mainCategoryId == $item->get_category_id() && !in_array($item->get_id(), $evaluationsAdded)) {
                    $weight = round(100 * $item->get_weight() / $main_weight, 1);
                    $headers[] = $item->get_name() . ' ' . $weight . ' % ';
                }
            }
        }
        $headers[] = api_strtoupper(get_lang('GradebookQualificationTotal'));
		return $headers;
	}
	
    /**
     * @param int $id
     *
     * @return int
     */
    public function get_max_result_by_link($id)
    {
		$max = 0;
		foreach ($this->users as $user) {
			$item  = $this->evals_links [$id];					
			$score = $item->calc_score($user[0]);			
			if ($score[0] > $max) {
				$max = $score[0];
			}			
		}		
		return $max ;
	}

	/**
	 * Get array containing evaluation items
	 */
    public function get_evaluation_items($items_start = 0, $items_count = null)
    {
		$headers = array();
		if (!isset($items_count)) {
			$items_count = count($this->evals_links) - $items_start;
		}
		for ($count=0; ($count < $items_count ) && ($items_start + $count < count($this->evals_links)); $count++) {
			$item = $this->evals_links [$count + $items_start];
			$headers[] = $item->get_name();
		}
		return $headers;
	}		

	/**
	 * Get actual array data
	 * @return array 2-dimensional array - each array contains the elements:
	 * 0: user id
	 * 1: user lastname
	 * 2: user firstname
	 * 3+: evaluation/link scores
	 */
    public function get_data(
        $users_sorting = 0,
        $users_start = 0,
        $users_count = null,
        $items_start = 0,
        $items_count = null,
        $ignore_score_color = false,
        $show_all = false
    ) {
		
		// do some checks on users/items counts, redefine if invalid values
		if (!isset($users_count)) {
			$users_count = count ($this->users) - $users_start;
		}
		if ($users_count < 0) {
			$users_count = 0;
		}        
		if (!isset($items_count)) {
			$items_count = count ($this->evals) + count ($this->links) - $items_start;
		}
		if ($items_count < 0) {
			$items_count = 0;
		}
        
		// copy users to a new array that we will sort
		// TODO - needed ?
        $userTable = array();
		foreach ($this->users as $user) {
            $userTable[] = $user;
		}
        
		// sort users array
		if ($users_sorting & self :: FVDG_SORT_LASTNAME) {
            usort($userTable, array ('FlatViewDataGenerator','sort_by_last_name'));
		} elseif ($users_sorting & self :: FVDG_SORT_FIRSTNAME) {
            usort($userTable, array ('FlatViewDataGenerator','sort_by_first_name'));
		}

		if ($users_sorting & self :: FVDG_SORT_DESC) {
            $userTable = array_reverse($userTable);
		}

		// select the requested users
        $selected_users = array_slice($userTable, $users_start, $users_count);
        
		// generate actual data array
		$scoredisplay = ScoreDisplay :: instance();

		$data = array ();
		$displaytype = SCORE_DIV;
		if ($ignore_score_color) {
			$displaytype |= SCORE_IGNORE_SPLIT;
		}
		//@todo move these in a function
		$sum_categories_weight_array = array();     
        $mainCategoryId = null;
        $mainCourseCategory = $this->getMainCourseCategory();
        if (!empty($mainCourseCategory)) {
            $mainCategoryId = $mainCourseCategory->get_id();
        }
        
        if (isset($this->category) && !empty($this->category)) {            
            $categories = Category::load(null, null, null, $this->category->get_id());
            if (!empty($categories)) {
                foreach($categories as $category) {                  
                    $sum_categories_weight_array[$category->get_id()] = $category->get_weight();
                }
            } else {
                $sum_categories_weight_array[$this->category->get_id()] = $this->category->get_weight();
            }
        }
        
        $parent_id = $this->category->get_parent_id();
        
        if ($parent_id == 0 or $this->params['only_subcat'] == $this->category->get_id()) {
            $main_weight  = $this->category->get_weight();
            $grade_model_id = $this->category->get_grade_model_id();
        } else {
            $main_cat  = Category::load($parent_id, null, null);
            $main_weight = $main_cat[0]->get_weight();
            $grade_model_id = $main_cat[0]->get_grade_model_id();
        }        
                
        $use_grade_model = true;
        if (empty($grade_model_id) || $grade_model_id == -1) {
            $use_grade_model = false;    
        }        
        
        $export_to_pdf = false;
        if (isset($this->params['export_pdf']) && $this->params['export_pdf']) {     
            $export_to_pdf = true;
        }
                
		foreach ($selected_users as $user) {             
			$row = array();     
            if ($export_to_pdf) {
                $row['user_id'] = $user_id = $user[0];	//user id
            } else {
                $row[] = $user_id = $user[0];	//user id
            }
            
            if (isset($this->params['show_official_code']) && $this->params['show_official_code']) {       
                if ($export_to_pdf) {
                    $row['official_code'] = $user[4];	//official code
                } else {
                    $row[] = $user[4];	//official code
                }
            }
            
            if (isset($this->params['join_firstname_lastname']) && $this->params['join_firstname_lastname']) {       
                if ($export_to_pdf) {
                    $row['name'] = api_get_person_name($user[3], $user[2]);	//last name			
                } else {
                    $row[] = api_get_person_name($user[3], $user[2]);	//last name			
                }
            } else {
                if ($export_to_pdf) {
                    if (api_is_western_name_order()) {
                        $row['firstname']   = $user[3];
                        $row['lastname']    = $user[2];                        
                    } else {
                        $row['lastname']    = $user[2];
                        $row['firstname']   = $user[3];
                    }
                } else {
                    if (api_is_western_name_order()) {
                        $row[]   = $user[3];	//first name    
                        $row[]   = $user[2];	//last name                        
                    } else {
                        $row[]   = $user[2];	//last name
                        $row[]   = $user[3];	//first name                            
                    }
                }
            }
          
			$item_value = 0;
            $item_value_total = 0;
			$item_total = 0;
            
            $convert_using_the_global_weight = true;
            
            $course_code 	= api_get_course_id();            
            $session_id		= api_get_session_id();
            $allcat         = $this->category->get_subcategories(null, $course_code, $session_id, 'ORDER BY id');

            $evaluationsAdded = array();
            if ($parent_id == 0 && !empty($allcat)) {
                                                
                foreach ($allcat as $sub_cat) {
                    $score 			= $sub_cat->calc_score($user_id);
                    $real_score     = $score;
                    $divide			= ( ($score[1])==0 ) ? 1 : $score[1];      
                    
                    $sub_cat_percentage = $sum_categories_weight_array[$sub_cat->get_id()];
                    $item_value     = $score[0]/$divide*$main_weight;

                    //Fixing total when using one or multiple gradebooks                    
                    $percentage     = $sub_cat->get_weight()/($sub_cat_percentage) * $sub_cat_percentage/$this->category->get_weight();
                    $item_value     = $percentage*$item_value;                    
                    $item_total		+= $sub_cat->get_weight();
                    
/*
                    if ($convert_using_the_global_weight) {                                             
                        $score[0] = $score[0]/$main_weight*$sub_cat->get_weight();                        
                        $score[1] = $main_weight ;
                    }                    
                                        
*/
                    if (api_get_setting('gradebook_show_percentage_in_reports') == 'false') {
                    //if (true) {
                        $real_score = $scoredisplay->display_score($real_score, SCORE_SIMPLE);    
                        $temp_score = $scoredisplay->display_score($score, SCORE_DIV_SIMPLE_WITH_CUSTOM);                        
                        $temp_score = Display::tip($real_score, $temp_score);
                    } else {                        
                        $real_score = $scoredisplay->display_score($real_score, SCORE_DIV_PERCENT, SCORE_ONLY_SCORE);                    
                        $temp_score = $scoredisplay->display_score($score, SCORE_DIV_SIMPLE_WITH_CUSTOM);
                        $temp_score = Display::tip($temp_score, $real_score);
                    }                    
                    
                    if (!isset($this->params['only_total_category']) ||
                        (isset($this->params['only_total_category']) &&
                        $this->params['only_total_category'] == false)
                    ) {
                        if (!$show_all) {
                           $row[] = $temp_score.' ';   
                        } else {                 
                           $row[] = $temp_score;
                        }                    
                    }
                    $item_value_total +=$item_value;    
                }
                if ($convert_using_the_global_weight) {
                    //$item_total = $main_weight;
                }
            } else  {
                $result = $this->parseEvaluations(
                    $user_id,
                    $sum_categories_weight_array,
                    $items_count,
                    $items_start,
                    $show_all,
                    $row
                );

                $item_total += $result['item_total'];
                $item_value_total += $result['item_value_total'];
                $evaluationsAdded = $result['evaluations_added'];
                $item_total = $main_weight;
            }

            // All evaluations
            $result = $this->parseEvaluations(
                $user_id,
                $sum_categories_weight_array,
                $items_count,
                $items_start,
                $show_all,
                $row,
                $mainCategoryId,
                $evaluationsAdded
            );

            $item_total += $result['item_total'];
            $item_value_total += $result['item_value_total'];

            $total_score = array($item_value_total, $item_total);

            if (!$show_all) {
                if ($export_to_pdf) {
                    $row['total'] = $scoredisplay->display_score($total_score);
                } else {
                    $row[] = $scoredisplay->display_score($total_score);
                }
            } else {
                if ($export_to_pdf) {
                    $row['total'] = $scoredisplay->display_score($total_score, SCORE_DIV_SIMPLE_WITH_CUSTOM_LETTERS);
                } else {
                    $row[] = $scoredisplay->display_score($total_score, SCORE_DIV_SIMPLE_WITH_CUSTOM_LETTERS);
                }
            }
            unset($score);
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Parse evaluations
     *
     * @param int $user_id
     * @param array $sum_categories_weight_array
     * @param int $items_count
     * @param int $items_start
     * @param int $show_all
     * @param int $parentCategoryIdFilter filter by category id if set
     * @return array
     */
    public function parseEvaluations(
        $user_id,
        $sum_categories_weight_array,
        $items_count,
        $items_start,
        $show_all,
        & $row,
        $parentCategoryIdFilter = null,
        $evaluationsAlreadyAdded = array()
    ) {
        // Generate actual data array
        $scoredisplay = ScoreDisplay :: instance();
        $item_total = 0;
        $item_value_total = 0;

        $evaluationsAdded = array();
                for ($count=0; ($count < $items_count ) && ($items_start + $count < count($this->evals_links)); $count++) {
                    $item  			= $this->evals_links[$count + $items_start];                    
            if (!empty($evaluationsAlreadyAdded)) {
                if (in_array($item->get_id(), $evaluationsAlreadyAdded)) {
                    continue;
                }
            }

            if (!empty($parentCategoryIdFilter)) {
                if ($item->get_category_id() != $parentCategoryIdFilter) {
                    continue;
                }
            }

            $evaluationsAdded[] = $item->get_id();
                    $score 			= $item->calc_score($user_id);
                    $real_score     = $score;
                    $divide			= ( ($score[1])==0 ) ? 1 : $score[1];
                    
                    //sub cat weight
                    $sub_cat_percentage = $sum_categories_weight_array[$item->get_category_id()];

                    $item_value     = $score[0]/$divide;
                    
                    //Fixing total when using one or multiple gradebooks 
            if (empty($parentCategoryIdFilter)) {
                    if ($this->category->get_parent_id() == 0 ) {                        
                        $item_value     = $score[0]/$divide*$item->get_weight();
                    } else {
                        $item_value     = $item_value*$item->get_weight();                         
                    }
            } else {
                $item_value = $score[0] / $divide * $item->get_weight();
            }
                    
                    $item_total     += $item->get_weight();                    
                    $complete_score = $scoredisplay->display_score($score, SCORE_DIV_PERCENT, SCORE_ONLY_SCORE);                    
                    
                    //if (true) {
                    if (api_get_setting('gradebook_show_percentage_in_reports') == 'false') {
                        $real_score = $scoredisplay->display_score($real_score, SCORE_SIMPLE);    
                        $temp_score = $scoredisplay->display_score(array($item_value, null), SCORE_DIV_SIMPLE_WITH_CUSTOM);                        
                        $temp_score = Display::tip($real_score, $temp_score);
                    } else {                                             
                        $temp_score     = $scoredisplay->display_score($real_score, SCORE_DIV_PERCENT_WITH_CUSTOM);
                        $temp_score = Display::tip($temp_score, $complete_score);
                    }
                    
            if (!isset($this->params['only_total_category']) ||
                (isset($this->params['only_total_category']) && $this->params['only_total_category'] == false)
            ) {
                        if (!$show_all) {                            
                    if (in_array($item->get_type(), array(
                            LINK_EXERCISE,
                            LINK_DROPBOX,
                            LINK_STUDENTPUBLICATION,
                            LINK_LEARNPATH,
                            LINK_FORUM_THREAD,
                            LINK_ATTENDANCE,
                            LINK_SURVEY,
                            LINK_HOTPOTATOES)
                    )
                    ) {
                                if (!empty($score[0])) {
                                   $row[] = $temp_score.' ';                                        
                                } else {
                                   $row[] = '';
                                }
                            } else {                                
                                $row[] = $temp_score.' ';
                            }					
                        } else {                         
                           $row[] = $temp_score;                           
                        }          
                    }                    
                    $item_value_total +=$item_value;
                }
            
        return array(
            'item_total' => $item_total,
            'item_value_total' => $item_value_total,
            'evaluations_added' => $evaluationsAdded
        );
	}

	/**
	 * Get actual array data evaluation/link scores
	 */
    public function get_evaluation_sumary_results ($session_id = null)
    {

		$usertable = array ();
        foreach ($this->users as $user) {
            $usertable[] = $user;
        }
		$selected_users = $usertable;

		// generate actual data array for all selected users
		$data = array();

		foreach ($selected_users as $user) {
			$row = array ();
			for ($count=0;$count < count($this->evals_links); $count++) {
				$item = $this->evals_links [$count];
				$score = $item->calc_score($user[0]);				
				$porcent_score = isset($score[1]) &&  $score[1] > 0 ? ($score[0]*100)/$score[1] :0;
				$row[$item->get_name()] = $porcent_score;
			}
			$data[$user[0]] = $row;
		}

		// get evaluations for every user by item
		$data_by_item = array();
		foreach ($data as $uid => $items) {
			$tmp = array();
			foreach ($items as $item => $value) {
				$tmp[] = $item;
				if (in_array($item,$tmp)) {
					$data_by_item[$item][$uid] = $value;
				}
			}
		}

		// get evaluation sumary results (maximum, minimum and average of evaluations for all students)
		$result = array();
		$maximum = $minimum = $average = 0;
		foreach ($data_by_item as $k => $v) {
			$average = round(array_sum($v)/count($v));
			arsort($v);
			$maximum = array_shift($v);
			$minimum = array_pop($v);
			$sumary_item = array('max'=>$maximum, 'min'=>$minimum, 'avg'=>$average);
			$result[$k] = $sumary_item;
		}

		return $result;
	}

    /**
     * @return array
     */
    public function get_data_to_graph()
    {
		// do some checks on users/items counts, redefine if invalid values
		$usertable = array ();
		foreach ($this->users as $user) {
			$usertable[] = $user;
		}
		// sort users array
		usort($usertable, array ('FlatViewDataGenerator','sort_by_first_name'));

		$data = array ();
		
		$selected_users = $usertable;
		foreach ($selected_users as $user) {
			$row = array ();
			$row[] = $user[0];	// user id
			$item_value = 0;
			$item_total = 0;

			for ($count=0;$count < count($this->evals_links); $count++) {
				$item = $this->evals_links[$count];
				$score = $item->calc_score($user[0]);
			
				$divide =( ($score[1])==0 ) ? 1 : $score[1];
                $item_value += $score[0]/$divide*$item->get_weight();
				$item_total += $item->get_weight();
				
				
				$score_denom = ($score[1]==0) ? 1 : $score[1];
				$score_final = ($score[0] / $score_denom) * 100;
				$row[] = $score_final;
			}
			$total_score = array($item_value, $item_total);
			$score_final = ($item_value / $item_total) * 100;
			
			$row[] = $score_final;
			$data[] = $row;
		}
		return $data;
	}

    /**
     * This is a function to show the generated data
     * @param bool $displayWarning
     * @return array
     */
    public function get_data_to_graph2($displayWarning = true)
    {
		// do some checks on users/items counts, redefine if invalid values
		$usertable = array ();
		foreach ($this->users as $user) {
			$usertable[] = $user;
		}
		// sort users array
		usort($usertable, array ('FlatViewDataGenerator','sort_by_first_name'));

		// generate actual data array
		$scoredisplay = ScoreDisplay :: instance();
		$data= array ();
		$displaytype = SCORE_DIV;
		$selected_users = $usertable;
		foreach ($selected_users as $user) {
			$row = array ();
			$row[] = $user[0];	// user id
			$item_value=0;
			$item_total=0;
            $final_score = 0;
            $item_value_total = 0;
            $convert_using_the_global_weight = true;

            $course_code     = api_get_course_id();
            $session_id        = api_get_session_id();
            $allcat         = $this->category->get_subcategories(null, $course_code, $session_id, 'ORDER BY id');

            if ($parent_id == 0 && !empty($allcat)) {

                foreach ($allcat as $sub_cat) {
                    $score             = $sub_cat->calc_score($user[0]);
                    $real_score     = $score;
                    $main_weight  = $this->category->get_weight();
                    $divide            = ( ($score[1])==0 ) ? 1 : $score[1];

                    $sub_cat_percentage = $sum_categories_weight_array[$sub_cat->get_id()];
                    $item_value     = $score[0]/$divide*$main_weight;
                    $item_total        += $sub_cat->get_weight();

                    $row[] = array ($item_value, trim($scoredisplay->display_score($real_score, SCORE_CUSTOM,null, true)));
                    $item_value_total += $item_value;
                    $final_score += $score[0];
                    //$final_score = ($final_score / $item_total) * 100;

                }
                $total_score = array($final_score, $item_total);
                $row[] = array ($final_score, trim($scoredisplay->display_score($total_score, SCORE_CUSTOM, null, true)));
            } else {
				for ($count=0;$count < count($this->evals_links); $count++) {
					$item = $this->evals_links [$count];
					$score = $item->calc_score($user[0]);
					$divide=( ($score[1])==0 ) ? 1 : $score[1];
					$item_value+= $score[0]/$divide*$item->get_weight();
					$item_total+=$item->get_weight();
					$score_denom=($score[1]==0) ? 1 : $score[1];
					$score_final = ($score[0] / $score_denom) * 100;
					$row[] = array ($score_final, trim($scoredisplay->display_score($score, SCORE_CUSTOM,null, true)));

				}
				$total_score=array($item_value,$item_total);
				$score_final = ($item_value / $item_total) * 100;
	            if ($displayWarning) {
	                Display::display_warning_message( Display::display_warning_message($total_score[1]));
	            }
				$row[] =array ($score_final, trim($scoredisplay->display_score($total_score, SCORE_CUSTOM, null, true)));
            }

			$data[] = $row;
		}
		return $data;
	}
	// Sort functions - used internally

    public function sort_by_last_name($item1, $item2)
    {
		return api_strcmp($item1[2], $item2[2]);
	}

    public function sort_by_first_name($item1, $item2)
    {
		return api_strcmp($item1[3], $item2[3]);
	}
}
