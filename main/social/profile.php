<?php
/* For licensing terms, see /license.txt */
/**
* This is the profile social main page
* @author Julio Montoya <gugli100@gmail.com>
* @author Isaac Flores Paz <florespaz_isaac@hotmail.com>
* @package chamilo.social
*/

$language_file = array('userInfo', 'index');
$cidReset = true;
require_once '../inc/global.inc.php';
// Include OpenGraph NOT AVAILABLE
require_once api_get_path(LIBRARY_PATH).'opengraph/OpenGraph.php';

if (api_get_setting('allow_social_tool') !='true') {
    $url = api_get_path(WEB_PATH).'whoisonline.php?id='.intval($_GET['u']);
    header('Location: '.$url);
    exit;
}

$user_id = api_get_user_id();

$friendId = isset($_GET['u']) ? intval($_GET['u']) : api_get_user_id();

$isAdmin = api_is_platform_admin($user_id);

$show_full_profile = true;
//social tab
$this_section = SECTION_SOCIAL;

//Initialize blocks
$social_extra_info_block = null;
$social_course_block = null;
$social_group_info_block = null;
$social_rss_block = null;
$social_skill_block = null;
$social_session_block = null;

if (!empty($_POST['social_wall_new_msg_main']) || !empty($_FILES['picture']['tmp_name'])) {
    $messageId = 0;
    $idMessage = SocialManager::sendWallMessage(api_get_user_id(), $friendId, $_POST['social_wall_new_msg_main'], $messageId, MESSAGE_STATUS_WALL_POST);
    if (!empty($_FILES['picture']['tmp_name']) && $idMessage > 0) {
        $error = SocialManager::sendWallMessageAttachmentFile(api_get_user_id(), $_FILES['picture'], $idMessage, $fileComment = '');
    }

    $url = api_get_path(WEB_CODE_PATH) . 'social/profile.php';
    $url .= empty($_SERVER['QUERY_STRING']) ? '' : '?'.Security::remove_XSS($_SERVER['QUERY_STRING']);
    header('Location: ' . $url);
    exit;

} else if (!empty($_POST['social_wall_new_msg'])  && !empty($_POST['messageId'])) {
    $messageId = intval($_POST['messageId']);
    $res = SocialManager::sendWallMessage(api_get_user_id(), $friendId, $_POST['social_wall_new_msg'], $messageId , MESSAGE_STATUS_WALL);
    $url = api_get_path(WEB_CODE_PATH) . 'social/profile.php';
    $url .= empty($_SERVER['QUERY_STRING']) ? '' : '?'.Security::remove_XSS($_SERVER['QUERY_STRING']);
    header('Location: ' . $url);
    exit;

} else if (isset($_GET['messageId'])) {
    $messageId = Security::remove_XSS($_GET['messageId']);
    $status = SocialManager::deleteMessage($messageId);
    header('Location: ' . api_get_path(WEB_CODE_PATH) . 'social/profile.php');
    exit;

} else if (isset($_GET['u'])) { //I'm your friend? I can see your profile?
    $user_id = intval($_GET['u']);
    if (api_is_anonymous($user_id, true)) {
        api_not_allowed(true);
    }
    // It's me!
    if (api_get_user_id() != $user_id) {
        $user_info    = UserManager::get_user_info_by_id($user_id);
        $show_full_profile = false;
        if (!$user_info) {
            // user does no exist !!
            api_not_allowed(true);
        } else {
            //checking the relationship between me and my friend
            $my_status= SocialManager::get_relation_between_contacts(api_get_user_id(), $user_id);
            if (in_array($my_status, array(
                    USER_RELATION_TYPE_PARENT,
                    USER_RELATION_TYPE_FRIEND,
                    USER_RELATION_TYPE_GOODFRIEND
                ))) {
                $show_full_profile = true;
            }
            //checking the relationship between my friend and me
            $my_friend_status = SocialManager::get_relation_between_contacts($user_id, api_get_user_id());
            if (in_array($my_friend_status, array(
                    USER_RELATION_TYPE_PARENT,
                    USER_RELATION_TYPE_FRIEND,
                    USER_RELATION_TYPE_GOODFRIEND
                ))) {
                $show_full_profile = true;
            } else {
                // im probably not a good friend
                $show_full_profile = false;
            }
        }
    } else {
        $user_info    = UserManager::get_user_info_by_id($user_id);
    }
} else {
    $user_info    = UserManager::get_user_info_by_id($user_id);
}



if ($user_info['user_id'] == api_get_user_id()) {
    $isSelfUser = true;
} else {
    $isSelfUser = false;
}
$userIsOnline = user_is_online($user_id);

$libpath = api_get_path(LIBRARY_PATH);
require_once api_get_path(SYS_CODE_PATH).'calendar/myagenda.inc.php';

