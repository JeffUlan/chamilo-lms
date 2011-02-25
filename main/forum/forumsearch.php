<?php
/* For licensing terms, see /license.txt */

/**
 * These files are a complete rework of the forum. The database structure is
 * based on phpBB but all the code is rewritten. A lot of new functionalities
 * are added:
 * - forum categories and forums can be sorted up or down, locked or made invisible
 * - consistent and integrated forum administration
 * - forum options:     are students allowed to edit their post?
 *                       moderation of posts (approval)
 *                       reply only forums (students cannot create new threads)
 *                       multiple forums per group
 * - sticky messages
 * - new view option: nested view
 * - quoting a message
 *
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @copyright Ghent University
 *
 *  @package chamilo.forum
 */

// name of the language file that needs to be included
$language_file = array ('forum', 'group');

// including the global dokeos file
require_once '../inc/global.inc.php';

// the section (tabs)
$this_section = SECTION_COURSES;

// notice for unauthorized people.
api_protect_course_script(true);

// including additional library scripts
require_once api_get_path(LIBRARY_PATH).'formvalidator/FormValidator.class.php';
include_once api_get_path(LIBRARY_PATH).'groupmanager.lib.php';
include 'forumfunction.inc.php';
include 'forumconfig.inc.php';

//are we in a lp ?
$origin = '';
if (isset($_GET['origin'])) {
    $origin =  Security::remove_XSS($_GET['origin']);
}

// name of the tool
$nameTools = get_lang('ToolForum');

// breadcrumbs

if (isset($_SESSION['gradebook'])){
    $gradebook = $_SESSION['gradebook'];
}

if (!empty($gradebook) && $gradebook == 'view') {
    $interbreadcrumb[] = array (
            'url' => '../gradebook/'.$_SESSION['gradebook_dest'],
            'name' => get_lang('ToolGradebook')
        );
}

if (!empty ($_GET['gidReq'])) {
    $toolgroup = Database::escape_string($_GET['gidReq']);
    api_session_register('toolgroup');
}

if ($origin=='group') {
    $_clean['toolgroup']=(int)$_SESSION['toolgroup'];
    $group_properties  = GroupManager :: get_group_properties($_clean['toolgroup']);
    $interbreadcrumb[] = array ("url" => "../group/group.php", "name" => get_lang('Groups'));
    $interbreadcrumb[] = array ("url" => "../group/group_space.php?gidReq=".$_SESSION['toolgroup'], "name"=> get_lang('GroupSpace').' ('.$group_properties['name'].')');
    $interbreadcrumb[] = array ("url" => "viewforum.php?origin=".$origin."&amp;gidReq=".$_SESSION['toolgroup']."&amp;forum=".Security::remove_XSS($_GET['forum']),"name" => prepare4display($current_forum['forum_title']));
    $interbreadcrumb[]=array('url' => 'forumsearch.php','name' => get_lang('ForumSearch'));
} else {
    $interbreadcrumb[]=array('url' => 'index.php?gradebook='.$gradebook.'','name' => $nameTools);
    $interbreadcrumb[]=array('url' => 'forumsearch.php','name' => get_lang('ForumSearch'));
}

// Display the header
if ($origin == 'learnpath') {
    include(api_get_path(INCLUDE_PATH).'reduced_header.inc.php');
} else {
    Display :: display_header($nameTools);
}

// Display the tool title
// api_display_tool_title($nameTools);

// Tool introduction
Display::display_introduction_section(TOOL_FORUM);

// tracking
event_access_tool(TOOL_FORUM);

// forum search
forum_search();

// footer
if ($origin != 'learnpath') {
    Display :: display_footer();
}