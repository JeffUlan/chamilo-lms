<?php
/* For licensing terms, see /license.txt */
/**
 * 	@package chamilo.admin
 */
// name of the language file that needs to be included
$language_file = 'admin';
$cidReset = true;

require_once '../inc/global.inc.php';
$this_section = SECTION_PLATFORM_ADMIN;

api_protect_admin_script();

$htmlHeadXtra[] = '<script src="' . api_get_path(WEB_LIBRARY_PATH) . 'javascript/tag/jquery.fcbkcomplete.js" type="text/javascript" language="javascript"></script>';

$course_table = Database::get_main_table(TABLE_MAIN_COURSE);
$course_user_table = Database::get_main_table(TABLE_MAIN_COURSE_USER);

$courseId = isset($_GET['id']) ? $_GET['id'] : null;

if (empty($courseId)) {
    api_not_allowed(true);
}

$courseInfo = api_get_course_info_by_id($courseId);

if (empty($courseInfo)) {
    api_not_allowed(true);
}

$tool_name = get_lang('ModifyCourseInfo');
$interbreadcrumb[] = array("url" => 'index.php', "name" => get_lang('PlatformAdmin'));
$interbreadcrumb[] = array("url" => "course_list.php", "name" => get_lang('CourseList'));

// Get all course categories
$table_user = Database :: get_main_table(TABLE_MAIN_USER);
$course_code = $courseInfo['code'];

// Get course teachers
$table_course_user = Database :: get_main_table(TABLE_MAIN_COURSE_USER);
$order_clause = api_sort_by_first_name() ? ' ORDER BY firstname, lastname' : ' ORDER BY lastname, firstname';
$sql = "SELECT user.user_id,lastname,firstname
        FROM $table_user as user,$table_course_user as course_user
        WHERE
            course_user.status='1' AND
            course_user.user_id=user.user_id AND
            course_user.course_code='" . $course_code . "'" .
        $order_clause;
$res = Database::query($sql);
$course_teachers = array();
while ($obj = Database::fetch_object($res)) {
    $course_teachers[] = $obj->user_id;
}

// Get all possible teachers without the course teachers
if (api_is_multiple_url_enabled()) {
    $access_url_rel_user_table = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
    $sql = "SELECT u.user_id,lastname,firstname
            FROM $table_user as u
            INNER JOIN $access_url_rel_user_table url_rel_user
            ON (u.user_id=url_rel_user.user_id)
            WHERE
                url_rel_user.access_url_id=" . api_get_current_access_url_id() . " AND
                status=1" . $order_clause;
} else {
    $sql = "SELECT user_id, lastname, firstname
            FROM $table_user WHERE status='1'" . $order_clause;
}
$courseInfo['tutor_name'] = null;

$res = Database::query($sql);
$teachers = array();
$allTeachers = array();
$platform_teachers[0] = '-- ' . get_lang('NoManager') . ' --';
while ($obj = Database::fetch_object($res)) {
    $allTeachers[$obj->user_id] = api_get_person_name($obj->firstname, $obj->lastname);
    if (!array_key_exists($obj->user_id, $course_teachers)) {
        $teachers[$obj->user_id] = api_get_person_name($obj->firstname, $obj->lastname);
    }

    if (isset($course_teachers[$obj->user_id]) &&
        $courseInfo['tutor_name'] == $course_teachers[$obj->user_id]
    ) {
        $courseInfo['tutor_name'] = $obj->user_id;
    }
    // We add in the array platform teachers
    $platform_teachers[$obj->user_id] = api_get_person_name($obj->firstname, $obj->lastname);
}

// Case where there is no teacher in the course
if (count($course_teachers) == 0) {
    $sql = 'SELECT tutor_name FROM ' . $course_table . ' WHERE code="' . $course_code . '"';
    $res = Database::query($sql);
    $tutor_name = Database::result($res, 0, 0);
    $courseInfo['tutor_name'] = array_search($tutor_name, $platform_teachers);
}

// Build the form
$form = new FormValidator('update_course', 'post', api_get_self().'?id='.$courseId);
$form->addElement('header', get_lang('Course') . '  #' . $courseInfo['real_id'] . ' ' . $course_code);
$form->addElement('hidden', 'code', $course_code);

//title
$form->addText('title', get_lang('Title'), true, array('class' => 'span6'));
$form->applyFilter('title', 'html_filter');
$form->applyFilter('title', 'trim');

// Code
$element = $form->addElement('text', 'real_code', array(get_lang('CourseCode'), get_lang('ThisValueCantBeChanged')));
$element->freeze();

// Visual code
$form->addText('visual_code', array(get_lang('VisualCode'), get_lang('OnlyLettersAndNumbers'), get_lang('ThisValueIsUsedInTheCourseURL')), true, array('class' => 'span4'));

