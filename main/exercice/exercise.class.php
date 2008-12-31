<?php
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2008 Dokeos SPRL

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, rue du Corbeau, 108, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/


/**
*	Exercise class: This class allows to instantiate an object of type Exercise
*	@package dokeos.exercise
* 	@author Olivier Brouckaert
* 	@version $Id: exercise.class.php 17489 2008-12-31 15:47:30Z marvil07 $
*/


if(!class_exists('Exercise')):

class Exercise
{
	var $id;
	var $exercise;
	var $description;
	var $sound;
	var $type;
	var $random;
	var $active;
	var $timeLimit;
	var $attempts;
	var $end_time;
    var $start_time;

	var $questionList;  // array with the list of this exercise's questions

	/**
	 * constructor of the class
	 *
	 * @author - Olivier Brouckaert
	 */
	function Exercise()
	{
		$this->id=0;
		$this->exercise='';
		$this->description='';
		$this->sound='';
		$this->type=1;
		$this->random=0;
		$this->active=1;
		$this->questionList=array();
		$this->timeLimit = 0;
		$this->end_time = '0000-00-00 00:00:00';
        $this->start_time = '0000-00-00 00:00:00';
	}

	/**
	 * reads exercise informations from the data base
	 *
	 * @author - Olivier Brouckaert
	 * @param - integer $id - exercise ID
	 * @return - boolean - true if exercise exists, otherwise false
	 */
	function read($id)
	{ 
		global $_course;
       	global $_configuration;
        global $questionList;

	    $TBL_EXERCICE_QUESTION  = Database::get_course_table(TABLE_QUIZ_TEST_QUESTION);
    	$TBL_EXERCICES          = Database::get_course_table(TABLE_QUIZ_TEST);
	    $TBL_QUESTIONS          = Database::get_course_table(TABLE_QUIZ_QUESTION);
    	#$TBL_REPONSES           = Database::get_course_table(TABLE_QUIZ_ANSWER);

		$sql="SELECT title,description,sound,type,random,active, results_disabled, max_attempt,start_time,end_time FROM $TBL_EXERCICES WHERE id='".Database::escape_string($id)."'";
		$result=api_sql_query($sql,__FILE__,__LINE__);

		// if the exercise has been found
		if($object=Database::fetch_object($result))
		{
			$this->id=$id;
			$this->exercise=$object->title;
			$this->description=$object->description;
			$this->sound=$object->sound;
			$this->type=$object->type;
			$this->random=$object->random;
			$this->active=$object->active;
			$this->results_disabled =$object->results_disabled;
			$this->attempts = $object->max_attempt;
			$this->end_time = $object->end_time;
            $this->start_time = $object->start_time;
			$sql="SELECT question_id, question_order FROM $TBL_EXERCICE_QUESTION,$TBL_QUESTIONS WHERE question_id=id AND exercice_id='".Database::escape_string($id)."' ORDER BY question_order";
			$result=api_sql_query($sql,__FILE__,__LINE__);

			// fills the array with the question ID for this exercise
			// the key of the array is the question position
			while($object=Database::fetch_object($result))
			{
				// makes sure that the question position is unique
				while(isset($this->questionList[$object->question_order]))
				{
					$object->question_order++;
				}

				$this->questionList[$object->question_order]=$object->question_id;
			}
			//var_dump($this->end_time,$object->start_time);
			if($this->random > 0){
          		$this->questionList = $this->selectRandomList();
          	}
            //overload questions list with recorded questions list
            //load questions only for exercises of type 'one question per page'
            //this is needed only is there is no questions   
            //
            if($this->type == 2 && $_configuration['live_exercise_tracking']==true && $_SERVER['REQUEST_METHOD']!='POST' && defined('QUESTION_LIST_ALREADY_LOGGED'))
	        {
            	//if(empty($_SESSION['questionList']))
            	$this->questionList = $questionList;
            }
			return true;
		}

		// exercise not found
		return false;
	}