require_once $libpath.'magpierss/rss_fetch.inc';
$ajax_url = api_get_path(WEB_AJAX_PATH).'message.ajax.php';
$socialAjaxUrl = api_get_path(WEB_AJAX_PATH).'social.ajax.php';
$javascriptDir = api_get_path(LIBRARY_PATH) . 'javascript/';
api_block_anonymous_users();
$locale = _api_get_locale_from_language();
// Add Jquery scroll pagination plugin
$htmlHeadXtra[] = api_get_js('jscroll/jquery.jscroll.js');
// Add Jquery Time ago plugin
$htmlHeadXtra[] = api_get_js('jquery-timeago/jquery.timeago.js');
$timeAgoLocaleDir = $javascriptDir . 'jquery-timeago/locales/jquery.timeago.' . $locale . '.js';
if (file_exists($timeAgoLocaleDir)) {
    $htmlHeadXtra[] = api_get_js('jquery-timeago/locales/jquery.timeago.' . $locale . '.js');
}

$htmlHeadXtra[] = '<script>

function checkLength( o, n, min, max ) {
    if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        //updateTips( "Length of " + n + " must be between " + min + " and " + max + "." );
        return false;
    } else {
        return true;
    }
}

function send_message_to_user(user_id) {
    var subject = $( "#subject_id" );
    var content = $( "#content_id" );

    $("#send_message_form").show();
    $("#send_message_div").dialog({
        modal:true,
        height:350,
        buttons: {
            "'.  addslashes(get_lang('Send')).'": function() {
                var bValid = true;
                bValid = bValid && checkLength( subject, "subject", 1, 255 );
                bValid = bValid && checkLength( content, "content", 1, 255 );

                if ( bValid ) {
                    var url = "'.$ajax_url.'?a=send_message&user_id="+user_id;
                    var params = $("#send_message_form").serialize();
                    $.ajax({
                        url: url+"&"+params,
                        success:function(data) {
                            $("#message_ajax_reponse").attr("class", "");
                            $("#message_ajax_reponse").html(data);
                            $("#message_ajax_reponse").show();
                            $("#send_message_div").dialog({ buttons:{}});
                            $("#send_message_form").hide();
                            $("#send_message_div").dialog("close");

                            $("#subject_id").val("");
                            $("#content_id").val("");
                        }
                    });
                }
            }
        },
        close: function() {
        }
    });
    $("#send_message_div").dialog("open");
    //prevent the browser to follow the link
}

function send_invitation_to_user(user_id) {
    var content = $( "#content_invitation_id" );
    $("#send_invitation_form").show();
    $("#send_invitation_div").dialog({
        modal:true,
        buttons: {
            "'.  addslashes(get_lang('SendInvitation')).'": function() {
                var bValid = true;
                bValid = bValid && checkLength( content, "content", 1, 255 );
                if (bValid) {
                    var url = "'.$ajax_url.'?a=send_invitation&user_id="+user_id;
                    var params = $("#send_invitation_form").serialize();
                    $.ajax({
                        url: url+"&"+params,
                        success:function(data) {
                            $("#message_ajax_reponse").attr("class", "");
                            $("#message_ajax_reponse").html(data);
                            $("#message_ajax_reponse").show();

                            $("#send_invitation_div").dialog({ buttons:{}});

                            $("#send_invitation_form").hide();
                            $("#send_invitation_div").dialog("close");
                            $("#content_invitation_id").val("");
                        }
                    });
                }
            },
        },
        close: function() {
        }
    });
    $("#send_invitation_div").dialog("open");
    //prevent the browser to follow the link
}

function toogle_course (element_html, course_code){
    elem_id=$(element_html).attr("id");
    id_elem=elem_id.split("_");
    ident="div#div_group_"+id_elem[1];

    id_button="#btn_"+id_elem[1];
    elem_src=$(id_button).attr("src");
    image_show=elem_src.split("/");
    my_image=image_show[2];
    var content = \'social_content\' + id_elem[1];
    if (my_image=="nolines_plus.gif") {
        $(id_button).attr("src","../img/nolines_minus.gif"); var action = "load_course";
        $("div#"+content).show("fast");
    } else {
        $("div#"+content).hide("fast");
        $(id_button).attr("src","../img/nolines_plus.gif"); var action = "unload";
        return false;
    }

     $.ajax({
        contentType: "application/x-www-form-urlencoded",
        beforeSend: function(objeto) {
        $("div#"+content).html("<img src=\'../inc/lib/javascript/indicator.gif\' />"); },
        type: "POST",
        url: "'.api_get_path(WEB_AJAX_PATH).'social.ajax.php?a=toogle_course",
        data: "load_ajax="+id_elem+"&action="+action+"&course_code="+course_code,
        success: function(datos) {
         $("div#"+content).html(datos);
        }
    });
}

$(document).ready(function (){
    $("input#id_btn_send_invitation").bind("click", function(){
        if (confirm("'.get_lang('SendMessageInvitation', '').'")) {
            $("#form_register_friend").submit();
        }
    });

    $("#send_message_div").dialog({
        autoOpen: false,
        modal    : false,
        width    : 550,
        height    : 300
       });

    $("#send_invitation_div").dialog({
        autoOpen: false,
        modal    : false,
        width    : 550,
        height    : 300
       });

    var container = $("#wallMessages");
    container.jscroll({
        loadingHtml: "<div class=\"well_border\">' . get_lang('Loading') . ' </div>",
        nextSelector: "a.nextPage:last",
        contentSelector: "",
        callback: timeAgo
    });
    timeAgo()

});

