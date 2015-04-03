<?php
/* For licensing terms, see /license.txt */
/**
 * Chamilo installation
 *
 * As seen from the user, the installation proceeds in 6 steps.
 * The user is presented with several webpages where he/she has to make choices
 * and/or fill in data.
 *
 * The aim is, as always, to have good default settings and suggestions.
 *
 * @todo reduce high level of duplication in this code
 * @todo (busy) organise code into functions
 * @package chamilo.install
 */

/*		CONSTANTS */

use \ChamiloSession as Session;
use Chamilo\UserBundle\Entity\User;

require_once __DIR__.'/../../vendor/autoload.php';

define('SYSTEM_INSTALLATION', 1);
define('INSTALL_TYPE_UPDATE', 'update');
define('FORM_FIELD_DISPLAY_LENGTH', 40);
define('DATABASE_FORM_FIELD_DISPLAY_LENGTH', 25);
define('MAX_FORM_FIELD_LENGTH', 80);

/*		PHP VERSION CHECK */

// Including necessary libraries.
require_once '../inc/lib/api.lib.php';

api_check_php_version('../inc/');

/* INITIALIZATION SECTION */

ob_implicit_flush(true);
session_start();
require_once api_get_path(LIBRARY_PATH).'database.constants.inc.php';
require_once 'install.lib.php';

// This value is use in database::query in order to prompt errors in the error log (course databases)
Database::$log_queries = true;

// The function api_get_setting() might be called within the installation scripts.
// We need to provide some limited support for it through initialization of the
// global array-type variable $_setting.
$_setting = array(
	'platform_charset' => 'UTF-8',
	'server_type' => 'production', // 'production' | 'test'
	'permissions_for_new_directories' => '0770',
	'permissions_for_new_files' => '0660',
	'stylesheets' => 'chamilo'
);

// Determination of the language during the installation procedure.
if (!empty($_POST['language_list'])) {
	$search = array('../', '\\0');
	$install_language = str_replace($search, '', urldecode($_POST['language_list']));
	Session::write('install_language',$install_language);
} elseif (isset($_SESSION['install_language']) && $_SESSION['install_language']) {
	$install_language = $_SESSION['install_language'];
} else {
	// Trying to switch to the browser's language, it is covenient for most of the cases.
	$install_language = detect_browser_language();
}

// Language validation.
if (!array_key_exists($install_language, get_language_folder_list())) {
    $install_language = 'english';
}

// Loading language files.
require api_get_path(SYS_LANG_PATH).'english/trad4all.inc.php';
require api_get_path(SYS_LANG_PATH).'english/install.inc.php';
if ($install_language != 'english') {
    include_once api_get_path(SYS_LANG_PATH).$install_language.'/trad4all.inc.php';
    include_once api_get_path(SYS_LANG_PATH).$install_language.'/install.inc.php';
}

// These global variables must be set for proper working of the function get_lang(...) during the installation.
$language_interface = $install_language;
$language_interface_initial_value = $install_language;

// Character set during the installation, it is always to be 'UTF-8'.
$charset = 'UTF-8';

// Initialization of the internationalization library.
api_initialize_internationalization();
// Initialization of the default encoding that will be used by the multibyte string routines in the internationalization library.
api_set_internationalization_default_encoding($charset);

// Page encoding initialization.
header('Content-Type: text/html; charset='. api_get_system_encoding());

// Setting the error reporting levels.
error_reporting(E_ALL);

// Overriding the timelimit (for large campusses that have to be migrated).
@set_time_limit(0);

// Upgrading from any subversion of 1.9
$update_from_version_8 = array('1.9.0', '1.9.2','1.9.4','1.9.6', '1.9.6.1', '1.9.8', '1.9.8.1', '1.9.8.2', '1.9.10', '1.9.10.2');

$my_old_version = '';
if (empty($tmp_version)) {
	$tmp_version = get_config_param('system_version');
}

if (!empty($_POST['old_version'])) {
	$my_old_version = $_POST['old_version'];
} elseif (!empty($tmp_version)) {
    $my_old_version = $tmp_version;
}

require_once __DIR__.'/version.php';

// A protection measure for already installed systems.

