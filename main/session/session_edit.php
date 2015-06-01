<?php
/* For licensing terms, see /license.txt */

/**
 * Sessions edition script
 * @package chamilo.admin
 */

$cidReset = true;
require_once '../inc/global.inc.php';

// setting the section (for the tabs)
$this_section = SECTION_PLATFORM_ADMIN;

$formSent = 0;

// Database Table Definitions
$tbl_user = Database::get_main_table(TABLE_MAIN_USER);
$tbl_session = Database::get_main_table(TABLE_MAIN_SESSION);

$id = intval($_GET['id']);

SessionManager::protectSession($id);

$sessionInfo = SessionManager::fetch($id);

$id_coach = $sessionInfo['id_coach'];
$tool_name = get_lang('EditSession');

//$interbreadcrumb[] = array('url' => 'index.php',"name" => get_lang('PlatformAdmin'));
$interbreadcrumb[] = array('url' => "session_list.php","name" => get_lang('SessionList'));
$interbreadcrumb[] = array('url' => "resume_session.php?id_session=".$id,"name" => get_lang('SessionOverview'));

list($year_start, $month_start, $day_start) = explode('-', $sessionInfo['date_start']);
list($year_end, $month_end, $day_end) = explode('-', $sessionInfo['date_end']);

if (isset($_POST['formSent']) && $_POST['formSent']) {
	$formSent = 1;
}

$order_clause = 'ORDER BY ';
$order_clause .= api_sort_by_first_name() ? 'firstname, lastname, username' : 'lastname, firstname, username';

$sql = "SELECT user_id,lastname,firstname,username
        FROM $tbl_user
        WHERE status='1'".$order_clause;

if (api_is_multiple_url_enabled()) {
	$table_access_url_rel_user= Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
	$access_url_id = api_get_current_access_url_id();
	if ($access_url_id != -1) {
		$sql = "SELECT DISTINCT u.user_id,lastname,firstname,username
		        FROM $tbl_user u
                INNER JOIN $table_access_url_rel_user url_rel_user ON (url_rel_user.user_id = u.user_id)
			    WHERE status='1' AND access_url_id = '$access_url_id' $order_clause";
	}
}

$result = Database::query($sql);
$coaches = Database::store_result($result);
$thisYear = date('Y');

$coachesOption = array(
    '' => '----- ' . get_lang('None') . ' -----'
);

foreach ($coaches as $coach) {
    $personName = api_get_person_name($coach['firstname'], $coach['lastname']);
    $coachesOption[$coach['user_id']] = "$personName ({$coach['username']})";
}

$categoriesList = SessionManager::get_all_session_category();

$categoriesOption = array(
    '0' => get_lang('None')
);

if ($categoriesList != false) {
    foreach ($categoriesList as $categoryItem) {
        $categoriesOption[$categoryItem['id']] = $categoryItem['name'];
    }
}

$formAction = api_get_self() . '?';
$formAction .= http_build_query(array(
    'page' => Security::remove_XSS($_GET['page']),
    'id' => $id
));

$form = new FormValidator('edit_session', 'post', $formAction);
$form->addElement('header', $tool_name);
$result = SessionManager::setForm($form);

$htmlHeadXtra[] = '
<script>
$(function() {
    '.$result['js'].'
});
</script>';

$form->addButtonUpdate(get_lang('ModifyThisSession'));


$formDefaults = $sessionInfo;

$formDefaults['session_category'] = $sessionInfo['session_category_id'];
$formDefaults['session_visibility'] = $sessionInfo['visibility'];

if ($formSent) {
    $formDefaults['name'] = api_htmlentities($name, ENT_QUOTES, $charset);
} else {
    $formDefaults['name'] = Security::remove_XSS($sessionInfo['name']);
}

$form->setDefaults($formDefaults);

if ($form->validate()) {
    $params = $form->getSubmitValues();

    $name = $params['name'];
    $startDate = $params['access_start_date'];
    $endDate = $params['access_end_date'];
    $displayStartDate = $params['display_start_date'];
    $displayEndDate = $params['display_end_date'];
    $coachStartDate = $params['coach_access_start_date'];
    $coachEndDate = $params['coach_access_end_date'];
    $coach_username = intval($params['coach_username']);
    $id_session_category = $params['session_category'];
    $id_visibility = $params['session_visibility'];
    $duration = isset($params['duration']) ? $params['duration'] : null;
    $description = $params['description'];
    $showDescription = isset($params['show_description']) ? 1: 0;

    $extraFields = array();
    foreach ($params as $key => $value) {
        if (strpos($key, 'extra_') === 0) {
            $extraFields[$key] = $value;
        }
    }

    $return = SessionManager::edit_session(
        $id,
        $name,
        $startDate,
        $endDate,
        $displayStartDate,
        $displayEndDate,
        $coachStartDate,
        $coachEndDate,
        $coach_username,
        $id_session_category,
        $id_visibility,
        $description,
        $showDescription,
        $duration,
        $extraFields
    );

    if ($return == strval(intval($return))) {
		header('Location: resume_session.php?id_session=' . $return);
		exit();
	}
}

// display the header
Display::display_header($tool_name);

if (!empty($return)) {
    Display::display_error_message($return,false);
}

$form->display();
?>

<script type="text/javascript">
$(document).ready( function() {

<?php
    if (!empty($sessionInfo['duration'])) {
        echo 'accessSwitcher(0);';
    } else {
        echo 'accessSwitcher(1);';
    }
?>
});


    function setDisable(select) {
	document.forms['edit_session'].elements['session_visibility'].disabled = (select.checked) ? true : false;
	document.forms['edit_session'].elements['session_visibility'].selectedIndex = 0;
}


function accessSwitcher(accessFromReady) {
    var access = $('#access option:selected').val();

    if (accessFromReady >= 0) {
        access = accessFromReady;
        $('[name=access]').val(access);
    }

    if (access == 1) {
        $('#duration').hide();
        $('#date_fields').show();
    } else {

        $('#duration').show();
        $('#date_fields').hide();
    }
    emptyDuration();
}

function emptyDuration() {
    if ($('#duration').val()) {
        $('#duration').val('');
    }
}

$(document).on('ready', function (){
    $('#show-options').on('click', function (e) {
        e.preventDefault();
        var display = $('#options').css('display');
        display === 'block' ? $('#options').slideUp() : $('#options').slideDown() ;
    });
});

</script>
<?php
Display::display_footer();
