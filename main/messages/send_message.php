<?php
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2009 Dokeos SPRL
	Copyright (c) 2009 Julio Montoya Armas <gugli100@gmail.com>
	Copyright (c) Facultad de Matematicas, UADY (México)
	Copyright (c) Evie, Free University of Brussels (Belgium)	
	Copyright (c) 2009 Isaac Flores Paz <isaac.flores@dokeos.com>	

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
$language_file = array('registration','messages','userInfo','admin');
$cidReset=true;
require_once '../inc/global.inc.php';
require_once '../messages/message.class.php';
require_once api_get_path(LIBRARY_PATH).'usermanager.lib.php';
require_once api_get_path(LIBRARY_PATH).'message.lib.php';
require_once api_get_path(LIBRARY_PATH).'social.lib.php';

if (api_is_anonymous()) {
	api_not_allowed();
}

$user_id=intval($_POST['user_id']);
$panel_id=intval($_POST['panel_id']);
$content_message=Security::remove_XSS($_POST['txt_content'],COURSEMANAGERLOWSECURITY); //check this is filtered on output
$subject_message=Security::remove_XSS($_POST['txt_subject']); //check this is filtered on output
$user_info=array();
$user_info=api_get_user_info($user_id);
if ($panel_id==2) {
?>
    <td height="20"><?php //echo api_xml_http_response_encode(get_lang('Info')).' :'; ?></td>
    <td height="20"><?php //echo api_xml_http_response_encode(get_lang('SocialUserInformationAttach')); ?></td>
    <td height="20"><?php echo api_xml_http_response_encode(get_lang('WriteAMessage'));  ?> :<br/><textarea id="txt_area_invite" rows="3" cols="25"></textarea></td>
    <td height="20"><input type="button" value="<?php echo api_xml_http_response_encode(get_lang('SendInviteMessage')); ?>" onclick="action_database_panel('4','<?php echo $user_id;?>')" /></td>
<?php
} 
if ($panel_id==1) {
?>
    <td height="20"><?php echo api_xml_http_response_encode(get_lang('To')); ?> &nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;<?php echo api_xml_http_response_encode($user_info['firstName'].' '.$user_info['lastName']); ?></td>
    <td height="20"><?php echo api_xml_http_response_encode(get_lang('Subject')); ?> :<br/><input id="txt_subject_id" type="text" style="width:200px;"></td>
    <td height="20"><?php echo api_xml_http_response_encode(get_lang('Message')); ?> :<br/><textarea id="txt_area_invite" rows="3" cols="25"></textarea></td>
    <td height="20"><input type="button" value="<?php echo api_xml_http_response_encode(get_lang('NewMessage')); ?>" onclick="hide_display_message()" />&nbsp;&nbsp;&nbsp; <input type="button" value="<?php echo api_xml_http_response_encode(get_lang('SendMessage')); ?>" onclick="action_database_panel('5','<?php echo $user_id;?>')" /></td>
<?php
} 
if ($panel_id==3) {
?>
<dl>
	<dd><a href="javascript:void(0)" onclick="change_panel('2','<?php echo $user_id; ?>')"><?php echo api_xml_http_response_encode(get_lang('SendInviteMessage')); ?></a></dd>
	<dd><a href="javascript:void(0)" onclick="change_panel('1','<?php echo $user_id; ?>')"><?php echo api_xml_http_response_encode(get_lang('SendMessage'));?></a></dd>
</dl>
<?php
//	<dd><a href="main/social/index.php#remote-tab-5"> echo api_xml_http_response_encode(get_lang('SocialSeeContacts'));</a></dd>
}

if ($panel_id==4) {
	if ($subject_message=='clear') {
		$subject_message=null;
	}
	UserFriend::send_invitation_friend_user($user_id,$subject_message,$content_message);
} elseif ($panel_id==5) {
	UserFriend::send_invitation_friend_user($user_id,$subject_message,$content_message);	
}
?>
