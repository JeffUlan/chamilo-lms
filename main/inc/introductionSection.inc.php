<?php

/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) Bart Mollet, Hogeschool Gent

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
==============================================================================
 * The INTRODUCTION MICRO MODULE is used to insert and edit
 * an introduction section on a Dokeos Module. It can be inserted on any
 * Dokeos Module, provided a connection to a course Database is already active.
 *
 * The introduction content are stored on a table called "introduction"
 * in the course Database. Each module introduction has an Id stored on
 * the table. It is this id that can make correspondance to a specific module.
 *
 * 'introduction' table description
 *   id : int
 *   intro_text :text
 *
 *
 * usage :
 *
 * $moduleId = XX // specifying the module Id
 * include(moduleIntro.inc.php);
*
*	@package dokeos.include
==============================================================================
*/


include_once(api_get_path(LIBRARY_PATH).'formvalidator/FormValidator.class.php');

/*
-----------------------------------------------------------
	Constants and variables
-----------------------------------------------------------
*/
$TBL_INTRODUCTION = Database::get_course_table(TABLE_TOOL_INTRO);
$intro_editAllowed = $is_allowed_to_edit;

global $charset;
$intro_cmdEdit = (empty($_GET['intro_cmdEdit'])?'':$_GET['intro_cmdEdit']);
$intro_cmdUpdate = (empty($_POST['intro_cmdUpdate'])?'':$_POST['intro_cmdUpdate']);
$intro_cmdDel= (empty($_GET['intro_cmdDel'])?'':$_GET['intro_cmdDel']);
$intro_cmdAdd= (empty($_GET['intro_cmdAdd'])?'':$_GET['intro_cmdAdd']);

if (!empty ($GLOBALS["_cid"]))
{
	$form = new FormValidator('introduction_text', 'post', api_get_self()."?".api_get_cidreq());
}
else
{
	$form = new FormValidator('introduction_text');
}
$renderer =& $form->defaultRenderer();
$renderer->setElementTemplate('<div style="width: 80%; margin: 0px auto; padding-bottom: 10px; ">{element}</div>');

$form->addElement('style_submit_button', 'intro_cmdUpdate', get_lang('Save'), 'class="save"');
$form->add_html_editor('intro_content',null,null,false);
$form->addElement('style_submit_button', 'intro_cmdUpdate', get_lang('Save'), 'class="save"');

/*=========================================================
  INTRODUCTION MICRO MODULE - COMMANDS SECTION (IF ALLOWED)
  ========================================================*/

if ($intro_editAllowed)
{
	/* Replace command */

	if( $intro_cmdUpdate )
	{
		if( $form->validate())
		{
			$form_values = $form->exportValues();
			$intro_content = $form_values['intro_content'];

			if ( ! empty($intro_content) )
			{
				$sql = "REPLACE $TBL_INTRODUCTION SET id='$moduleId',intro_text='".Database::escape_string($intro_content)."'";
				api_sql_query($sql,__FILE__,__LINE__);
				Display::display_confirmation_message(get_lang('IntroductionTextUpdated'),false);
			}
			else
			{
				$intro_cmdDel = true;	// got to the delete command
			}
		}
		else
		{
		$intro_cmdEdit = true;
		}
	}

	/* Delete Command */

	if($intro_cmdDel)
	{
		api_sql_query("DELETE FROM $TBL_INTRODUCTION WHERE id='".$moduleId."'",__FILE__,__LINE__);
		Display::display_confirmation_message(get_lang('IntroductionTextDeleted'));
	}
}


/*===========================================
  INTRODUCTION MICRO MODULE - DISPLAY SECTION
  ===========================================*/

/* Retrieves the module introduction text, if exist */

$sql = "SELECT intro_text FROM $TBL_INTRODUCTION WHERE id='".$moduleId."'";
$intro_dbQuery = api_sql_query($sql,__FILE__,__LINE__);
$intro_dbResult = mysql_fetch_array($intro_dbQuery);
$intro_content = $intro_dbResult['intro_text'];

/* Determines the correct display */

if ($intro_cmdEdit || $intro_cmdAdd)
{
	$intro_dispDefault = false;
	$intro_dispForm = true;
	$intro_dispCommand = false;
}
else
{
	$intro_dispDefault = true;
	$intro_dispForm = false;

	if ($intro_editAllowed)
	{
		$intro_dispCommand = true;
	}
	else
	{
		$intro_dispCommand = false;
	}
}


/* Executes the display */

if ($intro_dispForm)
{
	$default['intro_content'] = $intro_content;
	$form->setDefaults($default);
	//echo '<div id="courseintro">';
	echo '<div id="courseintro" style="width: 100%">';
	$form->display();
	echo '</div>';
}

if ($intro_dispDefault)
{
	//$intro_content = make_clickable($intro_content); // make url in text clickable
	$intro_content = text_filter($intro_content); // parse [tex] codes
	if (!empty($intro_content))
	{
		echo "<table align='center' style='width: 80%;'><tr><td>$intro_content</td></tr></table>";
	}
}

if ($intro_dispCommand)
{
	if( empty($intro_content) ) // displays "Add intro" Commands
	{
		echo "<div id=\"courseintro\"><p>\n";
		if (!empty ($GLOBALS["_cid"]))
		{
			echo "<a href=\"".api_get_self()."?".api_get_cidreq()."&amp;intro_cmdAdd=1\">\n".get_lang('AddIntro')."</a>\n";
		}
		else
		{
			echo "<a href=\"".api_get_self()."?intro_cmdAdd=1\">\n".get_lang('AddIntro')."</a>\n";
		}
		echo "</p>\n</div>";
	}
	else // displays "edit intro && delete intro" Commands
	{
		echo "<div id=\"courseintro_icons\"><p>\n";
		if (!empty ($GLOBALS["_cid"]))
		{
			echo "<a href=\"".api_get_self()."?".api_get_cidreq()."&amp;intro_cmdEdit=1\"><img src=\"".api_get_path(WEB_CODE_PATH)."img/edit.gif\" alt=\"".get_lang('Modify')."\" border=\"0\" /></a>\n";
			echo "<a href=\"".api_get_self()."?".api_get_cidreq()."&amp;intro_cmdDel=1\" onclick=\"javascript:if(!confirm('".addslashes(htmlentities(get_lang('ConfirmYourChoice'),ENT_QUOTES,$charset))."')) return false;\"><img src=\"".api_get_path(WEB_CODE_PATH)."img/delete.gif\" alt=\"".get_lang('Delete')."\" border=\"0\" /></a>\n";
		}
		else
		{
			echo "<a href=\"".api_get_self()."?intro_cmdEdit=1\"><img src=\"".api_get_path(WEB_CODE_PATH)."img/edit.gif\" alt=\"".get_lang('Modify')."\" border=\"0\" /></a>\n";
			echo "<a href=\"".api_get_self()."?intro_cmdDel=1\" onclick=\"javascript:if(!confirm('".addslashes(htmlentities(get_lang('ConfirmYourChoice'),ENT_QUOTES,$charset))."')) return false;\"><img src=\"".api_get_path(WEB_CODE_PATH)."img/delete.gif\" alt=\"".get_lang('Delete')."\" border=\"0\" /></a>\n";
		}
		echo "</p>\n</div>";
	}
}
?>
