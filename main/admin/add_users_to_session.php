<?php
/* For licensing terms, see /license.txt */

/**
*	@package chamilo.admin
*/

// name of the language file that needs to be included
$language_file = array('admin','registration');

// resetting the course id
$cidReset = true;

// including some necessary files
require_once '../inc/global.inc.php';
require_once '../inc/lib/xajax/xajax.inc.php';
$xajax = new xajax();

$xajax->registerFunction('search_users');

// setting the section (for the tabs)
$this_section = SECTION_PLATFORM_ADMIN;

$id_session = intval($_GET['id_session']);
$countSessionCoursesList = SessionManager::get_course_list_by_session_id($id_session, null, null, true);

$addProcess = isset($_GET['add']) ? Security::remove_XSS($_GET['add']) : null;

SessionManager::protect_session_edit($id_session);

// setting breadcrumbs
$interbreadcrumb[] = array('url' => 'index.php','name' => get_lang('PlatformAdmin'));
$interbreadcrumb[] = array('url' => 'session_list.php','name' => get_lang('SessionList'));
$interbreadcrumb[] = array('url' => "resume_session.php?id_session=".$id_session,"name" => get_lang('SessionOverview'));

// Database Table Definitions
$tbl_session = Database::get_main_table(TABLE_MAIN_SESSION);
$tbl_course = Database::get_main_table(TABLE_MAIN_COURSE);
$tbl_user = Database::get_main_table(TABLE_MAIN_USER);
$tbl_session_rel_user = Database::get_main_table(TABLE_MAIN_SESSION_USER);

// setting the name of the tool
$tool_name = get_lang('SubscribeUsersToSession');
$add_type = 'unique';

if (isset($_REQUEST['add_type']) && $_REQUEST['add_type']!='') {
	$add_type = Security::remove_XSS($_REQUEST['add_type']);
}

$page = isset($_GET['page']) ? Security::remove_XSS($_GET['page']) : null;

// Checking for extra field with filter on

$extra_field_list = UserManager::get_extra_fields();

$new_field_list = array();
if (is_array($extra_field_list)) {
    foreach ($extra_field_list as $extra_field) {
        //if is enabled to filter and is a "<select>" field type
        if ($extra_field[8]==1 && $extra_field[2]==4) {
            $new_field_list[] = array(
                'name'=> $extra_field[3],
                'variable'=>$extra_field[1],
                'data'=> $extra_field[9]
            );
        }
    }
}