$form->applyFilter('visual_code', 'strtoupper');
$form->applyFilter('visual_code', 'html_filter');

$form->addElement('advmultiselect', 'course_teachers', get_lang('CourseTeachers'), $allTeachers);

$courseInfo['course_teachers'] = $course_teachers;

if (array_key_exists('add_teachers_to_sessions_courses', $courseInfo)) {
    $form->addElement('checkbox', 'add_teachers_to_sessions_courses', null, get_lang('TeachersWillBeAddedAsCoachInAllCourseSessions'));
}

$coursesInSession = SessionManager::get_session_by_course($courseInfo['code']);
if (!empty($coursesInSession)) {
    foreach ($coursesInSession as $session) {
        $sessionId = $session['id'];
        $coaches = SessionManager::getCoachesByCourseSession($sessionId, $courseInfo['code']);
        $teachers = $allTeachers;

        $sessionTeachers = array();
        foreach ($coaches as $coachId) {
            $userInfo = api_get_user_info($coachId);
            $sessionTeachers[] = $coachId;

            if (isset($teachers[$coachId])) {
                unset($teachers[$coachId]);
            }
        }

        $groupName = 'session_coaches[' . $sessionId . ']';
        $platformTeacherId = 'platform_teachers_by_session_' . $sessionId;
        $coachId = 'coaches_by_session_' . $sessionId;

        $platformTeacherName = 'platform_teachers_by_session';
        $coachName = 'coaches_by_session';

        $sessionUrl = api_get_path(WEB_CODE_PATH) . 'admin/resume_session.php?id_session=' . $sessionId;
        $form->addElement(
            'advmultiselect',
            $groupName,
            Display::url(
                $session['name'], $sessionUrl, array('target' => '_blank')
            ) . ' - ' . get_lang('Coaches'),
            $allTeachers
        );
        $courseInfo[$groupName] = $sessionTeachers;

    }
}

// Category code
$url = api_get_path(WEB_AJAX_PATH) . 'course.ajax.php?a=search_category';
$categoryList = array();
if (!empty($course['category_code'])) {
    $data = getCategory($course['category_code']);
    $categoryList[] = array('id' => $data['code'], 'text' => $data['name']);
}

$form->addElement('select_ajax', 'category_code', get_lang('CourseFaculty'), null, array('url' => $url, 'defaults' => $categoryList));

$form->addText('department_name', get_lang('CourseDepartment'), false, array('size' => '60'));
$form->applyFilter('department_name', 'html_filter');
$form->applyFilter('department_name', 'trim');

$form->addText('department_url', get_lang('CourseDepartmentURL'), false, array('size' => '60'));
$form->applyFilter('department_url', 'html_filter');
$form->applyFilter('department_url', 'trim');

$form->addElement('select_language', 'course_language', get_lang('CourseLanguage'));
$form->applyFilter('select_language', 'html_filter');

$group = array();
$group[]= $form->createElement('radio', 'visibility', get_lang("CourseAccess"), get_lang('OpenToTheWorld'), COURSE_VISIBILITY_OPEN_WORLD);
$group[]= $form->createElement('radio', 'visibility', null, get_lang('OpenToThePlatform'), COURSE_VISIBILITY_OPEN_PLATFORM);
$group[]= $form->createElement('radio', 'visibility', null, get_lang('Private'), COURSE_VISIBILITY_REGISTERED);
$group[]= $form->createElement('radio', 'visibility', null, get_lang('CourseVisibilityClosed'), COURSE_VISIBILITY_CLOSED);
$group[]= $form->createElement('radio', 'visibility', null, get_lang('CourseVisibilityHidden'), COURSE_VISIBILITY_HIDDEN);
$form->addGroup($group, '', get_lang('CourseAccess'), '<br />');

$group = array();
$group[] = $form->createElement('radio', 'subscribe', get_lang('Subscription'), get_lang('Allowed'), 1);
$group[] = $form->createElement('radio', 'subscribe', null, get_lang('Denied'), 0);
$form->addGroup($group, '', get_lang('Subscription'), '<br />');

$group = array();
$group[] = $form->createElement('radio', 'unsubscribe', get_lang('Unsubscription'), get_lang('AllowedToUnsubscribe'), 1);
$group[] = $form->createElement('radio', 'unsubscribe', null, get_lang('NotAllowedToUnsubscribe'), 0);
$form->addGroup($group, '', get_lang('Unsubscription'), '<br />');

$form->addElement('text', 'disk_quota', array(get_lang('CourseQuota'), null, get_lang('MB')));
$form->addRule('disk_quota', get_lang('ThisFieldIsRequired'), 'required');
$form->addRule('disk_quota', get_lang('ThisFieldShouldBeNumeric'), 'numeric');

