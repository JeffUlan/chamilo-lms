<?php
/* For licensing terms, see /license.txt */

/**
* View (MVC patter) for thematic control 
* @author Christian Fasanando <christian1827@gmail.com>
* @author Julio Montoya <gugli100@gmail.com> Bug fixing
* @package chamilo.course_progress
*/

// protect a course script
api_protect_course_script(true);

if (api_is_allowed_to_edit(null, true)) {
	
	echo '<div class="actions" style="margin-bottom:30px">';	
	switch ($action) {		
		case 'thematic_add' :	
				echo '<a href="index.php?'.api_get_cidreq().'">'.Display::return_icon('back.png',get_lang('BackTo').' '.get_lang('ThematicDetails'),'','32').'</a>';
				//echo '<a href="index.php?'.api_get_cidreq().'&action=thematic_details">'.Display::return_icon('view_table.gif',get_lang('ThematicDetails')).' '.get_lang('ThematicDetails').'</a>';//TODO:delete, no need
				//echo '<a href="index.php?'.api_get_cidreq().'&action=thematic_list">'.Display::return_icon('view_list.gif',get_lang('ThematicList')).' '.get_lang('ThematicList').'</a>';//TODO:delete, no need
				break;		
		case 'thematic_list' :	
				echo '<a href="index.php?'.api_get_cidreq().'&action=thematic_add">'.Display::return_icon('new_course_progress.png',get_lang('NewThematicSection'),'','32').'</a>';
				echo '<a href="index.php?'.api_get_cidreq().'&action=thematic_details">'.Display::return_icon('view_detailed.png',get_lang('ThematicDetails'),'','32').'</a>';	
				//echo '<strong>'.Display::return_icon('view_list.gif',get_lang('ThematicList')).' '.get_lang('ThematicList').'</strong>&nbsp;&nbsp;';//TODO:delete, no need
				break;
		case 'thematic_details' :		
				echo '<a href="index.php?'.api_get_cidreq().'&action=thematic_add">'.Display::return_icon('new_course_progress.png',get_lang('NewThematicSection'),'','32').'</a>';
				//echo '<strong>'.Display::return_icon('view_table.gif',get_lang('ThematicDetails')).' '.get_lang('ThematicDetails').'</strong>&nbsp;&nbsp;';////TODO:delete, no need
				echo '<a href="index.php?'.api_get_cidreq().'&action=thematic_list">'.Display::return_icon('view_text.png',get_lang('ThematicList'),'','32').'</a>';
				break;
		default :
				echo '<a href="index.php?'.api_get_cidreq().'&action=thematic_add">'.Display::return_icon('new_course_progress.png',get_lang('NewThematicSection'),'','32').'</a>';
				echo '<a href="index.php?'.api_get_cidreq().'&action=thematic_details">'.Display::return_icon('view_detailed.png',get_lang('ThematicDetails'),'','32').'</a>';	
				echo '<a href="index.php?'.api_get_cidreq().'&action=thematic_list">'.Display::return_icon('view_text.png',get_lang('ThematicList'),'','32').'</a>'; 		
	}			
	echo '</div>';
}