function search_users($needle, $type)
{
    global $tbl_user, $tbl_session_rel_user, $id_session;

    $xajax_response = new XajaxResponse();
    $return = '';

    if (!empty($needle) && !empty($type)) {

        // Normal behaviour
        if ($type == 'any_session' && $needle == 'false') {
            $type = 'multiple';
            $needle = '';
        }

        // xajax send utf8 datas... datas in db can be non-utf8 datas
        $charset = api_get_system_encoding();
        $needle = api_convert_encoding($needle, $charset, 'utf-8');
        $needle = Database::escape_string($needle);
        $order_clause = api_sort_by_first_name() ? ' ORDER BY firstname, lastname, username' : ' ORDER BY lastname, firstname, username';
        $showOfficialCode = false;
        global $_configuration;
        if (isset($_configuration['order_user_list_by_official_code']) &&
            $_configuration['order_user_list_by_official_code']
        ) {
            $showOfficialCode = true;
            $order_clause = ' ORDER BY official_code, firstname, lastname, username';
        }
        if (api_is_session_admin()
            && isset($_configuration['prevent_session_admins_to_manage_all_users'])
            && $_configuration['prevent_session_admins_to_manage_all_users'] == 'true'
        ) {
            $order_clause = " AND user.creator_id = " . api_get_user_id() . $order_clause;
        }

        $cond_user_id = '';

        // Only for single & multiple
        if (in_array($type, array('single','multiple')))
        if (!empty($id_session)) {
            $id_session = intval($id_session);
            // check id_user from session_rel_user table
            $sql = 'SELECT id_user FROM '.$tbl_session_rel_user.'
                    WHERE id_session ="'.$id_session.'" AND relation_type<>'.SESSION_RELATION_TYPE_RRHH.' ';
            $res = Database::query($sql);
            $user_ids = array();
            if (Database::num_rows($res) > 0) {
                while ($row = Database::fetch_row($res)) {
                    $user_ids[] = (int)$row[0];
                }
            }
            if (count($user_ids) > 0) {
                $cond_user_id = ' AND user.user_id NOT IN('.implode(",",$user_ids).')';
            }
        }
        switch ($type) {
            case 'single':
                // search users where username or firstname or lastname begins likes $needle
                $sql = 'SELECT user.user_id, username, lastname, firstname, official_code
                        FROM '.$tbl_user.' user
                        WHERE
                            (
                                username LIKE "'.$needle.'%" OR
                                firstname LIKE "'.$needle.'%" OR
                                lastname LIKE "'.$needle.'%"
                            ) AND
                            user.status <> 6 AND
                            user.status <> '.DRH.''.
                        $order_clause.'
                        LIMIT 11';
                break;
            case 'multiple':
                $sql = 'SELECT user.user_id, username, lastname, firstname, official_code
                        FROM '.$tbl_user.' user
                        WHERE
                            '.(api_sort_by_first_name() ? 'firstname' : 'lastname').' LIKE "'.$needle.'%" AND
                            user.status <> '.DRH.' AND
                            user.status <> 6 '.$cond_user_id.
                        $order_clause;
                break;
            case 'any_session':
                $sql = 'SELECT DISTINCT user.user_id, username, lastname, firstname, official_code
                        FROM '.$tbl_user.' user
                        LEFT OUTER JOIN '.$tbl_session_rel_user.' s ON (s.id_user = user.user_id)
                        WHERE
                            s.id_user IS null AND
                            user.status<>'.DRH.' AND
                            user.status<>6 '.$cond_user_id.
                        $order_clause;
                break;
        }

        if (api_is_multiple_url_enabled()) {
            $tbl_user_rel_access_url= Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
            $access_url_id = api_get_current_access_url_id();
            if ($access_url_id != -1) {
                switch ($type) {
                    case 'single':
                        $sql = 'SELECT user.user_id, username, lastname, firstname, official_code
                        FROM '.$tbl_user.' user
                        INNER JOIN '.$tbl_user_rel_access_url.' url_user ON (url_user.user_id=user.user_id)
                        WHERE
                            access_url_id = '.$access_url_id.' AND
                            (
                                username LIKE "'.$needle.'%" OR
                                firstname LIKE "'.$needle.'%" OR
                                lastname LIKE "'.$needle.'%"
                            ) AND user.status<>6 AND
                            user.status<>'.DRH.' '.
                        $order_clause.
                        ' LIMIT 11';
                        break;
                    case 'multiple':
                        $sql = 'SELECT user.user_id, username, lastname, firstname, official_code
                                FROM '.$tbl_user.' user
                                INNER JOIN '.$tbl_user_rel_access_url.' url_user ON (url_user.user_id=user.user_id)
                                WHERE
                                    access_url_id = '.$access_url_id.' AND
                                    '.(api_sort_by_first_name() ? 'firstname' : 'lastname').' LIKE "'.$needle.'%" AND
                                    user.status<>'.DRH.' AND
                                    user.status<>6 '.$cond_user_id.
                                $order_clause;
                        break;
                    case 'any_session' :
                        $sql = 'SELECT DISTINCT user.user_id, username, lastname, firstname, official_code
                            FROM '.$tbl_user.' user
                            LEFT OUTER JOIN '.$tbl_session_rel_user.' s ON (s.id_user = user.user_id)
                            INNER JOIN '.$tbl_user_rel_access_url.' url_user ON (url_user.user_id=user.user_id)
                            WHERE
                                access_url_id = '.$access_url_id.' AND
                                s.id_user IS null AND
                                user.status<>'.DRH.' AND
                                user.status<>6 '.$cond_user_id.
                            $order_clause;
                        break;
                }
            }
        }
        //echo Database::fixQuery($sql);
        $rs = Database::query($sql);
        $i = 0;
        if ($type=='single') {
            while ($user = Database :: fetch_array($rs)) {
                $i++;
                if ($i<=10) {
                    $person_name = api_get_person_name($user['firstname'], $user['lastname']).' ('.$user['username'].') '.$user['official_code'];
                    if ($showOfficialCode) {
                        $officialCode = !empty($user['official_code']) ? $user['official_code'].' - ' : '? - ';
                        $person_name = $officialCode.api_get_person_name($user['firstname'], $user['lastname']).' ('.$user['username'].')';
                    }

                    $return .= '<a href="javascript: void(0);" onclick="javascript: add_user_to_session(\''.$user['user_id'].'\',\''.$person_name.' '.'\')">'.$person_name.' </a><br />';
                } else {
                    $return .= '...<br />';
                }
            }

            $xajax_response -> addAssign('ajax_list_users_single','innerHTML',api_utf8_encode($return));
        } else {
            global $nosessionUsersList;
            $return .= '<select id="origin_users" name="nosessionUsersList[]" multiple="multiple" size="15" style="width:360px;">';
            while ($user = Database :: fetch_array($rs)) {
                $person_name = api_get_person_name($user['firstname'], $user['lastname']).' ('.$user['username'].') '.$user['official_code'];
                if ($showOfficialCode) {
                    $officialCode = !empty($user['official_code']) ? $user['official_code'].' - ' : '? - ';
                    $person_name = $officialCode.api_get_person_name($user['firstname'], $user['lastname']).' ('.$user['username'].')';
                }
	            $return .= '<option value="'.$user['user_id'].'">'.$person_name.' </option>';
			}
			$return .= '</select>';
			$xajax_response -> addAssign('ajax_list_users_multiple','innerHTML',api_utf8_encode($return));
		}
	}

	return $xajax_response;
}

