<?php
/* For licensing terms, see /license.txt */
/**
 * Exercise library
 * @todo convert this lib into a static class
 *  
 * shows a question and its answers
 * @package chamilo.exercise
 * @author Olivier Brouckaert <oli.brouckaert@skynet.be>
 * @version $Id: exercise.lib.php 22247 2009-07-20 15:57:25Z ivantcholakov $
 * Modified by Hubert Borderiou 2011-10-21 Question Category
 */
/**
 * Code
 */
// The initialization class for the online editor is needed here.
require_once dirname(__FILE__).'/../inc/lib/fckeditor/fckeditor.php';

$TBL_EXERCICE_QUESTION      = Database::get_course_table(TABLE_QUIZ_TEST_QUESTION);
$TBL_EXERCICES              = Database::get_course_table(TABLE_QUIZ_TEST);
$TBL_QUESTIONS              = Database::get_course_table(TABLE_QUIZ_QUESTION);
$TBL_REPONSES               = Database::get_course_table(TABLE_QUIZ_ANSWER);
$TBL_DOCUMENT               = Database::get_course_table(TABLE_DOCUMENT);

$main_user_table            = Database::get_main_table(TABLE_MAIN_USER);
$main_course_user_table     = Database::get_main_table(TABLE_MAIN_COURSE_USER);
$TBL_TRACK_EXERCICES        = Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
$TBL_TRACK_ATTEMPT          = Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);



/**
 * Shows a question
 * 
 * @param int   question id
 * @param bool  if true only show the questions, no exercise title
 * @param bool  origin i.e = learnpath
 * @param int   current item from the list of questions
 * @param int   number of total questions
 * */
