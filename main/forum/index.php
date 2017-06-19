<?php
/* For licensing terms, see /license.txt */

use ChamiloSession as Session;
use Chamilo\CourseBundle\Entity\CForumPost;

/**
 * These files are a complete rework of the forum. The database structure is
 * based on phpBB but all the code is rewritten. A lot of new functionalities
 * are added:
 * - forum categories and forums can be sorted up or down, locked or made invisible
 * - consistent and integrated forum administration
 * - forum options:     are students allowed to edit their post?
 *                      moderation of posts (approval)
 *                      reply only forums (students cannot create new threads)
 *                      multiple forums per group
 * - sticky messages
 * - new view option: nested view
 * - quoting a message
 *
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @copyright Ghent University
 * @copyright Patrick Cool
 *
 * @package chamilo.forum
 */

require_once __DIR__.'/../inc/global.inc.php';
$current_course_tool = TOOL_FORUM;
$htmlHeadXtra[] = '<script>

$(document).ready(function() {
    $(\'.hide-me\').slideUp();
});

function hidecontent(content){
    $(content).slideToggle(\'normal\');
}

</script>';

// The section (tabs).
$this_section = SECTION_COURSES;

// Notification for unauthorized people.
api_protect_course_script(true);

$nameTools = get_lang('Forums');
$_course = api_get_course_info();
$sessionId = api_get_session_id();
$_user = api_get_user_info();

// Including necessary files.
require_once 'forumconfig.inc.php';
require_once 'forumfunction.inc.php';

if (!empty($_GET['gradebook']) && $_GET['gradebook'] == 'view') {
    $_SESSION['gradebook'] = Security::remove_XSS($_GET['gradebook']);
    $gradebook = $_SESSION['gradebook'];
} elseif (empty($_GET['gradebook'])) {
    unset($_SESSION['gradebook']);
    $gradebook = '';
}

if (!empty($gradebook) && $gradebook == 'view') {
    $interbreadcrumb[] = array(
        'url' => '../gradebook/'.$_SESSION['gradebook_dest'],
        'name' => get_lang('ToolGradebook'),
    );
}

$search_forum = isset($_GET['search']) ? Security::remove_XSS($_GET['search']) : '';

/* ACTIONS */

$actions = isset($_GET['action']) ? $_GET['action'] : '';

if ($actions === 'add') {
    switch ($_GET['content']) {
        case 'forum':
            $interbreadcrumb[] = array(
                'url' => 'index.php?search='.$search_forum.'&'.api_get_cidreq(),
                'name' => get_lang('Forum'),
            );
            $interbreadcrumb[] = array(
                'url' => '#',
                'name' => get_lang('AddForum'),
            );
            break;
        case 'forumcategory':
            $interbreadcrumb[] = array(
                'url' => 'index.php?search='.$search_forum.'&'.api_get_cidreq(),
                'name' => get_lang('Forum'),
            );
            $interbreadcrumb[] = array(
                'url' => '#',
                'name' => get_lang('AddForumCategory'),
            );
            break;
        default:
            break;
    }
} else {
    $interbreadcrumb[] = array(
        'url' => '#',
        'name' => get_lang('ForumCategories'),
    );
}
Display::display_header('');
// Tool introduction
$introduction = Display::return_introduction_section(TOOL_FORUM);

$form_count = 0;

if (api_is_allowed_to_edit(false, true)) {
    //if is called from a learning path lp_id
    $lp_id = isset($_REQUEST['lp_id']) ? intval($_REQUEST['lp_id']) : null;
    handle_forum_and_forumcategories($lp_id);
}

// Notification
if ($actions == 'notify' && isset($_GET['content']) && isset($_GET['id'])) {
    if (
        api_get_session_id() != 0
        && api_is_allowed_to_session_edit(false, true) == false
    ) {
        api_not_allowed();
    }
    $return_message = set_notification($_GET['content'], $_GET['id']);
    Display::addFlash(Display::return_message($return_message, 'confirm', false));
}

get_whats_new();

$whatsnew_post_info = Session::read('whatsnew_post_info');

$tpl = new Template($nameTools, false, false, false, false, true, false);
/* TRACKING */

Event::event_access_tool(TOOL_FORUM);

/*
    RETRIEVING ALL THE FORUM CATEGORIES AND FORUMS
    note: we do this here just after het handling of the actions to be
    sure that we already incorporate the latest changes
*/

// Step 1: We store all the forum categories in an array $forum_categories.
$forumCategories = get_forum_categories();

// Step 2: We find all the forums (only the visible ones if it is a student).
// display group forum in general forum tool depending to configuration option
$setting = api_get_setting('display_groups_forum_in_general_tool');

$allCourseForums = get_forums('', '', $setting === 'true');
$user_id = api_get_user_id();

/* RETRIEVING ALL GROUPS AND THOSE OF THE USER */

// The groups of the user.
$groups_of_user = array();
$groups_of_user = GroupManager::get_group_ids($_course['real_id'], $user_id);

// All groups in the course (and sorting them as the
// id of the group = the key of the array).
if (!api_is_anonymous()) {
    $all_groups = GroupManager::get_group_list();
    if (is_array($all_groups)) {
        foreach ($all_groups as $group) {
            $all_groups[$group['id']] = $group;
        }
    }
}

/* ACTION LINKS */
$actionLeft = null;
//if is called from learning path
if (!empty($_GET['lp_id']) || !empty($_POST['lp_id'])) {
    $url = "../lp/lp_controller.php?".api_get_cidreq()
        ."&gradebook=&action=add_item&type=step&lp_id='.$lp_id.'#resource_tab-5";
    $actionLeft .= Display::url(
        Display::return_icon(
            'back.png',
            get_lang("BackTo").' '.get_lang("LearningPaths"),
            null,
            ICON_SIZE_MEDIUM
        ),
        $url
    );
}
if (!empty($allCourseForums)) {
    $actionLeft .= search_link();
}

if (api_is_allowed_to_edit(false, true)) {
    $actionLeft .= Display::url(
        Display::return_icon(
            'new_folder.png',
            get_lang('AddForumCategory'),
            null,
            ICON_SIZE_MEDIUM
        ),
        api_get_self().'?'.api_get_cidreq().'&action=add&content=forumcategory&lp_id='.$lp_id);

    if (is_array($forumCategories) && !empty($forumCategories)) {
        $actionLeft .= Display::url(
            Display::return_icon(
                'new_forum.png',
                get_lang('AddForum'),
                null,
                ICON_SIZE_MEDIUM
            ),
            api_get_self().'?'.api_get_cidreq().'&action=add&content=forum&lp_id='.$lp_id
        );
    }
}

$actions = Display::toolbarAction('toolbar-forum', array($actionLeft));

// Fixes error if there forums with no category.
$forumsInNoCategory = get_forums_in_category(0);
if (!empty($forumsInNoCategory)) {
    $forumCategories = array_merge(
        $forumCategories,
        array(
            array(
                'cat_id' => 0,
                'session_id' => 0,
                'visibility' => 1,
                'cat_comment' => null
            )
        )
    );
}

/* Display Forum Categories and the Forums in it */
// Step 3: We display the forum_categories first.
$listForumCategory = array();
$dataForum = array();

if (is_array($forumCategories)) {
    foreach ($forumCategories as $forumCategory) {
        $dataForum['id'] = $forumCategory['cat_id'];
        if (empty($forumCategory['cat_title'])) {
            $dataForum['title'] = get_lang('WithoutCategory');
        } else {
            $dataForum['title'] = $forumCategory['cat_title'];
        }
        $dataForum['icon_session'] = api_get_session_image(
            $forumCategory['session_id'], $_user['status']
        );

        // Validation when belongs to a session
        $dataForum['description'] = $forumCategory['cat_comment'];

        if (empty($sessionId) && !empty($forumCategory['session_name'])) {
            $forumCategory['session_display'] = ' ('.Security::remove_XSS($forumCategory['session_name']).')';
        } else {
            $forumCategory['session_display'] = null;
        }

        $tools = null;
        $idCategory = $forumCategory['cat_id'];
        $dataForum['url'] = 'viewforumcategory.php?'.api_get_cidreq().'&forumcategory='.intval($idCategory);

        if (!empty($idCategory)) {
            if (
                api_is_allowed_to_edit(false, true)
                && !($forumCategory['session_id'] == 0
                    && intval($sessionId) != 0)
            ) {
                $tools .= '<a href="'.api_get_self().'?'.api_get_cidreq()
                    .'&action=edit&content=forumcategory&id='.intval($idCategory)
                    .'">'.Display::return_icon(
                        'edit.png', get_lang('Edit'), array(), ICON_SIZE_SMALL
                    )
                    .'</a>';

                $tools .= '<a href="'.api_get_self().'?'.api_get_cidreq()
                    .'&action=delete&content=forumcategory&id='.intval($idCategory)
                    ."\" onclick=\"javascript:if(!confirm('"
                    .addslashes(api_htmlentities(
                        get_lang('DeleteForumCategory'), ENT_QUOTES
                    ))
                    ."')) return false;\">"
                    .Display::return_icon(
                        'delete.png', get_lang('Delete'), array(), ICON_SIZE_SMALL
                    )
                    .'</a>';
                $tools .= return_visible_invisible_icon(
                    'forumcategory',
                    strval(intval($idCategory)),
                    strval(intval($forumCategory['visibility']))
                );
                $tools .= return_lock_unlock_icon(
                    'forumcategory',
                    strval(intval($idCategory)),
                    strval(intval($forumCategory['locked']))
                );
                $tools .= return_up_down_icon(
                    'forumcategory',
                    strval(intval($idCategory)),
                    $forumCategories
                );
            }
        }
        $dataForum['tools'] = $tools;
        $dataForum['forums'] = [];
        // The forums in this category.
        $forumInfo = array();
        $forumsInCategory = get_forums_in_category($forumCategory['cat_id']);

        if (!empty($forumsInCategory)) {
            $forumsDetailsList = null;
            // We display all the forums in this category.
            foreach ($allCourseForums as $forum) {
                // Here we clean the whatnew_post_info array a little bit because to display the icon we
                // test if $whatsnew_post_info[$forum['forum_id']] is empty or not.
                if (isset($forum['forum_id'])) {
                    if (!empty($whatsnew_post_info)) {
                        if (
                            isset($whatsnew_post_info[$forum['forum_id']])
                            && is_array($whatsnew_post_info[$forum['forum_id']])
                        ) {
                            foreach ($whatsnew_post_info[$forum['forum_id']] as $key_thread_id => $new_post_array) {
                                if (empty($whatsnew_post_info[$forum['forum_id']][$key_thread_id])) {
                                    unset($whatsnew_post_info[$forum['forum_id']][$key_thread_id]);
                                    unset($_SESSION['whatsnew_post_info'][$forum['forum_id']][$key_thread_id]);
                                }
                            }
                        }
                    }
                }

                // Note: This can be speed up if we transform the $allCourseForums
                // to an array that uses the forum_category as the key.
                if (isset($forum['forum_category']) && $forum['forum_category'] == $forumCategory['cat_id']) {
                    $show_forum = false;
                    // SHOULD WE SHOW THIS PARTICULAR FORUM
                    // you are teacher => show forum
                    if (api_is_allowed_to_edit(false, true)) {
                        $show_forum = true;
                    } else {
                        // you are not a teacher
                        // it is not a group forum => show forum
                        // (invisible forums are already left out see get_forums function)
                        if ($forum['forum_of_group'] == '0') {
                            $show_forum = true;
                        } else {
                            $show_forum = GroupManager::user_has_access(
                                $user_id,
                                $forum['forum_of_group'],
                                GroupManager::GROUP_TOOL_FORUM
                            );
                        }
                    }

                    if ($show_forum) {
                        $form_count++;
                        $mywhatsnew_post_info = isset($whatsnew_post_info[$forum['forum_id']])
                            ? $whatsnew_post_info[$forum['forum_id']]
                            : null;
                        $forumInfo['id'] = $forum['forum_id'];
                        $forumInfo['forum_of_group'] = $forum['forum_of_group'];
                        $forumInfo['title'] = $forum['forum_title'];
                        $forumInfo['forum_image'] = null;
                        // Showing the image
                        if (!empty($forum['forum_image'])) {
                            $image_path = api_get_path(WEB_COURSE_PATH).api_get_course_path().'/upload/forum/images/'
                                .$forum['forum_image'];
                            $image_size = api_getimagesize($image_path);
                            $img_attributes = '';
                            if (!empty($image_size)) {
                                $forumInfo['forum_image'] = $image_path;
                            }
                        }
                        // Validation when belongs to a session
                        $forumInfo['icon_session'] = api_get_session_image(
                            $forum['session_id'], $_user['status']
                        );
                        if ($forum['forum_of_group'] != '0') {
                            $my_all_groups_forum_name = isset($all_groups[$forum['forum_of_group']]['name'])
                                ? $all_groups[$forum['forum_of_group']]['name']
                                : null;
                            $my_all_groups_forum_id = isset($all_groups[$forum['forum_of_group']]['id'])
                                ? $all_groups[$forum['forum_of_group']]['id']
                                : null;
                            $group_title = api_substr($my_all_groups_forum_name, 0, 30);
                            $forumInfo['forum_group_title'] = $group_title;
                        }

                        $forum['forum_of_group'] == 0 ? $groupid = '' : $groupid = $forum['forum_of_group'];
                        $forumInfo['visibility'] = $forum['visibility'];
                        $forumInfo['number_threads'] = isset($forum['number_of_threads'])
                            ? (int) $forum['number_of_threads']
                            : 0;
                        //$number_posts = isset($forum['number_of_posts']) ? $forum['number_of_posts'] : 0;

                        $linkForum = api_get_path(WEB_CODE_PATH).'forum/viewforum.php?'.api_get_cidreq()
                            .'&gidReq='.$groupid.'&forum='.$forum['forum_id'];
                        $forumInfo['url'] = $linkForum;

                        if (!empty($forum['start_time']) && !empty($forum['end_time'])) {
                            $res = api_is_date_in_date_range($forum['start_time'], $forum['end_time']);
                            if (!$res) {
                                $linkForum = $forum['forum_title'];
                            }
                        }

                        $forumInfo['description'] = Security::remove_XSS($forum['forum_comment']);

                        if ($forum['moderated'] == 1 && api_is_allowed_to_edit(false, true)) {
                            $waitingCount = getCountPostsWithStatus(
                                CForumPost::STATUS_WAITING_MODERATION, $forum
                            );
                            if (!empty($waitingCount)) {
                                $forumInfo['moderation'] = $waitingCount;
                            }
                        }

                        $toolActions = null;
                        $forumInfo['alert'] = null;
                        // The number of topics and posts.
                        if ($forum['forum_of_group'] !== '0') {
                            if (is_array($mywhatsnew_post_info) && !empty($mywhatsnew_post_info)) {
                                $forumInfo['alert'] = ' '.Display::return_icon(
                                        'alert.png', get_lang('Forum'), null, ICON_SIZE_SMALL
                                    );
                            }
                        } else {
                            if (is_array($mywhatsnew_post_info) && !empty($mywhatsnew_post_info)) {
                                $forumInfo['alert'] = ' '.Display::return_icon(
                                    'alert.png',
                                    get_lang('Forum'),
                                    null,
                                    ICON_SIZE_SMALL
                                );
                            }
                        }
                        $poster_id = null;
                        // The last post in the forum.
                        if (isset($forum['last_poster_name']) && $forum['last_poster_name'] != '') {
                            $name = $forum['last_poster_name'];
                            $poster_id = 0;
                            $username = "";
                        } else {
                            if (isset($forum['last_poster_firstname'])) {
                                $name = api_get_person_name(
                                    $forum['last_poster_firstname'],
                                    $forum['last_poster_lastname']
                                );
                                $poster_id = $forum['last_poster_id'];
                                $userinfo = api_get_user_info($poster_id);
                                $username = sprintf(
                                    get_lang('LoginX'),
                                    $userinfo['username']
                                );
                            }
                        }
                        $forumInfo['last_poster_id'] = $poster_id;

                        if (!empty($forum['last_poster_id'])) {
                            $forumInfo['last_poster_date'] = api_convert_and_format_date($forum['last_post_date']);
                            $forumInfo['last_poster_user'] = display_user_link($poster_id, $name, null, $username);
                        }

                        if (api_is_allowed_to_edit(false, true)
                            && !($forum['session_id'] == 0 && intval($sessionId) != 0)
                        ) {
                            $toolActions .= '<a href="'.api_get_self().'?'.api_get_cidreq()
                                .'&action=edit&content=forum&id='.$forum['forum_id'].'">'
                                .Display::return_icon('edit.png', get_lang('Edit'), array(), ICON_SIZE_SMALL)
                                .'</a>';
                            $toolActions .= '<a href="'.api_get_self().'?'.api_get_cidreq()
                                .'&action=delete&content=forum&id='.$forum['forum_id']
                                ."\" onclick=\"javascript:if(!confirm('".addslashes(
                                    api_htmlentities(get_lang('DeleteForum'), ENT_QUOTES)
                                )
                                ."')) return false;\">"
                                .Display::return_icon('delete.png', get_lang('Delete'), array(), ICON_SIZE_SMALL)
                                .'</a>';

                            $toolActions .= return_visible_invisible_icon(
                                'forum',
                                $forum['forum_id'],
                                $forum['visibility']
                            );

                            $toolActions .= return_lock_unlock_icon(
                                'forum',
                                $forum['forum_id'],
                                $forum['locked']
                            );

                            $toolActions .= return_up_down_icon(
                                'forum',
                                $forum['forum_id'],
                                $forumsInCategory
                            );
                        }

                        $iconnotify = 'notification_mail_na.png';
                        $session_forum_notification = isset($_SESSION['forum_notification']['forum'])
                            ? $_SESSION['forum_notification']['forum']
                            : false;

                        if (is_array($session_forum_notification)) {
                            if (in_array($forum['forum_id'], $session_forum_notification)) {
                                $iconnotify = 'notification_mail.png';
                            }
                        }

                        if (!api_is_anonymous() && api_is_allowed_to_session_edit(false, true)) {
                            $toolActions .= '<a href="'.api_get_self().'?'.api_get_cidreq()
                                .'&action=notify&content=forum&id='.$forum['forum_id'].'">'
                                .Display::return_icon($iconnotify, get_lang('NotifyMe'), null, ICON_SIZE_SMALL)
                                .'</a>';
                        }
                        $forumInfo['tools'] = $toolActions;
                        $forumsDetailsList[] = $forumInfo;
                    }
                }
            }
            $dataForum['forums'] = $forumsDetailsList;
        }
        $listForumCategory[] = $dataForum;
    }
}

$tpl->assign('introduction_section', $introduction);
$tpl->assign('actions', $actions);
$tpl->assign('data', $listForumCategory);
$layout = $tpl->get_template('forum/list.tpl');
$tpl->display($layout);
Display:: display_footer();
