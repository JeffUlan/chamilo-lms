<?php
/* For licensing terms, see /license.txt */

/**
 * @package chamilo.learnpath
 * @author Yannick Warnier <ywarnier@beeznest.org>
 */

/**
 * Start a timer and hand it back to the JS by assigning the current time (of start) to
 * var asset_timer
 * @return string JavaScript time intializer
 */
$app['template.show_footer'] = false;
$app['template.show_header'] = false;
$app['default_layout'] = 'default/layout/blank.tpl';

function start_timer() {
    $time = time();
    return $time; //"olms.asset_timer='$time'; olms.asset_timer_total = 0;";
}
echo start_timer();
exit;