function showQuestion($questionId, $only_questions = false, $origin = false, $current_item = '', $show_title = true, $freeze = false, $user_choice = array()) {
	
	// Text direction for the current language
	$is_ltr_text_direction = api_get_text_direction() != 'rtl';
	
	$remind_question = 1;

	// Change false to true in the following line to enable answer hinting.
	$debug_mark_answer = api_is_allowed_to_edit() && false;

	// Reads question information
	if (!$objQuestionTmp = Question::read($questionId)) {
		// Question not found        
		return false;
	}
    
	$answerType    = $objQuestionTmp->selectType();
	$pictureName   = $objQuestionTmp->selectPicture();
	
	$html = '';
	if ($answerType != HOT_SPOT && $answerType != HOT_SPOT_DELINEATION) {
		// Question is not a hotspot
        
		if (!$only_questions) {
			$questionDescription = $objQuestionTmp->selectDescription();
			
			if ($show_title) {
				Testcategory::displayCategoryAndTitle($objQuestionTmp->id);	// 				
				echo Display::div($current_item.'. '.$objQuestionTmp->selectTitle(), array('class'=>'question_title'));
			}
			if (!empty($questionDescription)) {
				echo Display::div($questionDescription, array('class'=>'question_description'));
			}
			//@deprecated
			if (!empty($pictureName)) {
				//echo "<img src='../document/download.php?doc_url=%2Fimages%2F'".$pictureName."' border='0'>";
			}
		}
		
		echo '<div class="question_options">';
        
        if ($answerType == FREE_ANSWER && $freeze) {
            return '';
        }
        
		//$s .= '<table width="720" class="exercise_options" style="width: 720px;'.$option_ie.' background-color:#fff;">';
		$s = '';
		$s .= '<table class="exercise_options">';
		// construction of the Answer object (also gets all answers details)
		$objAnswerTmp = new Answer($questionId);
        
		$nbrAnswers   = $objAnswerTmp->selectNbrAnswers();
        $course_id = api_get_course_int_id();
        $quiz_question_options = Question::readQuestionOption($questionId, $course_id);
        
		// For "matching" type here, we need something a little bit special
		// because the match between the suggestions and the answers cannot be
		// done easily (suggestions and answers are in the same table), so we
		// have to go through answers first (elems with "correct" value to 0).
		$select_items = array();
		//This will contain the number of answers on the left side. We call them
		// suggestions here, for the sake of comprehensions, while the ones
		// on the right side are called answers
		$num_suggestions = 0;

		if ($answerType == MATCHING) {
			$x = 1; //iterate through answers
			$letter = 'A'; //mark letters for each answer
			$answer_matching = $cpt1 = array();
			$answer_suggestions = $nbrAnswers;

			for ($answerId=1; $answerId <= $nbrAnswers; $answerId++) {
				$answerCorrect = $objAnswerTmp->isCorrect($answerId);
				$numAnswer = $objAnswerTmp->selectAutoId($answerId);
				$answer=$objAnswerTmp->selectAnswer($answerId);
				if ($answerCorrect==0) {
					// options (A, B, C, ...) that will be put into the list-box
					// have the "correct" field set to 0 because they are answer
					$cpt1[$x] = $letter;
					$answer_matching[$x]=$objAnswerTmp->selectAnswerByAutoId($numAnswer);
					$x++; $letter++;
				}
			}
			$i = 1;
			
			$select_items[0]['id'] = 0;
			$select_items[0]['letter'] = '--';
			$select_items[0]['answer'] = '';
			
			foreach ($answer_matching as $id => $value) {
				$select_items[$i]['id'] 	= $value['id'];
				$select_items[$i]['letter'] = $cpt1[$id];
				$select_items[$i]['answer'] = $value['answer'];
				$i ++;
			}
			$num_suggestions = ($nbrAnswers - $x) + 1;
			
		} elseif ($answerType == FREE_ANSWER) {
			$fck_content = isset($user_choice[0]) && !empty($user_choice[0]['answer']) ? $user_choice[0]['answer']:null;
			
			$oFCKeditor = new FCKeditor("choice[".$questionId."]") ;
			
			$oFCKeditor->ToolbarSet = 'TestFreeAnswer';
			$oFCKeditor->Width  = '100%';
			$oFCKeditor->Height = '200';
			$oFCKeditor->Value	= $fck_content;
            $s .= '<tr><td colspan="3">';
            $s .= $oFCKeditor->CreateHtml();
            $s .= '</td></tr>';
		}  
    
		// Now navigate through the possible answers, using the max number of
		// answers for the question as a limiter
		$lines_count = 1; // a counter for matching-type answers
        $question_list = array();
            
        if ($answerType == MULTIPLE_ANSWER_TRUE_FALSE || $answerType ==  MULTIPLE_ANSWER_COMBINATION_TRUE_FALSE) {            
            $header = '';
            $header .= Display::tag('th', get_lang('Options'));   
            foreach ($objQuestionTmp->options as $key=>$item) {                
                $header .= Display::tag('th', $item);                           
            }                
            $s.= Display::tag('tr',$header, array('style'=>'text-align:left;'));  
        }
        
        $matching_correct_answer = 0;
        $user_choice_array = array();
        if (!empty($user_choice)) {        	
        	foreach($user_choice as $item) {
        		$user_choice_array[] = $item['answer'];
        	}
        }
        
        
		for ($answerId=1; $answerId <= $nbrAnswers; $answerId++) {
			$answer          = $objAnswerTmp->selectAnswer($answerId);            
			$answerCorrect   = $objAnswerTmp->isCorrect($answerId);            
			$numAnswer       = $objAnswerTmp->selectAutoId($answerId);
		
			// Unique answer
			if ($answerType == UNIQUE_ANSWER || $answerType == UNIQUE_ANSWER_NO_OPTION) {
				// set $debug_mark_answer to true at function start to
				// show the correct answer with a suffix '-x'
                
				$help = $selected = '';
				if ($debug_mark_answer) {
					if ($answerCorrect) {
						$help = 'x-';
						$selected = 'checked';
					}
				}
				$input_id = 'choice-'.$questionId.'-'.$answerId;
				if (isset($user_choice[0]['answer']) && $user_choice[0]['answer'] == $numAnswer ) {
					$attributes = array('id' =>$input_id, 'class'=>'checkbox','checked'=>1, 'selected'=>1);
				} else {
					$attributes = array('id' =>$input_id, 'class'=>'checkbox');
				}
				
				$answer = Security::remove_XSS($answer, STUDENT);
                
				$s .= Display::input('hidden','choice2['.$questionId.']','0');
				//@todo fix $is_ltr_text_direction
				//<p style="float: '.($is_ltr_text_direction ? 'left' : 'right').'; padding-'.($is_ltr_text_direction ? 'right' : 'left').': 4px;">
				//$s .= '<div style="margin-'.($is_ltr_text_direction ? 'left' : 'right').': 24px;">'.
						
				$s .= '<tr><td colspan="3">';
				$s .= '<span class="question_answer">';
				$s .= Display::input('radio', 'choice['.$questionId.']', $numAnswer, $attributes);
				$s .= Display::tag('label', $answer, array('for'=>$input_id)).'</span>';
				$s .= '</td></tr>';
			} elseif ($answerType == MULTIPLE_ANSWER || $answerType == MULTIPLE_ANSWER_TRUE_FALSE) {
               
				// multiple answers
				// set $debug_mark_answer to true at function start to
				// show the correct answer with a suffix '-x'
				$help = $selected = '';                
				if ($debug_mark_answer) {
					if ($answerCorrect) {
						$help = 'x-';
						$selected = 'checked="checked"';
					}
				}
				$input_id = 'choice-'.$questionId.'-'.$answerId;
				 
				$answer = Security::remove_XSS($answer, STUDENT);
				
				if (in_array($numAnswer, $user_choice_array)) {
					$attributes = array('id' =>$input_id, 'class'=>'checkbox','checked'=>1, 'selected'=>1);
				} else {
					$attributes = array('id' =>$input_id, 'class'=>'checkbox');
				}				

				$answer = Security::remove_XSS($answer, STUDENT);
                
                if ($answerType == MULTIPLE_ANSWER) {
                    $s .= '<input type="hidden" name="choice2['.$questionId.']" value="0" />';                
                    $s .= '<tr><td colspan="3">';
                    $s .= '<span class="question_answer">';
                                        
                    if ($debug_mark_answer) {
                        if ($answerCorrect) {
                            //$options['checked'] = 'checked';
                        }
                    }
    				
                    $s .= Display::tag('span', Display::input('checkbox', 'choice['.$questionId.']['.$numAnswer.']', $numAnswer, $attributes));                                        
                    $s .= Display::tag('label', $answer, array('for'=>$input_id)).'</span></td></tr>';                	

                } elseif ($answerType == MULTIPLE_ANSWER_TRUE_FALSE) {
                	$my_choice = array();
                    if (!empty($user_choice_array)) {
                        foreach ($user_choice_array as $item) {
                            $item = explode(':', $item);
                            $my_choice[$item[0]] = $item[1];
                        }
                    }
                    $s .='<tr>';
                    $s .= Display::tag('td', $answer);
                    if (!empty($quiz_question_options)) {
                    	foreach ($quiz_question_options as $id=>$item) {
                    		if (isset($my_choice[$numAnswer]) && $id == $my_choice[$numAnswer]) {
                    			$attributes = array('class'=>'checkbox','checked'=>1, 'selected'=>1);
                    		} else {
                    			$attributes = array('class'=>'checkbox');
                    		}
                    
                    		$s .= Display::tag('td', Display::input('radio', 'choice['.$questionId.']['.$numAnswer.']', $id, $attributes));
                    	}
                    }
                    $s.='<tr>';
                }
            
			} elseif ($answerType == MULTIPLE_ANSWER_COMBINATION) {
				// multiple answers
				// set $debug_mark_answer to true at function start to
				// show the correct answer with a suffix '-x'
				$help = $selected = '';
				if ($debug_mark_answer) {
					if ($answerCorrect) {
						$help = 'x-';
						$selected = 'checked="checked"';
					}
				}
				$input_id = 'choice-'.$questionId.'-'.$answerId;
				
				if (in_array($numAnswer, $user_choice_array)) {				    
				    $attributes = array('id'=>$input_id, 'class'=>'checkbox','checked'=>1, 'selected'=>1);
				} else {
				    $attributes = array('id'=>$input_id, 'class'=>'checkbox');
				}		
								
				$answer = Security::remove_XSS($answer, STUDENT);
				$s .= '<input type="hidden" name="choice2['.$questionId.']" value="0" />'.
				    '<tr><td colspan="3">';
				$s .= '<span class="question_answer">';				
				$s .= Display::tag('span', Display::input('checkbox', 'choice['.$questionId.']['.$numAnswer.']', 1, $attributes));				
			    $s .= Display::tag('label', $answer, array('for'=>$input_id)).'</span></td></tr>';
					
            } elseif ($answerType == MULTIPLE_ANSWER_COMBINATION_TRUE_FALSE) {
                // multiple answers
                // set $debug_mark_answer to true at function start to
                // show the correct answer with a suffix '-x'
                
            	$s .= '<input type="hidden" name="choice2['.$questionId.']" value="0" />';
            	
            	$my_choice = array();
            	if (!empty($user_choice_array)) {
            		foreach ($user_choice_array as $item) {
            			$item = explode(':', $item);
            			$my_choice[$item[0]] = $item[1];
            		}
            	}
            	$answer = Security::remove_XSS($answer, STUDENT);
            	$s .='<tr>';
            	$s .= Display::tag('td', $answer);
            	
            	foreach ($objQuestionTmp->options as $key => $item) {
            		//$options['value'] = $key;
            		if (isset($my_choice[$numAnswer]) && $key == $my_choice[$numAnswer]) {
            			$attributes = array('class'=>'checkbox','checked'=>1, 'selected'=>1);
            		} else {
            			$attributes = array('class'=>'checkbox');
            		}
            		$s .= Display::tag('td', Display::input('radio','choice['.$questionId.']['.$numAnswer.']', $key, $attributes));
            	}
            	$s.='<tr>';
			} elseif ($answerType == FILL_IN_BLANKS) {
				
				/*
				// splits text and weightings that are joined with the character '::'
				list($answer) = explode('::',$answer);
			
				//getting the matches
				$answer = api_ereg_replace('\[[^]]+\]','<input type="text" name="choice['.$questionId.'][]" size="10" />',($answer));
				
			
				
				$answer = api_preg_replace('/\[[^]]+\]/', Display::input('text', "choice[$questionId][]", '', $attributes), $answer);
				
				api_preg_match_all('/\[[^]]+\]/', $answer, $fill_list);
				

				if (isset($user_choice[0]['answer'])) {
				    api_preg_match_all('/\[[^]]+\]/', $user_choice[0]['answer'], $user_fill_list);	
				    $user_fill_list = $user_fill_list[0];
				}*/
				
				list($answer) = explode('::',$answer);
				
				api_preg_match_all('/\[[^]]+\]/', $answer, $teacher_answer_list);				
				
				if (isset($user_choice[0]['answer'])) {
					api_preg_match_all('/\[[^]]+\]/', $user_choice[0]['answer'], $student_answer_list);
					$student_answer_list = $student_answer_list[0];
				}
				
				//var_dump($teacher_answer_list, $student_answer_list);				
								
				if (!empty($teacher_answer_list) && !empty($student_answer_list)) {
				    $teacher_answer_list = $teacher_answer_list[0];
				    
				    $i = 0;				    
				    foreach($teacher_answer_list as $teacher_item) {				    	
				        $value = null;
				        if (isset($student_answer_list[$i]) && !empty($student_answer_list[$i])) {
				        	//Cleaning student answer list
				            $value = strip_tags($student_answer_list[$i]);				            				            
				            $value = api_substr($value,1, api_strlen($value)-2);
				            $value = explode('/', $value);
				            if (!empty($value[0])) {
				            	$value = trim($value[0]);
				            	$value = str_replace('&nbsp;', '',  $value);
				            }
				            $answer = api_preg_replace('/\['.$teacher_item.'+\]/', Display::input('text', "choice[$questionId][]", $value), $answer);				            
				        }				        				        
				        $i++;				        
				    }
				} else {
					$answer = api_preg_replace('/\[[^]]+\]/', Display::input('text', "choice[$questionId][]", '', $attributes), $answer);
				}
				
				$s .= '<tr><td colspan="3">'.$answer.'</td></tr>';
            } elseif ($answerType == MATCHING) {
				//  matching type, showing suggestions and answers
				// TODO: replace $answerId by $numAnswer
				
				if ($answerCorrect != 0) {
					// only show elements to be answered (not the contents of
					// the select boxes, who are corrrect = 0)
					$s .= '<tr><td width="45%" valign="top">';
					$parsed_answer = $answer;
                    //$question_list[] = $parsed_answer;
					//left part questions
					$s .= ' <span style="float:left; width:8%;"><b>'.$lines_count.'</b>.&nbsp;</span>
						 	<span style="float:left; width:92%;">'.$parsed_answer.'</span></td>';
					//middle part (matches selects)					
					
					$s .= '<td width="10%" valign="top" align="center">&nbsp;&nbsp;
				            <select name="choice['.$questionId.']['.$numAnswer.']">';
					
					// fills the list-box
					foreach ($select_items as $key=>$val) {
						// set $debug_mark_answer to true at function start to
						// show the correct answer with a suffix '-x'
						$help = $selected = '';
						if ($debug_mark_answer) {
							if ($val['id'] == $answerCorrect) {
								$help = '-x';
								//$selected = 'selected="selected"';
							}
						}						
						if (isset($user_choice[$matching_correct_answer]) && $val['id'] == $user_choice[$matching_correct_answer]['answer']) {
						    $selected = 'selected="selected"';
						}
						$s .= '<option value="'.$val['id'].'" '.$selected.'>'.$val['letter'].$help.'</option>';						
				
					}  // end foreach()

					$s .= '</select></td>';
					//print_r($select_items);
					//right part (answers)
					$s.='<td width="45%" valign="top" >';
					if (isset($select_items[$lines_count])) {
						$s.='<span style="float:left; width:5%;"><b>'.$select_items[$lines_count]['letter'].'.</b></span>'.
							 '<span style="float:left; width:95%;">'.$select_items[$lines_count]['answer'].'</span>';
					} else {
						$s.='&nbsp;';
					}
					$s .= '</td>';
					$s .= '</tr>';
					$lines_count++;
					//if the left side of the "matching" has been completely
					// shown but the right side still has values to show...
					if (($lines_count -1) == $num_suggestions) {
						// if it remains answers to shown at the right side
						while (isset($select_items[$lines_count])) {
							$s .= '<tr>
								  <td colspan="2"></td>
								  <td valign="top">';
							$s.='<b>'.$select_items[$lines_count]['letter'].'.</b> '.$select_items[$lines_count]['answer'];
							$s.="</td>
							</tr>";
							$lines_count++;
						}	// end while()
					}  // end if()
					$matching_correct_answer++;
				}
			}
		}	// end for()
		$s .= '</table>';		
		$s .= '</div>';
		

		// destruction of the Answer object
		unset($objAnswerTmp);

		// destruction of the Question object
		unset($objQuestionTmp);

		if ($origin != 'export') {
			echo $s;
		} else {
			return $s;
		}
	} elseif ($answerType == HOT_SPOT || $answerType == HOT_SPOT_DELINEATION) {
		// Question is a HOT_SPOT        
        //checking document/images visibility
        if (api_is_platform_admin() || api_is_course_admin()) {
            require_once api_get_path(LIBRARY_PATH).'document.lib.php';
            $course = api_get_course_info();        
            $doc_id = DocumentManager::get_document_id($course, '/images/'.$pictureName);  
            if (is_numeric($doc_id)) {              
                $images_folder_visibility = api_get_item_visibility($course,'document', $doc_id, api_get_session_id());                 
                if (!$images_folder_visibility) {
                    //This message is shown only to the course/platform admin if the image is set to visibility = false
                    Display::display_warning_message(get_lang('ChangeTheVisibilityOfTheCurrentImage'));
                }
            }
        }
		$questionName         = $objQuestionTmp->selectTitle();
		$questionDescription  = $objQuestionTmp->selectDescription();
        
        if ($freeze) {
            echo Display::img($objQuestionTmp->selectPicturePath());
            return;
        }        

		// Get the answers, make a list
		$objAnswerTmp         = new Answer($questionId);
		$nbrAnswers           = $objAnswerTmp->selectNbrAnswers();

		// get answers of hotpost
		$answers_hotspot = array();
		for ($answerId=1;$answerId <= $nbrAnswers;$answerId++) {
			$answers = $objAnswerTmp->selectAnswerByAutoId($objAnswerTmp->selectAutoId($answerId));
			$answers_hotspot[$answers['id']] = $objAnswerTmp->selectAnswer($answerId);
		}

		// display answers of hotpost order by id
		$answer_list = '<div style="padding: 10px; margin-left: 0px; border: 1px solid #A4A4A4; height: 408px; width: 200px;"><b>'.get_lang('HotspotZones').'</b><dl>';
		if (!empty($answers_hotspot)) {
			ksort($answers_hotspot);
			foreach ($answers_hotspot as $key => $value) {
				$answer_list .= '<dt>'.$key.'.- '.$value.'</dt><br />';
			}
		}
		$answer_list .= '</dl></div>';
		
		if ($answerType == HOT_SPOT_DELINEATION) {
			$answer_list='';
			$swf_file = 'hotspot_delineation_user';
			$swf_height = 405;			
		} else {
			$swf_file = 'hotspot_user';
			$swf_height = 436;
		}

		if (!$only_questions) {
            if ($show_title) {
								Testcategory::displayCategoryAndTitle($objQuestionTmp->id);	//             	
                echo '<div class="question_title">'.$current_item.'. '.$questionName.'</div>';
            }
			//@todo I need to the get the feedback type
			echo '<input type="hidden" name="hidden_hotspot_id" value="'.$questionId.'" />';
			echo '<table class="exercise_questions" >
				  <tr>
			  		<td valign="top" colspan="2">';
			echo $questionDescription;
			echo '</td></tr>';
		}        
		$canClick = isset($_GET['editQuestion']) ? '0' : (isset($_GET['modifyAnswers']) ? '0' : '1');
        
		$s .= '<script language="JavaScript" type="text/javascript" src="../plugin/hotspot/JavaScriptFlashGateway.js"></script>
						<script src="../plugin/hotspot/hotspot.js" type="text/javascript" language="JavaScript"></script>
						<script language="JavaScript" type="text/javascript">
						<!--
						// Globals
						// Major version of Flash required
						var requiredMajorVersion = 7;
						// Minor version of Flash required
						var requiredMinorVersion = 0;
						// Minor version of Flash required
						var requiredRevision = 0;
						// the version of javascript supported
						var jsVersion = 1.0;
						// -->
						</script>
						<script language="VBScript" type="text/vbscript">
						<!-- // Visual basic helper required to detect Flash Player ActiveX control version information
						Function VBGetSwfVer(i)
						  on error resume next
						  Dim swControl, swVersion
						  swVersion = 0

						  set swControl = CreateObject("ShockwaveFlash.ShockwaveFlash." + CStr(i))
						  if (IsObject(swControl)) then
						    swVersion = swControl.GetVariable("$version")
						  end if
						  VBGetSwfVer = swVersion
						End Function
						// -->
						</script>

						<script language="JavaScript1.1" type="text/javascript">
						<!-- // Detect Client Browser type
						var isIE  = (navigator.appVersion.indexOf("MSIE") != -1) ? true : false;
						var isWin = (navigator.appVersion.toLowerCase().indexOf("win") != -1) ? true : false;
						var isOpera = (navigator.userAgent.indexOf("Opera") != -1) ? true : false;
						jsVersion = 1.1;
						// JavaScript helper required to detect Flash Player PlugIn version information
						function JSGetSwfVer(i) {
							// NS/Opera version >= 3 check for Flash plugin in plugin array
							if (navigator.plugins != null && navigator.plugins.length > 0) {
								if (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]) {
									var swVer2 = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
						      		var flashDescription = navigator.plugins["Shockwave Flash" + swVer2].description;
									descArray = flashDescription.split(" ");
									tempArrayMajor = descArray[2].split(".");
									versionMajor = tempArrayMajor[0];
									versionMinor = tempArrayMajor[1];
									if ( descArray[3] != "" ) {
										tempArrayMinor = descArray[3].split("r");
									} else {
										tempArrayMinor = descArray[4].split("r");
									}
						      		versionRevision = tempArrayMinor[1] > 0 ? tempArrayMinor[1] : 0;
						            flashVer = versionMajor + "." + versionMinor + "." + versionRevision;
						      	} else {
									flashVer = -1;
								}
							}
							// MSN/WebTV 2.6 supports Flash 4
							else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.6") != -1) flashVer = 4;
							// WebTV 2.5 supports Flash 3
							else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.5") != -1) flashVer = 3;
							// older WebTV supports Flash 2
							else if (navigator.userAgent.toLowerCase().indexOf("webtv") != -1) flashVer = 2;
							// Can\'t detect in all other cases
							else
							{
								flashVer = -1;
							}
							return flashVer;
						}
						// When called with reqMajorVer, reqMinorVer, reqRevision returns true if that version or greater is available

						function DetectFlashVer(reqMajorVer, reqMinorVer, reqRevision) {
						 	reqVer = parseFloat(reqMajorVer + "." + reqRevision);
						   	// loop backwards through the versions until we find the newest version
							for (i=25;i>0;i--) {
								if (isIE && isWin && !isOpera) {
									versionStr = VBGetSwfVer(i);
								} else {
									versionStr = JSGetSwfVer(i);
								}
								if (versionStr == -1 ) {
									return false;
								} else if (versionStr != 0) {
									if(isIE && isWin && !isOpera) {
										tempArray         = versionStr.split(" ");
										tempString        = tempArray[1];
										versionArray      = tempString .split(",");
									} else {
										versionArray      = versionStr.split(".");
									}
									versionMajor      = versionArray[0];
									versionMinor      = versionArray[1];
									versionRevision   = versionArray[2];

									versionString     = versionMajor + "." + versionRevision;   // 7.0r24 == 7.24
									versionNum        = parseFloat(versionString);
						        	// is the major.revision >= requested major.revision AND the minor version >= requested minor
									if ( (versionMajor > reqMajorVer) && (versionNum >= reqVer) ) {
										return true;
									} else {
										return ((versionNum >= reqVer && versionMinor >= reqMinorVer) ? true : false );
									}
								}
							}
						}
						// -->
						</script>';
		$s .= '<tr><td valign="top" colspan="2" width="520"><table><tr><td width="520">
					<script language="JavaScript" type="text/javascript">
						<!--
						// Version check based upon the values entered above in "Globals"
						var hasReqestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);


						// Check to see if the version meets the requirements for playback
						if (hasReqestedVersion) {  // if we\'ve detected an acceptable version
						    var oeTags = \'<object type="application/x-shockwave-flash" data="../plugin/hotspot/'.$swf_file.'.swf?modifyAnswers='.$questionId.'&amp;canClick:'.$canClick.'" width="600" height="'.$swf_height.'">\'
						    			+ \'<param name="wmode" value="transparent">\'
										+ \'<param name="movie" value="../plugin/hotspot/'.$swf_file.'.swf?modifyAnswers='.$questionId.'&amp;canClick:'.$canClick.'" />\'
										+ \'<\/object>\';
						    document.write(oeTags);   // embed the Flash Content SWF when all tests are passed
						} else {  // flash is too old or we can\'t detect the plugin
							var alternateContent = "Error<br \/>"
								+ "Hotspots requires Macromedia Flash 7.<br \/>"
								+ "<a href=\"http://www.macromedia.com/go/getflash/\">Get Flash<\/a>";
							document.write(alternateContent);  // insert non-flash content
						}
						// -->
					</script>
					</td>
					<td valign="top" align="left">'.$answer_list.'</td></tr>
					</table>
		</td></tr>';        
		echo $s;
	}
	echo '</table>';
	return $nbrAnswers;
}

