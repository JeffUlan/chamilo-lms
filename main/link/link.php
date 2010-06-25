<?php
/* For licensing terms, see /license.txt */

/**
 * Main script for the links tool.
 *
 * Features:
 * - Organize links into categories;
 * - favorites/bookmarks-like interface;
 * - move links up/down within a category;
 * - move categories up/down;
 * - expand/collapse all categories (except the main "non"-category);
 * - add link to 'root' category => category-less link is always visible.
 *
 * @author Patrick Cool, main author, completely rewritten
 * @author René Haentjens, added CSV file import (October 2004)
 * @package chamilo.link
 * @todo improve organisation, tables should come from database library
 * @todo Needs serious rewriting here. This doesn't make sense
 */

/*	INIT SECTION */

// Language files that need to be included
$language_file = array('link', 'admin');

// Including libraries
require_once '../inc/global.inc.php';
require_once 'linkfunctions.php';

$this_section = SECTION_COURSES;
api_protect_course_script();

$htmlHeadXtra[] = '<script src="'.api_get_path(WEB_LIBRARY_PATH).'javascript/jquery.js" type="text/javascript" language="javascript"></script>'; //jQuery
$htmlHeadXtra[] = '<script type="text/javascript">
$(document).ready( function() {
	for (i=0;i<$(".actions").length;i++) {
		if ($(".actions:eq("+i+")").html()=="<table border=\"0\"></table>" || $(".actions:eq("+i+")").html()=="" || $(".actions:eq("+i+")").html()==null) {
			$(".actions:eq("+i+")").hide();
		}
	}

	$("#div_target").attr("style","display:none;")//hide

	$("#id_check_target").click(function () {
      if($(this).attr("checked")==true) {
      	$("#div_target").attr("style","display:block;")//show
      } else {
     	 $("#div_target").attr("style","display:none;")//hide
      }
    });

    $(window).load(function () {
      if($("#id_check_target").attr("checked")==true) {
      	$("#div_target").attr("style","display:block;")//show
      } else {
     	 $("#div_target").attr("style","display:none;")//hide
      }
    });


 } );

 </script>';

// @todo change the $_REQUEST into $_POST or $_GET
// @todo remove this code
$link_submitted = isset($_POST['submitLink']);
$category_submitted = isset($_POST['submitCategory']);
$urlview = !empty($_GET['urlview']) ? $_GET['urlview'] : '';
$submit_import = !empty($_POST['submitImport']) ? $_POST['submitImport'] : '';
$down = !empty($_GET['down']) ? $_GET['down'] : '';
$up = !empty($_GET['up']) ? $_GET['up'] : '';
$catmove = !empty($_GET['catmove']) ? $_GET['catmove'] : '';
$editlink = !empty($_REQUEST['editlink']) ? $_REQUEST['editlink'] : '';
$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : '';
$urllink = !empty($_REQUEST['urllink']) ? $_REQUEST['urllink'] : '';
$title = !empty($_REQUEST['title']) ? $_REQUEST['title'] : '';
$description = !empty($_REQUEST['description']) ? $_REQUEST['description'] : '';
$selectcategory = !empty($_REQUEST['selectcategory']) ? $_REQUEST['selectcategory'] : '';
$submit_link = isset($_REQUEST['submitLink']);
$action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';
$category_title = !empty($_REQUEST['category_title']) ? $_REQUEST['category_title'] : '';
$submit_category = isset($_POST['submitCategory']);
$target_link = !empty($_REQUEST['target_link']) ? $_REQUEST['target_link'] : '_self';

$nameTools = get_lang('Links');

// Condition for the session
$session_id = api_get_session_id();
$condition_session = api_get_session_condition($session_id, false);

if (isset($_GET['action']) && $_GET['action'] == 'addlink') {
	$nameTools = '';
	$interbreadcrumb[] = array('url' => 'link.php', 'name' => get_lang('Links'));
	$interbreadcrumb[] = array('url' => 'link.php?action=addlink', 'name' => get_lang('AddLink'));
}

if (isset($_GET['action']) && $_GET['action'] == 'addcategory') {
	$nameTools = '';
	$interbreadcrumb[] = array('url' => 'link.php', 'name' => get_lang('Links'));
	$interbreadcrumb[] = array('url' => 'link.php?action=addcategory', 'name' => get_lang('AddCategory'));
}

