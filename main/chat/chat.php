<?php // $Id: chat.php 17220 2008-12-10 22:40:05Z herodoto $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Olivier Brouckaert

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
==============================================================================
*/
/**
==============================================================================
*	Frameset of the Chat tool
*
*	@author Olivier Brouckaert
*	@package dokeos.chat
==============================================================================
*/

$language_file = array ('chat');
include('../inc/global.inc.php');
$nameTools=get_lang('Chat');
if ($_GET["origin"] != 'whoisonline') {
	api_protect_course_script(true);
}
else
{
	$origin = $_SESSION['origin'];
	$target = $_SESSION['target'];
	$_SESSION['origin']=$_GET["origin"];
	$_SESSION['target']=$_GET["target"];
}
/* ============================================================================== 
  			TRACKING
==============================================================================  */
include('../inc/lib/events.lib.inc.php');
event_access_tool(TOOL_CHAT);



/*
 * Choose CSS style (platform's, user's, or course's) 
 */
$platform_theme = api_get_setting('stylesheets'); 	// plataform's css
$my_style=$platform_theme;
if(api_get_setting('user_selected_theme') == 'true') 
{		
	$useri = api_get_user_info();
	$user_theme = $useri['theme'];
	if(!empty($user_theme) && $user_theme != $my_style)
	{
		$my_style = $user_theme;					// user's css
	}
}

$mycourseid = api_get_course_id();
if (!empty($mycourseid) && $mycourseid != -1) 
{	
	if (api_get_setting('allow_course_theme') == 'true') 
	{	
		$mycoursetheme=api_get_course_setting('course_theme');			
		if (!empty($mycoursetheme) && $mycoursetheme!=-1)		 
		{							
			if(!empty($mycoursetheme) && $mycoursetheme != $my_style)
			{				
				$my_style = $mycoursetheme;		// course's css
			}			
		}				
	
	}
	$open_chat_window=api_get_course_setting('allow_open_chat_window');
}

switch($my_style){
	case 'dokeos_classic' : 
		$footer_size = 48;
		break;
	case 'academica' : 
		$footer_size = 140;
		break;
	case 'silver_line' : 
		$footer_size = 60;
		break;
	case 'baby_orange' : 
		$footer_size = 120;
		break;
	case 'public_admin' : 
		$footer_size = 90;
		break;
	default : 
		$footer_size = 48;
		break;
}
$cidreq=$_GET['cidReq'];

echo '<html>';
echo'<HEAD><TITLE>'.get_lang('Chat').' - '.$mycourseid.' - '.api_get_setting('siteName').'</TITLE>';

if ($open_chat_window==false)
{
	echo'<frameset rows="135,*,'.$footer_size.'" border="0" frameborder="0" framespacing="1">';
	echo '<frame src="chat_banner.php?cidReq='.$cidreq.'" name="chat_banner" scrolling="no">';
}
echo '<frameset cols="165,*,0" border="1" frameborder="1" framespacing="1">';
echo '<frame src="chat_whoisonline.php?cidReq='.$cidreq.'" name="chat_whoisonline" scrolling="auto">';
echo'<frameset rows="25,15" border="1" frameborder="1" framespacing="1">';
echo '<frame src="chat_chat.php?origin='.$_GET["origin"].'&target='.$_GET["target"].'&amp;cidReq='.$cidreq.'" name="chat_chat" scrolling="auto">';
echo '<frame src="chat_message.php?cidReq='.$cidreq.'" name="chat_message" scrolling="no">';
echo '</frameset>';
echo '<frame src="chat_hidden.php?cidReq='.$cidreq.'" name="chat_hidden" scrolling="no">';
echo'</frameset>';

if ($open_chat_window==false)
{
	echo '<frame src="chat_footer.php?cidReq='.$cidreq.'" name="chat_footer" scrolling="no">';
	echo '</frameset>';
}
echo'<noframes></noframes>';
echo '</html>';
?>