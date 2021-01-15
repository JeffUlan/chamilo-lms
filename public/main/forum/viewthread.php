<?php

/* For licensing terms, see /license.txt */

use Chamilo\CoreBundle\Framework\Container;
use Chamilo\CourseBundle\Entity\CForumForum;
use Chamilo\CourseBundle\Entity\CForumPost;
use Chamilo\CourseBundle\Entity\CForumThread;

require_once __DIR__.'/../inc/global.inc.php';

api_protect_course_script(true);
require_once 'forumfunction.inc.php';
$nameTools = get_lang('Forum');
$forumUrl = api_get_path(WEB_CODE_PATH).'forum/';

// Are we in a lp ?
$origin = api_get_origin();
$_user = api_get_user_info();
$my_search = null;
$moveForm = '';

$forumId = isset($_GET['forum']) ? (int) $_GET['forum'] : 0;
$postId = isset($_GET['post_id']) ? $_GET['post_id'] : 0;
$threadId = isset($_GET['thread']) ? (int) $_GET['thread'] : 0;

$repo = Container::getForumRepository();
$forumEntity = null;
if (!empty($forumId)) {
    /** @var CForumForum $forumEntity */
    $forumEntity = $repo->find($forumId);
}

$repoThread = Container::getForumThreadRepository();
/** @var CForumThread $threadEntity */
$threadEntity = $repoThread->find($threadId);

if (empty($threadEntity)) {
    $url = api_get_path(WEB_CODE_PATH).'forum/viewforum.php?'.api_get_cidreq().'&forum='.$forumId;
    header('Location: '.$url);
    exit;
}

$repoPost = Container::getForumPostRepository();
$postEntity = null;
if (!empty($postId)) {
    /** @var CForumPost $postEntity */
    $postEntity = $repoPost->find($postId);
}

$courseEntity = api_get_course_entity(api_get_course_int_id());
$sessionEntity = api_get_session_entity(api_get_session_id());

$current_forum_category = $forumEntity->getForumCategory();
$whatsnew_post_info = isset($_SESSION['whatsnew_post_info']) ? $_SESSION['whatsnew_post_info'] : null;

if (api_is_in_gradebook()) {
    $interbreadcrumb[] = [
        'url' => Category::getUrl(),
        'name' => get_lang('Assessments'),
    ];
}

$groupId = api_get_group_id();
$groupEntity = null;
if (!empty($groupId)) {
    $groupEntity = api_get_group_entity($groupId);
}

$sessionId = api_get_session_id();

$ajaxURL = api_get_path(WEB_AJAX_PATH).'forum.ajax.php?'.api_get_cidreq().'&a=change_post_status';
$htmlHeadXtra[] = '<script>
$(function() {
    $("span").on("click", ".change_post_status", function() {
        var updateDiv = $(this).parent();
        var postId = updateDiv.attr("id");

        $.ajax({
            url: "'.$ajaxURL.'&post_id="+postId,
            type: "GET",
            success: function(data) {
                updateDiv.html(data);
            }
        });
    });
});

</script>';

/* Actions */
$my_action = isset($_GET['action']) ? $_GET['action'] : '';

$logInfo = [
    'tool' => TOOL_FORUM,
    'tool_id' => $forumId,
    'tool_id_detail' => $threadId,
    'action' => !empty($my_action) ? $my_action : 'view-thread',
    'action_details' => isset($_GET['content']) ? $_GET['content'] : '',
];
Event::registerLog($logInfo);

$currentUrl = api_get_path(WEB_CODE_PATH).'forum/viewthread.php?forum='.$forumId.'&'.api_get_cidreq().'&thread='.$threadId;