if (isAlreadyInstalledSystem()) {
	// The system has already been installed, so block re-installation.
	$global_error_code = 6;
	/*require '../inc/global_error_message.inc.php';
	die();*/
}

/*		STEP 1 : INITIALIZES FORM VARIABLES IF IT IS THE FIRST VISIT */

// Is valid request
$is_valid_request = isset($_REQUEST['is_executable']) ? $_REQUEST['is_executable'] : null;
/*foreach ($_POST as $request_index => $request_value) {
	if (substr($request_index, 0, 4) == 'step') {
		if ($request_index != $is_valid_request) {
			unset($_POST[$request_index]);
		}
	}
}*/

$badUpdatePath = false;
$emptyUpdatePath = true;
$proposedUpdatePath = '';
if (!empty($_POST['updatePath'])) {
	$proposedUpdatePath = $_POST['updatePath'];
}

if (@$_POST['step2_install'] || @$_POST['step2_update_8'] || @$_POST['step2_update_6']) {
	if (@$_POST['step2_install']) {
		$installType = 'new';
		$_POST['step2'] = 1;
	} else {
		$installType = 'update';
		if (@$_POST['step2_update_8']) {
			$emptyUpdatePath = false;
			$proposedUpdatePath = api_add_trailing_slash(empty($_POST['updatePath']) ? api_get_path(SYS_PATH) : $_POST['updatePath']);
			if (file_exists($proposedUpdatePath)) {
				if (in_array($my_old_version, $update_from_version_8)) {
					$_POST['step2'] = 1;
				} else {
					$badUpdatePath = true;
				}
			} else {
				$badUpdatePath = true;
			}
		}
	}
} elseif (@$_POST['step1']) {
	$_POST['updatePath'] = '';
	$installType = '';
	$updateFromConfigFile = '';
	unset($_GET['running']);
} else {
	$installType = isset($_GET['installType']) ? $_GET['installType'] : null;
	$updateFromConfigFile = isset($_GET['updateFromConfigFile']) ? $_GET['updateFromConfigFile'] : false;
}

if ($installType == 'update' && in_array($my_old_version, $update_from_version_8)) {
	// This is the main configuration file of the system before the upgrade.
	include api_get_path(CONFIGURATION_PATH).'configuration.php'; // Don't change to include_once
}

if (!isset($_GET['running'])) {
	$dbHostForm = 'localhost';
	$dbUsernameForm = 'root';
	$dbPassForm = '';
	$dbNameForm = 'chamilo';

	// Extract the path to append to the url if Chamilo is not installed on the web root directory.
	$urlAppendPath  = api_remove_trailing_slash(api_get_path(REL_PATH));
  	$urlForm 		= api_get_path(WEB_PATH);
	$pathForm 		= api_get_path(SYS_PATH);
	$emailForm = 'webmaster@localhost';
	if (!empty($_SERVER['SERVER_ADMIN'])) {
		$emailForm      = $_SERVER['SERVER_ADMIN'];
	}
	$email_parts = explode('@', $emailForm);
	if (isset($email_parts[1]) && $email_parts[1] == 'localhost') {
		$emailForm .= '.localdomain';
	}
	$adminLastName	= 'Doe';
	$adminFirstName	= 'John';
	$loginForm		= 'admin';
	$passForm		= api_generate_password();

	$campusForm		= 'My campus';
	$educationForm	= 'Albert Einstein';
	$adminPhoneForm	= '(000) 001 02 03';
	$institutionForm    = 'My Organisation';
	$institutionUrlForm = 'http://www.chamilo.org';
	$languageForm	    = api_get_interface_language();

	$checkEmailByHashSent = 0;
	$ShowEmailnotcheckedToStudent = 1;
	$userMailCanBeEmpty = 1;
	$allowSelfReg = 1;
	$allowSelfRegProf = 1;
	$encryptPassForm = 'sha1';
	$session_lifetime = 360000;
} else {
	foreach ($_POST as $key => $val) {
		$magic_quotes_gpc = ini_get('magic_quotes_gpc');
		if (is_string($val)) {
			if ($magic_quotes_gpc) {
				$val = stripslashes($val);
			}
			$val = trim($val);
			$_POST[$key] = $val;
		} elseif (is_array($val)) {
			foreach ($val as $key2 => $val2) {
				if ($magic_quotes_gpc) {
					$val2 = stripslashes($val2);
				}
				$val2 = trim($val2);
				$_POST[$key][$key2] = $val2;
			}
		}
		$GLOBALS[$key] = $_POST[$key];
	}
}

