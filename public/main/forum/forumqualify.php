<?php

/* For licensing terms, see /license.txt */

use Chamilo\CoreBundle\Framework\Container;
use Chamilo\CourseBundle\Entity\CForumForum;
use Chamilo\CourseBundle\Entity\CForumThread;

/**
 * @todo fix all this qualify files avoid including files, use classes POO jmontoya
 */

require_once __DIR__.'/../inc/global.inc.php';
require_once 'forumfunction.inc.php';

api_protect_course_script(true);

$nameTools = get_lang('Forums');
$this_section = SECTION_COURSES;
$message = '';
//are we in a lp ?
$origin = api_get_origin();

$currentUserId = api_get_user_id();
$userIdToQualify = isset($_GET['user_id']) ? (int) ($_GET['user_id']) : null;
$forumId = isset($_GET['forum']) ? (int) ($_GET['forum']) : 0;
$threadId = isset($_GET['thread']) ? (int) ($_GET['thread']) : 0;
api_block_course_item_locked_by_gradebook($threadId, LINK_FORUM_THREAD);
$nameTools = get_lang('Forums');
$allowed_to_edit = api_is_allowed_to_edit(null, true);

$repo = Container::getForumRepository();
$repoThread = Container::getForumThreadRepository();
/** @var CForumForum $forumEntity */
$forumEntity = $repo->find($forumId);
/** @var CForumThread $threadEntity */
$threadEntity = $repoThread->find($threadId);

$course = api_get_course_entity();
$session = api_get_session_entity();

$allowToQualify = false;
if ($allowed_to_edit) {
    $allowToQualify = true;
} else {
    $allowToQualify = $threadEntity->isThreadPeerQualify() && $forumEntity->isVisible($course, $session) && $userIdToQualify != $currentUserId;
}

if (!$allowToQualify) {
    api_not_allowed(true);
}

// Show max qualify in my form
$maxQualify = showQualify('2', $userIdToQualify, $threadId);
$score = 0;

if (isset($_POST['idtextqualify'])) {
    $score = (float) ($_POST['idtextqualify']);

    if ($score <= $maxQualify) {
        saveThreadScore(
            $threadEntity,
            $userIdToQualify,
            $threadId,
            $score,
            api_get_utc_datetime(),
            api_get_session_id()
        );

        header(
            'Location: '.api_get_path(WEB_CODE_PATH).'forum/viewforum.php?'.api_get_cidreq().'&'
            .http_build_query([
                'forum' => $forumId,
                'action' => 'liststd',
                'content' => 'thread',
                'id' => $threadId,
                'list' => 'qualify',
            ])
        );
        exit;
    }

    Display::addFlash(
        Display::return_message(get_lang('Grade cannot exceed max score'), 'error')
    );
}

