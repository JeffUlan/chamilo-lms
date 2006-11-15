<?php
// @todo Add dokeos header here
/*
 * Created on 30 mai 2006 by Elixir Interactive http://www.elixir-interactive.com
 */

// language file
$langFile = array ('courses', 'index');

// including necessary files
include_once('main/inc/global.inc.php');
include_once (api_get_path(LIBRARY_PATH).'/system_announcements.lib.php');

$tool_name = get_lang("SystemAnnouncements");
Display::display_header($tool_name);
 
if(isset($_GET['start']))
{
	$start = (int)$_GET['start'];
}
else
{
	$start = 0;
}

if (isset($_user['user_id']))
{
	$visibility = api_is_allowed_to_create_course() ? VISIBLE_TEACHER : VISIBLE_STUDENT;
	SystemAnnouncementManager :: display_all_announcements($visibility, $announcement, $start, $_user['user_id']);
}
else
{
	SystemAnnouncementManager :: display_all_announcements(VISIBLE_GUEST, $announcement, $start);
}
Display::display_footer();
?>