/*		NEXT STEPS IMPLEMENTATION */

$total_steps = 7;
if (!$_POST) {
	$current_step = 1;
} elseif (!empty($_POST['language_list']) or !empty($_POST['step1']) or ((!empty($_POST['step2_update_8']) or (!empty($_POST['step2_update_6'])))  && ($emptyUpdatePath or $badUpdatePath))) {
	$current_step = 2;
} elseif (!empty($_POST['step2']) or (!empty($_POST['step2_update_8']) or (!empty($_POST['step2_update_6'])) )) {
	$current_step = 3;
} elseif (!empty($_POST['step3'])) {
	$current_step = 4;
} elseif (!empty($_POST['step4'])) {
	$current_step = 5;
} elseif (!empty($_POST['step5'])) {
	$current_step = 6;
}

// Managing the $encryptPassForm
if ($encryptPassForm == '1') {
	$encryptPassForm = 'sha1';
} elseif ($encryptPassForm == '0') {
	$encryptPassForm = 'none';
}

?>
<!DOCTYPE html>
<head>
	<title>&mdash; <?php echo get_lang('ChamiloInstallation').' &mdash; '.get_lang('Version_').' '.$new_version; ?></title>
	<style type="text/css" media="screen, projection">
		/*<![CDATA[*/
        @import "../../web/assets/bootstrap/dist/css/bootstrap.min.css";
        @import "../../web/assets/fontawesome/css/font-awesome.min.css";
		@import "../css/base.css";
		@import "../css/<?php echo api_get_visual_theme(); ?>/default.css";
		/*]]>*/
	</style>
	<script type="text/javascript" src="../../web/assets/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript">
		$(document).ready( function() {
            $("#button_please_wait").hide();
			$("button").addClass('btn btn-default');

    		// Allow Chamilo install in IE
    		$("button").click(function() {
    			$("#is_executable").attr("value",$(this).attr("name"));
    		});

			//Blocking step6 button
    		$("#button_step6").click(function() {
            	$("#button_step6").hide();
    			$("#button_please_wait").html('<?php echo addslashes(get_lang('PleaseWait'));?>');
                $("#button_please_wait").show();
                $("#button_please_wait").attr('disabled', true);
    			$("#is_executable").attr("value",'step6');
        	});
	 	});

		init_visibility=0;
        $(document).ready( function() {
            $(".advanced_parameters").click(function() {
                if ($("#id_contact_form").css("display") == "none") {
					$("#id_contact_form").css("display","block");
					$("#img_plus_and_minus").html('&nbsp;<img src="<?php echo api_get_path(WEB_IMG_PATH) ?>div_hide.gif" alt="<?php echo get_lang('Hide') ?>" title="<?php echo get_lang('Hide')?>" style ="vertical-align:middle" >&nbsp;<?php echo get_lang('ContactInformation') ?>');
                } else {
					$("#id_contact_form").css("display","none");
					$("#img_plus_and_minus").html('&nbsp;<img src="<?php echo api_get_path(WEB_IMG_PATH) ?>div_show.gif" alt="<?php echo get_lang('Show') ?>" title="<?php echo get_lang('Show') ?>" style ="vertical-align:middle" >&nbsp;<?php echo get_lang('ContactInformation') ?>');
                }
            });
        });

        function send_contact_information() {
            var data_post = "";
            data_post += "person_name="+$("#person_name").val()+"&";
            data_post += "person_email="+$("#person_email").val()+"&";
            data_post += "company_name="+$("#company_name").val()+"&";
            data_post += "company_activity="+$("#company_activity option:selected").val()+"&";
            data_post += "person_role="+$("#person_role option:selected").val()+"&";
            data_post += "company_country="+$("#country option:selected").val()+"&";
            data_post += "company_city="+$("#company_city").val()+"&";
            data_post += "language="+$("#language option:selected").val()+"&";
            data_post += "financial_decision="+$("input[@name='financial_decision']:checked").val();

            $.ajax({
				contentType: "application/x-www-form-urlencoded",
				beforeSend: function(objeto) {},
				type: "POST",
				url: "<?php echo api_get_path(WEB_AJAX_PATH) ?>install.ajax.php?a=send_contact_information",
				data: data_post,
				success: function(datos) {
					if (datos == 'required_field_error') {
						message = "<?php echo get_lang('FormHasErrorsPleaseComplete') ?>";
					} else if (datos == '1') {
						message = "<?php echo get_lang('ContactInformationHasBeenSent') ?>";
					} else {
						message = "<?php echo get_lang('Error').': '.get_lang('ContactInformationHasNotBeenSent') ?>";
					}
					alert(message);
				}
            });
        }
    </script>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo api_get_system_encoding(); ?>" />
