<?php
/* For licensing terms, see /license.txt */

/**
 *	This is the main database library for Chamilo.
 *	Include/require it in your code to use its functionality.
 *   Because this library contains all the basic database calls, it could be
 *   replaced by another library for say, PostgreSQL, to actually use Chamilo
 *   with another database (this is not ready yet because a lot of code still
 *   uses the MySQL database functions extensively).
 *
 *	@package chamilo.library
 * 	@todo the table constants have all to start with TABLE_
 * 		  This is because of the analogy with the tool constants TOOL_
 */

/*	CONSTANTS */

// Main database tables
define('TABLE_MAIN_COURSE', 				'course');
define('TABLE_MAIN_USER', 					'user');
define('TABLE_MAIN_CLASS', 					'class');
define('TABLE_MAIN_ADMIN', 					'admin');
define('TABLE_MAIN_COURSE_CLASS', 			'course_rel_class');
define('TABLE_MAIN_COURSE_USER', 			'course_rel_user');
define('TABLE_MAIN_CLASS_USER', 			'class_user');
define('TABLE_MAIN_CATEGORY', 				'course_category');
define('TABLE_MAIN_COURSE_MODULE', 			'course_module');
define('TABLE_MAIN_SYSTEM_ANNOUNCEMENTS',	'sys_announcement');
define('TABLE_MAIN_LANGUAGE', 				'language');
define('TABLE_MAIN_SETTINGS_OPTIONS', 		'settings_options');
define('TABLE_MAIN_SETTINGS_CURRENT', 		'settings_current');
define('TABLE_MAIN_SESSION', 				'session');
define('TABLE_MAIN_SESSION_CATEGORY', 		'session_category');
define('TABLE_MAIN_SESSION_COURSE', 		'session_rel_course');
define('TABLE_MAIN_SESSION_USER', 			'session_rel_user');
define('TABLE_MAIN_SESSION_CLASS', 			'session_rel_class');
define('TABLE_MAIN_SESSION_COURSE_USER', 	'session_rel_course_rel_user');
define('TABLE_MAIN_SHARED_SURVEY', 			'shared_survey');
define('TABLE_MAIN_SHARED_SURVEY_QUESTION', 'shared_survey_question');
define('TABLE_MAIN_SHARED_SURVEY_QUESTION_OPTION', 'shared_survey_question_option');
define('TABLE_MAIN_TEMPLATES', 				'templates');
define('TABLE_MAIN_SYSTEM_TEMPLATE', 		'system_template');
define('TABLE_MAIN_OPENID_ASSOCIATION', 	'openid_association');
define('TABLE_MAIN_COURSE_REQUEST',         'course_request');

// Gradebook
define('TABLE_MAIN_GRADEBOOK_CATEGORY', 	'gradebook_category');
define('TABLE_MAIN_GRADEBOOK_EVALUATION', 	'gradebook_evaluation');
define('TABLE_MAIN_GRADEBOOK_LINKEVAL_LOG', 'gradebook_linkeval_log');
define('TABLE_MAIN_GRADEBOOK_RESULT', 		'gradebook_result');
define('TABLE_MAIN_GRADEBOOK_RESULT_LOG', 	'gradebook_result_log');
define('TABLE_MAIN_GRADEBOOK_LINK', 		'gradebook_link');
define('TABLE_MAIN_GRADEBOOK_SCORE_DISPLAY','gradebook_score_display');
define('TABLE_MAIN_GRADEBOOK_CERTIFICATE', 	'gradebook_certificate');

//Profiling
define('TABLE_MAIN_USER_FIELD',			'user_field');
define('TABLE_MAIN_USER_FIELD_OPTIONS',	'user_field_options');
define('TABLE_MAIN_USER_FIELD_VALUES',	'user_field_values');

//User tags
define('TABLE_MAIN_TAG',				'tag');
define('TABLE_MAIN_USER_REL_TAG',		'user_rel_tag');

//User groups
define('TABLE_MAIN_GROUP',				'groups');
define('TABLE_MAIN_USER_REL_GROUP',		'group_rel_user');
define('TABLE_MAIN_GROUP_REL_TAG',		'group_rel_tag');

// Search engine
define('TABLE_MAIN_SPECIFIC_FIELD',			'specific_field');
define('TABLE_MAIN_SPECIFIC_FIELD_VALUES',	'specific_field_values');
define('TABLE_MAIN_SEARCH_ENGINE_REF',		'search_engine_ref');

// Access URLs
define('TABLE_MAIN_ACCESS_URL', 'access_url');
define('TABLE_MAIN_ACCESS_URL_REL_USER',	'access_url_rel_user');
define('TABLE_MAIN_ACCESS_URL_REL_COURSE', 	'access_url_rel_course');
define('TABLE_MAIN_ACCESS_URL_REL_SESSION', 'access_url_rel_session');

// Global calendar
define('TABLE_MAIN_SYSTEM_CALENDAR', 'sys_calendar');

// Reservation System
define('TABLE_MAIN_RESERVATION_ITEM', 			'reservation_item');
define('TABLE_MAIN_RESERVATION_RESERVATION', 	'reservation_main');
define('TABLE_MAIN_RESERVATION_SUBSCRIBTION', 	'reservation_subscription');
define('TABLE_MAIN_RESERVATION_CATEGORY', 		'reservation_category');
define('TABLE_MAIN_RESERVATION_ITEM_RIGHTS', 	'reservation_item_rights');

// Social networking
define('TABLE_MAIN_USER_REL_USER', 'user_rel_user');
define('TABLE_MAIN_USER_FRIEND_RELATION_TYPE', 'user_friend_relation_type');

// Web services
define('TABLE_MAIN_USER_API_KEY', 			'user_api_key');
define('TABLE_MAIN_COURSE_FIELD',			'course_field');
define('TABLE_MAIN_COURSE_FIELD_VALUES',	'course_field_values');
define('TABLE_MAIN_SESSION_FIELD',			'session_field');
define('TABLE_MAIN_SESSION_FIELD_VALUES',	'session_field_values');

// Message
define('TABLE_MAIN_MESSAGE', 'message');

// Term and conditions
define('TABLE_MAIN_LEGAL', 'legal');

// Dashboard blocks plugin
define('TABLE_MAIN_BLOCK', 'block');

// Statistic database tables
define('TABLE_STATISTIC_TRACK_E_LASTACCESS', 	'track_e_lastaccess');
define('TABLE_STATISTIC_TRACK_E_ACCESS', 		'track_e_access');
define('TABLE_STATISTIC_TRACK_E_LOGIN', 		'track_e_login');
define('TABLE_STATISTIC_TRACK_E_DOWNLOADS', 	'track_e_downloads');
define('TABLE_STATISTIC_TRACK_E_LINKS', 		'track_e_links');
define('TABLE_STATISTIC_TRACK_E_ONLINE', 		'track_e_online');
define('TABLE_STATISTIC_TRACK_E_HOTPOTATOES', 	'track_e_hotpotatoes');
define('TABLE_STATISTIC_TRACK_E_COURSE_ACCESS', 'track_e_course_access');
define('TABLE_STATISTIC_TRACK_E_EXERCICES', 	'track_e_exercices');
define('TABLE_STATISTIC_TRACK_E_ATTEMPT', 		'track_e_attempt');
define('TABLE_STATISTIC_TRACK_E_ATTEMPT_RECORDING', 'track_e_attempt_recording');
define('TABLE_STATISTIC_TRACK_E_DEFAULT', 		'track_e_default');
define('TABLE_STATISTIC_TRACK_E_UPLOADS', 		'track_e_uploads');
define('TABLE_STATISTIC_TRACK_E_HOTSPOT', 		'track_e_hotspot');
define('TABLE_STATISTIC_TRACK_E_ITEM_PROPERTY', 'track_e_item_property');