switch ($my_action) {
    case 'delete_attach':
        delete_attachment($_GET['post'], $_GET['id_attach']);
        header('Location: '.$currentUrl);
        exit;
        break;
    case 'delete':
        if (
            isset($_GET['content']) &&
            isset($_GET['id']) &&
            (api_is_allowed_to_edit(false, true) ||
                ($groupEntity && GroupManager::isTutorOfGroup(api_get_user_id(), $groupEntity)))
        ) {
            /** @var CForumPost $postEntity */
            $postEntity = $repoPost->find($_GET['id']);
            deletePost($postEntity);
        }
        header('Location: '.$currentUrl);
        exit;

        break;
    case 'invisible':
    case 'visible':
        if (isset($_GET['id']) &&
            (api_is_allowed_to_edit(false, true) ||
                ($groupEntity && GroupManager::isTutorOfGroup(api_get_user_id(), $groupEntity)))
        ) {
            /** @var CForumPost $postEntity */
            $postEntity = $repoPost->find($_GET['id']);
            $message = approvePost($postEntity, $_GET['action']);
            Display::addFlash(Display::return_message(get_lang($message)));
        }
        header('Location: '.$currentUrl);
        exit;

        break;
    case 'move':
        if (isset($_GET['post'])) {
            $form = move_post_form();

            // Validation or display
            if ($form->validate()) {
                $values = $form->exportValues();
                store_move_post($values);

                $currentUrl = api_get_path(WEB_CODE_PATH).
                    'forum/viewthread.php?forum='.$forumId.'&'.api_get_cidreq().'&thread='.$threadId;

                header('Location: '.$currentUrl);
                exit;
            } else {
                $moveForm = $form->returnForm();
            }
        }

        break;
    case 'report':
        $result = reportPost($postEntity, $forumEntity, $threadEntity);
        Display::addFlash(Display::return_message(get_lang('Reported')));
        header('Location: '.$currentUrl);
        exit;

        break;
    case 'ask_revision':
        if (api_get_configuration_value('allow_forum_post_revisions')) {
            $result = savePostRevision($postEntity);
            Display::addFlash(Display::return_message(get_lang('Saved.')));
        }
        header('Location: '.$currentUrl);
        exit;

        break;
}

if (!empty($groupId)) {
    $interbreadcrumb[] = [
        'url' => api_get_path(WEB_CODE_PATH).'group/group.php?'.api_get_cidreq(),
        'name' => get_lang('Groups'),
    ];
    $interbreadcrumb[] = [
        'url' => api_get_path(WEB_CODE_PATH).'group/group_space.php?'.api_get_cidreq(),
        'name' => get_lang('Group area').' '.$groupEntity->getName(),
    ];
    $interbreadcrumb[] = [
        'url' => api_get_path(WEB_CODE_PATH).'forum/viewforum.php?forum='.$forumId.'&'.api_get_cidreq().'&search='.Security::remove_XSS(urlencode($my_search)),
        'name' => Security::remove_XSS($forumEntity->getForumTitle()),
    ];
    $interbreadcrumb[] = [
        'url' => api_get_path(WEB_CODE_PATH).'forum/viewthread.php?forum='.$forumId.'&'.api_get_cidreq().'&thread='.$threadId,
        'name' => Security::remove_XSS($threadEntity->getThreadTitle()),
    ];
} else {
    $my_search = isset($_GET['search']) ? $_GET['search'] : '';
    if ('learnpath' != $origin) {
        $interbreadcrumb[] = [
            'url' => api_get_path(WEB_CODE_PATH).'forum/index.php?'.api_get_cidreq().'&search='.Security::remove_XSS(
                    urlencode($my_search)
                ),
            'name' => $nameTools,
        ];
        $interbreadcrumb[] = [
            'url' => api_get_path(
                    WEB_CODE_PATH
                ).'forum/viewforumcategory.php?forumcategory='.$current_forum_category->getIid().'&search='.Security::remove_XSS(
                    urlencode($my_search)
                ),
            'name' => Security::remove_XSS($current_forum_category->getCatTitle()),
        ];
        $interbreadcrumb[] = [
            'url' => api_get_path(WEB_CODE_PATH).'forum/viewforum.php?'.api_get_cidreq().'&forum='.$forumId.'&search='.Security::remove_XSS(urlencode($my_search)),
            'name' => Security::remove_XSS($forumEntity->getForumTitle()),
        ];
        $interbreadcrumb[] = [
            'url' => '#',
            'name' => Security::remove_XSS($threadEntity->getThreadTitle()),
        ];
    }
}

