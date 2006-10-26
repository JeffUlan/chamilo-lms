<?php
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2006 Dokeos S.A.
	Copyright (c) 2006 Ghent University (UGent)

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

/*
==============================================================================
		INIT SECTION
==============================================================================
*/
/*
-----------------------------------------------------------
	Language Initialisation
-----------------------------------------------------------
*/
$langFile = 'forum';
require ('../inc/global.inc.php');
require_once (api_get_path(LIBRARY_PATH).'formvalidator/FormValidator.class.php');
include_once (api_get_path(LIBRARY_PATH).'groupmanager.lib.php');
//require_once (api_get_path(LIBRARY_PATH).'resourcelinker.lib.php');
$nameTools=get_lang('Forum');

/*
-----------------------------------------------------------
	Including necessary files
-----------------------------------------------------------
*/
include('forumconfig.inc.php');
include('forumfunction.inc.php');


/*
==============================================================================
		MAIN DISPLAY SECTION
==============================================================================
*/
/*
-----------------------------------------------------------
	Retrieving forum and forum categorie information
-----------------------------------------------------------
*/
// we are getting all the information about the current forum and forum category. 
// note pcool: I tried to use only one sql statement (and function) for this
// but the problem is that the visibility of the forum AND forum cateogory are stored in the item_property table
$current_thread=get_thread_information($_GET['thread']); // note: this has to be validated that it is an existing thread
$current_forum=get_forum_information($current_thread['forum_id']); // note: this has to be validated that it is an existing forum. 
$current_forum_category=get_forumcategory_information($current_forum['forum_category']);

$whatsnew_post_info=$_SESSION['whatsnew_post_info'];

/*
-----------------------------------------------------------
	Header and Breadcrumbs
-----------------------------------------------------------
*/
$interbreadcrumb[]=array("url" => "index.php","name" => $nameTools);
$interbreadcrumb[]=array("url" => "viewforumcategory.php?forumcategory=".$current_forum_category['cat_id'],"name" => prepare4display($current_forum_category['cat_title']));
$interbreadcrumb[]=array("url" => "viewforum.php?forum=".$_GET['forum'],"name" => prepare4display($current_forum['forum_title']));
if ($message<>'PostDeletedSpecial')
{
	$interbreadcrumb[]=array("url" => "viewthread.php?forum=".$_GET['forum']."&amp;thread=".$_GET['thread'],"name" => prepare4display($current_thread['thread_title']));
}

Display :: display_header();
api_display_tool_title($nameTools);
//echo '<link href="forumstyles.css" rel="stylesheet" type="text/css" />';

/*
-----------------------------------------------------------
	Is the user allowed here? 
-----------------------------------------------------------
*/
// if the user is not a course administrator and the forum is hidden
// then the user is not allowed here. 
if (!api_is_allowed_to_edit() AND ($current_forum['visibility']==0 OR $current_thread['visibility']==0))
{
	forum_not_allowed_here();
}

/*
-----------------------------------------------------------
	Actions
-----------------------------------------------------------
*/
if ($_GET['action']=='delete' AND isset($_GET['content']) AND isset($_GET['id']) AND api_is_allowed_to_edit())
{
	$message=delete_post($_GET['id']); // note: this has to be cleaned first
}
if (($_GET['action']=='invisible' OR $_GET['action']=='visible') AND isset($_GET['id']) AND api_is_allowed_to_edit())
{
	$message=approve_post($_GET['id'],$_GET['action']); // note: this has to be cleaned first
}
if ($_GET['action']=='move' and isset($_GET['post']))
{
	$message=move_post_form();
}

/*
-----------------------------------------------------------
	Display the action messages
-----------------------------------------------------------
*/
if (isset($message))
{
	Display :: display_normal_message(get_lang($message));
}


