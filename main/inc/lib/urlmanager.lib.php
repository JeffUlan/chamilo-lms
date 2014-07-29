<?php
/* For licensing terms, see /license.txt */
/**
*	This library provides functions for the access_url management.
*	Include/require it in your code to use its functionality.
*
*	@package chamilo.library
*/
/**
 * Class UrlManager
 */
class UrlManager
{
    /**
     * Creates a new url access
     *
     * @author Julio Montoya <gugli100@gmail.com>,
     *
     * @param string The URL of the site
     * @param string The description of the site
     * @param int is active or not
     * @param int the user_id of the owner
     * @param int The type of URL (1=multiple-access-url, 2=sincro-server, 3=sincro-client)
     * @param array If the type is different than 1, then there might be extra URL parameters to take into account
     * @return boolean if success
     */
    public static function add($url, $description, $active, $type = 1, $extra_params)
    {
        $tms              = time();
        $type             = intval($type);
        $table_access_url = Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
        $u                = api_get_user_id();
        if ($u == 0) {
            $u = api_get_anonymous_id();
        }
        if ($type > 1) {
            $active = 0;
        }
        $sql    = "INSERT INTO $table_access_url ".
            " SET url 	= '".Database::escape_string($url)."', ".
            " description = '".Database::escape_string($description)."', ".
            " active 		= $active, ".
            " created_by 	= $u, ".
            " url_type        = $type, ".
            " tms = FROM_UNIXTIME(".$tms.")";
        $result = Database::query($sql);
        $id     = Database::insert_id();
        if ($result !== false && $type == 3 && count($extra_params) > 0) {
            // Register extra parameters in the branch_sync table
            $t      = Database::get_main_table(TABLE_BRANCH_SYNC);
            $sql    = "INSERT INTO $t SET ".
                " access_url_id = $id ".
                (!empty($extra_params['ip']) ? ", branch_ip = '".Database::escape_string($extra_params['ip'])."'" : "").
                (!empty($extra_params['name']) ? ", branch_name = '".Database::escape_string(
                        $extra_params['name']
                    )."'" : "").
                (!empty($extra_params['last_sync']) ? ", last_sync_trans_id = '".Database::escape_string(
                        $extra_params['last_sync']
                    )."'" : "").
                (!empty($extra_params['dwn_speed']) ? ", dwn_speed = '".Database::escape_string(
                        $extra_params['dwn_speed']
                    )."'" : "").
                (!empty($extra_params['up_speed']) ? ", up_speed = '".Database::escape_string(
                        $extra_params['up_speed']
                    )."'" : "").
                (!empty($extra_params['delay']) ? ", delay = '".Database::escape_string(
                        $extra_params['delay']
                    )."'" : "").
                (!empty($extra_params['admin_mail']) ? ", admin_mail = '".Database::escape_string(
                        $extra_params['admin_mail']
                    )."'" : "").
                (!empty($extra_params['admin_name']) ? ", admin_name = '".Database::escape_string(
                        $extra_params['admin_name']
                    )."'" : "").
                (!empty($extra_params['admin_phone']) ? ", admin_phone = '".Database::escape_string(
                        $extra_params['admin_phone']
                    )."'" : "").
                (!empty($extra_params['latitude']) ? ", latitude = '".Database::escape_string(
                        $extra_params['latitude']
                    )."'" : "").
                (!empty($extra_params['longitude']) ? ", longitude = '".Database::escape_string(
                        $extra_params['longitude']
                    )."'" : "").
                ", last_sync_trans_date = '".api_get_utc_datetime()."'";
            $result = $result && Database::query($sql);
        }

        return $result;
    }

    /**
     * Updates an URL access to Dokeos
     * @author Julio Montoya <gugli100@gmail.com>,
     *
     * @param    int    The url id
     * @param    string  The description of the site
     * @param    int        is active or not
     * @param    int     the user_id of the owner
     * @param    int    The URL type
     * @param    array    Extra parameters for type > 1
     * @return    boolean if success
     */
    public static function update($url_id, $url, $description, $active, $type, $extra_params)
    {
        $url_id = intval($url_id);
        $table_access_url= Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
        $tms = time();
        $sql = "UPDATE $table_access_url
                SET url 	= '".Database::escape_string($url)."',
                description = '".Database::escape_string($description)."',
                active 		= '".Database::escape_string($active)."',
                created_by 	= '".api_get_user_id()."',
                tms 		= FROM_UNIXTIME(".$tms.")
                WHERE id = '$url_id'";
        $result = Database::query($sql);
        return $result;
    }