// SCORM database tables
define('TABLE_SCORM_MAIN', 'scorm_main');
define('TABLE_SCORM_SCO_DATA', 'scorm_sco_data');

// Course tables
define('TABLE_AGENDA',				 			'calendar_event');
define('TABLE_AGENDA_REPEAT', 					'calendar_event_repeat');
define('TABLE_AGENDA_REPEAT_NOT', 				'calendar_event_repeat_not');
define('TABLE_AGENDA_ATTACHMENT', 				'calendar_event_attachment');
define('TABLE_ANNOUNCEMENT', 					'announcement');
define('TABLE_ANNOUNCEMENT_ATTACHMENT', 		'announcement_attachment');
define('TABLE_CHAT_CONNECTED',			 		'chat_connected'); // @todo: probably no longer in use !!!
define('TABLE_COURSE_DESCRIPTION',		 		'course_description');
define('TABLE_DOCUMENT', 						'document');
define('TABLE_ITEM_PROPERTY', 					'item_property');
define('TABLE_LINK', 							'link');
define('TABLE_LINK_CATEGORY', 					'link_category');
define('TABLE_TOOL_LIST', 						'tool');
define('TABLE_TOOL_INTRO', 						'tool_intro');
define('TABLE_SCORMDOC', 						'scormdocument');
define('TABLE_STUDENT_PUBLICATION', 			'student_publication');
define('TABLE_STUDENT_PUBLICATION_ASSIGNMENT',	'student_publication_assignment');
define('CHAT_CONNECTED_TABLE',					'chat_connected');

// Course forum tables
define('TABLE_FORUM_CATEGORY', 				'forum_category');
define('TABLE_FORUM', 						'forum_forum');
define('TABLE_FORUM_THREAD',	 			'forum_thread');
define('TABLE_FORUM_POST', 					'forum_post');
define('TABLE_FORUM_ATTACHMENT', 			'forum_attachment');
define('TABLE_FORUM_MAIL_QUEUE', 			'forum_mailcue');
define('TABLE_FORUM_THREAD_QUALIFY', 		'forum_thread_qualify');
define('TABLE_FORUM_THREAD_QUALIFY_LOG', 	'forum_thread_qualify_log');
define('TABLE_FORUM_NOTIFICATION', 			'forum_notification');

// Course group tables
define('TABLE_GROUP', 			'group_info');
define('TABLE_GROUP_USER', 		'group_rel_user');
define('TABLE_GROUP_TUTOR', 	'group_rel_tutor');
define('TABLE_GROUP_CATEGORY', 	'group_category');

// Course dropbox tables
define('TABLE_DROPBOX_CATEGORY','dropbox_category');
define('TABLE_DROPBOX_FEEDBACK','dropbox_feedback');
define('TABLE_DROPBOX_POST', 	'dropbox_post');
define('TABLE_DROPBOX_FILE', 	'dropbox_file');
define('TABLE_DROPBOX_PERSON', 	'dropbox_person');

// Course quiz (or test, or exercice) tables
define('TABLE_QUIZ_QUESTION', 		'quiz_question');
define('TABLE_QUIZ_TEST', 			'quiz');
define('TABLE_QUIZ_ANSWER', 		'quiz_answer');
define('TABLE_QUIZ_TEST_QUESTION', 	'quiz_rel_question');
define('TABLE_QUIZ_QUESTION_OPTION','quiz_question_option');

// Linked resource table
define('TABLE_LINKED_RESOURCES', 'resource');

// New SCORM tables
define('TABLE_LP_MAIN', 'lp');
define('TABLE_LP_ITEM', 'lp_item');
define('TABLE_LP_VIEW', 'lp_view');
define('TABLE_LP_ITEM_VIEW', 'lp_item_view');
define('TABLE_LP_IV_INTERACTION', 'lp_iv_interaction'); // IV = Item View
define('TABLE_LP_IV_OBJECTIVE', 'lp_iv_objective'); // IV = Item View

// Smartblogs (Kevin Van Den Haute::kevin@develop-it.be)
// Permission tables
define('TABLE_PERMISSION_USER', 'permission_user');
define('TABLE_PERMISSION_TASK', 'permission_task');
define('TABLE_PERMISSION_GROUP', 'permission_group');
// Role tables
define('TABLE_ROLE', 'role');
define('TABLE_ROLE_PERMISSION', 'role_permissions');
define('TABLE_ROLE_USER', 'role_user');
define('TABLE_ROLE_GROUP', 'role_group');
// Blog tables
define('TABLE_BLOGS', 'blog');
define('TABLE_BLOGS_POSTS', 'blog_post');
define('TABLE_BLOGS_COMMENTS', 'blog_comment');
define('TABLE_BLOGS_REL_USER', 'blog_rel_user');
define('TABLE_BLOGS_TASKS', 'blog_task');
define('TABLE_BLOGS_TASKS_REL_USER', 'blog_task_rel_user');
define('TABLE_BLOGS_RATING', 'blog_rating');
define('TABLE_BLOGS_ATTACHMENT', 'blog_attachment');
define('TABLE_BLOGS_TASKS_PERMISSIONS', 'permission_task');
//end of Smartblogs

// User information tables
define('TABLE_USER_INFO', 			'userinfo_def');
define('TABLE_USER_INFO_CONTENT', 	'userinfo_content');

// Course settings table
define('TABLE_COURSE_SETTING', 'course_setting');

// Course online tables
define('TABLE_ONLINE_LINK', 	'online_link');
define('TABLE_ONLINE_CONNECTED','online_connected');

// User database
define('TABLE_PERSONAL_AGENDA', 			'personal_agenda');
define('TABLE_PERSONAL_AGENDA_REPEAT', 		'personal_agenda_repeat');
define('TABLE_PERSONAL_AGENDA_REPEAT_NOT', 	'personal_agenda_repeat_not');
define('TABLE_USER_COURSE_CATEGORY', 		'user_course_category');

// Survey
// @TODO: Are these MAIN tables or course tables?
// @TODO: Probably these constants are obsolete.
define('TABLE_MAIN_SURVEY', 		'survey');
define('TABLE_MAIN_SURVEYQUESTION', 'questions');

// Survey
define('TABLE_SURVEY', 					'survey');
define('TABLE_SURVEY_QUESTION', 		'survey_question');
define('TABLE_SURVEY_QUESTION_OPTION', 	'survey_question_option');
define('TABLE_SURVEY_INVITATION', 		'survey_invitation');
define('TABLE_SURVEY_ANSWER', 			'survey_answer');
define('TABLE_SURVEY_QUESTION_GROUP', 	'survey_group');
define('TABLE_SURVEY_REPORT', 			'survey_report');

// Wiki tables
define('TABLE_WIKI', 			'wiki');
define('TABLE_WIKI_CONF', 		'wiki_conf');
define('TABLE_WIKI_DISCUSS', 	'wiki_discuss');
define('TABLE_WIKI_MAILCUE', 	'wiki_mailcue');

// Glossary
define('TABLE_GLOSSARY', 'glossary');

// Notebook
define('TABLE_NOTEBOOK', 'notebook');

// Message
define('TABLE_MESSAGE', 'message');
define('TABLE_MESSAGE_ATTACHMENT', 'message_attachment');

// Metadata
define('TABLE_METADATA', 'metadata');

