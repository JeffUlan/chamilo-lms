<?php
/* For licensing terms, see /license.txt */

/**
 *	File containing the MultipleAnswer class.
 *	@package dokeos.exercise
 * 	@author Eric Marguin
 */

if(!class_exists('MultipleAnswerTrueFalse')):

/**
	CLASS MultipleAnswer
 *
 *	This class allows to instantiate an object of type MULTIPLE_ANSWER (MULTIPLE CHOICE, MULTIPLE ANSWER),
 *	extending the class question
 *
 *	@author Eric Marguin
 *	@package dokeos.exercise
 **/

class MultipleAnswerTrueFalse extends Question {

	static $typePicture = 'mcma.gif';
	static $explanationLangVar = 'MultipleAnswerTrueFalseSelect';
    var    $options;
	/**
	 * Constructor
	 */
	function MultipleAnswerTrueFalse(){
		parent::question();
		$this->type = MULTIPLE_ANSWER_TRUE_FALSE;
		$this->isContent = $this-> getIsContent();
        $this->options = array(1=>get_lang('True'),2 =>get_lang('False'), 3 =>get_lang('DoubtScore'));
	}

	/**
	 * function which redifines Question::createAnswersForm
	 * @param the formvalidator instance
	 * @param the answers number to display
	 */
	function createAnswersForm ($form) {

		$nb_answers  = isset($_POST['nb_answers']) ? $_POST['nb_answers'] : 4;  // The previous default value was 2. See task #1759.
		$nb_answers += (isset($_POST['lessAnswers']) ? -1 : (isset($_POST['moreAnswers']) ? 1 : 0));

		$obj_ex = $_SESSION['objExercise'];
        
		$html.='<div class="row">
			     <div class="label">
			         '.get_lang('Answers').'<br /><img src="../img/fill_field.png">
			     </div>
			     <div class="formw">';        
        
        $html2 ='<div class="row">
                 <div class="label">               
                 </div>
                 <div class="formw">';
        
        $form -> addElement ('html', $html2);        
        $form -> addElement ('html', '<table><tr>');  
        $renderer = & $form->defaultRenderer();
        $defaults = array();
        
        if (!empty($this->extra)) {
            $scores = explode(':',$this->extra);
        
            if (!empty($scores)) {
                for ($i = 1; $i <=3; $i++) {
                    $defaults['option['.$i.']']	= $scores[$i-1];                
                }        
            }        
        }
        
        // 3 scores
        $form->addElement('text', 'option[1]',get_lang('True'),      array('size'=>'5','value'=>'1'));
        $form->addElement('text', 'option[2]',get_lang('False'),     array('size'=>'5','value'=>'-0.5'));        
        $form->addElement('text', 'option[3]',get_lang('DoubtScore'),array('size'=>'5','value'=>'0'));  
                
        $form -> addElement('hidden', 'options_count', 3);
                    
        $form -> addElement ('html', '</tr></table>');
        $form -> addElement ('html', '</div></div>');
       
		$html.='<table class="data_table">
					<tr style="text-align: center;">
						<th>
							'.get_lang('Number').'
						</th>
						<th>
							'.get_lang('True').'
						</th>
                        <th>
                            '.get_lang('False').'
                        </th>     
						<th>
							'.get_lang('Answer').'
						</th>';
        				// show column comment when feedback is enable
        				if ($obj_ex->selectFeedbackType() != EXERCISE_FEEDBACK_TYPE_EXAM ) {
        				    $html .='<th>'.get_lang('Comment').'</th>';
        				}
				$html .= '</tr>';
		$form -> addElement ('html', $html);

		
		$correct = 0;
		if (!empty($this -> id))	{
			$answer = new Answer($this -> id);
			$answer->read();
			if (count($answer->nbrAnswers) > 0 && !$form->isSubmitted()) {
				$nb_answers = $answer->nbrAnswers;
			}
		}

		$form -> addElement('hidden', 'nb_answers');
		$boxes_names = array();

		if ($nb_answers < 1) {
			$nb_answers = 1;
			Display::display_normal_message(get_lang('YouHaveToCreateAtLeastOneAnswer'));
		}
        
        // Can be more options        
        $option_data = Question::readQuestionOption($this->id);                    
  
		for ($i = 1 ; $i <= $nb_answers ; ++$i) {
            
            $renderer->setElementTemplate('<td><!-- BEGIN error --><span class="form_error">{error}</span><!-- END error -->{label} &nbsp;&nbsp;{element}</td>');            
            $answer_number=$form->addElement('text', null,null,'value="'.$i.'"');
            $answer_number->freeze();
            
            
			if (is_object($answer)) {               
				$defaults['answer['.$i.']']     = $answer -> answer[$i];
				$defaults['comment['.$i.']']    = $answer -> comment[$i];
				//$defaults['weighting['.$i.']']  = float_format($answer -> weighting[$i], 1);
      
                $correct = $answer->correct[$i];
                                
                //$this->options
				$defaults['correct['.$i.']']    = $correct;  
                $j = 1;             
                if (!empty($option_data)) {
                    foreach ($option_data as $id=>$data) {
                        $form->addElement('radio', 'correct['.$i.']', null, null,$id);
                        $j++;
                        if ($j == 3) {
                        	break;
                        }
                    }            
                }
			} else {                
                $form->addElement('radio', 'correct['.$i.']', null, null, 1);            
                $form->addElement('radio', 'correct['.$i.']', null, null, 2);
            
                $defaults['answer['.$i.']']     = '';
                $defaults['comment['.$i.']']    = '';
                $defaults['correct['.$i.']']    = '';                
			}          
            
            //$form->addElement('select', 'correct['.$i.']',null, $this->options, array('id'=>$i,'onchange'=>'multiple_answer_true_false_onchange(this)'));
            
			$boxes_names[] = 'correct['.$i.']';

			$form->addElement('html_editor', 'answer['.$i.']',null, 'style="vertical-align:middle"', array('ToolbarSet' => 'TestProposedAnswer', 'Width' => '100%', 'Height' => '100'));
			$form->addRule('answer['.$i.']', get_lang('ThisFieldIsRequired'), 'required');

			// show comment when feedback is enable
			if ($obj_ex->selectFeedbackType() != EXERCISE_FEEDBACK_TYPE_EXAM) {
				$form->addElement('html_editor', 'comment['.$i.']',null, 'style="vertical-align:middle"', array('ToolbarSet' => 'TestProposedAnswer', 'Width' => '100%', 'Height' => '100'));
			}
			$form->addElement ('html', '</tr>');
		}
		$form -> addElement ('html', '</table>');
		$form -> addElement ('html', '<br />');

		//$form -> add_multiple_required_rule ($boxes_names , get_lang('ChooseAtLeastOneCheckbox') , 'multiple_required');


		$navigator_info = api_get_navigator();

		global $text, $class, $show_quiz_edition;
		if ($show_quiz_edition) {
			//ie6 fix
			if ($navigator_info['name']=='Internet Explorer' &&  $navigator_info['version']=='6') {
                $form->addElement('submit','submitQuestion',$text, 'class="'.$class.'"');
                $form->addElement('submit', 'moreAnswers', get_lang('PlusAnswer'),'class="plus"');
				$form->addElement('submit', 'lessAnswers', get_lang('LessAnswer'),'class="minus"');
			} else {
                // setting the save button here and not in the question class.php
                $form->addElement('style_submit_button','submitQuestion',$text, 'class="'.$class.'"');
                $form->addElement('style_submit_button', 'lessAnswers', get_lang('LessAnswer'),'style="float:right"; class="minus"');
                $form->addElement('style_submit_button', 'moreAnswers', get_lang('PlusAnswer'),'style="float:right"; class="plus"');	
			}
		}
		$renderer->setElementTemplate('{element}&nbsp;','lessAnswers');
		$renderer->setElementTemplate('{element}&nbsp;','submitQuestion');
		$renderer->setElementTemplate('{element}','moreAnswers');
		$form -> addElement ('html', '</div></div>');
		$defaults['correct'] = $correct;

		if (!empty($this -> id)) {
			$form -> setDefaults($defaults);
		} else {
			//if ($this -> isContent == 1) {
				$form -> setDefaults($defaults);
			//}
		}

		$form->setConstants(array('nb_answers' => $nb_answers));
	}


