<?php
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2009 Dokeos SPRL
	Copyright (c) Julio Montoya <gugli100@gmail.com>

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
include_once(api_get_path(LIBRARY_PATH).'/main_api.lib.php');
include_once(api_get_path(LIBRARY_PATH).'/online.inc.php');
class MessageManager {	
	function MessageManager() {
		
	}
	public static function get_online_user_list($current_user_id) {
		$min=30;
		global $_configuration;
		$userlist = WhoIsOnline($current_user_id,$_configuration['statistics_database'],$min);
		foreach($userlist as $row) {
			$receiver_id = $row[0];
			$online_user_list[$receiver_id] = GetFullUserName($receiver_id).($current_user_id==$receiver_id?("&nbsp;(".get_lang('Myself').")"):(""));
		}
		return $online_user_list;
	}
	
	/**
	* Displays info stating that the message is sent successfully.
	*/
	public static function display_success_message($uid) {
		$success= get_lang('MessageSentTo').
				"&nbsp;<b>".
				GetFullUserName($uid).
				"</b>".
				"<br><a href=\"".
				"inbox.php\">".
				get_lang('BackToInbox').
				"</a>";
		Display::display_confirmation_message($success, false);
	}
	
	/**
	* Displays the wysiwyg html editor.
	*/
	public static function display_html_editor_area($name,$resp) {
		api_disp_html_area($name, get_lang('TypeYourMessage'), '', '100%');
	}
	
	/**
	* Get the new messages for the current user from the database.
	*/
	public static function get_new_messages() {
		$table_message = Database::get_main_table(TABLE_MESSAGE); 
		if (!api_get_user_id()) {
			return false;
		}
		$i=0;		
		$query = "SELECT * FROM $table_message WHERE user_receiver_id=".api_get_user_id()." AND msg_status=1";
		$result = api_sql_query($query,__FILE__,__LINE__);
		$i = Database::num_rows($result);
		return $i;
	}
	
	/**
	* Get the list of user_ids of users who are online.
	*/
	public static function users_connected_by_id() {
		global $_configuration, $_user;
		$minute=30;
		$user_connect = WhoIsOnline($_user['user_id'],$_configuration['statistics_database'],$minute);
		for ($i=0; $i<count($user_connect); $i++) {
			$user_id_list[$i]=$user_connect[$i][0];
		}
		return $user_id_list;
	}
	
	/**
	 * Gets the total number of messages, used for the inbox sortable table
	 */
	public static function get_number_of_messages () {
		$table_message = Database::get_main_table(TABLE_MESSAGE); 
		$sql_query = "SELECT COUNT(*) as number_messages FROM $table_message WHERE msg_status IN (0,1,3) AND user_receiver_id=".api_get_user_id();
		$sql_result = api_sql_query($sql_query,__FILE__,__LINE__);
		$result = Database::fetch_array($sql_result);
		return $result['number_messages'];
	}
	
	/**
	 * Gets information about some messages, used for the inbox sortable table
	 * @param int $from
	 * @param int $number_of_items
	 * @param string $direction
	 */
	public static function get_message_data ($from, $number_of_items, $column, $direction) {
		$table_message = Database::get_main_table(TABLE_MESSAGE); 
		$request=api_is_xml_http_request();
		$sql_query = "SELECT id as col0, user_sender_id as col1, title as col2, send_date as col3 FROM $table_message " .
					 "WHERE user_receiver_id=".api_get_user_id()." AND msg_status IN (0,1,3)" .
					 "ORDER BY col$column $direction LIMIT $from,$number_of_items";
		$sql_result = api_sql_query($sql_query,__FILE__,__LINE__);
		$i = 0;
		$message_list = array ();
		while ($result = Database::fetch_row($sql_result)) {
			$message[0] = ($result[0]);
			if ($request===true) {
				$message[1] = utf8_encode(GetFullUserName(($result[1])));
				$message[2] = '<a href="../messages/view_message.php?rs=1&amp;id='.$result[0].'">'.utf8_encode($result[2]).'</a>';
				$message[4] = '<a href="../messages/new_message.php?rs=1&amp;re_id='.$result[0].'">'.Display::return_icon('message_reply.png',get_lang('ReplyToMessage')).'</a>'.
						  '&nbsp;&nbsp;<a href="../messages/inbox.php?rs=1&amp;action=deleteone&id='.$result[0].'"  onclick="javascript:if(!confirm('."'".addslashes(htmlentities(get_lang('ConfirmDeleteMessage')))."'".')) return false;">'.Display::return_icon('message_delete.png',get_lang('DeleteMessage')).'</a>';
			} else {
				$message[1] = GetFullUserName(($result[1]));
				$message[2] = '<a href="view_message.php?id='.$result[0].'">'.$result[2].'</a>';
				$message[4] = '<a href="new_message.php?re_id='.$result[0].'">'.Display::return_icon('message_reply.png',get_lang('ReplyToMessage')).'</a>'.
						  '&nbsp;&nbsp;<a href="inbox.php?action=deleteone&id='.$result[0].'"  onclick="javascript:if(!confirm('."'".addslashes(htmlentities(get_lang('ConfirmDeleteMessage')))."'".')) return false;">'.Display::return_icon('message_delete.png',get_lang('DeleteMessage')).'</a>';	
			}
			$message[3] = ($result[3]); //date stays the same
			$message_list[] = $message;
			
			$i++;
		}
		return $message_list;
	}
	
