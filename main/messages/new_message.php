<?php
/* For licensing terms, see /license.txt */
/**
*	@package chamilo.messages
*/

/**
* This script shows a compose area (wysiwyg editor if supported, otherwise
* a simple textarea) where the user can type a message.
* There are three modes
* - standard: type a message, select a user to send it to, press send
* - reply on message (when pressing reply when viewing a message)
* - send to specific user (when pressing send message in the who is online list)
*/
$cidReset	= true;
require_once '../inc/global.inc.php';

api_block_anonymous_users();

if (api_get_setting('allow_message_tool') !='true') {
    api_not_allowed();
}

$nameTools = api_xml_http_response_encode(get_lang('Messages'));
/*	Constants and variables */

$htmlHeadXtra[]='
<script language="javascript">
function validate(form, list) {
	if(list.selectedIndex<0) {
    	alert("Please select someone to send the message to.")
    	return false
	} else {
    	return true
    }
}

</script>';

$htmlHeadXtra[] = '<script src="'.api_get_path(WEB_LIBRARY_PATH).'javascript/tag/jquery.fcbkcomplete.js" type="text/javascript" language="javascript"></script>';
$htmlHeadXtra[] = '<link  href="'.api_get_path(WEB_LIBRARY_PATH).'javascript/tag/style.css" rel="stylesheet" type="text/css" />';
$htmlHeadXtra[] = '<script type="text/javascript">
$(document).ready(function () {
    $("#users").fcbkcomplete({
        json_url: "'.api_get_path(WEB_AJAX_PATH).'message.ajax.php?a=find_users",
        cache: false,
        filter_case: false,
        filter_hide: true,
        complete_text:"'.get_lang('StartToType').'",
    	firstselected: true,
        //onremove: "testme",
    	onselect:"check_users",
        filter_selected: true,
        newel: true
    });
});

function check_users() {
    //selecting only selected users
    $("#users option:selected").each(function() {
        var user_id = $(this).val();
        if (user_id != "" ) {
            $.ajax({
                url: "'.api_get_path(WEB_AJAX_PATH).'user_manager.ajax.php?a=user_id_exists",
                data: "user_id="+user_id,
                success: function(return_value) {
                    if (return_value == 0 ) {
                        alert("'.get_lang('UserDoesNotExist').'");

                        //Deleting select option tag
                        $("#users option[value="+user_id+"]").remove();

                        //Deleting holder
                        $(".holder li").each(function () {
                            if ($(this).attr("rel") == user_id) {
                                $(this).remove();
                            }
                        });
                    }
                }
            });
        }
    });
}

var counter_image = 1;
/*
function remove_image_form(id_elem1) {
	var elem1 = document.getElementById(id_elem1);
	elem1.parentNode.removeChild(elem1);
}
*/
function add_image_form() {
	// Multiple filepaths for image form
	var filepaths = document.getElementById("filepaths");
	if (document.getElementById("filepath_"+counter_image)) {
		counter_image = counter_image + 1;
	}  else {
		counter_image = counter_image;
	}
	var elem1 = document.createElement("div");
	elem1.setAttribute("id","filepath_"+counter_image);
	filepaths.appendChild(elem1);
	id_elem1 = "filepath_"+counter_image;
	id_elem1 = "\'"+id_elem1+"\'";
	document.getElementById("filepath_"+counter_image).innerHTML = "<input type=\"file\" name=\"attach_"+counter_image+"\" />&nbsp; <br />'.get_lang('Description').'&nbsp;&nbsp;<input type=\"text\" name=\"legend[]\"  /><br /><br />";
	if (filepaths.childNodes.length == 6) {
		var link_attach = document.getElementById("link-more-attach");
		if (link_attach) {
			link_attach.innerHTML="";
		}
	}
}
</script>';
$nameTools = get_lang('ComposeMessage');
/*		FUNCTIONS  */

/**
* Shows the compose area + a list of users to select from.
*/
function show_compose_to_any($user_id) {
	$online_user_list = MessageManager::get_online_user_list($user_id);
	$default['user_list'] = 0;
	$online_user_list=null;
	$html = manage_form($default, $online_user_list);
    return $html;
}

function show_compose_reply_to_message($message_id, $receiver_id)
{
	$table_message = Database::get_main_table(TABLE_MESSAGE);
	$query = "SELECT user_sender_id FROM $table_message
			  WHERE user_receiver_id=".intval($receiver_id)." AND id='".intval($message_id)."';";
	$result = Database::query($query);
	$row = Database::fetch_array($result,'ASSOC');
	if (!isset($row['user_sender_id'])) {
		$html = get_lang('InvalidMessageId');

		return $html;
	}
	$userInfo = api_get_user_info($row['user_sender_id']);
	$default['users'] = array($row['user_sender_id']);
	$html = manage_form($default, null, $userInfo['complete_name']);

    return $html;
}

function show_compose_to_user ($receiver_id) {
	$html = get_lang('To').':&nbsp;<strong>'.GetFullUserName($receiver_id).'</strong>';
	$default['title'] = api_xml_http_response_encode(get_lang('EnterTitle'));
	$default['users'] = array($receiver_id);
	$html .= manage_form($default);
    return $html;
}

