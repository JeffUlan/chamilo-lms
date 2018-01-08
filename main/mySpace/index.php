<?php
/* For licensing terms, see /license.txt */

use ChamiloSession as Session;

/**
 * Homepage for the MySpace directory
 * @package chamilo.reporting
 */

// resetting the course id
$cidReset = true;

require_once __DIR__.'/../inc/global.inc.php';

$htmlHeadXtra[] = api_get_jqgrid_js();
// the section (for the tabs)
$this_section = SECTION_TRACKING;
//for HTML editor repository
Session::erase('this_section');

ob_start();
$nameTools = get_lang('MySpace');
$export_csv  = isset($_GET['export']) && $_GET['export'] == 'csv' ? true : false;
$display = isset($_GET['display']) ? Security::remove_XSS($_GET['display']) : null;
$csv_content = [];
$user_id = api_get_user_id();
$session_id = isset($_GET['session_id']) ? intval($_GET['session_id']) : 0;
$is_coach = api_is_coach($session_id);
$is_platform_admin = api_is_platform_admin();
$is_drh = api_is_drh();
$is_session_admin = api_is_session_admin();
$title = '';
$skipData = api_get_configuration_value('tracking_skip_generic_data');

// Access control
api_block_anonymous_users();

if (!$export_csv) {
    Display :: display_header($nameTools);
}

if ($is_session_admin) {
    header('location:session.php');
    exit;
}

// Get views
$views = ['admin', 'teacher', 'coach', 'drh'];
$view = 'teacher';
if (isset($_GET['view']) && in_array($_GET['view'], $views)) {
    $view = $_GET['view'];
}

$menu_items = [];
if ($is_platform_admin) {
    if ($view == 'admin') {
        $title = get_lang('CoachList');
        $menu_items[] = Display::url(
            Display::return_icon('teacher.png', get_lang('TeacherInterface'), [], ICON_SIZE_MEDIUM),
            api_get_self().'?view=teacher'
        );
        $menu_items[] = Display::url(
            Display::return_icon('star_na.png', get_lang('AdminInterface'), [], ICON_SIZE_MEDIUM),
            api_get_path(WEB_CODE_PATH).'mySpace/admin_view.php'
        );
        $menu_items[] = Display::url(
            Display::return_icon('quiz.png', get_lang('ExamTracking'), [], ICON_SIZE_MEDIUM),
            api_get_path(WEB_CODE_PATH).'tracking/exams.php'
        );
        $menu_items[] = Display::url(
            Display::return_icon('statistics.png', get_lang('CurrentCoursesReport'), [], ICON_SIZE_MEDIUM),
            api_get_path(WEB_CODE_PATH).'mySpace/current_courses.php'
        );
    } else {
        $menu_items[] = Display::url(
            Display::return_icon(
                'teacher_na.png',
                get_lang('TeacherInterface'),
                [],
                ICON_SIZE_MEDIUM
            ),
            ''
        );
        $menu_items[] = Display::url(
            Display::return_icon('star.png', get_lang('AdminInterface'), [], ICON_SIZE_MEDIUM),
            //api_get_path(WEB_CODE_PATH).'tracking/course_session_report.php?view=admin'
            api_get_path(WEB_CODE_PATH).'mySpace/admin_view.php'
        );
        $menu_items[] = Display::url(
            Display::return_icon('quiz.png', get_lang('ExamTracking'), [], ICON_SIZE_MEDIUM),
            api_get_path(WEB_CODE_PATH).'tracking/exams.php'
        );
        $menu_items[] = Display::url(
            Display::return_icon('statistics.png', get_lang('CurrentCoursesReport'), [], ICON_SIZE_MEDIUM),
            api_get_path(WEB_CODE_PATH).'mySpace/current_courses.php'
        );
    }
}

