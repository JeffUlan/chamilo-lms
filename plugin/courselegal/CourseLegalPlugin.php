<?php
/* For licensing terms, see /license.txt */

/**
 * Class CourseLegalPlugin
 */
class CourseLegalPlugin extends Plugin
{
    public $isCoursePlugin = true;

    // When creating a new course this settings are added to the course
    public $course_settings = array(
        array(
            'name' => 'courselegal',
            'type' => 'text'
        )
    );

    /**
     * @return CourseLegalPlugin
     */
    static function create()
    {
        static $result = null;
        return $result ? $result : $result = new self();
    }

    /**
     *
     */
    protected function __construct()
    {
        parent::__construct(
            '0.1',
            'Julio Montoya',
            array(
                'tool_enable' => 'boolean'
            )
        );
    }

    /**
     * @return string
     */
    public function getTeacherLink()
    {
        $link = null;
        if (api_is_allowed_to_edit()) {
            $url = api_get_path(WEB_PLUGIN_PATH).'courselegal/start.php?'.api_get_cidreq();
            $link = Display::url(
                get_lang('Legal'),
                $url,
                array('class' => 'btn')
            );
        }

        return $link;
    }

    /**
     * @param int $userId
     * @param int $courseId
     * @param int $sessionId
     *
     * @return array
     */
    public function getUserAcceptedLegal($userId, $courseId, $sessionId)
    {
        $userId = intval($userId);
        $courseId = intval($courseId);
        $sessionId = intval($sessionId);

        $table = Database::get_main_table('session_rel_course_rel_user_legal');
        $sql = "SELECT *
                FROM $table
                WHERE user_id = $userId AND c_id = $courseId AND session_id = $sessionId";
        $result = Database::query($sql);
        $data = array();
        if (Database::num_rows($result) > 0 ) {
            $data = Database::fetch_array($result, 'ASSOC');
        }

        return $data;
    }

    /**
     * @param int $userId
     * @param string $courseCode
     * @param int $sessionId
     *
     * @return bool
     */
    public function isUserAcceptedLegal($userId, $courseCode, $sessionId)
    {
        $courseInfo = api_get_course_info($courseCode);
        $courseId = $courseInfo['real_id'];
        $result = $this->getUserAcceptedLegal($userId, $courseId, $sessionId);

        if (!empty($result)) {
            if ($result['mail_agreement'] == 1 &&
                $result['web_agreement'] == 1
            ) {
                return true;
            }
        }

        return false;
    }

    /**
 * @param int $userId
 * @param int $courseCode
 * @param int $sessionId
 *
 * @return bool
 */
    public function saveUserLegal($userId, $courseCode, $sessionId)
    {
        $courseInfo = api_get_course_info($courseCode);
        $courseId = $courseInfo['real_id'];
        $data = $this->getUserAcceptedLegal($userId, $courseId, $sessionId);

        $id = false;
        if (empty($data)) {
            $table = Database::get_main_table(
                'session_rel_course_rel_user_legal'
            );
            $uniqueId = api_get_unique_id();
            $values = array(
                'user_id' => $userId,
                'c_id' => $courseId,
                'session_id' => $sessionId,
                'web_agreement' => 1,
                'web_agreement_date' => api_get_utc_datetime(),
                'mail_agreement_link' => $uniqueId
            );
            $id = Database::insert($table, $values);

            $url = api_get_path(WEB_CODE_PATH).'course_info/legal.php?web_agreement_link='.$uniqueId.'&course_code='.Security::remove_XSS($courseCode).'&session_id='.$sessionId;
            $courseUrl = Display::url($url, $url);

            $subject = get_lang("MailAgreement");
            $message = sprintf(get_lang("MailAgreementWasSentWithClickX"), $courseUrl);
            MessageManager::send_message_simple($userId, $subject, $message);
        }

        return $id;
    }

    /**
     * @param string $link
     * @param int $userId
     * @param int $courseId
     * @param int $sessionId
     *
     * @return bool
     */
    public function saveUserMailLegal($link, $userId, $courseId, $sessionId)
    {
        $data = $this->getUserAcceptedLegal($userId, $courseId, $sessionId);

        if (empty($data)) {
            return null;
        }

        if ($data['mail_agreement_link'] == $link) {
            $table = Database::get_main_table('session_rel_course_rel_user_legal');
            $id = $data['id'];
            $values = array(
                'mail_agreement' => 1,
                'mail_agreement_date' => api_get_utc_datetime()
            );
            Database::update($table, $values, array('id = ?' => array($id)));
        }
    }

    /**
     * @param int $courseId
     * @param int $sessionId
     */
    public function warnUsersByEmail($courseId, $sessionId)
    {
        $courseInfo = api_get_course_info_by_id($courseId);
        $courseCode = $courseInfo['code'];

        if (empty($sessionId)) {
            $students = CourseManager::get_student_list_from_course_code($courseCode, false);
        } else {
            $students = CourseManager::get_student_list_from_course_code($courseCode, true, $sessionId);
        }

        $url = api_get_course_url($courseCode, $sessionId);
        $url = Display::url($url, $url);

        $subject = get_lang("AgreementUpdated");
        $message = sprintf(get_lang("AgreementWasUpdatedClickHere"), $url);

        if (!empty($students)) {
            foreach ($students as $student) {
                $userId = $student['user_id'];
                MessageManager::send_message_simple($userId, $subject, $message);
            }
        }
    }

