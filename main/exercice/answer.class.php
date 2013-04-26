<?php
/* For licensing terms, see /license.txt */
/**
 * This class allows to instantiate an object of type Answer
 * 5 arrays are created to receive the attributes of each answer belonging to a specified question
 * @package chamilo.exercise
 * @author Olivier Brouckaert
 * @version $Id: answer.class.php 21172 2009-06-01 20:58:05Z darkvela $
 */
/**
 * Code
 */
/**
 * Answer class
 * @package chamilo.exercise
 */
class Answer
{
    public $questionId;

    // these are arrays
    public $answer;
    public $correct;
    public $comment;
    public $weighting;
    public $position;
    public $hotspot_coordinates;
    public $hotspot_type;
    public $destination;
    // these arrays are used to save temporarily new answers
    // then they are moved into the arrays above or deleted in the event of cancellation
    public $new_answer;
    public $new_correct;
    public $new_comment;
    public $new_weighting;
    public $new_position;
    public $new_hotspot_coordinates;
    public $new_hotspot_type;

    public $nbrAnswers;
    public $new_nbrAnswers;
    public $new_destination; // id of the next question if feedback option is set to Directfeedback
    public $course; //Course information

    /**
     * constructor of the class
     *
     * @author     Olivier Brouckaert
     * @param     integer    Question ID that answers belong to
     */
    function Answer($questionId, $course_id = null)
    {
        $this->questionId          = intval($questionId);
        $this->answer              = array();
        $this->correct             = array();
        $this->comment             = array();
        $this->weighting           = array();
        $this->position            = array();
        $this->hotspot_coordinates = array();
        $this->hotspot_type        = array();
        $this->destination         = array();
        // clears $new_* arrays
        $this->cancel();

        if (!empty($course_id)) {
            $course_info = api_get_course_info_by_id($course_id);
        } else {
            $course_info = api_get_course_info();
        }

        $this->course    = $course_info;
        $this->course_id = $course_info['real_id'];

        // fills arrays
        $objExercise = new Exercise($this->course_id);
        $exerciseId = isset($_REQUEST['exerciseId']) ? $_REQUEST['exerciseId'] : null;

        if ($exerciseId) {
            $objExercise->read($exerciseId);
            if ($objExercise->random_answers == '1') {
                $this->readOrderedBy('rand()', ''); // randomize answers
            } else {
                $this->read(); // natural order
            }
        } else {
            $this->read();
        }
    }

    /**
     * Clears $new_* arrays
     *
     * @author - Olivier Brouckaert
     */
    function cancel()
    {
        $this->new_answer              = array();
        $this->new_correct             = array();
        $this->new_comment             = array();
        $this->new_weighting           = array();
        $this->new_position            = array();
        $this->new_hotspot_coordinates = array();
        $this->new_hotspot_type        = array();
        $this->new_nbrAnswers          = 0;
        $this->new_destination         = array();
    }

    /**
     * Reads answer informations from the data base
     *
     * @author - Olivier Brouckaert
     */
    function read()
    {
        $TBL_ANSWER = Database::get_course_table(TABLE_QUIZ_ANSWER);
        $questionId = $this->questionId;

        /*$sql = "SELECT id, id_auto, answer,correct,comment,ponderation, position, hotspot_coordinates, hotspot_type, destination  FROM
		      $TBL_ANSWER WHERE c_id = {$this->course_id} AND question_id ='".$questionId."' ORDER BY position";
        */
        $sql = "SELECT iid, answer, correct, comment, ponderation, position, hotspot_coordinates, hotspot_type, destination
                FROM $TBL_ANSWER
                WHERE c_id = {$this->course_id} AND question_id ='".$questionId."' ORDER BY position";

        $result = Database::query($sql);
        $counter = 1;

        // while a record is found
        while ($object = Database::fetch_object($result)) {
            $i = $object->iid;

            $this->id[$i]                  = $object->iid;
            $this->answer[$i]              = $object->answer;
            $this->correct[$i]             = $object->correct;
            $this->comment[$i]             = $object->comment;
            $this->weighting[$i]           = $object->ponderation;
            $this->position[$i]            = $object->position;
            $this->hotspot_coordinates[$i] = $object->hotspot_coordinates;
            $this->hotspot_type[$i]        = $object->hotspot_type;
            $this->destination[$i]         = $object->destination;
                //$this->autoId[$i]            = $object->id_auto;
            $counter++;
        }
        $this->nbrAnswers = $counter - 1;
    }

