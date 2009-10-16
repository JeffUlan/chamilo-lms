<?php // $Id: user_import.php 21895 2009-07-08 15:51:31Z juliomontoya $
/* For licensing terms, see /dokeos_license.txt */
/**
==============================================================================
* This tool allows platform admins to add users by uploading a CSV or XML file
* @todo Add some langvars to DLTT
* @package dokeos.admin
==============================================================================
*/

/**
 * Validate the imported data.
 */

$language_file = array ('admin', 'registration');

require '../inc/global.inc.php';
require_once api_get_path(LIBRARY_PATH).'mail.lib.inc.php';

// Set this option to true to enforce strict purification for usenames.
$purification_option_for_usernames = false;

function validate_data($users) {
	global $defined_auth_sources;
	$errors = array();
	$usernames = array();
	foreach ($users as $index => $user) {
		// 1. Check if mandatory fields are set.
		$mandatory_fields = array('LastName', 'FirstName');
		if (api_get_setting('registration', 'email') == 'true') {
			$mandatory_fields[] = 'Email';
		}
		foreach ($mandatory_fields as $key => $field) {
			if (empty($user[$field])) {
				$user['error'] = get_lang($field.'Mandatory');
				$errors[] = $user;
			}
		}
		// 2. Check username, first, check whether it is empty.
		if (!UserManager::is_username_empty($user['UserName'])) {
			// 2.1. Check whether username is too long.
			if (UserManager::is_username_too_long($user['UserName'])) {
				$user['error'] = get_lang('UserNameTooLong');
				$errors[] = $user;
			}
			// 2.2. Check whether the username was used twice in import file.
			if (isset($usernames[$user['UserName']])) {
				$user['error'] = get_lang('UserNameUsedTwice');
				$errors[] = $user;
			}
			$usernames[$user['UserName']] = 1;
			// 2.3. Check whether username is allready occupied.
			if (!UserManager::is_username_available($user['UserName'])) {
				$user['error'] = get_lang('UserNameNotAvailable');
				$errors[] = $user;
			}
		}
		// 3. Check status.
		if (isset($user['Status']) && !api_status_exists($user['Status'])) {
			$user['error'] = get_lang('WrongStatus');
			$errors[] = $user;
		}
		// 4. Check classname
		if (!empty($user['ClassName'])) {
			if (!ClassManager :: class_name_exists($user['ClassName'])) {
				$user['error'] = get_lang('ClassNameNotAvailable');
				$errors[] = $user;
			}
		}
		// 5. Check authentication source
		if (!empty($user['AuthSource'])) {
			if (!in_array($user['AuthSource'], $defined_auth_sources)) {
				$user['error'] = get_lang('AuthSourceNotAvailable');
				$errors[] = $user;
			}
		}
	}
	return $errors;
}

/**
 * Add missing user-information (which isn't required, like password, username etc).
 */
function complete_missing_data($user) {
	global $purification_option_for_usernames;
	// 1. Create a username if necessary.
	if (UserManager::is_username_empty($user['UserName'])) {
		$user['UserName'] = UserManager::create_unique_username($user['FirstName'], $user['LastName']);
	} else {
		$user['UserName'] = UserManager::purify_username($user['UserName'], $purification_option_for_usernames);
	}
	// 2. Generate a password if necessary.
	if (empty($user['Password'])) {
		$user['Password'] = api_generate_password();
	}
	// 3. Set status if not allready set.
	if (empty($user['Status'])) {
		$user['Status'] = 'user';
	}
	// 4. Set authsource if not allready set.
	if (empty($user['AuthSource'])) {
		$user['AuthSource'] = PLATFORM_AUTH_SOURCE;
	}
	return $user;
}

/**
 * Save the imported data
 * @param   array   List of users
 * @return  void
 * @uses global variable $inserted_in_course, which returns the list of courses the user was inserted in
 */
