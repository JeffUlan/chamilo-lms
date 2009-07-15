<?php
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2008 Dokeos SPRL
	Copyright (c) 2003 Ghent University (UGent)

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
*	The Dokeos wiki is a further development of the CoolWiki plugin.
*
*	@Author Patrick Cool <patrick.cool@UGent.be>, Ghent University, Belgium
* 	@Author Juan Carlos Raña <herodoto@telefonica.net>
*	@Copyright Ghent University
*	@Copyright Patrick Cool
*
* 	@package dokeos.wiki
*/

/*
==============================================================================
FUNCTIONS FOR WIKI
==============================================================================
*/

// including the global dokeos file

/**
* @author Patrick Cool <patrick.cool@ugent.be>, Ghent University
* @desc This function checks weither the proposed reflink is not in use yet. It is a recursive function because every newly created reflink suggestion
*		has to be checked also
*/
function createreflink($testvalue)
{
	global $groupfilter;
	$counter='';
	while (!checktitle($testvalue.$counter))
	{
		$counter++; 
		echo $counter."-".$testvalue.$counter."<br />"; 
	
	}
			// the reflink has not been found yet, so it is OK
	return $testvalue.$counter;
}


/**
* @author Patrick Cool <patrick.cool@ugent.be>, Ghent University 
**/
function checktitle($paramwk) 
{
	global $tbl_wiki;
	global $groupfilter;

	$sql='SELECT * FROM '.$tbl_wiki.' WHERE reflink="'.html_entity_decode(Database::escape_string(stripslashes(urldecode($paramwk)))).'" AND '.$groupfilter.''; // TODO: check if need entity
	$result=api_sql_query($sql,__FILE__,__LINE__);
	$numberofresults=Database::num_rows($result);

	if ($numberofresults==0) // the value has not been found and is this available
	{
		return true;
	}
	else // the value has been found
	{
		return false;
	}
}


/**
* @author Juan Carlos Raña <herodoto@telefonica.net>
* check wikilinks that has a page
**/
function links_to($input)
{
    $input_array=preg_split("/(\[\[|\]\])/",$input,-1, PREG_SPLIT_DELIM_CAPTURE);
    $all_links = array();
	
	foreach ($input_array as $key=>$value)
	{
		
		if ($input_array[$key-1]=='[[' AND $input_array[$key+1]==']]')
		{
		
		    if (strpos($value, "|") != false)
			{
			 	$full_link_array=explode("|", $value);			
				$link=trim($full_link_array[0]);
				$title=trim($full_link_array[1]);				
			}
		    else
			{
				$link=$value;
				$title=$value;
	        }
		
			unset($input_array[$key-1]);
			unset($input_array[$key+1]);
			
			$all_links[]= Database::escape_string(str_replace(' ','_',$link)).' ';	//replace blank spaces by _ within the links. But to remove links at the end add a blank space
		}

    }
	
	$output=implode($all_links);
	return $output;
	
}

/*
detect and add style to external links
author Juan Carlos Raña Trabado
**/
function detect_external_link($input)
{
	$exlink='href=';
	$exlinkStyle='class="wiki_link_ext" href=';	
	$output=str_replace($exlink, $exlinkStyle, $input);		
	return $output;
}

/*
detect and add style to anchor links
author Juan Carlos Raña Trabado
**/
function detect_anchor_link($input)
{
	$anchorlink='href="#';
	$anchorlinkStyle='class="wiki_anchor_link" href="#';	
	$output=str_replace($anchorlink, $anchorlinkStyle, $input);		
	return $output;
}

/*
detect and add style to mail links
author Juan Carlos Raña Trabado
**/
function detect_mail_link($input)
{
	$maillink='href="mailto';
	$maillinkStyle='class="wiki_mail_link" href="mailto';	
	$output=str_replace($maillink, $maillinkStyle, $input);		
	return $output;
}

/*
detect and add style to ftp links
author Juan Carlos Raña Trabado
**/
function detect_ftp_link($input)
{
	$ftplink='href="ftp';
	$ftplinkStyle='class="wiki_ftp_link" href="ftp';	
	$output=str_replace($ftplink, $ftplinkStyle, $input);		
	return $output;
}

/*
detect and add style to news links
author Juan Carlos Raña Trabado
**/
function detect_news_link($input)
{
	$newslink='href="news';
	$newslinkStyle='class="wiki_news_link" href="news';	
	$output=str_replace($newslink, $newslinkStyle, $input);		
	return $output;
}

/*
detect and add style to irc links
author Juan Carlos Raña Trabado
**/
function detect_irc_link($input)
{
	$irclink='href="irc';
	$irclinkStyle='class="wiki_irc_link" href="irc';	
	$output=str_replace($irclink, $irclinkStyle, $input);		
	return $output;
}


/*
* This function allows users to have [link to a title]-style links like in most regular wikis.
* It is true that the adding of links is probably the most anoying part of Wiki for the people
* who know something about the wiki syntax.
* @author Patrick Cool <patrick.cool@ugent.be>, Ghent University
* Improvements [[]] and [[ | ]]by Juan Carlos Raña
* Improvements internal wiki style and mark group by Juan Carlos Raña
**/
function make_wiki_link_clickable($input)
{
	if (isset($_SESSION['_gid']))
	{
		$_clean['group_id']=(int)$_SESSION['_gid'];
	}
	if (isset($_GET['group_id']))
	{
		$_clean['group_id']=(int)Security::remove_XSS($_GET['group_id']);
	}


	$input_array=preg_split("/(\[\[|\]\])/",$input,-1, PREG_SPLIT_DELIM_CAPTURE); //now doubles brackets

	foreach ($input_array as $key=>$value)
	{
		
		if ($input_array[$key-1]=='[[' AND $input_array[$key+1]==']]') //now doubles brackets
		{
		
		/////////// TODO: metawiki
		/*
		    if ($_clean['group_id']==0) 
			{
				$titleg_ex='';
			}
			else
			{
				$group_properties  = GroupManager :: get_group_properties($_clean['group_id']);	
				$group_name= $group_properties['name'];
				$titleg_ex='<sup><img src="css/wgroup.gif" alt="('.$group_name.')" title="Link to Wikigroup:'.$group_name.'"/></sup>';
		    }	
		*/
		/////////
					
			//now full wikilink
			if (strpos($value, "|") != false)
			 {
			 	$full_link_array=explode("|", $value);			
				$link=trim($full_link_array[0]);
				$title=trim($full_link_array[1]);				
		      }
		      else
			  {
				$link=$value;
				$title=$value;
	          }
			  
			//if wikilink is homepage			
			if($link=='index'){
				$title=get_lang('DefaultTitle');				
			}
			if ($link==get_lang('DefaultTitle')){
				$link='index';
			}
			
		
			// note: checkreflink checks if the link is still free. If it is not used then it returns true, if it is used, then it returns false. Now the title may be different
			if (checktitle(strtolower(str_replace(' ','_',$link))))
			{			
				$input_array[$key]='<a href="'.api_get_path(WEB_PATH).'main/wiki/index.php?cidReq='.$_course[id].'&action=addnew&amp;title='.urldecode($link).'&group_id='.$_clean['group_id'].'" class="new_wiki_link">'.$title.$titleg_ex.'</a>';		
			}
			else 
			{				
						
				$input_array[$key]='<a href="'.api_get_path(WEB_PATH).'main/wiki/index.php?cidReq='.$_course[id].'&action=showpage&amp;title='.strtolower(str_replace(' ','_',$link)).'&group_id='.$_clean['group_id'].'" class="wiki_link">'.$title.$titleg_ex.'</a>';
			}
			unset($input_array[$key-1]);
			unset($input_array[$key+1]);
		}
	}
	$output=implode('',$input_array);
	return $output;
}


/**
* This function saves a change in a wiki page
* @author Patrick Cool <patrick.cool@ugent.be>, Ghent University
* @return language string saying that the changes are stored
**/
function save_wiki() {
	global $charset;
	global $tbl_wiki;
	
	// NOTE: visibility, visibility_disc and ratinglock_disc changes are not made here, but through the interce buttons
	
	// cleaning the variables
	$_clean['page_id']=Database::escape_string($_POST['page_id']);
	$_clean['reflink']=Database::escape_string(Security::remove_XSS($_POST['reflink']));
	$_clean['title']=Database::escape_string(Security::remove_XSS($_POST['title']));
	$_clean['content']= Database::escape_string(Security::remove_XSS(stripslashes(api_html_entity_decode($_POST['content'])),COURSEMANAGERLOWSECURITY));
	$_clean['user_id']=(int)Database::escape_string(api_get_user_id());
	$_clean['assignment']=Database::escape_string($_POST['assignment']);
    $_clean['comment']=Database::escape_string(Security::remove_XSS($_POST['comment']));	
    $_clean['progress']=Database::escape_string($_POST['progress']);	
	$_clean['version']=Database::escape_string($_POST['version'])+1;
	$_clean['linksto'] = links_to($_clean['content']); //and check links content	
	$dtime = date( "Y-m-d H:i:s" );

	if (isset($_SESSION['_gid']))
    {
	  	$_clean['group_id']=Database::escape_string($_SESSION['_gid']);
    }
    if (isset($_GET['group_id']))
    {
 	   	$_clean['group_id']=Database::escape_string($_GET['group_id']);
    }
	
	$sql="INSERT INTO ".$tbl_wiki." (page_id, reflink, title, content, user_id, group_id, dtime, assignment, comment, progress, version, linksto, user_ip) VALUES ('".$_clean['page_id']."','".$_clean['reflink']."','".$_clean['title']."','".$_clean['content']."','".$_clean['user_id']."','".$_clean['group_id']."','".$dtime."','".$_clean['assignment']."','".$_clean['comment']."','".$_clean['progress']."','".$_clean['version']."','".$_clean['linksto']."','".Database::escape_string($_SERVER['REMOTE_ADDR'])."')";
	
	$result=api_sql_query($sql);	
    $Id = Database::insert_id();
	
	if ($_clean['page_id']	==0)   
	{	    
		$sql='UPDATE '.$tbl_wiki.' SET page_id="'.$Id.'" WHERE id="'.$Id.'"';		 	
		api_sql_query($sql,__FILE__,__LINE__);
	}
				
	api_item_property_update($_course, 'wiki', $Id, 'WikiAdded', api_get_user_id());
	
	check_emailcue($_clean['reflink'], 'P', $dtime, $_clean['user_id']);
	
	return get_lang('ChangesStored');
}