	/**
	 * returns the exercise ID
	 *
	 * @author - Olivier Brouckaert
	 * @return - integer - exercise ID
	 */
	function selectId()
	{
		return $this->id;
	}

	/**
	 * returns the exercise title
	 *
	 * @author - Olivier Brouckaert
	 * @return - string - exercise title
	 */
	function selectTitle()
	{
		return $this->exercise;
	}
	
	/**
	 * returns the number of attempts setted
	 *
	 * @return - numeric - exercise attempts
	 */
	function selectAttempts()
	{
		return $this->attempts;
	}
	/**
	 * returns the time limit
	 */
	function selectTimeLimit()
	{
		return $this->timeLimit;
	}

	/**
	 * returns the exercise description
	 *
	 * @author - Olivier Brouckaert
	 * @return - string - exercise description
	 */
	function selectDescription()
	{
		return $this->description;
	}

	/**
	 * returns the exercise sound file
	 *
	 * @author - Olivier Brouckaert
	 * @return - string - exercise description
	 */
	function selectSound()
	{
		return $this->sound;
	}

	/**
	 * returns the exercise type
	 *
	 * @author - Olivier Brouckaert
	 * @return - integer - exercise type
	 */
	function selectType()
	{
		return $this->type;
	}

	/**
	 * tells if questions are selected randomly, and if so returns the draws
	 *
	 * @author - Olivier Brouckaert
	 * @return - integer - 0 if not random, otherwise the draws
	 */
	function isRandom()
	{
		if($this->random > 0){
			return true;
		}
		else{
			return false;
		}
	}
	/**
	 * Same as isRandom() but has a name applied to values different than 0 or 1
	 */
	function getShuffle()
	{
		return $this->random;
	}

	/**
	 * returns the exercise status (1 = enabled ; 0 = disabled)
	 *
	 * @author - Olivier Brouckaert
	 * @return - boolean - true if enabled, otherwise false
	 */
	function selectStatus()
	{
		return $this->active;
	}

	/**
	 * returns the array with the question ID list
	 *
	 * @author - Olivier Brouckaert
	 * @return - array - question ID list
	 */
	function selectQuestionList()
	{
		return $this->questionList;
	}

	/**
	 * returns the number of questions in this exercise
	 *
	 * @author - Olivier Brouckaert
	 * @return - integer - number of questions
	 */
	function selectNbrQuestions()
	{
		return sizeof($this->questionList);
	}

	/**
     * selects questions randomly in the question list
	 *
	 * @author - Olivier Brouckaert
	 * @return - array - if the exercise is not set to take questions randomly, returns the question list
	 *					 without randomizing, otherwise, returns the list with questions selected randomly
     */
	function selectRandomList()
	{		
		$nbQuestions = $this->selectNbrQuestions();
		$temp_list = $this->questionList;
		shuffle($temp_list);
		return array_combine(range(1,$nbQuestions),$temp_list);
		
		$nbQuestions = $this->selectNbrQuestions();
		
		//Not a random exercise, or if there are not at least 2 questions
		if($this->random == 0 || $nbQuestions < 2)
		{
			return $this->questionList;
		}

		$randQuestionList = array();
		$alreadyChosen = array();
		
		for($i=0; $i < $this->random; $i++)
		{	
			if($i < $nbQuestions){
				do
				{
					$rand=rand(1,$nbQuestions);
				}
				while(in_array($rand,$alreadyChosen));
				
				$alreadyChosen[]=$rand;
				$randQuestionList[$rand] = $this->questionList[$rand];
			}
		}

		return $randQuestionList;
	}

	/**
	 * returns 'true' if the question ID is in the question list
	 *
	 * @author - Olivier Brouckaert
	 * @param - integer $questionId - question ID
	 * @return - boolean - true if in the list, otherwise false
	 */
	function isInList($questionId)
	{
		return in_array($questionId,$this->questionList);
	}

