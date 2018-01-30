<?php
/* For license terms, see /license.txt */
/**
 * This script is a configuration file for the date plugin. You can use it as a master for other platform plugins (course plugins are slightly different).
 * These settings will be used in the administration interface for plugins (Chamilo configuration settings->Plugins)
 * @package chamilo.plugin.test2pdf
 */
/**
 * Plugin details (must be present)
 */
require_once dirname(__FILE__) . '/config.php';
$plugin_info = Test2pdfPlugin::create()->get_info();