    /**
     * reads answer informations from the data base ordered by parameter
     * @param    string    Field we want to order by
     * @param    string    DESC or ASC
     * @author     Frederic Vauthier
     */
    function readOrderedBy($field, $order = 'ASC')
    {
        $field = Database::escape_string($field);
        if (empty($field)) {
            $field = 'position';
        }

        if ($order != 'ASC' && $order != 'DESC') {
            $order = 'ASC';
        }


        $TBL_ANSWER = Database::get_course_table(TABLE_QUIZ_ANSWER);
        $TBL_QUIZ   = Database::get_course_table(TABLE_QUIZ_QUESTION);
        $questionId = intval($this->questionId);

        //$sql             = "SELECT type FROM $TBL_QUIZ WHERE c_id = {$this->course_id} AND id = $questionId";
        $sql = "SELECT type FROM $TBL_QUIZ WHERE c_id = {$this->course_id} AND iid = $questionId";
        $result_question = Database::query($sql);
        $question_type   = Database::fetch_array($result_question);

        /*$sql    = "SELECT answer,correct,comment,ponderation,position, hotspot_coordinates, hotspot_type, destination, id_auto ".
            "FROM $TBL_ANSWER WHERE c_id = {$this->course_id} AND question_id='".$questionId."'   ".
            "ORDER BY $field $order";
        */
        $sql = "SELECT * FROM $TBL_ANSWER
                WHERE c_id = {$this->course_id} AND question_id = '".$questionId."'
				ORDER BY $field $order";
        $result = Database::query($sql);

        // while a record is found
        $doubt_data = null;
        while ($object = Database::fetch_object($result)) {
            if ($question_type['type'] == UNIQUE_ANSWER_NO_OPTION && $object->position == 666) {
                $doubt_data = $object;
                continue;
            }
            $i = $object->iid;
            $this->id[$i] = $object->iid;

            $this->answer[$i]      = $object->answer;
            $this->correct[$i]     = $object->correct;
            $this->comment[$i]     = $object->comment;
            $this->weighting[$i]   = $object->ponderation;
            $this->position[$i]    = $object->position;
            $this->destination[$i] = $object->destination;
            //$this->autoId[$i]      = $object->id_auto;
        }

        if ($question_type['type'] == UNIQUE_ANSWER_NO_OPTION && !empty($doubt_data)) {
            $i = $doubt_data->iid;
            $this->id[$i] = $doubt_data->iid;

            $this->answer[$i]      = $doubt_data->answer;
            $this->correct[$i]     = $doubt_data->correct;
            $this->comment[$i]     = $doubt_data->comment;
            $this->weighting[$i]   = $doubt_data->ponderation;
            $this->position[$i]    = $doubt_data->position;
            $this->destination[$i] = $doubt_data->destination;
            //$this->autoId[$i]      = $doubt_data->id_auto;
        }
        $this->nbrAnswers = count($this->answer);
    }


    /**
     * returns the autoincrement id identificator
     * @deprecated Should not be used anymore
     * @author - Juan Carlos Ra�a
     * @return - integer - answer num
     */
    function selectAutoId($id)
    {
        return $this->autoId[$id];
    }

    /**
     * returns the number of answers in this question
     *
     * @author - Olivier Brouckaert
     * @return - integer - number of answers
     */
    function selectNbrAnswers()
    {
        return $this->nbrAnswers;
    }

    /**
     * returns the question ID which the answers belong to
     *
     * @author - Olivier Brouckaert
     * @return - integer - the question ID
     */
    function selectQuestionId()
    {
        return $this->questionId;
    }

    /**
     * returns the question ID of the destination question
     *
     * @author - Julio Montoya
     * @return - integer - the question ID
     */
    public function selectDestination($id)
    {
        return isset($this->destination[$id]) ? $this->destination[$id] : null;
    }

    /**
     * returns the answer title
     *
     * @author - Olivier Brouckaert
     * @param - integer $id - answer ID
     * @return - string - answer title
     */
    public function selectAnswer($id)
    {
        return isset($this->answer[$id]) ? $this->answer[$id] : null;
    }

    /**
     * return array answer by id else return a bool
     */
    function selectAnswerByAutoId($auto_id)
    {
        $TBL_ANSWER = Database::get_course_table(TABLE_QUIZ_ANSWER);

        $auto_id = intval($auto_id);
        $sql     = "SELECT id, answer FROM $TBL_ANSWER WHERE c_id = {$this->course_id} AND id_auto='$auto_id'";
        $rs      = Database::query($sql);

        if (Database::num_rows($rs) > 0) {
            $row = Database::fetch_array($rs);
            return $row;
        }
        return false;
    }

