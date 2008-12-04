<?php // $Id: document.inc.php 17072 2008-12-04 21:54:56Z yannoo $

/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2008 Dokeos SPRL
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) various contributors

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

/*
==============================================================================
		EXTRA FUNCTIONS FOR DOCUMENT.PHP/UPLOAD.PHP
==============================================================================
/////////////////////////////////////////////////
//--> leave these here or move them elsewhere? //
/////////////////////////////////////////////////
*/


/**
 * Builds the form thats enables the user to 
 * select a directory to browse/upload in
 *
 * @param array 	An array containing the folders we want to be able to select
 * @param string	The current folder (path inside of the "document" directory, including the prefix "/")
 * @param string	Group directory, if empty, prevents documents to be uploaded (because group documents cannot be uploaded in root)
 * @param	boolean	Whether to change the renderer (this will add a template <span> to the QuickForm object displaying the form)
 * @return string html form
 */
function build_directory_selector($folders,$curdirpath,$group_dir='',$changeRenderer=false)
{
	$folder_titles = array();
	if(get_setting('use_document_title') == 'true')
	{
		if (is_array($folders))
		{
			$escaped_folders = array();
			foreach($folders as $key=>$val){$escaped_folders[$key] = Database::escape_string($val);}
			$folder_sql = implode("','",$escaped_folders);
			$doc_table = Database::get_course_table(TABLE_DOCUMENT);
			$sql = "SELECT * FROM $doc_table WHERE filetype='folder' AND path IN ('".$folder_sql."')";
			$res = api_sql_query($sql,__FILE__,__LINE__);
			$folder_titles = array();
			while($obj = Database::fetch_object($res))
			{
				$folder_titles[$obj->path] = $obj->title;	
			}
		}
	}
	else
	{
		foreach($folders as $folder)
		{
			$folder_titles[$folder] = basename($folder);	
		}	
	}
	
	require_once (api_get_path(LIBRARY_PATH).'formvalidator/FormValidator.class.php');
	$form = new FormValidator('selector','POST',api_get_self());
	
	$parent_select = $form->addElement('select', 'curdirpath', get_lang('CurrentDirectory'),'','onchange="javascript:document.selector.submit()"');
	
	if($changeRenderer==true){
		$renderer = $form->defaultRenderer();
		$renderer->setElementTemplate('<span>{label} : {element}</span> ','curdirpath');
	}
	
	//group documents cannot be uploaded in the root
	if(empty($group_dir))
	{
		$parent_select -> addOption(get_lang('HomeDirectory'),'/');
		if(is_array($folders))
		{
			foreach ($folders as $folder)
			{
				$selected = ($curdirpath==$folder)?' selected="selected"':'';
				$path_parts = explode('/',$folder);
				$label = str_repeat('&nbsp;&nbsp;&nbsp;',count($path_parts)-2).' &mdash; '.$folder_titles[$folder];
				$parent_select -> addOption($label,$folder);
				if($selected!='') $parent_select->setSelected($folder);
			}
		}
	}
	else
	{
		foreach ($folders as $folder)
		{
			$selected = ($curdirpath==$folder)?' selected="selected"':'';
			$label = $folder_titles[$folder];
			if( $folder == $group_dir)
			{
				$label = '/ ('.get_lang('HomeDirectory').')';
			}
			else
			{
				$path_parts = explode('/',str_replace($group_dir,'',$folder));
				$label = str_repeat('&nbsp;&nbsp;&nbsp;',count($path_parts)-2).' &mdash; '.$label;			
			}
			$parent_select -> addOption($label,$folder);
			if($selected!='') $parent_select->setSelected($folder);
		}
	}
	
	$form=$form->toHtml();

	return $form;
}

/**
 * Create a html hyperlink depending on if it's a folder or a file
 *
 * @param string $www
 * @param string $title
 * @param string $path
 * @param string $filetype (file/folder)
 * @param int $visibility (1/0)
 * @return string url
 */
