<?php
/* For licensing terms, see /license.txt */

/**
*	Exercise class: This class allows to instantiate an object of type Exercise
*	@package chamilo.exercise
* 	@author Olivier Brouckaert
* 	@author Julio Montoya Cleaning exercises
* 	@version $Id: exercise.class.php 22046 2009-07-14 01:45:19Z ivantcholakov $
*/

define('ALL_ON_ONE_PAGE',1);
define('ONE_PER_PAGE',2);
//0=>Feedback , 1=>DirectFeedback, 2=>NoFeedback
define('EXERCISE_FEEDBACK_TYPE_END',0);
define('EXERCISE_FEEDBACK_TYPE_DIRECT',1);
define('EXERCISE_FEEDBACK_TYPE_EXAM',2);

require_once dirname(__FILE__).'/../inc/lib/exercise_show_functions.lib.php';

if(!class_exists('Exercise')):

class Exercise {
	
	public $id;
	public $exercise;
	public $description;
	public $sound;
	public $type; //ALL_ON_ONE_PAGE or ONE_PER_PAGE
	public $random;
	public $random_answers;
	public $active;
	public $timeLimit;
	public $attempts;
	public $feedbacktype;
	public $end_time;
  	public $start_time;
	public $questionList;  // array with the list of this exercise's questions
	public $results_disabled;
  	public $expired_time;    
    public $course;
    public $propagate_neg;
    
  	
	/**
	 * Constructor of the class
	 *
	 * @author - Olivier Brouckaert
	 */
	function Exercise($course_id = null) {
		$this->id				= 0;
		$this->exercise			= '';
		$this->description		= '';
		$this->sound			= '';
		$this->type				= ALL_ON_ONE_PAGE;
		$this->random			= 0;
		$this->random_answers	= 0;
		$this->active			= 1;
		$this->questionList		= array();
		$this->timeLimit 		= 0;
		$this->end_time 		= '0000-00-00 00:00:00';
		$this->start_time 		= '0000-00-00 00:00:00';
		$this->results_disabled = 1;
		$this->expired_time 	= '0000-00-00 00:00:00';
		$this->propagate_neg    = 0;
        
        if (!empty($course_id)) {
            $this->course_id        =  intval($course_id);
            $course_info            =  api_get_course_info_by_id($this->course_id);
        } else {            
        	$course_info = api_get_course_info();
        }
        $this->course   = $course_info; 
        
	}

