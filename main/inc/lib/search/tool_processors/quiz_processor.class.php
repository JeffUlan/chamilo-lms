<?php
include_once dirname(__FILE__) . '/../../../global.inc.php';
require_once dirname(__FILE__) . '/search_processor.class.php';

/**
 * Process exercises before pass it to search listing scripts
 */
class quiz_processor extends search_processor {
    public $exercices = array();

    function quiz_processor($rows) {
        $this->rows = $rows;
        // group by exercise
        foreach ($rows as $row_id => $row_val) {
            $courseid = $row_val['courseid'];
            $se_data = $row_val['xapian_data'][SE_DATA];
            switch ($row_val['xapian_data'][SE_DATA]['type']) {
                case SE_DOCTYPE_EXERCISE_EXERCISE:
                    $exercise_id = $se_data['exercise_id'];
                    $question = NULL;
                    $item = array(
                              'courseid' => $courseid,
                              'question' => $question,
                              'score' => $row_val['score'],
                              'row_id' => $row_id,
                            );
                    $this->exercises[$courseid][$exercise_id][] = $item;
                    $this->exercises[$courseid][$exercise_id]['total_score'] += $row_val['score'];
                    break;
                case SE_DOCTYPE_EXERCISE_QUESTION:
                    if (is_array($se_data['exercise_ids'])) {
                        foreach ($se_data['exercise_ids'] as $exercise_id) {
                            $question = $se_data['question_id'];
                            $item = array(
                                'courseid' => $courseid,
                                'question' => $question,
                                'score' => $row_val['score'],
                                'row_id' => $row_id,
                            );
                            $this->exercises[$courseid][$exercise_id][] = $item;
                            $this->exercises[$courseid][$exercise_id]['total_score'] += $row_val['score'];
                        }
                    }
                    break;
            }
        }
        //print_r($this->exercises);
    }

    public function process() {
        $results = array();
        foreach ($this->exercises as $courseid => $exercises) {
	        // can view course?
	        if (api_is_course_visible_for_user(NULL, $courseid)) {
		        foreach ($exercises as $exercise_id => $exercise) {
			        // is visible?
			        $visibility = api_get_item_visibility(api_get_course_info($courseid), TOOL_QUIZ, $exercise_id);
			        if($visibility) {
				        list($thumbnail, $image, $name, $author) = $this->get_information($courseid, $exercise_id);
				        $url = api_get_path(WEB_PATH) . 'main/exercice/exercice_submit.php?cidReq=%s&exerciseId=%s';
				        $results[] = array(
					        'toolid' => TOOL_QUIZ,
							'score' => $exercise['total_score']/(count($exercise)-1), // not count total_score array item
							'url' => sprintf($url, $courseid, $exercise_id),
							'thumbnail' => $thumbnail,
							'image' => $image,
							'title' => $name,
							'author' => $author,
				        );
			        }
		        }
	        }
        }

        // get information to sort
        foreach ($results as $key => $row) {
	        $score[$key]  = $row['score'];
        }
        // Sort results with score descending
        array_multisort($score, SORT_DESC, $results);
        return $results;
    }

    /**
     * Get learning path information
     */
    private function get_information($course_id, $exercise_id) {
        $exercise_table = Database::get_course_table_from_code($course_id, TABLE_QUIZ_TEST);
        $item_property_table = Database::get_course_table_from_code($course_id, TABLE_ITEM_PROPERTY);

        $sql = "SELECT *
          FROM       $exercise_table
          WHERE      id = $exercise_id
          LIMIT 1";
        $dk_result = api_sql_query ($sql);

        //actually author isn't saved on exercise tool, but prepare for when it's ready
        $sql = "SELECT insert_user_id
          FROM       $item_property_table
          WHERE      ref = $doc_id
                     AND tool = '". TOOL_DOCUMENT ."'
          LIMIT 1";

        $name = '';
        if ($row = Database::fetch_array ($dk_result)) {
            // Get the image path
            $thumbnail = api_get_path(WEB_PATH) . 'main/img/quiz.gif';
            $image = $thumbnail; //FIXME: use big images
            $name = $row['title'];
            // get author
            $author = '';
            $item_result = api_sql_query ($sql);
            if ($row = Database::fetch_array ($item_result)) {
                $user_data = api_get_user_info($row['insert_user_id']);
                $author = $user_data['firstName'] .' '. $user_data['lastName'];
            }
        }

        return array($thumbnail, $image, $name, $author);
    }
}
?>
