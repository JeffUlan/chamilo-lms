<?php

/* For licensing terms, see /license.txt */

use Chamilo\CoreBundle\Framework\Container;
use Chamilo\CourseBundle\Entity\CLpCategory;
use Chamilo\CourseBundle\Entity\CQuiz;
use ChamiloSession as Session;

if (!isset($_GET['course'])) {
    $cidReset = true;
}

require_once __DIR__.'/../inc/global.inc.php';
require_once '../work/work.lib.php';

api_block_anonymous_users();

//$htmlHeadXtra[] = '<script type="text/javascript" src="'.api_get_path(WEB_PUBLIC_PATH).'assets/jquery.easy-pie-chart/dist/jquery.easypiechart.js"></script>';

$export = isset($_GET['export']) ? $_GET['export'] : false;
$sessionId = isset($_GET['sid']) ? (int) $_GET['sid'] : 0;
$origin = api_get_origin();
$courseId = isset($_GET['cid']) ? (int) $_GET['cid'] : '';
$courseInfo = api_get_course_info_by_id($courseId);
$courseCode = '';
if ($courseInfo) {
    $courseCode = $courseInfo['code'];
}
$studentId = isset($_GET['student']) ? (int) $_GET['student'] : 0;
$coachId = isset($_GET['id_coach']) ? (int) $_GET['id_coach'] : 0;
$details = isset($_GET['details']) ? Security::remove_XSS($_GET['details']) : '';
$currentUrl = api_get_self().'?student='.$studentId.'&course='.$courseCode.'&id_session='.$sessionId
    .'&origin='.$origin.'&details='.$details.'&cid='.$courseId;
$allowMessages = api_get_configuration_value('private_messages_about_user');
$workingTime = api_get_configuration_value('considered_working_time');
$workingTimeEdit = api_get_configuration_value('allow_working_time_edition');

$allowToQualify = api_is_allowed_to_edit(null, true) ||
    api_is_course_tutor() ||
    api_is_session_admin() ||
    api_is_drh() ||
    api_is_student_boss();

$allowedToTrackUser =
    api_is_platform_admin(true, true) ||
    api_is_allowed_to_edit(null, true) ||
    api_is_session_admin() ||
    api_is_drh() ||
    api_is_student_boss() ||
    api_is_course_admin() ||
    api_is_teacher();

if (false === $allowedToTrackUser && !empty($courseInfo)) {
    if (empty($sessionId)) {
        $isTeacher = CourseManager::isCourseTeacher(
            api_get_user_id(),
            $courseInfo['code']
        );

        if ($isTeacher) {
            $allowedToTrackUser = true;
        } else {
            // Check if the user is tutor of the course
            $userCourseStatus = CourseManager::get_tutor_in_course_status(
                api_get_user_id(),
                $courseInfo['real_id']
            );

            if ($userCourseStatus == 1) {
                $allowedToTrackUser = true;
            }
        }
    } else {
        $coach = api_is_coach($sessionId, $courseInfo['real_id']);

        if ($coach) {
            $allowedToTrackUser = true;
        }
    }
}

if (!$allowedToTrackUser) {
    api_not_allowed(true);
}
if (empty($studentId)) {
    api_not_allowed(true);
}

$user_info = $userInfo = api_get_user_info($studentId);

if (empty($user_info)) {
    api_not_allowed(true);
}

if ($export) {
    ob_start();
}
$csv_content = [];
$from_myspace = false;
$this_section = SECTION_COURSES;
if (isset($_GET['from']) && 'myspace' === $_GET['from']) {
    $from_myspace = true;
    $this_section = SECTION_TRACKING;
}

$nameTools = get_lang('Learner details');

if (!empty($details)) {
    if ('user_course' === $origin) {
        if (empty($cidReq)) {
            $interbreadcrumb[] = [
                'url' => $courseInfo['course_public_url'],
                'name' => $courseInfo['title'],
            ];
        }
        $interbreadcrumb[] = [
            'url' => '../user/user.php?cidReq='.$courseCode,
            'name' => get_lang('Users'),
        ];
    } else {
        if ('tracking_course' === $origin) {
            $interbreadcrumb[] = [
                'url' => '../tracking/courseLog.php?cidReq='.$courseCode.'&id_session='.api_get_session_id(),
                'name' => get_lang('Reporting'),
            ];
        } else {
            if ('resume_session' === $origin) {
                $interbreadcrumb[] = [
                    'url' => "../session/session_list.php",
                    'name' => get_lang('Session list'),
                ];
                $interbreadcrumb[] = [
                    'url' => '../session/resume_session.php?id_session='.$sessionId,
                    'name' => get_lang('Session overview'),
                ];
            } else {
                $interbreadcrumb[] = [
                    'url' => api_is_student_boss() ? '#' : 'index.php',
                    'name' => get_lang('Reporting'),
                ];
                if (!empty($coachId)) {
                    $interbreadcrumb[] = [
                        'url' => 'student.php?id_coach='.$coachId,
                        'name' => get_lang('Learners of trainer'),
                    ];
                    $interbreadcrumb[] = [
                        'url' => 'myStudents.php?student='.$studentId.'&id_coach='.$coachId,
                        'name' => get_lang('Learner details'),
                    ];
                } else {
                    $interbreadcrumb[] = [
                        'url' => 'student.php',
                        'name' => get_lang('My learners'),
                    ];
                    $interbreadcrumb[] = [
                        'url' => 'myStudents.php?student='.$studentId,
                        'name' => get_lang('Learner details'),
                    ];
                }
            }
        }
    }
    $nameTools = get_lang('Learner details in course');
} else {
    if ('resume_session' === $origin) {
        $interbreadcrumb[] = [
            'url' => '../session/session_list.php',
            'name' => get_lang('Session list'),
        ];
        if (!empty($sessionId)) {
            $interbreadcrumb[] = [
                'url' => '../session/resume_session.php?id_session='.$sessionId,
                'name' => get_lang('Session overview'),
            ];
        }
    } elseif ('teacher_details' === $origin) {
        $this_section = SECTION_TRACKING;
        $interbreadcrumb[] = ['url' => 'index.php', 'name' => get_lang('Reporting')];
        $interbreadcrumb[] = ['url' => 'teachers.php', 'name' => get_lang('Trainers')];
        $nameTools = $userInfo['complete_name'];
    } else {
        $interbreadcrumb[] = [
            'url' => api_is_student_boss() ? '#' : 'index.php',
            'name' => get_lang('Reporting'),
        ];
        if (!empty($coachId)) {
            if ($sessionId) {
                $interbreadcrumb[] = [
                    'url' => 'student.php?id_coach='.$coachId.'&id_session='.$sessionId,
                    'name' => get_lang('Learners of trainer'),
                ];
            } else {
                $interbreadcrumb[] = [
                    'url' => 'student.php?id_coach='.$coachId,
                    'name' => get_lang('Learners of trainer'),
                ];
            }
        } else {
            $interbreadcrumb[] = [
                'url' => 'student.php',
                'name' => get_lang('My learners'),
            ];
        }
    }
}

