<?php
/* For licensing terms, see /license.txt */

require_once '../inc/global.inc.php';
$current_course_tool  = TOOL_STUDENTPUBLICATION;

require_once 'work.lib.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$work = get_work_data_by_id($id);

if (empty($id) || empty($work)) {
    api_not_allowed(true);
}

if ($work['active'] != 1) {
    api_not_allowed(true);
}

$interbreadcrumb[] = array ('url' => 'work.php', 'name' => get_lang('StudentPublications'));

$my_folder_data = get_work_data_by_id($work['parent_id']);
$courseInfo = api_get_course_info();

allowOnlySubscribedUser(
    api_get_user_id(),
    $work['parent_id'],
    $courseInfo['real_id']
);

$isDrhOfCourse = CourseManager::isUserSubscribedInCourseAsDrh(
    api_get_user_id(),
    $courseInfo
);

if ((user_is_author($id) || $isDrhOfCourse || (api_is_allowed_to_edit() || api_is_coach())) ||
    (
        $courseInfo['show_score'] == 0 &&
        $work['active'] == 1 &&
        $work['accepted'] == 1
    )
) {
    if ((api_is_allowed_to_edit() || api_is_coach()) || api_is_drh()) {
        $url_dir = 'work_list_all.php?id='.$my_folder_data['id'];
    } else {
        $url_dir = 'work_list.php?id='.$my_folder_data['id'];
    }

    $interbreadcrumb[] = array('url' => $url_dir, 'name' => $my_folder_data['title']);
    $interbreadcrumb[] = array('url' => '#','name' => $work['title']);
    //|| api_is_drh()
    if (($courseInfo['show_score'] == 0 &&
        $work['active'] == 1 &&
        $work['accepted'] == 1
        ) ||
        (api_is_allowed_to_edit() || api_is_coach()) ||
        user_is_author($id) ||
        $isDrhOfCourse
    ) {
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
        switch ($action) {
            case 'send_comment':
                if (isset($_FILES["file"])) {
                    $_POST['file'] = $_FILES["file"];
                }
                addWorkComment(
                    api_get_course_info(),
                    api_get_user_id(),
                    $my_folder_data,
                    $work,
                    $_POST
                );
                $url = api_get_path(WEB_CODE_PATH).'work/view.php?id='.$work['id'].'&'.api_get_cidreq();
                header('Location: '.$url);
                exit;
                break;
            case 'delete_attachment':
                deleteCommentFile(
                    $_REQUEST['comment_id'],
                    api_get_course_info()
                );
                $url = api_get_path(WEB_CODE_PATH).'work/view.php?id='.$work['id'].'&'.api_get_cidreq();
                header('Location: '.$url);
                exit;
                break;
        }

        $comments = getWorkComments($work);
        $commentForm = getWorkCommentForm($work);

        $tpl = new Template();
        $tpl->assign('work', $work);
        $tpl->assign('work_comment_enabled', ALLOW_USER_COMMENTS);
        $tpl->assign('comments', $comments);
        if (api_is_allowed_to_session_edit()) {
            $tpl->assign('form', $commentForm);
        }
        $tpl->assign('is_allowed_to_edit', api_is_allowed_to_edit());

        $template = $tpl->get_template('work/view.tpl');
        $content  = $tpl->fetch($template);
        $tpl->assign('content', $content);
        $tpl->display_one_col_template();
    } else {
        api_not_allowed(true);
    }
} else {
    api_not_allowed(true);
}