	 public static function send_message ($receiver_user_id, $title, $content) {
		$table_message = Database::get_main_table(TABLE_MESSAGE); 
		$query = "INSERT INTO $table_message(user_sender_id, user_receiver_id, msg_status, send_date, title, content ) ".
				 " VALUES (".
		 		 "'".api_get_user_id()."', '".Database::escape_string($receiver_user_id)."', '1', '".date('Y-m-d H:i:s')."','".Database::escape_string($title)."','".Database::escape_string($content)."'".
		 		 ")";
		$result = api_sql_query($query,__FILE__,__LINE__);
		return $result;	
	}
	
	 public static function delete_message_by_user_receiver ($user_receiver_id,$id) {	
		$table_message = Database::get_main_table(TABLE_MESSAGE); 
		$query = "DELETE FROM $table_message " .
				 "WHERE user_receiver_id=".Database::escape_string($user_receiver_id)." AND id=".Database::escape_string($id);
		$result = api_sql_query($query,__FILE__,__LINE__);
		return $result;	
	}
	/**
	 * Set status deleted 
	 * @author Isaac FLores Paz <isaac.flores@dokeos.com>
	 * @param  integer
	 * @param  integer
	 * @return array
	 */
	public static function delete_message_by_user_sender ($user_sender_id,$id) {
		$table_message = Database::get_main_table(TABLE_MESSAGE); 
		$query = "UPDATE $table_message " .
				 "SET msg_status=3 WHERE user_sender_id=".Database::escape_string($user_sender_id)." AND id=".Database::escape_string($id);
		$result = api_sql_query($query,__FILE__,__LINE__);
		return $result;		
	}
	public static function update_message ($user_id, $id) {
		$table_message = Database::get_main_table(TABLE_MESSAGE); 
		$query = "UPDATE $table_message SET msg_status = '0' WHERE user_receiver_id=".Database::escape_string($user_id)." AND id='".Database::escape_string($id)."'";
		$result = api_sql_query($query,__FILE__,__LINE__);	
	}
	