/**
* This function restore a wikipage
* @author Juan Carlos Raña <herodoto@telefonica.net>
**/
function restore_wikipage($r_page_id, $r_reflink, $r_title, $r_content, $r_group_id, $r_assignment, $r_progress, $c_version, $r_version, $r_linksto)
{

	global $tbl_wiki;
	
	$r_user_id= api_get_user_id();
	$r_dtime = date( "Y-m-d H:i:s" );
	$r_version = $r_version+1;
	$r_comment = get_lang('RestoredFromVersion').': '.$c_version;
	
	$sql="INSERT INTO ".$tbl_wiki." (page_id, reflink, title, content, user_id, group_id, dtime, assignment, comment, progress, version, linksto, user_ip) VALUES ('".$r_page_id."','".$r_reflink."','".$r_title."','".$r_content."','".$r_user_id."','".$r_group_id."','".$r_dtime."','".$r_assignment."','".$r_comment."','".$r_progress."','".$r_version."','".$r_linksto."','".Database::escape_string($_SERVER['REMOTE_ADDR'])."')";
	
	$result=api_sql_query($sql);	
    $Id = Database::insert_id();		
	api_item_property_update($_course, 'wiki', $Id, 'WikiAdded', api_get_user_id());
	
	check_emailcue($r_reflink, 'P', $r_dtime, $r_user_id);
	
	return get_lang('PageRestored');
}

/**
* This function delete a wiki
* @author Juan Carlos Raña <herodoto@telefonica.net>
**/

function delete_wiki()
{

	global $tbl_wiki, $tbl_wiki_discuss, $tbl_wiki_mailcue, $groupfilter;
	//identify the first id by group = identify wiki
	$sql='SELECT * FROM '.$tbl_wiki.'  WHERE  '.$groupfilter.' ORDER BY id DESC';
	$allpages=api_sql_query($sql,__FILE__,__LINE__);
		
	while ($row=Database::fetch_array($allpages))	{
		$id 		= $row['id'];
		$group_id	= $row['group_id'];
	}

	api_sql_query('DELETE FROM '.$tbl_wiki_discuss.' WHERE publication_id="'.$id.'"' ,__FILE__,__LINE__);
	api_sql_query('DELETE FROM '.$tbl_wiki_mailcue.' WHERE group_id="'.$group_id.'"' ,__FILE__,__LINE__);	
	api_sql_query('DELETE FROM '.$tbl_wiki.' WHERE '.$groupfilter.'',__FILE__,__LINE__);	
	return get_lang('WikiDeleted');
}


/**
* This function saves a new wiki page.
* @author Patrick Cool <patrick.cool@ugent.be>, Ghent University
* @todo consider merging this with the function save_wiki into one single function.
**/
function save_new_wiki() {
	global $charset;
	global $tbl_wiki;
    global $assig_user_id; //need for assignments mode
	
	// cleaning the variables
	$_clean['assignment']=Database::escape_string($_POST['assignment']);
			
	if($_clean['assignment']==2 || $_clean['assignment']==1) {// Unlike ordinary pages of pages of assignments. Allow create a ordinary page although there is a assignment with the same name
		$_clean['reflink']=Database::escape_string(Security::remove_XSS(str_replace(' ','_',$_POST['title']."_uass".$assig_user_id)));			
    } else {		
	 	$_clean['reflink']=Database::escape_string(Security::remove_XSS(str_replace(' ','_',$_POST['title'])));			
	}	

	$_clean['title']=Database::escape_string(Security::remove_XSS($_POST['title']));		    
	$_clean['content']= Database::escape_string(Security::remove_XSS(stripslashes(api_html_entity_decode($_POST['content'])),COURSEMANAGERLOWSECURITY));
	
	if($_clean['assignment']==2)  {//config by default for individual assignment (students)	 
	
	 	$_clean['user_id']=(int)Database::escape_string($assig_user_id);//Identifies the user as a creator, not the teacher who created
		
		$_clean['visibility']=0;
		$_clean['visibility_disc']=0;
		$_clean['ratinglock_disc']=0;
		
	} else {
	 	$_clean['user_id']=(int)Database::escape_string(api_get_user_id());
		
		$_clean['visibility']=1;
		$_clean['visibility_disc']=1;
		$_clean['ratinglock_disc']=1;		
		
	}	

	$_clean['comment']=Database::escape_string(Security::remove_XSS($_POST['comment']));
	$_clean['progress']=Database::escape_string($_POST['progress']);
	$_clean['version']=1;
		
	if (isset($_SESSION['_gid']))
	  {
	  $_clean['group_id']=(int)$_SESSION['_gid'];
	}
	if (isset($_GET['group_id']))
	  {
	   $_clean['group_id']=(int)Database::escape_string($_GET['group_id']);
	}		   
	
	$_clean['linksto'] = links_to($_clean['content']);	//check wikilinks
	
	//filter no _uass
	if (api_eregi('_uass', $_POST['title']) || (api_strtoupper(trim($_POST['title'])) == 'INDEX' || api_strtoupper(trim(api_htmlentities($_POST['title'], ENT_QUOTES, $charset))) == api_strtoupper(api_htmlentities(get_lang('DefaultTitle'), ENT_QUOTES, $charset)))) {
		$message= get_lang('GoAndEditMainPage');
		Display::display_warning_message($message,false);
	} else {		
	
		$var=$_clean['reflink'];
	
		$group_id=Security::remove_XSS($_GET['group_id']);
		if(!checktitle($var)) {
		   return get_lang('WikiPageTitleExist').'<a href="index.php?action=edit&amp;title='.$var.'&group_id='.$group_id.'">'.$_POST['title'].'</a>'; 
		} else { 	
			$dtime = date( "Y-m-d H:i:s" );
			$sql="INSERT INTO ".$tbl_wiki." (reflink, title, content, user_id, group_id, dtime, visibility, visibility_disc, ratinglock_disc, assignment, comment, progress, version, linksto, user_ip) VALUES ('".$_clean['reflink']."','".$_clean['title']."','".$_clean['content']."','".$_clean['user_id']."','".$_clean['group_id']."','".$dtime."','".$_clean['visibility']."','".$_clean['visibility_disc']."','".$_clean['ratinglock_disc']."','".$_clean['assignment']."','".$_clean['comment']."','".$_clean['progress']."','".$_clean['version']."','".$_clean['linksto']."','".Database::escape_string($_SERVER['REMOTE_ADDR'])."')";
		   $result=api_sql_query($sql,__LINE__,__FILE__);
		   $Id = Database::insert_id();
		   
		   $sql='UPDATE '.$tbl_wiki.' SET page_id="'.$Id.'" WHERE id="'.$Id.'"';		 	
	       api_sql_query($sql,__FILE__,__LINE__);
		   	
		   api_item_property_update($_course, 'wiki', $Id, 'WikiAdded', api_get_user_id());
		   check_emailcue(0, 'A');
		   return get_lang('NewWikiSaved').' <a href="index.php?action=showpage&amp;title='.$_clean['reflink'].'&group_id='.$group_id.'">'.$_POST['title'].'</a>'; 
		} 
	}//end filter no _uass
}


