<?php
/**
 * This script is included by main/admin/settings.lib.php and generally 
 * includes things to execute in the main database (settings_current table)
 */
$t_settings = Database::get_main_table(TABLE_MAIN_SETTINGS_CURRENT);
$t_options = Database::get_main_table(TABLE_MAIN_SETTINGS_OPTIONS);
$sql = "INSERT INTO $t_settings
    (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) 
    VALUES
    ('bbb_plugin', '', 'radio', 'Extra', 'false', 'BigBlueButtonEnableTitle','BigBlueButtonEnableComment',NULL,NULL, 1)";
Database::query($sql);
$sql = "INSERT INTO $t_options (variable, value, display_text) VALUES ('bbb_plugin', 'true', 'Yes')";
Database::query($sql);
$sql = "INSERT INTO $t_options (variable, value, display_text) VALUES ('bbb_plugin', 'false', 'No')";
Database::query($sql);
$sql = "INSERT INTO $t_settings
    (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) 
    VALUES
    ('bbb_plugin_host', '', 'textfield', 'Extra', '192.168.0.100', 'BigBlueButtonHostTitle','BigBlueButtonHostComment',NULL,NULL, 1)";
Database::query($sql);
$sql = "INSERT INTO $t_settings
    (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) 
    VALUES
    ('bbb_plugin_salt', '', 'textfield', 'Extra', '', 'BigBlueButtonSecuritySaltTitle','BigBlueButtonSecuritySaltComment',NULL,NULL, 1)";
Database::query($sql);
$table = Database::get_main_table('plugin_bbb');
$sql = "CREATE TABLE $table ( " .
        "id BIGINT unsigned NOT NULL auto_increment PRIMARY KEY, " .
        "course_id INT unsigned NOT NULL DEFAULT 0, " .
        "name VARCHAR(255) NOT NULL DEFAULT '', " .
        "meetingname VARCHAR(255) NOT NULL DEFAULT '', " .
        "meetingid VARCHAR(255) NOT NULL DEFAULT '', " .
        "attendeepw VARCHAR(255) NOT NULL DEFAULT '', " .
        "moderatorpw VARCHAR(255) NOT NULL DEFAULT '', " .
        "autologin VARCHAR(255) NOT NULL DEFAULT '', " .
        "newwindow VARCHAR(255) NOT NULL DEFAULT '', " .
        "welcomemsg VARCHAR(255) NOT NULL DEFAULT '')";
Database::query($sql);
// update existing courses to add conference settings
$t_courses = Database::get_main_table(TABLE_MAIN_COURSE);
$sql = "SELECT id, code, db_name FROM $t_courses ORDER BY id";
$res = Database::query($sql);
while ($row = Database::fetch_assoc($res)) {
    $t_course = Database::get_course_table(TABLE_COURSE_SETTING,$row['db_name']);
    $sql_course = "INSERT INTO $t_course (variable,value,category) VALUES ('big_blue_button_meeting_name','','plugins')";
    $r = Database::query($sql_course);
    $sql_course = "INSERT INTO $t_course (variable,value,category) VALUES ('big_blue_button_attendee_password','','plugins')";
    $r = Database::query($sql_course);
    $sql_course = "INSERT INTO $t_course (variable,value,category) VALUES ('big_blue_button_moderator_password','','plugins')";
    $r = Database::query($sql_course);
    $sql_course = "INSERT INTO $t_course (variable,value,category) VALUES ('big_blue_button_welcome_message','','plugins')";
    $r = Database::query($sql_course);
    $t_tool = Database::get_course_table(TABLE_TOOL_LIST,$row['db_name']);
    $sql_course = "INSERT INTO $t_tool VALUES (NULL, 'videoconference','../../plugin/bbb/start.php','visio.gif','".string2binary(api_get_setting('course_create_active_tools', 'videoconference'))."','0','squaregrey.gif','NO','_blank','plugin','0')";
    $r = Database::query($sql_course);
}