function timeAgo() {
    $(".timeago").timeago();
}

function display_hide () {
    setTimeout("hide_display_message()",3000);
}

function hide_display_message () {
    $("div#display_response_id").html("");
    try {
        $("#txt_subject_id").val("");
        $("#txt_area_invite").val("");
    }catch(e) {
        $("#txt_area_invite").val("");
    }
}
function register_friend(element_input) {
    if(confirm("'.get_lang('AddToFriends').'")) {
        name_button=$(element_input).attr("id");
        name_div_id="id_"+name_button.substring(13);
        user_id=name_div_id.split("_");
        user_friend_id=user_id[1];
        $.ajax({
            contentType: "application/x-www-form-urlencoded",
            beforeSend: function(objeto) {
                $("div#dpending_"+user_friend_id).html("<img src=\'../inc/lib/javascript/indicator.gif\' />");
            },
            type: "POST",
            url: "'.api_get_path(WEB_AJAX_PATH).'social.ajax.php?a=add_friend",
            data: "friend_id="+user_friend_id+"&is_my_friend="+"friend",
            success: function(datos) {
                $("#dpending_" + user_friend_id).html(datos);
            }
        });
    }
}

</script>';
$nametool = get_lang('ViewMySharedProfile');
if (isset($_GET['shared'])) {
    $my_link='../social/profile.php';
    $link_shared='shared='.Security::remove_XSS($_GET['shared']);
} else {
    $my_link='../social/profile.php';
    $link_shared='';
}
$interbreadcrumb[]= array ('url' =>'home.php','name' => get_lang('SocialNetwork') );

if (isset($_GET['u']) && is_numeric($_GET['u']) && $_GET['u'] != api_get_user_id()) {
    $info_user =   api_get_user_info($_GET['u']);
    $interbreadcrumb[]= array (
        'url' => '#',
        'name' => api_get_person_name($info_user['firstName'], $info_user['lastName']));
    $nametool = '';
}
if (isset($_GET['u'])) {
    $param_user='u='.Security::remove_XSS($_GET['u']);
}else {
    $info_user = api_get_user_info(api_get_user_id());
    $param_user = '';
}

$_SESSION['social_user_id'] = intval($user_id);

/**
 * Display
 */

//Setting some course info
$my_user_id = isset($_GET['u']) ? Security::remove_XSS($_GET['u']) : api_get_user_id();
$personal_course_list = UserManager::get_personal_session_course_list($my_user_id);

$course_list_code = array();
$i=1;

if (is_array($personal_course_list)) {
    foreach ($personal_course_list as $my_course) {
        if ($i<=10) {
            $list[] = SocialManager::get_logged_user_course_html($my_course, $i);
            $course_list_code[] = array('code'=> $my_course['code']);
        } else {
            break;
        }
        $i++;
    }
    //to avoid repeted courses
    $course_list_code = array_unique_dimensional($course_list_code);
}
//Block Avatar Social
$social_avatar_block = '<div class="panel panel-info social-avatar">';
$social_avatar_block .= SocialManager::show_social_avatar_block('shared_profile', null, $user_id);
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

if (api_get_user_id() === $friendId) {
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
}

$social_avatar_block .= '</div>';

//Social Block Menu
$social_menu_block = SocialManager::show_social_menu('shared_profile', null, $user_id, $show_full_profile);

//Setting some session info
$user_info = api_get_user_info($my_user_id);
$sessionList = SessionManager::getSessionsFollowedByUser($my_user_id, $user_info['status']);
$htmlSessionList = null;
foreach ($sessionList as $session) {
    $htmlSessionList .= '<div>';
    $htmlSessionList .= Display::return_icon('session.png', get_lang('Session'));
    $htmlSessionList .= $session['name'];
    $htmlSessionList .= '</div>';
}

// My friends
$friend_html = listMyFriends($user_id, $link_shared ,$show_full_profile);
$social_left_content = '<div class="well sidebar-nav">' .$friend_html . '</div>';