/**
* This function displays the form for adding a new wiki page.
* @author Patrick Cool <patrick.cool@ugent.be>, Ghent University
* @return html code
**/
function display_new_wiki_form()
{
	echo '<form name="form1" method="post" action="'.api_get_self().'?cidReq='.$_course[id].'&action=showpage&amp;title='.$page.'&group_id='.Security::remove_XSS($_GET['group_id']).'">';
	echo '<div id="wikititle">';
	echo  '<span class="form_required">*</span> '.get_lang(Title).': <input type="text" name="title" value="'.urldecode($_GET['title']).'">';
	
	if(api_is_allowed_to_edit() || api_is_platform_admin())
	{	
	
		$_clean['group_id']=(int)$_SESSION['_gid']; // TODO: check if delete ?
		
		echo '&nbsp;<img src="../img/wiki/assignment.gif" />&nbsp;'.get_lang('DefineAssignmentPage').'&nbsp;<input type="checkbox" name="assignment" value="1">'; // 1= teacher 2 =student
			
			//by now turned off			
			//echo'<div style="border:groove">';			
			//echo '&nbsp;'.get_lang('StartDate').': <INPUT TYPE="text" NAME="startdate_assig" VALUE="0000-00-00 00:00:00">(yyyy-mm-dd hh:mm:ss)'; //by now turned off
			//echo '&nbsp;'.get_lang('EndDate').': <INPUT TYPE="text" NAME="enddate_assig" VALUE="0000-00-00 00:00:00">(yyyy-mm-dd hh:mm:ss)'; //by now turned off				
		    //echo '<br />&nbsp;'.get_lang('AllowLaterSends').'&nbsp;<INPUT TYPE="checkbox" NAME="delayedsubmit" VALUE="0">'; //by now turned off		
			//echo'</div>';			
	}
	echo '</div>';
	echo '<div id="wikicontent">';
	api_disp_html_area('content', '', '', '', null, api_is_allowed_to_edit()
		? array('ToolbarSet' => 'Wiki', 'Width' => '100%', 'Height' => '400')
		: array('ToolbarSet' => 'Wiki_Student', 'Width' => '100%', 'Height' => '400', 'UserStatus' => 'student')
	); 	
	echo '<br/>';
	echo '<br/>'; 	
	echo get_lang('Comments').':&nbsp;&nbsp;<input type="text" name="comment" value="'.stripslashes($row['comment']).'"><br /><br />';
	echo get_lang('Progress').':&nbsp;&nbsp;<select name="progress" id="progress">
	   <option value="0" selected>0</option>	   
	   <option value="10">10</option>
	   <option value="20">20</option>
	   <option value="30">30</option>
	   <option value="40">40</option>
	   <option value="50">50</option>
	   <option value="60">60</option>
	   <option value="70">70</option>
	   <option value="80">80</option>
	   <option value="90">90</option>
	   <option value="100">100</option>   
	   </select> %';
	echo '<br/><br/>'; 
	echo '<input type="hidden" name="wpost_id" value="'.md5(uniqid(rand(), true)).'">';//prevent double post
	echo '<input type="hidden" name="SaveWikiNew" value="'.get_lang('langSave').'">'; //for save icon
	echo '<button class="save" type="submit" name="SaveWikiNew">'.get_lang('langSave').'</button>';//for button icon
	echo '</div>';
	echo '</form>';
}

/**
* This function displays a wiki entry
* @author Patrick Cool <patrick.cool@ugent.be>, Ghent University
* @return html code
**/
function display_wiki_entry()
{
	global $charset;
	global $tbl_wiki;
	global $groupfilter;
	global $page;

   
	$_clean['group_id']=(int)$_SESSION['_gid']; 
	if ($_GET['view'])
	{
		$_clean['view']=(int)Database::escape_string($_GET['view']);
		$filter=" AND id='".$_clean['view']."'";
	}	

	//first, check page visibility in the first page version
	$sql='SELECT * FROM '.$tbl_wiki.'WHERE reflink="'.html_entity_decode(Database::escape_string(stripslashes(urldecode($page)))).'" AND '.$groupfilter.' ORDER BY id ASC';
		$result=api_sql_query($sql,__LINE__,__FILE__);	
		$row=Database::fetch_array($result);		
		$KeyVisibility=$row['visibility'];	

	// second, show the last version
	$sql="SELECT * FROM ".$tbl_wiki."WHERE reflink='".html_entity_decode(Database::escape_string(stripslashes(urldecode($page))))."' AND $groupfilter $filter ORDER BY id DESC";
		$result=api_sql_query($sql,__LINE__,__FILE__);
		$row=Database::fetch_array($result); // we do not need a while loop since we are always displaying the last version	


	//update visits 
	if($row['id'])
	{
		$sql='UPDATE '.$tbl_wiki.' SET hits=(hits+1) WHERE id='.$row['id'].'';
		api_sql_query($sql,__FILE__,__LINE__);
	}


	// if both are empty and we are displaying the index page then we display the default text.
	if ($row['content']=='' AND $row['title']=='' AND $page=='index')
	{
		if(api_is_allowed_to_edit() || api_is_platform_admin() || GroupManager :: is_user_in_group(api_get_user_id(),$_SESSION['_gid'])) 
		{
			$content=sprintf(get_lang('DefaultContent'),api_get_path(WEB_IMG_PATH));
			$title=get_lang('DefaultTitle');
		}
		else
		{		
			return Display::display_normal_message(get_lang('WikiStandBy'));
		}
	}
	else
	{
  		$content=Security::remove_XSS($row['content'],COURSEMANAGERLOWSECURITY);
		$title= Security::remove_XSS($row['title']);
	}
	

	//assignment mode: for identify page type
	if(stripslashes($row['assignment'])==1)
	{
		$icon_assignment='<img src="../img/wiki/assignment.gif" title="'.get_lang('AssignmentDescExtra').'" alt="'.get_lang('AssignmentDescExtra').'" />';
	}
	elseif(stripslashes($row['assignment'])==2)
	{
		$icon_assignment='<img src="../img/wiki/works.gif" title="'.get_lang('AssignmentWorkExtra').'" alt="'.get_lang('AssignmentWorkExtra').'" />';
	}



	//Show page. Show page to all users if isn't hide page. Mode assignments: if student is the author, can view
	if($KeyVisibility=="1" || api_is_allowed_to_edit() || api_is_platform_admin() || ($row['assignment']==2 && $KeyVisibility=="0" && (api_get_user_id()==$row['user_id'])))
	{
		echo '<div id="wikititle">';
	
		// page action: protecting (locking) the page
	if (check_protect_page())
	{	
		if(api_is_allowed_to_edit() || api_is_platform_admin())
		{
				$protect_page= '<img src="../img/wiki/lock.gif" title="'.get_lang('PageLockedExtra').'" alt="'.get_lang('PageLockedExtra').'" />';
		}
		else
		{
				$protect_page= '<img src="../img/wiki/lock.gif" title="'.get_lang('PageLockedExtra').'" alt="'.get_lang('PageLockedExtra').'" />';
		}
	}
	else
	{					  
		if(api_is_allowed_to_edit() || api_is_platform_admin()) 
	   	{
				$protect_page= '<img src="../img/wiki/unlock.gif" title="'.get_lang('PageUnlockedExtra').'" alt="'.get_lang('PageUnlockedExtra').'" />';
	   	}   	
	}	
		echo '<span style="float:right">';
		echo '<a href="index.php?action=showpage&amp;actionpage=lock&amp;title='.$page.'">'.$protect_page.'</a>';
		echo '</span>';

		//page action: visibility
	if (check_visibility_page())
	{
		//This hides the icon eye closed to users of work they can see yours
		if(($row['assignment']==2 && $KeyVisibility=="0" && (api_get_user_id()==$row['user_id']))==false)
	  	{	  
				$visibility_page= '<img src="../img/wiki/invisible.gif" title="'.get_lang('HidePageExtra').'" alt="'.get_lang('HidePageExtra').'" />';
	    }
	}
	else
	{			  
		if(api_is_allowed_to_edit() || api_is_platform_admin()) 
		{
				$visibility_page= '<img src="../img/wiki/visible.gif" title="'.get_lang('ShowPageExtra').'" alt="'.get_lang('ShowPageExtra').'" />';
		}	     	
	}		
		echo '<span style="float:right">';
		echo '<a href="index.php?action=showpage&amp;actionpage=visibility&amp;title='.$page.'">'.$visibility_page.'</a>';
		echo '</span>';
	
		//page action: notification
	if (check_notify_page($page))
	{
			$notify_page= '<img src="../img/wiki/send_mail_checked.gif" title="'.get_lang('NotifyByEmail').'" alt="'.get_lang('NotifyByEmail').'" />';
		}
		else
		{
			$notify_page= '<img src="../img/wiki/send_mail.gif" title="'.get_lang('CancelNotifyByEmail').'" alt="'.get_lang('CancelNotifyByEmail').'" />';
		}
		echo '<span style="float:right">';
		echo '<a href="index.php?action=showpage&amp;actionpage=notify&amp;title='.$page.'">'.$notify_page.'</a>';
		echo '</span>';
		
		//page action: export to pdf
		echo '<span style="float:right">';
		echo '<form name="form_export2PDF" method="post" action="export_html2pdf.php" target="_blank, fullscreen">'; // also with  export_tcpdf.php
		echo '<input type=hidden name="titlePDF" value="'.api_htmlentities($title, ENT_QUOTES, $charset).'">';
		echo '<input type=hidden name="contentPDF" value="'.api_htmlentities(trim(preg_replace("/\[\[|\]\]/", " ", $content)), ENT_QUOTES, $charset).'">';
		echo '<input type="image" src="../img/wiki/wexport2pdf.gif" border ="0" title="'.get_lang('ExportToPDF').'" alt="'.get_lang('ExportToPDF').'" style=" border:none;">';
		echo '</form>';
		echo '</span>';

		//page action: copy last version to doc area
		if(api_is_allowed_to_edit() || api_is_platform_admin())
		{
			echo '<span style="float:right;">';				
			echo '<form name="form_export2DOC" method="post" action="index.php">';
			echo '<input type=hidden name="export2DOC" value="export2doc">';
			echo '<input type=hidden name="titleDOC" value="'.api_htmlentities($title, ENT_QUOTES, $charset).'">';
			echo '<input type=hidden name="contentDOC" value="'.api_htmlentities($content, ENT_QUOTES, $charset).'">';
			echo '<input type="image" src="../img/wiki/wexport2doc.png" border ="0" title="'.get_lang('ExportToDocArea').'" alt="'.get_lang('ExportToDocArea').'" style=" border:none;">';
			echo '</form>';
			echo '</span>';	
		}
		//export to print
		?>	
    	
		<script>
        function goprint()
        {
            var a = window.open('','','width=800,height=600');
            a.document.open("text/html");
            a.document.write(document.getElementById('wikicontent').innerHTML);
            a.document.close();
            a.print();
        }
        </script>
		<?php				
		echo '<span style="float:right; cursor: pointer;">';
		echo '<img src="../img/wiki/wprint.gif" title="'.get_lang('Print').'" alt="'.get_lang('Print').'" onclick="javascript: goprint();">';
		echo '</span>';
		

		if (empty($title))
		{
			$title=get_lang('DefaultTitle');

		}

		if (wiki_exist($title))
		{
			echo $icon_assignment.'&nbsp;&nbsp;&nbsp;'.stripslashes($title);
		}
		else
		{
			echo stripslashes($title);
		}
			
			echo '</div>';	
			echo '<div id="wikicontent">'. make_wiki_link_clickable(detect_external_link(detect_anchor_link(detect_mail_link(detect_ftp_link(detect_irc_link(detect_news_link(stripslashes($content)))))))).'</div>';

		echo '<div id="wikifooter">'.get_lang('Progress').': '.stripslashes($row['progress']).'%&nbsp;&nbsp;&nbsp;'.get_lang('Rating').': '.stripslashes($row['score']).'&nbsp;&nbsp;&nbsp;'.get_lang('Words').': '.word_count($content).'</div>';

	}//end filter visibility
} // end function display_wiki_entry


