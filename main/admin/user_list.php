<?php
/* For licensing terms, see /license.txt */

use ChamiloSession as Session;

/**
 * @author Bart Mollet
 * @author Julio Montoya <gugli100@gmail.com> BeezNest 2011
 *
 * @package chamilo.admin
 */
$cidReset = true;
require_once __DIR__.'/../inc/global.inc.php';

api_protect_session_admin_list_users();

$urlId = api_get_current_access_url_id();
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// Login as can be used by different roles
if (isset($_GET['user_id']) && $action == 'login_as') {
    $check = Security::check_token('get');
    if ($check && api_can_login_as($_GET['user_id'])) {
        $result = UserManager::loginAsUser($_GET['user_id']);
        if ($result) {
            $userInfo = api_get_user_info();
            $userId = $userInfo['id'];
            $message = sprintf(
                get_lang('AttemptingToLoginAs'),
                $userInfo['complete_name_with_username'],
                '',
                $userId
            );

            $url = api_get_path(WEB_PATH).'user_portal.php';
            $goTo = sprintf(get_lang('LoginSuccessfulGoToX'), Display::url($url, $url));
            Display::display_header(get_lang('UserList'));
            echo Display::return_message($message, 'normal', false);
            echo Display::return_message($goTo, 'normal', false);
            Display::display_footer();
            exit;
        } else {
            api_not_allowed(true);
        }
    }
    Security::clear_token();
}

api_protect_admin_script(true);

trimVariables();

$url = api_get_path(WEB_AJAX_PATH).'course.ajax.php?a=get_user_courses';
$urlSession = api_get_path(WEB_AJAX_PATH).'session.ajax.php?a=get_user_sessions';
$extraField = new ExtraField('user');
$variables = $extraField->get_all_extra_field_by_type(ExtraField::FIELD_TYPE_TAG);
$variablesSelect = $extraField->get_all_extra_field_by_type(ExtraField::FIELD_TYPE_SELECT);
if (!empty($variablesSelect)) {
    $variables = array_merge($variables, $variablesSelect);
}
$variablesToShow = [];
if ($variables) {
    foreach ($variables as $variableId) {
        $extraFieldInfo = $extraField->get($variableId);
        $variablesToShow[] = $extraFieldInfo['variable'];
    }
}

$currentUser = api_get_current_user();

Session::write('variables_to_show', $variablesToShow);

$htmlHeadXtra[] = '<script>
function load_course_list (div_course,my_user_id) {
     $.ajax({
        contentType: "application/x-www-form-urlencoded",
        beforeSend: function(myObject) {
            $("div#"+div_course).html("<img src=\'../inc/lib/javascript/indicator.gif\' />"); },
        type: "POST",
        url: "'.$url.'",
        data: "user_id="+my_user_id,
        success: function(datos) {
            $("div#"+div_course).html(datos);
            $("div#div_"+my_user_id).attr("class","blackboard_show");
            $("div#div_"+my_user_id).attr("style","");
        }
    });
}

function load_session_list(div_session, my_user_id) {
     $.ajax({
        contentType: "application/x-www-form-urlencoded",
        beforeSend: function(myObject) {
            $("div#"+div_session).html("<img src=\'../inc/lib/javascript/indicator.gif\' />"); },
        type: "POST",
        url: "'.$urlSession.'",
        data: "user_id="+my_user_id,
        success: function(datos) {
            $("div#"+div_session).html(datos);
            $("div#div_s_"+my_user_id).attr("class","blackboard_show");
            $("div#div_s_"+my_user_id).attr("style","");
        }
    });
}

function active_user(element_div) {
    id_image=$(element_div).attr("id");
    image_clicked=$(element_div).attr("src");
    image_clicked_info = image_clicked.split("/");
    image_real_clicked = image_clicked_info[image_clicked_info.length-1];
    var status = 1;
    if (image_real_clicked == "accept.png") {
        status = 0;
    }
    user_id=id_image.split("_");
    ident="#img_"+user_id[1];
    if (confirm("'.get_lang('AreYouSureToEditTheUserStatus', '').'")) {
         $.ajax({
            contentType: "application/x-www-form-urlencoded",
            beforeSend: function(myObject) {
                $(ident).attr("src","'.Display::returnIconPath('loading1.gif').'"); }, //candy eye stuff
            type: "GET",
            url: "'.api_get_path(WEB_AJAX_PATH).'user_manager.ajax.php?a=active_user",
            data: "user_id="+user_id[1]+"&status="+status,
            success: function(data) {
                if (data == 1) {
                    $(ident).attr("src", "'.Display::returnIconPath('accept.png', ICON_SIZE_TINY).'");
                    $(ident).attr("title","'.get_lang('Lock').'");
                }
                if (data == 0) {
                    $(ident).attr("src","'.Display::returnIconPath('error.png').'");
                    $(ident).attr("title","'.get_lang('Unlock').'");
                }
                if (data == -1) {
                    $(ident).attr("src", "'.Display::returnIconPath('warning.png').'");
                    $(ident).attr("title","'.get_lang('ActionNotAllowed').'");
                }
            }
        });
    }
}

function clear_course_list(div_course) {
    $("div#"+div_course).html("");
    $("div#"+div_course).hide("");
}
function clear_session_list(div_session) {
    $("div#"+div_session).html("");
    $("div#"+div_session).hide("");
}

