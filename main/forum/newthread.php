<?php
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2006-2008 Dokeos S.A.
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

// name of the language file that needs to be included
$language_file = 'forum';

// including the global dokeos file
require ('../inc/global.inc.php');

// the section (tabs)
$this_section=SECTION_COURSES;

// notice for unauthorized people.
api_protect_course_script(true);

// including additional library scripts
require_once (api_get_path(LIBRARY_PATH).'formvalidator/FormValidator.class.php');
include_once (api_get_path(LIBRARY_PATH).'groupmanager.lib.php');
$nameTools=get_lang('Forum');

// configuration for FCKeditor
$fck_attribute['Width'] = '100%';
$fck_attribute['Height'] = '300';
$fck_attribute['ToolbarSet'] = 'Middle';
$fck_attribute['Config']['IMUploadPath'] = 'upload/forum/';
$fck_attribute['Config']['FlashUploadPath'] = 'upload/forum/';
if(!api_is_allowed_to_edit())
{
	$fck_attribute['Config']['UserStatus'] = 'student';
}
/*
-----------------------------------------------------------
	Including necessary files
-----------------------------------------------------------
*/
include('forumconfig.inc.php');
include('forumfunction.inc.php');


//are we in a lp ?
$origin = '';
if(isset($_GET['origin']))
{
	$origin =  Security::remove_XSS($_GET['origin']);
}


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
$current_forum=get_forum_information($_GET['forum']); // note: this has to be validated that it is an existing forum.
$current_forum_category=get_forumcategory_information($current_forum['forum_category']);

/*
-----------------------------------------------------------
	Breadcrumbs
-----------------------------------------------------------
*/
$interbreadcrumb[]=array("url" => "index.php","name" => $nameTools);
$interbreadcrumb[]=array("url" => "viewforumcategory.php?forumcategory=".$current_forum_category['cat_id'],"name" => $current_forum_category['cat_title']);
$interbreadcrumb[]=array("url" => "viewforum.php?forum=".Security::remove_XSS($_GET['forum']),"name" => $current_forum['forum_title']);
$interbreadcrumb[]=array("url" => "newthread.php?forum=".Security::remove_XSS($_GET['forum']),"name" => get_lang('NewTopic'));

/*
-----------------------------------------------------------
	Resource Linker
-----------------------------------------------------------
*/
if (isset($_POST['add_resources']) AND $_POST['add_resources']==get_lang('Resources'))
{
	$_SESSION['formelements']=$_POST;
	$_SESSION['origin']=$_SERVER['REQUEST_URI'];
	$_SESSION['breadcrumbs']=$interbreadcrumb;
	header("Location: ../resourcelinker/resourcelinker.php");
}

/*
-----------------------------------------------------------
	Header
-----------------------------------------------------------
*/
if($origin=='learnpath')
{
	include(api_get_path(INCLUDE_PATH).'reduced_header.inc.php');
} else
{
	Display :: display_header(null);
	api_display_tool_title($nameTools);
}
//echo '<link href="forumstyles.css" rel="stylesheet" type="text/css" />';
/*
-----------------------------------------------------------
	Is the user allowed here?
-----------------------------------------------------------
*/
// the user is not allowed here if:
// 1. the forumcategory or forum is invisible (visibility==0) and the user is not a course manager
// 2. the forumcategory or forum is locked (locked <>0) and the user is not a course manager
// 3. new threads are not allowed and the user is not a course manager
// 4. anonymous posts are not allowed and the user is not logged in
// I have split this is several pieces for clarity.

if (!api_is_allowed_to_edit() AND (($current_forum_category['visibility']==0 OR $current_forum['visibility']==0)))
{
	forum_not_allowed_here();
}
// 2. the forumcategory or forum is locked (locked <>0) and the user is not a course manager
if (!api_is_allowed_to_edit() AND ($current_forum_category['locked']<>0 OR $current_forum['locked']<>0))
{
	forum_not_allowed_here();
}
// 3. new threads are not allowed and the user is not a course manager
if (!api_is_allowed_to_edit() AND $current_forum['allow_new_threads']<>1)
{
	forum_not_allowed_here();
}
// 4. anonymous posts are not allowed and the user is not logged in
if (!$_user['user_id']  AND $current_forum['allow_anonymous']<>1)
{
	forum_not_allowed_here();
}

/*
-----------------------------------------------------------
	Display forms / Feedback Messages
-----------------------------------------------------------
*/
handle_forum_and_forumcategories();

/*
-----------------------------------------------------------
	Display Forum Category and the Forum information
-----------------------------------------------------------
*/
echo "<table class=\"data_table\" width='100%'>\n";

if($origin != 'learnpath')
{
	echo "\t<tr>\n\t\t<th style=\"padding-left:5px;\" align=\"left\"  colspan=\"2\">";
	
	echo '<span class="forum_title">'.prepare4display($current_forum['forum_title']).'</span>';
		
	if (!empty ($current_forum['forum_comment'])) 
	{
		echo '<br><span class="forum_description">'.prepare4display($current_forum['forum_comment']).'</span>';
	}
	
	if (!empty ($current_forum_category['cat_title'])) 
	{
		echo '<br /><span class="forum_low_description">'.prepare4display($current_forum_category['cat_title'])."</span><br />";
	}	
	echo "</th>\n";
	echo "\t</tr>\n";
}
echo '</table>';

$values=show_add_post_form('newthread','', $_SESSION['formelements']);
if (!empty($values) and isset($values['SubmitPost']))
{
	store_thread($values);
}

/*
==============================================================================
		FOOTER
==============================================================================
*/

Display :: display_footer();
?>