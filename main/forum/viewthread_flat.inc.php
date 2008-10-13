<?php
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2008 Dokeos SPRL
	Copyright (c) 2006 Ghent University (UGent)

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

/**
*	These files are a complete rework of the forum. The database structure is 
*	based on phpBB but all the code is rewritten. A lot of new functionalities
*	are added:
* 	- forum categories and forums can be sorted up or down, locked or made invisible
*	- consistent and integrated forum administration
* 	- forum options: 	are students allowed to edit their post? 
* 						moderation of posts (approval)
* 						reply only forums (students cannot create new threads)
* 						multiple forums per group
*	- sticky messages
* 	- new view option: nested view
* 	- quoting a message
*	
*	@Author Patrick Cool <patrick.cool@UGent.be>, Ghent University
*	@Copyright Ghent University
*	@Copyright Patrick Cool
* 
* 	@package dokeos.forum
*/

/**
 **************************************************************************
 *						IMPORTANT NOTICE
 * Please do not change anything is this code yet because there are still
 * some significant code that need to happen and I do not have the time to
 * merge files and test it all over again. So for the moment, please do not
 * touch the code
 * 							-- Patrick Cool <patrick.cool@UGent.be>
 ************************************************************************** 
 */

$rows=get_posts($current_thread['thread_id']);