/*     Including necessary files */
$htmlHeadXtra[] = '<script>
    $(function() {
        $(\'.hide-me\').slideUp()
    });

    function hidecontent(content){
        $(content).slideToggle(\'normal\');
    }
</script>';
$category = $forumEntity->getForumCategory();
$groupId = api_get_group_id();

if (api_is_in_gradebook()) {
    $interbreadcrumb[] = [
        'url' => Category::getUrl(),
        'name' => get_lang('Assessments'),
    ];
}

$search = isset($_GET['search']) ? Security::remove_XSS(urlencode($_GET['search'])) : '';

if ('learnpath' === $origin) {
    Display::display_reduced_header();
} else {
    if (!empty($groupId)) {
        $group_properties = GroupManager::get_group_properties($groupId);
        $interbreadcrumb[] = [
            'url' => '../group/group.php?'.api_get_cidreq(),
            'name' => get_lang('Groups'),
        ];
        $interbreadcrumb[] = [
            'url' => '../group/group_space.php?'.api_get_cidreq(),
            'name' => get_lang('Group area').' ('.$group_properties['name'].')',
        ];
        $interbreadcrumb[] = [
            'url' => 'viewforum.php?'.api_get_cidreq().'&forum='.$forumId.'&search='.$search,
            'name' => prepare4display($forumEntity->getForumTitle()),
        ];
        if ('PostDeletedSpecial' != $message) {
            $interbreadcrumb[] = [
                'url' => 'viewthread.php?'.api_get_cidreq().'&forum='.$forumId.'&thread='.$threadId,
                'name' => prepare4display($threadEntity->getThreadTitle()),
            ];
        }

        $interbreadcrumb[] = [
            'url' => '#',
            'name' => get_lang('Grade thread'),
        ];

        // the last element of the breadcrumb navigation is already set in interbreadcrumb, so give empty string
        Display::display_header('');
        api_display_tool_title($nameTools);
    } else {
        $interbreadcrumb[] = [
            'url' => 'index.php?'.api_get_cidreq().'&search='.$search,
            'name' => $nameTools,
        ];
        $interbreadcrumb[] = [
            'url' => 'viewforumcategory.php?'.api_get_cidreq().'&forumcategory='.$category->getIid().'&search='.$search,
            'name' => prepare4display($category->getCatTitle()),
        ];
        $interbreadcrumb[] = [
            'url' => 'viewforum.php?'.api_get_cidreq().'&forum='.$forumId.'&search='.$search,
            'name' => prepare4display($forumEntity->getForumTitle()),
        ];

        if ('PostDeletedSpecial' != $message) {
            $interbreadcrumb[] = [
                'url' => 'viewthread.php?'.api_get_cidreq().'&forum='.$forumId.'&thread='.$threadId,
                'name' => prepare4display($threadEntity->getThreadTitle()),
            ];
        }
        // the last element of the breadcrumb navigation is already set in interbreadcrumb, so give empty string
        $interbreadcrumb[] = [
            'url' => '#',
            'name' => get_lang('Grade thread'),
        ];
        Display::display_header('');
    }
}

$postId = isset($_GET['id']) ? (int) ($_GET['id']) : 0;
$repoPost = Container::getForumPostRepository();
$postEntity = !empty($postId) ? $repoPost->find($postId) : null;
$action = isset($_GET['action']) ? $_GET['action'] : '';

$currentUrl = api_get_self().'?forum='.$forumId.'&'.api_get_cidreq().'&thread='.$threadId;

if ('delete' === $action &&
    isset($_GET['content']) &&
    isset($_GET['id']) && api_is_allowed_to_edit(false, true)
) {
    deletePost($postEntity);
    api_location($currentUrl);
}
if (('invisible' === $action || 'visible' === $action) &&
    isset($_GET['id']) && api_is_allowed_to_edit(false, true)
) {
    approvePost($postEntity, $action);
    api_location($currentUrl);
}
if ('move' === $action && isset($_GET['post'])) {
    $message = move_post_form();
}

if (!empty($message)) {
    echo Display::return_message(get_lang($message), 'confirm');
}

// show qualifications history
$type = isset($_GET['type']) ? $_GET['type'] : '';
$historyList = getThreadScoreHistory(
    $userIdToQualify,
    $threadId,
    $type
);

$counter = count($historyList);

// Show current qualify in my form
$qualify = current_qualify_of_thread(
    $threadId,
    api_get_session_id(),
    $_GET['user']
);

$result = get_statistical_information(
    $threadId,
    $_GET['user_id'],
    api_get_course_int_id()
);

$url = api_get_path(WEB_CODE_PATH).'forum/forumqualify.php?'.
    api_get_cidreq().'&forum='.$forumId.'&thread='.$threadId.'&user='.(int) ($_GET['user']).'&user_id='.(int) ($_GET['user']);

$userToQualifyInfo = api_get_user_info($userIdToQualify);
$form = new FormValidator('forum-thread-qualify', 'post', $url);
$form->addHeader($userToQualifyInfo['complete_name']);
$form->addLabel(get_lang('Thread'), $threadEntity->getThreadTitle());
$form->addLabel(get_lang('Users in course'), $result['user_course']);
$form->addLabel(get_lang('Number of posts'), $result['post']);
$form->addLabel(get_lang('Number of posts for this user'), $result['user_post']);
$form->addLabel(
    get_lang('Posts by user'),
    round($result['user_post'] / $result['post'], 2)
);
$form->addText(
    'idtextqualify',
    [get_lang('Score'), get_lang('Max score').' '.$maxQualify],
    $qualify
);

$course = api_get_course_info();

$rows = get_thread_user_post($course['code'], $threadId, $_GET['user']);
if (isset($rows)) {
    $counter = 1;
    foreach ($rows as $row) {
        if ('0' == $row['status']) {
            $style = " id = 'post".$post_en."' class=\"hide-me\" style=\"border:1px solid red; display:none; background-color:#F7F7F7; width:95%; margin: 0px 0px 4px 40px; \" ";
        } else {
            $style = '';
            $post_en = $row['post_parent_id'];
        }

        if ('0' == $row['user_id']) {
            $name = prepare4display($row['poster_name']);
        } else {
            $name = api_get_person_name($row['firstname'], $row['lastname']);
        }
        if (1 == $counter) {
            echo Display::page_subheader($name);
        }

        echo '<div '.$style.'><table class="data_table">';

        if ('0' == $row['visible']) {
            $titleclass = 'forum_message_post_title_2_be_approved';
            $messageclass = 'forum_message_post_text_2_be_approved';
            $leftclass = 'forum_message_left_2_be_approved';
        } else {
            $titleclass = 'forum_message_post_title';
            $messageclass = 'forum_message_post_text';
            $leftclass = 'forum_message_left';
        }

        echo '<tr>';
        echo "<td rowspan=\"3\" class=\"$leftclass\">";
        echo '<br /><b>'.api_convert_and_format_date($row['post_date'], DATE_TIME_FORMAT_LONG).'</b><br />';
        echo '</td>';

        // The post title
        echo "<td class=\"$titleclass\">".prepare4display($row['post_title']).'</td>';
        echo '</tr>';

        // The post message
        echo '<tr >';
        echo "<td class=\"$messageclass\">".prepare4display($row['post_text']).'</td>';
        echo '</tr>';

        // The check if there is an attachment
        $attachment_list = get_attachment($row['post_id']);
        if (!empty($attachment_list)) {
            echo '<tr ><td height="50%">';
            $realname = $attachment_list['path'];
            $user_filename = $attachment_list['filename'];
            echo Display::return_icon('attachment.gif', get_lang('Attachment'));
            echo '<a href="download.php?file=';
            echo $realname;
            echo ' "> '.$user_filename.' </a>';
            echo '<span class="forum_attach_comment" >'.$attachment_list['comment'].'</span><br />';
            echo '</td></tr>';
        }

        echo '</table></div>';
        $counter++;
    }
}

$form->addButtonSave(get_lang('Grade this thread'));
$form->setDefaults(['idtextqualify' => $qualify]);
$form->display();

// Show past data
if (api_is_allowed_to_edit() && $counter > 0) {
    echo '<h4>'.get_lang('ScoreChangesHistory').'</h4>';
    if (isset($_GET['type']) && 'false' === $_GET['type']) {
        $buttons = '<a
            class="btn btn-default"
            href="forumqualify.php?'.api_get_cidreq().'&forum='.$forumId.'&origin='.$origin.'&thread='.$threadId.'&user='.(int) ($_GET['user']).'&user_id='.(int) ($_GET['user_id']).'&type=true&idtextqualify='.$score.'#history">'.
            get_lang('more recent').'</a> <a class="btn btn-default disabled" >'.get_lang('older').'</a>';
    } else {
        $buttons = '<a class="btn btn-default">'.get_lang('more recent').'</a>
                        <a
                            class="btn btn-default"
                            href="forumqualify.php?'.api_get_cidreq().'&forum='.$forumId.'&origin='.$origin.'&thread='.$threadId.'&user='.(int) ($_GET['user']).'&user_id='.(int) ($_GET['user_id']).'&type=false&idtextqualify='.$score.'#history">'.
            get_lang('older').'</a>';
    }

    $table_list = '<br /><div class="btn-group">'.$buttons.'</div>';
    $table_list .= '<br /><table class="table">';
    $table_list .= '<tr>';
    $table_list .= '<th width="50%">'.get_lang('Who changed').'</th>';
    $table_list .= '<th width="10%">'.get_lang('Note changed').'</th>';
    $table_list .= '<th width="40%">'.get_lang('Date changed').'</th>';
    $table_list .= '</tr>';

    for ($i = 0; $i < count($historyList); $i++) {
        $userInfo = api_get_user_info($historyList[$i]['qualify_user_id']);
        $table_list .= '<tr><td>'.$userInfo['complete_name'].'</td>';
        $table_list .= '<td>'.$historyList[$i]['qualify'].'</td>';
        $table_list .= '<td>'.api_convert_and_format_date(
            $historyList[$i]['qualify_time'],
            DATE_TIME_FORMAT_LONG
        );
        $table_list .= '</td></tr>';
    }
    $table_list .= '</table>';

    echo $table_list;
}

if ('learnpath' !== $origin) {
    Display:: display_footer();
}