//more for export to course document area. See display_wiki_entry
if ($_POST['export2DOC'])
{ 
	$titleDOC=$_POST['titleDOC'];
	$contentDOC=$_POST['contentDOC'];
	$groupIdDOC=$_clean['group_id'];
	export2doc($titleDOC,$contentDOC,$groupIdDOC); 
}

/**
* This function counted the words in a document. Thanks Adeel Khan
*/

function word_count($document) {

	$search = array(
	'@<script[^>]*?>.*?</script>@si',
	'@<style[^>]*?>.*?</style>@siU',
	'@<![\s\S]*?--[ \t\n\r]*>@'
	);
	
    $document = preg_replace($search, '', $document);

  	# strip all html tags
  	$wc = strip_tags($document);

  	# remove 'words' that don't consist of alphanumerical characters or punctuation
  	$pattern = "#[^(\w|\d|\'|\"|\.|\!|\?|;|,|\\|\/|\-|:|\&|@)]+#";
  	$wc = trim(preg_replace($pattern, " ", $wc));

  	# remove one-letter 'words' that consist only of punctuation
  	$wc = trim(preg_replace("#\s*[(\'|\"|\.|\!|\?|;|,|\\|\/|\-|:|\&|@)]\s*#", " ", $wc));

  	# remove superfluous whitespace
  	$wc = preg_replace("/\s\s+/", " ", $wc);

  	# split string into an array of words
  	$wc = explode(" ", $wc);

  	# remove empty elements
  	$wc = array_filter($wc);
  
  	# return the number of words
 	 return count($wc);

}

/**
 * This function checks if wiki title exist
 */

function wiki_exist($title)
{
	global $tbl_wiki;
	global $groupfilter;
	$sql='SELECT id FROM '.$tbl_wiki.'WHERE title="'.Database::escape_string($title).'" AND '.$groupfilter.' ORDER BY id ASC'; 
	$result=api_sql_query($sql,__LINE__,__FILE__);
	$cant=Database::num_rows($result);
	if ($cant>0)
		return true;
	else 
		return false;
}

/**
* This function a wiki warning
* @author Patrick Cool <patrick.cool@ugent.be>, Ghent University
* @return html code
**/
function display_wiki_warning($variable)
{
	echo '<div class="wiki_warning">'.$variable.'</div>';
}


/**
 * Checks if this navigation tab has to be set to active
 * @author Patrick Cool <patrick.cool@ugent.be>, Ghent University
 * @return html code
 */
function is_active_navigation_tab($paramwk) 
{
	if ($_GET['action']==$paramwk) 
	{
		return ' class="active"';
	}
}


/**
 * Lock add pages
 * @author Juan Carlos Raña <herodoto@telefonica.net>
 */

function check_addnewpagelock() 
{

	global $tbl_wiki;
	global $groupfilter;

	$_clean['group_id']=(int)$_SESSION['_gid'];
		
	$sql='SELECT * FROM '.$tbl_wiki.'WHERE '.$groupfilter.' ORDER BY id ASC'; 
	$result=api_sql_query($sql,__LINE__,__FILE__);
	$row=Database::fetch_array($result); 
							
	$status_addlock=$row['addlock'];
			
	//change status
	if ($_GET['actionpage']=='addlock' && (api_is_allowed_to_edit() || api_is_platform_admin())) 
	{	
		if ($row['addlock']==1)
		{
			$status_addlock=0;
		}
		else
		{
			$status_addlock=1; 
		}       
		
		api_sql_query('UPDATE '.$tbl_wiki.' SET addlock="'.Database::escape_string($status_addlock).'" WHERE '.$groupfilter.'',__LINE__,__FILE__);	
	  
		$sql='SELECT * FROM '.$tbl_wiki.'WHERE '.$groupfilter.' ORDER BY id ASC'; 
		$result=api_sql_query($sql,__LINE__,__FILE__);
		$row=Database::fetch_array($result);
	} 
	 		
	//show status				
				
	if ($row['addlock']==1 || ($row['content']=='' AND $row['title']=='' AND $page=='index'))
	{
		return false;		
	}
	else
	{
		return true;				
	}		
}


/**
 * Protect page
 * @author Juan Carlos Raña <herodoto@telefonica.net>
 */
function check_protect_page() 
{
	global $tbl_wiki;
	global $page;
	global $groupfilter;

	$_clean['group_id']=(int)$_SESSION['_gid'];

	$sql='SELECT * FROM '.$tbl_wiki.'WHERE reflink="'.html_entity_decode(Database::escape_string(stripslashes(urldecode($page)))).'" AND '.$groupfilter.' ORDER BY id ASC';
		
	$result=api_sql_query($sql,__LINE__,__FILE__);
	$row=Database::fetch_array($result);
			
	$status_editlock=$row['editlock'];
	$id=$row['id'];	

	///change status
	if ($_GET['actionpage']=='lock' && (api_is_allowed_to_edit() || api_is_platform_admin())) 
    {		 	 
	    if ($row['editlock']==0)
	    {
	    	$status_editlock=1;
	    }
	    else
		{
	 		$status_editlock=0; 
	 	}
		
		$sql='UPDATE '.$tbl_wiki.' SET editlock="'.Database::escape_string($status_editlock).'" WHERE id="'.$id.'"';			   
	    api_sql_query($sql,__FILE__,__LINE__); 
	  
	    $sql='SELECT * FROM '.$tbl_wiki.'WHERE reflink="'.html_entity_decode(Database::escape_string(stripslashes(urldecode($page)))).'" AND '.$groupfilter.' ORDER BY id ASC';
		
	    $result=api_sql_query($sql,__LINE__,__FILE__);
	    $row=Database::fetch_array($result); 

	}
	 				
	//show status	
	if ($row['editlock']==0 || ($row['content']=='' AND $row['title']=='' AND $page=='index'))
	{
	 	return false;
	}
	else
	{
	 	return true;				
	}    
		
}


/**
 * Visibility page
 * @author Juan Carlos Raña <herodoto@telefonica.net>
 */
function check_visibility_page() 
{

	global $tbl_wiki;
	global $page;
	global $groupfilter;

	$_clean['group_id']=(int)$_SESSION['_gid'];

	$sql='SELECT * FROM '.$tbl_wiki.'WHERE reflink="'.html_entity_decode(Database::escape_string(stripslashes(urldecode($page)))).'" AND '.$groupfilter.' ORDER BY id ASC';
	$result=api_sql_query($sql,__LINE__,__FILE__);
	$row=Database::fetch_array($result);
				
	$status_visibility=$row['visibility'];
	$id=$row['id'];	//need ? check. TODO
		
	//change status
	if ($_GET['actionpage']=='visibility' && (api_is_allowed_to_edit() || api_is_platform_admin())) 
	{	
		if ($row['visibility']==1)
	    {
	    	$status_visibility=0;
	    }
	    else
		{
		 	$status_visibility=1; 
		}       
				
		$sql='UPDATE '.$tbl_wiki.' SET visibility="'.Database::escape_string($status_visibility).'" WHERE reflink="'.html_entity_decode(Database::escape_string(stripslashes(urldecode($page)))).'" AND '.$groupfilter; 
	    api_sql_query($sql,__FILE__,__LINE__); 
		
	    //Although the value now is assigned to all (not only the first), these three lines remain necessary. They do that by changing the page state is made when you press the button and not have to wait to change his page
	    $sql='SELECT * FROM '.$tbl_wiki.'WHERE reflink="'.html_entity_decode(Database::escape_string(stripslashes(urldecode($page)))).'" AND '.$groupfilter.' ORDER BY id ASC';
	    $result=api_sql_query($sql,__LINE__,__FILE__);
	    $row=Database::fetch_array($result); 

     } 
	 			
	//show status
	if ($row['visibility']=="1" || ($row['content']=='' AND $row['title']=='' AND $page=='index'))
	{
		return false;
	}
	else
	{
		return true;				
	}    
		
}


