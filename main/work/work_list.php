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

$my_folder_data = get_work_data_by_id($workId);
if (empty($my_folder_data)) {
    api_not_allowed(true);
}

$work_data = get_work_assignment_by_id($workId);
$tool_name = get_lang('StudentPublications');

$group_id = api_get_group_id();
$courseInfo = api_get_course_info();
$htmlHeadXtra[] = api_get_jqgrid_js();
$url_dir = api_get_path(WEB_CODE_PATH).'work/work.php?'.api_get_cidreq();

allowOnlySubscribedUser(api_get_user_id(), $workId, $courseInfo['real_id']);

if (!empty($group_id)) {
    $group_properties  = GroupManager :: get_group_properties($group_id);
    $show_work = false;

    if (api_is_allowed_to_edit(false, true)) {
        $show_work = true;
    } else {
        // you are not a teacher
        $show_work = GroupManager::user_has_access($user_id, $group_id, GroupManager::GROUP_TOOL_WORK);
    }

    if (!$show_work) {
        api_not_allowed();
    }

    $interbreadcrumb[] = array ('url' => '../group/group.php', 'name' => get_lang('Groups'));
    $interbreadcrumb[] = array ('url' => '../group/group_space.php?gidReq='.$group_id, 'name' => get_lang('GroupSpace').' '.$group_properties['name']);
}

$interbreadcrumb[] = array ('url' => api_get_path(WEB_CODE_PATH).'work/work.php?'.api_get_cidreq(), 'name' => get_lang('StudentPublications'));
$interbreadcrumb[] = array ('url' => api_get_path(WEB_CODE_PATH).'work/work_list.php?'.api_get_cidreq().'&id='.$workId, 'name' =>  $my_folder_data['title']);

Display :: display_header(null);

echo '<div class="actions">';
echo '<a href="'.api_get_path(WEB_CODE_PATH).'work/work.php?'.api_get_cidreq().'&origin='.$origin.'&gradebook='.$gradebook.'">'.Display::return_icon('back.png', get_lang('BackToWorksList'),'',ICON_SIZE_MEDIUM).'</a>';
if (api_is_allowed_to_session_edit(false, true) && !empty($workId)) {
    echo '<a href="'.api_get_path(WEB_CODE_PATH).'work/upload.php?'.api_get_cidreq().'&id='.$workId.'&origin='.$origin.'&gradebook='.$gradebook.'">';
    echo Display::return_icon('upload_file.png', get_lang('UploadADocument'),'',ICON_SIZE_MEDIUM).'</a>';
}
echo '</div>';

$error_message = isset($_GET['error_message']) ? Security::remove_XSS($_GET['error_message']) : null;
if (!empty($error_message)) {
    echo $error_message;
}

if (!empty($my_folder_data['description'])) {
    echo '<p><div><strong>'.get_lang('Description').':</strong><p>'.Security::remove_XSS($my_folder_data['description']).'</p></div></p>';
}

$documents = getAllDocumentToWork($workId, $courseInfo['real_id']);
if (!empty($documents)) {
    $docContent = '<ul class="nav nav-list well">';
    $docContent .= '<li class="nav-header">'.get_lang('Documents').'</li>';
    foreach ($documents as $doc) {
        $docData = DocumentManager::get_document_data_by_id($doc['document_id'], $courseInfo['code']);
        if ($docData) {
            $docContent .= '<li><a target="_blank" href="'.$docData['url'].'">'.$docData['title'].'</a></li>';
        }

    }
    $docContent .= '</ul><br />';
    echo $docContent;
}

$check_qualification = intval($my_folder_data['qualification']);

if (!empty($work_data['enable_qualification']) && !empty($check_qualification)) {
    $type = 'simple';
    $columns        = array(get_lang('Type'), get_lang('FirstName'), get_lang('LastName'), get_lang('Title'), get_lang('Qualification'), get_lang('Date'), get_lang('Status'), get_lang('Actions'));
    $column_model   = array (
        array('name'=>'type',           'index'=>'file',            'width'=>'12',   'align'=>'left', 'search' => 'false'),
        array('name'=>'firstname',      'index'=>'firstname',       'width'=>'35',   'align'=>'left', 'search' => 'true'),
        array('name'=>'lastname',		'index'=>'lastname',        'width'=>'35',   'align'=>'left', 'search' => 'true'),
        //array('name'=>'username',       'index'=>'username',        'width'=>'30',   'align'=>'left', 'search' => 'true'),
        array('name'=>'title',          'index'=>'title',           'width'=>'40',   'align'=>'left', 'search' => 'false', 'wrap_cell' => 'true'),
        //                array('name'=>'file',           'index'=>'file',            'width'=>'20',   'align'=>'left', 'search' => 'false'),
        array('name'=>'qualification',	'index'=>'qualification',	'width'=>'20',   'align'=>'left', 'search' => 'true'),
        array('name'=>'sent_date',           'index'=>'sent_date',            'width'=>'50',   'align'=>'left', 'search' => 'true', 'wrap_cell' => 'true'),
        array('name'=>'qualificator_id','index'=>'qualificator_id', 'width'=>'30',   'align'=>'left', 'search' => 'true'),
        array('name'=>'actions',        'index'=>'actions',         'width'=>'40',   'align'=>'left', 'search' => 'false', 'sortable'=>'false')
    );
} else {
    $type = 'complex';
    $columns  = array(get_lang('Type'), get_lang('FirstName'), get_lang('LastName'), get_lang('Title'), get_lang('Date'),  get_lang('Actions'));
    $column_model   = array (
        array('name'=>'type',           'index'=>'file',            'width'=>'12',   'align'=>'left', 'search' => 'false'),
        array('name'=>'firstname',      'index'=>'firstname',       'width'=>'35',   'align'=>'left', 'search' => 'true'),
        array('name'=>'lastname',		'index'=>'lastname',        'width'=>'35',   'align'=>'left', 'search' => 'true'),
        //array('name'=>'username',       'index'=>'username',        'width'=>'30',   'align'=>'left', 'search' => 'true'),
        array('name'=>'title',          'index'=>'title',           'width'=>'40',   'align'=>'left', 'search' => 'false', 'wrap_cell' => "true"),
        //                array('name'=>'file',           'index'=>'file',            'width'=>'20',   'align'=>'left', 'search' => 'false'),
        //array('name'=>'qualification',	'index'=>'qualification',	'width'=>'20',   'align'=>'left', 'search' => 'true'),
        array('name'=>'sent_date',       'index'=>'sent_date',            'width'=>'50',   'align'=>'left', 'search' => 'true', 'wrap_cell' => 'true'),
        //array('name'=>'qualificator_id','index'=>'qualificator_id', 'width'=>'30',   'align'=>'left', 'search' => 'true'),
        array('name'=>'actions',        'index'=>'actions',         'width'=>'40',   'align'=>'left', 'search' => 'false', 'sortable'=>'false')
    );
}

$extra_params = array();

// Auto width
$extra_params['autowidth'] = 'true';

// Height
$extra_params['height'] = 'auto';

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