	/**
	 * changes the exercise title
	 *
	 * @author - Olivier Brouckaert
	 * @param - string $title - exercise title
	 */
	function updateTitle($title)
	{
		$this->exercise=$title;
	}
	
	/**
	 * changes the exercise max attempts
	 *
	 * @param - numeric $attempts - exercise max attempts
	 */
	function updateAttempts($attempts)
	{
		$this->attempts=$attempts;
	}

	/**
	 * changes the exercise description
	 *
	 * @author - Olivier Brouckaert
	 * @param - string $description - exercise description
	 */
	function updateDescription($description)
	{
		$this->description=$description;
	}

	/**
	 * changes the exercise sound file
	 *
	 * @author - Olivier Brouckaert
	 * @param - string $sound - exercise sound file
	 * @param - string $delete - ask to delete the file
	 */
	function updateSound($sound,$delete)
	{
		global $audioPath, $documentPath,$_course, $_user;
        $TBL_DOCUMENT = Database::get_course_table(TABLE_DOCUMENT);
        $TBL_ITEM_PROPERTY = Database::get_course_table(TABLE_ITEM_PROPERTY);

		if($sound['size'] && (strstr($sound['type'],'audio') || strstr($sound['type'],'video')))
		{
			$this->sound=$sound['name'];

			if(@move_uploaded_file($sound['tmp_name'],$audioPath.'/'.$this->sound))
			{
				$query="SELECT 1 FROM $TBL_DOCUMENT "
            ." WHERE path='".str_replace($documentPath,'',$audioPath).'/'.$this->sound."'";
				$result=api_sql_query($query,__FILE__,__LINE__);

				if(!mysql_num_rows($result))
				{
        /*$query="INSERT INTO $TBL_DOCUMENT(path,filetype) VALUES "
            ." ('".str_replace($documentPath,'',$audioPath).'/'.$this->sound."','file')";
        api_sql_query($query,__FILE__,__LINE__);*/
        $id = add_document($_course,str_replace($documentPath,'',$audioPath).'/'.$this->sound,'file',$sound['size'],$sound['name']);

        //$id = Database::get_last_insert_id();
        //$time = time();
        //$time = date("Y-m-d H:i:s", $time);
        // insert into the item_property table, using default visibility of "visible"
        /*$query = "INSERT INTO $TBL_ITEM_PROPERTY "
                ."(tool, ref, insert_user_id,to_group_id, insert_date, lastedit_date, lastedit_type) "
                ." VALUES "
                ."('".TOOL_DOCUMENT."', $id, $_user['user_id'], 0, '$time', '$time', 'DocumentAdded' )";
        api_sql_query($query,__FILE__,__LINE__);*/
        api_item_property_update($_course, TOOL_DOCUMENT, $id, 'DocumentAdded',$_user['user_id']);
        item_property_update_on_folder($_course,str_replace($documentPath,'',$audioPath),$_user['user_id']);
				}
			}
		}
		elseif($delete && is_file($audioPath.'/'.$this->sound))
		{
			$this->sound='';
		}
	}

	/**
	 * changes the exercise type
	 *
	 * @author - Olivier Brouckaert
	 * @param - integer $type - exercise type
	 */
	function updateType($type)
	{
		$this->type=$type;
	}

	/**
	 * sets to 0 if questions are not selected randomly
	 * if questions are selected randomly, sets the draws
	 *
	 * @author - Olivier Brouckaert
	 * @param - integer $random - 0 if not random, otherwise the draws
	 */
	function setRandom($random)
	{
		$this->random=$random;
	}

	/**
	 * enables the exercise
	 *
	 * @author - Olivier Brouckaert
	 */
	function enable()
	{
		$this->active=1;
	}

	/**
	 * disables the exercise
	 *
	 * @author - Olivier Brouckaert
	 */
	function disable()
	{
		$this->active=0;
	}
	