// If the user is not a course administrator and the forum is hidden
// then the user is not allowed here.
if (!api_is_allowed_to_edit(false, true) &&
    (!$forumEntity->isVisible($courseEntity, $sessionEntity) || !$threadEntity->isVisible($courseEntity, $sessionEntity))
) {
    api_not_allowed();
}
// this increases the number of times the thread has been viewed
increase_thread_view($threadId);

if ('learnpath' == $origin) {
    $template = new Template('', false, false, true, true, false);
} else {
    $template = new Template();
}

$actions = '<span style="float:right;">'.search_link().'</span>';
if ('learnpath' != $origin) {
    $actions .= '<a href="'.$forumUrl.'viewforum.php?forum='.$forumId.'&'.api_get_cidreq().'">'
        .Display::return_icon('back.png', get_lang('Back to forum'), '', ICON_SIZE_MEDIUM).'</a>';
}

// The reply to thread link should only appear when the forum_category is
// not locked AND the forum is not locked AND the thread is not locked.
// If one of the three levels is locked then the link should not be displayed.
if (($current_forum_category &&
    0 == $current_forum_category->getLocked()) &&
    0 == $forumEntity->getLocked() &&
    0 == $threadEntity->getLocked() ||
    api_is_allowed_to_edit(false, true)
) {
    // The link should only appear when the user is logged in or when anonymous posts are allowed.
    if ($_user['user_id'] || (1 == $forumEntity->getAllowAnonymous() && !$_user['user_id'])) {
        // reply link
        if (!api_is_anonymous() && api_is_allowed_to_session_edit(false, true)) {
            $actions .= '<a href="'.$forumUrl.'reply.php?'.api_get_cidreq().'&forum='.$forumId.'&thread='
                .$threadId.'&action=replythread">'
                .Display::return_icon('reply_thread.png', get_lang('Reply to this thread'), '', ICON_SIZE_MEDIUM)
                .'</a>';
        }
        // new thread link
        if ((
            api_is_allowed_to_edit(false, true) &&
            !(api_is_session_general_coach() && $forumEntity->getSessionId() != $sessionId)) ||
            (1 == $forumEntity->getAllowNewThreads() && isset($_user['user_id'])) ||
            (1 == $forumEntity->getAllowNewThreads() && !isset($_user['user_id']) && 1 == $forumEntity->getAllowAnonymous())
        ) {
            if (1 != $forumEntity->getLocked() && 1 != $forumEntity->getLocked()) {
                $actions .= '&nbsp;&nbsp;';
            } else {
                $actions .= get_lang('Forum blocked');
            }
        }
    }
}

$template->assign('forum_actions', $actions);
$template->assign('origin', api_get_origin());

/* Display Forum Category and the Forum information */
if (!isset($_SESSION['view'])) {
    $viewMode = $forumEntity->getDefaultView();
} else {
    $viewMode = $_SESSION['view'];
}

$whiteList = ['flat', 'threaded', 'nested'];
if (isset($_GET['view']) && in_array($_GET['view'], $whiteList)) {
    $viewMode = $_GET['view'];
    $_SESSION['view'] = $viewMode;
}

if (empty($viewMode)) {
    $viewMode = 'flat';
}

if ($threadEntity->isThreadPeerQualify()) {
    Display::addFlash(Display::return_message(get_lang('To get the expected score in this forum, your contribution will have to be scored by another student, and you will have to score at least 2 other student\'s contributions. Until you reach this objective, even if scored, your contribution will show as a 0 score in the global grades for this course.'), 'info'));
}

$allowReport = reportAvailable();
$origin = api_get_origin();
$sessionId = api_get_session_id();
$_user = api_get_user_info();
$userId = api_get_user_id();
$groupId = api_get_group_id();