$xajax->processRequests();

$htmlHeadXtra[] = $xajax->getJavascript('../inc/lib/xajax/');
$htmlHeadXtra[] = '
<script type="text/javascript">
function add_user_to_session (code, content) {
	document.getElementById("user_to_add").value = "";
	document.getElementById("ajax_list_users_single").innerHTML = "";

	destination = document.getElementById("destination_users");

	for (i=0;i<destination.length;i++) {
		if(destination.options[i].text == content) {
				return false;
		}
	}
	destination.options[destination.length] = new Option(content,code);
	destination.selectedIndex = -1;
	sortOptions(destination.options);
}

function remove_item(origin) {
	for(var i = 0 ; i<origin.options.length ; i++) {
		if(origin.options[i].selected) {
			origin.options[i]=null;
			i = i-1;
		}
	}
}

function validate_filter() {
    document.formulaire.add_type.value = \''.$add_type.'\';
    document.formulaire.form_sent.value=0;
    document.formulaire.submit();
}

function checked_in_no_session(checked) {
    $("#first_letter_user")
    .find("option")
    .attr("selected", false);
    xajax_search_users(checked, "any_session");
}

function change_select(val) {
    $("#user_with_any_session_id").attr("checked", false);
    xajax_search_users(val,"multiple");
}
</script>';

$form_sent = 0;
$errorMsg = $firstLetterUser = $firstLetterSession = '';
$UserList = $SessionList = array();
$sessions = array();
$noPHP_SELF = true;

if (isset($_POST['form_sent']) && $_POST['form_sent']) {
    $form_sent             = $_POST['form_sent'];
    $firstLetterUser       = $_POST['firstLetterUser'];
    $firstLetterSession    = $_POST['firstLetterSession'];
    $UserList              = $_POST['sessionUsersList'];

    if (!is_array($UserList)) {
        $UserList=array();
    }

    if ($form_sent == 1) {
        // Added a parameter to send emails when registering a user
        SessionManager::suscribe_users_to_session($id_session, $UserList, null, true);
        header('Location: resume_session.php?id_session='.$id_session);
        exit;
    }
}

$session_info = SessionManager::fetch($id_session);
Display::display_header($tool_name);

$nosessionUsersList = $sessionUsersList = array();
$where_filter = null;
$ajax_search = $add_type == 'unique' ? true : false;

$order_clause = api_sort_by_first_name() ? ' ORDER BY firstname, lastname, username' : ' ORDER BY lastname, firstname, username';

$showOfficialCode = false;
global $_configuration;
if (isset($_configuration['order_user_list_by_official_code']) &&
    $_configuration['order_user_list_by_official_code']
) {
    $showOfficialCode = true;
    $order_clause = ' ORDER BY official_code, firstname, lastname, username';
}