function create_document_link($www,$title,$path,$filetype,$size,$visibility)
{
	global $dbl_click_id;
	if(isset($_SESSION['_gid']))
	{
		$req_gid = '&amp;gidReq='.$_SESSION['_gid'];
	}
	else 
	{
		$req_gid = '';
	}
	$url_path = urlencode($path);
	//add class="invisible" on invisible files
	$visibility_class= ($visibility==0)?' class="invisible"':'';
	//build download link (icon)
	$forcedownload_link=($filetype=='folder')?api_get_self().'?'.api_get_cidreq().'&action=downloadfolder&amp;path='.$url_path.$req_gid:api_get_self().'?'.api_get_cidreq().'&amp;action=download&amp;id='.$url_path.$req_gid;
	//folder download or file download?
	$forcedownload_icon=($filetype=='folder')?'folder_zip.gif':'filesave.gif';
	//prevent multiple clicks on zipped folder download
	$prevent_multiple_click =($filetype=='folder')?" onclick=\"javascript:if(typeof clic_$dbl_click_id == 'undefined' || clic_$dbl_click_id == false) { clic_$dbl_click_id=true; window.setTimeout('clic_".($dbl_click_id++)."=false;',10000); } else { return false; }\"":'';
	$target='_top';
	if($filetype=='file') {
		//check the extension
		$ext=explode('.',$path);
		$ext=strtolower($ext[sizeof($ext)-1]);
		//"htmlfiles" are shown in a frameset
		if($ext == 'htm' || $ext == 'html' || $ext == 'gif' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'png')
		{
			$url = "showinframes.php?".api_get_cidreq()."&amp;file=".$url_path.$req_gid;
		}
		else 
		{
			//url-encode for problematic characters (we may not call them dangerous characters...)
			$path = str_replace('%2F', '/',$url_path).'?'.api_get_cidreq();
			$url=$www.$path;
		}
		//files that we want opened in a new window
		if($ext=='txt') //add here
		{
			$target='_blank';
		}
	}
	else 
	{
		$url=api_get_self().'?'.api_get_cidreq().'&amp;curdirpath='.$url_path.$req_gid;
	}
	//the little download icon
	$force_download_html = ($size==0)?'':'<a href="'.$forcedownload_link.'" style="float:right"'.$prevent_multiple_click.'>'.Display::return_icon($forcedownload_icon, get_lang('Download'),array('height'=>'16', 'width' => '16')).'</a>';
	
	$tooltip_title = str_replace('?cidReq='.$_GET['cidReq'],'',basename($path));
	return '<a href="'.$url.'" title="'.$tooltip_title.'" target="'.$target.'"'.$visibility_class.' style="float:left">'.$title.'</a>'.$force_download_html;
}

/**
 * Builds an img html tag for the filetype
 *
 * @param string $type (file/folder)
 * @param string $path
 * @return string img html tag
 */
function build_document_icon_tag($type,$path)
{
	$icon='folder_document.gif';
	if($type=='file')
	{
		$icon=choose_image(basename($path));
	}
	return Display::return_icon($icon, '', array('hspace'=>'5', 'align' => 'middle'));
}

/**
 * Creates the row of edit icons for a file/folder
 *
 * @param string $curdirpath current path (cfr open folder)
 * @param string $type (file/folder)
 * @param string $path dbase path of file/folder
 * @param int $visibility (1/0)
 * @param int $id dbase id of the document
 * @return string html img tags with hyperlinks
 */
