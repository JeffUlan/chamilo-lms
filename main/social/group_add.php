<?php
/* For licensing terms, see /chamilo_license.txt */
/**
 * @package dokeos.social
 * @author Julio Montoya <gugli100@gmail.com>
 */
 
$language_file= 'userInfo';
$cidReset=true;
require_once '../inc/global.inc.php';
require_once api_get_path(LIBRARY_PATH).'/formvalidator/FormValidator.class.php';
require_once api_get_path(LIBRARY_PATH).'social.lib.php';
require_once api_get_path(LIBRARY_PATH).'group_portal_manager.lib.php';

api_block_anonymous_users();

if (api_get_setting('allow_students_to_create_groups_in_social') == 'false' && !api_is_allowed_to_edit()) {
	api_not_allowed();
}

global $charset;

$htmlHeadXtra[] = '<script src="'.api_get_path(WEB_LIBRARY_PATH).'javascript/jquery.js" type="text/javascript" language="javascript"></script>'; //jQuery
$htmlHeadXtra[] = '<script type="text/javascript">
textarea = "";
num_characters_permited = 255;
function text_longitud(){
   num_characters = document.forms[0].description.value.length;
  if (num_characters > num_characters_permited){
      document.forms[0].description.value = textarea;
   }else{
      textarea = document.forms[0].description.value;
   } 
} 
</script>';
					
$table_message = Database::get_main_table(TABLE_MESSAGE);

$form = new FormValidator('add_group');

// name
$form->addElement('text', 'name', get_lang('Name'), array('size'=>60, 'maxlength'=>120));
$form->applyFilter('name', 'html_filter');
$form->applyFilter('name', 'trim');
$form->addRule('name', get_lang('ThisFieldIsRequired'), 'required');

// Description
$form->addElement('textarea', 'description', get_lang('Description'), array('rows'=>3, 'cols'=>58, onKeyDown => "text_longitud()", onKeyUp => "text_longitud()"));
$form->applyFilter('description', 'html_filter');
$form->applyFilter('description', 'trim');

// url
$form->addElement('text', 'url', get_lang('URL'), array('size'=>35));
$form->applyFilter('url', 'html_filter');
$form->applyFilter('url', 'trim');

// Picture
$form->addElement('file', 'picture', get_lang('AddPicture'));
$allowed_picture_types = array ('jpg', 'jpeg', 'png', 'gif');
$form->addRule('picture', get_lang('OnlyImagesAllowed').' ('.implode(',', $allowed_picture_types).')', 'filetype', $allowed_picture_types);

// Status
$status = array();
$status[GROUP_PERMISSION_OPEN] 		= get_lang('Open');
$status[GROUP_PERMISSION_CLOSED]	= get_lang('Closed');

$form->addElement('select', 'visibility', get_lang('GroupPermissions'), $status);
$form->addElement('style_submit_button','add_group', get_lang('AddGroup'),'class="save"');

$form->setRequiredNote(api_xml_http_response_encode('<span class="form_required">*</span> <small>'.get_lang('ThisFieldIsRequired').'</small>'));
$form->setDefaults($default);
if ($form->validate()) {
	$values = $form->exportValues();

	$picture_element = & $form->getElement('picture');
	$picture 		= $picture_element->getValue();
	$picture_uri 	= '';
	$name 			= $values['name'];
	$description	= $values['description'];
	$url 			= $values['url'];	
	$status 		= intval($values['visibility']);
	$picture 		= $_FILES['picture'];

	$group_id = GroupPortalManager::add($name, $description, $url, $status);
	GroupPortalManager::add_user_to_group(api_get_user_id(), $group_id,GROUP_USER_PERMISSION_ADMIN);
		
	if (!empty($picture['name'])) {
		$picture_uri = GroupPortalManager::update_group_picture($group_id, $_FILES['picture']['name'], $_FILES['picture']['tmp_name']);
		GroupPortalManager::update($group_id, $name, $description, $url,$status, $picture_uri);
	}
	header('Location: groups.php?id='.$group_id.'&action=show_message&message='.urlencode(get_lang('GroupAdded')));
	exit();		
}

$nameTools = get_lang('AddGroup');
$this_section = SECTION_SOCIAL;

$interbreadcrumb[]= array ('url' =>'home.php','name' => get_lang('Social'));
Display :: display_header($tool_name, 'Groups');

$user_online_list = WhoIsOnline(api_get_setting('time_limit_whosonline'));
$user_online_count = count($user_online_list); 
echo '<div class="actions-title-groups">';
echo '<table width="100%"><tr><td width="150px" bgcolor="#32578b"><center><span class="menuTex1">'.strtoupper(get_lang('Menu')).'</span></center></td>
		<td width="15px">&nbsp;</td><td bgcolor="#32578b">'.Display::return_icon('whoisonline.png','',array('hspace'=>'6')).'<a href="#" ><span class="menuTex1">'.get_lang('FriendsOnline').' '.$user_online_count.'</span></a></td>
		</tr></table>';
/*
echo '<div class="menuTitle" align="center"><span class="menuTex1">'.get_lang('Menu').'</span></div>';
echo '<div class="TitleRigth">'.Display::return_icon('whoisonline.png','',array('hspace'=>'6')).'<a href="#" ><span class="menuTex1">'.$who_is_on_line.'</span></a></div>';
*/
echo '</div>';
/*
echo '<div class="actions-title">';
echo get_lang('Groups');
echo '</div>';
*/
echo '<div id="socialContent">';
	echo '<div id="socialContentLeft">';
		//show the action menu			
		SocialManager::show_social_menu('groups');
	echo '</div>';
	echo '<div id="socialContentRigth">';
		$form->display();	
	echo '</div>';
echo '</div>';

Display :: display_footer();
?>