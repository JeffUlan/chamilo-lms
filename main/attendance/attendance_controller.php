<?php
/* For licensing terms, see /license.txt */

/**
 * This file contains class used like controller,
 * it should be included inside a dispatcher file (e.g: index.php)
 *
 * !!! WARNING !!! : ALL DATES IN THIS MODULE ARE STORED IN UTC !
 * DO NOT CONVERT DURING THE TRANSITION FROM CHAMILO 1.8.x TO 2.0
 *
 * @author Christian Fasanando <christian1827@gmail.com>
 * @author Julio Montoya <gugli100@gmail.com> lot of bugfixes + improvements
 * @package chamilo.attendance
 */
class AttendanceController
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->toolname = 'attendance';
        $this->view = new View($this->toolname);
    }

    /**
     * It's used for listing attendace,
     * render to attendance_list view
     * @param boolean   true for listing history (optional)
     * @param array 	message for showing by action['edit','add','delete'] (optional)
     */
    public function attendance_list($history = false, $messages = array())
    {
        $attendance = new Attendance();
        $data = array();

        // render to the view
        $this->view->set_data($data);
        $this->view->set_layout('layout');
        $this->view->set_template('attendance_list');
        $this->view->render();
    }

    /**
     * It's used for adding attendace,
     * render to attendance_add or attendance_list view
     */
    public function attendance_add()
    {
        $attendance = new Attendance();
        $data = array();

        if (strtoupper($_SERVER['REQUEST_METHOD']) == "POST") {

            if (!empty($_POST['title'])) {
                $check = Security::check_token();
                if ($check) {
                    $attendance->set_name($_POST['title']);
                    $attendance->set_description($_POST['description']);
                    $attendance->set_attendance_qualify_title($_POST['attendance_qualify_title']);
                    $attendance->set_attendance_weight($_POST['attendance_weight']);
                    $link_to_gradebook = false;
                    if ( isset($_POST['attendance_qualify_gradebook']) && $_POST['attendance_qualify_gradebook'] == 1 ) {
                        $link_to_gradebook = true;
                    }
                    $attendance->category_id = $_POST['category_id'];
                    $last_id = $attendance->attendance_add($link_to_gradebook);
                    Security::clear_token();
                }
                $param_gradebook = '';
                if (isset($_SESSION['gradebook'])) {
                    $param_gradebook = '&gradebook='.Security::remove_XSS($_SESSION['gradebook']);
                }
                //header('location:index.php?action=attendance_sheet_list&attendance_id='.$last_id.'&'.api_get_cidreq().$param_gradebook);
                header('location:index.php?action=calendar_add&attendance_id='.$last_id.'&'.api_get_cidreq().$param_gradebook);
                exit;
            } else {
                $data['error'] = true;
                $this->view->set_data($data);
                $this->view->set_layout('layout');
                $this->view->set_template('attendance_add');
                $this->view->render();
            }
        } else {
            $this->view->set_data($data);
            $this->view->set_layout('layout');
            $this->view->set_template('attendance_add');
            $this->view->render();
        }
    }

    /**
     * It's used for editing attendace,
     * render to attendance_edit or attendance_list view
     * @param int	attendance id
     */
    public function attendance_edit($attendance_id)
    {
        $attendance = new Attendance();
        $data = array();
        $attendance_id = intval($attendance_id);

        if (strtoupper($_SERVER['REQUEST_METHOD']) == "POST") {

            if (!empty($_POST['title'])) {
                $check = Security::check_token();
                if ($check) {
                    $attendance->set_name($_POST['title']);
                    $attendance->set_description($_POST['description']);
                    $attendance->set_attendance_qualify_title($_POST['attendance_qualify_title']);
                    $attendance->set_attendance_weight($_POST['attendance_weight']);

                    $attendance->category_id = $_POST['category_id'];
                    $link_to_gradebook = false;
                    if ( isset($_POST['attendance_qualify_gradebook']) && $_POST['attendance_qualify_gradebook'] == 1 ) {
                        $link_to_gradebook = true;
                    }
                    $last_id = $attendance->attendance_edit($attendance_id,$link_to_gradebook);
                    Security::clear_token();

                    $param_gradebook = '';
                    if (isset($_SESSION['gradebook'])) {
                        $param_gradebook = '&gradebook='.Security::remove_XSS($_SESSION['gradebook']);
                    }
                    header('location:index.php?action=attendance_list&'.api_get_cidreq().$param_gradebook);
                    exit;
                }
            } else {
                $data['attendance_id'] = $_POST['attendance_id'];
                $data['error'] = true;
                $this->view->set_data($data);
                $this->view->set_layout('layout');
                $this->view->set_template('attendance_edit');
                $this->view->render();
            }
        } else {
            // default values
            $attendance_data 		= $attendance->get_attendance_by_id($attendance_id);
            $data['attendance_id'] 	= $attendance_data['id'];
            $data['title'] 			= $attendance_data['name'];
            $data['description'] 	= $attendance_data['description'];
            $data['attendance_qualify_title'] = $attendance_data['attendance_qualify_title'];
            $data['attendance_weight'] = $attendance_data['attendance_weight'];

            $this->view->set_data($data);
            $this->view->set_layout('layout');
            $this->view->set_template('attendance_edit');
            $this->view->render();
        }
    }

    /**
     * It's used for delete attendaces
     * render to attendance_list view
     * @param int	attendance id
     */
    public function attendance_delete($attendance_id)
    {
        $allowDeleteAttendance = api_get_configuration_value('allow_delete_attendance');
        if ($allowDeleteAttendance == false) {
            $this->attendance_list();
            return false;
        }
        $attendance = new Attendance();
        if (!empty($attendance_id)) {
            $affected_rows = $attendance->attendance_delete($attendance_id);
        }
        if ($affected_rows) {
            $message['message_attendance_delete'] = true;
        }
        $this->attendance_list();
    }

    /**
     * It's used for make attendance visible
     * render to attendance_list view
     * @param int	attendance id
     */
    public function attendanceSetVisible($attendanceId)
    {
        $attendance = new Attendance();
        if (!empty($attendanceId)) {
            $affectedRows = $attendance->changeVisibility($attendanceId, 1);
        }
        if ($affectedRows) {
            $message['message_attendance_delete'] = true;
        }
        $this->attendance_list();
    }

    /**
     * It's used for make attendance invisible
     * render to attendance_list view
     * @param int	attendance id
     */
    public function attendanceSetInvisible($attendanceId)
    {
        $attendance = new Attendance();
        if (!empty($attendanceId)) {
            $affectedRows = $attendance->changeVisibility($attendanceId, 0);
        }
        if ($affectedRows) {
            $message['message_attendance_delete'] = true;
        }
        $this->attendance_list();
    }


    /**
     * Restores an attendance entry and fallback to attendances rendering
     * @param int	attendance id
     */
    public function attendance_restore($attendance_id)
    {
        $attendance = new Attendance();
        if (!empty($attendance_id)) {
            $affected_rows = $attendance->attendance_restore($attendance_id);
        }
        if ($affected_rows) {
            $message['message_attendance_restore'] = true;
        }
        $this->attendance_list();
    }

    /**
     * Lock or unlock an attendance
     * render to attendance_list view
     * @param string  action (lock_attendance or unlock_attendance)
     * @param int     attendance id
     * render to attendance_list view
     */
    public function lock_attendance($action, $attendance_id)
    {
        $attendance = new Attendance();
        $attendance_id = intval($attendance_id);

        if ($action == 'lock_attendance') {
            $result = $attendance->lock_attendance($attendance_id);
        } else {
            $result = $attendance->lock_attendance($attendance_id, false);
        }
        if ($result) {
            $message['message_locked_attendance'] = true;
        }
        $this->attendance_list();
    }

    public function export($id, $type = 'pdf')
    {
        $attendance = new Attendance();
        $attendance_id = intval($attendance_id);
    }

    /**
     * It's used for controlling attendance sheet (list, add),
     * render to attendance_sheet view
     * @param string $action
     * @param int	 $attendance_id
     * @param int $student_id
     * @param bool $edit
     */
    public function attendance_sheet($action, $attendance_id, $student_id = 0, $edit = true)
    {
        $attendance = new Attendance();
        $data = array();
        $data['attendance_id'] = $attendance_id;
        $groupId = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] : null;
        $data['users_in_course'] = $attendance->get_users_rel_course($attendance_id, $groupId);

        $filter_type = 'today';
        if (!empty($_REQUEST['filter'])) {
            $filter_type = $_REQUEST['filter'];
        }

        $isDrhOfCourse = CourseManager::isUserSubscribedInCourseAsDrh(
            api_get_user_id(),
            api_get_course_info()
        );

        if ($edit == true) {
            if (api_is_allowed_to_edit(null, true) || $isDrhOfCourse) {
                $data['users_presence'] = $attendance->get_users_attendance_sheet($attendance_id);
            }
        } else {
            if (!empty($student_id)) {
                $user_id = intval($student_id);
            } else {
                $user_id = api_get_user_id();
            }

            if (api_is_allowed_to_edit(null, true) ||
                api_is_coach(api_get_session_id(), api_get_course_id()) ||
                $isDrhOfCourse
            ) {
                $data['users_presence'] = $attendance->get_users_attendance_sheet($attendance_id);
            } else {
                $data['users_presence'] = $attendance->get_users_attendance_sheet($attendance_id, $user_id);
            }

            $data['faults']  = $attendance->get_faults_of_user($user_id, $attendance_id);
            $data['user_id'] = $user_id;
        }

        $data['next_attendance_calendar_id'] = $attendance->get_next_attendance_calendar_id($attendance_id);
        $data['next_attendance_calendar_datetime'] = $attendance->get_next_attendance_calendar_datetime($attendance_id);

        if (strtoupper($_SERVER['REQUEST_METHOD']) == "POST") {
            if (isset($_POST['hidden_input'])) {
                foreach ($_POST['hidden_input'] as $cal_id) {
                    $users_present = array();
                    if (isset($_POST['check_presence'][$cal_id])) {
                        $users_present = $_POST['check_presence'][$cal_id];
                    }
                    $attendance->attendance_sheet_add(
                        $cal_id,
                        $users_present,
                        $attendance_id
                    );
                }
            }

            $data['users_in_course'] = $attendance->get_users_rel_course($attendance_id, $groupId);
            $my_calendar_id = null;
            if (is_numeric($filter_type)) {
                $my_calendar_id = $filter_type;
                $filter_type = 'calendar_id';
            }
            $data['attendant_calendar'] = $attendance->get_attendance_calendar(
                $attendance_id,
                $filter_type,
                $my_calendar_id,
                $groupId
            );
            $data['attendant_calendar_all'] = $attendance->get_attendance_calendar($attendance_id);
            $data['users_presence'] = $attendance->get_users_attendance_sheet($attendance_id);
            $data['next_attendance_calendar_id'] = $attendance->get_next_attendance_calendar_id($attendance_id);
            $data['next_attendance_calendar_datetime'] = $attendance->get_next_attendance_calendar_datetime($attendance_id);
        } else {
            $data['attendant_calendar_all'] = $attendance->get_attendance_calendar($attendance_id);
            $data['attendant_calendar'] = $attendance->get_attendance_calendar($attendance_id, $filter_type, null, $groupId);
        }

        $data['edit_table'] = intval($edit);
        $data['is_locked_attendance'] = $attendance->is_locked_attendance($attendance_id);
        $this->view->set_data($data);
        $this->view->set_layout('layout');
        $this->view->set_template('attendance_sheet');
        $this->view->render();
    }

    /**
     * It's used for controlling attendance calendar (list, add, edit, delete),
     * render to attendance_calendar view
     * @param string $action (optional, by default 'calendar_list')
     * @param int	 $attendance_id (optional)
     * @param int	 $calendar_id (optional)
     */
    public function attendance_calendar($action = 'calendar_list', $attendance_id = 0, $calendar_id = 0)
    {
        $attendance = new Attendance();
        $calendar_id = intval($calendar_id);
        $data = array();
        $data['attendance_id'] = $attendance_id;
        $attendance_id = intval($attendance_id);
        $groupList = isset($_POST['groups']) ? array($_POST['groups']) : array();

        if ($action == 'calendar_add') {
            if (strtoupper($_SERVER['REQUEST_METHOD']) == "POST") {
                if (!isset($_POST['cancel'])) {
                    if (isset($_POST['repeat'])) {
                        //@todo  check this error_logs
                        $start_datetime = api_strtotime(
                            api_get_utc_datetime($_POST['date_time']), 'UTC'
                        );

                        $end_datetime = api_strtotime(api_get_utc_datetime($_POST['end_date_time'].' 23:59:59'), 'UTC');
                        $checkdate = api_is_valid_date(api_get_utc_datetime($_POST['end_date_time'].' 23:59:59'));

                        $repeat_type = $_POST['repeat_type'];
                        if (($end_datetime > $start_datetime) && $checkdate) {
                            $affected_rows = $attendance->attendance_repeat_calendar_add(
                                $attendance_id,
                                $start_datetime,
                                $end_datetime,
                                $repeat_type,
                                $groupList
                            );
                            $action = 'calendar_list';
                        } else {
                            if (!$checkdate) {
                                $data['error_checkdate'] = true;
                            } else {
                                $data['error_repeat_date'] = true;
                            }
                            $data['repeat'] = true;
                            $action = 'calendar_add';
                        }
                    } else {
                        $datetime = $_POST['date_time'];
                        $datetimezone = api_get_utc_datetime($datetime);
                        if (!empty($datetime)) {
                            $attendance->set_date_time($datetimezone);
                            $affected_rows = $attendance->attendance_calendar_add($attendance_id, $groupList);
                            $action = 'calendar_list';
                        } else {
                            $data['error_date'] = true;
                            $action = 'calendar_add';
                        }
                    }
                } else {
                    $action = 'calendar_list';
                }
            }
        } else if ($action == 'calendar_edit') {
            $data['calendar_id'] = $calendar_id;
            if (strtoupper($_SERVER['REQUEST_METHOD']) == "POST") {
                if (!isset($_POST['cancel'])) {
                    $datetime = $attendance->build_datetime_from_array($_POST['date_time']);
                    $datetimezone = api_get_utc_datetime($datetime);
                    $attendance->set_date_time($datetimezone);
                    $affected_rows = $attendance->attendance_calendar_edit($calendar_id, $attendance_id);
                    $data['calendar_id'] = 0;
                    $action = 'calendar_list';
                } else {
                    $action = 'calendar_list';
                }
            }
        } else if ($action == 'calendar_delete') {
            $affected_rows = $attendance->attendance_calendar_delete($calendar_id, $attendance_id);
            $action = 'calendar_list';
        } else if ($action == 'calendar_all_delete') {
            $affected_rows = $attendance->attendance_calendar_delete(0, $attendance_id, true);
            $action = 'calendar_list';
        }

        $data['action'] = $action;
        $data['attendance_calendar'] = $attendance->get_attendance_calendar($attendance_id);
        $data['is_locked_attendance'] = $attendance->is_locked_attendance($attendance_id);
        // render to the view
        $this->view->set_data($data);
        $this->view->set_layout('layout');
        $this->view->set_template('attendance_calendar');
        $this->view->render();
    }

    /**
     * It's used to print attendance sheet
     * @param string $action
     * @param int    $attendance_id
     */
    public function attendance_sheet_export_to_pdf($action, $attendance_id, $student_id = 0, $course_id = '')
    {
        $attendance = new Attendance();
        $courseInfo = CourseManager::get_course_information($course_id);
        $attendance->set_course_id($courseInfo['code']);
        $groupId = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] : null;
        $data_array = array();
        $data_array['attendance_id'] = $attendance_id;
        $data_array['users_in_course'] = $attendance->get_users_rel_course($attendance_id, $groupId);

        $filter_type = 'today';

        if (!empty($_REQUEST['filter'])) {
            $filter_type = $_REQUEST['filter'];
        }

        $my_calendar_id = null;
        if (is_numeric($filter_type)) {
            $my_calendar_id = $filter_type;
            $filter_type = 'calendar_id';
        }

        $data_array['attendant_calendar'] = $attendance->get_attendance_calendar($attendance_id, $filter_type, $my_calendar_id, $groupId);

        if (api_is_allowed_to_edit(null, true) || api_is_drh()) {
            $data_array['users_presence'] = $attendance->get_users_attendance_sheet($attendance_id);
        } else {
            if (!empty($student_id)) {
                $user_id = intval($student_id);
            } else {
                $user_id = api_get_user_id();
            }
            $data_array['users_presence'] = $attendance->get_users_attendance_sheet($attendance_id, $user_id);
            $data_array['faults'] = $attendance->get_faults_of_user($user_id, $attendance_id);
            $data_array['user_id'] = $user_id;
        }

        $data_array['next_attendance_calendar_id'] = $attendance->get_next_attendance_calendar_id($attendance_id);

        // Set headers pdf.
        $courseCategory = CourseManager::get_course_category($courseInfo['category_code']);
        $teacherInfo    = CourseManager::get_teacher_list_from_course_code($courseInfo['code']);
        $teacherName = null;
        foreach ($teacherInfo as $dados) {
            if ($teacherName != null)
                $teacherName = $teacherName . " / ";
            $teacherName.= $dados['firstname']." ".$dados['lastname'];
        }

        // Get data table

        $data_table = array();

        $head_table = array('#', get_lang('Name'));
        foreach ($data_array['attendant_calendar'] as $class_day) {
            $head_table[] = api_format_date($class_day['date_time'], DATE_FORMAT_NUMBER_NO_YEAR);
        }
        $data_table[] = $head_table;
        $dataClass = array();
        $max_dates_per_page = 10;
        $data_attendant_calendar = $data_array['attendant_calendar'];
        $data_users_presence = $data_array['users_presence'];
        $count = 1;

        if (!empty($data_array['users_in_course'])) {
            foreach ($data_array['users_in_course'] as $user) {
                $cols = 1;
                $result = array();
                $result['count'] = $count;
                $result['full_name'] = api_get_person_name($user['firstname'], $user['lastname']);
                foreach ($data_array['attendant_calendar'] as $class_day) {
                    if ($class_day['done_attendance'] == 1) {
                        if ($data_users_presence[$user['user_id']][$class_day['id']]['presence'] == 1)
                            $result[$class_day['id']] = get_lang('UserAttendedSymbol');
                        else
                            $result[$class_day['id']] = get_lang('UserNotAttendedSymbol');
                    } else {
                        $result[$class_day['id']] = " ";
                    }
                    $cols++;
                }
                $count++;
                $data_table[] = $result;
            }
        }
        $max_cols_per_page = 12; //10 dates + 2 name and number
        $max_dates_per_page = $max_dates_per_page_original = $max_cols_per_page - 2;//10
        $rows = count($data_table);

        if ($cols > $max_cols_per_page) {
            $number_tables = round(($cols-2)/$max_dates_per_page);
            $headers = $data_table[0];
            $all = array();
            $tables = array();
            $changed = 1;

            for ($i= 0; $i <= $rows; $i++) {
                $row = $data_table[$i];
                $key = 1;
                $max_dates_per_page = 10;
                $item = $data_table[$i];
                $count_j = 0;

                if (!empty($item)) {
                    foreach ($item as $value) {
                        if ($count_j >= $max_dates_per_page) {
                            $key++;
                            $max_dates_per_page = $max_dates_per_page_original*$key;
                            //magic hack
                            $tables[$key][$i][] = $tables[1][$i][0];
                            $tables[$key][$i][] = $tables[1][$i][1];
                        }
                        $tables[$key][$i][] = $value;
                        $count_j++;
                    }
                }
            }

            $content = null;

            if (!empty($tables)) {
                foreach ($tables as $sub_table) {
                    $content .= Export::convert_array_to_html($sub_table).'<br /><br />';
                }
            }
        } else {
            $content .= Export::convert_array_to_html($data_table, array('header_attributes' =>   array('align' => 'center')));
        }

        $params = array(
            'filename' => get_lang('Attendance').'-'.api_get_local_time(),
            'pdf_title' => $courseInfo['title'],
            'course_code' => $courseInfo['code'],
            'add_signatures' => true,
            'orientation' => 'landscape',
            'pdf_teachers' => $teacherName,
            'pdf_course_category' => $courseCategory['name'],
            'format' => 'A4-L',
            'orientation' => 'L'
        );

        Export::export_html_to_pdf($content, $params);
        exit;
    }

    /**
     * Gets attendance base in the table:
     * TABLE_STATISTIC_TRACK_E_COURSE_ACCESS
     * @param bool $showForm
     * @throws ViewException
     */
    public function getAttendanceBaseInLogin($showForm = false, $exportToPdf = true)
    {
        $table = null;
        $formToDisplay = null;
        $startDate = null;
        $endDate = null;

        $sessionId = api_get_session_id();
        if ($showForm) {
            $form = new FormValidator(
                'search',
                'post',
                api_get_self() . '?' . api_get_cidreq(
                ) . '&action=calendar_logins'
            );
            $form->addDateRangePicker('range', get_lang('Range'));
            $form->addButton('submit', get_lang('submit'));

            if ($form->validate()) {
                $values = $form->getSubmitValues();

                $startDate = api_get_utc_datetime($values['range_start']);
                $endDate = api_get_utc_datetime($values['range_end']);
            }
            $formToDisplay = $form->return_form();
        } else {
           if (!empty($sessionId)) {
               $sessionInfo = api_get_session_info($sessionId);
               $startDate = $sessionInfo['date_start'];
               $endDate = $sessionInfo['date_end'];
           }
        }

        $attendance = new Attendance();

        if ($exportToPdf) {
            $result = $attendance->exportAttendanceLogin($startDate, $endDate);
            if (empty($result)) {
                api_not_allowed(true, get_lang('NoDataAvailable'));
            }
        }
        $table = $attendance->getAttendanceLoginTable($startDate, $endDate);
        $data = array(
            'form' => $formToDisplay,
            'table' => $table
        );
        $this->view->set_data($data);
        $this->view->set_layout('layout');
        $this->view->set_template('calendar_logins');
        $this->view->render();
    }
}
