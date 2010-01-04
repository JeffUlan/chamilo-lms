<?php
/* For licensing terms, see /chamilo_license.txt */
/**
 * @package dokeos.social
 * @author Julio Montoya <gugli100@gmail.com>
 */
$language_file = array('messages','userInfo');
$cidReset=true;
require '../inc/global.inc.php';
require_once api_get_path(LIBRARY_PATH).'image.lib.php';
require_once api_get_path(LIBRARY_PATH).'usermanager.lib.php';
require_once api_get_path(LIBRARY_PATH).'social.lib.php';
require_once api_get_path(LIBRARY_PATH).'group_portal_manager.lib.php';

$this_section = SECTION_SOCIAL;

$interbreadcrumb[]= array ('url' =>'profile.php','name' => get_lang('Social'));
$interbreadcrumb[]= array ('url' =>'#','name' => get_lang('Invitations'));

$htmlHeadXtra[] = '<script src="'.api_get_path(WEB_LIBRARY_PATH).'javascript/jquery.js" type="text/javascript" language="javascript"></script>'; //jQuery
$htmlHeadXtra[] = '<script type="text/javascript" src="'.api_get_path(WEB_LIBRARY_PATH).'javascript/thickbox.js"></script>';
$htmlHeadXtra[] = '<link rel="stylesheet" href="'.api_get_path(WEB_LIBRARY_PATH).'javascript/thickbox.css" type="text/css" media="projection, screen">';

$htmlHeadXtra[] = '
<script type="text/javascript">
		
function denied_friend (element_input) {
	name_button=$(element_input).attr("id");
	name_div_id="id_"+name_button.substring(13);
	user_id=name_div_id.split("_");
	friend_user_id=user_id[1];	
	 $.ajax({
		contentType: "application/x-www-form-urlencoded",
		beforeSend: function(objeto) {
		$("#id_response").html("<img src=\'../inc/lib/javascript/indicator.gif\' />"); },
		type: "POST",
		url: "'.api_get_path(WEB_AJAX_PATH).'social.ajax.php?a=deny_friend",
		data: "denied_friend_id="+friend_user_id,
		success: function(datos) {
		 $("div#"+name_div_id).hide("slow");
		 $("#id_response").html(datos);
		}
	});
}
function register_friend(element_input) {
 if(confirm("'.get_lang('AddToFriends').'")) {
		name_button=$(element_input).attr("id");
		name_div_id="id_"+name_button.substring(13);
		user_id=name_div_id.split("_");
		user_friend_id=user_id[1];
		 $.ajax({
			contentType: "application/x-www-form-urlencoded",
			beforeSend: function(objeto) {
			$("div#dpending_"+user_friend_id).html("<img src=\'../inc/lib/javascript/indicator.gif\' />"); },
			type: "POST",
			url: "'.api_get_path(WEB_AJAX_PATH).'social.ajax.php?a=add_friend",
			data: "friend_id="+user_friend_id+"&is_my_friend="+"friend",
			success: function(datos) {  $("div#"+name_div_id).hide("slow");
				$("form").submit()
			}
		});
 }
}

</script>';
api_block_anonymous_users();

Display :: display_header($tool_name, 'Groups');

$user_online_list = WhoIsOnline(api_get_setting('time_limit_whosonline'), true);
$user_online_count = count($user_online_list); 
echo '<div class="social-header">';
echo '<table width="100%"><tr><td width="150px" bgcolor="#32578b"><center><span class="social-menu-text1">'.strtoupper(get_lang('Menu')).'</span></center></td>
		<td width="15px">&nbsp;</td><td bgcolor="#32578b">'.Display::return_icon('whoisonline.png','',array('hspace'=>'6')).'<a href="#" ><span class="social-menu-text1">'.get_lang('FriendsOnline').' '.$user_online_count.'</span></a></td>
		</tr></table>';
