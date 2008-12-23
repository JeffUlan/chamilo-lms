<?php // $Id: profile.php 17445 2008-12-23 22:47:46Z derrj $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2008 Dokeos SPRL
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Roan Embrechts (Vrije Universiteit Brussel)

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
==============================================================================
* This file displays the user's profile,
* optionally it allows users to modify their profile as well.
*
* See inc/conf/profile.conf.php to modify settings
*
* @package dokeos.auth
==============================================================================
*/

/*
==============================================================================
		INIT SECTION
==============================================================================
*/
// name of the language file that needs to be included
$language_file = 'registration';
$cidReset = true;

require ('../inc/global.inc.php');
require_once (api_get_path(LIBRARY_PATH).'formvalidator/FormValidator.class.php');
$this_section = SECTION_MYPROFILE;
 
api_block_anonymous_users();

$htmlHeadXtra[] = '<script type="text/javascript">
function confirmation(name)
{
	if (confirm("'.get_lang('AreYouSureToDelete').' " + name + " ?"))
		{return true;}
	else
		{return false;}
}
		
function show_image(image,width,height) {
	width = parseInt(width) + 20;
	height = parseInt(height) + 20;			
	window_x = window.open(\'\',\'windowX\',\'width=\'+ width + \', height=\'+ height + \'\');
	window_x.document.write("<img src=\'"+image+"?rand='.time().'\'/>");		
}
				
</script>';

if (!empty ($_GET['coursePath']))
{
	$course_url = api_get_path(WEB_COURSE_PATH).htmlentities(strip_tags($_GET['coursePath'])).'/index.php';
	$interbreadcrumb[] = array ('url' => $course_url, 'name' => Security::remove_XSS($_GET['courseCode']));
}
$warning_msg = '';
if(!empty($_GET['fe']))
{
	$warning_msg .= get_lang('UplUnableToSaveFileFilteredExtension');
	$_GET['fe'] = null;
}
/*
-----------------------------------------------------------
	Configuration file
-----------------------------------------------------------
*/
require_once (api_get_path(CONFIGURATION_PATH).'profile.conf.php');

/*
-----------------------------------------------------------
	Libraries
-----------------------------------------------------------
*/
include_once (api_get_path(LIBRARY_PATH).'fileManage.lib.php');
include_once (api_get_path(LIBRARY_PATH).'fileUpload.lib.php');
include_once (api_get_path(LIBRARY_PATH).'image.lib.php');
require_once (api_get_path(LIBRARY_PATH).'usermanager.lib.php');

if (is_profile_editable())
	$tool_name = get_lang('ModifProfile');
else
	$tool_name = get_lang('ViewProfile');

$table_user = Database :: get_main_table(TABLE_MAIN_USER);

/*
-----------------------------------------------------------
	Form
-----------------------------------------------------------
*/
/*
 * Get initial values for all fields.
 */
$user_data = UserManager::get_user_info_by_id(api_get_user_id());
if ($user_data !== false)
{
	if (is_null($user_data['language']))
		$user_data['language'] = api_get_setting('platformLanguage');
}

$user_image = UserManager::get_user_picture_path_by_id(api_get_user_id(),'none');
$fck_attribute['Height'] = "150";
$fck_attribute['Width'] = "100%";
$fck_attribute['ToolbarSet'] = "Profil";

/*
 * Initialize the form.
 */
$form = new FormValidator('profile', 'post', api_get_self()."?".str_replace('&fe=1','',$_SERVER['QUERY_STRING']), null, array('style' => 'width: 75%; float: '.($text_dir=='rtl'?'right;':'left;')));

/* Make sure this is the first submit on the form, even though it is hidden!
 * Otherwise, if a user has productions and presses ENTER to submit, he will
 * attempt to delete the first production in the list. */
if (is_profile_editable())
	$form->addElement('submit', null, get_lang('Ok'), array('style' => 'visibility:hidden;'));

//	SUBMIT (visible)
if (is_profile_editable())
{
	$form->addElement('submit', 'apply_change', get_lang('Ok'));
}
else
{
	$form->freeze();
}

//THEME
if (is_profile_editable() && api_get_setting('user_selected_theme') == 'true')
{
	$form->addElement('select_theme', 'theme', get_lang('Theme'));
	if (api_get_setting('profile', 'theme') !== 'true')
		$form->freeze('theme');
	$form->applyFilter('theme', 'trim');
	//if (api_get_setting('registration', 'openid') == 'true')
	//	$form->addRule('openid', get_lang('ThisFieldIsRequired'), 'required');
}

//	LAST NAME and FIRST NAME
$form->addElement('text', 'lastname',  get_lang('LastName'),  array('size' => 40));
$form->addElement('text', 'firstname', get_lang('FirstName'), array('size' => 40));
if (api_get_setting('profile', 'name') !== 'true')
	$form->freeze(array('lastname', 'firstname'));
$form->applyFilter(array('lastname', 'firstname'), 'stripslashes');
$form->applyFilter(array('lastname', 'firstname'), 'trim');
$form->addRule('lastname' , get_lang('ThisFieldIsRequired'), 'required');
$form->addRule('firstname', get_lang('ThisFieldIsRequired'), 'required');


//	USERNAME
$form->addElement('text', 'username', get_lang('UserName'), array('size' => 40));
if (api_get_setting('profile', 'login') !== 'true')
	$form->freeze('username');
$form->applyFilter('username', 'stripslashes');
$form->applyFilter('username', 'trim');
$form->addRule('username', get_lang('ThisFieldIsRequired'), 'required');
$form->addRule('username', get_lang('UsernameWrong'), 'username');
$form->addRule('username', get_lang('UserTaken'), 'username_available', $user_data['username']);


//	OFFICIAL CODE
if (CONFVAL_ASK_FOR_OFFICIAL_CODE)
{
	$form->addElement('text', 'official_code', get_lang('OfficialCode'), array('size' => 40));
	if (api_get_setting('profile', 'officialcode') !== 'true')
		$form->freeze('official_code');
	$form->applyFilter('official_code', 'stripslashes');
	$form->applyFilter('official_code', 'trim');
	if (api_get_setting('registration', 'officialcode') == 'true' && api_get_setting('profile', 'officialcode') == 'true')
		$form->addRule('official_code', get_lang('ThisFieldIsRequired'), 'required');
}

//	EMAIL
$form->addElement('text', 'email', get_lang('Email'), array('size' => 40));
if (api_get_setting('profile', 'email') !== 'true')
	$form->freeze('email');
$form->applyFilter('email', 'stripslashes');
$form->applyFilter('email', 'trim');
if (api_get_setting('registration', 'email') == 'true')
	$form->addRule('email', get_lang('ThisFieldIsRequired'), 'required');
$form->addRule('email', get_lang('EmailWrong'), 'email');

// OPENID URL
if(is_profile_editable() && api_get_setting('openid_authentication')=='true')
{
	$form->addElement('text', 'openid', get_lang('OpenIDURL'), array('size' => 40));
	if (api_get_setting('profile', 'openid') !== 'true')
		$form->freeze('openid');
	$form->applyFilter('openid', 'trim');
	//if (api_get_setting('registration', 'openid') == 'true')
	//	$form->addRule('openid', get_lang('ThisFieldIsRequired'), 'required');
}

//	PHONE
$form->addElement('text', 'phone', get_lang('phone'), array('size' => 20));
if (api_get_setting('profile', 'phone') !== 'true')
	$form->freeze('phone');
$form->applyFilter('phone', 'stripslashes');
$form->applyFilter('phone', 'trim');
/*if (api_get_setting('registration', 'phone') == 'true')
	$form->addRule('phone', get_lang('ThisFieldIsRequired'), 'required');
$form->addRule('phone', get_lang('EmailWrong'), 'email');*/


//	PICTURE
if (is_profile_editable() && api_get_setting('profile', 'picture') == 'true')
{
	$form->addElement('file', 'picture', ($user_image != '' ? get_lang('UpdateImage') : get_lang('AddImage')));
	$form->add_progress_bar();
	if( strlen($user_data['picture_uri']) > 0)
	{
		$form->addElement('checkbox', 'remove_picture', null, get_lang('DelImage'));
	}
	$form->addRule('picture', get_lang('OnlyImagesAllowed'), 'mimetype', array('image/gif', 'image/jpeg', 'image/png','image/pjpeg'));
}

//	LANGUAGE
$form->addElement('select_language', 'language', get_lang('Language'));
if (api_get_setting('profile', 'language') !== 'true')
	$form->freeze('language');

//	EXTENDED PROFILE
if (api_get_setting('extended_profile') == 'true')
{
	$form->addElement('static', null, '<em>'.get_lang('OptionalTextFields').'</em>');

	//	MY COMPETENCES
	$form->add_html_editor('competences', get_lang('MyCompetences'), false);

	//	MY DIPLOMAS
	$form->add_html_editor('diplomas', get_lang('MyDiplomas'), false);

	//	WHAT I AM ABLE TO TEACH
	$form->add_html_editor('teach', get_lang('MyTeach'), false);

	//	MY PRODUCTIONS
	$form->addElement('file', 'production', get_lang('MyProductions'));
	if ($production_list = UserManager::build_production_list($_user['user_id'],'',true))
	{
			$form->addElement('static', 'productions_list', null, $production_list);
	}

	//	MY PERSONAL OPEN AREA
	$form->add_html_editor('openarea', get_lang('MyPersonalOpenArea'), false);

	$form->applyFilter(array('competences', 'diplomas', 'teach', 'openarea'), 'stripslashes');
	$form->applyFilter(array('competences', 'diplomas', 'teach'), 'trim'); // openarea is untrimmed for maximum openness
}

//	PASSWORD
if (is_profile_editable() && api_get_setting('profile', 'password') == 'true')
{
	$form->addElement('static', null, null, '<em>'.get_lang('Enter2passToChange').'</em>');

	$form->addElement('password', 'password1', get_lang('Pass'),         array('size' => 40));
	$form->addElement('password', 'password2', get_lang('Confirmation'), array('size' => 40));

	//	user must enter identical password twice so we can prevent some user errors
	$form->addRule(array('password1', 'password2'), get_lang('PassTwo'), 'compare');
	if (CHECK_PASS_EASY_TO_FIND)
		$form->addRule('password1', get_lang('PassTooEasy').': '.api_generate_password(), 'callback', 'api_check_password');
}

// EXTRA FIELDS
$extra = UserManager::get_extra_fields(0,50,5,'ASC');
$extra_data = UserManager::get_extra_user_data(api_get_user_id(),true);
foreach($extra as $id => $field_details)
{
	if($field_details[6] == 0)
	{
		continue;
	}
	switch($field_details[2])
	{
		case USER_FIELD_TYPE_TEXT:
			$form->addElement('text', 'extra_'.$field_details[1], $field_details[3], array('size' => 40));
			$form->applyFilter('extra_'.$field_details[1], 'stripslashes');
			$form->applyFilter('extra_'.$field_details[1], 'trim');
			if ($field_details[7] == 0)	$form->freeze('extra_'.$field_details[1]);
			break;
		case USER_FIELD_TYPE_TEXTAREA:
			$form->add_html_editor('extra_'.$field_details[1], $field_details[3], false);
			//$form->addElement('textarea', 'extra_'.$field_details[1], $field_details[3], array('size' => 80));
			$form->applyFilter('extra_'.$field_details[1], 'stripslashes');
			$form->applyFilter('extra_'.$field_details[1], 'trim');
			if ($field_details[7] == 0)	$form->freeze('extra_'.$field_details[1]);
			break;
		case USER_FIELD_TYPE_RADIO:
			$group = array();
			foreach($field_details[8] as $option_id => $option_details)
			{
				$options[$option_details[1]] = $option_details[2];
				$group[] =& HTML_QuickForm::createElement('radio', 'extra_'.$field_details[1], $option_details[1],$option_details[2].'<br />',$option_details[1]);
			}
			$form->addGroup($group, 'extra_'.$field_details[1], $field_details[3], '');
			if ($field_details[7] == 0)	$form->freeze('extra_'.$field_details[1]);	
			break;
		case USER_FIELD_TYPE_SELECT:
			$options = array();
			foreach($field_details[8] as $option_id => $option_details)
			{
				$options[$option_details[1]] = $option_details[2];
			}
			$form->addElement('select','extra_'.$field_details[1],$field_details[3],$options,'');	
			if ($field_details[7] == 0)	$form->freeze('extra_'.$field_details[1]);			
			break;
		case USER_FIELD_TYPE_SELECT_MULTIPLE:
			$options = array();
			foreach($field_details[8] as $option_id => $option_details)
			{
				$options[$option_details[1]] = $option_details[2];
			}
			$form->addElement('select','extra_'.$field_details[1],$field_details[3],$options,array('multiple' => 'multiple'));
			if ($field_details[7] == 0)	$form->freeze('extra_'.$field_details[1]);	
			break;
		case USER_FIELD_TYPE_DATE:
			$form->addElement('datepickerdate', 'extra_'.$field_details[1], $field_details[3]);
			$form->_elements[$form->_elementIndex['extra_'.$field_details[1]]]->setLocalOption('minYear',1900);
			if ($field_details[7] == 0)	$form->freeze('extra_'.$field_details[1]);
			$form->applyFilter('theme', 'trim');
			break;
		case USER_FIELD_TYPE_DATETIME:
			$form->addElement('datepicker', 'extra_'.$field_details[1], $field_details[3]);
			if ($field_details[7] == 0)	$form->freeze('extra_'.$field_details[1]);
			$form->applyFilter('theme', 'trim');
			break;
		case USER_FIELD_TYPE_DOUBLE_SELECT:
			foreach ($field_details[8] as $key=>$element)
			{
				if ($element[2][0] == '*')
				{
					$values['*'][$element[0]] = str_replace('*','',$element[2]);
				}
				else 
				{
					$values[0][$element[0]] = $element[2];
				}
			}
			
			$group='';
			$group[] =& HTML_QuickForm::createElement('select', 'extra_'.$field_details[1],'',$values[0],'');
			$group[] =& HTML_QuickForm::createElement('select', 'extra_'.$field_details[1].'*','',$values['*'],'');
			$form->addGroup($group, 'extra_'.$field_details[1], $field_details[3], '&nbsp;');
			if ($field_details[7] == 0)	$form->freeze('extra_'.$field_details[1]);

			// recoding the selected values for double : if the user has selected certain values, we have to assign them to the correct select form
			if (key_exists('extra_'.$field_details[1], $extra_data))
			{
				// exploding all the selected values (of both select forms)
				$selected_values = explode(';',$extra_data['extra_'.$field_details[1]]);
				$extra_data['extra_'.$field_details[1]]  =array();
				
				// looping through the selected values and assigning the selected values to either the first or second select form
				foreach ($selected_values as $key=>$selected_value)
				{
					if (key_exists($selected_value,$values[0]))
					{
						$extra_data['extra_'.$field_details[1]]['extra_'.$field_details[1]] = $selected_value;
					}
					else 
					{
						$extra_data['extra_'.$field_details[1]]['extra_'.$field_details[1].'*'] = $selected_value;
					}
				}
			}
			break;
		case USER_FIELD_TYPE_DIVIDER:
			$form->addElement('static',$field_details[1], '<br /><strong>'.$field_details[3].'</strong>');
			break;
	}
}

//	SUBMIT
if (is_profile_editable())
{
	$form->addElement('submit', 'apply_change', get_lang('Ok'));
}
else
{
	$form->freeze();
}

$user_data = array_merge($user_data,$extra_data);
$form->setDefaults($user_data);

/*
==============================================================================
		FUNCTIONS
==============================================================================
*/

/*
-----------------------------------------------------------
	LOGIC FUNCTIONS
-----------------------------------------------------------
*/

/**
 * Can a user edit his/her profile?
 *
 * @return	boolean	Editability of the profile
 */
function is_profile_editable()
{
	return $GLOBALS['profileIsEditable'];
}

/*
-----------------------------------------------------------
	USER IMAGE FUNCTIONS
-----------------------------------------------------------
*/

/**
 * Upload a submitted user image.
 *
 * @param	$user_id User id
 * @return	The filename of the new picture or FALSE if the upload has failed
 */
function upload_user_image($user_id)
{
	/* Originally added by Miguel (miguel@cesga.es) - 2003-11-04
	 * Code Refactoring by Hugues Peeters (hugues.peeters@claroline.net) - 2003-11-24
	 * Moved inside a function and refactored by Thomas Corthals - 2005-11-04
	 */

	$image_path = UserManager::get_user_picture_path_by_id($user_id,'system',true);
	$image_repository = $image_path['dir'];
	$existing_image = $image_path['file'];
  	$file_extension = explode('.', $_FILES['picture']['name']);
	$file_extension = strtolower($file_extension[count($file_extension) - 1]);

	if (!file_exists($image_repository)) {
		mkpath($image_repository);
	} 
	
	if ($existing_image != '') {
		if (KEEP_THE_NAME_WHEN_CHANGE_IMAGE) {
			$picture_filename = $existing_image;
			$old_picture_filename = 'saved_'.date('Y_m_d_H_i_s').'_'.uniqid('').'_'.$existing_image;
		} else {
			$old_picture_filename = $existing_image;
			$picture_filename = (PREFIX_IMAGE_FILENAME_WITH_UID ? 'u'.$user_id.'_' : '').uniqid('').'.'.$file_extension;
		}

		if (KEEP_THE_OLD_IMAGE_AFTER_CHANGE) {
			@rename($image_repository.$existing_image, $image_repository.$old_picture_filename);
		} else {
			@unlink($image_repository.$existing_image);
		}
	} else {
		$picture_filename = (PREFIX_IMAGE_FILENAME_WITH_UID ? $user_id.'_' : '').uniqid('').'.'.$file_extension;
	}

	// get the picture and resize it 200x200
	$temp = new image($_FILES['picture']['tmp_name']);
	
	$picture_infos=getimagesize($_FILES['picture']['tmp_name']);
	$thumbwidth = 200;
	if (empty($thumbwidth) or $thumbwidth==0) {
		$thumbwidth=200;
	}
	$new_height = round(($thumbwidth/$picture_infos[0])*$picture_infos[1]);

	$temp->resize($thumbwidth,$new_height,0);
	$type=$picture_infos[2];
	
	// original picture
	$big_temp = new image($_FILES['picture']['tmp_name']);
		      
    switch (!empty($type)) {
            case 2 : $temp->send_image('JPG',$image_repository.$picture_filename);
            		 $big_temp->send_image('JPG',$image_repository.'big_'.$picture_filename);
            		 break;
            case 3 : $temp->send_image('PNG',$image_repository.$picture_filename);
            		 $big_temp->send_image('JPG',$image_repository.'big_'.$picture_filename);
            		 break;
            case 1 : $temp->send_image('GIF',$image_repository.$picture_filename);
            		 $big_temp->send_image('JPG',$image_repository.'big_'.$picture_filename);
            		 break;
    }
    return $picture_filename;
}

/**
 * Remove an existing user image.
 *
 * @param	$user_id	User id
 */
function remove_user_image($user_id)
{
	$image_path = UserManager::get_user_picture_path_by_id($user_id,'system');
	$image_repository = $image_path['dir'];
	$image = $image_path['file'];

	if ($image != '')
	{
		if (KEEP_THE_OLD_IMAGE_AFTER_CHANGE) 
		{
			@rename($image_repository.$image, $image_repository.'deleted_'.date('Y_m_d_H_i_s').'_'.$image);
		}
		else
		{
			@unlink($image_repository.$image);
		}
	}
}

/*
-----------------------------------------------------------
	PRODUCTIONS FUNCTIONS
-----------------------------------------------------------
*/

/**
 * Upload a submitted user production.
 *
 * @param	$user_id	User id
 * @return	The filename of the new production or FALSE if the upload has failed
 */
function upload_user_production($user_id)
{
	$image_path = UserManager::get_user_picture_path_by_id($user_id,'system',true);
	$production_repository = $image_path['dir'].$user_id.'/';

	if (!file_exists($production_repository)) 
	{
		mkpath($production_repository);
	}

	$filename = replace_dangerous_char($_FILES['production']['name']);
	$filename = disable_dangerous_file($filename);

	if(filter_extension($filename))
	{
		if (@move_uploaded_file($_FILES['production']['tmp_name'], $production_repository.$filename))
		{
			return $filename;
		}
	}
	return false; // this should be returned if anything went wrong with the upload
}

/*
==============================================================================
		MAIN CODE
==============================================================================
*/
$filtered_extension = false;

if (!empty($_SESSION['profile_update']))
{
	$update_success = ($_SESSION['profile_update'] == 'success');
	unset($_SESSION['profile_update']);
}

if (!empty($_SESSION['image_uploaded']))
{
	$upload_picture_success = ($_SESSION['image_uploaded'] == 'success');
	unset($_SESSION['image_uploaded']);
}

if (!empty($_SESSION['production_uploaded']))
{
	$upload_production_success = ($_SESSION['production_uploaded'] == 'success');
	unset($_SESSION['production_uploaded']);
}


elseif (isset($_POST['remove_production']))
{
	foreach (array_keys($_POST['remove_production']) as $production)
	{
		UserManager::remove_user_production($_user['user_id'], urldecode($production));
	}

	if ($production_list = UserManager::build_production_list($_user['user_id'], true,true))
		$form->insertElementBefore($form->createElement('static', null, null, $production_list), 'productions_list');

	$form->removeElement('productions_list');

	$file_deleted = true;
}
elseif ($form->validate())
{
	$user_data = $form->exportValues();

	// set password if a new one was provided
	if (isset($user_data['password1']))
		$password = $user_data['password1'];

	// upload picture if a new one is provided
	if ($_FILES['picture']['size'])
	{
		if ($new_picture = upload_user_image($_user['user_id'])) 
		{
			$user_data['picture_uri'] = $new_picture;
			$_SESSION['image_uploaded'] = 'success';			
		}
	}
	// remove existing picture if asked
	elseif (isset($user_data['remove_picture']))
	{
		remove_user_image($_user['user_id']);
		$user_data['picture_uri'] = '';
	}

	// upload production if a new one is provided
	if ($_FILES['production']['size'])
	{
		$res = upload_user_production($_user['user_id']);
		if(!$res)
		{
			//it's a bit excessive to assume the extension is the reason why upload_user_production() returned false, but it's true in most cases
			$filtered_extension = true;
		}
		else
		{
			$_SESSION['production_uploaded'] = 'success';	
		}
	}

	
	// remove values that shouldn't go in the database
	unset($user_data['password1'], $user_data['password2'], $user_data['MAX_FILE_SIZE'],
		$user_data['remove_picture'], $user_data['apply_change']);

	// Following RFC2396 (http://www.faqs.org/rfcs/rfc2396.html), a URI uses ':' as a reserved character
	// we can thus ensure the URL doesn't contain any scheme name by searching for ':' in the string
	$my_user_openid=isset($user_data['openid']) ? $user_data['openid'] : '';
	if(!preg_match('/^[^:]*:\/\/.*$/',$my_user_openid))
	{	//ensure there is at least a http:// scheme in the URI provided
		$user_data['openid'] = 'http://'.$my_user_openid;
	}
	$extras = array();
	// build SQL query
	$sql = "UPDATE $table_user SET";

	foreach($user_data as $key => $value)
	{
		if(substr($key,0,6)=='extra_') //an extra field
		{
			$extras[substr($key,6)] = $value;
		}
		else
		{
			$sql .= " $key = '".Database::escape_string($value)."',";
		}
	}

	if (isset($password))
	{
		if ($userPasswordCrypted)
		{
			$sql .= " password = MD5('".Database::escape_string($password)."')";
		}
		else
		{
			$sql .= " password = '".Database::escape_string($password)."'";
		}
	}
	else // remove trailing , from the query we have so far
	{
		$sql = rtrim($sql, ',');
	}

	$sql .= " WHERE user_id  = '".$_user['user_id']."'";
		
	api_sql_query($sql, __FILE__, __LINE__);

	//update the extra fields
	foreach($extras as $key=>$value)
	{
		$myres = UserManager::update_extra_field_value($_user['user_id'],$key,$value);
	}
	
	// re-init the system to take new settings into account
	$uidReset = true;
	include (api_get_path(INCLUDE_PATH).'local.inc.php');
	$_SESSION['profile_update'] = 'success';
	header("Location: ".api_get_self()."?{$_SERVER['QUERY_STRING']}".($filtered_extension && strstr($_SERVER['QUERY_STRING'],'&fe=1')===false?'&fe=1':''));
	exit;
}

/*
==============================================================================
		MAIN DISPLAY SECTION
==============================================================================
*/
Display :: display_header(get_lang('ModifyProfile'));

if (!empty($file_deleted))
{
	Display :: display_confirmation_message(get_lang('FileDeleted'),false);
}
elseif (!empty($update_success))
{
	$message=get_lang('ProfileReg');
	
	if (isset($upload_picture_success)) 
	{
		$message.='<br /> '.get_lang('PictureUploaded');
	}
	
	if (isset($upload_production_success)) 
	{
		$message.='<br />'.get_lang('ProductionUploaded');
	}
		
	Display :: display_confirmation_message($message,false);	
}

if(!empty($warning_msg))
{
	Display :: display_warning_message($warning_msg,false);
}
//User picture size is calculated from SYSTEM path
$image_syspath = UserManager::get_user_picture_path_by_id($userid,'system',false,true);
$image_size = getimagesize($image_syspath['dir'].$image_syspath['file']);
//Web path
$image_path = UserManager::get_user_picture_path_by_id($_user['user_id'],'web',false,true);
$image_dir = $image_path['dir'];
$image = $image_path['file'];
$image_file = $image_dir.$image;
$img_attributes = 'src="'.$image_file.'?rand='.time().'" '
	.'alt="'.$user_data['lastname'].' '.$user_data['firstname'].'" '
	.'style="float:'.($text_dir == 'rtl' ? 'left' : 'right').'; padding:5px;" ';

if ($image_size[0] > 300) {
	//limit display width to 300px
	$img_attributes .= 'width="300" ';	
}

// get the path,width and height from original picture
$big_image = $image_dir.'big_'.$image;
$big_image_size = @getimagesize($big_image);
$big_image_width= $big_image_size[0];
$big_image_height= $big_image_size[1];

echo '<input type="image" '.$img_attributes.' onclick="return show_image(\''.$big_image.'\',\''.$big_image_width.'\',\''.$big_image_height.'\');"/>';

$form->display();

Display :: display_footer();
?>
