<?php
/* For licensing terms, see /license.txt */

use ChamiloSession as Session;

$language_file = array('exercice', 'work', 'document', 'admin', 'gradebook');

require_once '../inc/global.inc.php';
$current_course_tool  = TOOL_STUDENTPUBLICATION;

api_protect_course_script(true);

// Including necessary files
require_once 'work.lib.php';
$this_section = SECTION_COURSES;

$workId = isset($_GET['id']) ? intval($_GET['id']) : null;
$origin = isset($_REQUEST['origin']) ? Security::remove_XSS($_REQUEST['origin']) : '';

if (empty($workId)) {
    api_not_allowed(true);
}

$my_folder_data = get_work_data_by_id($workId);

if (empty($my_folder_data)) {
    api_not_allowed(true);
}

if ($my_folder_data['active'] != 1) {
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
        $show_work = GroupManager::user_has_access(
            $user_id,
            $group_id,
            GroupManager::GROUP_TOOL_WORK
        );
    }

    if (!$show_work) {
        api_not_allowed();
    }

    $interbreadcrumb[] = array ('url' => api_get_path(WEB_CODE_PATH).'group/group.php', 'name' => get_lang('Groups'));
    $interbreadcrumb[] = array ('url' => api_get_path(WEB_CODE_PATH).'group/group_space.php?gidReq='.$group_id, 'name' => get_lang('GroupSpace').' '.$group_properties['name']);
}

$interbreadcrumb[] = array ('url' => api_get_path(WEB_CODE_PATH).'work/work.php?'.api_get_cidreq(), 'name' => get_lang('StudentPublications'));
$interbreadcrumb[] = array ('url' => api_get_path(WEB_CODE_PATH).'work/work_list.php?'.api_get_cidreq().'&id='.$workId, 'name' =>  $my_folder_data['title']);

$documentsAddedInWork = getAllDocumentsFromWorkToString($workId, $courseInfo);

Display :: display_header(null);

echo '<div class="actions">';
echo '<a href="'.api_get_path(WEB_CODE_PATH).'work/work.php?'.api_get_cidreq().'&origin='.$origin.'">'.Display::return_icon('back.png', get_lang('BackToWorksList'),'',ICON_SIZE_MEDIUM).'</a>';
if (api_is_allowed_to_session_edit(false, true) && !empty($workId) && !api_is_invitee() ) {
    echo '<a href="'.api_get_path(WEB_CODE_PATH).'work/upload.php?'.api_get_cidreq().'&id='.$workId.'&origin='.$origin.'">';
    echo Display::return_icon('upload_file.png', get_lang('UploadADocument'), '', ICON_SIZE_MEDIUM).'</a>';
}
echo '</div>';

if (!empty($my_folder_data['title'])) {
    echo Display::page_subheader($my_folder_data['title']);
}

$error_message = Session::read('error_message');
if (!empty($error_message)) {
    echo $error_message;
    Session::erase('error_message');
}

if (!empty($my_folder_data['description'])) {
    echo '<p><div><strong>'.get_lang('Description').':</strong><p>'.Security::remove_XSS($my_folder_data['description']).'</p></div></p>';
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
$item_id = isset($_REQUEST['item_id']) ? intval($_REQUEST['item_id']) : null;

switch ($action) {
    case 'delete':
        $fileDeleted = deleteWorkItem($item_id, $courseInfo);

        if (!$fileDeleted) {
            Display::display_error_message(get_lang('YouAreNotAllowedToDeleteThisDocument'));
        } else {
            Display::display_confirmation_message(get_lang('TheDocumentHasBeenDeleted'));
        }
        break;
}

$result = getWorkDateValidationStatus($work_data);
echo $result['message'];
$check_qualification = intval($my_folder_data['qualification']);

if (!api_is_invitee()) {
    if (!empty($work_data['enable_qualification']) && !empty($check_qualification)) {
        $type = 'simple';

        $columns = array(
            get_lang('Type'),
            get_lang('Title'),
            get_lang('Qualification'),
            get_lang('Date'),
            get_lang('Status'),
            get_lang('Actions')
        );

        $column_model = array(
            array('name'=>'type', 'index'=>'file', 'width'=>'5',   'align'=>'left', 'search' => 'false', 'sortable' => 'false'),
            array('name'=>'title', 'index'=>'title', 'width'=>'40',   'align'=>'left', 'search' => 'false', 'wrap_cell' => 'true'),
            array('name'=>'qualification',	'index'=>'qualification', 'width'=>'10',   'align'=>'left', 'search' => 'true'),
            array('name'=>'sent_date', 'index'=>'sent_date', 'width'=>'30',   'align'=>'left', 'search' => 'true', 'wrap_cell' => 'true'),
            array('name'=>'qualificator_id', 'index'=>'qualificator_id', 'width'=>'20', 'align'=>'left', 'search' => 'true'),
            array('name'=>'actions', 'index'=>'actions', 'width'=>'20', 'align'=>'left', 'search' => 'false', 'sortable'=>'false')
        );
    } else {
        $type = 'complex';

        $columns = array(
            get_lang('Type'),
            get_lang('Title'),
            get_lang('Date'),
            get_lang('Actions')
        );

        $column_model = array(
            array('name'=>'type',      'index'=>'file',      'width'=>'5',  'align'=>'left', 'search' => 'false', 'sortable' => 'false'),
            array('name'=>'title',     'index'=>'title',     'width'=>'60', 'align'=>'left', 'search' => 'false', 'wrap_cell' => "true"),
            array('name'=>'sent_date', 'index'=>'sent_date', 'width'=>'30', 'align'=>'left', 'search' => 'true', 'wrap_cell' => 'true', 'sortable'=>'false'),
            array('name'=>'actions',   'index'=>'actions',   'width'=>'20', 'align'=>'left', 'search' => 'false', 'sortable'=>'false')
        );

        if (ALLOW_USER_COMMENTS) {
            $columns = array(
                get_lang('Type'),
                get_lang('Title'),
                get_lang('Feedback'),
                get_lang('Date'),
                get_lang('Actions')
            );

            $column_model = array(
                array('name'=>'type',      'index'=>'file',      'width'=>'5',  'align'=>'left', 'search' => 'false', 'sortable' => 'false'),
                array('name'=>'title',     'index'=>'title',     'width'=>'60', 'align'=>'left', 'search' => 'false', 'wrap_cell' => "true"),
                array('name'=>'qualification',	'index'=>'qualification', 'width'=>'10',   'align'=>'left', 'search' => 'true'),
                array('name'=>'sent_date', 'index'=>'sent_date', 'width'=>'30', 'align'=>'left', 'search' => 'true', 'wrap_cell' => 'true', 'sortable'=>'false'),
                array('name'=>'actions',   'index'=>'actions',   'width'=>'20', 'align'=>'left', 'search' => 'false', 'sortable'=>'false')
            );
        }
    }

    $extra_params = array(
        'autowidth' =>  'true',
        'height' =>  'auto',
        'sortname' => 'firstname'
    );

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
}

Display :: display_footer();