/*
$personal_info = null;
if (!empty($user_info['firstname']) || !empty($user_info['lastname'])) {
    $personal_info .= '<div><h3>'.api_get_person_name($user_info['firstname'], $user_info['lastname']).'</h3></div>';
} else {
    //--- Basic Information
    $personal_info .=  '<div><h3>'.get_lang('Profile').'</h3></div>';
}

if ($show_full_profile) {
    $personal_info .=  '<dl class="dl-horizontal">';
    if ($isAdmin || $isSelfUser) {
        $personal_info .=  '<dt>'.get_lang('UserName').'</dt><dd>'. $user_info['username'].'    </dd>';
    }
    if (!empty($user_info['firstname']) || !empty($user_info['lastname'])) {
        $personal_info .=  '<dt>'.get_lang('Name')
            .'</dt><dd>'. api_get_person_name($user_info['firstname'], $user_info['lastname']).'</dd>';
    }
    if (($isAdmin || $isSelfUser) && !empty($user_info['official_code'])) {
        $personal_info .=  '<dt>'.get_lang('OfficialCode').'</dt><dd>'.$user_info['official_code'].'</dd>';
    }
    if (!empty($user_info['email'])) {
        if (api_get_setting('show_email_addresses')=='true') {
            $personal_info .=  '<dt>'.get_lang('Email').'</dt><dd>'.$user_info['email'].'</dd>';
        }
        if (!empty($user_info['phone'])) {
            $personal_info .=  '<dt>'.get_lang('Phone').'</dt><dd>'. $user_info['phone'].'</dd>';
        }
        $personal_info .=  '</dl>';
    }
} else {
    $personal_info .=  '<dl class="dl-horizontal">';
    if (!empty($user_info['username'])) {
        if ($isAdmin || $isSelfUser) {
            $personal_info .=  '<dt>'.get_lang('UserName').'</dt><dd>'. $user_info['username'].'</dd>';
        }
    }
    $personal_info .=  '</dl>';
}
*/
//Social Block Wall

$wallSocialAddPost = wallSocialAddPost();
$social_wall_block = $wallSocialAddPost;

//Social Post Wall
$post_wall = wallSocialPost($my_user_id,$friendId) ;
$social_post_wall_block  = '<div class="panel panel-info social-post">';
$social_post_wall_block .= '<div class="panel-heading">Mis publicaciones</div>';
$social_post_wall_block .='<div class="panel-body">';
if(empty($post_wall)){
    $social_post_wall_block .= '<p>'.get_lang("NoPosts").'</p>';
}else{
    $social_post_wall_block .= $post_wall;
}
$social_post_wall_block .= '</div></div>';

$socialAutoExtendLink = Display::url(
    get_lang('SeeMore'),
    $socialAjaxUrl . '?u='. $my_user_id . '&a=listWallMessage&start=10&length=5',
    array(
        'class' => 'nextPage next',
    )
);

/* $socialRightInformation =  SocialManager::social_wrapper_div($personal_info, 4); */
$socialRightInformation = null;

//$social_right_content .= SocialManager::social_wrapper_div($wallSocial, 5);
$social_right_content = null;