</head>
<body dir="<?php echo api_get_text_direction(); ?>" class="install-chamilo">

<div id="page-wrap">
<div id="main" class="container well-install">
    <header>
		<div class="row">
            <div id="header_left" class="col-md-4">
                <div id="logo">
                    <img src="../css/chamilo/images/header-logo.png" hspace="10" vspace="10" alt="Chamilo" />
                </div>
            </div>
        </div>
        <div class="navbar subnav">
            <div class="navbar-inner">
                <div class="container">
                    <div class="nav-collapse">
                        <ul class="nav nav-pills">
                            <li id="current" class="active">
                                <a target="_top" href="index.php"><?php echo get_lang('Homepage'); ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
	</header>
    <?php
    echo '<div class="page-header"><h1>'.get_lang('ChamiloInstallation').' &ndash; '.get_lang('Version_').' '.$new_version.'</h1></div>';
    ?>
    <div class="row">
        <div class="col-md-3">
            <div class="well">
                <ol>
                    <li <?php step_active('1'); ?>><?php echo get_lang('InstallationLanguage'); ?></li>
                    <li <?php step_active('2'); ?>><?php echo get_lang('Requirements'); ?></li>
                    <li <?php step_active('3'); ?>><?php echo get_lang('Licence'); ?></li>
                    <li <?php step_active('4'); ?>><?php echo get_lang('DBSetting'); ?></li>
                    <li <?php step_active('5'); ?>><?php echo get_lang('CfgSetting'); ?></li>
                    <li <?php step_active('6'); ?>><?php echo get_lang('PrintOverview'); ?></li>
                    <li <?php step_active('7'); ?>><?php echo get_lang('Installing'); ?></li>
                </ol>
            </div>
            <div id="note">
				<a class="btn btn-default" href="../../documentation/installation_guide.html" target="_blank">
                    <?php echo get_lang('ReadTheInstallationGuide'); ?>
                </a>
			</div>
        </div>

        <div class="col-md-9">