if ($ajax_search) {
    $sql = "SELECT user_id, lastname, firstname, username, id_session, official_code
            FROM $tbl_user u
            INNER JOIN $tbl_session_rel_user
                ON $tbl_session_rel_user.id_user = u.user_id AND
                $tbl_session_rel_user.relation_type<>".SESSION_RELATION_TYPE_RRHH."
                AND $tbl_session_rel_user.id_session = ".intval($id_session)."
            WHERE u.status<>".DRH." AND u.status<>6
            $order_clause";

    if (api_is_multiple_url_enabled()) {
        $tbl_user_rel_access_url= Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
        $access_url_id = api_get_current_access_url_id();
        if ($access_url_id != -1) {
            $sql="SELECT u.user_id, lastname, firstname, username, id_session, official_code
            FROM $tbl_user u
            INNER JOIN $tbl_session_rel_user
                ON $tbl_session_rel_user.id_user = u.user_id AND
                $tbl_session_rel_user.relation_type<>".SESSION_RELATION_TYPE_RRHH."
                AND $tbl_session_rel_user.id_session = ".intval($id_session)."
            INNER JOIN $tbl_user_rel_access_url url_user ON (url_user.user_id=u.user_id)
            WHERE access_url_id = $access_url_id AND u.status<>".DRH." AND u.status<>6
            $order_clause";
        }
    }
    $result = Database::query($sql);
    $users = Database::store_result($result);
    foreach ($users as $user) {
        $sessionUsersList[$user['user_id']] = $user ;
    }

    $sessionUserInfo = SessionManager::getTotalUserCoursesInSession($id_session);

    // Filter the user list in all courses in the session
    foreach ($sessionUserInfo as $sessionUser) {
        // filter students in session
        if ($sessionUser['status_in_session'] != 0) {
            continue;
        }
        
        if (!array_key_exists($sessionUser['user_id'], $sessionUsersList)) {
            continue;
        }

        if ($sessionUser['count'] != $countSessionCoursesList) {
            unset($sessionUsersList[$sessionUser['user_id']]);
        }
    }

    unset($users); //clean to free memory
} else {
    //Filter by Extra Fields
    $use_extra_fields = false;
    if (is_array($extra_field_list)) {
        if (is_array($new_field_list) && count($new_field_list)>0 ) {
            $result_list=array();
            foreach ($new_field_list as $new_field) {
                $varname = 'field_'.$new_field['variable'];
                if (UserManager::is_extra_field_available($new_field['variable'])) {
                    if (isset($_POST[$varname]) && $_POST[$varname]!='0') {
                        $use_extra_fields = true;
                        $extra_field_result[]= UserManager::get_extra_user_data_by_value($new_field['variable'], $_POST[$varname]);
                    }
                }
            }
        }
    }

    if ($use_extra_fields) {
        $final_result = array();
       	if (count($extra_field_result)>1) {
	    for($i=0;$i<count($extra_field_result)-1;$i++) {
                if (is_array($extra_field_result[$i+1])) {
                    $final_result  = array_intersect($extra_field_result[$i],$extra_field_result[$i+1]);
                }
            }
        } else {
            $final_result = $extra_field_result[0];
        }

        if (api_is_multiple_url_enabled()) {
            if (is_array($final_result) && count($final_result)>0) {
                $where_filter = " AND u.user_id IN  ('".implode("','",$final_result)."') ";
            } else {
                //no results
                $where_filter = " AND u.user_id  = -1";
            }
        } else {
            if (is_array($final_result) && count($final_result)>0) {
                $where_filter = " WHERE u.user_id IN  ('".implode("','", $final_result)."') ";
            } else {
                //no results
                $where_filter = " WHERE u.user_id  = -1";
            }
        }
    }
    if (api_is_session_admin()
        && isset($_configuration['prevent_session_admins_to_manage_all_users'])
        && $_configuration['prevent_session_admins_to_manage_all_users'] == 'true'
    ) {
        $order_clause = " AND u.creator_id = " . api_get_user_id() . $order_clause;
    }
    if ($use_extra_fields) {
        $sql = "SELECT  user_id, lastname, firstname, username, id_session, official_code
               FROM $tbl_user u
                    LEFT JOIN $tbl_session_rel_user
                    ON $tbl_session_rel_user.id_user = u.user_id AND
                    $tbl_session_rel_user.id_session = '$id_session' AND
                    $tbl_session_rel_user.relation_type<>".SESSION_RELATION_TYPE_RRHH."
                $where_filter AND u.status<>".DRH." AND u.status<>6
                $order_clause";

    } else {
        $sql = "SELECT  user_id, lastname, firstname, username, id_session, official_code
                FROM $tbl_user u
                LEFT JOIN $tbl_session_rel_user
                ON $tbl_session_rel_user.id_user = u.user_id AND
                $tbl_session_rel_user.id_session = '$id_session' AND
                $tbl_session_rel_user.relation_type<>".SESSION_RELATION_TYPE_RRHH."
                WHERE u.status<>".DRH." AND u.status<>6
                $order_clause";
    }
    if (api_is_multiple_url_enabled()) {
        $tbl_user_rel_access_url= Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
        $access_url_id = api_get_current_access_url_id();
        if ($access_url_id != -1) {
            $sql = "SELECT  u.user_id, lastname, firstname, username, id_session, official_code
                    FROM $tbl_user u
                    LEFT JOIN $tbl_session_rel_user
                        ON $tbl_session_rel_user.id_user = u.user_id AND
                        $tbl_session_rel_user.id_session = '$id_session' AND
                        $tbl_session_rel_user.relation_type <> ".SESSION_RELATION_TYPE_RRHH."
                    INNER JOIN $tbl_user_rel_access_url url_user ON (url_user.user_id=u.user_id)
                    WHERE access_url_id = $access_url_id $where_filter AND u.status<>".DRH." AND u.status<>6
                    $order_clause";
        }
    }

    $result = Database::query($sql);
    $users = Database::store_result($result,'ASSOC');
    foreach ($users as $uid => $user) {
        if ($user['id_session'] != $id_session) {
            $nosessionUsersList[$user['user_id']] = array(
                'fn' => $user['firstname'],
                'ln' => $user['lastname'],
                'un' => $user['username'],
                'official_code' => $user['official_code']
            ) ;
            unset($users[$uid]);
	}
    }
    unset($users); //clean to free memory

    //filling the correct users in list
    $sql="SELECT  user_id, lastname, firstname, username, id_session, official_code
          FROM $tbl_user u
          LEFT JOIN $tbl_session_rel_user
          ON $tbl_session_rel_user.id_user = u.user_id AND
            $tbl_session_rel_user.id_session = '$id_session' AND
            $tbl_session_rel_user.relation_type<>".SESSION_RELATION_TYPE_RRHH."
          WHERE u.status<>".DRH." AND u.status<>6 $order_clause";

    if (api_is_multiple_url_enabled()) {
        $tbl_user_rel_access_url= Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
        $access_url_id = api_get_current_access_url_id();
        if ($access_url_id != -1) {
            $sql="SELECT  u.user_id, lastname, firstname, username, id_session, official_code
                FROM $tbl_user u
                LEFT JOIN $tbl_session_rel_user
                    ON $tbl_session_rel_user.id_user = u.user_id AND
                    $tbl_session_rel_user.id_session = '$id_session' AND
                    $tbl_session_rel_user.relation_type<>".SESSION_RELATION_TYPE_RRHH."
                INNER JOIN $tbl_user_rel_access_url url_user ON (url_user.user_id=u.user_id)
                WHERE access_url_id = $access_url_id AND u.status<>".DRH." AND u.status<>6
                $order_clause";
        }
    }
    $result = Database::query($sql);
    $users = Database::store_result($result,'ASSOC');
    foreach ($users as $uid => $user) {
        if ($user['id_session'] == $id_session) {
            $sessionUsersList[$user['user_id']] = $user;
            if (array_key_exists($user['user_id'],$nosessionUsersList)) {
                unset($nosessionUsersList[$user['user_id']]);
            }
        }
        unset($users[$uid]);
    }
    unset($users); //clean to free memory
}