function get_exercise_track_exercise_info($exe_id) {
	$TBL_EXERCICES         	= Database::get_course_table(TABLE_QUIZ_TEST);
	$TBL_TRACK_EXERCICES	= Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
	$exe_id = intval($exe_id);
    $result = array();
    if (!empty($exe_id)) {
	   $sql_fb_type = 'SELECT * FROM '.$TBL_EXERCICES.' as e INNER JOIN '.$TBL_TRACK_EXERCICES.' as te  ON (e.id=te.exe_exo_id) WHERE te.exe_id='.$exe_id;
	   $res_fb_type = Database::query($sql_fb_type);
	   $result      = Database::fetch_array($res_fb_type, 'ASSOC');
    }
	return $result;	
}


/**
 * Validates the time control key
 */
function exercise_time_control_is_valid($exercise_id) {
	//Fast check
	$exercise_id = intval($exercise_id);
	$TBL_EXERCICES =  Database::get_course_table(TABLE_QUIZ_TEST);
	$sql 	= "SELECT expired_time FROM $TBL_EXERCICES WHERE id = $exercise_id";
	$result = Database::query($sql);
	$row	= Database::fetch_array($result, 'ASSOC');
	if (!empty($row['expired_time']) ) {
		$current_expired_time_key = get_time_control_key($exercise_id);        
		if (isset($_SESSION['expired_time'][$current_expired_time_key])) {                	
	        $current_time = time();
			$expired_time = api_strtotime($_SESSION['expired_time'][$current_expired_time_key], 'UTC');
			$total_time_allowed = $expired_time + 30;
			//error_log('expired time converted + 30: '.$total_time_allowed);
			//error_log('$current_time: '.$current_time);
	        if ($total_time_allowed < $current_time) {
	        	return false;
	        }
	        return true;
		} else {
			return false;
		}
	} else {
		return true;
	}
}

