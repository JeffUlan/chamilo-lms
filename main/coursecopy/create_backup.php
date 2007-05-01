<?php
// $Id: create_backup.php 12219 2007-05-01 18:46:59Z yannoo $
/*
============================================================================== 
	Dokeos - elearning and course management software
	
	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Bart Mollet (bart.mollet@hogent.be)
	
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
 * ==============================================================================
 * Create a backup.
 * 
 * @author Bart Mollet <bart.mollet@hogent.be>
 * @package dokeos.backup
 * ==============================================================================
 */

// name of the language file that needs to be included 
$language_file = array ('admin','coursebackup');

// including the global file
include ('../inc/global.inc.php');

// Check access rights (only teachers are allowed here)
if (!api_is_allowed_to_edit())
{
	api_not_allowed(true);
}
// section for the tabs
$this_section=SECTION_COURSES;

// breadcrumbs
$interbreadcrumb[] = array ("url" => "../course_info/maintenance.php", "name" => get_lang('Maintenance'));

// Displaying the header
$nameTools = get_lang('CreateBackup');
Display::display_header($nameTools);

// include additional libraries
include_once(api_get_path(LIBRARY_PATH) . 'fileManage.lib.php');
require_once ('classes/CourseBuilder.class.php');
require_once ('classes/CourseArchiver.class.php');
require_once ('classes/CourseRestorer.class.php');
require_once ('classes/CourseSelectForm.class.php');

// Display the tool title
api_display_tool_title($nameTools);

/*
==============================================================================
		MAIN CODE
==============================================================================
*/ 
if ((isset ($_POST['action']) && $_POST['action'] == 'course_select_form') || (isset ($_POST['backup_option']) && $_POST['backup_option'] == 'full_backup'))
{
	if (isset ($_POST['action']) && $_POST['action'] == 'course_select_form')
	{
		$course = CourseSelectForm :: get_posted_course();
	}
	else
	{
		$cb = new CourseBuilder();
		$course = $cb->build();
	}
	$zip_file = CourseArchiver :: write_course($course);
	Display::display_confirmation_message(get_lang('BackupCreated').'<br/><br/><a href="../course_info/download.php?archive='.$zip_file.'">'.$zip_file.'</a>', false);
	echo '<p><a href="../course_home/course_home.php">&lt;&lt; '.get_lang('CourseHomepage').'</a></p>';
?>
	<!-- Manual download <script language="JavaScript">
	 setTimeout('download_backup()',2000);
	 function download_backup()
	 {
		window.location="../course_info/download.php?archive=<?php echo $zip_file ?>";
	 }
	</script> //-->
	<?php

}
elseif (isset ($_POST['backup_option']) && $_POST['backup_option'] == 'select_items')
{
	$cb = new CourseBuilder();
	$course = $cb->build();
	CourseSelectForm :: display_form($course);
}
else
{
	$cb = new CourseBuilder();
	$course = $cb->build();
	if (!$course->has_resources())
	{
		echo get_lang('NoResourcesToBackup');
	}
	else
	{
		echo get_lang('SelectOptionForBackup');
		
		include (api_get_path(LIBRARY_PATH).'formvalidator/FormValidator.class.php');
		$form = new FormValidator('create_backup_form','POST');
		$renderer = $form->defaultRenderer();
		$renderer->setElementTemplate('<div>{element}</div> ');
		$form->addElement('radio', 'backup_option', '', get_lang('CreateFullBackup'), 'full_backup');
		$form->addElement('radio', 'backup_option', '',  get_lang('LetMeSelectItems'), 'select_items');
		$form->addElement('html','<br/>');
		$form->addElement('submit', null, get_lang('CreateBackup'));
		
		$form->add_progress_bar();
		
		$values['backup_option'] = 'full_backup';
		$form->setDefaults($values);
		
		$form->display();
	}
}
/*
==============================================================================
		FOOTER 
==============================================================================
*/
Display::display_footer();
?>