if ($add_type == 'multiple') {
	$link_add_type_unique = '<a href="'.api_get_self().'?id_session='.$id_session.'&add='.$addProcess.'&add_type=unique">'.Display::return_icon('single.gif').get_lang('SessionAddTypeUnique').'</a>';
	$link_add_type_multiple = Display::url(Display::return_icon('multiple.gif').get_lang('SessionAddTypeMultiple'), '');
} else {
	$link_add_type_unique = Display::url(Display::return_icon('single.gif').get_lang('SessionAddTypeUnique'), '');
	$link_add_type_multiple = '<a href="'.api_get_self().'?id_session='.$id_session.'&amp;add='.$addProcess.'&amp;add_type=multiple">'.Display::return_icon('multiple.gif').get_lang('SessionAddTypeMultiple').'</a>';
}
$link_add_group = '<a href="usergroups.php">'.Display::return_icon('multiple.gif',get_lang('RegistrationByUsersGroups')).get_lang('RegistrationByUsersGroups').'</a>';

$newLinks = Display::url(get_lang('EnrollTrainersFromExistingSessions'), api_get_path(WEB_CODE_PATH).'admin/add_teachers_to_session.php');
$newLinks .= Display::url(get_lang('EnrollStudentsFromExistingSessions'), api_get_path(WEB_CODE_PATH).'admin/add_students_to_session.php');
?>
<div class="actions">
	<?php
    echo $link_add_type_unique;
    echo $link_add_type_multiple;
    echo $link_add_group;
    echo $newLinks;
    ?>