/**
	Deletes the time control token 
*/
function exercise_time_control_delete($exercise_id) {	
	$current_expired_time_key = get_time_control_key($exercise_id);
	unset($_SESSION['expired_time'][$current_expired_time_key]);	
}

/**
	Generates the time control key
*/
function generate_time_control_key($exercise_id) {
	$exercise_id = intval($exercise_id);
	return api_get_course_int_id().'_'.api_get_session_id().'_'.$exercise_id.'_'.api_get_user_id();
}
/**
	Returns the time controller key
    @todo this function is the same as generate_time_control_key
*/
function get_time_control_key($exercise_id) {
	$exercise_id = intval($exercise_id);
	return api_get_course_int_id().'_'.api_get_session_id().'_'.$exercise_id.'_'.api_get_user_id();
}
/**
 * @todo use this function instead of get_time_control_key
 */
function get_session_time_control_key($exercise_id) {
    $time_control_key = get_time_control_key($exercise_id);
    $return_value = $_SESSION['expired_time'][$time_control_key];
    return $return_value;
}

/**
 * Gets count of exam results
 * @todo this function should be moved in a library  + no global calls 
 * Modified by hubert borderiou 08-11-2011
 */
function get_count_exam_results() {
    // I know it's bad to add a static integer here... but
    // it factorise function get_exam_results_data
    // and I think it worths it.
    $tabres = get_exam_results_data(0, 9999999, 0, "ASC");
    return count($tabres);
}


/**
 * Gets the exam'data results
 * @todo this function should be moved in a library  + no global calls 
 */
