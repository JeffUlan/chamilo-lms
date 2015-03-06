<?php
/* For licensing terms, see /license.txt*/

/**
 * Class CourseManager
 *
 * This is the course library for Chamilo.
 *
 * All main course functions should be placed here.
 *
 * Many functions of this library deal with providing support for
 * virtual/linked/combined courses (this was already used in several universities
 * but not available in standard Chamilo).
 *
 * The implementation changed, initially a course was a real course
 * if target_course_code was 0 , this was changed to NULL.
 * There are probably some places left with the wrong code.
 *
 * @package chamilo.library
 */
class CourseManager
{
    const MAX_COURSE_LENGTH_CODE = 40;
    /** This constant is used to show separate user names in the course
     * list (userportal), footer, etc */
    const USER_SEPARATOR = ' |';
    const COURSE_FIELD_TYPE_CHECKBOX = 10;
    public $columns = array();

    /**
     * Creates a course
     * @param   array $params columns in the main.course table
     *
     * @return  mixed  false if the course was not created, array with the course info
     */
    public static function create_course($params, $extraFields = array())
    {
        global $_configuration;
        // Check portal limits
        $access_url_id = 1;
        if (api_get_multiple_access_url()) {
            $access_url_id = api_get_current_access_url_id();
        }

        if (isset($_configuration[$access_url_id]) &&
            is_array($_configuration[$access_url_id])
        ) {
            if (isset($_configuration[$access_url_id]['hosting_limit_courses']) &&
                $_configuration[$access_url_id]['hosting_limit_courses'] > 0
            ) {
                $num = self::count_courses($access_url_id);
                if ($num >= $_configuration[$access_url_id]['hosting_limit_courses']) {
                    api_warn_hosting_contact('hosting_limit_courses');

                    return api_set_failure(get_lang('PortalCoursesLimitReached'));
                }
            }

            if (isset($_configuration[$access_url_id]['hosting_limit_active_courses']) &&
                $_configuration[$access_url_id]['hosting_limit_active_courses'] > 0
            ) {
                $num = self::countActiveCourses($access_url_id);
                if ($num >= $_configuration[$access_url_id]['hosting_limit_active_courses']) {
                    api_warn_hosting_contact('hosting_limit_active_courses');

                    return api_set_failure(
                        get_lang('PortalActiveCoursesLimitReached')
                    );
                }
            }
        }


        if (empty($params['title'])) {
            return false;
        }

        if (empty($params['wanted_code'])) {
            $params['wanted_code'] = $params['title'];
            // Check whether the requested course code has already been occupied.
            $params['wanted_code'] = CourseManager::generate_course_code(api_substr($params['title'], 0,
                    self::MAX_COURSE_LENGTH_CODE));
        }

        // Create the course keys
        $keys = AddCourse::define_course_keys($params['wanted_code']);

        $params['exemplary_content'] = isset($params['exemplary_content']) ? $params['exemplary_content'] : false;

        if (count($keys)) {

            $params['code'] = $keys['currentCourseCode'];
            $params['visual_code'] = $keys['currentCourseId'];
            $params['directory'] = $keys['currentCourseRepository'];

            $course_info = api_get_course_info($params['code']);

            if (empty($course_info)) {
                $course_id = AddCourse::register_course($params);
                $course_info = api_get_course_info_by_id($course_id);

                if (!empty($course_info)) {
                    AddCourse::prepare_course_repository($course_info['directory'], $course_info['code']);
                    AddCourse::fill_db_course($course_id, $course_info['directory'], $course_info['course_language'],
                        $params['exemplary_content']);

                    if (api_get_setting('gradebook_enable_grade_model') == 'true') {
                        //Create gradebook_category for the new course and add a gradebook model for the course
                        if (isset($params['gradebook_model_id']) && !empty($params['gradebook_model_id']) && $params['gradebook_model_id'] != '-1') {
                            GradebookUtils::create_default_course_gradebook($course_info['code'],
                                $params['gradebook_model_id']);
                        }
                    }
                    // If parameter defined, copy the contents from a specific
                    // template course into this new course
                    if (!empty($_configuration['course_creation_use_template'])) {
                        // Include the necessary libraries to generate a course copy
                        require_once api_get_path(SYS_CODE_PATH) . 'coursecopy/classes/CourseBuilder.class.php';
                        require_once api_get_path(SYS_CODE_PATH) . 'coursecopy/classes/CourseRestorer.class.php';
                        require_once api_get_path(SYS_CODE_PATH) . 'coursecopy/classes/CourseSelectForm.class.php';
                        // Call the course copy object
                        $originCourse = api_get_course_info_by_id($_configuration['course_creation_use_template']);
                        $originCourse['official_code'] = $originCourse['code'];
                        $cb = new CourseBuilder(null, $originCourse);
                        $course = $cb->build(null, $originCourse['code']);
                        $cr = new CourseRestorer($course);
                        $cr->set_file_option();
                        $cr->restore($course_info['id']); //course_info[id] is the course.code value (I know...)
                    }

                    $params['course_code'] = $course_info['code'];

                    $courseFieldValue = new ExtraFieldValue('course');
                    $courseFieldValue->save_field_values($params);

                    return $course_info;
                }
            }
        }

        return false;
    }