    /**
     * Deletes an url
     * @author Julio Montoya
     * @param int url id
     * @return boolean true if success
     * */
    public static function delete($id)
    {
        $id               = intval($id);
        $table_bs         = Database :: get_main_table(TABLE_BRANCH_SYNC);
        $table_bsl        = Database :: get_main_table(TABLE_BRANCH_SYNC_LOG);
        $table_bt         = Database :: get_main_table(TABLE_BRANCH_TRANSACTION);
        $table_access_url = Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
        $sql              = "DELETE FROM $table_bt WHERE branch_id = ".$id;
        $result           = Database::query($sql);
        $sql              = "DELETE FROM $table_bsl WHERE branch_sync_id = ".$id;
        $result           = Database::query($sql);
        $sql              = "DELETE FROM $table_bs WHERE access_url_id = ".$id;
        $result           = Database::query($sql);
        $sql              = "DELETE FROM $table_access_url WHERE id = ".$id;
        $result           = Database::query($sql);

        return $result;
    }

    /**
     * @param string $url
     * @return int
     */
    public static function url_exist($url)
    {
        $table_access_url= Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
        $sql = "SELECT id FROM $table_access_url WHERE url = '".Database::escape_string($url)."' ";
        $res = Database::query($sql);
        $num = Database::num_rows($res);
        return $num;
    }

    /**
     * @param string $url
     * @return int
     */
    public static function url_id_exist($url)
    {
        $table_access_url= Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
        $sql = "SELECT id FROM $table_access_url WHERE id = '".Database::escape_string($url)."' ";
        $res = Database::query($sql);
        $num = Database::num_rows($res);
        return $num;
    }

    /**
     * This function get the quantity of URLs
     * @author Julio Montoya
     * @return int count of urls
     * */
    public static function url_count()
    {
        $table_access_url= Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
        $sql = "SELECT count(id) as count_result FROM $table_access_url";
        $res = Database::query($sql);
        $url = Database::fetch_array($res,'ASSOC');
        $result = $url['count_result'];
        return $result;
    }

    /**
     * Gets the id, url, description, and active status of ALL URLs
     * @author Julio Montoya
     * @return array
     * */
    public static function get_url_data()
    {
        $table_access_url  = Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
        $table_branch_sync = Database :: get_main_table(TABLE_BRANCH_SYNC);
        $sql               = "SELECT id, url, description, active, url_type FROM $table_access_url ORDER BY id";
        $res               = Database::query($sql);
        $urls              = array();
        while ($url = Database::fetch_assoc($res)) {
            if ($url['url_type'] > 1) {
                $sql2 = "SELECT branch_name, branch_ip, latitude, longitude, dwn_speed, up_speed, delay, admin_mail, admin_name, admin_phone, last_sync_trans_id, last_sync_trans_date, last_sync_type FROM $table_branch_sync WHERE access_url_id = ".$url['id'];
                $res2 = Database::query($sql2);
                $url2 = Database::fetch_assoc($res2);
                $url  = array_merge($url, $url2);
            }
            $urls[] = $url;
        }

        return $urls;
    }

    /**
     * Gets the id, url, description, and active status of ALL URLs
     * @author Julio Montoya
     * @param int $url_id
     * @return array
     * */
    public static function get_url_data_from_id($url_id)
    {
        $table_access_url = Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
        $sql              = "SELECT id, url, description, active, url_type FROM $table_access_url WHERE id = ".Database::escape_string(
                $url_id
            );
        $res              = Database::query($sql);
        $row              = Database::fetch_array($res);
        if ($row['url_type'] > 1) {
            $sql2 = "SELECT * FROM $table_branch_sync WHERE access_url_id = ".$url['id'];
            $res2 = Database::query($sql);
            $row2 = Database::fetch_array($res2);
            $row  = array_merge($row, $row2);
        }

        return $row;
    }