if (isset($_GET['action']) && $_GET['action'] == 'editlink') {
	$nameTools = '';
	$interbreadcrumb[] = array('url' => 'link.php', 'name' => get_lang('Links'));
	$interbreadcrumb[] = array('url' => '#', 'name' => get_lang('EditLink'));
}

// Database Table definitions
$tbl_link = Database::get_course_table(TABLE_LINK);
$tbl_categories = Database::get_course_table(TABLE_LINK_CATEGORY);

// Statistics
event_access_tool(TOOL_LINK);

Display::display_header($nameTools, 'Links');

?>
<script type="text/javascript">
/* <![CDATA[ */
function MM_popupMsg(msg) { //v1.0
  confirm(msg);
}
/* ]]> */
</script>

<?php

/*	Action Handling */

$nameTools = get_lang('Links');

if (isset($_GET['action'])) {
	switch ($_GET['action']) {
		case 'addlink':
			if ($link_submitted) {
				if(!addlinkcategory("link")) {	// Here we add a link
					unset($submit_link);
				}
			}
			break;
		case 'addcategory':
			if ($category_submitted) {
				if (!addlinkcategory('category')) {	// Here we add a category
					unset($submit_category);
				}
			}
			break;
		case 'importcsv':
			if ($_POST['submitImport']) {
				import_csvfile();
			}
			break;
		case 'deletelink':
			deletelinkcategory('link'); // Here we delete a link
			break;

		case 'deletecategory':
			deletelinkcategory('category'); // Here we delete a category
			break;
		case 'editlink':
			editlinkcategory('link'); // Here we edit a link
			break;
		case 'editcategory':
			editlinkcategory('category'); // Here we edit a category
			break;
		case 'visible':
			change_visibility($_GET['id'], $_GET['scope']); // Here we edit a category
			break;
		case 'invisible':
			change_visibility($_GET['id'], $_GET['scope']); // Here we edit a category
			break;
	}
}

/*	Introduction section */

Display::display_introduction_section(TOOL_LINK);