/*
echo '<div class="social-menu-title" align="center"><span class="social-menu-text1">'.get_lang('Menu').'</span></div>';
echo '<div class="social-menu-title-right">'.Display::return_icon('whoisonline.png','',array('hspace'=>'6')).'<a href="#" ><span class="social-menu-text1">'.$who_is_on_line.'</span></a></div>';
*/
echo '</div>';

// easy links
if (is_array($_GET) && count($_GET)>0) {
	foreach($_GET as $key => $value) { 
		switch ($key) {
			case 'accept':				
				$user_role = GroupPortalManager::get_user_group_role(api_get_user_id(), $value);				
				if (in_array($user_role , array(GROUP_USER_PERMISSION_PENDING_INVITATION_SENT_BY_USER,GROUP_USER_PERMISSION_PENDING_INVITATION))) {				
					GroupPortalManager::update_user_role(api_get_user_id(), $value, GROUP_USER_PERMISSION_READER);
					$show_message = get_lang('UserIsSubscribedToThisGroup');
				} else {
					$show_message = get_lang('UserIsNotSubscribedToThisGroup');
				}				
			break 2;			
			case 'deny':
				// delete invitation
				GroupPortalManager::delete_user_rel_group(api_get_user_id(), $value); 
				$show_message = get_lang('GroupInvitationWasDeny');
			break 2;
		}		
	}
}
 



$language_variable = get_lang('PendingInvitations');
$language_comment  = get_lang('SocialInvitesComment');