if ($is_drh) {
    $view = 'drh';
    $menu_items[] = Display::url(
        Display::return_icon('user_na.png', get_lang('Students'), [], ICON_SIZE_MEDIUM),
        '#'
    );
    $menu_items[] = Display::url(
        Display::return_icon('teacher.png', get_lang('Trainers'), [], ICON_SIZE_MEDIUM),
        'teachers.php'
    );
    $menu_items[] = Display::url(
        Display::return_icon('course.png', get_lang('Courses'), [], ICON_SIZE_MEDIUM),
        'course.php'
    );
    $menu_items[] = Display::url(
        Display::return_icon('session.png', get_lang('Sessions'), [], ICON_SIZE_MEDIUM),
        'session.php'
    );
    $menu_items[] = Display::url(
        Display::return_icon('empty_evaluation.png', get_lang('CompanyReport'), [], ICON_SIZE_MEDIUM),
        'company_reports.php'
    );
    $menu_items[] = Display::url(
        Display::return_icon('evaluation_rate.png', get_lang('CompanyReportResumed'), [], ICON_SIZE_MEDIUM),
        'company_reports_resumed.php'
    );
}

echo '<div id="actions" class="actions">';
echo '<span style="float:right">';
if ($display == 'useroverview' || $display == 'sessionoverview' || $display == 'courseoverview') {
    echo '<a href="'.api_get_self().'?display='.$display.'&export=csv&view='.$view.'">';
    echo Display::return_icon("export_csv.png", get_lang('ExportAsCSV'), [], 32);
    echo '</a>';
}
echo '<a href="javascript: void(0);" onclick="javascript: window.print()">'.
    Display::return_icon('printer.png', get_lang('Print'), '', ICON_SIZE_MEDIUM).'</a>';
echo '</span>';

if (!empty($session_id) &&
    !in_array($display, ['accessoverview', 'lpprogressoverview', 'progressoverview', 'exerciseprogress', 'surveyoverview'])
) {
    echo '<a href="index.php">'.Display::return_icon('back.png', get_lang('Back'), '', ICON_SIZE_MEDIUM).'</a>';
    if (!api_is_platform_admin()) {
        if (api_get_setting('add_users_by_coach') == 'true') {
            if ($is_coach) {
                echo "<div align=\"right\">";
                echo '<a href="user_import.php?id_session='.$session_id.'&action=export&amp;type=xml">'.
                        Display::return_icon('excel.gif', get_lang('ImportUserList')).'&nbsp;'.get_lang('ImportUserList').'</a>';
                echo "</div><br />";
            }
        }
    } else {
        echo "<div align=\"right\">";
        echo '<a href="user_import.php?id_session='.$session_id.'&action=export&amp;type=xml">'.
                Display::return_icon('excel.gif', get_lang('ImportUserList')).'&nbsp;'.get_lang('ImportUserList').'</a>';
        echo "</div><br />";
    }
} else {
    echo Display::url(
        Display::return_icon('stats.png', get_lang('MyStats'), '', ICON_SIZE_MEDIUM),
        api_get_path(WEB_CODE_PATH)."auth/my_progress.php"
    );
    echo Display::url(
        Display::return_icon("certificate_list.png", get_lang("GradebookSeeListOfStudentsCertificates"), [], ICON_SIZE_MEDIUM),
        api_get_path(WEB_CODE_PATH)."gradebook/certificate_report.php"
    );
}

// Actions menu
$nb_menu_items = count($menu_items);
if (empty($session_id) ||
    in_array($display, ['accessoverview', 'lpprogressoverview', 'progressoverview', 'exerciseprogress', 'surveyoverview'])
) {
    if ($nb_menu_items > 1) {
        foreach ($menu_items as $key => $item) {
            echo $item;
        }
    }
}

echo '</div>';

$userId = api_get_user_id();
$stats = Tracking::getStats($userId, true);

$numberStudents = $stats['student_count'];
$students = $stats['student_list'];
$numberStudentBosses = $stats['student_bosses'];
$numberTeachers = $stats['teachers'];
$countHumanResourcesUsers = $stats['drh'];
$countAssignedCourses = $stats['assigned_courses'];
$countCourses = $stats['courses'];
$sessions = $stats['session_list'];

$sessionIdList = [];
if (!empty($sessions)) {
    foreach ($sessions as $session) {
        $sessionIdList[] = $session['id'];
    }
}