	function disable_results()
	{
		$this->results_disabled = true;
	}
	
	function enable_results()
	{
		$this->results_disabled = false;
	}

	/**
	 * updates the exercise in the data base
	 *
	 * @author - Olivier Brouckaert
	 */
	function save()
	{
		$TBL_EXERCICES = Database::get_course_table(TABLE_QUIZ_TEST);
        $TBL_QUESTIONS = Database::get_course_table(TABLE_QUIZ_QUESTION);        
        $TBL_QUIZ_QUESTION= Database::get_course_table(TABLE_QUIZ_TEST_QUESTION);        

		$id=$this->id;
		$exercise=addslashes($this->exercise);
		$description=addslashes($this->description);
		$sound=addslashes($this->sound);
		$type=$this->type;
		$attempts=$this->attempts;
		$random=$this->random;
		$active=$this->active;
		$results_disabled = intval($this->results_disabled);
        $start_time = Database::escape_string($this->start_time);
        $end_time = Database::escape_string($this->end_time);
		// exercise already exists
		if($id)
		{
			$sql="UPDATE $TBL_EXERCICES SET 
						start_time='$start_time',end_time='$end_time', 
						title='".Database::escape_string($exercise)."',
						description='".Database::escape_string($description)."',
						sound='".Database::escape_string($sound)."',
						type='".Database::escape_string($type)."',
						random='".Database::escape_string($random)."',
						active='".Database::escape_string($active)."',
						max_attempt='".Database::escape_string($attempts)."', " .
						"results_disabled='".Database::escape_string($results_disabled)."'
					WHERE id='".Database::escape_string($id)."'";
			api_sql_query($sql,__FILE__,__LINE__);
		}
		// creates a new exercise
		else
		{
			$sql="INSERT INTO $TBL_EXERCICES(start_time,end_time,title,description,sound,type,random,active, results_disabled, max_attempt) 
					VALUES(
						'$start_time','$end_time',
						'".Database::escape_string($exercise)."',
						'".Database::escape_string($description)."',
						'".Database::escape_string($sound)."',
						'".Database::escape_string($type)."',
						'".Database::escape_string($random)."',
						'".Database::escape_string($active)."',
						'".Database::escape_string($results_disabled)."',
						'".Database::escape_string($attempts)."'
						)";
			api_sql_query($sql,__FILE__,__LINE__);

			$this->id=mysql_insert_id();
		}

		// updates the question position
        $this->update_question_positions();
	}

    function update_question_positions() {
    	// updates the question position
        $TBL_QUIZ_QUESTION= Database::get_course_table(TABLE_QUIZ_TEST_QUESTION);
        foreach($this->questionList as $position=>$questionId)
        {
            //$sql="UPDATE $TBL_QUESTIONS SET position='".Database::escape_string($position)."' WHERE id='".Database::escape_string($questionId)."'";
            $sql="UPDATE $TBL_QUIZ_QUESTION SET question_order='".Database::escape_string($position)."' " .
                 "WHERE question_id='".Database::escape_string($questionId)."' and exercice_id=".Database::escape_string($this->id)."";
            api_sql_query($sql,__FILE__,__LINE__);

        }
    }