function save_data($users) {
	global $inserted_in_course;
	// Not all scripts declare the $inserted_in_course array (although they should).
	if (!isset($inserted_in_course)) {
		$inserted_in_course = array();
	}
	require_once api_get_path(LIBRARY_PATH).'mail.lib.inc.php';
	$user_table = Database :: get_main_table(TABLE_MAIN_USER);
	$send_mail = $_POST['sendMail'] ? 1 : 0;
	if (is_array($users)) {
		foreach ($users as $index => $user)	{
			$user = complete_missing_data($user);

			$user['Status'] = api_status_key($user['Status']);

			$user_id = UserManager :: create_user($user['FirstName'], $user['LastName'], $user['Status'], $user['Email'], $user['UserName'], $user['Password'], $user['OfficialCode'], api_get_setting('PlatformLanguage'), $user['PhoneNumber'], '', $user['AuthSource']);
			if (!is_array($user['Courses']) && !empty($user['Courses'])) {
				$user['Courses'] = array($user['Courses']);
			}
			if (is_array($user['Courses'])) {
				foreach ($user['Courses'] as $index => $course) {
					if (CourseManager :: course_exists($course)) {
						CourseManager :: subscribe_user($user_id, $course,$user['Status']);
						$course_info = CourseManager::get_course_information($course);
						$inserted_in_course[$course] = $course_info['title'];
					}
					if (CourseManager :: course_exists($course, true)) {
						// Also subscribe to virtual courses through check on visual code.
						$list = CourseManager :: get_courses_info_from_visual_code($course);
						foreach ($list as $vcourse) {
							if ($vcourse['code'] == $course) {
								// Ignore, this has already been inserted.
							} else {
								CourseManager :: subscribe_user($user_id, $vcourse['code'],$user['Status']);
								$inserted_in_course[$vcourse['code']] = $vcourse['title'];
							}
						}
					}
				}
			}
			if (!empty($user['ClassName'])) {
				$class_id = ClassManager :: get_class_id($user['ClassName']);
				ClassManager :: add_user($user_id, $class_id);
			}

			// Saving extra fields.
			global $extra_fields;

			// We are sure that the extra field exists.
			foreach($extra_fields as $extras) {
				if (isset($user[$extras[1]])) {
					$key 	= $extras[1];
					$value 	= $user[$extras[1]];
					UserManager::update_extra_field_value($user_id, $key,$value);
				}
			}

			if ($send_mail) {
				$recipient_name = api_get_person_name($user['FirstName'], $user['LastName'], null, PERSON_NAME_EMAIL_ADDRESS);
				$emailsubject = '['.api_get_setting('siteName').'] '.get_lang('YourReg').' '.api_get_setting('siteName');
				$emailbody = get_lang('Dear').api_get_person_name($user['FirstName'], $user['LastName']).",\n\n".get_lang('YouAreReg')." ".api_get_setting('siteName')." ".get_lang('Settings')." $user[UserName]\n".get_lang('Pass')." : $user[Password]\n\n".get_lang('Address')." ".api_get_setting('siteName')." ".get_lang('Is')." : ".api_get_path('WEB_PATH')." \n\n".get_lang('Problem')."\n\n".get_lang('Formula').",\n\n".api_get_person_name(api_get_setting('administratorName'), api_get_setting('administratorSurname'))."\n".get_lang('Manager')." ".api_get_setting('siteName')."\nT. ".api_get_setting('administratorTelephone')."\n".get_lang('Email')." : ".api_get_setting('emailAdministrator')."";
				$sender_name = api_get_person_name(api_get_setting('administratorName'), api_get_setting('administratorSurname'), null, PERSON_NAME_EMAIL_ADDRESS);
				$email_admin = api_get_setting('emailAdministrator');
				@api_mail($recipient_name, $user['Email'], $emailsubject, $emailbody, $sender_name, $email_admin);
			}
		}
	}
}

/**
 * Read the CSV-file
 * @param string $file Path to the CSV-file
 * @return array All userinformation read from the file
 */
function parse_csv_data($file) {
	$users = Import :: csv_to_array($file);
	foreach ($users as $index => $user) {
		if (isset ($user['Courses'])) {
			$user['Courses'] = explode('|', trim($user['Courses']));
		}
		$users[$index] = $user;
	}
	return $users;
}
/**
 * XML-parser: handle start of element
 */
function element_start($parser, $data) {
	$data = api_utf8_decode($data);
	global $user;
	global $current_tag;
	switch ($data) {
		case 'Contact' :
			$user = array ();
			break;
		default :
			$current_tag = $data;
	}
}

/**
 * XML-parser: handle end of element
 */
function element_end($parser, $data) {
	$data = api_utf8_decode($data);
	global $user;
	global $users;
	global $current_value;
	switch ($data) {
		case 'Contact' :
			if ($user['Status'] == '5') {
				$user['Status'] = STUDENT;
			}
			if ($user['Status'] == '1') {
				$user['Status'] = COURSEMANAGER;
			}
			$users[] = $user;
			break;
		default :
			$user[$data] = $current_value;
			break;
	}
}

/**
 * XML-parser: handle character data
 */
function character_data($parser, $data) {
	$data = trim(api_utf8_decode($data));
	global $current_value;
	$current_value = $data;
}

/**
 * Read the XML-file
 * @param string $file Path to the XML-file
 * @return array All userinformation read from the file
 */
