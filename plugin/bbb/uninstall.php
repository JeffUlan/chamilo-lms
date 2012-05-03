<?php

/**
 * This script is included by main/admin/settings.lib.php when unselecting a plugin 
 * and is meant to remove things installed by the install.php script in both
 * the global database and the courses tables
 * @package chamilo.plugin.bigbluebutton
 */
/**
 * Queries
 */
require 'config.php';

$t_settings = Database::get_main_table(TABLE_MAIN_SETTINGS_CURRENT);
$t_options = Database::get_main_table(TABLE_MAIN_SETTINGS_OPTIONS);

$sql = "DELETE FROM $t_settings WHERE variable = 'bbb_plugin'";
Database::query($sql);
$sql = "DELETE FROM $t_options WHERE variable = 'bbb_plugin'";
Database::query($sql);
$sql = "DELETE FROM $t_settings WHERE variable = 'bbb_plugin_host'";
Database::query($sql);
$sql = "DELETE FROM $t_settings WHERE variable = 'bbb_plugin_salt'";
Database::query($sql);
$sql = "DROP TABLE IF EXISTS plugin_bbb_meeting";
Database::query($sql);
// update existing courses to add conference settings
$t_courses = Database::get_main_table(TABLE_MAIN_COURSE);
$sql = "SELECT id, code FROM $t_courses ORDER BY id";
$res = Database::query($sql);
while ($row = Database::fetch_assoc($res)) {
    $t_course = Database::get_course_table(TABLE_COURSE_SETTING);
    // $variables is loaded in the config.php file
    foreach ($variables as $variable) {
        $sql_course = "DELETE FROM $t_course WHERE c_id = " . $row['id'] . " AND variable = '$variable'";
        $r = Database::query($sql_course);
    }

    $t_tool = Database::get_course_table(TABLE_TOOL_LIST);
    $sql_course = "DELETE FROM $t_tool WHERE  c_id = " . $row['id'] . " AND link = '../../plugin/bbb/start.php'";
    $r = Database::query($sql_course);
}
