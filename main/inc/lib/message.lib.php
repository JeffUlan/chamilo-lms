<?php
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2008 Dokeos SPRL
	Copyright (c) Julio Montoya <gugli100@gmail.com>
	Copyright (c) Isaac Flores <florespaz_isaac@hotmail.com>
	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, rue du Corbeau, 108, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
	
==============================================================================
*/

include_once(api_get_path(LIBRARY_PATH).'/online.inc.php');
require_once '../messages/message.class.php';
function inbox_display() {
	$table_message = Database::get_main_table(TABLE_MESSAGE); 
	$request=api_is_xml_http_request();
	if ($_SESSION['social_exist']===true) {
		$redirect="#remote-tab-2";	
		if (api_get_setting('allow_social_tool')=='true' && api_get_setting('allow_message_tool')=='true') {
			$success= get_lang('SelectedMessagesDeleted').
			"&nbsp
			<br><a href=\"".
			"../social/index.php$redirect\">".
			get_lang('BackToInbox').
			"</a>";
		}else {
			$success= get_lang('SelectedMessagesDeleted').
			"&nbsp
			<br><a href=\"".
			"../social/index.php$redirect\">".
			get_lang('BackToInbox').
			"</a>";				
		}
			
	} else {
		$success= get_lang('SelectedMessagesDeleted').
			"&nbsp
			</b>".
			"<br><a href=\"".
			"inbox.php\">".
			get_lang('BackToOutbox').
			"</a>";
	}
	
	if (isset ($_REQUEST['action'])) {
		switch ($_REQUEST['action']) {
			case 'delete' :
			$number_of_selected_messages = count($_POST['id']);
			foreach ($_POST['id'] as $index => $message_id) {
				MessageManager::delete_message_by_user_receiver(api_get_user_id(), $message_id);	
			}
			Display::display_normal_message($success,false);
			break;
			case 'deleteone' :
			MessageManager::delete_message_by_user_receiver(api_get_user_id(), $_GET['id']);
			Display::display_confirmation_message($success,false);
			echo '<br/>';	
			break;
		}
	}
	
	// display sortable table with messages of the current user
	$table = new SortableTable('messages', 'get_number_of_messages_mask', 'get_message_data_mask', 1);
	$table->set_header(0, '', false);
	if ($request===true) {
		$title= utf8_encode(get_lang('Title'));
		$action=utf8_encode(get_lang('Actions'));
	} else {
		$title= get_lang('Title');
		$action=get_lang('Actions');		
	}
	$table->set_header(1, get_lang('From'),false);
	$table->set_header(2,$title,false);
	$table->set_header(3, get_lang('Date'),false);
	$table->set_header(4,$action,false);
echo '<div id="div_content_table_data">';
	if ($request===true) {
		echo '<form name="form_send" id="form_send" action="" method="post">';
		echo '<input type="hidden" name="action" value="delete" />';
		$table->display();
		echo '</form>';
		if (get_number_of_messages_mask() > 0) {
			echo '<a href="javascript:void(0)" onclick="selectall_cheks()">'.get_lang('SelectAll').'</a>&nbsp;&nbsp;&nbsp;';
			echo '<a href="javascript:void(0)" onclick="unselectall_cheks()">'.get_lang('UnSelectAll').'</a>&nbsp;&nbsp;&nbsp;';
			echo '<input name="delete" type="button" value="'.get_lang('DeleteSelectedMessages').'" onclick="submit_form(\'inbox\')"/>';
		}
	} else {
		$table->set_form_actions(array ('delete' => get_lang('DeleteSelectedMessages')));
		$table->display();
	}
echo '</div>';
}
function get_number_of_messages_mask() {
	return MessageManager::get_number_of_messages();
}
function get_message_data_mask($from, $number_of_items, $column, $direction) {
	return MessageManager::get_message_data($from, $number_of_items, $column, $direction);
}
function outbox_display() {
	$table_message = Database::get_main_table(TABLE_MESSAGE); 
	$request=api_is_xml_http_request();
	if ($_SESSION['social_exist']===true) {
		$redirect="#remote-tab-3";	
		if (api_get_setting('allow_social_tool')=='true' && api_get_setting('allow_message_tool')=='true') {
			$success= get_lang('SelectedMessagesDeleted').
			"&nbsp
			<br><a href=\"".
			"../social/index.php$redirect\">".
			get_lang('BackToOutbox').
			"</a>";
		}else {
			$success= get_lang('SelectedMessagesDeleted').
			"&nbsp
			<br><a href=\"".
			"../social/index.php$redirect\">".
			get_lang('BackToOutbox').
			"</a>";				
		}
			
	} else {
		$success= get_lang('SelectedMessagesDeleted').
			"&nbsp
			</b>".
			"<br><a href=\"".
			"outbox.php\">".
			get_lang('BackToOutbox').
			"</a>";
	}
if (isset ($_REQUEST['action'])) {
	switch ($_REQUEST['action']) {
		case 'delete' :
		$number_of_selected_messages = count($_POST['id']);
		if ($number_of_selected_messages!=0) {
			foreach ($_POST['id'] as $index => $message_id) {
				MessageManager::delete_message_by_user_receiver(api_get_user_id(), $message_id);	
			}
		}		
		Display::display_normal_message($success,false);
		break;
		case 'deleteone' :
		MessageManager::delete_message_by_user_receiver(api_get_user_id(), $_GET['id']);
		Display::display_confirmation_message($success,false);
		echo '<br/>';	
		break;
	}
}

// display sortable table with messages of the current user
$table = new SortableTable('messages', 'get_number_of_messages_send_mask', 'get_message_data_send_mask', 1);
if ($request===true) {
	$title= utf8_encode(get_lang('Title'));
	$action=utf8_encode(get_lang('Actions'));
} else {
	$title= get_lang('Title');
	$action=get_lang('Actions');		
}
$table->set_header(0, '', false);
$table->set_header(1, get_lang('From'),false);
$table->set_header(2, $title,false);
$table->set_header(3, get_lang('Date'),false);
$table->set_header(4,$action, false);
echo '<div id="div_content_table_data_sent">';
	if ($request===true) {
		echo '<form name="form_send_out" id="form_send_out" action="" method="post">';
		echo '<input type="hidden" name="action" value="delete" />';
		$table->display();
		echo '</form>';
		if (get_number_of_messages_send_mask() > 0) {
			echo '<a href="javascript:void(0)" onclick="selectall_cheks()">'.get_lang('SelectAll').'</a>&nbsp;&nbsp;&nbsp;';
			echo '<a href="javascript:void(0)" onclick="unselectall_cheks()">'.get_lang('UnSelectAll').'</a>&nbsp;&nbsp;&nbsp;';
			echo '<input name="delete" type="button" value="'.get_lang('DeleteSelectedMessages').'" onclick="submit_form(\'outbox\')"/>';
		}
	} else {
		$table->set_form_actions(array ('delete' => get_lang('DeleteSelectedMessages')));
		$table->display();
	}
echo '</div>';
}
function get_number_of_messages_send_mask() {
	return MessageManager::get_number_of_messages_sent();
}
function get_message_data_send_mask($from, $number_of_items, $column, $direction) {
	return MessageManager::get_message_data_sent($from, $number_of_items, $column, $direction);
}
?>