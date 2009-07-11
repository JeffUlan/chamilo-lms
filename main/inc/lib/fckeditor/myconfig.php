<?php

/*
 *	Dokeos - elearning and course management software
 *
 *	Copyright (c) 2009 Dokeos SPRL
 *	Copyright (c) 2009 Juan Carlos Raña
 *	Copyright (c) 2009 Ivan Tcholakov
 *
 *	For a full list of contributors, see "credits.txt".
 *	The full license can be read in "license.txt".
 *
 *	This program is free software; you can redistribute it and/or
 *	modify it under the terms of the GNU General Public License
 *	as published by the Free Software Foundation; either version 2
 *	of the License, or (at your option) any later version.
 *
 *	See the GNU General Public License for more details.
 *
 * Contact address: Dokeos, rue du Corbeau, 108, B-1030 Brussels, Belgium
 * Mail: info@dokeos.com
 */


/*
 * Custom editor configuration settings, php-side.
 * See http://docs.fckeditor.net/FCKeditor_2.x/Developers_Guide/Configuration/Configuration_Options
 *
 * Configuration data for the editor comes from different sources which are prioritised.
 *
 * 1-st level (the highest priority)
 * "Hardcoded" options by developers. 'Width' and 'Height' might be exception of this rule.
 *
 * 2-nd level
 * Configuration settings from myconfig.php (this file).
 *
 * 3-rd level
 * Default configuration settings that are determined (calculated) by the system..
 *
 * 4-th level
 * Configuration settings from myconfig.js. This file may be modified for customization purposes too.
 * You may choose to create there options or to transfer options from here (not all) which have low probability of future changes.
 * Thus, you will gain performance by exploiting caching, but changes in myconfig.js do not enforce immediatelly.
 * Here is the mapping rule:
 *
 * myconfig.php                                      myconfig.js
 * ---------------------------------------------------------------------------------------------
 * $config['FontFormats'] = 'p;h1;h2;h3;h4;h5';      FCKConfig.FontFormats = 'p;h1;h2;h3;h4;h5';
 *
 * 5-th level (the lowest priority)
 * Configuration settings from myconfig.js. This file is "as is" in the original source, modifying it is not recommended.
 */


/*
 * Toolbar definitions.
 */

// The following setting is the directory where the online editor's toobar definitions reside in correspondent php-files.
// By default, the directory name is 'toolbars' and it has been created at .../dokeos/main/inc/lib/fckeditor/ .
// For using your customized toolbars, crate another directory 'toolbars_custom' at the same path, i.e.
// create .../dokeos/main/inc/lib/fckeditor/toolbars_custom/ . Then, copy the original php-definition files
// from .../dokeos/main/inc/lib/fckeditor/toolbars/ to the new one. Change the following configuration setting, so it to
// point to the new directory:
// $config['ToolbarSets']['Directory'] = 'toolbars_custom';
// Then, you may modify the newly copied toolbar definitions at your will, just keep correct php-syntax.
$config['ToolbarSets']['Directory'] = 'toolbars';


/*
 * Plugins.
 */

// customizations : This plugin has been developed by the Dokeos team for editor's integration within the system.
// Please, do not disable it.
$config['LoadPlugin'][] = 'customizations';

// dragresizetable & tablecommands : Plugins for improvement table-related operations.
$config['LoadPlugin'][] = 'dragresizetable';
$config['LoadPlugin'][] = 'tablecommands';

// audio: Adds a dialog for inserting audio files (.mp3).
$config['LoadPlugin'][] = 'audio';

// MP3 : This is the old plugin for inserting audio files. Probably this plugin will be removed at the next release.
// If you wish to use it, disable the "audio" plugin first.
//$config['LoadPlugin'][] = 'MP3';

// ImageManager : Adds a dialog (image gallery) for inserting images. The advanced file manager has its own functionality
// for previewing images. This is why we load this plugin only in case when the simple file manager is used.
if (!(api_get_setting('advanced_filemanager') == 'true')) {
	$config['LoadPlugin'][] = 'ImageManager';
}

// fckEmbedMovies : Adds a dilog for inserting video files.
$config['LoadPlugin'][] = 'fckEmbedMovies';

// flvPlayer : Adds a dilog for inserting video files (.flv, .mp4), so they to be viewed through a flash-based player.
$config['LoadPlugin'][] = 'flvPlayer';

// youtube : Adds a dilog for inserting YouTube video-streams.
$config['LoadPlugin'][] = 'youtube';

// googlemaps : Adds a dilog for inserting Google maps.
$config['LoadPlugin'][] = 'googlemaps';

// mimetex : // Adds a dilog for inserting mathematical formulas. In order this plugin to work prpoperly, preliminary changes
// in your server configuration have to be done. See the installation guide.
$config['LoadPlugin'][] = 'mimetex';

// wikilink : Adds a dialog for inserting wiki-formatted links.
$config['LoadPlugin'][] = 'wikilink';

// imgmap : Adds a dialog for assigning hyperlinks to specified image areas.
$config['LoadPlugin'][] = 'imgmap';

// Simple File Manager
// This setting defines how the simple file manager to be opened:
// true  - in a new browser window, or
// false - as a dialog whithin the page (recommended).
$config['OpenSimpleFileManagerInANewWindow'] = false;


/*
 * Additional note:
 * For debugging purposes the editor may run using original source versions of its javascripts, not the "compressed" versions.
 * In case of problems, when you need to use this feature, go to the platform administration settings page and switch the system
 * into "test server" mode. Don't forged to switch it back to "production server" mode after testing.
 */