    /**
     * Gets the inner join of users and urls table
     * @author Julio Montoya
     * @param int  access url id
     * @param string $order_by
     * @return array   Database::store_result of the result
     **/
    public static function get_url_rel_user_data($access_url_id = null, $order_by = null)
    {
        $where              = '';
        $table_url_rel_user = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
        $tbl_user           = Database :: get_main_table(TABLE_MAIN_USER);
        if (!empty($access_url_id)) {
            $where = "WHERE $table_url_rel_user.access_url_id = ".Database::escape_string($access_url_id);
        }
        if (empty($order_by)) {
            $order_clause = api_sort_by_first_name(
            ) ? ' ORDER BY firstname, lastname, username' : ' ORDER BY lastname, firstname, username';
        } else {
            $order_clause = $order_by;
        }
        $sql = "SELECT u.user_id, lastname, firstname, username, official_code, access_url_id
			FROM $tbl_user u
			INNER JOIN $table_url_rel_user
			ON $table_url_rel_user.user_id = u.user_id
			$where  $order_clause";
        $result = Database::query($sql);
        $users = Database::store_result($result);

        return $users;
    }

    /**
     * Gets the inner join of access_url and the course table
     * @author Julio Montoya
     * @param int  access url id
     * @return array   Database::store_result of the result
     * */
    public static function get_url_rel_course_data($access_url_id = '')
    {
        $where                = '';
        $table_url_rel_course = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        $tbl_course           = Database :: get_main_table(TABLE_MAIN_COURSE);

        if (!empty($access_url_id)) {
            $where = "WHERE $table_url_rel_course.access_url_id = ".Database::escape_string($access_url_id);
        }

        $sql = "SELECT c_id, code, title, access_url_id
				FROM $tbl_course u
				INNER JOIN $table_url_rel_course
				ON $table_url_rel_course.c_id = u.id
				$where
				ORDER BY title, c_id";

        $result  = Database::query($sql);
        $courses = Database::store_result($result);

        return $courses;
    }

    /**
     * Gets the inner join of access_url and the usergroup table
     *
     * @author Julio Montoya
     * @param int  access url id
     * @return array   Database::store_result of the result
     **/

    public static function getUrlRelCourseCategory($access_url_id = null)
    {

        $table_url_rel = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE_CATEGORY);
        $table = Database::get_main_table(TABLE_MAIN_CATEGORY);
        $where = " WHERE 1=1 ";
        if (!empty($access_url_id)) {
            $where .= " AND $table_url_rel.access_url_id = ".intval($access_url_id);
        }
        $where .= " AND (parent_id IS NULL) ";

        $sql = "SELECT id, name, access_url_id
            FROM $table u
            INNER JOIN $table_url_rel
            ON $table_url_rel.course_category_id = u.id
            $where
            ORDER BY name";

