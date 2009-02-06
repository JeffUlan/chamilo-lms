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
	
	if (isset ($_REQUEST['action'])) {
		switch ($_REQUEST['action']) {
			case 'delete' :
			$number_of_selected_messages = count($_POST['id']);
			foreach ($_POST['id'] as $index => $message_id) {
				MessageManager::delete_message_by_user_receiver(api_get_user_id(), $message_id);	
			}
			Display::display_normal_message(get_lang('SelectedMessagesDeleted'));
			break;
			case 'deleteone' :
			MessageManager::delete_message_by_user_receiver(api_get_user_id(), $_GET['id']);
			Display::display_confirmation_message(get_lang('MessageDeleted'));
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
		$param=true;
	} else {
		$title= get_lang('Title');
		$action=get_lang('Actions');
		$param=true;		
	}
	$table->set_header(1, get_lang('From'),$param);
	$table->set_header(2,$title,$param);
	$table->set_header(3, get_lang('Date'),$param);
	$table->set_header(4,$action,$param);
	$table->set_form_actions(array ('delete' => get_lang('DeleteSelectedMessages')));
	$table->display();
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

if (isset ($_REQUEST['action'])) {
	switch ($_REQUEST['action']) {
		case 'delete' :
		$number_of_selected_messages = count($_POST['id']);
		if ($number_of_selected_messages!=0) {
			foreach ($_POST['id'] as $index => $message_id) {
				MessageManager::delete_message_by_user_receiver(api_get_user_id(), $message_id);	
			}
		}
		Display::display_normal_message(get_lang('SelectedMessagesDeleted'));
		break;
		case 'deleteone' :
		MessageManager::delete_message_by_user_receiver(api_get_user_id(), $_GET['id']);
		Display::display_confirmation_message(get_lang('MessageDeleted'));
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
$table->set_header(1, get_lang('From'));
$table->set_header(2, $title);
$table->set_header(3, get_lang('Date'));
$table->set_header(4,$action, false);
$table->set_form_actions(array ('delete' => get_lang('DeleteSelectedMessages')));
	$table->display();
}
function get_number_of_messages_send_mask() {
	return MessageManager::get_number_of_messages_sent();
}
function get_message_data_send_mask($from, $number_of_items, $column, $direction) {
	return MessageManager::get_message_data_sent($from, $number_of_items, $column, $direction);
}
?>