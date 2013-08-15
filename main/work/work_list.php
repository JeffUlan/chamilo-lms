<?php
/* For licensing terms, see /license.txt */

use ChamiloSession as Session;

$language_file = array('exercice', 'work', 'document', 'admin', 'gradebook');

require_once '../inc/global.inc.php';
$current_course_tool  = TOOL_STUDENTPUBLICATION;

/*	Configuration settings */

api_protect_course_script(true);

// Including necessary files
require_once 'work.lib.php';
$this_section = SECTION_COURSES;

$workId = isset($_GET['id']) ? intval($_GET['id']) : null;

if (empty($workId)) {
    api_not_allowed(true);
}

$tool_name = get_lang('StudentPublications');

$group_id = api_get_group_id();

$courseInfo = api_get_course_info();

if ($courseInfo['show_score'] == 1) {
    api_not_allowed(true);
}

$htmlHeadXtra[] = api_get_jqgrid_js();

Display :: display_header(null);

echo '<div class="actions">';
echo '<a href="'.api_get_path(WEB_CODE_PATH).'work/work.php?'.api_get_cidreq().'&origin='.$origin.'&gradebook='.$gradebook.'">'.Display::return_icon('back.png', get_lang('BackToWorksList'),'',ICON_SIZE_MEDIUM).'</a>';
echo '</div>';

// User works
$work_data = get_work_assignment_by_id($workId);
$check_qualification = intval($my_folder_data['qualification']);

if (!empty($work_data['enable_qualification']) && !empty($check_qualification)) {
    $type = 'simple';
    $columns        = array(get_lang('Type'), get_lang('FirstName'), get_lang('LastName'), get_lang('LoginName'), get_lang('Title'), get_lang('Qualification'), get_lang('Date'),  get_lang('Status'), get_lang('Actions'));
    $column_model   = array (
    array('name'=>'type',           'index'=>'file',            'width'=>'12',   'align'=>'left', 'search' => 'false'),
    array('name'=>'firstname',      'index'=>'firstname',       'width'=>'35',   'align'=>'left', 'search' => 'true'),
    array('name'=>'lastname',		'index'=>'lastname',        'width'=>'35',   'align'=>'left', 'search' => 'true'),
    array('name'=>'username',       'index'=>'username',        'width'=>'30',   'align'=>'left', 'search' => 'true'),
    array('name'=>'title',          'index'=>'title',           'width'=>'40',   'align'=>'left', 'search' => 'false', 'wrap_cell' => 'true'),
    //                array('name'=>'file',           'index'=>'file',            'width'=>'20',   'align'=>'left', 'search' => 'false'),
    array('name'=>'qualification',	'index'=>'qualification',	'width'=>'20',   'align'=>'left', 'search' => 'true'),
    array('name'=>'sent_date',           'index'=>'sent_date',            'width'=>'50',   'align'=>'left', 'search' => 'true'),
    array('name'=>'qualificator_id','index'=>'qualificator_id', 'width'=>'30',   'align'=>'left', 'search' => 'true'),
    array('name'=>'actions',        'index'=>'actions',         'width'=>'40',   'align'=>'left', 'search' => 'false', 'sortable'=>'false')
    );
} else {
    $type = 'complex';
    $columns  = array(get_lang('Type'), get_lang('FirstName'), get_lang('LastName'), get_lang('LoginName'), get_lang('Title'), get_lang('Date'),  get_lang('Actions'));
    $column_model   = array (
    array('name'=>'type',           'index'=>'file',            'width'=>'12',   'align'=>'left', 'search' => 'false'),
    array('name'=>'firstname',      'index'=>'firstname',       'width'=>'35',   'align'=>'left', 'search' => 'true'),
    array('name'=>'lastname',		'index'=>'lastname',        'width'=>'35',   'align'=>'left', 'search' => 'true'),
    array('name'=>'username',       'index'=>'username',        'width'=>'30',   'align'=>'left', 'search' => 'true'),
    array('name'=>'title',          'index'=>'title',           'width'=>'40',   'align'=>'left', 'search' => 'false', 'wrap_cell' => "true"),
    //                array('name'=>'file',           'index'=>'file',            'width'=>'20',   'align'=>'left', 'search' => 'false'),
    //array('name'=>'qualification',	'index'=>'qualification',	'width'=>'20',   'align'=>'left', 'search' => 'true'),
    array('name'=>'sent_date',       'index'=>'sent_date',            'width'=>'50',   'align'=>'left', 'search' => 'true'),
    //array('name'=>'qualificator_id','index'=>'qualificator_id', 'width'=>'30',   'align'=>'left', 'search' => 'true'),
    array('name'=>'actions',        'index'=>'actions',         'width'=>'40',   'align'=>'left', 'search' => 'false', 'sortable'=>'false')
    );
}

$extra_params = array();

//Autowidth
$extra_params['autowidth'] = 'true';

//height auto
$extra_params['height'] = 'auto';
//$extra_params['excel'] = 'excel';

//$extra_params['rowList'] = array(10, 20 ,30);

$extra_params['sortname'] = 'firstname';
$url = api_get_path(WEB_AJAX_PATH).'model.ajax.php?a=get_work_user_list&work_id='.$workId.'&type='.$type;
?>
<script>
$(function() {
    <?php
    echo Display::grid_js('results', $url, $columns, $column_model, $extra_params);
?>
});
</script>
<?php
echo Display::grid_html('results');

Display :: display_footer();