// Sessions for the user
$countSessions = count($sessions);
$total_time_spent = 0;
$total_courses = 0;
$avgTotalProgress = 0;
$nb_inactive_students = 0;
$numberAssignments = 0;
$inactiveTime = time() - (3600 * 24 * 7);
$daysAgo = 7;
$studentIds = [];
$avg_courses_per_student = 0;

$linkAddUser = null;
$linkCourseDetailsAsTeacher = null;
$linkAddCourse = null;
$linkAddSession = null;

if (api_is_platform_admin()) {
    $linkAddUser = ' '.Display::url(
        Display::return_icon('2rightarrow.png', get_lang('Add')),
        api_get_path(WEB_CODE_PATH).'admin/dashboard_add_users_to_user.php?user='.api_get_user_id(),
        ['class' => '']
    );
    $linkCourseDetailsAsTeacher = ' '.Display::url(
        Display::return_icon('2rightarrow.png', get_lang('Details')),
        api_get_path(WEB_CODE_PATH).'mySpace/course.php',
        ['class' => '']
    );
    $linkAddCourse = ' '.Display::url(
        Display::return_icon('2rightarrow.png', get_lang('Details')),
        api_get_path(WEB_CODE_PATH).'mySpace/course.php?follow',
        ['class' => '']
    );
    $linkAddSession = ' '.Display::url(
        Display::return_icon('2rightarrow.png', get_lang('Add')),
        api_get_path(WEB_CODE_PATH).'admin/dashboard_add_sessions_to_user.php?user='.api_get_user_id(),
        ['class' => '']
    );
}

echo Display::page_subheader(get_lang('Overview'));
echo '<div class="report_section">
        <table class="table table-bordered table-striped">
            <tr>
                <td>'.Display::url(
                    get_lang('FollowedStudents'),
                    api_get_path(WEB_CODE_PATH).'mySpace/student.php'
                ).'</td>
                <td align="right">'.$numberStudents.'</td>
            </tr>
            <tr>
                <td>'.Display::url(
                    get_lang('FollowedStudentBosses'),
                    api_get_path(WEB_CODE_PATH).'mySpace/users.php?status='.STUDENT_BOSS
                ).'</td>
                <td align="right">'.$numberStudentBosses.'</td>
            </tr>
            <tr>
                <td>'.Display::url(
                    get_lang('FollowedTeachers'),
                    api_get_path(WEB_CODE_PATH).'mySpace/teachers.php'
                ).
                '</td>
                <td align="right">'.$numberTeachers.'</td>
            </tr>
            <tr>
                <td>'.Display::url(
                get_lang('FollowedHumanResources'),
                api_get_path(WEB_CODE_PATH).'mySpace/users.php?status='.DRH
            ).
            '</td>
            <td align="right">'.$countHumanResourcesUsers.'</td>
            </tr>
            <tr>
             <td>'.Display::url(
                get_lang('FollowedUsers'),
                api_get_path(WEB_CODE_PATH).'mySpace/users.php'
            ).
            '</td>
                <td align="right">'.($numberStudents + $numberStudentBosses + $numberTeachers + $countHumanResourcesUsers).$linkAddUser.'</td>
            </tr>
            <tr>
                <td>'.Display::url(
                    get_lang('AssignedCourses'),
                    api_get_path(WEB_CODE_PATH).'mySpace/course.php'
                ).
                '</td>
                <td align="right">'.$countCourses.$linkCourseDetailsAsTeacher.'</td>
            </tr>
            <tr>
                <td>'.Display::url(
                    get_lang('FollowedCourses'),
                    api_get_path(WEB_CODE_PATH).'mySpace/course.php?follow'
                ).
                '</td>
                <td align="right">'.$countAssignedCourses.$linkAddCourse.'</td>
            </tr>
            <tr>
                <td>'.Display::url(
                    get_lang('FollowedSessions'),
                    api_get_path(WEB_CODE_PATH).'mySpace/session.php'
                ).
                '</td>
            <td align="right">'.$countSessions.$linkAddSession.'</td>
            </tr>
            </table>';
echo '</div>';

echo Display::page_subheader(get_lang('Students').' ('.$numberStudents.')');

$form = new FormValidator(
    'search_user',
    'get',
    api_get_path(WEB_CODE_PATH).'mySpace/student.php'
);
$form = Tracking::setUserSearchForm($form);
$form->display();