if ($message<>'PostDeletedSpecial') // in this case the first and only post of the thread is removed
{
	
	// this increases the number of times the thread has been viewed
	increase_thread_view($_GET['thread']);
	
	
	
	/*
	-----------------------------------------------------------
		Action Links
	-----------------------------------------------------------
	*/
	echo '<div style="float:right;">';
	echo '<a href="viewthread.php?forum='.$_GET['forum'].'&amp;thread='.$_GET['thread'].'&amp;view=flat">'.get_lang('FlatView').'</a> | ';
	echo '<a href="viewthread.php?forum='.$_GET['forum'].'&amp;thread='.$_GET['thread'].'&amp;view=threaded">'.get_lang('ThreadedView').'</a> | ';
	echo '<a href="viewthread.php?forum='.$_GET['forum'].'&amp;thread='.$_GET['thread'].'&amp;view=nested">'.get_lang('NestedView').'</a>';
	echo '</div>';
	// the reply to thread link should only appear when the forum_category is not locked AND the forum is not locked AND the thread is not locked.
	// if one of the three levels is locked then the link should not be displayed
	if ($current_forum_category['locked']==0 AND $current_forum['locked']==0 AND $current_thread['locked']==0 OR api_is_allowed_to_edit())
	{
		// The link should only appear when the user is logged in or when anonymous posts are allowed. 
		if ($_uid OR ($current_forum['allow_anonymous']==1 AND !$_uid))
		{
			echo '<a href="reply.php?forum='.$_GET['forum'].'&amp;thread='.$_GET['thread'].'&amp;action=replythread">'.get_lang('ReplyToThread').'</a>';
		}
	}
	// note: this is to prevent that some browsers display the links over the table (FF does it but Opera doesn't)
	echo '&nbsp;';
	
	
	/*
	-----------------------------------------------------------
		Display Forum Category and the Forum information
	-----------------------------------------------------------
	*/
	
	if (!$_SESSION['view'])
	{
		$viewmode=$current_forum['default_view']; 
	}
	else 
	{
		$viewmode=$_SESSION['view']; 
	}
	
	$viewmode_whitelist=array('flat', 'threaded', 'nested');
	if (isset($_GET['view']) and in_array($_GET['view'],$viewmode_whitelist))
	{
		$viewmode=$_GET['view'];
		$_SESSION['view']=$viewmode; 
	}
	
	
	/*
	-----------------------------------------------------------
		Display Forum Category and the Forum information
	-----------------------------------------------------------
	*/
	// we are getting all the information about the current forum and forum category. 
	// note pcool: I tried to use only one sql statement (and function) for this
	// but the problem is that the visibility of the forum AND forum cateogory are stored in the item_property table
	echo "<table width='100%'>\n";
	
	// the forum category
	echo "\t<tr class=\"forum_category\">\n\t\t<td colspan=\"6\">";
	echo '<a href="index.php" '.class_visible_invisible($current_forum_category['visibility']).'>'.prepare4display($current_forum_category['cat_title']).'</a><br />';
	echo '<span>'.prepare4display($current_forum_category['cat_comment']).'</span>';
	echo "</td>\n";
	echo "\t</tr>\n";
	
	// the forum 
	echo "\t<tr class=\"forum_header\">\n";
	echo "\t\t<td><a href=\"viewforum.php?forum=".$current_forum['forum_id']."\" ".class_visible_invisible($current_forum['visibility']).">".prepare4display($current_forum['forum_title'])."</a><br />";
	echo '<span>'.prepare4display($current_forum['forum_comment']).'</span>';
	echo "</td>\n";
	echo "\t</tr>\n";
	
	// the thread 
	echo "\t<tr class=\"forum_thread\">\n";
	echo "\t\t<td><span ".class_visible_invisible($current_thread['visibility']).">".prepare4display($current_thread['thread_title'])."</span><br />";
	echo "</td>\n";
	echo "\t</tr>\n";
	echo "</table>";
	
	echo '<br />';
	
	switch ($viewmode)
	{
		case 'flat':
			include_once('viewthread_flat.inc.php');
			break;
		case 'threaded':
			include_once('viewthread_threaded.inc.php');
			break;		
		case 'nested':
			include_once('viewthread_nested.inc.php');
			break;			
	}
} // if ($message<>'PostDeletedSpecial') // in this case the first and only post of the thread is removed



/*
==============================================================================
		FOOTER
==============================================================================
*/

Display :: display_footer();
?>



