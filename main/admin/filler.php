<?php // $Id: index.php 22269 2009-07-21 15:06:15Z juliomontoya $
/* For licensing terms, see /license.txt */
/**
*	Index of the admin tools
*
*	@package chamilo.admin
*/
// name of the language file that needs to be included <br />
$language_file=array('admin','tracking');

// resetting the course id
$cidReset=true;

// including some necessary chamilo files
require_once '../inc/global.inc.php';
require_once api_get_path(LIBRARY_PATH).'security.lib.php';

// setting the section (for the tabs)
$this_section=SECTION_PLATFORM_ADMIN;

// Access restrictions
api_protect_admin_script(true);
$nameTools = get_lang('PlatformAdmin');

// setting breadcrumbs
$interbreadcrumb[] = array ("url" => 'index.php', "name" => $nameTools);

// setting the name of the tool
$nameTools = get_lang('DataFiller');

$output = array();
if (!empty($_GET['fill'])) {
	require api_get_path('SYS_TEST_PATH').'datafiller/fill_users.php';
	$output = fill_users();
}

// Displaying the header
Display::display_header($nameTools);

$result = '';
if (count($output)>0) {
    $result = '<div class="filler-report">'."\n";
    $result .= '<div class="filler-report-title">'.$output[0]['title'].'</div>'."\n";
    $result .= '<table>';
    foreach ($output as $line) {
        $result .= '<tr>';
	    $result .= '<td class="filler-report-data-init">'.$line['line-init'].'</td><td class="filler-report-data">'.$line['line-info'].'</td>';
	    $result .= '</tr>'."\n";
    }
    $result .= '</table>';
    $result .= '</div>';
    Display::display_normal_message($result);
}
?>
<div class="admin_section">
  <h4><?php Display::display_icon('bug.gif', 'DataFiller'); echo ' '.api_ucfirst(get_lang('DataFiller'));?></h4>
  <div><?php echo get_lang('ThisSectionIsOnlyVisibleOnSourceInstalls');?></div>
  <ul>
    <li><a href="filler.php?fill=users"><?php echo get_lang('FillUsers');?></a></li>
  </ul>
</div>
<?php
/* FOOTER */
Display::display_footer();