<?php /* For licensing terms, see /license.txt */
/**
 * Cron script to list unused, but defined, language variables.
 *
 * @package chamilo.cron.lang
 */
/**
 * Includes and declarations.
 */
die();
require_once __DIR__.'/../../inc/global.inc.php';
$path = api_get_path(SYS_LANG_PATH).'english';
ini_set('memory_limit', '128M');
/**
 * Main code.
 */
$terms = [];
$list = SubLanguageManager::get_lang_folder_files_list($path);
foreach ($list as $entry) {
    $file = $path.'/'.$entry;
    if (is_file($file)) {
        $terms = array_merge($terms, SubLanguageManager::get_all_language_variable_in_file($file, true));
    }
}
// get only the array keys (the language variables defined in language files)
$defined_terms = array_flip(array_keys($terms));
$terms = null;
echo count($defined_terms)." terms were found in language files<br />";

// now get all terms found in all PHP files of Chamilo (this takes some
// time and memory)
$usedTerms = [];
$l = strlen(api_get_path(SYS_PATH));
$files = getAllPhpFiles(api_get_path(SYS_PATH));
// Browse files
foreach ($files as $file) {
    //echo 'Analyzing '.$file."<br />";
    $shortFile = substr($file, $l);
    //echo 'Analyzing '.$shortFile."<br />";
    $lines = file($file);
    // Browse lines inside file $file
    foreach ($lines as $line) {
        $myTerms = [];
        $res = preg_match_all('/get_lang\(\'(\\w*)\'\)/', $line, $myTerms);
        if ($res > 0) {
            foreach ($myTerms[1] as $term) {
                if (substr($term, 0, 4) == 'lang') {
                    $term = substr($term, 4);
                }
                $usedTerms[$term] = $shortFile;
            }
        } else {
            $res = 0;
            $res = preg_match_all('/\{[\'"](\\w*)[\'"]\|get_lang\}/', $line, $myTerms);
            if ($res > 0) {
                foreach ($myTerms[1] as $term) {
                    if (substr($term, 0, 4) == 'lang') {
                        $term = substr($term, 4);
                    }
                    $usedTerms[$term] = $shortFile;
                }
            }
        }
    }
    flush();
}

// Compare defined terms VS used terms. Used terms should be smaller than
// defined terms, and this should prove the concept that there are much
// more variables than what we really use
if (count($usedTerms) < 1) {
    die("No used terms<br />\n");
} else {
    echo "The following terms were defined but never used: <br />\n<table>";
}
$i = 1;
foreach ($defined_terms as $term => $file) {
    // remove "lang" prefix just in case
    if (substr($term, 0, 4) == 'lang') {
        $term = substr($term, 4);
    }
    if (!isset($usedTerms[$term])) {
        echo "<tr><td>$i</td><td>$term</td></tr>\n";
        $i++;
    }
}
echo "</table>\n";