/**
 * Visibility discussion
 * @author Juan Carlos Raña <herodoto@telefonica.net>
 */
function check_visibility_discuss() 
{

	global $tbl_wiki;
	global $page;
	global $groupfilter;

	$_clean['group_id']=(int)$_SESSION['_gid'];

	$sql='SELECT * FROM '.$tbl_wiki.'WHERE reflink="'.html_entity_decode(Database::escape_string(stripslashes(urldecode($page)))).'" AND '.$groupfilter.' ORDER BY id ASC';
	$result=api_sql_query($sql,__LINE__,__FILE__);
	$row=Database::fetch_array($result);
				
	$status_visibility_disc=$row['visibility_disc'];
	$id=$row['id'];	//need ? check. TODO	
		
	//change status
	if ($_GET['actionpage']=='visibility_disc' && (api_is_allowed_to_edit() || api_is_platform_admin())) 
	{	
		if ($row['visibility_disc']==1)
	    {
	    	$status_visibility_disc=0;
	    }
	    else
		{
			$status_visibility_disc=1; 
		}       
		
		$sql='UPDATE '.$tbl_wiki.' SET visibility_disc="'.Database::escape_string($status_visibility_disc).'" WHERE reflink="'.html_entity_decode(Database::escape_string(stripslashes(urldecode($page)))).'" AND '.$groupfilter;
	    api_sql_query($sql,__FILE__,__LINE__); 
		
	   //Although the value now is assigned to all (not only the first), these three lines remain necessary. They do that by changing the page state is made when you press the button and not have to wait to change his page
	    $sql='SELECT * FROM '.$tbl_wiki.'WHERE reflink="'.html_entity_decode(Database::escape_string(stripslashes(urldecode($page)))).'" AND '.$groupfilter.' ORDER BY id ASC';
	    $result=api_sql_query($sql,__LINE__,__FILE__);
	    $row=Database::fetch_array($result); 

	}
					
	//show status			

	if ($row['visibility_disc']==1 || ($row['content']=='' AND $row['title']=='' AND $page=='index'))
	{
	 	return false;		 

	}
	else
	{
	 	return true;				
	}   		
}


/**
 * Lock add discussion
 * @author Juan Carlos Raña <herodoto@telefonica.net>
 */
function check_addlock_discuss() 
{
	global $tbl_wiki;
	global $page;
	global $groupfilter;

	$_clean['group_id']=(int)$_SESSION['_gid'];

	$sql='SELECT * FROM '.$tbl_wiki.'WHERE reflink="'.html_entity_decode(Database::escape_string(stripslashes(urldecode($page)))).'" AND '.$groupfilter.' ORDER BY id ASC';
	$result=api_sql_query($sql,__LINE__,__FILE__);
	$row=Database::fetch_array($result);
				
	$status_addlock_disc=$row['addlock_disc'];
	$id=$row['id'];		//need ? check. TODO	
		
	//change status
	if ($_GET['actionpage']=='addlock_disc' && (api_is_allowed_to_edit() || api_is_platform_admin())) 
    {	
		if ($row['addlock_disc']==1)
	    {
	    	$status_addlock_disc=0;
	    }
	    else
		{
			$status_addlock_disc=1; 
		}       
		
		$sql='UPDATE '.$tbl_wiki.' SET addlock_disc="'.Database::escape_string($status_addlock_disc).'" WHERE reflink="'.html_entity_decode(Database::escape_string(stripslashes(urldecode($page)))).'" AND '.$groupfilter;		
	    api_sql_query($sql,__FILE__,__LINE__); 
		
	  	//Although the value now is assigned to all (not only the first), these three lines remain necessary. They do that by changing the page state is made when you press the button and not have to wait to change his page
	    $sql='SELECT * FROM '.$tbl_wiki.'WHERE reflink="'.html_entity_decode(Database::escape_string(stripslashes(urldecode($page)))).'" AND '.$groupfilter.' ORDER BY id ASC';
	    $result=api_sql_query($sql,__LINE__,__FILE__);
	    $row=Database::fetch_array($result); 

	}
	 		
	//show status			

	if ($row['addlock_disc']==1 || ($row['content']=='' AND $row['title']=='' AND $page=='index'))
	{
		return false;
	}
	else
	{
		return true;				
	}    
		
}


/**
 * Lock rating discussion
 * @author Juan Carlos Raña <herodoto@telefonica.net>
 */
function check_ratinglock_discuss() 
{

	global $tbl_wiki;
	global $page;
	global $groupfilter;

	$_clean['group_id']=(int)$_SESSION['_gid'];

	$sql='SELECT * FROM '.$tbl_wiki.'WHERE reflink="'.html_entity_decode(Database::escape_string(stripslashes(urldecode($page)))).'" AND '.$groupfilter.' ORDER BY id ASC';
	$result=api_sql_query($sql,__LINE__,__FILE__);
	$row=Database::fetch_array($result);
				
	$status_ratinglock_disc=$row['ratinglock_disc'];
	$id=$row['id'];	//need ? check. TODO	
		
	//change status
	if ($_GET['actionpage']=='ratinglock_disc' && (api_is_allowed_to_edit() || api_is_platform_admin())) 
    {	
		if ($row['ratinglock_disc']==1)
	    {
	    	$status_ratinglock_disc=0;
	    }
	    else
		{
			$status_ratinglock_disc=1; 
		}       
		
		$sql='UPDATE '.$tbl_wiki.' SET ratinglock_disc="'.Database::escape_string($status_ratinglock_disc).'" WHERE reflink="'.html_entity_decode(Database::escape_string(stripslashes(urldecode($page)))).'" AND '.$groupfilter; //Visibility. Value to all,not only for the first	
	    api_sql_query($sql,__FILE__,__LINE__); 
		
	  	//Although the value now is assigned to all (not only the first), these three lines remain necessary. They do that by changing the page state is made when you press the button and not have to wait to change his page
	    $sql='SELECT * FROM '.$tbl_wiki.'WHERE reflink="'.html_entity_decode(Database::escape_string(stripslashes(urldecode($page)))).'" AND '.$groupfilter.' ORDER BY id ASC';
	    $result=api_sql_query($sql,__LINE__,__FILE__);
	    $row=Database::fetch_array($result); 

	}
	 			
	//show status
	if ($row['ratinglock_disc']==1 || ($row['content']=='' AND $row['title']=='' AND $page=='index'))
	{
		return false;
	}
	else
	{
		return true;				
	}    
		
}


/**
 * Notify page changes
 * @author Juan Carlos Raña <herodoto@telefonica.net>
 */
 
function check_notify_page($reflink)
{
	global $tbl_wiki;
	global $groupfilter;	
	global $tbl_wiki_mailcue;
	
	$_clean['group_id']=(int)$_SESSION['_gid'];
	$sql='SELECT * FROM '.$tbl_wiki.'WHERE reflink="'.$reflink.'" AND '.$groupfilter.' ORDER BY id ASC';
	$result=api_sql_query($sql,__LINE__,__FILE__);
	$row=Database::fetch_array($result);
	
	$id=$row['id'];		
		
	$sql='SELECT * FROM '.$tbl_wiki_mailcue.'WHERE id="'.$id.'" AND user_id="'.api_get_user_id().'" AND type="P"';
	$result=api_sql_query($sql,__LINE__,__FILE__);
	$row=Database::fetch_array($result);
			
	$idm=$row['id'];
	
	if (empty($idm))	
	{ 
		$status_notify=0;
	}
	else
	{
		$status_notify=1;
	}	
			
	//change status
	if ($_GET['actionpage']=='notify')
	{	
		
		if ($status_notify==0)
	    {		
		   	  
			$sql="INSERT INTO ".$tbl_wiki_mailcue." (id, user_id, type, group_id) VALUES ('".$id."','".api_get_user_id()."','P','".$_clean['group_id']."')";
			api_sql_query($sql,__FILE__,__LINE__);		
			
	    	$status_notify=1;			
	    }
	    else
		{					
		    $sql='DELETE FROM '.$tbl_wiki_mailcue.' WHERE id="'.$id.'" AND user_id="'.api_get_user_id().'" AND type="P"'; //$_clean['group_id'] not necessary
			api_sql_query($sql,__FILE__,__LINE__);
			
		    $status_notify=0;				
	    } 			
	}	
		
	//show status
	if ($status_notify==0)
	{
		return false;
	}
	else
	{
		return true;				
	}
}