if ($action == 'thematic_list') {
	
	$table = new SortableTable('thematic_list', array('Thematic', 'get_number_of_thematics'), array('Thematic', 'get_thematic_data'));
	
	$parameters['action'] = $action;
	$table->set_additional_parameters($parameters);
	$table->set_header(0, '', false, array('style'=>'width:20px;'));
	$table->set_header(1, get_lang('Title'), false );	
	if (api_is_allowed_to_edit(null, true)) {
		$table->set_header(2, get_lang('Actions'), false,array('style'=>'text-align:center;width:40%;'));
		$table->set_form_actions(array ('thematic_delete_select' => get_lang('DeleteAllThematics')));	
	}
	
	//echo '<div><strong>'.get_lang('ThematicList').'</strong></div><br />';	
	$table->display();
	
} else if ($action == 'thematic_details') {
	
	if ($last_id) {
		$link_to_thematic_plan = '<a href="index.php?'.api_get_cidreq().'&action=thematic_plan_list&thematic_id='.$last_id.'">'.Display::return_icon('lesson_plan.png', get_lang('ThematicPlan'), array('style'=>'vertical-align:middle'),22).'</a>';
		$link_to_thematic_advance = '<a href="index.php?'.api_get_cidreq().'&action=thematic_advance_list&thematic_id='.$last_id.'">'.Display::return_icon('porcent.png', get_lang('ThematicAdvance'), array('style'=>'vertical-align:middle'),22).'</a>';
		Display::display_confirmation_message(get_lang('ThematicSectionHasBeenCreatedSuccessfull').'<br />'.sprintf(get_lang('NowYouShouldAddThematicPlanXAndThematicAdvanceX'),$link_to_thematic_plan, $link_to_thematic_advance), false);
	}

	// display title
	if (!empty($thematic_id)) {
		//echo '<div><strong>'.Security::remove_XSS($thematic_data[$thematic_id]['title'], STUDENT).': '.get_lang('Details').'</strong></div><br />';							
	} else {
		//echo '<div><strong>'.get_lang('ThematicDetails').'</strong></div><br />';	
		// display information
		$message = '<strong>'.get_lang('Information').'</strong><br />';
		$message .= get_lang('ThematicDetailsDescription');	
		Display::display_normal_message($message, false);
		echo '<br />';			
	}
	
	// display thematic data
	if (!empty($thematic_data)) {
		
		// display progress
		echo '<div style="text-align:right;"><h2>'.get_lang('Progress').': <span id="div_result">'.$total_average_of_advances.'</span> %</h2></div>';
		
		echo '<table width="100%" class="data_table">';	
		echo '<tr><th width="33%">'.get_lang('Thematic').'</th><th>'.get_lang('ThematicPlan').'</th><th width="33%">'.get_lang('ThematicAdvance').'</th></tr>';
	   
		foreach ($thematic_data as $thematic) {
		    $session_star = '';
		    if (api_get_session_id() == $thematic['session_id']) {
                $session_star = api_get_session_image(api_get_session_id(), $user_info['status']);
            }  else { 
                continue;
            }            
			echo '<tr>';
			
			// display thematic title		
			echo '<td><h2>'.Security::remove_XSS($thematic['title'], STUDENT).$session_star.'</h2><div>'.Security::remove_XSS($thematic['content'], STUDENT).'</div></td>';
			
			// display thematic plan data
			echo '<td>';					
            
			if (api_is_allowed_to_edit(null, true) &&  api_get_session_id() == $thematic['session_id']) {
				echo '<div style="text-align:right"><a href="index.php?'.api_get_cidreq().'&origin=thematic_details&action=thematic_plan_list&thematic_id='.$thematic['id'].'">'.Display::return_icon('edit.png',get_lang('EditThematicPlan'),array('style'=>'vertical-align:middle'),22).'</a></div><br />';
			}  
                    
            $new_thematic_plan_data = array();
            if (!empty($thematic_plan_data[$thematic['id']]))
            foreach($thematic_plan_data[$thematic['id']] as $thematic_item) {    
                $thematic_simple_list[] = $thematic_item['description_type'];
                $new_thematic_plan_data[$thematic_item['description_type']] = $thematic_item;       
            }
            
            $new_id = ADD_THEMATIC_PLAN;
            if (!empty($thematic_simple_list))
            foreach($thematic_simple_list as $item) {  
                if ($item >= ADD_THEMATIC_PLAN) {        
                    $new_id = $item + 1;
                    $default_thematic_plan_title[$item] = $new_thematic_plan_data[$item]['title'];               
                }
            }
            $no_data = true; 
			if (!empty($default_thematic_plan_title)) {
				foreach ($default_thematic_plan_title as $id=>$title) { 
                    //avoid others  
                    if ($title == 'Others' && empty($thematic_plan_data[$thematic['id']][$id]['description'])) { continue; }     
                    if (!empty($thematic_plan_data[$thematic['id']][$id]['title']) && !empty($thematic_plan_data[$thematic['id']][$id]['description'])) {                       
					   echo '<h3>'.Security::remove_XSS($thematic_plan_data[$thematic['id']][$id]['title'], STUDENT).'</h3><div>';
					   echo Security::remove_XSS($thematic_plan_data[$thematic['id']][$id]['description'], STUDENT).'</div>';
					   $no_data  = false;					   
                    } else {
                    	//echo '<h3>'.$title.'</strong></h3><br />';
                    }                             
				}
			}
			
			if ($no_data) {
                echo '<div><em>'.get_lang('StillDoNotHaveAThematicPlan').'</em></div>';
			}		
			echo '</td>';
			
			// display thematic advance data
			echo '<td>';					
			if (api_is_allowed_to_edit(null, true) &&  api_get_session_id() == $thematic['session_id']) {
				echo '<div style="text-align:right"><a href="index.php?'.api_get_cidreq().'&origin=thematic_details&action=thematic_advance_list&thematic_id='.$thematic['id'].'">'.Display::return_icon('edit.png',get_lang('EditThematicAdvance'),array('style'=>'vertical-align:middle'),22).'</a></div><br />';
			}					
			
			//if (api_is_allowed_to_edit(null, true) &&  api_get_session_id() == $thematic['session_id']) {
			if (!empty($thematic_advance_data[$thematic['id']])) {
			    echo '<table width="100%">';                
				foreach ($thematic_advance_data[$thematic['id']] as $thematic_advance) {
					$thematic_advance['start_date'] = api_get_local_time($thematic_advance['start_date']);
					$thematic_advance['start_date'] = api_format_date($thematic_advance['start_date'], DATE_TIME_FORMAT_LONG);
					echo '<tr>';
					echo '<td width="90%">';
						echo '<div><strong>'.$thematic_advance['start_date'].'</strong></div>';
						echo '<div>'.Security::remove_XSS($thematic_advance['content'], STUDENT).'</div>';
						echo '<div>'.get_lang('DurationInHours').' : '.$thematic_advance['duration'].'</div>';
					echo '</td>';
					
					if (api_is_allowed_to_edit(null, true) && api_get_session_id() == $thematic['session_id']) {
    					if (empty($thematic_id)) {
    						$checked = '';
    						if ($last_done_thematic_advance == $thematic_advance['id']) {
    							$checked = 'checked';
    						}
    						$style = '';
    						if ($thematic_advance['done_advance'] == 1) {
    							$style = ' style="background-color:#E5EDF9" ';
    						} else {
    							$style = ' style="background-color:#fff" ';
    						}														
    						echo '<td id="td_done_thematic_'.$thematic_advance['id'].'" '.$style.'><center><input type="radio" id="done_thematic_'.$thematic_advance['id'].'" name="done_thematic" value="'.$thematic_advance['id'].'" '.$checked.' onclick="update_done_thematic_advance(this.value)"></center></td>';									
    					} else {
    						if ($thematic_advance['done_advance'] == 1) {
    							echo '<td><center>'.get_lang('Done').'</center></td>';	
    						} else {
    							echo '<td><center>-</center></td>';
    						}									
    					}
					}					
					echo '</tr>';							 
				}
				echo '</table>';
			} else {
				echo '<div><em>'.get_lang('ThereIsNoAThematicAdvance').'</em></div>';
			}							
			echo '</td>';				
			echo '</tr>';				
       } //End for
	   echo '</table>';
    } else {
	   echo '<div><em>'.get_lang('ThereIsNoAThematicSection').'</em></div>';		
    }	
} else if ($action == 'thematic_add' || $action == 'thematic_edit') {

	if (!$error) {
	    //@todo why the heck you create your token? use Security::get_token()! jm
		$token = md5(uniqid(rand(),TRUE));
		$_SESSION['thematic_token'] = $token;
	}
		
	// Display form
	$form = new FormValidator('thematic_add','POST','index.php?action=thematic_add&'.api_get_cidreq());
	
	if ($action == 'thematic_edit') {
		$form->addElement('header', '', get_lang('EditThematicSection'));	
	}
	
	$form->addElement('hidden', 'thematic_token',$token);
	$form->addElement('hidden', 'action', $action);
	
	if (!empty($thematic_id)) {
		$form->addElement('hidden', 'thematic_id',$thematic_id);
	}
		
	$form->add_textfield('title', get_lang('Title'), true, array('size'=>'50'));
	$form->add_html_editor('content', get_lang('Content'), false, false, array('ToolbarSet' => 'TrainingDescription', 'Width' => '100%', 'Height' => '250'));	
	$form->addElement('html','<div class="clear" style="margin-top:50px;"></div>');
	$form->addElement('style_submit_button', null, get_lang('Save'), 'class="save"');
	
    $show_form = true;
    
	if (!empty($thematic_data)) {
        
        if (api_get_session_id()) {
        	if ($thematic_data['session_id'] != api_get_session_id()) {
        		$show_form  = false;
                Display::display_error_message(get_lang('NotAllowedClickBack'),false);  
        	}
        }
		// set default values
		$default['title'] = $thematic_data['title'];
		$default['content'] = $thematic_data['content'];	
		$form->setDefaults($default);
	}
	
	// error messages
	if ($error) {	
		Display::display_error_message(get_lang('FormHasErrorsPleaseComplete'),false);	
	}
    if ($show_form)
	$form->display();		
}