function get_exam_results_data($from, $number_of_items, $column, $direction) {
    //@todo replace all this globals
    global  $is_allowedToEdit, $is_tutor,$TBL_USER, $TBL_EXERCICES,$TBL_TRACK_HOTPOTATOES, 
            $TBL_TRACK_EXERCICES, $TBL_TRACK_ATTEMPT_RECORDING,
            $filter_by_not_revised, $filter_by_revised, $documentPath, $filter, 
            $filterByGroup, $TBL_GROUP_REL_USER;
    
    $session_id_and = ' AND te.session_id = ' . api_get_session_id() . ' ';
    
    $exercise_id = intval($_GET['exerciseId']);
    
    $exercise_where = '';
    if (!empty($exercise_id)) {
        $exercise_where .= ' AND te.exe_exo_id = '.$exercise_id.'  ';
    }  
    $hotpotatoe_where = '';
    if (!empty($_GET['path'])) {
        $hotpotatoe_path = Database::escape_string($_GET['path']);
        $hotpotatoe_where .= ' AND exe_name = "'.$hotpotatoe_path.'"  ';
    }  
    
    if ($is_allowedToEdit || $is_tutor) {
        
        $user_id_and = '';
        if (!empty ($_REQUEST['filter_by_user'])) {
            if ($_REQUEST['filter_by_user'] == 'all') {
                $user_id_and = " AND user_id like '%'";
            } else {
                $user_id_and = " AND user_id = '" . intval($_REQUEST['filter_by_user']) . "' ";
            }
        }        
        
          
        if ($_GET['gradebook'] == 'view') {
            $exercise_where_query = ' te.exe_exo_id =ce.id AND ';
        }

		$sqlFromOption = "";
		$sqlWhereOption = "";           // for hpsql
	    $sql_inner_join_tbl_user = "";    
	    $sql_inner_join_tbl_track_exercices = "";
		$filterByGroup = intval($filterByGroup);
		
        //@todo fix to work with COURSE_RELATION_TYPE_RRHH in both queries

        // 
        // Filter by test revised or not
        // 
        switch ($filter) {
            case 0 :    // filter_by_not_revised
            case 1 :
                $sql_inner_join_tbl_track_exercices = "
                    (SELECT * FROM $TBL_TRACK_EXERCICES AS tte
                    WHERE tte.exe_id NOT IN (SELECT DISTINCT exe_id FROM $TBL_TRACK_ATTEMPT_RECORDING))";
                break;
            case 2 :    // filter_by_revised
                $sql_inner_join_tbl_track_exercices = "
                    (SELECT * FROM $TBL_TRACK_EXERCICES AS tte
                    WHERE tte.exe_id IN (SELECT DISTINCT exe_id FROM $TBL_TRACK_ATTEMPT_RECORDING))";
                break;
           default :    // no filter for revised
                $sql_inner_join_tbl_track_exercices = $TBL_TRACK_EXERCICES;
                break;
        }
		
		// 
		// Filter by group
		// 
		switch ($filterByGroup) {
		    case -1 : // no filter
        	    $sql_inner_join_tbl_user = $TBL_USER;    
		        break;
		    case 0 : // user not in any group
    		    $sql_inner_join_tbl_user = " 
                    (SELECT u.user_id, firstname, lastname, email , username
                    FROM $TBL_USER u 
                    WHERE 
                    u.user_id NOT IN (
                        SELECT user_id 
                        FROM $TBL_GROUP_REL_USER
                        WHERE c_id=".api_get_course_int_id()."))";
    			$sqlFromOption = " ,$TBL_GROUP_REL_USER AS gru ";
    			$sqlWhereOption = " AND user.user_id NOT IN	( SELECT user_id FROM $TBL_GROUP_REL_USER WHERE c_id=".api_get_course_int_id().") ";
		        break;
		    default : // user in group $filterByGroup
    		    $sql_inner_join_tbl_user = " 
                    (SELECT u.user_id, firstname, lastname, email  , username
                    FROM $TBL_USER u 
                    INNER JOIN $TBL_GROUP_REL_USER gru ON (
                        gru.user_id=u.user_id 
                        AND gru.group_id=$filterByGroup 
                        AND gru.c_id=".api_get_course_int_id()."))";
    			$sqlFromOption = " ,$TBL_GROUP_REL_USER AS gru ";
    			$sqlWhereOption = "  AND gru.c_id=".api_get_course_int_id()." AND gru.user_id=user.user_id AND gru.group_id=".Database :: escape_string($filterByGroup);
		        break;
		}
		
        $first_and_last_name = api_is_western_name_order() ? "firstname as col0, lastname col1" : "lastname as col0, firstname as col1";
        
        // 
        // sql for chamilo-type tests for teacher / tutor view
        // 
        $sql="  
                SELECT
                    user_id, 
                    $first_and_last_name, 
                    ce.title as col3, 
                    username as col2,
                    te.exe_result as exresult , 
                    te.exe_weighting as exweight,
                    te.exe_date as exdate, 
                    te.exe_id as exid, 
                    email as exemail, 
                    te.start_date as col6, 
                    steps_counter as exstep, 
                    exe_user_id as excruid,
                    te.exe_duration as exduration, 
                    propagate_neg
                FROM 
                    $TBL_EXERCICES  AS ce 
                INNER JOIN $sql_inner_join_tbl_track_exercices AS te ON (te.exe_exo_id = ce.id) 
                INNER JOIN $sql_inner_join_tbl_user  AS user ON (user.user_id = exe_user_id)
                WHERE 
                    te.status != 'incomplete' 
                    AND te.exe_cours_id='" . api_get_course_id() . "'  $user_id_and  $session_id_and 
                    AND ce.active <>-1 
                    AND orig_lp_id = 0 
                    AND orig_lp_item_id = 0
                    AND ce.c_id=".api_get_course_int_id()."
                    $exercise_where ";

        // 
        // sql for hotpotatoes tests for teacher / tutor view
        // 
        $hpsql="
                SELECT 
                    $first_and_last_name , 
                    tth.exe_name, 
                    tth.exe_result , 
                    tth.exe_weighting, 
                    tth.exe_date
                FROM 
                    $TBL_TRACK_HOTPOTATOES tth, 
                    $TBL_USER user
                    $sqlFromOption
                WHERE  
                    user.user_id=tth.exe_user_id 
                    AND tth.exe_cours_id = '" . api_get_course_id()."'  
                    $hotpotatoe_where 
                    $sqlWhereOption
                ORDER BY 
                    tth.exe_cours_id ASC, 
                    tth.exe_date DESC";
    } 
    else {
        // get only this user's results
        $user_id_and = ' AND te.exe_user_id = ' . api_get_user_id() . ' ';
   
        /*$sql="SELECT ".(api_is_western_name_order() ? "firstname as col0, lastname col1" : "lastname as col0, firstname as col1").", ce.title as extitle, te.exe_result as exresult, " .
                    "te.exe_weighting as exweight, te.exe_date as exdate, te.exe_id as exid, email as exemail, " .
                    "te.start_date as exstart, steps_counter as exstep, cuser.user_id as excruid, te.exe_duration as exduration, ce.results_disabled as exdisabled
                    FROM $TBL_EXERCICES AS ce , $TBL_TRACK_EXERCICES AS te, $TBL_USER AS user,$tbl_course_rel_user AS cuser
            WHERE  user.user_id=cuser.user_id AND te.exe_exo_id = ce.id AND te.status != 'incomplete' AND cuser.user_id=te.exe_user_id 
            AND te.exe_cours_id='" . Database :: escape_string($_cid) . "'
            AND cuser.relation_type<>".COURSE_RELATION_TYPE_RRHH." $user_id_and $session_id_and AND ce.active <>-1 AND" .
            " orig_lp_id = 0 AND orig_lp_item_id = 0 AND cuser.course_code=te.exe_cours_id ORDER BY col1, te.exe_cours_id ASC, ce.title ASC, te.exe_date DESC";*/

        $sql="
                SELECT 
                    ce.title as col0, 
                    ce.title as col1, 
                    te.exe_duration as exduration, 
                    firstname, 
                    lastname, 
                    te.exe_result as exresult, 
                    te.exe_weighting as exweight, 
                    te.exe_date as exdate, 
                    te.exe_id as exid, 
                    email as exemail,
                    te.start_date as col2, 
                    steps_counter as exstep, 
                    exe_user_id as excruid,  
                    ce.results_disabled as exdisabled, 
                    propagate_neg
                FROM 
                    $TBL_EXERCICES  AS ce 
                    INNER JOIN $TBL_TRACK_EXERCICES AS te ON (te.exe_exo_id = ce.id) 
                    INNER JOIN  $TBL_USER  AS user ON (user.user_id = exe_user_id)
                WHERE 
                    te.status != 'incomplete' 
                    AND te.exe_cours_id='" . api_get_course_id() . "'  
                    $user_id_and $session_id_and 
                    AND ce.active <>-1 
                    AND orig_lp_id = 0 
                    AND orig_lp_item_id = 0  
                    AND ce.c_id= ".api_get_course_int_id()."
                    $exercise_where";

        $hpsql = "SELECT '', '', exe_name, exe_result , exe_weighting, exe_date
                  FROM $TBL_TRACK_HOTPOTATOES
                  WHERE exe_user_id = '" . api_get_user_id() . "' AND exe_cours_id = '" . api_get_course_id() . "' $hotpotatoe_where
                  ORDER BY exe_cours_id ASC, exe_date DESC";
    }   
    
    $teacher_list = CourseManager::get_teacher_list_from_course_code(api_get_course_id());
    $teacher_id_list = array();
    foreach ($teacher_list as $teacher) {
    	$teacher_id_list[] = $teacher['user_id'];
    }    
    
    if (empty($hotpotatoe_where)) {
        $column             = intval($column);
        $from               = intval($from);
        $number_of_items    = intval($number_of_items);
        $sql               .= " ORDER BY col$column $direction ";
        $sql               .= " LIMIT $from, $number_of_items";
    
        $results = array();
    
        $resx = Database::query($sql);
        while ($rowx = Database::fetch_array($resx,'ASSOC')) {
            $results[] = $rowx;
        }
    
        $list_info = array();
    
        // Print test results.
        $lang_nostartdate = get_lang('NoStartDate') . ' / ';    
        
        if (is_array($results)) {
            $users_array_id = array ();
            if ($_GET['gradebook'] == 'view') {
                $filter_by_no_revised = true;
                $from_gradebook = true;
            }
            $sizeof = count($results);
    
            $user_list_id = array ();
            $user_last_name = '';
            $user_first_name = '';
            $quiz_name_list = '';
            $duration_list = '';
            $date_list = '';
            $result_list = '';
            $more_details_list = '';
            for ($i = 0; $i < $sizeof; $i++) {
                $revised = false;
                $sql_exe = 'SELECT exe_id FROM ' . $TBL_TRACK_ATTEMPT_RECORDING . ' WHERE author != "" AND exe_id = ' . Database :: escape_string($results[$i]['exid']) .' LIMIT 1';            
                $query = Database::query($sql_exe);
                if (Database :: num_rows($query) > 0) {
                    $revised = true;
                }
                if ($from_gradebook && ($is_allowedToEdit || $is_tutor)) {
                    if (in_array($results[$i]['col2'] . $results[$i]['col0'] . $results[$i]['col1'], $users_array_id)) {
                        continue;
                    }
                    $users_array_id[] = $results[$i]['col2'] . $results[$i]['col0'] . $results[$i]['col1'];
                }
                if ($is_allowedToEdit || $is_tutor) {      
                    $user_first_name = $results[$i]['col0'];
                    $user_last_name = $results[$i]['col1'];
                    $user_login = $results[$i]['col2'];
                    $user = $results[$i]['col0'] . $results[$i]['col1'];
                    $test = $results[$i]['col3'];                    
                    $user_groups = displayGroupsForUser('<br/>', $results[$i]['user_id']);
                } else {
                    $user_first_name = $results[$i]['firstname'];
                    $user_last_name = $results[$i]['lastname'];
                    $user = $results[$i]['firstname'] . $results[$i]['lastname'];
                    $test = $results[$i]['col0'];                
                }
                $user_list_id[] = $results[$i]['excruid'];
                $id = $results[$i]['exid'];   
               
                $quiz_name_list = $test;
                $dt = api_convert_and_format_date($results[$i]['exweight']);
                $res = $results[$i]['exresult'];
    
                $duration = intval($results[$i]['exduration']);
                // we filter the results if we have the permission to
                if (isset ($results[$i]['exdisabled']))
                    $result_disabled = intval($results[$i]['exdisabled']);
                else
                    $result_disabled = 0;
    
                if ($result_disabled == 0) {
                    $add_start_date = $lang_nostartdate;
    
                    if ($is_allowedToEdit || $is_tutor) {
                        $user = $results[$i]['col0'] . $results[$i]['col1'];
                        $start_date = $results[$i]['col4'];
                    } else {
                        $start_date = $results[$i]['col2'];
                    }
                    
                    if ($start_date != "0000-00-00 00:00:00") {
                        $start_date_timestamp   = api_strtotime($start_date);
                        $exe_date_timestamp     = api_strtotime($results[$i]['exdate']);                        
    
                        $my_duration = ceil((($exe_date_timestamp - $start_date_timestamp) / 60));
                        if ($my_duration == 1 ) {
                            $duration_list = $my_duration . ' ' . get_lang('MinMinute');
                        } else {
                            $duration_list =  $my_duration. ' ' . get_lang('MinMinutes');
                        }
                        if ($results[$i]['exstep'] > 1) {
                            //echo ' ( ' . $results[$i][8] . ' ' . get_lang('Steps') . ' )';
                            $duration_list = ' ( ' . $results[$i]['exstep'] . ' ' . get_lang('Steps') . ' )';
                        }
                        //$add_start_date = api_convert_and_format_date($start_date) . ' / ';
                    } else {
                        $duration_list = get_lang('NoLogOfDuration');
                        //echo get_lang('NoLogOfDuration');
                    }
                    // Date conversion
                    if ($is_allowedToEdit || $is_tutor) {                        
                        $date_list = api_get_local_time($results[$i]['col4']). ' / ' . api_get_local_time($results[$i]['exdate']);
                    } else {                        
                        $date_list = api_get_local_time($results[$i]['col2']). ' / ' . api_get_local_time($results[$i]['exdate']);
                    }
                    // there are already a duration test period calculated??
                    //echo '<td>'.sprintf(get_lang('DurationFormat'), $duration).'</td>';
    
                    // if the float look like 10.00 we show only 10
    
                    $my_res     = $results[$i]['exresult'];
                    $my_total   = $results[$i]['exweight'];
                    if (!$results[$i]['propagate_neg'] && $my_res < 0) {
                        $my_res = 0;
                    }
                    $ex = show_score($my_res, $my_total);
                    
                    $result_list = $ex;
    
                    $html_link = '';
                    if ($is_allowedToEdit || $is_tutor) {
                    	if (in_array($results[$i]['excruid'], $teacher_id_list)) {
                    		$html_link.= Display::return_icon('teachers.gif', get_lang('Teacher'));
                    	}
                        if ($revised) {
                            $html_link.= "<a href='exercise_show.php?".api_get_cidreq()."&action=edit&id=$id'>".Display :: return_icon('edit.png', get_lang('Edit'), array(), 22);
                            $html_link.= '&nbsp;';
                        } else {
                            $html_link.="<a href='exercise_show.php?".api_get_cidreq()."&action=qualify&id=$id'>".Display :: return_icon('quiz.gif', get_lang('Qualify'));
                            $html_link.='&nbsp;';
                        }
                        $html_link.="</a>";                     
                        if ($is_allowedToEdit) {
                            if ($filter==2){
                                $html_link.=' <a href="exercise_history.php?'.api_get_cidreq().'&exe_id=' . $id . '">' .Display :: return_icon('history.gif', get_lang('ViewHistoryChange')).'</a>';
                            }
                        }
                        if (api_is_platform_admin() || $is_tutor) {
                        	
                            $html_link.=' <a href="exercice.php?'.api_get_cidreq().'&show=result&filter_by_user='.intval($_GET['filter_by_user']).'&filter=' . $filter . '&exerciseId='.$exercise_id.'&delete=delete&did=' . $id . '" onclick="javascript:if(!confirm(\'' . sprintf(get_lang('DeleteAttempt'), $user, $dt) . '\')) return false;">'.Display :: return_icon('delete.png', get_lang('Delete')).'</a>';
                            
                            $html_link.='&nbsp;';
                        }
                    } else {
                    	
                    	$attempt_url 	= api_get_path(WEB_CODE_PATH).'exercice/result.php?'.api_get_cidreq().'&id='.$results[$i]['exid'].'&id_session='.api_get_session_id().'&height=500&width=750';
                    	$attempt_link 	= Display::url(get_lang('Show'), $attempt_url, array('class'=>'thickbox'))."&nbsp;&nbsp;&nbsp;";
                    	
                    	$html_link.= $attempt_link;
                    	
                        if ($revised) {
                        	$html_link.= Display::span(get_lang('Validated'), array('class'=>'label_tag notice'));                            
                        } else {
                        	$html_link.= Display::span(get_lang('NotValidated'), array('class'=>'label_tag notice'));                            
                        }
                    }
                    $more_details_list = $html_link;
                    if ($is_allowedToEdit || $is_tutor) {
                        $list_info[] = array($user_first_name,$user_last_name,$user_login,$user_groups,$quiz_name_list,$duration_list,$date_list,$result_list,$more_details_list);
                    } else {
                        $list_info[] = array($quiz_name_list,$duration_list,$date_list,$result_list,$more_details_list);
                    }
                }
            }
        }
    } else {
        $hpresults = getManyResultsXCol($hpsql, 6);
   
        // Print HotPotatoes test results.
        if (is_array($hpresults)) {
            
            for ($i = 0; $i < sizeof($hpresults); $i++) {
               
                $hp_title = GetQuizName($hpresults[$i][2], $documentPath);
                if ($hp_title == '') {
                    $hp_title = basename($hpresults[$i][2]);
                }
                //$hp_date = api_convert_and_format_date($hpresults[$i][4], null, date_default_timezone_get());
                $hp_date = api_get_local_time($hpresults[$i][5], null, date_default_timezone_get());
                $hp_result = round(($hpresults[$i][3] / ($hpresults[$i][4] != 0 ? $hpresults[$i][4] : 1)) * 100, 2).'% ('.$hpresults[$i][3].' / '.$hpresults[$i][4].')';
                if ($is_allowedToEdit || $is_tutor) {                   
                    $list_info[] = array($hpresults[$i][0], $hpresults[$i][1], $hp_title, '-',  $hp_date , $hp_result , '-');
                } else {
                    $list_info[] = array($hp_title, '-', $hp_date , $hp_result , '-');
                }
            }
        }
    }
    return $list_info;
}