if (api_is_allowed_to_edit(null, true) && isset($_GET['action'])) {
	echo '<div class="actions">';
	//echo '<a href="link.php?cidReq='.Security::remove_XSS($_GET['cidReq']).'&amp;urlview='.Security::remove_XSS($_GET['urlview']).'">'.Display::return_icon('back.png', get_lang('BackToLinksOverview')).get_lang('BackToLinksOverview').'</a>';
	echo '</div>';
	if (api_get_setting('search_enabled') == 'true') {
		if (!extension_loaded('xapian')) {
			Display::display_error_message(get_lang('SearchXapianModuleNotInstaled'));
		}
	}
	// Displaying the correct title and the form for adding a category or link. This is only shown when nothing
	// has been submitted yet, hence !isset($submit_link)
	if (($_GET['action'] == 'addlink' || $_GET['action'] == 'editlink') && empty($_POST['submitLink'])) {
		echo '<div class="row">';
		if ($_GET['action'] == 'addlink') {
			echo '<div class="form_header">'.get_lang('LinkAdd').'</div>';
		} else {
			echo '<div class="form_header">'.get_lang('LinkMod').'</div>';
		}
		echo '</div>';
		if ($category == '') {
			$category = 0;
		}
		echo '<form method="post" action="'.api_get_self().'?action='.Security::remove_XSS($_GET['action']).'&amp;urlview='.Security::remove_XSS($urlview).'">';
		if ($_GET['action'] == 'editlink') {
			echo '<input type="hidden" name="id" value="'.Security::remove_XSS($_GET['id']).'" />';
			$clean_link_id = trim(Security::remove_XSS($_GET['id']));
		}

		echo '	<div class="row">
					<div class="label">
						<span class="form_required">*</span> '.get_lang('Url').'
					</div>
					<div class="formw">
						<input type="text" name="urllink" size="50" value="' . (empty($urllink) ? 'http://' : Security::remove_XSS($urllink)) . '" />
					</div>
				</div>';

		echo '	<div class="row">
					<div class="label">
						'.get_lang('LinkName').'
					</div>
					<div class="formw">
						<input type="text" name="title" size="50" value="' . Security::remove_XSS($title) . '" />
					</div>
				</div>';
		echo '	<div class="row">
					<div class="label">
						'.get_lang('Metadata').'
					</div>
					<div class="formw">
						<a href="../metadata/index.php?eid='.urlencode('Link.'.$clean_link_id).'">'.get_lang('AddMetadata').'</a>
					</div>
				</div>';
		echo '	<div class="row">
					<div class="label">
						'.get_lang('Description').'
					</div>
					<div class="formw">
						<textarea rows="3" cols="50" name="description">' .	Security::remove_XSS($description) . '</textarea>
					</div>
				</div>';

		$sqlcategories = "SELECT * FROM ".$tbl_categories." $condition_session ORDER BY display_order DESC";
		$resultcategories = Database::query($sqlcategories);

		if (Database::num_rows($resultcategories)) {
			echo '	<div class="row">
						<div class="label">
							'.get_lang('Category').'
						</div>
						<div class="formw">';
			echo '			<select name="selectcategory">';
			echo '			<option value="0">--</option>';
			while ($myrow = Database::fetch_array($resultcategories)) {
				echo '		<option value="'.$myrow['id'].'"';
				if ($myrow['id'] == $category) {
					echo ' selected';
				}
				echo '>'.$myrow['category_title'].'</option>';
			}
			echo '			</select>';
			echo '		</div>
					</div>';
		}

		echo '	<div class="row">
					<div class="label">
						'.get_lang('OnHomepage').'?
					</div>
					<div class="formw">
						<input id="id_check_target" class="checkbox" type="checkbox" name="onhomepage" id="onhomepage" value="1"'.$onhomepage.'><label for="onhomepage"> '.get_lang('Yes').'</label>
					</div>
				</div>';
		echo '	<div class="row" id="div_target">
					<div class="label">
						'.get_lang('AddTargetOfLinkOnHomepage').'
					</div>
					<div class="formw" >
						<select  name="target_link" id="target_link">';
        $targets = array('_self'=>get_lang('LinkOpenSelf'),'_blank'=>get_lang('LinkOpenBlank'),'_parent'=>get_lang('LinkOpenParent'),'_top'=>get_lang('LinkOpenTop'));
		foreach ($targets as $target_id => $target) {
			$selected = '';
			if ($target_id == $target_link) { 
				$selected = ' selected="selected"';
			}
			echo '    	<option value="'.$target_id.'"'.$selected.'>'.$target.'</option> ';
		}
		echo '        </select>
					</div>
				</div>';

		if (api_get_setting('search_enabled') == 'true') {
			require_once api_get_path(LIBRARY_PATH).'specific_fields_manager.lib.php';
			$specific_fields = get_specific_field_list();

			echo '	<div class="row">
						<div class="label">
							'.get_lang('SearchFeatureDoIndexLink').'?
						</div>
						<div class="formw">
							<input class="checkbox" type="checkbox" name="index_document" id="index_document" checked="checked"><label for="index_document"> '.get_lang('Yes').'</label>
						</div>';

			foreach ($specific_fields as $specific_field) {
				// Author : <input name="A" type="text" />

				$default_values = '';
                if ($_GET['action'] == 'editlink') {
					$filter = array('course_code'=> "'". api_get_course_id() ."'", 'field_id' => $specific_field['id'], 'ref_id' => Security::remove_XSS($_GET['id']), 'tool_id' => '\''. TOOL_LINK .'\'');
					$values = get_specific_field_values_list($filter, array('value'));
					if (!empty($values)) {
						$arr_str_values = array();
						foreach ($values as $value) {
							$arr_str_values[] = $value['value'];
						}
						$default_values = implode(', ', $arr_str_values);
					}
				}

				$sf_textbox = '
						<div class="row">
							<div class="label">%s</div>
							<div class="formw">
								<input name="%s" type="text" value="%s"/>
							</div>
						</div>';

				echo sprintf($sf_textbox, $specific_field['name'], $specific_field['code'], $default_values);
			}
		}

		echo '	<div class="row">
					<div class="label">
					</div>
					<div class="formw">
						<button class="save" type="Submit" name="submitLink" value="OK">'.get_lang('SaveLink').'</button>
					</div>
				</div>';

		echo '</form>';
	} elseif(($_GET['action'] == 'addcategory' || $_GET['action'] == 'editcategory') && !$submit_category) {
		echo '<div class="row">';
		if ($_GET['action'] == 'addcategory') {
			echo '<div class="form_header">'.get_lang('CategoryAdd').'</div>';
			$my_cat_title = get_lang('CategoryAdd');
		} else {
			echo '<div class="form_header">'.get_lang('CategoryMod').'</div>';
			$my_cat_title = get_lang('CategoryMod');
		}
		echo "</div>\n\n";
		echo '<form method="post" action="'.api_get_self().'?action='.Security::remove_XSS($_GET['action']).'&amp;urlview='.Security::remove_XSS($urlview).'">';
		if ($_GET['action'] == 'editcategory') {
			echo '<input type="hidden" name="id" value="'.$id.'" />';
		}

		echo '	<div class="row">
					<div class="label">
						<span class="form_required">*</span> '.get_lang('CategoryName').'
					</div>
					<div class="formw">
						<input type="text" name="category_title" size="50" value="'.Security::remove_XSS($category_title).'" />
					</div>
				</div>';

		echo '	<div class="row">
					<div class="label">
						'.get_lang('Description').'
					</div>
					<div class="formw">
						<textarea rows="3" cols="50" name="description">'.Security::remove_XSS($description).'</textarea>
					</div>
				</div>';

		echo '	<div class="row">
					<div class="label">
					</div>
					<div class="formw">
						<button class="save" type="submit" name="submitCategory">'.$my_cat_title.' </button>
					</div>
				</div>';

		echo "</form>";
	}
	/*elseif(($_GET['action'] == 'importcsv') and !$submit_import) {
		echo "<h4>", get_lang('CsvImport'), "</h4>\n\n",
		     "<form method=\"post\" action=\"".api_get_self()."?action=".$_GET['action']."&amp;urlview=".$urlview."\" enctype=\"multipart/form-data\">",
                // uncomment if you want to set a limit: '<input type="hidden" name="MAX_FILE_SIZE" value="32768">', "\n",
                '<input type="file" name="import_file" size="30">', "\n",
		     	"<input type=\"Submit\" name=\"submitImport\" value=\"".get_lang('Ok')."\">",
		     "</form>";
		echo get_lang('CsvExplain');
	}*/
}

