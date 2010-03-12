<?php //$id: $
/* For licensing terms, see /dokeos_license.txt */
/**
 * @package dokeos.glossary
 * @author Christian Fasanando, initial version
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University, Belgium, refactoring and tighter integration in Dokeos
 */

// name of the language file that needs to be included
$language_file = array('glossary');

// including the global dokeos file
require_once '../inc/global.inc.php';
require_once(api_get_path(LIBRARY_PATH).'glossary.lib.php');

// the section (tabs)
$this_section=SECTION_COURSES;

// notice for unauthorized people.
api_protect_course_script(true);

// including additional libraries
require_once (api_get_path(LIBRARY_PATH).'formvalidator/FormValidator.class.php');

// additional javascript
$htmlHeadXtra[] = GlossaryManager::javascript_glossary();
$htmlHeadXtra[] = '<script src="'.api_get_path(WEB_LIBRARY_PATH).'javascript/jquery.js" type="text/javascript" language="javascript"></script>'; //jQuery
$htmlHeadXtra[] = '<script type="text/javascript">
function setFocus(){
$("#glossary_title").focus();
}
$(document).ready(function () {
  setFocus();
});
</script>';
// setting the tool constants
$tool = TOOL_GLOSSARY;

// tracking
event_access_tool(TOOL_GLOSSARY);

// displaying the header

if (isset($_GET['action']) && ($_GET['action'] == 'addglossary' || $_GET['action'] == 'edit_glossary')) {
	$tool='GlossaryManagement';
	$interbreadcrumb[] = array ("url"=>"index.php", "name"=> get_lang('ToolGlossary'));
}

Display::display_header(get_lang(ucfirst($tool)));

// Tool introduction
Display::display_introduction_section(TOOL_GLOSSARY);


if ($_GET['action'] == 'changeview' AND in_array($_GET['view'],array('list','table'))) {
	$_SESSION['glossary_view'] = $_GET['view'];
} else {
  if (!isset($_SESSION['glossary_view'])) {
    $_SESSION['glossary_view'] = 'table';//Default option
  }
}

if (api_is_allowed_to_edit(null,true)) {
	// Adding a glossary
	if (isset($_GET['action']) && $_GET['action'] == 'addglossary') {
		// initiate the object
		$form = new FormValidator('glossary','post', api_get_self().'?action='.Security::remove_XSS($_GET['action']));
		// settting the form elements
		$form->addElement('header', '', get_lang('TermAddNew'));
		$form->addElement('text', 'glossary_title', get_lang('TermName'), array('size'=>'95', 'id'=>'glossary_title'));
		//$form->applyFilter('glossary_title', 'html_filter');
		$form->addElement('html_editor', 'glossary_comment', get_lang('TermDefinition'), null, array('ToolbarSet' => 'Glossary', 'Width' => '100%', 'Height' => '300'));
		$form->addElement('style_submit_button', 'SubmitGlossary', get_lang('TermAddButton'), 'class="save"');
		// setting the rules
		$form->addRule('glossary_title',get_lang('ThisFieldIsRequired'), 'required');
		// The validation or display
		if ($form->validate()) {
			$check = Security::check_token('post');
			if ($check) {
		   		$values = $form->exportValues();
		   		GlossaryManager::save_glossary($values);
			}
			Security::clear_token();
			GlossaryManager::display_glossary();
		} else {
			$token = Security::get_token();
			$form->addElement('hidden','sec_token');
			$form->setConstants(array('sec_token' => $token));
			$form->display();
		}
	}	else if (isset($_GET['action']) && $_GET['action'] == 'edit_glossary' && is_numeric($_GET['glossary_id']))  { // Editing a glossary
		// initiate the object
		$form = new FormValidator('glossary','post', api_get_self().'?action='.Security::remove_XSS($_GET['action']).'&glossary_id='.Security::remove_XSS($_GET['glossary_id']));
		// settting the form elements
		$form->addElement('header', '', get_lang('TermEdit'));
		$form->addElement('hidden', 'glossary_id');
		$form->addElement('text', 'glossary_title', get_lang('TermName'),array('size'=>'100'));
		//$form->applyFilter('glossary_title', 'html_filter');
		$form->addElement('html_editor', 'glossary_comment', get_lang('TermDefinition'), null, array('ToolbarSet' => 'Glossary', 'Width' => '100%', 'Height' => '300'));
		$form->addElement('style_submit_button', 'SubmitGlossary', get_lang('TermUpdateButton'), 'class="save"');

		// setting the defaults
		$defaults = GlossaryManager::get_glossary_information(Security::remove_XSS($_GET['glossary_id']));
		$form->setDefaults($defaults);

		// setting the rules
		$form->addRule('glossary_title', '<div class="required">'.get_lang('ThisFieldIsRequired'), 'required');

		// The validation or display
		if ($form->validate()) {
			$check = Security::check_token('post');
			if ($check) {
		   		$values = $form->exportValues();
		   		GlossaryManager::update_glossary($values);
			}
			Security::clear_token();
			GlossaryManager::display_glossary();
		} else {
			$token = Security::get_token();
			$form->addElement('hidden','sec_token');
			$form->setConstants(array('sec_token' => $token));
			$form->display();
		}
	} else if (isset($_GET['action']) && $_GET['action'] == 'delete_glossary' && is_numeric($_GET['glossary_id'])) 	{// deleting a glossary
		GlossaryManager::delete_glossary(Security::remove_XSS($_GET['glossary_id']));
		GlossaryManager::display_glossary();
	} else if (isset($_GET['action']) && $_GET['action'] == 'moveup' && is_numeric($_GET['glossary_id'])) {	// moving a glossary term up
		GlossaryManager::move_glossary('up',$_GET['glossary_id']);
		GlossaryManager::display_glossary();
	} else if (isset($_GET['action']) && $_GET['action'] == 'movedown' && is_numeric($_GET['glossary_id'])) {// moving a glossary term up
		GlossaryManager::move_glossary('down',$_GET['glossary_id']);
		GlossaryManager::display_glossary();
	} else {
		GlossaryManager::display_glossary();
	}
} else {
	GlossaryManager::display_glossary();
}


// footer
Display::display_footer();