$skipData = api_get_configuration_value('tracking_skip_generic_data');

$totalTimeSpent = null;
$averageScore = null;
$posts = null;

if ($skipData == false) {
    if (!empty($students)) {
        // Students
        $studentIds = array_values($students);
        $progress = Tracking::get_avg_student_progress($studentIds);
        $countAssignments = Tracking::count_student_assignments($studentIds);
        // average progress
        $avgTotalProgress = $progress / $numberStudents;
        // average assignments
        $numberAssignments = $countAssignments / $numberStudents;
        $avg_courses_per_student = $countCourses / $numberStudents;
        $totalTimeSpent = Tracking::get_time_spent_on_the_platform($studentIds);
        $posts = Tracking::count_student_messages($studentIds);
        $averageScore = Tracking::getAverageStudentScore($studentIds);
    }

    if ($export_csv) {
        //csv part
        $csv_content[] = [get_lang('Students')];
        $csv_content[] = [get_lang('InactivesStudents'), $nb_inactive_students];
        $csv_content[] = [get_lang('AverageTimeSpentOnThePlatform'), $totalTimeSpent];
        $csv_content[] = [get_lang('AverageCoursePerStudent'), $avg_courses_per_student];
        $csv_content[] = [get_lang('AverageProgressInLearnpath'), is_null($avgTotalProgress) ? null : round($avgTotalProgress, 2).'%'];
        $csv_content[] = [get_lang('AverageResultsToTheExercices'), is_null($averageScore) ? null : round($averageScore, 2).'%'];
        $csv_content[] = [get_lang('AveragePostsInForum'), $posts];
        $csv_content[] = [get_lang('AverageAssignments'), $numberAssignments];
        $csv_content[] = [];
    } else {
        $lastConnectionDate = api_get_utc_datetime(strtotime('15 days ago'));
        $countActiveUsers = SessionManager::getCountUserTracking(
            null,
            1,
            null,
            [],
            []
        );
        $countSleepingTeachers = SessionManager::getTeacherTracking(
            api_get_user_id(),
            1,
            $lastConnectionDate,
            true,
            $sessionIdList
        );

        $countSleepingStudents = SessionManager::getCountUserTracking(
            null,
            1,
            $lastConnectionDate,
            $sessionIdList,
            $studentIds
        );

        // html part
        echo '<div class="report_section">
                <table class="table table-bordered table-striped">
                    <tr>
                        <td>'.get_lang('AverageCoursePerStudent').'</td>
                        <td align="right">'.(is_null($avg_courses_per_student) ? '' : round($avg_courses_per_student, 2)).'</td>
                    </tr>
                    <tr>
                        <td>'.get_lang('InactivesStudents').'</td>
                        <td align="right">'.$nb_inactive_students.'</td>
                    </tr>
                    <tr>
                        <td>'.get_lang('AverageTimeSpentOnThePlatform').'</td>
                        <td align="right">'.(is_null($totalTimeSpent) ? '' : api_time_to_hms($totalTimeSpent)).'</td>
                    </tr>
                    <tr>
                        <td>'.get_lang('AverageProgressInLearnpath').'</td>
                        <td align="right">'.(is_null($avgTotalProgress) ? '' : round($avgTotalProgress, 2).'%').'</td>
                    </tr>
                    <tr>
                        <td>'.get_lang('AvgCourseScore').'</td>
                        <td align="right">'.(is_null($averageScore) ? '' : round($averageScore, 2).'%').'</td>
                    </tr>
                    <tr>
                        <td>'.get_lang('AveragePostsInForum').'</td>
                        <td align="right">'.(is_null($posts) ? '' : round($posts, 2)).'</td>
                    </tr>
                    <tr>
                        <td>'.get_lang('AverageAssignments').'</td>
                        <td align="right">'.(is_null($numberAssignments) ? '' : round($numberAssignments, 2)).'</td>
                    </tr>
                </table>
             </div>';
    }
}

// Send the csv file if asked
if ($export_csv) {
    ob_end_clean();
    Export:: arrayToCsv($csv_content, 'reporting_index');
    exit;
}

if (!$export_csv) {
    Display::display_footer();
}