    /**
     * returns the answer title from an answer's position
     *
     * @author - Yannick Warnier
     * @param - integer $id - answer ID
     * @return - bool - answer title
     */
    public function selectAnswerIdByPosition($pos)
    {
        foreach ($this->position as $k => $v) {
            if ($v != $pos) {
                continue;
            }
            return $k;
        }
        return false;
    }

    /**
     * Returns a list of answers
     * @author Yannick Warnier <ywarnier@beeznest.org>
     * @return array    List of answers where each answer is an array of (id, answer, comment, grade) and grade=weighting
     */
    function getAnswersList($decode = false)
    {
        $list = array();
        for ($i = 1; $i <= $this->nbrAnswers; $i++) {
            if (!empty($this->answer[$i])) {

                //Avoid problems when parsing elements with accents
                if ($decode) {
                    $this->answer[$i]  = api_html_entity_decode(
                        $this->answer[$i],
                        ENT_QUOTES,
                        api_get_system_encoding()
                    );
                    $this->comment[$i] = api_html_entity_decode(
                        $this->comment[$i],
                        ENT_QUOTES,
                        api_get_system_encoding()
                    );
                }

                $list[] = array(
                    'iid'            => $i,
                    'answer'        => $this->answer[$i],
                    'comment'       => $this->comment[$i],
                    'grade'         => $this->weighting[$i],
                    'hotspot_coord' => $this->hotspot_coordinates[$i],
                    'hotspot_type'  => $this->hotspot_type[$i],
                    'correct'       => $this->correct[$i],
                    'destination'   => $this->destination[$i]
                );
            }
        }
        return $list;
    }

    /**
     * Returns a list of grades
     * @author Yannick Warnier <ywarnier@beeznest.org>
     * @return array    List of grades where grade=weighting (?)
     */
    function getGradesList()
    {
        $list = array();
        for ($i = 0; $i < $this->nbrAnswers; $i++) {
            if (!empty($this->answer[$i])) {
                $list[$i] = $this->weighting[$i];
            }
        }
        return $list;
    }

    /**
     * Returns the question type
     * @author    Yannick Warnier <ywarnier@beeznest.org>
     * @return    integer    The type of the question this answer is bound to
     */
    function getQuestionType()
    {
        $TBL_QUESTIONS = Database::get_course_table(TABLE_QUIZ_QUESTION);
        $sql = "SELECT type FROM $TBL_QUESTIONS WHERE c_id = {$this->course_id} AND iid = '".$this->questionId."'";
        $res = Database::query($sql);
        if (Database::num_rows($res) <= 0) {
            return null;
        }
        $row = Database::fetch_array($res);
        return $row['type'];
    }

    /**
     * tells if answer is correct or not
     *
     * @author - Olivier Brouckaert
     * @param - integer $id - answer ID
     * @return - integer - 0 if bad answer, not 0 if good answer
     */
    function isCorrect($id)
    {
        return $this->correct[$id];
    }

    function getAnswerIdFromList($answer_id) {
        $counter = 1;
        foreach ($this->answer as $my_answer_id => $item) {
            if ($answer_id == $my_answer_id) {
                return $counter;
            }
            $counter++;
        }
    }

    function getRealAnswerIdFromList($answer_id) {
        $counter = 1;
        foreach ($this->answer as $my_answer_id => $item) {
            if ($answer_id == $counter) {
                return $my_answer_id;
            }
            $counter++;
        }
    }

    function getCorrectAnswerPosition($correct_id) {
        $counter = 1;
        foreach ($this->correct as $my_correct_id => $item) {
            if ($correct_id == $my_correct_id) {
                return $counter;
            }
            $counter++;
        }
    }

    /**
     * returns answer comment
     *
     * @author - Olivier Brouckaert
     * @param - integer $id - answer ID
     * @return - string - answer comment
     */
    function selectComment($id)
    {
        return $this->comment[$id];
    }

    /**
     * returns answer weighting
     *
     * @author - Olivier Brouckaert
     * @param - integer $id - answer ID
     * @return - integer - answer weighting
     */
    function selectWeighting($id)
    {
        return $this->weighting[$id];
    }

    /**
     * returns answer position
     *
     * @author - Olivier Brouckaert
     * @param - integer $id - answer ID
     * @return - integer - answer position
     */
    function selectPosition($id)
    {
        return $this->position[$id];
    }

    /**
     * Returns answer hotspot coordinates
     *
     * @author    Olivier Brouckaert
     * @param    integer    Answer ID
     * @return    integer    Answer position
     */
    function selectHotspotCoordinates($id)
    {
        return isset($this->hotspot_coordinates[$id]) ? $this->hotspot_coordinates[$id] : null;
    }

