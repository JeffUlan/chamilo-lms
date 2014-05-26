<?php
/**
 * Initialization
 */
require_once dirname(__FILE__) . '/buy_course.lib.php';
require_once '../../../main/inc/global.inc.php';
require_once 'lib/buy_course_plugin.class.php';

$_cid = 0;
$interbreadcrumb[] = array("url" => "list.php", "name" => 'Listado de cursos a la venta');
$interbreadcrumb[] = array("url" => "paymentsetup.php", "name" => get_lang('Configuraci&oacute;n pagos'));


$tpl = new Template('Configuraci&oacute;n de cursos disponibles');

$teacher = api_is_platform_admin();
api_protect_course_script(true);

if ($teacher) {
    $pendingList = pendingList();
    $ruta = api_get_path(WEB_PLUGIN_PATH) . 'buy_courses/resources/message_confirmation.png';
    $ruta2 = api_get_path(WEB_PLUGIN_PATH) . 'buy_courses/resources/borrar.png';
    $currencyType = findCurrency();


    $tpl->assign('server', $_configuration['root_web']);
    $tpl->assign('pendientes', $pendingList);
    $tpl->assign('confirmation_img', $ruta);
    $tpl->assign('ruta_imagen_borrar', $ruta2);
    $tpl->assign('currency', $currencyType);

    $listing_tpl = 'buy_courses/view/pending_orders.tpl';
    $content = $tpl->fetch($listing_tpl);
    $tpl->assign('content', $content);
    $tpl->display_one_col_template();
}