function display_advanced_search_form () {
    if ($("#advanced_search_form").css("display") == "none") {
        $("#advanced_search_form").css("display","block");
        $("#img_plus_and_minus").html(\'&nbsp;'.Display::returnFontAwesomeIcon('arrow-down').' '.get_lang('AdvancedSearch').'\');
    } else {
        $("#advanced_search_form").css("display","none");
        $("#img_plus_and_minus").html(\'&nbsp;'.Display::returnFontAwesomeIcon('arrow-right').' '.get_lang('AdvancedSearch').'\');
    }
}

$(document).ready(function() {
    var select_val = $("#input_select_extra_data").val();
    if ( document.getElementById(\'extra_data_text\')) {
        if (select_val != 0) {
            document.getElementById(\'extra_data_text\').style.display="block";
            if (document.getElementById(\'input_extra_text\'))
                document.getElementById(\'input_extra_text\').value = "";
        } else {
            document.getElementById(\'extra_data_text\').style.display="none";
        }
    }
});

//Load user calendar
function load_calendar(user_id, month, year) {
    var url = "'.api_get_path(WEB_AJAX_PATH).'agenda.ajax.php?a=get_user_agenda&user_id=" +user_id + "&month="+month+"&year="+year;
    $(".modal-body").load(url);
}
</script>';

$this_section = SECTION_PLATFORM_ADMIN;

/**
 * Trim variable values to avoid trailing spaces.
 */
function trimVariables()
{
    $filterVariables = [
        'keyword',
        'keyword_firstname',
        'keyword_lastname',
        'keyword_username',
        'keyword_email',
        'keyword_officialcode',
    ];

    foreach ($filterVariables as $variable) {
        if (isset($_GET[$variable])) {
            $_GET[$variable] = trim($_GET[$variable]);
        }
    }
}

/**
 * Prepares the shared SQL query for the user table.
 * See get_user_data() and get_number_of_users().
 *
 * @param bool $getCount Whether to count, or get data
 *
 * @return string SQL query
 */
function prepare_user_sql_query($getCount)
{
    $sql = '';
    $user_table = Database::get_main_table(TABLE_MAIN_USER);
    $admin_table = Database::get_main_table(TABLE_MAIN_ADMIN);

    if ($getCount) {
        $sql .= "SELECT COUNT(u.id) AS total_number_of_items FROM $user_table u";
    } else {
        $sql .= "SELECT u.id AS col0, u.official_code AS col2, ";

        if (api_is_western_name_order()) {
            $sql .= "u.firstname AS col3, u.lastname AS col4, ";
        } else {
            $sql .= "u.lastname AS col3, u.firstname AS col4, ";
        }

        $sql .= " u.username AS col5,
                    u.email AS col6,
                    u.status AS col7,
                    u.active AS col8,
                    u.id AS col9,
                    u.registration_date AS col10,
                    u.expiration_date AS exp,
                    u.password
                FROM $user_table u";
    }

    // adding the filter to see the user's only of the current access_url
    if ((api_is_platform_admin() || api_is_session_admin()) && api_get_multiple_access_url()) {
        $access_url_rel_user_table = Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
        $sql .= " INNER JOIN $access_url_rel_user_table url_rel_user 
                  ON (u.id=url_rel_user.user_id)";
    }

    $keywordList = [
        'keyword_firstname',
        'keyword_lastname',
        'keyword_username',
        'keyword_email',
        'keyword_officialcode',
        'keyword_status',
        'keyword_active',
        'keyword_inactive',
        'check_easy_passwords',
    ];

    $keywordListValues = [];
    $atLeastOne = false;
    foreach ($keywordList as $keyword) {
        $keywordListValues[$keyword] = null;
        if (isset($_GET[$keyword]) && !empty($_GET[$keyword])) {
            $keywordListValues[$keyword] = $_GET[$keyword];
            $atLeastOne = true;
        }
    }

    if ($atLeastOne == false) {
        $keywordListValues = [];
    }

    /*
    // This block is never executed because $keyword_extra_data never exists
    if (isset($keyword_extra_data) && !empty($keyword_extra_data)) {
        $extra_info = UserManager::get_extra_field_information_by_name($keyword_extra_data);
        $field_id = $extra_info['id'];
        $sql.= " INNER JOIN user_field_values ufv ON u.id=ufv.user_id AND ufv.field_id=$field_id ";
    }
    */

    if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
        $keywordFiltered = Database::escape_string("%".$_GET['keyword']."%");
        $sql .= " WHERE (
                    u.firstname LIKE '$keywordFiltered' OR
                    u.lastname LIKE '$keywordFiltered' OR
                    concat(u.firstname, ' ', u.lastname) LIKE '$keywordFiltered' OR
                    concat(u.lastname,' ',u.firstname) LIKE '$keywordFiltered' OR
                    u.username LIKE '$keywordFiltered' OR
                    u.official_code LIKE '$keywordFiltered' OR
                    u.email LIKE '$keywordFiltered'
                )
        ";
    } elseif (isset($keywordListValues) && !empty($keywordListValues)) {
        $query_admin_table = '';
        $keyword_admin = '';

        if (isset($keywordListValues['keyword_status']) &&
            $keywordListValues['keyword_status'] == PLATFORM_ADMIN
        ) {
            $query_admin_table = " , $admin_table a ";
            $keyword_admin = ' AND a.user_id = u.id ';
            $keywordListValues['keyword_status'] = '%';
        }

        $keyword_extra_value = '';

        // This block is never executed because $keyword_extra_data never exists
        /*
        if (isset($keyword_extra_data) && !empty($keyword_extra_data) &&
            !empty($keyword_extra_data_text)) {
            $keyword_extra_value = " AND ufv.field_value LIKE '%".trim($keyword_extra_data_text)."%' ";
        }
        */
        $sql .= " $query_admin_table
            WHERE (
                u.firstname LIKE '".Database::escape_string("%".$keywordListValues['keyword_firstname']."%")."' AND
                u.lastname LIKE '".Database::escape_string("%".$keywordListValues['keyword_lastname']."%")."' AND
                u.username LIKE '".Database::escape_string("%".$keywordListValues['keyword_username']."%")."' AND
                u.email LIKE '".Database::escape_string("%".$keywordListValues['keyword_email']."%")."' AND
                u.status LIKE '".Database::escape_string($keywordListValues['keyword_status'])."' ";
        if (!empty($keywordListValues['keyword_officialcode'])) {
            $sql .= " AND u.official_code LIKE '".Database::escape_string("%".$keywordListValues['keyword_officialcode']."%")."' ";
        }

        $sql .= "
            $keyword_admin
            $keyword_extra_value
        ";

        if (isset($keywordListValues['keyword_active']) &&
            !isset($keywordListValues['keyword_inactive'])
        ) {
            $sql .= " AND u.active = 1";
        } elseif (isset($keywordListValues['keyword_inactive']) &&
            !isset($keywordListValues['keyword_active'])
        ) {
            $sql .= " AND u.active = 0";
        }
        $sql .= " ) ";
    }

    $preventSessionAdminsToManageAllUsers = api_get_setting('prevent_session_admins_to_manage_all_users');
    if (api_is_session_admin() && $preventSessionAdminsToManageAllUsers === 'true') {
        $sql .= " AND u.creator_id = ".api_get_user_id();
    }

    $variables = Session::read('variables_to_show', []);
    if (!empty($variables)) {
        $extraField = new ExtraField('user');
        $extraFieldResult = [];
        $extraFieldHasData = [];
        foreach ($variables as $variable) {
            if (isset($_GET['extra_'.$variable])) {
                if (is_array($_GET['extra_'.$variable])) {
                    $values = $_GET['extra_'.$variable];
                } else {
                    $values = [$_GET['extra_'.$variable]];
                }

                if (empty($values)) {
                    continue;
                }

                $info = $extraField->get_handler_field_info_by_field_variable(
                    $variable
                );

                if (empty($info)) {
                    continue;
                }

                foreach ($values as $value) {
                    if (empty($value)) {
                        continue;
                    }
                    if ($info['field_type'] == ExtraField::FIELD_TYPE_TAG) {
                        $result = $extraField->getAllUserPerTag(
                            $info['id'],
                            $value
                        );
                        $result = empty($result) ? [] : array_column(
                            $result,
                            'user_id'
                        );
                    } else {
                        $result = UserManager::get_extra_user_data_by_value(
                            $variable,
                            $value
                        );
                    }
                    $extraFieldHasData[] = true;
                    if (!empty($result)) {
                        $extraFieldResult = array_merge(
                            $extraFieldResult,
                            $result
                        );
                    }
                }
            }
        }

        if (!empty($extraFieldHasData)) {
            $sql .= " AND (u.id IN ('".implode("','", $extraFieldResult)."')) ";
        }
    }

    // adding the filter to see the user's only of the current access_url
    if ((api_is_platform_admin() || api_is_session_admin()) &&
        api_get_multiple_access_url()
    ) {
        $sql .= " AND url_rel_user.access_url_id=".api_get_current_access_url_id();
    }

    return $sql;
}

/**
 * Get the total number of users on the platform.
 *
 * @see SortableTable#get_total_number_of_items()
 */
function get_number_of_users()
{
    $sql = prepare_user_sql_query(true);
    $res = Database::query($sql);
    $obj = Database::fetch_object($res);

    return $obj->total_number_of_items;
}

/**
 * Get the users to display on the current page (fill the sortable-table).
 *
 * @param   int     offset of first user to recover
 * @param   int     Number of users to get
 * @param   int     Column to sort on
 * @param   string  Order (ASC,DESC)
 *
 * @return array Users list
 *
 * @see SortableTable#get_table_data($from)
 */
function get_user_data($from, $number_of_items, $column, $direction)
{
    $sql = prepare_user_sql_query(false);
    if (!in_array($direction, ['ASC', 'DESC'])) {
        $direction = 'ASC';
    }
    $_admins_list = Session::read('admin_list', []);
    $column = intval($column);
    $from = intval($from);
    $number_of_items = intval($number_of_items);
    $sql .= " ORDER BY col$column $direction ";
    $sql .= " LIMIT $from,$number_of_items";

    $res = Database::query($sql);

    $users = [];
    $t = time();
    $currentUser = api_get_current_user();

    while ($user = Database::fetch_row($res)) {
        $userPicture = UserManager::getUserPicture(
            $user[0],
            USER_IMAGE_SIZE_SMALL
        );
        $is_admin = in_array($user[0], $_admins_list);
        $photo = '<img 
            src="'.$userPicture.'" class="rounded-circle avatar" 
            alt="'.api_get_person_name($user[2], $user[3]).'" 
            title="'.api_get_person_name($user[2], $user[3]).'" />';

        if ($user[7] == 1 && !empty($user[10])) {
            // check expiration date
            $expiration_time = convert_sql_date($user[10]);
            // if expiration date is passed, store a special value for active field
            if ($expiration_time < $t) {
                $user[7] = '-1';
            }
        }

        $iconAdmin = '';
        if ($is_admin) {
            $iconAdmin .= Display::return_icon(
                'admin_star.png',
                get_lang('IsAdministrator'),
                null,
                ICON_SIZE_SMALL
            );
        }

        $iconActive = null;
        $action = null;
        $image = null;

        if ($user[7] == '1') {
            $action = 'Lock';
            $image = 'accept';
        } elseif ($user[7] == '-1') {
            $action = 'edit';
            $image = 'warning';
        } elseif ($user[7] == '0') {
            $action = 'Unlock';
            $image = 'error';
        }

        if ($action === 'edit') {
            $iconActive = Display::return_icon(
                $image.'.png',
                get_lang('AccountExpired'),
                [],
                16
            );
        } elseif ($user['0'] != $currentUser->getId()) {
            // you cannot lock yourself out otherwise you could disable all the
            // accounts including your own => everybody is locked out and nobody
            // can change it anymore.
            $iconActive = Display::return_icon(
                $image.'.png',
                get_lang(ucfirst($action)),
                ['onclick' => 'active_user(this);', 'id' => 'img_'.$user['0']],
                16
            );
        }

        $profile = '<div class="avatar-user">'.$photo.'<span class="is-admin">'
            .$iconAdmin.'</span><span class="is-active">'.$iconActive.'</span></div>';

        // forget about the expiration date field
        $users[] = [
            $user[0],
            $profile,
            $user[1],
            $user[2],
            $user[3],
            $user[4],
            $user[5],
            $user[6],
            api_get_local_time($user[9]),
            $user[0],
        ];
    }

    return $users;
}

/**
 * Returns a mailto-link.
 *
 * @param string $email An email-address
 *
 * @return string HTML-code with a mailto-link
 */
function email_filter($email)
{
    return Display:: encrypted_mailto_link($email, $email, null, true);
}

/**
 * Returns a mailto-link.
 *
 * @param string $email  An email-address
 * @param array  $params Deprecated
 * @param array  $row
 *
 * @return string HTML-code with a mailto-link
 */
function user_filter($name, $params, $row)
{
    return '<a href="'.api_get_path(WEB_CODE_PATH).'admin/user_information.php?user_id='.$row[0].'">'.$name.'</a>';
}

/**
 * Build the modify-column of the table.
 *
 * @param   int     The user id
 * @param   string  URL params to add to table links
 * @param   array   Row of elements to alter
 *
 * @throws Exception
 *
 * @return string Some HTML-code with modify-buttons
 */
function modify_filter($user_id, $url_params, $row)
{
    $statusname = api_get_status_langvars();
    $user_is_anonymous = false;
    $current_user_status_label = $row['7'];

    if ($current_user_status_label == $statusname[ANONYMOUS]) {
        $user_is_anonymous = true;
    }
    $result = '<div class="toolbar-table">';
    if (!$user_is_anonymous) {
        $icon = Display::return_icon(
            'course.png',
            get_lang('Courses'),
            ['onmouseout' => 'clear_course_list (\'div_'.$user_id.'\')']
        );
        $result .= '<a href="javascript:void(0)" onclick="load_course_list(\'div_'.$user_id.'\','.$user_id.')" >
                    '.$icon.'
                    <div class="blackboard_hide" id="div_'.$user_id.'"></div>
                    </a>';

        $icon = Display::return_icon(
            'session.png',
            get_lang('Sessions'),
            ['onmouseout' => 'clear_session_list (\'div_s_'.$user_id.'\')']
        );
        $result .= '<a href="javascript:void(0)" onclick="load_session_list(\'div_s_'.$user_id.'\','.$user_id.')" >
                    '.$icon.'
                    <div class="blackboard_hide" id="div_s_'.$user_id.'"></div>
                    </a>';
    } else {
        $result .= Display::return_icon('course_na.png', get_lang('Courses'));
        $result .= Display::return_icon('course_na.png', get_lang('Sessions'));
    }

    if (api_is_platform_admin()) {
        if (!$user_is_anonymous) {
            $result .= '<a href="user_information.php?user_id='.$user_id.'">'.
                Display::return_icon('info2.png', get_lang('Info')).'</a>';
        } else {
            $result .= Display::return_icon('info2_na.png', get_lang('Info'));
        }
    }

    //only allow platform admins to login_as, or session admins only for students (not teachers nor other admins)
    $loginAsStatusForSessionAdmins = [$statusname[STUDENT]];

    //except when session.allow_session_admin_login_as_teacher is enabled, then can login_as teachers also
    if (api_get_configuration_value('session.allow_session_admin_login_as_teacher')) {
        $loginAsStatusForSessionAdmins[] = $statusname[COURSEMANAGER];
    }

    $sessionAdminCanLoginAs = api_is_session_admin() &&
        in_array($current_user_status_label, $loginAsStatusForSessionAdmins);

    if (api_is_platform_admin() || $sessionAdminCanLoginAs) {
        if (!$user_is_anonymous) {
            if (api_global_admin_can_edit_admin($user_id, null, $sessionAdminCanLoginAs)) {
                $result .= '<a href="user_list.php?action=login_as&user_id='.$user_id.'&sec_token='.Security::getTokenFromSession().'">'.
                    Display::return_icon('login_as.png', get_lang('LoginAs')).'</a>';
            } else {
                $result .= Display::return_icon('login_as_na.png', get_lang('LoginAs'));
            }
        } else {
            $result .= Display::return_icon('login_as_na.png', get_lang('LoginAs'));
        }
    } else {
        $result .= Display::return_icon('login_as_na.png', get_lang('LoginAs'));
    }

    if ($current_user_status_label != $statusname[STUDENT]) {
        $result .= Display::return_icon(
            'statistics_na.png',
            get_lang('Reporting')
        );
    } else {
        $result .= '<a href="../mySpace/myStudents.php?student='.$user_id.'">'.
            Display::return_icon('statistics.png', get_lang('Reporting')).
            '</a>';
    }

    if (api_is_platform_admin(true)) {
        $editProfileUrl = Display::getProfileEditionLink($user_id, true);
        if (!$user_is_anonymous &&
            api_global_admin_can_edit_admin($user_id, null, true)
        ) {
            $result .= '<a href="'.$editProfileUrl.'">'.
                Display::return_icon(
                    'edit.png',
                    get_lang('Edit'),
                    [],
                    ICON_SIZE_SMALL
                ).
                '</a>';
        } else {
            $result .= Display::return_icon(
                    'edit_na.png',
                    get_lang('Edit'),
                    [],
                    ICON_SIZE_SMALL
                ).'</a>';
        }
    }

    $allowAssignSkill = api_is_platform_admin(false, true);

    if ($allowAssignSkill) {
        $result .= Display::url(
            Display::return_icon(
                'skill-badges.png',
                get_lang('AssignSkill'),
                null,
                ICON_SIZE_SMALL
            ),
            api_get_path(WEB_CODE_PATH).'badge/assign.php?'.http_build_query(['user' => $user_id])
        );
    }

    // actions for assigning sessions, courses or users
    if (!api_is_session_admin()) {
        if ($current_user_status_label == $statusname[SESSIONADMIN]) {
            $result .= Display::url(
                Display::return_icon(
                    'view_more_stats.gif',
                    get_lang('AssignSessions')
                ),
                "dashboard_add_sessions_to_user.php?user={$user_id}"
            );
        } else {
            if ($current_user_status_label == $statusname[DRH] ||
                UserManager::is_admin($user_id) ||
                $current_user_status_label == $statusname[STUDENT_BOSS]
            ) {
                $result .= Display::url(
                    Display::return_icon(
                        'user_subscribe_course.png',
                        get_lang('AssignUsers'),
                        '',
                        ICON_SIZE_SMALL
                    ),
                    "dashboard_add_users_to_user.php?user={$user_id}"
                );
            }

            if ($current_user_status_label == $statusname[DRH] || UserManager::is_admin($user_id)) {
                $result .= Display::url(
                    Display::return_icon(
                        'add.png',
                        get_lang('AssignCourses')
                    ),
                    "dashboard_add_courses_to_user.php?user={$user_id}"
                );

                $result .= Display::url(
                    Display::return_icon(
                        'view_more_stats.gif',
                        get_lang('AssignSessions')
                    ),
                    "dashboard_add_sessions_to_user.php?user={$user_id}"
                );
            }
        }
    }

    $allowDelete = api_get_configuration_value('allow_delete_user_for_session_admin');

    if (api_is_session_admin() && $allowDelete) {
        if ($user_id != api_get_user_id() &&
            !$user_is_anonymous &&
            api_global_admin_can_edit_admin($user_id, null, true)
        ) {
            // you cannot lock yourself out otherwise you could disable all the accounts including your own => everybody is locked out and nobody can change it anymore.
            $result .= ' <a href="user_list.php?action=delete_user&user_id='.$user_id.'&'.$url_params.'&sec_token='.Security::getTokenFromSession().'"  title="'.addslashes(api_htmlentities(get_lang('ConfirmYourChoice'))).'" class="delete-swal">'.
                Display::return_icon(
                    'delete.png',
                    get_lang('Delete'),
                    [],
                    ICON_SIZE_SMALL
                ).
                '</a>';
        }
    }
    if (api_is_platform_admin()) {
        $result .= ' <a data-title="'.get_lang('FreeBusyCalendar').'" href="'.api_get_path(WEB_AJAX_PATH).'agenda.ajax.php?a=get_user_agenda&user_id='.$user_id.'&modal_size=lg" class="agenda_opener ajax">'.
            Display::return_icon(
                'calendar.png',
                get_lang('FreeBusyCalendar'),
                [],
                ICON_SIZE_SMALL
            ).
            '</a>';

        if ($user_id != api_get_user_id() &&
            !$user_is_anonymous &&
            api_global_admin_can_edit_admin($user_id)
        ) {
            $result .= ' <a href="user_list.php?action=anonymize&user_id='.$user_id.'&'.$url_params.'&sec_token='.Security::getTokenFromSession().'"  class="delete-swal" title="'.addslashes(api_htmlentities(get_lang("ConfirmYourChoice"))).'" >'.
                Display::return_icon(
                    'anonymous.png',
                    get_lang('Anonymize'),
                    [],
                    ICON_SIZE_SMALL
                ).
                '</a>';
        }

        $deleteAllowed = !api_get_configuration_value('deny_delete_users');
        if ($deleteAllowed) {
            if ($user_id != api_get_user_id() &&
                !$user_is_anonymous &&
                api_global_admin_can_edit_admin($user_id)
            ) {
                // you cannot lock yourself out otherwise you could disable all the accounts
                // including your own => everybody is locked out and nobody can change it anymore.
                $result .= ' <a href="user_list.php?action=delete_user&user_id='.$user_id.'&'.$url_params.'&sec_token='.Security::getTokenFromSession().'"  title="'.addslashes(api_htmlentities(get_lang("ConfirmYourChoice"))).'" class="delete-swal">'.
                    Display::return_icon(
                        'delete.png',
                        get_lang('Delete'),
                        [],
                        ICON_SIZE_SMALL
                    ).
                    '</a>';
            } else {
                $result .= Display::return_icon(
                    'delete_na.png',
                    get_lang('Delete'),
                    [],
                    ICON_SIZE_SMALL
                );
            }
        }
        $result .= '</div>';
    }

    return $result;
}

/**
 * Build the active-column of the table to lock or unlock a certain user
 * lock = the user can no longer use this account.
 *
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 *
 * @param int    $active the current state of the account
 * @param string $params
 * @param array  $row
 *
 * @return string Some HTML-code with the lock/unlock button
 */
function active_filter($active, $params, $row)
{
    $currentUser = api_get_current_user();

    if ($active == '1') {
        $action = 'Lock';
        $image = 'accept';
    } elseif ($active == '-1') {
        $action = 'edit';
        $image = 'warning';
    } elseif ($active == '0') {
        $action = 'Unlock';
        $image = 'error';
    }

    $result = '';

    if ($action === 'edit') {
        $result = Display::return_icon(
            $image.'.png',
            get_lang('AccountExpired'),
            [],
            16
        );
    } elseif ($row['0'] != $currentUser->getId()) {
        // you cannot lock yourself out otherwise you could disable all the
        // accounts including your own => everybody is locked out and nobody
        // can change it anymore.
        $result = Display::return_icon(
            $image.'.png',
            get_lang(ucfirst($action)),
            ['onclick' => 'active_user(this);', 'id' => 'img_'.$row['0']],
            16
        );
    }

    return $result;
}

/**
 * Instead of displaying the integer of the status, we give a translation for the status.
 *
 * @param int $status
 *
 * @return string translation
 *
 * @version march 2008
 *
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University, Belgium
 */
function status_filter($status)
{
    $statusname = api_get_status_langvars();

    return $statusname[$status];
}

if (isset($_GET['keyword']) || isset($_GET['keyword_firstname'])) {
    $interbreadcrumb[] = ["url" => 'index.php', "name" => get_lang('PlatformAdmin')];
    $interbreadcrumb[] = ["url" => 'user_list.php', "name" => get_lang('UserList')];
    $tool_name = get_lang('SearchUsers');
} else {
    $interbreadcrumb[] = ["url" => 'index.php', "name" => get_lang('PlatformAdmin')];
    $tool_name = get_lang('UserList');
}

$message = '';

if (!empty($action)) {
    $check = Security::check_token('get');
    if ($check) {
        switch ($action) {
            case 'add_user_to_my_url':
                $user_id = $_REQUEST['user_id'];
                $result = UrlManager::add_user_to_url($user_id, $urlId);
                if ($result) {
                    $user_info = api_get_user_info($user_id);
                    $message = get_lang('UserAdded').' '.$user_info['complete_name_with_username'];
                    $message = Display::return_message($message, 'confirmation');
                }
                break;
            case 'delete_user':
                $message = UserManager::deleteUserWithVerification($_GET['user_id']);
                Display::addFlash($message);
                header('Location: '.api_get_self());
                exit;
                break;
            case 'delete':
                if (api_is_platform_admin() && !empty($_POST['id'])) {
                    $number_of_selected_users = count($_POST['id']);
                    $number_of_affected_users = 0;
                    if (is_array($_POST['id'])) {
                        foreach ($_POST['id'] as $index => $user_id) {
                            if ($user_id != $currentUser->getId()) {
                                if (UserManager::delete_user($user_id)) {
                                    $number_of_affected_users++;
                                }
                            }
                        }
                    }
                    if ($number_of_selected_users == $number_of_affected_users) {
                        $message = Display::return_message(
                            get_lang('SelectedUsersDeleted'),
                            'confirmation'
                        );
                    } else {
                        $message = Display::return_message(
                            get_lang('SomeUsersNotDeleted'),
                            'error'
                        );
                    }
                }
                break;
            case 'disable':
                if (api_is_platform_admin()) {
                    $number_of_selected_users = count($_POST['id']);
                    $number_of_affected_users = 0;
                    if (is_array($_POST['id'])) {
                        foreach ($_POST['id'] as $index => $user_id) {
                            if ($user_id != $currentUser->getId()) {
                                if (UserManager::disable($user_id)) {
                                    $number_of_affected_users++;
                                }
                            }
                        }
                    }
                    if ($number_of_selected_users == $number_of_affected_users) {
                        $message = Display::return_message(
                            get_lang('SelectedUsersDisabled'),
                            'confirmation'
                        );
                    } else {
                        $message = Display::return_message(
                            get_lang('SomeUsersNotDisabled'),
                            'error'
                        );
                    }
                }
                break;
            case 'enable':
                if (api_is_platform_admin()) {
                    $number_of_selected_users = count($_POST['id']);
                    $number_of_affected_users = 0;
                    if (is_array($_POST['id'])) {
                        foreach ($_POST['id'] as $index => $user_id) {
                            if ($user_id != $currentUser->getId()) {
                                if (UserManager::enable($user_id)) {
                                    $number_of_affected_users++;
                                }
                            }
                        }
                    }
                    if ($number_of_selected_users == $number_of_affected_users) {
                        $message = Display::return_message(
                            get_lang('SelectedUsersEnabled'),
                            'confirmation'
                        );
                    } else {
                        $message = Display::return_message(
                            get_lang('SomeUsersNotEnabled'),
                            'error'
                        );
                    }
                }
                break;
            case 'anonymize':
                $message = UserManager::anonymizeUserWithVerification($_GET['user_id']);
                Display::addFlash($message);
                header('Location: '.api_get_self());
                exit;
                break;
        }
        Security::clear_token();
    }
}

// Create a search-box
$form = new FormValidator('search_simple', 'get', null, null, null, 'inline');
$form->addText(
    'keyword',
    get_lang('Search'),
    false,
    [
        'aria-label' => get_lang("SearchUsers"),
    ]
);
$form->addButtonSearch(get_lang('Search'));

$searchAdvanced = '
<a id="advanced_params" href="javascript://" 
    class="btn btn-light advanced_options" onclick="display_advanced_search_form();">
    <span id="img_plus_and_minus">&nbsp;
    '.Display::returnFontAwesomeIcon('arrow-right').' '.get_lang('AdvancedSearch').'
    </span>
</a>';
$actionsLeft = '';
$actionsCenter = '';
$actionsRight = '';
if (api_is_platform_admin()) {
    $actionsRight .= '<a class="float-right" href="'.api_get_path(WEB_CODE_PATH).'admin/user_add.php">'.
        Display::return_icon('new_user.png', get_lang('AddUsers'), '', ICON_SIZE_MEDIUM).'</a>';
}

$actionsLeft .= $form->returnForm();
$actionsCenter .= $searchAdvanced;

if (isset($_GET['keyword'])) {
    $parameters = ['keyword' => Security::remove_XSS($_GET['keyword'])];
} elseif (isset($_GET['keyword_firstname'])) {
    $parameters['keyword_firstname'] = Security::remove_XSS($_GET['keyword_firstname']);
    $parameters['keyword_lastname'] = Security::remove_XSS($_GET['keyword_lastname']);
    $parameters['keyword_username'] = Security::remove_XSS($_GET['keyword_username']);
    $parameters['keyword_email'] = Security::remove_XSS($_GET['keyword_email']);
    $parameters['keyword_officialcode'] = Security::remove_XSS($_GET['keyword_officialcode']);
    $parameters['keyword_status'] = Security::remove_XSS($_GET['keyword_status']);
    $parameters['keyword_active'] = Security::remove_XSS($_GET['keyword_active']);
    $parameters['keyword_inactive'] = Security::remove_XSS($_GET['keyword_inactive']);
}
// Create a sortable table with user-data
$parameters['sec_token'] = Security::get_token();

$_admins_list = array_keys(UserManager::get_all_administrators());
Session::write('admin_list', $_admins_list);
// Display Advanced search form.
$form = new FormValidator(
    'advanced_search',
    'get',
    '',
    '',
    [],
    FormValidator::LAYOUT_HORIZONTAL
);

$form->addElement('html', '<div id="advanced_search_form" style="display:none;">');
$form->addElement('header', get_lang('AdvancedSearch'));
$form->addText('keyword_firstname', get_lang('FirstName'), false);
$form->addText('keyword_lastname', get_lang('LastName'), false);
$form->addText('keyword_username', get_lang('LoginName'), false);
$form->addText('keyword_email', get_lang('Email'), false);
$form->addText('keyword_officialcode', get_lang('OfficialCode'), false);

$status_options = [];
$status_options['%'] = get_lang('All');
$status_options[STUDENT] = get_lang('Student');
$status_options[COURSEMANAGER] = get_lang('Teacher');
$status_options[DRH] = get_lang('Drh');
$status_options[SESSIONADMIN] = get_lang('SessionsAdmin');
$status_options[PLATFORM_ADMIN] = get_lang('Administrator');

$form->addElement(
    'select',
    'keyword_status',
    get_lang('Profile'),
    $status_options
);

$active_group = [];
$active_group[] = $form->createElement('checkbox', 'keyword_active', '', get_lang('Active'));
$active_group[] = $form->createElement('checkbox', 'keyword_inactive', '', get_lang('Inactive'));
$form->addGroup($active_group, '', get_lang('ActiveAccount'), null, false);
$form->addElement('checkbox', 'check_easy_passwords', null, get_lang('CheckEasyPasswords'));
$data = $extraField->addElements($form, 0, [], true, false, $variablesToShow);

$htmlHeadXtra[] = '
    <script>
    $(document).ready(function() {
        '.$data['jquery_ready_content'].'
    })
    </script>
';

$form->addButtonSearch(get_lang('SearchUsers'));

$defaults = [];
$defaults['keyword_active'] = 1;
$defaults['keyword_inactive'] = 1;
$form->setDefaults($defaults);
$form->addElement('html', '</div>');

$form = $form->returnForm();

$table = new SortableTable(
    'users',
    'get_number_of_users',
    'get_user_data',
    (api_is_western_name_order() xor api_sort_by_first_name()) ? 3 : 2
);
$table->set_additional_parameters($parameters);
$table->set_header(0, '', false, 'width="18px"');
$table->set_header(1, get_lang('Photo'), false);
$table->set_header(2, get_lang('OfficialCode'));

if (api_is_western_name_order()) {
    $table->set_header(3, get_lang('FirstName'));
    $table->set_header(4, get_lang('LastName'));
} else {
    $table->set_header(3, get_lang('LastName'));
    $table->set_header(4, get_lang('FirstName'));
}
$table->set_header(5, get_lang('LoginName'));
$table->set_header(6, get_lang('Email'));
$table->set_header(7, get_lang('Profile'));
//$table->set_header(8, get_lang('Active'), true);
$table->set_header(8, get_lang('RegistrationDate'), true);
$table->set_header(9, get_lang('Action'), false);

$table->set_column_filter(3, 'user_filter');
$table->set_column_filter(4, 'user_filter');
$table->set_column_filter(6, 'email_filter');
$table->set_column_filter(7, 'status_filter');
//$table->set_column_filter(8, 'active_filter');
$table->set_column_filter(9, 'modify_filter');

// Only show empty actions bar if delete users has been blocked
$actionsList = [];
if (api_is_platform_admin() &&
    !api_get_configuration_value('deny_delete_users')
) {
    $actionsList['delete'] = get_lang('DeleteFromPlatform');
}
$actionsList['disable'] = get_lang('Disable');
$actionsList['enable'] = get_lang('Enable');
$table->set_form_actions($actionsList);

$table_result = $table->return_table();
$extra_search_options = '';

//Try to search the user everywhere
if ($table->get_total_number_of_items() == 0) {
    if (api_get_multiple_access_url() && isset($_REQUEST['keyword'])) {
        $keyword = Database::escape_string($_REQUEST['keyword']);
        $conditions = ['username' => $keyword];
        $user_list = UserManager::get_user_list(
            $conditions,
            [],
            false,
            ' OR '
        );
        if (!empty($user_list)) {
            $extra_search_options = Display::page_subheader(get_lang('UsersFoundInOtherPortals'));

            $table = new HTML_Table(['class' => 'data_table']);
            $column = 0;
            $row = 0;
            $headers = [get_lang('User'), 'URL', get_lang('Actions')];
            foreach ($headers as $header) {
                $table->setHeaderContents($row, $column, $header);
                $column++;
            }
            $row++;

            foreach ($user_list as $user) {
                $column = 0;
                $access_info = UrlManager::get_access_url_from_user($user['id']);
                $access_info_to_string = '';
                $add_user = true;
                if (!empty($access_info)) {
                    foreach ($access_info as $url_info) {
                        if ($urlId == $url_info['access_url_id']) {
                            $add_user = false;
                        }
                        $access_info_to_string .= $url_info['url'].'<br />';
                    }
                }
                if ($add_user) {
                    $row_table = [];
                    $row_table[] = api_get_person_name($user['firstname'],
                            $user['lastname']).' ('.$user['username'].') ';
                    $row_table[] = $access_info_to_string;
                    $url = api_get_self().'?action=add_user_to_my_url&user_id='.$user['id'].'&sec_token='.Security::getTokenFromSession();
                    $row_table[] = Display::url(
                        get_lang('AddUserToMyURL'),
                        $url,
                        ['class' => 'btn']
                    );

                    foreach ($row_table as $cell) {
                        $table->setCellContents($row, $column, $cell);
                        $table->updateCellAttributes(
                            $row,
                            $column,
                            'align="center"'
                        );
                        $column++;
                    }
                    $table->updateRowAttributes(
                        $row,
                        $row % 2 ? 'class="row_even"' : 'class="row_odd"',
                        true
                    );
                    $row++;
                }
            }
            $extra_search_options .= $table->toHtml();
            $table_result = '';
        }
    }
}
$toolbarActions = Display::toolbarAction(
    'toolbarUser',
    [$actionsLeft, $actionsCenter, $actionsRight],
    [4, 4, 4]
);

$tpl = new Template($tool_name);
$tpl->assign('actions', $toolbarActions);
$tpl->assign('message', $message);
$tpl->assign('content', $form.$table_result.$extra_search_options);
$tpl->display_one_col_template();