function manage_form($default, $select_from_user_list = null, $sent_to = null)
{
	$group_id 		= isset($_REQUEST['group_id']) ? intval($_REQUEST['group_id']) : null;
	$message_id 	= isset($_GET['message_id'])  ?  intval($_GET['message_id']) : null;
	$param_f 		= isset($_GET['f']) && $_GET['f'] == 'social' ? 'social' : null;

	$form = new FormValidator('compose_message', null, api_get_self().'?f='.$param_f, null, array('enctype'=>'multipart/form-data'));
	if (empty($group_id)) {
		if (isset($select_from_user_list)) {
			$form->addText(
                'id_text_name',
                get_lang('SendMessageTo'),
                true,
                array(
                    'id'=>'id_text_name',
                    'onkeyup'=>'send_request_and_search()',
                    'autocomplete'=>'off'
                )
            );
			$form->addRule('id_text_name', get_lang('ThisFieldIsRequired'), 'required');
			$form->addElement('html','<div id="id_div_search" style="padding:0px" class="message-select-box" >&nbsp;</div>');
			$form->addElement('hidden','user_list', 0, array('id'=>'user_list'));
		} else {
            if (!empty($sent_to)) {
                $form->addLabel(get_lang('SendMessageTo'), $sent_to);
            }
			if (empty($default['users'])) {
                //fb select
                $form->addElement('select', 'users', get_lang('SendMessageTo'), array(), array('id' => 'users'));
			} else {
				$form->addElement('hidden','hidden_user',$default['users'][0],array('id'=>'hidden_user'));
			}
		}
	} else {
		$group_info = GroupPortalManager::get_group_data($group_id);

		$form->addElement('label', get_lang('ToGroup'), api_xml_http_response_encode($group_info['name']));
		$form->addElement('hidden','group_id',$group_id);
		$form->addElement('hidden','parent_id',$message_id);
	}

	$form->addText('title', get_lang('Subject'), true);
	$form->addHtmlEditor('content', get_lang('Message'), false, false, array('ToolbarSet' => 'Messages', 'Width' => '100%', 'Height' => '250'));

	if (isset($_GET['re_id'])) {
	    $message_reply_info = MessageManager::get_message_by_id($_GET['re_id']);
        $default['title'] = get_lang('MailSubjectReplyShort')." ".$message_reply_info['title'];
		$form->addElement('hidden','re_id', intval($_GET['re_id']));
		$form->addElement('hidden','save_form','save_form');

		//adding reply mail
		$user_reply_info = UserManager::get_user_info_by_id($message_reply_info['user_sender_id']);
		$default['content'] = '<p><br/></p>'.sprintf(get_lang('XWroteY'), api_get_person_name($user_reply_info['firstname'], $user_reply_info['lastname']), Security::filter_terms($message_reply_info['content']));
	}

	if (empty($group_id)) {

        $form->addElement('label', '', '<div  id="filepaths" class="form-group">
                    <div id="filepath_1">
                    <label>'.get_lang('FilesAttachment').'</label>
                    <input type="file" name="attach_1"/>
                    <label>'.get_lang('Description').'</label>
                    <input id="file-descrtiption" type="text" name="legend[]" class="form-control"/>
                    </div>
                </div>'
		);

		$form->addElement('label', '', '<span id="link-more-attach"><a href="javascript://" onclick="return add_image_form()">'.get_lang('AddOneMoreFile').'</a></span>&nbsp;('.sprintf(get_lang('MaximunFileSizeX'),format_file_size(api_get_setting('message_max_upload_filesize'))).')');
	}

	$form->addButtonSend(get_lang('SendMessage'), 'compose');
	$form->setRequiredNote('<span class="form_required">*</span> <small>'.get_lang('ThisFieldIsRequired').'</small>');

	if (!empty($group_id) && !empty($message_id)) {
		$message_info = MessageManager::get_message_by_id($message_id);
		$default['title'] = get_lang('MailSubjectReplyShort')." ".$message_info['title'];
	}
	$form->setDefaults($default);
    $html = '';
	if ($form->validate()) {
		$check = Security::check_token('post');
		if ($check) {
			$user_list		= $default['users'];
			$file_comments	= $_POST['legend'];
			$title 			= $default['title'];
			$content 		= $default['content'];
			$group_id		= isset($default['group_id']) ? $default['group_id'] : null;
			$parent_id 		= isset($default['parent_id']) ? $default['parent_id'] : null;
			if (is_array($user_list) && count($user_list)> 0) {
				//all is well, send the message
				foreach ($user_list as $user) {
					$res = MessageManager::send_message(
						$user,
						$title,
						$content,
						$_FILES,
						$file_comments,
						$group_id,
						$parent_id
					);
					if ($res) {
						if (is_string($res)) {
							$html .= Display::return_message($res, 'error');
						} else {
							$html .= MessageManager::display_success_message($user);
						}
					}
				}
			} else {
				Display::display_error_message('ErrorSendingMessage');
			}
		}
		Security::clear_token();
	} else {
		$token = Security::get_token();
		$form->addElement('hidden','sec_token');
		$form->setConstants(array('sec_token' => $token));
		$html .= $form->returnForm();
	}
    return $html;
}