<form class="form-horizontal" id="install_form" method="post" action="<?php echo api_get_self(); ?>?running=1&amp;installType=<?php echo $installType; ?>&amp;updateFromConfigFile=<?php echo urlencode($updateFromConfigFile); ?>">
<?php

    $instalation_type_label = '';
    if ($installType == 'new'){
        $instalation_type_label  = get_lang('NewInstallation');
    }elseif ($installType == 'update') {
        $update_from_version = isset($update_from_version) ? $update_from_version : null;
        $instalation_type_label = get_lang('UpdateFromLMSVersion').(is_array($update_from_version) ? implode('|', $update_from_version) : '');
    }
    if (!empty($instalation_type_label) && empty($_POST['step6'])) {
    	echo '<div class="page-header"><h2>'.$instalation_type_label.'</h2></div>';
    }
    ?>
	<input type="hidden" name="updatePath"           value="<?php if (!$badUpdatePath) echo api_htmlentities($proposedUpdatePath, ENT_QUOTES); ?>" />
	<input type="hidden" name="urlAppendPath"        value="<?php echo api_htmlentities($urlAppendPath, ENT_QUOTES); ?>" />
	<input type="hidden" name="pathForm"             value="<?php echo api_htmlentities($pathForm, ENT_QUOTES); ?>" />
	<input type="hidden" name="urlForm"              value="<?php echo api_htmlentities($urlForm, ENT_QUOTES); ?>" />
	<input type="hidden" name="dbHostForm"           value="<?php echo api_htmlentities($dbHostForm, ENT_QUOTES); ?>" />
	<input type="hidden" name="dbUsernameForm"       value="<?php echo api_htmlentities($dbUsernameForm, ENT_QUOTES); ?>" />
	<input type="hidden" name="dbPassForm"           value="<?php echo api_htmlentities($dbPassForm, ENT_QUOTES); ?>" />
	<input type="hidden" name="dbNameForm"           value="<?php echo api_htmlentities($dbNameForm, ENT_QUOTES); ?>" />
	<input type="hidden" name="allowSelfReg"         value="<?php echo api_htmlentities($allowSelfReg, ENT_QUOTES); ?>" />
	<input type="hidden" name="allowSelfRegProf"     value="<?php echo api_htmlentities($allowSelfRegProf, ENT_QUOTES); ?>" />
	<input type="hidden" name="emailForm"            value="<?php echo api_htmlentities($emailForm, ENT_QUOTES); ?>" />
	<input type="hidden" name="adminLastName"        value="<?php echo api_htmlentities($adminLastName, ENT_QUOTES); ?>" />
	<input type="hidden" name="adminFirstName"       value="<?php echo api_htmlentities($adminFirstName, ENT_QUOTES); ?>" />
	<input type="hidden" name="adminPhoneForm"       value="<?php echo api_htmlentities($adminPhoneForm, ENT_QUOTES); ?>" />
	<input type="hidden" name="loginForm"            value="<?php echo api_htmlentities($loginForm, ENT_QUOTES); ?>" />
	<input type="hidden" name="passForm"             value="<?php echo api_htmlentities($passForm, ENT_QUOTES); ?>" />
	<input type="hidden" name="languageForm"         value="<?php echo api_htmlentities($languageForm, ENT_QUOTES); ?>" />
	<input type="hidden" name="campusForm"           value="<?php echo api_htmlentities($campusForm, ENT_QUOTES); ?>" />
	<input type="hidden" name="educationForm"        value="<?php echo api_htmlentities($educationForm, ENT_QUOTES); ?>" />
	<input type="hidden" name="institutionForm"      value="<?php echo api_htmlentities($institutionForm, ENT_QUOTES); ?>" />
	<input type="hidden" name="institutionUrlForm"   value="<?php echo api_stristr($institutionUrlForm, 'http://', false) ? api_htmlentities($institutionUrlForm, ENT_QUOTES) : api_stristr($institutionUrlForm, 'https://', false) ? api_htmlentities($institutionUrlForm, ENT_QUOTES) : 'http://'.api_htmlentities($institutionUrlForm, ENT_QUOTES); ?>" />
	<input type="hidden" name="checkEmailByHashSent" value="<?php echo api_htmlentities($checkEmailByHashSent, ENT_QUOTES); ?>" />
	<input type="hidden" name="ShowEmailnotcheckedToStudent" value="<?php echo api_htmlentities($ShowEmailnotcheckedToStudent, ENT_QUOTES); ?>" />
	<input type="hidden" name="userMailCanBeEmpty"   value="<?php echo api_htmlentities($userMailCanBeEmpty, ENT_QUOTES); ?>" />
	<input type="hidden" name="encryptPassForm"      value="<?php echo api_htmlentities($encryptPassForm, ENT_QUOTES); ?>" />
	<input type="hidden" name="session_lifetime"     value="<?php echo api_htmlentities($session_lifetime, ENT_QUOTES); ?>" />
	<input type="hidden" name="old_version"          value="<?php echo api_htmlentities($my_old_version, ENT_QUOTES); ?>" />
	<input type="hidden" name="new_version"          value="<?php echo api_htmlentities($new_version, ENT_QUOTES); ?>" />
<?php

