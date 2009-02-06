<?php 
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2009 Dokeos SPRL
	Copyright (c) 2009 Julio Montoya Armas <gugli100@gmail.com>
	Copyright (c) Facultad de Matematicas, UADY (M�xico)
	Copyright (c) Evie, Free University of Brussels (Belgium)		

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
		INIT SECTION
==============================================================================
*/ 
// name of the language file that needs to be included 
$language_file= 'messages';
$cidReset=true;
include_once ('../inc/global.inc.php');
require_once '../messages/message.class.php';
require_once (api_get_path(LIBRARY_PATH).'message.lib.php');
api_block_anonymous_users();

if (api_get_setting('allow_message_tool')!='true'){
	api_not_allowed();
}


$htmlHeadXtra[]='<script language="javascript">
<!--
function enviar(miforma) 
{ 
	if(confirm("'.get_lang("SureYouWantToDeleteSelectedMessages").'"))
		miforma.submit();
} 
function select_all(formita)
{ 
   for (i=0;i<formita.elements.length;i++) 
	{
      		if(formita.elements[i].type == "checkbox") 			
				formita.elements[i].checked=1			
	}
}
function deselect_all(formita)
{ 
   for (i=0;i<formita.elements.length;i++) 
	{
      		if(formita.elements[i].type == "checkbox") 			
				formita.elements[i].checked=0			
	}	
}
//-->
</script>';


/*
==============================================================================
		MAIN CODE
==============================================================================
*/

$nameTools = get_lang('Messages');
$request=api_is_xml_http_request();
if ($request===false) {
	$interbreadcrumb[]= array (
		'url' => '#',
		'name' => get_lang($nameTools)
	);
	$interbreadcrumb[]= array (
		'url' => 'inbox.php',
		'name' => get_lang('Inbox')
	);
	$interbreadcrumb[]= array (
		'url' => 'outbox.php',
		'name' => get_lang('Outbox')
	);
	Display::display_header('');
}
api_display_tool_title(get_lang('Outbox'));

$table_message = Database::get_main_table(TABLE_MESSAGE);
echo '<div class=actions>';
echo get_lang('ReadMessageComment');
echo '</div>';

$user_sender_id=api_get_user_id();
$id=Security::remove_XSS($_GET['id']);
if ($_REQUEST['action']!='delete') {
	outbox_display();
} else {
	$delete_list_id=array();
	$delete_list_id=$_POST['id'];
	for ($i=0;$i<count($delete_list_id);$i++) {
		//the user_id was necesarry to delete a message??
		MessageManager::delete_message_by_user_sender(api_get_user_id(), $delete_list_id[$i]);		
	}
	outbox_display();
}
/*
==============================================================================
		FOOTER 
==============================================================================
*/
if ($request===false) {
	Display::display_footer();
}
?>