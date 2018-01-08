<?php
/* For licensing terms, see /license.txt */

/**
 * This script manages the display of forum threads in flat view
 * @copyright Julio Montoya <gugli100@gmail.com> UI Improvements + lots of bugfixes
 * @package chamilo.forum
 */

// Delete attachment file
if ((isset($_GET['action']) &&
    $_GET['action'] == 'delete_attach') &&
    isset($_GET['id_attach'])
) {
    delete_attachment(0, $_GET['id_attach']);
}

// Are we in a lp ?
$origin = api_get_origin();
$sessionId = api_get_session_id();
$_user = api_get_user_info();
$userId = api_get_user_id();
$groupId = api_get_group_id();

// Decide whether we show the latest post first
$sortDirection = isset($_GET['posts_order']) && $_GET['posts_order'] === 'desc' ? 'DESC' : ($origin != 'learnpath' ? 'ASC' : 'DESC');

if (isset($current_thread['thread_id'])) {
    $rows = getPosts($current_forum, $current_thread['thread_id'], $sortDirection);
    $increment = 0;
    $clean_forum_id = intval($_GET['forum']);
    $clean_thread_id = intval($_GET['thread']);
    $locked = api_resource_is_locked_by_gradebook(
        $clean_thread_id,
        LINK_FORUM_THREAD
    );

    $buttonReply = '';
    $buttonQuote = '';
    $closedPost = '';

    if (!empty($rows)) {
        $postCount = count($rows);
        foreach ($rows as $row) {
            $posterId = isset($row['user_id']) ? $row['user_id'] : 0;
            $name = '';
            if (empty($posterId)) {
                $name = prepare4display($row['poster_name']);
            } else {
                if (isset($row['complete_name'])) {
                    $name = $row['complete_name'];
                }
            }

            $username = '';
            if (isset($row['username'])) {
                $username = sprintf(get_lang('LoginX'), $row['username']);
            }

            if (($current_forum_category && $current_forum_category['locked'] == 0) &&
                $current_forum['locked'] == 0 &&
                $current_thread['locked'] == 0 ||
                api_is_allowed_to_edit(false, true)
            ) {
                if ($_user['user_id'] ||
                    ($current_forum['allow_anonymous'] == 1 && !$_user['user_id'])
                ) {
                    if ((api_is_anonymous() && $current_forum['allow_anonymous'] == 1) ||
                        (!api_is_anonymous() && api_is_allowed_to_session_edit(false, true))
                    ) {
                        $buttonReply = Display::toolbarButton(
                            get_lang('ReplyToMessage'),
                            'reply.php?'.api_get_cidreq().'&'.http_build_query([
                                'forum' => $clean_forum_id,
                                'thread' => $clean_thread_id,
                                'post' => $row['post_id'],
                                'action' => 'replymessage'
                            ]),
                            'reply',
                            'primary',
                            ['id' => "reply-to-post-{$row['post_id']}"]
                        );

                        $buttonQuote = Display::toolbarButton(
                            get_lang('QuoteMessage'),
                            'reply.php?'.api_get_cidreq().'&'.http_build_query([
                                'forum' => $clean_forum_id,
                                'thread' => $clean_thread_id,
                                'post' => $row['post_id'],
                                'action' => 'quote'
                            ]),
                            'quote-left',
                            'success',
                            ['id' => "quote-post-{$row['post_id']}"]
                        );
                    }
                }
            } else {
                if (($current_forum_category && $current_forum_category['locked'] == 1)) {
                    $closedPost = Display::tag(
                        'div',
                        '<em class="fa fa-exclamation-triangle"></em> '.get_lang('ForumcategoryLocked'),
                        ['class' => 'alert alert-warning post-closed']
                    );
                }
                if ($current_forum['locked'] == 1) {
                    $closedPost = Display::tag(
                        'div',
                        '<em class="fa fa-exclamation-triangle"></em> '.get_lang('ForumLocked'),
                        ['class' => 'alert alert-warning post-closed']
                    );
                }
                if ($current_thread['locked'] == 1) {
                    $closedPost = Display::tag(
                        'div',
                        '<em class="fa fa-exclamation-triangle"></em> '.get_lang('ThreadLocked'),
                        ['class' => 'alert alert-warning post-closed']
                    );
                }
            }

            $html = '';
            $html .= '<div class="panel panel-default forum-post">';
            $html .= '<div class="panel-body">';
            $html .= '<div class="row">';
            $html .= '<div class="col-md-2">';

            if ($origin != 'learnpath') {
                if (api_get_course_setting('allow_user_image_forum')) {
                    $html .= '<div class="thumbnail">'.display_user_image($posterId, $name, $origin).'</div>';
                }
                $html .= Display::tag(
                    'h4',
                    display_user_link($posterId, $name),
                    ['class' => 'title-username']
                );
            } else {
                if (api_get_course_setting('allow_user_image_forum')) {
                    $html .= '<div class="thumbnail">'.display_user_image($posterId, $name, $origin).'</div>';
                }
                $name = Display::tag('strong', "#".$postCount--, ['class' => 'text-info'])." | $name";
                $html .= Display::tag(
                    'p',
                    $name,
                    [
                        'title' => api_htmlentities($username, ENT_QUOTES),
                        'class' => 'lead'
                    ]
                );
            }

            if ($origin != 'learnpath') {
                $html .= Display::tag(
                    'p',
                    Display::dateToStringAgoAndLongDate($row['post_date']),
                    ['class' => 'post-date']
                );
            } else {
                $html .= Display::tag(
                    'p',
                    Display::dateToStringAgoAndLongDate($row['post_date']),
                    ['class' => 'text-muted']
                );
            }

            // get attach id
            $attachment_list = get_attachment($row['post_id']);
            $id_attach = !empty($attachment_list) ? $attachment_list['iid'] : '';
            $iconEdit = '';
            $statusIcon = '';
            // The user who posted it can edit his thread only if the course admin allowed
            // this in the properties of the forum
            // The course admin him/herself can do this off course always
            $groupInfo = GroupManager::get_group_properties($groupId);
            if ((isset($groupInfo['iid']) && GroupManager::is_tutor_of_group($userId, $groupInfo)) ||
                ($current_forum['allow_edit'] == 1 && $row['user_id'] == $_user['user_id']) ||
                (
                api_is_allowed_to_edit(false, true) &&
                !(api_is_session_general_coach() && $current_forum['session_id'] != $sessionId)
                )
            ) {
                if (api_is_allowed_to_session_edit(false, true)) {
                    if ($locked == false && postIsEditableByStudent($current_forum, $row)) {
                        $iconEdit .= "<a href=\"editpost.php?".api_get_cidreq()."&forum=".$clean_forum_id
                            . "&thread=".$clean_thread_id."&post=".$row['post_id']
                            . "&edit=edition&id_attach=".$id_attach."\">"
                            . Display::return_icon('edit.png', get_lang('Edit'), [], ICON_SIZE_SMALL)."</a>";
                    }
                }
            }

            if ($origin != 'learnpath') {
                if (GroupManager::is_tutor_of_group($userId, $groupInfo) ||
                    api_is_allowed_to_edit(false, true) &&
                    !(api_is_session_general_coach() && $current_forum['session_id'] != $sessionId)
                ) {
                    if ($locked === false) {
                        $deleteUrl = api_get_self().'?'.api_get_cidreq().'&'.http_build_query([
                            'forum' => $clean_forum_id,
                            'thread' => $clean_thread_id,
                            'action' => 'delete',
                            'content' => 'post',
                            'id' => $row['post_id']
                        ]);
                        $iconEdit .= Display::url(
                            Display::return_icon('delete.png', get_lang('Delete'), [], ICON_SIZE_SMALL),
                            $deleteUrl,
                            [
                                'onclick' => "javascript:if(!confirm('"
                                    . addslashes(api_htmlentities(get_lang('DeletePost'), ENT_QUOTES))
                                    . "')) return false;",
                                'id' => "delete-post-{$row['post_id']}"
                            ]
                        );
                    }
                }

                $statusIcon = getPostStatus($current_forum, $row);

                if (GroupManager::is_tutor_of_group($userId, $groupInfo) ||
                    (
                        api_is_allowed_to_edit(false, true) &&
                    !(api_is_session_general_coach() && $current_forum['session_id'] != $sessionId)
                    )
                ) {
                    $iconEdit .= return_visible_invisible_icon(
                        'post',
                        $row['post_id'],
                        $row['visible'],
                        [
                            'forum' => $clean_forum_id,
                            'thread' => $clean_thread_id,
                            'origin' => $origin
                        ]
                    );

                    if ($increment > 0) {
                        $iconEdit .= "<a href=\"viewthread.php?".api_get_cidreq()."&forum=".$clean_forum_id
                            . "&thread=".$clean_thread_id."&action=move&post=".$row['post_id']."\">"
                            . Display::return_icon('move.png', get_lang('MovePost'), [], ICON_SIZE_SMALL)
                            . "</a>";
                    }
                }
            }

            $user_status = api_get_status_of_user_in_course($posterId, api_get_course_int_id());
            $current_qualify_thread = showQualify('1', $row['poster_id'], $_GET['thread']);
            if (($current_thread['thread_peer_qualify'] == 1 || api_is_allowed_to_edit(null, true)) &&
                $current_thread['thread_qualify_max'] > 0 && $origin != 'learnpath'
            ) {
                $my_forum_id = $clean_forum_id;
                $info_thread = get_thread_information($clean_forum_id, $clean_thread_id);
                $my_forum_id = $info_thread['forum_id'];
                $userCanEdit = $current_thread['thread_peer_qualify'] == 1 && $row['poster_id'] != $userId;
                /*if ($row['poster_id'] != $userId && $current_forum['moderated'] == 1 && $row['status']) {
                }*/
                if (api_is_allowed_to_edit(null, true)) {
                    $userCanEdit = true;
                }

                if ($increment > 0 && $locked == false && $userCanEdit) {
                    $iconEdit .= "<a href=\"forumqualify.php?".api_get_cidreq()."&forum=".$my_forum_id
                        . "&thread=".$clean_thread_id."&action=list&post=".$row['post_id']
                        . "&user=".$row['poster_id']."&user_id=".$row['poster_id']
                        . "&idtextqualify=".$current_qualify_thread."\" >"
                        . Display::return_icon('quiz.png', get_lang('Qualify'))
                        . "</a> ";
                }
            }

            if (!empty($iconEdit)) {
                $html .= '<div class="tools-icons">'.$iconEdit.' '.$statusIcon.'</div>';
            } else {
                if (!empty(strip_tags($statusIcon))) {
                    $html .= '<div class="tools-icons">'.$statusIcon.'</div>';
                }
            }
            $html .= $closedPost;
            $html .= '</div>';
            $html .= '<div class="col-md-10">';

            $titlePost = Display::tag(
                'h3',
                $row['post_title'],
                ['class' => 'forum_post_title']
            );

            $html .= Display::tag(
                'div',
                $titlePost,
                ['class' => 'post-header']
            );

            // see comments inside forumfunction.inc.php to lower filtering and allow more visual changes
            $html .= Display::tag(
                'div',
                $row['post_text'],
                ['class' => 'post-body']
            );
            $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="row">';
            $html .= '<div class="col-md-7">';

            // prepare the notification icon
            if (isset($whatsnew_post_info[$current_forum['forum_id']][$current_thread['thread_id']][$row['post_id']]) &&
                !empty(
                    $whatsnew_post_info[$current_forum['forum_id']][$current_thread['thread_id']][$row['post_id']]
                ) &&
                !empty($whatsnew_post_info[$_GET['forum']][$row['thread_id']])
            ) {
                $post_image = Display::return_icon('forumpostnew.gif');
            } else {
                $post_image = Display::return_icon('forumpost.gif');
            }

            if ($row['post_notification'] == '1' && $row['poster_id'] == $_user['user_id']) {
                $post_image .= Display::return_icon('forumnotification.gif', get_lang('YouWillBeNotified'));
            }
            // The post title
            // The check if there is an attachment
            $attachment_list = getAllAttachment($row['post_id']);
            if (!empty($attachment_list) && is_array($attachment_list)) {
                foreach ($attachment_list as $attachment) {
                    $realname = $attachment['path'];
                    $user_filename = $attachment['filename'];
                    $html .= Display::return_icon('attachment.gif', get_lang('Attachment'));
                    $html .= '<a href="download.php?file='.$realname.'"> '.$user_filename.' </a>';

                    if (($current_forum['allow_edit'] == 1 && $row['user_id'] == $_user['user_id']) ||
                        (api_is_allowed_to_edit(false, true) && !(api_is_session_general_coach() && $current_forum['session_id'] != $sessionId))
                    ) {
                        $html .= '&nbsp;&nbsp;<a href="'.api_get_self().'?'.api_get_cidreq().'&action=delete_attach&id_attach='
                            . $attachment['iid'].'&forum='.$clean_forum_id.'&thread='.$clean_thread_id
                            . '" onclick="javascript:if(!confirm(\''
                            . addslashes(api_htmlentities(get_lang('ConfirmYourChoice'), ENT_QUOTES))
                            . '\')) return false;">'
                            . Display::return_icon('delete.png', get_lang('Delete'), [], ICON_SIZE_SMALL)
                            . '</a><br />';
                    }
                    $html .= '<span class="forum_attach_comment" >'.$attachment['comment'].'</span>';
                }
            }

            $html .= '</div>';
            $html .= '<div class="col-md-5 text-right">';
            $html .= $buttonReply.' '.$buttonQuote;
            $html .= '</div>';
            $html .= '</div>';

            // The post has been displayed => it can be removed from the what's new array
            unset($whatsnew_post_info[$current_forum['forum_id']][$current_thread['thread_id']][$row['post_id']]);
            unset($whatsnew_post_info[$current_forum['forum_id']][$current_thread['thread_id']]);
            unset($_SESSION['whatsnew_post_info'][$current_forum['forum_id']][$current_thread['thread_id']][$row['post_id']]);
            unset($_SESSION['whatsnew_post_info'][$current_forum['forum_id']][$current_thread['thread_id']]);

            $increment++;

            $html .= '</div>';
            $html .= '</div>';
            echo $html;
        }
    }
}