// Database Table Definitions
$tbl_course_user = Database::get_main_table(TABLE_MAIN_COURSE_USER);
$tbl_stats_exercices = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCISES);
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'add_work_time':
        if (false === $workingTimeEdit) {
            api_not_allowed(true);
        }
        $workingTime = isset($_GET['time']) ? $_GET['time'] : '';
        $workId = isset($_GET['work_id']) ? $_GET['work_id'] : '';
        Event::eventAddVirtualCourseTime($courseInfo['real_id'], $studentId, $sessionId, $workingTime, $workId);
        Display::addFlash(Display::return_message(get_lang('Updated')));

        header('Location: '.$currentUrl);
        exit;
    case 'remove_work_time':
        if (false === $workingTimeEdit) {
            api_not_allowed(true);
        }
        $workingTime = isset($_GET['time']) ? $_GET['time'] : '';
        $workId = isset($_GET['work_id']) ? $_GET['work_id'] : '';
        Event::eventRemoveVirtualCourseTime($courseInfo['real_id'], $studentId, $sessionId, $workingTime, $workId);

        Display::addFlash(Display::return_message(get_lang('Updated')));

        header('Location: '.$currentUrl);
        exit;
        break;
    case 'export_to_pdf':
        $sessionToExport = $sId = isset($_GET['session_to_export']) ? (int) $_GET['session_to_export'] : 0;
        $sessionInfo = api_get_session_info($sessionToExport);
        if (empty($sessionInfo)) {
            api_not_allowed(true);
        }
        $courses = Tracking::get_courses_list_from_session($sessionToExport);
        $timeSpent = 0;
        $numberVisits = 0;
        $table = Database::get_main_table(TABLE_STATISTIC_TRACK_E_COURSE_ACCESS);
        $progress = 0;
        $timeSpentPerCourse = [];
        $progressPerCourse = [];
        foreach ($courses as $course) {
            $courseId = $course['c_id'];
            $courseTimeSpent = Tracking::get_time_spent_on_the_course($studentId, $courseId, $sessionToExport);
            $timeSpentPerCourse[$courseId] = $courseTimeSpent;
            $timeSpent += $courseTimeSpent;
            $sql = "SELECT DISTINCT count(course_access_id) as count
                    FROM $table
                    WHERE
                        c_id = $courseId AND
                        session_id = $sessionToExport AND
                        user_id = $studentId";
            $result = Database::query($sql);
            $row = Database::fetch_array($result);
            $numberVisits += $row['count'];
            $courseProgress = Tracking::get_avg_student_progress($studentId, $course['code'], [], $sessionToExport);
            $progressPerCourse[$courseId] = $courseProgress;
            $progress += $courseProgress;
        }

        $average = round($progress / count($courses), 1);
        $average = empty($average) ? '0%' : $average.'%';

        $first = Tracking::get_first_connection_date($studentId);
        $last = Tracking::get_last_connection_date($studentId);

        $table = new HTML_Table(['class' => 'data_table']);
        $column = 0;
        $row = 0;
        $headers = [
            get_lang('Time spent'),
            get_lang('Number of visits'),
            get_lang('Global progress'),
            get_lang('First connection'),
            get_lang('Last connexion date'),
        ];

        foreach ($headers as $header) {
            $table->setHeaderContents($row, $column, $header);
            $column++;
        }
        $table->setCellContents(1, 0, api_time_to_hms($timeSpent));
        $table->setCellContents(1, 1, $numberVisits);
        $table->setCellContents(1, 2, $average);
        $table->setCellContents(1, 3, $first);
        $table->setCellContents(1, 4, $last);

        $courseTable = '';

        if (!empty($courses)) {
            $courseTable .= '<table class="data_table">';
            $courseTable .= '<thead>';
            $courseTable .= '<tr>
                    <th>'.get_lang('Formation unit').'</th>
                    <th>'.get_lang('Connection time').'</th>
                    <th>'.get_lang('Progress').'</th>
                    <th>'.get_lang('Score').'</th>
                </tr>';
            $courseTable .= '</thead>';
            $courseTable .= '<tbody>';

            $totalCourseTime = 0;
            $totalAttendance = [0, 0];
            $totalScore = 0;
            $totalProgress = 0;
            $gradeBookTotal = [0, 0];
            $totalEvaluations = '0/0 (0%)';
            $totalCourses = count($courses);
            $scoreDisplay = ScoreDisplay::instance();

            foreach ($courses as $course) {
                $courseId = $course['c_id'];
                $courseInfoItem = api_get_course_info_by_id($courseId);
                $courseId = $courseInfoItem['real_id'];
                $courseCodeItem = $courseInfoItem['code'];

                $isSubscribed = CourseManager::is_user_subscribed_in_course(
                    $studentId,
                    $courseCodeItem,
                    true,
                    $sId
                );

                if ($isSubscribed) {
                    $timeInSeconds = $timeSpentPerCourse[$courseId];
                    $totalCourseTime += $timeInSeconds;
                    $time_spent_on_course = api_time_to_hms($timeInSeconds);
                    $progress = $progressPerCourse[$courseId];
                    $totalProgress += $progress;

                    $bestScore = Tracking::get_avg_student_score(
                        $studentId,
                        $courseCodeItem,
                        [],
                        $sId,
                        false,
                        false,
                        true
                    );

                    if (is_numeric($bestScore)) {
                        $totalScore += $bestScore;
                    }

                    $progress = empty($progress) ? '0%' : $progress.'%';
                    $score = empty($bestScore) ? '0%' : $bestScore.'%';

                    $courseTable .= '<tr>
                        <td>
                            <a href="'.$courseInfoItem['course_public_url'].'?id_session='.$sId.'">'.
                        $courseInfoItem['title'].'</a>
                        </td>
                        <td >'.$time_spent_on_course.'</td>
                        <td >'.$progress.'</td>
                        <td >'.$score.'</td>';
                    $courseTable .= '</tr>';
                }
            }

            $totalAttendanceFormatted = $scoreDisplay->display_score($totalAttendance);
            $totalScoreFormatted = $scoreDisplay->display_score([$totalScore / $totalCourses, 100], SCORE_AVERAGE);
            $totalProgressFormatted = $scoreDisplay->display_score(
                [$totalProgress / $totalCourses, 100],
                SCORE_AVERAGE
            );
            $totalEvaluations = $scoreDisplay->display_score($gradeBookTotal);
            $totalTimeFormatted = api_time_to_hms($totalCourseTime);
            $courseTable .= '
            <tr>
                <th>'.get_lang('Total').'</th>
                <th>'.$totalTimeFormatted.'</th>
                <th>'.$totalProgressFormatted.'</th>
                <th>'.$totalScoreFormatted.'</th>
            </tr>';
            $courseTable .= '</tbody></table>';
        }

        $tpl = new Template('', false, false, false, true, false, false);
        $tpl->assign('title', get_lang('Attestation of attendance'));
        $tpl->assign('session_title', $sessionInfo['name']);
        $tpl->assign('student', $userInfo['complete_name']);
        $tpl->assign('table_progress', $table->toHtml());
        $tpl->assign(
            'subtitle',
            sprintf(
                get_lang('In session %s, you had the following results'),
                $sessionInfo['name']
            )
        );
        $tpl->assign('table_course', $courseTable);
        $content = $tpl->fetch($tpl->get_template('my_space/pdf_export_student.tpl'));

        $params = [
            'pdf_title' => get_lang('Resume'),
            'session_info' => $sessionInfo,
            'course_info' => '',
            'pdf_date' => '',
            'student_info' => $userInfo,
            'show_grade_generated_date' => true,
            'show_real_course_teachers' => false,
            'show_teacher_as_myself' => false,
            'orientation' => 'P',
        ];
        $pdf = new PDF('A4', $params['orientation'], $params);

        try {
            $pdf->setBackground($tpl->theme);
            @$pdf->content_to_pdf(
                $content,
                '',
                '',
                null,
                'D',
                false,
                null,
                false,
                true,
                false
            );
        } catch (MpdfException $e) {
            error_log($e);
        }
        exit;
        break;
    case 'export_one_session_row':
        $sessionToExport = isset($_GET['session_to_export']) ? (int) $_GET['session_to_export'] : 0;
        $exportList = Session::read('export_course_list');

        if (isset($exportList[$sessionToExport])) {
            $dataToExport = $exportList[$sessionToExport];
            $title = '';
            if (!empty($sessionToExport)) {
                $sessionInfo = api_get_session_info($sessionToExport);
                $title .= '_'.$sessionInfo['name'];
            }

            $fileName = 'report'.$title.'_'.$userInfo['complete_name'];
            switch ($export) {
                case 'csv':
                    Export::arrayToCsv($dataToExport, $fileName);
                    break;
                case 'xls':
                    Export::arrayToXls($dataToExport, $fileName);
                    break;
            }
        } else {
            api_not_allowed(true);
        }
        break;
    case 'send_message':
        if (true === $allowMessages) {
            $subject = isset($_POST['subject']) ? $_POST['subject'] : '';
            $message = isset($_POST['message']) ? $_POST['message'] : '';

            if (!empty($subject) && !empty($message)) {
                $currentUserInfo = api_get_user_info();
                MessageManager::sendMessageAboutUser(
                    $userInfo,
                    $currentUserInfo,
                    $subject,
                    $message
                );

                // Send also message to all student bosses
                $bossList = UserManager::getStudentBossList($studentId);

                if (!empty($bossList)) {
                    $url = api_get_path(WEB_CODE_PATH).'mySpace/myStudents.php?student='.$studentId;
                    $link = Display::url($url, $url);

                    foreach ($bossList as $boss) {
                        MessageManager::send_message_simple(
                            $boss['boss_id'],
                            sprintf(get_lang('Follow up message about student %s'), $userInfo['complete_name']),
                            sprintf(
                                get_lang('Hi,<br/><br/>'),
                                $currentUserInfo['complete_name'],
                                $userInfo['complete_name'],
                                $link
                            )
                        );
                    }
                }

                Display::addFlash(Display::return_message(get_lang('Message Sent')));
            } else {
                Display::addFlash(Display::return_message(get_lang('AllFieldsRequired'), 'warning'));
            }

            header('Location: '.$currentUrl);
            exit;
        }
        break;
    case 'generate_certificate':
        // Delete old certificate
        $myCertificate = GradebookUtils::get_certificate_by_user_id(
            0,
            $studentId
        );
        if ($myCertificate) {
            $certificate = new Certificate($myCertificate['id'], $studentId);
            $certificate->delete(true);
        }
        // Create new one
        $certificate = new Certificate(0, $studentId);
        $certificate->generatePdfFromCustomCertificate();
        exit;
        break;
    case 'send_legal':
        $isBoss = UserManager::userIsBossOfStudent(api_get_user_id(), $studentId);
        if ($isBoss || api_is_platform_admin()) {
            $subject = get_lang('Legal conditions');
            $content = sprintf(
                get_lang(
                    'Hello,<br />Your tutor sent you your terms and conditions. You can sign it following this URL: %s'
                ),
                api_get_path(WEB_PATH)
            );
            MessageManager::send_message_simple($studentId, $subject, $content);
            Display::addFlash(Display::return_message(get_lang('Sent')));
        }
        break;
    case 'delete_legal':
        $isBoss = UserManager::userIsBossOfStudent(api_get_user_id(), $studentId);
        if ($isBoss || api_is_platform_admin()) {
            $extraFieldValue = new ExtraFieldValue('user');
            $value = $extraFieldValue->get_values_by_handler_and_field_variable(
                $studentId,
                'legal_accept'
            );
            $result = $extraFieldValue->delete($value['id']);
            if ($result) {
                Display::addFlash(Display::return_message(get_lang('Deleted')));
            }
        }
        break;
    case 'reset_lp':
        $lp_id = isset($_GET['lp_id']) ? (int) $_GET['lp_id'] : '';
        $check = true;

        if (!empty($lp_id) &&
            !empty($studentId) &&
            api_is_allowed_to_edit() &&
            Security::check_token('get')
        ) {
            Event::delete_student_lp_events(
                $studentId,
                $lp_id,
                $courseInfo,
                $sessionId
            );

            // @todo delete the stats.track_e_exercises records.
            // First implement this http://support.chamilo.org/issues/1334
            Display::addFlash(Display::return_message(get_lang('Learning path was reset for the learner'), 'success'));
            Security::clear_token();
        }
        break;
    default:
        break;
}