/**
 * Converts the score with the exercise_max_note and exercise_min_score the platform settings + formats the results using the float_format function
 * 
 * @param   float   score
 * @param   float   weight
 * @param   bool    show porcentage or not
 * @param	bool	use or not the platform settings
 * @return  string  an html with the score modified
 */
function show_score($score, $weight, $show_percentage = true, $use_platform_settings = true) {
    if (is_null($score) && is_null($weight)) {
        return '-';
    }
    $html  = '';
    $score_rounded = $score;     
    $max_note =  api_get_setting('exercise_max_score');
    $min_note =  api_get_setting('exercise_min_score');
    
    if ($use_platform_settings) {
        if ($max_note != '' && $min_note != '') {        
            if (!empty($weight) && intval($weight) != 0) {
    	       $score        = $min_note + ($max_note - $min_note) * $score /$weight;
            } else {
               $score        = $min_note;
            }            
            $weight         = $max_note;
        }
    }    
    $score_rounded = float_format($score, 1);    
    $weight = float_format($weight, 1);    
    if ($show_percentage) {        
        $parent = '(' . $score_rounded . ' / ' . $weight . ')';
        $html = float_format(($score / ($weight != 0 ? $weight : 1)) * 100, 1) . "%  $parent";	
    } else {    
    	$html = $score_rounded . ' / ' . $weight;
    }    
    return $html;	
}

