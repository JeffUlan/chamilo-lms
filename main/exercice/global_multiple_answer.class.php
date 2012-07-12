<?php

	if(!class_exists('GlobalMultipleAnswer')):															

	class GlobalMultipleAnswer extends Question {														

		static $typePicture = 'mcmagl.gif'; 															
		static $explanationLangVar = 'GlobalMultipleAnswer';										

		/*Constructor*/
		function GlobalMultipleAnswer(){																
			parent::question();																			
			$this -> type = GLOBAL_MULTIPLE_ANSWER;													
			$this -> isContent = $this-> getIsContent();
		}

	
		/**
		 * function which redifines Question::createAnswersForm
		 * @param the formvalidator instance
		 * @param the answers number to display
		 */
		function createAnswersForm ($form) {
		
		
	
			$nb_answers = isset($_POST['nb_answers']) ? $_POST['nb_answers'] : 4;  
			$nb_answers += (isset($_POST['lessAnswers']) ? -1 : (isset($_POST['moreAnswers']) ? 1 : 0)); 	
		
			$obj_ex = $_SESSION['objExercise'];
		
			/* Mise en variable de Affichage "Reponses" et son icone, "N�", "Vrai", "Reponse" */
			$html='<table class="data_table">
					<tr>
						<th width="10px">
							'.get_lang('Number').'
						</th>
						<th width="10px">
							'.get_lang('True').'
						</th>
						<th width="50%">
							'.get_lang('Answer').'
						</th>';
							
							// Espace entre l'entete et les r�ponses					
							if ($obj_ex->selectFeedbackType() != EXERCISE_FEEDBACK_TYPE_EXAM ) {	
								$html .='<th>'.get_lang('Comment').'</th>';
							}	
			
			/*$html .= '<th width="50px">
							'.get_lang('Weighting').'
						</th>
					</tr>';
			*/
			$html .='</tr>';
			/* Excution de l'affichage*/		
			$form -> addElement ('label', get_lang('Answers').'<br /> <img src="../img/fill_field.png">', $html);
			
			
			/*Initialiation variable*/
			$defaults = array();
			$correct = 0;
			
			/*Mise en variable du nombre de reponse */
			if(!empty($this -> id))	{
				$answer = new Answer($this -> id);
				$answer -> read();
				if(count($answer->nbrAnswers)>0 && !$form->isSubmitted()) {
					$nb_answers = $answer->nbrAnswers;
				}
			}

#le nombre de r�ponses est bien enregistr� sous la forme int(nb)
			
			/* Ajout mise en forme nb reponse */
			$form -> addElement('hidden', 'nb_answers');
			$boxes_names = array();
			
			/* V�rification : Cr�action d'au moins une r�ponse */
			if ($nb_answers < 1) {
				$nb_answers = 1;
				Display::display_normal_message(get_lang('YouHaveToCreateAtLeastOneAnswer'));
			}	
			
//---------------------------------- D�but affichage score global dans la modification d'une question
			$scoreA = "0"; //par reponse
			$scoreG = "0"; //Global
//--------------------------------- Fin

			/* boucle pour sauvegarder les donn�es dans le tableau defaults */
			for($i = 1 ; $i <= $nb_answers ; ++$i) {
				/* si la reponse est de type objet */
				if(is_object($answer)) {
					$defaults['answer['.$i.']'] = $answer -> answer[$i];
					$defaults['comment['.$i.']'] = $answer -> comment[$i];
					$defaults['correct['.$i.']'] = $answer -> correct[$i];
					
//------------- D�but
					$scoreA = $answer -> weighting[$i];
				}
				if ($scoreA>0){
				$scoreG = $scoreG  + $scoreA ;
				}
//------------- Fin
	
//------------- Debut si un des scores par reponse est egal � 0 : la coche vaut 1 (coch�)
				if ($scoreA == 0)
					$defaults['pts'] = 1;
				else
					$defaults['pts'] = 0;
//------------- Fin

				$renderer = & $form->defaultRenderer();
				$renderer->setElementTemplate('<td><!-- BEGIN error --><span class="form_error">{error}</span><!-- END error --><br/>{element}</td>');
				
				//$answer_number=$form->addElement('text', null,null,'value="'.$i.'"');
				$answer_number=$form->addElement('text', 'counter['.$i.']', null, 'value="'.$i.'"');
				$answer_number->freeze();

				$form->addElement('checkbox', 'correct['.$i.']', null, null, 'class="checkbox" style="margin-left: 2em;"');
				$boxes_names[] = 'correct['.$i.']';

				$form->addElement('html_editor', 'answer['.$i.']',null, 'style="vertical-align:middle"', array('ToolbarSet' => 'TestProposedAnswer', 'Width' => '100%', 'Height' => '100'));
				$form->addRule('answer['.$i.']', get_lang('ThisFieldIsRequired'), 'required');

				// show comment when feedback is enable
				if ($obj_ex->selectFeedbackType() != EXERCISE_FEEDBACK_TYPE_EXAM) {
					$form->addElement('html_editor', 'comment['.$i.']',null, 'style="vertical-align:middle"', array('ToolbarSet' => 'TestProposedAnswer', 'Width' => '100%', 'Height' => '100'));
				}
					
				$form -> addElement ('html', '</tr>');
			}
//--------- Mise en variable du score global lors d'une modification de la question/r�ponse
			$defaults['weighting[1]'] = (round($scoreG)); 
			
			$form -> addElement ('html', '</div></div></table>');
			//$form -> addElement ('html', '<br />');
			$form -> add_multiple_required_rule ($boxes_names , get_lang('ChooseAtLeastOneCheckbox') , 'multiple_required');

//--------- Affichage score suivant la langue	
			$form -> addElement ('html', '<table><div class="row">
										<tr>'.get_lang('Score').'</tr><tr><td>');		

			//only 1 answer the all deal ...
			$form->addElement('text', 'weighting[1]');

			$form -> addElement ('html', '</td><td width=18%></td><td width="1" align="right">');			
			
			global $pts;
//--------- Creation coche pour ne pas prendre en compte les n�gatifs
			$form -> addElement('checkbox', 'pts','',get_lang('SansNeg'));
			$form -> addElement ('html', '</td></tr></div></table><br>');
//------------------------------------------------------------------------------
					
			// Affiche un message si le score n'est pas renseign�
			$form->addRule('weighting[1]', get_lang('ScoreVide'), 'required');	
			
			$navigator_info = api_get_navigator();
			global $text, $class, $show_quiz_edition;
			
			//ie6 fix
			if ($show_quiz_edition) {
				if ($navigator_info['name']=='Internet Explorer' &&  $navigator_info['version']=='6') {
					
					$form->addElement('submit', 'lessAnswers', get_lang('LessAnswer'),'class="minus"');
					$form->addElement('submit', 'moreAnswers', get_lang('PlusAnswer'),'class="plus"');
					$form->addElement('submit','submitQuestion',$text, 'class="'.$class.'"');				
				} else {
					
					$form->addElement('style_submit_button', 'lessAnswers', get_lang('LessAnswer'),'class="minus"');
					$form->addElement('style_submit_button', 'moreAnswers', get_lang('PlusAnswer'),'class="plus"');
					$form->addElement('style_submit_button','submitQuestion',$text, 'class="'.$class.'"');
					
					// setting the save button here and not in the question class.php
					
				}
			}
			$renderer->setElementTemplate('{element}&nbsp;','lessAnswers');
			$renderer->setElementTemplate('{element}&nbsp;','submitQuestion');
			$renderer->setElementTemplate('{element}','moreAnswers');
			$form -> addElement ('html', '</div></div>');
			
			$defaults['correct'] = $correct;
			
			if (!empty($this->id)) {
				$form -> setDefaults($defaults);
			} else {
				if ($this -> isContent == 1) {
					$form -> setDefaults($defaults);
				}
			}
			$form->setConstants(array('nb_answers' => $nb_answers));
		
		
		}
/**************************************************************************************************/
	/**
	 * abstract function which creates the form to create / edit the answers of the question
	 * @param the formvalidator instance
	 * @param the answers number to display
	 */
	function processAnswersCreation($form) {
	
	

		$questionWeighting = $nbrGoodAnswers = 0;
		$objAnswer = new Answer($this->id);
		$nb_answers = $form -> getSubmitValue('nb_answers');

		// Score total
		$answer_score = trim($form->getSubmitValue('weighting[1]'));
		
		// Reponses correctes
		$nbr_corrects = 0;
		for ($i = 1; $i <= $nb_answers; $i++) {
			$goodAnswer = trim($form->getSubmitValue('correct[' . $i . ']'));
			if ($goodAnswer) {
				$nbr_corrects++;
			}
		}
		// Set question weighting (score total)
		$questionWeighting = $answer_score;

		// Set score per answer
		$nbr_corrects = $nbr_corrects == 0 ? 1 : $nbr_corrects;
		$answer_score = $nbr_corrects == 0 ? 0 : $answer_score;
							//echo('affiche1');var_dump($answer_score);echo('<br>');
		
		$answer_score = ($answer_score/$nbr_corrects);
							//echo('affiche2');var_dump($answer_score);echo('<br>');
		
		//$answer_score �quivaut � la valeur d'une bonne r�ponse
		
// cr�ation variable pour r�cuperer la valeur de la coche pour la prise en compte des n�gatifs
		$test="";
		$test = $form -> getSubmitValue('pts');
//---------------
		
		for ($i = 1; $i <= $nb_answers; $i++) {
			$answer = trim($form->getSubmitValue('answer[' . $i . ']'));
			$comment = trim($form -> getSubmitValue('comment['.$i.']'));
			$goodAnswer = trim($form->getSubmitValue('correct[' . $i . ']'));

			if ($goodAnswer) {
				$weighting = abs($answer_score);
			} 
			else{
					if ($test == 1)
					{
						$weighting = 0;
					}else
						$weighting = -abs($answer_score);
				}
		
			$objAnswer -> createAnswer($answer,$goodAnswer,$comment,$weighting,$i);
		}
		// saves the answers into the data base
        $objAnswer -> save();

        // sets the total weighting of the question --> sert � donner le score total pendant l'examen
        $this -> updateWeighting($questionWeighting);
        $this -> save();
	}
	
/**************************************************************************************************/
	function return_header($feedback_type = null, $counter = null) {
	    parent::return_header($feedback_type, $counter);
	    $header = '<table width="100%" class="data_table_exercise_result">			
			<tr>
				<td><i>'.get_lang("Choice").'</i> </td>
				<td><i>'. get_lang("ExpectedChoice").'</i></td>
				<td><i>'. get_lang("Answer").'</i></td>';
				if ($feedback_type != EXERCISE_FEEDBACK_TYPE_EXAM) { 
    				$header .= '<td><i>'.get_lang("Comment").'</i></td>';
				} else { 
					$header .= '<td>&nbsp;</td>';
				}
        $header .= '</tr>';
        return $header;	    
	}
	# Choice --> "Votre Choix"
	# ExpectedChoice --> "choix attendu"
	# Answer --> "Reponse"
/**********************************************************************************************************/
	/**
   * Display the question in tracking mode (use templates in tracking/questions_templates)
   * @param $nbAttemptsInExercise the number of users who answered the quiz
   */
	/*function displayTracking($exerciseId, $nbAttemptsInExercise) {

   if (!class_exists('Answer'))
    require_once(api_get_path(SYS_CODE_PATH) . 'exercice/answer.class.php');

   $o_answer = new Answer($this->id);
   $o_answer->stats = $this->getAverageStats($exerciseId, $nbAttemptsInExercise);
   include(api_get_path(SYS_CODE_PATH) . 'exercice/tracking/questions_templates/multiple_answer.page');
  }*/
 
/**********************************************************************************************************/
  /**
   * Returns learners choices for each question in percents
   * @param $nbAttemptsInExercise the number of users who answered the quiz
   * @return array the percents
   */
 /* function getAverageStats($exerciseId, $nbAttemptsInExercise) {

   $preparedSql = 'SELECT attempts.answer, COUNT(1) as nbAttempts
						FROM ' . Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_ATTEMPT) . ' as attempts
						INNER JOIN ' . Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_EXERCICES) . ' as exercises
							ON exercises.exe_id = attempts.exe_id
						WHERE attempts.course_code LIKE "%s"
						AND attempts.question_id = %d
						AND exercises.exe_exo_id = %d
						GROUP BY answer';
   $sql = sprintf($preparedSql, api_get_course_id(), $this->id, $exerciseId);
   $rs = Database::query($sql, __FILE__, __LINE__);

   $totalAttempts = 0;
   $stats = array();
   while ($answer = Database::fetch_object($rs)) {
    $stats[$answer->answer] = array();
    $stats[$answer->answer]['total'] = $answer->nbAttempts;
   }

   foreach ($stats as $answerId => &$stat) {
    $stat['average'] = $stat['total'] / $nbAttemptsInExercise * 100;
   }


   return $stats;
  }*/
}
endif;
?>