// Decide whether we show the latest post first
$sortDirection = isset($_GET['posts_order']) && 'desc' === $_GET['posts_order'] ? 'DESC' : ('learnpath' != $origin ? 'ASC' : 'DESC');
$posts = getPosts($forumEntity, $threadId, $sortDirection, true);
$count = 0;
$group_id = api_get_group_id();
$locked = api_resource_is_locked_by_gradebook($threadId, LINK_FORUM_THREAD);
$sessionId = api_get_session_id();
$userId = api_get_user_id();
$postCount = 1;
$allowUserImageForum = api_get_course_setting('allow_user_image_forum');
$tutorGroup = false;
$groupEntity = null;
if (!empty($group_id)) {
    $groupEntity = api_get_group_entity($group_id);
    // The user who posted it can edit his thread only if the course admin allowed this in the properties
    // of the forum
    // The course admin him/herself can do this off course always
    $tutorGroup = GroupManager::isTutorOfGroup(api_get_user_id(), $groupEntity);
}


$postList = [];
foreach ($posts as $post) {
    /** @var CForumPost $postEntity */
    $postEntity = $post['entity'];
    $posterId = isset($post['user_id']) ? $post['user_id'] : 0;
    $username = '';
    if (isset($post['username'])) {
        $username = sprintf(get_lang('Login: %s'), $post['username']);
    }

    /*$name = $post['complete_name'];
    if (empty($posterId)) {
        $name = $post['poster_name'];
    }*/

    $post['user_data'] = '';
    $post['author'] = $postEntity->getUser();
    $posterId = $postEntity->getUser()->getId();

    /*if ('learnpath' !== $origin) {
        if ($allowUserImageForum) {
            $post['user_data'] = '<div class="thumbnail">'.
                display_user_image($posterId, $name, $origin).'</div>';
        }

        $post['user_data'] .= Display::tag(
            'h4',
            display_user_link($posterId, $name, $origin, $username),
            ['class' => 'title-username']
        );

        $_user = api_get_user_info($posterId);
        $iconStatus = $_user['icon_status'];
        $post['user_data'] .= '<div class="user-type text-center">'.$iconStatus.'</div>';
    } else {
        if ($allowUserImageForum) {
            $post['user_data'] .= '<div class="thumbnail">'.
                display_user_image($posterId, $name, $origin).'</div>';
        }

        $post['user_data'] .= Display::tag(
            'p',
            $name,
            [
                'title' => api_htmlentities($username, ENT_QUOTES),
                'class' => 'lead',
            ]
        );
    }*/

    if ('learnpath' !== $origin) {
        $post['user_data'] .= Display::tag(
            'p',
            Display::dateToStringAgoAndLongDate($post['post_date']),
            ['class' => 'post-date']
        );
    } else {
        $post['user_data'] .= Display::tag(
            'p',
            Display::dateToStringAgoAndLongDate($post['post_date']),
            ['class' => 'text-muted']
        );
    }

    // get attach id
    $attachment_list = get_attachment($post['post_id']);
    $id_attach = !empty($attachment_list) ? $attachment_list['iid'] : '';

    $iconEdit = '';
    $editButton = '';
    $askForRevision = '';

    if (($groupEntity && $tutorGroup) ||
        (1 == $forumEntity->getAllowEdit() && $posterId == $userId) ||
        (api_is_allowed_to_edit(false, true) &&
        !(api_is_session_general_coach() && $forumEntity->getSessionId() != $sessionId))
    ) {
        if (false == $locked && postIsEditableByStudent($forumEntity, $post)) {
            $editUrl = api_get_path(WEB_CODE_PATH).'forum/editpost.php?'.api_get_cidreq();
            $editUrl .= "&forum=$forumId&thread=$threadId&post={$post['post_id']}&id_attach=$id_attach";
            $iconEdit .= "<a href='".$editUrl."'>"
                .Display::return_icon('edit.png', get_lang('Edit'), [], ICON_SIZE_SMALL)
                .'</a>';

            $editButton = Display::toolbarButton(
                get_lang('Edit'),
                $editUrl,
                'pencil-alt',
                'default'
            );
        }
    }

    if (($groupEntity && $tutorGroup) ||
        api_is_allowed_to_edit(false, true) &&
        !(api_is_session_general_coach() && $forumEntity->getSessionId() != $sessionId)
    ) {
        if (false == $locked) {
            $deleteUrl = api_get_self().'?'.api_get_cidreq().'&'.http_build_query(
                [
                    'forum' => $forumId,
                    'thread' => $threadId,
                    'action' => 'delete',
                    'content' => 'post',
                    'id' => $post['post_id'],
                ]
            );
            $iconEdit .= Display::url(
                Display::return_icon('delete.png', get_lang('Delete'), [], ICON_SIZE_SMALL),
                $deleteUrl,
                [
                    'onclick' => "javascript:if(!confirm('"
                        .addslashes(api_htmlentities(get_lang('Are you sure you want to delete this post? Deleting this post will also delete the replies on this post. Please check the threaded view to see which posts will also be deleted'), ENT_QUOTES))
                        ."')) return false;",
                    'id' => "delete-post-{$post['post_id']}",
                ]
            );
        }
    }

    if (api_is_allowed_to_edit(false, true) &&
        !(
            api_is_session_general_coach() &&
            $forumEntity->getSessionId() != $sessionId
        )
    ) {
        $iconEdit .= return_visible_invisible_icon(
            'post',
            $post['post_id'],
            $post['visible'],
            [
                'forum' => $forumId,
                'thread' => $threadId,
            ]
        );

        if ($count > 0) {
            $iconEdit .= '<a href="viewthread.php?'.api_get_cidreq()
                ."&forum=$forumId&thread=$threadId&action=move&post={$post['post_id']}"
                .'">'.Display::return_icon('move.png', get_lang('Move post'), [], ICON_SIZE_SMALL).'</a>';
        }
    }

    $userCanQualify = 1 == $threadEntity->isThreadPeerQualify() && $posterId != $userId;
    if (api_is_allowed_to_edit(null, true)) {
        $userCanQualify = true;
    }

    $postIsARevision = false;
    $flagRevision = '';

    if ($posterId == $userId) {
        $revision = getPostRevision($post['post_id']);
        if (empty($revision)) {
            $askForRevision = getAskRevisionButton($postEntity, $threadEntity);
        } else {
            $postIsARevision = true;
            $languageId = api_get_language_id(strtolower($revision));
            $languageInfo = api_get_language_info($languageId);
            if ($languageInfo) {
                $languages = api_get_language_list_for_flag();
                $flagRevision = '<span class="flag-icon flag-icon-'.$languages[$languageInfo['english_name']].'"></span> ';
            }
        }
    } else {
        if (postNeedsRevision($postEntity)) {
            $askForRevision = giveRevisionButton($post['post_id'], $threadEntity);
        } else {
            $revision = getPostRevision($post['post_id']);
            if (!empty($revision)) {
                $postIsARevision = true;
                $languageId = api_get_language_id(strtolower($revision));
                $languageInfo = api_get_language_info($languageId);
                if ($languageInfo) {
                    $languages = api_get_language_list_for_flag();
                    $flagRevision = '<span class="flag-icon flag-icon-'.$languages[$languageInfo['english_name']].'"></span> ';
                }
            }
        }
    }

    $post['is_a_revision'] = $postIsARevision;
    $post['flag_revision'] = $flagRevision;

    if (empty($threadEntity->getThreadQualifyMax())) {
        $userCanQualify = false;
    }

    if ($userCanQualify) {
        if ($count > 0) {
            $current_qualify_thread = showQualify(
                '1',
                $posterId,
                $threadId
            );
            if (false == $locked) {
                $iconEdit .= '<a href="forumqualify.php?'.api_get_cidreq()
                    ."&forum=$forumId&thread=$threadId&action=list&post={$post['post_id']}"
                    ."&user={$post['user_id']}&user_id={$post['user_id']}"
                    ."&idtextqualify=$current_qualify_thread"
                    .'" >'.Display::return_icon('quiz.png', get_lang('Grade activity')).'</a>';
            }
        }
    }

    $reportButton = '';
    if ($allowReport) {
        $reportButton = getReportButton($post['post_id'], $threadEntity);
    }

    $statusIcon = getPostStatus($forumEntity, $post);
    if (!empty($iconEdit)) {
        $post['user_data'] .= "<div class='tools-icons'> $iconEdit $statusIcon </div>";
    } else {
        if (!empty(strip_tags($statusIcon))) {
            $post['user_data'] .= "<div class='tools-icons'> $statusIcon </div>";
        }
    }

    $buttonReply = '';
    $buttonQuote = '';
    $waitingValidation = '';

    if (($current_forum_category && 0 == $current_forum_category->getLocked()) &&
        0 == $forumEntity->getLocked() && 0 == $threadEntity->getLocked() || api_is_allowed_to_edit(false, true)
    ) {
        if ($userId || (1 == $forumEntity->getAllowAnonymous() && !$userId)) {
            if (!api_is_anonymous() && api_is_allowed_to_session_edit(false, true)) {
                $buttonReply = Display::toolbarButton(
                    get_lang('Reply to this message'),
                    'reply.php?'.api_get_cidreq().'&'.http_build_query([
                        'forum' => $forumId,
                        'thread' => $threadId,
                        'post' => $post['post_id'],
                        'action' => 'replymessage',
                    ]),
                    'reply',
                    'primary',
                    ['id' => "reply-to-post-{$post['post_id']}"]
                );

                $buttonQuote = Display::toolbarButton(
                    get_lang('Quote this message'),
                    'reply.php?'.api_get_cidreq().'&'.http_build_query([
                        'forum' => $forumId,
                        'thread' => $threadId,
                        'post' => $post['post_id'],
                        'action' => 'quote',
                    ]),
                    'quote-left',
                    'success',
                    ['id' => "quote-post-{$post['post_id']}"]
                );

                if ($forumEntity->isModerated() && !api_is_allowed_to_edit(false, true)) {
                    if (empty($post['status']) || CForumPost::STATUS_WAITING_MODERATION == $post['status']) {
                        $buttonReply = '';
                        $buttonQuote = '';
                    }
                }
            }
        }
    } else {
        $closedPost = '';
        if ($current_forum_category && 1 == $current_forum_category->getLocked()) {
            $closedPost = Display::tag(
                'div',
                '<em class="fa fa-exclamation-triangle"></em> '.get_lang('Forum category Locked'),
                ['class' => 'alert alert-warning post-closed']
            );
        }
        if (1 == $forumEntity->getLocked()) {
            $closedPost = Display::tag(
                'div',
                '<em class="fa fa-exclamation-triangle"></em> '.get_lang('Forum blocked'),
                ['class' => 'alert alert-warning post-closed']
            );
        }
        if (1 == $threadEntity->getLocked()) {
            $closedPost = Display::tag(
                'div',
                '<em class="fa fa-exclamation-triangle"></em> '.get_lang('Thread is locked.'),
                ['class' => 'alert alert-warning post-closed']
            );
        }

        $post['user_data'] .= $closedPost;
    }

    // note: this can be removed here because it will be displayed in the tree
    /*if (isset($whatsnew_post_info[$forumId][$threadId][$post['post_id']]) &&
        !empty($whatsnew_post_info[$forumId][$threadId][$post['post_id']]) &&
        !empty($whatsnew_post_info[$forumId][$post['thread_id']])
    ) {
        $post_image = Display::return_icon('forumpostnew.gif');
    } else {
        $post_image = Display::return_icon('forumpost.gif');
    }

    if ('1' == $post['post_notification'] && $post['poster_id'] == $userId) {
        $post_image .= Display::return_icon(
            'forumnotification.gif',
            get_lang('You will be notified')
        );
    }*/

    $post['current'] = false;
    if (isset($_GET['post_id']) && $_GET['post_id'] == $post['post_id']) {
        $post['current'] = true;
    }

    // Replace Re: with an icon
    $search = [
        get_lang('Re:'),
        'Re:',
        'RE:',
        'AW:',
        'Aw:',
    ];
    $replace = '<span>'.Display::returnFontAwesomeIcon('mail-reply').'</span>';
    $post['post_title'] = str_replace($search, $replace, Security::remove_XSS($post['post_title']));

    // The post title
    $titlePost = Display::tag('h3', $post['post_title'], ['class' => 'forum_post_title']);
    $post['post_title'] = '<a name="post_id_'.$post['post_id'].'"></a>';
    $post['post_title'] .= Display::tag('div', $titlePost, ['class' => 'post-header']);

    // the post body
    $post['post_data'] = Display::tag('div', $post['post_text'], ['class' => 'post-body']);

    // The check if there is an attachment
    $post['post_attachments'] = '';

    $attachments = $postEntity->getAttachments();
    if ($attachments) {
        $repo = Container::getForumAttachmentRepository();
        /** @var \Chamilo\CourseBundle\Entity\CForumAttachment $attachment */
        foreach ($attachments as $attachment) {
            $post['post_attachments'] .= Display::returnFontAwesomeIcon('paperclip');
            $url = $repo->getResourceFileDownloadUrl($attachment).'?'.api_get_cidreq();
            $post['post_attachments'] .= Display::url($attachment->getFilename(), $url);
            $post['post_attachments'] .= '<span class="forum_attach_comment" >'.$attachment->getComment().'</span>';
            if ((1 == $forumEntity->getAllowEdit() && $post['user_id'] == $userId) ||
                (api_is_allowed_to_edit(false, true) &&
                !(api_is_session_general_coach() && $forumEntity->getSessionId() != $sessionId))
            ) {
                $post['post_attachments'] .= '&nbsp;&nbsp;<a href="'.api_get_self().'?'.api_get_cidreq().'&action=delete_attach&id_attach='
                    .$attachment->getIid().'&forum='.$forumId.'&thread='.$threadId.'&post='.$post['post_id']
                    .'" onclick="javascript:if(!confirm(\''
                    .addslashes(api_htmlentities(get_lang('Please confirm your choice'), ENT_QUOTES)).'\')) return false;">'
                    .Display::return_icon('delete.png', get_lang('Delete')).'</a><br />';
            }
        }
    }

    $post['post_buttons'] = "$askForRevision $editButton $reportButton $buttonReply $buttonQuote $waitingValidation";
    $postList[] = $post;

    // The post has been displayed => it can be removed from the what's new array
    //unset($whatsnew_post_info[$current_forum['forum_id']][$current_thread['thread_id']][$post['post_id']]);
    //unset($_SESSION['whatsnew_post_info'][$current_forum['forum_id']][$current_thread['thread_id']][$post['post_id']]);
    $count++;
}