$socialToolIsActive = isset($_GET['f']) && $_GET['f'] == 'social';

/* MAIN SECTION */
if ($socialToolIsActive) {
	$this_section = SECTION_SOCIAL;
	$interbreadcrumb[]= array ('url' => api_get_path(WEB_PATH).'main/social/home.php','name' => get_lang('SocialNetwork'));
} else {
	$this_section = SECTION_MYPROFILE;
	$interbreadcrumb[]= array ('url' => api_get_path(WEB_PATH).'main/auth/profile.php','name' => get_lang('Profile'));
}

$group_id = isset($_REQUEST['group_id']) ? intval($_REQUEST['group_id']) : null;
$social_right_content = null;
if ($group_id != 0) {
	$social_right_content .= '<div class=actions>';
	$social_right_content .= '<a href="'.api_get_path(WEB_PATH).'main/social/groups.php?id='.$group_id.'">'.
		Display::return_icon('back.png',api_xml_http_response_encode(get_lang('ComposeMessage'))).'</a>';
	$social_right_content .= '<a href="'.api_get_path(WEB_PATH).'main/messages/new_message.php?group_id='.$group_id.'">'.
		Display::return_icon('message_new.png',api_xml_http_response_encode(get_lang('ComposeMessage'))).'</a>';
	$social_right_content .= '</div>';
} else {
	if ($socialToolIsActive) {
	} else {
		$social_right_content .= '<div class=actions>';
		if (api_get_setting('allow_social_tool') == 'true' && api_get_setting('allow_message_tool') == 'true') {
			$social_right_content .= '<a href="'.api_get_path(WEB_PATH).'main/social/profile.php">'.Display::return_icon('shared_profile.png', get_lang('ViewSharedProfile')).'</a>';
		}
		if (api_get_setting('allow_message_tool') == 'true') {
			$social_right_content .= '<a href="'.api_get_path(WEB_PATH).'main/messages/new_message.php">'.Display::return_icon('message_new.png',get_lang('ComposeMessage')).'</a>';
			$social_right_content .= '<a href="'.api_get_path(WEB_PATH).'main/messages/inbox.php">'.Display::return_icon('inbox.png',get_lang('Inbox')).'</a>';
            $social_right_content .= '<a href="'.api_get_path(WEB_PATH).'main/messages/outbox.php">'.Display::return_icon('outbox.png',get_lang('Outbox')).'</a>';
		}
		$social_right_content .= '</div>';
	}
}

// LEFT COLUMN
$social_left_content = null;
$userInfo    = UserManager::get_user_info_by_id($user_id);
if (api_get_setting('allow_social_tool') == 'true') {
    //Block Social Menu
    $social_menu_block = SocialManager::show_social_menu('messages');
    $social_right_content .= '<div class="row">';
    $social_right_content .= '<div class="col-md-12">';
    $social_right_content .= '<div class="actions">';
    $social_right_content .=  '<a href="'.api_get_path(WEB_PATH).'main/messages/inbox.php?f=social">'.Display::return_icon('back.png', get_lang('Back'), array(), 32).'</a>';
    $social_right_content .=  '</div>';
    $social_right_content .=  '</div>';
    $social_right_content .= '<div class="col-md-12">';
}

// MAIN CONTENT
if (!isset($_POST['compose'])) {
    if(isset($_GET['re_id'])) {
        $social_right_content .= show_compose_reply_to_message($_GET['re_id'], api_get_user_id());
    } elseif(isset($_GET['send_to_user'])) {
        $social_right_content .= show_compose_to_user($_GET['send_to_user']);
    } else {
        $social_right_content .= show_compose_to_any(api_get_user_id());
    }
} else {
    $restrict = false;
    if (isset($_POST['users'])) {
        $restrict = true;
    } elseif (isset($_POST['group_id'])) {
        $restrict = true;
    } elseif(isset($_POST['hidden_user'])) {
        $restrict = true;
    }

    $default['title']	= $_POST['title'];
    $default['content'] = $_POST['content'];

    // comes from a reply button
    if (isset($_GET['re_id'])) {
        $social_right_content .= manage_form($default);
    } else {
        // post
        if ($restrict) {
            if (!isset($_POST['group_id'])) {
                $default['users'] = isset($_POST['users']) ? $_POST['users'] : null;
            } else {
                $default['group_id'] = $_POST['group_id'];
            }
            if (isset($_POST['hidden_user'])) {
                $default['users']	 = array($_POST['hidden_user']);
            }
            $social_right_content .= manage_form($default);
        } else {
            $social_right_content .= Display::return_message(get_lang('ErrorSendingMessage'),'error');
        }
    }
}
if (api_get_setting('allow_social_tool') == 'true') {
    $social_right_content .=  '</div>';
    $social_right_content .=  '</div>';
}

$tpl = new Template(get_lang('ComposeMessage'));
// Block Social Avatar
SocialManager::setSocialUserBlock($tpl, $user_id, 'messages');

if (api_get_setting('allow_social_tool') == 'true') {
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
