<?php
/* For licensing terms, see /license.txt */
/**
 * 	@package chamilo.messages
 */

// name of the language file that needs to be included
$language_file = array('registration', 'messages', 'userInfo');
$cidReset = true;

require_once '../inc/global.inc.php';

api_block_anonymous_users();
if (isset($_GET['messages_page_nr'])) {
    $social_link = '';
    if ($_REQUEST['f'] == 'social') {
        $social_link = '?f=social';
    }
    if (api_get_setting('allow_social_tool') == 'true' && api_get_setting('allow_message_tool') == 'true') {
        header('Location:inbox.php'.$social_link);
    }
}
if (api_get_setting('allow_message_tool') != 'true') {
    api_not_allowed();
}

$htmlHeadXtra[] = '<script>

function show_icon_edit(element_html) {
	ident="#edit_image";
	$(ident).show();
}

function hide_icon_edit(element_html)  {
	ident="#edit_image";
	$(ident).hide();
}

</script>';

/*
  MAIN CODE
 */
$nameTools = get_lang('Messages');
$request = api_is_xml_http_request();
$show_message = null;
if (isset($_GET['form_reply']) || isset($_GET['form_delete'])) {
    $info_reply = array();
    $info_delete = array();

    if (isset($_GET['form_reply'])) {
        //allow to insert messages
        $info_reply = explode(base64_encode('&%ff..x'), $_GET['form_reply']);
        $count_reply = count($info_reply);
        $button_sent = urldecode($info_reply[4]);
    }

    if (isset($_GET['form_delete'])) {
        //allow to delete messages
        $info_delete = explode(',', $_GET['form_delete']);
        $count_delete = (count($info_delete) - 1);
    }

    if (isset($button_sent)) {
        $title = urldecode($info_reply[0]);
        $content = str_replace("\\", "", urldecode($info_reply[1]));

        $user_reply = $info_reply[2];
        $user_email_base = str_replace(')', '(', $info_reply[5]);
        $user_email_prepare = explode('(', $user_email_base);
        if (count($user_email_prepare) == 1) {
            $user_email = trim($user_email_prepare[0]);
        } elseif (count($user_email_prepare) == 3) {
            $user_email = trim($user_email_prepare[1]);
        }
        $user_id_by_email = MessageManager::get_user_id_by_email($user_email);

        if ($info_reply[6] == 'save_form') {
            $user_id_by_email = $info_reply[2];
        }
        if (isset($user_reply) && !is_null($user_id_by_email) && strlen($info_reply[0]) > 0) {
            MessageManager::send_message($user_id_by_email, $title, $content);
            $show_message .= MessageManager::return_message($user_id_by_email, 'confirmation');
            $social_right_content .= MessageManager::inbox_display();
            exit;
        } elseif (is_null($user_id_by_email)) {
            $message_box = get_lang('ErrorSendingMessage');
            $show_message .= Display::return_message(api_xml_http_response_encode($message_box), 'error');
            $social_right_content .= MessageManager::inbox_display();
            exit;
        }
    } elseif (trim($info_delete[0]) == 'delete') {
        for ($i = 1; $i <= $count_delete; $i++) {
            MessageManager::delete_message_by_user_receiver(api_get_user_id(), $info_delete[$i]);
        }
        $message_box = get_lang('SelectedMessagesDeleted');
        $show_message .= Display::return_message(api_xml_http_response_encode($message_box));
        $social_right_content .= MessageManager::inbox_display();
        exit;
    }
}

if (isset($_GET['f']) && $_GET['f'] == 'social') {
    $this_section = SECTION_SOCIAL;
    $interbreadcrumb[] = array('url' => api_get_path(WEB_PATH).'main/social/home.php', 'name' => get_lang('SocialNetwork'));
    $interbreadcrumb[] = array('url' => '#', 'name' => get_lang('Inbox'));
} else {
    $this_section = SECTION_MYPROFILE;
    $interbreadcrumb[] = array('url' => api_get_path(WEB_PATH).'main/auth/profile.php', 'name' => get_lang('Profile'));
}

$social_parameter = '';