function build_edit_icons($curdirpath,$type,$path,$visibility,$id,$is_template,$is_read_only=0)
{
	if(isset($_SESSION['_gid']))
	{
		$req_gid = '&amp;gidReq='.$_SESSION['_gid'];
	}
	else 
	{
		$req_gid = '';
	}
	//build URL-parameters for table-sorting
	$sort_params = array();
	if( isset($_GET['column']))
	{
		$sort_params[] = 'column='.$_GET['column'];
	}
	if( isset($_GET['page_nr']))
	{
		$sort_params[] = 'page_nr='.$_GET['page_nr'];
	}
	if( isset($_GET['per_page']))
	{
		$sort_params[] = 'per_page='.$_GET['per_page'];
	}
	if( isset($_GET['direction']))
	{
		$sort_params[] = 'direction='.$_GET['direction'];
	}	
	$sort_params = implode('&amp;',$sort_params);
	$visibility_icon = ($visibility==0)?'invisible':'visible';
	$visibility_command = ($visibility==0)?'set_visible':'set_invisible';
	$curdirpath = urlencode($curdirpath);
	
	$modify_icons = '';
		
	if ($is_read_only)
	{
		$modify_icons = Display::return_icon('edit_na.gif', get_lang('Modify'));		
        $modify_icons .= '&nbsp;'.Display::return_icon('delete.gif', get_lang('Delete'));
        $modify_icons .= '&nbsp;'.Display::return_icon('deplacer_fichier_na.gif', get_lang('Move'));
        $modify_icons .= '&nbsp;'.Display::return_icon($visibility_icon.'_na.gif', get_lang('Visible'));
	}
	else
	{
		$modify_icons = '<a href="edit_document.php?'.api_get_cidreq().'&curdirpath='.$curdirpath.'&amp;file='.urlencode($path).$req_gid.'"><img src="../img/edit.gif" border="0" title="'.get_lang('Modify').'" alt="" /></a>';
        if (strcmp($path,'/audio')===0 or strcmp($path,'/flash')===0 or strcmp($path,'/images')===0) {
        	$modify_icons .= '&nbsp;<img src="../img/delete_na.gif" border="0" title="'.get_lang('Delete').'" alt="'.get_lang('Delete').'" /></a>';
        } else {
        	$modify_icons .= '&nbsp;<a href="'.api_get_self().'?'.api_get_cidreq().'&curdirpath='.$curdirpath.'&amp;delete='.urlencode($path).$req_gid.'&amp;'.$sort_params.'" onclick="return confirmation(\''.basename($path).'\');"><img src="../img/delete.gif" border="0" title="'.get_lang('Delete').'" alt="" /></a>';
        }
        $modify_icons .= '&nbsp;<a href="'.api_get_self().'?'.api_get_cidreq().'&curdirpath='.$curdirpath.'&amp;move='.urlencode($path).$req_gid.'"><img src="../img/deplacer_fichier.gif" border="0" title="'.get_lang('Move').'" alt="" /></a>';
        $modify_icons .= '&nbsp;<a href="'.api_get_self().'?'.api_get_cidreq().'&curdirpath='.$curdirpath.'&amp;'.$visibility_command.'='.$id.$req_gid.'&amp;'.$sort_params.'"><img src="../img/'.$visibility_icon.'.gif" border="0" title="'.get_lang('Visible').'" alt="" /></a>';
	}	
	
	if($type == 'file' && pathinfo($path,PATHINFO_EXTENSION)=='html')
	{
		if($is_template==0)
		{
			$modify_icons .= '&nbsp;<a href="'.api_get_self().'?'.api_get_cidreq().'&curdirpath='.$curdirpath.'&amp;add_as_template='.$id.$req_gid.'&amp;'.$sort_params.'"><img src="../img/wizard_small.gif" border="0" title="'.get_lang('AddAsTemplate').'" alt="'.get_lang('AddAsTemplate').'" /></a>';
		}
		else{
			$modify_icons .= '&nbsp;<a href="'.api_get_self().'?'.api_get_cidreq().'&curdirpath='.$curdirpath.'&amp;remove_as_template='.$id.$req_gid.'&amp;'.$sort_params.'"><img src="../img/wizard_gray_small.gif" border="0" title="'.get_lang('RemoveAsTemplate').'" alt=""'.get_lang('RemoveAsTemplate').'" /></a>';
		}
	}	
	return $modify_icons;
}