	/**
	 * reads exercise informations from the data base
	 *
	 * @author - Olivier Brouckaert
	 * @param - integer $id - exercise ID
	 * @return - boolean - true if exercise exists, otherwise false
	 */
	function read($id) {
	    $TBL_EXERCICE_QUESTION  = Database::get_course_table(TABLE_QUIZ_TEST_QUESTION,$this->course['db_name']);
    	$TBL_EXERCICES          = Database::get_course_table(TABLE_QUIZ_TEST,$this->course['db_name']);
	    $TBL_QUESTIONS          = Database::get_course_table(TABLE_QUIZ_QUESTION,$this->course['db_name']);
    	#$TBL_REPONSES           = Database::get_course_table(TABLE_QUIZ_ANSWER);

		$sql="SELECT title,description,sound,type,random, random_answers, active, results_disabled, max_attempt,start_time,end_time,feedback_type,expired_time,propagate_neg FROM $TBL_EXERCICES WHERE id='".Database::escape_string($id)."'";
		$result=Database::query($sql);

		// if the exercise has been found
		if ($object=Database::fetch_object($result)) {
			$this->id				= $id;
			$this->exercise			= $object->title;
			$this->description		= $object->description;
			$this->sound			= $object->sound;
			$this->type				= $object->type;
			$this->random			= $object->random;
			$this->random_answers	= $object->random_answers;
			$this->active			= $object->active;
			$this->results_disabled = $object->results_disabled;
			$this->attempts 		= $object->max_attempt;
			$this->feedbacktype 	= $object->feedback_type;
			$this->propagate_neg    = $object->propagate_neg;
			
			if ($object->end_time != '0000-00-00 00:00:00') {
				$this->end_time 		= api_get_local_time($object->end_time); 
			}
			if ($object->start_time != '0000-00-00 00:00:00') {
      			$this->start_time 		= api_get_local_time($object->start_time);
			}
      		$this->expired_time 	= $object->expired_time; //control time
      		
      		
      		
			$sql="SELECT question_id, question_order FROM $TBL_EXERCICE_QUESTION, $TBL_QUESTIONS WHERE question_id=id AND exercice_id='".Database::escape_string($id)."' ORDER BY question_order";
			
			$result=Database::query($sql);

			// fills the array with the question ID for this exercise
			// the key of the array is the question position
			 
			while ($new_object = Database::fetch_object($result)) {
				$this->questionList[$new_object->question_order]=  $new_object->question_id;
			}
					
            //overload questions list with recorded questions list
            //load questions only for exercises of type 'one question per page'
            //this is needed only is there is no questions
            //
            // @todo not sure were in the code this is used somebody mess with the exercise 
            global $_configuration, $questionList;
            if ($this->type == ONE_PER_PAGE && $_configuration['live_exercise_tracking'] && $_SERVER['REQUEST_METHOD'] != 'POST' && defined('QUESTION_LIST_ALREADY_LOGGED')) {
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
	function selectId() {
		return $this->id;
	}

	/**
	 * returns the exercise title
	 *
	 * @author - Olivier Brouckaert
	 * @return - string - exercise title
	 */
	function selectTitle() {
		return $this->exercise;
	}

	/**
	 * returns the number of attempts setted
	 *
	 * @return - numeric - exercise attempts
	 */
	function selectAttempts() {
		return $this->attempts;
	}

	/** returns the number of FeedbackType  *
	 *  0=>Feedback , 1=>DirectFeedback, 2=>NoFeedback
	 * @return - numeric - exercise attempts
	 */
	function selectFeedbackType() {
		return $this->feedbacktype;
	}


	/**
	 * returns the time limit
	 */
	function selectTimeLimit() {
		return $this->timeLimit;
	}

	/**
	 * returns the exercise description
	 *
	 * @author - Olivier Brouckaert
	 * @return - string - exercise description
	 */
	function selectDescription() {
		return $this->description;
	}

	/**
	 * returns the exercise sound file
	 *
	 * @author - Olivier Brouckaert
	 * @return - string - exercise description
	 */
	function selectSound() {
		return $this->sound;
	}

	/**
	 * returns the exercise type
	 *
	 * @author - Olivier Brouckaert
	 * @return - integer - exercise type
	 */
	function selectType() {
		return $this->type;
	}

	/**
	 * tells if questions are selected randomly, and if so returns the draws
	 *
	 * @author - Carlos Vargas
	 * @return - integer - results disabled exercise
	 */
	function selectResultsDisabled() {
		return $this->results_disabled;
	}

	/**
	 * tells if questions are selected randomly, and if so returns the draws
	 *
	 * @author - Olivier Brouckaert
	 * @return - integer - 0 if not random, otherwise the draws
	 */
	function isRandom() {
		if($this->random > 0){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * returns random answers status.
	 *
	 * @author - Juan Carlos Ra�a
	 */
	function selectRandomAnswers() {
		$this->random_answers;
		return $this->random_answers;
	}


	/**
	 * Same as isRandom() but has a name applied to values different than 0 or 1
	 */
	function getShuffle() {
		return $this->random;
	}

	/**
	 * returns the exercise status (1 = enabled ; 0 = disabled)
	 *
	 * @author - Olivier Brouckaert
	 * @return - boolean - true if enabled, otherwise false
	 */
	function selectStatus() {
		return $this->active;
	}

	/**
	 * returns the array with the question ID list
	 *
	 * @author - Olivier Brouckaert
	 * @return - array - question ID list
	 */
	function selectQuestionList() {
		return $this->questionList;
	}

	/**
	 * returns the number of questions in this exercise
	 *
	 * @author - Olivier Brouckaert
	 * @return - integer - number of questions
	 */
	function selectNbrQuestions() {
		return sizeof($this->questionList);
	}
	
    function selectPropagateNeg() {
		return $this->propagate_neg;
	}

	/**
     * Selects questions randomly in the question list
	 *
	 * @author - Olivier Brouckaert
	 * @return - array - if the exercise is not set to take questions randomly, returns the question list
	 *					 without randomizing, otherwise, returns the list with questions selected randomly
     */
	function selectRandomList() {
		$nbQuestions	= $this->selectNbrQuestions();
		$temp_list		= $this->questionList;
		
		//Not a random exercise, or if there are not at least 2 questions
		if($this->random == 0 || $nbQuestions < 2) {
			return $this->questionList;
		}
		 
		if ($nbQuestions != 0) {
			shuffle($temp_list);
			$my_random_list = array_combine(range(1,$nbQuestions),$temp_list);			
			$my_question_list = array();
			$i = 0;
			foreach ($my_random_list as $item) {
	            if ($i < $this->random) {
	                $my_question_list[$i] = $item;
	            } else {
	                break;
	            }
	            $i++;
	        }
			return $my_question_list;
		}	
	}

	/**
	 * returns 'true' if the question ID is in the question list
	 *
	 * @author - Olivier Brouckaert
	 * @param - integer $questionId - question ID
	 * @return - boolean - true if in the list, otherwise false
	 */
	function isInList($questionId) {
		if (is_array($this->questionList))
			return in_array($questionId,$this->questionList);
		else
			return false;
	}

	/**
	 * changes the exercise title
	 *
	 * @author - Olivier Brouckaert
	 * @param - string $title - exercise title
	 */
	function updateTitle($title) {
		$this->exercise=$title;
	}

	/**
	 * changes the exercise max attempts
	 *
	 * @param - numeric $attempts - exercise max attempts
	 */
	function updateAttempts($attempts) {
		$this->attempts=$attempts;
	}


	/**
	 * changes the exercise feedback type
	 *
	 * @param - numeric $attempts - exercise max attempts
	 */
	function updateFeedbackType($feedback_type) {
		$this->feedbacktype=$feedback_type;
	}

	/**
	 * changes the exercise description
	 *
	 * @author - Olivier Brouckaert
	 * @param - string $description - exercise description
	 */
	function updateDescription($description) {
		$this->description=$description;
	}

	/**
	* changes the exercise expired_time
	*
	* @author - Isaac flores
	* @param - int The expired time of the quiz
	*/
	function updateExpiredTime($expired_time) {
	    $this->expired_time = $expired_time;
	}
	
	function updatePropagateNegative($value) {
	    $this->propagate_neg = $value;
	}

	/**
	 * changes the exercise sound file
	 *
	 * @author - Olivier Brouckaert
	 * @param - string $sound - exercise sound file
	 * @param - string $delete - ask to delete the file
	 */
	function updateSound($sound,$delete) {
		global $audioPath, $documentPath;
        $TBL_DOCUMENT = Database::get_course_table(TABLE_DOCUMENT, $this->course['db_name']);
        $TBL_ITEM_PROPERTY = Database::get_course_table(TABLE_ITEM_PROPERTY,$this->course['db_name']);

		if ($sound['size'] && (strstr($sound['type'],'audio') || strstr($sound['type'],'video'))) {
			$this->sound=$sound['name'];

			if (@move_uploaded_file($sound['tmp_name'],$audioPath.'/'.$this->sound)) {
				$query="SELECT 1 FROM $TBL_DOCUMENT "
            ." WHERE path='".str_replace($documentPath,'',$audioPath).'/'.$this->sound."'";
				$result=Database::query($query);

				if(!Database::num_rows($result)) {
			        /*$query="INSERT INTO $TBL_DOCUMENT(path,filetype) VALUES "
			            ." ('".str_replace($documentPath,'',$audioPath).'/'.$this->sound."','file')";
			        Database::query($query);*/
			        $id = add_document($this->course,str_replace($documentPath,'',$audioPath).'/'.$this->sound,'file',$sound['size'],$sound['name']);
			
			        //$id = Database::insert_id();
			        //$time = time();
			        //$time = date("Y-m-d H:i:s", $time);
			        // insert into the item_property table, using default visibility of "visible"
			        /*$query = "INSERT INTO $TBL_ITEM_PROPERTY "
			                ."(tool, ref, insert_user_id,to_group_id, insert_date, lastedit_date, lastedit_type) "
			                ." VALUES "
			                ."('".TOOL_DOCUMENT."', $id, $_user['user_id'], 0, '$time', '$time', 'DocumentAdded' )";
			        Database::query($query);*/
			        api_item_property_update($this->course, TOOL_DOCUMENT, $id, 'DocumentAdded',api_get_user_id());
			        item_property_update_on_folder($this->course,str_replace($documentPath,'',$audioPath),api_get_user_id());
				}
			}
		} elseif($delete && is_file($audioPath.'/'.$this->sound)) {
			$this->sound='';
		}
	}

	/**
	 * changes the exercise type
	 *
	 * @author - Olivier Brouckaert
	 * @param - integer $type - exercise type
	 */
	function updateType($type) {
		$this->type=$type;
	}

	/**
	 * sets to 0 if questions are not selected randomly
	 * if questions are selected randomly, sets the draws
	 *
	 * @author - Olivier Brouckaert
	 * @param - integer $random - 0 if not random, otherwise the draws
	 */
	function setRandom($random) {
		$this->random=$random;
	}


	/**
	 * sets to 0 if answers are not selected randomly
	 * if answers are selected randomly
	 * @author - Juan Carlos Ra�a
	 * @param - integer $random_answers - random answers
	 */
	function updateRandomAnswers($random_answers) {
		$this->$random_answers = $random_answers;
	}

	/**
	 * enables the exercise
	 *
	 * @author - Olivier Brouckaert
	 */
	function enable() {
		$this->active=1;
	}

	/**
	 * disables the exercise
	 *
	 * @author - Olivier Brouckaert
	 */
	function disable() {
		$this->active=0;
	}

	function disable_results() {
		$this->results_disabled = true;
	}

	function enable_results()
	{
		$this->results_disabled = false;
	}
	function updateResultsDisabled($results_disabled) {
	    $this->results_disabled = intval($results_disabled);		
	}


	/**
	 * updates the exercise in the data base
	 *
	 * @author - Olivier Brouckaert
	 */
	function save($type_e='') {
		global $_course,$_user;
		$TBL_EXERCICES = Database::get_course_table(TABLE_QUIZ_TEST);
        $TBL_QUESTIONS = Database::get_course_table(TABLE_QUIZ_QUESTION);
        $TBL_QUIZ_QUESTION= Database::get_course_table(TABLE_QUIZ_TEST_QUESTION);


		$id 			= $this->id;
		$exercise 		= $this->exercise;
		$description 	= $this->description;
		$sound 			= $this->sound;
		$type 			= $this->type;
		$attempts 		= $this->attempts;
		$feedbacktype 	= $this->feedbacktype;
		$random 		= $this->random;
		$random_answers = $this->random_answers;
		$active 		= $this->active;
		$propagate_neg  = $this->propagate_neg;
		$session_id 	= api_get_session_id();    	

		if ($feedbacktype==1){
			$results_disabled = 1;
		} else {
			$results_disabled = intval($this->results_disabled);
		}
		
		$expired_time = intval($this->expired_time);
        $start_time = Database::escape_string(api_get_utc_datetime($this->start_time));
        $end_time 	= Database::escape_string(api_get_utc_datetime($this->end_time));

		// Exercise already exists
		if($id) {
			$sql="UPDATE $TBL_EXERCICES SET
						title='".Database::escape_string($exercise)."',
						description='".Database::escape_string($description)."'";

				if ($type_e != 'simple') {
						$sql .= ", sound='".Database::escape_string($sound)."',
						type='".Database::escape_string($type)."',
						random='".Database::escape_string($random)."',
						random_answers='".Database::escape_string($random_answers)."',
						active='".Database::escape_string($active)."',
						feedback_type='".Database::escape_string($feedbacktype)."',
						start_time='$start_time',end_time='$end_time',
						max_attempt='".Database::escape_string($attempts)."',
            			expired_time='".Database::escape_string($expired_time)."',
            			propagate_neg='".Database::escape_string($propagate_neg)."',
						results_disabled='".Database::escape_string($results_disabled)."'";
				}
			$sql .= " WHERE id='".Database::escape_string($id)."'";

			Database::query($sql);

			// update into the item_property table
			api_item_property_update($_course, TOOL_QUIZ, $id,'QuizUpdated',$_user['user_id']);

            if (api_get_setting('search_enabled')=='true') {
                $this -> search_engine_edit();
            }

		} else {// creates a new exercise
		//add condition by anonymous user

		/*if (!api_is_anonymous()) {
			//is course manager
			$cond1=Database::escape_string($exercise);
			$cond2=Database::escape_string($description);
		} else {
			//is anonymous user
			$cond1=Database::escape_string(Security::remove_XSS($exercise));
			$cond2=Database::escape_string(Security::remove_XSS(api_html_entity_decode($description),COURSEMANAGERLOWSECURITY));
		}*/
			$sql="INSERT INTO $TBL_EXERCICES (start_time, end_time, title, description, sound, type, random, random_answers,active, results_disabled, max_attempt, feedback_type, expired_time, session_id)
					VALUES(
						'$start_time','$end_time',
						'".Database::escape_string($exercise)."',
						'".Database::escape_string($description)."',
						'".Database::escape_string($sound)."',
						'".Database::escape_string($type)."',
						'".Database::escape_string($random)."',
						'".Database::escape_string($random_answers)."',
						'".Database::escape_string($active)."',
						'".Database::escape_string($results_disabled)."',
						'".Database::escape_string($attempts)."',
						'".Database::escape_string($feedbacktype)."',
						'".Database::escape_string($expired_time)."',
						'".Database::escape_string($session_id)."'
						)";
			Database::query($sql);
			$this->id=Database::insert_id();
        	// insert into the item_property table

        	api_item_property_update($_course, TOOL_QUIZ, $this->id,'QuizAdded',$_user['user_id']);
			if (api_get_setting('search_enabled')=='true' && extension_loaded('xapian')) {
				$this -> search_engine_save();
			}
		}

		// updates the question position
        $this->update_question_positions();
	}

    function update_question_positions() {
    	// updates the question position
        $TBL_QUIZ_QUESTION= Database::get_course_table(TABLE_QUIZ_TEST_QUESTION);
        foreach($this->questionList as $position=>$questionId) {
            //$sql="UPDATE $TBL_QUESTIONS SET position='".Database::escape_string($position)."' WHERE id='".Database::escape_string($questionId)."'";
            $sql="UPDATE $TBL_QUIZ_QUESTION SET question_order='".Database::escape_string($position)."' " .
                 "WHERE question_id='".Database::escape_string($questionId)."' and exercice_id=".Database::escape_string($this->id)."";
            Database::query($sql);

        }
    }

	/**
	 * moves a question up in the list
	 *
	 * @author - Olivier Brouckaert
 	 * @author - Julio Montoya (rewrote the code)
	 * @param - integer $id - question ID to move up
	 */
	function moveUp($id) {
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
	function moveDown($id) {
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
		foreach($this->questionList as $position=>$questionId) {
			$question_list[]=	$questionId;
		}
		$len=count($question_list);
		$orderlist=array_keys($this->questionList);

		for($i=0;$i<$len;$i++) {
			$questionId = $question_list[$i];
			if($questionId == $id) {
				$pos1=$orderlist[$i+1];
				$pos2 =$orderlist[$i];
				if(!$pos2) {
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
	function addToList($questionId) {
		// checks if the question ID is not in the list
		if(!$this->isInList($questionId)) {
			// selects the max position
			if(!$this->selectNbrQuestions()) {
				$pos=1;
			} else {
				if (is_array($this->questionList))
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
	function removeFromList($questionId) {
		// searches the position of the question ID in the list
		$pos=array_search($questionId,$this->questionList);

		// question not found
		if($pos === false) {
			return false;
		} else {
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
	function delete() {
		global $_course,$_user;
		$TBL_EXERCICES = Database::get_course_table(TABLE_QUIZ_TEST);
		$sql="UPDATE $TBL_EXERCICES SET active='-1' WHERE id='".Database::escape_string($this->id)."'";
		Database::query($sql);
		api_item_property_update($_course, TOOL_QUIZ, $this->id,'QuizDeleted',$_user['user_id']);

		if (api_get_setting('search_enabled')=='true' && extension_loaded('xapian') ) {
			$this -> search_engine_delete();
		}
	}

	/**
	 * Creates the form to create / edit an exercise
	 * @param FormValidator $form the formvalidator instance (by reference)
	 */
	function createForm ($form, $type='full') {
		global $id;
		if(empty($type)){
			$type='full';
		}
		// form title
		if (!empty($_GET['exerciseId'])) {
			$form_title = get_lang('ModifyExercise');
		} else {
			$form_title = get_lang('NewEx');
		}
		$form->addElement('header', '', $form_title);
		// title
		$form -> addElement('text', 'exerciseTitle', get_lang('ExerciseName'),'class="input_titles" id="exercise_title"');
		//$form->applyFilter('exerciseTitle','html_filter');

		$form -> addElement('html','<div class="row">
		<div class="label"></div>
		<div class="formw" style="height:50px">
			<a href="javascript://" onclick=" return show_media()"> <span id="media_icon"> <img style="vertical-align: middle;" src="../img/looknfeel.png" alt="" />&nbsp;'.get_lang('ExerciseDescription').'</span></a>
		</div>
		</div>');

		$editor_config = array('ToolbarSet' => 'TestQuestionDescription', 'Width' => '100%', 'Height' => '150');
		if(is_array($type)){
			$editor_config = array_merge($editor_config, $type);
		}

		$form -> addElement ('html','<div class="HideFCKEditor" id="HiddenFCKexerciseDescription" >');
		$form -> add_html_editor('exerciseDescription', get_lang('langExerciseDescription'), false, false, $editor_config);
		$form -> addElement ('html','</div>');


		$form -> addElement('html','<div class="row">
			<div class="label">&nbsp;</div>
			<div class="formw">
				<a href="javascript://" onclick=" return advanced_parameters()"><span id="img_plus_and_minus"><div style="vertical-align:top;" ><img style="vertical-align:middle;" src="../img/div_show.gif" alt="" />&nbsp;'.get_lang('AdvancedParameters').'</div></span></a>
			</div>
			</div>');

		// Random questions
		$form -> addElement('html','<div id="options" style="display:none">');

		if($type=='full') {
			// feedback type
			$radios_feedback = array();
			$radios_feedback[] = FormValidator :: createElement ('radio', 'exerciseFeedbackType', null, get_lang('ExerciseAtTheEndOfTheTest'),'0',array('id' =>'exerciseType_1','onclick' => 'check_feedback()'));
			$radios_feedback[] = FormValidator :: createElement ('radio', 'exerciseFeedbackType', null, get_lang('NoFeedback'),'2',array('id' =>'exerciseType_2'));
			$form -> addGroup($radios_feedback, null, get_lang('FeedbackType'));

			$feedback_option[0]=get_lang('ExerciseAtTheEndOfTheTest');
			$feedback_option[1]=get_lang('DirectFeedback');
			$feedback_option[2]=get_lang('NoFeedback');

			//Can't modify a DirectFeedback question
			if ($this->selectFeedbackType() != 1 ) {
			//	$form -> addElement('select', 'exerciseFeedbackType',get_lang('FeedbackType'),$feedback_option,'onchange="javascript:feedbackselection()"');
				// test type
				$radios = array();
				$radios[] = FormValidator :: createElement ('radio', 'exerciseType', null, get_lang('QuestionsPerPageOne'),'2','onclick = "check_per_page_one() " ');
				$radios[] = FormValidator :: createElement ('radio', 'exerciseType', null, get_lang('QuestionsPerPageAll'),'1',array('onclick' => 'check_per_page_all()', 'id'=>'OptionPageAll'));

				$form -> addGroup($radios, null, get_lang('QuestionsPerPage'));
			} else {
				// if is Directfeedback but has not questions we can allow to modify the question type
				if ($this->selectNbrQuestions()== 0) {
					$form -> addElement('select', 'exerciseFeedbackType',get_lang('FeedbackType'),$feedback_option,'onchange="javascript:feedbackselection()"');
					// test type
					$radios = array();
					$radios[] = FormValidator :: createElement ('radio', 'exerciseType', null, get_lang('SimpleExercise'),'1');
					$radios[] = FormValidator :: createElement ('radio', 'exerciseType', null, get_lang('SequentialExercise'),'2');
					$form -> addGroup($radios, null, get_lang('ExerciseType'));
				} else {
					//we force the options to the DirectFeedback exercisetype
					$form -> addElement('hidden', 'exerciseFeedbackType','1');
					$form -> addElement('hidden', 'exerciseType','2');
				}
			}

			$radios_results_disabled = array();
			$radios_results_disabled[] = FormValidator :: createElement ('radio', 'results_disabled', null, get_lang('Yes'), '0', array('id'=>'result_disabled_0'));
			$radios_results_disabled[] = FormValidator :: createElement ('radio', 'results_disabled', null, get_lang('No'),  '1',array('id'=>'result_disabled_1','onclick' => 'check_results_disabled()'));
			$radios_results_disabled[] = FormValidator :: createElement ('radio', 'results_disabled', null, get_lang('OnlyShowScore'),  '2',array('id'=>'result_disabled_2','onclick' => 'check_results_disabled()'));
			$form -> addGroup($radios_results_disabled, null, get_lang('ShowResultsToStudents'));

			$random = array();
			$option=array();
			$max = ($this->id > 0) ? $this->selectNbrQuestions() : 10 ;
			$option = range(0,$max);
			$option[0]=get_lang('No');

			$random[] = FormValidator :: createElement ('select', 'randomQuestions',null,$option);
			$random[] = FormValidator :: createElement ('static', 'help','help','<span style="font-style: italic;">'.get_lang('RandomQuestionsHelp').'</span>');
			//$random[] = FormValidator :: createElement ('text', 'randomQuestions', null,null,'0');
			$form -> addGroup($random,null,get_lang('RandomQuestions'),'<br />');

			//random answers
			$radios_random_answers = array();
			$radios_random_answers[] = FormValidator :: createElement ('radio', 'randomAnswers', null, get_lang('Yes'),'1');
			$radios_random_answers[] = FormValidator :: createElement ('radio', 'randomAnswers', null, get_lang('No'),'0');
			$form -> addGroup($radios_random_answers, null, get_lang('RandomAnswers'));

			//Attempts
			$attempt_option=range(0,10);
			$attempt_option[0]=get_lang('Infinite');

			$form -> addElement('select', 'exerciseAttempts',get_lang('ExerciseAttempts'),$attempt_option);

			$form -> addElement('checkbox', 'enabletimelimit',get_lang('EnableTimeLimits'),null,'onclick = "  return timelimit() "');
		  	$var= Exercise::selectTimeLimit();

			if(($this -> start_time!='0000-00-00 00:00:00')||($this -> end_time!='0000-00-00 00:00:00'))
				$form -> addElement('html','<div id="options2" style="display:block;">');
			else
				$form -> addElement('html','<div id="options2" style="display:none;">');

	    	//$form -> addElement('date', 'start_time', get_lang('ExeStartTime'), array('language'=>'es','format' => 'dMYHi'));
	    	//$form -> addElement('date', 'end_time', get_lang('ExeEndTime'), array('language'=>'es','format' => 'dMYHi'));
	   		$form->addElement('datepicker', 'start_time', get_lang('ExeStartTime'), array('form_name'=>'exercise_admin'), 5);
			$form->addElement('datepicker', 'end_time', get_lang('ExeEndTime'), array('form_name'=>'exercise_admin'), 5);

     		//$form -> addElement('select', 'enabletimercontroltotalminutes',get_lang('ExerciseTimerControlMinutes'),$time_minutes_option);
      		$form -> addElement('html','</div>');


      		$check_option=$this -> selectType();

			if ($check_option==1 && isset($_GET['exerciseId'])) {
				$diplay = 'none';
			} else {
				$diplay = 'block';
			}
			
			$form -> addElement('checkbox', 'propagate_neg',get_lang('PropagateNegativeResults'),null);
			

    		$form -> addElement('html','<div id="divtimecontrol"  style="display:'.$diplay.';">');

			//Timer control
			$time_hours_option = range(0,12);
			$time_minutes_option = range(0,59);
			$form -> addElement('checkbox', 'enabletimercontrol',get_lang('EnableTimerControl'),null,array('onclick' =>'option_time_expired()','id'=>'enabletimercontrol','onload'=>'check_load_time()'));
			$expired_date = (int)$this->selectExpiredTime();

			if(($expired_date!='0')) {
			    $form -> addElement('html','<div id="timercontrol" style="display:block;">');
			} else {
			    $form -> addElement('html','<div id="timercontrol" style="display:none;">');
			}

			$form -> addElement('text', 'enabletimercontroltotalminutes',get_lang('ExerciseTotalDurationInMinutes'),array('style' => 'width : 35px','id' => 'enabletimercontroltotalminutes'));
			$form -> addElement('html','</div>');
			//$form -> addElement('text', 'exerciseAttempts', get_lang('ExerciseAttempts').' : ',array('size'=>'2'));
			
			
			

			$form -> addElement('html','</div>');  //End advanced setting
			$form -> addElement('html','</div>');

	        $defaults = array();

	        if (api_get_setting('search_enabled') === 'true') {
	            require_once(api_get_path(LIBRARY_PATH) . 'specific_fields_manager.lib.php');

	            $form -> addElement ('checkbox', 'index_document','', get_lang('SearchFeatureDoIndexDocument'));
	            $form -> addElement ('html','<br /><div class="row">');
	            $form -> addElement ('html', '<div class="label">'. get_lang('SearchFeatureDocumentLanguage') .'</div>');
	            $form -> addElement ('html', '<div class="formw">'. api_get_languages_combo() .'</div>');
	            $form -> addElement ('html','</div><div class="sub-form">');

	            $specific_fields = get_specific_field_list();
	            foreach ($specific_fields as $specific_field) {
	                $form -> addElement ('text', $specific_field['code'], $specific_field['name']);
	                $filter = array('course_code'=> "'". api_get_course_id() ."'", 'field_id' => $specific_field['id'], 'ref_id' => $this->id, 'tool_id' => '\''. TOOL_QUIZ .'\'');
	                $values = get_specific_field_values_list($filter, array('value'));
	                if ( !empty($values) ) {
	                    $arr_str_values = array();
	                    foreach ($values as $value) {
	                        $arr_str_values[] = $value['value'];
	                    }
	                    $defaults[$specific_field['code']] = implode(', ', $arr_str_values);
	                }
	            }
	            $form -> addElement ('html','</div>');
	        }
		}

		// submit
		isset($_GET['exerciseId'])?$text=get_lang('ModifyExercise'):$text=get_lang('ProcedToQuestions');
		$form -> addElement('html', '<br /><br />');
		$form -> addElement('style_submit_button', 'submitExercise', $text, 'class="save"');

		$form -> addRule ('exerciseTitle', get_lang('GiveExerciseName'), 'required');
		if($type=='full') {
			// rules
			$form -> addRule ('exerciseAttempts', get_lang('Numeric'), 'numeric');
			$form -> addRule ('start_time', get_lang('InvalidDate'), 'date');
	        $form -> addRule ('end_time', get_lang('InvalidDate'), 'date');
	        $form -> addRule(array ('start_time', 'end_time'), get_lang('StartDateShouldBeBeforeEndDate'), 'date_compare', 'lte');
		}

		// defaults
		if($type=='full') {
			if($this -> id > 0) {
				if ($this -> random > $this->selectNbrQuestions()) {
					$defaults['randomQuestions'] =  $this->selectNbrQuestions();
				} else {
					$defaults['randomQuestions'] = $this -> random;
				}
				$defaults['randomAnswers'] = $this ->selectRandomAnswers();
				$defaults['exerciseType'] = $this -> selectType();
				$defaults['exerciseTitle'] = $this -> selectTitle();
				$defaults['exerciseDescription'] = $this -> selectDescription();
				$defaults['exerciseAttempts'] = $this->selectAttempts();
				$defaults['exerciseFeedbackType'] = $this->selectFeedbackType();
				$defaults['results_disabled'] = $this->selectResultsDisabled();
				$defaults['propagate_neg'] = $this->selectPropagateNeg();

	  			if(($this -> start_time!='0000-00-00 00:00:00')||($this -> end_time!='0000-00-00 00:00:00'))
	            	$defaults['enabletimelimit'] = 1;

			    $defaults['start_time'] = ($this->start_time!='0000-00-00 00:00:00')? $this -> start_time : date('Y-m-d 12:00:00');
	        	$defaults['end_time'] = ($this->end_time!='0000-00-00 00:00:00')?$this -> end_time : date('Y-m-d 12:00:00',time()+84600);

				//Get expired time
				if($this -> expired_time != '0') {
				$defaults['enabletimercontrol'] = 1;
				$defaults['enabletimercontroltotalminutes'] = $this -> expired_time;
				} else {
				$defaults['enabletimercontroltotalminutes'] = 0;
				}

			} else {
				$defaults['exerciseType'] = 2;
				$defaults['exerciseAttempts'] = 0;
				$defaults['randomQuestions'] = 0;
				$defaults['randomAnswers'] = 0;
				$defaults['exerciseDescription'] = '';
				$defaults['exerciseFeedbackType'] = 0;
				$defaults['results_disabled'] = 0;

				$defaults['start_time'] = date('Y-m-d 12:00:00');
				$defaults['end_time'] = date('Y-m-d 12:00:00',time()+84600);
			}
		} else {
			$defaults['exerciseTitle'] = $this -> selectTitle();
			$defaults['exerciseDescription'] = $this -> selectDescription();
		}

        if (api_get_setting('search_enabled') === 'true') {
            $defaults['index_document'] = 'checked="checked"';
        }

		$form -> setDefaults($defaults);
	}

	/**
	 * function which process the creation of exercises
	 * @param FormValidator $form the formvalidator instance
	 */
	function processCreation($form, $type='') {

		$this -> updateTitle($form -> getSubmitValue('exerciseTitle'));
		$this -> updateDescription($form -> getSubmitValue('exerciseDescription'));
		$this -> updateAttempts($form -> getSubmitValue('exerciseAttempts'));
		$this -> updateFeedbackType($form -> getSubmitValue('exerciseFeedbackType'));
		$this -> updateType($form -> getSubmitValue('exerciseType'));
		$this -> setRandom($form -> getSubmitValue('randomQuestions'));
		$this -> updateRandomAnswers($form -> getSubmitValue('randomAnswers'));
		$this -> updateResultsDisabled($form -> getSubmitValue('results_disabled'));
    	$this -> updateExpiredTime($form -> getSubmitValue('enabletimercontroltotalminutes'));
    	
    	$this -> updatePropagateNegative($form -> getSubmitValue('propagate_neg'));

		if($form -> getSubmitValue('enabletimelimit')==1) {
           $start_time = $form -> getSubmitValue('start_time');
           $this->start_time = $start_time['Y'].'-'.$start_time['F'].'-'.$start_time['d'].' '.$start_time['H'].':'.$start_time['i'].':00';
           $end_time = $form -> getSubmitValue('end_time');
           $this->end_time = $end_time['Y'].'-'.$end_time['F'].'-'.$end_time['d'].' '.$end_time['H'].':'.$end_time['i'].':00';
    	} else {
           $this->start_time = '0000-00-00 00:00:00';
           $this->end_time = '0000-00-00 00:00:00';
        }

		if($form -> getSubmitValue('enabletimercontrol') == 1) {
		   $expired_total_time = $form -> getSubmitValue('enabletimercontroltotalminutes');
		   if ($this->expired_time == 0) {
			   $this->expired_time = $expired_total_time;
		   }
		} else {
			$this->expired_time = 0;
		}

		if($form -> getSubmitValue('randomAnswers') == 1) {
           $this->random_answers=1;
    	} else {
           $this->random_answers=0;
        }

		$this -> save($type);
	}

	function search_engine_save() {
		if ($_POST['index_document'] != 1) {
			return;
		}

		$course_id = api_get_course_id();

		require_once(api_get_path(LIBRARY_PATH) . 'search/DokeosIndexer.class.php');
		require_once(api_get_path(LIBRARY_PATH) . 'search/IndexableChunk.class.php');
		require_once(api_get_path(LIBRARY_PATH) . 'specific_fields_manager.lib.php');

		$specific_fields = get_specific_field_list();
		$ic_slide = new IndexableChunk();

	    $all_specific_terms = '';
	    foreach ($specific_fields as $specific_field) {
		    if (isset($_REQUEST[$specific_field['code']])) {
			    $sterms = trim($_REQUEST[$specific_field['code']]);
			    if (!empty($sterms)) {
				    $all_specific_terms .= ' '. $sterms;
				    $sterms = explode(',', $sterms);
				    foreach ($sterms as $sterm) {
					    $ic_slide->addTerm(trim($sterm), $specific_field['code']);
					    add_specific_field_value($specific_field['id'], $course_id, TOOL_QUIZ, $this->id, $sterm);
				    }
			    }
		    }
	    }

	    // build the chunk to index
	    $ic_slide->addValue("title", $this->exercise);
	    $ic_slide->addCourseId($course_id);
	    $ic_slide->addToolId(TOOL_QUIZ);
	    $xapian_data = array(
		    SE_COURSE_ID => $course_id,
			SE_TOOL_ID => TOOL_QUIZ,
			SE_DATA => array('type' => SE_DOCTYPE_EXERCISE_EXERCISE, 'exercise_id' => (int)$this->id),
			SE_USER => (int)api_get_user_id(),
	    );
	    $ic_slide->xapian_data = serialize($xapian_data);
	    $exercise_description = $all_specific_terms .' '. $this->description;
	    $ic_slide->addValue("content", $exercise_description);

	    $di = new DokeosIndexer();
	    isset($_POST['language'])? $lang=Database::escape_string($_POST['language']): $lang = 'english';
	    $di->connectDb(NULL, NULL, $lang);
	    $di->addChunk($ic_slide);

	    //index and return search engine document id
	    $did = $di->index();
	    if ($did) {
		    // save it to db
            $tbl_se_ref = Database::get_main_table(TABLE_MAIN_SEARCH_ENGINE_REF);
		    $sql = 'INSERT INTO %s (id, course_code, tool_id, ref_id_high_level, search_did)
			    VALUES (NULL , \'%s\', \'%s\', %s, %s)';
		    $sql = sprintf($sql, $tbl_se_ref, $course_id, TOOL_QUIZ, $this->id, $did);
		    Database::query($sql);
	    }

    }

    function search_engine_edit() {
        // update search enchine and its values table if enabled
        if (api_get_setting('search_enabled')=='true' && extension_loaded('xapian')) {
            $course_id = api_get_course_id();

            // actually, it consists on delete terms from db, insert new ones, create a new search engine document, and remove the old one
            // get search_did
            $tbl_se_ref = Database::get_main_table(TABLE_MAIN_SEARCH_ENGINE_REF);
            $sql = 'SELECT * FROM %s WHERE course_code=\'%s\' AND tool_id=\'%s\' AND ref_id_high_level=%s LIMIT 1';
            $sql = sprintf($sql, $tbl_se_ref, $course_id, TOOL_QUIZ, $this->id);
            $res = Database::query($sql);

            if (Database::num_rows($res) > 0) {
                require_once(api_get_path(LIBRARY_PATH) . 'search/DokeosIndexer.class.php');
                require_once(api_get_path(LIBRARY_PATH) . 'search/IndexableChunk.class.php');
                require_once(api_get_path(LIBRARY_PATH) . 'specific_fields_manager.lib.php');

                $se_ref = Database::fetch_array($res);
                $specific_fields = get_specific_field_list();
                $ic_slide = new IndexableChunk();

                $all_specific_terms = '';
                foreach ($specific_fields as $specific_field) {
                    delete_all_specific_field_value($course_id, $specific_field['id'], TOOL_QUIZ, $this->id);
                    if (isset($_REQUEST[$specific_field['code']])) {
                        $sterms = trim($_REQUEST[$specific_field['code']]);
                        $all_specific_terms .= ' '. $sterms;
                        $sterms = explode(',', $sterms);
                        foreach ($sterms as $sterm) {
                            $ic_slide->addTerm(trim($sterm), $specific_field['code']);
                            add_specific_field_value($specific_field['id'], $course_id, TOOL_QUIZ, $this->id, $sterm);
                        }
                    }
                }

                // build the chunk to index
                $ic_slide->addValue("title", $this->exercise);
                $ic_slide->addCourseId($course_id);
                $ic_slide->addToolId(TOOL_QUIZ);
                $xapian_data = array(
                    SE_COURSE_ID => $course_id,
                    SE_TOOL_ID => TOOL_QUIZ,
                    SE_DATA => array('type' => SE_DOCTYPE_EXERCISE_EXERCISE, 'exercise_id' => (int)$this->id),
                    SE_USER => (int)api_get_user_id(),
                );
                $ic_slide->xapian_data = serialize($xapian_data);
                $exercise_description = $all_specific_terms .' '. $this->description;
                $ic_slide->addValue("content", $exercise_description);

                $di = new DokeosIndexer();
                isset($_POST['language'])? $lang=Database::escape_string($_POST['language']): $lang = 'english';
                $di->connectDb(NULL, NULL, $lang);
                $di->remove_document((int)$se_ref['search_did']);
                $di->addChunk($ic_slide);

                //index and return search engine document id
                $did = $di->index();
                if ($did) {
                    // save it to db
                    $sql = 'DELETE FROM %s WHERE course_code=\'%s\' AND tool_id=\'%s\' AND ref_id_high_level=\'%s\'';
                    $sql = sprintf($sql, $tbl_se_ref, $course_id, TOOL_QUIZ, $this->id);
                    Database::query($sql);
                    $sql = 'INSERT INTO %s (id, course_code, tool_id, ref_id_high_level, search_did)
                        VALUES (NULL , \'%s\', \'%s\', %s, %s)';
                    $sql = sprintf($sql, $tbl_se_ref, $course_id, TOOL_QUIZ, $this->id, $did);
                    Database::query($sql);
                }

            }
        }

    }

    function search_engine_delete() {
	    // remove from search engine if enabled
	    if (api_get_setting('search_enabled') == 'true' && extension_loaded('xapian') ) {
		    $course_id = api_get_course_id();
		    $tbl_se_ref = Database::get_main_table(TABLE_MAIN_SEARCH_ENGINE_REF);
		    $sql = 'SELECT * FROM %s WHERE course_code=\'%s\' AND tool_id=\'%s\' AND ref_id_high_level=%s AND ref_id_second_level IS NULL LIMIT 1';
		    $sql = sprintf($sql, $tbl_se_ref, $course_id, TOOL_QUIZ, $this->id);
		    $res = Database::query($sql);
		    if (Database::num_rows($res) > 0) {
			    $row = Database::fetch_array($res);
			    require_once(api_get_path(LIBRARY_PATH) .'search/DokeosIndexer.class.php');
			    $di = new DokeosIndexer();
			    $di->remove_document((int)$row['search_did']);
                unset($di);
                $tbl_quiz_question = Database::get_course_table(TABLE_QUIZ_QUESTION);
                foreach ( $this->questionList as $question_i) {
                    $sql = 'SELECT type FROM %s WHERE id=%s';
                    $sql = sprintf($sql, $tbl_quiz_question, $question_i);
                    $qres = Database::query($sql);
                    if (Database::num_rows($qres) > 0) {
                        $qrow = Database::fetch_array($qres);
                        $objQuestion = Question::getInstance($qrow['type']);
                        $objQuestion = Question::read((int)$question_i);
                        $objQuestion->search_engine_edit($this->id, FALSE, TRUE);
                        unset($objQuestion);
                    }
                }
		    }
		    $sql = 'DELETE FROM %s WHERE course_code=\'%s\' AND tool_id=\'%s\' AND ref_id_high_level=%s AND ref_id_second_level IS NULL LIMIT 1';
		    $sql = sprintf($sql, $tbl_se_ref, $course_id, TOOL_QUIZ, $this->id);
		    Database::query($sql);

		    // remove terms from db
            require_once(api_get_path(LIBRARY_PATH) .'specific_fields_manager.lib.php');
		    delete_all_values_for_item($course_id, TOOL_QUIZ, $this->id);
	    }
    }
	function selectExpiredTime() {
  	   return $this->expired_time;
	}

	/**
	* Cleans the student's results only for the Exercise tool (Not from the LP)
	* The LP results are NOT deleted
	* Works with exercises in sessions
	* @return int quantity of user's exercises deleted
	*/
	function clean_results() {
		$table_track_e_exercises = Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
		$table_track_e_attempt   = Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);

		$sql_select = "SELECT exe_id FROM $table_track_e_exercises
					   WHERE exe_cours_id = '".api_get_course_id()."' AND exe_exo_id = ".$this->id." AND orig_lp_id = 0 AND orig_lp_item_id = 0 AND session_id = ".api_get_session_id()."";

		$result   = Database::query($sql_select);
		$exe_list = Database::store_result($result);

		//deleting TRACK_E_ATTEMPT table
		$i = 0;
		if (is_array($exe_list) && count($exe_list) > 0) {
			foreach($exe_list as $item) {
				$sql = "DELETE FROM $table_track_e_attempt WHERE exe_id = '".$item['exe_id']."'";
				Database::query($sql);
				$i++;
			}
		}

		//delete TRACK_E_EXERCICES table
		$sql = "DELETE FROM $table_track_e_exercises
				WHERE exe_cours_id = '".api_get_course_id()."' AND exe_exo_id = ".$this->id." AND orig_lp_id = 0 AND orig_lp_item_id = 0 AND session_id = ".api_get_session_id()."";
		Database::query($sql);
		return $i;
	}

	/**
	 * Copies an exercise (duplicate all questions and answers)
	*/

	public function copy_exercise() {
		$exercise_obj= new Exercise();
		$exercise_obj = $this;

	    $TBL_EXERCICE_QUESTION  = Database::get_course_table(TABLE_QUIZ_TEST_QUESTION);
    	$TBL_EXERCICES          = Database::get_course_table(TABLE_QUIZ_TEST);
	    $TBL_QUESTIONS          = Database::get_course_table(TABLE_QUIZ_QUESTION);

	    // force the creation of a new exercise
		$exercise_obj->updateTitle($exercise_obj->selectTitle().' - '.get_lang('Copy'));
		//Hides the new exercise
		$exercise_obj->updateStatus(false);
		$exercise_obj->updateId(0);
		$exercise_obj->save();

		$new_exercise_id = $exercise_obj->selectId();
		$question_list 	 = $exercise_obj->selectQuestionList();

		//Question creation
		foreach ($question_list as $old_question_id) {
			$old_question_obj = Question::read($old_question_id);
			$new_id = $old_question_obj->duplicate();

			$new_question_obj = Question::read($new_id);

			$new_question_obj->addToList($new_exercise_id);
			// This should be moved to the duplicate function
			$new_answer_obj = new Answer($old_question_id);
			$new_answer_obj->read();
			$new_answer_obj->duplicate($new_id);
		}
	}  

	/**
	 * Changes the exercise id
	 *
	 * @param - in $id - exercise id
	 */
	private function updateId($id) {
		$this->id = $id;
	}

	/**
	 * Changes the exercise status
	 *
	 * @param - string $status - exercise status
	 */
	function updateStatus($status) {
		$this->active = $status;
	}
	
	public function get_stat_track_exercise_info($lp_id = 0, $lp_item_id = 0, $lp_item_view_id = 0, $status = 'incomplete') {		
		$track_exercises = Database :: get_statistic_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
		if (empty($lp_id)) {
			$lp_id = 0;
		}		
		if (empty($lp_item_id)) {
			$lp_item_id = 0;
		}	
        if (empty($lp_item_view_id)) {
            $lp_item_view_id = 0;
        }   
		$condition = ' WHERE exe_exo_id 	= ' . "'" . $this->id . "'" .' AND 
					   exe_user_id 			= ' . "'" . api_get_user_id() . "'" . ' AND 
					   exe_cours_id 		= ' . "'" . api_get_course_id() . "'" . ' AND 
					   status 				= ' . "'" . Database::escape_string($status). "'" . ' AND 
					   orig_lp_id 			= ' . "'" . $lp_id . "'" . ' AND 
					   orig_lp_item_id 		= ' . "'" . $lp_item_id . "'" . ' AND
                       orig_lp_item_view_id = ' . "'" . $lp_item_view_id . "'" . ' AND
					   session_id 			= ' . "'" . api_get_session_id() . "' LIMIT 1"; //Adding limit 1 just in case
					   
		$sql_track = 'SELECT * FROM '.$track_exercises.$condition;
				
		$result = Database::query($sql_track);
		$new_array = array();
		if (Database::num_rows($result) > 0 ) {
			$new_array = Database::fetch_array($result, 'ASSOC');
		}			
		return $new_array;
	}
	
	
	public function save_stat_track_exercise_info($clock_expired_time = 0, $safe_lp_id = 0, $safe_lp_item_id = 0, $safe_lp_item_view_id = 0, $questionList = array()) {
		$track_exercises = Database :: get_statistic_table(TABLE_STATISTIC_TRACK_E_EXERCICES);		
		if (empty($safe_lp_id)) {
			$safe_lp_id = 0;
		}		
		if (empty($safe_lp_item_id)) {
			$safe_lp_item_id = 0;
		}
		if (empty($clock_expired_time)) {
			$clock_expired_time = 0;
		}		
		if ($this->expired_time != 0) {
        	$sql_fields = "expired_time_control, ";        	
        	$sql_fields_values = "'"."$clock_expired_time"."',";
        } else {
       		$sql_fields = "";
       		$sql_fields_values = "";
        }		
        if ($this->type == ONE_PER_PAGE) {
            $sql = "INSERT INTO $track_exercises($sql_fields exe_exo_id,exe_user_id,exe_cours_id,status,session_id,data_tracking,start_date,orig_lp_id,orig_lp_item_id)
                    VALUES($sql_fields_values '".$this->id."','" . api_get_user_id() . "','" . api_get_course_id() . "','incomplete','" . api_get_session_id() . "','" . implode(',', $questionList) . "','" . api_get_utc_datetime() . "',$safe_lp_id,$safe_lp_item_id)";
        } else {
            $sql = "INSERT INTO $track_exercises ($sql_fields exe_exo_id,exe_user_id,exe_cours_id,status,session_id,start_date,orig_lp_id,orig_lp_item_id)
                    VALUES($sql_fields_values '".$this->id."','" . api_get_user_id() . "','" . api_get_course_id() . "','incomplete','" . api_get_session_id() . "','" . api_get_utc_datetime() . "',$safe_lp_id,$safe_lp_item_id)";
        }
        Database::query($sql);
	}	
	
	public function show_button($nbrQuestions, $questionNum) {
		$html = '';
	    $html =  '<div style="margin-top:-10px;">';	
	    $confirmation_alert = $this->type == 1? " onclick=\"javascript:if(!confirm('".get_lang("ConfirmYourChoice")."')) return false;\" ":"";	    
	    $submit_btn = '<button class="next" type="submit" name="submit" name="submit_save" id="submit_save" '.$confirmation_alert.' >';
		$hotspot_get = $_POST['hotspot'];
				
	    if ($this->selectFeedbackType() == EXERCISE_FEEDBACK_TYPE_DIRECT && $this->type == ONE_PER_PAGE) {
	        $submit_btn = '';
	        $html .='<script src="' . api_get_path(WEB_LIBRARY_PATH) . 'javascript/thickbox.js" type="text/javascript"></script>';
	        $html .='<style type="text/css" media="all">@import "' . api_get_path(WEB_LIBRARY_PATH) . 'javascript/thickbox.css";</style>';
	    } else {	    	
	        if (api_is_allowed_to_session_edit() ) {	        	
	            if ($this->type == ALL_ON_ONE_PAGE || $nbrQuestions == $questionNum) {
	                $submit_btn .= get_lang('ValidateAnswer');
	       			$name_btn 	= get_lang('ValidateAnswer');
	            } else {
	                $submit_btn .= get_lang('NextQuestion');
	        		$name_btn = get_lang('NextQuestion');
	            }
	            $submit_btn .= '</button>';	            
				if ($this->expired_time != 0) {
			    	$html .= $submit_btn ='<button class="next" type="submit" id="submit_save" value="'.$name_btn.'" name="submit_save"/>'.$name_btn.'</button>';
			    } else {
			    	$html .= $submit_btn;
			    }
	        }
	    }
	    $html .= '</div>';  //margin top -10
	    return $html;	    
	}
	
	
	/**
	 * So the time control will work
	 */
	public function show_time_control_js($time_left) {
		$time_left = intval($time_left);
		return "<script type=\"text/javascript\">
	
			$(document).ready(function(){
	
			function get_expired_date_string(expired_time) {
		        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		        var day, month, year, hours, minutes, seconds, date_string;
		        var obj_date = new Date(expired_time);
		        day     = obj_date.getDate();
		        if (day < 10) day = '0' + day;
			        month   = obj_date.getMonth();
			        year    = obj_date.getFullYear();
			        hours   = obj_date.getHours();
		        if (hours < 10) hours = '0' + hours;
		        minutes = obj_date.getMinutes();
		        if (minutes < 10) minutes = '0' + minutes;
		        seconds = obj_date.getSeconds();
		        if (seconds < 10) seconds = '0' + seconds;
		        date_string = months[month] +' ' + day + ', ' + year + ' ' + hours + ':' + minutes + ':' + seconds;
		        return date_string;
		      }
	
	      function onExpiredTimeExercise() { 
	        $('#wrapper-clock').hide(); $('#exercise_form').hide();
	        $('#expired-message-id').show();
	        $('#exercise_form').submit();	     		
	      }
	
	      var current_time = new Date().getTime();
	      var time_left    = parseInt(".$time_left.");
	      var expired_time = current_time + (time_left*1000);
	      var expired_date = get_expired_date_string(expired_time);
	
	       $('#text-content').epiclock({
	         mode: EC_COUNTDOWN,
	         format: 'x{ : } i{ : } s{}',
	         target: expired_date,
	         onTimer: function(){ onExpiredTimeExercise(); }
	       }).clocks(EC_RUN);
	       
	       $('#submit_save').click(function () {});
	    });
	    </script>";
	}
	
	/**
	 * Lp javascript for hotspots
	 */
	public function show_lp_javascript() {
		
		return "<script type=\"text/javascript\" src=\"../plugin/hotspot/JavaScriptFlashGateway.js\"></script>
                    <script src=\"../plugin/hotspot/hotspot.js\" type=\"text/javascript\"></script>
                    <script language=\"JavaScript\" type=\"text/javascript\">
                    <!--
                    // -----------------------------------------------------------------------------
                    // Globals
                    // Major version of Flash required
                    var requiredMajorVersion = 7;
                    // Minor version of Flash required
                    var requiredMinorVersion = 0;
                    // Minor version of Flash required
                    var requiredRevision = 0;
                    // the version of javascript supported
                    var jsVersion = 1.0;
                    // -----------------------------------------------------------------------------
                    // -->
                    </script>
                    <script language=\"VBScript\" type=\"text/vbscript\">
                    <!-- // Visual basic helper required to detect Flash Player ActiveX control version information
                    Function VBGetSwfVer(i)
                      on error resume next
                      Dim swControl, swVersion
                      swVersion = 0

                      set swControl = CreateObject(\"ShockwaveFlash.ShockwaveFlash.\" + CStr(i))
                      if (IsObject(swControl)) then
                        swVersion = swControl.GetVariable(\"\$version\")
                      end if
                      VBGetSwfVer = swVersion
                    End Function
                    // -->
                    </script>

                    <script language=\"JavaScript1.1\" type=\"text/javascript\">
                    <!-- // Detect Client Browser type
                    var isIE  = (navigator.appVersion.indexOf(\"MSIE\") != -1) ? true : false;
                    var isWin = (navigator.appVersion.toLowerCase().indexOf(\"win\") != -1) ? true : false;
                    var isOpera = (navigator.userAgent.indexOf(\"Opera\") != -1) ? true : false;
                    jsVersion = 1.1;
                    // JavaScript helper required to detect Flash Player PlugIn version information
                    function JSGetSwfVer(i){
                        // NS/Opera version >= 3 check for Flash plugin in plugin array
                        if (navigator.plugins != null && navigator.plugins.length > 0) {
                            if (navigator.plugins[\"Shockwave Flash 2.0\"] || navigator.plugins[\"Shockwave Flash\"]) {
                                var swVer2 = navigator.plugins[\"Shockwave Flash 2.0\"] ? \" 2.0\" : \"\";
                                var flashDescription = navigator.plugins[\"Shockwave Flash\" + swVer2].description;
                                descArray = flashDescription.split(\" \");
                                tempArrayMajor = descArray[2].split(\".\");
                                versionMajor = tempArrayMajor[0];
                                versionMinor = tempArrayMajor[1];
                                if ( descArray[3] != \"\" ) {
                                    tempArrayMinor = descArray[3].split(\"r\");
                                } else {
                                    tempArrayMinor = descArray[4].split(\"r\");
                                }
                                versionRevision = tempArrayMinor[1] > 0 ? tempArrayMinor[1] : 0;
                                flashVer = versionMajor + \".\" + versionMinor + \".\" + versionRevision;
                            } else {
                                flashVer = -1;
                            }
                        }
                        // MSN/WebTV 2.6 supports Flash 4
                        else if (navigator.userAgent.toLowerCase().indexOf(\"webtv/2.6\") != -1) flashVer = 4;
                        // WebTV 2.5 supports Flash 3
                        else if (navigator.userAgent.toLowerCase().indexOf(\"webtv/2.5\") != -1) flashVer = 3;
                        // older WebTV supports Flash 2
                        else if (navigator.userAgent.toLowerCase().indexOf(\"webtv\") != -1) flashVer = 2;
                        // Can't detect in all other cases
                        else {

                            flashVer = -1;
                        }
                        return flashVer;
                    }
                    // When called with reqMajorVer, reqMinorVer, reqRevision returns true if that version or greater is available
                    function DetectFlashVer(reqMajorVer, reqMinorVer, reqRevision)
                    {
                        reqVer = parseFloat(reqMajorVer + \".\" + reqRevision);
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
                                    tempArray         = versionStr.split(\" \");
                                    tempString        = tempArray[1];
                                    versionArray      = tempString .split(\",\");
                                } else {
                                    versionArray      = versionStr.split(\".\");
                                }
                                versionMajor      = versionArray[0];
                                versionMinor      = versionArray[1];
                                versionRevision   = versionArray[2];

                                versionString     = versionMajor + \".\" + versionRevision;   // 7.0r24 == 7.24
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
                    </script>";
	}
	
	/**
     * This function was originally found in the exercise_show.php
     * @param   int     exe id
     * @param   int     question id
     * @param   int     the choice the user selected
     * @param   array   the hotspot coordinates $hotspot[$question_id] = coordinates
     * @param   string  function is called from 'exercise_show' or 'exercise_result'
     * @param   bool    save results in the DB or just show the reponse
     * @param   bool    gets information from DB or from the current selection
     * @param   bool    show results or not
     * @todo    reduce parameters of this function
     * @return  string  html code
	 */
	function manage_answer($exeId, $questionId, $choice, $from = 'exercise_show', $exerciseResultCoordinates = array(), $saved_results = true, $from_database = false, $show_result = true, $propagate_neg = 0) {        
		global $_configuration, $feedback_type;   
		
		$html = '';
		   
        $questionId   = intval($questionId);
		$exeId        = intval($exeId);        
        $TBL_TRACK_ATTEMPT      = Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
        $table_ans              = Database::get_course_table(TABLE_QUIZ_ANSWER);        
                    
     	// Creates a temporary Question object
        $objQuestionTmp         = Question :: read($questionId);

        $questionName 			= $objQuestionTmp->selectTitle();
        $questionDescription 	= $objQuestionTmp->selectDescription();
        $questionWeighting 		= $objQuestionTmp->selectWeighting();            
        $answerType 			= $objQuestionTmp->selectType();
        $quesId 				= $objQuestionTmp->selectId();        
        $extra                  = $objQuestionTmp->extra;
                 
        //Extra information of the question 
        if (!empty($extra)){
            $extra          = explode(':', $extra);            
            $true_score     = $extra[0];
            $false_score    = $extra[1];
            $doubt_score    = $extra[2];        	
        }        
        
        $totalWeighting 		= 0;
        $totalScore				= 0;

        // Destruction of the Question object
        unset ($objQuestionTmp);
        
        // Construction of the Answer object
        $objAnswerTmp = new Answer($questionId);
        $nbrAnswers = $objAnswerTmp->selectNbrAnswers();
        $questionScore = 0;
        if ($answerType == FREE_ANSWER) {
            $nbrAnswers = 1;
        }   
        $user_answer = '';             
        
                
        // Get answer list for matching
        $sql_answer = 'SELECT id, answer FROM '.$table_ans.' WHERE question_id="'.$questionId.'" ';
        $res_answer = Database::query($sql_answer);
        $answer_matching =array();
        while ($real_answer = Database::fetch_array($res_answer)) {
            $answer_matching[$real_answer['id']]= $real_answer['answer'];
        }        
        
		$real_answers = array();        
        $quiz_question_options = Question::readQuestionOption($questionId);
 
        for ($answerId = 1; $answerId <= $nbrAnswers; $answerId++) {
            $answer             = $objAnswerTmp->selectAnswer($answerId);
            $answerComment      = $objAnswerTmp->selectComment($answerId);
            $answerCorrect      = $objAnswerTmp->isCorrect($answerId);
            $answerWeighting    = $objAnswerTmp->selectWeighting($answerId);
            $numAnswer          = $objAnswerTmp->selectAutoId($answerId);
            
            switch ($answerType) {
                // for unique answer
                case UNIQUE_ANSWER :
                case UNIQUE_ANSWER_NO_OPTION :                 
                    if ($from_database) {
                    	$queryans = "SELECT answer FROM ".$TBL_TRACK_ATTEMPT." where exe_id = '".$exeId."' and question_id= '".$questionId."'";
                        $resultans = Database::query($queryans);
                        $choice = Database::result($resultans,0,"answer");
        
                        $numAnswer=$objAnswerTmp->selectAutoId($answerId);
        
                        $studentChoice=($choice == $numAnswer)?1:0;
                        if ($studentChoice) {
                            $questionScore+=$answerWeighting;
                            $totalScore+=$answerWeighting;
                        }
                    } else {
                    	$studentChoice=($choice == $numAnswer)?1:0;
                        if ($studentChoice) {
                            $questionScore+=$answerWeighting;
                            $totalScore+=$answerWeighting;
                        }
                    }               
                    break;
                    // for multiple answers
                case MULTIPLE_ANSWER_TRUE_FALSE :                    
                    if ($from_database) {
                        $choice=array();
                        $queryans = "SELECT answer FROM ".$TBL_TRACK_ATTEMPT." where exe_id = '".$exeId."' and question_id= '".$questionId."'";                                                
                        $resultans = Database::query($queryans);                        
                        while ($row = Database::fetch_array($resultans)) {
                            $ind          = $row['answer'];
                            $result       = explode(':',$ind);
                            $my_answer_id = $result[0];
                            $option       = $result[1];
                            $choice[$my_answer_id] = $option;                            
                        }                        
                        $numAnswer=$objAnswerTmp->selectAutoId($answerId);                        
                        $studentChoice  =$choice[$numAnswer];
                    } else {                             
                        $studentChoice  =$choice[$numAnswer];                   
                    }                    
                    
                    if ($studentChoice == $answerCorrect ) {
                        $questionScore  +=$true_score;
                    } else {                        
                        if ($quiz_question_options[$studentChoice]['name'] != 'DoubtScore') {
                           $questionScore  +=  $false_score;                      
                        } else {
                        	$questionScore  +=  $doubt_score;
                        }
                   }                  
                   $totalScore       +=$true_score;                                 
                   break;
                case MULTIPLE_ANSWER :                   
                    if ($from_database) {
                        $choice=array();
                        $queryans = "SELECT answer FROM ".$TBL_TRACK_ATTEMPT." where exe_id = '".$exeId."' and question_id= '".$questionId."'";                        
                        $resultans = Database::query($queryans);
                        while ($row = Database::fetch_array($resultans)) {
                            $ind = $row['answer'];
                            $choice[$ind] = 1;
                        }                        
                        $numAnswer=$objAnswerTmp->selectAutoId($answerId);
                        $studentChoice=$choice[$numAnswer];
                        if ($studentChoice) {
                            $questionScore  +=$answerWeighting;
                            $totalScore     +=$answerWeighting;
                        }
                    } else {
                        $studentChoice=$choice[$numAnswer];
                        if ($studentChoice) {
                            $questionScore  +=$answerWeighting;
                            $totalScore     +=$answerWeighting;
                        } 
                    }                   
                    break;
                case MULTIPLE_ANSWER_COMBINATION_TRUE_FALSE:
                    if ($from_database) {
                        $queryans = "SELECT answer FROM ".$TBL_TRACK_ATTEMPT." where exe_id = '".$exeId."' and question_id= '".$questionId."'";
                        $resultans = Database::query($queryans);
                        while ($row = Database::fetch_array($resultans)) {
                            $ind = $row['answer'];
                            $result = explode(':',$ind);
                            $my_answer_id = $result[0];
                            $option       = $result[1];
                            $choice[$my_answer_id] = $option;    
                        }     
                                             
                        $numAnswer=$objAnswerTmp->selectAutoId($answerId);
                        $studentChoice=$choice[$numAnswer];
                       
                        if ($answerCorrect == 1) {
                            if ($studentChoice == 1) { //true value see MultipleAnswerCombinationTrueFalse class
                                $real_answers[$answerId] = true;
                            } elseif ($studentChoice == 2) { //false value
                                $real_answers[$answerId] = false;
                            } else {
                            	$real_answers[$answerId] = false;
                            }
                        } else {
                            if ($studentChoice == 1) { //true value
                                $real_answers[$answerId] = false;
                            } elseif ($studentChoice == 2) { //false value see MultipleAnswerCombinationTrueFalse class
                                $real_answers[$answerId] = true;                            
                            } else {
                                $real_answers[$answerId] = true;
                            }
                        }
                                  
                    } else {                        
                        $studentChoice=$choice[$numAnswer];
                          if ($answerCorrect == 1) {
                            if ($studentChoice == 1) { //true value see MultipleAnswerCombinationTrueFalse class
                                $real_answers[$answerId] = true;
                            } elseif ($studentChoice == 2) { //false value
                                $real_answers[$answerId] = false;
                            } else {
                                $real_answers[$answerId] = false;
                            }
                        } else {
                            if ($studentChoice == 1) { //true value
                                $real_answers[$answerId] = false;
                            } elseif ($studentChoice == 2) { //false value see MultipleAnswerCombinationTrueFalse class
                                $real_answers[$answerId] = true;                            
                            } else {
                                $real_answers[$answerId] = true;
                            }
                        }
                        $final_answer = true;
                        foreach($real_answers as $my_answer) {
                            if (!$my_answer) {
                                $final_answer = false;
                            }
                        }
                    }                    
                break;
                case MULTIPLE_ANSWER_COMBINATION:                
                    if ($from_database) {
                        $queryans = "SELECT answer FROM ".$TBL_TRACK_ATTEMPT." where exe_id = '".$exeId."' and question_id= '".$questionId."'";
                        $resultans = Database::query($queryans);
                        while ($row = Database::fetch_array($resultans)) {
                            $ind = $row['answer'];
                            $choice[$ind] = 1;
                        }
                        $numAnswer=$objAnswerTmp->selectAutoId($answerId);
                        $studentChoice=$choice[$numAnswer];
        
                        if ($answerCorrect == 1) {
                            if ($studentChoice) {
                                $real_answers[$answerId] = true;
                            } else {
                                $real_answers[$answerId] = false;
                            }
                        } else {
                            if ($studentChoice) {
                                $real_answers[$answerId] = false;
                            } else {
                                $real_answers[$answerId] = true;
                            }
                        }                        
                    } else {
                        $studentChoice=$choice[$numAnswer];
                        if ($answerCorrect == 1) {
                            if ($studentChoice) {
                                $real_answers[$answerId] = true;
                            } else {
                                $real_answers[$answerId] = false;
                            }
                        } else {
                            if ($studentChoice) {
                                $real_answers[$answerId] = false;
                            } else {
                                $real_answers[$answerId] = true;
                            }
                        }
                        $final_answer = true;
                        foreach($real_answers as $my_answer) {
                            if (!$my_answer) {
                                $final_answer = false;
                            }
                        }
                    }
                    break;
                    // for fill in the blanks
                case FILL_IN_BLANKS :                
                    // the question is encoded like this
                    // [A] B [C] D [E] F::10,10,10@1
                    // number 1 before the "@" means that is a switchable fill in blank question
                    // [A] B [C] D [E] F::10,10,10@ or  [A] B [C] D [E] F::10,10,10
                    // means that is a normal fill blank question
                    // first we explode the "::"
                    $pre_array = explode('::', $answer);
                    // is switchable fill blank or not
                    $last = count($pre_array) - 1;
                    $is_set_switchable = explode('@', $pre_array[$last]);
                    $switchable_answer_set = false;
                    if (isset ($is_set_switchable[1]) && $is_set_switchable[1] == 1) {
                        $switchable_answer_set = true;
                    }
                    $answer = '';
                    for ($k = 0; $k < $last; $k++) {
                        $answer .= $pre_array[$k];
                    }
                    // splits weightings that are joined with a comma
                    $answerWeighting = explode(',', $is_set_switchable[0]);

                    // we save the answer because it will be modified
                    //$temp = $answer;
                    $temp = text_filter($answer);

                    /* // Deprecated code
                    // TeX parsing
                    // 1. find everything between the [tex] and [/tex] tags
                    $startlocations = api_strpos($temp, '[tex]');
                    $endlocations = api_strpos($temp, '[/tex]');

                    if ($startlocations !== false && $endlocations !== false) {
                        $texstring = api_substr($temp, $startlocations, $endlocations - $startlocations +6);
                        // 2. replace this by {texcode}
                        $temp = str_replace($texstring, '{texcode}', $temp);
                    }
                    */
                    $answer = '';
                    $j = 0;
                    //initialise answer tags
                    $user_tags = array ();
                    $correct_tags = array ();
                    $real_text = array ();
                    // the loop will stop at the end of the text
                    while (1) {
                        // quits the loop if there are no more blanks (detect '[')
                        if (($pos = api_strpos($temp, '[')) === false) {
                            // adds the end of the text
                            $answer = $temp;
                            /* // Deprecated code
                            // TeX parsing - replacement of texcode tags
                            $texstring = api_parse_tex($texstring);
                            $answer = str_replace("{texcode}", $texstring, $answer);
                            */
                            $real_text[] = $answer;
                            break; //no more "blanks", quit the loop
                        }
                        // adds the piece of text that is before the blank
                        //and ends with '[' into a general storage array
                        $real_text[] = api_substr($temp, 0, $pos +1);
                        $answer .= api_substr($temp, 0, $pos +1);
                        //take the string remaining (after the last "[" we found)
                        $temp = api_substr($temp, $pos +1);
                        // quit the loop if there are no more blanks, and update $pos to the position of next ']'
                        if (($pos = api_strpos($temp, ']')) === false) {
                            // adds the end of the text
                            $answer .= $temp;
                            break;
                        }                        
                        if ($from_database) {
                            $queryfill = "SELECT answer FROM ".$TBL_TRACK_ATTEMPT." WHERE exe_id = '".Database::escape_string($exeId)."' AND question_id= '".Database::escape_string($questionId)."'";
                            $resfill = Database::query($queryfill);
                            $str = Database::result($resfill,0,'answer');
                            
                            preg_match_all('#\[([^[]*)\]#', $str, $arr);
                            $str = str_replace('\r\n', '', $str);
                            $choice = $arr[1];
    
                            $tmp=strrpos($choice[$j],' / ');
                            $choice[$j]=api_substr($choice[$j],0,$tmp);
                            $choice[$j]=trim($choice[$j]);
    
                            //Needed to let characters ' and " to work as part of an answer
                            $choice[$j] = stripslashes($choice[$j]);
                        } else {
                            $choice[$j] = trim($choice[$j]);	
                        }                       
                        
                        $user_tags[] = api_strtolower($choice[$j]);
                        //put the contents of the [] answer tag into correct_tags[]
                        $correct_tags[] = api_strtolower(api_substr($temp, 0, $pos));
                        $j++;
                        $temp = api_substr($temp, $pos +1);
                        //$answer .= ']';
                    }
                    $answer = '';
                    $real_correct_tags = $correct_tags;
                    $chosen_list = array ();

                    for ($i = 0; $i < count($real_correct_tags); $i++) {
                        if ($i == 0) {
                            $answer .= $real_text[0];
                        }
                        if (!$switchable_answer_set) {
                        	//needed to parse ' and " characters
                        	$user_tags[$i] = stripslashes($user_tags[$i]);
                            if ($correct_tags[$i] == $user_tags[$i]) {
                                // gives the related weighting to the student
                                $questionScore += $answerWeighting[$i];
                                // increments total score
                                $totalScore += $answerWeighting[$i];
                                // adds the word in green at the end of the string
                                $answer .= $correct_tags[$i];
                            }
                            // else if the word entered by the student IS NOT the same as the one defined by the professor
                            elseif (!empty ($user_tags[$i])) {
                                // adds the word in red at the end of the string, and strikes it
                                $answer .= '<font color="red"><s>' . $user_tags[$i] . '</s></font>';
                            } else {
                                // adds a tabulation if no word has been typed by the student
                                $answer .= '&nbsp;&nbsp;&nbsp;';
                            }
                        } else {
                            // switchable fill in the blanks
                            if (in_array($user_tags[$i], $correct_tags)) {
                                $chosen_list[] = $user_tags[$i];
                                $correct_tags = array_diff($correct_tags, $chosen_list);

                                // gives the related weighting to the student
                                $questionScore += $answerWeighting[$i];
                                // increments total score
                                $totalScore += $answerWeighting[$i];
                                // adds the word in green at the end of the string
                                $answer .= $user_tags[$i];
                            }
                            elseif (!empty ($user_tags[$i])) {
                                // else if the word entered by the student IS NOT the same as the one defined by the professor
                                // adds the word in red at the end of the string, and strikes it
                                $answer .= '<font color="red"><s>' . $user_tags[$i] . '</s></font>';
                            } else {
                                // adds a tabulation if no word has been typed by the student
                                $answer .= '&nbsp;&nbsp;&nbsp;';
                            }
                        }
                        // adds the correct word, followed by ] to close the blank
                        $answer .= ' / <font color="green"><b>' . $real_correct_tags[$i] . '</b></font>]';
                        if (isset ($real_text[$i +1])) {
                            $answer .= $real_text[$i +1];
                        }
                    }
                    break;
                    // for free answer
                case FREE_ANSWER :
                    if ($from_database) {                        
                        $query  = "SELECT answer, marks FROM ".$TBL_TRACK_ATTEMPT." WHERE exe_id = '".$exeId."' AND question_id= '".$questionId."'";
                        $resq   = Database::query($query);
                        $choice = Database::result($resq,0,'answer');
                        $choice = str_replace('\r\n', '', $choice);
                        $choice = stripslashes($choice);            
                        $questionScore = Database::result($resq,0,"marks");
                        if ($questionScore==-1) {
                            $totalScore+=0;
                        } else {
                            $totalScore+=$questionScore;
                        }
                        $arrques[] = $questionName;
                        $arrans[]  = $choice;   
                    } else {
                        $studentChoice = $choice;
                        if ($studentChoice) {
                            //Fixing negative puntation see #2193 
                            $questionScore = 0;
                            $totalScore += 0;
                        }                        
                    }
                    break;
                    // for matching
                case MATCHING :   
                    if ($from_database) {                                  
                        $sql_answer = 'SELECT id, answer FROM '.$table_ans.' WHERE question_id="'.$questionId.'" AND correct=0';
                        $res_answer = Database::query($sql_answer);
                        // getting the real answer
                        $real_list =array();
                        while ($real_answer = Database::fetch_array($res_answer)) {
                            $real_list[$real_answer['id']]= $real_answer['answer'];
                        }
            
                        $sql_select_answer = 'SELECT id, answer, correct, id_auto FROM '.$table_ans.'
                                              WHERE question_id="'.$questionId.'" AND correct <> 0 ORDER BY id_auto';
            
                        $res_answers = Database::query($sql_select_answer);
                        
                   
                        $questionScore = 0;
            
                        while ($a_answers = Database::fetch_array($res_answers)) {        
                            $i_answer_id    = $a_answers['id']; //3
                            $s_answer_label = $a_answers['answer'];  // your daddy - your mother
                            $i_answer_correct_answer = $a_answers['correct']; //1 - 2
                            $i_answer_id_auto = $a_answers['id_auto']; // 3 - 4
            
                            $sql_user_answer = "SELECT answer FROM $TBL_TRACK_ATTEMPT
                                                WHERE exe_id = '$exeId' AND question_id = '$questionId' AND position='$i_answer_id_auto'";
            
                            $res_user_answer = Database::query($sql_user_answer);
            
                            if (Database::num_rows($res_user_answer)>0 ) {
                                $s_user_answer = Database::result($res_user_answer,0,0); //  rich - good looking
                            } else {
                                $s_user_answer = 0;
                            }            
                            $i_answerWeighting=$objAnswerTmp->selectWeighting($i_answer_id);            
                            $user_answer = '';            
                            if (!empty($s_user_answer)) {
                                if ($s_user_answer == $i_answer_correct_answer) {
                                    $questionScore  += $i_answerWeighting;
                                    $totalScore     += $i_answerWeighting;
                                    $user_answer = '<span>'.$real_list[$i_answer_correct_answer].'</span>';
                                } else {
                                    $user_answer = '<span style="color: #FF0000; text-decoration: line-through;">'.$real_list[$s_user_answer].'</span>';
                                }
                            }
                            if ($show_result) {
                                echo '<tr>';
                                echo '<td>'.$s_answer_label.'</td><td>'.$user_answer.' / <b><span style="color: #008000;">'.$real_list[$i_answer_correct_answer].'</span></b></td>';
                                echo '</tr>';
                            }
                        }
                        break(2); //break the switch and the "for" condition                    
                    } else {                   
                        $numAnswer=$objAnswerTmp->selectAutoId($answerId);
                        if ($answerCorrect) {
                            if ($answerCorrect == $choice[$numAnswer]) {
                                $questionScore  += $answerWeighting;
                                $totalScore     += $answerWeighting;
                                $user_answer = '<span>'.$answer_matching[$choice[$numAnswer]].'</span>';
                            } else {
                                $user_answer = '<span style="color: #FF0000; text-decoration: line-through;">'.$answer_matching[$choice[$numAnswer]].'</span>';                    
                            }         
                            $matching[$numAnswer] =  $choice[$numAnswer];
                        }
                        break;
                    }  
                    
                    // for hotspot with no order
                case HOT_SPOT :                         
                    if ($from_database) {                      
                        if ($show_result) {
                            $TBL_TRACK_HOTSPOT = Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_HOTSPOT);
                            $query = "SELECT hotspot_correct FROM ".$TBL_TRACK_HOTSPOT." where hotspot_exe_id = '".$exeId."' and hotspot_question_id= '".$questionId."' AND hotspot_answer_id='".Database::escape_string($answerId)."'";
                            $resq=Database::query($query);
                            $studentChoice = Database::result($resq,0,"hotspot_correct");                                
                        }
                    }  else {
                        $studentChoice = $choice[$answerId];                        
                        if ($studentChoice) {
                            $questionScore  += $answerWeighting;
                            $totalScore     += $answerWeighting;
                        }
                    }
                    break;
                    // @todo never added to chamilo 
                    //for hotspot with fixed order
                case HOT_SPOT_ORDER :
                    $studentChoice = $choice['order'][$answerId];
                    if ($studentChoice == $answerId) {
                        $questionScore  += $answerWeighting;
                        $totalScore     += $answerWeighting;
                        $studentChoice = true;
                    } else {
                        $studentChoice = false;
                    }
                    break;
            } // end switch Answertype            
            
            global $origin;   
                   
            if ($show_result) {
                          
                if ($from == 'exercise_result') {       
                     //display answers (if not matching type, or if the answer is correct)                        
                    if ($answerType != MATCHING || $answerCorrect) {
                        if (in_array($answerType, array(UNIQUE_ANSWER, UNIQUE_ANSWER_NO_OPTION, MULTIPLE_ANSWER, MULTIPLE_ANSWER_COMBINATION))) {
                            if ($origin!='learnpath') {
                                ExerciseShowFunctions::display_unique_or_multiple_answer($answerType, $studentChoice, $answer, $answerComment, $answerCorrect,0,0,0);
                            }
                        } elseif($answerType == MULTIPLE_ANSWER_TRUE_FALSE) {
                            if ($origin!='learnpath') {
                                ExerciseShowFunctions::display_multiple_answer_true_false($answerType, $studentChoice, $answer, $answerComment, $answerCorrect,0,$questionId,0);
                            }                            
                        } elseif($answerType == MULTIPLE_ANSWER_COMBINATION_TRUE_FALSE ) {
                            if ($origin!='learnpath') {
                                ExerciseShowFunctions::display_multiple_answer_combination_true_false($answerType, $studentChoice, $answer, $answerComment, $answerCorrect,0,0,0);
                            }                            
                        } elseif($answerType == FILL_IN_BLANKS) {
                            if ($origin!='learnpath') {
                                ExerciseShowFunctions::display_fill_in_blanks_answer($answer,0,0);
                            }
                        } elseif($answerType == FREE_ANSWER) {
                            // to store the details of open questions in an array to be used in mail
                            $arrques[] = $questionName;
                            $arrans[]  = $choice;
                            if($origin != 'learnpath') {
                                ExerciseShowFunctions::display_free_answer($choice,0,0);
                            }
                        } elseif($answerType == HOT_SPOT) {                            
                            if ($origin != 'learnpath') {
                                ExerciseShowFunctions::display_hotspot_answer($answerId, $answer, $studentChoice, $answerComment);
                            }
                        } elseif($answerType == HOT_SPOT_ORDER) {
                            ExerciseShowFunctions::display_hotspot_order_answer($answerId, $answer, $studentChoice, $answerComment);
                        } elseif($answerType==MATCHING) {
                            if ($origin != 'learnpath') {
                                echo '<tr>';
                                echo '<td>'.text_filter($answer_matching[$answerId]).'</td><td>'.text_filter($user_answer).' / <b><span style="color: #008000;">'.text_filter($answer_matching[$answerCorrect]).'</span></b></td>';
                                echo '</tr>';
                            }
                        }
                    }
                } else { 
                    switch($answerType) {
                        case UNIQUE_ANSWER :
                        case UNIQUE_ANSWER_NO_OPTION: 
                        case MULTIPLE_ANSWER :                        
                    	case MULTIPLE_ANSWER_COMBINATION :                
                            if ($answerId==1) {                                
                                ExerciseShowFunctions::display_unique_or_multiple_answer($answerType, $studentChoice, $answer, $answerComment, $answerCorrect,$exeId,$questionId,$answerId);
                            } else {
                                ExerciseShowFunctions::display_unique_or_multiple_answer($answerType, $studentChoice, $answer, $answerComment, $answerCorrect,$exeId,$questionId,"");
                            }
                            break;   
                        case MULTIPLE_ANSWER_COMBINATION_TRUE_FALSE:                        
                            if ($answerId==1) {                                
                                ExerciseShowFunctions::display_multiple_answer_combination_true_false($answerType, $studentChoice, $answer, $answerComment, $answerCorrect,$exeId,$questionId,$answerId);
                            } else {
                                ExerciseShowFunctions::display_multiple_answer_combination_true_false($answerType, $studentChoice, $answer, $answerComment, $answerCorrect,$exeId,$questionId,"");
                            }                         
                            break;
                        case MULTIPLE_ANSWER_TRUE_FALSE :                        
                            if ($answerId==1) {                                
                                ExerciseShowFunctions::display_multiple_answer_true_false($answerType, $studentChoice, $answer, $answerComment, $answerCorrect,$exeId,$questionId,$answerId);
                            } else {
                                ExerciseShowFunctions::display_multiple_answer_true_false($answerType, $studentChoice, $answer, $answerComment, $answerCorrect,$exeId,$questionId,"");
                            }                            
                        break;
                        case FILL_IN_BLANKS:
                            echo '<tr><td>';
                            ExerciseShowFunctions::display_fill_in_blanks_answer($answer,$exeId,$questionId);
                            echo '</td></tr>';
                            break;
                        case FREE_ANSWER:
                            echo '<tr>
                            <td valign="top">'.ExerciseShowFunctions::display_free_answer($choice, $exeId, $questionId).'</td>
                            </tr>
                            </table>';
                            break;
                        case HOT_SPOT:
                            ExerciseShowFunctions::display_hotspot_answer($answerId, $answer, $studentChoice, $answerComment);
                            break;                    
                        case HOT_SPOT_ORDER:                            
                            ExerciseShowFunctions::display_hotspot_order_answer($answerId, $answer, $studentChoice, $answerComment);                                   
                        break;
                        case MATCHING:
                         if ($origin != 'learnpath') {
                                echo '<tr>';
                                echo '<td>'.text_filter($answer_matching[$answerId]).'</td><td>'.text_filter($user_answer).' / <b><span style="color: #008000;">'.text_filter($answer_matching[$answerCorrect]).'</span></b></td>';
                                echo '</tr>';
                            }                        
                        break;
                    }	  
                }
            }       
        } // end for that loops over all answers of the current question
        
        // destruction of Answer
        
        if (!$saved_results && $answerType == HOT_SPOT) {         
            $queryfree      = "SELECT marks FROM ".$TBL_TRACK_ATTEMPT." where exe_id = '".Database::escape_string($exeId)."' and question_id= '".Database::escape_string($questionId)."'";
            $resfree        = Database::query($queryfree);
            $questionScore  = Database::result($resfree,0,"marks");
        }

        $final_answer = true;
        foreach($real_answers as $my_answer) {
            if (!$my_answer) {
                $final_answer = false;
            }
        }        
        
        //we add the total score after dealing with the answers
        if ($answerType == MULTIPLE_ANSWER_COMBINATION || $answerType == MULTIPLE_ANSWER_COMBINATION_TRUE_FALSE ) {            
            if ($final_answer) {
                //getting only the first score where we save the weight of all the question
                $answerWeighting     = $objAnswerTmp->selectWeighting(1);
                $questionScore      += $answerWeighting;
                $totalScore         += $answerWeighting;
            }
        }        
       
       if ($from == 'exercise_result') {            
            // if answer is hotspot. To the difference of exercise_show.php, we use the results from the session (from_db=0)
            // TODO Change this, because it is wrong to show the user some results that haven't been stored in the database yet
            if ($answerType == HOT_SPOT || $answerType == HOT_SPOT_ORDER) {
                // We made an extra table for the answers
                 if ($show_result) {
                    if ($origin != 'learnpath') {
                                          
                        echo '</table></td></tr>';
                        echo '<tr>
                            <td colspan="2">';
                        echo '<i>'.get_lang('HotSpot').'</i><br /><br />';
                        echo '<object type="application/x-shockwave-flash" data="'.api_get_path(WEB_CODE_PATH).'plugin/hotspot/hotspot_solution.swf?modifyAnswers='.Security::remove_XSS($questionId).'&exe_id=&from_db=0" width="552" height="352">';
                        echo '<param name="movie" value="'.api_get_path(WEB_PLUGIN_PATH).'plugin/hotspot/hotspot_solution.swf?modifyAnswers='.Security::remove_XSS($questionId).'&exe_id=&from_db=0" />
                                </object>
                            </td>
                        </tr>';  
                                          
                    }
                 }
            }
            if ($origin != 'learnpath') { 
                if ($show_result) {            
                    echo '</table>'; 
                    if ($this->type == ALL_ON_ONE_PAGE) {
                        echo '<div id="question_score">';                        
                        if ($propagate_neg == 0 && $questionScore < 0) {
                	        $questionScore = 0;
                	    }                    	    
                        echo get_lang('Score').": ".show_score($questionScore, $questionWeighting, false, false);
                        
                        echo '</div>';
                    }
                    echo '<br />';
                }
            }
        }        
        unset ($objAnswerTmp);
        $i++;
    
        $totalWeighting += $questionWeighting;
        // Store results directly in the database
        // For all in one page exercises, the results will be
        // stored by exercise_results.php (using the session)
        
        if ($saved_results) {
            if (empty ($choice)) {
                $choice = 0;
            }
            if ($answerType ==  MULTIPLE_ANSWER_TRUE_FALSE || $answerType ==  MULTIPLE_ANSWER_COMBINATION_TRUE_FALSE ) {                
                if ($choice != 0) {
                    $reply = array_keys($choice);
                    for ($i = 0; $i < sizeof($reply); $i++) {
                        $ans = $reply[$i];                        
                        exercise_attempt($questionScore, $ans.':'.$choice[$ans], $quesId, $exeId, $i, $this->id);
                    }
                } else {
                    exercise_attempt($questionScore, 0, $quesId, $exeId, 0, $this->id);
                }                
            } elseif ($answerType == MULTIPLE_ANSWER) {
                if ($choice != 0) {
                    $reply = array_keys($choice);
                    for ($i = 0; $i < sizeof($reply); $i++) {
                        $ans = $reply[$i];
                        exercise_attempt($questionScore, $ans, $quesId, $exeId, $i, $this->id);
                    }
                } else {
                    exercise_attempt($questionScore, 0, $quesId, $exeId, 0, $this->id);
                }
            } elseif ($answerType == MULTIPLE_ANSWER_COMBINATION) {
                if ($choice != 0) {
                    $reply = array_keys($choice);
                    for ($i = 0; $i < sizeof($reply); $i++) {
                        $ans = $reply[$i];
                        exercise_attempt($questionScore, $ans, $quesId, $exeId, $i, $this->id);
                    }
                } else {
                    exercise_attempt($questionScore, 0, $quesId, $exeId, 0, $this->id);
                }
            } elseif ($answerType == MATCHING) {
                foreach ($matching as $j => $val) {
                    exercise_attempt($questionScore, $val, $quesId, $exeId, $j, $this->id);
                }
            } elseif ($answerType == FREE_ANSWER) {
                $answer = $choice;
                exercise_attempt($questionScore, $answer, $quesId, $exeId, 0, $this->id);
            } elseif ($answerType == UNIQUE_ANSWER || $answerType == UNIQUE_ANSWER_NO_OPTION) {
                $answer = $choice;
                exercise_attempt($questionScore, $answer, $quesId, $exeId, 0, $this->id);
            } elseif ($answerType == HOT_SPOT) {                
                exercise_attempt($questionScore, $answer, $quesId, $exeId, 0, $this->id);                
                if (isset($exerciseResultCoordinates[$questionId]) && !empty($exerciseResultCoordinates[$questionId])) {
                    foreach($exerciseResultCoordinates[$questionId] as $idx => $val) {
                        exercise_attempt_hotspot($exeId,$quesId,$idx,$choice[$idx],$val,$this->id);
                    }
                }
            } else {
                exercise_attempt($questionScore, $answer, $quesId, $exeId, 0,$this->id);
            }
        }
            
        if ($saved_results) {
            $stat_table = Database :: get_statistic_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
            $sql_update = 'UPDATE ' . $stat_table . ' SET exe_result = exe_result + ' . floatval($totalScore) . ',exe_weighting = exe_weighting + ' .  floatval($totalWeighting) . ' WHERE exe_id = ' . $exeId;
    		Database::query($sql_update);
        }
        	
    	if ($propagate_neg == 0 && $questionScore < 0) {
    	    $questionScore = 0;
    	}
        $return_array = array('score'=>$questionScore, 'weight'=>$questionWeighting);
        return $return_array;
	} //End function
    
        
    function send_notification($arrques, $arrans) {
        
        // Email configuration settings
        $coursecode     = api_get_course_id();
        $course_info    = api_get_course_info(api_get_course_id());
        $to = '';
        $teachers = array();
        if (api_get_session_id()) {
            $teachers = CourseManager::get_coach_list_from_course_code($coursecode, api_get_session_id());
        } else {
            $teachers = CourseManager::get_teacher_list_from_course_code($coursecode);
        }        
        $num = count($teachers);
        if($num>1) {
            $to = array();
            foreach($teachers as $teacher) {
                $to[] = $teacher['email'];
            }
        } elseif ($num>0){
            foreach($teachers as $teacher) {
                $to = $teacher['email'];
            }
        } else {
            //this is a problem (it means that there is no admin for this course)
        }
                
                
        global $courseName, $exerciseTitle, $url_email;
        require_once api_get_path(LIBRARY_PATH).'usermanager.lib.php';
        $user_info = UserManager::get_user_info_by_id(api_get_user_id());

        if (api_get_course_setting('email_alert_manager_on_new_quiz') != 1 ) {
            return '';
        }

        $mycharset = api_get_system_encoding();
        $msg = '<html><head>
                <link rel="stylesheet" href="'.api_get_path(WEB_CODE_PATH).'css/'.api_get_setting('stylesheets').'/default.css" type="text/css">
                <meta content="text/html; charset='.$mycharset.'" http-equiv="content-type"></head>';
        if(count($arrques)>0) {
            $msg .= '<body>
            <p>'.get_lang('OpenQuestionsAttempted').' :
            </p>
            <p>'.get_lang('AttemptDetails').' : <br />
            </p>
            <table width="730" height="136" border="0" cellpadding="3" cellspacing="3">
                                                    <tr>
                <td width="229" valign="top"><h2>&nbsp;&nbsp;'.get_lang('CourseName').'</h2></td>
                <td width="469" valign="top"><h2>#course#</h2></td>
              </tr>
              <tr>
                <td width="229" valign="top" class="outerframe">&nbsp;&nbsp;'.get_lang('TestAttempted').'</span></td>
                <td width="469" valign="top" class="outerframe">#exercise#</td>
              </tr>
              <tr>
                <td valign="top">&nbsp;&nbsp;<span class="style10">'.get_lang('StudentName').'</span></td>
                <td valign="top" >#firstName# #lastName#</td>
              </tr>
              <tr>
                <td valign="top" >&nbsp;&nbsp;'.get_lang('StudentEmail').' </td>
                <td valign="top"> #mail#</td>
            </tr></table>
            <p><br />'.get_lang('OpenQuestionsAttemptedAre').' :</p>
             <table width="730" height="136" border="0" cellpadding="3" cellspacing="3">';

            for($i=0;$i<sizeof($arrques);$i++) {
                      $msg.='
                            <tr>
                        <td width="220" valign="top" bgcolor="#E5EDF8">&nbsp;&nbsp;<span class="style10">'.get_lang('Question').'</span></td>
                        <td width="473" valign="top" bgcolor="#F3F3F3"><span class="style16"> #questionName#</span></td>
                            </tr>
                            <tr>
                        <td width="220" valign="top" bgcolor="#E5EDF8">&nbsp;&nbsp;<span class="style10">'.get_lang('Answer').' </span></td>
                        <td valign="top" bgcolor="#F3F3F3"><span class="style16"> #answer#</span></td>
                            </tr>';

                            $msg1= str_replace("#exercise#",$exerciseTitle,$msg);
                            $msg= str_replace("#firstName#",$user_info['firstname'],$msg1);
                            $msg1= str_replace("#lastName#",$user_info['lastname'],$msg);
                            $msg= str_replace("#mail#",$user_info['email'],$msg1);
                            $msg1= str_replace("#questionName#",$arrques[$i],$msg);
                            $msg= str_replace("#answer#",$arrans[$i],$msg1);
                            $msg1= str_replace("#i#",$i,$msg);
                            $msg= str_replace("#course#",$courseName,$msg1);
            }
            $msg.='</table><br>
                            <span class="style16">'.get_lang('ClickToCommentAndGiveFeedback').',<br />
                            <a href="#url#">#url#</a></span></body></html>';

            $msg1= str_replace("#url#",$url_email,$msg);
            $mail_content = $msg1;

            $subject = get_lang('OpenQuestionsAttempted');


            $sender_name = api_get_person_name(api_get_setting('administratorName'), api_get_setting('administratorSurname'), null, PERSON_NAME_EMAIL_ADDRESS);
            $email_admin = api_get_setting('emailAdministrator');
            $result = @api_mail_html('', $to, $subject, $mail_content, $sender_name, $email_admin, array('charset'=>$mycharset));
        } else {

            $msg .= '<body>
            <p>'.get_lang('ExerciseAttempted').' <br />
            </p>
            <table width="730" height="136" border="0" cellpadding="3" cellspacing="3">
                <tr>
                <td width="229" valign="top"><h2>&nbsp;&nbsp;'.get_lang('CourseName').'</h2></td>
                <td width="469" valign="top"><h2>#course#</h2></td>
              </tr>
              <tr>
                <td width="229" valign="top" class="outerframe">&nbsp;&nbsp;'.get_lang('TestAttempted').'</span></td>
                <td width="469" valign="top" class="outerframe">#exercise#</td>
              </tr>
              <tr>
                <td valign="top">&nbsp;&nbsp;<span class="style10">'.get_lang('StudentName').'</span></td>
                '.(api_is_western_name_order() ? '<td valign="top" >#firstName# #lastName#</td>' : '<td valign="top" >#lastName# #firstName#</td>').'
              </tr>
              <tr>
                <td valign="top" >&nbsp;&nbsp;'.get_lang('StudentEmail').' </td>
                <td valign="top"> #mail#</td>
            </tr></table>';

            $msg= str_replace("#exercise#",$exerciseTitle,$msg);
            $msg= str_replace("#firstName#",$user_info['firstname'],$msg);
            $msg= str_replace("#lastName#",$user_info['lastname'],$msg);
            $msg= str_replace("#mail#",$user_info['email'],$msg);
            $msg= str_replace("#course#",$courseName,$msg);

            $msg.='<br />
                    <span class="style16">'.get_lang('ClickToCommentAndGiveFeedback').',<br />
                    <a href="#url#">#url#</a></span></body></html>';

            $msg= str_replace("#url#",$url_email,$msg);
            $mail_content = $msg;

            $sender_name = api_get_person_name(api_get_setting('administratorName'), api_get_setting('administratorSurname'), null, PERSON_NAME_EMAIL_ADDRESS);
            $email_admin = api_get_setting('emailAdministrator');

            $subject = get_lang('ExerciseAttempted');
            $result = @api_mail_html('', $to, $subject, $mail_content, $sender_name, $email_admin, array('charset'=>$mycharset));
        }
    }
    
    function show_exercise_result_header($user_data, $date = null) {
        $description = '';
        if (!empty($this->description)) {
        	$description = '<tr>
        		<td style="font-weight:bold" width="10%">
        			&nbsp;'.get_lang("Description").' :
        		</td>
        		<td width="90%">			
        		'.$this->description.'
        		</td>
        	</tr>';
        }
        $date_data = '';
        if (!empty($date)) {
        	$date_data = '<tr>
        		<td style="font-weight:bold" width="10%">
        			&nbsp;'.get_lang("Date").' :
        		</td>
        		<td width="90%">			
        		'.$date.'
        		</td>
        	</tr>';
        }
        
        
        $html = '<table width="100%">
        	<tr>
        	<td colspan="2">
        		<h2>'.Display::return_icon('quiz_big.png', get_lang('Result')).' '.$this->exercise.' : '.get_lang('Result').'</h2>
        	</td>	
        	</tr>		
        	<tr>
        		<td style="font-weight:bold" width="90px">&nbsp;'.get_lang('User').' : </td>
        		<td>
        		'.$user_data.'
                </td>
            </tr>            
            '.$date_data.'
        	'.$description.'
        </table>
        <br />';
        return $html;
    }
}
endif;
?>