if (isset($_GET['f']) && $_GET['f'] == 'social' || api_get_setting('allow_social_tool') == 'true') {
    $social_parameter = '?f=social';
} else {
    $actions = null;
    //Comes from normal profile
    if (api_get_setting('allow_social_tool') == 'true' && api_get_setting('allow_message_tool') == 'true') {
        $actions .= '<a href="'.api_get_path(WEB_PATH).'main/social/profile.php">'.Display::return_icon('shared_profile.png', get_lang('ViewSharedProfile')).'</a>';
    }

    if (api_get_setting('allow_message_tool') == 'true') {
        $actions .= '<a href="'.api_get_path(WEB_PATH).'main/messages/new_message.php">'.Display::return_icon('message_new.png', get_lang('ComposeMessage')).'</a>';
        $actions .= '<a href="'.api_get_path(WEB_PATH).'main/messages/inbox.php">'.Display::return_icon('inbox.png', get_lang('Inbox')).'</a>';
        $actions .= '<a href="'.api_get_path(WEB_PATH).'main/messages/outbox.php">'.Display::return_icon('outbox.png', get_lang('Outbox')).'</a>';
    }
}
$user_info    = UserManager::get_user_info_by_id($user_id);
//LEFT CONTENT
if (api_get_setting('allow_social_tool') == 'true') {
    //Block Social Avatar
    $social_avatar_block = '<div class="panel panel-info social-avatar">';
    $social_avatar_block .= SocialManager::show_social_avatar_block('messages');
    $social_avatar_block .= '<div class="lastname">'.$user_info['lastname'].'</div>';
    $social_avatar_block .= '<div class="firstname">'.$user_info['firstname'].'</div>';
    /* $social_avatar_block .= '<div class="username">'.Display::return_icon('user.png','','',ICON_SIZE_TINY).$user_info['username'].'</div>'; */
    $social_avatar_block .= '<div class="email">'.Display::return_icon('instant_message.png').'&nbsp;' .$user_info['email'].'</div>';
    $chat_status = $user_info['extra'];
    if(!empty($chat_status['user_chat_status'])){
        $social_avatar_block.= '<div class="status">'.Display::return_icon('online.png').get_lang('Chat')." (".get_lang('Online').')</div>';
    }else{
        $social_avatar_block.= '<div class="status">'.Display::return_icon('offline.png').get_lang('Chat')." (".get_lang('Offline').')</div>';
    }

    $editProfileUrl = api_get_path(WEB_CODE_PATH) . 'auth/profile.php';

    if (api_get_setting('sso_authentication') === 'true') {
        $subSSOClass = api_get_setting('sso_authentication_subclass');
        $objSSO = null;

        if (!empty($subSSOClass)) {
            require_once api_get_path(SYS_CODE_PATH) . 'auth/sso/sso.' . $subSSOClass . '.class.php';

            $subSSOClass = 'sso' . $subSSOClass;
            $objSSO = new $subSSOClass();
        } else {
            $objSSO = new sso();
        }

        $editProfileUrl = $objSSO->generateProfileEditingURL();
    }
    $social_avatar_block .= '<div class="edit-profile">
                            <a class="btn" href="' . $editProfileUrl . '">' . get_lang('EditProfile') . '</a>
                         </div>';
    $social_avatar_block .= '</div>';

    //Block Social Menu
    $social_menu_block = SocialManager::show_social_menu('messages');
}

//Right content
$social_right_content = null;

if (api_get_setting('allow_social_tool') == 'true') {
    $social_right_content .= '<div class="span9">';
    $social_right_content .= '<div class="actions">';
    $social_right_content .= '<a href="'.api_get_path(WEB_PATH).'main/messages/new_message.php?f=social">'.Display::return_icon('compose_message.png', get_lang('ComposeMessage'), array(), 32).'</a>';
    $social_right_content .= '<a href="'.api_get_path(WEB_PATH).'main/messages/outbox.php?f=social">'.Display::return_icon('outbox.png', get_lang('Outbox'), array(), 32).'</a>';
    $social_right_content .= '</div>';
    $social_right_content .= '</div>';
    $social_right_content .= '<div class="span9">';
}
//MAIN CONTENT

if (!isset($_GET['del_msg'])) {
    $social_right_content .= MessageManager::inbox_display();
} else {
    $num_msg = intval($_POST['total']);
    for ($i = 0; $i < $num_msg; $i++) {
        if ($_POST[$i]) {
            //the user_id was necesarry to delete a message??
            $show_message .= MessageManager::delete_message_by_user_receiver(api_get_user_id(), $_POST['_'.$i]);
        }
    }
    $social_right_content .= MessageManager::inbox_display();
}

if (api_get_setting('allow_social_tool') == 'true') {
    $social_right_content .= '</div>';
}

$tpl = new Template(null);
if (api_get_setting('allow_social_tool') == 'true') {
    $tpl->assign('social_avatar_block', $social_avatar_block);
    $tpl->assign('social_menu_block', $social_menu_block);
    $tpl->assign('social_right_content', $social_right_content);
    $social_layout = $tpl->get_template('social/inbox.tpl');
    $tpl->display($social_layout);
} else {
    $content = $social_right_content;
    $tpl->assign('actions', $actions);
    $tpl->assign('message', $show_message);
    $tpl->assign('content', $content);
    $tpl->display_one_col_template();
}
