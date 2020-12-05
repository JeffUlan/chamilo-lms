<?php

/* For licensing terms, see /license.txt */

/**
 * Only updates the configuration.php file
 */
if (defined('SYSTEM_INSTALLATION')) {
    error_log('Starting '.basename(__FILE__));
    $newConfFile = api_get_path(SYMFONY_SYS_PATH).'config/configuration.php';

    // Edit the configuration file.
    $file = file($newConfFile);
    $fh = fopen($newConfFile, 'w');

    $found_version_old = false;
    $found_stable_old = false;
    $found_version = false;
    $found_stable = false;
    $found_software_name = false;
    $found_software_url = false;

    /*foreach ($file as $line) {
        $ignore = false;
        if (false !== stripos($line, '$_configuration[\'system_version\']')) {
            $found_version = true;
            $line = '$_configuration[\'system_version\'] = \''.$GLOBALS['new_version'].'\';'."\r\n";
        } elseif (false !== stripos($line, '$_configuration[\'system_stable\']')) {
            $found_stable = true;
            $line = '$_configuration[\'system_stable\'] = '.($GLOBALS['new_version_stable'] ? 'true' : 'false').';'."\r\n";
        } elseif (false !== stripos($line, '$_configuration[\'software_name\']')) {
            $found_software_name = true;
            $line = '$_configuration[\'software_name\'] = \''.$GLOBALS['software_name'].'\';'."\r\n";
        } elseif (false !== stripos($line, '$_configuration[\'software_url\']')) {
            $found_software_url = true;
            $line = '$_configuration[\'software_url\'] = \''.$GLOBALS['software_url'].'\';'."\r\n";
        } elseif (false !== stripos($line, '$userPasswordCrypted')) {
            //$line = '$_configuration[\'password_encryption\'] = \''.$userPasswordCrypted.'\';'."\r\n";
        } elseif (false !== stripos($line, '?>')) {
            $ignore = true;
        }
        if (!$ignore) {
            fwrite($fh, $line);
        }
    }*/
    /*
    if (!$found_version) {
        fwrite($fh, '$_configuration[\'system_version\'] = \''.$new_version.'\';'."\r\n");
    }
    if (!$found_stable) {
        fwrite($fh, '$_configuration[\'system_stable\'] = '.($new_version_stable ? 'true' : 'false').';'."\r\n");
    }
    if (!$found_software_name) {
        fwrite($fh, '$_configuration[\'software_name\'] = \''.$software_name.'\';'."\r\n");
    }
    if (!$found_software_url) {
        fwrite($fh, '$_configuration[\'software_url\'] = \''.$software_url.'\';'."\r\n");
    }*/
    fclose($fh);

    error_log("configuration.php file updated.");
} else {
    echo 'You are not allowed here !'.__FILE__;
}