</div>
<form name="formulaire" method="post" action="<?php echo api_get_self(); ?>?page=<?php echo $page; ?>&id_session=<?php echo $id_session; ?><?php if(!empty($addProcess)) echo '&add=true' ; ?>" style="margin:0px;" <?php if ($ajax_search) { echo ' onsubmit="valide();"';}?>>
<?php echo '<legend>'.$tool_name.' ('.$session_info['name'].') </legend>'; ?>
<?php
if ($add_type=='multiple') {
	if (is_array($extra_field_list)) {
		if (is_array($new_field_list) && count($new_field_list)>0 ) {
			echo '<h3>'.get_lang('FilterUsers').'</h3>';
			foreach ($new_field_list as $new_field) {
				echo $new_field['name'];
				$varname = 'field_'.$new_field['variable'];
				echo '&nbsp;<select name="'.$varname.'">';
				echo '<option value="0">--'.get_lang('Select').'--</option>';
				foreach	($new_field['data'] as $option) {
					$checked='';
					if (isset($_POST[$varname])) {
						if ($_POST[$varname]==$option[1]) {
							$checked = 'selected="true"';
						}
					}
					echo '<option value="'.$option[1].'" '.$checked.'>'.$option[1].'</option>';
				}
				echo '</select>';
				echo '&nbsp;&nbsp;';
			}
			echo '<input type="button" value="'.get_lang('Filter').'" onclick="validate_filter()" />';
			echo '<br /><br />';
		}
	}
}
?>
<input type="hidden" name="form_sent" value="1" />
<input type="hidden" name="add_type"  />