        $result = Database::query($sql);
        $courses = Database::store_result($result, 'ASSOC');
        return $courses;
    }

    /** Gets the inner join of access_url and the session table
     * @author Julio Montoya
     * @param int  access url id
     * @return array   Database::store_result of the result
     * */
    public static function get_url_rel_session_data($access_url_id = '')
    {
        $where                 = '';
        $table_url_rel_session = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_SESSION);
        $tbl_session           = Database :: get_main_table(TABLE_MAIN_SESSION);

        if (!empty($access_url_id)) {
            $where = "WHERE $table_url_rel_session.access_url_id = ".Database::escape_string($access_url_id);
        }

        $sql = "SELECT id, name, access_url_id
				FROM $tbl_session u
				INNER JOIN $table_url_rel_session
				ON $table_url_rel_session.session_id = id
				$where
				ORDER BY name, id";

        $result   = Database::query($sql);
        $sessions = Database::store_result($result);

        return $sessions;
    }


    /**
     * Sets the status of an URL 1 or 0
     * @author Julio Montoya
     * @param string lock || unlock
     * @param int url id
     * */
    public static function set_url_status($status, $url_id)
    {
        $url_table = Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
        if ($status == 'lock') {
            $status_db = '0';
        }
        if ($status == 'unlock') {
            $status_db = '1';
        }
        if (($status_db == '1' OR $status_db == '0') AND is_numeric($url_id)) {
            $sql    = "UPDATE $url_table SET active='".Database::escape_string(
                    $status_db
                )."' WHERE id='".Database::escape_string($url_id)."'";
            $result = Database::query($sql);
        }
    }

    /**
    * Checks the relationship between an URL and a User (return the num_rows)
    * @author Julio Montoya
    * @param int user id
    * @param int url id
    * @return boolean true if success
    * */
    public static function relation_url_user_exist($user_id, $url_id)
    {
        $table_url_rel_user= Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
        $sql= "SELECT user_id FROM $table_url_rel_user
               WHERE access_url_id = ".Database::escape_string($url_id)." AND  user_id = ".Database::escape_string($user_id)." ";
        $result = Database::query($sql);
        $num = Database::num_rows($result);

        return $num;
	}

    /**
    * Checks the relationship between an URL and a Course (return the num_rows)
    * @author Julio Montoya
    * @param int user id
    * @param int url id
    * @return boolean true if success
    * */
    public static function relation_url_course_exist($course_id, $url_id)
    {
        $table_url_rel_course= Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        $sql= "SELECT course_code FROM $table_url_rel_course
               WHERE access_url_id = ".Database::escape_string($url_id)." AND
                     course_code = '".Database::escape_string($course_id)."'";
        $result = Database::query($sql);
        $num = Database::num_rows($result);
        return $num;
    }

    /**
     * Checks the relationship between an URL and a UserGr
     * oup (return the num_rows)
     * @author Julio Montoya
     * @param int $userGroupId
     * @param int $urlId
     * @return boolean true if success
     * */
    public static function relation_url_usergroup_exist($userGroupId, $urlId)
    {
        $table = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_USERGROUP);
        $sql= "SELECT usergroup_id FROM $table
               WHERE access_url_id = ".Database::escape_string($urlId)." AND
                     usergroup_id = ".Database::escape_string($userGroupId);
        $result = Database::query($sql);
        $num = Database::num_rows($result);
        return $num;
    }

    /**
    * Checks the relationship between an URL and a Session (return the num_rows)
    * @author Julio Montoya
    * @param int user id
    * @param int url id
    * @return boolean true if success
    * */
    public static function relation_url_session_exist($session_id, $url_id)
    {
        $table_url_rel_session= Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_SESSION);
        $session_id = intval($session_id);
        $url_id		= intval($url_id);
        $sql= "SELECT session_id FROM $table_url_rel_session WHERE access_url_id = ".Database::escape_string($url_id)." AND session_id = ".Database::escape_string($session_id);
        $result 	= Database::query($sql);
        $num 		= Database::num_rows($result);
        return $num;
    }

    /**
     * Add a group of users into a group of URLs
     * @author Julio Montoya
     * @param  array of user_ids
     * @param  array of url_ids
     * @return array
     * */
    public static function add_users_to_urls($user_list, $url_list)
    {
        $table_url_rel_user = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
        $result_array = array();

        if (is_array($user_list) && is_array($url_list)){
            foreach ($url_list as $url_id) {
                foreach ($user_list as $user_id) {
                    $count = UrlManager::relation_url_user_exist($user_id,$url_id);
                    if ($count==0) {
                        $sql = "INSERT INTO $table_url_rel_user
                                SET user_id = ".Database::escape_string($user_id).", access_url_id = ".Database::escape_string($url_id);
                        $result = Database::query($sql);
                        if ($result) {
                            $result_array[$url_id][$user_id] = 1;
                        } else {
                            $result_array[$url_id][$user_id] = 0;
                        }
                    }
                }
            }
        }

        return $result_array;
    }


    /**
     * Add a group of courses into a group of URLs
     * @author Julio Montoya
     * @param  array of course ids
     * @param  array of url_ids
     * @return array
     **/
    public static function add_courses_to_urls($course_list,$url_list)
    {
        $table_url_rel_course = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        $result_array         = array();

        if (is_array($course_list) && is_array($url_list)) {
            foreach ($url_list as $url_id) {
                foreach ($course_list as $courseId) {
                    $count = UrlManager::relation_url_course_exist($courseId, $url_id);
                    if ($count == 0) {
                        $sql    = "INSERT INTO $table_url_rel_course
		               			   SET c_id = '".Database::escape_string(
                                $courseId
                            )."', access_url_id = ".Database::escape_string($url_id);
                        $result = Database::query($sql);
                        if ($result) {
                            $result_array[$url_id][$courseId] = 1;
                        } else {
                            $result_array[$url_id][$courseId] = 0;
                        }
                    }
                }
            }
        }

        return $result_array;
    }

    /**
     * Add a group of user group into a group of URLs
     * @author Julio Montoya
     * @param  array of course ids
     * @param  array of url_ids
     * @return array
     **/
    public static function addCourseCategoryListToUrl($courseCategoryList, $urlList)
    {
        $resultArray = array();
        if (is_array($courseCategoryList) && is_array($urlList)) {
            foreach ($urlList as $urlId) {
                foreach ($courseCategoryList as $categoryCourseId) {
                    $count = self::relationUrlCourseCategoryExist($categoryCourseId, $urlId);
                    if ($count == 0) {
                        $result = self::addCourseCategoryToUrl($categoryCourseId, $urlId);
                        if ($result) {
                            $resultArray[$urlId][$categoryCourseId] = 1;
                        } else {
                            $resultArray[$urlId][$categoryCourseId] = 0;
                        }
                    }
                }
            }
        }

        return 	$resultArray;
    }

    /**
     * Checks the relationship between an URL and a UserGr
     * oup (return the num_rows)
     * @author Julio Montoya
     * @param int $categoryCourseId
     * @param int $urlId
     * @return boolean true if success
     * */
    public static function relationUrlCourseCategoryExist($categoryCourseId, $urlId)
    {
        $table = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE_CATEGORY);
        $sql= "SELECT course_category_id FROM $table
               WHERE access_url_id = ".Database::escape_string($urlId)." AND
                     course_category_id = ".Database::escape_string($categoryCourseId);
        $result = Database::query($sql);
        $num = Database::num_rows($result);
        return $num;
    }

    /**
     * @param int $userGroupId
     * @param int $urlId
     * @return int
     */
    public static function addUserGroupToUrl($userGroupId, $urlId)
    {
        $urlRelUserGroupTable = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_USERGROUP);
        $sql = "INSERT INTO $urlRelUserGroupTable
                SET
                usergroup_id = '".intval($userGroupId)."',
                access_url_id = ".intval($urlId);
        Database::query($sql);
        return Database::insert_id();
    }

    /**
     * @param int $categoryId
     * @param int $urlId
     * @return int
     */
    public static function addCourseCategoryToUrl($categoryId, $urlId)
    {
        $exists = self::relationUrlCourseCategoryExist($categoryId, $urlId);
        if (empty($exists)) {
            $table = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE_CATEGORY);

            $sql = "INSERT INTO $table
                    SET
                    course_category_id = '".intval($categoryId)."',
                    access_url_id = ".intval($urlId);
            Database::query($sql);

            return Database::insert_id();
        }
        return 0;
    }

    /**
     * Add a group of sessions into a group of URLs
     * @author Julio Montoya
     * @param  array of session ids
     * @param  array of url_ids
     * @return array
     * */
    public static function add_sessions_to_urls($session_list, $url_list)
    {
        $table_url_rel_session = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_SESSION);
        $result_array = array();

        if (is_array($session_list) && is_array($url_list)) {
            foreach ($url_list as $url_id) {
                foreach ($session_list as $session_id) {
                    $count = UrlManager::relation_url_session_exist($session_id, $url_id);

                    if ($count == 0) {
                        $sql    = "INSERT INTO $table_url_rel_session
		               			SET session_id = ".Database::escape_string(
                                $session_id
                            ).", access_url_id = ".Database::escape_string($url_id);
                        $result = Database::query($sql);
                        if ($result) {
                            $result_array[$url_id][$session_id] = 1;
                        } else {
                            $result_array[$url_id][$session_id] = 0;
                        }
                    }
                }
            }
        }

        return $result_array;
    }

    /**
     * Add a user into a url
     * @author Julio Montoya
     * @param  user_id
     * @param  url_id
     * @return boolean true if success
     * */
    public static function add_user_to_url($user_id, $url_id = 1)
    {
        $table_url_rel_user = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
        if (empty($url_id)) {
            $url_id = 1;
        }
        $count  = UrlManager::relation_url_user_exist($user_id, $url_id);
        $result = true;
        if (empty($count)) {
            $sql = "INSERT INTO $table_url_rel_user (user_id, access_url_id)  VALUES ('".Database::escape_string($user_id)."', '".Database::escape_string($url_id)."') ";
            $result = Database::query($sql);
        }

        return $result;
    }

    /**
     * @param string $course_code
     * @param int $url_id
     * @return resource
     */
    public static function add_course_to_url($courseId, $url_id = 1)
    {
        $table_url_rel_course = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        if (empty($url_id)) {
            $url_id = 1;
        }
        $count  = UrlManager::relation_url_course_exist($courseId, $url_id);
        $result = false;
        if (empty($count)) {
            $sql    = "INSERT INTO $table_url_rel_course
           			    SET c_id = '".Database::escape_string(
                    $courseId
                )."', access_url_id = ".Database::escape_string($url_id);
            $result = Database::query($sql);
        }

        return $result;
    }

    /**
     * Inserts a session to a URL (access_url_rel_session table)
     * @param   int     Session ID
     * @param   int     URL ID
     * @return  bool    True on success, false session already exists or insert failed
     */
    public static function add_session_to_url($session_id, $url_id = 1)
    {
        $table_url_rel_session = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_SESSION);
        if (empty($url_id)) {
            $url_id = 1;
        }
        $result = false;
        $count = UrlManager::relation_url_session_exist($session_id, $url_id);
        $session_id	= intval($session_id);
        if (empty($count) && !empty($session_id)) {
            $url_id = intval($url_id);
            $sql = "INSERT INTO $table_url_rel_session
                    SET session_id = ".Database::escape_string($session_id).", access_url_id = ".Database::escape_string($url_id);
            $result = Database::query($sql);
        }

        return $result;
    }

    /**
    * Deletes an url and user relationship
    * @author Julio Montoya
    * @param int user id
    * @param int url id
    * @return boolean true if success
    * */
    public static function delete_url_rel_user($user_id, $url_id)
    {
        $table_url_rel_user = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
        $result             = true;
        if (!empty($user_id) && !empty($url_id)) {
            $sql    = "DELETE FROM $table_url_rel_user WHERE user_id = ".Database::escape_string(
                    $user_id
                )." AND access_url_id = ".Database::escape_string($url_id);
            $result = Database::query($sql);
        }

        return $result;
    }

    /**
     * Deletes an url and course relationship
     * @author Julio Montoya
     * @param  int course id
     * @param  int url id
     * @return boolean true if success
     * */
    public static function delete_url_rel_course($courseId, $url_id)
    {
        $table_url_rel_course = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
        $sql                  = "DELETE FROM $table_url_rel_course
                                 WHERE c_id = '".Database::escape_string($courseId)."' AND access_url_id=".Database::escape_string($url_id)."  ";
        $result = Database::query($sql);

        return $result;
    }

    /**
     * Deletes an url and session relationship
     * @author Julio Montoya
     * @param  char  course code
     * @param  int url id
     * @return boolean true if success
     * */
    public static function deleteUrlRelCourseCategory($userGroupId, $urlId)
    {
        $table = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE_CATEGORY);
        $sql= "DELETE FROM $table
               WHERE course_category_id = '".intval($userGroupId)."' AND
                     access_url_id=".intval($urlId)."  ";
        $result = Database::query($sql);
        return $result;
    }



    /**
    * Deletes an url and session relationship
    * @author Julio Montoya
    * @param  char  course code
    * @param  int url id
    * @return boolean true if success
    * */
    public static function delete_url_rel_session($session_id, $url_id)
    {
        $table_url_rel_session = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_SESSION);
        $sql= "DELETE FROM $table_url_rel_session
               WHERE session_id = ".Database::escape_string($session_id)." AND access_url_id=".Database::escape_string($url_id)."  ";
        $result = Database::query($sql,'ASSOC');
        return $result;
    }

    /**
     * Updates the access_url_rel_user table  with a given user list
     * @author Julio Montoya
     * @param array user list
     * @param int access_url_id
     * */
    public static function update_urls_rel_user($user_list, $access_url_id)
    {
        $table_url_rel_user	= Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
        $sql = "SELECT user_id FROM $table_url_rel_user WHERE access_url_id = ".intval($access_url_id);
        $result = Database::query($sql);
        $existing_users = array();

        // Getting all users
        while ($row = Database::fetch_array($result)) {
            $existing_users[] = $row['user_id'];
        }

        // Adding users
        $users_added = array();
        foreach ($user_list as $user_id_to_add) {
            if (!in_array($user_id_to_add, $existing_users)) {
                $result = UrlManager::add_user_to_url($user_id_to_add, $access_url_id);
                if ($result) {
                    $users_added[] = $user_id_to_add;
                }
            }
        }

        $users_deleted = array();
        //deleting old users
        foreach ($existing_users as $user_id_to_delete) {
            if (!in_array($user_id_to_delete, $user_list)) {
                $result = UrlManager::delete_url_rel_user($user_id_to_delete, $access_url_id);
                if ($result) {
                    $users_deleted[] = $user_id_to_delete;
                }
            }
        }

        if (empty($users_added) && empty($users_deleted)) {
            return false;
        }

        return array('users_added' => $users_added, 'users_deleted' => $users_deleted);
    }

    /**
     * Updates the access_url_rel_course table  with a given user list
     * @author Julio Montoya
     * @param array user list
     * @param int access_url_id
     * */
    public static function update_urls_rel_course($course_list, $access_url_id)
    {
        $table_url_rel_course = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);

        $sql              = "SELECT c_id FROM $table_url_rel_course WHERE access_url_id=".intval($access_url_id);
        $result           = Database::query($sql);
        $existing_courses = array();

        while ($row = Database::fetch_array($result)) {
            $existing_courses[] = $row['c_id'];
        }

        //adding courses
        foreach ($course_list as $courseId) {
            if (!in_array($courseId, $existing_courses)) {
                UrlManager::add_course_to_url($courseId, $access_url_id);
                CourseManager::update_course_ranking($courseId, 0, $access_url_id);
            }
        }

        //deleting old courses
        foreach ($existing_courses as $courseId) {
            if (!in_array($courseId, $course_list)) {
                UrlManager::delete_url_rel_course($courseId, $access_url_id);
                CourseManager::update_course_ranking($courseId, 0, $access_url_id);
            }
        }
    }

    /**
     * Updates the access_url_rel_course table  with a given user list
     * @author Julio Montoya
     * @param array user list
     * @param int access_url_id
     * */
    public static function update_urls_rel_usergroup($userGroupList, $urlId)
    {
        $table = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_USERGROUP);

        $sql = "SELECT usergroup_id FROM $table WHERE access_url_id = ".intval($urlId);
        $result = Database::query($sql);
        $existingItems = array();

        while ($row = Database::fetch_array($result)){
            $existingItems[] = $row['usergroup_id'];
        }

        // Adding
        foreach ($userGroupList as $userGroupId) {
            if (!in_array($userGroupId, $existingItems)) {
                UrlManager::addUserGroupToUrl($userGroupId, $urlId);
            }
        }

        // Deleting old items
        foreach ($existingItems as $userGroupId) {
            if (!in_array($userGroupId, $userGroupList)) {
                UrlManager::delete_url_rel_usergroup($userGroupId, $urlId);
            }
        }
    }

    /**
     * Updates the access_url_rel_course_category table with a given list
     * @author Julio Montoya
     * @param array course category list
     * @param int access_url_id
     **/
    public static function updateUrlRelCourseCategory($list, $urlId)
    {
        $table = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE_CATEGORY);

        $sql = "SELECT course_category_id FROM $table WHERE access_url_id = ".intval($urlId);
        $result = Database::query($sql);
        $existingItems = array();

        while ($row = Database::fetch_array($result)){
            $existingItems[] = $row['course_category_id'];
        }

        // Adding

        foreach ($list as $id) {
            UrlManager::addCourseCategoryToUrl($id, $urlId);
            $categoryInfo = getCategoryById($id);
            $children = getChildren($categoryInfo['code']);
            if (!empty($children)) {
                foreach ($children as $category) {
                    UrlManager::addCourseCategoryToUrl($category['id'], $urlId);
                }
            }
        }

        // Deleting old items
        foreach ($existingItems as $id) {
            if (!in_array($id, $list)) {
                UrlManager::deleteUrlRelCourseCategory($id, $urlId);
                $categoryInfo = getCategoryById($id);

                $children = getChildren($categoryInfo['code']);
                if (!empty($children)) {
                    foreach ($children as $category) {
                        UrlManager::deleteUrlRelCourseCategory($category['id'], $urlId);
                    }
                }
            }
        }
    }



    /**
     * Updates the access_url_rel_session table with a given user list
     * @author Julio Montoya
     * @param array user list
     * @param int access_url_id
     * */
    public static function update_urls_rel_session($session_list, $access_url_id)
    {
        $table_session         = Database :: get_main_table(TABLE_MAIN_SESSION);
        $table_url_rel_session = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_SESSION);

        $sql               = "SELECT session_id FROM $table_url_rel_session WHERE access_url_id=".Database::escape_string(
                $access_url_id
            );
        $result            = Database::query($sql);
        $existing_sessions = array();

        while ($row = Database::fetch_array($result)) {
            $existing_sessions[] = $row['session_id'];
        }

        //adding users
        foreach ($session_list as $session) {
            if (!in_array($session, $existing_sessions)) {
                if (!empty($session) && !empty($access_url_id)) {
                    UrlManager::add_session_to_url($session, $access_url_id);
                }
            }
        }

        //deleting old users
        foreach ($existing_sessions as $existing_session) {
            if (!in_array($existing_session, $session_list)) {
                if (!empty($existing_session) && !empty($access_url_id)) {
                    UrlManager::delete_url_rel_session($existing_session, $access_url_id);
                }
            }
        }
    }

    /**
     * @param int $user_id
     * @return array
     */
    public static function get_access_url_from_user($user_id)
    {
        $table_url_rel_user = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
        $table_url          = Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
        $sql                = "SELECT url, access_url_id FROM $table_url_rel_user url_rel_user INNER JOIN $table_url u
			    ON (url_rel_user.access_url_id = u.id)
			    WHERE user_id = ".Database::escape_string($user_id);
        $result             = Database::query($sql);
        $url_list           = Database::store_result($result, 'ASSOC');

        return $url_list;
    }

    /**
     * @param $session_id
     * @return array
     */
    public static function get_access_url_from_session($session_id)
    {
        $table_url_rel_session = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_SESSION);
        $table_url  = Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
        $sql = "SELECT url, access_url_id FROM $table_url_rel_session url_rel_session INNER JOIN $table_url u
                ON (url_rel_session.access_url_id = u.id)
                WHERE session_id = ".Database::escape_string($session_id);
        $result = Database::query($sql);
        $url_list = Database::store_result($result);

        return $url_list;
    }

   /**
     * @param string $url
     * @return bool|mixed|null
     */
    public static function get_url_id($url)
    {
        $table_access_url= Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
        $sql = "SELECT id FROM $table_access_url WHERE url = '".Database::escape_string($url)."'";
        $result = Database::query($sql);
        $access_url_id = Database::result($result, 0, 0);
        return $access_url_id;
    }

    /**
     *
     * @param string $needle
     * @return XajaxResponse
     */
    public static function searchCourseCategoryAjax($needle)
    {
        $response = new XajaxResponse();
        $return = '';

        if (!empty($needle)) {
            // xajax send utf8 datas... datas in db can be non-utf8 datas
            $charset = api_get_system_encoding();
            $needle = api_convert_encoding($needle, $charset, 'utf-8');
            $needle = Database::escape_string($needle);
            // search courses where username or firstname or lastname begins likes $needle
            $sql = 'SELECT id, name FROM '.Database::get_main_table(TABLE_MAIN_CATEGORY).' u
                    WHERE name LIKE "'.$needle.'%" AND (parent_id IS NULL or parent_id = 0)
                    ORDER BY name
                    LIMIT 11';
            $result = Database::query($sql);
            $i = 0;
            while ($data = Database::fetch_array($result)) {
                $i++;
                if ($i <= 10) {
                    $return .= '<a
                    href="javascript: void(0);"
                    onclick="javascript: add_user_to_url(\''.addslashes($data['id']).'\',\''.addslashes($data['name']).' \')">'.$data['name'].' </a><br />';
                } else {
                    $return .= '...<br />';
                }
            }
        }
        $response->addAssign('ajax_list_courses', 'innerHTML', api_utf8_encode($return));
        return $response;
    }
}
