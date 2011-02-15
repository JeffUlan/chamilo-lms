<?php
/* For licensing terms, see /license.txt */
/**
*	This is the array library for Chamilo.
*	Include/require it in your code to use its functionality.
*
*	@package chamilo.library
*/


/**
 * Removes duplicate values from a dimensional array
 *
 * @param array a dimensional array
 * @return array an array with unique values
 * 
 */
function array_unique_dimensional($array) {
    if(!is_array($array))
		return $array;

    foreach ($array as &$myvalue) {
        $myvalue=serialize($myvalue);
    }

    $array=array_unique($array);

    foreach ($array as &$myvalue) {
        $myvalue=unserialize($myvalue);
    }
    return $array;
}

/**
 * 
 * Sort multidimensional arrays
 * 
 * @param 	array 	unsorted multidimensional array
 * @param 	string	key to be sorted
 * @return 	array	result array
 * @author	found in http://php.net/manual/en/function.sort.php
 */
function msort($array, $id='id') {
    if (empty($array)) {
        return $array;
    }
    $temp_array = array();
    while (count($array)>0) {
        $lowest_id = 0;
        $index=0;
        foreach ($array as $item) {
            if ($item[$id]<$array[$lowest_id][$id]) {
                $lowest_id = $index;
            }
            $index++;
        }
        $temp_array[] = $array[$lowest_id];
        $array = array_merge(array_slice($array, 0, $lowest_id), array_slice($array, $lowest_id+1));
    }
    return $temp_array;
}