/**
 * Notify discussion changes
 * @author Juan Carlos Raña <herodoto@telefonica.net>
 */
function check_notify_discuss($reflink)
{
	global $tbl_wiki;
	global $groupfilter;	
	global $tbl_wiki_mailcue;	
	
	$_clean['group_id']=(int)$_SESSION['_gid'];
	$sql='SELECT * FROM '.$tbl_wiki.'WHERE reflink="'.$reflink.'" AND '.$groupfilter.' ORDER BY id ASC';
	$result=api_sql_query($sql,__LINE__,__FILE__);
	$row=Database::fetch_array($result);
	
	$id=$row['id'];	
			
	$sql='SELECT * FROM '.$tbl_wiki_mailcue.'WHERE id="'.$id.'" AND user_id="'.api_get_user_id().'" AND type="D"';
	$result=api_sql_query($sql,__LINE__,__FILE__);
	$row=Database::fetch_array($result);
			
	$idm=$row['id'];
		
	if (empty($idm))	
	{ 	
		$status_notify_disc=0;
		
	}
	else
	{
		$status_notify_disc=1;
	}	
			
	//change status
	if ($_GET['actionpage']=='notify_disc')
	{	
		
		if ($status_notify_disc==0)
	    {	
		
			if (!$_POST['Submit'])
			{	  
								
				$sql="INSERT INTO ".$tbl_wiki_mailcue." (id, user_id, type, group_id) VALUES ('".$id."','".api_get_user_id()."','D','".$_clean['group_id']."')";
				api_sql_query($sql,__FILE__,__LINE__);		
				
				$status_notify_disc=1;
			}
			else
			{
				$status_notify_disc=0;
			}			
	    }
	    else
		{	
			if (!$_POST['Submit'])
			{				
				$sql='DELETE FROM '.$tbl_wiki_mailcue.' WHERE id="'.$id.'" AND user_id="'.api_get_user_id().'" AND type="D"'; //$_clean['group_id'] not necessary
				api_sql_query($sql,__FILE__,__LINE__);
				
				$status_notify_disc=0;
			}
			else
			{
				$status_notify_disc=1;
			}					
	    } 			
	}	
		
	//show status
	if ($status_notify_disc==0)
	{				
		return false;
	}
	else
	{
		return true;					
	}
}


/**
 * Notify all changes
 * @author Juan Carlos Raña <herodoto@telefonica.net>
 */
 
function check_notify_all()
{

	global $tbl_wiki_mailcue;
	
	$_clean['group_id']=(int)$_SESSION['_gid'];	
		
	$sql='SELECT * FROM '.$tbl_wiki_mailcue.'WHERE user_id="'.api_get_user_id().'" AND type="F" AND group_id="'.$_clean['group_id'].'"';
	$result=api_sql_query($sql,__LINE__,__FILE__);
	$row=Database::fetch_array($result);
			
	$idm=$row['user_id'];
	
	if (empty($idm))
	{ 
		$status_notify_all=0;	
	}
	else
	{
		$status_notify_all=1;
	}	
			
	//change status
	if ($_GET['actionpage']=='notify_all')
	{	
		
		if ($status_notify_all==0)
	    {	
			$sql="INSERT INTO ".$tbl_wiki_mailcue." (user_id, type, group_id) VALUES ('".api_get_user_id()."','F','".$_clean['group_id']."')";
			api_sql_query($sql,__FILE__,__LINE__);		
			
			$status_notify_all=1;				
	    }
	    else
		{								
			$sql='DELETE FROM '.$tbl_wiki_mailcue.' WHERE user_id="'.api_get_user_id().'" AND type="F" AND group_id="'.$_clean['group_id'].'"';
			api_sql_query($sql,__FILE__,__LINE__);
		
			$status_notify_all=0;			
	    } 					
	}	
		
	//show status
	if ($status_notify_all==0)
	{
		return false;
	}
	else
	{
		return true;				
	}
}


/**
 * Function check emailcue and send email when a page change
 * @author Juan Carlos Raña <herodoto@telefonica.net>
 */
 
function check_emailcue($id_or_ref, $type, $lastime='', $lastuser='')
{
	global $tbl_wiki;
	global $groupfilter;	
	global $tbl_wiki_mailcue;
	global $_course;		

    $_clean['group_id']=(int)$_SESSION['_gid'];
	
	$group_properties  = GroupManager :: get_group_properties($_clean['group_id']);	
	$group_name= $group_properties['name'];

    $allow_send_mail=false; //define the variable to below
	
	if ($type=='P')
	{
	//if modifying a wiki page
		
		//first, current author and time
		//Who is the author?
		$userinfo=	Database::get_user_info_from_id($lastuser);		
		$email_user_author= get_lang('EditedBy').': '.$userinfo['firstname'].' '.$userinfo['lastname'];		
		
		//When ?		
		$year = substr($lastime, 0, 4);
		$month = substr($lastime, 5, 2);
		$day = substr($lastime, 8, 2);
		$hours=substr($lastime, 11,2);
		$minutes=substr($lastime, 14,2);
		$seconds=substr($lastime, 17,2);
		$email_date_changes=$day.' '.$month.' '.$year.' '.$hours.":".$minutes.":".$seconds;	
		
		//second, extract data from first reg
	 	$sql='SELECT * FROM '.$tbl_wiki.'WHERE reflink="'.$id_or_ref.'" AND '.$groupfilter.' ORDER BY id ASC'; //id_or_ref is reflink from tblwiki
		
		$result=api_sql_query($sql,__LINE__,__FILE__);
		$row=Database::fetch_array($result);
		
		$id=$row['id'];
		$email_page_name=$row['title'];
		
			
		if ($row['visibility']==1)
		{
			$allow_send_mail=true; //if visibility off - notify off	
		
			$sql='SELECT * FROM '.$tbl_wiki_mailcue.'WHERE id="'.$id.'" AND type="'.$type.'" OR type="F" AND group_id="'.$_clean['group_id'].'"'; //type: P=page, D=discuss, F=full.		
			$result=api_sql_query($sql,__LINE__,__FILE__);
			
			$emailtext=get_lang('EmailWikipageModified').' <strong>'.$email_page_name.'</strong> '.get_lang('Wiki');
		}
	
	}
	elseif ($type=='D')
	{
	//if added a post to discuss
	
		//first, current author and time
		//Who is the author of last message?
		$userinfo=	Database::get_user_info_from_id($lastuser);		
		$email_user_author= get_lang('AddedBy').': '.$userinfo['firstname'].' '.$userinfo['lastname'];		
		
		//When ?		
		$year = substr($lastime, 0, 4);
		$month = substr($lastime, 5, 2);
		$day = substr($lastime, 8, 2);
		$hours=substr($lastime, 11,2);
		$minutes=substr($lastime, 14,2);
		$seconds=substr($lastime, 17,2);
		$email_date_changes=$day.' '.$month.' '.$year.' '.$hours.":".$minutes.":".$seconds;	
		
		//second, extract data from first reg	
		
		$id=$id_or_ref; //$id_or_ref is id from tblwiki
		
		$sql='SELECT * FROM '.$tbl_wiki.'WHERE id="'.$id.'" ORDER BY id ASC';
		
		$result=api_sql_query($sql,__LINE__,__FILE__);
		$row=Database::fetch_array($result);
		
		$email_page_name=$row['title'];
		
		
		if ($row['visibility_disc']==1)
		{
			$allow_send_mail=true; //if visibility off - notify off				
				
			$sql='SELECT * FROM '.$tbl_wiki_mailcue.'WHERE id="'.$id.'" AND type="'.$type.'" OR type="F" AND group_id="'.$_clean['group_id'].'"'; //type: P=page, D=discuss, F=full
			$result=api_sql_query($sql,__LINE__,__FILE__);			
			
			$emailtext=get_lang('EmailWikiPageDiscAdded').' <strong>'.$email_page_name.'</strong> '.get_lang('Wiki');
		}
	}
	elseif($type=='A')
	{
	//for added pages
		$id=0; //for tbl_wiki_mailcue
	
		$sql='SELECT * FROM '.$tbl_wiki.' ORDER BY id DESC'; //the added is always the last
		
		$result=api_sql_query($sql,__LINE__,__FILE__);
		$row=Database::fetch_array($result);
		
		$email_page_name=$row['title'];
		
		//Who is the author?
		$userinfo=	Database::get_user_info_from_id($row['user_id']);		
		$email_user_author= get_lang('AddedBy').': '.$userinfo['firstname'].' '.$userinfo['lastname'];		
		
		//When ?		
		$year = substr($row['dtime'], 0, 4);
		$month = substr($row['dtime'], 5, 2);
		$day = substr($row['dtime'], 8, 2);
		$hours=substr($row['dtime'], 11,2);
		$minutes=substr($row['dtime'], 14,2);
		$seconds=substr($row['dtime'], 17,2);
		$email_date_changes=$day.' '.$month.' '.$year.' '.$hours.":".$minutes.":".$seconds;		
		
		
		if($row['assignment']==0)	
		{
			$allow_send_mail=true;
		}
		elseif($row['assignment']==1)	
		{
			$email_assignment=get_lang('AssignmentDescExtra').' ('.get_lang('AssignmentMode').')';
			$allow_send_mail=true;
		}
		elseif($row['assignment']==2)		
		{
			$allow_send_mail=false; //Mode tasks: avoids notifications to all users about all users
		}		
		
		$sql='SELECT * FROM '.$tbl_wiki_mailcue.'WHERE id="'.$id.'" AND type="F" AND group_id="'.$_clean['group_id'].'"'; //type: P=page, D=discuss, F=full
		$result=api_sql_query($sql,__LINE__,__FILE__);
	
		$emailtext=get_lang('EmailWikiPageAdded').' <strong>'.$email_page_name.'</strong> '.get_lang('In').' '. get_lang('Wiki');
	}
	elseif($type=='E')
	{
		$id=0;
		
		$allow_send_mail=true;
		
		//Who is the author?
		$userinfo=	Database::get_user_info_from_id(api_get_user_id());	//current user
		$email_user_author= get_lang('DeletedBy').': '.$userinfo['firstname'].' '.$userinfo['lastname'];		
		
		
		//When ?		
		$today = date('r');		//current time
		$email_date_changes=$today;	
		
		$sql='SELECT * FROM '.$tbl_wiki_mailcue.'WHERE id="'.$id.'" AND type="F" AND group_id="'.$_clean['group_id'].'"'; //type: P=page, D=discuss, F=wiki
		$result=api_sql_query($sql,__LINE__,__FILE__);
				
		$emailtext=get_lang('EmailWikipageDedeleted');
	}			
	
		
	///make and send email		
		
	if ($allow_send_mail)
	{	
		while ($row=Database::fetch_array($result))
		{		
			if(empty($charset)){$charset='ISO-8859-1';}
			$headers = 'Content-Type: text/html; charset='. $charset;
			$userinfo=Database::get_user_info_from_id($row['user_id']);	//$row['user_id'] obtained from tbl_wiki_mailcue
			$name_to=$userinfo['firstname'].' '.$userinfo['lastname'];
			$email_to=$userinfo['email'];
			$sender_name=get_setting('emailAdministrator');
			$sender_email=get_setting('emailAdministrator');
			$email_subject = get_lang('EmailWikiChanges').' - '.$_course['official_code'];
			$email_body= get_lang('DearUser').' '.$userinfo['firstname'].' '.$userinfo['lastname'].',<br /><br />';
			$email_body .= $emailtext.' <strong>'.$_course['name'].' - '.$group_name.'</strong><br /><br /><br />';						
			$email_body .= $email_user_author.' ('.$email_date_changes.')<br /><br /><br />';		
			$email_body .= $email_assignment.'<br /><br /><br />';					
			$email_body .= '<font size="-2">'.get_lang('EmailWikiChangesExt_1').': <strong>'.get_lang('NotifyChanges').'</strong><br />';
			$email_body .= get_lang('EmailWikiChangesExt_2').': <strong>'.get_lang('NotNotifyChanges').'</strong></font><br />';
			api_mail_html($name_to, $email_to, $email_subject, $email_body, $sender_name, $sender_email, $headers);
		}	
	}
}  


