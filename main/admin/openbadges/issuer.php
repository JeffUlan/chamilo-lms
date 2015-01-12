<?php
/* For licensing terms, see /license.txt */
/**
 * Show information about Mozilla OpenBadges
 * @author Angel Fernando Quiroz Campos <angel.quiroz@beeznest.com>
 * @package chamilo.admin.openbadges
 */
$cidReset = true;

require_once '../../inc/global.inc.php';

$this_section = SECTION_PLATFORM_ADMIN;

$interbreadcrumb = array(
    array(
        'url' => api_get_path(WEB_CODE_PATH) . 'admin/index.php',
        'name' => get_lang('Administration')
    ),
    array(
        'url' => api_get_path(WEB_CODE_PATH) . 'admin/openbadges/index.php',
        'name' => get_lang('OpenBadges')
    )
);

$tpl = new Template('OpenBadges');

$contentTemplate = $tpl->get_template('openbadges/issuer.tpl');

$tpl->display($contentTemplate);