	/**
	 * abstract function which creates the form to create / edit the answers of the question
	 * @param the formvalidator instance
	 * @param the answers number to display
	 */
	function processAnswersCreation($form) {
		$questionWeighting = $nbrGoodAnswers = 0;
		$objAnswer        = new Answer($this->id);
		$nb_answers       = $form->getSubmitValue('nb_answers');        
        $options_count    = $form->getSubmitValue('options_count');        
                
       
        $correct = array();
        $options = Question::readQuestionOption($this->id);
        
        if (!empty($options)) {
            foreach ($options as $option_data) {
                $id = $option_data['id'];
                unset($option_data['id']);
                Question::updateQuestionOption($id, $option_data);
            }
        } else {            
            for ($i=1 ; $i <= 3 ; $i++) {
        	   $last_id = Question::saveQuestionOption($this->id, $this->options[$i], $i);
               $correct[$i] = $last_id;
            }            
        }
        
        $new_options = Question::readQuestionOption($this->id);
        $sorted_by_position = array();
        foreach($new_options as $item) {
        	$sorted_by_position[$item['position']] = $item;
        }
        
        
        //Saving quiz_question.extra values
        $extra_values = array();
        for ($i=1 ; $i <= 3 ; $i++) {
            $score = trim($form -> getSubmitValue('option['.$i.']'));
            $extra_values[]= $score;
        }       
        
        $this->setExtra(implode(':',$extra_values));       
          
		for ($i=1 ; $i <= $nb_answers ; $i++) {
        	$answer     = trim($form -> getSubmitValue('answer['.$i.']'));
            $comment    = trim($form -> getSubmitValue('comment['.$i.']'));
            $goodAnswer = trim($form -> getSubmitValue('correct['.$i.']'));  
            if (empty($options)) {
                //new 
                $goodAnswer = $sorted_by_position[$goodAnswer]['id'];
            }         
    	    $questionWeighting += $correct[1];
        	$objAnswer->createAnswer($answer, $goodAnswer, $comment,'',$i);            
        }        
    

    	// saves the answers into the data base
        $objAnswer -> save();
    
        // sets the total weighting of the question
        $this -> updateWeighting($questionWeighting);
        $this -> save();
	}
}

endif;
?>