echo '<div id="social-content">';

	echo '<div id="social-content-left">';	
		//this include the social menu div
		SocialManager::show_social_menu('invitations');
	echo '</div>';

	echo '<div id="social-content-right">';
	
		if (! empty($show_message)){
			Display :: display_normal_message($show_message);
		}
	
		echo '<div id="id_response" align="center"></div>';
			$list_get_invitation=array();
			$user_id = api_get_user_id();
			
			$list_get_invitation		= SocialManager::get_list_invitation_of_friends_by_user_id($user_id);
			$list_get_invitation_sent	= SocialManager::get_list_invitation_sent_by_user_id($user_id);
			$pending_invitations 		= GroupPortalManager::get_groups_by_user($user_id, GROUP_USER_PERMISSION_PENDING_INVITATION);
			
			$number_loop=count($list_get_invitation);
			
			if ($number_loop != 0) {
				echo '<h2>'.get_lang('InvitationReceived').'</h2>';	
				
				foreach ($list_get_invitation as $invitation) { 
					$sender_user_id = $invitation['user_sender_id']
					?>
					<div id="<?php echo 'id_'.$sender_user_id ?>" class="invitation_confirm">
					   	<?php 
					   		$picture = UserManager::get_user_picture_path_by_id($sender_user_id,'web',false,true);
					   		$friends_profile = SocialManager::get_picture_user($sender_user_id, $picture['file'], 92);
					        $user_info	= api_get_user_info($sender_user_id);	        
					        $title		= api_convert_encoding($invitation['title'],$charset);
							$content	= api_convert_encoding($invitation['content'],$charset);
					        $date		= $invitation['send_date'];                  
					    ?>	   	
						<table cellspacing="0" border="0">
						<tbody>
							<tr>
								<td class="invitation_image">
									<a href="profile.php?u=<? echo $sender_user_id; ?>">
									<img src="<?php echo $friends_profile['file']; ?>" <?php echo $friends_profile['style']; ?> /></a>
								</td>
								<td class="info">
										<a class="profile_link" href="profile.php?u=<?php echo $sender_user_id;?>"><? echo api_get_person_name($user_info['firstName'], $user_info['lastName']);?></a>
										<div>
										<?php echo $title.' : '.$content;?>
										</div>
										<div>
										<?php echo get_lang('DateSend').' : '.$date;?>
										</div> 
										<div class="buttons">
					   						<button class="save" name="btn_accepted" type="submit" id="<?php echo "btn_accepted_".$sender_user_id ?>" value="<?php echo get_lang('Accept');?>"onclick="javascript:register_friend(this)">
					   						<?php echo get_lang('Accept') ?></button>
						     				<button class="cancel" name="btn_denied" type="submit" id="<?php echo "btn_deniedst_".$sender_user_id ?>" value="<?php echo get_lang('Deny'); ?>" onclick="javascript:denied_friend(this)" >
						     				<?php echo get_lang('Deny')?></button>
										</div>					
								</td>
							</tr>
						</tbody>
						</table>
					</div>
					<?php
				}
			}
			echo '<div class="clear"></div>';
			
			if (count($list_get_invitation_sent) > 0 ){	
				echo '<h2>'.get_lang('InvitationSent').'</h2>';
				foreach ($list_get_invitation_sent as $invitation) { 
					$sender_user_id = $invitation['user_receiver_id'];?>
					<div id="<?php echo 'id_'.$sender_user_id ?>" class="invitation_confirm">
					   	<?php 
					   		$picture = UserManager::get_user_picture_path_by_id($sender_user_id,'web',false,true);
					   		$friends_profile = SocialManager::get_picture_user($sender_user_id, $picture['file'], 92);
					        $user_info	= api_get_user_info($sender_user_id);	  
					              
					        $title		= api_convert_encoding($invitation['title'], $charset);
							$content	= api_convert_encoding($invitation['content'],$charset);
					        $date		= $invitation['send_date'];                  
					    ?>	   	
						<table cellspacing="0" border="0">
						<tbody>
							<tr>
								<td class="invitation_image">
									<a href="profile.php?u=<?php echo $sender_user_id;?>">
									<img src="<?php echo $friends_profile['file']; ?>" <?php echo $friends_profile['style']; ?> /></a>
								</td>
								<td class="info">
										<a class="profile_link" href="profile.php?u=<?php echo $sender_user_id; ?>"><?php echo api_get_person_name($user_info['firstName'], $user_info['lastName']);?></a>
										<div>
										<?php echo $title.' : '.$content;?>
										</div>
										<div>
										<?php echo get_lang('DateSend').' : '.$date;?>
										</div>		
								</td>
							</tr>
						</tbody>
						</table>
					</div>
				<?php
				}
			}
			
			if (count($pending_invitations) > 0) {					
				echo '<h2>'.get_lang('GroupsWaitingApproval').'</h2>';
				$new_invitation = array();				
				foreach ($pending_invitations as $invitation) {					
					$picture = GroupPortalManager::get_picture_group($invitation['id'], $invitation['picture_uri'],80);							
					$img = '<img class="social-groups-image" src="'.$picture['file'].'" hspace="4" height="50" border="2" align="left" width="50" />';
					
					$invitation['picture_uri'] = '<a href="groups.php?id='.$invitation['id'].'">'.$img.'</a>';		
					$invitation['name'] = '<a href="groups.php?id='.$invitation['id'].'">'.cut($invitation['name'],120,true).'</a>';
					$invitation['join'] = '<a href="invitations.php?accept='.$invitation['id'].'">'.get_lang('AcceptInvitation').'</a>';
					$invitation['deny'] = '<a href="invitations.php?deny='.$invitation['id'].'">'.get_lang('DenyInvitation').'</a>';
					$invitation['description'] = cut($invitation['description'],220,true);
					//$invitation['send_message'] = '<a href="'.api_get_path(WEB_PATH).'main/messages/send_message_to_userfriend.inc.php?height=300&width=610&user_friend='.$invitation['id'].'&view=profile&view_panel=1" class="thickbox" title="'.get_lang('SendMessage').'">';
					//$invitation['send_message'] .= Display::return_icon('message_new.png').'&nbsp;&nbsp;'.get_lang('SendMessage').'</a>';
					$new_invitation[]=$invitation;
				}
				Display::display_sortable_grid('waiting_user', array(), $new_invitation, array('hide_navigation'=>true, 'per_page' => 100), $query_vars, false, array(true, true, true,false,false,true,true,true,true));
			}
	echo '</div>';
echo '</div>';

Display::display_footer();
?>