    /**
     * Returns answer hotspot type
     *
     * @author    Toon Keppens
     * @param    integer        Answer ID
     * @return    integer        Answer position
     */
    function selectHotspotType($id)
    {
        return isset($this->hotspot_type[$id]) ? $this->hotspot_type[$id] : null;
    }

    /**
     * Creates a new answer
     *
     * @author Olivier Brouckaert
     * @param string     answer title
     * @param integer     0 if bad answer, not 0 if good answer
     * @param string     answer comment
     * @param integer     answer weighting
     * @param integer     answer position
     * @param coordinates     Coordinates for hotspot exercises (optional)
     * @param integer        Type for hotspot exercises (optional)
     */
    public function createAnswer(
        $answer,
        $correct,
        $comment,
        $weighting,
        $position,
        $new_hotspot_coordinates = null,
        $new_hotspot_type = null,
        $destination = ''
    ) {
        $this->new_nbrAnswers++;
        $id                                 = $this->new_nbrAnswers;
        $this->new_answer[$id]              = $answer;
        $this->new_correct[$id]             = $correct;
        $this->new_comment[$id]             = $comment;
        $this->new_weighting[$id]           = $weighting;
        $this->new_position[$id]            = $position;
        $this->new_hotspot_coordinates[$id] = $new_hotspot_coordinates;
        $this->new_hotspot_type[$id]        = $new_hotspot_type;
        $this->new_destination[$id]         = $destination;
    }

    /**
     * updates an answer
     *
     * @author Toon Keppens
     * @param    string    Answer title
     * @param    string    Answer comment
     * @param    integer    Answer weighting
     * @param    integer    Answer position
     */
    function updateAnswers($answer, $comment, $weighting, $position, $destination)
    {
        $TBL_REPONSES = Database :: get_course_table(TABLE_QUIZ_ANSWER);

        $questionId = $this->questionId;
        $sql = "UPDATE $TBL_REPONSES SET
                answer = '".Database::escape_string($answer)."',
				comment = '".Database::escape_string($comment)."',
				ponderation = '".Database::escape_string($weighting)."',
				position = '".Database::escape_string($position)."',
				destination = '".Database::escape_string($destination)."'
				WHERE c_id = {$this->course_id} AND iid = '".Database::escape_string($position)."'
				AND question_id = '".Database::escape_string($questionId)."'";
        Database::query($sql);
    }

    /**
     * Records answers into the data base
     *
     * @author - Olivier Brouckaert
     */
    function save()
    {
        $table_quiz_answer = Database :: get_course_table(TABLE_QUIZ_ANSWER);
        $questionId = intval($this->questionId);

        // Removes old answers before inserting of new ones
        /*$sql = "DELETE FROM $TBL_REPONSES WHERE c_id = {$this->course_id} AND question_id = '".($questionId)."'";
        Database::query($sql);*/

        // @todo don't do this!
        $sql = "DELETE FROM $table_quiz_answer WHERE question_id = '".($questionId)."'";
        Database::query($sql);

        $c_id = $this->course['real_id'];

        // Inserts new answers into database
        $real_correct_ids = array();
        for ($i = 1; $i <= $this->new_nbrAnswers; $i++) {

            $answer = Database::escape_string($this->new_answer[$i]);
            $correct = Database::escape_string($this->new_correct[$i]);
            $comment = Database::escape_string($this->new_comment[$i]);
            $weighting = Database::escape_string($this->new_weighting[$i]);
            $position = Database::escape_string($this->new_position[$i]);
            $hotspot_coordinates = Database::escape_string($this->new_hotspot_coordinates[$i]);
            $hotspot_type = Database::escape_string($this->new_hotspot_type[$i]);
            $destination = Database::escape_string($this->new_destination[$i]);

            $sql = "INSERT INTO $table_quiz_answer (c_id, question_id, answer, correct, comment, ponderation, position, hotspot_coordinates,hotspot_type, destination) VALUES ";
            $sql.= "($c_id, '$questionId','$answer','$correct','$comment','$weighting','$position','$hotspot_coordinates','$hotspot_type','$destination')";
            Database::query($sql);
            $latest_insert_id = Database::insert_id();
            $real_correct_ids[$i] = $latest_insert_id;
        }

        $question_info = Question::read($questionId);
        if ($question_info->type == MATCHING) {

            //Fixing real answer id
            for ($i = 1; $i <= $this->new_nbrAnswers; $i++) {
                if (isset($this->new_correct[$i]) && !empty($this->new_correct[$i])) {
                    $real_correct_id = $real_correct_ids[$this->new_correct[$i]];
                    $current_answer_id = $real_correct_ids[$i];
                    $sql = "UPDATE $table_quiz_answer SET correct = '$real_correct_id' WHERE iid = $current_answer_id";
                    Database::query($sql);
                }
            }
        }

        // moves $new_* arrays
        $this->answer              = $this->new_answer;
        $this->correct             = $this->new_correct;
        $this->comment             = $this->new_comment;
        $this->weighting           = $this->new_weighting;
        $this->position            = $this->new_position;
        $this->hotspot_coordinates = $this->new_hotspot_coordinates;
        $this->hotspot_type        = $this->new_hotspot_type;

        $this->nbrAnswers  = $this->new_nbrAnswers;
        $this->destination = $this->new_destination;
        // clears $new_* arrays
        $this->cancel();
    }