$courses_in_session = [];

// See #4676
$drh_can_access_all_courses = false;
if (api_is_drh() || api_is_platform_admin() || api_is_student_boss() || api_is_session_admin()) {
    $drh_can_access_all_courses = true;
}

$courses = CourseManager::get_course_list_of_user_as_course_admin(api_get_user_id());
$courses_in_session_by_coach = [];
$sessions_coached_by_user = Tracking::get_sessions_coached_by_user(api_get_user_id());

// RRHH or session admin
if (api_is_session_admin() || api_is_drh()) {
    $courses = CourseManager::get_courses_followed_by_drh(api_get_user_id());
    if (!empty($courses)) {
        $courses = array_column($courses, 'real_id');
    }
    $session_by_session_admin = SessionManager::get_sessions_followed_by_drh(api_get_user_id());

    if (!empty($session_by_session_admin)) {
        foreach ($session_by_session_admin as $session_coached_by_user) {
            $courses_followed_by_coach = Tracking::get_courses_list_from_session(
                $session_coached_by_user['id']
            );
            $courses_in_session_by_coach[$session_coached_by_user['id']] = $courses_followed_by_coach;
        }
    }
}

// Teacher or admin
if (!empty($sessions_coached_by_user)) {
    foreach ($sessions_coached_by_user as $session_coached_by_user) {
        $sid = (int) $session_coached_by_user['id'];
        $courses_followed_by_coach = Tracking::get_courses_followed_by_coach(api_get_user_id(), $sid);
        $courses_in_session_by_coach[$sid] = $courses_followed_by_coach;
    }
}

$sql = "SELECT c_id
        FROM $tbl_course_user
        WHERE
            relation_type <> ".COURSE_RELATION_TYPE_RRHH." AND
            user_id = ".$studentId;
$rs = Database::query($sql);

while ($row = Database::fetch_array($rs)) {
    if ($drh_can_access_all_courses) {
        $courses_in_session[0][] = $row['c_id'];
    } else {
        if (isset($courses[$row['c_id']])) {
            $courses_in_session[0][] = $row['c_id'];
        }
    }
}

// Get the list of sessions where the user is subscribed as student
$sql = 'SELECT session_id, c_id
        FROM '.Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER).'
        WHERE user_id='.$studentId;
$rs = Database::query($sql);
$tmp_sessions = [];
while ($row = Database::fetch_array($rs, 'ASSOC')) {
    $tmp_sessions[] = $row['session_id'];
    if ($drh_can_access_all_courses) {
        if (in_array($row['session_id'], $tmp_sessions)) {
            $courses_in_session[$row['session_id']][] = $row['c_id'];
        }
    } else {
        if (isset($courses_in_session_by_coach[$row['session_id']])) {
            if (in_array($row['session_id'], $tmp_sessions)) {
                $courses_in_session[$row['session_id']][] = $row['c_id'];
            }
        }
    }
}

$isDrhOfCourse = CourseManager::isUserSubscribedInCourseAsDrh(
    api_get_user_id(),
    $courseInfo
);

if (api_is_drh() && !api_is_platform_admin()) {
    if (!empty($studentId)) {
        if (api_drh_can_access_all_session_content()) {
        } else {
            if (!$isDrhOfCourse) {
                if (api_is_drh() &&
                    !UserManager::is_user_followed_by_drh($studentId, api_get_user_id())
                ) {
                    api_not_allowed(true);
                }
            }
        }
    }
}

$pluginCalendar = 'true' === api_get_plugin_setting('learning_calendar', 'enabled');

if ($pluginCalendar) {
    $plugin = LearningCalendarPlugin::create();
    $plugin->setJavaScript($htmlHeadXtra);
}

Display::display_header($nameTools);
$token = Security::get_token();

// Actions bar
echo '<div class="actions">';
echo '<a href="javascript: window.history.go(-1);">'
    .Display::return_icon('back.png', get_lang('Back'), '', ICON_SIZE_MEDIUM).'</a>';

echo '<a href="javascript: void(0);" onclick="javascript: window.print();">'
    .Display::return_icon('printer.png', get_lang('Print'), '', ICON_SIZE_MEDIUM).'</a>';

echo '<a href="'.api_get_self().'?'.Security::remove_XSS($_SERVER['QUERY_STRING']).'&export=csv">'
    .Display::return_icon('export_csv.png', get_lang('CSV export'), '', ICON_SIZE_MEDIUM).'</a> ';

echo '<a href="'.api_get_self().'?'.Security::remove_XSS($_SERVER['QUERY_STRING']).'&export=xls">'
    .Display::return_icon('export_excel.png', get_lang('Excel export'), '', ICON_SIZE_MEDIUM).'</a> ';

echo Display::url(
    Display::return_icon('attendance.png', get_lang('Access details'), '', ICON_SIZE_MEDIUM),
    api_get_path(WEB_CODE_PATH).'mySpace/access_details_session.php?user_id='.$studentId
);

if (!empty($userInfo['email'])) {
    $send_mail = '<a href="mailto:'.$userInfo['email'].'">'.
        Display::return_icon('mail_send.png', get_lang('Send message mail'), '', ICON_SIZE_MEDIUM).'</a>';
} else {
    $send_mail = Display::return_icon('mail_send_na.png', get_lang('Send message mail'), '', ICON_SIZE_MEDIUM);
}
echo $send_mail;
if (!empty($studentId) && !empty($courseCode)) {
    // Only show link to connection details if course and student were defined in the URL
    echo '<a href="access_details.php?student='.$studentId.'&course='.$courseCode.'&origin='.$origin.'&cid='
        .$courseId.'&id_session='.$sessionId.'">'
        .Display::return_icon('statistics.png', get_lang('Access details'), '', ICON_SIZE_MEDIUM)
        .'</a>';
}

$notebookTeacherEnable = 'true' === api_get_plugin_setting('notebookteacher', 'enable_plugin_notebookteacher');
if ($notebookTeacherEnable && !empty($studentId) && !empty($course_code)) {
    // link notebookteacher
    $optionsLink = 'student_id='.$studentId.'&origin='.$origin.'&cid='.$courseId.'&id_session='.$sessionId;
    echo '<a href="'.api_get_path(WEB_PLUGIN_PATH).'notebookteacher/src/index.php?'.$optionsLink.'">'
        .Display::return_icon('notebookteacher.png', get_lang('Notebook'), '', ICON_SIZE_MEDIUM)
        .'</a>';
}

if (api_can_login_as($studentId)) {
    echo '<a href="'.api_get_path(WEB_CODE_PATH).'admin/user_list.php?action=login_as&user_id='.$studentId
        .'&sec_token='.$token.'">'
        .Display::return_icon('login_as.png', get_lang('Login as'), null, ICON_SIZE_MEDIUM).'</a>&nbsp;&nbsp;';
}

if (Skill::isAllowed($studentId, false)) {
    echo Display::url(
        Display::return_icon(
            'skill-badges.png',
            get_lang('Assign skill'),
            null,
            ICON_SIZE_MEDIUM
        ),
        api_get_path(WEB_CODE_PATH).'badge/assign.php?'.http_build_query(['user' => $studentId])
    );
}

if (Skill::isAllowed($studentId, false)) {
    echo Display::url(
        Display::return_icon(
            'attendance.png',
            get_lang('CountDoneAttendance'),
            null,
            ICON_SIZE_MEDIUM
        ),
        api_get_path(WEB_CODE_PATH).'mySpace/myStudents.php?action=all_attendance&student='.$studentId
    );
}

$permissions = StudentFollowUpPlugin::getPermissions(
    $studentId,
    api_get_user_id()
);

$isAllow = $permissions['is_allow'];
if ($isAllow) {
    echo Display::url(
        Display::return_icon(
            'blog.png',
            get_lang('Blog'),
            null,
            ICON_SIZE_MEDIUM
        ),
        api_get_path(WEB_PLUGIN_PATH).'studentfollowup/posts.php?student_id='.$studentId
    );
}
echo '</div>';

// is the user online ?
$online = get_lang('No');
if (user_is_online($studentId)) {
    $online = get_lang('Yes');
}

// get average of score and average of progress by student
$avg_student_progress = $avg_student_score = 0;

if (empty($sessionId)) {
    $isSubscribedToCourse = CourseManager::is_user_subscribed_in_course($studentId, $courseCode);
} else {
    $isSubscribedToCourse = CourseManager::is_user_subscribed_in_course(
        $studentId,
        $courseCode,
        true,
        $sessionId
    );
}

if ($isSubscribedToCourse) {
    $avg_student_progress = Tracking::get_avg_student_progress(
        $studentId,
        $courseCode,
        [],
        $sessionId
    );

    // the score inside the Reporting table
    $avg_student_score = Tracking::get_avg_student_score(
        $studentId,
        $courseCode,
        [],
        $sessionId
    );
}

