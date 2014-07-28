<?php
/* See license terms in /license.txt */
/**
 * EVENTS LIBRARY
 * @ŧodo add listeners
 *
 * This is the events library for Chamilo.
 * Include/require it in your code to use its functionality.
 * Functions of this library are used to record informations when some kind
 * of event occur. Each event has his own types of informations then each event
 * use its own function.
 *
 * @package chamilo.library
 */
use Application\Sonata\UserBundle\Entity\User;

class Event
{
    /**
     * @author Sebastien Piraux <piraux_seb@hotmail.com> old code
     * @author Julio Montoya 2013
     * @desc Record information for login event when an user identifies himself with username & password
     */
    function event_login(User $user)
    {
        $userId =  $user->getUserId();

        $TABLETRACK_LOGIN = Database::get_main_table(TABLE_STATISTIC_TRACK_E_LOGIN);

        $reallyNow = api_get_utc_datetime();

        $sql = "INSERT INTO ".$TABLETRACK_LOGIN." (login_user_id, login_ip, login_date, logout_date) VALUES
                    ('".$userId."',
                    '".Database::escape_string(api_get_real_ip())."',
                    '".$reallyNow."',
                    '".$reallyNow."'
                    )";
        Database::query($sql);

        $roles = $user->getRoles();
        // auto subscribe

        foreach ($roles as $role) {
            $userStatusParsed = 'student';

            switch ($role) {
                case 'ROLE_SESSION_MANAGER':
                    $userStatusParsed = 'sessionadmin';
                    break;
                case 'ROLE_TEACHER':
                    $userStatusParsed = 'teacher';
                    break;
                case 'ROLE_RRHH':
                    $userStatusParsed = 'DRH';
                    break;
            }

            $autoSubscribe = api_get_setting($userStatusParsed.'_autosubscribe');
            if ($autoSubscribe) {
                $autoSubscribe = explode('|', $autoSubscribe);
                foreach ($autoSubscribe as $code) {
                    if (CourseManager::course_exists($code)) {
                        CourseManager::subscribe_user($userId, $code);
                    }
                }
            }
        }
    }

    /**
     * @param tool name of the tool (name in mainDb.accueil table)
     * @author Sebastien Piraux <piraux_seb@hotmail.com>
     * @desc Record information for access event for courses
     */
    public static function accessCourse()
    {
        $TABLETRACK_ACCESS = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ACCESS);
        $TABLETRACK_LASTACCESS = Database::get_main_table(TABLE_STATISTIC_TRACK_E_LASTACCESS); //for "what's new" notification

        $id_session = api_get_session_id();
        $now = api_get_utc_datetime();
        $courseId = api_get_course_int_id();
        $user_id = api_get_user_id();

        if ($user_id) {
            $user_id = "'".$user_id."'";
        } else {
            $user_id = "0"; // no one
        }
        $sql = "INSERT INTO ".$TABLETRACK_ACCESS."  (access_user_id, c_id, access_date, access_session_id) VALUES
                (".$user_id.", '".$courseId."', '".$now."','".$id_session."')";
        Database::query($sql);

        // added for "what's new" notification
        $sql = "UPDATE $TABLETRACK_LASTACCESS  SET access_date = '$now'
                WHERE access_user_id = $user_id AND c_id = '$courseId' AND access_tool IS NULL AND access_session_id=".$id_session;
        $result = Database::query($sql);

        if (Database::affected_rows($result) == 0) {
            $sql = "INSERT INTO $TABLETRACK_LASTACCESS (access_user_id, c_id, access_date, access_session_id)
                    VALUES (".$user_id.", '".$courseId."', '$now', '".$id_session."')";
            Database::query($sql);
        }

