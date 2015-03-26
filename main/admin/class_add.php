<?php
/* For licensing terms, see /license.txt */
/**
*	@package chamilo.admin
*/
/**
 * Code
 */
// Resetting the course id.
$cidReset = true;

// Including some necessary dokeos files.
require_once '../inc/global.inc.php';

// Setting the section (for the tabs).
$this_section = SECTION_PLATFORM_ADMIN;

// Access restrictions.
api_protect_admin_script();

// Setting breadcrumbs.
$interbreadcrumb[] = array('url' => 'index.php', 'name' => get_lang('PlatformAdmin'));
$interbreadcrumb[] = array ('url' => 'class_list.php', 'name' => get_lang('Classes'));

// Setting the name of the tool.
$tool_name = get_lang("AddClasses");

$form = new FormValidator('add_class');
$form->addText('name', get_lang('ClassName'));
$form->addButtonCreate(get_lang('Ok'));
if ($form->validate()) {
    $values = $form->exportValues();
    ClassManager::create_class($values['name']);
    header('Location: class_list.php');
}

// Displaying the header.
Display :: display_header($tool_name);

// Displaying the form.
$form->display();

// Displaying the footer.
Display :: display_footer();