// Attendance Sheet
define('TABLE_ATTENDANCE',			'attendance');
define('TABLE_ATTENDANCE_CALENDAR', 'attendance_calendar');
define('TABLE_ATTENDANCE_SHEET',	'attendance_sheet');
define('TABLE_ATTENDANCE_RESULT', 	'attendance_result');

// Thematic
define('TABLE_THEMATIC','thematic');
define('TABLE_THEMATIC_PLAN', 'thematic_plan');
define('TABLE_THEMATIC_ADVANCE','thematic_advance');


define('TABLE_CAREER',      'career');
define('TABLE_PROMOTION',   'promotion');

define('TABLE_USERGROUP',               'usergroup');
define('TABLE_USERGROUP_REL_USER',      'usergroup_rel_user');
define('TABLE_USERGROUP_REL_COURSE',    'usergroup_rel_course');
define('TABLE_USERGROUP_REL_SESSION',   'usergroup_rel_session');



/*		DATABASE CLASS
        The class and its methods
*/

class Database {

    /*
        Accessor methods
        Usually, you won't need these directly but instead
        rely on of the get_xxx_table methods.
    */

    /**
     *	Returns the name of the main database.
     */
    public static function get_main_database() {
        global $_configuration;
        return $_configuration['main_database'];
    }

    /**
     *	Returns the name of the statistics database.
     */
    public static function get_statistic_database() {
        global $_configuration;
        return $_configuration['statistics_database'];
    }

    /**
     *	Returns the name of the SCORM database.
     *	@deprecated
     */
    public static function get_scorm_database() {
        global $_configuration;
        return $_configuration['scorm_database'];
    }

    /**
     *	Returns the name of the database where all the personal stuff of the user is stored
     */
    public static function get_user_personal_database() {
        global $_configuration;
        return $_configuration['user_personal_database'];
    }

    /**
     *	Returns the name of the current course database.
     *  @return    mixed   Glued database name of false if undefined
     */
    public static function get_current_course_database() {
        $course_info = api_get_course_info();
        if (empty($course_info['dbName'])) {
            return false;
        }
        return $course_info['dbName'];
    }

    /**
     *	Returns the glued name of the current course database.
     *  @return    mixed   Glued database name of false if undefined
     */
    public static function get_current_course_glued_database() {
        $course_info = api_get_course_info();
        if (empty($course_info['dbNameGlu'])) {
            return false;
        }
        return $course_info['dbNameGlu'];
    }

    /**
     *	The glue is the string needed between database and table.
     *	The trick is: in multiple databases, this is a period (with backticks).
     *	In single database, this can be e.g. an underscore so we just fake
     *	there are multiple databases and the code can be written independent
     *	of the single / multiple database setting.
     */
    public static function get_database_glue() {
        global $_configuration;
        return $_configuration['db_glue'];
    }

    /**
     *	Returns the database prefix.
     *	All created COURSE databases are prefixed with this string.
     *
     *	TIP: This can be convenient e.g. if you have multiple system installations
     *	on the same physical server.
     */
    public static function get_database_name_prefix() {
        global $_configuration;
        return $_configuration['db_prefix'];
    }

    /**
     *	Returns the course table prefix for single database.
     *	Not certain exactly when this is used.
     *	Do research.
     *	It's used in local.inc.php.
     */
    public static function get_course_table_prefix() {
        global $_configuration;
        return $_configuration['table_prefix'];
    }

    /*
        Table name methods
        Use these methods to get table names for queries,
        instead of constructing them yourself.

        Backticks automatically surround the result,
        e.g. `COURSE_NAME`.`link`
        so the queries can look cleaner.

        Example:
        $table = Database::get_course_table(TABLE_DOCUMENT);
        $sql_query = "SELECT * FROM $table WHERE $condition";
        $sql_result = Database::query($sql_query);
        $result = Database::fetch_array($sql_result);
    */

    /**
     * A more generic method than the other get_main_xxx_table methods,
     * This one returns the correct complete name of any table of the main database of which you pass
     * the short name as a parameter.
     * Please, define table names as constants in this library and use them
     * instead of directly using magic words in your tool code.
     *
     * @param string $short_table_name, the name of the table
     */
    public static function get_main_table($short_table_name) {
        return self::format_table_name(self::get_main_database(), $short_table_name);
    }

    /**
     * A more generic method than the older get_course_xxx_table methods,
     * This one can return the correct complete name of any course table of which you pass
     * the short name as a parameter.
     * Please, define table names as constants in this library and use them
     * instead of directly using magic words in your tool code.
     *
     * @param string $short_table_name, the name of the table
     * @param string $database_name, optional, name of the course database
     * - if you don't specify this, you work on the current course.
     */
    public static function get_course_table($short_table_name, $database_name = '') {
        return self::format_glued_course_table_name(self::fix_database_parameter($database_name), $short_table_name);
    }