$list_course_extra_field = CourseManager::get_course_extra_field_list($course_code);

$specialCourseField = new CourseField();
$specialCourseFieldInfo = $specialCourseField->get_handler_field_info_by_field_variable('special_course');

if (!empty($specialCourseFieldInfo)) {
    $specialCourseValue = new ExtraFieldValue('course');
    $specialCourseValueInfo = $specialCourseValue->get_values_by_handler_and_field_variable($course_code, 'special_course');

    $specialCourseAttributes = array();

    if (!empty($specialCourseValueInfo) && $specialCourseValueInfo['field_value'] == 1) {
        $specialCourseAttributes['checked'] = '';
    }

    $form->addElement('hidden', '_extra_special_course', 0);
    $form->addElement('checkbox', 'extra_special_course', array(
        null, get_lang('AllUsersAreAutomaticallyRegistered')
    ), get_lang('SpecialCourse'), $specialCourseAttributes);
}

//Extra fields
$extra_field = new CourseField();
$extra = $extra_field->addElements($form, $course_code);

$htmlHeadXtra[] = '
<script>
$(function() {
    ' . $extra['jquery_ready_content'] . '
});
</script>';

$form->addButton('submit', get_lang('ModifyCourseInfo'), 'pencil', 'primary');

// Set some default values
$courseInfo['disk_quota'] = round(DocumentManager::get_course_quota($courseInfo['code']) / 1024 / 1024, 1);
$courseInfo['real_code'] = $courseInfo['code'];
$courseInfo['add_teachers_to_sessions_courses'] = isset($courseInfo['add_teachers_to_sessions_courses']) ? $courseInfo['add_teachers_to_sessions_courses'] : 0;

$form->setDefaults($courseInfo);

