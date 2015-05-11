<?php
/* For licensing terms, see /license.txt */

/**
* This file displays the user's profile,
* optionally it allows users to modify their profile as well.
*
* See inc/conf/profile.conf.php to modify settings
*
* @package chamilo.auth
*/

$cidReset = true;
require_once '../inc/global.inc.php';

if (api_get_setting('allow_social_tool') == 'true') {
    $this_section = SECTION_SOCIAL;
} else {
    $this_section = SECTION_MYPROFILE;
}

$htmlHeadXtra[] = api_get_password_checker_js('#username', '#password1');

$_SESSION['this_section'] = $this_section;

if (!(isset($_user['user_id']) && $_user['user_id']) || api_is_anonymous($_user['user_id'], true)) {
    api_not_allowed(true);
}

$htmlHeadXtra[] = '<script>
function confirmation(name) {
    if (confirm("'.get_lang('AreYouSureToDelete', '').' " + name + " ?")) {
            document.forms["profile"].submit();
    } else {
        return false;
    }
}
function show_image(image,width,height) {
    width = parseInt(width) + 20;
    height = parseInt(height) + 20;
    window_x = window.open(image,\'windowX\',\'width=\'+ width + \', height=\'+ height + \'\');

}
function generate_open_id_form() {
    $.ajax({
        contentType: "application/x-www-form-urlencoded",
        beforeSend: function(objeto) {
        /*$("#div_api_key").html("Loading...");*/ },
        type: "POST",
        url: "'.api_get_path(WEB_AJAX_PATH).'user_manager.ajax.php?a=generate_api_key",
        data: "num_key_id="+"",
        success: function(datos) {
         $("#div_api_key").html(datos);
        }
    });
}

function hide_icon_edit(element_html)  {
    ident="#edit_image";
    $(ident).hide();
}
function show_icon_edit(element_html) {
    ident="#edit_image";
    $(ident).show();
}
</script>';

$warning_msg = '';
if (!empty($_GET['fe'])) {
    $warning_msg .= get_lang('UplUnableToSaveFileFilteredExtension');
    $_GET['fe'] = null;
}

$jquery_ready_content = '';
if (api_get_setting('allow_message_tool') == 'true') {
    $jquery_ready_content = <<<EOF
    $(".message-content .message-delete").click(function(){
        $(this).parents(".message-content").animate({ opacity: "hide" }, "slow");
        $(".message-view").animate({ opacity: "show" }, "slow");
    });
EOF;
}

$tool_name = is_profile_editable() ? get_lang('ModifProfile') : get_lang('ViewProfile');
$table_user = Database :: get_main_table(TABLE_MAIN_USER);

/*
 * Get initial values for all fields.
 */
$user_data = api_get_user_info(api_get_user_id());
$array_list_key = UserManager::get_api_keys(api_get_user_id());
$id_temp_key = UserManager::get_api_key_id(api_get_user_id(), 'dokeos');
$value_array = $array_list_key[$id_temp_key];
$user_data['api_key_generate'] = $value_array;

if ($user_data !== false) {
    if (api_get_setting('login_is_email') == 'true') {
        $user_data['username'] = $user_data['email'];
    }
    if (is_null($user_data['language'])) {
        $user_data['language'] = api_get_setting('platformLanguage');
    }
}

/*
 * Initialize the form.
 */
$form = new FormValidator(
    'profile',
    'post',
    api_get_self()."?".str_replace('&fe=1', '', Security::remove_XSS($_SERVER['QUERY_STRING'])),
    null,
    array('style' => 'width: 70%; float: '.($text_dir == 'rtl' ? 'right;' : 'left;'))
);

if (api_is_western_name_order()) {
    //    FIRST NAME and LAST NAME
    $form->addElement('text', 'firstname', get_lang('FirstName'), array('size' => 40));
    $form->addElement('text', 'lastname',  get_lang('LastName'),  array('size' => 40));
} else {
    //    LAST NAME and FIRST NAME
    $form->addElement('text', 'lastname',  get_lang('LastName'),  array('size' => 40));
    $form->addElement('text', 'firstname', get_lang('FirstName'), array('size' => 40));
}
if (api_get_setting('profile', 'name') !== 'true') {
    $form->freeze(array('lastname', 'firstname'));
}
$form->applyFilter(array('lastname', 'firstname'), 'stripslashes');
$form->applyFilter(array('lastname', 'firstname'), 'trim');
$form->applyFilter(array('lastname', 'firstname'), 'html_filter');
$form->addRule('lastname' , get_lang('ThisFieldIsRequired'), 'required');
$form->addRule('firstname', get_lang('ThisFieldIsRequired'), 'required');

//    USERNAME
$form->addElement(
    'text',
    'username',
    get_lang('UserName'),
    array(
        'id' => 'username',
        'maxlength' => USERNAME_MAX_LENGTH,
        'size' => USERNAME_MAX_LENGTH,
    )
);
if (api_get_setting('profile', 'login') !== 'true' || api_get_setting('login_is_email') == 'true') {
    $form->freeze('username');
}
$form->applyFilter('username', 'stripslashes');
$form->applyFilter('username', 'trim');
$form->addRule('username', get_lang('ThisFieldIsRequired'), 'required');
$form->addRule('username', get_lang('UsernameWrong'), 'username');
$form->addRule('username', get_lang('UserTaken'), 'username_available', $user_data['username']);

//    OFFICIAL CODE
if (CONFVAL_ASK_FOR_OFFICIAL_CODE) {
    $form->addElement('text', 'official_code', get_lang('OfficialCode'), array('size' => 40));
    if (api_get_setting('profile', 'officialcode') !== 'true') {
        $form->freeze('official_code');
    }
    $form->applyFilter('official_code', 'stripslashes');
    $form->applyFilter('official_code', 'trim');
    $form->applyFilter('official_code', 'html_filter');
    if (api_get_setting('registration', 'officialcode') == 'true' && api_get_setting('profile', 'officialcode') == 'true') {
        $form->addRule('official_code', get_lang('ThisFieldIsRequired'), 'required');
    }
}

//    EMAIL
$form->addElement('email', 'email', get_lang('Email'), array('size' => 40));
if (api_get_setting('profile', 'email') !== 'true') {
    $form->freeze('email');
}

if (api_get_setting('registration', 'email') == 'true' &&  api_get_setting('profile', 'email') == 'true') {
    $form->applyFilter('email', 'stripslashes');
    $form->applyFilter('email', 'trim');
    $form->addRule('email', get_lang('ThisFieldIsRequired'), 'required');
    $form->addRule('email', get_lang('EmailWrong'), 'email');
}

// OPENID URL
if (is_profile_editable() && api_get_setting('openid_authentication') == 'true') {
    $form->addElement('text', 'openid', get_lang('OpenIDURL'), array('size' => 40));
    if (api_get_setting('profile', 'openid') !== 'true') {
        $form->freeze('openid');
    }
    $form->applyFilter('openid', 'trim');
    //if (api_get_setting('registration', 'openid') == 'true') {
    //    $form->addRule('openid', get_lang('ThisFieldIsRequired'), 'required');
    //}
}

//    PHONE
$form->addElement('text', 'phone', get_lang('Phone'), array('size' => 20));
if (api_get_setting('profile', 'phone') !== 'true') {
    $form->freeze('phone');
}
$form->applyFilter('phone', 'stripslashes');
$form->applyFilter('phone', 'trim');
$form->applyFilter('phone', 'html_filter');

//  PICTURE
if (is_profile_editable() && api_get_setting('profile', 'picture') == 'true') {
    $form->addElement(
        'file',
        'picture',
        ($user_data['picture_uri'] != '' ? get_lang('UpdateImage') : get_lang(
            'AddImage'
        )),
        array('class' => 'picture-form')
    );
    $form->add_progress_bar();
    if (!empty($user_data['picture_uri'])) {
        $form->addElement('checkbox', 'remove_picture', null, get_lang('DelImage'));
    }
    $allowed_picture_types = array ('jpg', 'jpeg', 'png', 'gif');
    $form->addRule(
        'picture',
        get_lang('OnlyImagesAllowed').' ('.implode(',', $allowed_picture_types).')',
        'filetype',
        $allowed_picture_types
    );
}

//    LANGUAGE
$form->addElement('select_language', 'language', get_lang('Language'));
if (api_get_setting('profile', 'language') !== 'true') {
    $form->freeze('language');
}

//THEME
if (is_profile_editable() && api_get_setting('user_selected_theme') == 'true') {
    $form->addElement('SelectTheme', 'theme', get_lang('Theme'));
    if (api_get_setting('profile', 'theme') !== 'true') {
        $form->freeze('theme');
    }
    $form->applyFilter('theme', 'trim');
}

//    EXTENDED PROFILE  this make the page very slow!
if (api_get_setting('extended_profile') == 'true') {
    $width_extended_profile = 500;
    //    MY COMPETENCES
    $form->addHtmlEditor(
        'competences',
        get_lang('MyCompetences'),
        false,
        false,
        array(
            'ToolbarSet' => 'Profile',
            'Width' => $width_extended_profile,
            'Height' => '130',
        )
    );
    //    MY DIPLOMAS
    $form->addHtmlEditor(
        'diplomas',
        get_lang('MyDiplomas'),
        false,
        false,
        array(
            'ToolbarSet' => 'Profile',
            'Width' => $width_extended_profile,
            'Height' => '130',
        )
    );
    // WHAT I AM ABLE TO TEACH
    $form->addHtmlEditor(
        'teach',
        get_lang('MyTeach'),
        false,
        false,
        array(
            'ToolbarSet' => 'Profile',
            'Width' => $width_extended_profile,
            'Height' => '130',
        )
    );

    //    MY PRODUCTIONS
    $form->addElement('file', 'production', get_lang('MyProductions'));
    if ($production_list = UserManager::build_production_list(api_get_user_id(), '', true)) {
        $form->addElement('static', 'productions_list', null, $production_list);
    }
    //    MY PERSONAL OPEN AREA
    $form->addHtmlEditor(
        'openarea',
        get_lang('MyPersonalOpenArea'),
        false,
        false,
        array(
            'ToolbarSet' => 'Profile',
            'Width' => $width_extended_profile,
            'Height' => '350',
        )
    );
    // openarea is untrimmed for maximum openness
    $form->applyFilter(array('competences', 'diplomas', 'teach', 'openarea'), 'stripslashes');
    $form->applyFilter(array('competences', 'diplomas', 'teach'), 'trim');
}

//    PASSWORD, if auth_source is platform
if (is_platform_authentication() &&
    is_profile_editable() &&
    api_get_setting('profile', 'password') == 'true'
) {
    $form->addElement('password', 'password0', array(get_lang('Pass'), get_lang('Enter2passToChange')), array('size' => 40));
    $form->addElement('password', 'password1', get_lang('NewPass'), array('id'=> 'password1', 'size' => 40));

    if (isset($_configuration['allow_strength_pass_checker']) && $_configuration['allow_strength_pass_checker']) {
        $form->addElement('label', null, '<div id="password_progress"></div>');
    }
    $form->addElement('password', 'password2', get_lang('Confirmation'), array('size' => 40));
    //    user must enter identical password twice so we can prevent some user errors
    $form->addRule(array('password1', 'password2'), get_lang('PassTwo'), 'compare');
    if (CHECK_PASS_EASY_TO_FIND) {
        $form->addRule('password1', get_lang('CurrentPasswordEmptyOrIncorrect'), 'callback', 'api_check_password');
    }
}

// EXTRA FIELDS
//$extra_data = UserManager::get_extra_user_data(api_get_user_id(), true);
/*$return_params = UserManager::set_extra_fields_in_form(
    $form,
    $extra_data,
    false,
    api_get_user_id()
);*/

$extraField = new ExtraField('user');
$return = $extraField->addElements($form, api_get_user_id());

$jquery_ready_content = $return['jquery_ready_content'];

// the $jquery_ready_content variable collects all functions that
// will be load in the $(document).ready javascript function
$htmlHeadXtra[] ='<script>
$(document).ready(function(){
    '.$jquery_ready_content.'
});
</script>';

if (api_get_setting('profile', 'apikeys') == 'true') {
    $form->addElement('html', '<div id="div_api_key">');
    $form->addElement(
        'text',
        'api_key_generate',
        get_lang('MyApiKey'),
        array('size' => 40, 'id' => 'id_api_key_generate')
    );
    $form->addElement('html', '</div>');
    $form->addElement(
        'button',
        'generate_api_key',
        get_lang('GenerateApiKey'),
        array(
            'id' => 'id_generate_api_key',
            'onclick' => 'generate_open_id_form(); return false;',
        )
    ); //generate_open_id_form()
}
//    SUBMIT
if (is_profile_editable()) {
    $form->addButtonUpdate(get_lang('SaveSettings'), 'apply_change');
} else {
    $form->freeze();
}
$form->setDefaults($user_data);

/**
 * Is user auth_source is platform ?
 *
 * @return  boolean if auth_source is platform
 */
function is_platform_authentication() {
    $tab_user_info = api_get_user_info();
    return $tab_user_info['auth_source'] == PLATFORM_AUTH_SOURCE;
}

/**
 * Can a user edit his/her profile?
 *
 * @return    boolean    Editability of the profile
 */
function is_profile_editable() {
    return $GLOBALS['profileIsEditable'];
}

/*
    PRODUCTIONS FUNCTIONS
*/

/**
 * Upload a submitted user production.
 *
 * @param    $user_id    User id
 * @return    The filename of the new production or FALSE if the upload has failed
 */
function upload_user_production($user_id)
{
    $production_repository = UserManager::getUserPathById($user_id, 'system');

    if (!file_exists($production_repository)) {
        @mkdir($production_repository, api_get_permissions_for_new_directories(), true);
    }
    $filename = api_replace_dangerous_char($_FILES['production']['name']);
    $filename = disable_dangerous_file($filename);

    if (filter_extension($filename)) {
        if (@move_uploaded_file($_FILES['production']['tmp_name'], $production_repository.$filename)) {
            return $filename;
        }
    }
    return false; // this should be returned if anything went wrong with the upload
}

/**
 * Check current user's current password
 * @param    char    password
 * @return    bool true o false
 * @uses Gets user ID from global variable
 */
function check_user_password($password) {
    $user_id = api_get_user_id();
    if ($user_id != strval(intval($user_id)) || empty($password)) { return false; }
    $table_user = Database :: get_main_table(TABLE_MAIN_USER);
    $password = api_get_encrypted_password($password);
    $password = Database::escape_string($password);
    $sql_password = "SELECT * FROM $table_user WHERE user_id='".$user_id."' AND password='".$password."'";
    $result = Database::query($sql_password);
    return Database::num_rows($result) != 0;
}
/**
 * Check current user's current password
 * @param    char    email
 * @return    bool true o false
 * @uses Gets user ID from global variable
 */
function check_user_email($email) {
    $user_id = api_get_user_id();
    if ($user_id != strval(intval($user_id)) || empty($email)) {
        return false;
    }
    $table_user = Database :: get_main_table(TABLE_MAIN_USER);
    $email = Database::escape_string($email);
    $sql = "SELECT * FROM $table_user
            WHERE user_id='".$user_id."' AND email='".$email."'";
    $result = Database::query($sql);
    return Database::num_rows($result) != 0;
}

/*        MAIN CODE */
$filtered_extension = false;
$update_success = false;
$upload_picture_success = false;
$upload_production_success = false;
$msg_fail_changue_email = false;
$msg_is_not_password = false;

if (is_platform_authentication()) {
    if (!empty($_SESSION['change_email'])) {
        $msg_fail_changue_email= ($_SESSION['change_email'] == 'success');
        unset($_SESSION['change_email']);
    } elseif (!empty($_SESSION['is_not_password'])) {
        $msg_is_not_password = ($_SESSION['is_not_password'] == 'success');
        unset($_SESSION['is_not_password']);
    } elseif (!empty($_SESSION['profile_update'])) {
        $update_success = ($_SESSION['profile_update'] == 'success');
        unset($_SESSION['profile_update']);
    } elseif (!empty($_SESSION['image_uploaded'])) {
        $upload_picture_success = ($_SESSION['image_uploaded'] == 'success');
        unset($_SESSION['image_uploaded']);
    } elseif (!empty($_SESSION['production_uploaded'])) {
        $upload_production_success = ($_SESSION['production_uploaded'] == 'success');
        unset($_SESSION['production_uploaded']);
    }
}

if ($form->validate()) {
    $wrong_current_password = false;
    $user_data = $form->getSubmitValues(1);

    // set password if a new one was provided
    if (!empty($user_data['password0'])) {
        if (check_user_password($user_data['password0'])) {
            if (!empty($user_data['password1'])) {
                $password = $user_data['password1'];
            }
        } else {
            $wrong_current_password = true;
            $_SESSION['is_not_password'] = 'success';
        }
    }
    if (empty($user_data['password0']) && !empty($user_data['password1'])) {
        $wrong_current_password = true;
        $_SESSION['is_not_password'] = 'success';
    }

    $allow_users_to_change_email_with_no_password = true;
    if (is_platform_authentication() && api_get_setting('allow_users_to_change_email_with_no_password') == 'false') {
        $allow_users_to_change_email_with_no_password = false;
    }

    // If user sending the email to be changed (input available and not frozen )
    if (api_get_setting('profile', 'email') == 'true') {
        if ($allow_users_to_change_email_with_no_password) {
            if (!check_user_email($user_data['email'])) {
                $changeemail = $user_data['email'];
                //$_SESSION['change_email'] = 'success';
            }
        } else {
            //Normal behaviour
            if (!check_user_email($user_data['email']) && !empty($user_data['password0']) && !$wrong_current_password) {
                $changeemail = $user_data['email'];
            }

            if (!check_user_email($user_data['email']) && empty($user_data['password0'])){
                $_SESSION['change_email'] = 'success';
            }
        }
    }

    // Upload picture if a new one is provided
    if ($_FILES['picture']['size']) {
        $new_picture = UserManager::update_user_picture(
            api_get_user_id(),
            $_FILES['picture']['name'],
            $_FILES['picture']['tmp_name']
        );

        if ($new_picture) {
            $user_data['picture_uri'] = $new_picture;
            $_SESSION['image_uploaded'] = 'success';
        }
    } elseif (!empty($user_data['remove_picture'])) {
        // remove existing picture if asked
        UserManager::delete_user_picture(api_get_user_id());
        $user_data['picture_uri'] = '';
    }

    // Remove production.
    if (isset($user_data['remove_production']) &&
        is_array($user_data['remove_production'])
    ) {
        foreach (array_keys($user_data['remove_production']) as $production) {
            UserManager::remove_user_production(api_get_user_id(), urldecode($production));
        }
        if ($production_list = UserManager::build_production_list(api_get_user_id(), true, true)) {
            $form->insertElementBefore(
                $form->createElement('static', null, null, $production_list),
                'productions_list'
            );
        }
        $form->removeElement('productions_list');
        $file_deleted = true;
    }

    // upload production if a new one is provided
    if (isset($_FILES['production']) && $_FILES['production']['size']) {
        $res = upload_user_production(api_get_user_id());
        if (!$res) {
            //it's a bit excessive to assume the extension is the reason why
            // upload_user_production() returned false, but it's true in most cases
            $filtered_extension = true;
        } else {
            $_SESSION['production_uploaded'] = 'success';
        }
    }

    // remove values that shouldn't go in the database
    unset($user_data['password0'],$user_data['password1'], $user_data['password2'], $user_data['MAX_FILE_SIZE'],
    $user_data['remove_picture'], $user_data['apply_change'], $user_data['email'] );

    // Following RFC2396 (http://www.faqs.org/rfcs/rfc2396.html), a URI uses ':' as a reserved character
    // we can thus ensure the URL doesn't contain any scheme name by searching for ':' in the string
    $my_user_openid = isset($user_data['openid']) ? $user_data['openid'] : '';
    if (!preg_match('/^[^:]*:\/\/.*$/', $my_user_openid)) {
        //ensure there is at least a http:// scheme in the URI provided
        $user_data['openid'] = 'http://'.$my_user_openid;
    }
    $extras = array();

    //Checking the user language
    $languages = api_get_languages();
    if (!in_array($user_data['language'], $languages['folder'])) {
        $user_data['language'] = api_get_setting('platformLanguage');
    }

    //Only update values that are request by the "profile" setting
    $profile_list = api_get_setting('profile');
    //Adding missing variables

    $available_values_to_modify = array();
    foreach($profile_list as $key => $status) {
        if ($status == 'true') {
            switch($key) {
                case 'login':
                    $available_values_to_modify[] = 'username';
                    break;
                case 'name':
                    $available_values_to_modify[] = 'firstname';
                    $available_values_to_modify[] = 'lastname';
                    break;
                case 'picture':
                    $available_values_to_modify[] = 'picture_uri';
                    break;
                default:
                    $available_values_to_modify[] = $key;
                    break;
            }
        }
    }

    //Fixing missing variables
    $available_values_to_modify = array_merge(
        $available_values_to_modify,
        array('competences', 'diplomas', 'openarea', 'teach', 'openid')
    );

    // build SQL query
    $sql = "UPDATE $table_user SET";
    unset($user_data['api_key_generate']);

    foreach ($user_data as $key => $value) {
        if (substr($key, 0, 6) == 'extra_') { //an extra field
           continue;
        } elseif (strpos($key, 'remove_extra_') !== false) {
            /*$extra_value = Security::filter_filename(urldecode(key($value)));
            // To remove from user_field_value and folder
            UserManager::update_extra_field_value($user_id, substr($key,13), $extra_value);*/
        } else {
            if (in_array($key, $available_values_to_modify)) {
                $sql .= " $key = '".Database::escape_string($value)."',";
            }
        }
    }

    //change email
    if ($allow_users_to_change_email_with_no_password) {
        if (isset($changeemail) && in_array('email', $available_values_to_modify)) {
            $sql .= " email = '".Database::escape_string($changeemail)."',";
        }
        if (isset($password) && in_array('password', $available_values_to_modify)) {
            $password = api_get_encrypted_password($password);
            $sql .= " password = '".Database::escape_string($password)."'";
        } else {
            // remove trailing , from the query we have so far
            $sql = rtrim($sql, ',');
        }
    } else {
        if (isset($changeemail) && !isset($password) && in_array('email', $available_values_to_modify)) {
            $sql .= " email = '".Database::escape_string($changeemail)."'";
        } else {
            if (isset($password) && in_array('password', $available_values_to_modify)) {
                if (isset($changeemail) && in_array('email', $available_values_to_modify)) {
                    $sql .= " email = '".Database::escape_string($changeemail)."',";
                }
                $password = api_get_encrypted_password($password);
                $sql .= " password = '".Database::escape_string($password)."'";
            } else {
                // remove trailing , from the query we have so far
                $sql = rtrim($sql, ',');
            }
        }
    }

    if (api_get_setting('profile', 'officialcode') == 'true' && isset($user_data['official_code'])) {
        $sql .= ", official_code = '".Database::escape_string($user_data['official_code'])."'";
    }

    $sql .= " WHERE user_id  = '".api_get_user_id()."'";
    Database::query($sql);

    $extraField = new ExtraFieldValue('user');
    $extraField->saveFieldValues($user_data);

    // User tag process
    //1. Deleting all user tags
    //$list_extra_field_type_tag = UserManager::get_all_extra_field_by_type(UserManager::USER_FIELD_TYPE_TAG);

    /*if (is_array($list_extra_field_type_tag) && count($list_extra_field_type_tag)>0) {
        foreach ($list_extra_field_type_tag as $id) {
            UserManager::delete_user_tags(api_get_user_id(), $id);
        }
    }

    //2. Update the extra fields and user tags if available
    if (is_array($extras) && count($extras)> 0) {
        foreach ($extras as $key => $value) {
            //3. Tags are process in the UserManager::update_extra_field_value by the UserManager::process_tags function
            // For array $value -> if exists key 'tmp_name' then must not be empty
            // This avoid delete from user field value table when doesn't upload a file
            if (is_array($value)) {
                if (array_key_exists('tmp_name', $value) && empty($value['tmp_name'])) {
                    //Nothing to do
                } else {
                    if (array_key_exists('tmp_name', $value)) {
                        $value['tmp_name'] = Security::filter_filename($value['tmp_name']);
                    }
                    if (array_key_exists('name', $value)) {
                        $value['name'] = Security::filter_filename($value['name']);
                    }
                    UserManager::update_extra_field_value($user_id, $key, $value);
                }
            } else {
                UserManager::update_extra_field_value($user_id, $key, $value);
            }
        }
    }*/

    // re-init the system to take new settings into account
    $_SESSION['_user']['uidReset'] = true;
    $_SESSION['noredirection'] = true;
    $_SESSION['profile_update'] = 'success';
    $url = api_get_self()."?{$_SERVER['QUERY_STRING']}".($filtered_extension && strpos($_SERVER['QUERY_STRING'], '&fe=1') === false ? '&fe=1' : '');
    header("Location: ".$url);
    exit;
}

// the header

$actions = null;
if (api_get_setting('allow_social_tool') != 'true') {
    if (api_get_setting('extended_profile') == 'true') {
        $actions .= '<div class="actions">';

        if (api_get_setting('allow_social_tool') == 'true' && api_get_setting('allow_message_tool') == 'true') {
            $actions .= '<a href="'.api_get_path(WEB_PATH).'main/social/profile.php">'.Display::return_icon('shared_profile.png', get_lang('ViewSharedProfile')).'</a>';
        }
        if (api_get_setting('allow_message_tool') == 'true') {
            $actions .= '<a href="'.api_get_path(WEB_PATH).'main/messages/inbox.php">'.Display::return_icon('inbox.png', get_lang('Messages')).'</a>';
        }
        $show = isset($_GET['show']) ? '&amp;show='.Security::remove_XSS($_GET['show']) : '';

        if (isset($_GET['type']) && $_GET['type'] == 'extended') {
            $actions .= '<a href="profile.php?type=reduced'.$show.'">'.Display::return_icon('edit.png', get_lang('EditNormalProfile'),'',16).'</a>';
        } else {
            $actions .= '<a href="profile.php?type=extended'.$show.'">'.Display::return_icon('edit.png', get_lang('EditExtendProfile'),'',16).'</a>';
        }
        $actions .= '</div>';
    }
}

if (!empty($file_deleted)) {
    Display::addFlash(Display :: return_message(get_lang('FileDeleted'), 'normal', false));
} elseif (!empty($update_success)) {
    $message = get_lang('ProfileReg');

    if ($upload_picture_success) {
        $message .= '<br /> '.get_lang('PictureUploaded');
    }

    if ($upload_production_success) {
        $message.='<br />'.get_lang('ProductionUploaded');
    }
    Display::addFlash(Display :: return_message($message, 'normal', false));
}

if (!empty($msg_fail_changue_email)){
    $errormail=get_lang('ToChangeYourEmailMustTypeYourPassword');
    Display::addFlash(Display :: return_message($errormail, 'error', false));
}

if (!empty($msg_is_not_password)){
    $warning_msg = get_lang('CurrentPasswordEmptyOrIncorrect');
    Display::addFlash(Display :: return_message($warning_msg, 'warning', false));
}

$show_delete_account_button = api_get_setting('platform_unsubscribe_allowed') == 'true' ? true : false;

$tpl = new Template(get_lang('ModifyProfile'));
$tpl->assign('actions', $actions);

SocialManager::setSocialUserBlock($tpl, $user_id, 'messages');

if (api_get_setting('allow_social_tool') == 'true') {
    SocialManager::setSocialUserBlock($tpl, api_get_user_id(), 'home');
    $menu = SocialManager::show_social_menu(
        'home',
        null,
        api_get_user_id(),
        false,
        $show_delete_account_button
    );

    $tpl->assign('social_menu_block', $menu);
    $tpl->assign('social_right_content', $form->returnForm());
    $social_layout = $tpl->get_template('social/inbox.tpl');
    $tpl->display($social_layout);
} else {
    $bigImage = Usermanager::getUserPicture(api_get_user_id(), USER_IMAGE_SIZE_BIG);
    $normalImage = Usermanager::getUserPicture(api_get_user_id(), USER_IMAGE_SIZE_ORIGINAL);

    $imageToShow = '<div id="image-message-container">';
    $imageToShow .= '<a class="expand-image" href="'.$bigImage.'" /><img src="'.$normalImage.'"></a>';
    $imageToShow .= '</div>';

    $content = $imageToShow.$form->returnForm();

    $tpl->assign('content', $content);
    $tpl->display_one_col_template();
}
