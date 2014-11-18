<?php
/* For licensing terms, see /license.txt */

/**
 *	Chat frame that shows the message list
 *
 *	@author Olivier Brouckaert
 *	@package chamilo.chat
 */

define('FRAME', 'chat');

$language_file = array('chat');
require_once '../inc/global.inc.php';
require_once api_get_path(LIBRARY_PATH).'document.lib.php';
require_once api_get_path(LIBRARY_PATH).'fileUpload.lib.php';

$course = $_GET['cidReq'];
$session_id = api_get_session_id();
$group_id 	= api_get_group_id();
$userId = api_get_user_id();
$_course = api_get_course_info();
$time = api_get_utc_datetime();

// if we have the session set up
if (!empty($course)) {
    $reset = isset($_GET['reset']) ? (bool)$_GET['reset'] : null;
    $tbl_user = Database::get_main_table(TABLE_MAIN_USER);
    $query = "SELECT username FROM $tbl_user WHERE user_id='".$userId."'";
    $result = Database::query($query);

    list($pseudo_user) = Database::fetch_row($result);

    $isAllowed = !(empty($pseudo_user) || !$_cid);
    $isMaster = (bool)api_is_course_admin();

    $date_now = date('Y-m-d');
    $basepath_chat = '';
    $document_path = api_get_path(SYS_COURSE_PATH).$_course['path'].'/document';
    if (!empty($group_id)) {
        $group_info = GroupManager :: get_group_properties($group_id);
        $basepath_chat = $group_info['directory'].'/chat_files';
    } else {
        $basepath_chat = '/chat_files';
    }
    $chat_path = $document_path.$basepath_chat.'/';
    $TABLEITEMPROPERTY = Database::get_course_table(TABLE_ITEM_PROPERTY);
    $course_id = api_get_course_int_id();

    if (!is_dir($chat_path)) {
        if (is_file($chat_path)) {
            @unlink($chat_path);
        }

        if (!api_is_anonymous()) {
            @mkdir($chat_path, api_get_permissions_for_new_directories());
            // Save chat files document for group into item property
            if (!empty($group_id)) {
                $doc_id = add_document($_course, $basepath_chat, 'folder', 0, 'chat_files');
                $sql = "INSERT INTO $TABLEITEMPROPERTY (c_id, tool,insert_user_id,insert_date,lastedit_date,ref,lastedit_type,lastedit_user_id,to_group_id,to_user_id,visibility)
                        VALUES ($course_id, 'document',1,NOW(),NOW(),$doc_id,'FolderCreated',1,$group_id,NULL,0)";
                Database::query($sql);
            }
        }
    }

    $filename_chat = '';
    if (!empty($group_id)) {
        $filename_chat = 'messages-'.$date_now.'_gid-'.$group_id.'.log.html';
    } else if (!empty($session_id)) {
        $filename_chat = 'messages-'.$date_now.'_sid-'.$session_id.'.log.html';
    } else {
        $filename_chat = 'messages-'.$date_now.'.log.html';
    }

	if (!file_exists($chat_path.$filename_chat)) {
		@fclose(fopen($chat_path.$filename_chat, 'w'));
		if (!api_is_anonymous()) {
			$doc_id = add_document($_course, $basepath_chat.'/'.$filename_chat, 'file', 0, $filename_chat);
			api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'DocumentAdded', $userId, $group_id, null, null, null, $session_id);
			api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'invisible', $userId, $group_id, null, null, null, $session_id);
			item_property_update_on_folder($_course, $basepath_chat, $userId);
		}
	}

	$basename_chat = '';
	if (!empty($group_id)) {
		$basename_chat = 'messages-'.$date_now.'_gid-'.$group_id;
	} else if (!empty($session_id)) {
		$basename_chat = 'messages-'.$date_now.'_sid-'.$session_id;
	} else {
		$basename_chat = 'messages-'.$date_now;
	}

	if ($reset && $isMaster) {

		$i = 1;
		while (file_exists($chat_path.$basename_chat.'-'.$i.'.log.html')) {
			$i++;
		}

		@rename($chat_path.$basename_chat.'.log.html', $chat_path.$basename_chat.'-'.$i.'.log.html');
		@fclose(fopen($chat_path.$basename_chat.'.log.html', 'w'));
		$doc_id = add_document($_course, $basepath_chat.'/'.$basename_chat.'-'.$i.'.log.html', 'file', filesize($chat_path.$basename_chat.'-'.$i.'.log.html'), $basename_chat.'-'.$i.'.log.html');

		api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'DocumentAdded', $userId, $group_id, null, null, null, $session_id);
		api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'invisible', $userId, $group_id, null, null, null, $session_id);
		item_property_update_on_folder($_course, $basepath_chat, $userId);

		$doc_id = DocumentManager::get_document_id($_course, $basepath_chat.'/'.$basename_chat.'.log.html');

		update_existing_document($_course, $doc_id, 0);
	}

	$remove = 0;
	$content = array();
	if (file_exists($chat_path.$basename_chat.'.log.html')) {
		$content = file($chat_path.$basename_chat.'.log.html');
		$nbr_lines = sizeof($content);
		$remove = $nbr_lines - 100;
	}

	if ($remove < 0) {
		$remove = 0;
	}

	array_splice($content, 0, $remove);
	require 'header_frame.inc.php';

	if (isset($_GET['origin']) && $_GET['origin'] == 'whoisonline') {
	    //the caller
		$content[0] = get_lang('CallSent').'<br />'.$content[0];
	}
	if (isset($_GET['origin']) && $_GET['origin'] == 'whoisonlinejoin') {
	    //the joiner (we have to delete the chat request to him when he joins the chat)
		$track_user_table = Database::get_main_table(TABLE_MAIN_USER);
		$sql = "UPDATE $track_user_table SET
                    chatcall_user_id = '',
                    chatcall_date = '',
                    chatcall_text=''
		        WHERE (user_id = ".$userId.")";
		$result = Database::query($sql);
	}

	echo '<div id="content-chat markdown-body">';
	foreach ($content as & $this_line) {
		//echo strip_tags(api_html_entity_decode($this_line), '<div> <br> <span> <b> <i> <img> <font>');
        echo $this_line;
	}
	echo '</div>';
	echo '<a name="bottom" style="text-decoration:none;">&nbsp;</a>';
	if ($isMaster || $is_courseCoach) {
		$rand = mt_rand(1, 1000);
		echo '<div id="clear-chat">';
		echo '<a class="btn btn-danger btn-small " href="'.api_get_self().'?rand='.$rand.'&reset=1&'.api_get_cidreq().'#bottom" onclick="javascript: if(!confirm(\''.addslashes(api_htmlentities(get_lang('ConfirmReset'), ENT_QUOTES)).'\')) return false;">'.
            get_lang('ClearList').
            '</a>';
		echo '</div>';
	}
} else {
	echo '</div>';
	require 'header_frame.inc.php';
	$message = get_lang('CloseOtherSession');
	Display :: display_error_message($message);
}
require 'footer_frame.inc.php';