/**
 * Converts a numeric value in a percentage example 0.66666 to 66.67 %
 * @param $value
 * @return float Converted number
 */
function convert_to_percentage($value) {
    $return = '-';
    if ($value != '') {
        $return = float_format($value * 100, 1).' %';
    }
    return $return;    
}

/**
 * Converts a score/weight values to the platform scale 
 * @param   float   score
 * @param   float   weight
 * @return  float   the score rounded converted to the new range
 */
function convert_score($score, $weight) {
    $max_note =  api_get_setting('exercise_max_score');
    $min_note =  api_get_setting('exercise_min_score');  
          
    if ($score != '' && $weight != '') {        
        if ($max_note != '' && $min_note != '') {           
           if (!empty($weight)) {          
               $score   = $min_note + ($max_note - $min_note) * $score / $weight;
           } else {
               $score   = $min_note;
           }                   
        }           
    }    
    $score_rounded  = float_format($score, 1);  
    return $score_rounded;
}

/**
 * Getting all active exercises from a course from a session (if a session_id is provided we will show all the exercises in the course + all exercises in the session)
 * @param   array   course data
 * @param   int     session id
 * @return  array   array with exercise data
 */
function get_all_exercises($course_info = null, $session_id = 0, $check_dates = false) {
	$TBL_EXERCICES              = Database :: get_course_table(TABLE_QUIZ_TEST);
	$course_id = api_get_course_int_id();
	
    if (!empty($course_info) && !empty($course_info['id'])) {
    	$course_id = $course_info['id'];
    }    
    
    if ($session_id == -1) {
    	$session_id  = 0;
    }
    if ($session_id == 0) {
    	$conditions = array('where'=>array('active = ? AND session_id = ? AND c_id = ? '=>array('1', $session_id, $course_id)), 'order'=>'title');
    } else {
        //All exercises
    	$conditions = array('where'=>array('active = ? AND  (session_id = 0 OR session_id = ? ) AND c_id = ? ' => array('1', $session_id, $course_id)), 'order'=>'title');
    }
    //var_dump($conditions);
    return Database::select('*',$TBL_EXERCICES, $conditions);
}


/**
 * Getting all active exercises from a course from a session (if a session_id is provided we will show all the exercises in the course + all exercises in the session)
 * @param   array   course data
 * @param   int     session id
 * @param		int			course c_id
 * @return  array   array with exercise data
 * modified by Hubert Borderiou 
 */
function get_all_exercises_for_course_id($course_info = null, $session_id = 0, $course_id=0) {
   	$TBL_EXERCICES = Database :: get_course_table(TABLE_QUIZ_TEST);
    if ($session_id == -1) {
    	$session_id  = 0;
    }
    if ($session_id == 0) {
    	$conditions = array('where'=>array('active = ? AND session_id = ? AND c_id = ?'=>array('1', $session_id, $course_id)), 'order'=>'title');
    } else {
        //All exercises
    	$conditions = array('where'=>array('active = ? AND (session_id = 0 OR session_id = ? ) AND c_id=?' =>array('1', $session_id, $course_id)), 'order'=>'title');
    }
    return Database::select('*',$TBL_EXERCICES, $conditions);
}

/**
 * Gets the position of the score based in a given score (result/weight) and the exe_id based in the user list
 * (NO Exercises in LPs )
 * @param   float   user score to be compared *attention* $my_score = score/weight and not just the score
 * @param   int     exe id of the exercise (this is necesary because if 2 students have the same score the one with the minor exe_id will have a best position, just to be fair and FIFO)
 * @param   int     exercise id
 * @param   string  course code
 * @param   int     session id
 * @return  int     the position of the user between his friends in a course (or course within a session)
 */
function get_exercise_result_ranking($my_score, $my_exe_id, $exercise_id, $course_code, $session_id = 0, $user_list, $return_string = true) { 
    //No score given we return 
    if (is_null($my_score)) {
        return '-';
    }   
    if (empty($user_list)) {
        return '-';
    }
    $best_attempts = array(); 
   
    foreach($user_list as $user_data) {
        $user_id = $user_data['user_id'];        
        $best_attempts[$user_id]= get_best_attempt_by_user($user_id, $exercise_id, $course_code, $session_id);   
    }

    $position_data = array();
    if (empty($best_attempts)) {
    	return 1;
    } else {
        $position = 1; 
        $my_ranking = array();
        foreach($best_attempts as $user_id => $result) {            
            if (!empty($result['exe_weighting']) && intval($result['exe_weighting']) != 0) {
                $my_ranking[$user_id] = $result['exe_result']/$result['exe_weighting'];
            } else {
                $my_ranking[$user_id] = 0;
            }         
        }         
        //if (!empty($my_ranking)) { 
            asort($my_ranking);
            $position = count($my_ranking);
            if (!empty($my_ranking)) {        
                foreach($my_ranking as $user_id => $ranking) {
                	if ($my_score >= $ranking) {
                        if ($my_score == $ranking) {
                            $exe_id = $best_attempts[$user_id]['exe_id'];
                            if ($my_exe_id < $exe_id) {
                                $position--;
                            }
                        } else {           
                		  $position--;                    
                        }
                	}
                }        
            }
        //}
        $return_value = array('position'=>$position, 'count'=>count($my_ranking)); 
        //var_dump($my_score, $my_ranking);
        if ($return_string) {
            if (!empty($position) && !empty($my_ranking)) {
               $return_value = $position.'/'.count($my_ranking); 
            } else {
                $return_value = '-';
            }
        }
        return $return_value;    
    }
}

/**
 * Gets the position of the score based in a given score (result/weight) and the exe_id based in all attempts
 * (NO Exercises in LPs ) old funcionality by attempt
 * @param   float   user score to be compared attention => score/weight
 * @param   int     exe id of the exercise (this is necesary because if 2 students have the same score the one with the minor exe_id will have a best position, just to be fair and FIFO)
 * @param   int     exercise id
 * @param   string  course code
 * @param   int     session id
 * @return  int     the position of the user between his friends in a course (or course within a session)
 */
