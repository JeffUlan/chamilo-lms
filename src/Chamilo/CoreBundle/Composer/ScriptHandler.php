<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Composer;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Class DumpTheme
 */
class ScriptHandler
{
    /**
     * Dump files to the web/css folder
     */
    public static function dumpCssFiles()
    {
        $appCss = __DIR__.'/../../../../app/Resources/public/css';
        $newPath = __DIR__.'/../../../../web/css';
        $fs = new Filesystem();
        $fs->mirror($appCss, $newPath);
    }

    /**
     * Delete old symfony folder before update (generates conflicts with composer)
     */
    public static function deleteOldFilesFrom19x()
    {
        $paths = [
            __DIR__.'/../../../../main/conference/',
            __DIR__.'/../../../../main/course_notice/',
            __DIR__.'/../../../../main/metadata/',
            __DIR__.'/../../../../main/reservation/',
            __DIR__.'/../../../../main/inc/lib/symfony/',
            __DIR__.'/../../../../main/inc/entity/',
            //__DIR__.'/../../../../main/inc/lib/phpdocx/',
            __DIR__.'/../../../../main/inc/lib/phpqrcode/',
            __DIR__.'/../../../../main/inc/lib/fckeditor',
            //__DIR__.'/../../../../main/inc/lib/htmlpurifier/',
            __DIR__.'/../../../../main/inc/lib/mpdf/',
            __DIR__.'/../../../../main/inc/lib/symfony/',
            __DIR__.'/../../../../main/inc/lib/system/media/renderer',
            __DIR__.'/../../../../main/inc/lib/system/io',
            __DIR__.'/../../../../main/inc/lib/system/net',
            __DIR__.'/../../../../main/inc/lib/system/text/',
            __DIR__.'/../../../../main/inc/lib/tools/',
            __DIR__.'/../../../../main/inc/lib/pchart/',
        ];

        $files = [

            __DIR__.'/../../../../main/admin/statistics/statistics.lib.php',
            __DIR__.'/../../../../main/inc/lib/main_api.lib.php',
            __DIR__.'/../../../../main/exercice/export/scorm/scorm_export.php',
            __DIR__.'/../../../../main/exercice/export/qti/qti_export.php',
            __DIR__.'/../../../../main/exercice/export/qti/qti_classes.php',
            //__DIR__.'/../../../../main/inc/lib/nusoap/class.soapclient.php',
            __DIR__.'/../../../../main/inc/lib/nusoap/nusoap.php',
            __DIR__.'/../../../../main/admin/admin_page.class.php',
            __DIR__.'/../../../../main/inc/lib/autoload.class.php',
            __DIR__.'/../../../../main/inc/autoload.inc.php',
            __DIR__.'/../../../../main/inc/lib/uri.class.php',
            __DIR__.'/../../../../main/inc/lib/db.class.php',
            __DIR__.'/../../../../main/install/i_database.class.php',
            __DIR__.'/../../../../main/inc/lib/phpmailer/test/phpmailerTest.php',
            __DIR__.'/../../../../main/inc/lib/xht.lib.php',
            __DIR__.'/../../../../main/inc/lib/xmd.lib.php',
            __DIR__.'/../../../../main/inc/lib/entity.class.php',
            __DIR__.'/../../../../main/inc/lib/entity_repository.class.php',
            __DIR__.'/../../../../main/install/install.class.php',
            __DIR__.'/../../../../main/inc/lib/javascript.class.php',
            __DIR__.'/../../../../main/inc/lib/course.class.php',
            __DIR__.'/../../../../main/inc/lib/document.class.php',
            __DIR__.'/../../../../main/inc/lib/item_property.class.php',
            __DIR__.'/../../../../main/inc/lib/chamilo.class.php',
            __DIR__.'/../../../../main/inc/lib/events.lib.inc.php',
            __DIR__.'/../../../../main/inc/lib/ezpdf/class.ezpdf.php',
            __DIR__.'/../../../../main/inc/lib/current_user.class.php',
            __DIR__.'/../../../../main/inc/lib/current_course.class.php',
            __DIR__.'/../../../../main/inc/lib/response.class.php',
            __DIR__.'/../../../../main/inc/lib/result_set.class.php',
            __DIR__.'/../../../../main/inc/lib/session_handler.class.php',
            __DIR__.'/../../../../main/exercice/testcategory.class.php',
            __DIR__.'/../../../../main/inc/lib/WCAG/WCAG_rendering.php',
            __DIR__.'/../../../../main/inc/lib/zip.class.php',
            __DIR__.'/../../../../main/inc/lib/student_publication.class.php',
            __DIR__.'/../../../../main/inc/lib/ajax_controller.class.php',
            __DIR__.'/../../../../main/inc/lib/system/closure_compiler.class.php',
            __DIR__.'/../../../../main/inc/lib/system/code_utilities.class.php',
            __DIR__.'/../../../../main/inc/lib/controller.class.php',
            __DIR__.'/../../../../main/inc/lib/system/text/converter.class.php',
            __DIR__.'/../../../../main/inc/lib/course_entity_repository.class.php',
            __DIR__.'/../../../../main/inc/lib/course_entity.class.php',
            __DIR__.'/../../../../main/inc/lib/cache.class.php',
            __DIR__.'/../../../../main/inc/lib/system/web/request_server.class.php',
            __DIR__.'/../../../../main/inc/lib/system/session.class.php',
            __DIR__.'/../../../../main/inc/lib/page.class.php',
            __DIR__.'/../../../../main/inc/lib/mail.lib.inc.php',
            __DIR__.'/../../../../main/admin/system_management.php',

        ];

        foreach ($paths as $path) {
            if (is_dir($path) && is_writable($path)) {
                self::rmdirr($path);
            }
        }

        $fs = new Filesystem();
        $fs->remove($files);
    }

    private static function rmdirr($dirname, $delete_only_content_in_folder = false, $strict = false)
    {
        $res = true;

        // A sanity check.
        if (!file_exists($dirname)) {
            return false;
        }
        // Simple delete for a file.
        if (is_file($dirname) || is_link($dirname)) {
            $res = unlink($dirname);

            return $res;
        }

        // Loop through the folder.
        $dir = dir($dirname);
        // A sanity check.
        $is_object_dir = is_object($dir);
        if ($is_object_dir) {
            while (false !== $entry = $dir->read()) {
                // Skip pointers.
                if ($entry == '.' || $entry == '..') {
                    continue;
                }

                // Recurse.
                if ($strict) {
                    $result = self::rmdirr("$dirname/$entry");
                    if ($result == false) {
                        $res = false;
                        break;
                    }
                } else {
                    self::rmdirr("$dirname/$entry");
                }
            }
        }

        // Clean up.
        if ($is_object_dir) {
            $dir->close();
        }

        if ($delete_only_content_in_folder == false) {
            $res = rmdir($dirname);
        }

        return $res;
    }
}
