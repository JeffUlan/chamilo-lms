<?php
/* For licensing terms, see /license.txt */

/**
 * This is a function library for the learning path.
 *
 * Due to the face that the learning path has been built upon the resoucelinker,
 * naming conventions have changed at least 2 times. You can see here in order the :
 * 1. name used in the first version of the resourcelinker
 * 2. name used in the first version of the LP
 * 3. name used in the second (current) version of the LP
 *
 *       1.       2.        3.
 *   Category = Chapter = Module
 *   Item (?) = Item    = Step
 *
 * @author  Denes Nagy <darkden@evk.bke.hu>, main author
 * @author  Roan Embrechts, some code cleaning
 * @author  Yannick Warnier <yannick.warnier@beeznest.com>, multi-level learnpath behaviour + new SCORM tool
 * @access  public
 * @package chamilo.learnpath
 * @todo rename functions to coding conventions: not deleteitem but delete_item, etc
 * @todo rewrite functions to comply with phpDocumentor
 * @todo remove code duplication
 */

/**
 * This function deletes an entire directory
 * @param	string	The directory path
 * @return boolean	True on success, false on failure
 */
function deldir($dir) {
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
        if ($file != '.' && $file != '..') {
            $fullpath = $dir.'/'.$file;
            if (!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath);
            }
        }
    }

    closedir($dh);

    if (rmdir($dir)) {
        return true;
    }
    return false;
}

function rcopy($source, $dest) {
    //error_log($source." -> ".$dest, 0);
    if (!file_exists($source)) {
        //error_log($source." does not exist", 0);
        return false;
    }

    if (is_dir($source)) {
        //error_log($source." is a dir", 0);
        // This is a directory.
        // Remove trailing '/'
        if (strrpos($source, '/') == sizeof($source) - 1) {
            $source = substr($source, 0, size_of($source) - 1);
        }
        if (strrpos($dest, '/') == sizeof($dest) - 1) {
            $dest = substr($dest, 0, size_of($dest) - 1);
        }

        if (!is_dir($dest)) {
            $res = @mkdir($dest, api_get_permissions_for_new_directories());
            if ($res !== false) {
                return true;
            } else {
                // Remove latest part of path and try creating that.
                if (rcopy(substr($source, 0, strrpos($source, '/')), substr($dest, 0, strrpos($dest, '/')))) {
                    return @mkdir($dest, api_get_permissions_for_new_directories());
                } else {
                    return false;
                }
            }
        }
        return true;
    } else {
        // This is presumably a file.
        //error_log($source." is a file", 0);
        if (!@ copy($source, $dest)) {
            //error_log("Could not simple-copy $source", 0);
            $res = rcopy(dirname($source), dirname($dest));
            if ($res === true) {
                //error_log("Welcome dir created", 0);
                return @ copy($source, $dest);
            } else {
                return false;
                //error_log("Error creating path", 0);
            }
        } else {
            //error_log("Could well simple-copy $source", 0);
            return true;
        }
    }
}