if ($show_full_profile) {

    // Block Extra information
    $t_uf    = Database :: get_main_table(TABLE_MAIN_USER_FIELD);
    $t_ufo    = Database :: get_main_table(TABLE_MAIN_USER_FIELD_OPTIONS);
    $extra_user_data = UserManager::get_extra_user_data($user_id);
    $extra_information = '';
    if (is_array($extra_user_data) && count($extra_user_data)>0 ) {

        $extra_information .= '<div class="panel panel-info">';
        $extra_information .= '<div class="panel-heading">'.get_lang('ExtraInformation').'</div>';
        $extra_information .='<div class="panel-body">';
        $extra_information_value = '';
        foreach($extra_user_data as $key=>$data) {
            //Avoding parameters
            if (in_array($key, array('mail_notify_invitation','mail_notify_message', 'mail_notify_group_message' ))) {
                continue;
            }
            // get display text, visibility and type from user_field table
            $field_variable = str_replace('extra_','',$key);
            $sql = "SELECT field_display_text,field_visible,field_type,id "
                ." FROM $t_uf WHERE field_variable ='$field_variable'";
            $res_field = Database::query($sql);
            $row_field = Database::fetch_row($res_field);
            $field_display_text = $row_field[0];
            $field_visible = $row_field[1];
            $field_type = $row_field[2];
            $field_id = $row_field[3];
            if ($field_visible == 1) {
                if (is_array($data)) {
                    $extra_information_value .= '<dt>'.ucfirst($field_display_text).'</dt>'
                        .'<dd> '.implode(',',$data).'</dd>';
                } else {
                    if ($field_type == UserManager::USER_FIELD_TYPE_DOUBLE_SELECT) {
                        $id_options = explode(';',$data);
                        $value_options = array();
                        // get option display text from user_field_options table
                        foreach ($id_options as $id_option) {
                            $sql = "SELECT option_display_text FROM $t_ufo WHERE id = '$id_option'";
                            $res_options = Database::query($sql);
                            $row_options = Database::fetch_row($res_options);
                            $value_options[] = $row_options[0];
                        }
                        $extra_information_value .= '<dt>'.ucfirst($field_display_text).':</dt>'
                            .'<dd>'.implode(' ',$value_options).'</dd>';
                    } elseif ($field_type == UserManager::USER_FIELD_TYPE_TAG ) {
                        $user_tags = UserManager::get_user_tags($user_id, $field_id);
                        $tag_tmp = array();
                        foreach ($user_tags as $tags) {
                            $tag_tmp[] = '<a class="label label_tag"'
                                .' href="'.api_get_path(WEB_PATH).'main/social/search.php?q='.$tags['tag'].'">'
                                .$tags['tag']
                                .'</a>';
                        }
                        if (is_array($user_tags) && count($user_tags)>0) {
                            $extra_information_value .= '<dt>'.ucfirst($field_display_text).':</dt>'
                                .'<dd>'.implode('', $tag_tmp).'</dd>';
                        }
                    } elseif ($field_type == UserManager::USER_FIELD_TYPE_SOCIAL_PROFILE) {
                        $icon_path = UserManager::get_favicon_from_url($data);
                        $bottom = '0.2';
                        //quick hack for hi5
                        $domain = parse_url($icon_path, PHP_URL_HOST);
                        if ($domain == 'www.hi5.com' or $domain == 'hi5.com') {
                            $bottom = '-0.8';
                        }
                        $data = '<a href="'.$data.'">'
                            .'<img src="'.$icon_path.'" alt="icon"'
                            .' style="margin-right:0.5em;margin-bottom:'.$bottom.'em;" />'
                            .$field_display_text
                            .'</a>';
                        $extra_information_value .= '<dd>'.$data.'</dd>';
                    } else {
                        if (!empty($data)) {
                            $extra_information_value .= '<dt>'.ucfirst($field_display_text).':</dt><dd>'.$data.'</dd>';
                        }
                    }
                }
            }
        }
        // if there are information to show
        if (!empty($extra_information_value)) {
            $extra_information .= $extra_information_value;
        }
        $extra_information .= '</div></div>'; //social-profile-info
    }

 //If there are information to show Block Extra Information

    if (!empty($extra_information_value)) {
        $social_extra_info_block =  $extra_information;
    }

    // MY GROUPS
    $results = GroupPortalManager::get_groups_by_user($my_user_id, 0);
    $grid_my_groups = array();
    $max_numbers_of_group = 4;

    if (is_array($results) && count($results) > 0) {
        $i = 1;
        foreach ($results as $result) {
            if ($i > $max_numbers_of_group) break;
            $id = $result['id'];
            $url_open  = '<a href="groups.php?id='.$id.'">';
            $url_close = '</a>';
            $icon = '';
            $name = cut($result['name'],CUT_GROUP_NAME,true);
            if ($result['relation_type'] == GROUP_USER_PERMISSION_ADMIN) {
                $icon = Display::return_icon(
                    'social_group_admin.png',
                    get_lang('Admin'),
                    array('style'=>'vertical-align:middle;width:16px;height:16px;')
                );
            } elseif ($result['relation_type'] == GROUP_USER_PERMISSION_MODERATOR) {
                $icon = Display::return_icon(
                    'social_group_moderator.png',
                    get_lang('Moderator'),
                    array('style'=>'vertical-align:middle;width:16px;height:16px;')
                );
            }
            $count_users_group = count(GroupPortalManager::get_all_users_by_group($id));
            if ($count_users_group == 1 ) {
                $count_users_group = $count_users_group.' '.get_lang('Member');
            } else {
                $count_users_group = $count_users_group.' '.get_lang('Members');
            }
            //$picture = GroupPortalManager::get_picture_group($result['id'], $result['picture_uri'],80);
            $item_name = $url_open.$name.$icon.$url_close;

            if ($result['description'] != '') {
                //$item_description = '<div class="box_shared_profile_group_description">'
                //.'<p class="social-groups-text4">'.cut($result['description'],100,true).'</p></div>';
            } else {
                //$item_description = '<div class="box_shared_profile_group_description">'
                //.'<span class="social-groups-text2"></span><p class="social-groups-text4"></p></div>';
            }
            //$result['picture_uri'] = '<div class="box_shared_profile_group_image">'
            //.'<img class="social-groups-image" src="'.$picture['file'].'" hspace="4" height="50"'
            //.' border="2" align="left" width="50" /></div>';
            $item_actions = '';
            //if (api_get_user_id() == $user_id) {
                //$item_actions = '<div class="box_shared_profile_group_actions"><a href="groups.php?id='.$id.'">'
                //.get_lang('SeeMore').$url_close.'</div>';
            //}
            $grid_my_groups[]= array($item_name,$url_open.$result['picture_uri'].$url_close, $item_actions);
            $i++;
        }
    }

    //Block My Groups
    if (count($grid_my_groups) > 0) {
        $my_groups = '';
        $count_groups = 0;
        if (count($results) == 1 ) {
            $count_groups = count($results);
        } else {
            $count_groups = count($results);
        }
        $my_groups .= '<div class="panel panel-info">';
        $my_groups .= '<div class="panel-heading">'.get_lang('MyGroups').' ('.$count_groups.') </div>';

        if ($i > $max_numbers_of_group) {
            if (api_get_user_id() == $user_id) {
                $my_groups .=  '<div class="box_shared_profile_group_actions">'
                    .'<a href="groups.php?#tab_browse-1">'.get_lang('SeeAllMyGroups').'</a></div>';
            } else {
                $my_groups .=  '<div class="box_shared_profile_group_actions">'
                    .'<a href="'.api_get_path(WEB_CODE_PATH).'social/profile_friends_and_groups.inc.php'
                    .'?view=mygroups&height=390&width=610&user_id='.$user_id.'"'
                    .' class="thickbox" title="'.get_lang('SeeAll').'" >'
                    .get_lang('SeeAllMyGroups')
                    .'</a></div>';
            }
        }

        $total = count($grid_my_groups);
        $i = 1;
        foreach($grid_my_groups as $group) {
            $my_groups .= '<div class="panel-body">';
            $my_groups .=  $group[0];
            $my_groups .= '</div>';
            if ($i < $total) {
                $my_groups .=  ', ';
            }
            $i++;
        }
        $my_groups .= '</div>';
        $social_group_info_block =  $my_groups;
    }

    //Block Social Course

    $my_courses = null;
    // COURSES LIST
    if ( is_array($list) ) {
        $my_courses .=  '<div class="panel panel-info">';
        $my_courses .=  '<div class="panel-heading">'.api_ucfirst(get_lang('MyCourses')).'</div>';
        $my_courses .=  '<div class="panel-body">';

        //Courses without sessions
        $i=1;
        foreach ($list as $key => $value) {
            if ( empty($value[2]) ) { //if out of any session
                $my_courses .=  $value[1];
                $my_courses .=  '<div id="social_content'.$i.'"'
                .' class="course_social_content" style="display:none" >s</div>';
                $i++;
            }
        }
        $my_courses .=  '</div></div>';

        $social_course_block .=  $my_courses;
    }

    //Block Social Sessions

    if (count($sessionList) > 0) {
        $sessions  = '<div class="panel panel-info">';
        $sessions .= '<div class="panel-heading">'.api_ucfirst(get_lang('MySessions')).'</div>';
        $sessions .= '<div class="panel-body">'.$htmlSessionList.'</div>';
        $sessions .= '</div>';
        $social_session_block = $sessions;
    }

    // Block Social User Feeds
    $user_feeds = SocialManager::get_user_feeds($user_id);

    if (!empty($user_feeds)) {
        $rss  = '<div class="panel panel-info social-rss">';
        $rss .= '<div class="panel-heading">'.get_lang('RSSFeeds').'</div>';
        $rss .= '<div class="panel-body">'.$user_feeds.'</div></div>';
        $social_rss_block =  $rss;

    }

    //BLock Social Skill
    if (api_get_setting('allow_skills_tool') == 'true') {        
        $skill = new Skill();

        $ranking = $skill->get_user_skill_ranking($my_user_id);
        $skills = $skill->get_user_skills($my_user_id, true);

        $social_skill_block = '<div class="panel panel-info social-skill">';
        $social_skill_block .= '<div class="panel-heading">' . get_lang('Skills');
        $social_skill_block .= '<div class="btn-group pull-right"> <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                            <span class="caret"></span></a>
                             <ul class="dropdown-menu">';
        if (api_is_student() || api_is_student_boss() || api_is_drh()) {
            $social_skill_block .= '<li>' . Display::url(
                    get_lang('SkillsReport'),
                    api_get_path(WEB_CODE_PATH) . 'social/my_skills_report.php'
                ) . '</li>';
        }

        $social_skill_block .= '<li>' . Display::url(
                get_lang('SkillsWheel'),
                api_get_path(WEB_CODE_PATH) . 'social/skills_wheel.php'
            ) . '</li>';

        $social_skill_block .= '<li>' . Display::url(
                sprintf(get_lang('YourSkillRankingX'), $ranking),
                api_get_path(WEB_CODE_PATH) . 'social/skills_ranking.php'
            ) . '</li>';

        $social_skill_block .= '</ul></div></div>';

        $lis = '';
        if (!empty($skills)) {
            foreach ($skills as $skill) {
                $badgeImage = null;

                if (!empty($skill['icon'])) {
                    $badgeImage = Display::img(
                        api_get_path(WEB_DATA_PATH) . $skill['icon'],
                        $skill['name']
                    );
                } else {
                    $badgeImage = Display::return_icon(
                        'award_red.png',
                        $skill['name'],
                        array('title' => $skill['name'])
                    );
                }

                $lis .= Display::tag(
                    'li',
                    $badgeImage .
                    '<div class="badges-name">' . $skill['name'] . '</div>'
                );
            }
            $social_skill_block .= '<div class="panel-body">';
            $social_skill_block .= Display::tag('ul', $lis, array('class' => 'list-badges'));
            $social_skill_block .= '</div>';
        }else{

            $social_skill_block .= '<div class="panel-body">';
            $social_skill_block .= '<p>'. get_lang("WithoutAchievedSkills") . '</p>';
            $social_skill_block .= '<p>' . Display::url(get_lang('SkillsWheel'),api_get_path(WEB_CODE_PATH) . 'social/skills_wheel.php').'</p>';
            $social_skill_block .= '</div>';
        }
        $social_skill_block.='</div>';
    }


    //--Productions
    $production_list =  UserManager::build_production_list($user_id);

    // Images uploaded by course
    $file_list = '';
    if (is_array($course_list_code) && count($course_list_code)>0) {
        foreach ($course_list_code as $course) {
            $file_list.= UserManager::get_user_upload_files_by_course($user_id,$course['code'],$resourcetype='images');
        }
    }

    $count_pending_invitations = 0;
    if (!isset($_GET['u']) || (isset($_GET['u']) && $_GET['u']==api_get_user_id())) {
        $pending_invitations = SocialManager::get_list_invitation_of_friends_by_user_id(api_get_user_id());
        $list_get_path_web     = SocialManager::get_list_web_path_user_invitation_by_user_id(api_get_user_id());
        $count_pending_invitations = count($pending_invitations);
    }

    if (!empty($production_list) || !empty($file_list) || $count_pending_invitations > 0) {

        //Pending invitations
        if (!isset($_GET['u']) || (isset($_GET['u']) && $_GET['u']==api_get_user_id())) {
            if ($count_pending_invitations > 0) {
                $invitations =  '<div><h3>'.get_lang('PendingInvitations').'</h3></div>';
                for ($i=0;$i<$count_pending_invitations;$i++) {
                    $user_invitation_id = $pending_invitations[$i]['user_sender_id'];
                    $invitations .=  '<div id="dpending_'.$user_invitation_id.'" class="friend_invitations">';
                        $invitations .=  '<div style="float:left;width:60px;" >';
                            $invitations .=  '<img style="margin-bottom:5px;"'
                                .' src="'.$list_get_path_web[$i]['dir'].'/'.$list_get_path_web[$i]['file'].'"'
                                .' width="60px">';
                        $invitations .=  '</div>';

                        $invitations .=  '<div style="padding-left:70px;">';
                            $user_invitation_info = api_get_user_info($user_invitation_id);
                            $invitations .=  '<a href="'.api_get_path(WEB_PATH).'main/social/profile.php'
                                .'?u='.$user_invitation_id.'">'
                                .api_get_person_name(
                                    $user_invitation_info['firstname'],
                                    $user_invitation_info['lastname'])
                                .'</a>';
                            $invitations .=  '<br />';
                            $invitations .=  Security::remove_XSS(
                                cut($pending_invitations[$i]['content'], 50),
                                STUDENT,
                                true
                            );
                            $invitations .=  '<br />';
                            $invitations .=  '<a id="btn_accepted_'.$user_invitation_id.'"'
                                .' class="btn" onclick="register_friend(this)" href="javascript:void(0)">'
                                .get_lang('SocialAddToFriends')
                                .'</a>';
                            $invitations .=  '<div id="id_response"></div>';
                        $invitations .=  '</div>';
                    $invitations .=  '</div>';
                }
                $socialRightInformation .=  SocialManager::social_wrapper_div($invitations, 4);
            }
        }

        //--Productions
        $production_list =  UserManager::build_production_list($user_id);

        $product_content  = '';
        if (!empty($production_list)) {
            $product_content .= '<div><h3>'.get_lang('MyProductions').'</h3></div>';
            $product_content .=  $production_list;
            $socialRightInformation .=  SocialManager::social_wrapper_div($product_content, 4);
        }

        $images_uploaded = null;
        // Images uploaded by course
        if (!empty($file_list)) {
            $images_uploaded .=  '<div><h3>'.get_lang('ImagesUploaded').'</h3></div>';
            $images_uploaded .=  '<div class="social-content-information">';
            $images_uploaded .=  $file_list;
            $images_uploaded .=  '</div>';
            $socialRightInformation .=  SocialManager::social_wrapper_div($images_uploaded, 4);
        }
    }

    if (!empty($user_info['competences']) || !empty($user_info['diplomas'])
        || !empty($user_info['openarea']) || !empty($user_info['teach']) ) {

        $more_info .=  '<div><h3>'.get_lang('MoreInformation').'</h3></div>';
        if (!empty($user_info['competences'])) {
            $more_info .=  '<br />';
                $more_info .=  '<div class="social-actions-message"><strong>'.get_lang('MyCompetences').'</strong></div>';
                $more_info .=  '<div class="social-profile-extended">'.$user_info['competences'].'</div>';
            $more_info .=  '<br />';
        }
        if (!empty($user_info['diplomas'])) {
            $more_info .=  '<div class="social-actions-message"><strong>'.get_lang('MyDiplomas').'</strong></div>';
            $more_info .=  '<div class="social-profile-extended">'.$user_info['diplomas'].'</div>';
            $more_info .=  '<br />';
        }
        if (!empty($user_info['openarea'])) {
            $more_info .=  '<div class="social-actions-message"><strong>'.get_lang('MyPersonalOpenArea').'</strong></div>';
            $more_info .=  '<div class="social-profile-extended">'.$user_info['openarea'].'</div>';
            $more_info .=  '<br />';
        }
        if (!empty($user_info['teach'])) {
            $more_info .=  '<div class="social-actions-message"><strong>'.get_lang('MyTeach').'</strong></div>';
            $more_info .=  '<div class="social-profile-extended">'.$user_info['teach'].'</div>';
            $more_info .=  '<br />';
        }
        $socialRightInformation .=  SocialManager::social_wrapper_div($more_info, 4);
    }
}
$social_right_content .= MessageManager::generate_message_form('send_message');
$social_right_content .= MessageManager::generate_invitation_form('send_invitation');


