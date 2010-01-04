<?php
/* For licensing terms, see /chamilo_license.txt */
/**
 * @package dokeos.social
 * @author Julio Montoya <gugli100@gmail.com>
 */
 
// name of the language file that needs to be included
$language_file = array('registration','admin','userInfo');
$cidReset = true;
require_once '../inc/global.inc.php';
require_once api_get_path(LIBRARY_PATH).'usermanager.lib.php';
require_once api_get_path(LIBRARY_PATH).'social.lib.php';
require_once api_get_path(LIBRARY_PATH).'group_portal_manager.lib.php';

api_block_anonymous_users();

$this_section = SECTION_SOCIAL;
$tool_name = get_lang('Search');
$interbreadcrumb[]= array ('url' =>'profile.php','name' => get_lang('Social'));

Display :: display_header($tool_name);

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

echo '<div id="social-content">';
	echo '<div id="social-content-left">';
		//show the action menu			
		SocialManager::show_social_menu('search');
	echo '</div>';
	echo '<div id="social-content-right">';
		
		$query = $_GET['q'];
		echo UserManager::get_search_form($query);
			
		//I'm searching something
		if ($query != '') {
			if (isset($query) && $query!='') {		
				//get users from tags
				$users = UserManager::get_all_user_tags($query, 0, 0, 5);	
				$groups = GroupPortalManager::get_all_group_tags($query);
				
				if (empty($users) && empty($groups)) {
					echo get_lang('SorryNoResults');	
				}
						
				$results = array();
				if (is_array($users) && count($users)> 0) {
					
					echo '<h2>'.get_lang('Users').'</h2>';			
					foreach($users as $user) {
						$picture = UserManager::get_picture_user($user['user_id'], $user['picture_uri'],80);
						$url_open = '<a href="'.api_get_path(WEB_PATH).'main/social/profile.php?u='.$user['user_id'].'">';
						$url_close ='</a>';
						$img = $url_open.'<img src="'.$picture['file'].'" />'.$url_close;
						$user['firstname'] = $url_open.$user['firstname'].$url_close;
						$user['lastname'] = $url_open.$user['lastname'].$url_close;						
						$results[] = array($img, $user['firstname'],$user['lastname'],$user['tag']);			
					}					
					echo '<div class="social-box-container2">';
					echo '<div>'.Display::return_icon('content-post-group1.jpg').'</div>';
					echo '<div id="div_content_table" class="social-box-content2">';					
							Display::display_sortable_grid('search_user', array(), $results, array('hide_navigation'=>true, 'per_page' => 5), $query_vars, false ,true);
					echo '</div>';
					echo '</div>';						
				}	
				
				
				
				//get users from tags
				if (is_array($results) && count($results) > 0) {
					foreach ($results as $result) {
						$id = $result['id'];
						$url_open  = '<a href="groups.php?id='.$id.'">';
						$url_close = '</a>';
						
						$name = strtoupper(cut($result['name'],25,true));				
						if ($result['relation_type'] == GROUP_USER_PERMISSION_ADMIN) {		 	
							$name .= Display::return_icon('admin_star.png', get_lang('Admin'), array('style'=>'vertical-align:middle'));
						} elseif ($result['relation_type'] == GROUP_USER_PERMISSION_MODERATOR) {			
							$name .= Display::return_icon('moderator_star.png', get_lang('Moderator'), array('style'=>'vertical-align:middle'));
						}
						$count_users_group = count(GroupPortalManager::get_all_users_by_group($id));
						if ($count_users_group == 1 ) {
							$count_users_group = $count_users_group.' '.get_lang('Member');	
						} else {
							$count_users_group = $count_users_group.' '.get_lang('Members');
						}					
						
						$picture = GroupPortalManager::get_picture_group($result['id'], $result['picture_uri'],80);							
						$result['picture_uri'] = '<img class="social-groups-image" src="'.$picture['file'].'" hspace="4" height="50" border="2" align="left" width="50" />';			
						$grid_item_1 = Display::return_icon('boxmygroups.jpg');						
						$item_1 = '<div>'.$url_open.$result['picture_uri'].'<p class="social-groups-text1"><strong>'.$name.'<br />('.$count_users_group.')</strong></p>'.$url_close.Display::return_icon('linegroups.jpg').'</div>';
						$item_2 = '<div class="box_description_group_title" ><span class="social-groups-text2">'.strtoupper(get_lang('DescriptionGroup')).'</span></div>';
						$item_3 = '<div class="box_description_group_content" >'.cut($result['description'],100,true).'</div>';	
						$item_4 = '<div class="box_description_group_actions" >'.$url_open.get_lang('SeeMore').$url_close.'</div>';			
						$grid_item_2 = $item_1.$item_2.$item_3.$item_4;				
						$grid_my_groups[]= array($grid_item_1,$grid_item_2);
					}
				}
				
				$grid_groups = array();
				if (is_array($groups) && count($groups)>0) {
					echo '<h2>'.get_lang('Groups').'</h2>';
					foreach($groups as $group) {
						
						$id = $group['id'];
						$url_open  = '<a href="groups.php?id='.$id.'">';
						$url_close = '</a>';
						
						$name = strtoupper(cut($group['name'],25,true));
						$count_users_group = count(GroupPortalManager::get_all_users_by_group($id));
						if ($count_users_group == 1 ) {
							$count_users_group = $count_users_group.' '.get_lang('Member');	
						} else {
							$count_users_group = $count_users_group.' '.get_lang('Members');
						}				
						$picture = GroupPortalManager::get_picture_group($group['id'], $group['picture_uri'],80);
						$tags = GroupPortalManager::get_group_tags($group['id']);						
						$group['picture_uri'] = '<img class="social-groups-image" src="'.$picture['file'].'" hspace="4" height="50" border="2" align="left" width="50" />';			
						$grid_item_1 = Display::return_icon('boxmygroups.jpg');						
						$item_1 = '<div>'.$url_open.$group['picture_uri'].'<p class="social-groups-text1"><strong>'.$name.'<br />('.$count_users_group.')</strong></p>'.$url_close.Display::return_icon('linegroups.jpg').'</div>';
						$item_2 = '<div class="box_description_group_title" ><span class="social-groups-text2">'.strtoupper(get_lang('DescriptionGroup')).'</span></div>';
						$item_3 = '<div class="box_description_group_content" >'.cut($group['description'],100,true).'</div>';
						$item_4 = '<div class="box_description_group_tags" >'.$tags.'</div>';	
						$item_5 = '<div class="box_description_group_actions" >'.$url_open.get_lang('SeeMore').$url_close.'</div>';			
						$grid_item_2 = $item_1.$item_2.$item_3.$item_4.$item_5;				
						$grid_groups[]= array($grid_item_1,$grid_item_2);
									
					}		
				}		
				Display::display_sortable_grid('search_group', array(), $grid_groups, array('hide_navigation'=>true, 'per_page' => 5), $query_vars,  false, array(true,true,true,true,true));							    
			}		
		} else {
			//we should show something
		}
					
	echo '</div>';
echo '</div>';

Display :: display_footer();
?>