/**
 * Function export last wiki page version to document area
 * @author Juan Carlos Raña <herodoto@telefonica.net>
 */
function export2doc($wikiTitle, $wikiContents, $groupId)
{

	if ( 0 != $groupId)
	{
		$groupPart = '_group' . $groupId; // and add groupId to put the same title document in different groups
		$group_properties  = GroupManager :: get_group_properties($groupId);
		$groupPath = $group_properties['directory'];
	}
	else
	{
		$groupPart = '';
		$groupPath ='';
	}
	
	$exportDir = api_get_path(SYS_COURSE_PATH).api_get_course_path(). '/document'.$groupPath;
	$exportFile = replace_dangerous_char(replace_accents($wikiTitle), 'strict' ) . $groupPart;
	
	$wikiContents = stripslashes($wikiContents);	
	$wikiContents = trim(preg_replace("/\[\[|\]\]/", " ", $wikiContents));
	
	$i = 1;
	while ( file_exists($exportDir . '/' .$exportFile.'_'.$i.'.html') ) $i++; //only export last version, but in new export new version in document area
	$wikiFileName = $exportFile . '_' . $i . '.html';
	$exportPath = $exportDir . '/' . $wikiFileName;
	file_put_contents( $exportPath, $wikiContents );		 
	$doc_id = add_document($_course, $groupPath.'/'.$wikiFileName,'file',filesize($exportPath),$wikiFileName);
	api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'DocumentAdded', api_get_user_id(), $groupId);					              
    // TODO: link to go document area
}


/**
 * Function prevent double post (reload or F5)
 */

function double_post($wpost_id)
{
	if(isset($_SESSION['wpost_id'])) 
	{
 		if ($wpost_id == $_SESSION['wpost_id'])
		{
 			return false;
		}
		else
		{
			$_SESSION['wpost_id'] = $wpost_id;
			return true;
		}
	}
	else
	{
		$_SESSION['wpost_id'] = $wpost_id;
		return true;
    }
}

/**
 * Function convert date to number
 * 2008-10-12 00:00:00 ---to--> 12345672218 (timestamp)
 */
function convert_date_to_number($default)
{
	$parts = split(' ',$default);
	list($d_year,$d_month,$d_day) = split('-',$parts[0]);
	list($d_hour,$d_minute,$d_second) = split(':',$parts[1]);	
	return mktime($d_hour, $d_minute, $d_second, $d_month, $d_day, $d_year);
}

/**
 * Function wizard individual assignment
 * @author Juan Carlos Raña <herodoto@telefonica.net>
 */
function auto_add_page_users($assignment_type)
{
	global $assig_user_id; //need to identify end reflinks	

	$_clean['group_id']=(int)$_SESSION['_gid'];	
	
	
	if($_clean['group_id']==0)   
  	{	
  		//extract course members
		if(!empty($_SESSION["id_session"])){
			$a_users_to_add = CourseManager :: get_user_list_from_course_code($_SESSION['_course']['id'], true, $_SESSION['id_session']);
		}
		else
		{
			$a_users_to_add = CourseManager :: get_user_list_from_course_code($_SESSION['_course']['id'], true);
		}
	}
	else
	{ 
		//extract group members
		$subscribed_users = GroupManager :: get_subscribed_users($_clean['group_id']);
		$subscribed_tutors = GroupManager :: get_subscribed_tutors($_clean['group_id']);
		$a_users_to_add_with_duplicates=array_merge($subscribed_users, $subscribed_tutors);
	
		//remove duplicates
		$a_users_to_add = $a_users_to_add_with_duplicates;
		array_walk($a_users_to_add, create_function('&$value,$key', '$value = json_encode($value);'));
		$a_users_to_add = array_unique($a_users_to_add);
		array_walk($a_users_to_add, create_function('&$value,$key', '$value = json_decode($value, true);'));	
	}    

	
	$all_students_pages = array();

	//data about teacher
	$userinfo=Database::get_user_info_from_id(api_get_user_id());
	require_once(api_get_path(INCLUDE_PATH).'/lib/usermanager.lib.php');
	if (api_get_user_id()<>0)
	{		
		$image_path = UserManager::get_user_picture_path_by_id(api_get_user_id(),'web',false, true);
		$image_repository = $image_path['dir'];
		$existing_image = $image_path['file'];
		$photo= '<img src="'.$image_repository.$existing_image.'" alt="'.$name.'"  width="40" height="50" align="top" title="'.$name.'"  />';	
	}
	else
	{
		$photo= '<img src="'.api_get_path(WEB_CODE_PATH)."img/unknown.jpg".'" alt="'.$name.'"  width="40" height="50" align="top"  title="'.$name.'"  />';
	}			 
	
	//teacher assignment title
	$title_orig=$_POST['title'];
	
	//teacher assignment reflink
	$link2teacher=$_POST['title']= $title_orig."_uass".api_get_user_id();
	
	//first: teacher name, photo, and assignment description (original content)	
    $content_orig_A='<div align="center" style="font-size:24px; background-color: #F5F8FB;  border:double">'.$photo.get_lang('Teacher').': '.$userinfo['firstname'].' '.$userinfo['lastname'].'</div><br/><div>';
	$content_orig_B='<h1>'.get_lang('AssignmentDescription').'</h1></div><br/>'.$_POST['content'];
	
    //Second: student list (names, photo and links to their works).
	//Third: Create Students work pages.
	
   	foreach($a_users_to_add as $user_id=>$o_user_to_add)
	{					  
		if($o_user_to_add['user_id'] != api_get_user_id()) //except that puts the task
		{
											 		 
			$assig_user_id= $o_user_to_add['user_id']; //identifies each page as created by the student, not by teacher		
			$image_path = UserManager::get_user_picture_path_by_id($assig_user_id,'web',false, true);
			$image_repository = $image_path['dir'];
			$existing_image = $image_path['file'];
			$name= $o_user_to_add['lastname'].', '.$o_user_to_add['firstname'];
			$photo= '<img src="'.$image_repository.$existing_image.'" alt="'.$name.'"  width="40" height="50" align="bottom" title="'.$name.'"  />';
			
			$is_tutor_of_group = GroupManager :: is_tutor_of_group($assig_user_id,$_clean['group_id']); //student is tutor			
			$is_tutor_and_member = (GroupManager :: is_tutor_of_group($assig_user_id,$_clean['group_id']) && GroupManager :: is_subscribed($assig_user_id, $_clean['group_id'])); //student is tutor and member		
			
			if($is_tutor_and_member)
			{
				$status_in_group=get_lang('GroupTutorAndMember');
				
			}
			else
			{
				if($is_tutor_of_group)
				{
					$status_in_group=get_lang('GroupTutor');
				}
				else
				{
					$status_in_group=" "; //get_lang('GroupStandardMember')
				}
			}					
			
			if($assignment_type==1)
			{			 
				$_POST['title']= $title_orig;
				$_POST['comment']=get_lang('AssignmentFirstComToStudent');				
				$_POST['content']='<div align="center" style="font-size:24px; background-color: #F5F8FB;  border:double">'.$photo.get_lang('Student').': '.$name.'</div>[['.$link2teacher.' | '.get_lang('AssignmentLinktoTeacherPage').']] '; //If $content_orig_B is added here, the task written by the professor was copied to the page of each student. TODO: config options
				
			   //AssignmentLinktoTeacherPage	        
			 	$all_students_pages[] = '<li>'.$o_user_to_add['lastname'].', '.$o_user_to_add['firstname'].' [['.$_POST['title']."_uass".$assig_user_id.' | '.$photo.']] '.$status_in_group.'</li>';
				
				$_POST['assignment']=2;
				
			}			
			save_new_wiki();	
		}	
        		
	}//end foreach for each user
	
	
	foreach($a_users_to_add as $user_id=>$o_user_to_add)
	{
			
		if($o_user_to_add['user_id'] == api_get_user_id())
		{		
			$assig_user_id=$o_user_to_add['user_id'];			
			if($assignment_type==1)			
			 {					 
				$_POST['title']= $title_orig;	
				$_POST['comment']=get_lang('AssignmentDesc');
				sort($all_students_pages);
				$_POST['content']=$content_orig_A.$content_orig_B.'<br/><div align="center" style="font-size:24px; background-color: #F5F8FB;  border:double">'.get_lang('AssignmentLinkstoStudentsPage').'<br/><strong>'.$title_orig.'</strong></div><div style="background-color: #F5F8FB; border:double"><ol>'.implode($all_students_pages).'</ol></div><br/>';
			 	$_POST['assignment']=1;
						
			 }					 
			
			save_new_wiki();
		}
			
	} //end foreach to teacher
}

