<?php

/* For licensing terms, see /license.txt */

/**
 * Get the intro steps for the web page
 * @author Angel Fernando Quiroz Campos <angel.quiroz@beeznest.com>
 * @package chamilo.plugin.tour
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/main/inc/global.inc.php';
require_once api_get_path(LIBRARY_PATH) . 'plugin.class.php';
require_once api_get_path(PLUGIN_PATH) . 'tour/src/tour_plugin.class.php';

if (!api_is_anonymous()) {
    $currentPageClass = isset($_GET['page_class']) ? $_GET['page_class'] : '';

    $tourPlugin = Tour::create();

    $jsonData = file_get_contents('../config/tour.json');
    $json = json_decode($jsonData, true);

    $currentPageSteps = array();

    foreach ($json as $pageContent) {
        if ($pageContent['pageClass'] == $currentPageClass) {
            $currentPageSteps[] = array(
                'intro' => $tourPlugin->get_lang('LogoStep')
            );

            foreach ($pageContent['steps'] as $step) {
                $currentPageSteps[] = array(
                    'element' => $step['elementSelector'],
                    'intro' => $tourPlugin->get_lang($step['message'])
                );
            }

            break;
        }
    }

    if (!empty($currentPageSteps)) {
        echo json_encode($currentPageSteps);
    }
}