    /**
     * Returns all the information of a given course code
     * @param string $course_code , the course code
     * @return an array with all the fields of the course table
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     * @assert ('') === false
     */
    public static function get_course_information($course_code)
    {
        return Database::fetch_array(Database::query(
                "SELECT *, id as real_id FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . "
            WHERE code='" . Database::escape_string($course_code) . "'"), 'ASSOC'
        );
    }

    /**
     * Returns all the information of a given coursecode
     * @param   int     the course id
     * @return an array with all the fields of the course table
     * @deprecated use api_get_course_info_by_id()
     * @assert ('') === false
     */
    public static function get_course_information_by_id($course_id)
    {
        return Database::select('*, id as real_id', Database::get_main_table(TABLE_MAIN_COURSE),
            array('where' => array('id = ?' => intval($course_id))), 'first');
    }

    /**
     * Returns a list of courses. Should work with quickform syntax
     * @param    integer $from Offset (from the 7th = '6'). Optional.
     * @param    integer $howmany Number of results we want. Optional.
     * @param    string $orderby The column we want to order it by. Optional, defaults to first column.
     * @param    string $orderdirection The direction of the order (ASC or DESC). Optional, defaults to ASC.
     * @param    string $visibility The visibility of the course, or all by default.
     * @param    string $startwith If defined, only return results for which the course *title* begins with this string
     * @param    string $urlId The Access URL ID, if using multiple URLs
     * @param    string $alsoSearchCode An extension option to indicate that we also want to search for course codes (not *only* titles)
     * @param array $conditions
     * @return array
     */
    public static function get_courses_list(
        $from = 0,
        $howmany = 0,
        $orderby = 1,
        $orderdirection = 'ASC',
        $visibility = -1,
        $startwith = '',
        $urlId = null,
        $alsoSearchCode = false,
        $conditionsLike = array()
    ) {
        $sql = "SELECT course.* FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . " course ";

        if (!empty($urlId)) {
            $table = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
            $sql .= " INNER JOIN $table url ON (url.course_code = course.code) ";
        }

        if (!empty($startwith)) {
            $sql .= "WHERE (title LIKE '" . Database::escape_string($startwith) . "%' ";
            if ($alsoSearchCode) {
                $sql .= "OR code LIKE '" . Database::escape_string($startwith) . "%' ";
            }
            $sql .= ') ';
            if ($visibility !== -1 && $visibility == strval(intval($visibility))) {
                $sql .= " AND visibility = $visibility ";
            }
        } else {
            $sql .= "WHERE 1 ";
            if ($visibility !== -1 && $visibility == strval(intval($visibility))) {
                $sql .= " AND visibility = $visibility ";
            }
        }

        if (!empty($urlId)) {
            $urlId = intval($urlId);
            $sql .= " AND access_url_id= $urlId";
        }

        $allowedFields = array(
            'title',
            'code'
        );

        if (count($conditionsLike) > 0) {
            $sql .= ' AND ';
            $temp_conditions = array();
            foreach ($conditionsLike as $field => $value) {
                if (!in_array($field, $allowedFields)) {
                    continue;
                }
                $field = Database::escape_string($field);
                $value = Database::escape_string($value);
                $simple_like = false;
                if ($simple_like) {
                    $temp_conditions[] = $field . " LIKE '$value%'";
                } else {
                    $temp_conditions[] = $field . ' LIKE \'%' . $value . '%\'';
                }
            }
            $condition = ' AND ';
            if (!empty($temp_conditions)) {
                $sql .= implode(' ' . $condition . ' ', $temp_conditions);
            }
        }

        if (!empty($orderby)) {
            $sql .= " ORDER BY " . Database::escape_string($orderby) . " ";
        } else {
            $sql .= " ORDER BY 1 ";
        }

        if (!in_array($orderdirection, array('ASC', 'DESC'))) {
            $sql .= 'ASC';
        } else {
            $sql .= ($orderdirection == 'ASC' ? 'ASC' : 'DESC');
        }

        if (!empty($howmany) && is_int($howmany) and $howmany > 0) {
            $sql .= ' LIMIT ' . Database::escape_string($howmany);
        } else {
            $sql .= ' LIMIT 1000000'; //virtually no limit
        }
        if (!empty($from)) {
            $from = intval($from);
            $sql .= ' OFFSET ' . intval($from);
        } else {
            $sql .= ' OFFSET 0';
        }

        return Database::store_result(Database::query($sql));
    }

    /**
     * Returns the access settings of the course:
     * which visibility;
     * wether subscribing is allowed;
     * wether unsubscribing is allowed.
     *
     * @param string $course_code , the course code
     * @todo for more consistency: use course_info call from database API
     * @return an array with int fields "visibility", "subscribe", "unsubscribe"
     * @assert ('') === false
     */
    public static function get_access_settings($course_code)
    {
        return Database::fetch_array(Database::query(
                "SELECT visibility, subscribe, unsubscribe from " . Database::get_main_table(TABLE_MAIN_COURSE) . "
            WHERE code = '" . Database::escape_string($course_code) . "'")
        );
    }

    /**
     * Returns the status of a user in a course, which is COURSEMANAGER or STUDENT.
     * @param   int $user_id
     * @param   string $course_code
     *
     * @return int the status of the user in that course
     */
    public static function get_user_in_course_status($user_id, $course_code)
    {
        $result = Database::fetch_array(Database::query(
                "SELECT status FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . "
            WHERE course_code = '" . Database::escape_string($course_code) . "' AND user_id = " . intval($user_id))
        );

        return $result['status'];
    }

    /**
     * @param int $user_id
     * @param string $course_code
     * @return mixed
     */
    public static function get_tutor_in_course_status($user_id, $course_code)
    {
        $result = Database::fetch_array(Database::query(
                "SELECT tutor_id FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . "
                WHERE course_code = '" . Database::escape_string($course_code) . "' AND user_id = " . intval($user_id))
        );

        return $result['tutor_id'];
    }

    /**
     * Unsubscribe one or more users from a course
     *
     * @param   mixed   user_id or an array with user ids
     * @param   string  course code
     * @param   int     session id
     * @assert ('', '') === false
     *
     */
    public static function unsubscribe_user($user_id, $course_code, $session_id = 0)
    {
        if (!is_array($user_id)) {
            $user_id = array($user_id);
        }

        if (count($user_id) == 0) {
            return;
        }

        if (!empty($session_id)) {
            $session_id = intval($session_id);
        } else {
            $session_id = api_get_session_id();
        }

        $user_list = array();

        // Cleaning the $user_id variable
        if (is_array($user_id)) {
            $new_user_id_list = array();
            foreach ($user_id as $my_user_id) {
                $new_user_id_list[] = intval($my_user_id);
            }
            $new_user_id_list = array_filter($new_user_id_list);
            $user_list = $new_user_id_list;
            $user_ids = implode(',', $new_user_id_list);
        } else {
            $user_ids = intval($user_id);
            $user_list[] = $user_id;
        }

        $course_info = api_get_course_info($course_code);
        $course_id = $course_info['real_id'];

        // Unsubscribe user from all groups in the course.
        $sql = "DELETE FROM " . Database::get_course_table(TABLE_GROUP_USER) . "
                WHERE c_id = $course_id AND user_id IN (" . $user_ids . ")";
        Database::query($sql);
        $sql = "DELETE FROM " . Database::get_course_table(TABLE_GROUP_TUTOR) . "
                WHERE c_id = $course_id AND user_id IN (" . $user_ids . ")";
        Database::query($sql);

        // Erase user student publications (works) in the course - by André Boivin

        if (!empty($user_list)) {
            require_once api_get_path(SYS_CODE_PATH) . 'work/work.lib.php';
            foreach ($user_list as $userId) {
                // Getting all work from user
                $workList = getWorkPerUser($userId);
                if (!empty($workList)) {
                    foreach ($workList as $work) {
                        $work = $work['work'];
                        // Getting user results
                        if (!empty($work->user_results)) {
                            foreach ($work->user_results as $workSent) {
                                deleteWorkItem($workSent['id'], $course_info);
                            }
                        }
                    }
                }
            }
        }

        // Unsubscribe user from all blogs in the course.
        Database::query("DELETE FROM " . Database::get_course_table(TABLE_BLOGS_REL_USER) . " WHERE c_id = $course_id AND  user_id IN (" . $user_ids . ")");
        Database::query("DELETE FROM " . Database::get_course_table(TABLE_BLOGS_TASKS_REL_USER) . " WHERE c_id = $course_id AND  user_id IN (" . $user_ids . ")");

        // Deleting users in forum_notification and mailqueue course tables
        $sql = "DELETE FROM  " . Database::get_course_table(TABLE_FORUM_NOTIFICATION) . "
                WHERE c_id = $course_id AND user_id IN (" . $user_ids . ")";
        Database::query($sql);

        $sql = "DELETE FROM " . Database::get_course_table(TABLE_FORUM_MAIL_QUEUE) . "
                WHERE c_id = $course_id AND user_id IN (" . $user_ids . ")";
        Database::query($sql);

        // Unsubscribe user from the course.
        if (!empty($session_id)) {

            // Delete in table session_rel_course_rel_user
            $sql = "DELETE FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . "
                    WHERE   id_session ='" . $session_id . "' AND
                            course_code = '" . Database::escape_string($course_info['code']) . "' AND
                            id_user IN ($user_ids)";
            Database::query($sql);

            foreach ($user_list as $uid) {
                // check if a user is register in the session with other course
                $sql = "SELECT id_user FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . "
                        WHERE id_session='$session_id' AND id_user='$uid'";
                $rs = Database::query($sql);

                if (Database::num_rows($rs) == 0) {
                    // Delete in table session_rel_user
                    $sql = "DELETE FROM " . Database::get_main_table(TABLE_MAIN_SESSION_USER) . "
                            WHERE id_session ='" . $session_id . "' AND id_user='$uid' AND relation_type<>" . SESSION_RELATION_TYPE_RRHH . "";
                    Database::query($sql);
                }
            }

            // Update the table session
            $sql = "SELECT COUNT(*) FROM " . Database::get_main_table(TABLE_MAIN_SESSION_USER) . "
                    WHERE id_session = '" . $session_id . "' AND relation_type <> " . SESSION_RELATION_TYPE_RRHH;
            $row = Database::fetch_array(Database::query($sql));
            $count = $row[0];
            // number of users by session
            $sql = "UPDATE " . Database::get_main_table(TABLE_MAIN_SESSION) . " SET nbr_users = '$count'
                    WHERE id = '" . $session_id . "'";
            Database::query($sql);

            // Update the table session_rel_course
            $sql = "SELECT COUNT(*) FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . "
                    WHERE id_session = '$session_id' AND course_code = '$course_code' AND status<>2";
            $row = Database::fetch_array(@Database::query($sql));
            $count = $row[0];

            // number of users by session and course
            $sql = "UPDATE " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE) . "
                    SET nbr_users = '$count'
                    WHERE id_session = '$session_id' AND course_code = '$course_code'";
            Database::query($sql);

        } else {
            $sql = "DELETE FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . "
                    WHERE
                        user_id IN (" . $user_ids . ") AND
                        relation_type<>" . COURSE_RELATION_TYPE_RRHH . " AND
                        course_code = '" . $course_code . "'";
            Database::query($sql);

            // add event to system log
            $user_id = api_get_user_id();
            Event::addEvent(
                LOG_UNSUBSCRIBE_USER_FROM_COURSE,
                LOG_COURSE_CODE,
                $course_code,
                api_get_utc_datetime(),
                $user_id
            );

            foreach ($user_list as $userId) {
                $userInfo = api_get_user_info($userId);
                Event::addEvent(
                    LOG_UNSUBSCRIBE_USER_FROM_COURSE,
                    LOG_USER_OBJECT,
                    $userInfo,
                    api_get_utc_datetime(),
                    $user_id
                );
            }
        }
    }

    /**
     * Subscribe a user to a course. No checks are performed here to see if
     * course subscription is allowed.
     * @param   int     User ID
     * @param   string  Course code
     * @param   int     Status (STUDENT, COURSEMANAGER, COURSE_ADMIN, NORMAL_COURSE_MEMBER)
     * @return  bool    True on success, false on failure
     * @see add_user_to_course
     * @assert ('', '') === false
     */
    public static function subscribe_user(
        $user_id,
        $course_code,
        $status = STUDENT,
        $session_id = 0,
        $userCourseCategoryId = 0
    ) {
        if ($user_id != strval(intval($user_id))) {
            return false; //detected possible SQL injection
        }

        $course_code = Database::escape_string($course_code);
        $userCourseCategoryId = intval($userCourseCategoryId);

        if (empty($user_id) || empty ($course_code)) {
            return false;
        }

        if (!empty($session_id)) {
            $session_id = intval($session_id);
        } else {
            $session_id = api_get_session_id();
        }

        $status = ($status == STUDENT || $status == COURSEMANAGER) ? $status : STUDENT;
        //$role_id = ($status == COURSEMANAGER) ? COURSE_ADMIN : NORMAL_COURSE_MEMBER;

        // A preliminary check whether the user has bben already registered on the platform.
        if (Database::num_rows(@Database::query("SELECT status FROM " . Database::get_main_table(TABLE_MAIN_USER) . "
                WHERE user_id = '$user_id' ")) == 0
        ) {
            return false; // The user has not been registered to the platform.
        }

        // Check whether the user has not been already subscribed to the course.
        if (empty($session_id)) {
            if (Database::num_rows(@Database::query("SELECT * FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . "
                    WHERE user_id = '$user_id' AND relation_type<>" . COURSE_RELATION_TYPE_RRHH . " AND course_code = '$course_code'")) > 0
            ) {
                return false; // The user has been already subscribed to the course.
            }
        }

        if (!empty($session_id)) {

            // Check whether the user has not already been stored in the session_rel_course_user table
            if (Database::num_rows(@Database::query("SELECT * FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . "
                    WHERE course_code = '" . $course_code . "'
                    AND id_session ='" . $session_id . "'
                    AND id_user = '" . $user_id . "'")) > 0
            ) {
                return false;
            }

            // check if the user is registered in the session with other course
            $sql = "SELECT id_user FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . "
                    WHERE id_session='" . $session_id . "' AND id_user='$user_id'";
            $rs = Database::query($sql);
            if (Database::num_rows($rs) == 0) {
                // Check whether the user has not already been stored in the session_rel_user table
                if (Database::num_rows(@Database::query("SELECT * FROM " . Database::get_main_table(TABLE_MAIN_SESSION_USER) . "
                        WHERE id_session ='" . $session_id . "'
                        AND id_user = '" . $user_id . "' AND relation_type<>" . SESSION_RELATION_TYPE_RRHH . " ")) > 0
                ) {
                    return false;
                }
            }

            // Add him/her in the table session_rel_course_rel_user
            @Database::query("INSERT INTO " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . "
                    SET id_session ='" . $session_id . "',
                    course_code = '" . $course_code . "',
                    id_user = '" . $user_id . "'");

            // Add him/her in the table session_rel_user
            @Database::query("INSERT INTO " . Database::get_main_table(TABLE_MAIN_SESSION_USER) . "
                    SET id_session ='" . $session_id . "',
                    id_user = '" . $user_id . "'");

            // Update the table session
            $row = Database::fetch_array(@Database::query("SELECT COUNT(*) FROM " . Database::get_main_table(TABLE_MAIN_SESSION_USER) . " WHERE id_session = '" . $session_id . "' AND relation_type<>" . SESSION_RELATION_TYPE_RRHH . ""));
            $count = $row[0]; // number of users by session
            $result = @Database::query("UPDATE " . Database::get_main_table(TABLE_MAIN_SESSION) . " SET nbr_users = '$count' WHERE id = '" . $session_id . "'");

            // Update the table session_rel_course
            $row = Database::fetch_array(@Database::query("SELECT COUNT(*) FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . " WHERE id_session = '" . $session_id . "' AND course_code = '$course_code' AND status<>2"));
            $count = $row[0]; // number of users by session
            $result = @Database::query("UPDATE " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE) . " SET nbr_users = '$count' WHERE id_session = '" . $session_id . "' AND course_code = '$course_code' ");

        } else {
            $course_sort = self::userCourseSort($user_id, $course_code);
            $sql = "INSERT INTO " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . "
                    SET course_code = '$course_code',
                        user_id     = '$user_id',
                        status      = '" . $status . "',
                        sort        = '" . ($course_sort) . "',
                        user_course_cat = $userCourseCategoryId
                    ";
            $result = @Database::query($sql);

            // Add event to the system log
            Event::addEvent(
                LOG_SUBSCRIBE_USER_TO_COURSE,
                LOG_COURSE_CODE,
                $course_code,
                api_get_utc_datetime(),
                api_get_user_id()
            );

            $user_info = api_get_user_info($user_id);
            Event::addEvent(
                LOG_SUBSCRIBE_USER_TO_COURSE,
                LOG_USER_OBJECT,
                $user_info,
                api_get_utc_datetime(),
                api_get_user_id()
            );
        }
        return (bool)$result;
    }

    /**
     * Get the course id based on the original id and field name in the
     * extra fields. Returns 0 if course was not found
     *
     * @param string Original course id
     * @param string Original field name
     * @return int Course id
     * @assert ('', '') === false
     */
    public static function get_course_code_from_original_id($original_course_id_value, $original_course_id_name)
    {
        $t_cfv = Database::get_main_table(TABLE_MAIN_COURSE_FIELD_VALUES);
        $table_field = Database::get_main_table(TABLE_MAIN_COURSE_FIELD);
        $sql = "SELECT course_code FROM $table_field cf INNER JOIN $t_cfv cfv ON cfv.field_id=cf.id
                WHERE field_variable='$original_course_id_name' AND field_value='$original_course_id_value'";
        $res = Database::query($sql);
        $row = Database::fetch_object($res);
        if ($row) {
            return $row->course_code;
        } else {
            return 0;
        }
    }

    /**
     * Gets the course code from the course id. Returns null if course id was not found
     *
     * @param int Course id
     * @return string Course code
     * @assert ('') === false
     */
    public static function get_course_code_from_course_id($id)
    {
        $table = Database::get_main_table(TABLE_MAIN_COURSE);
        $id = intval($id);
        $sql = "SELECT code FROM $table WHERE id = '$id' ";
        $res = Database::query($sql);
        $row = Database::fetch_object($res);
        if ($row) {
            return $row->code;
        } else {
            return null;
        }
    }

    /**
     * Subscribe a user $user_id to a course $course_code.
     * @author Hugues Peeters
     * @author Roan Embrechts
     *
     * @param  int $user_id the id of the user
     * @param  string $course_code the course code
     * @param string $status (optional) The user's status in the course
     *
     * @return boolean true if subscription succeeds, boolean false otherwise.
     * @assert ('', '') === false
     */
    public static function add_user_to_course($user_id, $course_code, $status = STUDENT)
    {
        $debug = false;
        $user_table = Database::get_main_table(TABLE_MAIN_USER);
        $course_table = Database::get_main_table(TABLE_MAIN_COURSE);
        $course_user_table = Database::get_main_table(TABLE_MAIN_COURSE_USER);

        $status = ($status == STUDENT || $status == COURSEMANAGER) ? $status : STUDENT;
        if (empty($user_id) || empty($course_code) || ($user_id != strval(intval($user_id)))) {
            return false;
        }
        $course_code = Database::escape_string($course_code);

        // Check in advance whether the user has already been registered on the platform.
        $sql = "SELECT status FROM " . $user_table . " WHERE user_id = '$user_id' ";
        if (Database::num_rows(Database::query($sql)) == 0) {
            if ($debug) {
                error_log('The user has not been registered to the platform');
            }
            return false; // The user has not been registered to the platform.
        }

        // Check whether the user has already been subscribed to this course.
        $sql = "SELECT * FROM $course_user_table
                WHERE
                    user_id = '$user_id' AND
                    relation_type<>" . COURSE_RELATION_TYPE_RRHH . " AND
                    course_code = '$course_code'";
        if (Database::num_rows(Database::query($sql)) > 0) {
            if ($debug) {
                error_log('The user has been already subscribed to the course');
            }
            return false; // The user has been subscribed to the course.
        }

        // Check in advance whether subscription is allowed or not for this course.
        $sql = "SELECT code, visibility FROM $course_table
                WHERE code = '$course_code' AND subscribe = '" . SUBSCRIBE_NOT_ALLOWED . "'";
        if (Database::num_rows(Database::query($sql)) > 0) {
            if ($debug) {
                error_log('Subscription is not allowed for this course');
            }
            return false; // Subscription is not allowed for this course.
        }

        // Ok, subscribe the user.
        $max_sort = api_max_sort_value('0', $user_id);
        return (bool)Database::query("INSERT INTO " . $course_user_table . "
                SET course_code = '$course_code',
                user_id = '$user_id',
                status = '" . $status . "',
                sort = '" . ($max_sort + 1) . "'");
    }

    /**
     *    Checks wether a parameter exists.
     *    If it doesn't, the function displays an error message.
     *
     * @return true if parameter is set and not empty, false otherwise
     * @todo move function to better place, main_api ?
     */
    public static function check_parameter($parameter, $error_message)
    {
        if (empty($parameter)) {
            Display::display_normal_message($error_message);
            return false;
        }
        return true;
    }

    /**
     *    Lets the script die when a parameter check fails.
     * @todo move function to better place, main_api ?
     */
    public static function check_parameter_or_fail($parameter, $error_message)
    {
        if (!self::check_parameter($parameter, $error_message)) {
            die();
        }
    }

    /**
     * @return true if there already are one or more courses
     *  with the same code OR visual_code (visualcode), false otherwise
     */
    public static function course_code_exists($wanted_course_code)
    {
        $wanted_course_code = Database::escape_string($wanted_course_code);
        $sql = "SELECT COUNT(*) as number FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . "
                WHERE code = '$wanted_course_code' OR visual_code = '$wanted_course_code'";
        $result = Database::fetch_array(Database::query($sql));
        return $result['number'] > 0;
    }

    /**
     * @return an array with the course info of all real courses on the platform
     */
    public static function get_real_course_list()
    {
        $sql_result = Database::query("SELECT * FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . "
                        WHERE target_course_code IS NULL");
        $real_course_list = array();
        while ($result = Database::fetch_array($sql_result)) {
            $real_course_list[$result['code']] = $result;
        }
        return $real_course_list;
    }

    /**
     * Lists all virtual courses
     * @return array   Course info (course code => details) of all virtual courses on the platform
     * @deprecated virtual course feature is not supported
     */
    public static function get_virtual_course_list()
    {
        $sql_result = Database::query("SELECT * FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . " WHERE target_course_code IS NOT NULL");
        $virtual_course_list = array();
        while ($result = Database::fetch_array($sql_result)) {
            $virtual_course_list[$result['code']] = $result;
        }
        return $virtual_course_list;
    }

    /**
     * Returns an array with the course info of the real courses of which
     * the current user is course admin
     * @return array   A list of courses details for courses to which the user is subscribed as course admin (status = 1)
     */
    public static function get_real_course_list_of_user_as_course_admin($user_id)
    {
        $result_array = array();
        if ($user_id != strval(intval($user_id))) {
            return $result_array;
        }
        $sql_result = Database::query("SELECT *
                FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . " course
                LEFT JOIN " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . " course_user
                ON course.code = course_user.course_code
                WHERE course.target_course_code IS NULL
                    AND course_user.user_id = '$user_id'
                    AND course_user.status = '1'");
        if ($sql_result === false) {
            return $result_array;
        }
        while ($result = Database::fetch_array($sql_result)) {
            $result_array[] = $result;
        }

        return $result_array;
    }

    /**
     * Get course list as coach
     *
     * @param int $user_id
     * @param bool $include_courses_in_sessions
     * @return array Course list
     *
     **/
    public static function get_course_list_as_coach($user_id, $include_courses_in_sessions = false)
    {
        // 1. Getting courses as teacher (No session)
        $courses_temp = CourseManager::get_course_list_of_user_as_course_admin($user_id);
        $courseList = array();

        if (!empty($courses_temp)) {
            foreach ($courses_temp as $course_item) {
                $courseList[0][$course_item['course_code']] = $course_item['course_code'];
            }
        }

        //2. Include courses in sessions
        if ($include_courses_in_sessions) {
            $sessions = Tracking::get_sessions_coached_by_user($user_id);

            if (!empty($sessions)) {
                foreach ($sessions as $session_item) {
                    $courses = Tracking:: get_courses_followed_by_coach($user_id, $session_item['id']);
                    if (is_array($courses)) {
                        foreach ($courses as $course_item) {
                            $courseList[$session_item['id']][$course_item] = $course_item;
                        }
                    }
                }
            }
        }

        return $courseList;
    }

    /**
     * @param int $user_id
     * @param bool $include_sessions
     * @return array
     */
    public static function get_user_list_from_courses_as_coach($user_id, $include_sessions = true)
    {
        $students_in_courses = array();
        $sessions = CourseManager::get_course_list_as_coach($user_id, true);

        if (!empty($sessions)) {
            foreach ($sessions as $session_id => $courses) {
                if (!$include_sessions) {
                    if (!empty($session_id)) {
                        continue;
                    }
                }
                if (empty($session_id)) {
                    foreach ($courses as $course_code) {
                        $students_in_course = CourseManager::get_user_list_from_course_code($course_code);

                        foreach ($students_in_course as $user_item) {
                            //Only students
                            if ($user_item['status_rel'] == STUDENT) {
                                $students_in_courses[$user_item['user_id']] = $user_item['user_id'];
                            }
                        }
                    }
                } else {
                    $students_in_course = SessionManager::get_users_by_session($session_id, '0');
                    if (is_array($students_in_course)) {
                        foreach ($students_in_course as $user_item) {
                            $students_in_courses[$user_item['user_id']] = $user_item['user_id'];
                        }
                    }
                }
            }
        }

        $students = Tracking:: get_student_followed_by_coach($user_id);
        if (!empty($students_in_courses)) {
            if (!empty($students)) {
                $students = array_merge($students, $students_in_courses);
            } else {
                $students = $students_in_courses;
            }
        }

        if (!empty($students)) {
            $students = array_unique($students);
        }
        return $students;
    }

    /**
     * @param int $user_id
     * @return array An array with the course info of all the courses (real and virtual)
     * of which the current user is course admin.
     */
    public static function get_course_list_of_user_as_course_admin($user_id)
    {
        if ($user_id != strval(intval($user_id))) {
            return array();
        }

        // Definitions database tables and variables
        $tbl_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $tbl_course_user = Database::get_main_table(TABLE_MAIN_COURSE_USER);
        $user_id = intval($user_id);
        $data = array();

        $sql = "SELECT
                    course_rel_user.course_code,
                    course.title,
                    course.id,
                    course.db_name,
                    course.id as real_id
                FROM $tbl_course_user as course_rel_user
                INNER JOIN $tbl_course as course
                ON course.code = course_rel_user.course_code
                WHERE
                    course_rel_user.user_id='$user_id' AND
                    course_rel_user.status='1'
                ORDER BY course.title";

        if (api_get_multiple_access_url()) {
            $tbl_course_rel_access_url = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
            $access_url_id = api_get_current_access_url_id();
            if ($access_url_id != -1) {
                $sql = "
                    SELECT
                        course_rel_user.course_code,
                        course.title,
                        course.id,
                        course.db_name,
                        course.id as real_id
                    FROM $tbl_course_user as course_rel_user
                    INNER JOIN $tbl_course as course
                    ON course.code = course_rel_user.course_code
                    INNER JOIN $tbl_course_rel_access_url course_rel_url
                    ON (course_rel_url.course_code= course.code)
                    WHERE
                        access_url_id =  $access_url_id  AND
                        course_rel_user.user_id='$user_id' AND
                        course_rel_user.status='1'
                    ORDER BY course.title";
            }
        }

        $result_nb_cours = Database::query($sql);
        if (Database::num_rows($result_nb_cours) > 0) {
            while ($row = Database::fetch_array($result_nb_cours, 'ASSOC')) {
                $data[$row['course_code']] = $row;
            }
        }

        return $data;
    }

    /**
     * Find out for which courses the user is registered and determine a visual course code and course title from that.
     * Takes virtual courses into account
     *
     * Default case: the name and code stay what they are.
     *
     * Scenarios:
     * - User is registered in real course and virtual courses; name / code become a mix of all
     * - User is registered in real course only: name stays that of real course
     * - User is registered in virtual course only: name becomes that of virtual course
     * - user is not registered to any of the real/virtual courses: name stays that of real course
     * (I'm not sure about the last case, but this seems not too bad)
     *
     * @author Roan Embrechts
     * @param int $user_id , the id of the user
     * @param array $course_info , an array with course info that you get using Database::get_course_info($course_system_code);
     * @return array An array with indices
     *    $return_result['title'] - the course title of the combined courses
     *    $return_result['code']  - the course code of the combined courses
     * @deprecated use api_get_course_info()
     */
    public static function determine_course_title_from_course_info($user_id, $course_info)
    {
        if ($user_id != strval(intval($user_id))) {
            return array();
        }

        $real_course_id = $course_info['system_code'];
        $real_course_info = api_get_course_info($real_course_id);
        $real_course_name = $real_course_info['title'];
        $real_course_visual_code = $real_course_info['visual_code'];
        $real_course_real_code = Database::escape_string($course_info['system_code']);

        //is the user registered in the real course?
        $result = Database::fetch_array(Database::query("SELECT * FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . "
                WHERE user_id = '$user_id' AND relation_type<>" . COURSE_RELATION_TYPE_RRHH . " AND course_code = '$real_course_real_code'"));
        $user_is_registered_in_real_course = !empty($result);

        //get a list of virtual courses linked to the current real course and to which the current user is subscribed
        $user_subscribed_virtual_course_list = self::get_list_of_virtual_courses_for_specific_user_and_real_course($user_id,
            $real_course_id);
        $virtual_courses_exist = count($user_subscribed_virtual_course_list) > 0;

        //now determine course code and name
        if ($user_is_registered_in_real_course && $virtual_courses_exist) {
            $course_info['name'] = self::create_combined_name($user_is_registered_in_real_course, $real_course_name,
                $user_subscribed_virtual_course_list);
            $course_info['official_code'] = self::create_combined_code($user_is_registered_in_real_course,
                $real_course_visual_code, $user_subscribed_virtual_course_list);
        } elseif ($user_is_registered_in_real_course) {
            //course name remains real course name
            $course_info['name'] = $real_course_name;
            $course_info['official_code'] = $real_course_visual_code;
        } elseif ($virtual_courses_exist) {
            $course_info['name'] = self::create_combined_name($user_is_registered_in_real_course, $real_course_name,
                $user_subscribed_virtual_course_list);
            $course_info['official_code'] = self::create_combined_code($user_is_registered_in_real_course,
                $real_course_visual_code, $user_subscribed_virtual_course_list);
        } else {
            //course name remains real course name
            $course_info['name'] = $real_course_name;
            $course_info['official_code'] = $real_course_visual_code;
        }

        $return_result['title'] = $course_info['name'];
        $return_result['code'] = $course_info['official_code'];
        return $return_result;
    }

    /**
     * Create a course title based on all real and virtual courses the user is registered in.
     * @param boolean $user_is_registered_in_real_course
     * @param string $real_course_name , the title of the real course
     * @param array $virtual_course_list , the list of virtual courses
     * @deprecated
     */
    public static function create_combined_name(
        $user_is_registered_in_real_course,
        $real_course_name,
        $virtual_course_list
    ) {
        $complete_course_name = array();

        if ($user_is_registered_in_real_course) {
            // Add the real name to the result.
            $complete_course_name[] = $real_course_name;
        }

        // Add course titles of all virtual courses.
        foreach ($virtual_course_list as $current_course) {
            $complete_course_name[] = $current_course['title'];
        }

        // 'CombinedCourse' is from course_home language file.
        return (($user_is_registered_in_real_course || count($virtual_course_list) > 1) ? get_lang('CombinedCourse') . ' ' : '') . implode(' &amp; ',
            $complete_course_name);
    }

    /**
     *    Create a course code based on all real and virtual courses the user is registered in.
     * @deprecated
     */
    public static function create_combined_code(
        $user_is_registered_in_real_course,
        $real_course_code,
        $virtual_course_list
    ) {
        $complete_course_code = array();

        if ($user_is_registered_in_real_course) {
            // Add the real code to the result
            $complete_course_code[] = $real_course_code;
        }

        // Add codes of all virtual courses.
        foreach ($virtual_course_list as $current_course) {
            $complete_course_code[] = $current_course['visual_code'];
        }

        return implode(' &amp; ', $complete_course_code);
    }

    /**
     *    Return course info array of virtual course
     *
     *    Note this is different from getting information about a real course!
     *
     * @param $real_course_code , the id of the real course which the virtual course is linked to
     * @deprecated virtual courses doesn't exist anymore
     */
    public static function get_virtual_course_info($real_course_code)
    {
        $sql_result = Database::query("SELECT * FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . "
                WHERE target_course_code = '" . Database::escape_string($real_course_code) . "'");
        $result = array();
        while ($virtual_course = Database::fetch_array($sql_result)) {
            $result[] = $virtual_course;
        }
        return $result;
    }

    /**
     * @param string $system_code , the system code of the course
     * @return true if the course is a virtual course, false otherwise
     * @deprecated virtual courses doesn't exist anymore
     */
    public static function is_virtual_course_from_system_code($system_code)
    {
        $result = Database::fetch_array(Database::query("SELECT target_course_code FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . "
                WHERE code = '" . Database::escape_string($system_code) . "'"));
        return !empty($result['target_course_code']);
    }

    /**
     *    Returns whether the course code given is a visual code
     * @param  string  Visual course code
     * @return true if the course is a virtual course, false otherwise
     * @deprecated virtual courses doesn't exist anymore
     */
    public static function is_virtual_course_from_visual_code($visual_code)
    {
        $result = Database::fetch_array(Database::query("SELECT target_course_code FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . "
                WHERE visual_code = '" . Database::escape_string($visual_code) . "'"));
        return !empty($result['target_course_code']);
    }

    /**
     * @return true if the real course has virtual courses that the user is subscribed to, false otherwise
     * @deprecated virtual courses doesn't exist anymore
     */
    public static function has_virtual_courses_from_code($real_course_code, $user_id)
    {
        return count(self::get_list_of_virtual_courses_for_specific_user_and_real_course($user_id,
                $real_course_code)) > 0;
    }

    /**
     *  Return an array of arrays, listing course info of all virtual course
     *  linked to the real course ID $real_course_code
     *
     * @param string The id of the real course which the virtual courses are linked to
     * @return array List of courses details
     * @deprecated virtual courses doesn't exist anymore
     */
    public static function get_virtual_courses_linked_to_real_course($real_course_code)
    {
        $sql_result = Database::query("SELECT * FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . "
                WHERE target_course_code = '" . Database::get_main_table(TABLE_MAIN_COURSE) . "'");
        $result_array = array();
        while ($result = Database::fetch_array($sql_result)) {
            $result_array[] = $result;
        }
        return $result_array;
    }

    /**
     * This function returns the course code of the real course
     * to which a virtual course is linked.
     * @deprecated
     * @param the course code of the virtual course
     * @return the course code of the real course
     */
    public static function get_target_of_linked_course($virtual_course_code)
    {
        //get info about the virtual course
        $result = Database::fetch_array(Database::query("SELECT * FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . "
                WHERE code = '" . Database::escape_string($virtual_course_code) . "'"));
        return $result['target_course_code'];
    }

    /**
     * @param int $userId
     * @param array $courseInfo
     * @return bool
     */
    public static function isUserSubscribedInCourseAsDrh($userId, $courseInfo)
    {
        $userId = intval($userId);

        if (!api_is_drh()) {
            return false;
        }

        if (empty($courseInfo) || empty($userId)) {
            return false;
        }
        $courseCode = Database::escape_string($courseInfo['code']);
        $table = Database::get_main_table(TABLE_MAIN_COURSE_USER);

        $sql = "SELECT * FROM $table
                WHERE
                    user_id = $userId AND
                    relation_type = " . COURSE_RELATION_TYPE_RRHH . " AND
                    course_code = '$courseCode'";

        $result = Database::fetch_array(Database::query($sql));

        if (!empty($result)) {
            // The user has been registered in this course.
            return true;
        }
    }

    /**
     * Check if user is subscribed inside a course
     * @param  int $user_id
     * @param  string $course_code , if this parameter is null, it'll check for all courses
     * @param  bool $in_a_session True for checking inside sessions too, by default is not checked
     * @return bool   $session_id true if the user is registered in the course, false otherwise
     */
    public static function is_user_subscribed_in_course(
        $user_id,
        $course_code = null,
        $in_a_session = false,
        $session_id = null
    ) {
        $user_id = intval($user_id);

        if (empty($session_id)) {
            $session_id = api_get_session_id();
        } else {
            $session_id = intval($session_id);
        }

        $condition_course = '';
        if (isset($course_code)) {
            $course_code = Database::escape_string($course_code);
            $condition_course = ' AND course_code = "' . $course_code . '" ';
        }

        $sql = "SELECT * FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . "
                WHERE user_id = $user_id AND relation_type<>" . COURSE_RELATION_TYPE_RRHH . " $condition_course ";

        $result = Database::fetch_array(Database::query($sql));

        if (!empty($result)) {
            // The user has been registered in this course.
            return true;
        }

        if (!$in_a_session) {
            // The user has not been registered in this course.
            return false;
        }

        $tableSessionCourseUser = Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER);
        $sql = 'SELECT 1 FROM ' . $tableSessionCourseUser .
            ' WHERE id_user = ' . $user_id . ' ' . $condition_course;
        if (Database::num_rows(Database::query($sql)) > 0) {
            return true;
        }

        $sql = 'SELECT 1 FROM ' . $tableSessionCourseUser .
            ' WHERE id_user = ' . $user_id . ' AND status=2 ' . $condition_course;
        if (Database::num_rows(Database::query($sql)) > 0) {
            return true;
        }

        $sql = 'SELECT 1 FROM ' . Database::get_main_table(TABLE_MAIN_SESSION) .
            ' WHERE id=' . $session_id . ' AND id_coach=' . $user_id;

        if (Database::num_rows(Database::query($sql)) > 0) {
            return true;
        }

        return false;
    }

    /**
     *    Is the user a teacher in the given course?
     *
     * @param $user_id , the id (int) of the user
     * @param $course_code , the course code
     *
     * @return true if the user is a teacher in the course, false otherwise
     */
    public static function is_course_teacher($user_id, $course_code)
    {
        if ($user_id != strval(intval($user_id))) {
            return false;
        }
        $sql_result = Database::query('SELECT status FROM ' . Database::get_main_table(TABLE_MAIN_COURSE_USER) .
            ' WHERE course_code="' . Database::escape_string($course_code) . '" and user_id="' . $user_id . '"');
        if (Database::num_rows($sql_result) > 0) {
            return Database::result($sql_result, 0, 'status') == 1;
        }
        return false;
    }

    /**
     *    Is the user subscribed in the real course or linked courses?
     *
     * @param int the id of the user
     * @param array info about the course (comes from course table, see database lib)
     *
     * @return true if the user is registered in the real course or linked courses, false otherwise
     */
    public static function is_user_subscribed_in_real_or_linked_course($user_id, $course_code, $session_id = '')
    {
        if ($user_id != strval(intval($user_id))) {
            return false;
        }

        $course_code = Database::escape_string($course_code);

        if ($session_id == '') {
            $result = Database::fetch_array(
                Database::query("SELECT *
                    FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . " course
                    LEFT JOIN " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . " course_user
                    ON course.code = course_user.course_code
                    WHERE
                        course_user.user_id = '$user_id' AND
                        course_user.relation_type<>" . COURSE_RELATION_TYPE_RRHH . " AND
                        ( course.code = '$course_code' OR target_course_code = '$course_code')"));
            return !empty($result);
        }

        $session_id = intval($session_id);

        // From here we trust session id.
        // Is he/she subscribed to the session's course?

        // A user?
        if (Database::num_rows(Database::query("SELECT id_user
                    FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . "
                    WHERE id_session='" . $session_id . "'
                    AND id_user='$user_id'"))
        ) {
            return true;
        }

        // A course coach?
        if (Database::num_rows(Database::query("SELECT id_user
                    FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . "
                    WHERE id_session='" . $session_id . "'
                    AND id_user = '$user_id' AND status = 2
                    AND course_code='$course_code'"))
        ) {
            return true;
        }

        // A session coach?
        if (Database::num_rows(Database::query("SELECT id_coach
                    FROM " . Database::get_main_table(TABLE_MAIN_SESSION) . " AS session
                    WHERE session.id='" . $session_id . "'
                    AND id_coach='$user_id'"))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Return user info array of all users registered in the specified real or virtual course
     * This only returns the users that are registered in this actual course, not linked courses.
     * @param string $course_code
     * @param int $session_id
     * @param string $limit
     * @param string $order_by the field to order the users by.
     * Valid values are 'lastname', 'firstname', 'username', 'email', 'official_code' OR a part of a SQL statement
     * that starts with ORDER BY ...
     * @param null $filter_by_status if using the session_id: 0 or 2 (student, coach),
     * if using session_id = 0 STUDENT or COURSEMANAGER
     * @param null $return_count
     * @param bool $add_reports
     * @param bool $resumed_report
     * @param array $extra_field
     * @param array $courseCodeList
     * @param array $userIdList
     * @param string $filterByActive
     * @return array|int
     */
    public static function get_user_list_from_course_code(
        $course_code = null,
        $session_id = 0,
        $limit = null,
        $order_by = null,
        $filter_by_status = null,
        $return_count = null,
        $add_reports = false,
        $resumed_report = false,
        $extra_field = array(),
        $courseCodeList = array(),
        $userIdList = array(),
        $filterByActive = null
    ) {
        // variable initialisation
        $session_id = intval($session_id);
        $course_code = Database::escape_string($course_code);
        $courseInfo = api_get_course_info($course_code);
        $courseId = $courseInfo['real_id'];

        $where = array();

        if (empty($order_by)) {
            $order_by = 'user.lastname, user.firstname';
            if (api_is_western_name_order()) {
                $order_by = 'user.firstname, user.lastname';
            }
        }

        // if the $order_by does not contain 'ORDER BY' we have to check if it is a valid field that can be sorted on
        if (!strstr($order_by, 'ORDER BY')) {
            if (!empty($order_by)) {
                $order_by = 'ORDER BY ' . $order_by;
            } else {
                $order_by = '';
            }
        }

        $filter_by_status_condition = null;

        if (!empty($session_id)) {
            $sql = 'SELECT DISTINCT user.user_id, session_course_user.status as status_session, user.*  ';
            if ($return_count) {
                $sql = " SELECT COUNT(user.user_id) as count";
            }
            $sql .= ' FROM ' . Database::get_main_table(TABLE_MAIN_USER) . ' as user ';
            $sql .= ' LEFT JOIN ' . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . ' as session_course_user
                      ON
                        user.user_id = session_course_user.id_user AND
                        session_course_user.course_code="' . $course_code . '" AND
                        session_course_user.id_session = ' . $session_id;
            $where[] = ' session_course_user.course_code IS NOT NULL ';

            // 2 = coach
            // 0 = student
            if (isset($filter_by_status)) {
                $filter_by_status = intval($filter_by_status);
                $filter_by_status_condition = " session_course_user.status = $filter_by_status AND ";
            }

        } else {
            if ($return_count) {
                $sql = " SELECT COUNT(*) as count";
                if ($resumed_report) {
                    //$sql = " SELECT count(field_id) ";
                }
            } else {
                if (empty($course_code)) {
                    $sql = 'SELECT DISTINCT
                                course.title,
                                course.code,
                                course_rel_user.status as status_rel,
                                user.user_id,
                                course_rel_user.role,
                                course_rel_user.tutor_id,
                                user.*  ';
                } else {
                    $sql = 'SELECT DISTINCT
                                course_rel_user.status as status_rel,
                                user.user_id,
                                course_rel_user.role,
                                course_rel_user.tutor_id,
                                user.*  ';
                }
            }

            $sql .= ' FROM ' . Database::get_main_table(TABLE_MAIN_USER) . ' as user ';
            $sql .= ' LEFT JOIN ' . Database::get_main_table(TABLE_MAIN_COURSE_USER) . ' as course_rel_user
                        ON user.user_id = course_rel_user.user_id AND
                        course_rel_user.relation_type <> ' . COURSE_RELATION_TYPE_RRHH . '  ';
            if (!empty($course_code)) {
                $sql .= ' AND course_rel_user.course_code="' . $course_code . '"';
            } else {
                $course_table = Database::get_main_table(TABLE_MAIN_COURSE);
                $sql .= " INNER JOIN  $course_table course ON course_rel_user.course_code = course.code ";
            }
            $where[] = ' course_rel_user.course_code IS NOT NULL ';

            if (isset($filter_by_status) && is_numeric($filter_by_status)) {
                $filter_by_status = intval($filter_by_status);
                $filter_by_status_condition = " course_rel_user.status = $filter_by_status AND ";
            }
        }

        $multiple_access_url = api_get_multiple_access_url();
        if ($multiple_access_url) {
            $sql .= ' LEFT JOIN ' . Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER) . '  au ON (au.user_id = user.user_id) ';
        }

        if ($return_count && $resumed_report) {
            foreach ($extra_field as $extraField) {
                $extraFieldInfo = UserManager::get_extra_field_information_by_name($extraField);
                $sql .= ' LEFT JOIN ' . Database::get_main_table(TABLE_MAIN_USER_FIELD_VALUES) . ' as ufv
                          ON (user.user_id = ufv.user_id AND (field_id = ' . $extraFieldInfo['id'] . ' OR field_id IS NULL ) )';
            }
        }

        $sql .= ' WHERE ' . $filter_by_status_condition . ' ' . implode(' OR ', $where);

        if ($multiple_access_url) {
            $current_access_url_id = api_get_current_access_url_id();
            $sql .= " AND (access_url_id =  $current_access_url_id ) ";
        }

        if ($return_count && $resumed_report) {
            $sql .= ' AND field_id IS NOT NULL GROUP BY field_value ';
        }

        if (!empty($courseCodeList)) {
            $courseCodeList = array_map(array('Database', 'escape_string'), $courseCodeList);
            $courseCodeList = implode('","', $courseCodeList);
            $sql .= ' AND course.code IN ("' . $courseCodeList . '")';
        }

        if (!empty($userIdList)) {
            $userIdList = array_map('intval', $userIdList);
            $userIdList = implode('","', $userIdList);
            $sql .= ' AND user.user_id IN ("' . $userIdList . '")';
        }

        if (isset($filterByActive)) {
            $filterByActive = intval($filterByActive);
            $sql .= ' AND user.active = ' . $filterByActive;
        }

        $sql .= ' ' . $order_by . ' ' . $limit;

        $rs = Database::query($sql);
        $users = array();

        $extra_fields = UserManager::get_extra_fields(0, 100, null, null, true, true);

        $counter = 1;
        $count_rows = Database::num_rows($rs);

        if ($return_count && $resumed_report) {
            return $count_rows;
        }

        $table_user_field_value = Database::get_main_table(TABLE_MAIN_USER_FIELD_VALUES);
        if ($count_rows) {
            while ($user = Database::fetch_array($rs)) {
                if ($return_count) {
                    return $user['count'];
                }
                $report_info = array();

                $user_info = $user;
                $user_info['status'] = $user['status'];

                if (isset($user['role'])) {
                    $user_info['role'] = $user['role'];
                }
                if (isset($user['tutor_id'])) {
                    $user_info['tutor_id'] = $user['tutor_id'];
                }

                if (!empty($session_id)) {
                    $user_info['status_session'] = $user['status_session'];
                }

                if ($add_reports) {
                    $course_code = $user['code'];
                    if ($resumed_report) {
                        foreach ($extra_fields as $extra) {
                            if (in_array($extra['1'], $extra_field)) {
                                $user_data = UserManager::get_extra_user_data_by_field($user['user_id'], $extra['1']);
                                break;
                            }
                        }

                        if (empty($user_data[$extra['1']])) {
                            $row_key = '-1';
                            $name = '-';
                        } else {
                            $row_key = $user_data[$extra['1']];
                            $name = $user_data[$extra['1']];
                        }

                        $users[$row_key]['extra_' . $extra['1']] = $name;
                        $users[$row_key]['training_hours'] += Tracking::get_time_spent_on_the_course($user['user_id'],
                            $courseId, 0);
                        $users[$row_key]['count_users'] += $counter;

                        $registered_users_with_extra_field = 0;

                        if (!empty($name) && $name != '-') {
                            $name = Database::escape_string($name);
                            $sql = "SELECT count(user_id) as count FROM $table_user_field_value WHERE field_value = '$name'";
                            $result_count = Database::query($sql);
                            if (Database::num_rows($result_count)) {
                                $row_count = Database::fetch_array($result_count);
                                $registered_users_with_extra_field = $row_count['count'];
                            }
                        }

                        $users[$row_key]['count_users_registered'] = $registered_users_with_extra_field;
                        $users[$row_key]['average_hours_per_user'] = $users[$row_key]['training_hours'] / $users[$row_key]['count_users'];

                        $category = Category:: load(null, null, $course_code);
                        if (!isset($users[$row_key]['count_certificates'])) {
                            $users[$row_key]['count_certificates'] = 0;
                        }
                        if (isset($category[0]) && $category[0]->is_certificate_available($user['user_id'])) {
                            $users[$row_key]['count_certificates']++;
                        }

                        foreach ($extra_fields as $extra) {
                            if ($extra['1'] == 'ruc') {
                                continue;
                            }

                            if (!isset($users[$row_key][$extra['1']])) {
                                $user_data = UserManager::get_extra_user_data_by_field($user['user_id'], $extra['1']);
                                if (!empty($user_data[$extra['1']])) {
                                    $users[$row_key][$extra['1']] = $user_data[$extra['1']];
                                }
                            }
                        }

                    } else {
                        $report_info['course'] = $user['title'];
                        $report_info['user'] = api_get_person_name($user['firstname'], $user['lastname']);
                        $report_info['time'] = api_time_to_hms(Tracking::get_time_spent_on_the_course($user['user_id'],
                                $courseId, 0));

                        $category = Category:: load(null, null, $course_code);
                        $report_info['certificate'] = Display::label(get_lang('No'));
                        if (isset($category[0]) && $category[0]->is_certificate_available($user['user_id'])) {
                            $report_info['certificate'] = Display::label(get_lang('Yes'), 'success');
                        }

                        $progress = intval(Tracking::get_avg_student_progress($user['user_id'], $course_code, array(),
                                0));
                        $report_info['progress_100'] = $progress == 100 ? Display::label(get_lang('Yes'),
                            'success') : Display::label(get_lang('No'));
                        $report_info['progress'] = $progress . "%";

                        foreach ($extra_fields as $extra) {
                            $user_data = UserManager::get_extra_user_data_by_field($user['user_id'], $extra['1']);
                            $report_info[$extra['1']] = $user_data[$extra['1']];
                        }
                        $users[] = $report_info;
                    }
                } else {
                    $users[$user['user_id']] = $user_info;
                }
            }
        }
        return $users;
    }

    /**
     * @param bool $resumed_report
     * @param array $extra_field
     * @param array $courseCodeList
     * @param array $userIdList
     * @return array|int
     */
    static function get_count_user_list_from_course_code(
        $resumed_report = false,
        $extra_field = array(),
        $courseCodeList = array(),
        $userIdList = array()
    ) {
        return self::get_user_list_from_course_code(
            null,
            0,
            null,
            null,
            null,
            true,
            false,
            $resumed_report,
            $extra_field,
            $courseCodeList,
            $userIdList
        );
    }

    /**
     * Gets subscribed users in a course or in a course/session
     *
     * @param   string $course_code
     * @param   int $session_id
     * @return  int
     */
    public static function get_users_count_in_course($course_code, $session_id = 0)
    {
        // variable initialisation
        $session_id = intval($session_id);
        $course_code = Database::escape_string($course_code);

        $sql = 'SELECT DISTINCT count(*) as count  FROM ' . Database::get_main_table(TABLE_MAIN_USER) . ' as user ';
        $where = array();
        if (!empty($session_id)) {
            $sql .= ' LEFT JOIN ' . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . ' as session_course_user
                      ON user.user_id = session_course_user.id_user
                      AND session_course_user.course_code = "' . $course_code . '"
                      AND session_course_user.id_session  = ' . $session_id;

            $where[] = ' session_course_user.course_code IS NOT NULL ';
        } else {
            $sql .= ' LEFT JOIN ' . Database::get_main_table(TABLE_MAIN_COURSE_USER) . ' as course_rel_user
                        ON user.user_id = course_rel_user.user_id AND course_rel_user.relation_type<>' . COURSE_RELATION_TYPE_RRHH . '
                        AND course_rel_user.course_code="' . $course_code . '"';
            $where[] = ' course_rel_user.course_code IS NOT NULL ';
        }

        $multiple_access_url = api_get_multiple_access_url();
        if ($multiple_access_url) {
            $sql .= ' LEFT JOIN ' . Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER) . '  au ON (au.user_id = user.user_id) ';
        }

        $sql .= ' WHERE ' . implode(' OR ', $where);

        if ($multiple_access_url) {
            $current_access_url_id = api_get_current_access_url_id();
            $sql .= " AND (access_url_id =  $current_access_url_id ) ";
        }
        $rs = Database::query($sql);
        $count = 0;
        if (Database::num_rows($rs)) {
            $user = Database::fetch_array($rs);
            $count = $user['count'];
        }
        return $count;
    }

    /**
     * Get a list of coaches of a course and a session
     * @param   string  Course code
     * @param   int     Session ID
     * @return  array   List of users
     */
    public static function get_coach_list_from_course_code($course_code, $session_id)
    {
        if (empty($course_code) OR empty($session_id)) {
            return array();
        }

        $course_code = Database::escape_string($course_code);
        $session_id = intval($session_id);
        $users = array();

        // We get the coach for the given course in a given session.
        $sql = 'SELECT id_user FROM ' . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) .
            ' WHERE id_session="' . $session_id . '" AND course_code="' . $course_code . '" AND status = 2';
        $rs = Database::query($sql);
        while ($user = Database::fetch_array($rs)) {
            $user_info = api_get_user_info($user['id_user']);
            $user_info['status'] = $user['status'];
            $user_info['role'] = $user['role'];
            $user_info['tutor_id'] = $user['tutor_id'];
            $user_info['email'] = $user['email'];
            $users[$user['id_user']] = $user_info;
        }

        $table = Database::get_main_table(TABLE_MAIN_SESSION);
        // We get the session coach.
        $sql = 'SELECT id_coach FROM ' . $table . ' WHERE id=' . $session_id;
        $rs = Database::query($sql);
        $session_id_coach = Database::result($rs, 0, 'id_coach');
        $user_info = api_get_user_info($session_id_coach);
        $user_info['status'] = $user['status'];
        $user_info['role'] = $user['role'];
        $user_info['tutor_id'] = $user['tutor_id'];
        $user_info['email'] = $user['email'];
        $users[$session_id_coach] = $user_info;

        return $users;
    }

    /**
     *  Return user info array of all users registered in the specified real or virtual course
     *  This only returns the users that are registered in this actual course, not linked courses.
     *
     * @param string $course_code
     * @param boolean $with_session
     * @param integer $session_id
     * @param string $date_from
     * @param string $date_to
     * @param boolean $includeInvitedUsers Whether include the invited users
     * @return array with user id
     */
    public static function get_student_list_from_course_code(
        $course_code,
        $with_session = false,
        $session_id = 0,
        $date_from = null,
        $date_to = null,
        $includeInvitedUsers = true
    ) {
        $session_id = intval($session_id);
        $course_code = Database::escape_string($course_code);

        $students = array();

        if ($session_id == 0) {
            // students directly subscribed to the course
            $sql = "SELECT * FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . " cu
                    INNER JOIN user u ON cu.user_id = u.user_id
                   WHERE course_code = '$course_code' AND cu.status = " . STUDENT;

            if (!$includeInvitedUsers) {
                $sql .= " AND u.status != " . INVITEE;
            }

            $rs = Database::query($sql);
            while ($student = Database::fetch_array($rs)) {
                $students[$student['user_id']] = $student;
            }
        }

        // students subscribed to the course through a session

        if ($with_session) {

            $joinSession = "";
            //Session creation date
            if (!empty($date_from) && !empty($date_to)) {
                $joinSession = "INNER JOIN " . Database::get_main_table(TABLE_MAIN_SESSION) . " s";
            }

            $sql_query = "SELECT * FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . " scu
                          $joinSession
                          INNER JOIN $userTable u ON scu.id_user = u.user_id
                          WHERE scu.course_code = '$course_code' AND scu.status <> 2";

            if (!empty($date_from) && !empty($date_to)) {
                $date_from = Database::escape_string($date_from);
                $date_to = Database::escape_string($date_to);
                $sql_query .= " AND s.date_start >= '$date_from' AND s.date_end <= '$date_to'";
            }

            if ($session_id != 0) {
                $sql_query .= ' AND scu.id_session = ' . $session_id;
            }

            if (!$includeInvitedUsers) {
                $sql .= " AND u.status != " . INVITEE;
            }

            $rs = Database::query($sql_query);
            while ($student = Database::fetch_array($rs)) {
                $students[$student['id_user']] = $student;
            }
        }

        return $students;
    }

    /**
     * Return user info array of all teacher-users registered in the specified real or virtual course
     * This only returns the users that are registered in this actual course, not linked courses.
     *
     * @param string $course_code
     * @return array with user id
     */
    public static function get_teacher_list_from_course_code($course_code)
    {
        $course_code = Database::escape_string($course_code);
        $teachers = array();
        $sql = "SELECT DISTINCT u.user_id, u.lastname, u.firstname, u.email, u.username, u.status
                FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . " cu
                INNER JOIN " . Database::get_main_table(TABLE_MAIN_USER) . " u
                ON (cu.user_id = u.user_id)
                WHERE   cu.course_code = '$course_code' AND
                        cu.status = 1 ";
        $rs = Database::query($sql);
        while ($teacher = Database::fetch_array($rs)) {
            $teachers[$teacher['user_id']] = $teacher;
        }
        return $teachers;
    }

    /**
     * Returns a string list of teachers assigned to the given course
     * @param string $course_code
     * @param string $separator between teachers names
     * @param bool $add_link_to_profile Whether to add a link to the teacher's profile
     * @return string List of teachers teaching the course
     */
    public static function get_teacher_list_from_course_code_to_string(
        $course_code,
        $separator = self::USER_SEPARATOR,
        $add_link_to_profile = false
    ) {
        $teacher_list = self::get_teacher_list_from_course_code($course_code);
        $teacher_string = '';
        $list = array();
        if (!empty($teacher_list)) {
            foreach ($teacher_list as $teacher) {
                $teacher_name = api_get_person_name(
                    $teacher['firstname'],
                    $teacher['lastname']
                );
                if ($add_link_to_profile) {
                    $url = api_get_path(WEB_AJAX_PATH) . 'user_manager.ajax.php?a=get_user_popup&resizable=0&height=300&user_id=' . $teacher['user_id'];
                    $teacher_name = Display::url(
                        $teacher_name,
                        $url,
                        array('class' => 'ajax')
                    );
                }
                $list[] = $teacher_name;
            }
            if (!empty($list)) {
                $teacher_string = array_to_string($list, $separator);
            }
        }
        return $teacher_string;
    }

    /**
     * This function returns information about coachs from a course in session
     * @param int - optional, session id
     * @param string - optional, course code
     * @return array    - array containing user_id, lastname, firstname, username
     *
     */
    public static function get_coachs_from_course($session_id = 0, $course_code = '')
    {
        if (!empty($session_id)) {
            $session_id = intval($session_id);
        } else {
            $session_id = api_get_session_id();
        }

        if (!empty($course_code)) {
            $course_code = Database::escape_string($course_code);
        } else {
            $course_code = api_get_course_id();
        }

        $tbl_user = Database:: get_main_table(TABLE_MAIN_USER);
        $tbl_session_course_user = Database:: get_main_table(TABLE_MAIN_SESSION_COURSE_USER);
        $coaches = array();

        $sql = "SELECT DISTINCT u.user_id,u.lastname,u.firstname,u.username FROM $tbl_user u,$tbl_session_course_user scu
                WHERE u.user_id = scu.id_user AND scu.id_session = '$session_id' AND scu.course_code = '$course_code' AND scu.status = 2";
        $rs = Database::query($sql);

        if (Database::num_rows($rs) > 0) {
            while ($row = Database::fetch_array($rs)) {
                $coaches[] = $row;
            }
            return $coaches;
        } else {
            return false;
        }
    }

    /**
     * @param int $session_id
     * @param string $course_code
     * @param string $separator
     * @param bool $add_link_to_profile
     * @return null|string
     */
    public static function get_coachs_from_course_to_string(
        $session_id = 0,
        $course_code = null,
        $separator = self::USER_SEPARATOR,
        $add_link_to_profile = false
    ) {
        $coachs_course = self::get_coachs_from_course($session_id, $course_code);
        $course_coachs = array();

        if (is_array($coachs_course)) {
            foreach ($coachs_course as $coach_course) {
                $coach_name = api_get_person_name($coach_course['firstname'], $coach_course['lastname']);
                if ($add_link_to_profile) {
                    $url = api_get_path(WEB_AJAX_PATH) . 'user_manager.ajax.php?a=get_user_popup&resizable=0&height=300&user_id=' . $coach_course['user_id'];
                    $coach_name = Display::url($coach_name, $url, array('class' => 'ajax'));
                }
                $course_coachs[] = $coach_name;
            }
        }
        $coaches_to_string = null;
        if (is_array($course_coachs) && count($course_coachs) > 0) {
            $coaches_to_string = array_to_string($course_coachs, $separator);
        }
        return $coaches_to_string;
    }

    /**
     * @param string $course_code
     * @param int $session_id
     * @return string
     */
    public static function get_coach_list_from_course_code_to_string($course_code, $session_id)
    {
        $tutor_data = '';
        if ($session_id != 0) {
            $coaches = self::get_email_of_tutor_to_session($session_id, $course_code);
            $coach_list = array();
            foreach ($coaches as $coach) {
                $coach_list[] = $coach['complete_name'];
            }
            if (!empty($coach_list)) {
                $tutor_data = implode(self::USER_SEPARATOR, $coach_list);
            }
        }
        return $tutor_data;
    }

    /**
     * Return user info array of all users registered in the specified course
     * this includes the users of the course itself and the users of all linked courses.
     *
     * @param string $course_code
     * @param bool $with_sessions
     * @param int $session_id
     * @return array with user info
     */
    public static function get_real_and_linked_user_list($course_code, $with_sessions = true, $session_id = 0)
    {
        //get list of virtual courses
        $virtual_course_list = self::get_virtual_courses_linked_to_real_course($course_code);
        $complete_user_list = array();

        //get users from real course
        $user_list = self::get_user_list_from_course_code($course_code, $session_id);
        foreach ($user_list as $this_user) {
            $complete_user_list[] = $this_user;
        }

        //get users from linked courses
        foreach ($virtual_course_list as $this_course) {
            $course_code = $this_course['code'];
            $user_list = self::get_user_list_from_course_code($course_code, $session_id);
            foreach ($user_list as $this_user) {
                $complete_user_list[] = $this_user;
            }
        }

        return $complete_user_list;
    }

    /**
     * Return an array of arrays, listing course info of all courses in the list
     * linked to the real course $real_course_code, to which the user $user_id is subscribed.
     *
     * @param $user_id , the id (int) of the user
     * @param $real_course_code , the id (char) of the real course
     *
     * @return array of course info arrays
     * @deprecated virtual course feature is not supported
     */
    public static function get_list_of_virtual_courses_for_specific_user_and_real_course($user_id, $course_code)
    {
        $result_array = array();

        if ($user_id != strval(intval($user_id))) {
            return $result_array;
        }

        $course_code = Database::escape_string($course_code);
        $sql = "SELECT *
                FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . " course
                LEFT JOIN " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . " course_user
                ON course.code = course_user.course_code
                WHERE
                    course.target_course_code = '$course_code' AND
                    course_user.user_id = '$user_id' AND
                    course_user.relation_type<>" . COURSE_RELATION_TYPE_RRHH . " ";
        $sql_result = Database::query($sql);

        while ($result = Database::fetch_array($sql_result)) {
            $result_array[] = $result;
        }
        return $result_array;
    }

    /**
     * Get the list of groups from the course
     * @param   string $course_code
     * @param   int $session_id Session ID (optional)
     * @param   boolean $in_get_empty_group get empty groups (optional)
     * @return  array   List of groups info
     */
    public static function get_group_list_of_course($course_code, $session_id = 0, $in_get_empty_group = 0)
    {
        $course_info = api_get_course_info($course_code);

        if (empty($course_info)) {
            return array();
        }
        $course_id = $course_info['real_id'];

        if (empty($course_id)) {
            return array();
        }

        $group_list = array();
        $session_id != 0 ? $session_condition = ' WHERE g.session_id IN(1,' . intval($session_id) . ')' : $session_condition = ' WHERE g.session_id = 0';

        if ($in_get_empty_group == 0) {
            // get only groups that are not empty
            $sql = "SELECT DISTINCT g.id, g.name
                    FROM " . Database::get_course_table(TABLE_GROUP) . " AS g
                    INNER JOIN " . Database::get_course_table(TABLE_GROUP_USER) . " gu
                    ON (g.id = gu.group_id AND g.c_id = $course_id AND gu.c_id = $course_id)
                    $session_condition
                    ORDER BY g.name";
        } else {
            // get all groups even if they are empty
            $sql = "SELECT g.id, g.name
                    FROM " . Database::get_course_table(TABLE_GROUP) . " AS g
                    $session_condition
                    AND c_id = $course_id";
        }
        $result = Database::query($sql);

        while ($group_data = Database::fetch_array($result)) {
            $group_data['userNb'] = GroupManager::number_of_students($group_data['id'], $course_id);
            $group_list[$group_data['id']] = $group_data;
        }
        return $group_list;
    }

    /**
     * Delete a course
     * This function deletes a whole course-area from the platform. When the
     * given course is a virtual course, the database and directory will not be
     * deleted.
     * When the given course is a real course, also all virtual courses refering
     * to the given course will be deleted.
     * Considering the fact that we remove all traces of the course in the main
     * database, it makes sense to remove all tracking as well (if stats databases exist)
     * so that a new course created with this code would not use the remains of an older
     * course.
     *
     * @param string The code of the course to delete
     * @todo When deleting a virtual course: unsubscribe users from that virtual
     * course from the groups in the real course if they are not subscribed in
     * that real course.
     * @todo Remove globals
     */
    public static function delete_course($code)
    {
        $table_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $table_course_user = Database::get_main_table(TABLE_MAIN_COURSE_USER);
        $table_session_course = Database::get_main_table(TABLE_MAIN_SESSION_COURSE);
        $table_session_course_user = Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER);
        $table_course_survey = Database::get_main_table(TABLE_MAIN_SHARED_SURVEY);
        $table_course_survey_question = Database::get_main_table(TABLE_MAIN_SHARED_SURVEY_QUESTION);
        $table_course_survey_question_option = Database::get_main_table(TABLE_MAIN_SHARED_SURVEY_QUESTION_OPTION);
        $table_course_rel_url = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);

        $table_stats_hotpots = Database::get_main_table(TABLE_STATISTIC_TRACK_E_HOTPOTATOES);
        $table_stats_attempt = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);
        $table_stats_exercises = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCISES);
        $table_stats_access = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ACCESS);
        $table_stats_lastaccess = Database::get_main_table(TABLE_STATISTIC_TRACK_E_LASTACCESS);
        $table_stats_course_access = Database::get_main_table(TABLE_STATISTIC_TRACK_E_COURSE_ACCESS);
        $table_stats_online = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ONLINE);
        $table_stats_default = Database::get_main_table(TABLE_STATISTIC_TRACK_E_DEFAULT);
        $table_stats_downloads = Database::get_main_table(TABLE_STATISTIC_TRACK_E_DOWNLOADS);
        $table_stats_links = Database::get_main_table(TABLE_STATISTIC_TRACK_E_LINKS);
        $table_stats_uploads = Database::get_main_table(TABLE_STATISTIC_TRACK_E_UPLOADS);

        $codeFiltered = Database::escape_string($code);
        $sql = "SELECT * FROM $table_course WHERE code='" . $codeFiltered . "'";
        $res = Database::query($sql);
        if (Database::num_rows($res) == 0) {
            return;
        }
        $count = 0;
        if (api_is_multiple_url_enabled()) {
            $url_id = 1;
            if (api_get_current_access_url_id() != -1) {
                $url_id = api_get_current_access_url_id();
            }
            UrlManager::delete_url_rel_course($code, $url_id);
            $count = UrlManager::getcountUrlRelCourse($code);
        }
        if ($count == 0) {
            self::create_database_dump($code);

            $sql = "SELECT * FROM $table_course WHERE code = '" . $codeFiltered . "'";
            $res = Database::query($sql);
            $course = Database::fetch_array($res);
            $course_tables = AddCourse::get_course_tables();
            $courseId = $course['id'];

            // Cleaning c_x tables
            if (!empty($courseId)) {
                foreach ($course_tables as $table) {
                    $table = Database::get_course_table($table);
                    $sql = "DELETE FROM $table WHERE c_id = $courseId ";
                    Database::query($sql);
                }
            }

            $course_dir = api_get_path(SYS_COURSE_PATH) . $course['directory'];
            $archive_dir = api_get_path(SYS_ARCHIVE_PATH) . $course['directory'] . '_' . time();
            if (is_dir($course_dir)) {
                rename($course_dir, $archive_dir);
            }

            // Unsubscribe all classes from the course
            /*$sql = "DELETE FROM $table_course_class WHERE course_code='".$code."'";
            Database::query($sql);*/
            // Unsubscribe all users from the course
            $sql = "DELETE FROM $table_course_user WHERE course_code='" . $codeFiltered . "'";
            Database::query($sql);
            // Delete the course from the sessions tables
            $sql = "DELETE FROM $table_session_course WHERE course_code='" . $codeFiltered . "'";
            Database::query($sql);
            $sql = "DELETE FROM $table_session_course_user WHERE course_code='" . $codeFiltered . "'";
            Database::query($sql);

            // Delete from Course - URL
            $sql = "DELETE FROM $table_course_rel_url WHERE course_code = '" . $codeFiltered . "'";
            Database::query($sql);

            $sql = 'SELECT survey_id FROM ' . $table_course_survey . ' WHERE course_code="' . $codeFiltered . '"';
            $result_surveys = Database::query($sql);
            while ($surveys = Database::fetch_array($result_surveys)) {
                $survey_id = $surveys[0];
                $sql = 'DELETE FROM ' . $table_course_survey_question . ' WHERE survey_id="' . $survey_id . '"';
                Database::query($sql);
                $sql = 'DELETE FROM ' . $table_course_survey_question_option . ' WHERE survey_id="' . $survey_id . '"';
                Database::query($sql);
                $sql = 'DELETE FROM ' . $table_course_survey . ' WHERE survey_id="' . $survey_id . '"';
                Database::query($sql);
            }

            // Delete the course from the stats tables

            $sql = "DELETE FROM $table_stats_hotpots WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_attempt WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_exercises WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_access WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_lastaccess WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_course_access WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_online WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_default WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_downloads WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_links WHERE c_id = $courseId";
            Database::query($sql);
            $sql = "DELETE FROM $table_stats_uploads WHERE c_id = $courseId";
            Database::query($sql);

            // Delete the course from the database
            $sql = "DELETE FROM $table_course WHERE code = '" . $codeFiltered . "'";
            Database::query($sql);

            // delete extra course fields
            $t_cf = Database::get_main_table(TABLE_MAIN_COURSE_FIELD);
            $t_cfv = Database::get_main_table(TABLE_MAIN_COURSE_FIELD_VALUES);

            $sql = "SELECT distinct field_id FROM $t_cfv WHERE course_code = '$codeFiltered'";
            $res_field_ids = Database::query($sql);
            $field_ids = array();
            while ($row_field_id = Database::fetch_row($res_field_ids)) {
                $field_ids[] = $row_field_id[0];
            }

            // Delete from table_course_field_value from a given course_code
            $sql_course_field_value = "DELETE FROM $t_cfv WHERE course_code = '$codeFiltered'";
            Database::query($sql_course_field_value);

            $sql = "SELECT distinct field_id FROM $t_cfv";
            $res_field_all_ids = Database::query($sql);
            $field_all_ids = array();
            while ($row_field_all_id = Database::fetch_row($res_field_all_ids)) {
                $field_all_ids[] = $row_field_all_id[0];
            }

            if (is_array($field_ids) && count($field_ids) > 0) {
                foreach ($field_ids as $field_id) {
                    // check if field id is used into table field value
                    if (is_array($field_all_ids)) {
                        if (in_array($field_id, $field_all_ids)) {
                            continue;
                        } else {
                            $sql_course_field = "DELETE FROM $t_cf WHERE id = '$field_id'";
                            Database::query($sql_course_field);
                        }
                    }
                }
            }

            // Add event to system log
            $user_id = api_get_user_id();
            Event::addEvent(
                LOG_COURSE_DELETE,
                LOG_COURSE_CODE,
                $code,
                api_get_utc_datetime(),
                $user_id,
                $code
            );
        }
    }

    /**
     * Creates a file called mysql_dump.sql in the course folder
     * @param $course_code The code of the course
     * @todo Implementation for single database
     */
    public static function create_database_dump($course_code)
    {
        global $_configuration;

        if ($_configuration['single_database']) {
            return;
        }
        $sql_dump = '';
        $course_code = Database::escape_string($course_code);
        $table_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $sql = "SELECT * FROM $table_course WHERE code = '$course_code'";
        $res = Database::query($sql);
        $course = Database::fetch_array($res);

        $course_tables = AddCourse::get_course_tables();

        if (!empty($course['id'])) {
            //Cleaning c_x tables
            foreach ($course_tables as $table) {
                $table = Database::get_course_table($table);
                $sql = "SELECT * FROM $table WHERE c_id = {$course['id']} ";
                $res_table = Database::query($sql);

                while ($row = Database::fetch_array($res_table, 'ASSOC')) {
                    $row_to_save = array();
                    foreach ($row as $key => $value) {
                        $row_to_save[$key] = $key . "='" . Database::escape_string($row[$key]) . "'";
                    }
                    $sql_dump .= "\nINSERT INTO $table SET " . implode(', ', $row_to_save) . ';';
                }
            }
        }

        if (is_dir(api_get_path(SYS_COURSE_PATH) . $course['directory'])) {
            $file_name = api_get_path(SYS_COURSE_PATH) . $course['directory'] . '/mysql_dump.sql';
            $handle = fopen($file_name, 'a+');
            if ($handle !== false) {
                fwrite($handle, $sql_dump);
                fclose($handle);
            } else {
                //TODO trigger exception in a try-catch
            }
        }
    }

    /**
     * Sort courses for a specific user ??
     * @param   int     User ID
     * @param   string  Course code
     * @return  int     Minimum course order
     * @todo Review documentation
     */
    public static function userCourseSort($user_id, $course_code)
    {

        if ($user_id != strval(intval($user_id))) {
            return false;
        }

        $course_code = Database::escape_string($course_code);
        $TABLECOURSE = Database::get_main_table(TABLE_MAIN_COURSE);
        $TABLECOURSUSER = Database::get_main_table(TABLE_MAIN_COURSE_USER);

        $course_title = Database::result(Database::query('SELECT title FROM ' . $TABLECOURSE . ' WHERE code="' . $course_code . '"'),
            0, 0);

        $sql = 'SELECT course.code as code, course.title as title, cu.sort as sort FROM ' . $TABLECOURSUSER . ' as cu, ' . $TABLECOURSE . ' as course
                WHERE   course.code = cu.course_code AND user_id = "' . $user_id . '" AND
                        cu.relation_type<>' . COURSE_RELATION_TYPE_RRHH . ' AND
                        user_course_cat = 0
                ORDER BY cu.sort';
        $result = Database::query($sql);

        $course_title_precedent = '';
        $counter = 0;
        $course_found = false;
        $course_sort = 1;

        if (Database::num_rows($result) > 0) {
            while ($courses = Database::fetch_array($result)) {
                if ($course_title_precedent == '') {
                    $course_title_precedent = $courses['title'];
                }
                if (api_strcasecmp($course_title_precedent, $course_title) < 0) {
                    $course_found = true;
                    $course_sort = $courses['sort'];
                    if ($counter == 0) {
                        $sql = 'UPDATE ' . $TABLECOURSUSER . ' SET sort = sort+1
                                WHERE user_id= "' . $user_id . '" AND relation_type<>' . COURSE_RELATION_TYPE_RRHH . ' AND user_course_cat="0" AND sort > "' . $course_sort . '"';
                        $course_sort++;
                    } else {
                        $sql = 'UPDATE ' . $TABLECOURSUSER . ' SET sort = sort+1
                                WHERE user_id= "' . $user_id . '" AND relation_type<>' . COURSE_RELATION_TYPE_RRHH . ' AND user_course_cat="0" AND sort >= "' . $course_sort . '"';
                    }
                    Database::query($sql);
                    break;

                } else {
                    $course_title_precedent = $courses['title'];
                }
                $counter++;
            }

            // We must register the course in the beginning of the list
            if (!$course_found) {
                $course_sort = Database::result(Database::query('SELECT min(sort) as min_sort FROM ' . $TABLECOURSUSER . ' WHERE user_id="' . $user_id . '" AND user_course_cat="0"'),
                    0, 0);
                Database::query('UPDATE ' . $TABLECOURSUSER . ' SET sort = sort+1 WHERE user_id= "' . $user_id . '" AND user_course_cat="0"');
            }
        }
        return $course_sort;
    }

    /**
     * create recursively all categories as option of the select passed in parameter.
     *
     * @param HTML_QuickForm_Element $element
     * @param string $defaultCode the option value to select by default (used mainly for edition of courses)
     * @param string $parentCode the parent category of the categories added (default=null for root category)
     * @param string $padding the indent param (you shouldn't indicate something here)
     * @deprecated use the select_ajax solution see admin/course_edit.php
     */
    public static function select_and_sort_categories(
        $element,
        $defaultCode = null,
        $parentCode = null,
        $padding = null
    ) {
        setCategoriesInForm($element, $defaultCode, $parentCode, $padding);
    }

    /**
     * check if course exists
     * @param string course_code
     * @param string whether to accept virtual course codes or not
     * @return true if exists, false else
     */
    public static function course_exists($course_code, $accept_virtual = false)
    {
        if ($accept_virtual === true) {
            $sql = 'SELECT 1 FROM ' . Database::get_main_table(TABLE_MAIN_COURSE) . ' WHERE code="' . Database::escape_string($course_code) . '" OR visual_code="' . Database::escape_string($course_code) . '"';
        } else {
            $sql = 'SELECT 1 FROM ' . Database::get_main_table(TABLE_MAIN_COURSE) . ' WHERE code="' . Database::escape_string($course_code) . '"';
        }
        return Database::num_rows(Database::query($sql));
    }

    /**
     * Send an email to tutor after the auth-suscription of a student in your course
     * @author Carlos Vargas <carlos.vargas@dokeos.com>, Dokeos Latino
     * @param  int $user_id the id of the user
     * @param  string $course_code the course code
     * @param  string $send_to_tutor_also
     * @return string we return the message that is displayed when the action is succesfull
     */
    public static function email_to_tutor($user_id, $course_code, $send_to_tutor_also = false)
    {

        if ($user_id != strval(intval($user_id))) {
            return false;
        }

        $course_code = Database::escape_string($course_code);

        $student = Database::fetch_array(Database::query("SELECT * FROM " . Database::get_main_table(TABLE_MAIN_USER) . "
                WHERE user_id='" . $user_id . "'"));
        $information = self::get_course_information($course_code);
        $name_course = $information['title'];
        $sql = "SELECT * FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . " WHERE course_code='" . $course_code . "'";

        // TODO: Ivan: This is a mistake, please, have a look at it. Intention here is diffcult to be guessed.
        //if ($send_to_tutor_also = true)
        // Proposed change:
        if ($send_to_tutor_also) {
            //
            $sql .= " AND tutor_id=1";
        } else {
            $sql .= " AND status=1";
        }

        $result = Database::query($sql);
        while ($row = Database::fetch_array($result)) {
            $tutor = Database::fetch_array(Database::query("SELECT * FROM " . Database::get_main_table(TABLE_MAIN_USER) . "
                    WHERE user_id='" . $row['user_id'] . "'"));
            $emailto = $tutor['email'];
            $emailsubject = get_lang('NewUserInTheCourse') . ': ' . $name_course;
            $emailbody = get_lang('Dear') . ': ' . api_get_person_name($tutor['firstname'], $tutor['lastname']) . "\n";
            $emailbody .= get_lang('MessageNewUserInTheCourse') . ': ' . $name_course . "\n";
            $emailbody .= get_lang('UserName') . ': ' . $student['username'] . "\n";
            if (api_is_western_name_order()) {
                $emailbody .= get_lang('FirstName') . ': ' . $student['firstname'] . "\n";
                $emailbody .= get_lang('LastName') . ': ' . $student['lastname'] . "\n";
            } else {
                $emailbody .= get_lang('LastName') . ': ' . $student['lastname'] . "\n";
                $emailbody .= get_lang('FirstName') . ': ' . $student['firstname'] . "\n";
            }
            $emailbody .= get_lang('Email') . ': ' . $student['email'] . "\n\n";
            $recipient_name = api_get_person_name($tutor['firstname'], $tutor['lastname'], null,
                PERSON_NAME_EMAIL_ADDRESS);
            $sender_name = api_get_person_name(api_get_setting('administratorName'),
                api_get_setting('administratorSurname'), null, PERSON_NAME_EMAIL_ADDRESS);
            $email_admin = api_get_setting('emailAdministrator');

            $plugin = new AppPlugin();
            $additionalParameters = array(
                'smsType' => constant($plugin->getSMSPluginName() . '::NEW_USER_SUBSCRIBED_COURSE'),
                'userId' => $tutor['user_id'],
                'userUsername' => $student['username'],
                'courseCode' => $course_code
            );

            api_mail_html(
                $recipient_name,
                $emailto,
                $emailsubject,
                $emailbody,
                $sender_name,
                $email_admin,
                null,
                null,
                null,
                $additionalParameters
            );
        }
    }

    /**
     * @return array
     */
    public static function get_special_course_list()
    {
        $tbl_course_field = Database:: get_main_table(TABLE_MAIN_COURSE_FIELD);
        $tbl_course_field_value = Database:: get_main_table(TABLE_MAIN_COURSE_FIELD_VALUES);

        //we filter the courses from the URL
        $join_access_url = $where_access_url = '';
        if (api_get_multiple_access_url()) {
            $access_url_id = api_get_current_access_url_id();
            if ($access_url_id != -1) {
                $tbl_url_course = Database:: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
                $join_access_url = "LEFT JOIN $tbl_url_course url_rel_course ON url_rel_course.course_code= tcfv.course_code ";
                $where_access_url = " AND access_url_id = $access_url_id ";
            }
        }

        // get course list auto-register
        $sql = "SELECT DISTINCT(tcfv.course_code) FROM $tbl_course_field_value tcfv INNER JOIN $tbl_course_field tcf
                ON tcfv.field_id =  tcf.id $join_access_url
                WHERE tcf.field_variable = 'special_course' AND tcfv.field_value = 1  $where_access_url";
        $special_course_result = Database::query($sql);
        $special_course_list = array();

        if (Database::num_rows($special_course_result) > 0) {
            $special_course_list = array();
            while ($result_row = Database::fetch_array($special_course_result)) {
                $special_course_list[] = $result_row['course_code'];
            }
        }
        return $special_course_list;
    }

    /**
     * Get list of courses for a given user
     * @param int $user_id
     * @param boolean $include_sessions Whether to include courses from session or not
     * @return array    List of codes and db names
     * @author isaac flores paz
     */
    public static function get_courses_list_by_user_id($user_id, $include_sessions = false)
    {
        $user_id = intval($user_id);
        $course_list = array();
        $codes = array();
        $tbl_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $tbl_course_user = Database::get_main_table(TABLE_MAIN_COURSE_USER);
        $tbl_user_course_category = Database::get_main_table(TABLE_USER_COURSE_CATEGORY);
        $special_course_list = self::get_special_course_list();

        $with_special_courses = $without_special_courses = '';
        if (!empty($special_course_list)) {
            $sc_string = '"' . implode('","', $special_course_list) . '"';
            $with_special_courses = ' course.code IN (' . $sc_string . ')';
            $without_special_courses = ' AND course.code NOT IN (' . $sc_string . ')';
        }

        if (!empty($with_special_courses)) {
            $sql = "SELECT DISTINCT(course.code), course.id as real_id
                        FROM    " . $tbl_course_user . " course_rel_user
                        LEFT JOIN " . $tbl_course . " course
                        ON course.code = course_rel_user.course_code
                        LEFT JOIN " . $tbl_user_course_category . " user_course_category
                        ON course_rel_user.user_course_cat = user_course_category.id
                        WHERE  $with_special_courses
                        GROUP BY course.code
                        ORDER BY user_course_category.sort,course.title,course_rel_user.sort ASC";
            $rs_special_course = Database::query($sql);
            if (Database::num_rows($rs_special_course) > 0) {
                while ($result_row = Database::fetch_array($rs_special_course)) {
                    $result_row['special_course'] = 1;
                    $course_list[] = $result_row;
                    $codes[] = $result_row['real_id'];
                }
            }
        }

        // get course list not auto-register. Use Distinct to avoid multiple
        // entries when a course is assigned to a HRD (DRH) as watcher
        $sql = "SELECT DISTINCT(course.code), course.id as real_id
                FROM $tbl_course course
                INNER JOIN $tbl_course_user cru ON course.code=cru.course_code
                WHERE cru.user_id='$user_id' $without_special_courses";

        $result = Database::query($sql);

        if (Database::num_rows($result)) {
            while ($row = Database::fetch_array($result, 'ASSOC')) {
                $course_list[] = $row;
                $codes[] = $row['real_id'];
            }
        }

        if ($include_sessions === true) {
            $sql = "SELECT DISTINCT(c.code), c.id as real_id
                    FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER) . " s, " . Database::get_main_table(TABLE_MAIN_COURSE) . " c
                    WHERE id_user = $user_id AND s.course_code=c.code";
            $r = Database::query($sql);
            while ($row = Database::fetch_array($r, 'ASSOC')) {
                if (!in_array($row['real_id'], $codes)) {
                    $course_list[] = $row;
                }
            }
        }

        return $course_list;
    }

    /**
     * Get course ID from a given course directory name
     * @param   string  Course directory (without any slash)
     * @return  string  Course code, or false if not found
     */
    public static function get_course_id_from_path($path)
    {
        $path = Database::escape_string(str_replace('.', '', str_replace('/', '', $path)));
        $res = Database::query("SELECT code FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . "
                WHERE directory LIKE BINARY '$path'");
        if ($res === false) {
            return false;
        }
        if (Database::num_rows($res) != 1) {
            return false;
        }
        $row = Database::fetch_array($res);
        return $row['code'];
    }

    /**
     * Get course code(s) from visual code
     * @deprecated
     * @param   string  Visual code
     * @return  array   List of codes for the given visual code
     */
    public static function get_courses_info_from_visual_code($code)
    {
        $result = array();
        $sql_result = Database::query("SELECT * FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . "
                WHERE visual_code = '" . Database::escape_string($code) . "'");
        while ($virtual_course = Database::fetch_array($sql_result)) {
            $result[] = $virtual_course;
        }
        return $result;
    }

    /**
     * Get emails of tutors to course
     * @param string Visual code
     * @return array List of emails of tutors to course
     * @author @author Carlos Vargas <carlos.vargas@dokeos.com>, Dokeos Latino
     * */
    public static function get_emails_of_tutors_to_course($code)
    {
        $list = array();
        $res = Database::query("SELECT user_id FROM " . Database::get_main_table(TABLE_MAIN_COURSE_USER) . "
                WHERE course_code='" . Database::escape_string($code) . "' AND status=1");
        while ($list_users = Database::fetch_array($res)) {
            $result = Database::query("SELECT * FROM " . Database::get_main_table(TABLE_MAIN_USER) . "
                    WHERE user_id=" . $list_users['user_id']);
            while ($row_user = Database::fetch_array($result)) {
                $name_teacher = api_get_person_name($row_user['firstname'], $row_user['lastname']);
                $list[] = array($row_user['email'] => $name_teacher);
            }
        }
        return $list;
    }

    /**
     * Get coaches emails by session
     * @param int session id
     * @param string course code
     * @return array  array(email => name_tutor)  by coach
     * @author Carlos Vargas <carlos.vargas@dokeos.com>
     */
    public static function get_email_of_tutor_to_session($session_id, $course_code)
    {
        $tbl_session_course_user = Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER);
        $tbl_user = Database::get_main_table(TABLE_MAIN_USER);
        $coachs_emails = array();

        $course_code = Database::escape_string($course_code);
        $session_id = intval($session_id);

        $sql = "SELECT id_user FROM $tbl_session_course_user
                WHERE id_session='$session_id' AND course_code='$course_code' AND status = 2";
        $rs = Database::query($sql);

        if (Database::num_rows($rs) > 0) {

            $user_ids = array();
            while ($row = Database::fetch_array($rs)) {
                $user_ids[] = $row['id_user'];
            }

            $sql = "SELECT firstname, lastname, email FROM $tbl_user
                    WHERE user_id IN (" . implode(",", $user_ids) . ")";
            $rs_user = Database::query($sql);

            while ($row_emails = Database::fetch_array($rs_user)) {
                $mail_tutor = array(
                    'email' => $row_emails['email'],
                    'complete_name' => api_get_person_name($row_emails['firstname'], $row_emails['lastname'])
                );
                $coachs_emails[] = $mail_tutor;
            }
        }
        return $coachs_emails;
    }

    /**
     * Creates a new extra field for a given course
     * @param    string    Field's internal variable name
     * @param    int        Field's type
     * @param    string    Field's language var name
     * @return int     new extra field id
     */
    public static function create_course_extra_field($fieldvarname, $fieldtype, $fieldtitle)
    {
        // database table definition
        $t_cfv = Database::get_main_table(TABLE_MAIN_COURSE_FIELD_VALUES);
        $t_cf = Database::get_main_table(TABLE_MAIN_COURSE_FIELD);
        $fieldvarname = Database::escape_string($fieldvarname);
        $fieldtitle = Database::escape_string($fieldtitle);
        $fieldtype = (int)$fieldtype;
        $time = time();
        $sql_field = "SELECT id FROM $t_cf WHERE field_variable = '$fieldvarname'";
        $res_field = Database::query($sql_field);

        $r_field = Database::fetch_row($res_field);

        if (Database::num_rows($res_field) > 0) {
            return $r_field[0];
        }

        // save new fieldlabel into course_field table
        $sql = "SELECT MAX(field_order) FROM $t_cf";
        $res = Database::query($sql);

        $order = 0;
        if (Database::num_rows($res) > 0) {
            $row = Database::fetch_row($res);
            $order = $row[0] + 1;
        }

        $sql = "INSERT INTO $t_cf SET
                field_type = '$fieldtype',
                field_variable = '$fieldvarname',
                field_display_text = '$fieldtitle',
                field_order = '$order',
                tms = FROM_UNIXTIME($time)";
        Database::query($sql);

        return Database::insert_id();
    }

    /**
     * Updates course attribute. Note that you need to check that your attribute is valid before you use this function
     *
     * @param int Course id
     * @param string Attribute name
     * @param string Attribute value
     * @return bool True if attribute was successfully updated, false if course was not found or attribute name is invalid
     */
    public static function update_attribute($id, $name, $value)
    {
        $id = (int)$id;
        $table = Database::get_main_table(TABLE_MAIN_COURSE);
        $sql = "UPDATE $table SET $name = '" . Database::escape_string($value) . "' WHERE id = '$id';";
        return Database::query($sql);
    }

    /**
     * Update course attributes. Will only update attributes with a non-empty value.
     * Note that you NEED to check that your attributes are valid before using this function
     *
     * @param int Course id
     * @param array Associative array with field names as keys and field values as values
     * @return bool True if update was successful, false otherwise
     */
    public static function update_attributes($id, $attributes)
    {
        $id = (int)$id;
        $table = Database::get_main_table(TABLE_MAIN_COURSE);
        $sql = "UPDATE $table SET ";
        $i = 0;
        foreach ($attributes as $name => $value) {
            if ($value != '') {
                if ($i > 0) {
                    $sql .= ", ";
                }
                $sql .= " $name = '" . Database::escape_string($value) . "'";
                $i++;
            }
        }
        $sql .= " WHERE id = '$id';";
        return Database::query($sql);
    }

    /**
     * Update an extra field value for a given course
     * @param    integer    Course ID
     * @param    string    Field variable name
     * @param    string    Field value
     * @return    boolean    true if field updated, false otherwise
     */
    public static function update_course_extra_field_value($course_code, $fname, $fvalue = '')
    {
        $t_cfv = Database::get_main_table(TABLE_MAIN_COURSE_FIELD_VALUES);
        $t_cf = Database::get_main_table(TABLE_MAIN_COURSE_FIELD);
        $fname = Database::escape_string($fname);
        $course_code = Database::escape_string($course_code);
        $fvalues = '';
        if (is_array($fvalue)) {
            foreach ($fvalue as $val) {
                $fvalues .= Database::escape_string($val) . ';';
            }
            if (!empty($fvalues)) {
                $fvalues = substr($fvalues, 0, -1);
            }
        } else {
            $fvalues = Database::escape_string($fvalue);
        }

        $sqlcf = "SELECT * FROM $t_cf WHERE field_variable='$fname'";
        $rescf = Database::query($sqlcf);
        if (Database::num_rows($rescf) == 1) {
            // Ok, the field exists
            // Check if enumerated field, if the option is available
            $rowcf = Database::fetch_array($rescf);

            $tms = time();
            $sqlcfv = "SELECT * FROM $t_cfv
                       WHERE course_code = '$course_code' AND field_id = '" . $rowcf['id'] . "'
                       ORDER BY id";
            $rescfv = Database::query($sqlcfv);
            $n = Database::num_rows($rescfv);
            if ($n > 1) {
                //problem, we already have to values for this field and user combination - keep last one
                while ($rowcfv = Database::fetch_array($rescfv)) { // See the TODO note below.
                    if ($n > 1) {
                        $sqld = "DELETE FROM $t_cfv WHERE id = " . $rowcfv['id'];
                        $resd = Database::query($sqld);
                        $n--;
                    }
                    $rowcfv = Database::fetch_array($rescfv);
                    if ($rowcfv['field_value'] != $fvalues) {
                        $sqlu = "UPDATE $t_cfv SET field_value = '$fvalues', tms = FROM_UNIXTIME($tms)
                                 WHERE id = " . $rowcfv['id'];
                        $resu = Database::query($sqlu);
                        return ($resu ? true : false);
                    }
                    return true;
                    /* TODO: Sure exit from the function occures in this "while" cycle.
                    Logic should checked. Maybe "if" instead of "while"? It is not clear... */
                }
            } elseif ($n == 1) {
                //we need to update the current record
                $rowcfv = Database::fetch_array($rescfv);
                if ($rowcfv['field_value'] != $fvalues) {
                    $sqlu = "UPDATE $t_cfv SET field_value = '$fvalues', tms = FROM_UNIXTIME($tms) WHERE id = " . $rowcfv['id'];
                    $resu = Database::query($sqlu);
                    return ($resu ? true : false);
                }
                return true;
            } else {
                $sqli = "INSERT INTO $t_cfv (course_code,field_id,field_value,tms) " .
                    "VALUES ('$course_code'," . $rowcf['id'] . ",'$fvalues',FROM_UNIXTIME($tms))";
                $resi = Database::query($sqli);
                return ($resi ? true : false);
            }
        } else {
            return false; //field not found
        }
    }

    /**
     * Get the course id of an course by the database name
     * @deprecated
     * @param string The database name
     * @return string The course id
     */
    public static function get_course_id_by_database_name($db_name)
    {
        return Database::result(Database::query('SELECT code FROM ' . Database::get_main_table(TABLE_MAIN_COURSE) .
            ' WHERE db_name="' . Database::escape_string($db_name) . '"'), 0, 'code');
    }

    public static function get_session_category_id_by_session_id($session_id)
    {
        return Database::result(Database::query('SELECT  sc.id session_category
                FROM ' . Database::get_main_table(TABLE_MAIN_SESSION_CATEGORY) . ' sc
                INNER JOIN ' . Database::get_main_table(TABLE_MAIN_SESSION) . ' s
                ON sc.id=s.session_category_id WHERE s.id="' . Database::escape_string($session_id) . '"'),
            0, 'session_category');
    }

    /**
     * Get the course id of an course by the database name
     * @param string The database name
     * @return string The course id
     */
    public static function get_course_extra_field_list($code)
    {
        $tbl_course_field = Database::get_main_table(TABLE_MAIN_COURSE_FIELD);
        $tbl_course_field_value = Database::get_main_table(TABLE_MAIN_COURSE_FIELD_VALUES);
        $sql_field = "SELECT id, field_type, field_variable, field_display_text, field_default_value
            FROM $tbl_course_field  WHERE field_visible = '1' ";
        $res_field = Database::query($sql_field);
        $extra_fields = array();
        while ($rowcf = Database::fetch_array($res_field)) {
            $extra_field_id = $rowcf['id'];
            $sql = "SELECT field_value FROM $tbl_course_field_value
                    WHERE course_code = '$code' AND field_id = '$extra_field_id'";
            $res_field_value = Database::query($sql);
            if (Database::num_rows($res_field_value) > 0) {
                $r_field_value = Database::fetch_row($res_field_value);
                $rowcf['extra_field_value'] = $r_field_value[0];
            }
            $extra_fields[] = $rowcf;
        }
        return $extra_fields;
    }

    /**
     * Gets the value of a course extra field. Returns null if it was not found
     *
     * @param string Name of the extra field
     * @param string Course code
     * @return string Value
     */
    public static function get_course_extra_field_value($field_name, $code)
    {
        $tbl_course_field = Database::get_main_table(TABLE_MAIN_COURSE_FIELD);
        $tbl_course_field_value = Database::get_main_table(TABLE_MAIN_COURSE_FIELD_VALUES);
        $sql = "SELECT id FROM $tbl_course_field WHERE field_visible = '1' AND field_variable = '$field_name';";
        $res = Database::query($sql);
        $row = Database::fetch_object($res);
        if (!$row) {
            return null;
        } else {
            $sql = "SELECT field_value FROM $tbl_course_field_value
                    WHERE course_code = '$code' AND field_id = '{$row->id}';";
            $res_field_value = Database::query($sql);
            $row_field_value = Database::fetch_object($res_field_value);
            if (!$row_field_value) {
                return null;
            } else {
                return $row_field_value['field_value'];
            }
        }
    }

    /**
     * Get the database name of a course by the code
     * @deprecated
     * @param string The course code
     * @return string The database name
     */
    public static function get_name_database_course($course_code)
    {
        return Database::result(Database::query('SELECT db_name FROM ' . Database::get_main_table(TABLE_MAIN_COURSE) .
            ' WHERE code="' . Database::escape_string($course_code) . '"'), 0, 'db_name');
    }

    /**
     * Lists details of the course description
     * @param array        The course description
     * @param string    The encoding
     * @param bool        If true is displayed if false is hidden
     * @return string     The course description in html
     */
    public static function get_details_course_description_html($descriptions, $charset, $action_show = true)
    {
        $data = null;
        if (isset($descriptions) && count($descriptions) > 0) {
            foreach ($descriptions as $description) {
                $data .= '<div class="sectiontitle">';
                if (api_is_allowed_to_edit() && $action_show) {
                    //delete
                    $data .= '<a href="' . api_get_self() . '?' . api_get_cidreq() . '&amp;action=delete&amp;description_id=' . $description->id . '" onclick="javascript:if(!confirm(\'' . addslashes(api_htmlentities(get_lang('ConfirmYourChoice'),
                                ENT_QUOTES, $charset)) . '\')) return false;">';
                    $data .= Display::return_icon('delete.gif', get_lang('Delete'),
                        array('style' => 'vertical-align:middle;float:right;'));
                    $data .= '</a> ';
                    //edit
                    $data .= '<a href="' . api_get_self() . '?' . api_get_cidreq() . '&amp;description_id=' . $description->id . '">';
                    $data .= Display::return_icon('edit.png', get_lang('Edit'),
                        array('style' => 'vertical-align:middle;float:right; padding-right:4px;'), ICON_SIZE_SMALL);
                    $data .= '</a> ';
                }
                $data .= $description->title;
                $data .= '</div>';
                $data .= '<div class="sectioncomment">';
                $data .= $description->content;
                $data .= '</div>';
            }
        } else {
            $data .= '<em>' . get_lang('ThisCourseDescriptionIsEmpty') . '</em>';
        }

        return $data;
    }

    /**
     * Returns the details of a course category
     *
     * @param string Category code
     * @return array Course category
     */
    public static function get_course_category($code)
    {
        $table_categories = Database::get_main_table(TABLE_MAIN_CATEGORY);
        $sql = "SELECT * FROM $table_categories WHERE code = '$code';";
        return Database::fetch_array(Database::query($sql));
    }

    /**
     *    This code creates a select form element to let the user
     *    choose a real course to link to.
     *
     *    A good non-display library should not use echo statements, but just return text/html
     *    so users of the library can choose when to display.
     *
     *    We display the course code, but internally store the course id.
     *
     * @param boolean $has_size , true the select tag gets a size element, false it stays a dropdownmenu
     * @param boolean $only_current_user_courses , true only the real courses of which the
     *    current user is course admin are displayed, false all real courses are shown.
     * @param string $element_name the name of the select element
     * @return a string containing html code for a form select element.
     * @deprecated Function not in use
     */
    public static function get_real_course_code_select_html(
        $element_name,
        $has_size = true,
        $only_current_user_courses = true,
        $user_id
    ) {
        if ($only_current_user_courses) {
            $real_course_list = self::get_real_course_list_of_user_as_course_admin($user_id);
        } else {
            $real_course_list = self::get_real_course_list();
        }

        if ($has_size) {
            $size_element = "size=\"" . SELECT_BOX_SIZE . "\"";
        } else {
            $size_element = "";
        }
        $html_code = "<select name=\"$element_name\" $size_element >\n";
        foreach ($real_course_list as $real_course) {
            $course_code = $real_course["code"];
            $html_code .= "<option value=\"" . $course_code . "\">";
            $html_code .= $course_code;
            $html_code .= "</option>\n";
        }
        $html_code .= "</select>\n";

        return $html_code;
    }

    /**
     *  Get count rows of a table inside a course database
     * @param  string    The table of which the rows should be counted
     * @param  int        optionally count rows by session id
     * @return int     The number of rows in the given table.
     * @deprecated
     */
    public static function count_rows_course_table($table, $session_id = '', $course_id = null)
    {
        $condition_session = '';
        if ($session_id !== '') {
            $session_id = intval($session_id);
            $condition_session = " AND session_id = '$session_id' ";
        }
        if (!empty($course_id)) {
            $course_id = intval($course_id);
        } else {
            $course_id = api_get_course_int_id();
        }
        $condition_session .= " AND c_id = '$course_id' ";

        $sql = "SELECT COUNT(*) AS n FROM $table WHERE 1=1 $condition_session ";
        $rs = Database::query($sql);
        $row = Database::fetch_row($rs);
        return $row[0];
    }

    /**
     * Subscribes courses to human resource manager (Dashboard feature)
     * @param    int         Human Resource Manager id
     * @param    array        Courses code
     * @param    int            Relation type
     **/
    public static function suscribe_courses_to_hr_manager($hr_manager_id, $courses_list)
    {
        global $_configuration;

        // Database Table Definitions
        $tbl_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $tbl_course_rel_user = Database::get_main_table(TABLE_MAIN_COURSE_USER);
        $tbl_course_rel_access_url = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);

        $hr_manager_id = intval($hr_manager_id);
        $affected_rows = 0;

        //Deleting assigned courses to hrm_id
        if ($_configuration['multiple_access_urls']) {
            $sql = "SELECT s.course_code FROM $tbl_course_rel_user s
                    INNER JOIN $tbl_course_rel_access_url a ON (a.course_code = s.course_code)
                    WHERE user_id = $hr_manager_id AND relation_type=" . COURSE_RELATION_TYPE_RRHH . " AND access_url_id = " . api_get_current_access_url_id() . "";
        } else {
            $sql = "SELECT course_code FROM $tbl_course_rel_user
                    WHERE user_id = $hr_manager_id AND relation_type=" . COURSE_RELATION_TYPE_RRHH . " ";
        }
        $result = Database::query($sql);
        if (Database::num_rows($result) > 0) {
            while ($row = Database::fetch_array($result)) {
                $sql = "DELETE FROM $tbl_course_rel_user
                        WHERE course_code = '{$row['course_code']}' AND user_id = $hr_manager_id AND relation_type=" . COURSE_RELATION_TYPE_RRHH . " ";
                Database::query($sql);
            }
        }

        // inserting new courses list
        if (is_array($courses_list)) {
            foreach ($courses_list as $course_code) {
                $course_code = Database::escape_string($course_code);
                $insert_sql = "INSERT IGNORE INTO $tbl_course_rel_user(course_code, user_id, status, relation_type)
                               VALUES('$course_code', $hr_manager_id, '" . DRH . "', '" . COURSE_RELATION_TYPE_RRHH . "')";
                Database::query($insert_sql);
                if (Database::affected_rows()) {
                    $affected_rows++;
                }
            }
        }

        return $affected_rows;
    }

    /**
     * get courses followed by human resources manager
     * @param int $user_id
     * @param int $from
     * @param int $limit
     * @param string $column
     * @param string $direction
     * @return array    courses
     */
    public static function get_courses_followed_by_drh(
        $user_id,
        $status = DRH,
        $from = null,
        $limit = null,
        $column = null,
        $direction = null,
        $getCount = false
    ) {
        return self::getCoursesFollowedByUser(
            $user_id,
            $status,
            $from,
            $limit,
            $column,
            $direction,
            $getCount
        );
    }

    /**
     * get courses followed by user
     * @param   int $user_id
     * @param   int $status
     * @param   int $from
     * @param   int $limit
     * @param   string $column
     * @param   string $direction
     * @param   boolean $getCount
     * @param   string $keyword
     * @param   int $sessionId
     * @param   boolean $showAllAssignedCourses
     * @return  array   courses
     */
    public static function getCoursesFollowedByUser(
        $user_id,
        $status = null,
        $from = null,
        $limit = null,
        $column = null,
        $direction = null,
        $getCount = false,
        $keyword = null,
        $sessionId = null,
        $showAllAssignedCourses = false
    ) {
        // Database Table Definitions
        $tbl_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $tbl_course_rel_user = Database::get_main_table(TABLE_MAIN_COURSE_USER);
        $tbl_course_rel_access_url = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        $sessionId = intval($sessionId);
        $user_id = intval($user_id);
        $select = "SELECT DISTINCT *, id as real_id ";

        if ($getCount) {
            $select = "SELECT COUNT(DISTINCT id) as count";
        }

        $whereConditions = null;
        switch ($status) {
            case COURSEMANAGER:
                $whereConditions .= " AND cru.user_id = '$user_id'";
                if (!$showAllAssignedCourses) {
                    $whereConditions .= " AND status = " . COURSEMANAGER;
                } else {
                    $whereConditions .= " AND relation_type = " . COURSE_RELATION_TYPE_COURSE_MANAGER;
                }
                break;
            case DRH:
                $whereConditions .= " AND
                    cru.user_id = '$user_id' AND
                    status = " . DRH . " AND
                    relation_type = '" . COURSE_RELATION_TYPE_RRHH . "'
                ";
                break;
        }

        $keywordCondition = null;
        if (!empty($keyword)) {
            $keyword = Database::escape_string($keyword);
            $keywordCondition = " AND (c.code LIKE '%$keyword%' OR c.title LIKE '%$keyword%' ) ";
        }

        $orderBy = null;
        $extraInnerJoin = null;

        if (!empty($sessionId)) {
            if (!empty($sessionId)) {
                $courseList = SessionManager::get_course_list_by_session_id(
                    $sessionId
                );
                if (!empty($courseList)) {
                    $courseListToString = implode("','", array_keys($courseList));
                    $whereConditions .= " AND c.id IN ('" . $courseListToString . "')";
                }
                $tableSessionRelCourse = Database::get_main_table(TABLE_MAIN_SESSION_COURSE);
                $orderBy = ' ORDER BY position';
                $extraInnerJoin = " INNER JOIN $tableSessionRelCourse src
                                    ON (c.code = src.course_code AND id_session = $sessionId) ";
            }
        }

        $whereConditions .= $keywordCondition;
        $sql = "$select
                FROM $tbl_course c
                    INNER JOIN $tbl_course_rel_user cru ON (cru.course_code = c.code)
                    INNER JOIN $tbl_course_rel_access_url a ON (a.course_code = c.code)
                    $extraInnerJoin
                WHERE
                    access_url_id = " . api_get_current_access_url_id() . "
                    $whereConditions
                $orderBy
                ";
        if (isset($from) && isset($limit)) {
            $from = intval($from);
            $limit = intval($limit);
            $sql .= " LIMIT $from, $limit";
        }

        $result = Database::query($sql);

        if ($getCount) {
            $row = Database::fetch_array($result);
            return $row['count'];
        }

        $courses = array();
        if (Database::num_rows($result) > 0) {
            while ($row = Database::fetch_array($result)) {
                $courses[$row['code']] = $row;
            }
        }
        return $courses;
    }

    /**
     * check if a course is special (autoregister)
     * @param string $course_code
     */
    public static function is_special_course($course_code)
    {
        $tbl_course_field_value = Database::get_main_table(TABLE_MAIN_COURSE_FIELD_VALUES);
        $tbl_course_field = Database::get_main_table(TABLE_MAIN_COURSE_FIELD);

        $is_special = false;
        $course_code = Database::escape_string($course_code);
        $sql = "SELECT course_code
                FROM $tbl_course_field_value tcfv
                INNER JOIN $tbl_course_field tcf ON tcfv.field_id =  tcf.id
                WHERE
                    tcf.field_variable = 'special_course' AND
                    tcfv.field_value = 1 AND
                    course_code='$course_code'";
        $result = Database::query($sql);
        $num_rows = Database::num_rows($result);
        if ($num_rows > 0) {
            $is_special = true;
        }

        return $is_special;
    }

    /**
     * Update course picture
     * @param   string  Course code
     * @param   string  File name
     * @param   string  The full system name of the image from which course picture will be created.
     * @return  bool    Returns the resulting. In case of internal error or negative validation returns FALSE.
     */
    public static function update_course_picture($course_code, $filename, $source_file = null)
    {
        $course_info = api_get_course_info($course_code);
        // course path
        $store_path = api_get_path(SYS_COURSE_PATH) . $course_info['path'];
        // image name for courses
        $course_image = $store_path . '/course-pic.png';
        $course_medium_image = $store_path . '/course-pic85x85.png';

        if (file_exists($course_image)) {
            unlink($course_image);
        }
        if (file_exists($course_medium_image)) {
            unlink($course_medium_image);
        }

        $my_course_image = new Image($source_file);
        $result = $my_course_image->send_image($course_image, -1, 'png');
        // Redimension image to 100x85 (should be 85x85 but 100x85 visually gives
        // better results for most images people put as course icon)
        if ($result) {
            $medium = new Image($course_image);
            //$picture_infos = $medium->get_image_size();
            $medium->resize(100, 85, 0, false);
            $medium->send_image($store_path . '/course-pic85x85.png', -1, 'png');
        }
        return $result;
    }

    /**
     * Deletes the course picture
     * @param string $courseCode
     */
    public static function deleteCoursePicture($courseCode)
    {
        $course_info = api_get_course_info($courseCode);
        // course path
        $storePath = api_get_path(SYS_COURSE_PATH) . $course_info['path'];
        // image name for courses
        $courseImage = $storePath . '/course-pic.png';
        $courseMediumImage = $storePath . '/course-pic85x85.png';
        $courseSmallImage = $storePath . '/course-pic32.png';

        if (file_exists($courseImage)) {
            unlink($courseImage);
        }
        if (file_exists($courseMediumImage)) {
            unlink($courseMediumImage);
        }
        if (file_exists($courseSmallImage)) {
            unlink($courseSmallImage);
        }
    }

    /**
     * @deprecated See CourseManager::course_code_exists()
     */
    public static function is_existing_course_code($wanted_course_code)
    {
        return self::course_code_exists($wanted_course_code);
    }

    /**
     * Builds the course block in user_portal.php
     * @todo use Twig
     */
    public static function course_item_html_no_icon($params)
    {
        $html = '<div class="course_item">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-7">';

        $notifications = isset($params['notifications']) ? $params['notifications'] : null;

        $html .= '<h3>' . $params['title'] . $notifications . '</h3> ';

        if (isset($params['description'])) {
            $html .= '<p>' . $params['description'] . '</p>';
        }
        if (!empty($params['subtitle'])) {
            $html .= '<small>' . $params['subtitle'] . '</small>';
        }
        if (!empty($params['teachers'])) {
            $html .= '<h5>' . Display::return_icon('teacher.png', get_lang('Teacher'), array(),
                    ICON_SIZE_TINY) . $params['teachers'] . '</h5>';
        }
        if (!empty($params['coaches'])) {
            $html .= '<h5>' . Display::return_icon('teacher.png', get_lang('Coach'), array(),
                    ICON_SIZE_TINY) . $params['coaches'] . '</h5>';
        }

        $html .= '</div>';
        $params['right_actions'] = isset($params['right_actions']) ? $params['right_actions'] : null;
        $html .= '<div class="span1 pull-right course-box-actions">' . $params['right_actions'] . '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Builds the course block in user_portal.php
     * @todo use Twig
     */
    public static function course_item_html($params, $is_sub_content = false)
    {
        $html = '';
        $class = "panel panel-default";
        if ($is_sub_content) {
            $class = "course_item";
        }
        $html .= '<div class="' . $class . '">';
        $html .= '<div class="panel-body">';
        $html .= '<div class="course-items">';
        $html .= ' <div class="row">';
        $html .= '<div class="col-md-2">';
        if (!empty($params['link'])) {
            $html .= '<a class="thumbnail" href="' . $params['link'] . '">';
            $html .= $params['icon'];
            $html .= '</a>';
        } else {
            $html .= '<div class="thumbnail">';
            $html .= $params['icon'];
            $html .= '</div>';
        }

        $html .= '</div>';
        $notifications = isset($params['notifications']) ? $params['notifications'] : null;
        $param_class = isset($params['class']) ? $params['class'] : null;

        $html .= '<div class="col-md-10 ' . $param_class . '">';
        $html .= '<h3 class="title">' . $params['title'] . $notifications . '</h3> ';

        if (isset($params['show_description'], $params['description']) && $params['show_description'] == 1) {
            $html .= '<p class="description-session">' . $params['description'] . '</p>';
        }
        if (!empty($params['subtitle'])) {
            $html .= '<div class="subtitle-session">' . $params['subtitle'] . '</div>';
        }
        if (!empty($params['teachers'])) {
            $html .= '<h5>' . Display::return_icon('teacher.png', get_lang('Teacher'), array(),
                    ICON_SIZE_TINY) . $params['teachers'] . '</h5>';
        }
        if (!empty($params['coaches'])) {
            $html .= '<h5>' . Display::return_icon('teacher.png', get_lang('Coach'), array(),
                    ICON_SIZE_TINY) . $params['coaches'] . '</h5>';
        }

        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';
        $params['right_actions'] = isset($params['right_actions']) ? $params['right_actions'] : null;
        $html .= '<div class="pull-right">' . $params['right_actions'] . '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * @param string $main_content
     * @param string $sub_content
     * @param string $sub_sub_content
     * @return string
     */
    public static function course_item_parent($main_content, $sub_content, $sub_sub_content = null)
    {
        return '<div class="panel panel-default">' . $main_content . $sub_content . $sub_sub_content . '</div>';
    }

    /**
     * Display special courses (and only these) as several HTML divs of class userportal-course-item
     *
     * Special courses are courses that stick on top of the list and are "auto-registerable"
     * in the sense that any user clicking them is registered as a student
     * @param int       User id
     * @param bool      Whether to show the document quick-loader or not
     * @return string
     */
    public static function display_special_courses($user_id, $load_dirs = false)
    {
        $user_id = intval($user_id);
        $tbl_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $tbl_course_user = Database::get_main_table(TABLE_MAIN_COURSE_USER);

        $special_course_list = self::get_special_course_list();

        $with_special_courses = $without_special_courses = '';
        if (!empty($special_course_list)) {
            $with_special_courses = ' course.code IN ("' . implode('","', $special_course_list) . '")';
        }
        $html = null;

        if (!empty($with_special_courses)) {
            $sql = "SELECT course.id, course.code, course.subscribe subscr, course.unsubscribe unsubscr, course_rel_user.status status,
                           course_rel_user.sort sort, course_rel_user.user_course_cat user_course_cat, course_rel_user.user_id
                    FROM $tbl_course course
                    LEFT JOIN $tbl_course_user course_rel_user
                    ON course.code = course_rel_user.course_code AND course_rel_user.user_id = '$user_id'
                    WHERE $with_special_courses group by course.code";

            $rs_special_course = Database::query($sql);
            $number_of_courses = Database::num_rows($rs_special_course);
            $key = 0;

            if ($number_of_courses > 0) {
                while ($course = Database::fetch_array($rs_special_course)) {
                    $course_info = api_get_course_info($course['code']);
                    if ($course_info['visibility'] == COURSE_VISIBILITY_HIDDEN) {
                        continue;
                    }
                    $params = array();
                    // Get notifications.

                    $course_info['id_session'] = null;
                    $course_info['status'] = $course['status'];
                    $show_notification = Display::show_notification($course_info);

                    if (empty($course['user_id'])) {
                        $course['status'] = STUDENT;
                    }

                    $params['icon'] = Display::return_icon('blackboard.png', api_htmlentities($course_info['title']),
                        array(), ICON_SIZE_LARGE);

                    $params['right_actions'] = '';
                    if (api_is_platform_admin()) {
                        if ($load_dirs) {
                            $params['right_actions'] .= '<a id="document_preview_' . $course['real_id'] . '_0" class="document_preview" href="javascript:void(0);">' . Display::return_icon('folder.png',
                                    get_lang('Documents'), array('align' => 'absmiddle'), ICON_SIZE_SMALL) . '</a>';
                            $params['right_actions'] .= '<a href="' . api_get_path(WEB_CODE_PATH) . 'course_info/infocours.php?cidReq=' . $course['code'] . '">' . Display::return_icon('edit.png',
                                    get_lang('Edit'), array('align' => 'absmiddle'), ICON_SIZE_SMALL) . '</a>';
                            $params['right_actions'] .= Display::div('', array(
                                    'id' => 'document_result_' . $course['real_id'] . '_0',
                                    'class' => 'document_preview_container'
                                ));
                        } else {
                            $params['right_actions'] .= '<a href="' . api_get_path(WEB_CODE_PATH) . 'course_info/infocours.php?cidReq=' . $course['code'] . '">' . Display::return_icon('edit.png',
                                    get_lang('Edit'), array('align' => 'absmiddle'), ICON_SIZE_SMALL) . '</a>';
                        }
                        if ($course['status'] == COURSEMANAGER) {
                            //echo Display::return_icon('teachers.gif', get_lang('Status').': '.get_lang('Teacher'), array('style'=>'width: 11px; height: 11px;'));
                        }
                    } else {
                        if ($course_info['visibility'] != COURSE_VISIBILITY_CLOSED) {
                            if ($load_dirs) {
                                $params['right_actions'] .= '<a id="document_preview_' . $course['real_id'] . '_0" class="document_preview" href="javascript:void(0);">' . Display::return_icon('folder.png',
                                        get_lang('Documents'), array('align' => 'absmiddle'), ICON_SIZE_SMALL) . '</a>';
                                $params['right_actions'] .= Display::div('', array(
                                        'id' => 'document_result_' . $course['real_id'] . '_0',
                                        'class' => 'document_preview_container'
                                    ));
                            }
                        }
                    }

                    if ($course_info['visibility'] != COURSE_VISIBILITY_CLOSED || $course['status'] == COURSEMANAGER) {
                        $course_title = '<a href="' . api_get_path(WEB_COURSE_PATH) . $course_info['path'] . '/?id_session=0&amp;autoreg=1">' . $course_info['title'] . '</a>';
                    } else {
                        $course_title = $course_info['title'] . " " . Display::tag('span', get_lang('CourseClosed'),
                                array('class' => 'item_closed'));
                    }

                    if (api_get_setting('display_coursecode_in_courselist') == 'true') {
                        $course_title .= ' (' . $course_info['visual_code'] . ') ';
                    }
                    if (api_get_setting('display_teacher_in_courselist') == 'true') {
                        $params['teachers'] = CourseManager::get_teacher_list_from_course_code_to_string($course['code'],
                            self::USER_SEPARATOR, true);
                    }
                    $course_title .= '&nbsp;';
                    $course_title .= Display::return_icon('klipper.png', get_lang('CourseAutoRegister'));

                    $params['title'] = $course_title;
                    $params['link'] = api_get_path(WEB_COURSE_PATH) . $course_info['path'] . '/?id_session=0&amp;autoreg=1';

                    if ($course_info['visibility'] != COURSE_VISIBILITY_CLOSED) {
                        $params['notifications'] = $show_notification;
                    }

                    $html .= self::course_item_html($params, false);
                    $key++;
                }
            }
        }

        return $html;
    }

    /**
     * Display courses (without special courses) as several HTML divs
     * of course categories, as class userportal-catalog-item.
     * @uses display_courses_in_category() to display the courses themselves
     * @param int        user id
     * @param bool      Whether to show the document quick-loader or not
     * @return string
     */
    public static function display_courses($user_id, $load_dirs = false)
    {
        $user_id = intval($user_id);
        if (empty($user_id)) {
            $user_id = api_get_user_id();
        }

        // Step 1: We get all the categories of the user
        $tucc = Database::get_main_table(TABLE_USER_COURSE_CATEGORY);
        $sql = "SELECT id, title FROM $tucc WHERE user_id='" . $user_id . "' ORDER BY sort ASC";
        $result = Database::query($sql);
        $html = null;
        while ($row = Database::fetch_array($result)) {
            // We simply display the title of the category.
            $params = array(
                'icon' => Display::return_icon('folder_yellow.png', api_htmlentities($row['title']), array(),
                    ICON_SIZE_LARGE),
                'title' => $row['title'],
                'class' => 'table_user_course_category'
            );
            $html .= self::course_item_parent(
                self::course_item_html($params, true),
                self:: display_courses_in_category($row['id'], $load_dirs)
            );
        }

        // Step 2: We display the course without a user category.
        $html .= self:: display_courses_in_category(0, $load_dirs);

        return $html;
    }

    /**
     *  Display courses inside a category (without special courses) as HTML dics of
     *  class userportal-course-item.
     * @param int      User category id
     * @param bool      Whether to show the document quick-loader or not
     * @return string
     */
    public static function display_courses_in_category($user_category_id, $load_dirs = false)
    {
        $user_id = api_get_user_id();
        // Table definitions
        $TABLECOURS = Database:: get_main_table(TABLE_MAIN_COURSE);
        $TABLECOURSUSER = Database:: get_main_table(TABLE_MAIN_COURSE_USER);
        $TABLE_ACCESS_URL_REL_COURSE = Database:: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        $current_url_id = api_get_current_access_url_id();

        // Get course list auto-register
        $special_course_list = self::get_special_course_list();

        $without_special_courses = '';
        if (!empty($special_course_list)) {
            $without_special_courses = ' AND course.code NOT IN ("' . implode('","', $special_course_list) . '")';
        }

        //AND course_rel_user.relation_type<>".COURSE_RELATION_TYPE_RRHH."
        $sql = "SELECT
                course.id,
                course.title,
                course.code,
                course.subscribe subscr,
                course.unsubscribe unsubscr,
                course_rel_user.status status,
                course_rel_user.sort sort,
                course_rel_user.user_course_cat user_course_cat
                FROM $TABLECOURS      course,
                     $TABLECOURSUSER  course_rel_user,
                     $TABLE_ACCESS_URL_REL_COURSE url
                WHERE
                    course.code = course_rel_user.course_code AND
                    url.course_code = course.code AND
                    course_rel_user.user_id = '" . $user_id . "' AND
                    course_rel_user.user_course_cat='" . $user_category_id . "'
                    $without_special_courses ";

        // If multiple URL access mode is enabled, only fetch courses
        // corresponding to the current URL.
        if (api_get_multiple_access_url() && $current_url_id != -1) {
            $sql .= " AND url.course_code = course.code AND access_url_id='" . $current_url_id . "'";
        }
        // Use user's classification for courses (if any).
        $sql .= " ORDER BY course_rel_user.user_course_cat, course_rel_user.sort ASC";

        $result = Database::query($sql);
        $status_icon = '';
        $html = '';

        $course_list = array();
        $showCustomIcon = api_get_configuration_value('course_images_in_courses_list');

        // Browse through all courses.
        while ($course = Database::fetch_array($result)) {
            $course_info = api_get_course_info($course['code']);
            if ($course_info['visibility'] == COURSE_VISIBILITY_HIDDEN) {
                continue;
            }
            $course_info['id_session'] = null;
            $course_info['status'] = $course['status'];

            //In order to avoid doubles
            if (in_array($course_info['real_id'], $course_list)) {
                continue;
            } else {
                $course_list[] = $course_info['real_id'];
            }

            // For each course, get if there is any notification icon to show
            // (something that would have changed since the user's last visit).
            $show_notification = Display::show_notification($course_info);

            $status_icon = Display::return_icon(
                'blackboard.png',
                api_htmlentities($course_info['title']),
                array(),
                ICON_SIZE_LARGE
            );

            $iconName = basename($course_info['course_image']);
            if ($showCustomIcon == true && $iconName != 'course.png') {
                $status_icon = Display::img(
                    $course_info['course_image'],
                    api_htmlentities($course_info['title']),
                    array()
                );
            }

            $params = array();
            $params['right_actions'] = '';

            if (api_is_platform_admin()) {
                if ($load_dirs) {
                    $params['right_actions'] .= '<a id="document_preview_' . $course_info['real_id'] . '_0" class="document_preview" href="javascript:void(0);">' . Display::return_icon('folder.png',
                            get_lang('Documents'), array('align' => 'absmiddle'), ICON_SIZE_SMALL) . '</a>';
                    $params['right_actions'] .= '<a href="' . api_get_path(WEB_CODE_PATH) . 'course_info/infocours.php?cidReq=' . $course['code'] . '">' . Display::return_icon('edit.png',
                            get_lang('Edit'), array('align' => 'absmiddle'), ICON_SIZE_SMALL) . '</a>';
                    $params['right_actions'] .= Display::div('', array(
                            'id' => 'document_result_' . $course_info['real_id'] . '_0',
                            'class' => 'document_preview_container'
                        ));
                } else {
                    $params['right_actions'] .= '<a href="' . api_get_path(WEB_CODE_PATH) . 'course_info/infocours.php?cidReq=' . $course['code'] . '">' . Display::return_icon('edit.png',
                            get_lang('Edit'), array('align' => 'absmiddle'), ICON_SIZE_SMALL) . '</a>';
                }

                if ($course_info['status'] == COURSEMANAGER) {
                    //echo Display::return_icon('teachers.gif', get_lang('Status').': '.get_lang('Teacher'), array('style'=>'width: 11px; height: 11px;'));
                }
            } else {
                if ($course_info['visibility'] != COURSE_VISIBILITY_CLOSED) {
                    if ($load_dirs) {
                        $params['right_actions'] .= '<a id="document_preview_' . $course_info['real_id'] . '_0" class="document_preview" href="javascript:void(0);">' . Display::return_icon('folder.png',
                                get_lang('Documents'), array('align' => 'absmiddle'), ICON_SIZE_SMALL) . '</a>';
                        $params['right_actions'] .= Display::div('', array(
                                'id' => 'document_result_' . $course_info['real_id'] . '_0',
                                'class' => 'document_preview_container'
                            ));
                    } else {
                        if ($course_info['status'] == COURSEMANAGER) {
                            $params['right_actions'] .= '<a href="' . api_get_path(WEB_CODE_PATH) . 'course_info/infocours.php?cidReq=' . $course['code'] . '">' . Display::return_icon('edit.png',
                                    get_lang('Edit'), array('align' => 'absmiddle'), ICON_SIZE_SMALL) . '</a>';
                        }
                    }
                }
            }

            $course_title = $course_info['title'];

            $course_title_url = '';
            if ($course_info['visibility'] != COURSE_VISIBILITY_CLOSED || $course['status'] == COURSEMANAGER) {
                $course_title_url = api_get_path(WEB_COURSE_PATH) . $course_info['path'] . '/index.php?id_session=0';
                $course_title = Display::url($course_info['title'], $course_title_url);
            } else {
                $course_title = $course_info['title'] . " " . Display::tag('span', get_lang('CourseClosed'),
                        array('class' => 'item_closed'));
            }

            // Start displaying the course block itself
            if (api_get_setting('display_coursecode_in_courselist') == 'true') {
                $course_title .= ' (' . $course_info['visual_code'] . ') ';
            }

            if (api_get_setting('display_teacher_in_courselist') == 'true') {
                $teachers = CourseManager::get_teacher_list_from_course_code_to_string(
                    $course['code'],
                    self::USER_SEPARATOR,
                    true
                );
            }

            $params['link'] = $course_title_url;
            $params['icon'] = $status_icon;
            $params['title'] = $course_title;
            $params['teachers'] = $teachers;

            if ($course_info['visibility'] != COURSE_VISIBILITY_CLOSED) {
                $params['notifications'] = $show_notification;
            }

            $isSubcontent = true;
            if (empty($user_category_id)) {
                $isSubcontent = false;
            }
            $html .= self::course_item_html($params, $isSubcontent);
        }

        return $html;
    }

    /**
     * Retrieves the user defined course categories
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     * @return array containing all the titles of the user defined courses with the id as key of the array
     */
    public static function get_user_course_categories()
    {
        global $_user;
        $output = array();
        $table_category = Database::get_main_table(TABLE_USER_COURSE_CATEGORY);
        $sql = "SELECT * FROM " . $table_category . " WHERE user_id='" . intval($_user['user_id']) . "'";
        $result = Database::query($sql);
        while ($row = Database::fetch_array($result)) {
            $output[$row['id']] = $row['title'];
        }
        return $output;
    }

    /**
     * Get the course id based on the original id and field name in the extra fields.
     * Returns 0 if course was not found
     *
     * @param string $original_course_id_value Original course code
     * @param string $original_course_id_name Original field name
     * @return int Course id
     */
    public static function get_course_id_from_original_id($original_course_id_value, $original_course_id_name)
    {
        $t_cfv = Database::get_main_table(TABLE_MAIN_COURSE_FIELD_VALUES);
        $table_field = Database::get_main_table(TABLE_MAIN_COURSE_FIELD);
        $original_course_id_name = Database::escape_string($original_course_id_name);
        $original_course_id_name = Database::escape_string($original_course_id_name);
        $sql = "SELECT course_code FROM $table_field cf
                INNER JOIN $t_cfv cfv ON cfv.field_id=cf.id
                WHERE
                    field_variable='$original_course_id_name' AND
                    field_value='$original_course_id_value'";
        $res = Database::query($sql);
        $row = Database::fetch_object($res);
        if ($row != false) {
            return $row->course_code;
        } else {
            return 0;
        }
    }

    /**
     * Display code for one specific course a logged in user is subscribed to.
     * Shows a link to the course, what's new icons...
     *
     * $my_course['d'] - course directory
     * $my_course['i'] - course title
     * $my_course['c'] - visual course code
     * $my_course['k']  - system course code
     *
     * @param   array       Course details
     * @param   integer     Session ID
     * @param   string      CSS class to apply to course entry
     * @param   boolean     Whether the session is supposedly accessible now (not in the case it has passed and is in invisible/unaccessible mode)
     * @param bool      Whether to show the document quick-loader or not
     * @return  string      The HTML to be printed for the course entry
     *
     * @version 1.0.3
     * @todo refactor into different functions for database calls | logic | display
     * @todo replace single-character $my_course['d'] indices
     * @todo move code for what's new icons to a separate function to clear things up
     * @todo add a parameter user_id so that it is possible to show the courselist of other users (=generalisation). This will prevent having to write a new function for this.
     */
    public static function get_logged_user_course_html(
        $course,
        $session_id = 0,
        $class = 'courses',
        $session_accessible = true,
        $load_dirs = false
    ) {
        global $nosession;
        $user_id = api_get_user_id();
        $course_info = api_get_course_info($course['code']);
        $status_course = CourseManager::get_user_in_course_status($user_id, $course_info['code']);
        $course_info['status'] = empty($session_id) ? $status_course : STUDENT;
        $course_info['id_session'] = $session_id;

        if (!$nosession) {
            global $now, $date_start, $date_end;
        }
        if (empty($date_start) or empty($date_end)) {
            $sess = SessionManager::get_sessions_list(
                array('s.id' => array('operator' => '=', 'value' => $course_info['id_session']))
            );
            $date_start = $sess[$course_info['id_session']]['date_start'];
            $date_end = $sess[$course_info['id_session']]['date_end'];
        }
        if (empty($now)) {
            // maybe use api_get_utcdate() here?
            $now = date('Y-m-d h:i:s');
        }

        // Table definitions
        $main_user_table = Database:: get_main_table(TABLE_MAIN_USER);
        $tbl_session = Database:: get_main_table(TABLE_MAIN_SESSION);
        $tbl_session_category = Database:: get_main_table(TABLE_MAIN_SESSION_CATEGORY);

        $course_access_settings = CourseManager::get_access_settings($course_info['code']);
        $course_visibility = $course_access_settings['visibility'];

        if ($course_visibility == COURSE_VISIBILITY_HIDDEN) {
            return '';
        }

        $user_in_course_status = CourseManager::get_user_in_course_status(
            api_get_user_id(),
            $course_info['code']
        );

        $is_coach = api_is_coach($course_info['id_session'], $course['code']);

        // Display course entry.
        // Show a hyperlink to the course, unless the course is closed and user is not course admin.
        $session_url = '';
        $session_title = '';

        if ($session_accessible) {
            if ($course_visibility != COURSE_VISIBILITY_CLOSED ||
                $user_in_course_status == COURSEMANAGER
            ) {
                if (!$nosession) {
                    if (empty($course_info['id_session'])) {
                        $course_info['id_session'] = 0;
                    }

                    $sessionCourseAvailable = false;
                    $sessionCourseStatus = api_get_session_visibility($session_id, $course_info['code']);
                    if (in_array($sessionCourseStatus,
                        array(SESSION_VISIBLE_READ_ONLY, SESSION_VISIBLE, SESSION_AVAILABLE))) {
                        $sessionCourseAvailable = true;
                    }

                    if ($user_in_course_status == COURSEMANAGER || $sessionCourseAvailable) {
                        $session_url = api_get_path(WEB_COURSE_PATH) . $course_info['path'] . '/?id_session=' . $course_info['id_session'];
                        $session_title = '<a href="' . api_get_path(WEB_COURSE_PATH) . $course_info['path'] . '/?id_session=' . $course_info['id_session'] . '">' . $course_info['name'] . '</a>';
                    } else {
                        $session_title = $course_info['name'];
                    }
                } else {
                    $session_url = api_get_path(WEB_COURSE_PATH) . $course_info['path'] . '/';
                    $session_title = '<a href="' . api_get_path(WEB_COURSE_PATH) . $course_info['path'] . '/">' . $course_info['name'] . '</a>';
                }
            } else {
                $session_title = $course_info['name'] . ' ' . Display::tag('span', get_lang('CourseClosed'),
                        array('class' => 'item_closed'));
            }
        } else {
            $session_title = $course_info['name'];
        }

        $params = array();
        $params['icon'] = Display::return_icon(
            'blackboard_blue.png',
            api_htmlentities($course_info['name']),
            array(),
            ICON_SIZE_LARGE
        );

        $showCustomIcon = api_get_configuration_value('course_images_in_courses_list');
        $iconName = basename($course_info['course_image']);
        if ($showCustomIcon && $iconName != 'course.png') {
            $params['icon'] = Display::img(
                $course_info['course_image'],
                api_htmlentities($course_info['name']),
                array()
            );
        }

        $params['link'] = $session_url;
        $params['title'] = $session_title;
        $params['right_actions'] = '';

        if ($course_visibility != COURSE_VISIBILITY_CLOSED &&
            $course_visibility != COURSE_VISIBILITY_HIDDEN
        ) {
            if ($load_dirs) {
                $params['right_actions'] .= '<a id="document_preview_' . $course_info['real_id'] . '_' . $course_info['id_session'] . '" class="document_preview" href="javascript:void(0);">' . Display::return_icon('folder.png',
                        get_lang('Documents'), array('align' => 'absmiddle'), ICON_SIZE_SMALL) . '</a>';
                $params['right_actions'] .= Display::div('', array(
                        'id' => 'document_result_' . $course_info['real_id'] . '_' . $course_info['id_session'],
                        'class' => 'document_preview_container'
                    ));
            }
        }

        if (api_get_setting('display_coursecode_in_courselist') == 'true') {
            $session_title .= ' (' . $course_info['visual_code'] . ') ';
        }

        if (api_get_setting('display_teacher_in_courselist') == 'true') {
            $teacher_list = null;
            if (!$nosession) {
                $teacher_list = CourseManager::get_teacher_list_from_course_code_to_string(
                    $course_info['code'],
                    self::USER_SEPARATOR,
                    true
                );
                $course_coachs = CourseManager::get_coachs_from_course_to_string(
                    $course_info['id_session'],
                    $course['code'],
                    self::USER_SEPARATOR,
                    true
                );

                if ($course_info['status'] == COURSEMANAGER || ($course_info['status'] == STUDENT && empty($course_info['id_session'])) || empty($course_info['status'])) {
                    $params['teachers'] = $teacher_list;
                }
                if (($course_info['status'] == STUDENT && !empty($course_info['id_session'])) || ($is_coach && $course_info['status'] != COURSEMANAGER)) {
                    $params['coaches'] = $course_coachs;
                }
            } else {
                $params['teachers'] = $teacher_list;
            }
        }

        $session_title .= isset($course['special_course']) ? ' ' . Display::return_icon('klipper.png',
                get_lang('CourseAutoRegister')) : '';

        // Display the "what's new" icons
        if ($course_visibility != COURSE_VISIBILITY_CLOSED && $course_visibility != COURSE_VISIBILITY_HIDDEN) {
            $session_title .= Display:: show_notification($course_info);
        }

        $params['title'] = $session_title;
        $params['extra'] = '';

        $html = self::course_item_html($params, true);

        $session_category_id = null;
        if (!$nosession) {
            $session = '';
            $active = false;
            if (!empty($course_info['session_name'])) {

                // Request for the name of the general coach
                $sql = 'SELECT lastname, firstname,sc.name
                FROM ' . $tbl_session . ' ts
                LEFT JOIN ' . $main_user_table . ' tu
                ON ts.id_coach = tu.user_id
                INNER JOIN ' . $tbl_session_category . ' sc ON ts.session_category_id = sc.id
                WHERE ts.id=' . (int)$course_info['id_session'] . ' LIMIT 1';

                $rs = Database::query($sql);
                $sessioncoach = Database::store_result($rs);
                $sessioncoach = $sessioncoach[0];

                $session = array();
                $session['title'] = $course_info['session_name'];
                $session_category_id = CourseManager::get_session_category_id_by_session_id($course_info['id_session']);
                $session['category'] = $sessioncoach['name'];
                if ($course_info['date_start'] == '0000-00-00') {
                    //$session['dates'] = get_lang('WithoutTimeLimits');
                    $session['dates'] = '';
                    if (api_get_setting('show_session_coach') === 'true') {
                        $session['coach'] = get_lang('GeneralCoach') . ': ' . api_get_person_name($sessioncoach['firstname'],
                                $sessioncoach['lastname']);
                    }
                    $active = true;
                } else {
                    $session ['dates'] = ' - ' . get_lang('From') . ' ' . $course_info['date_start'] . ' ' . get_lang('To') . ' ' . $course_info['date_end'];
                    if (api_get_setting('show_session_coach') === 'true') {
                        $session['coach'] = get_lang('GeneralCoach') . ': ' . api_get_person_name($sessioncoach['firstname'],
                                $sessioncoach['lastname']);
                    }
                    $active = ($date_start <= $now && $date_end >= $now);
                }
            }
            $user_course_category = '';
            if (isset($course_info['user_course_cat'])) {
                $user_course_category = $course_info['user_course_cat'];
            }
            $output = array(
                $user_course_category,
                $html,
                $course_info['id_session'],
                $session,
                'active' => $active,
                'session_category_id' => $session_category_id
            );
        } else {
            $output = array($course_info['user_course_cat'], $html);
        }
        return $output;
    }

    /**
     *
     * @param    string    source course code
     * @param     int        source session id
     * @param    string    destination course code
     * @param     int        destination session id
     * @return  bool
     */
    public static function copy_course(
        $source_course_code,
        $source_session_id,
        $destination_course_code,
        $destination_session_id,
        $params = array()
    ) {
        require_once api_get_path(SYS_CODE_PATH) . 'coursecopy/classes/CourseBuilder.class.php';
        require_once api_get_path(SYS_CODE_PATH) . 'coursecopy/classes/CourseRestorer.class.php';
        require_once api_get_path(SYS_CODE_PATH) . 'coursecopy/classes/CourseSelectForm.class.php';

        $course_info = api_get_course_info($source_course_code);

        if (!empty($course_info)) {
            $cb = new CourseBuilder('', $course_info);
            $course = $cb->build($source_session_id, $source_course_code, true);
            $course_restorer = new CourseRestorer($course);
            $course_restorer->skip_content = $params;
            $course_restorer->restore($destination_course_code, $destination_session_id, true, true);
            return true;
        }
        return false;
    }

    /**
     * A simpler version of the copy_course, the function creates an empty course with an autogenerated course code
     *
     * @param    string    new course title
     * @param    string    source course code
     * @param     int        source session id
     * @param     int        destination session id
     * @param    bool    new copied tools (Exercises and LPs)will be set to invisible by default?
     *
     * @return     array
     */
    public static function copy_course_simple(
        $new_title,
        $source_course_code,
        $source_session_id = 0,
        $destination_session_id = 0,
        $params = array()
    ) {
        $source_course_info = api_get_course_info($source_course_code);
        if (!empty($source_course_info)) {
            $new_course_code = self::generate_nice_next_course_code($source_course_code);
            if ($new_course_code) {
                $new_course_info = self::create_course($new_title, $new_course_code, false);
                if (!empty($new_course_info['code'])) {
                    $result = self::copy_course($source_course_code, $source_session_id, $new_course_info['code'],
                        $destination_session_id, $params);
                    if ($result) {
                        return $new_course_info;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Creates a new course code based in a given code
     *
     * @param string    wanted code
     * <code>    $wanted_code = 'curse' if there are in the DB codes like curse1 curse2 the function will return: course3</code>
     * if the course code doest not exist in the DB the same course code will be returned
     * @return string    wanted unused code
     */
    public static function generate_nice_next_course_code($wanted_code)
    {
        $course_code_ok = !self::course_code_exists($wanted_code);
        if (!$course_code_ok) {
            $wanted_code = CourseManager::generate_course_code($wanted_code);
            $table = Database::get_main_table(TABLE_MAIN_COURSE);
            $wanted_code = Database::escape_string($wanted_code);
            $sql = "SELECT count(*) as count FROM $table WHERE code LIKE '$wanted_code%'";
            $result = Database::query($sql);
            if (Database::num_rows($result) > 0) {
                $row = Database::fetch_array($result);
                $count = $row['count'] + 1;
                $wanted_code = $wanted_code . '_' . $count;
                $result = api_get_course_info($wanted_code);
                if (empty($result)) {
                    return $wanted_code;
                }
            }
            return false;
        }

        return $wanted_code;
    }

    /**
     * Gets the status of the users agreement in a course course-session
     *
     * @param int $user_id
     * @param string $course_code
     * @param int $session_id
     * @return boolean
     */
    public static function is_user_accepted_legal($user_id, $course_code, $session_id = null)
    {
        $user_id = intval($user_id);
        $course_code = Database::escape_string($course_code);
        $session_id = intval($session_id);

        // Course legal
        $enabled = api_get_plugin_setting('courselegal', 'tool_enable');

        if ($enabled == 'true') {
            require_once api_get_path(SYS_PLUGIN_PATH) . 'courselegal/config.php';
            $plugin = CourseLegalPlugin::create();
            return $plugin->isUserAcceptedLegal($user_id, $course_code, $session_id);
        }

        if (empty($session_id)) {
            $table = Database::get_main_table(TABLE_MAIN_COURSE_USER);
            $sql = "SELECT legal_agreement FROM $table
                    WHERE user_id = $user_id AND course_code ='$course_code' ";
            $result = Database::query($sql);
            if (Database::num_rows($result) > 0) {
                $result = Database::fetch_array($result);
                if ($result['legal_agreement'] == 1) {
                    return true;
                }
            }
            return false;
        } else {
            $table = Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER);
            $sql = "SELECT legal_agreement FROM $table
                    WHERE id_user = $user_id AND course_code ='$course_code' AND id_session = $session_id";
            $result = Database::query($sql);
            if (Database::num_rows($result) > 0) {
                $result = Database::fetch_array($result);
                if ($result['legal_agreement'] == 1) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * Saves the user-course legal agreement
     * @param   int user id
     * @param   string course code
     * @param   int session id
     */
    public static function save_user_legal($user_id, $course_code, $session_id = null)
    {
        // Course plugin legal
        $enabled = api_get_plugin_setting('courselegal', 'tool_enable');

        if ($enabled == 'true') {
            require_once api_get_path(SYS_PLUGIN_PATH) . 'courselegal/config.php';
            $plugin = CourseLegalPlugin::create();
            return $plugin->saveUserLegal($user_id, $course_code, $session_id);
        }

        $user_id = intval($user_id);
        $course_code = Database::escape_string($course_code);
        $session_id = intval($session_id);

        if (empty($session_id)) {
            $table = Database::get_main_table(TABLE_MAIN_COURSE_USER);
            $sql = "UPDATE $table SET legal_agreement = '1'
                    WHERE user_id =  $user_id AND course_code  ='$course_code' ";
            Database::query($sql);
        } else {
            $table = Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER);
            $sql = "UPDATE  $table SET legal_agreement = '1'
                    WHERE id_user =  $user_id AND course_code  = '$course_code' AND id_session = $session_id";
            Database::query($sql);
        }
    }

    /**
     * @param int $user_id
     * @param int $course_id
     * @param int $session_id
     * @param int $url_id
     * @return bool
     */
    public static function get_user_course_vote($user_id, $course_id, $session_id = null, $url_id = null)
    {
        $table_user_course_vote = Database::get_main_table(TABLE_MAIN_USER_REL_COURSE_VOTE);

        $session_id = !isset($session_id) ? api_get_session_id() : intval($session_id);
        $url_id = empty($url_id) ? api_get_current_access_url_id() : intval($url_id);
        $user_id = intval($user_id);

        if (empty($user_id)) {
            return false;
        }

        $params = array(
            'user_id' => $user_id,
            'c_id' => $course_id,
            'session_id' => $session_id,
            'url_id' => $url_id
        );

        $result = Database::select(
            'vote',
            $table_user_course_vote,
            array(
                'where' => array(
                    'user_id = ? AND c_id = ? AND session_id = ? AND url_id = ?' => $params
                )
            ),
            'first'
        );
        if (!empty($result)) {
            return $result['vote'];
        }
        return false;
    }

    /**
     * @param int $course_id
     * @param int $session_id
     * @param int $url_id
     * @return array
     */
    public static function get_course_ranking($course_id, $session_id = null, $url_id = null)
    {
        $table_course_ranking = Database::get_main_table(TABLE_STATISTIC_TRACK_COURSE_RANKING);

        $session_id = !isset($session_id) ? api_get_session_id() : intval($session_id);
        $url_id = empty($url_id) ? api_get_current_access_url_id() : intval($url_id);
        $now = api_get_utc_datetime();

        $params = array(
            'c_id' => $course_id,
            'session_id' => $session_id,
            'url_id' => $url_id,
            'creation_date' => $now,
        );

        $result = Database::select(
            'c_id, accesses, total_score, users',
            $table_course_ranking,
            array('where' => array('c_id = ? AND session_id = ? AND url_id = ?' => $params)),
            'first'
        );

        $point_average_in_percentage = 0;
        $point_average_in_star = 0;
        $users_who_voted = 0;

        if (!empty($result['users'])) {
            $users_who_voted = $result['users'];
            $point_average_in_percentage = round($result['total_score'] / $result['users'] * 100 / 5, 2);
            $point_average_in_star = round($result['total_score'] / $result['users'], 1);
        }

        $result['user_vote'] = false;

        if (!api_is_anonymous()) {
            $result['user_vote'] = self::get_user_course_vote(api_get_user_id(), $course_id, $session_id, $url_id);
        }

        $result['point_average'] = $point_average_in_percentage;
        $result['point_average_star'] = $point_average_in_star;
        $result['users_who_voted'] = $users_who_voted;

        return $result;
    }

    /**
     *
     * Updates the course ranking
     * @param int   course id
     * @param int   session id
     * @param id    url id
     *
     **/
    public static function update_course_ranking(
        $course_id = null,
        $session_id = null,
        $url_id = null,
        $points_to_add = null,
        $add_access = true,
        $add_user = true
    ) {
        // Course catalog stats modifications see #4191
        $table_course_ranking = Database::get_main_table(TABLE_STATISTIC_TRACK_COURSE_RANKING);

        $now = api_get_utc_datetime();

        $course_id = empty($course_id) ? api_get_course_int_id() : intval($course_id);
        $session_id = !isset($session_id) ? api_get_session_id() : intval($session_id);
        $url_id = empty($url_id) ? api_get_current_access_url_id() : intval($url_id);

        $params = array(
            'c_id' => $course_id,
            'session_id' => $session_id,
            'url_id' => $url_id,
            'creation_date' => $now,
        );

        $result = Database::select(
            'id, accesses, total_score, users',
            $table_course_ranking,
            array('where' => array('c_id = ? AND session_id = ? AND url_id = ?' => $params)),
            'first'
        );

        // Problem here every time we load the courses/XXXX/index.php course home page we update the access

        if (empty($result)) {
            if ($add_access) {
                $params['accesses'] = 1;
            }
            //The votes and users are empty
            if (isset($points_to_add) && !empty($points_to_add)) {
                $params['total_score'] = intval($points_to_add);
            }
            if ($add_user) {
                $params['users'] = 1;
            }
            $result = Database::insert($table_course_ranking, $params);
        } else {
            $my_params = array();

            if ($add_access) {
                $my_params['accesses'] = intval($result['accesses']) + 1;
            }
            if (isset($points_to_add) && !empty($points_to_add)) {
                $my_params['total_score'] = $result['total_score'] + $points_to_add;
            }
            if ($add_user) {
                $my_params['users'] = $result['users'] + 1;
            }

            if (!empty($my_params)) {
                $result = Database::update(
                    $table_course_ranking,
                    $my_params,
                    array('c_id = ? AND session_id = ? AND url_id = ?' => $params)
                );
            }
        }

        return $result;
    }

    /**
     * Add user vote to a course
     *
     * @param   int user id
     * @param   int vote [1..5]
     * @param   int course id
     * @param   int session id
     * @param   int url id (access_url_id)
     * @return    mixed 'added', 'updated' or 'nothing'
     */
    public static function add_course_vote($user_id, $vote, $course_id, $session_id = null, $url_id = null)
    {
        $table_user_course_vote = Database::get_main_table(TABLE_MAIN_USER_REL_COURSE_VOTE);
        $course_id = empty($course_id) ? api_get_course_int_id() : intval($course_id);

        if (empty($course_id) || empty($user_id)) {
            return false;
        }

        if (!in_array($vote, array(1, 2, 3, 4, 5))) {
            return false;
        }

        $session_id = !isset($session_id) ? api_get_session_id() : intval($session_id);
        $url_id = empty($url_id) ? api_get_current_access_url_id() : intval($url_id);
        $vote = intval($vote);

        $params = array(
            'user_id' => intval($user_id),
            'c_id' => $course_id,
            'session_id' => $session_id,
            'url_id' => $url_id,
            'vote' => $vote
        );

        $action_done = 'nothing';

        $result = Database::select(
            'id, vote',
            $table_user_course_vote,
            array('where' => array('user_id = ? AND c_id = ? AND session_id = ? AND url_id = ?' => $params)),
            'first'
        );

        if (empty($result)) {
            Database::insert($table_user_course_vote, $params);
            $points_to_add = $vote;
            $add_user = true;
            $action_done = 'added';
        } else {
            $my_params = array('vote' => $vote);
            $points_to_add = $vote - $result['vote'];
            $add_user = false;

            Database::update(
                $table_user_course_vote,
                $my_params,
                array('user_id = ? AND c_id = ? AND session_id = ? AND url_id = ?' => $params)
            );
            $action_done = 'updated';
        }

        // Current points
        if (!empty($points_to_add)) {
            self::update_course_ranking(
                $course_id,
                $session_id,
                $url_id,
                $points_to_add,
                false,
                $add_user
            );
        }
        return $action_done;
    }

    /**
     * Remove course ranking + user votes
     *
     * @param int $course_id
     * @param int $session_id
     * @param int $url_id
     *
     */
    public static function remove_course_ranking($course_id, $session_id, $url_id = null)
    {
        $table_course_ranking = Database::get_main_table(TABLE_STATISTIC_TRACK_COURSE_RANKING);
        $table_user_course_vote = Database::get_main_table(TABLE_MAIN_USER_REL_COURSE_VOTE);

        if (!empty($course_id) && isset($session_id)) {
            $url_id = empty($url_id) ? api_get_current_access_url_id() : intval($url_id);
            $params = array(
                'c_id' => $course_id,
                'session_id' => $session_id,
                'url_id' => $url_id,
            );
            Database::delete($table_course_ranking, array('c_id = ? AND session_id = ? AND url_id = ?' => $params));
            Database::delete($table_user_course_vote, array('c_id = ? AND session_id = ? AND url_id = ?' => $params));
        }
    }

    /**
     * Returns an array with the hottest courses
     * @param   int $days number of days
     * @param   int $limit number of hottest courses
     */
    public static function return_hot_courses($days = 30, $limit = 5)
    {
        global $_configuration;

        if (api_is_invitee()) {
            return array();
        }

        $limit = intval($limit);

        // Getting my courses
        $my_course_list = CourseManager::get_courses_list_by_user_id(api_get_user_id());

        $my_course_code_list = array();
        foreach ($my_course_list as $course) {
            $my_course_code_list[$course['real_id']] = $course['real_id'];
        }

        if (api_is_drh()) {
            $courses = CourseManager::get_courses_followed_by_drh(api_get_user_id());
            foreach ($courses as $course) {
                $my_course_code_list[$course['real_id']] = $course['real_id'];
            }
        }

        $table_course_access = Database::get_main_table(TABLE_STATISTIC_TRACK_E_COURSE_ACCESS);
        $table_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $table_course_url = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);

        //@todo all dates in the tracking_course_access, last_access are in the DB time (NOW) not UTC
        /*
        $today                    = api_get_utc_datetime();
        $today_diff                = time() -intval($days)*24*60*60;
        $today_diff                = api_get_utc_datetime($today_diff);
         * */

        //WHERE login_course_date <= '$today' AND login_course_date >= '$today_diff'

        //$table_course_access table uses the now() and interval ...
        $now = api_get_utc_datetime(time());
        $sql = "SELECT COUNT(course_access_id) course_count, a.c_id, visibility
                FROM $table_course c INNER JOIN $table_course_access a
                ON (c.id = a.c_id) INNER JOIN $table_course_url u ON u.course_code = c.code
                WHERE
                    u.access_url_id = " . api_get_current_access_url_id() . " AND
                    login_course_date <= '$now' AND
                    login_course_date > DATE_SUB(now(), INTERVAL $days DAY) AND
                    visibility <> '" . COURSE_VISIBILITY_CLOSED . "' AND visibility <> '" . COURSE_VISIBILITY_HIDDEN . "'
                GROUP BY a.c_id
                ORDER BY course_count DESC
                LIMIT $limit
            ";

        $result = Database::query($sql);
        $courses = array();
        if (Database::num_rows($result)) {
            $courses = Database::store_result($result, 'ASSOC');
            $courses = self::process_hot_course_item($courses, $my_course_code_list);
        }
        return $courses;
    }

    /**
     * @param array $courses
     * @param array $my_course_code_list
     * @return mixed
     */
    public static function process_hot_course_item($courses, $my_course_code_list = array())
    {
        $ajax_url = api_get_path(WEB_AJAX_PATH) . 'course.ajax.php?a=add_course_vote';

        $stok = Security::get_existing_token();

        foreach ($courses as $courseId) {
            $course_info = api_get_course_info_by_id($courseId);
            $courseCode = $course_info['code'];
            $categoryCode = !empty($course_info['categoryCode']) ? $course_info['categoryCode'] : "";
            $my_course['extra_info'] = $course_info;
            $my_course['extra_info']['go_to_course_button'] = '';
            $my_course['extra_info']['register_button'] = '';

            $access_link = self::get_access_link_by_user(
                api_get_user_id(),
                $course_info,
                $my_course_code_list
            );

            //Course visibility
            if ($access_link && in_array('register', $access_link)) {
                $my_course['extra_info']['register_button'] = Display::url(get_lang('Subscribe'),
                    api_get_path(WEB_COURSE_PATH) . $course_info['path'] . '/index.php?action=subscribe&amp;sec_token=' . $stok,
                    array('class' => 'btn btn-primary'));
            }

            if ($access_link && in_array('enter',
                    $access_link) || $course_info['visibility'] == COURSE_VISIBILITY_OPEN_WORLD
            ) {
                $my_course['extra_info']['go_to_course_button'] = Display::url(get_lang('GoToCourse'),
                    api_get_path(WEB_COURSE_PATH) . $course_info['path'] . '/index.php',
                    array('class' => 'btn btn-primary'));
            }

            if ($access_link && in_array('unsubscribe', $access_link)) {
                $my_course['extra_info']['unsubscribe_button'] = Display::url(get_lang('Unsubscribe'),
                    api_get_path(WEB_CODE_PATH) . 'auth/courses.php?action=unsubscribe&amp;unsubscribe=' . $courseCode . '&amp;sec_token=' . $stok . '&amp;category_code=' . $categoryCode,
                    array('class' => 'btn btn-primary'));
            }

            //Description
            $my_course['extra_info']['description_button'] = '';
            if ($course_info['visibility'] == COURSE_VISIBILITY_OPEN_WORLD || in_array($course_info['real_id'],
                    $my_course_code_list)
            ) {
                $my_course['extra_info']['description_button'] = Display::url(get_lang('Description'),
                    api_get_path(WEB_AJAX_PATH) . 'course_home.ajax.php?a=show_course_information&amp;code=' . $course_info['code'],
                    array('class' => 'ajax btn'));
            }

            $my_course['extra_info']['teachers'] = CourseManager::get_teacher_list_from_course_code_to_string($course_info['code']);
            $point_info = self::get_course_ranking($course_info['real_id'], 0);
            $my_course['extra_info']['rating_html'] = Display::return_rating_system('star_' . $course_info['real_id'],
                $ajax_url . '&amp;course_id=' . $course_info['real_id'], $point_info);
        }
        return $courses;
    }

    /**
     * @param int $limit
     * @return array
     */
    public static function return_most_accessed_courses($limit = 5)
    {
        $table_course_ranking = Database::get_main_table(TABLE_STATISTIC_TRACK_COURSE_RANKING);
        $params['url_id'] = api_get_current_access_url_id();

        $result = Database::select(
            'c_id, accesses, total_score, users',
            $table_course_ranking,
            array('where' => array('url_id = ?' => $params), 'order' => 'accesses DESC', 'limit' => $limit),
            'all',
            true
        );
        return $result;
    }

    /**
     * @deprecated seems not to be used
     */
    static function list_inactive_courses($ceiling, $visibility_level = COURSE_VISIBILITY_REGISTERED)
    {
        $ceiling = is_numeric($ceiling) ? (int)$ceiling : strtotime($ceiling);
        $ceiling = date('Y-m-d H:i:s', $ceiling);
        $visibility_level = $visibility_level ? $visibility_level : '0';

        $table_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $table_category = Database::get_main_table(TABLE_MAIN_CATEGORY);
        $sql = "SELECT
                    c.*,
                    cat.name AS category
                FROM
                    $table_course AS c
                LEFT JOIN
                    $table_category AS cat
                ON
                    c.category_code = cat.code
                WHERE
                    c.visibility >= $visibility_level AND
                    c.last_visit<='$ceiling'
        ";

        return ResultSet::create($sql);
    }

    /**
     * Get courses count
     * @param int Access URL ID (optional)
     * @param int $visibility
     *
     * @return int Number of courses
     */
    public static function count_courses($access_url_id = null, $visibility = null)
    {
        $table_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $table_course_rel_access_url = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        $sql = "SELECT count(id) FROM $table_course c";
        if (!empty($access_url_id) && $access_url_id == intval($access_url_id)) {
            $sql .= ", $table_course_rel_access_url u
                    WHERE c.code = u.course_code AND u.access_url_id = $access_url_id";
            if (!empty($visibility)) {
                $visibility = intval($visibility);
                $sql .= " AND visibility = $visibility ";
            }
        } else {
            if (!empty($visibility)) {
                $visibility = intval($visibility);
                $sql .= " WHERE visibility = $visibility ";
            }
        }

        $res = Database::query($sql);
        $row = Database::fetch_row($res);
        return $row[0];
    }

    /**
     * Get active courses count.
     * Active = all courses except the ones with hidden visibility.
     *
     * @param int $urlId Access URL ID (optional)
     * @return int Number of courses
     */
    public static function countActiveCourses($urlId = null)
    {
        $table_course = Database::get_main_table(TABLE_MAIN_COURSE);
        $table_course_rel_access_url = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        $sql = "SELECT count(id) FROM $table_course c";
        if (!empty($urlId) && $urlId == intval($urlId)) {
            $sql .= ", $table_course_rel_access_url u
                    WHERE
                        c.code = u.course_code AND
                        u.access_url_id = $urlId AND
                        visibility <> " . COURSE_VISIBILITY_HIDDEN;
        } else {
            $sql .= " WHERE visibility <> " . COURSE_VISIBILITY_HIDDEN;
        }
        $res = Database::query($sql);
        $row = Database::fetch_row($res);
        return $row[0];
    }

    /**
     * Get available le courses count
     * @param int Access URL ID (optional)
     * @return int Number of courses
     */
    public static function countAvailableCourses($accessUrlId = null)
    {
        global $_configuration;
        $tableCourse = Database::get_main_table(TABLE_MAIN_COURSE);
        $tableCourseRelAccessUrl = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        $specialCourseList = self::get_special_course_list();

        $withoutSpecialCourses = '';
        if (!empty($specialCourseList)) {
            $withoutSpecialCourses = ' AND c.code NOT IN ("' . implode('","', $specialCourseList) . '")';
        }

        $visibilityCondition = null;

        if (isset($_configuration['course_catalog_hide_private'])) {
            if ($_configuration['course_catalog_hide_private'] == true) {
                $courseInfo = api_get_course_info();
                $courseVisibility = $courseInfo['visibility'];
                $visibilityCondition = ' AND c.visibility <> 1';
            }
        }
        if (!empty($accessUrlId) && $accessUrlId == intval($accessUrlId)) {
            $sql = "SELECT count(id) FROM $tableCourse c, $tableCourseRelAccessUrl u
                    WHERE
                        c.code = u.course_code AND
                        u.access_url_id = $accessUrlId AND
                        c.visibility != 0 AND
                        c.visibility != 4
                        $withoutSpecialCourses
                        $visibilityCondition
                    ";
        }
        $res = Database::query($sql);
        $row = Database::fetch_row($res);

        return $row[0];
    }

    /**
     * Return a link to go to the course, validating the visibility of the
     * course and the user status
     * @param int User ID
     * @param array Course details array
     * @param array  List of courses to which the user is subscribed (if not provided, will be generated)
     * @return mixed 'enter' for a link to go to the course or 'register' for a link to subscribe, or false if no access
     */
    static function get_access_link_by_user($uid, $course, $user_courses = array())
    {
        if (empty($uid) or empty($course)) {
            return false;
        }

        if (empty($user_courses)) {
            // get the array of courses to which the user is subscribed
            $user_courses = CourseManager::get_courses_list_by_user_id($uid);
            foreach ($user_courses as $k => $v) {
                $user_courses[$k] = $v['real_id'];
            }
        }

        if (!isset($course['real_id']) && empty($course['real_id'])) {
            $course = api_get_course_info($course['code']);
        }

        if ($course['visibility'] == COURSE_VISIBILITY_HIDDEN) {
            return array();
        }

        $is_admin = api_is_platform_admin_by_id($uid);
        $options = array();
        // Register button
        if (!api_is_anonymous($uid) &&
            (
            ($course['visibility'] == COURSE_VISIBILITY_OPEN_WORLD || $course['visibility'] == COURSE_VISIBILITY_OPEN_PLATFORM)
                //$course['visibility'] == COURSE_VISIBILITY_REGISTERED && $course['subscribe'] == SUBSCRIBE_ALLOWED
            ) &&
            $course['subscribe'] == SUBSCRIBE_ALLOWED &&
            (!in_array($course['real_id'], $user_courses) || empty($user_courses))
        ) {
            $options[] = 'register';
        }

        // Go To Course button (only if admin, if course public or if student already subscribed)
        if ($is_admin ||
            $course['visibility'] == COURSE_VISIBILITY_OPEN_WORLD && empty($course['registration_code']) ||
            (api_user_is_login($uid) && $course['visibility'] == COURSE_VISIBILITY_OPEN_PLATFORM && empty($course['registration_code'])) ||
            (in_array($course['real_id'], $user_courses) && $course['visibility'] != COURSE_VISIBILITY_CLOSED)
        ) {
            $options[] = 'enter';
        }

        if ($is_admin ||
            $course['visibility'] == COURSE_VISIBILITY_OPEN_WORLD && empty($course['registration_code']) ||
            (api_user_is_login($uid) && $course['visibility'] == COURSE_VISIBILITY_OPEN_PLATFORM && empty($course['registration_code'])) ||
            (in_array($course['real_id'], $user_courses) && $course['visibility'] != COURSE_VISIBILITY_CLOSED)
        ) {
            $options[] = 'enter';
        }

        if ($course['visibility'] != COURSE_VISIBILITY_HIDDEN && empty($course['registration_code']) && $course['unsubscribe'] == UNSUBSCRIBE_ALLOWED && api_user_is_login($uid) && (in_array($course['real_id'],
                $user_courses))
        ) {
            $options[] = 'unsubscribe';
        }

        return $options;
    }

    /**
     * @param int $courseId
     * @param array $teachers
     * @param bool $deleteTeachersNotInList
     * @param bool $editTeacherInSessions
     * @param bool $deleteSessionTeacherNotInList
     * @return bool
     */
    public static function updateTeachers(
        $courseId,
        $teachers,
        $deleteTeachersNotInList = true,
        $editTeacherInSessions = false,
        $deleteSessionTeacherNotInList = false
    ) {
        if (empty($teachers)) {
            return false;
        }
        if (!is_array($teachers)) {
            $teachers = array($teachers);
        }

        $courseInfo = api_get_course_info_by_id($courseId);
        $course_code = $courseInfo['code'];

        $course_user_table = Database::get_main_table(TABLE_MAIN_COURSE_USER);
        $alreadyAddedTeachers = CourseManager::get_teacher_list_from_course_code($course_code);

        if ($deleteTeachersNotInList) {

            // Delete only teacher relations that doesn't match the selected teachers
            $cond = null;
            if (count($teachers) > 0) {
                foreach ($teachers as $key) {
                    $key = Database::escape_string($key);
                    $cond .= " AND user_id <> '" . $key . "'";
                }
            }

            $sql = 'DELETE FROM ' . $course_user_table . '
                    WHERE course_code="' . Database::escape_string($course_code) . '" AND status="1"' . $cond;
            Database::query($sql);
        }

        if (count($teachers) > 0) {
            foreach ($teachers as $userId) {
                $userId = intval($userId);
                // We check if the teacher is already subscribed in this course
                $sql = 'SELECT 1 FROM ' . $course_user_table . '
                        WHERE user_id = "' . $userId . '" AND course_code = "' . $course_code . '" ';
                $result = Database::query($sql);
                if (Database::num_rows($result)) {
                    $sql = 'UPDATE ' . $course_user_table . ' SET status = "1"
                            WHERE course_code = "' . $course_code . '" AND user_id = "' . $userId . '"  ';
                } else {
                    $sql = "INSERT INTO " . $course_user_table . " SET
                        course_code = '" . Database::escape_string($course_code) . "',
                        user_id = '" . $userId . "',
                        status = '1',
                        role = '',
                        tutor_id = '0',
                        sort = '0',
                        user_course_cat='0'";
                }
                Database::query($sql);
            }
        }

        if ($editTeacherInSessions) {
            $sessions = SessionManager::get_session_by_course($course_code);

            if (!empty($sessions)) {
                foreach ($sessions as $session) {
                    // Remove old and add new
                    if ($deleteSessionTeacherNotInList) {
                        foreach ($teachers as $userId) {
                            SessionManager::set_coach_to_course_session($userId, $session['id'], $course_code);
                        }

                        $teachersToDelete = array();
                        if (!empty($alreadyAddedTeachers)) {
                            $teachersToDelete = array_diff(array_keys($alreadyAddedTeachers), $teachers);
                        }

                        if (!empty($teachersToDelete)) {
                            foreach ($teachersToDelete as $userId) {
                                SessionManager::set_coach_to_course_session($userId, $session['id'], $course_code,
                                    true);
                            }
                        }
                    } else {
                        // Add new teachers only
                        foreach ($teachers as $userId) {
                            SessionManager::set_coach_to_course_session($userId, $session['id'], $course_code);
                        }
                    }
                }
            }
        }
    }

    /**
     * Course available settings variables see c_course_setting table
     * @param AppPlugin $appPlugin
     * @return array
     */
    public static function getCourseSettingVariables(AppPlugin $appPlugin)
    {
        $pluginCourseSettings = $appPlugin->getAllPluginCourseSettings();
        $courseSettings = array(
            // Get allow_learning_path_theme from table
            'allow_learning_path_theme',
            // Get allow_open_chat_window from table
            'allow_open_chat_window',
            'allow_public_certificates',
            // Get allow_user_edit_agenda from table
            'allow_user_edit_agenda',
            // Get allow_user_edit_announcement from table
            'allow_user_edit_announcement',
            // Get allow_user_image_forum from table
            'allow_user_image_forum',
            //Get allow show user list
            'allow_user_view_user_list',
            // Get course_theme from table
            'course_theme',
            //Get allow show user list
            'display_info_advance_inside_homecourse',
            'documents_default_visibility',
            // Get send_mail_setting (work)from table
            'email_alert_manager_on_new_doc',
            // Get send_mail_setting (work)from table
            'email_alert_manager_on_new_quiz',
            // Get send_mail_setting (dropbox) from table
            'email_alert_on_new_doc_dropbox',
            'email_alert_students_on_new_homework',
            // Get send_mail_setting (auth)from table
            'email_alert_to_teacher_on_new_user_in_course',
            'enable_lp_auto_launch',
            'pdf_export_watermark_text',
            'show_system_folders'
        );

        global $_configuration;
        if (isset($_configuration['allow_lp_return_link']) && $_configuration['allow_lp_return_link']) {
            $courseSettings[] = 'lp_return_link';
        }

        if (!empty($pluginCourseSettings)) {
            $courseSettings = array_merge(
                $courseSettings,
                $pluginCourseSettings
            );
        }

        return $courseSettings;
    }

    /**
     * @param AppPlugin $appPlugin
     * @param string $variable
     * @param string $value
     * @param int $courseId
     * @return bool
     */
    public static function saveCourseConfigurationSetting(AppPlugin $appPlugin, $variable, $value, $courseId)
    {
        $settingList = self::getCourseSettingVariables($appPlugin);

        if (!in_array($variable, $settingList)) {
            return false;
        }

        $courseSettingTable = Database::get_course_table(TABLE_COURSE_SETTING);

        if (self::hasCourseSetting($variable, $courseId)) {
            // Update
            Database::update(
                $courseSettingTable,
                array('value' => $value),
                array('variable = ? AND c_id = ?' => array($variable, $courseId))
            );
        } else {
            // Create
            Database::insert(
                $courseSettingTable,
                array('value' => $value, 'c_id' => $courseId, 'variable' => $variable)
            );
        }
        return true;
    }

    /**
     * Check if course setting exists
     * @param string $variable
     * @param int $courseId
     * @return bool
     */
    public static function hasCourseSetting($variable, $courseId)
    {
        $courseSetting = Database::get_course_table(TABLE_COURSE_SETTING);
        $courseId = intval($courseId);
        $variable = Database::escape_string($variable);
        $sql = "SELECT variable FROM $courseSetting
                WHERE c_id = $courseId AND variable = '$variable'";
        $result = Database::query($sql);
        return Database::num_rows($result) > 0;
    }

    /**
     * Get information from the track_e_course_access table
     * @param int $sessionId
     * @param int $userId
     * @return array
     */
    public static function getCourseAccessPerSessionAndUser($sessionId, $userId, $limit = null)
    {
        $table = Database:: get_main_table(TABLE_STATISTIC_TRACK_E_COURSE_ACCESS);

        $sessionId = intval($sessionId);
        $userId = intval($userId);

        $sql = "SELECT * FROM $table
                WHERE session_id = $sessionId AND user_id = $userId";

        if (!empty($limit)) {
            $limit = intval($limit);
            $sql .= " LIMIT $limit";
        }
        $result = Database::query($sql);

        return Database::store_result($result);
    }

    /**
     * Get information from the track_e_course_access table
     * @param int $courseId
     * @param int $sessionId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public static function getCourseAccessPerCourseAndSession(
        $courseId,
        $sessionId,
        $startDate,
        $endDate
    ) {
        $table = Database:: get_main_table(TABLE_STATISTIC_TRACK_E_COURSE_ACCESS);
        $courseId = intval($courseId);
        $sessionId = intval($sessionId);
        $startDate = Database::escape_string($startDate);
        $endDate = Database::escape_string($endDate);

        $sql = "SELECT * FROM $table
                WHERE
                    c_id = $courseId AND
                    session_id = $sessionId AND
                    login_course_date BETWEEN '$startDate' AND '$endDate'
                ";

        $result = Database::query($sql);

        return Database::store_result($result);
    }

    /**
     * Get login information from the track_e_course_access table, for any
     * course in the given session
     * @param int $sessionId
     * @param int $userId
     * @return array
     */
    public static function getFirstCourseAccessPerSessionAndUser($sessionId, $userId)
    {
        $sessionId = intval($sessionId);
        $userId = intval($userId);

        $table = Database:: get_main_table(TABLE_STATISTIC_TRACK_E_COURSE_ACCESS);
        $sql = "SELECT * FROM $table
                WHERE session_id = $sessionId AND user_id = $userId
                ORDER BY login_course_date ASC
                LIMIT 1";

        $result = Database::query($sql);
        $courseAccess = array();
        if (Database::num_rows($result)) {
            $courseAccess = Database::fetch_array($result, 'ASSOC');
        }
        return $courseAccess;
    }

    /**
     * @param int $courseId
     * @param int $sessionId
     * @param bool $getAllSessions
     * @return mixed
     */
    public static function getCountForum(
        $courseId,
        $sessionId = 0,
        $getAllSessions = false
    ) {
        $forum = Database::get_course_table(TABLE_FORUM);
        if ($getAllSessions) {
            $sql = "SELECT count(*) as count
            FROM $forum f
            where f.c_id = %s";
        } else {
            $sql = "SELECT count(*) as count
            FROM $forum f
            where f.c_id = %s and f.session_id = %s";
        }

        $sql = sprintf($sql, intval($courseId), intval($sessionId));
        $result = Database::query($sql);
        $row = Database::fetch_array($result);

        return $row['count'];
    }

    /**
     * @param int $userId
     * @param int $courseId
     * @param int $sessionId
     * @return mixed
     */
    public static function getCountPostInForumPerUser(
        $userId,
        $courseId,
        $sessionId = 0
    ) {
        $forum = Database::get_course_table(TABLE_FORUM);
        $forum_post = Database::get_course_table(TABLE_FORUM_POST);

        $sql = "SELECT count(distinct post_id) as count
                FROM $forum_post p
                INNER JOIN $forum f
                ON f.forum_id = p.forum_id AND f.c_id = p.c_id
                WHERE p.poster_id = %s and f.session_id = %s and p.c_id = %s";

        $sql = sprintf(
            $sql,
            intval($userId),
            intval($sessionId),
            intval($courseId)
        );

        $result = Database::query($sql);
        $row = Database::fetch_array($result);
        return $row['count'];
    }

    /**
     * @param int $userId
     * @param int $courseId
     * @param int $sessionId
     * @return mixed
     */
    public static function getCountForumPerUser(
        $userId,
        $courseId,
        $sessionId = 0
    ) {
        $forum = Database::get_course_table(TABLE_FORUM);
        $forum_post = Database::get_course_table(TABLE_FORUM_POST);

        $sql = "SELECT count(distinct f.forum_id) as count
                FROM $forum_post p
                INNER JOIN $forum f
                ON f.forum_id = p.forum_id AND f.c_id = p.c_id
                WHERE p.poster_id = %s and f.session_id = %s and p.c_id = %s";

        $sql = sprintf(
            $sql,
            intval($userId),
            intval($sessionId),
            intval($courseId)
        );

        $result = Database::query($sql);
        $row = Database::fetch_array($result);
        return $row['count'];
    }

    /**
     * Returns the course name from a given code
     * @param string $code
     */
    public static function getCourseNameFromCode($code)
    {
        $tbl_main_categories = Database:: get_main_table(TABLE_MAIN_COURSE);
        $sql = 'SELECT title
                FROM ' . $tbl_main_categories . '
                WHERE code = "' . Database::escape_string($code) . '"';
        $result = Database::query($sql);
        if ($col = Database::fetch_array($result)) {
            return $col['title'];
        }
    }

    /**
     * Generates a course code from a course title
     * @todo Such a function might be useful in other places too. It might be moved in the CourseManager class.
     * @todo the function might be upgraded for avoiding code duplications (currently, it might suggest a code that is already in use)
     * @param string A course title
     * @param string The course title encoding (defaults to type defined globally)
     * @return string A proposed course code
     * @assert (null,null) === false
     * @assert ('ABC_DEF', null) === 'ABCDEF'
     * @assert ('ABC09*^[%A', null) === 'ABC09A'
     */
    public static function generate_course_code($course_title, $encoding = null)
    {
        if (empty($encoding)) {
            $encoding = api_get_system_encoding();
        }
        return substr(preg_replace('/[^A-Z0-9]/', '', strtoupper(api_transliterate($course_title, 'X', $encoding))), 0,
            CourseManager::MAX_COURSE_LENGTH_CODE);
    }

    /**
     * @param $courseId
     * @return array
     */
    public static function getCourseSettings($courseId)
    {
        $settingTable = Database::get_course_table(TABLE_COURSE_SETTING);
        $courseId = intval($courseId);
        $sql = "SELECT * FROM $settingTable WHERE c_id = $courseId";
        $result = Database::query($sql);
        $settings = array();
        if (Database::num_rows($result)) {
            while ($row = Database::fetch_array($result, 'ASSOC')) {
                $settings[$row['variable']] = $row;
            }
        }
        return $settings;
    }

    /**
     * this function gets all the users of the course,
     * including users from linked courses
     */
    public static function getCourseUsers()
    {
        //this would return only the users from real courses:
        $session_id = api_get_session_id();
        if ($session_id != 0) {
            $user_list = self::get_real_and_linked_user_list(api_get_course_id(), true, $session_id);
        } else {
            $user_list = self::get_real_and_linked_user_list(api_get_course_id(), false, 0);
        }

        return $user_list;
    }

    /**
     * this function gets all the groups of the course,
     * not including linked courses
     */
    public static function getCourseGroups()
    {
        $session_id = api_get_session_id();
        if ($session_id != 0) {
            $new_group_list = self::get_group_list_of_course(api_get_course_id(), $session_id, 1);
        } else {
            $new_group_list = self::get_group_list_of_course(api_get_course_id(), 0, 1);
        }

        return $new_group_list;
    }

    /**
     * @param FormValidator $form
     * @param array $to_already_selected
     */
    public static function addUserGroupMultiSelect(&$form, $to_already_selected)
    {
        $user_list = self::getCourseUsers();
        $group_list = self::getCourseGroups();
        $array = self::buildSelectOptions($group_list, $user_list, $to_already_selected);

        $result = array();
        foreach ($array as $content) {
            $result[$content['value']] = $content['content'];
        }

        $group = $form->addElement('advmultiselect', 'users', get_lang('Users'), $result,
            array('select_all_checkbox' => true));
    }

    /**
     * this function shows the form for sending a message to a specific group or user.
     */
    /**
     * @param FormValidator $form
     * @param int $group_id
     * @param array $to
     */
    public static function addGroupMultiSelect($form, $group_id, $to = array())
    {
        $group_users = GroupManager::get_subscribed_users($group_id);

        $array = self::buildSelectOptions(null, $group_users, $to);

        $result = array();
        foreach ($array as $content) {
            $result[$content['value']] = $content['content'];
        }

        $group = $form->addElement('advmultiselect', 'users', get_lang('Users'), $result);
    }

    /**
     * this function shows the form for sending a message to a specific group or user.
     * @param array $group_list
     * @param array $user_list
     * @param array $to_already_selected
     * @return array
     */
    public static function buildSelectOptions(
        $group_list = array(),
        $user_list = array(),
        $to_already_selected = array()
    ) {
        if (empty($to_already_selected)) {
            $to_already_selected = array();
        }

        $result = array();
        // adding the groups to the select form
        if ($group_list) {
            foreach ($group_list as $this_group) {
                if (is_array($to_already_selected)) {
                    if (!in_array(
                        "GROUP:" . $this_group['id'],
                        $to_already_selected
                    )
                    ) { // $to_already_selected is the array containing the groups (and users) that are already selected
                        $user_label = ($this_group['userNb'] > 0) ? get_lang('Users') : get_lang('LowerCaseUser');
                        $user_disabled = ($this_group['userNb'] > 0) ? "" : "disabled=disabled";
                        $result [] = array(
                            'disabled' => $user_disabled,
                            'value' => "GROUP:" . $this_group['id'],
                            'content' => "G: " . $this_group['name'] . " - " . $this_group['userNb'] . " " . $user_label
                        );
                    }
                }
            }
        }

        // adding the individual users to the select form
        if ($user_list) {
            foreach ($user_list as $user) {
                if (is_array($to_already_selected)) {
                    if (!in_array(
                        "USER:" . $user['user_id'],
                        $to_already_selected
                    )
                    ) { // $to_already_selected is the array containing the users (and groups) that are already selected

                        $result[] = array(
                            'value' => "USER:" . $user['user_id'],
                            'content' => api_get_person_name($user['firstname'], $user['lastname'])
                        );
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return array a list (array) of all courses.
     */
    public static function get_course_list()
    {
        $table = Database::get_main_table(TABLE_MAIN_COURSE);
        return Database::store_result(self::query("SELECT *, id as real_id FROM $table"));
    }

    /**
     * Returns course code from a given gradebook category's id
     * @param int  Category ID
     * @return string  Course code
     */
    public static function get_course_by_category($category_id)
    {
        $category_id = intval($category_id);
        $info = Database::fetch_array(
            Database::query('SELECT course_code FROM ' . Database::get_main_table(TABLE_MAIN_GRADEBOOK_CATEGORY) . '
            WHERE id=' . $category_id), 'ASSOC'
        );
        return $info ? $info['course_code'] : false;
    }

    /**
     * This function gets all the courses that are not in a session
     * @param date Start date
     * @param date End date
     * @return array Not-in-session courses
     */
    public static function getCoursesWithoutSession($startDate = null, $endDate = null)
    {
        $dateConditional = ($startDate && $endDate) ?
            " WHERE id_session IN (SELECT id FROM " . Database::get_main_table(TABLE_MAIN_SESSION) .
            " WHERE date_start = '$startDate' AND date_end = '$endDate')" :
            null;
        $query = "SELECT id, code, title FROM " . Database::get_main_table(TABLE_MAIN_COURSE) . " WHERE CODE NOT IN
            (SELECT DISTINCT course_code FROM " . Database::get_main_table(TABLE_MAIN_SESSION_COURSE) . $dateConditional . ")
            ORDER BY id";

        $result = Database::query($query);
        $courses = array();
        while ($row = Database::fetch_array($result)) {
            $courses[] = $row;
        }
        return $courses;
    }
}
