<?php
/* For licensing terms, see /license.txt */
/**
 * Get the plugin info
 * @author Imanol Losada Oriol <imanol.losada@beeznest.com>
 * @package chamilo.plugin.skype
 */
require_once __DIR__.'/config.php';

$plugin_info = Skype::create()->get_info();
