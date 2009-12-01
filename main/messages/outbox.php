<?php
/* For licensing terms, see /chamilo_license.txt */
/*
==============================================================================
		INIT SECTION
==============================================================================
*/
// name of the language file that needs to be included
$language_file = array('registration','messages','userInfo','admin');
$cidReset=true;
require_once '../inc/global.inc.php';
require_once api_get_path(LIBRARY_PATH).'message.lib.php';

api_block_anonymous_users();
if (isset($_GET['messages_page_nr'])) {
	if (api_get_setting('allow_social_tool')=='true' &&  api_get_setting('allow_message_tool')=='true') {
		header('Location:../social/index.php?pager="'.Security::remove_XSS($_GET['messages_page_nr']).'"&remote=3#remote-tab-3');
	}
}
if (api_get_setting('allow_message_tool')!='true'){
	api_not_allowed();
}
$htmlHeadXtra[]='<script language="javascript">
<!--
function enviar(miforma)
{
	if(confirm("'.get_lang('SureYouWantToDeleteSelectedMessages', '').'"))
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

//$nameTools = get_lang('Messages');


//api_display_tool_title(api_xml_http_response_encode(get_lang('Inbox')));
if ($_GET['f']=='social') {
	$this_section = SECTION_SOCIAL;
	$interbreadcrumb[]= array ('url' => '#','name' => get_lang('Profile'));
	$interbreadcrumb[]= array ('url' => 'outbox.php','name' => get_lang('Inbox'));	
} else {
	$this_section = SECTION_MYPROFILE;
	$interbreadcrumb[]= array ('url' => '#','name' => get_lang('Profile'));
	$interbreadcrumb[]= array ('url' => 'outbox.php','name' => get_lang('Inbox'));
}

Display::display_header('');

echo '<div class=actions>';
	echo '<a href="'.api_get_path(WEB_PATH).'main/messages/inbox.php">'.Display::return_icon('inbox.png',api_xml_http_response_encode(get_lang('Inbox'))).api_xml_http_response_encode(get_lang('Inbox')).'</a>';
	echo '<a href="'.api_get_path(WEB_PATH).'main/messages/new_message.php">'.Display::return_icon('message_new.png',api_xml_http_response_encode(get_lang('ComposeMessage'))).api_xml_http_response_encode(get_lang('ComposeMessage')).'</a>';
	echo '<a href="'.api_get_path(WEB_PATH).'main/messages/outbox.php">'.Display::return_icon('outbox.png',api_xml_http_response_encode(get_lang('Outbox'))).api_xml_http_response_encode(get_lang('Outbox')).'</a>';
echo '</div>';	

/**************************************************************/
$info_delete_outbox=array();
$info_delete_outbox=explode(',',$_GET['form_delete_outbox']);
$count_delete_outbox=(count($info_delete_outbox)-1);
/**************************************************************/
if( trim($info_delete_outbox[0])=='delete' ) {
	for ($i=1;$i<=$count_delete_outbox;$i++) {
		MessageManager::delete_message_by_user_sender(api_get_user_id(),$info_delete_outbox[$i]);
	}
		$message_box=get_lang('SelectedMessagesDeleted').
			'&nbsp
			<br><a href="../social/index.php?#remote-tab-3">'.
			get_lang('BackToOutbox').
			'</a>';
		Display::display_normal_message(api_xml_http_response_encode($message_box),false);
	    exit;
}
/**************************************************************/
$table_message = Database::get_main_table(TABLE_MESSAGE);

$user_sender_id=api_get_user_id();
if ($_REQUEST['action']=='delete') {
	$delete_list_id=array();
	if (isset($_POST['out'])) {
		$delete_list_id=$_POST['out'];
	}
	if (isset($_POST['id'])) {
		$delete_list_id=$_POST['id'];
	}
	for ($i=0;$i<count($delete_list_id);$i++) {
		MessageManager::delete_message_by_user_sender(api_get_user_id(), $delete_list_id[$i]);
	}
	$delete_list_id=array();
	outbox_display();
} elseif ($_REQUEST['action']=='deleteone') {
	$delete_list_id=array();
	$id=Security::remove_XSS($_GET['id']);
	MessageManager::delete_message_by_user_sender(api_get_user_id(),$id);
	$delete_list_id=array();
	outbox_display();
}else {
	outbox_display();
}
/*
==============================================================================
		FOOTER
==============================================================================
*/
Display::display_footer();

?>