if (!empty($down)) {
	movecatlink($down);
}
if (!empty($up)) {
	movecatlink($up);
}

if (empty($_GET['action']) || ($_GET['action'] != 'editlink' && $_GET['action'] != 'addcategory' && $_GET['action'] != 'addlink') || $link_submitted || $category_submitted) {

	/*	Action Links */

	if ((isset($_GET['action']) &&  $_GET['action'] == 'editcategory' && isset($_GET['id'])) || (isset($_GET['action']) && $_GET['action'] == 'addcategory')) {
			echo '<br /><br /><br />';
	}
	echo '<div class="actions">';
	if (api_is_allowed_to_edit(null, true)) {
		$urlview = Security::remove_XSS($urlview);
		echo Display::return_icon('linksnew.gif', get_lang('LinkAdd')).' <a href="'.api_get_self().'?'.api_get_cidreq().'&amp;action=addlink&amp;category='.(!empty($category) ? $category : '').'&amp;urlview='.$urlview.'">'.get_lang('LinkAdd')."</a>\n";
		echo Display::return_icon('folder_new.gif', get_lang('CategoryAdd')).' <a href="'.api_get_self().'?'.api_get_cidreq().'&amp;action=addcategory&amp;urlview='.$urlview.'">'.get_lang('CategoryAdd')."</a>\n";
		   /* "<a href=\"".api_get_self()."?".api_get_cidreq()."&action=importcsv&amp;urlview=".$urlview."\">".get_lang('CsvImport')."</a>\n", // RH*/
	}
	// Making the show none / show all links. Show none means urlview=0000 (number of zeros depending on the
	// number of categories). Show all means urlview=1111 (number of 1 depending on teh number of categories).
	$sqlcategories = "SELECT * FROM ".$tbl_categories." $condition_session ORDER BY display_order DESC";
	$resultcategories = Database::query($sqlcategories);
	$aantalcategories = Database::num_rows($resultcategories);
	if ($aantalcategories > 0) {
		echo Display::return_icon('remove.gif', $shownone).' <a href="'.api_get_self().'?'.api_get_cidreq().'&urlview=';
		for ($j = 1; $j <= $aantalcategories; $j++) {
			echo '0';
		}
		echo '">'.get_lang('shownone').'</a>';
		echo Display::return_icon('add.gif', $showall).' <a href="'.api_get_self().'?'.api_get_cidreq().'&urlview=';
		for ($j = 1; $j <= $aantalcategories; $j++) {
			echo '1';
		}
		echo '">'.get_lang('showall').'</a>';
	}
	echo '</div>';

	// Starting the table which contains the categories
	$sqlcategories = "SELECT * FROM ".$tbl_categories." $condition_session ORDER BY display_order DESC";
	$resultcategories = Database::query($sqlcategories);

	echo '<table class="data_table">';
	// Displaying the links which have no category (thus category = 0 or NULL), if none present this will not be displayed
	$sqlLinks = "SELECT * FROM ".$tbl_link." WHERE category_id=0 OR category_id IS NULL";
	$result = Database::query($sqlLinks);
	$numberofzerocategory = Database::num_rows($result);
	if ($numberofzerocategory !== 0) {
		echo '<tr><th style="font-weight: bold; text-align:left;padding-left: 10px;"><i>'.get_lang('General').'</i></th></tr>';
		echo '</table>';
		showlinksofcategory(0);
	}

	$i = 0;
	$catcounter = 1;
	$view = '0';

	while ($myrow = Database::fetch_array($resultcategories)) {
		// Validacion when belongs to a session
		$session_img = api_get_session_image($myrow['session_id'], $_user['status']);

		//if (!isset($urlview)) {
		if ($urlview == '') {
			// No $view set in the url, thus for each category link it should be all zeros except it's own
			makedefaultviewcode($i);
		} else {
			$view = $urlview;
			$view[$i] = '1';
		}
		// If the $urlview has a 1 for this categorie, this means it is expanded and should be desplayed as a
		// - instead of a +, the category is no longer clickable and all the links of this category are displayed
		$myrow['description'] = text_filter($myrow['description']);

		if ($urlview[$i] == '1') {
			$newurlview = $urlview;
			$newurlview[$i] = '0';

			echo '<tr>';
				echo '<table class="data_table">';
				echo '<tr>';
					echo '<th width="81%" style="font-weight: bold; text-align:left;padding-left: 5px;">';
					echo '<a href="'.api_get_self().'?'.api_get_cidreq().'&amp;urlview='.Security::remove_XSS($newurlview).'">';
					echo '<img src="../img/remove.gif" />&nbsp;&nbsp;'.Security::remove_XSS($myrow['category_title']).'</a><br />&nbsp;&nbsp;&nbsp;'.$myrow['description'];

					if (api_is_allowed_to_edit(null, true)) {
						echo '<th>';
						showcategoryadmintools($myrow['id']);
						echo '</th>';
					}
					echo '</th>';
				echo '</tr>';
				echo '</table>';
				echo showlinksofcategory($myrow['id']);
			echo '</tr>';
		} else {
			echo '<tr>';
				echo '<table class="data_table">';
				echo '<tr>';
					echo '<th width="81%" style="font-weight: bold; text-align:left;padding-left: 5px;"><a href="'.api_get_self().'?'.api_get_cidreq().'&amp;urlview=';
					echo is_array($view) ? implode('', $view) : $view;
					echo '"><img src="../img/add.gif" />&nbsp;&nbsp;'.Security::remove_XSS($myrow['category_title']).$session_img;
					echo'</a><br />&nbsp;&nbsp;&nbsp;';
					echo $myrow['description'];
					if (api_is_allowed_to_edit(null, true)) {
						echo '<th style="text-align:center;">';
						showcategoryadmintools($myrow['id']);
						echo '</th>';
					}
					echo '</th>';
				echo '</tr>';

				echo '</table>';
			echo '</tr>';
		}
		// Displaying the link of the category
		$i++;
	}
	echo '</table>';
}

Display::display_footer();