// Validate form
if ($form->validate()) {
    $course = $form->getSubmitValues();

    $visibility = $course['visibility'];

    global $_configuration;
    $urlId = api_get_current_access_url_id();
    if (isset($_configuration[$urlId]) &&
        isset($_configuration[$urlId]['hosting_limit_active_courses']) &&
        $_configuration[$urlId]['hosting_limit_active_courses'] > 0
    ) {
        // Check if
        if ($courseInfo['visibility'] == COURSE_VISIBILITY_HIDDEN &&
            $visibility != $courseInfo['visibility']
        ) {
            $num = CourseManager::countActiveCourses($urlId);
            if ($num >= $_configuration[$urlId]['hosting_limit_active_courses']) {
                api_warn_hosting_contact('hosting_limit_active_courses');

                api_set_failure(get_lang('PortalActiveCoursesLimitReached'));

                header('Location: course_list.php?action=show_msg&warn=' . urlencode(get_lang('PortalActiveCoursesLimitReached')));
                exit;
            }
        }
    }

    $visual_code = $course['visual_code'];
    $visual_code = CourseManager::generate_course_code($visual_code);

    // Check if the visual code is already used by *another* course
    $visual_code_is_used = false;

    $warn = get_lang('TheFollowingCoursesAlreadyUseThisVisualCode');
    if (!empty($visual_code)) {
        $list = CourseManager::get_courses_info_from_visual_code($visual_code);
        foreach ($list as $course_temp) {
            if ($course_temp['code'] != $course_code) {
                $visual_code_is_used = true;
                $warn .= ' ' . $course_temp['title'] . ' (' . $course_temp['code'] . '),';
            }
        }
        $warn = substr($warn, 0, -1);
    }

    $tutor_id = isset($course['tutor_name']) ? $course['tutor_name'] : null;
    $tutor_name = isset($platform_teachers[$tutor_id]) ? $platform_teachers[$tutor_id] : null;
    $teachers = $course['course_teachers'];

    $title = $course['title'];
    $category_code = $course['category_code'];
    $department_name = $course['department_name'];
    $department_url = $course['department_url'];
    $course_language = $course['course_language'];
    $course['disk_quota'] = $course['disk_quota'] * 1024 * 1024;
    $disk_quota = $course['disk_quota'];
    $subscribe = $course['subscribe'];
    $unsubscribe = $course['unsubscribe'];
    $course['course_code'] = $course_code;

    if (!stristr($department_url, 'http://')) {
        $department_url = 'http://' . $department_url;
    }

    $sql = "UPDATE $course_table SET course_language='" . Database::escape_string($course_language) . "',
                title='" . Database::escape_string($title) . "',
                category_code='" . Database::escape_string($category_code) . "',
                tutor_name='" . Database::escape_string($tutor_name) . "',
                visual_code='" . Database::escape_string($visual_code) . "',
                department_name='" . Database::escape_string($department_name) . "',
                department_url='" . Database::escape_string($department_url) . "',
                disk_quota='" . Database::escape_string($disk_quota) . "',
                visibility = '" . Database::escape_string($visibility) . "',
                subscribe = '" . Database::escape_string($subscribe) . "',
                unsubscribe='" . Database::escape_string($unsubscribe) . "'
            WHERE id = $courseId ";
    Database::query($sql);

    // update the extra fields
    $courseFieldValue = new ExtraFieldValue('course');
    $courseFieldValue->save_field_values($course);
    $addTeacherToSessionCourses = isset($course['add_teachers_to_sessions_courses']) && !empty($course['add_teachers_to_sessions_courses']) ? 1 : 0;

    // Updating teachers

    if ($addTeacherToSessionCourses) {

        // Updating session coaches
        $sessionCoaches = $course['session_coaches'];
        if (!empty($sessionCoaches)) {
            foreach ($sessionCoaches as $sessionId => $teacherInfo) {
                $coachesToSubscribe = $teacherInfo['coaches_by_session'];
                SessionManager::updateCoaches($sessionId, $course['code'], $coachesToSubscribe, true);
            }
        }

        CourseManager::updateTeachers($courseId, $teachers, false, true, false);
    } else {
        // Normal behaviour
        CourseManager::updateTeachers($courseId, $teachers, true, false);

        // Updating session coaches
        $sessionCoaches = $course['session_coaches'];
        if (!empty($sessionCoaches)) {
            foreach ($sessionCoaches as $sessionId => $coachesToSubscribe) {
                //$coachesToSubscribe = isset($teacherInfo['coaches_by_session']) ? $teacherInfo['coaches_by_session'] : null;
                if (!empty($coachesToSubscribe)) {
                    SessionManager::updateCoaches(
                        $sessionId,
                        $course['code'],
                        $coachesToSubscribe,
                        true
                    );
                }
            }
        }
    }

    $sql = "INSERT IGNORE INTO " . $course_user_table . " SET
                course_code = '" . Database::escape_string($course_code) . "',
                user_id = '" . $tutor_id . "',
                status = '1',
                role = '',
                tutor_id='0',
                sort='0',
                user_course_cat='0'";
    Database::query($sql);

    if (array_key_exists('add_teachers_to_sessions_courses', $courseInfo)) {
        $sql = "UPDATE $course_table SET add_teachers_to_sessions_courses = '$addTeacherToSessionCourses'
                WHERE id = " . $courseInfo['real_id'];
        Database::query($sql);
    }

    $course_id = $courseInfo['real_id'];
    /* $forum_config_table = Database::get_course_table(TOOL_FORUM_CONFIG_TABLE);
      $sql = "UPDATE ".$forum_config_table." SET default_lang='".Database::escape_string($course_language)."' WHERE c_id = $course_id "; */

    Display::addFlash(Display::return_message(get_lang('ItemUpdated')));

    if ($visual_code_is_used) {
        header('Location: course_list.php?action=show_msg&warn=' . urlencode($warn));
    } else {
        header('Location: course_list.php');
    }
    exit;
}

Display::display_header($tool_name);

echo '<div class="actions">';
echo Display::url(Display::return_icon('back.png', get_lang('Back')), api_get_path(WEB_CODE_PATH).'admin/course_list.php');
echo Display::url(Display::return_icon('course_home.png', get_lang('CourseHome')), $courseInfo['course_public_url'], array('target' => '_blank'));
echo '</div>';

echo "<script>
function moveItem(origin , destination) {
    for (var i = 0 ; i<origin.options.length ; i++) {
        if (origin.options[i].selected) {
            destination.options[destination.length] = new Option(origin.options[i].text,origin.options[i].value);
            origin.options[i]=null;
            i = i-1;
        }
    }
    destination.selectedIndex = -1;
    sortOptions(destination.options);
}

function sortOptions(options) {

    newOptions = new Array();
    for (i = 0 ; i<options.length ; i++) {
        newOptions[i] = options[i];
    }
    newOptions = newOptions.sort(mysort);
    options.length = 0;
    for (i = 0 ; i < newOptions.length ; i++) {
        options[i] = newOptions[i];
    }
}

function mysort(a, b) {
    if (a.text.toLowerCase() > b.text.toLowerCase()) {
        return 1;
    }
    if (a.text.toLowerCase() < b.text.toLowerCase()) {
        return -1;
    }
    return 0;
}

function valide() {
    // Checking all multiple

    $('select').filter(function() {
        if ($(this).attr('multiple')) {
            $(this).find('option').each(function() {
                $(this).attr('selected', true);
            });
        }
    });
	//document.update_course.submit();
}
</script>";

// Display the form
$form->display();

Display :: display_footer();