$template->assign('posts', $postList);

$formToString = '';
$showForm = true;
if (!api_is_allowed_to_edit(false, true) &&
    (($current_forum_category && 0 == !$current_forum_category->isVisible($courseEntity, $sessionEntity)) || !$forumEntity->isVisible($courseEntity, $sessionEntity))
) {
    $showForm = false;
}

if (!api_is_allowed_to_edit(false, true) &&
    (
        ($current_forum_category && 0 != $current_forum_category->getLocked()) ||
            0 != $forumEntity->getLocked() || 0 != $threadEntity->getLocked()
    )
) {
    $showForm = false;
}

if (!$_user['user_id'] && 0 == $forumEntity->getAllowAnonymous()) {
    $showForm = false;
}

if (0 != $forumEntity->getForumOfGroup()) {
    $show_forum = GroupManager::user_has_access(
        api_get_user_id(),
        $forumEntity->getForumOfGroup(),
        GroupManager::GROUP_TOOL_FORUM
    );
    if (!$show_forum) {
        $showForm = false;
    }
}

if ($showForm) {
    $form = show_add_post_form(
        $forumEntity,
        $threadEntity,
        null,
        null,
        null
    );
    $formToString = $form->returnForm();
}

$template->assign('form', $formToString);
$template->assign('move_form', $moveForm);

$layout = $template->get_template('forum/posts.tpl');

$template->display($layout);
