<?php /*
for more information: see languages.txt in the lang folder. 
*/
$langStatDB              = "Tracking DB.";
$langEnableTracking      = "Enable Tracking";
$langInstituteShortName  = "Your organisation short name";
$langWarningResponsible  = "Use this script only after backup. Chamilo team is not responsible if you lost or corrupted data";
$langAllowSelfRegProf  = "Allow self-registration as course creator";
$langEG = "ex.";
$langDBHost = "Database Host";
$langDBLogin = "Database Username";
$langDBPassword = "Database Password";
$langMainDB = "Main Chamilo database (DB)";
$langAllFieldsRequired = "all fields required";
$langPrintVers = "Printable version";
$langLocalPath = "Corresponding local path";
$langAdminEmail = "Administrator email";
$langAdminName = "Administrator Name";
$langAdminSurname = "Surname of the Administrator";
$langAdminLogin = "Administrator login";
$langAdminPass = "Administrator password (<font color=\"red\">you may want to change this</font>)";
$langEducationManager = "Project manager";
$langCampusName = "Your portal name";
$langDBSettingIntro = "The install script will create the Chamilo main database(s). Please note that Chamilo will need to create several databases. If you are allowed to use only one database by your Hosting Service, Chamilo will not work, unless you chose the option \"One database\".";
$langStep1 = "Step 1 of 6 ";
$langStep2 = "Step 2 of 6 ";
$langStep3 = "Step 3 of 6 ";
$langStep4 = "Step 4 of 6 ";
$langStep5 = "Step 5 of 6 ";
$langStep6 = "Step 6 of 6 ";
$langCfgSetting = "Config settings";
$langDBSetting = "MySQL database settings";
$langMainLang = "Main language";
$langLicence = "Licence";
$langLastCheck = "Last check before install";
$langRequirements = "Requirements";
$langDbPrefixForm = "MySQL database prefix";
$langDbPrefixCom = "Leave empty if not requested";
$langEncryptUserPass = "Encrypt user passwords in database";
$langSingleDb = "Use one or several DB for Chamilo";
$langAllowSelfReg = "Allow self-registration";
$langRecommended = "(recommended)";
$langScormDB = "Scorm DB";
$langAdminLastName = "Administrator last name";
$langAdminFirstName = "Administrator first name";
$langAdminPhone = "Administrator telephone";
$langInstituteURL = "URL of this organisation";
$langDokeosURL = "URL of Chamilo";
$langUserDB = "User DB";
$langInstallationLanguage = "Installation Language";
$ReadThoroughly = "Read thoroughly";
$DokeosNeedFollowingOnServer = "For Chamilo to work, you need the following on your server";
$WarningExistingDokeosInstallationDetected = "Warning!<br />The installer has detected an existing Chamilo platform on your system.";
$NewInstallation = "New installation";
$CheckDatabaseConnection = "Check database connection";
$PrintOverview = "Show Overview";
$Installing = "Install";
$of = "of";
$Step = "Step";
$Of = "of";
$MoreDetails = "For more details";
$ServerRequirements = "Server requirements";
$ServerRequirementsInfo = "Libraries and features the server must provide to use Chamilo to its full extent";
$PHPVersion = "PHP version";
$support = "support";
$PHPVersionOK = "PHP version is OK";
$OK = "OK";
$RecommendedSettings = "Recommended settings";
$RecommendedSettingsInfo = "Recommended settings for your server configuration. These settings are set in the php.ini configuration file on your server.";
$Setting = "Setting";
$Actual = "Currently";
$DirectoryAndFilePermissions = "Directory and files permissions";
$DirectoryAndFilePermissionsInfo = "Some directories and the files they include must be writeable by the web server in order for Chamilo to run (students uploaded files, homepage html files, ...). This might imply a manual change on your server (outside of this interface).";
$NotWritable = "Not writable";
$Writable = "Writable";
$ExtensionLDAPNotAvailable = "LDAP Extension not available";
$ExtensionGDNotAvailable = "GD Extension not available";
$DokeosLicenseInfo = "Chamilo is free software distributed under the GNU General Public licence (GPL).";
$IAccept = "I Accept";
$ConfigSettingsInfo = "The following values will be written into your configuration file";
$DokeosInstallation = "Chamilo Installation";
$InstallDokeos = "Install Chamilo";
$GoToYourNewlyCreatedPortal = "Go to your newly created portal.";
$FirstUseTip = "When you enter your campus for the first time, the best way to understand it is to register with the option \\\'Create course area\\\' and then follow the way.";
$Version_ = "Version";
$UpdateFromDokeosVersion = "Update from Chamilo";
$WelcomToTheDokeosInstaller = "Welcome to the Chamilo Installer";
$PleaseSelectInstallationProcessLanguage = "Please select the language you\\\'d like to use when installing";
$ReadTheInstallGuide = "read the installation guide";
$HereAreTheValuesYouEntered = "Here are the values you entered";
$PrintThisPageToRememberPassAndOthers = "Print this page to remember your password and other settings";
$TheInstallScriptWillEraseAllTables = "The install script will erase all tables of the selected databases. We heavily recommend you do a full backup of them before confirming this last install step.";
$PleaseWait = "Please wait";
$Warning = "Warning";
$ReadWarningBelow = "read warning below";
$SecurityAdvice = "Security advice";
$YouHaveMoreThanXCourses = "You have more than %d courses on your Chamilo platform ! Only %d courses have been updated. To update the other courses, %sclick here %s";
$ToProtectYourSiteMakeXAndYReadOnly = "To protect your site, make %s and %s read-only (CHMOD 444).";
$Error = "Error";
$Back = "Back";
$HasNotBeenFound = "has not been found";
$PleaseGoBackToStep1 = "Please go back to Step 1";
$HasNotBeenFoundInThatDir = "has not been found in that directory";
$OldVersionRootPath = "Old version\\\'s root path";
$NoWritePermissionPleaseReadInstallGuide = "Some files or folders don\\\'t have writing permission. To be able to install Chamilo you should first change their permissions (using CHMOD). Please read the %s installation guide %s";
$DBServerDoesntWorkOrLoginPassIsWrong = "The database server doesn\\\'t work or login / pass is bad";
$PleaseCheckTheseValues = "Please check these values";
$PleaseGoBackToStep = "Please go back to Step";
$DBSettingUpgradeIntro = "The upgrade script will recover and update the Chamilo database(s). In order to do this, this script will use the databases and settings defined below. Because our software runs on a wide range of systems and because all of them might not have been tested, we strongly recommend you do a full backup of your databases before you proceed with the upgrade!";
$ExtensionMBStringNotAvailable = "MBString extension not available";
$ExtensionMySQLNotAvailable = "MySQL extension not available";
$DokeosArtLicense = "The images and media galleries of Chamilo use images from Nuvola, Crystal Clear and Tango icon galleries. Other images and media like diagrams and Flash animations are borrowed from Wikimedia and Ali Pakdel\\\'s and Denis Hoa\\\'s courses with their agreement and released under BY-SA Creative Commons license. You may find the license details at <a href=\"http://creativecommons.org/licenses/by-sa/3.0/\">the CC website</a>, where a link to the full text of the license is provided at the bottom of the page.";
$PleasGoBackToStep1 = "يرجى العودة إلى الخطوة 1";
?>