$tpl = new Template(get_lang('Social'));
$tpl->assign('social_avatar_block', $social_avatar_block);
$tpl->assign('social_menu_block', $social_menu_block);
$tpl->assign('social_wall_block', $social_wall_block);
$tpl->assign('social_post_wall_block', $social_post_wall_block);
$tpl->assign('social_extra_info_block', $social_extra_info_block);
$tpl->assign('social_course_block', $social_course_block);
$tpl->assign('social_group_info_block', $social_group_info_block);
$tpl->assign('social_rss_block', $social_rss_block);
$tpl->assign('social_skill_block', $social_skill_block);
$tpl->assign('social_session_block', $social_session_block);
$tpl->assign('socialRightInformation', $socialRightInformation);
$tpl->assign('socialAutoExtendLink', $socialAutoExtendLink);
$social_layout = $tpl->get_template('social/profile.tpl');
$tpl->display($social_layout);

/*
* function list my friends
*/
function listMyFriends($user_id, $link_shared, $show_full_profile)
{
    //SOCIALGOODFRIEND , USER_RELATION_TYPE_FRIEND, USER_RELATION_TYPE_PARENT
    $friends = SocialManager::get_friends($user_id, USER_RELATION_TYPE_FRIEND);

    $friendHtml = '';
    $number_of_images = 30;
    $number_friends = 0;
    $number_friends = count($friends);

    $friendHtml = '<div class="nav-list"><h3>'.get_lang('SocialFriend').'<span>(' . $number_friends . ')</span></h3></div>';

    if ($number_friends != 0) {
        if ($number_friends > $number_of_images) {
            if (api_get_user_id() == $user_id) {
                $friendHtml.= ' : <span><a href="friends.php">'.get_lang('SeeAll').'</a></span>';
            } else {
                $friendHtml.= ' : <span>'
                    .'<a href="'.api_get_path(WEB_CODE_PATH).'social/profile_friends_and_groups.inc.php'
                    .'?view=friends&height=390&width=610&user_id='.$user_id.'"'
                    .'class="thickbox" title="'.get_lang('SeeAll').'" >'.get_lang('SeeAll').'</a></span>';
            }
        }

        $friendHtml.= '<ul class="nav nav-list">';
        $j = 1;
        for ($k=0; $k < $number_friends; $k++) {
            if ($j > $number_of_images) break;

            if (isset($friends[$k])) {
                $friend = $friends[$k];
                $name_user    = api_get_person_name($friend['firstName'], $friend['lastName']);
                $user_info_friend = api_get_user_info($friend['friend_user_id'], true);

                if ($user_info_friend['user_is_online']) {
                    $statusIcon = Display::span('', array('class' => 'online_user_in_text'));
                } else {
                    $statusIcon = Display::span('', array('class' => 'offline_user_in_text'));
                }

                $friendHtml.= '<li class="">';
                // the height = 92 must be the sqme in the image_friend_network span style in default.css
                $friends_profile = SocialManager::get_picture_user($friend['friend_user_id'], $friend['image'], 20, USER_IMAGE_SIZE_SMALL);
                $friendHtml.= '<img src="'.$friends_profile['file'].'" id="imgfriend_'.$friend['friend_user_id'].'" title="'.$name_user.'"/>';
                $link_shared = (empty($link_shared)) ? '' : '&'.$link_shared;
                $friendHtml.= $statusIcon .'<a href="profile.php?' .'u=' . $friend['friend_user_id'] . $link_shared . '">' . $name_user .'</a>';
                $friendHtml.= '</li>';
            }
            $j++;
        }
        $friendHtml.='</ul>';
    } else {
        $friendHtml.= '<div class="">'.get_lang('NoFriendsInYourContactList').'<br />'
            .'<a class="btn" href="'.api_get_path(WEB_PATH).'whoisonline.php">'. get_lang('TryAndFindSomeFriends').'</a></div>';
    }

    return $friendHtml;
}