	/**
	 * moves a question up in the list
	 *
	 * @author - Olivier Brouckaert
 	 * @author - Julio Montoya (rewrote the code)
	 * @param - integer $id - question ID to move up
	 */
	function moveUp($id)
	{	
		// there is a bug with some version of PHP with the key and prev functions
		// the script commented was tested in dev.dokeos.com with no success
		// Instead of using prev and next this was change with arrays.
		/*
		foreach($this->questionList as $position=>$questionId)		
		{			
			// if question ID found
			if($questionId == $id)
			{							
				// position of question in the array
				echo $pos1=$position; //1
				echo "<br>";	
				
				prev($this->questionList);				
				prev($this->questionList);			
												
				// position of previous question in the array				
				$pos2=key($this->questionList);			
				//if the cursor of the array hit the end 
				// then we must reset the array to get the previous key			
								
				if($pos2===null)
				{					
					end($this->questionList);
					prev($this->questionList);
					$pos2=key($this->questionList);					
				}
				
				// error, can't move question
				if(!$pos2)
				{						
					//echo 'cant move!';
					$pos2=key($this->questionList); 
					reset($this->questionList);
				}
				$id2=$this->questionList[$pos2];
				// exits foreach()
				break;
			}
			$i++;
		}
		*/	
		$question_list =array();		
		foreach($this->questionList as $position=>$questionId)		
		{
			$question_list[]=	$questionId;
		}		
		$len=count($question_list);
		$orderlist=array_keys($this->questionList);		
		for($i=0;$i<$len;$i++)		
		{			
			$questionId = $question_list[$i];			
			if($questionId == $id)
			{							
				// position of question in the array
				$pos1=$orderlist[$i];		
				$pos2=$orderlist[$i-1];		
				if($pos2===null)
				{		
					$pos2 =		$orderlist[$len-1];							
				}				
				// error, can't move question
				if(!$pos2)
				{
					$pos2=$orderlist[0];	
					$i=0;			
				}		
				break;
			}			
		}
		// permutes questions in the array
		$temp=$this->questionList[$pos2];
		$this->questionList[$pos2]=$this->questionList[$pos1];
		$this->questionList[$pos1]=$temp;
	}

	/**
	 * moves a question down in the list
	 *
	 * @author - Olivier Brouckaert
	 * @param - integer $id - question ID to move down
	 */
	function moveDown($id)
	{
		// there is a bug with some version of PHP with the key and prev functions
		// the script commented was tested in dev.dokeos.com with no success
		// Instead of using prev and next this was change with arrays.
		
		/*
		foreach($this->questionList as $position=>$questionId)
		{
			// if question ID found
			if($questionId == $id)
			{
				// position of question in the array
				$pos1=$position;

				//next($this->questionList);

				// position of next question in the array
				$pos2=key($this->questionList);

				// error, can't move question
				if(!$pos2)
				{
					//echo 'cant move!';
					return;
				}

				$id2=$this->questionList[$pos2];

				// exits foreach()
				break;
			}
		}
		*/
		
		$question_list =array();		
		foreach($this->questionList as $position=>$questionId)		
		{
			$question_list[]=	$questionId;
		}		
		$len=count($question_list);
		$orderlist=array_keys($this->questionList);
				
		for($i=0;$i<$len;$i++)		
		{
			$questionId = $question_list[$i];				
			if($questionId == $id)
			{
				$pos1=$orderlist[$i+1];					
				$pos2 =$orderlist[$i];			
				if(!$pos2)
				{
					//echo 'cant move!';					
				} 
				break;
			}			
		}
			
		// permutes questions in the array
		$temp=$this->questionList[$pos2];
		$this->questionList[$pos2]=$this->questionList[$pos1];
		$this->questionList[$pos1]=$temp;
	}

	/**
	 * adds a question into the question list
	 *
	 * @author - Olivier Brouckaert
	 * @param - integer $questionId - question ID
	 * @return - boolean - true if the question has been added, otherwise false
	 */
	function addToList($questionId)
	{
		// checks if the question ID is not in the list
		if(!$this->isInList($questionId))
		{
			// selects the max position
			if(!$this->selectNbrQuestions())
			{
				$pos=1;
			}
			else
			{
				$pos=max(array_keys($this->questionList))+1;
			}

			$this->questionList[$pos]=$questionId;

			return true;
		}

		return false;
	}