foreach ($rows as $row)
{
	echo "<table width=\"100%\" class=\"post\" cellspacing=\"5\" border=\"0\">\n";
	// the style depends on the status of the message: approved or not
	if ($row['visible']=='0')
	{
		$titleclass='forum_message_post_title_2_be_approved';
		$messageclass='forum_message_post_text_2_be_approved';
		$leftclass='forum_message_left_2_be_approved';	
	}
	else 
	{
		$titleclass='forum_message_post_title';
		$messageclass='forum_message_post_text';
		$leftclass='forum_message_left';		
	}
	
	echo "\t<tr>\n";
	echo "\t\t<td rowspan=\"3\" class=\"$leftclass\">";
	if ($row['user_id']=='0')
	{
		$name=prepare4display($row['poster_name']);
	}
	else 
	{
		$name=$row['firstname'].' '.$row['lastname'];
	}
	if($origin!='learnpath')
	{
	
		if (api_get_course_setting('allow_user_image_forum')) {
			echo '<br />'.display_user_image($row['user_id'],$name).'<br />';
		}
				
		echo display_user_link($row['user_id'], $name).'<br />';
	}
	else 
	{
		echo $name. '<br />';
	}
	echo $row['post_date'].'<br /><br />';
	// The user who posted it can edit his thread only if the course admin allowed this in the properties of the forum
	// The course admin him/herself can do this off course always
	if (($current_forum['allow_edit']==1 AND $row['user_id']==$_user['user_id']) or (api_is_allowed_to_edit(false,true)  && !(api_is_course_coach() && $current_forum['session_id']!=$_SESSION['id_session'])))
	{
		echo "<a href=\"editpost.php?".api_get_cidreq()."&forum=".Security::remove_XSS($_GET['forum'])."&amp;thread=".Security::remove_XSS($_GET['thread'])."&amp;post=".$row['post_id']."&origin=".$origin."\">".icon('../img/edit.gif',get_lang('Edit'))."</a>\n";
	}
	if (api_is_allowed_to_edit(false,true)  && !(api_is_course_coach() && $current_forum['session_id']!=$_SESSION['id_session']))
	{
		echo "<a href=\"".api_get_self()."?".api_get_cidreq()."&forum=".Security::remove_XSS($_GET['forum'])."&amp;thread=".Security::remove_XSS($_GET['thread'])."&amp;action=delete&amp;content=post&amp;id=".$row['post_id']."&origin=".$origin."\" onclick=\"javascript:if(!confirm('".addslashes(htmlentities(get_lang("DeletePost"),ENT_QUOTES,$charset))."')) return false;\">".icon('../img/delete.gif',get_lang('Delete'))."</a>\n";
		display_visible_invisible_icon('post', $row['post_id'], $row['visible'],array('forum'=>Security::remove_XSS($_GET['forum']),'thread'=>Security::remove_XSS($_GET['thread']), 'origin'=>$origin ));
		echo "\n";
		echo "<a href=\"viewthread.php?".api_get_cidreq()."&forum=".Security::remove_XSS($_GET['forum'])."&amp;thread=".Security::remove_XSS($_GET['thread'])."&amp;action=move&amp;post=".$row['post_id']."&origin=".$origin."\">".icon('../img/deplacer_fichier.gif',get_lang('MovePost'))."</a>";
	}
	echo '<br /><br />';
	//if (($current_forum_category['locked']==0 AND $current_forum['locked']==0 AND $current_thread['locked']==0) OR api_is_allowed_to_edit())
	if ($current_forum_category['locked']==0 AND $current_forum['locked']==0 AND $current_thread['locked']==0 OR api_is_allowed_to_edit(false,true))
	{
		if ($_user['user_id'] OR ($current_forum['allow_anonymous']==1 AND !$_user['user_id']))
		{
			echo '<a href="reply.php?'.api_get_cidreq().'&forum='.Security::remove_XSS($_GET['forum']).'&amp;thread='.Security::remove_XSS($_GET['thread']).'&amp;post='.$row['post_id'].'&amp;action=replymessage&origin='.$origin.'">'.get_lang('ReplyToMessage').'</a><br />';
			echo '<a href="reply.php?'.api_get_cidreq().'&forum='.Security::remove_XSS($_GET['forum']).'&amp;thread='.Security::remove_XSS($_GET['thread']).'&amp;post='.$row['post_id'].'&amp;action=quote&origin='.$origin.'">'.get_lang('QuoteMessage').'</a><br /><br />';
		}
	}
	else 
	{
		if ($current_forum_category['locked']==1)
		{
			echo get_lang('ForumcategoryLocked').'<br />';
		}
		if ($current_forum['locked']==1)
		{
			echo get_lang('ForumLocked').'<br />';
		}
		if ($current_thread['locked']==1)
		{
			echo get_lang('ThreadLocked').'<br />';
		}				
	}
	echo "</td>\n";
	// show the 
	if (isset($whatsnew_post_info[$current_forum['forum_id']][$current_thread['thread_id']][$row['post_id']]) and !empty($whatsnew_post_info[$current_forum['forum_id']][$current_thread['thread_id']][$row['post_id']]) and !empty($whatsnew_post_info[$_GET['forum']][$row['thread_id']]))
	{
		$post_image=icon('../img/forumpostnew.gif');
	}
	else 
	{
		$post_image=icon('../img/forumpost.gif');
	}
	if ($row['post_notification']=='1' AND $row['poster_id']==$_user['user_id'])
	{
		$post_image.=icon('../img/forumnotification.gif',get_lang('YouWillBeNotified'));
	}	
	// The post title
	echo "\t\t<td class=\"$titleclass\">".prepare4display($row['post_title'])."</td>\n";
	echo "\t</tr>\n";	
	
	// The post message
	echo "\t<tr>\n";
	echo "\t\t<td class=\"$messageclass\">".prepare4display($row['post_text'])."</td>\n";
	echo "\t</tr>\n";
	
	// The check if there is an attachment
	$attachment_list=get_attachment($row['post_id']);	
	
	if (!empty($attachment_list))
	{
		echo '<tr><td height="50%">';	
		$realname=$attachment_list['path'];			
		$user_filename=$attachment_list['filename'];
						
		echo Display::return_icon('attachment.gif',get_lang('Attachment'));
		echo '<a href="download.php?file=';		
		echo $realname;
		echo ' "> '.$user_filename.' </a>';
		echo '<span class="forum_attach_comment" >'.$attachment_list['comment'].'</span><br />';	
		echo '</td></tr>';		
	}
	
	// The post has been displayed => it can be removed from the what's new array
	unset($whatsnew_post_info[$current_forum['forum_id']][$current_thread['thread_id']][$row['post_id']]);
	unset($whatsnew_post_info[$current_forum['forum_id']][$current_thread['thread_id']]);
	unset($_SESSION['whatsnew_post_info'][$current_forum['forum_id']][$current_thread['thread_id']][$row['post_id']]);
	unset($_SESSION['whatsnew_post_info'][$current_forum['forum_id']][$current_thread['thread_id']]);
	echo "</table>";
}
?>