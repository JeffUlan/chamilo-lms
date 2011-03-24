<?php
/* For licensing terms, see /license.txt */
/**
 * @package chamilo.social
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

//jquery thickbox already called from main/inc/header.inc.php

$htmlHeadXtra[] = '<script type="text/javascript">
		
function show_icon_edit(element_html) {	
	ident="#edit_image";
	$(ident).show();
}		

function hide_icon_edit(element_html)  {
	ident="#edit_image";
	$(ident).hide();
}		
		
</script>';

Display :: display_header($tool_name);

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
				$users  = UserManager::get_all_user_tags($query, 0, 0, 5);
				$groups = GroupPortalManager::get_all_group_tags($query);
				
				if (empty($users) && empty($groups)) {
					echo get_lang('SorryNoResults');	
				}
						
				$results = array();
				if (is_array($users) && count($users)> 0) {					
					echo '<h2>'.get_lang('Users').'</h2>';			
					foreach($users as $user) {
					    
					    if (empty($user['picture_uri'])) {
                            $picture['file'] = api_get_path(WEB_CODE_PATH).'img/unknown_180_100.jpg'; 
                        } else {
                            $picture = UserManager::get_picture_user($user['user_id'], $user['picture_uri'], 80, USER_IMAGE_SIZE_ORIGINAL );    
                        }
						//$picture = UserManager::get_picture_user($user['user_id'], $user['picture_uri'],'', USER_IMAGE_SIZE_ORIGINAL);
						$url_open = '<a href="'.api_get_path(WEB_PATH).'main/social/profile.php?u='.$user['user_id'].'">';
						$url_close ='</a>';
						$img = $url_open.'<img src="'.$picture['file'].'" />'.$url_close;
						$user['firstname'] = $url_open.$user['firstname'].$url_close;
						$user['lastname'] = $url_open.$user['lastname']. $url_close;						
						$results[] = array($img, $user['firstname'], $user['lastname'], $user['tag']);			
					}					
					echo '<div class="social-box-container2">';
					echo '<div>'.Display::return_icon('content-post-group1.jpg',get_lang('Users')).'</div>';
            
					echo '<div id="div_content_table" class="social-box-content2">';				
						Display::display_sortable_grid('online', array(), $results, array('hide_navigation'=>true, 'per_page' => 5), $query_vars, false ,true);
					echo '</div>';
					echo '</div>';						
				}	
				
				
				
				//get users from tags
				if (is_array($results) && count($results) > 0) {
					foreach ($results as $result) {
						$id = $result['id'];
						$url_open  = '<a href="groups.php?id='.$id.'">';
						$url_close = '</a>';
						
						$name = api_strtoupper(cut($result['name'],25,true));				
						if ($result['relation_type'] == GROUP_USER_PERMISSION_ADMIN) {		 	
							$name .= Display::return_icon('social_group_admin.png', get_lang('Admin'), array('style'=>'vertical-align:middle'));
						} elseif ($result['relation_type'] == GROUP_USER_PERMISSION_MODERATOR) {			
							$name .= Display::return_icon('social_group_moderator.png', get_lang('Moderator'), array('style'=>'vertical-align:middle'));
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
						$item_1 = '<div>'.$url_open.$result['picture_uri'].'<strong>'.$name.'<br />('.$count_users_group.')</strong>'.$url_close.Display::return_icon('linegroups.jpg').'</div>';
					
						if ($result['description'] != '') {
							$item_2 = '<div class="box_description_group_title" ><span class="social-groups-text2">'.get_lang('Description').'</span></div>';
							$item_3 = '<div class="box_description_group_content" >'.cut($result['description'],100,true).'</div>';
						} else {
							$item_2 = '<div class="box_description_group_title" ><span class="social-groups-text2"></span></div>';
							$item_3 = '<div class="box_description_group_content" ></div>';
						}
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
						$name = cut($group['name'],25,true);
						$count_users_group = count(GroupPortalManager::get_all_users_by_group($id));
						if ($count_users_group == 1 ) {
							$count_users_group = $count_users_group.' '.get_lang('Member');	
						} else {
							$count_users_group = $count_users_group.' '.get_lang('Members');
						}				
					    $picture = GroupPortalManager::get_picture_group($group['id'], $group['picture_uri'],80);
						$tags = GroupPortalManager::get_group_tags($group['id']);
						$group['picture_uri'] = '<img class="social-groups-image" src="'.$picture['file'].'" hspace="4" height="50" border="2" align="left" width="50" />';			
						

        				$item_0 = Display::div($group['picture_uri'], array('class'=>'box_description_group_image'));
        				$members = Display::span($count_users_group, array('class'=>'box_description_group_member'));
        				$item_1  = Display::div(Display::tag('h3', $url_open.$name.$url_close).$members, array('class'=>'box_description_group_title'));
        				
        				$item_2 = '';
        				$item_3 = '';
        				if ($group['description'] != '') {					
        					$item_3 = '<div class="box_description_group_content" >'.cut($group['description'],100,true).'</div>';
        				} else {
        					$item_2 = '<div class="box_description_group_title" ><span class="social-groups-text2"></span></div>';
        					$item_3 = '<div class="box_description_group_content" ></div>';
        				}
						$item_4 = '<div class="box_description_group_tags" >'.$tags.'</div>';	
        				$item_5 = '<div class="box_description_group_actions" >'.$url_open.get_lang('SeeMore').$url_close.'</div>';			
					

                        /*$join_url = '<a href="groups.php?id='.$group_id.'&action=join&u='.api_get_user_id().'">'.Display::return_icon('group_join.png', get_lang('JoinGroup'), array('hspace'=>'6')).''.get_lang('JoinGroup').'</a> ';                
        				$item_4 = '<div class="box_description_group_actions" >'.$join_url. $url_open.get_lang('SeeMore').$url_close.'</div>';*/				
        				$grid_item_2 = $item_0.$item_1.$item_2.$item_3.$item_4.$item_5;
        				$grid_groups[]= array('',$grid_item_2);			
						//$grid_groups[]= array('', $data);									
					}		
				}
				echo '<div class="social-box-container2">';									
						Display::display_sortable_grid('mygroups', array(), $grid_groups, array('hide_navigation'=>true, 'per_page' => 5), $query_vars,  false, array(true,true,true,true,true));		
					echo '</div>';
				
			}		
		} else {
			//we should show something
		}
					
	echo '</div>';
echo '</div>';

Display :: display_footer();
?>