    /**
     * Duplicates answers by copying them into another question
     *
     * @author Olivier Brouckaert
     * @param  int question id
     * @param  array destination course info (result of the function api_get_course_info() )
     */
    function duplicate($newQuestionId, $course_info = null)
    {
        if (empty($course_info)) {
            $course_info = $this->course;
        } else {
            $course_info = $course_info;
        }

        $TBL_REPONSES = Database :: get_course_table(TABLE_QUIZ_ANSWER);

        if (self::getQuestionType() == MULTIPLE_ANSWER_TRUE_FALSE || self::getQuestionType(
        ) == MULTIPLE_ANSWER_TRUE_FALSE
        ) {

            //Selecting origin options
            $origin_options = Question::readQuestionOption($this->selectQuestionId(), $this->course['real_id']);

            if (!empty($origin_options)) {
                foreach ($origin_options as $item) {
                    $new_option_list[] = $item['id'];
                }
            }

            $destination_options = Question::readQuestionOption($newQuestionId, $course_info['real_id']);
            $i                   = 0;
            $fixed_list          = array();
            if (!empty($destination_options)) {
                foreach ($destination_options as $item) {
                    $fixed_list[$new_option_list[$i]] = $item['id'];
                    $i++;
                }
            }
        }

        // if at least one answer
        if ($this->nbrAnswers) {
            // inserts new answers into data base

            $c_id = $course_info['real_id'];
            $correct_answers = array();
            $new_ids = array();

            foreach ($this->answer as $answer_id => $answer_item) {
                $i = $answer_id;
                if ($this->course['id'] != $course_info['id']) {
                    $this->answer[$i] = DocumentManager::replace_urls_inside_content_html_from_copy_course($this->answer[$i], $this->course['id'], $course_info['id']);
                    $this->comment[$i] = DocumentManager::replace_urls_inside_content_html_from_copy_course($this->comment[$i], $this->course['id'], $course_info['id']);
                }

                $answer = Database::escape_string($this->answer[$i]);
                $correct = Database::escape_string($this->correct[$i]);



                if (self::getQuestionType() == MULTIPLE_ANSWER_TRUE_FALSE || self::getQuestionType() == MULTIPLE_ANSWER_TRUE_FALSE) {
                    $correct = $fixed_list[intval($correct)];
                }

                $comment = Database::escape_string($this->comment[$i]);
                $weighting = Database::escape_string($this->weighting[$i]);
                $position = Database::escape_string($this->position[$i]);
                $hotspot_coordinates = Database::escape_string($this->hotspot_coordinates[$i]);
                $hotspot_type = Database::escape_string($this->hotspot_type[$i]);
                $destination = Database::escape_string($this->destination[$i]);
                $sql = "INSERT INTO $TBL_REPONSES (c_id, question_id, answer, correct, comment, ponderation, position, hotspot_coordinates, hotspot_type ,destination) VALUES";
                $sql.= "($c_id, '$newQuestionId','$answer','$correct','$comment', '$weighting','$position','$hotspot_coordinates','$hotspot_type','$destination')";
                Database::query($sql);
                $new_id = Database::insert_id();
                $new_ids[$answer_id] = $new_id;
                if ($correct) {
                    $correct_answers[$new_id] = $correct;
                }
            }
            if (self::getQuestionType() == MATCHING) {
                if (!empty($correct_answers)) {
                    foreach ($correct_answers as $new_id => $correct_id) {
                        $correct = $new_ids[$correct_id];
                        $sql = "UPDATE $TBL_REPONSES SET correct = $correct WHERE iid = $new_id";
                        Database::query($sql);
                    }
                }
            }
        }
    }


    function getJs() {
        //if ($this->questionId == 2)
        echo '<script>jsPlumb.ready(function() {
                if ($("#drag'.$this->questionId.'_question").length > 0) {
                    jsPlumbDemo.init('.$this->questionId.');
                }
            });</script>';
    }
}