function parse_xml_data($file) {
	global $current_tag;
	global $current_value;
	global $user;
	global $users;
	$users = array();
	$parser = xml_parser_create('UTF-8');
	xml_set_element_handler($parser, 'element_start', 'element_end');
	xml_set_character_data_handler($parser, 'character_data');
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
	xml_parse($parser, api_utf8_encode_xml(file_get_contents($file)));
	xml_parser_free($parser);
	return $users;
}

$cidReset = true;
include ('../inc/global.inc.php');
$this_section = SECTION_PLATFORM_ADMIN;
api_protect_admin_script();
require_once api_get_path(LIBRARY_PATH).'fileManage.lib.php';
require_once api_get_path(LIBRARY_PATH).'usermanager.lib.php';
require_once api_get_path(LIBRARY_PATH).'classmanager.lib.php';
require_once api_get_path(LIBRARY_PATH).'import.lib.php';
require_once api_get_path(LIBRARY_PATH).'formvalidator/FormValidator.class.php';

$defined_auth_sources[] = PLATFORM_AUTH_SOURCE;
if (is_array($extAuthSource)) {
	$defined_auth_sources = array_merge($defined_auth_sources, array_keys($extAuthSource));
}

$tool_name = get_lang('ImportUserListXMLCSV');

$interbreadcrumb[] = array ("url" => 'index.php', "name" => get_lang('PlatformAdmin'));

set_time_limit(0);
$extra_fields = Usermanager::get_extra_fields(0, 0, 5, 'ASC', false);
$user_id_error = array();
if ($_POST['formSent'] AND $_FILES['import_file']['size'] !== 0) {
	$file_type = $_POST['file_type'];
	Security::clear_token();
	$tok = Security::get_token();

	if (strcmp($file_type, 'csv') === 0) { //&& strcmp($_FILES['import_file']['type'],'text/'.$file_type.'')===0) {
		$users = parse_csv_data($_FILES['import_file']['tmp_name']);
		$errors = validate_data($users);
		$error_kind_file = false;
	} elseif (strcmp($file_type, 'xml') === 0) { // && strcmp($_FILES['import_file']['type'],'text/'.$file_type.'')===0) {
		$users = parse_xml_data($_FILES['import_file']['tmp_name']);
		$errors = validate_data($users);
		$error_kind_file = false;
	} else {
		$error_kind_file = true;
	}

	// List user id whith error.
	$user_id_error = array();
	if (is_array($errors)) {
		foreach ($errors as $my_errors) {
			$user_id_error[] = $my_errors['UserName'];
		}
	}
	if (is_array($users)) {
		foreach ($users as $my_user) {
			if (!in_array($my_user['UserName'], $user_id_error)) {
				$users_to_insert[] = $my_user;
			}
		}
	}

	$inserted_in_course = array();

	if (strcmp($file_type, 'csv') === 0) // this replace if (strcmp($_FILES['import_file']['type'], 'text/'.$file_type.'') === 0)
	{
		save_data($users_to_insert);
		
	} elseif (strcmp($file_type, 'xml') === 0) {
		
		save_data($users_to_insert);
		
	} else {
		
		$error_message = get_lang('YouMustImportAFileAccordingToSelectedOption');
		header('Location: '.api_get_self().'?warn='.urlencode($error_message).'&amp;file_type='.$file_type.'&amp;sec_token='.$tok);
		exit ();
	}
	

	if (count($errors) > 0) {
		$see_message_import = get_lang('FileImportedJustUsersThatAreNotRegistered');
	} else {
		$see_message_import = get_lang('FileImported');
	}
	$msg2 = '';
	if (count($inserted_in_course) > 1) {
		$msg2 .= '<br>'.get_lang('UsersSubscribedToSeveralCoursesBecauseOfVirtualCourses').':';
		foreach ($inserted_in_course as $course) {
			$msg2 .= ' '.$course.',';
		}
		$msg2 = substr($msg2, 0, -1);
		$msg2 .= '</br>';
	}

	if (count($errors) != 0) {
		$error_message = '<ul>';
		foreach ($errors as $index => $error_user) {
			$error_message .= '<li><b>'.$error_user['error'].'</b>: ';
			$error_message .= '<strong>'.$error_user['UserName'].'</strong>&nbsp;('.api_get_person_name($error_user['FirstName'], $error_user['LastName']).')';
			$error_message .= '</li>';
		}
		$error_message .= '</ul>';
	}

	header('Location: user_list.php?action=show_message&message='.urlencode($see_message_import).'&warn='.urlencode($error_message).'&sec_token='.$tok);
	exit ();
}

Display :: display_header($tool_name);
//api_display_tool_title($tool_name);

if ($_FILES['import_file']['size'] == 0 AND $_POST) {
	Display::display_error_message(get_lang('ThisFieldIsRequired'));
}

