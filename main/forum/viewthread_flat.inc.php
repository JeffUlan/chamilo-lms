<?php
/* For licensing terms, see /license.txt */

/**
 * This script manages the display of forum threads in flat view
 * @copyright Julio Montoya <gugli100@gmail.com> UI Improvements + lots of bugfixes
 * @package chamilo.forum
 */

//delete attachment file
if ((isset($_GET['action']) &&
    $_GET['action']=='delete_attach') &&
    isset($_GET['id_attach'])
) {
    delete_attachment(0,$_GET['id_attach']);
}

$sessionId = api_get_session_id();
$_user = api_get_user_info();
$userId = api_get_user_id();
$groupId = api_get_group_id();

if (isset($current_thread['thread_id'])) {
    $rows = get_posts($current_thread['thread_id']);
    $increment = 0;
    $clean_forum_id  = intval($_GET['forum']);
    $clean_thread_id = intval($_GET['thread']);

    $locked = api_resource_is_locked_by_gradebook(
        $clean_thread_id,
        LINK_FORUM_THREAD
    );

    if (!empty($rows)) {
        foreach ($rows as $row) {

            if (($current_forum_category && $current_forum_category['locked'] == 0) &&
                $current_forum['locked'] == 0 &&
                $current_thread['locked'] == 0 ||
                api_is_allowed_to_edit(false, true)
            ) {
                if ($_user['user_id'] || ($current_forum['allow_anonymous'] == 1 && !$_user['user_id'])) {
                    if (!api_is_anonymous() && api_is_allowed_to_session_edit(false,true)) {

                        $buttonReply = Display::tag('a','<i class="fa fa-reply"></i> '.get_lang('ReplyToMessage') ,array('href' => 'reply.php?'.api_get_cidreq().'&forum='.$clean_forum_id.'&thread='.$clean_thread_id.'&post='.$row['post_id'].'&action=replymessage&origin='.$origin, 'class' => 'btn btn-primary'));

                        $buttonQuote = Display::tag('a','<i class="fa fa-quote-left"></i> '.get_lang('QuoteMessage'),array('href' => 'reply.php?'.api_get_cidreq().'&forum='.$clean_forum_id.'&thread='.$clean_thread_id.'&post='.$row['post_id'].'&action=quote&origin='.$origin, 'class' => 'btn btn-success'));


                    }
                }
            } else {

                if (($current_forum_category && $current_forum_category['locked'] == 1)) {
                    $closedPost = Display::tag('div','<i class="fa fa-exclamation-triangle"></i> '.get_lang('ForumcategoryLocked'),array('class'=>'alert alert-warning post-closed'));
                }
                if ($current_forum['locked']==1) {
                    $closedPost = Display::tag('div','<i class="fa fa-exclamation-triangle"></i> '.get_lang('ForumLocked'),array('class'=>'alert alert-warning post-closed'));
                }
                if ($current_thread['locked']==1) {
                    $closedPost = Display::tag('div','<i class="fa fa-exclamation-triangle"></i> '.get_lang('ThreadLocked'),array('class'=>'alert alert-warning post-closed'));
                }
            }

            $html = '';
            $html .= '<div class="panel panel-default forum-post">';
            $html .= '<div class="panel-body">';
            $html .= '<div class="row">';

            $html .= '<div class="col-md-2">';

            if ($origin!='learnpath') {
                if (api_get_course_setting('allow_user_image_forum')) {
                    $html .= '<div class="thumbnail">'.display_user_image($row['user_id'], $name).'</div>';
                }
                $html .= Display::tag('h4', display_user_link($row['user_id'], $name, '', $username), array('class' => 'title-username'));
            } else {
                $html .=  Display::tag('span', $name, array('title' => api_htmlentities($username, ENT_QUOTES)));
            }

            $html .= Display::tag('p',api_convert_and_format_date($row['post_date']),array('class' => 'post-date'));

            // get attach id
            $attachment_list = get_attachment($row['post_id']);
            $id_attach = !empty($attachment_list) ? $attachment_list['id'] : '';
            $iconEdit = '';
            // The user who posted it can edit his thread only if the course admin allowed this in the properties of the forum
            // The course admin him/herself can do this off course always
            if (
                GroupManager::is_tutor_of_group($userId, $groupId) ||
                ($current_forum['allow_edit'] == 1 && $row['user_id'] == $_user['user_id']) ||
                (
                    api_is_allowed_to_edit(false, true) &&
                    !(api_is_course_coach() && $current_forum['session_id'] != $sessionId)
                )
            ) {
                if (api_is_allowed_to_session_edit(false, true)) {
                    if ($locked == false) {
                        $iconEdit .= "<a href=\"editpost.php?".api_get_cidreq()."&forum=".$clean_forum_id."&thread=".$clean_thread_id."&post=".$row['post_id']."&amp;origin=".$origin."&amp;edit=edition&amp;id_attach=".$id_attach."\">".
                            Display::return_icon('edit.png',get_lang('Edit'), array(), ICON_SIZE_SMALL)."</a>";
                    }
                }
            }

            if ($origin != 'learnpath') {
                if (GroupManager::is_tutor_of_group($userId, $groupId) ||
                    api_is_allowed_to_edit(false, true) &&
                    !(api_is_course_coach() && $current_forum['session_id'] != $sessionId)
                ) {

                    if ($locked == false) {
                        $iconEdit .= "<a href=\"".api_get_self()."?".api_get_cidreq()."&forum=".$clean_forum_id."&thread=".$clean_thread_id."&action=delete&amp;content=post&amp;id=".$row['post_id']."&amp;origin=".$origin."\" onclick=\"javascript:if(!confirm('".addslashes(api_htmlentities(get_lang('DeletePost'), ENT_QUOTES))."')) return false;\">".
                            Display::return_icon('delete.png', get_lang('Delete'),array(), ICON_SIZE_SMALL)."</a>";
                    }
                }
                if (api_is_allowed_to_edit(false, true) &&
                    !(api_is_course_coach() && $current_forum['session_id'] != $sessionId)
                ) {
                    $iconEdit .= return_visible_invisible_icon(
                        'post',
                        $row['post_id'],
                        $row['visible'],
                        array(
                            'forum' => $clean_forum_id,
                            'thread' => $clean_thread_id,
                            'origin' => $origin,
                        )
                    );
                    $iconEdit .= "";
                    if ($increment>0) {
                        $iconEdit .= "<a href=\"viewthread.php?".api_get_cidreq()."&amp;forum=".$clean_forum_id."&amp;thread=".$clean_thread_id."&amp;action=move&amp;post=".$row['post_id']."&amp;origin=".$origin."\">".Display::return_icon('move.png',get_lang('MovePost'), array(), ICON_SIZE_SMALL)."</a>";
                    }
                }
            }

            $user_status = api_get_status_of_user_in_course(
                $row['user_id'],
                api_get_course_int_id()
            );

            $current_qualify_thread = showQualify(
                '1',
                $row['poster_id'],
                $_GET['thread']
            );

            if (
                (
                    $current_thread['thread_peer_qualify'] == 1 ||
                    api_is_allowed_to_edit(null, true)
                ) && $current_thread['thread_qualify_max'] > 0
                && $origin != 'learnpath'
            ) {
                $my_forum_id = $clean_forum_id;
                if (isset($_GET['gradebook'])) {
                    $info_thread = get_thread_information($clean_thread_id);
                    $my_forum_id = $info_thread['forum_id'];
                }

                $userCanEdit = $current_thread['thread_peer_qualify'] == 1 && $row['poster_id'] != $userId;
                if (api_is_allowed_to_edit(null, true)) {
                    $userCanEdit = true;
                }
                if ($increment > 0 && $locked == false && $userCanEdit) {
                    $iconEdit .= "<a href=\"forumqualify.php?".api_get_cidreq()."&forum=".$my_forum_id."&thread=".$clean_thread_id."&action=list&post=".$row['post_id']."&amp;user=".$row['poster_id']."&amp;user_id=".$row['poster_id']."&origin=".$origin."&idtextqualify=".$current_qualify_thread."\" >".
                        Display::return_icon('quiz.gif',get_lang('Qualify'))."</a> ";
                }
            }
            if($iconEdit != ''){
                $html .= '<div class="tools-icons">'.$iconEdit.'</div>';
            }

            $html .= $closedPost;
            $html .= '</div>';

            $html .= '<div class="col-md-10">';


            $titlePost = Display::tag('h3', $row['post_title'], array('class'=>'forum_post_title'));
            $html .= Display::tag('div',$titlePost,array('class' => 'post-header'));

            // see comments inside forumfunction.inc.php to lower filtering and allow more visual changes

            $html .= Display::tag('div',$row['post_text'],array('class' => 'post-body'));
            $html .= '</div>';
            $html .= '</div>';


            // the style depends on the status of the message: approved or not
            /* if ($row['visible']=='0') {
                $titleclass = 'forum_message_post_title_2_be_approved';
                $messageclass = 'forum_message_post_text_2_be_approved';
                $leftclass = 'forum_message_left_2_be_approved';
            } else {
                $titleclass = 'forum_message_post_title';
                $messageclass = 'forum_message_post_text';
                $leftclass = 'forum_message_left';
            }


            if ($row['user_id']=='0') {
                $name = prepare4display($row['poster_name']);
            } else {
                $name = api_get_person_name($row['firstname'], $row['lastname']);
            }
            $username = sprintf(get_lang('LoginX'), $row['username']);

            */

            $html .= '<div class="row">';
            $html .= '<div class="col-md-12">';
            $html .= '<div class="pull-right">'.$buttonReply . ' ' . $buttonQuote . '</div>';
            $html .= '</div>';
            $html .= '</div>';

            // prepare the notification icon
            if (isset($whatsnew_post_info[$current_forum['forum_id']][$current_thread['thread_id']][$row['post_id']]) &&
                !empty($whatsnew_post_info[$current_forum['forum_id']][$current_thread['thread_id']][$row['post_id']]) &&
                !empty($whatsnew_post_info[$_GET['forum']][$row['thread_id']])
            ) {
                $post_image = Display::return_icon('forumpostnew.gif');
            } else {
                $post_image = Display::return_icon('forumpost.gif');
            }

            if ($row['post_notification']=='1' && $row['poster_id'] == $_user['user_id']) {
                $post_image .= Display::return_icon('forumnotification.gif', get_lang('YouWillBeNotified'));
            }
            // The post title






            // The check if there is an attachment

            $attachment_list = getAllAttachment($row['post_id']);
            if (!empty($attachment_list) && is_array($attachment_list)) {
                foreach ($attachment_list as $attachment) {

                    $realname = $attachment['path'];
                    $user_filename=$attachment['filename'];

                    echo Display::return_icon('attachment.gif',get_lang('Attachment'));
                    echo '<a href="download.php?file='.$realname.'"> '.$user_filename.' </a>';

                    if (($current_forum['allow_edit']==1 && $row['user_id']==$_user['user_id']) ||
                        (api_is_allowed_to_edit(false,true)  && !(api_is_course_coach() && $current_forum['session_id']!=$sessionId))
                    )	{
                        echo '&nbsp;&nbsp;<a href="'.api_get_self().'?'.api_get_cidreq().'&amp;origin='.Security::remove_XSS($_GET['origin']).'&amp;action=delete_attach&amp;id_attach='.$attachment['id'].'&amp;forum='.$clean_forum_id.'&amp;thread='.$clean_thread_id.'" onclick="javascript:if(!confirm(\''.addslashes(api_htmlentities(get_lang('ConfirmYourChoice'), ENT_QUOTES)).'\')) return false;">'.Display::return_icon('delete.png',get_lang('Delete'), array(), ICON_SIZE_SMALL).'</a><br />';
                    }
                    echo '<span class="forum_attach_comment" >'.$attachment['comment'].'</span>';

                }
            }

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