    /**
     * Gets a complete course table name from a course code
     *
     * @param string $course_code
     * @param string $table the name of the table
     */
    public static function get_course_table_from_code($course_code, $table) {
        $course_table = self::get_main_table(TABLE_MAIN_COURSE);
        $course_cat_table = self::get_main_table(TABLE_MAIN_CATEGORY);
        $result = self::fetch_array(self::query(
            "SELECT $course_table.db_name, $course_cat_table.code
            FROM $course_table
                LEFT JOIN $course_cat_table
                    ON $course_table.category_code =  $course_cat_table.code
            WHERE $course_table.code = '$course_code'
            LIMIT 1"));
        return sprintf("%s.%s", $result[0], $table);
    }

    /**
     * This generic method returns the correct and complete name of any statistic table
     * of which you pass the short name as a parameter.
     * Please, define table names as constants in this library and use them
     * instead of directly using magic words in your tool code.
     *
     * @param string $short_table_name, the name of the table
     */
    public static function get_statistic_table($short_table_name) {
        return self::format_table_name(self::get_statistic_database(), $short_table_name);
    }

    /**
     * This generic method returns the correct and complete name of any scorm
     * table of which you pass the short name as a parameter. Please, define
     * table names as constants in this library and use them instead of directly
     * using magic words in your tool code.
     *
     * @param string $short_table_name, the name of the table
     */
    public static function get_scorm_table($short_table_name) {
        return self::format_table_name(self::get_scorm_database(), $short_table_name);
    }

    /**
     * This generic method returns the correct and complete name of any scorm
     * table of which you pass the short name as a parameter. Please, define
     * table names as constants in this library and use them instead of directly
     * using magic words in your tool code.
     *
     * @param string $short_table_name, the name of the table
     */
    public static function get_user_personal_table($short_table_name) {
        return self::format_table_name(self::get_user_personal_database(), $short_table_name);
    }

    public static function get_course_chat_connected_table($database_name = '') {
        return self::format_glued_course_table_name(self::fix_database_parameter($database_name), CHAT_CONNECTED_TABLE);
    }

    /*
        Query methods
        These methods execute a query and return the result(s).
    */

    /**
     *	@return a list (array) of all courses.
     * 	@todo shouldn't this be in the course.lib.php script?
     */
    public static function get_course_list() {
        $table = self::get_main_table(TABLE_MAIN_COURSE);
        return self::store_result(self::query("SELECT * FROM $table"));
    }

    /**
     *	Returns an array with all database fields for the specified course.
     *
     *	@param the real (system) code of the course (ID from inside the main course table)
     * 	@todo shouldn't this be in the course.lib.php script?
     */
    public static function get_course_info($course_code) {
        $course_code = self::escape_string($course_code);
        $table = self::get_main_table(TABLE_MAIN_COURSE);
        $result = self::generate_abstract_course_field_names(
            self::fetch_array(self::query("SELECT * FROM $table WHERE `code` = '$course_code'")));
        return $result === false ? array('db_name' => '') : $result;
    }

    /**
     *	@param $user_id (integer): the id of the user
     *	@return $user_info (array): user_id, lastname, firstname, username, email, ...
     *	@author Patrick Cool <patrick.cool@UGent.be>, expanded to get info for any user
     *	@author Roan Embrechts, first version + converted to Database API
     *	@version 30 September 2004
     *	@desc find all the information about a specified user. Without parameter this is the current user.
     * 	@todo shouldn't this be in the user.lib.php script?
     */
    public static function get_user_info_from_id($user_id = '') {
        if (empty($user_id)) {
            return $GLOBALS['_user'];
        }
        $table = self::get_main_table(TABLE_MAIN_USER);
        $user_id = self::escape_string($user_id);
        return self::generate_abstract_user_field_names(
            self::fetch_array(self::query("SELECT * FROM $table WHERE user_id = '$user_id'")));
    }

    /**
     * Returns course code from a given gradebook category's id
     * @param int  Category ID
     * @return string  Course code
     * @todo move this function in a gradebook-related library
     */
    public static function get_course_by_category($category_id) {
        $info = self::fetch_array(self::query('SELECT course_code FROM '.self::get_main_table(TABLE_MAIN_GRADEBOOK_CATEGORY).' WHERE id='.$category_id), 'ASSOC');
        return $info ? $info['course_code'] : false;
    }

    /**
     *	This method creates an abstraction layer between database field names
     *	and field names expected in code.
     *
     *	This approach helps when changing database names.
     *	It's also useful now to get rid of the 'franglais'.
     *
     *	@todo	add more array entries to abstract course info from field names
     *	@author	Roan Embrechts
     *
     * 	@todo What's the use of this method. I think this is better removed.
     * 		  There should be consistency in the variable names and the use throughout the scripts
     * 		  for the database name we should consistently use or db_name or database (db_name probably being the better one)
     */
    public static function generate_abstract_course_field_names($result_array) {
        $visual_code = isset($result_array['visual_code']) ? $result_array['visual_code'] : null;
        $code        = isset($result_array['code']) ? $result_array['code'] : null;
        $title       = isset($result_array['title']) ? $result_array['title'] : null;
        $db_name     = isset($result_array['db_name']) ? $result_array['db_name'] : null;
        $category_code = isset($result_array['category_code']) ? $result_array['category_code'] : null;
        $result_array['official_code'] = $visual_code;
        $result_array['visual_code']   = $visual_code;
        $result_array['real_code']     = $code;
        $result_array['system_code']   = $code;
        $result_array['title']         = $title;
        $result_array['database']      = $db_name;
        $result_array['faculty']       = $category_code;
        //$result_array['directory'] = $result_array['directory'];
        /*
        still to do: (info taken from local.inc.php)

        $_course['id'          ]         = $cData['cours_id'         ]; //auto-assigned integer
        $_course['name'        ]         = $cData['title'            ];
        $_course['official_code']        = $cData['visual_code'        ]; // use in echo
        $_course['sysCode'     ]         = $cData['code'             ]; // use as key in db
        $_course['path'        ]         = $cData['directory'        ]; // use as key in path
        $_course['dbName'      ]         = $cData['db_name'           ]; // use as key in db list
        $_course['dbNameGlu'   ]         = $_configuration['table_prefix'] . $cData['dbName'] . $_configuration['db_glue']; // use in all queries
        $_course['titular'     ]         = $cData['tutor_name'       ];
        $_course['language'    ]         = $cData['course_language'   ];
        $_course['extLink'     ]['url' ] = $cData['department_url'    ];
        $_course['extLink'     ]['name'] = $cData['department_name'];
        $_course['categoryCode']         = $cData['faCode'           ];
        $_course['categoryName']         = $cData['faName'           ];

        $_course['visibility'  ]         = (bool) ($cData['visibility'] == 2 || $cData['visibility'] == 3);
        $_course['registrationAllowed']  = (bool) ($cData['visibility'] == 1 || $cData['visibility'] == 2);
        */
        return $result_array;
    }

    /**
     *	This method creates an abstraction layer between database field names
     *	and field names expected in code.
     *
     *	This helps when changing database names.
     *	It's also useful now to get rid of the 'franglais'.
     *
     *	@todo add more array entries to abstract user info from field names
     *	@author Roan Embrechts
     *	@author Patrick Cool
     *
     * 	@todo what's the use of this function. I think this is better removed.
     * 		There should be consistency in the variable names and the use throughout the scripts
     */
    public static function generate_abstract_user_field_names($result_array) {
        $result_array['firstName'] 		= $result_array['firstname'];
        $result_array['lastName'] 		= $result_array['lastname'];
        $result_array['mail'] 			= $result_array['email'];
        #$result_array['picture_uri'] 	= $result_array['picture_uri'];
        #$result_array ['user_id']		= $result_array['user_id'];
        return $result_array;
    }

    /**
     * Counts the number of rows in a table
     * @param string $table The table of which the rows should be counted
     * @return int The number of rows in the given table.
     */
    public static function count_rows($table) {
        $obj = self::fetch_object(self::query("SELECT COUNT(*) AS n FROM $table"));
        return $obj->n;
    }

    /*
        An intermediate API-layer between the system and the dabase server.
    */

    /**
     * Returns the number of affected rows in the last database operation.
     * @param resource $connection (optional)	The database server connection, for detailed description see the method query().
     * @return int								Returns the number of affected rows on success, and -1 if the last query failed.
     */
    public static function affected_rows($connection = null) {
        return self::use_default_connection($connection) ? mysql_affected_rows() : mysql_affected_rows($connection);
    }

    /**
     * Closes non-persistent database connection.
     * @param resource $connection (optional)	The database server connection, for detailed description see the method query().
     * @return bool								Returns TRUE on success or FALSE on failure.
     */
    public static function close($connection = null) {
        return self::use_default_connection($connection) ? mysql_close() : mysql_close($connection);
    }

    /**
     * Opens a connection to a database server.
     * @param array $parameters (optional)		An array that contains the necessary parameters for accessing the server.
     * @return resource/boolean					Returns a database connection on success or FALSE on failure.
     * Note: Currently the array could contain MySQL-specific parameters:
     * $parameters['server'], $parameters['username'], $parameters['password'],
     * $parameters['new_link'], $parameters['client_flags'], $parameters['persistent'].
     * For details see documentation about the functions mysql_connect() and mysql_pconnect().
     * @link http://php.net/manual/en/function.mysql-connect.php
     * @link http://php.net/manual/en/function.mysql-pconnect.php
     */
    public static function connect($parameters = array()) {
        // A MySQL-specific implementation.
        if (!isset($parameters['server'])) {
            $parameters['server'] = @ini_get('mysql.default_host');
            if (empty($parameters['server'])) {
                $parameters['server'] = 'localhost:3306';
            }
        }
        if (!isset($parameters['username'])) {
            $parameters['username'] = @ini_get('mysql.default_user');
        }
        if (!isset($parameters['password'])) {
            $parameters['password'] = @ini_get('mysql.default_password');
        }
        if (!isset($parameters['new_link'])) {
            $parameters['new_link'] = false;
        }
        if (!isset($parameters['client_flags'])) {
            $parameters['client_flags'] = 0;
        }
        return $parameters['persistent']
            ? mysql_pconnect($parameters['server'], $parameters['username'], $parameters['password'], $parameters['client_flags'])
            : mysql_connect($parameters['server'], $parameters['username'], $parameters['password'], $parameters['new_link'], $parameters['client_flags']);
    }

    /**
     * Returns the error number from the last operation done on the database server.
     * @param resource $connection (optional)	The database server connection, for detailed description see the method query().
     * @return int								Returns the error number from the last database (operation, or 0 (zero) if no error occurred.
     */
    public static function errno($connection = null) {
        return self::use_default_connection($connection) ? mysql_errno() : mysql_errno($connection);
    }

    /**
     * Returns the error text from the last operation done on the database server.
     * @param resource $connection (optional)	The database server connection, for detailed description see the method query().
     * @return string							Returns the error text from the last database operation, or '' (empty string) if no error occurred.
     */
    public static function error($connection = null) {
        return self::use_default_connection($connection) ? mysql_error() : mysql_error($connection);
    }

    /**
     * Escapes a string to insert into the database as text
     * @param string							The string to escape
     * @param resource $connection (optional)	The database server connection, for detailed description see the method query().
     * @return string							The escaped string
     * @author Yannick Warnier <yannick.warnier@dokeos.com>
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     */
    public static function escape_string($string, $connection = null) {
        return get_magic_quotes_gpc()
            ? (self::use_default_connection($connection)
                ? mysql_real_escape_string(stripslashes($string))
                : mysql_real_escape_string(stripslashes($string), $connection))
            : (self::use_default_connection($connection)
                ? mysql_real_escape_string($string)
                : mysql_real_escape_string($string, $connection));
    }

    /**
     * Gets the array from a SQL result (as returned by Database::query) - help achieving database independence
     * @param resource		The result from a call to sql_query (e.g. Database::query)
     * @param string		Optional: "ASSOC","NUM" or "BOTH", as the constant used in mysql_fetch_array.
     * @return array		Array of results as returned by php
     * @author Yannick Warnier <yannick.warnier@dokeos.com>
     */
    public static function fetch_array($result, $option = 'BOTH') {
        return $option == 'ASSOC' ? mysql_fetch_array($result, MYSQL_ASSOC) : ($option == 'NUM' ? mysql_fetch_array($result, MYSQL_NUM) : mysql_fetch_array($result));
    }

    /**
     * Gets an associative array from a SQL result (as returned by Database::query).
     * This method is equivalent to calling Database::fetch_array() with 'ASSOC' value for the optional second parameter.
     * @param resource $result	The result from a call to sql_query (e.g. Database::query).
     * @return array			Returns an associative array that corresponds to the fetched row and moves the internal data pointer ahead.
     */
    public static function fetch_assoc($result) {
        return mysql_fetch_assoc($result);
    }

    /**
     * Gets the next row of the result of the SQL query (as returned by Database::query) in an object form
     * @param	resource	The result from a call to sql_query (e.g. Database::query)
     * @param	string		Optional class name to instanciate
     * @param	array		Optional array of parameters
     * @return	object		Object of class StdClass or the required class, containing the query result row
     * @author	Yannick Warnier <yannick.warnier@dokeos.com>
     */
    public static function fetch_object($result, $class = null, $params = null) {
        return !empty($class) ? (is_array($params) ? mysql_fetch_object($result, $class, $params) : mysql_fetch_object($result, $class)) : mysql_fetch_object($result);
    }

    /**
     * Gets the array from a SQL result (as returned by Database::query) - help achieving database independence
     * @param resource		The result from a call to sql_query (see Database::query()).
     * @return array		Array of results as returned by php (mysql_fetch_row)
     */
    public static function fetch_row($result) {
        return mysql_fetch_row($result);
    }

    /**
     * Frees all the memory associated with the provided result identifier.
     * @return bool		Returns TRUE on success or FALSE on failure.
     * Notes: Use this method if you are concerned about how much memory is being used for queries that return large result sets.
     * Anyway, all associated result memory is automatically freed at the end of the script's execution.
     */
    public static function free_result($result) {
        return mysql_free_result($result);
    }

    /**
     * Returns the database client library version.
     * @return strung		Returns a string that represents the client library version.
     */
    public function get_client_info() {
        return mysql_get_client_info();
    }

    /**
     * Returns a list of databases created on the server. The list may contain all of the
     * available database names or filtered database names by using a pattern.
     * @param string $pattern (optional)		A pattern for filtering database names as if it was needed for the SQL's LIKE clause, for example 'chamilo_%'.
     * @param resource $connection (optional)	The database server connection, for detailed description see the method query().
     * @return array							Returns in an array the retrieved list of database names.
     */
    public static function get_databases($pattern = '', $connection = null) {
        $result = array();
        $query_result = Database::query(!empty($pattern) ? "SHOW DATABASES LIKE '".self::escape_string($pattern, $connection)."'" : "SHOW DATABASES", $connection);
        while ($row = Database::fetch_row($query_result)) {
            $result[] = $row[0];
        }
        return $result;
    }

    /**
     * Returns a list of the fields that a given table contains. The list may contain all of the available field names or filtered field names by using a pattern.
     * By using a special option, this method is able to return an indexed list of fields' properties, where field names are keys.
     * @param string $table						This is the examined table.
     * @param string $pattern (optional)		A pattern for filtering field names as if it was needed for the SQL's LIKE clause, for example 'column_%'.
     * @param string $database (optional)		The name of the targeted database. If it is omited, the current database is assumed, see Database::select_db().
     * @param bool $including_properties (optional)	When this option is true, the returned result has the followong format:
     * 												array(field_name_1 => array(0 => property_1, 1 => property_2, ...), fieald_name_2 => array(0 => property_1, ...), ...)
     * @param resource $connection (optional)	The database server connection, for detailed description see the method query().
     * @return array							Returns in an array the retrieved list of field names.
     */
    public static function get_fields($table, $pattern = '', $database = '', $including_properties = false, $connection = null) {
        $result = array();
        $query = "SHOW COLUMNS FROM `".self::escape_string($table, $connection)."`";
        if (!empty($database)) {
            $query .= " FROM `".self::escape_string($database, $connection)."`";
        }
        if (!empty($pattern)) {
            $query .= " LIKE '".self::escape_string($pattern, $connection)."'";
        }
        $query_result = Database::query($query, $connection);
        if ($including_properties) {
            // Making an indexed list of the fields and their properties.
            while ($row = Database::fetch_row($query_result)) {
                $result[$row[0]] = $row;
            }
        } else {
            // Making a plain, flat list.
            while ($row = Database::fetch_row($query_result)) {
                $result[] = $row[0];
            }
        }
        return $result;
    }

    /**
     * Returns information about the type of the current connection and the server host name.
     * @param resource $connection (optional)	The database server connection, for detailed description see the method query().
     * @return string/boolean					Returns string data on success or FALSE on failure.
     */
    public function get_host_info($connection = null) {
        return self::use_default_connection($connection) ? mysql_get_host_info() : mysql_get_host_info($connection);
    }

    /**
     * Retrieves database client/server protocol version.
     * @param resource $connection (optional)	The database server connection, for detailed description see the method query().
     * @return int/boolean						Returns the protocol version on success or FALSE on failure.
     */
    public function get_proto_info($connection = null) {
        return self::use_default_connection($connection) ? mysql_get_proto_info() : mysql_get_proto_info($connection);
    }

    /**
     * Retrieves the database server version.
     * @param resource $connection (optional)	The database server connection, for detailed description see the method query().
     * @return string/boolean					Returns the MySQL server version on success or FALSE on failure.
     */
    public function get_server_info($connection = null) {
        return self::use_default_connection($connection) ? mysql_get_server_info() : mysql_get_server_info($connection);
    }

    /**
     * Returns a list of tables within a database. The list may contain all of the
     * available table names or filtered table names by using a pattern.
     * @param string $database (optional)		The name of the examined database. If it is omited, the current database is assumed, see Database::select_db().
     * @param string $pattern (optional)		A pattern for filtering table names as if it was needed for the SQL's LIKE clause, for example 'access_%'.
     * @param resource $connection (optional)	The database server connection, for detailed description see the method query().
     * @return array							Returns in an array the retrieved list of table names.
     */
    public static function get_tables($database = '', $pattern = '', $connection = null) {
        $result = array();
        $query = "SHOW TABLES";
        if (!empty($database)) {
            $query .= " FROM `".self::escape_string($database, $connection)."`";
        }
        if (!empty($pattern)) {
            $query .= " LIKE '".self::escape_string($pattern, $connection)."'";
        }
        $query_result = Database::query($query, $connection);
        while ($row = Database::fetch_row($query_result)) {
            $result[] = $row[0];
        }
        return $result;
    }

    /**
     * Gets the ID of the last item inserted into the database
     * @param resource $connection (optional)	The database server connection, for detailed description see the method query().
     * @return int								The last ID as returned by the DB function
     * @comment This should be updated to use ADODB at some point
     */
    public static function insert_id($connection = null) {
        return self::use_default_connection($connection) ? mysql_insert_id() : mysql_insert_id($connection);
    }

    /**
     * Gets the number of rows from the last query result - help achieving database independence
     * @param resource		The result
     * @return integer		The number of rows contained in this result
     * @author Yannick Warnier <yannick.warnier@dokeos.com>
     **/
    public static function num_rows($result) {
        return is_resource($result) ? mysql_num_rows($result) : false;
    }

    /**
     * Acts as the relative *_result() function of most DB drivers and fetches a
     * specific line and a field
     * @param	resource	The database resource to get data from
     * @param	integer		The row number
     * @param	string		Optional field name or number
     * @result	mixed		One cell of the result, or FALSE on error
     */
    public static function result($resource, $row, $field = '') {
        return self::num_rows($resource) > 0 ? (!empty($field) ? mysql_result($resource, $row, $field) : mysql_result($resource, $row)) : null;
    }

    /**
     * This method returns a resource
     * Documentation has been added by Arthur Portugal
     * Some adaptations have been implemented by Ivan Tcholakov, 2009, 2010
     * @author Olivier Brouckaert
     * @param string $query						The SQL query
     * @param resource $connection (optional)	The database server (MySQL) connection.
     * 											If it is not specified, the connection opened by mysql_connect() is assumed.
     * 											If no connection is found, the server will try to create one as if mysql_connect() was called with no arguments.
     * 											If no connection is found or established, an E_WARNING level error is generated.
     * @param string $file (optional)			On error it shows the file in which the error has been trigerred (use the "magic" constant __FILE__ as input parameter)
     * @param string $line (optional)			On error it shows the line in which the error has been trigerred (use the "magic" constant __LINE__ as input parameter)
     * @return resource							The returned result from the query
     * Note: The parameter $connection could be skipped. Here are examples of this method usage:
     * Database::query($query);
     * $result = Database::query($query);
     * Database::query($query, $connection);
     * $result = Database::query($query, $connection);
     * The following ways for calling this method are obsolete:
     * Database::query($query, __FILE__, __LINE__);
     * $result = Database::query($query, __FILE__, __LINE__);
     * Database::query($query, $connection, __FILE__, __LINE__);
     * $result = Database::query($query, $connection, __FILE__, __LINE__);
     */
    public static function query($query, $connection = null, $file = null, $line = null) {
        $use_default_connection = self::use_default_connection($connection);
        if ($use_default_connection) {
            // Let us do parameter shifting, thus the method would be similar
            // (in regard to parameter order) to the original function mysql_query().
            $line = $file;
            $file = $connection;
            $connection = null;
        }
        if (!($result = $use_default_connection ? @mysql_query($query) : @mysql_query($query, $connection))) {
            $backtrace = debug_backtrace(); // Retrieving information about the caller statement.
            if (isset($backtrace[0])) {
                $caller = & $backtrace[0];
            } else {
                $caller = array();
            }
            if (isset($backtrace[1])) {
                $owner = & $backtrace[1];
            } else {
                $owner = array();
            }
            if (empty($file)) {
                $file = $caller['file'];
            }
            if (empty($line) && $line !== false) {
                $line = $caller['line'];
            }
            $type = $owner['type'];
            $function = $owner['function'];
            $class = $owner['class'];
            $server_type = api_get_setting('server_type');
            if (!empty($line) && !empty($server_type) && $server_type != 'production') {
                $info = '<pre>' .
                    '<strong>DATABASE ERROR #'.self::errno($connection).':</strong><br /> ' .
                    self::remove_XSS(self::error($connection)) . '<br />' .
                    '<strong>QUERY       :</strong><br /> ' .
                    self::remove_XSS($query) . '<br />' .
                    '<strong>FILE        :</strong><br /> ' .
                    (empty($file) ? ' unknown ' : $file) . '<br />' .
                    '<strong>LINE        :</strong><br /> ' .
                    (empty($line) ? ' unknown ' : $line) . '<br />';
                if (empty($type)) {
                    if (!empty($function)) {
                        $info .= '<strong>FUNCTION    :</strong><br /> ' . $function;
                    }
                } else {
                    if (!empty($class) && !empty($function)) {
                        $info .= '<strong>CLASS       :</strong><br /> ' . $class . '<br />';
                        $info .= '<strong>METHOD      :</strong><br /> ' . $function;
                    }
                }
                $info .= '</pre>';
                echo $info;
            }
        }
        return $result;
    }

    /**
     * Selects a database.
     * @param string $database_name				The name of the database that is to be selected.
     * @param resource $connection (optional)	The database server connection, for detailed description see the method query().
     * @return bool								Returns TRUE on success or FALSE on failure.
     */
    public static function select_db($database_name, $connection = null) {
        return self::use_default_connection($connection) ? mysql_select_db($database_name) : mysql_select_db($database_name, $connection);
    }

    /**
     * Stores a query result into an array.
     *
     * @author Olivier Brouckaert
     * @param  resource $result - the return value of the query
     * @param  option BOTH, ASSOC, or NUM
     * @return array - the value returned by the query
     */
    public static function store_result($result, $option = 'BOTH') {
        $array = array();
        if ($result !== false) { // For isolation from database engine's behaviour.
            while ($row = self::fetch_array($result, $option)) {
                $array[] = $row;
            }
        }
        return $array;
    }

    /*
        Encodings and collations supported by MySQL database server
    */

    /**
     * Checks whether a given encoding is supported by the database server.
     * @param string $encoding	The encoding (a system conventional id, for example 'UTF-8') to be checked.
     * @return bool				Returns a boolean value as a check-result.
     * @author Ivan Tcholakov
     */
    public static function is_encoding_supported($encoding) {
        static $supported = array();
        if (!isset($supported[$encoding])) {
            $supported[$encoding] = false;
            if (strlen($db_encoding = self::to_db_encoding($encoding)) > 0) {
                if (self::num_rows(self::query("SHOW CHARACTER SET WHERE Charset =  '".self::escape_string($db_encoding)."';")) > 0) {
                    $supported[$encoding] = true;
                }
            }
        }
        return $supported[$encoding];
    }

    /**
     * Constructs a SQL clause about default character set and default collation for newly created databases and tables.
     * Example: Database::make_charset_clause('UTF-8', 'bulgarian') returns
     *  DEFAULT CHARACTER SET `utf8` DEFAULT COLLATE `utf8_general_ci`
     * @param string $encoding (optional)	The default database/table encoding (a system conventional id) to be used.
     * @param string $language (optional)	Language (a system conventional id) used for choosing language sensitive collation (if it is possible).
     * @return string						Returns the constructed SQL clause or empty string if $encoding is not correct or is not supported.
     * @author Ivan Tcholakov
     */
    public static function make_charset_clause($encoding = null, $language = null) {
        if (empty($encoding)) {
            $encoding = api_get_system_encoding();
        }
        if (empty($language)) {
            $language = api_get_interface_language();
        }
        $charset_clause = '';
        if (self::is_encoding_supported($encoding)) {
            $db_encoding = Database::to_db_encoding($encoding);
            $charset_clause .= " DEFAULT CHARACTER SET `".$db_encoding."`";
            $db_collation = Database::to_db_collation($encoding, $language);
            if (!empty($db_collation)) {
                $charset_clause .= " DEFAULT COLLATE `".$db_collation."`";
            }
        }
        return $charset_clause;
    }

    /**
     * Converts an encoding identificator to MySQL-specific encoding identifictor,
     * i.e. 'UTF-8' --> 'utf8'.
     * @param string $encoding	The conventional encoding identificator.
     * @return string			Returns the corresponding MySQL-specific encoding identificator if any, otherwise returns NULL.
     * @author Ivan Tcholakov
     */
    public static function to_db_encoding($encoding) {
        static $result = array();
        if (!isset($result[$encoding])) {
            $result[$encoding] = null;
            $encoding_map = & self::get_db_encoding_map();
            foreach ($encoding_map as $key => $value) {
                if (api_equal_encodings($encoding, $key)) {
                    $result[$encoding] = $value;
                    break;
                }
            }
        }
        return $result[$encoding];
    }

    /**
     * Converts a MySQL-specific encoding identifictor to conventional encoding identificator,
     * i.e. 'utf8' --> 'UTF-8'.
     * @param string $encoding	The MySQL-specific encoding identificator.
     * @return string			Returns the corresponding conventional encoding identificator if any, otherwise returns NULL.
     * @author Ivan Tcholakov
     */
    public static function from_db_encoding($db_encoding) {
        static $result = array();
        if (!isset($result[$db_encoding])) {
            $result[$db_encoding] = null;
            $encoding_map = & self::get_db_encoding_map();
            foreach ($encoding_map as $key => $value) {
                if (strtolower($db_encoding) == $value) {
                    $result[$db_encoding] = $key;
                    break;
                }
            }
        }
        return $result[$db_encoding];
    }

    /**
     * Chooses the default MySQL-specific collation from given encoding and language.
     * @param string $encoding				A conventional encoding id, i.e. 'UTF-8'
     * @param string $language (optional)	A conventional for the system language id, i.e. 'bulgarian'. If it is empty, the chosen collation is the default server value corresponding to the given encoding.
     * @return string						Returns a suitable default collation, for example 'utf8_general_ci', or NULL if collation was not found.
     * @author Ivan Tcholakov
     */
    public static function to_db_collation($encoding, $language = null) {
        static $result = array();
        if (!isset($result[$encoding][$language])) {
            $result[$encoding][$language] = null;
            if (self::is_encoding_supported($encoding)) {
                $db_encoding = self::to_db_encoding($encoding);
                if (!empty($language)) {
                    $lang = api_purify_language_id($language);
                    $res = self::check_db_collation($db_encoding, $lang);
                    if (empty($res)) {
                        $db_collation_map = & self::get_db_collation_map();
                        if (isset($db_collation_map[$lang])) {
                            $res = self::check_db_collation($db_encoding, $db_collation_map[$lang]);
                        }
                    }
                    if (empty($res)) {
                        $res = self::check_db_collation($db_encoding, null);
                    }
                    $result[$encoding][$language] = $res;
                } else {
                    $result[$encoding][$language] = self::check_db_collation($db_encoding, null);
                }
            }
        }
        return $result[$encoding][$language];
    }

    /*
        Private methods
        You should not access these from outside the class
        No effort is made to keep the names / results the same.
    */

    /**
     *	Glues a course database.
     *	glue format from local.inc.php.
     */
    private static function glue_course_database_name($database_name) {
        return self::get_course_table_prefix().$database_name.self::get_database_glue();
    }

    /**
     *	@param string $database_name, can be empty to use current course db
     *
     *	@return the glued parameter if it is not empty,
     *	or the current course database (glued) if the parameter is empty.
     */
    private static function fix_database_parameter($database_name) {
        if (empty($database_name)) {
            $course_info = api_get_course_info();
            return $course_info['dbNameGlu'];
        }
        return self::glue_course_database_name($database_name);
    }

    /**
     *	Structures a course database and table name to ready them
     *	for querying. The course database parameter is considered glued:
     *	e.g. COURSE001`.`
     */
    private static function format_glued_course_table_name($database_name_with_glue, $table) {
        return '`'.$database_name_with_glue.$table.'`';
    }

    /**
     *	Structures a database and table name to ready them
     *	for querying. The database parameter is considered not glued,
     *	just plain e.g. COURSE001
     */
    private static function format_table_name($database, $table) {
        return '`'.$database.'`.`'.$table.'`';
    }

    /**
     * This private method is to be used by the other methods in this class for
     * checking whether the input parameter $connection actually has been provided.
     * If the input parameter connection is not a resource or if it is not FALSE (in case of error)
     * then the default opened connection should be used by the called method.
     * @param resource/boolean $connection	The checked parameter $connection.
     * @return boolean						TRUE means that calling method should use the default connection.
     * 										FALSE means that (valid) parameter $connection has been provided and it should be used.
     */
    private static function use_default_connection($connection) {
        return !is_resource($connection) && $connection !== false;
    }

    /**
     * This private method tackles the XSS injections. It is similar to Security::remove_XSS() and works always,
     * including the time of initialization when the class Security has not been loaded yet.
     * @param string	The input variable to be filtered from XSS, in this class it is expected to be a string.
     * @return string	Returns the filtered string as a result.
     */
    private static function remove_XSS(& $var) {
        return class_exists('Security') && class_exists('HTMLPurifier') ? Security::remove_XSS($var) : api_htmlentities($var, ENT_QUOTES);
    }

    /**
     * This private method encapsulates a table with relations between
     * conventional and MuSQL-specific encoding identificators.
     * @author Ivan Tcholakov
     */
    private static function & get_db_encoding_map() {
        static $encoding_map = array(
            'ARMSCII-8'    => 'armscii8',
            'BIG5'         => 'big5',
            'BINARY'       => 'binary',
            'CP866'        => 'cp866',
            'EUC-JP'       => 'ujis',
            'EUC-KR'       => 'euckr',
            'GB2312'       => 'gb2312',
            'GBK'          => 'gbk',
            'ISO-8859-1'   => 'latin1',
            'ISO-8859-2'   => 'latin2',
            'ISO-8859-7'   => 'greek',
            'ISO-8859-8'   => 'hebrew',
            'ISO-8859-9'   => 'latin5',
            'ISO-8859-13'  => 'latin7',
            'ISO-8859-15'  => 'latin1',
            'KOI8-R'       => 'koi8r',
            'KOI8-U'       => 'koi8u',
            'SHIFT-JIS'    => 'sjis',
            'TIS-620'      => 'tis620',
            'US-ASCII'     => 'ascii',
            'UTF-8'        => 'utf8',
            'WINDOWS-1250' => 'cp1250',
            'WINDOWS-1251' => 'cp1251',
            'WINDOWS-1252' => 'latin1',
            'WINDOWS-1256' => 'cp1256',
            'WINDOWS-1257' => 'cp1257'
        );
        return $encoding_map;
    }

    /**
     * A helper language id translation table for choosing some collations.
     * @author Ivan Tcholakov
     */
    private static function & get_db_collation_map() {
        static $db_collation_map = array(
            'german' => 'german2',
            'simpl_chinese' => 'chinese',
            'trad_chinese' => 'chinese',
            'turkce' => 'turkish'
        );
        return $db_collation_map;
    }

    /**
     * Constructs a MySQL-specific collation and checks whether it is supported by the database server.
     * @param string $db_encoding	A MySQL-specific encoding id, i.e. 'utf8'
     * @param string $language		A MySQL-compatible language id, i.e. 'bulgarian'
     * @return string				Returns a suitable default collation, for example 'utf8_general_ci', or NULL if collation was not found.
     * @author Ivan Tcholakov
     */
    private static function check_db_collation($db_encoding, $language) {
        if (empty($db_encoding)) {
            return null;
        }
        if (empty($language)) {
            $result = self::fetch_array(self::query("SHOW COLLATION WHERE Charset = '".self::escape_string($db_encoding)."' AND  `Default` = 'Yes';"), 'NUM');
            return $result ? $result[0] : null;
        }
        $collation = $db_encoding.'_'.$language.'_ci';
        $query_result = self::query("SHOW COLLATION WHERE Charset = '".self::escape_string($db_encoding)."';");
        while ($result = self::fetch_array($query_result, 'NUM')) {
            if ($result[0] == $collation) {
                return $collation;
            }
        }
        return null;
    }

    /*
        New useful DB functions
    */
   
    /**
     * Experimental useful database insert 
     * @todo lot of stuff to do here
     */
    public static function insert($table_name, $attributes) {
        if (empty($attributes) || empty($table_name)) {
            return false;        	
        }
        $filtred_attributes = array();
        foreach($attributes as $key => $value) {
            $filtred_attributes[$key] = "'".self::escape_string($value)."'"; 
        }
        $params = array_keys($filtred_attributes); //@todo check if the field exists in the table we should use a describe of that table
        $values = array_values($filtred_attributes);
        if (!empty($params) && !empty($values)) {        
            $sql    = 'INSERT INTO '.$table_name.' ('.implode(',',$params).') VALUES ('.implode(',',$values).')';        
            $result = self::query($sql);
            return  self::get_last_insert_id();             
        }
        return false;
    }
    
    /**
     * Experimental useful database finder 
     * @todo lot of stuff to do here
    */
    
    public static function select($columns = '*' , $table_name,  $conditions = array(), $type_result = 'all', $option = 'ASSOC') {        
    	$conditions = self::parse_conditions($conditions); 
        
        //@todo we could do a describe here to check the columns ...       
        $clean_columns = '';
        if (is_array($columns)) {
        	$clean_columns = implode(',', $columns);
        } else {
        	if ($columns == '*') {
        		$clean_columns = '*';
        	} else {
        		$clean_columns = (string)$columns;
        	}
        }
        
             
         $sql    = "SELECT $clean_columns FROM $table_name $conditions";
      
               
        
        $result = self::query($sql);        
        $array = array();        
        //if (self::num_rows($result) > 0 ) {        
        if ($type_result == 'all') {  
            while ($row = self::fetch_array($result, $option)) {
                if (isset($row['id'])) {
                    $array[$row['id']] = $row;
                } else {
                	$array[] = $row;
                }                    
            }         
        } else {
        	$array = self::fetch_array($result, $option);
        }
        return $array;
    }
    
    /**
     * Parses WHERE/ORDER conditions i.e array('where'=>array('id = ?' =>'4'), 'order'=>'id DESC'))
     * @param   array   
     * @todo lot of stuff to do here
    */
    private function parse_conditions($conditions) {  
        if (empty($conditions)) {
        	return '';
        }        
        $return_value = '';     
        foreach ($conditions as $type_condition => $condition_data) {    
            $type_condition = strtolower($type_condition);        
             switch($type_condition) {
                case 'where':                    
                    foreach ($condition_data as $condition => $value_array) {                     
                        if (is_array($value_array)) {
                            $clean_values = array();                            
                            foreach($value_array as $item) {
                                $item = Database::escape_string($item);
                                $clean_values[]= "'$item'";
                            }
                        } else {
                            $value_array = Database::escape_string($value_array);
                            $clean_values = "'$value_array'";
                        }
                        if (!empty($condition) && !empty($clean_values)) {    
                            $condition = str_replace('?','%s', $condition); //we treat everything as string                
                            $condition = vsprintf($condition, $clean_values);
                            $where_return .= $condition;                            
                        }
                    }
                    if (!empty($where_return)) {
                        $return_value = " WHERE $where_return" ;	
                    }
                break;
                case 'order':
                    $order_array = explode(' ', $condition_data);
                    
                    if (!empty($order_array)) {
                        if (count($order_array) > 1) {
                            $order_array[0] = self::escape_string($order_array[0]);
                            if (!empty($order_array[1])) {
                                $order_array[1] = strtolower($order_array[1]);
                                $order = 'desc';
                            	if (in_array($order_array[1], array('desc', 'asc'))) {
                            		$order = $order_array[1];
                            	}
                            }
                            $return_value .= ' ORDER BY '.$order_array[0].'  '.$order;
                        }  else {
                            $return_value .= ' ORDER BY '.$order_array[0].' DESC ';
                        }
                    }
                break;
                
                case 'limit':
                    $limit_array = explode(',', $condition_data);                    
                    if (!empty($limit_array)) {
                        if (count($limit_array) > 1) {
                            $return_value .= ' LIMIT '.intval($limit_array[0]).' , '.intval($limit_array[1]);
                        }  else {
                            $return_value .= ' LIMIT '.intval($limit_array[0]);
                        }
                    }
                break;
                
            } 
        }
        return $return_value;       
    }
    
    private function parse_where_conditions($coditions){
    	return self::parse_conditions(array('where'=>$coditions));
    }
    
    /**
     * Experimental useful database update 
     * @todo lot of stuff to do here
     */
    public static function delete($table_name, $where_conditions) {
        $result = false;                
        $where_return = self::parse_where_conditions($where_conditions);
        $sql    = "DELETE FROM $table_name $where_return ";
        $result = self::query($sql);        
        $affected_rows = self::affected_rows();  	          
        //@todo should return affected_rows for 
        return $affected_rows;
    }   
   
    
    /**
     * Experimental useful database update 
     * @todo lot of stuff to do here
     */
    public static function update($table_name, $attributes, $where_conditions = array()) {
         
        if (!empty($table_name) && !empty($attributes)) {
            $update_sql = '';
            //Cleaning attributes
            $count = 1;            
            foreach ($attributes as $key=>$value) {
                $value = self::escape_string($value);
            	$update_sql .= "$key = '$value' ";
                if ($count < count($attributes)) {
                	$update_sql.=', ';
                }
                $count++;
            }
            if (!empty($update_sql)) {  
                //Parsing and cleaning the where conditions
                $where_return = self::parse_where_conditions($where_conditions);
                $sql    = "UPDATE $table_name SET $update_sql $where_return ";    
                //echo $sql; exit;
                $result = self::query($sql);
                $affected_rows = self::affected_rows(); 
                return $affected_rows;
            }                
        }
        return false;
    }
    
     /*
        DEPRECATED METHODS
    */

    /**
     * @deprecated Use api_get_language_isocode($language) instead.
     */
    public static function get_language_isocode($language) {
        return api_get_language_isocode($language);
    }

    /**
     * @deprecated Use Database::insert_id() instead.
     */
    public static function get_last_insert_id() {
        return mysql_insert_id();
    }
    
}
//end class Database