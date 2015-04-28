<?php
/* For licensing terms, see /license.txt */

/**
 * Chamilo LMS
 *
 * Updates the Chamilo files from version 1.9.0 to version 1.10.0
 * This script operates only in the case of an update, and only to change the
 * active version number (and other things that might need a change) in the
 * current configuration file.
 * @package chamilo.install
 */
Log::notice('Entering file');

if (defined('SYSTEM_INSTALLATION')) {

    $conf_dir = api_get_path(CONFIGURATION_PATH);

    // Changes for 1.10.x
    // Delete directories and files that are not necessary anymore
    // pChart (1) lib, etc

    // Delete the "chat" file in all language directories, as variables have been moved to the trad4all file
    $langPath = api_get_path(SYS_CODE_PATH).'lang/';
    // Only erase files from Chamilo languages (not sublanguages defined by the users)
    $officialLanguages = array(
        'arabic',
        'asturian',
        'basque',
        'bengali',
        'bosnian',
        'brazilian',
        'bulgarian',
        'catalan',
        'croatian',
        'czech',
        'danish',
        'dari',
        'dutch',
        'english',
        'esperanto',
        'faroese',
        'finnish',
        'french',
        'friulian',
        'galician',
        'georgian',
        'german',
        'greek',
        'hebrew',
        'hindi',
        'hungarian',
        'indonesian',
        'italian',
        'japanese',
        'korean',
        'latvian',
        'lithuanian',
        'macedonian',
        'malay',
        'norwegian',
        'occitan',
        'pashto',
        'persian',
        'polish',
        'portuguese',
        'quechua_cusco',
        'romanian',
        'russian',
        'serbian',
        'simpl_chinese',
        'slovak',
        'slovenian',
        'somali',
        'spanish',
        'spanish_latin',
        'swahili',
        'swedish',
        'tagalog',
        'thai',
        'tibetan',
        'trad_chinese',
        'turkish',
        'ukrainian',
        'vietnamese',
        'xhosa',
        'yoruba',
    );
    $filesToDelete = array(
        'accessibility',
        'admin',
        'agenda',
        'announcements',
        'blog',
        'chat',
        'coursebackup',
        'course_description',
        'course_home',
        'course_info',
        'courses',
        'create_course',
        'document',
        'dropbox',
        'exercice',
        'external_module',
        'forum',
        'glossary',
        'gradebook',
        'group',
        'help',
        'import',
        'index',
        'install',
        'learnpath',
        'link',
        'md_document',
        'md_link',
        'md_mix',
        'md_scorm',
        'messages',
        'myagenda',
        'notebook',
        'notification',
        'registration',
        'reservation',
        'pedaSuggest',
        'resourcelinker',
        'scorm',
        'scormbuilder',
        'scormdocument',
        'slideshow',
        'survey',
        'tracking',
        'userInfo',
        'videoconf',
        'wiki',
        'work',
    );
    $list = scandir($langPath);
    foreach ($list as $entry) {
        if (is_dir($langPath . $entry) &&
            in_array($entry, $officialLanguages)
        ) {
            foreach ($filesToDelete as $file) {
                if (is_file($langPath . $entry . '/' . $file . '.inc.php')) {
                    unlink($langPath . $entry . '/' . $file . '.inc.php');
                }
            }
        }
    }

    // Remove the "main/conference/" directory that wasn't used since years long
    // past - see rrmdir function declared below
    @rrmdir(api_get_path(SYS_CODE_PATH).'conference');
    // Other files that we renamed
    // events.lib.inc.php has been renamed to events.lib.php
    if (is_file(api_get_path(LIBRARY_PATH).'events.lib.inc.php')) {
        @unlink(api_get_path(LIBRARY_PATH).'events.lib.inc.php');
    }

    if (is_file(api_get_path(SYS_PATH).'courses/.htaccess')) {
        unlink(api_get_path(SYS_PATH).'courses/.htaccess');
    }

    // Move files and dirs.

    $movePathList = [
        api_get_path(SYS_CODE_PATH).'upload/users/groups' => api_get_path(SYS_UPLOAD_PATH).'groups',
        api_get_path(SYS_CODE_PATH).'upload/users' => api_get_path(SYS_UPLOAD_PATH).'users',
        api_get_path(SYS_CODE_PATH).'upload/badges' => api_get_path(SYS_UPLOAD_PATH).'badges',
        api_get_path(SYS_PATH).'courses' => api_get_path(SYS_COURSE_PATH),
    ];

    foreach ($movePathList as $origin => $destination) {
        if (is_dir($origin)) {
            rename($origin, $destination);
        }
    }
} else {
    echo 'You are not allowed here !'. __FILE__;
}

/**
 * Quick function to remove a directory with its subdirectories
 * @param $dir
 */
function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir") {
                    @rrmdir($dir."/".$object);
                } else {
                    @unlink($dir."/".$object);
                }
            }
        }
        reset($objects);
        rmdir($dir);
    }
}