if (@$_POST['step2']) {
	//STEP 3 : LICENSE
	display_license_agreement();
} elseif (@$_POST['step3']) {
	//STEP 4 : MYSQL DATABASE SETTINGS
	display_database_settings_form(
		$installType,
		$dbHostForm,
		$dbUsernameForm,
		$dbPassForm,
		$dbNameForm
	);
} elseif (@$_POST['step4']) {
	//STEP 5 : CONFIGURATION SETTINGS

	//if update, try getting settings from the database...
	if ($installType == 'update') {
		$db_name = $dbNameForm;

		$manager = testDbConnect(
			$dbHostForm,
			$dbUsernameForm,
			$dbPassForm,
			$dbNameForm
		);

		$tmp = get_config_param_from_db('platformLanguage');
		if (!empty($tmp)) $languageForm = $tmp;

		$tmp = get_config_param_from_db('emailAdministrator');
		if (!empty($tmp)) $emailForm = $tmp;

		$tmp = get_config_param_from_db('administratorName');
		if (!empty($tmp)) $adminFirstName = $tmp;

		$tmp = get_config_param_from_db('administratorSurname');
		if (!empty($tmp)) $adminLastName = $tmp;

		$tmp = get_config_param_from_db('administratorTelephone');
		if (!empty($tmp)) $adminPhoneForm = $tmp;

		$tmp = get_config_param_from_db('siteName');
		if (!empty($tmp)) $campusForm = $tmp;

		$tmp = get_config_param_from_db('Institution');
		if (!empty($tmp)) $institutionForm = $tmp;

		$tmp = get_config_param_from_db('InstitutionUrl');
		if (!empty($tmp)) $institutionUrlForm = $tmp;

		// For version 1.9
		$urlForm = $_configuration['root_web'];
		$encryptPassForm = get_config_param('userPasswordCrypted');
		// Managing the $encryptPassForm
		if ($encryptPassForm == '1') {
			$encryptPassForm = 'sha1';
		} elseif ($encryptPassForm == '0') {
			$encryptPassForm = 'none';
		}

		$allowSelfReg = false;
		$tmp = get_config_param_from_db('allow_registration');
		if (!empty($tmp)) $allowSelfReg = $tmp;

		$allowSelfRegProf = false;
		$tmp = get_config_param_from_db('allow_registration_as_teacher');
		if (!empty($tmp)) $allowSelfRegProf = $tmp;
	}

	display_configuration_settings_form(
		$installType,
		$urlForm,
		$languageForm,
		$emailForm,
		$adminFirstName,
		$adminLastName,
		$adminPhoneForm,
		$campusForm,
		$institutionForm,
		$institutionUrlForm,
		$encryptPassForm,
		$allowSelfReg,
		$allowSelfRegProf,
		$loginForm,
		$passForm
	);

} elseif (@$_POST['step5']) {
	//STEP 6 : LAST CHECK BEFORE INSTALL
?>
    <div class="RequirementHeading">
		<h2><?php echo display_step_sequence().get_lang('LastCheck'); ?></h2>
	</div>
    <div class="RequirementContent">
		<?php echo get_lang('HereAreTheValuesYouEntered'); ?>
	</div><br />

    <?php if ($installType == 'new'): ?>
	<?php echo get_lang('AdminLogin').' : <strong>'.$loginForm; ?></strong><br />
	<?php echo get_lang('AdminPass').' : <strong>'.$passForm; /* TODO: Maybe this password should be hidden too? */ ?></strong><br /><br />
	<?php else: ?>
	<?php endif;

	if (api_is_western_name_order()) {
		echo get_lang('AdminFirstName').' : '.$adminFirstName, '<br />', get_lang('AdminLastName').' : '.$adminLastName, '<br />';
	} else {
		echo get_lang('AdminLastName').' : '.$adminLastName, '<br />', get_lang('AdminFirstName').' : '.$adminFirstName, '<br />';
	}

    echo get_lang('AdminEmail').' : '.$emailForm; ?><br />
	<?php echo get_lang('AdminPhone').' : '.$adminPhoneForm; ?><br />
	<?php echo get_lang('MainLang').' : '.$languageForm; ?><br /><br />
	<?php echo get_lang('DBHost').' : '.$dbHostForm; ?><br />
	<?php echo get_lang('DBLogin').' : '.$dbUsernameForm; ?><br />
	<?php echo get_lang('DBPassword').' : '.str_repeat('*', api_strlen($dbPassForm)); ?><br />
	<?php echo get_lang('MainDB').' : <strong>'.$dbNameForm; ?></strong><br />
	<?php echo get_lang('AllowSelfReg').' : '.($allowSelfReg ? get_lang('Yes') : get_lang('No')); ?><br />
	<?php echo get_lang('EncryptMethodUserPass').' : ';
  	echo $encryptPassForm;
	?>
    <br /><br />

	<?php echo get_lang('CampusName').' : '.$campusForm; ?><br />
	<?php echo get_lang('InstituteShortName').' : '.$institutionForm; ?><br />
	<?php echo get_lang('InstituteURL').' : '.$institutionUrlForm; ?><br />
	<?php echo get_lang('ChamiloURL').' : '.$urlForm; ?><br />
	<?php
	if ($installType == 'new') {
		echo Display::display_warning_message(
			'<h3 style="text-align: center">'.get_lang(
				'Warning'
			).'</h3><br />'.get_lang('TheInstallScriptWillEraseAllTables'),
			false
		);
	}
	?>

	<table width="100%">
        <tr>
            <td>
                <button type="submit" class="btn btn-default" name="step4" value="&lt; <?php echo get_lang('Previous'); ?>" >
					<i class="fa fa-backward"> </i> <?php echo get_lang('Previous'); ?>
				</button>
            </td>
            <td align="right">
                <input type="hidden" name="is_executable" id="is_executable" value="-" />
                <input type="hidden" name="step6" value="1" />
                <button id="button_step6" class="btn btn-success" type="submit" name="button_step6" value="<?php echo get_lang('InstallChamilo'); ?>">
                    <i class="fa fa-floppy-o"> </i>
                    <?php echo get_lang('InstallChamilo'); ?>
                </button>
                <button class="btn btn-save" id="button_please_wait"></button>
            </td>
        </tr>
	</table>

<?php
} elseif (@$_POST['step6']) {
	//STEP 6 : INSTALLATION PROCESS
    $current_step = 7;
    $msg = get_lang('InstallExecution');
    if ($installType == 'update') {
        $msg = get_lang('UpdateExecution');
    }
    echo '<div class="RequirementHeading">
          <h2>'.display_step_sequence().$msg.'</h2>
          <div id="pleasewait" class="warning-message">'.get_lang('PleaseWaitThisCouldTakeAWhile').'</div>
          </div>';

    // Push the web server to send these strings before we start the real
    // installation process
    flush();
    $f = ob_get_contents();
    if (!empty($f)) {
        ob_flush(); //#5565
    }

	if ($installType == 'update') {
		remove_memory_and_time_limits();

		$manager = testDbConnect(
			$dbHostForm,
			$dbUsernameForm,
			$dbPassForm,
			$dbNameForm
		);

		$perm = api_get_permissions_for_new_directories();
		$perm_file = api_get_permissions_for_new_files();

        Log::notice('Starting migration process from '.$my_old_version.' ('.time().')');

		switch ($my_old_version) {
            case '1.9.0':
            case '1.9.2':
            case '1.9.4':
            case '1.9.6':
            case '1.9.6.1':
            case '1.9.8':
            case '1.9.8.1':
            case '1.9.8.2':
            case '1.9.10':
			case '1.9.10.2':

				// Fix type "enum" before running the migration with Doctrine
				Database::query("ALTER TABLE course_category MODIFY COLUMN auth_course_child VARCHAR(40) DEFAULT 'TRUE'");
				Database::query("ALTER TABLE course_category MODIFY COLUMN auth_cat_child VARCHAR(40) DEFAULT 'TRUE'");
				Database::query("ALTER TABLE c_quiz_answer MODIFY COLUMN hotspot_type varchar(40) default NULL");
				Database::query("ALTER TABLE c_tool MODIFY COLUMN target varchar(20) NOT NULL default '_self'");
				Database::query("ALTER TABLE c_link MODIFY COLUMN on_homepage char(10) NOT NULL default '0'");
				Database::query("ALTER TABLE c_blog_rating MODIFY COLUMN rating_type char(40) NOT NULL default 'post'");
				Database::query("ALTER TABLE c_survey MODIFY COLUMN anonymous char(10) NOT NULL default '0'");

				// Migrate using the file Version110.php
				migrate('110', 1, $dbNameForm, $dbUsernameForm, $dbPassForm, $dbHostForm);
                //include 'update-files-1.9.0-1.10.0.inc.php';
                // Only updates the configuration.inc.php with the new version
                //include 'update-configuration.inc.php';
                break;
            default:
                break;
        }
		exit;
    } else {
		set_file_folder_permissions();

		$manager = testDbConnect(
			$dbHostForm,
			$dbUsernameForm,
			$dbPassForm,
			null
		);

		$dbNameForm = preg_replace('/[^a-zA-Z0-9_\-]/', '', $dbNameForm);

		// Create database
		$createDatabase = true;
		$databases = $manager->getConnection()->getSchemaManager()->listDatabases();

		if (in_array($dbNameForm, $databases)) {
			$createDatabase = false;
		}

		// Create database
		if ($createDatabase) {
			//$manager->getConnection()->getSchemaManager()->dropAndCreateDatabase($dbNameForm);
			//$manager->getConnection()->getSchemaManager()->createDatabase($dbNameForm);
		}
		// Drop the database anyways
		$manager->getConnection()->getSchemaManager()->dropAndCreateDatabase($dbNameForm);

		$manager = testDbConnect(
			$dbHostForm,
			$dbUsernameForm,
			$dbPassForm,
			$dbNameForm
		);

		$metadatas = $manager->getMetadataFactory()->getAllMetadata();
		$schema = $manager->getConnection()->getSchemaManager()->createSchema();

		// Create database
		$tool = new \Doctrine\ORM\Tools\SchemaTool($manager);
		$tool->createSchema($metadatas);

		// Inserting data
		$data = file_get_contents('data.sql');
		$result = $manager->getConnection()->prepare($data);
		$result->execute();
		$result->closeCursor();

		// Create users
		switch ($encryptPassForm) {
			case 'md5' :
				$passToStore = md5($passForm);
				break;
			case 'sha1' :
				$passToStore = sha1($passForm);
				break;
			case 'none' :
			default:
				$passToStore = $passForm;
				break;
		}

		$sql = "INSERT INTO user (user_id, lastname, firstname, username, password, auth_source, email, status, official_code, phone, creator_id, registration_date, expiration_date,active,openid,language) VALUES
		(1, '$adminLastName','$adminFirstName','$loginForm','$passToStore','".PLATFORM_AUTH_SOURCE."','$emailForm',1,'ADMIN','$adminPhoneForm',1,NOW(),NULL,'1',NULL,'$languageForm'),
		(2, 'Anonymous', 'Joe', '', '', 'platform', 'anonymous@localhost', 6, 'anonymous', NULL, 1, NOW(), NULL, 1,NULL,'$languageForm')";
		Database::query($sql);

		// The chosen during the installation platform language should be enabled.
		$sql = "UPDATE language SET available=1 WHERE dokeos_folder = '$languageForm'";
		Database::query($sql);

		// Install settings

		installSettings(
			$institutionForm,
			$institutionUrlForm,
			$campusForm,
			$emailForm,
			$adminLastName,
			$adminFirstName,
			$languageForm,
			$allowSelfReg,
    		$allowSelfRegProf
		);

		lockSettings();

		update_dir_and_files_permissions();


	}
    display_after_install_message($installType);
    //Hide the "please wait" message sent previously
    echo '<script>$(\'#pleasewait\').hide(\'fast\');</script>';

} elseif (@$_POST['step1'] || $badUpdatePath) {
	//STEP 1 : REQUIREMENTS
    //make sure that proposed path is set, shouldn't be necessary but...
	if (empty($proposedUpdatePath)) {
		$proposedUpdatePath = $_POST['updatePath'];
	}
    display_requirements($installType, $badUpdatePath, $proposedUpdatePath, $update_from_version_8);
} else {
	// This is the start screen.
    display_language_selection();
}
?>
</form>
</div>                  <!-- col-md-9-->
</div>  <!-- row -->
</div> <!-- main end-->
<div class="push"></div>
</div><!-- wrapper end-->
<footer></footer>
</body>
</html>