$avg_student_progress = round($avg_student_progress, 2);
$time_spent_on_the_course = 0;
if (!empty($courseInfo)) {
    $time_spent_on_the_course = api_time_to_hms(
        Tracking::get_time_spent_on_the_course(
            $studentId,
            $courseInfo['real_id'],
            $sessionId
        )
    );
}

// get information about connections on the platform by student
$first_connection_date = Tracking::get_first_connection_date($studentId);
if ('' == $first_connection_date) {
    $first_connection_date = get_lang('No connection');
}

$last_connection_date = Tracking::get_last_connection_date(
    $studentId,
    true
);
if ('' == $last_connection_date) {
    $last_connection_date = get_lang('No connection');
}

// cvs information
$csv_content[] = [
    get_lang('Information'),
];
$csv_content[] = [
    get_lang('Name'),
    get_lang('e-mail'),
    get_lang('Tel'),
];
$csv_content[] = [
    $userInfo['complete_name'],
    $userInfo['email'],
    $userInfo['phone'],
];

$csv_content[] = [];

// csv tracking
$csv_content[] = [
    get_lang('Reporting'),
];
$csv_content[] = [
    get_lang('First connectionInPlatform'),
    get_lang('Latest login in platform'),
    get_lang('Time spentInTheCourse'),
    get_lang('Progress'),
    get_lang('Score'),
];
$csv_content[] = [
    strip_tags($first_connection_date),
    strip_tags($last_connection_date),
    $time_spent_on_the_course,
    $avg_student_progress.'%',
    $avg_student_score,
];

$coachs_name = '';
$session_name = '';

$userPicture = UserManager::getUserPicture($studentId, USER_IMAGE_SIZE_BIG);

$userGroupManager = new UserGroup();
$userGroups = $userGroupManager->getNameListByUser(
    $studentId,
    UserGroup::NORMAL_CLASS
);

$userInfo['complete_name_link'] = $userInfo['complete_name_with_message_link'];
$userInfo['groups'] = $userGroupManager;
$userInfo['avatar'] = $userPicture;
$userInfo['online'] = $online;

if (!empty($courseCode)) {
    $userInfo['url_access'] = Display::url(
        get_lang('See accesses'),
        'access_details.php?'
        .http_build_query(
            [
                'student' => $studentId,
                'course' => $courseCode,
                'origin' => $origin,
                'cidReq' => $courseCode,
                'id_session' => $sessionId,
            ]
        ),
        ['class' => 'btn btn-default']
    );
}

// Display timezone if the user selected one and if the admin allows the use of user's timezone
$timezone = null;
$timezone_user = UserManager::get_extra_user_data_by_field(
    $studentId,
    'timezone'
);
$use_users_timezone = api_get_setting('use_users_timezone', 'timezones');
if (null != $timezone_user['timezone'] && 'true' === $use_users_timezone) {
    $timezone = $timezone_user['timezone'];
}
if (null !== $timezone) {
    $userInfo['timezone'] = $timezone;
}

if (is_numeric($avg_student_score)) {
    $score = $avg_student_score.'%';
} else {
    $score = $avg_student_score;
}

$userInfo['student_score'] = $score;
$userInfo['student_progress'] = $avg_student_progress;
$userInfo['first_connection'] = $first_connection_date;
$userInfo['last_connection'] = $last_connection_date;
if ('true' === $details) {
    $userInfo['time_spent_course'] = $time_spent_on_the_course;
}

$icon = '';
$timeLegalAccept = '';
$btn = '';
$userInfo['legal'] = '';
if ('true' === api_get_setting('allow_terms_conditions')) {
    $isBoss = UserManager::userIsBossOfStudent(api_get_user_id(), $studentId);
    if ($isBoss || api_is_platform_admin()) {
        $extraFieldValue = new ExtraFieldValue('user');
        $value = $extraFieldValue->get_values_by_handler_and_field_variable(
            $studentId,
            'legal_accept'
        );
        $icon = Display::return_icon('accept_na.png');
        $legalTime = null;

        if (isset($value['value']) && !empty($value['value'])) {
            [$legalId, $legalLanguageId, $legalTime] = explode(':', $value['value']);
            $icon = Display::return_icon('accept.png');
            $btn = Display::url(
                get_lang('Delete legal agreement'),
                api_get_self().'?action=delete_legal&student='.$studentId.'&course='.$course_code,
                ['class' => 'btn btn-danger']
            );
            $timeLegalAccept = api_get_local_time($legalTime);
        } else {
            $btn = Display::url(
                get_lang('Send message legal agreement'),
                api_get_self().'?action=send_legal&student='.$studentId.'&course='.$course_code,
                ['class' => 'btn btn-primary']
            );
            $timeLegalAccept = get_lang('Not Registered');
        }
    }
    $userInfo['legal'] = [
        'icon' => $icon,
        'datetime' => $timeLegalAccept,
        'url_send' => $btn,
    ];
}

if (isset($_GET['action']) and 'all_attendance' == $_GET['action']) {
    /*Display all attendances */
    // Varible for all attendance list
    $startDate = new DateTime();
    $startDate = $startDate->modify('-1 week');
    if (isset($_GET['startDate'])) {
        $startDate = new DateTime($_GET['startDate']);
    }
    $startDate = $startDate->setTime(0, 0, 0);

    $endDate = new DateTime();
    if (isset($_GET['endDate'])) {
        $endDate = new DateTime($_GET['endDate']);
    }
    $endDate = $endDate->setTime(23, 59, 0);

    // $startDate = new DateTime(api_get_local_time($startDate));
    // $endDate = new DateTime(api_get_local_time($endDate));
    if ($startDate > $endDate) {
        $dataTemp = $startDate;
        $startDate = $endDate;
        $endDate = $dataTemp;
    }
    $startDateText = api_get_local_time($startDate);
    $endDateText = api_get_local_time($endDate);
    // Varible for all attendance list

    /** Start date and end date*/
    $defaults['startDate'] = $startDateText;
    $defaults['endDate'] = $endDateText;
    $form = new FormValidator(
        'all_attendance_list',
        'GET',
        'myStudents.php?action=all_attendance&student='.$studentId.'&startDate='.$defaults['startDate'].'&endDate='.$defaults['endDate'].'&&'.api_get_cidreq(
        ),
        ''
    );
    $form->addElement('html', '<input type="hidden" name="student" value="'.$studentId.'" >');
    $form->addElement('html', '<input type="hidden" name="action" value="all_attendance" >');

    $form->addDateTimePicker(
        'startDate',
        [
            get_lang('ExeStartTime'),
        ],
        [
            'form_name' => 'attendance_calendar_edit',
        ],
        5
    );
    $form->addDateTimePicker(
        'endDate',
        [
            get_lang('ExeEndTime'),
        ],
        [
            'form_name' => 'attendance_calendar_edit',
        ],
        5
    );

    $form->addButtonSave(get_lang('Submit'));
    $form->setDefaults($defaults);
    $form->display();
    /** Display dates */
    $attendance = new Attendance();
    $data = $attendance->getCoursesWithAttendance($studentId, $startDate, $endDate);

    // 'attendance from %s to %s'
    $title = sprintf(get_lang('AttendanceFromXToY'), $startDateText, $endDateText);
    echo '
    <h3>'.$title.'</h3>
    <div class="">
    <table class="table table-striped table-hover table-responsive">
        <thead>
            <tr>
                <th>'.get_lang('DateExo').'</th>
                <th>'.get_lang('Training').'</th>

                <th>'.get_lang('Present').'</th>
            </tr>
        </thead>
    <tbody>';

    foreach ($data as $attendanceData => $attendanceSheet) {
        $totalAttendance = count($attendanceSheet);
        for ($i = 0; $i < $totalAttendance; $i++) {
            $attendanceWork = $attendanceSheet[$i];
            $courseInfoItem = api_get_course_info_by_id($attendanceWork['courseId']);
            $date = api_get_local_time($attendanceWork[1]);
            $sId = $attendanceWork['session'];
            $printSession = '';
            if (0 != $sId) {
                // get session name
                $printSession = "(".$attendanceWork['sessionName'].")";
            }
            // $teacher = isset($attendanceWork['teacher'])?$attendanceWork['teacher']:'';
            echo '
        <tr>
            <td>'.$date.'</td>
            <td>'
                .'<a title="'.get_lang('GoAttendance').'" href="'.api_get_path(WEB_CODE_PATH)
                .'attendance/index.php?cidReq='.$attendanceWork['courseCode'].'&id_session='.$sId.'&student_id='
                .$studentId.'">'
                .$attendanceWork['courseTitle']." $printSession ".'</a>
            </td>

            <td>'.$attendanceWork['presence'].'</td>
        </tr>';
            //<td>'.$teacher.'</td>
        }
    }
    echo '</tbody>
    </table></div>';
    /** Display dates */
    /*Display all attendances */
    Display::display_footer();
    exit();
}
$details = true;
$tpl = new Template(
    '',
    false,
    false,
    false,
    false,
    false,
    false
);