        return 1;
    }

    /**
     * @param tool name of the tool (name in mainDb.accueil table)
     * @author Sebastien Piraux <piraux_seb@hotmail.com>
     * @desc Record information for access event for tools
     *
     *  $tool can take this values :
     *  Links, Calendar, Document, Announcements,
     *  Group, Video, Works, Users, Exercices, Course Desc
     *  ...
     *  Values can be added if new modules are created (15char max)
     *  I encourage to use $nameTool as $tool when calling this function
     *
     * 	Functionality for "what's new" notification is added by Toon Van Hoecke
     */
    public static function event_access_tool($tool, $id_session = 0)
    {
        global $_configuration;
        $TABLETRACK_ACCESS = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ACCESS);
        $TABLETRACK_LASTACCESS = Database::get_main_table(TABLE_STATISTIC_TRACK_E_LASTACCESS); //for "what's new" notification

        $_course = api_get_course_info();
        $courseId = api_get_course_int_id();
        $id_session = api_get_session_id();
        $tool = Database::escape_string($tool);
        $reallyNow = api_get_utc_datetime();
        $user_id = api_get_user_id();

        // record information
        // only if user comes from the course $_cid
        //if( eregi($_configuration['root_web'].$_cid,$_SERVER['HTTP_REFERER'] ) )
        //$pos = strpos($_SERVER['HTTP_REFERER'],$_configuration['root_web'].$_cid);

        $pos = isset($_SERVER['HTTP_REFERER']) ? strpos(strtolower($_SERVER['HTTP_REFERER']), strtolower(api_get_path(WEB_COURSE_PATH).$_course['path'])) : false;
        // added for "what's new" notification
        $pos2 = isset($_SERVER['HTTP_REFERER']) ? strpos(strtolower($_SERVER['HTTP_REFERER']), strtolower(api_get_path(WEB_PATH)."index")) : false;
        // end "what's new" notification
        if ($pos !== false || $pos2 !== false) {
            $sql = "INSERT INTO ".$TABLETRACK_ACCESS."
                        (access_user_id,
                         c_id,
                         access_tool,
                         access_date,
                         access_session_id
                         )
                    VALUES
                        (".$user_id.",".// Don't add ' ' around value, it's already done.
                        "'".$courseId."' ,
                        '".$tool."',
                        '".$reallyNow."',
                        '".$id_session."')";
            Database::query($sql);
        }
        // "what's new" notification
        $sql = "UPDATE $TABLETRACK_LASTACCESS
                SET access_date = '$reallyNow'
                WHERE access_user_id = ".$user_id." AND c_id = '".$courseId."' AND access_tool = '".$tool."' AND access_session_id=".$id_session;
        $result = Database::query($sql);
        if (Database::affected_rows($result) == 0) {
            $sql = "INSERT INTO $TABLETRACK_LASTACCESS (access_user_id, c_id, access_tool, access_date, access_session_id)
                    VALUES (".$user_id.", '".$courseId."' , '$tool', '$reallyNow', $id_session)";
            Database::query($sql);
        }
        return 1;
    }

    /**
     * @param doc_id id of document (id in mainDb.document table)
     * @author Sebastien Piraux <piraux_seb@hotmail.com>
     * @desc Record information for download event
     * (when an user click to d/l a document)
     * it will be used in a redirection page
     * bug fixed: Roan Embrechts
     * Roan:
     * The user id is put in single quotes,
     * (why? perhaps to prevent sql insertion hacks?)
     * and later again.
     * Doing this twice causes an error, I remove one of them.
     */
    function event_download($doc_url)
    {
        $tbl_stats_downloads = Database::get_main_table(TABLE_STATISTIC_TRACK_E_DOWNLOADS);
        $doc_url = Database::escape_string($doc_url);

        $reallyNow = api_get_utc_datetime();
        $user_id = "'".api_get_user_id()."'";
        $_cid = api_get_course_int_id();

        $sql = "INSERT INTO $tbl_stats_downloads (
                     down_user_id,
                     c_id,
                     down_doc_path,
                     down_date,
                     down_session_id
                    )
                    VALUES (
                     ".$user_id.",
                     '".$_cid."',
                     '".$doc_url."',
                     '".$reallyNow."',
                     '".api_get_session_id()."'
                    )";
        Database::query($sql);
        return 1;
    }

    /**
     * @param doc_id id of document (id in mainDb.document table)
     * @author Sebastien Piraux <piraux_seb@hotmail.com>
     * @desc Record information for upload event
     * used in the works tool to record informations when
     * an user upload 1 work
     */
    function event_upload($doc_id)
    {
        $TABLETRACK_UPLOADS = Database::get_main_table(TABLE_STATISTIC_TRACK_E_UPLOADS);
        $courseCode = api_get_course_id();
        $reallyNow = api_get_utc_datetime();
        $user_id = api_get_user_id();

        $sql = "INSERT INTO ".$TABLETRACK_UPLOADS."
                    ( upload_user_id,
                      upload_cours_id,
                      upload_work_id,
                      upload_date,
                      upload_session_id
                    )
                    VALUES (
                     ".$user_id.",
                     '".$courseCode."',
                     '".$doc_id."',
                     '".$reallyNow."',
                     '".api_get_session_id()."'
                    )";
        Database::query($sql);
        return 1;
    }

    /**
     * @param link_id (id in coursDb liens table)
     * @author Sebastien Piraux <piraux_seb@hotmail.com>
     * @desc Record information for link event (when an user click on an added link)
     * it will be used in a redirection page
     */
    function event_link($link_id)
    {
        $TABLETRACK_LINKS = Database::get_main_table(TABLE_STATISTIC_TRACK_E_LINKS);
        $reallyNow = api_get_utc_datetime();
        $user_id = api_get_user_id();
        $sql = "INSERT INTO ".$TABLETRACK_LINKS."
                    ( links_user_id,
                     c_id,
                     links_link_id,
                     links_date,
                     links_session_id
                    ) VALUES (
                     ".$user_id.",
                     '".api_get_course_int_id()."',
                     '".Database::escape_string($link_id)."',
                     '".$reallyNow."',
                     '".api_get_session_id()."'
                    )";
        Database::query($sql);
        return 1;
    }

    /**
     * Update the TRACK_E_EXERCICES exercises
     *
     * @param   int     id of the attempt
     * @param   int     exercise id
     * @param   mixed   score obtained
     * @param   int     highest score for this exercise (and combination of questions)
     * @param   int     duration ( duration of the attempt in seconds )
     * @param   int     session_id
     * @param   int     learnpath_id (id of the learnpath)
     * @param   int     learnpath_item_id (id of the learnpath_item)
     *
     * @author Sebastien Piraux <piraux_seb@hotmail.com>
     * @author Julio Montoya Armas <gugli100@gmail.com> Reworked 2010
     * @desc Record result of user when an exercice was done
     */
    function update_event_exercise($exeid, $exo_id, $score, $weight, $session_id, $learnpath_id = 0, $learnpath_item_id = 0, $learnpath_item_view_id = 0, $duration = 0, $status = '', $remind_list = array() , $end_date = null) {
        global $debug;
        $TABLETRACK_EXERCICES = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);

        if ($debug) {
            error_log('Called to update_event_exercise');
            error_log('duration:'.$duration);
        }

        if (!empty($exeid)) {
            // Validation in case of fraud with actived control time
            if (!ExerciseLib::exercise_time_control_is_valid($exo_id, $learnpath_id, $learnpath_item_id)) {
                $score = 0;
            }

            if (!isset($status) || empty($status)) {
                $status = '';
            } else {
                $status = Database::escape_string($status);
            }

            if (!empty($remind_list)) {
                $remind_list = array_map('intval', $remind_list);
                $remind_list = array_filter($remind_list);
                $remind_list = implode(",", $remind_list);
            } else {
                $remind_list = '';
            }

            if (empty($end_date)) {
                $end_date = api_get_utc_datetime();
            }

            $sql = "UPDATE $TABLETRACK_EXERCICES SET
                       exe_exo_id 			= '".Database::escape_string($exo_id)."',
                       exe_result			= '".Database::escape_string($score)."',
                       exe_weighting 		= '".Database::escape_string($weight)."',
                       session_id			= '".Database::escape_string($session_id)."',
                       orig_lp_id 			= '".Database::escape_string($learnpath_id)."',
                       orig_lp_item_id 		= '".Database::escape_string($learnpath_item_id)."',
                       orig_lp_item_view_id = '".Database::escape_string($learnpath_item_view_id)."',
                       exe_duration 		= '".Database::escape_string($duration)."',
                       exe_date				= '".$end_date."',
                       status 				= '".$status."',
                       questions_to_check 	= '".$remind_list."'
                     WHERE exe_id = '".Database::escape_string($exeid)."'";
            $res = Database::query($sql);

            if ($debug) {
                error_log('update_event_exercise called');
                error_log("$sql");
            }
            //Deleting control time session track
            //ExerciseLib::exercise_time_control_delete($exo_id);
            return $res;
        } else {
            return false;
        }
    }

    /**
     * This function creates an empty Exercise in STATISTIC_TRACK_E_EXERCICES table.
     * After that in exercise_result.php we call the update_event_exercise() to update the exercise
     * @return int $id the last id registered, or false on error
     * @author Julio Montoya <gugli100@gmail.com>
     * @desc Record result of user when an exercice was done
     * @deprecated this function seems to be deprecated
     */
    function createEventExercise($exo_id)
    {
        if (empty($exo_id) or (intval($exo_id) != $exo_id)) {
            return false;
        }
        $tbl_track_exe = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $tbl_exe = Database::get_course_table(TABLE_QUIZ_TEST);
        $uid = api_get_user_id();
        $course_id = api_get_course_int_id();

        // First, check the exercise exists
        $sql_exe_id = "SELECT exercises.id FROM $tbl_exe as exercises WHERE c_id = $course_id AND exercises.id=$exo_id";
        $res_exe_id = Database::query($sql_exe_id);
        if ($res_exe_id === false) {
            return false;
        } //sql error
        if (Database::num_rows($res_exe_id) < 1) {
            return false;
        } //exe not found
        $row_exe_id = Database::fetch_row($res_exe_id);
        $exercise_id = intval($row_exe_id[0]);
        // Second, check if the record exists in the database (looking for incomplete records)
        $sql = "SELECT exe_id FROM $tbl_track_exe
            WHERE exe_exo_id =   $exo_id AND
            exe_user_id =  $uid AND c_id = '".$course_id."' AND ".
            "status = 'incomplete' AND ".
            "session_id = ".api_get_session_id();
        $res = Database::query($sql);
        if ($res === false) {
            return false;
        }
        if (Database::num_rows($res) > 0) {
            $row = Database::fetch_array($res);
            return $row['exe_id'];
        }

        // No record was found, so create one
        // get expire time to insert into the tracking record
        $current_expired_time_key = ExerciseLib::get_time_control_key($exercise_id);
        if (isset($_SESSION['expired_time'][$current_expired_time_key])) { //Only for exercice of type "One page"
            $expired_date = $_SESSION['expired_time'][$current_expired_time_key];
        } else {
            $expired_date = '0000-00-00 00:00:00';
        }
        $sql = "INSERT INTO $tbl_track_exe (exe_user_id, c_id, expired_time_control, exe_exo_id, session_id)
                VALUES ($uid,  '".$course_id."' ,'$expired_date','$exo_id','".api_get_session_id()."')";
        Database::query($sql);
        $id = Database::insert_id();
        return $id;
    }

    /**
     * Record an event for this attempt at answering an exercise
     * @param	float	Score achieved
     * @param	string	Answer given
     * @param	integer	Question ID
     * @param	integer Exercise attempt ID a.k.a exe_id (from track_e_exercise)
     * @param	integer	Position
     * @param	integer Exercise ID (from c_quiz)
     * @param	bool update results?
     * @param	string  Filename (for audio answers - using nanogong)
     * @param	integer User ID The user who's going to get this score. Default value of null means "get from context".
     * @param	integer	Course ID (from the "id" column of course table). Default value of null means "get from context".
     * @param	integer	Session ID (from the session table). Default value of null means "get from context".
     * @param	integer	Learnpath ID (from c_lp table). Default value of null means "get from context".
     * @param	integer	Learnpath item ID (from the c_lp_item table). Default value of null means "get from context".
     * @return	boolean	Result of the insert query
     */
    function saveQuestionAttempt(
        $score,
        $answer,
        $question_id,
        $exe_id,
        $position,
        $exercise_id = 0,
        $updateResults = false,
        $nano = null,
        $user_id = null,
        $course_id = null,
        $session_id = null,
        $learnpath_id = null,
        $learnpath_item_id = null
    ) {
        global $debug;

        //$score = Database::escape_string($score);
        // $answer = Database::escape_string($answer);
        $question_id = Database::escape_string($question_id);
        $exe_id = Database::escape_string($exe_id);
        $position = Database::escape_string($position);
        $now = api_get_utc_datetime();

        // check user_id or get from context
        if (empty($user_id) or intval($user_id) != $user_id) {
            $user_id = api_get_user_id();
            // anonymous
            if (empty($user_id)) {
                $user_id = api_get_anonymous_id();
            }
        }
        // check course_id or get from context
        if (empty($course_id) or intval($course_id) != $course_id) {
            $course_id = api_get_course_int_id();
        }
        // check session_id or get from context
        if (empty($session_id) or intval($session_id) != $session_id) {
            $session_id = api_get_session_id();
        }
        // check learnpath_id or get from context
        if (empty($learnpath_id)) {
            global $learnpath_id;
        }
        // check learnpath_item_id or get from context
        if (empty($learnpath_item_id)) {
            global $learnpath_item_id;
        }

        $TBL_TRACK_ATTEMPT = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);

        if ($debug) {
            error_log("----- entering saveQuestionAttempt() function ------");
            error_log("answer: $answer");
            error_log("score: $score");
            error_log("question_id : $question_id");
            error_log("position: $position");
        }

        //Validation in case of fraud with active control time
        if (!ExerciseLib::exercise_time_control_is_valid($exercise_id, $learnpath_id, $learnpath_item_id)) {
            if ($debug) {
                error_log("exercise_time_control_is_valid is false");
            }
            $score = 0;
            $answer = 0;
        }
        $file = '';
        if (isset($nano)) {
            $file = Database::escape_string(basename($nano->load_filename_if_exists(false)));
        }

        if (!empty($question_id) && !empty($exe_id) && !empty($user_id)) {
            $attempt = array(
                'user_id' => $user_id,
                'question_id' => $question_id,
                'answer' => $answer,
                'marks' => $score,
                'c_id' => $course_id,
                'session_id' => $session_id,
                'position' => $position,
                'tms' => $now,
                'filename' => $file,
            );


            // Check if attempt exists.

            $sql = "SELECT exe_id FROM $TBL_TRACK_ATTEMPT
                    WHERE c_id = $course_id AND
                          session_id = $session_id AND
                          exe_id = $exe_id AND
                          user_id = $user_id AND
                          question_id = $question_id AND
                          position = $position";
            $result = Database::query($sql);
            if (Database::num_rows($result)) {
                if ($debug) {
                    error_log("Attempt already exist: exe_id: $exe_id - user_id:$user_id - question_id:$question_id");
                }
                if ($updateResults == false) {
                    //The attempt already exist do not update use  update_event_exercise() instead
                    return false;
                }
            } else {
                $attempt['exe_id'] = $exe_id;
            }

            if ($debug) {
                error_log("updateResults : $updateResults");
                error_log("Saving question attempt: ");
                error_log($sql);
            }

            $recording_table = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT_RECORDING);

            if ($updateResults == false) {
            $attempt_id = Database::insert($TBL_TRACK_ATTEMPT, $attempt);

            if (defined('ENABLED_LIVE_EXERCISE_TRACKING')) {
                if ($debug) {
                    error_log("Saving e attempt recording ");
                }
                $attempt_recording = array(
                    'exe_id' => $attempt_id,
                    'question_id' => $question_id,
                    'marks' => $score,
                    'insert_date' => $now,
                    'author' => '',
                    'session_id' => $session_id,
                );
                Database::insert($recording_table, $attempt_recording);
                }
            } else {
                Database::update($TBL_TRACK_ATTEMPT, $attempt,
                    array('exe_id = ? AND question_id = ? AND user_id = ? ' => array($exe_id, $question_id, $user_id)));

                if (defined('ENABLED_LIVE_EXERCISE_TRACKING')) {

                     $attempt_recording = array(
                        'exe_id' => $exe_id,
                        'question_id' => $question_id,
                        'marks' => $score,
                        'insert_date' => $now,
                        'author' => '',
                        'session_id' => $session_id,
                    );

                    Database::update($recording_table, $attempt_recording,
                        array('exe_id = ? AND question_id = ? AND session_id = ? ' => array($exe_id, $question_id, $session_id))
                    );
                }
                $attempt_id = $exe_id;
            }
            return $attempt_id;
        } else {
            return false;
        }
    }

    /**
     * Record an hotspot spot for this attempt at answering an hotspot question
     * @param	int		Exercise ID
     * @param	int		Question ID
     * @param	int		Answer ID
     * @param	int		Whether this answer is correct (1) or not (0)
     * @param	string	Coordinates of this point (e.g. 123;324)
     * @param	bool update results?
     * @return	boolean	Result of the insert query
     * @uses Course code and user_id from global scope $_cid and $_user
     */
    function saveExerciseAttemptHotspot($exe_id, $question_id, $answer_id, $correct, $coords, $updateResults = false, $exerciseId = 0)
    {
        global $safe_lp_id, $safe_lp_item_id;

        if ($updateResults == false) {
            // Validation in case of fraud with activated control time
            if (!ExerciseLib::exercise_time_control_is_valid($exerciseId, $safe_lp_id, $safe_lp_item_id)) {
                $correct = 0;
            }
        }
        $tbl_track_e_hotspot = Database :: get_main_table(TABLE_STATISTIC_TRACK_E_HOTSPOT);
        if ($updateResults) {
            $params = array(
                'hotspot_correct' => $correct,
                'hotspot_coordinate' => $coords
            );
            Database::update($tbl_track_e_hotspot, $params,
                array('hotspot_user_id = ? AND hotspot_exe_id = ? AND hotspot_question_id = ? AND hotspot_answer_id = ? ' => array(
                    api_get_user_id(),
                    $exe_id,
                    $question_id,
                    $answer_id,
                    $answer_id

                )));

        } else {
            $sql = "INSERT INTO $tbl_track_e_hotspot (hotspot_user_id, c_id, hotspot_exe_id, hotspot_question_id, hotspot_answer_id, hotspot_correct, hotspot_coordinate)".
                " VALUES ('".api_get_user_id()."',".
                " '".api_get_course_int_id()."', ".
                " '".Database :: escape_string($exe_id)."', ".
                " '".Database :: escape_string($question_id)."',".
                " '".Database :: escape_string($answer_id)."',".
                " '".Database :: escape_string($correct)."',".
                " '".Database :: escape_string($coords)."')";
            return $result = Database::query($sql);
        }
    }

    /**
     * Records information for common (or admin) events (in the track_e_default table)
     * @author Yannick Warnier <yannick.warnier@beeznest.com>
     * @param	string	Type of event
     * @param	string	Type of value
     * @param	string	Value
     * @param	string	Timestamp (defaults to null)
     * @param	integer	User ID (defaults to null)
     * @param	string	Course code (defaults to null)
     * @assert ('','','') === false
     */
    public static function addEvent(
        $event_type,
        $event_value_type,
        $event_value,
        $datetime = null,
        $user_id = null,
        $course_code = null
    ) {
        $TABLETRACK_DEFAULT = Database::get_main_table(TABLE_STATISTIC_TRACK_E_DEFAULT);

        if (empty($event_type)) {
            return false;
        }
        $event_type = Database::escape_string($event_type);
        $event_value_type = Database::escape_string($event_value_type);

        //Clean the user_info
        if ($event_value_type == LOG_USER_OBJECT) {
            if (is_array($event_value)) {
                unset($event_value['complete_name']);
                unset($event_value['complete_name_with_username']);
                unset($event_value['firstName']);
                unset($event_value['lastName']);
                unset($event_value['avatar_small']);
                unset($event_value['avatar_sys_path']);
                unset($event_value['avatar']);
                unset($event_value['mail']);
                unset($event_value['password']);
                unset($event_value['lastLogin']);
                unset($event_value['picture_uri']);
                $event_value = serialize($event_value);
            }
        }

        $event_value = Database::escape_string($event_value);
        $course_info = api_get_course_info($course_code);

        if (!empty($course_info)) {
            $course_id = $course_info['real_id'];
            $course_code = $course_info['code'];
            $course_code = Database::escape_string($course_code);
        } else {
            $course_id = null;
            $course_code = null;
        }

        if (!isset($datetime)) {
            $datetime = api_get_utc_datetime();
        }

        $datetime = Database::escape_string($datetime);

        if (!isset($user_id)) {
            $user_id = api_get_user_id();
        }

        $user_id = intval($user_id);

        $sql = "INSERT INTO $TABLETRACK_DEFAULT
                    (default_user_id,
                     default_cours_code,
                     c_id,
                     default_date,
                     default_event_type,
                     default_value_type,
                     default_value
                     )
                     VALUES('$user_id.',
                        '$course_code',
                        '$course_id',
                        '$datetime',
                        '$event_type',
                        '$event_value_type',
                        '$event_value')";
        Database::query($sql);
        return true;
    }

    /**
     * Get every email stored in the database
     *
     * @param int $etId
     * @return type
     * @assert () !== false
     */
    function get_all_event_types()
    {
        global $event_config;

        $sql = 'SELECT etm.id, event_type_name, activated, language_id, message, subject, dokeos_folder
                FROM '.Database::get_main_table(TABLE_EVENT_EMAIL_TEMPLATE).' etm
                INNER JOIN '.Database::get_main_table(TABLE_MAIN_LANGUAGE).' l
                ON etm.language_id = l.id';

        $events_types = Database::store_result(Database::query($sql), 'ASSOC');

        $to_return = array();
        foreach ($events_types as $et) {
            $et['nameLangVar'] = $event_config[$et["event_type_name"]]["name_lang_var"];
            $et['descLangVar'] = $event_config[$et["event_type_name"]]["desc_lang_var"];
            $to_return[] = $et;
        }
        return $to_return;
    }

    /**
     * Get users linked to an event
     *
     * @param int $etId
     * @return type
     */
    function get_users_subscribed_to_event($event_name)
    {
        $event_name = Database::escape_string($event_name);
        $sql = 'SELECT u.* FROM '.Database::get_main_table(TABLE_MAIN_USER).' u,'
            .Database::get_main_table(TABLE_MAIN_EVENT_TYPE).' e,'
            .Database::get_main_table(TABLE_EVENT_TYPE_REL_USER).' ue
                WHERE ue.user_id = u.user_id
                AND e.name = "'.$event_name.'"
                AND e.id = ue.event_type_id';
        $return = Database::store_result(Database::query($sql), 'ASSOC');
        return json_encode($return);
    }

    /**
     * Get the users related to one event
     *
     * @param string $event_name
     */
    function get_event_users($event_name)
    {
        $event_name = Database::escape_string($event_name);
        $sql = 'SELECT user.user_id,  user.firstname, user.lastname FROM '.Database::get_main_table(TABLE_MAIN_USER).' user JOIN '.Database::get_main_table(TABLE_EVENT_TYPE_REL_USER).' relUser
                ON relUser.user_id = user.user_id
                WHERE user.status <> '.ANONYMOUS.' AND relUser.event_type_name = "'.$event_name.'"';
        //For tests
        //$sql = 'SELECT user.user_id,  user.firstname, user.lastname FROM '.Database::get_main_table(TABLE_MAIN_USER);

        $user_list = Database::store_result(Database::query($sql), 'ASSOC');
        return json_encode($user_list);
    }

    function get_events_by_user_and_type($user_id, $event_type)
    {
        $TABLETRACK_DEFAULT = Database::get_main_table(TABLE_STATISTIC_TRACK_E_DEFAULT);
        $user_id = intval($user_id);
        $event_type = Database::escape_string($event_type);

        $sql = "SELECT * FROM $TABLETRACK_DEFAULT
                WHERE default_value_type = 'user_id' AND
                      default_value = $user_id AND
                      default_event_type = '$event_type'
                ORDER BY default_date ";
        $result = Database::query($sql);
        if ($result) {
            return Database::store_result($result, 'ASSOC');
        }
        return false;
    }

    function get_latest_event_by_user_and_type($user_id, $event_type)
    {
        $result = get_events_by_user_and_type($user_id, $event_type);
        if ($result && !empty($result)) {
            return $result[0];
        }
    }

    /**
     * Save the new message for one event and for one language
     *
     * @param string $eventName
     * @param array $users
     * @param string $message
     * @param string $subject
     * @param string $eventMessageLanguage
     * @param int $activated
     */
    function save_event_type_message($event_name, $users, $message, $subject, $event_message_language, $activated)
    {
        $event_name = Database::escape_string($event_name);
        $activated = intval($activated);
        $event_message_language = Database::escape_string($event_message_language);

        // Deletes then re-adds the users linked to the event
        $sql = 'DELETE FROM '.Database::get_main_table(TABLE_EVENT_TYPE_REL_USER).' WHERE event_type_name = "'.$event_name.'"	';
        Database::query($sql);

        foreach ($users as $user) {
            $sql = 'INSERT INTO '.Database::get_main_table(TABLE_EVENT_TYPE_REL_USER).' (user_id,event_type_name) VALUES('.intval($user).',"'.$event_name.'")';
            Database::query($sql);
        }
        $language_id = api_get_language_id($event_message_language);
        // check if this template in this language already exists or not
        $sql = 'SELECT COUNT(id) as total FROM '.Database::get_main_table(TABLE_EVENT_EMAIL_TEMPLATE).'
                WHERE event_type_name = "'.$event_name.'" AND language_id = '.$language_id;

        $sql = Database::store_result(Database::query($sql), 'ASSOC');

        // if already exists, we update
        if ($sql[0]["total"] > 0) {
            $sql = 'UPDATE '.Database::get_main_table(TABLE_EVENT_EMAIL_TEMPLATE).'
                SET message = "'.Database::escape_string($message).'",
                subject = "'.Database::escape_string($subject).'",
                activated = '.$activated.'
                WHERE event_type_name = "'.$event_name.'" AND language_id = (SELECT id FROM '.Database::get_main_table(TABLE_MAIN_LANGUAGE).'
                    WHERE dokeos_folder = "'.$event_message_language.'")';
            Database::query($sql);
        } else { // else we create a new record
            // gets the language_-_id
            $lang_id = '(SELECT id FROM '.Database::get_main_table(TABLE_MAIN_LANGUAGE).'
                        WHERE dokeos_folder = "'.$event_message_language.'")';
            $lang_id = Database::store_result(Database::query($lang_id), 'ASSOC');

            if (!empty($lang_id[0]["id"])) {
                $sql = 'INSERT INTO '.Database::get_main_table(TABLE_EVENT_EMAIL_TEMPLATE).' (event_type_name, language_id, message, subject, activated)
                    VALUES("'.$event_name.'", '.$lang_id[0]["id"].', "'.Database::escape_string($message).'", "'.Database::escape_string($subject).'", '.$activated.')';
                Database::query($sql);
            }
        }

        // set activated at every save
        $sql = 'UPDATE '.Database::get_main_table(TABLE_EVENT_EMAIL_TEMPLATE).'
                    SET activated = '.$activated.'
                    WHERE event_type_name = "'.$event_name.'"';
        Database::query($sql);
    }

    function eventType_mod($etId, $users, $message, $subject)
    {
        $etId = intval($etId);

        $sql = 'DELETE FROM '.Database::get_main_table(TABLE_EVENT_TYPE_REL_USER).' WHERE event_type_id = '.$etId.'	';
        Database::query($sql);

        foreach ($users as $user) {
            $sql = 'INSERT INTO '.Database::get_main_table(TABLE_EVENT_TYPE_REL_USER).' (user_id,event_type_id)
                VALUES('.intval($user).','.$etId.') ';
            Database::query($sql);
        }

        $sql = 'UPDATE '.Database::get_main_table(TABLE_MAIN_EVENT_TYPE_MESSAGE).'
                SET message = "'.Database::escape_string($message).'",
                    subject = "'.Database::escape_string($subject).'"
                    WHERE event_type_id = '.$etId.'';
        Database::query($sql);
    }

    /**
     * Gets the last attempt of an exercise based in the exe_id
     * @param int $exe_id
     * @return mixed
     */
    function getLastAttemptDateOfExercise($exe_id)
    {
        $exe_id = intval($exe_id);
        $track_attempts = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
        $sql_track_attempt = 'SELECT max(tms) as last_attempt_date FROM '.$track_attempts.' WHERE exe_id='.$exe_id;
        $rs_last_attempt = Database::query($sql_track_attempt);
        $row_last_attempt = Database::fetch_array($rs_last_attempt);
        $last_attempt_date = $row_last_attempt['last_attempt_date']; //Get the date of last attempt
        return $last_attempt_date;
    }

    /**
     * Gets the last attempt of an exercise based in the exe_id
     * @param int $exe_id
     * @return mixed
     */
    function getLatestQuestionIdFromAttempt($exe_id)
    {
        $exe_id = intval($exe_id);
        $track_attempts = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
        $sql = 'SELECT question_id FROM '.$track_attempts.' WHERE exe_id='.$exe_id.' ORDER BY tms DESC LIMIT 1';
        $result = Database::query($sql);
        if (Database::num_rows($result)) {
            $row = Database::fetch_array($result);
            return $row['question_id'];
        } else {
            return false;
        }
    }

    /**
     * Gets how many attempts exists by user, exercise, learning path
     * @param   int user id
     * @param   int exercise id
     * @param   int lp id
     * @param   int lp item id
     * @param   int lp item view id
     */
    function get_attempt_count($user_id, $exerciseId, $lp_id, $lp_item_id, $lp_item_view_id)
    {
        $stat_table = Database :: get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $user_id = intval($user_id);
        $exerciseId = intval($exerciseId);
        $lp_id = intval($lp_id);
        $lp_item_id = intval($lp_item_id);
        $lp_item_view_id = intval($lp_item_view_id);

        $sql = "SELECT count(*) as count FROM $stat_table WHERE
                    exe_exo_id 				= $exerciseId AND
                    exe_user_id 			= $user_id AND
                    status 			   	   != 'incomplete' AND
                    orig_lp_id 				= $lp_id AND
                    orig_lp_item_id 		= $lp_item_id AND
                    orig_lp_item_view_id 	= $lp_item_view_id AND
                    c_id 			        = '".api_get_course_int_id()."' AND
                    session_id 				= '".api_get_session_id()."'";

        $query = Database::query($sql);
        if (Database::num_rows($query) > 0) {
            $attempt = Database :: fetch_array($query, 'ASSOC');
            return $attempt['count'];
        } else {
            return 0;
        }
    }

    function get_attempt_count_not_finished($user_id, $exerciseId, $lp_id, $lp_item_id)
    {
        $stat_table = Database :: get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $user_id = intval($user_id);
        $exerciseId = intval($exerciseId);
        $lp_id = intval($lp_id);
        $lp_item_id = intval($lp_item_id);
        //$lp_item_view_id = intval($lp_item_view_id);

        $sql = "SELECT count(*) as count FROM $stat_table WHERE
                    exe_exo_id 			= $exerciseId AND
                    exe_user_id 		= $user_id AND
                    status 				!= 'incomplete' AND
                    orig_lp_id 			= $lp_id AND
                    orig_lp_item_id 	= $lp_item_id AND
                    c_id = '".api_get_course_int_id()."' AND
                    session_id = '".api_get_session_id()."'";

        $query = Database::query($sql);
        if (Database::num_rows($query) > 0) {
            $attempt = Database :: fetch_array($query, 'ASSOC');
            return $attempt['count'];
        } else {
            return 0;
        }
    }

    /**
     * @param int $user_id
     * @param int $lp_id
     * @param array $course
     * @param int $session_id
     */
    function delete_student_lp_events($user_id, $lp_id, $course, $session_id)
    {
        $lp_view_table = Database::get_course_table(TABLE_LP_VIEW);
        $lp_item_view_table = Database::get_course_table(TABLE_LP_ITEM_VIEW);
        $course_id = $course['real_id'];

        if (empty($course_id)) {
            $course_id = api_get_course_int_id();
        }

        $track_e_exercises = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $track_attempts = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
        $recording_table = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT_RECORDING);

        $user_id = intval($user_id);
        $lp_id = intval($lp_id);
        $session_id = intval($session_id);

        //Make sure we have the exact lp_view_id
        $sql = "SELECT id FROM $lp_view_table WHERE c_id = $course_id AND user_id = $user_id AND lp_id = $lp_id AND session_id = $session_id ";
        $result = Database::query($sql);

        if (Database::num_rows($result)) {
            $view = Database::fetch_array($result, 'ASSOC');
            $lp_view_id = $view['id'];

            $sql = "DELETE FROM $lp_item_view_table WHERE c_id = $course_id AND lp_view_id = $lp_view_id ";
            Database::query($sql);
        }

        $sql = "DELETE FROM $lp_view_table WHERE c_id = $course_id AND user_id = $user_id AND lp_id= $lp_id AND session_id = $session_id ";
        Database::query($sql);

        $sql = "SELECT exe_id FROM $track_e_exercises
                WHERE   exe_user_id = $user_id AND
                        session_id = $session_id AND
                        c_id = $course_id AND
                        orig_lp_id = $lp_id";
        $result = Database::query($sql);
        $exe_list = array();
        while ($row = Database::fetch_array($result, 'ASSOC')) {
            $exe_list[] = $row['exe_id'];
        }

        if (!empty($exe_list) && is_array($exe_list) && count($exe_list) > 0) {
            $sql_delete = "DELETE FROM $track_e_exercises   WHERE exe_id IN (".implode(',', $exe_list).")";
            Database::query($sql_delete);

            $sql_delete = "DELETE FROM $track_attempts      WHERE exe_id IN (".implode(',', $exe_list).")";
            Database::query($sql_delete);

            $sql_delete = "DELETE FROM $recording_table     WHERE exe_id IN (".implode(',', $exe_list).")";
            Database::query($sql_delete);
        }
    }

    /**
     * Delete all exercise attempts (included in LP or not)
     *
     * @param 	int		user id
     * @param 	int		exercise id
     * @param 	string	course code
     * @param 	int		session id
     */
    function delete_all_incomplete_attempts($user_id, $exercise_id, $course_id, $session_id = 0)
    {
        $track_e_exercises = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $user_id = intval($user_id);
        $exercise_id = intval($exercise_id);
        $course_id = intval($course_id);
        $session_id = intval($session_id);
        if (!empty($user_id) && !empty($exercise_id) && !empty($course_code)) {
            $sql = "DELETE FROM $track_e_exercises
                    WHERE   exe_user_id = $user_id AND
                            exe_exo_id = $exercise_id AND
                            c_id = '$course_id' AND
                            session_id = $session_id AND
                            status = 'incomplete' ";
            Database::query($sql);
        }
    }

    /**
     * Gets all exercise results (NO Exercises in LPs ) from a given exercise id, course, session
     * @param   int     exercise id
     * @param   string  course code
     * @param   int     session id
     * @return  array   with the results
     *
     */
    function get_all_exercise_results($exercise_id, $courseId, $session_id = 0, $load_question_list = true, $user_id = null)
    {
        $TABLETRACK_EXERCICES = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $TBL_TRACK_ATTEMPT = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
        $courseId = intval($courseId);
        $exercise_id = intval($exercise_id);
        $session_id = intval($session_id);

        $user_condition = null;
        if (!empty($user_id)) {
            $user_id = intval($user_id);
            $user_condition = "AND exe_user_id = $user_id ";
        }
        $sql = "SELECT * FROM $TABLETRACK_EXERCICES
                WHERE   status = ''  AND
                        c_id = '$courseId' AND
                        exe_exo_id = '$exercise_id' AND
                        session_id = $session_id  AND
                        orig_lp_id =0 AND
                        orig_lp_item_id = 0
                        $user_condition
                ORDER BY exe_id";
        $res = Database::query($sql);
        $list = array();
        while ($row = Database::fetch_array($res, 'ASSOC')) {
            $list[$row['exe_id']] = $row;
            if ($load_question_list) {
                $sql = "SELECT * FROM $TBL_TRACK_ATTEMPT WHERE exe_id = {$row['exe_id']}";
                $res_question = Database::query($sql);
                while ($row_q = Database::fetch_array($res_question, 'ASSOC')) {
                    $list[$row['exe_id']]['question_list'][$row_q['question_id']] = $row_q;
                }
            }
        }
        return $list;
    }

    /**
     * Gets all exercise results (NO Exercises in LPs ) from a given exercise id, course, session
     * @param   string  course code
     * @param   int     session id
     * @return  array   with the results
     *
     */
    function get_all_exercise_results_by_course($courseId, $session_id = 0, $get_count = true)
    {
        $table_track_exercises = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $table_track_attempt = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
        $courseId = intval($courseId);
        $session_id = intval($session_id);

        $select = '*';
        if ($get_count) {
            $select = 'count(*) as count';
        }
        $sql = "SELECT $select FROM $table_track_exercises
                WHERE   status = ''  AND
                        c_id = '$courseId' AND
                        session_id = $session_id  AND
                        orig_lp_id = 0 AND
                        orig_lp_item_id = 0
                ORDER BY exe_id";
        $res = Database::query($sql);
        if ($get_count) {
            $row = Database::fetch_array($res, 'ASSOC');
            return $row['count'];
        } else {
            $list = array();
            while ($row = Database::fetch_array($res, 'ASSOC')) {
                $list[$row['exe_id']] = $row;
                $sql = "SELECT * FROM $table_track_attempt WHERE exe_id = {$row['exe_id']}";
                $res_question = Database::query($sql);
                while ($row_q = Database::fetch_array($res_question, 'ASSOC')) {
                    $list[$row['exe_id']]['question_list'][$row_q['question_id']] = $row_q;
                }
            }
            return $list;
        }
    }

    /**
     * Gets all exercise results (NO Exercises in LPs) from a given exercise id, course, session
     * @param   int     exercise id
     * @param   string  course code
     * @param   int     session id
     * @return  array   with the results
     *
     */
    function get_all_exercise_results_by_user($user_id, $courseId, $session_id = 0)
    {
        $table_track_exercises = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $table_track_attempt = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
        $courseId = intval($courseId);
        $session_id = intval($session_id);
        $user_id = intval($user_id);

        $sql = "SELECT * FROM $table_track_exercises
                WHERE status = '' AND
                        exe_user_id = $user_id AND
                        c_id = '$courseId' AND
                        session_id = $session_id AND
                        orig_lp_id = 0 AND
                        orig_lp_item_id = 0
                ORDER by exe_id";

        $res = Database::query($sql);
        $list = array();
        while ($row = Database::fetch_array($res, 'ASSOC')) {
            $list[$row['exe_id']] = $row;
            $sql = "SELECT * FROM $table_track_attempt WHERE exe_id = {$row['exe_id']}";
            $res_question = Database::query($sql);
            while ($row_q = Database::fetch_array($res_question, 'ASSOC')) {
                $list[$row['exe_id']]['question_list'][$row_q['question_id']] = $row_q;
            }
        }
        //echo '<pre>'; print_r($list);
        return $list;
    }

    /**
     * Gets exercise results (NO Exercises in LPs) from a given exercise id, course, session
     * @param   int     exercise id
     * @param   string  course code
     * @param   int     session id
     * @return  array   with the results
     *
     */
    function get_exercise_results_by_attempt($exe_id, $status = null)
    {
        $table_track_exercises = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $table_track_attempt = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
        $table_track_attempt_recording = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT_RECORDING);
        $exe_id = intval($exe_id);

        $status = Database::escape_string($status);

        $sql = "SELECT * FROM $table_track_exercises WHERE status = '".$status."' AND exe_id = $exe_id";

        $res = Database::query($sql);
        $list = array();
        if (Database::num_rows($res)) {
            $row = Database::fetch_array($res, 'ASSOC');

            //Checking if this attempt was revised by a teacher
            $sql_revised = 'SELECT exe_id FROM '.$table_track_attempt_recording.' WHERE author != "" AND exe_id = '.$exe_id.' LIMIT 1';
            $res_revised = Database::query($sql_revised);
            $row['attempt_revised'] = 0;
            if (Database::num_rows($res_revised) > 0) {
                $row['attempt_revised'] = 1;
            }
            $list[$exe_id] = $row;
            $sql = "SELECT * FROM $table_track_attempt WHERE exe_id = $exe_id ORDER BY tms ASC";
            $res_question = Database::query($sql);
            while ($row_q = Database::fetch_array($res_question, 'ASSOC')) {
                $list[$exe_id]['question_list'][$row_q['question_id']] = $row_q;
            }
        }
        return $list;
    }

    /**
     * Gets exercise results (NO Exercises in LPs) from a given user, exercise id, course, session, lp_id, lp_item_id
     * @param   int     user id
     * @param   int     exercise id
     * @param   string  course code
     * @param   int     session id
     * @param   int     lp id
     * @param   int     lp item id
     * @param   string 	order asc or desc
     * @return  array   with the results
     *
     */
    function getExerciseResultsByUser($user_id, $exercise_id, $courseId, $session_id = 0, $lp_id = 0, $lp_item_id = 0, $order = null)
    {
        $table_track_exercises = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $table_track_attempt = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
        $table_track_attempt_recording = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT_RECORDING);
        $courseId = intval($courseId);
        $exercise_id = intval($exercise_id);
        $session_id = intval($session_id);
        $user_id = intval($user_id);
        $lp_id = intval($lp_id);
        $lp_item_id = intval($lp_item_id);

        if (!in_array(strtolower($order), array('asc', 'desc'))) {
            $order = 'asc';
        }

        $sql = "SELECT * FROM $table_track_exercises
                WHERE 	status 			= '' AND
                        exe_user_id 	= $user_id AND
                        c_id 	        = $courseId AND
                        exe_exo_id 		= $exercise_id AND
                        session_id 		= $session_id AND
                        orig_lp_id 		= $lp_id AND
                        orig_lp_item_id = $lp_item_id
                ORDER by exe_id $order ";

        $res = Database::query($sql);
        $list = array();
        while ($row = Database::fetch_array($res, 'ASSOC')) {
            //Checking if this attempt was revised by a teacher
            $sql_revised = 'SELECT exe_id FROM '.$table_track_attempt_recording.' WHERE author != "" AND exe_id = '.$row['exe_id'].' LIMIT 1';
            $res_revised = Database::query($sql_revised);
            $row['attempt_revised'] = 0;
            if (Database::num_rows($res_revised) > 0) {
                $row['attempt_revised'] = 1;
            }
            $list[$row['exe_id']] = $row;
            $sql = "SELECT * FROM $table_track_attempt WHERE exe_id = {$row['exe_id']}";
            $res_question = Database::query($sql);
            while ($row_q = Database::fetch_array($res_question, 'ASSOC')) {
                $list[$row['exe_id']]['question_list'][$row_q['question_id']] = $row_q;
            }
        }
        return $list;
    }

    /**
     * Count exercise attempts (NO Exercises in LPs ) from a given exercise id, course, session
     * @param   int     exercise id
     * @param   int     course id
     * @param   int     session id
     * @return  array   with the results
     *
     */
    function count_exercise_attempts_by_user($user_id, $exercise_id, $courseId, $session_id = 0)
    {
        $TABLETRACK_EXERCICES = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $courseId = intval($courseId);
        $exercise_id = intval($exercise_id);
        $session_id = intval($session_id);
        $user_id = intval($user_id);

        $sql = "SELECT count(*) as count FROM $TABLETRACK_EXERCICES
                WHERE status = ''  AND
                    exe_user_id = '$user_id' AND
                    c_id = '$courseId' AND
                    exe_exo_id = '$exercise_id' AND
                    session_id = $session_id AND
                    orig_lp_id =0 AND
                    orig_lp_item_id = 0
                ORDER BY exe_id";
        $res = Database::query($sql);
        $result = 0;
        if (Database::num_rows($res) > 0) {
            $row = Database::fetch_array($res, 'ASSOC');
            $result = $row['count'];
        }
        return $result;
    }

    /**
     * Gets all exercise BEST results attempts (NO Exercises in LPs ) from a given exercise id, course, session per user
     * @param   int     exercise id
     * @param   int     course id
     * @param   int     session id
     * @return  array   with the results
     * @todo rename this function
     *
     */
    function get_best_exercise_results_by_user($exercise_id, $courseId, $session_id = 0)
    {
        $table_track_exercises = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $table_track_attempt = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
        $courseId = intval($courseId);
        $exercise_id = intval($exercise_id);
        $session_id = intval($session_id);

        $sql = "SELECT * FROM $table_track_exercises
                WHERE   status = '' AND
                        c_id = $courseId AND
                        exe_exo_id = '$exercise_id' AND
                        session_id = $session_id AND
                        orig_lp_id = 0 AND
                        orig_lp_item_id = 0
                ORDER BY exe_id";

        $res = Database::query($sql);
        $list = array();
        while ($row = Database::fetch_array($res, 'ASSOC')) {
            $list[$row['exe_id']] = $row;
            $sql = "SELECT * FROM $table_track_attempt WHERE exe_id = {$row['exe_id']}";
            $res_question = Database::query($sql);
            while ($row_q = Database::fetch_array($res_question, 'ASSOC')) {
                $list[$row['exe_id']]['question_list'][$row_q['question_id']] = $row_q;
            }
        }

        //Getting the best results of every student
        $best_score_return = array();

        foreach ($list as $student_result) {
            $user_id = $student_result['exe_user_id'];
            $current_best_score[$user_id] = $student_result['exe_result'];

            if (isset($current_best_score[$user_id]) && isset($best_score_return[$user_id]) && $current_best_score[$user_id] > $best_score_return[$user_id]['exe_result']) {
                $best_score_return[$user_id] = $student_result;
            }
        }
        return $best_score_return;
    }

    /**
     * @param int $user_id
     * @param int $exercise_id
     * @param int $courseId
     * @param int $session_id
     * @return array
     */
    function get_best_attempt_exercise_results_per_user($user_id, $exercise_id, $courseId, $session_id = 0)
    {
        $table_track_exercises = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $courseId = intval($courseId);
        $exercise_id = intval($exercise_id);
        $session_id = intval($session_id);
        $user_id = intval($user_id);

        $sql = "SELECT * FROM $table_track_exercises
                WHERE   status = ''  AND
                        c_id = '$courseId' AND
                        exe_exo_id = '$exercise_id' AND
                        session_id = $session_id  AND
                        exe_user_id = $user_id AND
                        orig_lp_id =0 AND
                        orig_lp_item_id = 0
                        ORDER BY exe_id";

        $res = Database::query($sql);
        $list = array();
        while ($row = Database::fetch_array($res, 'ASSOC')) {
            $list[$row['exe_id']] = $row;  /*
              $sql = "SELECT * FROM $table_track_attempt WHERE exe_id = {$row['exe_id']}";
              $res_question = Database::query($sql);
              while($row_q = Database::fetch_array($res_question,'ASSOC')) {
              $list[$row['exe_id']]['question_list'][$row_q['question_id']] = $row_q;
              } */
        }
        //Getting the best results of every student
        $best_score_return = array();
        $best_score_return['exe_result'] = 0;

        foreach ($list as $result) {
            $current_best_score = $result;
            if ($current_best_score['exe_result'] > $best_score_return['exe_result']) {
                $best_score_return = $result;
            }
        }
        if (!isset($best_score_return['exe_weighting'])) {
            $best_score_return = array();
        }
        return $best_score_return;
    }

    /**
     * @param int $exercise_id
     * @param int $courseId
     * @param int $session_id
     * @return mixed
     */
    function count_exercise_result_not_validated($exercise_id, $courseId, $session_id = 0)
    {
        $table_track_exercises = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $table_track_attempt = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT_RECORDING);
        $courseId = intval($courseId);
        $session_id = intval($session_id);
        $exercise_id = intval($exercise_id);

        $sql = "SELECT count(e.exe_id) as count
                FROM $table_track_exercises e LEFT JOIN $table_track_attempt a  ON e.exe_id = a.exe_id
                WHERE   exe_exo_id = $exercise_id AND
                        c_id = '$courseId' AND
                        e.session_id = $session_id  AND
                        orig_lp_id = 0 AND
                        marks IS NULL AND
                        status = '' AND
                        orig_lp_item_id = 0 ORDER BY e.exe_id";
        $res = Database::query($sql);
        $row = Database::fetch_array($res, 'ASSOC');

        return $row['count'];
    }

    /**
     * Gets all exercise BEST results attempts (NO Exercises in LPs ) from a given exercise id, course, session per user
     * @param   int     exercise id
     * @param   int   course id
     * @param   int     session id
     * @return  array   with the results
     *
     */
    function get_count_exercises_attempted_by_course($courseId, $session_id = 0)
    {
        $table_track_exercises = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $courseId = intval($courseId);
        $session_id = intval($session_id);

        $sql = "SELECT DISTINCT exe_exo_id, exe_user_id
                FROM $table_track_exercises
                WHERE   status = '' AND
                        c_id = '$courseId' AND
                        session_id = $session_id AND
                        orig_lp_id =0 AND
                        orig_lp_item_id = 0
                ORDER BY exe_id";
        $res = Database::query($sql);
        $count = 0;
        if (Database::num_rows($res) > 0) {
            $count = Database::num_rows($res);
        }
        return $count;
    }

    /**
     * Gets all exercise events from a Learning Path within a Course 	nd Session
     * @param	int		exercise id
     * @param	string	course_code
     * @param 	int		session id
     * @return 	array
     */
    function get_all_exercise_event_from_lp($exercise_id, $courseId, $session_id = 0)
    {
        $table_track_exercises = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
        $table_track_attempt = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
        $courseId = intval($courseId);
        $exercise_id = intval($exercise_id);
        $session_id = intval($session_id);

        $sql = "SELECT * FROM $table_track_exercises
                WHERE   status = '' AND
                        c_id = $courseId AND
                        exe_exo_id = '$exercise_id' AND
                        session_id = $session_id AND
                        orig_lp_id !=0 AND
                        orig_lp_item_id != 0";

        $res = Database::query($sql);
        $list = array();
        while ($row = Database::fetch_array($res, 'ASSOC')) {
            $list[$row['exe_id']] = $row;
            $sql = "SELECT * FROM $table_track_attempt WHERE exe_id = {$row['exe_id']}";
            $res_question = Database::query($sql);
            while ($row_q = Database::fetch_array($res_question, 'ASSOC')) {
                $list[$row['exe_id']]['question_list'][$row_q['question_id']] = $row_q;
            }
        }
        return $list;
    }

    function get_all_exercises_from_lp($lp_id, $course_id)
    {
        $lp_item_table = Database :: get_course_table(TABLE_LP_ITEM);
        $course_id = intval($course_id);
        $lp_id = intval($lp_id);
        $sql = "SELECT * FROM $lp_item_table WHERE c_id = $course_id AND lp_id = '".$lp_id."'  ORDER BY parent_item_id, display_order";
        $res = Database::query($sql);
        $my_exercise_list = array();
        while ($row = Database::fetch_array($res, 'ASSOC')) {
            if ($row['item_type'] == 'quiz') {
                $my_exercise_list[] = $row;
            }
        }
        return $my_exercise_list;
    }

    /**
     * This function gets the comments of an exercise
     *
     * @param int $id
     * @param int $question_id
     * @return string the comment
     */
    function get_comments($exe_id, $question_id)
    {
        $table_track_attempt = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
        $sql = "SELECT teacher_comment FROM ".$table_track_attempt."
                WHERE exe_id='".Database::escape_string($exe_id)."' AND question_id = '".Database::escape_string($question_id)."'
                ORDER by question_id";
        $sqlres = Database::query($sql);
        $comm = Database::result($sqlres, 0, "teacher_comment");
        return $comm;
    }

    /**
     * @param int $exe_id
     * @return array
     */
    function getAllExerciseEventByExeId($exe_id)
    {
        $table_track_attempt = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
        $exe_id = intval($exe_id);
        $list = array();

        $sql = "SELECT * FROM $table_track_attempt WHERE exe_id = $exe_id ORDER BY position";
        $res_question = Database::query($sql);
        if (Database::num_rows($res_question)) {
            while ($row_q = Database::fetch_array($res_question, 'ASSOC')) {
                $list[$row_q['question_id']][] = $row_q;
            }
        }
        return $list;
    }

    /**
     *
     * @param int $exe_id
     * @param int $user_id
     * @param int $courseId
     * @param int $session_id
     * @param int $question_id
     */
    function delete_attempt($exe_id, $user_id, $courseId, $session_id, $question_id)
    {
        $table_track_attempt = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);

        $exe_id = intval($exe_id);
        $user_id = intval($user_id);
        $courseId = intval($courseId);
        $session_id = intval($session_id);
        $question_id = intval($question_id);

        $sql = "DELETE FROM $table_track_attempt
                WHERE   exe_id = $exe_id AND
                        user_id = $user_id AND
                        c_id = $courseId AND
                        session_id = $session_id AND
                        question_id = $question_id ";
        Database::query($sql);
    }

    /**
     * @param $exe_id
     * @param $user_id
     * @param int $courseId
     * @param $question_id
     */
    function delete_attempt_hotspot($exe_id, $user_id, $courseId, $question_id)
    {
        $table_track_attempt = Database::get_main_table(TABLE_STATISTIC_TRACK_E_HOTSPOT);

        $exe_id = intval($exe_id);
        $user_id = intval($user_id);
        $courseId = intval($courseId);
        $question_id = intval($question_id);

        $sql = "DELETE FROM $table_track_attempt
                WHERE   hotspot_exe_id = $exe_id AND
                        hotspot_user_id = $user_id AND
                        c_id = $courseId AND
                        hotspot_question_id = $question_id ";
        Database::query($sql);
    }

    function getAnsweredQuestionsFromAttempt($exe_id, $objExercise)
    {
        $attempt_list = getAllExerciseEventByExeId($exe_id);
        $exercise_result = array();
        if (!empty($attempt_list)) {
            foreach ($attempt_list as $question_id => $options) {
                foreach ($options as $item) {
                    $question_obj = Question::read($item['question_id']);
                    switch ($question_obj->type) {
                        case FILL_IN_BLANKS:
                            $item['answer'] = $objExercise->fill_in_blank_answer_to_string($item['answer']);
                            break;
                        case HOT_SPOT:
                            //var_dump($item['answer']);
                            break;
                    }

                    if ($item['answer'] != '0' && !empty($item['answer'])) {
                        $exercise_result[] = $question_id;
                        break;
                    }
                }
            }
        }
        return $exercise_result;
    }

    /**
     * User logs in for the first time to a course
     * @param $course_code
     * @param $user_id
     * @param $session_id
     */
    function event_course_login($courseId, $user_id, $session_id)
    {
        $course_tracking_table = Database::get_main_table(TABLE_STATISTIC_TRACK_E_COURSE_ACCESS);
        $time = api_get_datetime();

        $courseId = Database::escape_string($courseId);
        $user_id = Database::escape_string($user_id);
        $session_id = Database::escape_string($session_id);

        $sql = "INSERT INTO $course_tracking_table(c_id, user_id, login_course_date, logout_course_date, counter, session_id)
                  VALUES('".$courseId."', '".$user_id."', '$time', '$time', '1', '".$session_id."')";
        Database::query($sql);

        //Course catalog stats modifications see #4191
        CourseManager::update_course_ranking(null, null, null, null, true, false);
    }

    /**
     * For the sake of genericity, this function is a switch.
     * It's called by EventsDispatcher and fires the good function
     * with the good require_once.
     *
     * @param string $event_name
     * @param array $params
     */
    function event_send_mail($event_name, $params)
    {
        EventsMail::send_mail($event_name, $params);
    }

    /**
     * Internal function checking if the mail was already sent from that user to that user
     * @param string $event_name
     * @param int $user_from
     * @param int $user_to
     * @return boolean
     */
    function check_if_mail_already_sent($event_name, $user_from, $user_to = null)
    {
        if ($user_to == null) {
            $sql = 'SELECT COUNT(*) as total FROM '.Database::get_main_table(TABLE_EVENT_SENT).'
                    WHERE user_from = '.$user_from.' AND event_type_name = "'.$event_name.'"';
        } else {
            $sql = 'SELECT COUNT(*) as total FROM '.Database::get_main_table(TABLE_EVENT_SENT).'
                    WHERE user_from = '.$user_from.' AND user_to = '.$user_to.' AND event_type_name = "'.$event_name.'"';
        }
        $result = Database::store_result(Database::query($sql), 'ASSOC');
        return $result[0]["total"];
    }

    /**
     *
     * Filter EventEmailTemplate Filters see the main/inc/conf/events.conf.dist.php
     *
     */

    /**
     * Basic template event message filter (to be used by other filters as default)
     * @param array $values (passing by reference)
     * @return boolean True if everything is OK, false otherwise
     */
    function _event_send_mail_filter_func(&$values)
    {
        return true;
    }

    /**
     * user_registration - send_mail filter
     * @param array $values (passing by reference)
     * @return boolean True if everything is OK, false otherwise
     */
    function user_registration_event_send_mail_filter_func(&$values)
    {
        $res = _event_send_mail_filter_func($values);
        // proper logic for this filter
        return $res;
    }

    /**
     * portal_homepage_edited - send_mail filter
     * @param array $values (passing by reference)
     * @return boolean True if everything is OK, false otherwise
     */
    function portal_homepage_edited_event_send_mail_filter_func(&$values)
    {
        $res = _event_send_mail_filter_func($values);
        // proper logic for this filter
        return $res;
    }
}
