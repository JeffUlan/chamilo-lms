<?php // $Id: new_message.php 9929 2006-11-09 14:02:43Z evie_em $
/*
==============================================================================
	Dokeos - elearning and course management software
	
	Copyright (c) Facultad de Matematicas, UADY (México)
	Copyright (c) Evie, Free University of Brussels (Belgium)
	
	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	See the GNU General Public License for more details.
	
	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
/**
* This script shows a compose area (wysiwyg editor if supported, otherwise
* a simple textarea) where the user can type a message.
* There are three modes
* - standard: type a message, select a user to send it to, press send
* - reply on message (when pressing reply when viewing a message)
* - send to specific user (when pressing send message in the who is online list)
*/
/*
==============================================================================
		INIT SECTION
==============================================================================
*/ 
$langFile= "messages";
$cidReset=true;
include_once('../../main/inc/global.inc.php');
echo $_SESSION['prueba'];
api_block_anonymous_users();
require_once('./functions.inc.php');
require_once(api_get_path(LIBRARY_PATH).'/text.lib.php');
require_once(api_get_path(LIBRARY_PATH).'/formvalidator/FormValidator.class.php');

/*
-----------------------------------------------------------
	Constants and variables
-----------------------------------------------------------
*/
$htmlHeadXtra[]='
<script language="javascript">
function validate(form,list)
{
	if(list.selectedIndex<0)
	{
    	alert("Please select someone to send the message to.")
    	return false
	}
	else
    	return true
}

</script>';
$nameTools = get_lang('ComposeMessage');

/*
==============================================================================
		FUNCTIONS
==============================================================================
*/ 

/**
* Shows the compose area + a list of users to select from.
*/
function show_compose_to_any($user_id)
{
	$online_user_list = get_online_user_list($user_id,$_name,$width,$size);
	
	$default['title'] = "Please enter a title";
	$default['user_list'] = $receiver_id;
	
	$form = new FormValidator('compose_message');
	$form->addElement('select', 'user_list', get_lang('SendMessageTo'), $online_user_list);
	$form->add_textfield('title', get_lang('MessageTitle'));
	$form->add_html_editor('content', get_lang('MessageContent'));
	$form->addElement('submit', 'compose', get_lang('Ok'));
	$form->setDefaults($default);
	$form->display();
}

function show_compose_reply_to_message($message_id, $receiver_id)
{
	$query = "SELECT * FROM `".MESSAGES_DATABASE."` WHERE id_receiver=".$receiver_id." AND id='".$message_id."';";
	$result = api_sql_query($query,__FILE__,__LINE__);
	$row = mysql_fetch_array($result);
	
	if(!isset($row[1]))
	{
		echo get_lang('InvalidMessageId');
		die();
	}
		
	echo get_lang('To').':&nbsp;<strong>'.	GetFullUserName($row[1],$mysqlMainDb).'</strong>';
	
	$default['title'] = "Please enter a title";
	$default['user_list'] = $row[1];
	
	$form = new FormValidator('compose_message');
	$form->add_textfield('title', get_lang('MessageTitle'));
	$form->add_html_editor('content', get_lang('MessageContent'));
	$form->addElement('hidden', 'user_list');
	$form->addElement('submit', 'compose', get_lang('Ok'));
	$form->setDefaults($default);
	$form->display();
}

function show_compose_to_user($receiver_id)
{
	echo get_lang('To').':&nbsp;<strong>'.	GetFullUserName($receiver_id,$mysqlMainDb).'</strong>';
	
	$default['title'] = "Please enter a title";
	$default['user_list'] = $receiver_id;
	
	$form = new FormValidator('compose_message');
	$form->add_textfield('title', get_lang('MessageTitle'));
	$form->add_html_editor('content', get_lang('MessageContent'));
	$form->addElement('hidden', 'user_list');
	$form->addElement('submit', 'compose', get_lang('Ok'));
	$form->setDefaults($default);
	$form->display();
}

/*
==============================================================================
		MAIN SECTION
==============================================================================
*/ 
$interbreadcrumb[] = array ("url" => 'inbox.php', "name" => get_lang('Messages'));
Display::display_header($nameTools, get_lang("ComposeMessage"));
api_display_tool_title($nameTools);
if(!isset($_POST['compose']))
{
	if(isset($_GET['re_id']))
	{
		$message_id = $_GET['re_id'];
		$receiver_id = $_SESSION['_uid'];
		show_compose_reply_to_message($message_id, $receiver_id);
	}
	else if(isset($_GET['send_to_user']))
	{
		show_compose_to_user($_GET['send_to_user']);
	}
	else
	{
		show_compose_to_any($_uid);
  	}
}
else
{
	if(isset($_SESSION['_uid']) && isset($_POST['user_list']) && isset($_POST['content']))
	{
		$id_tmp = $_SESSION['_uid'].$_POST['user_list'].date('d-D-w-m-Y-H-s').
					microtime().rand();
		$id_msg = md5($id_tmp);
		$query = "INSERT INTO `".MESSAGES_DATABASE."` ( `id`, `id_sender`, `id_receiver`, `status`, `date`, `title`, `content` ) ".
				 " VALUES (".
		 		 "' ".$id_msg ."' , '".$_SESSION['_uid']."', '".$_POST['user_list']."', '1', '".date('Y-m-d H:i:s')."','".$_POST['title']."','".$_POST['content']."'".
		 		 ");";
		@api_sql_query($query,__FILE__,__LINE__);
		display_success_message($_POST['user_list']);
	}
	else
		Display::display_error_message(get_lang('ErrorSendingMessage'));
}


/*
==============================================================================
		FOOTER 
==============================================================================
*/ 
Display::display_footer();
?>