function get_exercise_result_ranking_by_attempt($my_score, $my_exe_id, $exercise_id, $course_code, $session_id = 0, $return_string = true) {
    if (empty($session_id)) {
    	$session_id = 0;
    }
    if (is_null($my_score)) {
        return '-';
    }    
    $user_results = get_all_exercise_results($exercise_id, $course_code, $session_id, false);
    $position_data = array();
    if (empty($user_results)) {
    	return 1;
    } else {
        $position = 1; 
        $my_ranking = array();
        foreach($user_results as $result) {
            //print_r($result);
            if (!empty($result['exe_weighting']) && intval($result['exe_weighting']) != 0) {
                $my_ranking[$result['exe_id']] = $result['exe_result']/$result['exe_weighting'];
            } else {
                $my_ranking[$result['exe_id']] = 0;
            }         
        }        
        asort($my_ranking);
        $position = count($my_ranking);
        if (!empty($my_ranking)) {        
            foreach($my_ranking as $exe_id=>$ranking) {
            	if ($my_score >= $ranking) {
                    if ($my_score == $ranking) {
                        if ($my_exe_id < $exe_id) {
                            $position--;
                        }
                    } else {           
            		  $position--;                    
                    }
            	}
            }        
        }
        $return_value = array('position'=>$position, 'count'=>count($my_ranking)); 
        //var_dump($my_score, $my_ranking);
        if ($return_string) {
            if (!empty($position) && !empty($my_ranking)) {
               return $position.'/'.count($my_ranking); 
            }
        }
        return $return_value;    
    }
}


/*
 *  Get the best attempt in a exercise (NO Exercises in LPs )
 */

function get_best_attempt_in_course($exercise_id, $course_code, $session_id) { 
    $user_results = get_all_exercise_results($exercise_id, $course_code, $session_id, false);
    $best_score_data = array();
    $best_score = 0;
    if (!empty($user_results)) {
        foreach($user_results as $result) {
            if (!empty($result['exe_weighting']) && intval($result['exe_weighting']) != 0) {
                $score = $result['exe_result']/$result['exe_weighting'];
                if ($score >= $best_score) {
                    $best_score = $score;
                    $best_score_data = $result;
                }
            }
        }
    }
    return $best_score_data;
}

/*
 *  Get the best score in a exercise (NO Exercises in LPs )
 */
function get_best_attempt_by_user($user_id, $exercise_id, $course_code, $session_id) { 
    $user_results = get_all_exercise_results($exercise_id, $course_code, $session_id, false);
    $best_score_data = array();
    $best_score = 0;
    if (!empty($user_results)) {       
        foreach($user_results as $result) {
            if ($result['exe_user_id'] != $user_id) continue;
            if (!empty($result['exe_weighting']) && intval($result['exe_weighting']) != 0) {
                $score = $result['exe_result']/$result['exe_weighting'];                
                if ($score >= $best_score) {
                    $best_score = $score;
                    $best_score_data = $result;
                }
            }
        }
    }    
    return $best_score_data;
}




/**
 * Get average score (NO Exercises in LPs )
 * @param 	int	exercise id
 * @param 	string	course code
 * @param 	int	session id
 * @return 	float	Average score
 */
function get_average_score($exercise_id, $course_code, $session_id) { 
    $user_results = get_all_exercise_results($exercise_id, $course_code, $session_id);
    $avg_score_data = array();    
    $avg_score = 0;
    if (!empty($user_results)) {        
        foreach($user_results as $result) {
            if (!empty($result['exe_weighting']) && intval($result['exe_weighting']) != 0) {
                $score = $result['exe_result']/$result['exe_weighting'];
                $avg_score +=$score;                
            }
        }
        $avg_score = float_format($avg_score / count($user_results), 1);
    }
    return $avg_score;
}

/**
 * Get average score by score (NO Exercises in LPs )
 * @param 	int	exercise id
 * @param 	string	course code
 * @param 	int	session id
 * @return 	float	Average score
 */
function get_average_score_by_course($course_code, $session_id) { 
    $user_results = get_all_exercise_results_by_course($course_code, $session_id, false);
    //echo $course_code.' - '.$session_id.'<br />';
    $avg_score = 0;    
    if (!empty($user_results)) {        
        foreach($user_results as $result) {
            if (!empty($result['exe_weighting']) && intval($result['exe_weighting']) != 0) { 
                $score = $result['exe_result']/$result['exe_weighting'];
                //var_dump($score);
                $avg_score +=$score;                               
            }
        }
        //We asume that all exe_weighting
        //$avg_score = show_score( $avg_score / count($user_results) , $result['exe_weighting']);
        $avg_score = ($avg_score / count($user_results));
    }   
    //var_dump($avg_score); 
    return $avg_score;
}

function get_average_score_by_course_by_user($user_id, $course_code, $session_id) {
	$user_results = get_all_exercise_results_by_user($user_id, $course_code, $session_id);
	$avg_score = 0;
	if (!empty($user_results)) {
		foreach($user_results as $result) {
			if (!empty($result['exe_weighting']) && intval($result['exe_weighting']) != 0) {
				$score = $result['exe_result']/$result['exe_weighting'];
				$avg_score +=$score;
			}
		}
		//We asume that all exe_weighting
		//$avg_score = show_score( $avg_score / count($user_results) , $result['exe_weighting']);
		$avg_score = ($avg_score / count($user_results));
	}
	return $avg_score;
}


/**
 * Get average score by score (NO Exercises in LPs )
 * @param 	int		exercise id
 * @param 	string	course code
 * @param 	int		session id
 * @return	float	Best average score
 */
function get_best_average_score_by_exercise($exercise_id, $course_code, $session_id, $user_count) { 
    $user_results = get_best_exercise_results_by_user($exercise_id, $course_code, $session_id);
    $avg_score = 0;
    if (!empty($user_results)) {        
        foreach($user_results as $result) {
            if (!empty($result['exe_weighting']) && intval($result['exe_weighting']) != 0) { 
                $score = $result['exe_result']/$result['exe_weighting'];
                $avg_score +=$score;                
            }
        }
        //We asume that all exe_weighting
        //$avg_score = show_score( $avg_score / count($user_results) , $result['exe_weighting']);
        //$avg_score = ($avg_score / count($user_results));
        if(!empty($user_count)) {
            $avg_score = float_format($avg_score / $user_count, 1) * 100;
        } else {
            $avg_score = 0;
        }
    }
    return $avg_score;
}



function get_exercises_to_be_taken($course_code, $session_id) {
	$course_info = api_get_course_info($course_code);
	$exercises = get_all_exercises($course_info, $session_id);
	$result = array();
	$now = time() + 15*24*60*60;
	foreach($exercises as $exercise_item) {
		if (isset($exercise_item['end_time'])  && !empty($exercise_item['end_time']) && $exercise_item['end_time'] != '0000-00-00 00:00:00' && api_strtotime($exercise_item['end_time']) < $now) {
			$result[] = $exercise_item;
		}
	}
	return $result;
}

/**
 * Get student results (only in completed exercises) stats by question
 * @param 	int		question id
 * @param 	int		exercise id
 * @param 	string	course code
 * @param 	int		session id
 *  
 * */
function get_student_stats_by_question($question_id,  $exercise_id, $course_code, $session_id) {
	$track_exercises	= Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
	$track_attempt		= Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
	
	$question_id 		= intval($question_id);
	$exercise_id 		= intval($exercise_id);
	$course_code 		= Database::escape_string($course_code);
	$session_id 		= intval($session_id);
	 
	$sql = "SELECT count(exe_user_id) as users, MAX(marks) as max , MIN(marks) as min, AVG(marks) as average 
			FROM $track_exercises e INNER JOIN $track_attempt a ON (a.exe_id = e.exe_id)
			WHERE 	exe_exo_id 		= $exercise_id AND 
					course_code 	= '$course_code' AND 
					e.session_id 	= $session_id AND
					question_id 	= $question_id AND status = '' LIMIT 1";	
	$result = Database::query($sql);	
	$return = array();
	if ($result) {
		$return = Database::fetch_array($result, 'ASSOC');	
			
	}
	return $return; 	
}