if (!empty($courseInfo)) {
    $nb_assignments = Tracking::count_student_assignments($studentId, $courseCode, $sessionId);
    $messages = Tracking::count_student_messages($studentId, $courseCode, $sessionId);
    $links = Tracking::count_student_visited_links($studentId, $courseInfo['real_id'], $sessionId);
    $chat_last_connection = Tracking::chat_last_connection($studentId, $courseInfo['real_id'], $sessionId);
    $documents = Tracking::count_student_downloaded_documents($studentId, $courseInfo['real_id'], $sessionId);
    $uploaded_documents = Tracking::count_student_uploaded_documents($studentId, $courseCode, $sessionId);
    $tpl->assign('title', $courseInfo['title']);

    $userInfo['tools'] = [
        'tasks' => $nb_assignments,
        'messages' => $messages,
        'links' => $links,
        'chat_connection' => $chat_last_connection,
        'documents' => $documents,
        'upload_documents' => $uploaded_documents,
        'course_first_access' => Tracking::get_first_connection_date_on_the_course(
            $studentId,
            $courseInfo['real_id'],
            $sessionId
        ),
        'course_last_access' => Tracking::get_last_connection_date_on_the_course($studentId, $courseInfo, $sessionId),
        'count_access_dates' => Tracking::getNumberOfCourseAccessDates($studentId, $courseInfo['real_id'], $sessionId),
    ];
} else {
    $details = false;
}

$tpl->assign('user', $userInfo);
$tpl->assign('details', $details);
$templateName = $tpl->get_template('my_space/user_details.tpl');
$content = $tpl->fetch($templateName);

echo $content;

$allowAll = api_get_configuration_value('allow_teacher_access_student_skills');
if ($allowAll) {
    // Show all skills
    echo Tracking::displayUserSkills(
        $studentId,
        0,
        0,
        true
    );
} else {
    // Default behaviour - Show all skills depending the course and session id
    echo Tracking::displayUserSkills(
        $studentId,
        $courseInfo ? $courseInfo['real_id'] : 0,
        $sessionId
    );
}

echo '<br /><br />';
echo '<div class="row">
        <div class="col-sm-5">';
if (!empty($userGroups)) {
    echo '<table class="table table-striped table-hover">
           <thead>
            <tr>
            <th>';
    echo get_lang('Classes');
    echo '</th>
                    </tr>
                    </thead>
                    <tbody>';
    foreach ($userGroups as $class) {
        echo '<tr><td>'.$class.'</td></tr>';
    }
    echo '</tbody></table>';
}
echo '</div></div>';

