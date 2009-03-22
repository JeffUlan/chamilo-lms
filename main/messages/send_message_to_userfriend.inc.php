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
include_once ('../inc/global.inc.php');
require_once '../messages/message.class.php';
include_once(api_get_path(LIBRARY_PATH).'/usermanager.lib.php');
include_once(api_get_path(LIBRARY_PATH).'/message.lib.php');
include_once(api_get_path(LIBRARY_PATH).'/social.lib.php');
api_block_anonymous_users();
if (api_get_setting('allow_message_tool')<>'true' && api_get_setting('allow_social_tool')<>'true'){
	api_not_allowed();
}
?>
<?php
    if ( isset($_REQUEST['user_friend']) ) {
		$info_user_friend=array();
		$info_path_friend=array();
     	$userfriend_id=Security::remove_XSS($_REQUEST['user_friend']);
     	$panel=Security::remove_XSS($_REQUEST['view_panel']);
     	$info_user_friend=api_get_user_info($userfriend_id);
     	$info_path_friend=UserManager::get_user_picture_path_by_id($userfriend_id,'web',false,true);
    }
?>
<table width="600" border="0" height="220">
    <tr height="20">
        <td><div class="message-top-title">
        <table width="600" border="0" height="20">
        <td width="450"><?php echo mb_convert_encoding(get_lang('SocialNetwork'),'UTF-8',$charset);?></td>
<?php 
			if (!isset($_REQUEST['view'])) {
?>
        <td width="150"><a href="javascript:void(0)" onclick="change_panel('3','<?php echo $userfriend_id; ?>')" ><?php echo Display::return_icon('folder_up.gif',mb_convert_encoding(get_lang('SeeMoreOptions'),'UTF-8',$charset)).'&nbsp;&nbsp;'.mb_convert_encoding(get_lang('SeeMoreOptions'),'UTF-8',$charset);?></a></td>
<?php
			}
?>
        </table>
        </div></td>
    </tr>
    <tr height="180">
        <td>
    
        <div class="message-content-body-left">
			<img class="message-image-info" src="<?php echo $info_path_friend['dir'].$info_path_friend['file']; ?>"/>
			<dl>
				<dd><?php echo mb_convert_encoding(get_lang('FirstName'),'UTF-8',$charset).' : '.mb_convert_encoding($info_user_friend['firstName'],'UTF-8',$charset); ?></dd>
				<dd><?php echo mb_convert_encoding(get_lang('LastName'),'UTF-8',$charset).' : '.mb_convert_encoding($info_user_friend['lastName'],'UTF-8',$charset); ?></dd>
			</dl>
		</div>
<div class="message-conten-body-right">
<div id="id_content_panel_init"><!--init content changed -->
			<dl>
<?php 
			if (!isset($_REQUEST['view'])) {
?>
				<dd><a href="javascript:void(0)" onclick="change_panel('2','<?php echo $userfriend_id; ?>')"><?php echo mb_convert_encoding(get_lang('SendInviteMessage'),'UTF-8',$charset);?></a></dd>
<?php
			}
?>
<?php       
			if (api_get_setting('allow_message_tool')=='true') {
				if  (isset($_REQUEST['view_panel'])) {
?>
<?php 
			   		$user_info=api_get_user_info($userfriend_id);
			  		 echo mb_convert_encoding(get_lang('To'),'UTF-8',$charset); ?> &nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;<?php echo mb_convert_encoding($user_info['firstName'],'UTF-8',$charset); ?>
			 		 <br/><?php echo mb_convert_encoding(get_lang('subject'),'UTF-8',$charset); ?> :<br/><input id="txt_subject_id" type="text" style="width:200px;">
			   		 <br/><?php echo mb_convert_encoding(get_lang('Message'),'UTF-8',$charset); ?> :<br/><textarea id="txt_area_invite" rows="3" cols="25"></textarea>
			    	 <input type="button" value="<?php echo mb_convert_encoding(get_lang('NewMessage'),'UTF-8',$charset); ?>" onclick="hide_display_message()" />&nbsp;&nbsp;&nbsp; 
			   		 <input type="button" value="<?php echo get_lang('SendMessage'); ?>" onclick="action_database_panel('5','<?php echo $userfriend_id;?>')" />

<?php
				} else {
?>
	<dd><a href="javascript:void(0)" onclick="change_panel('1','<?php echo $userfriend_id; ?>')"><?php echo mb_convert_encoding(get_lang('SendMessage'),'UTF-8',$charset);?></a></dd>
<?php					
				}
?>

<?php
			}
?>
<?php
			if (!isset($_REQUEST['view'])) {
				//<dd><a href="main/social/index.php#remote-tab-5"> echo get_lang('SocialSeeContacts'); </a></dd>
?>
<?php
			}
?>
			</dl>
			
</div><!-- end content changed-->
        </td>
    </tr>
        </div>
    <tr height="22">
        <td>
<div id="display_response_id" style="position:relative"></div>
<div class="message-bottom-title">&nbsp;</div></td>
    </tr>
</table>