function display_wiki_search_results($search_term, $search_content=0)
{
	global $tbl_wiki, $groupfilter, $MonthsLong; 
	
	echo '<div class="row"><div class="form_header">'.get_lang('WikiSearchResults').'</div></div>';

	$_clean['group_id']=(int)$_SESSION['_gid'];

	if(api_is_allowed_to_edit() || api_is_platform_admin()) //only by professors if page is hidden
	{
		if($search_content=='1')
		{
			$sql="SELECT * FROM ".$tbl_wiki." s1 WHERE title LIKE '%".Database::escape_string($search_term)."%' OR content LIKE '%".Database::escape_string($search_term)."%' AND id=(SELECT MAX(s2.id) FROM ".$tbl_wiki." s2 WHERE s1.reflink = s2.reflink AND ".$groupfilter.")";// warning don't use group by reflink because don't return the last version
		}
		else
		{
			$sql="SELECT * FROM ".$tbl_wiki." s1 WHERE title LIKE '%".Database::escape_string($search_term)."%' AND id=(SELECT MAX(s2.id) FROM ".$tbl_wiki." s2 WHERE s1.reflink = s2.reflink AND ".$groupfilter.")";// warning don't use group by reflink because don't return the last version
		}
	}
	else
	{
		if($search_content=='1')
		{

			$sql="SELECT * FROM ".$tbl_wiki." s1 WHERE  visibility=1 AND title LIKE '%".Database::escape_string($search_term)."%' OR content LIKE '%".Database::escape_string($search_term)."%' AND id=(SELECT MAX(s2.id) FROM ".$tbl_wiki." s2 WHERE s1.reflink = s2.reflink AND ".$groupfilter.")";// warning don't use group by reflink because don't return the last version
		}
		else
		{
			$sql="SELECT * FROM ".$tbl_wiki." s1 WHERE  visibility=1 AND title LIKE '%".Database::escape_string($search_term)."%' AND id=(SELECT MAX(s2.id) FROM ".$tbl_wiki." s2 WHERE s1.reflink = s2.reflink AND ".$groupfilter.")";// warning don't use group by reflink because don't return the last version
		}
	}

	$result=api_sql_query($sql,__LINE__,__FILE__);

	//show table
	if (mysql_num_rows($result) > 0)
	{
		$row = array ();
		while ($obj = mysql_fetch_object($result))
		{
			//get author
			$userinfo=Database::get_user_info_from_id($obj->user_id);

			//get time
			$year 	 = substr($obj->dtime, 0, 4);
			$month	 = substr($obj->dtime, 5, 2);
			$day 	 = substr($obj->dtime, 8, 2);
			$hours   = substr($obj->dtime, 11,2);
			$minutes = substr($obj->dtime, 14,2);
			$seconds = substr($obj->dtime, 17,2);

			//get type assignment icon
			if($obj->assignment==1)
			{
				$ShowAssignment='<img src="../img/wiki/assignment.gif" title="'.get_lang('AssignmentDesc').'" alt="'.get_lang('AssignmentDesc').'" />';
			}
			elseif ($obj->assignment==2)
			{
				$ShowAssignment='<img src="../img/wiki/works.gif" title="'.get_lang('AssignmentWork').'" alt="'.get_lang('AssignmentWork').'" />';
			}
			elseif ($obj->assignment==0)
			{
				$ShowAssignment='<img src="../img/wiki/trans.gif" />';
			}

			$row = array ();
			$row[] =$ShowAssignment;
			$row[] = '<a href="'.api_get_self().'?cidReq='.$_course[id].'&action=showpage&title='.urlencode($obj->reflink).'&group_id='.Security::remove_XSS($_GET['group_id']).'">'.$obj->title.'</a>';
			$row[] = $obj->user_id <>0 ? '<a href="../user/userInfo.php?uInfo='.$userinfo['user_id'].'">'.$userinfo['lastname'].', '.$userinfo['firstname'].'</a>' : get_lang('Anonymous').' ('.$obj->user_ip.')';
			$row[] = $year.'-'.$month.'-'.$day.' '.$hours.":".$minutes.":".$seconds;
			
			if(api_is_allowed_to_edit()|| api_is_platform_admin())
			{
				$showdelete=' <a href="'.api_get_self().'?cidReq='.$_course[id].'&action=delete&title='.urlencode(Security::remove_XSS($obj->reflink)).'&group_id='.Security::remove_XSS($_GET['group_id']).'"><img src="../img/delete.gif" title="'.get_lang('Delete').'" alt="'.get_lang('Delete').'" />';
			}			
			$row[] = '<a href="'.api_get_self().'?cidReq='.$_course[id].'&action=edit&title='.urlencode(Security::remove_XSS($obj->reflink)).'&group_id='.Security::remove_XSS($_GET['group_id']).'"><img src="../img/lp_quiz.png" title="'.get_lang('EditPage').'" alt="'.get_lang('EditPage').'" /></a> <a href="'.api_get_self().'?cidReq='.$_course[id].'&action=discuss&title='.urlencode(Security::remove_XSS($obj->reflink)).'&group_id='.Security::remove_XSS($_GET['group_id']).'"><img src="../img/comment_bubble.gif" title="'.get_lang('Discuss').'" alt="'.get_lang('Discuss').'" /></a> <a href="'.api_get_self().'?cidReq='.$_course[id].'&action=history&title='.urlencode(Security::remove_XSS($obj->reflink)).'&group_id='.Security::remove_XSS($_GET['group_id']).'"><img src="../img/history.gif" title="'.get_lang('History').'" alt="'.get_lang('History').'" /></a> <a href="'.api_get_self().'?cidReq='.$_course[id].'&action=links&title='.urlencode(Security::remove_XSS($obj->reflink)).'&group_id='.Security::remove_XSS($_GET['group_id']).'"><img src="../img/lp_link.png" title="'.get_lang('LinksPages').'" alt="'.get_lang('LinksPages').'" /></a>'.$showdelete;		
			
			$rows[] = $row;
		}

		$table = new SortableTableFromArrayConfig($rows,1,10,'SearchPages_table','','','ASC');
		$table->set_additional_parameters(array('cidReq' =>$_GET['cidReq'],'action'=>$_GET['action'],'group_id'=>Security::remove_XSS($_GET['group_id'])));

		$table->set_header(0,get_lang('Type'), true, array ('style' => 'width:30px;'));
		$table->set_header(1,get_lang('Title'), true);
		$table->set_header(2,get_lang('Author').' ('.get_lang('LastVersion').')', true);
		$table->set_header(3,get_lang('Date').' ('.get_lang('LastVersion').')', true);
		$table->set_header(4,get_lang('Actions'), true, array ('style' => 'width:100px;'));
		
		$table->display();
	}
	else 
	{
		echo get_lang('NoSearchResults');
	}
}
?>