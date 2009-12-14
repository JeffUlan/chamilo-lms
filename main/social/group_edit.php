<?php 
/* For licensing terms, see /chamilo_license.txt */
/**
 * @package dokeos.social
 * @author Julio Montoya <gugli100@gmail.com>
 */

// Language files that should be included
$language_file = array('userInfo');
$cidReset = true;
include '../inc/global.inc.php';
$this_section = SECTION_SOCIAL;

$libpath = api_get_path(LIBRARY_PATH);
require_once $libpath.'fileManage.lib.php';
require_once $libpath.'fileUpload.lib.php';
require_once $libpath.'group_portal_manager.lib.php';
require_once $libpath.'formvalidator/FormValidator.class.php';
require_once $libpath.'image.lib.php';
require_once $libpath.'mail.lib.inc.php';
require_once $libpath.'social.lib.php';

$group_id = isset($_GET['id']) ? intval($_GET['id']) : intval($_POST['id']);
$tool_name = get_lang('GroupEdit');

$interbreadcrumb[] = array('url' => 'index.php','name' => get_lang('PlatformAdmin'));
$interbreadcrumb[] = array('url' => 'group_list.php','name' => get_lang('GroupList'));

$table_group = Database::get_main_table(TABLE_MAIN_GROUP);

$sql = "SELECT * FROM $table_group WHERE id = '".$group_id."'";
$res = Database::query($sql, __FILE__, __LINE__);
if (Database::num_rows($res) != 1) {
	header('Location: groups.php?id='.$group_id);
	exit;
}

//only group admins can edit the group
if (!GroupPortalManager::is_group_admin($group_id)) {
	api_not_allowed();		
}	


$group_data = Database::fetch_array($res, 'ASSOC');

// Create the form
$form = new FormValidator('group_edit', 'post', '', '', array('style' => 'width: 60%; float: '.($text_dir == 'rtl' ? 'right;' : 'left;')));
$form->addElement('hidden', 'id', $group_id);

// name
$form->addElement('text', 'name', get_lang('Name'), array('size'=>30));
$form->applyFilter('name', 'html_filter');
$form->applyFilter('name', 'trim');
$form->addRule('name', get_lang('ThisFieldIsRequired'), 'required');

// Description
$form->addElement('textarea', 'description', get_lang('Description'), array('rows'=>5, 'cols'=>50));
$form->applyFilter('description', 'html_filter');
$form->applyFilter('description', 'trim');

// url
$form->addElement('text', 'url', get_lang('URL'), array('size'=>30));
$form->applyFilter('url', 'html_filter');
$form->applyFilter('url', 'trim');

// Picture
$form->addElement('file', 'picture', get_lang('AddPicture'));
$allowed_picture_types = array ('jpg', 'jpeg', 'png', 'gif');
$form->addRule('picture', get_lang('OnlyImagesAllowed').' ('.implode(',', $allowed_picture_types).')', 'filetype', $allowed_picture_types);
if (strlen($group_data['picture_uri']) > 0) {
	$form->addElement('checkbox', 'delete_picture', '', get_lang('DelImage'));
}


// Status
$status = array();
$status[GROUP_PERMISSION_OPEN] 		= get_lang('Open');
$status[GROUP_PERMISSION_CLOSED]	= get_lang('Closed');
$form->addElement('select', 'visibility', get_lang('GroupPermissions'), $status, array());


// Submit button
$form->addElement('style_submit_button', 'submit', get_lang('ModifyInformation'), 'class="save"');

// Set default values
$form->setDefaults($group_data);

// Validate form
if ( $form->validate()) {
	$group = $form->exportValues();
	
	$picture_element = & $form->getElement('picture');
	$picture = $picture_element->getValue();

	$picture_uri = $group_data['picture_uri'];
	if ($group['delete_picture']) {
		$picture_uri = GroupPortalManager::delete_group_picture($group_id);
		}
	elseif (!empty($picture['name'])) {
		$picture_uri = GroupPortalManager::update_group_picture($group_id, $_FILES['picture']['name'], $_FILES['picture']['tmp_name']);
	}
	
	$name 			= $group['name'];
	$description	= $group['description'];
	$url 			= $group['url'];	
	$status 		= intval($group['visibility']);
	
	GroupPortalManager::update($group_id, $name, $description, $url, $status, $picture_uri);	
	$tok = Security::get_token();
	header('Location: groups.php?id='.$group_id.'&action=show_message&message='.urlencode(get_lang('GroupUpdated')).'&sec_token='.$tok);
	exit();
}

Display::display_header($tool_name);

//show the action menu
SocialManager::show_social_menu();
echo '<div class="actions-title">';
echo get_lang('Groups');
echo '</div>';



// Group picture
$image_path = GroupPortalManager::get_group_picture_path_by_id($group_id,'web');
$image_dir = $image_path['dir'];
$image = $image_path['file'];
$image_file = ($image != '' ? $image_dir.$image : api_get_path(WEB_CODE_PATH).'img/unknown_group.jpg');
$image_size = api_getimagesize($image_file);

$img_attributes = 'src="'.$image_file.'?rand='.time().'" '
	.'alt="'.api_get_person_name($user_data['firstname'], $user_data['lastname']).'" '
	.'style="float:'.($text_dir == 'rtl' ? 'left' : 'right').'; padding:5px;" ';

if ($image_size[0] > 300) { //limit display width to 300px
	$img_attributes .= 'width="300" ';
}

// get the path,width and height from original picture
$big_image = $image_dir.'big_'.$image;
$big_image_size = api_getimagesize($big_image);
$big_image_width = $big_image_size[0];
$big_image_height = $big_image_size[1];
$url_big_image = $big_image.'?rnd='.time();
/*
if ($image == '') {
	echo '<img '.$img_attributes.' />';
} else {
	echo '<input type="image" '.$img_attributes.' onclick="javascript: return show_image(\''.$url_big_image.'\',\''.$big_image_width.'\',\''.$big_image_height.'\');"/>';
}
*/


//Shows left column
echo GroupPortalManager::show_group_column_information($group_id, api_get_user_id());


//-- Show message groups

echo '<div id="layout_right" style="margin-left: 282px;">';	
	
		// Display form
$form->display();
	
echo '</div>'; // end layout right


// Footer
Display::display_footer();