<?php
if (!empty($errorMsg)) {
	Display::display_normal_message($errorMsg); //main API
}
?>
<div class="row">
    <div class="span5">
        <div class="multiple_select_header">
            <b><?php echo get_lang('UserListInPlatform') ?> :</b>

        <?php if ($add_type=='multiple') { ?>
            <?php echo get_lang('FirstLetterUser'); ?> :
                <select id="first_letter_user" name="firstLetterUser" onchange = "change_select(this.value);" >
                <option value = "%">--</option>
                <?php
                    echo Display :: get_alphabet_options();
                ?>
                </select>
        <?php } ?>
        </div>
            <div id="content_source">
            <?php
            if (!($add_type=='multiple')) {
              ?>
              <input type="text" id="user_to_add" onkeyup="xajax_search_users(this.value,'single')" />
              <div id="ajax_list_users_single"></div>
              <?php
            } else {
            ?>
            <div id="ajax_list_users_multiple">
            <select id="origin_users" name="nosessionUsersList[]" multiple="multiple" size="15" class="span5">
              <?php
              foreach ($nosessionUsersList as $uid => $enreg) {
              ?>
                  <option value="<?php echo $uid; ?>" <?php if(in_array($uid,$UserList)) echo 'selected="selected"'; ?>>
                      <?php
                      $personName = api_get_person_name($enreg['fn'], $enreg['ln']).' ('.$enreg['un'].') '.$enreg['official_code'];
                      if ($showOfficialCode) {
                          $officialCode = !empty($enreg['official_code']) ? $enreg['official_code'].' - ' : '? - ';
                          $personName = $officialCode.api_get_person_name($enreg['fn'], $enreg['ln']).' ('.$enreg['un'].')';
                      }
                      echo $personName;
                      ?>
                  </option>
              <?php
              }
              ?>
            </select>
            </div>
                <input type="checkbox" onchange="checked_in_no_session(this.checked);" name="user_with_any_session" id="user_with_any_session_id">
                <label for="user_with_any_session_id"><?php echo get_lang('UsersRegisteredInNoSession'); ?></label>
            <?php
            }
            unset($nosessionUsersList);
           ?>
        </div>
    </div>

    <div class="span2">
        <div style="padding-top:54px;width:auto;text-align: center;">
        <?php
            if ($ajax_search) {
            ?>
              <button class="arrowl" type="button" onclick="remove_item(document.getElementById('destination_users'))" ></button>
            <?php
        } else {
            ?>
                <button class="arrowr" type="button" onclick="moveItem(document.getElementById('origin_users'), document.getElementById('destination_users'))" onclick="moveItem(document.getElementById('origin_users'), document.getElementById('destination_users'))">
                </button>
                <br /><br />
                <button class="arrowl" type="button" onclick="moveItem(document.getElementById('destination_users'), document.getElementById('origin_users'))" onclick="moveItem(document.getElementById('destination_users'), document.getElementById('origin_users'))">
                </button>
              <?php
            }
        ?>
        </div>
        <br />
        <br />
		<?php
		if (!empty($addProcess)) {
			echo '<button class="save" type="button" value="" onclick="valide()" >'.get_lang('FinishSessionCreation').'</button>';
        } else {
			echo '<button class="save" type="button" value="" onclick="valide()" >'.get_lang('SubscribeUsersToSession').'</button>';
        }
		?>
    </div>

    <div class="span5">
        <div class="multiple_select_header">
            <b><?php echo get_lang('UserListInSession') ?> :</b>
        </div>
        <select id="destination_users" name="sessionUsersList[]" multiple="multiple" size="15" class="span5">
        <?php
        foreach ($sessionUsersList as $enreg) {
        ?>
            <option value="<?php echo $enreg['user_id']; ?>">
            <?php
                $personName = api_get_person_name($enreg['firstname'], $enreg['lastname']).' ('.$enreg['username'].') '.$enreg['official_code'];
                if ($showOfficialCode) {
                    $officialCode = !empty($enreg['official_code']) ? $enreg['official_code'].' - ' : '? - ';
                    $personName = $officialCode.api_get_person_name($enreg['firstname'], $enreg['lastname']).' ('.$enreg['username'].')';
                }
                echo $personName;
            ?>
            </option>
        <?php
        }
        unset($sessionUsersList);
        ?>
        </select>
    </div>
</div>
</form>
<script>
function moveItem(origin , destination) {
	for(var i = 0 ; i<origin.options.length ; i++) {
		if(origin.options[i].selected) {
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
	for (i = 0 ; i<options.length ; i++)
		newOptions[i] = options[i];

	newOptions = newOptions.sort(mysort);
	options.length = 0;
	for(i = 0 ; i < newOptions.length ; i++)
		options[i] = newOptions[i];
}

function mysort(a, b){
	if(a.text.toLowerCase() > b.text.toLowerCase()){
		return 1;
	}
	if(a.text.toLowerCase() < b.text.toLowerCase()){
		return -1;
	}
	return 0;
}

function valide(){
	var options = document.getElementById('destination_users').options;
	for (i = 0 ; i<options.length ; i++)
		options[i].selected = true;
	document.forms.formulaire.submit();
}

function loadUsersInSelect(select){
	var xhr_object = null;
	if(window.XMLHttpRequest) // Firefox
		xhr_object = new XMLHttpRequest();
	else if(window.ActiveXObject) // Internet Explorer
		xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
	else  // XMLHttpRequest non supporté par le navigateur
	alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");

	xhr_object.open("POST", "loadUsersInSelect.ajax.php");
	xhr_object.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	nosessionUsers = makepost(document.getElementById('origin_users'));
	sessionUsers = makepost(document.getElementById('destination_users'));
	nosessionClasses = makepost(document.getElementById('origin_classes'));
	sessionClasses = makepost(document.getElementById('destination_classes'));
	xhr_object.send("nosessionusers="+nosessionUsers+"&sessionusers="+sessionUsers+"&nosessionclasses="+nosessionClasses+"&sessionclasses="+sessionClasses);

	xhr_object.onreadystatechange = function() {
		if (xhr_object.readyState == 4) {
			document.getElementById('content_source').innerHTML = result = xhr_object.responseText;
		}
	}
}

function makepost(select) {
	var options = select.options;
	var ret = "";
	for (i = 0 ; i<options.length ; i++)
		ret = ret + options[i].value +'::'+options[i].text+";;";
	return ret;
}
</script>
<?php

Display::display_footer();