    /**
     * @param int $courseId
     * @param int $sessionId
     */
    public function removePreviousAgreements($courseId, $sessionId)
    {
        $table = Database::get_main_table('session_rel_course_rel_user_legal');
        $sessionId = intval($sessionId);
        $courseId = intval($courseId);
        $sql = "DELETE FROM $table
                WHERE c_id = '$courseId' AND session_id = $sessionId ";
        Database::query($sql);
    }

    /**
     * @param array $values
     * @param array  $file $_FILES['uploaded_file']
     */
    public function save($values, $file = array(), $deleteFile = false)
    {
        $table = Database::get_main_table('session_rel_course_legal');

        $courseId = $values['c_id'];
        $sessionId = $values['session_id'];

        $conditions = array(
            'c_id' => $courseId,
            'session_id' => $sessionId,
        );

        $legalData = $this->getData($courseId, $sessionId);

        $conditions['content'] = $values['content'];

        $course = api_get_course_info();
        $coursePath = api_get_path(SYS_COURSE_PATH).$course['directory'].'/courselegal';

        if (!is_dir($coursePath)) {
            mkdir($coursePath, api_get_permissions_for_new_directories());
        }

        require_once api_get_path(LIBRARY_PATH) . 'fileUpload.lib.php';
        $uploadOk = process_uploaded_file($file, false);

        $fileName = null;
        if ($uploadOk) {
            $uploadResult = handle_uploaded_document(
                $course,
                $file,
                $coursePath,
                '/',
                api_get_user_id(),
                api_get_group_id(),
                null,
                false,
                false,
                false,
                true
            );

            if ($uploadResult) {
                $fileName = basename($uploadResult);
                // Delete old one if exists.
                if ($legalData) {
                    if (!empty($legalData['filename'])) {
                        $fileToDelete = $coursePath.'/'.$legalData['filename'];
                        if (file_exists($fileToDelete)) {
                            unlink($fileToDelete);
                        }
                    }
                }
            }
        }
        $conditions['filename'] = $fileName;

        if (empty($legalData)) {
            $id = Database::insert($table, $conditions);
        } else {
            $id = $legalData['id'];
            Database::update(
                $table,
                array(
                    'content' => $values['content'],
                    'filename' => $fileName
                ),
                array('id = ? ' => $id)
            );
        }

        if ($deleteFile) {
            Database::update(
                $table,
                array('filename' => ''),
                array('id = ? ' => $id)
            );
            if (!empty($legalData['filename'])) {
                $fileToDelete = $coursePath . '/' . $legalData['filename'];
                if (file_exists($fileToDelete)) {
                    unlink($fileToDelete);
                }
            }
        }

        if (isset($values['remove_previous_agreements']) && !empty($values['remove_previous_agreements'])) {
            $this->removePreviousAgreements($courseId, $sessionId);
        }

        if (isset($values['warn_users_by_email']) && !empty($values['warn_users_by_email'])) {
            $this->warnUsersByEmail($courseId, $sessionId);
        }
    }

    /**
     * @param int $courseId
     * @param int $sessionId
     * @return array|mixed
     */
    public function getData($courseId, $sessionId)
    {
        $table = Database::get_main_table('session_rel_course_legal');
        $conditions = array(
            'c_id  = ? AND session_id = ? ' => array(
                $courseId,
                $sessionId
            )
        );

        $result = Database::select('*', $table, array('where' => $conditions));
        $legalData = isset($result) && !empty($result) ? current($result) : array();

        return $legalData;
    }

    /**
     * @param int $courseId
     * @param int $sessionId
     *
     * @return string
     */
    public function getCurrentFile($courseId, $sessionId)
    {
        $data = $this->getData($courseId, $sessionId);

        if (isset($data['filename']) && !empty($data['filename'])) {
            $course = api_get_course_info_by_id($courseId);

            $coursePath = api_get_path(SYS_COURSE_PATH).$course['directory'].'/courselegal';
            $file = $coursePath.'/'.$data['filename'];

            if (file_exists($file)) {
                return Display::url(
                    $data['filename'],
                    api_get_path(WEB_COURSE_PATH).$course['directory'].'/courselegal/'.$data['filename']
                );
            }
        }
    }

    public function install()
    {
        $table = Database::get_main_table('session_rel_course_legal');
        $sql = "CREATE TABLE IF NOT EXISTS $table (
                    id int PRIMARY KEY AUTO_INCREMENT,
                    c_id int,
                    session_id int,
                    content text,
                    filename varchar(255)
                )";
        Database::query($sql);

        $table = Database::get_main_table('session_rel_course_rel_user_legal');

        $sql = "CREATE TABLE IF NOT EXISTS $table (
                    id int PRIMARY KEY AUTO_INCREMENT,
                    user_id int,
                    c_id int,
                    session_id int,
                    web_agreement varchar(255),
                    web_agreement_date datetime,
                    mail_agreement varchar(255),
                    mail_agreement_date datetime,
                    mail_agreement_link varchar(255)
                )";
        Database::query($sql);

        // Installing course settings
        $this->install_course_fields_in_all_courses(false);
    }

    public function uninstall()
    {
        $table = Database::get_main_table('session_rel_course_legal');
        $sql = "DROP TABLE $table ";
        Database::query($sql);

        $table = Database::get_main_table('session_rel_course_rel_user_legal');
        $sql = "DROP TABLE $table ";
        Database::query($sql);

        // Deleting course settings
        $this->uninstall_course_fields_in_all_courses($this->course_settings);
    }
}