function wallSocialAddPost()
{
    $html  = '<div class="panel panel-info social-wall">';
    $html .= '<div class="panel-heading">' . get_lang('SocialWall') . '</div>';
    $html .= '<div class="panel-body">';
    $html .=
        '<form name="social_wall_main" method="POST" enctype="multipart/form-data">
            <label for="social_wall_new_msg_main" class="hide">' . get_lang('SocialWallWhatAreYouThinkingAbout') . '</label>
        <textarea name="social_wall_new_msg_main" rows="2" cols="80" style="width: 98%" placeholder="'.get_lang('SocialWallWhatAreYouThinkingAbout').'"></textarea>
        <br />
        <input class="" name="picture" type="file" accept="image/*" style="width:80%;">
        <input type="submit" name="social_wall_new_msg_main_submit" value="'.get_lang('Post').'" class="pull-right btn btn-success" />
    </form>';
    $html.= '</div></div>';

    return $html;
}

function wallSocialPost($userId, $friendId)
{
    $array = SocialManager::getWallMessagesPostHTML($userId, $friendId);
    $html = '';

    for($i = 0; $i < count($array); $i++) {
        $post = $array[$i]['html'];
        $comment = SocialManager::getWallMessagesHTML($userId, $friendId, $array[$i]['id']);

        $html .= $post.$comment;
    }

    return $html;
}
