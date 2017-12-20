<?php
/* For licensing terms, see /license.txt */

require_once __DIR__.'/../../../global.inc.php';

$moreButtonsInMaximizedMode = false;

if (api_get_setting('more_buttons_maximized_mode') === 'true') {
    $moreButtonsInMaximizedMode = true;
}

$template = new Template();
$template->setCSSEditor();
$template->assign('moreButtonsInMaximizedMode', $moreButtonsInMaximizedMode);
$template->assign('course_condition', api_get_cidreq());

header('Content-type: application/x-javascript');
$template->display($template->get_template('javascript/editor/ckeditor/config_js.tpl'));
