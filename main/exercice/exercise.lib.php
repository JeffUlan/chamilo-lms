<?php
/* For licensing terms, see /license.txt */

/**
 *	Exercise library
 * 	shows a question and its answers
 *	@package dokeos.exercise
 * 	@author Olivier Brouckaert <oli.brouckaert@skynet.be>
 * 	@version $Id: exercise.lib.php 22247 2009-07-20 15:57:25Z ivantcholakov $
 */

// The initialization class for the online editor is needed here.
require_once dirname(__FILE__).'/../inc/lib/fckeditor/fckeditor.php';

/**
 * Shows a question
 * 
 * @param int   question id
 * @param bool  only answers
 * @param bool  origin i.e = learnpath
 * @param int   current item from the list of questions
 * @param int   number of total questions
 * */
function showQuestion($questionId, $onlyAnswers = false, $origin = false, $current_item = '', $show_title = true, $freeze = false) {

	// Text direction for the current language
	$is_ltr_text_direction = api_get_text_direction() != 'rtl';

	// Change false to true in the following line to enable answer hinting.
	$debug_mark_answer = api_is_allowed_to_edit() && false;

	// Reads question informations.
	if (!$objQuestionTmp = Question::read($questionId)) {
		// question not found
		return false;
	}
    
	$answerType    = $objQuestionTmp->selectType();
	$pictureName   = $objQuestionTmp->selectPicture();

	if ($answerType != HOT_SPOT) {
		// Question is not of type hotspot
		if (!$onlyAnswers) {
			$questionName=$objQuestionTmp->selectTitle();
			$questionDescription=$objQuestionTmp->selectDescription();

			$questionName=text_filter($questionName);
            
            if ($show_title) {
    			$s='<div id="question_title" class="sectiontitle">'.get_lang('Question').' ';
    			$s.=$current_item;
    			//@todo I need the get the feedback type
    			//if($answerType != 1)
    			//$s.=' / '.$total_item;
    			echo $s;
    			echo ' : ';
    			echo $questionName.'</div>';
            }
            
			$s='';
			$s.='<table class="exercise_questions" style="margin:4px 4px 4px 0px; padding:2px;">
				<tr><td valign="top" colspan="2">';
			$questionDescription=text_filter($questionDescription);
			$s.=$questionDescription;
			$s.='</td></tr></table>';

			if (!empty($pictureName)) {
				$s.="<tr>
				        <td align='center' colspan='2'><img src='../document/download.php?doc_url=%2Fimages%2F'".$pictureName."' border='0'></td>
				    </tr>";
			}
		}
		$s.= '</table>';
        
        $s .= '<div class="rounded exercise_questions" style="width: 720px; padding: 3px;">';
        $option_ie = '';
        /*
		if (!ereg("MSIE",$_SERVER["HTTP_USER_AGENT"])) {
			$s .= '<div class="rounded exercise_questions" style="width: 720px; padding: 3px;">';
		} else {
			$option_ie="margin-left:10px";
		}*/
        
        if ($answerType == FREE_ANSWER && $freeze) {
            return '';
        }
		$s .= '<table width="720" class="exercise_options" style="width: 720px;'.$option_ie.' background-color:#fff;">';
		// construction of the Answer object (also gets all answers details)
		$objAnswerTmp=new Answer($questionId);
		$nbrAnswers=$objAnswerTmp->selectNbrAnswers();

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

			for ($answerId=1;$answerId <= $nbrAnswers;$answerId++) {
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
			foreach ($answer_matching as $id => $value) {
				$select_items[$i]['id'] =  $value['id'];
				$select_items[$i]['letter'] =  $cpt1[$id];
				$select_items[$i]['answer'] = $value['answer'];
				$i ++;
			}
			$num_suggestions = ($nbrAnswers - $x) + 1;
		} elseif ($answerType == FREE_ANSWER) {
			$oFCKeditor = new FCKeditor("choice[".$questionId."]") ;
			$oFCKeditor->ToolbarSet = 'TestFreeAnswer';
			$oFCKeditor->Width  = '100%';
			$oFCKeditor->Height = '200';
			$oFCKeditor->Value	= '' ;
            $s .= '<tr><td colspan="3">';
            $s .= $oFCKeditor->CreateHtml();
            $s .= '</td></tr>';
		}
        
        
      ?>  
        
    <style>
    
   
   #questions {
    width:40%; 
    height:50px;
    float:left;  
    padding:5px;  
   }
   
   #options {
    width:40%;
    float:left;
    padding:5px;  
          
   }
   
   .question_item {   
      height:50px;
      padding:5px;
      margin:10px 0px 10px 0px;	
   }
   
   .option_item {
    width:150px;
    padding:3px;
    margin:10px;      
   }
   
   
    
    </style>
    <script>
    $(function() {
        
         var $options = $( "#options" );
        
        $( "div", $options ).draggable({            
            revert: "invalid", // when not dropped, the item will revert back to its initial position                        
            cursor: "move",            
        });
        
        
        var $question_1 = $( "#question_1" );        
        $question_1.droppable({        
            accept: "#options div",            
                  
            activeClass: "ui-state-hover",
            hoverClass: "ui-state-active",
                  
            drop: function( event, ui ) {
                //$( this ).addClass( "ui-state-highlight" );  
                              
            }
        });
        
        
        var $question_2 = $( "#question_2" );        
        $question_2.droppable({        
            accept: "#options div",            
                hoverClass: "ui-state-active",
            drop: function( event, ui ) {
                //$( this ).addClass( "ui-state-highlight" );
            }
        });        
        
        $options.droppable({        
            accept: "#options div",
            hoverClass: "ui-state-active",
            drop: function( event, ui ) {                
            }
        });

    });
    </script>
    
    <?php
		// Now navigate through the possible answers, using the max number of
		// answers for the question as a limiter
		$lines_count=1; // a counter for matching-type answers
        $question_list = array();
		for ($answerId=1;$answerId <= $nbrAnswers;$answerId++) {
			$answer          = $objAnswerTmp->selectAnswer($answerId);
			$answerCorrect   = $objAnswerTmp->isCorrect($answerId);
			$numAnswer       = $objAnswerTmp->selectAutoId($answerId);

			if ($answerType == FILL_IN_BLANKS) {
				// splits text and weightings that are joined with the character '::'
				list($answer) = explode('::',$answer);

				// because [] is parsed here we follow this procedure:
				$answer = text_filter($answer);
				//getting the matches
				$answer = api_ereg_replace('\[[^]]+\]','<input type="text" name="choice['.$questionId.'][]" size="10" />',($answer));
			}

			// Unique answer
			if ($answerType == UNIQUE_ANSWER || $answerType == UNIQUE_ANSWER_NO_OPTION) {
				// set $debug_mark_answer to true at function start to
				// show the correct answer with a suffix '-x'
				$help = $selected = '';
				if ($debug_mark_answer) {
					if ($answerCorrect) {
						$help = 'x-';
						$selected = 'checked="checked"';
					}
				}
				$answer = text_filter($answer);
				$answer = Security::remove_XSS($answer, STUDENT);
				$s .= '<input type="hidden" name="choice2['.$questionId.']" value="0" />'.
					'<tr><td colspan="3"><div class="u-m-answer"><p style="float: '.($is_ltr_text_direction ? 'left' : 'right').'; padding-'.($is_ltr_text_direction ? 'right' : 'left').': 4px;">'.
					'<span><input class="checkbox" type="radio" name="choice['.$questionId.']" value="'.$numAnswer.'" '.$selected.' /></span></p>'.
					'<div style="margin-'.($is_ltr_text_direction ? 'left' : 'right').': 24px;">'.
					$answer.
					'</div></div></td></tr>';

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
                
				$answer = text_filter($answer);
				$answer = Security::remove_XSS($answer, STUDENT);
				$s .= '<input type="hidden" name="choice2['.$questionId.']" value="0" />'.
					'<tr><td colspan="3"><div class="u-m-answer"><p style="float: '.($is_ltr_text_direction ? 'left' : 'right').'; padding-'.($is_ltr_text_direction ? 'right' : 'left').': 4px;">'.
					'<span><input class="checkbox" type="checkbox" name="choice['.$questionId.']['.$numAnswer.']" value="1" '.$selected.' /></span></p>'.
					'<div style="margin-'.($is_ltr_text_direction ? 'left' : 'right').': 24px;">'.
					$answer.
					'</div></div></td></tr>';
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
				$answer = text_filter($answer);
				$answer = Security::remove_XSS($answer, STUDENT);
				$s .= '<input type="hidden" name="choice2['.$questionId.']" value="0" />'.
					'<tr><td colspan="3"><div class="u-m-answer"><p style="float: '.($is_ltr_text_direction ? 'left' : 'right').'; padding-'.($is_ltr_text_direction ? 'right' : 'left').': 4px;">'.
					'<span><input class="checkbox" type="checkbox" name="choice['.$questionId.']['.$numAnswer.']" value="1" '.$selected.' /></span></p>'.
					'<div style="margin-'.($is_ltr_text_direction ? 'left' : 'right').': 24px;">'.
					$answer.
					'</div></div></td></tr>';

			} elseif ($answerType == FILL_IN_BLANKS) {
				// fill in blanks
				$s .= '<tr><td colspan="3">'.$answer.'</td></tr>';
			} else {
				//  matching type, showing suggestions and answers
				// TODO: replace $answerId by $numAnswer
				if ($answerCorrect != 0) {
					// only show elements to be answered (not the contents of
					// the select boxes, who are corrrect = 0)
					$s .= '<tr><td width="45%" valign="top" >';
					$parsed_answer = text_filter($answer);
                    $question_list[] = $parsed_answer;
					//left part questions
					$s .= ' <span style="float:left; width:8%;"><b>'.$lines_count.'</b>.&nbsp;</span>
						 	<span style="float:left; width:92%;">'.$parsed_answer.'</span></td>';
					//middle part (matches selects)
					$s .= '<td width="10%" valign="top" align="center">&nbsp;&nbsp;
				            <select name="choice['.$questionId.']['.$numAnswer.']">
							  <option value="0">--</option>';
					// fills the list-box
					foreach ($select_items as $key=>$val) {
						// set $debug_mark_answer to true at function start to
						// show the correct answer with a suffix '-x'
						$help = $selected = '';
						if ($debug_mark_answer) {
							if ($val['id'] == $answerCorrect) {
								$help = '-x';
								$selected = 'selected="selected"';
							}
						}
						$s.='<option value="'.$val['id'].'" '.$selected.'>'.$val['letter'].$help.'</option>';
					}  // end foreach()

					$s .= '</select>&nbsp;&nbsp;</td>';
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
								  <td colspan="2">&nbsp;</td>
								  <td valign="top">';
							$s.='<b>'.$select_items[$lines_count]['letter'].'.</b> '.$select_items[$lines_count]['answer'];
							$s.="</td>
							</tr>";
							$lines_count++;
						}	// end while()
					}  // end if()
				}
			}
		}	// end for()
        
        //Adding divs for the new MATCHING interface
        
        if ($answerType == MATCHING && !$freeze) {
            echo '<div id="questions">';
            echo Display::tag('h2','Questions'); 
            $i = 1;
            foreach ($question_list as $key=>$val) {
                echo Display::tag('div', Display::tag('p',$val), array('id'=>'question_'.$i, 'class'=>'question_item  ui-widget-header'));
                $i++;            
            }
            echo '</div>';
            
             
             echo Display::tag('h2','Options');
            echo '<div id="options" class=" ui-widget-header">';        
            foreach ($select_items as $key=>$val) {
                echo Display::tag('div', Display::tag('p',$val['answer']), array('id'=>'option_'.$i, 'class'=>'option_item ui-widget-content'));                        
            }
            echo '</ul>';
        }
		
			$s .= '</table>';
		
		$s .= '</div><br />';

		// destruction of the Answer object
		unset($objAnswerTmp);

		// destruction of the Question object
		unset($objQuestionTmp);

		if ($origin != 'export') {
			echo $s;
		} else {
			return($s);
		}
	} elseif ($answerType == HOT_SPOT) {

		// Question is of type HOT_SPOT
        
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
            exit;
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

		if (!$onlyAnswers) {
            if ($show_title) {
                echo '<div id="question_title" class="sectiontitle">'.get_lang('Question').' '.$current_item.' : '.$questionName.'</div>';
            }
			//@todo I need to the get the feedback type
			//if($answerType == 2)
			//	$s.=' / '.$total_item;
			echo '<input type="hidden" name="hidden_hotspot_id" value="'.$questionId.'" />';
			echo '<table class="exercise_questions" >
				  <tr>
			  		<td valign="top" colspan="2">';
			echo $questionDescription=text_filter($questionDescription);
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
						    var oeTags = \'<object type="application/x-shockwave-flash" data="../plugin/hotspot/hotspot_user.swf?modifyAnswers='.$questionId.'&amp;canClick:'.$canClick.'" width="560" height="436">\'
										+ \'<param name="movie" value="../plugin/hotspot/hotspot_user.swf?modifyAnswers='.$questionId.'&amp;canClick:'.$canClick.'" />\'
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
	echo '</table><br />';
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
 */
function get_count_exam_results($exercise_id = null) {
 global $is_allowedToEdit, $is_tutor,$_cid,$_user,$TBL_USER, $TBL_EXERCICES,$TBL_TRACK_EXERCICES, $TBL_TRACK_ATTEMPT_RECORDING,$filter_by_not_revised,$filter_by_revised,$documentPath;
    $session_id_and = ' AND te.session_id = ' . api_get_session_id() . ' ';
    if ($is_allowedToEdit || $is_tutor) {
        $user_id_and = '';
        if (!empty ($_POST['filter_by_user'])) {
            if ($_POST['filter_by_user'] == 'all') {
                $user_id_and = " AND user_id like '%'";
            } else {
                $user_id_and = " AND user_id = '" . Database :: escape_string((int) $_POST['filter_by_user']) . "' ";
            }
        }
        if ($_GET['gradebook'] == 'view') {
            $exercise_where_query = 'te.exe_exo_id =ce.id AND ';
        }       
                
        $exercise_where = '';
        if (isset($_GET['exerciseId'])) {
            $exercise_where = ' AND te.exe_exo_id = '.intval($_GET['exerciseId']).'  ';
        }
        
        if (!empty($exercise_id)) {
            $exercise_where = ' AND te.exe_exo_id = '.intval($exercise_id).'  ';
        }

        //@todo fix to work with COURSE_RELATION_TYPE_RRHH in both queries

        /*$sql="SELECT ".(api_is_western_name_order() ? "firstname as userpart1, lastname col1" : "lastname as userpart1, firstname as col1").", ce.title as extitle, te.exe_result as exresult ,
                             te.exe_weighting as exweight, te.exe_date as exdate, te.exe_id as exid, email as exemail, te.start_date as exstart, steps_counter as exstep,cuser.user_id as excruid,te.exe_duration as exduration
                      FROM $TBL_EXERCICES AS ce , $TBL_TRACK_EXERCICES AS te, $TBL_USER AS user,$tbl_course_rel_user AS cuser
                      WHERE  user.user_id=cuser.user_id AND cuser.relation_type<>".COURSE_RELATION_TYPE_RRHH." AND te.exe_exo_id = ce.id AND te.status != 'incomplete' AND cuser.user_id=te.exe_user_id AND te.exe_cours_id='" . Database :: escape_string($_cid) . "'
                      $user_id_and $session_id_and AND ce.active <>-1 AND orig_lp_id = 0 AND orig_lp_item_id = 0
                      AND cuser.course_code=te.exe_cours_id ORDER BY col1, te.exe_cours_id ASC, ce.title ASC, te.exe_date DESC";*/

        $sql="SELECT count(*)  as count
                FROM $TBL_EXERCICES  AS ce INNER JOIN $TBL_TRACK_EXERCICES AS te ON (te.exe_exo_id = ce.id) INNER JOIN  $TBL_USER  AS user ON (user.user_id = exe_user_id)
                WHERE te.status != 'incomplete' AND te.exe_cours_id='" . Database :: escape_string($_cid) . "'  $user_id_and  $session_id_and AND ce.active <>-1 AND orig_lp_id = 0 AND orig_lp_item_id = 0 $exercise_where ";

        $hpsql="SELECT ".(api_is_western_name_order() ? "firstname as col0, lastname col1" : "lastname as col0, firstname as col1").", tth.exe_name, tth.exe_result , tth.exe_weighting, tth.exe_date
                    FROM $TBL_TRACK_HOTPOTATOES tth, $TBL_USER tu
                    WHERE  tu.user_id=tth.exe_user_id AND tth.exe_cours_id = '" . Database :: escape_string($_cid) . " $user_id_and  $exercise_where
                    ORDER BY tth.exe_cours_id ASC, tth.exe_date DESC";



    } else {
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

      $sql="SELECT count(*) as count
            FROM $TBL_EXERCICES  AS ce INNER JOIN $TBL_TRACK_EXERCICES AS te ON (te.exe_exo_id = ce.id) INNER JOIN  $TBL_USER  AS user ON (user.user_id = exe_user_id)
            WHERE te.status != 'incomplete' AND te.exe_cours_id='" . Database :: escape_string($_cid) . "'  $user_id_and $session_id_and AND ce.active <>-1 AND" .
            " orig_lp_id = 0 AND orig_lp_item_id = 0 ";

        $hpsql = "SELECT '',exe_name, exe_result , exe_weighting, exe_date
                        FROM $TBL_TRACK_HOTPOTATOES
                        WHERE exe_user_id = '" . $_user['user_id'] . "' AND exe_cours_id = '" . Database :: escape_string($_cid) . "'
                        ORDER BY exe_cours_id ASC, exe_date DESC";
    }

    $resx = Database::query($sql);
    $rowx = Database::fetch_array($resx,'ASSOC');
    return $rowx['count'];
}




/**
 * Gets the exam'data results
 * @todo this function should be moved in a library  + no global calls 
 */
function get_exam_results_data($from, $number_of_items, $column, $direction) {

    global $is_allowedToEdit, $is_tutor,$_cid,$_user,$TBL_USER, $TBL_EXERCICES,$TBL_TRACK_EXERCICES, $TBL_TRACK_ATTEMPT_RECORDING,$filter_by_not_revised,$filter_by_revised,$documentPath,$filter;
    $session_id_and = ' AND te.session_id = ' . api_get_session_id() . ' ';
    if ($is_allowedToEdit || $is_tutor) {
        $user_id_and = '';
        if (!empty ($_POST['filter_by_user'])) {
            if ($_POST['filter_by_user'] == 'all') {
                $user_id_and = " AND user_id like '%'";
            } else {
                $user_id_and = " AND user_id = '" . Database :: escape_string((int) $_POST['filter_by_user']) . "' ";
            }
        }
        
        if ($_GET['gradebook'] == 'view') {
            $exercise_where_query = ' te.exe_exo_id =ce.id AND ';
        }
        
        $exercise_where = '';
        if (isset($_GET['exerciseId'])) {
            $exercise_where .= ' AND te.exe_exo_id = '.intval($_GET['exerciseId']).'  ';
        }
        

        //@todo fix to work with COURSE_RELATION_TYPE_RRHH in both queries

        /*$sql="SELECT ".(api_is_western_name_order() ? "firstname as col0, lastname col1" : "lastname as col0, firstname as col1").", ce.title as extitle, te.exe_result as exresult ,
                             te.exe_weighting as exweight, te.exe_date as exdate, te.exe_id as exid, email as exemail, te.start_date as exstart, steps_counter as exstep,cuser.user_id as excruid,te.exe_duration as exduration
                      FROM $TBL_EXERCICES AS ce , $TBL_TRACK_EXERCICES AS te, $TBL_USER AS user,$tbl_course_rel_user AS cuser
                      WHERE  user.user_id=cuser.user_id AND cuser.relation_type<>".COURSE_RELATION_TYPE_RRHH." AND te.exe_exo_id = ce.id AND te.status != 'incomplete' AND cuser.user_id=te.exe_user_id AND te.exe_cours_id='" . Database :: escape_string($_cid) . "'
                      $user_id_and $session_id_and AND ce.active <>-1 AND orig_lp_id = 0 AND orig_lp_item_id = 0
                      AND cuser.course_code=te.exe_cours_id ORDER BY col1, te.exe_cours_id ASC, ce.title ASC, te.exe_date DESC";*/

        $sql="SELECT ".(api_is_western_name_order() ? "firstname as col0, lastname col1" : "lastname as col0, firstname as col1").", ce.title as col2, te.exe_result as exresult , te.exe_weighting as exweight,
                te.exe_date as exdate, te.exe_id as exid, email as exemail, te.start_date as col4, steps_counter as exstep, exe_user_id as excruid,te.exe_duration as exduration
                FROM $TBL_EXERCICES  AS ce INNER JOIN $TBL_TRACK_EXERCICES AS te ON (te.exe_exo_id = ce.id) INNER JOIN  $TBL_USER  AS user ON (user.user_id = exe_user_id)
                WHERE te.status != 'incomplete' AND te.exe_cours_id='" . Database :: escape_string($_cid) . "'  $user_id_and  $session_id_and AND ce.active <>-1 AND orig_lp_id = 0 AND orig_lp_item_id = 0 $exercise_where ";

        $hpsql="SELECT ".(api_is_western_name_order() ? "firstname as col0, lastname col1" : "lastname as col0, firstname as col1").", tth.exe_name, tth.exe_result , tth.exe_weighting, tth.exe_date
                    FROM $TBL_TRACK_HOTPOTATOES tth, $TBL_USER tu
                    WHERE  tu.user_id=tth.exe_user_id AND tth.exe_cours_id = '" . Database :: escape_string($_cid)." $user_id_and $exercise_where 
                    ORDER BY tth.exe_cours_id ASC, tth.exe_date DESC";



    } else {
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



        $sql="SELECT ".(api_is_western_name_order() ? "firstname as col0, lastname col1" : "lastname as col0, firstname as col1")." , ce.title as col2, te.exe_result as exresult, " .
                            "te.exe_weighting as exweight, te.exe_date as exdate, te.exe_id as exid, email as exemail, " .
                            "te.start_date as col4, steps_counter as exstep, exe_user_id as excruid, te.exe_duration as exduration, ce.results_disabled as exdisabled
                    FROM $TBL_EXERCICES  AS ce INNER JOIN $TBL_TRACK_EXERCICES AS te ON (te.exe_exo_id = ce.id) INNER JOIN  $TBL_USER  AS user ON (user.user_id = exe_user_id)
                    WHERE te.status != 'incomplete' AND te.exe_cours_id='" . Database :: escape_string($_cid) . "'  $user_id_and $session_id_and AND ce.active <>-1 AND" .
                    " orig_lp_id = 0 AND orig_lp_item_id = 0 ";

        $hpsql = "SELECT '',exe_name, exe_result , exe_weighting, exe_date
                        FROM $TBL_TRACK_HOTPOTATOES
                        WHERE exe_user_id = '" . $_user['user_id'] . "' AND exe_cours_id = '" . Database :: escape_string($_cid) . "'
                        ORDER BY exe_cours_id ASC, exe_date DESC";
    }


    $column = intval($column);
    $from = intval($from);
    $number_of_items = intval($number_of_items);
    $sql .= " ORDER BY col$column $direction ";
    $sql .= " LIMIT $from,$number_of_items";

    $results = array();

    $resx = Database::query($sql);
    while ($rowx = Database::fetch_array($resx,'ASSOC')) {
        $results[] = $rowx;
    }
    $hpresults = getManyResultsXCol($hpsql, 5);

    $has_test_results = false;
    $list_info = array();

    // Print test results.
    $lang_nostartdate = get_lang('NoStartDate') . ' / ';
    
    
    if (is_array($results)) {
        $has_test_results = true;
        $users_array_id = array ();
        if ($_GET['gradebook'] == 'view') {
            $filter_by_no_revised = true;
            $from_gradebook = true;
        }
        $sizeof = sizeof($results);

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
            $sql_exe = 'SELECT exe_id FROM ' . $TBL_TRACK_ATTEMPT_RECORDING . '
                        WHERE author != ' . "''" . ' AND exe_id = ' . "'" . Database :: escape_string($results[$i]['exid']) . "'" . ' LIMIT 1';
            $query = Database::query($sql_exe);

            if (Database :: num_rows($query) > 0) {
                $revised = true;
            }
            if ($filter_by_not_revised && $revised) {
                continue;
            }
            if ($filter_by_revised && !$revised) {
                continue;
            }
            if ($from_gradebook && ($is_allowedToEdit || $is_tutor)) {
                if (in_array($results[$i]['col2'] . $results[$i]['col0'] . $results[$i]['col1'], $users_array_id)) {
                    continue;
                }
                $users_array_id[] = $results[$i]['col2'] . $results[$i]['col0'] . $results[$i]['col1'];
            }

            $user_first_name = $results[$i]['col0'];
            $user_last_name = $results[$i]['col1'];
            $user_list_id[] = $results[$i]['excruid'];
            $id = $results[$i]['exid'];   

            $user = $results[$i]['col0'] . $results[$i]['col1'];
            $test = $results[$i]['col2'];
            $quiz_name_list = $test;
            $dt = api_convert_and_format_date($results[$i]['exweight'], null, date_default_timezone_get());
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
                }
                if ($results[$i]['col4'] != "0000-00-00 00:00:00") {
                    //echo ceil((($results[$i][4] - $results[$i][7]) / 60)) . ' ' . get_lang('MinMinutes');
                    $exe_date_timestamp     = api_strtotime($results[$i]['exdate'], date_default_timezone_get());
                    $start_date_timestamp   = api_strtotime($results[$i]['col4'], date_default_timezone_get());

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
                    $add_start_date = api_convert_and_format_date($results[$i]['col4'], null, date_default_timezone_get()) . ' / ';
                } else {
                    $duration_list = get_lang('NoLogOfDuration');
                    //echo get_lang('NoLogOfDuration');
                }
                // Date conversion
                $date_list = api_get_local_time($results[$i]['col4']). ' / ' . api_get_local_time($results[$i]['exdate']);
                // there are already a duration test period calculated??
                //echo '<td>'.sprintf(get_lang('DurationFormat'), $duration).'</td>';

                // if the float look like 10.00 we show only 10

                $my_res     = float_format($results[$i]['exresult'],1);
                $my_total   = float_format($results[$i]['exweight'],1);

                $result_list = round(($my_res / ($my_total != 0 ? $my_total : 1)) * 100, 2) . '% (' . $my_res . ' / ' . $my_total . ')';

                $html_link = '';
                if ($is_allowedToEdit || $is_tutor) {
                    if ($revised) {
                        $html_link.= "<a href='exercise_show.php?".api_get_cidreq()."&action=edit&id=$id'>".Display :: return_icon('edit.gif', get_lang('Edit'));
                        $html_link.= '&nbsp;';
                    } else {
                        $html_link.="<a href='exercise_show.php?".api_get_cidreq()."&action=qualify&id=$id'>".Display :: return_icon('quizz_small.gif', get_lang('Qualify'));
                        $html_link.='&nbsp;';
                    }
                    $html_link.="</a>";
                    if (api_is_platform_admin() || $is_tutor) {
                        $html_link.=' <a href="exercice.php?'.api_get_cidreq().'&show=result&filter=' . $filter . '&delete=delete&did=' . $id . '" onclick="javascript:if(!confirm(\'' . sprintf(get_lang('DeleteAttempt'), $user, $dt) . '\')) return false;">'.Display :: return_icon('delete.gif', get_lang('Delete')).'</a>';
                        $html_link.='&nbsp;';
                    }
                    if ($is_allowedToEdit) {
                        if ($filter==2){
                            $html_link.=' <a href="exercice_history.php?'.api_get_cidreq().'&exe_id=' . $id . '">' .Display :: return_icon('history.gif', get_lang('ViewHistoryChange')).'</a>';
                        }
                    }
                } else {
                    if ($revised) {
                        $html_link.="<a href='exercise_show.php?".api_get_cidreq()."&id=$id'>" . get_lang('Show') . "</a> ";
                    } else {
                        $html_link.='&nbsp;' . get_lang('NoResult');
                    }
                }
                $more_details_list = $html_link;
                if ($is_allowedToEdit || $is_tutor) {
                    $list_info [] = array($user_first_name,$user_last_name,$quiz_name_list,$duration_list,$date_list,$result_list,$more_details_list);
                } else {
                    $list_info [] = array($quiz_name_list,$duration_list,$date_list,$result_list,$more_details_list);
                }
            }
        }
    }
    // Print HotPotatoes test results.
        if (is_array($hpresults)) {
            $has_test_results = true;
            for ($i = 0; $i < sizeof($hpresults); $i++) {
                $hp_title = GetQuizName($hpresults[$i][1], $documentPath);
                if ($hp_title == '') {
                    $hp_title = basename($hpresults[$i][1]);
                }
                //$hp_date = api_convert_and_format_date($hpresults[$i][4], null, date_default_timezone_get());
                $hp_date = api_get_local_time($hpresults[$i][4], null, date_default_timezone_get());
                $hp_result = round(($hpresults[$i][2] / ($hpresults[$i][3] != 0 ? $hpresults[$i][3] : 1)) * 100, 2).'% ('.$hpresults[$i][2].' / '.$hpresults[$i][3].')';
                if ($is_allowedToEdit) {
                    $list_info[] = array($hpresults[$i][0], $hp_title, '-', $hp_date , $hp_result , '-');
                } else {
                    $list_info[] = array($hp_title, '-', $hp_date , $hp_result , '-');
                }
            }
        }
    return $list_info;
}
