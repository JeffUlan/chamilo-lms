<?php
/* For licensing terms, see /license.txt */

/**
 *  INITIALISE OTHER VARIABLES & CONSTANTS
 */
$dropbox_cnf['courseId'] = $_cid;
//path to dropbox subdir in course containing the uploaded files
$dropbox_cnf['sysPath'] = api_get_path(SYS_COURSE_PATH) . $_course['path'] . '/dropbox';
$dropbox_cnf['webPath'] = api_get_path(WEB_COURSE_PATH) . $_course['path'] . '/dropbox';

//file size limit as imposed by the platform admin (see Chamilo Config Settings on the platform administration section)
$dropbox_cnf['maxFilesize'] = api_get_setting('dropbox_max_filesize');
$dropbox_cnf['allowOverwrite'] = api_string_2_boolean(api_get_setting('dropbox_allow_overwrite'));
$dropbox_cnf['allowJustUpload'] = api_string_2_boolean(api_get_setting('dropbox_allow_just_upload'));
$dropbox_cnf['allowStudentToStudent'] = api_string_2_boolean(api_get_setting('dropbox_allow_student_to_student'));
$dropbox_cnf['allowGroup'] = api_string_2_boolean(api_get_setting('dropbox_allow_group'));

/**
 * MAILING VARIABLES
 */
// false = no mailing functionality
$dropbox_cnf['allowMailing'] = api_string_2_boolean(api_get_setting('dropbox_allow_mailing'));
$dropbox_cnf['mailingIdBase'] = 10000000;  // bigger than any user_id,
// allowing enough space for pseudo_ids as uploader_id, dest_user_id, user_id:
// mailing pseudo_id = dropbox_cnf('mailingIdBase') + mailing id
$dropbox_cnf['mailingZipRegexp'] = '/^(.*)(STUDENTID|USERID|LOGINNAME)(.*)\.ZIP$/i';
$dropbox_cnf['mailingWhereSTUDENTID'] = 'official_code';
$dropbox_cnf['mailingWhereUSERID'] = 'username';
$dropbox_cnf['mailingWhereLOGINNAME'] = 'username';
$dropbox_cnf['mailingFileRegexp'] = '/^(.+)\.\w{1,4}$/';
$dropbox_cnf['sent_received_tabs'] = true;

return $dropbox_cnf;