	/**
	 * removes a question from the question list
	 *
	 * @author - Olivier Brouckaert
	 * @param - integer $questionId - question ID
	 * @return - boolean - true if the question has been removed, otherwise false
	 */
	function removeFromList($questionId)
	{
		// searches the position of the question ID in the list
		$pos=array_search($questionId,$this->questionList);

		// question not found
		if($pos === false)
		{
			return false;
		}
		else
		{
			// deletes the position from the array containing the wanted question ID
			unset($this->questionList[$pos]);

			return true;
		}
	}

	/**
	 * deletes the exercise from the database
	 * Notice : leaves the question in the data base
	 *
	 * @author - Olivier Brouckaert
	 */
	function delete(){
		$TBL_EXERCICES = Database::get_course_table(TABLE_QUIZ_TEST);
		$sql="UPDATE $TBL_EXERCICES SET active='-1' WHERE id='".Database::escape_string($this->id)."'";
		api_sql_query($sql);
	}

	/**
	 * Creates the form to create / edit an exercise
	 * @param FormValidator $form the formvalidator instance (by reference)
	 */
	function createForm ($form)
	{				
		
		// title
		$form -> addElement('text', 'exerciseTitle', get_lang('ExerciseName').' : ','class="input_titles"');

		// fck editor
		global $fck_attribute;
		$fck_attribute = array();
		$fck_attribute['Height'] = '250';
		$fck_attribute['Width'] = '100%';
		$fck_attribute['ToolbarSet'] = 'NewTest'; 
		
		$fck_attribute['Config']['InDocument'] = false;		
		$fck_attribute['Config']['CreateDocumentDir'] = '../../courses/'.api_get_course_path().'/document/';
		
		//$fck_attribute['Config']['CreateDocumentWebDir'] = api_get_path('WEB_COURSE_PATH').$_course['path'].'/document/';
		
		
		
		$form -> addElement ('html_editor', 'exerciseDescription', get_lang('ExerciseDescription').' : ');

		// type
		$radios = array();
		$radios[] = FormValidator :: createElement ('radio', 'exerciseType', null, get_lang('SimpleExercise'),'1');
		$radios[] = FormValidator :: createElement ('radio', 'exerciseType', null, get_lang('SequentialExercise'),'2');
		$form -> addGroup($radios, null, get_lang('ExerciseType').' : ', '<br />');
		
		$form -> addElement('html','<div class="row">
			<div class="label">&nbsp;</div>
			<div class="formw">
				<a href="javascript://" onclick=" return advanced_parameters()"><span id="img_plus_and_minus"><img src="../img/nolines_plus.gif" alt="" />'.get_lang('AdvancedParameters').'</span></a>
			</div>
			</div>');
			
		// Random questions
		$form -> addElement('html','<div id="options" style="display: none;">');
		$random = array();
		
		$max = ($this->id > 0) ? $this->selectNbrQuestions() : 10 ;
		$option = range(0,$max);
		$option[0]=get_lang('DoNotRandomize');

		$random[] = FormValidator :: createElement ('static', 'help','help','<span style="font-style: italic;">'.get_lang('RandomQuestionsHelp').'</span>');
		$random[] = FormValidator :: createElement ('select', 'randomQuestions',null,$option); 
		
		//$random[] = FormValidator :: createElement ('text', 'randomQuestions', null,null,'0');
		$form -> addGroup($random,null,get_lang('RandomQuestions').' : ','<br />');
		
		$attempt_option=range(0,10);
        $attempt_option[0]=get_lang('Infinite');
        
        $form -> addElement('select', 'exerciseAttempts',get_lang('ExerciseAttempts').' : ',$attempt_option);

        $form -> addElement('checkbox', 'enabletimelimit',null ,get_lang('EnableTimeLimits'));
        //$form -> addElement('date', 'start_time', get_lang('ExeStartTime'), array('language'=>'es','format' => 'dMYHi'));
        //$form -> addElement('date', 'end_time', get_lang('ExeEndTime'), array('language'=>'es','format' => 'dMYHi'));
        $form->addElement('datepicker', 'start_time', get_lang('ExeStartTime'), array('form_name'=>'exercise_admin'));
		$form->addElement('datepicker', 'end_time', get_lang('ExeEndTime'), array('form_name'=>'exercise_admin'));

		// Exercise attempts
		//$form -> addElement('text', 'exerciseAttempts', get_lang('ExerciseAttempts').' : ',array('size'=>'2'));		
		
		$form -> addElement('html','</div>');
		
		// submit
		$form -> addElement('submit', 'submitExercise', get_lang('Ok'));

		// rules
		$form -> addRule ('exerciseTitle', get_lang('GiveExerciseName'), 'required');
		$form -> addRule ('exerciseAttempts', get_lang('Numeric'), 'numeric');
		$form -> addRule ('start_time', get_lang('DateNotValid'), 'date');
        $form -> addRule ('end_time', get_lang('DateNotValid'), 'date');
        $form->addRule(array ('start_time', 'end_time'), get_lang('StartDateShouldBeBeforeEndDate'), 'date_compare', 'lte');
        

		// defaults
		$defaults = array();
		if($this -> id > 0)
		{
			if ($this -> random > $this->selectNbrQuestions())
			{
				$defaults['randomQuestions'] =  $this->selectNbrQuestions();
			}
			else
			{			
				$defaults['randomQuestions'] = $this -> random;
			}
			
			$defaults['exerciseType'] = $this -> selectType();
			$defaults['exerciseTitle'] = $this -> selectTitle();
			$defaults['exerciseDescription'] = $this -> selectDescription();
			$defaults['exerciseAttempts'] = $this->selectAttempts();	
			
  			if(($this -> start_time!='0000-00-00 00:00:00')||($this -> end_time!='0000-00-00 00:00:00'))
            	$defaults['enabletimelimit'] = 1;
		    
		    $defaults['start_time'] = ($this->start_time!='0000-00-00 00:00:00')? $this -> start_time : date('Y-m-d 12:00:00');
            $defaults['end_time'] = ($this->end_time!='0000-00-00 00:00:00')?$this -> end_time : date('Y-m-d 12:00:00');					
		}
		else
		{
			$defaults['exerciseType'] = 1;
			$defaults['exerciseAttempts'] = 0;
			$defaults['randomQuestions'] = 0;
			$defaults['exerciseDescription'] = '';
			
			$defaults['start_time'] = date('Y-m-d 12:00:00');
			$defaults['end_time'] = date('Y-m-d 12:00:00');
		}
		$form -> setDefaults($defaults);
	}


	/**
	 * function which process the creation of exercises
	 * @param FormValidator $form the formvalidator instance
	 */
	function processCreation($form)
	{
		$this -> updateAttempts($form -> getSubmitValue('exerciseAttempts'));
		$this -> updateTitle($form -> getSubmitValue('exerciseTitle'));
		$this -> updateDescription($form -> getSubmitValue('exerciseDescription'));
		$this -> updateType($form -> getSubmitValue('exerciseType'));
		$this -> setRandom($form -> getSubmitValue('randomQuestions'));

		if($form -> getSubmitValue('enabletimelimit')==1)
        {
           $start_time = $form -> getSubmitValue('start_time');
           $this->start_time = $start_time['Y'].'-'.$start_time['F'].'-'.$start_time['d'].' '.$start_time['H'].':'.$start_time['i'].':00';
           $end_time = $form -> getSubmitValue('end_time');
           $this->end_time = $end_time['Y'].'-'.$end_time['F'].'-'.$end_time['d'].' '.$end_time['H'].':'.$end_time['i'].':00';
        }
          else
  		{
           $this->start_time = '0000-00-00 00:00:00';
           $this->end_time = '0000-00-00 00:00:00';
        }
        //echo $end_time;exit;
        $this -> save();
	}
}

endif;
?>