	 public static function get_message_by_user ($user_id,$id) {
		$table_message = Database::get_main_table(TABLE_MESSAGE); 
		$query = "SELECT * FROM $table_message WHERE user_receiver_id=".Database::escape_string($user_id)." AND id='".Database::escape_string($id)."'";
		$result = api_sql_query($query,__FILE__,__LINE__);
		return $row = Database::fetch_array($result);
	}
	/**
	 * Gets information about if exist messages
	 * @author Isaac FLores Paz <isaac.flores@dokeos.com>
	 * @param  integer
	 * @param  integer
	 * @return boolean
	 */
	 public static function exist_message ($user_id, $id) {
		$table_message = Database::get_main_table(TABLE_MESSAGE); 
		$query = "SELECT id FROM $table_message WHERE user_receiver_id=".Database::escape_string($user_id)." AND id='".Database::escape_string($id)."'";
		$result = api_sql_query($query,__FILE__,__LINE__);
		$num = Database::num_rows($result);
		if ($num>0)
			return true;
		else
			return false;	
	}
	/**
	 * Gets information about messages sent
	 * @author Isaac FLores Paz <isaac.flores@dokeos.com>
	 * @param  integer
	 * @param  integer
	 * @param  string
	 * @return array
	 */
	 public static function get_message_data_sent ($from, $number_of_items, $column, $direction) {
		$table_message = Database::get_main_table(TABLE_MESSAGE); 
		$request=api_is_xml_http_request();
		$sql_query = "SELECT id as col0, user_sender_id as col1, title as col2, send_date as col3 FROM $table_message " .
					 "WHERE user_sender_id=".api_get_user_id()." AND msg_status IN (0,1)" .
					 "ORDER BY col$column $direction LIMIT $from,$number_of_items";
		$sql_result = api_sql_query($sql_query,__FILE__,__LINE__);
		$i = 0;
		$message_list = array ();
		while ($result = Database::fetch_row($sql_result)) {
			$message[0] = $result[0];
			if ($request===true) {
				$message[1] = utf8_encode(GetFullUserName($result[1]));
				$message[2] = '<a href="../messages/view_message.php?rs=1&amp;id_send='.$result[0].'">'.utf8_encode($result[2]).'</a>';
				$message[4] = '<a href="../messages/new_message.php?rs=1&amp;re_id='.$result[0].'">'.Display::return_icon('message_reply.png',get_lang('ReplyToMessage')).'</a>'.
						  '&nbsp;&nbsp;<a href="../messages/outbox.php?rs=1&amp;action=deleteone&id='.$result[0].'"  onclick="javascript:if(!confirm('."'".addslashes(htmlentities(get_lang('ConfirmDeleteMessage')))."'".')) return false;">'.Display::return_icon('message_delete.png',get_lang('DeleteMessage')).'</a>';
			} else {
				$message[1] = GetFullUserName($result[1]);
				$message[2] = '<a href="../messages/view_message.php?id_send='.$result[0].'">'.$result[2].'</a>';
				$message[4] = '<a href="new_message.php?re_id='.$result[0].'">'.Display::return_icon('message_reply.png',get_lang('ReplyToMessage')).'</a>'.
						  '&nbsp;&nbsp;<a href="outbox.php?action=deleteone&id='.$result[0].'"  onclick="javascript:if(!confirm('."'".addslashes(htmlentities(get_lang('ConfirmDeleteMessage')))."'".')) return false;">'.Display::return_icon('message_delete.png',get_lang('DeleteMessage')).'</a>';
			}
			$message[3] = $result[3]; //date stays the same
			$message_list[] = $message;
			$i++;
		}
		return $message_list;
	}
	/**
	 * Gets information about number messages sent
	 * @author Isaac FLores Paz <isaac.flores@dokeos.com>
	 * @param void
	 * @return integer
	 */
	 public static function get_number_of_messages_sent () {
		$table_message = Database::get_main_table(TABLE_MESSAGE); 
		$sql_query = "SELECT COUNT(*) as number_messages FROM $table_message WHERE msg_status IN (0,1) AND user_sender_id=".api_get_user_id();
		$sql_result = api_sql_query($sql_query,__FILE__,__LINE__);
		$result = Database::fetch_array($sql_result);
		return $result['number_messages'];
	}
	public static function show_message_box () {
		$table_message = Database::get_main_table(TABLE_MESSAGE);
		if (isset($_GET['id_send'])) {
			$query = "SELECT * FROM $table_message WHERE user_sender_id=".api_get_user_id()." AND id=".$_GET['id_send']." AND msg_status IN (0,1);";
			$result = api_sql_query($query,__FILE__,__LINE__);
		} else {
			$query = "UPDATE $table_message SET msg_status = '0' WHERE user_receiver_id=".api_get_user_id()." AND id='".Database::escape_string($_GET['id'])."';";
			$result = api_sql_query($query,__FILE__,__LINE__);
			$query = "SELECT * FROM $table_message WHERE user_receiver_id=".api_get_user_id()." AND id='".Database::escape_string($_GET['id'])."';";
			$result = api_sql_query($query,__FILE__,__LINE__);
		}
		$row = Database::fetch_array($result);
		$user_con = self::users_connected_by_id();
		$band=0;
		$reply='';
		for ($i=0;$i<count($user_con);$i++)
			if ($row[1]==$user_con[$i])
				$band=1;	
		if ($band==1 && !isset($_GET['id_send'])) {
			$reply = '<a href="new_message.php?re_id='.$_GET['id'].'">'.Display::return_icon('message_reply.png',get_lang('ReplyToMessage')).get_lang('ReplyToMessage').'</a>';
		}
		echo '<div class=actions>';
		echo $reply; 
		echo '<a href="inbox.php?action=deleteone&id='.$row[0].'"  onclick="javascript:if(!confirm('."'".addslashes(htmlentities(get_lang('ConfirmDeleteMessage')))."'".')) return false;">'.Display::return_icon('message_delete.png',get_lang('DeleteMessage')).''.get_lang('Delete').'</a>';
		echo '</div><br />';
		echo '
		<table class="message_view_table" >
		    <TR>
		      <TD width=10>&nbsp; </TD>
		      <TD vAlign=top width="100%">
		      	<TABLE>      
		            <TR>
		              <TD width="100%">                              
		                    <TR> <h1>'.$row[5].'</h1></TR>
		              </TD>              		
		              <TR>                       
		              	<TD>'.get_lang('From').'&nbsp;<b>'.GetFullUserName($row[1]).'</b> '.strtolower(get_lang('To')).'&nbsp;  <b>'.GetFullUserName($row[2]).'</b> </TD>
		              </TR>                    
		              <TR>
		              <TD >'.get_lang('Date').'&nbsp; '.$row[4].'</TD>                      
		              </TR>              
		            </TR>          
		        </TABLE>	      		
		        <br />
		        <TABLE height=209 width="100%" bgColor=#ffffff>
		          <TBODY>
		            <TR>
		              <TD vAlign=top>'.$row[6].'</TD>
		            </TR>
		          </TBODY>
		        </TABLE>
		        <DIV class=HT style="PADDING-BOTTOM: 5px"> </DIV></TD>
		      <TD width=10>&nbsp;</TD>
		    </TR>
		</TABLE>';
	}
}
?>