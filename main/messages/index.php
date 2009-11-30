<?php
/* For licensing terms, see /chamilo_license.txt */
$language_file= 'messages';
require_once '../inc/global.inc.php';
require_once api_get_path(LIBRARY_PATH).'message.lib.php';
api_block_anonymous_users();

if (api_get_setting('allow_message_tool')!='true'){
	api_not_allowed();
}


/* This page should be deleted */
 
if(api_get_user_id()!=0) {
	//echo '<script language="javascript" type="text/javascript" src="'.api_get_path(WEB_CODE_PATH).'messages/cookies.js"> </script> ';
	//echo '<script language="javascript" type="text/javascript">set_url("'.api_get_path(WEB_CODE_PATH).'messages/notify.php") ; notificar()</script> ';
	$number_of_new_messages = MessageManager::get_new_messages();
	
	if(is_null($number_of_new_messages)) {
		$number_of_new_messages = 0;
	}
	/*echo "<a href=inbox.php>".get_lang('Inbox')."(<span id=\"nuevos\" style=\"none\">".$number_of_new_messages."</span>)</a>";
	echo " - ";
	echo "<a href=new_message.php>".get_lang('ComposeMessage')."</a>";*/
	$number_of_new_messages = -1;
	if($number_of_new_messages > 0)
	{
	?>
		<div id="box" class="message-content-table">
		  <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="2" id="table" class="content">
		    <tr>
		      <td width="28%" height="16" class="content" id="ref"><a style="color:red;font-size:10px" href="javascript:;" onclick="ocultar_aviso()"><?php echo get_lang('Close');?></a></td>
		      <td width="72%" rowspan="2" class="content" id="ref"><?php echo '<a href="'.$e.'" style="color:#000000" onclick="ocultar_aviso()">'.get_lang('YouHaveNewMessage').'</a>'; ?></td>
		    </tr>
		    <tr>
		      <td class="content" id="ref"><?php Display::return_icon('message_new.gif',get_lang('NewMessage'));?> </td>
		    </tr>
		  </table>
		</div>
	<?php
	}
} else {
	//echo '<script language="javascript" type="text/javascript" src="'.api_get_path(WEB_CODE_PATH).'messages/cookies.js"> </script>';
	//echo '<script language="javascript" type="text/javascript">Set_Cookie( "nuevos", 0, 0, "/","","")</script> ';
}
?>