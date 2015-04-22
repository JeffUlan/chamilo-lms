<?php
/* For licensing terms, see /license.txt */

/**
 * This script contains a data filling procedure for users
 * @author Yannick Warnier <yannick.warnier@beeznest.com>
 *
 */

/**
 * Loads the data and injects it into the Chamilo database, using the Chamilo
 * internal functions.
 * @return  array  List of user IDs for the users that have just been inserted
 */
function fill_courses()
{
    $eol = PHP_EOL;
    $courses = array(); //declare only to avoid parsing notice
    require_once 'data_courses.php'; //fill the $users array
    $output = array();
    $output[] = array('title'=>'Courses Filling Report: ');
    $i = 1;
    foreach ($courses as $i => $course) {
        //first check that the first item doesn't exist already
    	$output[$i]['line-init'] = $course['title'];
        $res = CourseManager::create_course($course);
    	$output[$i]['line-info'] = $res ? get_lang('Added') : get_lang('NotInserted');
    	$i++;
    }

    return $output;
}