if ($error_kind_file === true) {
	Display :: display_error_message(get_lang('YouMustImportAFileAccordingToSelectedOption'));
} else if (isset($_GET['warn'])) {
	$error_message = Security::remove_XSS($_GET['warn']);
	Display :: display_error_message($error_message);
}

$form = new FormValidator('user_import');
$form->addElement('header', '', $tool_name);
$form->addElement('hidden', 'formSent');
$form->addElement('file', 'import_file', get_lang('ImportFileLocation'));
//$form->addRule('import_file', get_lang('ThisFieldIsRequired'), 'required'); // This rule does not work, probably due to the security mechanism here.
$allowed_file_types = array ('xml', 'csv');
//$form->addRule('import_file', get_lang('InvalidExtension').' ('.implode(',', $allowed_file_types).')', 'filetype', $allowed_file_types); // This rule does not work, probably due to the security mechanism here.
$form->addElement('radio', 'file_type', get_lang('FileType'), 'XML (<a href="exemple.xml" target="_blank">'.get_lang('ExampleXMLFile').'</a>)', 'xml');
$form->addElement('radio', 'file_type', null, 'CSV (<a href="exemple.csv" target="_blank">'.get_lang('ExampleCSVFile').'</a>)', 'csv');
$form->addElement('radio', 'sendMail', get_lang('SendMailToUsers'), get_lang('Yes'), 1);
$form->addElement('radio', 'sendMail', null, get_lang('No'), 0);
$form->addElement('style_submit_button', 'submit', get_lang('Import'), 'class="save"');
$defaults['formSent'] = 1;
$defaults['sendMail'] = 0;
$defaults['file_type'] = 'xml';
$form->setDefaults($defaults);
$form->display();

$list = array();
$list_reponse = array();
$result_xml = '';
$i = 0;
$count_fields = count($extra_fields);
if ($count_fields > 0) {
	foreach ($extra_fields as $extra) {
		$list[] = $extra[1];
		$list_reponse[] = 'xxx';
		$spaces = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$result_xml .= $spaces.'&lt;'.$extra[1].'&gt;xxx&lt;/'.$extra[1].'&gt;';
		if ($i != $count_fields - 1) {
			$result_xml .= '<br/>';
		}
		$i++;
	}
}

?>
<p><?php echo get_lang('CSVMustLookLike').' ('.get_lang('MandatoryFields').')'; ?> :</p>

<blockquote>
<pre>
<b>LastName</b>;<b>FirstName</b>;<b>Email</b>;UserName;Password;AuthSource;OfficialCode;PhoneNumber;Status;<font style="color:red;"><?php if (count($list) > 0) echo implode(';', $list).';'; ?></font>Courses;ClassName;
<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;xxx;xxx;<?php echo implode('/', $defined_auth_sources); ?>;xxx;xxx;user/teacher/drh;<font style="color:red;"><?php if (count($list_reponse) > 0) echo implode(';', $list_reponse).';'; ?></font>xxx1|xxx2|xxx3;xxx;<br />
</pre>
</blockquote>

<p><?php echo get_lang('XMLMustLookLike').' ('.get_lang('MandatoryFields').')'; ?> :</p>

<blockquote>
<pre>
&lt;?xml version=&quot;1.0&quot; encoding=&quot;<?php echo api_refine_encoding_id(api_get_system_encoding()); ?>&quot;?&gt;
&lt;Contacts&gt;
    &lt;Contact&gt;
        <b>&lt;LastName&gt;xxx&lt;/LastName&gt;</b>
        <b>&lt;FirstName&gt;xxx&lt;/FirstName&gt;</b>
        &lt;UserName&gt;xxx&lt;/UserName&gt;
        &lt;Password&gt;xxx&lt;/Password&gt;
        &lt;AuthSource&gt;<?php echo implode('/', $defined_auth_sources); ?>&lt;/AuthSource&gt;
        <b>&lt;Email&gt;xxx&lt;/Email&gt;</b>
        &lt;OfficialCode&gt;xxx&lt;/OfficialCode&gt;
        &lt;PhoneNumber&gt;xxx&lt;/PhoneNumber&gt;
        &lt;Status&gt;user/teacher/drh&lt;/Status&gt;         <?php if ($result_xml != '') { echo '<br /><font style="color:red;">', $result_xml; echo '</font>'; } ?>
        &lt;Courses&gt;xxx1|xxx2|xxx3&lt;/Courses&gt;
        &lt;ClassName&gt;class 1&lt;/ClassName&gt;
    &lt;/Contact&gt;
&lt;/Contacts&gt;
</pre>
</blockquote>

<?php
/*
==============================================================================
		FOOTER
==============================================================================
*/
Display :: display_footer();