$exportCourseList = [];
$lpIdList = [];
if (empty($details)) {
    $csv_content[] = [];
    $csv_content[] = [
        get_lang('Session'),
        get_lang('Course'),
        get_lang('Time'),
        get_lang('Progress'),
        get_lang('Score'),
        get_lang('Not attended'),
        get_lang('Evaluations'),
    ];

    $attendance = new Attendance();
    foreach ($courses_in_session as $sId => $courses) {
        $session_name = '';
        $access_start_date = '';
        $access_end_date = '';
        $date_session = '';
        $title = Display::return_icon('course.png', get_lang('Courses')).' '.get_lang('Courses');

        $session_info = api_get_session_info($sId);
        if ($session_info) {
            $session_name = $session_info['name'];
            if (!empty($session_info['access_start_date'])) {
                $access_start_date = api_format_date($session_info['access_start_date'], DATE_FORMAT_SHORT);
            }

            if (!empty($session_info['access_end_date'])) {
                $access_end_date = api_format_date($session_info['access_end_date'], DATE_FORMAT_SHORT);
            }

            if (!empty($access_start_date) && !empty($access_end_date)) {
                $date_session = get_lang('From').' '.$access_start_date.' '.get_lang('Until').' '.$access_end_date;
            }
            $title = Display::return_icon('session.png', get_lang('Session'))
                .' '.$session_name.($date_session ? ' ('.$date_session.')' : '');
        }

        // Courses
        echo '<h3>'.$title.'</h3>';
        echo '<div class="table-responsive">';
        echo '<table class="table table-striped table-hover courses-tracking">';
        echo '<thead>';
        echo '<tr>
            <th>'.get_lang('Course').'</th>
            <th>'.get_lang('Time').'</th>
            <th>'.get_lang('Progress').'</th>
            <th>'.get_lang('Score').'</th>
            <th>'.get_lang('Not attended').'</th>
            <th>'.get_lang('Evaluations').'</th>
            <th>'.get_lang('Details').'</th>
        </tr>';
        echo '</thead>';
        echo '<tbody>';

        $csvRow = [
            '',
            get_lang('Course'),
            get_lang('Time'),
            get_lang('Progress'),
            get_lang('Score'),
            get_lang('Not attended'),
            get_lang('Evaluations'),
            get_lang('Details'),
        ];

        $exportCourseList[$sId][] = $csvRow;

        if (!empty($courses)) {
            $totalCourseTime = 0;
            $totalAttendance = [0, 0];
            $totalScore = 0;
            $totalProgress = 0;
            $gradeBookTotal = [0, 0];
            $totalCourses = count($courses);
            $scoreDisplay = ScoreDisplay::instance();

            foreach ($courses as $courseId) {
                $courseInfoItem = api_get_course_info_by_id($courseId);
                $courseId = $courseInfoItem['real_id'];
                $courseCodeItem = $courseInfoItem['code'];

                if (empty($session_info)) {
                    $isSubscribed = CourseManager::is_user_subscribed_in_course(
                        $studentId,
                        $courseCodeItem
                    );
                } else {
                    $isSubscribed = CourseManager::is_user_subscribed_in_course(
                        $studentId,
                        $courseCodeItem,
                        true,
                        $sId
                    );
                }

                if ($isSubscribed) {
                    $timeInSeconds = Tracking::get_time_spent_on_the_course(
                        $studentId,
                        $courseId,
                        $sId
                    );
                    $totalCourseTime += $timeInSeconds;
                    $time_spent_on_course = api_time_to_hms($timeInSeconds);

                    // get average of faults in attendances by student
                    $results_faults_avg = $attendance->get_faults_average_by_course(
                        $studentId,
                        $courseCodeItem,
                        $sId
                    );

                    $attendances_faults_avg = '0/0 (0%)';
                    if (!empty($results_faults_avg['total'])) {
                        if (api_is_drh()) {
                            $attendances_faults_avg = Display::url(
                                $results_faults_avg['faults'].'/'.$results_faults_avg['total']
                                .' ('.$results_faults_avg['porcent'].'%)',
                                api_get_path(WEB_CODE_PATH)
                                .'attendance/index.php?cidReq='.$courseCodeItem.'&id_session='.$sId.'&student_id='
                                .$studentId,
                                ['title' => get_lang('GoAttendance')]
                            );
                        } else {
                            $attendances_faults_avg = $results_faults_avg['faults'].'/'
                                .$results_faults_avg['total']
                                .' ('.$results_faults_avg['percent'].'%)';
                        }
                        $totalAttendance[0] += $results_faults_avg['faults'];
                        $totalAttendance[1] += $results_faults_avg['total'];
                    }

                    // Get evaluations by student
                    $cats = Category::load(
                        null,
                        null,
                        $courseCodeItem,
                        null,
                        null,
                        $sId
                    );

                    $scoretotal = [];
                    if (isset($cats) && isset($cats[0])) {
                        if (!empty($sId)) {
                            $scoretotal = $cats[0]->calc_score($studentId, null, $courseCodeItem, $sId);
                        } else {
                            $scoretotal = $cats[0]->calc_score($studentId, null, $courseCodeItem);
                        }
                    }

                    $scoretotal_display = '0/0 (0%)';
                    if (!empty($scoretotal) && !empty($scoretotal[1])) {
                        $scoretotal_display =
                            round($scoretotal[0], 1).'/'.
                            round($scoretotal[1], 1).
                            ' ('.round(($scoretotal[0] / $scoretotal[1]) * 100, 2).' %)';

                        $gradeBookTotal[0] += $scoretotal[0];
                        $gradeBookTotal[1] += $scoretotal[1];
                    }

                    $progress = Tracking::get_avg_student_progress(
                        $studentId,
                        $courseCodeItem,
                        [],
                        $sId
                    );

                    $totalProgress += $progress;

                    $score = Tracking::get_avg_student_score(
                        $studentId,
                        $courseCodeItem,
                        [],
                        $sId
                    );

                    if (is_numeric($score)) {
                        $totalScore += $score;
                    }

                    $progress = empty($progress) ? '0%' : $progress.'%';
                    $score = empty($score) ? '0%' : $score.'%';

                    $csvRow = [
                        $session_name,
                        $courseInfoItem['title'],
                        $time_spent_on_course,
                        $progress,
                        $score,
                        $attendances_faults_avg,
                        $scoretotal_display,
                    ];

                    $csv_content[] = $csvRow;
                    $exportCourseList[$sId][] = $csvRow;

                    echo '<tr>
                    <td>
                        <a href="'.$courseInfoItem['course_public_url'].'?id_session='.$sId.'">'.
                        $courseInfoItem['title'].'
                        </a>
                    </td>
                    <td >'.$time_spent_on_course.'</td>
                    <td >'.$progress.'</td>
                    <td >'.$score.'</td>
                    <td >'.$attendances_faults_avg.'</td>
                    <td >'.$scoretotal_display.'</td>';

                    if (!empty($coachId)) {
                        echo '<td width="10"><a href="'.api_get_self().'?student='.$studentId
                            .'&details=true&course='.$courseInfoItem['code'].'&id_coach='.$coachId.'&origin='.$origin
                            .'&id_session='.$sId.'#infosStudent">'
                            .Display::return_icon('2rightarrow.png', get_lang('Details')).'</a></td>';
                    } else {
                        echo '<td width="10"><a href="'.api_get_self().'?student='.$studentId
                            .'&details=true&course='.$courseInfoItem['code'].'&origin='.$origin.'&id_session='.$sId.'#infosStudent">'
                            .Display::return_icon('2rightarrow.png', get_lang('Details')).'</a></td>';
                    }
                    echo '</tr>';
                }
            }

            $totalAttendanceFormatted = $scoreDisplay->display_score($totalAttendance);
            $totalScoreFormatted = $scoreDisplay->display_score([$totalScore / $totalCourses, 100], SCORE_AVERAGE);
            $totalProgressFormatted = $scoreDisplay->display_score(
                [$totalProgress / $totalCourses, 100],
                SCORE_AVERAGE
            );
            $totalEvaluations = $scoreDisplay->display_score($gradeBookTotal);
            $totalTimeFormatted = api_time_to_hms($totalCourseTime);
            echo '<tr>
                <th>'.get_lang('Total').'</th>
                <th>'.$totalTimeFormatted.'</th>
                <th>'.$totalProgressFormatted.'</th>
                <th>'.$totalScoreFormatted.'</th>
                <th>'.$totalAttendanceFormatted.'</th>
                <th>'.$totalEvaluations.'</th>
                <th></th>
            </tr>';

            $csvRow = [
                get_lang('Total'),
                '',
                $totalTimeFormatted,
                $totalProgressFormatted,
                $totalScoreFormatted,
                $totalAttendanceFormatted,
                $totalEvaluations,
                '',
            ];

            $csv_content[] = $csvRow;
            $exportCourseList[$sId][] = $csvRow;
            $sessionAction = Display::url(
                Display::return_icon('export_csv.png', get_lang('ExportAsCSV'), [], ICON_SIZE_MEDIUM),
                $currentUrl.'&'
                .http_build_query(
                    [
                        'action' => 'export_one_session_row',
                        'export' => 'csv',
                        'session_to_export' => $sId,
                    ]
                )
            );
            $sessionAction .= Display::url(
                Display::return_icon('export_excel.png', get_lang('ExportAsXLS'), [], ICON_SIZE_MEDIUM),
                $currentUrl.'&'
                .http_build_query(
                    [
                        'action' => 'export_one_session_row',
                        'export' => 'xls',
                        'session_to_export' => $sId,
                    ]
                )
            );

            if (!empty($sId)) {
                $sessionAction .= Display::url(
                    Display::return_icon('pdf.png', get_lang('ExportToPDF'), [], ICON_SIZE_MEDIUM),
                    api_get_path(WEB_CODE_PATH).'mySpace/session.php?'
                    .http_build_query(
                        [
                            'student' => $studentId,
                            'action' => 'export_to_pdf',
                            'type' => 'attendance',
                            'session_to_export' => $sId,
                        ]
                    )
                );
                $sessionAction .= Display::url(
                    Display::return_icon('pdf.png', get_lang('CertificateOfAchievement'), [], ICON_SIZE_MEDIUM),
                    api_get_path(WEB_CODE_PATH).'mySpace/session.php?'
                    .http_build_query(
                        [
                            'student' => $studentId,
                            'action' => 'export_to_pdf',
                            'type' => 'achievement',
                            'session_to_export' => $sId,
                        ]
                    )
                );
            }
            echo $sessionAction;
        } else {
            echo "<tr><td colspan='5'>".get_lang('This course could not be found')."</td></tr>";
        }
        Session::write('export_course_list', $exportCourseList);
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
} else {
    $columnHeaders = [
        'lp' => get_lang('Learning paths'),
        'time' => get_lang('Time').
            Display::return_icon(
                'info3.gif',
                get_lang('Total time in course'),
                ['align' => 'absmiddle', 'hspace' => '3px']
            ),
        'best_score' => get_lang('Best score'),
        'latest_attempt_avg_score' => get_lang('Latest attempt average score').
            Display::return_icon(
                'info3.gif',
                get_lang('Average is calculated based on the latest attempts'),
                ['align' => 'absmiddle', 'hspace' => '3px']
            ),
        'progress' => get_lang('Progress').
            Display::return_icon('info3.gif', get_lang('LPProgressScore'), ['align' => 'absmiddle', 'hspace' => '3px']),
        'last_connection' => get_lang('LastConnexion').
            Display::return_icon(
                'info3.gif',
                get_lang('LastTimeTheCourseWasUsed'),
                ['align' => 'absmiddle', 'hspace' => '3px']
            ),
    ];

    $timeCourse = null;
    if (Tracking::minimumTimeAvailable($sessionId, $courseInfo['real_id'])) {
        $timeCourse = Tracking::getCalculateTime($studentId, $courseInfo['real_id'], $sessionId);
    }

    if (INVITEE != $userInfo['status']) {
        $csv_content[] = [];
        $csv_content[] = [str_replace('&nbsp;', '', strip_tags($userInfo['complete_name']))];
        $trackingColumns = api_get_configuration_value('tracking_columns');
        if (isset($trackingColumns['my_students_lp'])) {
            foreach ($columnHeaders as $key => $value) {
                if (!isset($trackingColumns['my_progress_lp'][$key]) ||
                    false == $trackingColumns['my_students_lp'][$key]
                ) {
                    unset($columnHeaders[$key]);
                }
            }
        }

        $headers = '';
        $columnHeadersToExport = [];
        // csv export headers
        foreach ($columnHeaders as $key => $columnName) {
            $columnHeadersToExport[] = strip_tags($columnName);
            $headers .= Display::tag(
                'th',
                $columnName
            );
        }

        /*$hookLpTracking = HookMyStudentsLpTracking::create();
        if ($hookLpTracking) {
            $hookHeaders = $hookLpTracking->notifyTrackingHeader();

            foreach ($hookHeaders as $hookHeader) {
                if (isset($hookHeader['value'])) {
                $columnHeadersToExport[] = $hookHeader['value'];

                $headers .= Display::tag('th', $hookHeader['value'], $hookHeader['attrs']);
            }
}
        }*/

        $csv_content[] = $columnHeadersToExport;
        $columnHeadersKeys = array_keys($columnHeaders);
        $categoriesTempList = learnpath::getCategories($courseInfo['real_id']);
        $categoryTest = new CLpCategory();
        //$categoryTest->setId(0);
        //$categoryTest->setId(0);
        $categoryTest->setName(get_lang('WithOutCategory'));
        $categoryTest->setPosition(0);
        $categories = [
            $categoryTest,
        ];

        if (!empty($categoriesTempList)) {
            $categories = array_merge($categories, $categoriesTempList);
        }

        $userEntity = api_get_user_entity(api_get_user_id());

        /** @var CLpCategory $item */
        foreach ($categories as $item) {
            $categoryId = $item->getIid();
            if (!learnpath::categoryIsVisibleForStudent($item, $userEntity, $courseInfo['real_id'], $sessionId)) {
                continue;
            }

            $list = new LearnpathList(
                api_get_user_id(),
                $courseInfo,
                $sessionId,
                null,
                false,
                $categoryId,
                false,
                true
            );

            $flat_list = $list->get_flat_list();
            $i = 0;
            if (count($categories) > 1) {
                echo Display::page_subheader2($item->getName());
            }

            echo '<div class="table-responsive">';
            echo '<table class="table table-striped table-hover"><thead><tr>';
            echo $headers;
            echo '<th>'.get_lang('Details').'</th>';
            if (api_is_allowed_to_edit()) {
                echo '<th>'.get_lang('Reset Learning path').'</th>';
            }
            echo '</tr></thead><tbody>';

            foreach ($flat_list as $learnpath) {
                $lpIdList[] = $learnpath['iid'];
                $lp_id = $learnpath['lp_old_id'];
                $lp_name = $learnpath['lp_name'];
                $any_result = false;

                // Get progress in lp
                $progress = Tracking::get_avg_student_progress(
                    $studentId,
                    $courseCode,
                    [$lp_id],
                    $sessionId
                );

                if (null === $progress) {
                    $progress = '0%';
                } else {
                    $any_result = true;
                }

                // Get time in lp
                if (!empty($timeCourse)) {
                    $lpTime = isset($timeCourse[TOOL_LEARNPATH]) ? $timeCourse[TOOL_LEARNPATH] : 0;
                    $total_time = isset($lpTime[$lp_id]) ? (int) $lpTime[$lp_id] : 0;
                } else {
                    $total_time = Tracking::get_time_spent_in_lp(
                        $studentId,
                        $courseCode,
                        [$lp_id],
                        $sessionId
                    );
                }

                if (!empty($total_time)) {
                    $any_result = true;
                }

                // Get last connection time in lp
                $start_time = Tracking::get_last_connection_time_in_lp(
                    $studentId,
                    $courseCode,
                    $lp_id,
                    $sessionId
                );

                if (!empty($start_time)) {
                    $start_time = api_convert_and_format_date($start_time, DATE_TIME_FORMAT_LONG);
                } else {
                    $start_time = '-';
                }

                if (!empty($total_time)) {
                    $any_result = true;
                }

                // Quiz in lp
                $score = Tracking::get_avg_student_score(
                    $studentId,
                    $courseCode,
                    [$lp_id],
                    $sessionId
                );

                // Latest exercise results in a LP
                $score_latest = Tracking::get_avg_student_score(
                    $studentId,
                    $courseCode,
                    [$lp_id],
                    $sessionId,
                    false,
                    true
                );

                $bestScore = Tracking::get_avg_student_score(
                    $studentId,
                    $courseCode,
                    [$lp_id],
                    $sessionId,
                    false,
                    false,
                    true
                );

                if (empty($bestScore)) {
                    $bestScore = '';
                } else {
                    $bestScore = $bestScore.'%';
                }

                if (0 == $i % 2) {
                    $css_class = 'row_even';
                } else {
                    $css_class = 'row_odd';
                }

                $i++;

                if (isset($score_latest) && !is_null($score_latest)) {
                    if (is_numeric($score_latest)) {
                        $score_latest = $score_latest.'%';
                    }
                }

                if (is_numeric($progress)) {
                    $progress = $progress.'%';
                } else {
                    $progress = '-';
                }

                echo '<tr class="'.$css_class.'">';
                $contentToExport = [];
                if (in_array('lp', $columnHeadersKeys)) {
                    $contentToExport[] = api_html_entity_decode(
                        stripslashes($lp_name),
                        ENT_QUOTES,
                        $charset
                    );
                    echo Display::tag('td', stripslashes($lp_name));
                }
                if (in_array('time', $columnHeadersKeys)) {
                    $contentToExport[] = api_time_to_hms($total_time);
                    echo Display::tag('td', api_time_to_hms($total_time));
                }

                if (in_array('best_score', $columnHeadersKeys)) {
                    $contentToExport[] = $bestScore;
                    echo Display::tag('td', $bestScore);
                }
                if (in_array('latest_attempt_avg_score', $columnHeadersKeys)) {
                    $contentToExport[] = $score_latest;
                    echo Display::tag('td', $score_latest);
                }

                if (in_array('progress', $columnHeadersKeys)) {
                    $contentToExport[] = $progress;
                    echo Display::tag('td', $progress);
                }

                if (in_array('last_connection', $columnHeadersKeys)) {
                    // Do not change with api_convert_and_format_date, because this value came from the lp_item_view table
                    // which implies several other changes not a priority right now
                    $contentToExport[] = $start_time;
                    echo Display::tag('td', $start_time);
                }

/*                if ($hookLpTracking) {
                    $hookContents = $hookLpTracking->notifyTrackingContent($lp_id, $studentId);

                    foreach ($hookContents as $hookContent) {
                        if (isset($hookContent['value'])) {
                            $contentToExport[] = strip_tags($hookContent['value']);

                            echo Display::tag('td', $hookContent['value'], $hookContent['attrs']);
                        }
                    }
                }*/

                $csv_content[] = $contentToExport;

                if (true === $any_result) {
                    $from = '';
                    if ($from_myspace) {
                        $from = '&from=myspace';
                    }
                    $link = Display::url(
                        Display::return_icon('2rightarrow.png', get_lang('Details')),
                        $codePath.'mySpace/lp_tracking.php?cid='.$courseInfo['real_id'].'&course='.$course_code.$from.'&origin='.$origin
                        .'&lp_id='.$lp_id.'&student_id='.$studentId.'&sid='.$sessionId
                    );
                    echo Display::tag('td', $link);
                }

                if (api_is_allowed_to_edit()) {
                    echo '<td>';
                    if (true === $any_result) {
                        $url = 'myStudents.php?action=reset_lp&sec_token='.$token.'&cid='.$courseInfo['real_id'].'&course='
                            .$course_code.'&details='.$details.'&origin='.$origin.'&lp_id='.$lp_id.'&student='
                            .$studentId.'&details=true&sid='.$sessionId;
                        echo Display::url(
                            Display::return_icon('clean.png', get_lang('Clean')),
                            $url,
                            [
                                'onclick' => "javascript:if(!confirm('"
                                    .addslashes(
                                        api_htmlentities(get_lang('Are you sure you want to delete'))
                                    )
                                    ."')) return false;",
                            ]
                        );
                    }
                    echo '</td>';
                }

                echo '</tr>';
            }
            echo '</tbody></table></div>';
        }
    }

    if (INVITEE != $userInfo['status']) {
        echo '<div class="table-responsive">
        <table class="table table-striped table-hover">
        <thead>
        <tr>';
        echo '<th>'.get_lang('Tests').'</th>';
        echo '<th>'.get_lang('Learning paths').'</th>';
        echo '<th>'.get_lang('Average score in learning paths').' '.
            Display::return_icon(
                'info3.gif',
                get_lang('Average score'),
                ['align' => 'absmiddle', 'hspace' => '3px']
            ).'</th>';
        echo '<th>'.get_lang('Attempts').'</th>';
        echo '<th>'.get_lang('LatestAttempt').'</th>';
        echo '<th>'.get_lang('AllAttempts').'</th>';

        /*$hookQuizTracking = HookMyStudentsQuizTracking::create();
        if ($hookQuizTracking) {
            $hookHeaders = array_map(
                function ($hookHeader) {
                    if (isset($hookHeader['value'])) {
                     return Display::tag('th', $hookHeader['value'], $hookHeader['attrs']);
                    }
                },
                $hookQuizTracking->notifyTrackingHeader()
            );

            echo implode(PHP_EOL, $hookHeaders);
        }*/

        echo '</tr></thead><tbody>';

        $csv_content[] = [];
        $csv_content[] = [
            get_lang('Tests'),
            get_lang('Learning paths'),
            get_lang('Average score in learning paths'),
            get_lang('Attempts'),
        ];

        /*if ($hookQuizTracking) {
            $hookHeaders = array_map(
                function ($hookHeader) {
                    if (isset($hookHeader['value'])) {
                    return strip_tags($hookHeader['value']);
                    }
                },
                $hookQuizTracking->notifyTrackingHeader()
            );

            $csvContentIndex = count($csv_content) - 1;
            $csv_content[$csvContentIndex] = array_merge($csv_content[$csvContentIndex], $hookHeaders);
        }*/

        /*$t_quiz = Database::get_course_table(TABLE_QUIZ_TEST);
        $sessionCondition = api_get_session_condition(
            $sessionId,
            true,
            true,
            'quiz.session_id'
        );

        $sql = "SELECT quiz.title, id
                FROM $t_quiz AS quiz
                WHERE
                    quiz.c_id = ".$courseInfo['real_id']." AND
                    active IN (0, 1)
                    $sessionCondition
                ORDER BY quiz.title ASC ";

        $result_exercices = Database::query($sql);
        $i = 0;*/

        $repo = Container::getQuizRepository();
        $course = api_get_course_entity($courseInfo['real_id']);
        $session = api_get_session_entity($sessionId);
        // 2. Get query builder from repo.
        $qb = $repo->getResourcesByCourse($course, $session);
        $qb->andWhere('resource.active = 1 OR resource.active = 0');
        $exerciseList = $qb->getQuery()->getResult();

        if ($exerciseList) {
            //while ($exercices = Database::fetch_array($result_exercices)) {
            /** @var CQuiz $exercise */
            foreach ($exerciseList as $exercise) {
                $exercise_id = (int) $exercise->getIid();
                $count_attempts = Tracking::count_student_exercise_attempts(
                    $studentId,
                    $courseInfo['real_id'],
                    $exercise_id,
                    0,
                    0,
                    $sessionId,
                    2
                );
                $score_percentage = Tracking::get_avg_student_exercise_score(
                    $studentId,
                    $courseCode,
                    $exercise_id,
                    $sessionId,
                    1,
                    0
                );

                $lp_name = '-';
                /*$hookContents = $hookQuizTracking
                    ? $hookQuizTracking->notifyTrackingContent($exercise_id, $studentId)
                    : [];*/
                $hookContents = '';
                if (!isset($score_percentage) && $count_attempts > 0) {
                    $scores_lp = Tracking::get_avg_student_exercise_score(
                        $studentId,
                        $courseCode,
                        $exercise_id,
                        $sessionId,
                        2,
                        1
                    );
                    $score_percentage = $scores_lp[0];
                    $lp_name = $scores_lp[1];
                }
                $lp_name = !empty($lp_name) ? $lp_name : get_lang('No learning path');

                $css_class = 'row_even';
                if ($i % 2) {
                    $css_class = 'row_odd';
                }

                echo '<tr class="'.$css_class.'"><td>'.Exercise::get_formated_title_variable(
                        $exercices['title']
                    ).'</td>';
                echo '<td>';

                if (!empty($lp_name)) {
                    echo $lp_name;
                } else {
                    echo '-';
                }

                echo '</td>';
                echo '<td>';

                if ($count_attempts > 0) {
                    echo $score_percentage.'%';
                } else {
                    echo '-';
                    $score_percentage = 0;
                }

                echo '</td>';
                echo '<td>'.$count_attempts.'</td>';
                echo '<td>';

                $sql = 'SELECT exe_id FROM '.$tbl_stats_exercices.'
                         WHERE
                            exe_exo_id = "'.$exercise_id.'" AND
                            exe_user_id ="'.$studentId.'" AND
                            c_id = '.$courseInfo['real_id'].' AND
                            session_id = "'.$sessionId.'" AND
                            status = ""
                        ORDER BY exe_date DESC
                        LIMIT 1';
                $result_last_attempt = Database::query($sql);
                if (Database::num_rows($result_last_attempt) > 0) {
                    $id_last_attempt = Database::result($result_last_attempt, 0, 0);
                    if ($count_attempts > 0) {
                        $qualifyLink = '';
                        if ($allowToQualify) {
                            $qualifyLink = '&action=qualify';
                        }
                        $attemptLink = '../exercise/exercise_show.php?id='.$id_last_attempt.'&cidReq='.$courseCode
                            .'&id_session='.$sessionId.'&session_id='.$sessionId.'&student='.$studentId.'&origin='
                            .(empty($origin) ? 'tracking' : $origin).$qualifyLink;
                        echo Display::url(
                            Display::return_icon('quiz.png', get_lang('Test')),
                            $attemptLink
                        );
                    }
                }
                echo '</td>';

                echo '<td>';
                if ($count_attempts > 0) {
                    $all_attempt_url = "../exercise/exercise_report.php?id=$exercise_id&"
                        ."cidReq=$courseCode&filter_by_user=$studentId&id_session=$sessionId";
                    echo Display::url(
                        Display::return_icon(
                            'test_results.png',
                            get_lang('All attempts'),
                            [],
                            ICON_SIZE_SMALL
                        ),
                        $all_attempt_url
                    );
                }
                echo '</td>';

                if (!empty($hookContents)) {
                    foreach ($hookContents as $hookContent) {
                        if (isset($hookContent['value'])) {
                            echo Display::tag('td', $hookContent['value'], $hookContent['attrs']);
                        }
                    }
                }

                echo '</tr>';
                $data_exercices[$i][] = $exercices['title'];
                $data_exercices[$i][] = $score_percentage.'%';
                $data_exercices[$i][] = $count_attempts;

                $csv_content[] = [
                    $exercices['title'],
                    $lp_name,
                    $score_percentage,
                    $count_attempts,
                ];

                if (!empty($hookContents)) {
                    $csvContentIndex = count($csv_content) - 1;

                    foreach ($hookContents as $hookContent) {
                        if (isset($hookContent['value'])) {
                            $csv_content[$csvContentIndex][] = strip_tags($hookContent['value']);
                        }
                    }
                }
                $i++;
            }
        } else {
            echo '<tr><td colspan="6">'.get_lang('NoTest').'</td></tr>';
        }
        echo '</tbody></table></div>';
    }

    // @when using sessions we do not show the survey list
    if (empty($sessionId)) {
        if (!empty($survey_list)) {
            $survey_data = [];
            foreach ($survey_list as $survey) {
                $user_list = SurveyManager::get_people_who_filled_survey(
                    $survey['survey_id'],
                    false,
                    $courseInfo['real_id']
                );
                $survey_done = Display::return_icon(
                    'accept_na.png',
                    get_lang('There is no answer for the moment'),
                    [],
                    ICON_SIZE_SMALL
                );
                if (in_array($studentId, $user_list)) {
                    $survey_done = Display::return_icon(
                        'accept.png',
                        get_lang('Answered'),
                        [],
                        ICON_SIZE_SMALL
                    );
                }
                $data = ['title' => $survey['title'], 'done' => $survey_done];
                $survey_data[] = $data;
            }

            if (!empty($survey_data)) {
                $table = new HTML_Table(['class' => 'data_table']);
                $header_names = [get_lang('Survey'), get_lang('Answered')];
                $row = 0;
                $column = 0;
                foreach ($header_names as $item) {
                    $table->setHeaderContents($row, $column, $item);
                    $column++;
                }
                $row = 1;
                foreach ($survey_data as $data) {
                    $column = 0;
                    $table->setCellContents($row, $column, $data);
                    $class = 'class="row_odd"';
                    if ($row % 2) {
                        $class = 'class="row_even"';
                    }
                    $table->setRowAttributes($row, $class, true);
                    $column++;
                    $row++;
                }
                echo $table->toHtml();
            }
        }
    }

    $userWorks = getWorkPerUser($studentId, $courseInfo['real_id'], $sessionId);
    echo '
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>'.get_lang('Tasks').'</th>
                        <th class="text-center">'.get_lang('Document ID').'</th>
                        <th class="text-center">'.get_lang('Note').'</th>
                        <th class="text-center">'.get_lang('Handed out').'</th>
                        <th class="text-center">'.get_lang('Deadline').'</th>
                        <th class="text-center">'.get_lang('Assignment work time').'</th>
                    </tr>
                </thead>
                <tbody>
    ';
    $workingTime = api_get_configuration_value('considered_working_time');
    foreach ($userWorks as $work) {
        $work = $work['work'];
        $showOnce = true;
        foreach ($work->user_results as $key => $results) {
            $resultId = $results['id'];

            echo '<tr>';
            echo '<td>'.$work->title.'</td>';
            $documentNumber = $key + 1;
            $url = api_get_path(WEB_CODE_PATH).
                'work/view.php?cidReq='.$courseCode.'&id_session='.$sessionId.'&id='.$resultId;
            echo '<td class="text-center"><a href="'.$url.'">('.$documentNumber.')</a></td>';
            $qualification = !empty($results['qualification']) ? $results['qualification'] : '-';
            echo '<td class="text-center">'.$qualification.'</td>';
            echo '<td class="text-center">'.api_convert_and_format_date(
                    $results['sent_date_from_db']
                ).' '.$results['expiry_note'].'</td>';
            $assignment = get_work_assignment_by_id($work->id, $courseInfo['real_id']);

            echo '<td class="text-center">';
            if (!empty($assignment['expires_on'])) {
                echo api_convert_and_format_date($assignment['expires_on']);
            }
            echo '</td>';

            $fieldValue = new ExtraFieldValue('work');
            $resultExtra = $fieldValue->getAllValuesForAnItem(
                $work->iid,
                true
            );

            foreach ($resultExtra as $field) {
                $field = $field['value'];
                if ($workingTime == $field->getField()->getVariable()) {
                    $time = $field->getValue();
                    echo '<td class="text-center">';
                    echo $time;
                    if ($workingTimeEdit && $showOnce) {
                        $showOnce = false;
                        echo '&nbsp;'.Display::url(
                                get_lang('AddTime'),
                                $currentUrl.'&action=add_work_time&time='.$time.'&work_id='.$work->id
                            );

                        echo '&nbsp;'.Display::url(
                                get_lang('RemoveTime'),
                                $currentUrl.'&action=remove_work_time&time='.$time.'&work_id='.$work->id
                            );
                    }
                    echo '</td>';
                }
            }
            echo '</tr>';
        }
    }

    echo '</tbody>
            </table>
        </div>
    ';

    $csv_content[] = [];

    $csv_content[] = [
        get_lang('OTI (Online Training Interaction) settings report'),
    ];

    $csv_content[] = [
        get_lang('Assignments'),
        $nb_assignments,
    ];
    $csv_content[] = [
        get_lang('Messages'),
        $messages,
    ];
    $csv_content[] = [
        get_lang('Links accessed'),
        $links,
    ];
    $csv_content[] = [
        get_lang('Documents downloaded'),
        $documents,
    ];
    $csv_content[] = [
        get_lang('Uploaded documents'),
        $uploaded_documents,
    ];
    $csv_content[] = [
        get_lang('Latest chat connection'),
        $chat_last_connection,
    ];
} //end details

if (true === $allowMessages) {
    // Messages
    echo Display::page_subheader2(get_lang('Messages'));
    echo MessageManager::getMessagesAboutUserToString($userInfo);
    echo Display::url(
        get_lang('New message'),
        'javascript: void(0);',
        [
            'onClick' => "$('#compose_message').show();",
            'class' => 'btn btn-default',
        ]
    );

    $form = new FormValidator(
        'messages',
        'post',
        $currentUrl.'&action=send_message'
    );
    $form->addHtml('<div id="compose_message" style="display:none;">');
    $form->addText('subject', get_lang('Subject'));
    $form->addHtmlEditor('message', get_lang('Message'));
    $form->addButtonSend(get_lang('Send message'));
    $form->addHidden('sec_token', $token);
    $form->addHtml('</div>');
    $form->display();
}

$allow = api_get_configuration_value('allow_user_message_tracking');
if ($allow && (api_is_drh() || api_is_platform_admin())) {
    $users = MessageManager::getUsersThatHadConversationWithUser($studentId);
    echo Display::page_subheader2(get_lang('MessageReporting'));

    $table = new HTML_Table(['class' => 'table']);
    $column = 0;
    $row = 0;
    $headers = [
        get_lang('User'),
    ];
    foreach ($headers as $header) {
        $table->setHeaderContents($row, $column, $header);
        $column++;
    }
    $column = 0;
    $row++;
    foreach ($users as $userFollowed) {
        $followedUserId = $userFollowed['user_id'];
        $url = api_get_path(WEB_CODE_PATH).'tracking/messages.php?from_user='.$studentId.'&to_user='.$followedUserId;
        $link = Display::url(
            $userFollowed['complete_name'],
            $url
        );
        $table->setCellContents($row, $column, $link);
        $row++;
    }
    $table->display();
}

if ($pluginCalendar) {
    echo $plugin->getUserStatsPanel($studentId, $courses_in_session);
}

if ($export) {
    ob_end_clean();
    switch ($export) {
        case 'csv':
            Export::arrayToCsv($csv_content, 'reporting_student');
            break;
        case 'xls':
            Export::arrayToXls($csv_content, 'reporting_student');
            break;
    }
    exit;
}

Display::display_footer();