function build_move_to_selector($folders,$curdirpath,$move_file,$group_dir='')
{
	$form = '<form name="move_to" action="'.api_get_self().'" method="post">'."\n";
	$form .= '<input type="hidden" name="move_file" value="'.$move_file.'" />'."\n";
	$form .= get_lang('MoveTo').' <select name="move_to">'."\n";
	
	//group documents cannot be uploaded in the root
	if($group_dir=='') 
	{
		if($curdirpath!='/')
		{
			$form .= '<option value="/">/ ('.get_lang('HomeDirectory').')</option>';
		}
		if(is_array($folders))
		{
			foreach ($folders AS $folder)
			{	
				//you cannot move a file to:
				//1. current directory
				//2. inside the folder you want to move
				//3. inside a subfolder of the folder you want to move
				if(($curdirpath!=$folder) && ($folder!=$move_file) && (substr($folder,0,strlen($move_file)+1) != $move_file.'/'))
				{
					$path_displayed = $folder;
					
					// if document title is used, we have to display titles instead of real paths...
					if(api_get_setting('use_document_title'))
					{
						$path_displayed = get_titles_of_path($folder);
					}
					$form .= '<option value="'.$folder.'">'.$path_displayed.'</option>'."\n";
				}
			}
		}
	}
	else
	{
		foreach ($folders AS $folder)
		{	
			if(($curdirpath!=$folder) && ($folder!=$move_file) && (substr($folder,0,strlen($move_file)+1) != $move_file.'/'))//cannot copy dir into his own subdir
			{
				if(api_get_setting('use_document_title'))
				{
					$path_displayed = get_titles_of_path($folder);
				}
				
				$display_folder = substr($path_displayed,strlen($group_dir));
				$display_folder = ($display_folder == '')?'/ ('.get_lang('HomeDirectory').')':$display_folder;
								
				$form .= '<option value="'.$folder.'">'.$display_folder.'</option>'."\n";
			}
		}
	}

	$form .= '</select>'."\n";
	$form .= '<input type="submit" name="move_file_submit" value="'.get_lang('Ok').'" />'."\n";
	$form .= '</form>';

	return $form;
}


/**
 * get the path translated with title of docs and folders
 * @param string the real path
 * @return the path which should be displayed
 */
function get_titles_of_path($path)
{
	global $tmp_folders_titles;

	$nb_slashes = substr_count($path,'/');
	$tmp_path = '';
	$current_slash_pos = 0;
	$path_displayed = '';
	for($i=0; $i<$nb_slashes; $i++)
	{ // foreach folders of the path, retrieve title.
	
		$current_slash_pos = strpos($path,'/',$current_slash_pos+1);
		$tmp_path = substr($path,strpos($path,'/',0),$current_slash_pos);
		
		if(empty($tmp_path)) // if empty, then we are in the final part of the path
			$tmp_path = $path;
			
		if(!empty($tmp_folders_titles[$tmp_path])) // if this path has soon been stored here we don't need a new query
		{
			$path_displayed .= $tmp_folders_titles[$tmp_path];
		}
		else
		{
			$sql = 'SELECT title FROM '.Database::get_course_table(TABLE_DOCUMENT).' WHERE path LIKE BINARY "'.$tmp_path.'"';
			$rs = api_sql_query($sql,__FILE__,__LINE__);
			$tmp_title = '/'.Database::result($rs,0,0);
			$path_displayed .= $tmp_title;
			$tmp_folders_titles[$tmp_path] = $tmp_title;
		}
	}
	return $path_displayed;
}



/**
* This function displays the name of the user and makes the link tothe user tool.
*
* @param $user_id 
* @param $name
* @return a link to the userInfo.php
* @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
* @version february 2006, dokeos 1.8
*/ 
function display_user_link_document($user_id, $name)
{	
	if ($user_id<>0)
	{
		return '<a href="../user/userInfo.php?uInfo='.$user_id.'">'.$name.'</a>';
	}
	else
	{
		return get_lang('Anonymous');
	}
}

?>