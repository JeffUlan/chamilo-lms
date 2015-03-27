<?php
/* For licensing terms, see /license.txt */

/**
 * Chamilo LMS
 *
 * Update the Chamilo database from an older Chamilo version
 * Notice : This script has to be included by index.php
 *
 * @package chamilo.install
 * @todo
 * - conditional changing of tables. Currently we execute for example
 * ALTER TABLE $dbNameForm.cours
 * instructions without checking whether this is necessary.
 * - reorganise code into functions
 * @todo use database library
 */
Log::notice('Entering file');

$oldFileVersion = '1.9.0';
$newFileVersion = '1.10.0';

// Check if we come from index.php or update_courses.php - otherwise display error msg
if (defined('SYSTEM_INSTALLATION')) {

    // Check if the current Chamilo install is eligible for update
    // If not, emergency exit (back to step 1)
    if (!file_exists('../inc/conf/configuration.php')) {
        echo '<strong>'.get_lang('Error').' !</strong> '
            .'Chamilo '.implode('|', $updateFromVersion).' '.get_lang('HasNotBeenFound').'
            .<br /><br />'
            .get_lang('PleasGoBackToStep1')
            .'<p>'
            .'<button type="submit" class="back" name="step1" value="'.get_lang('Back').'">'
            .get_lang('Back')
            .'</button>'
            .'</p>'
            .'</td></tr></table></form></body></html>';
        exit ();
    }

    /*   Normal upgrade procedure: start by updating the main database */

    // If this script has been included by index.php, not update_courses.php, so
    // that we want to change the main databases as well...
    $onlyTest = false;
    if (defined('SYSTEM_INSTALLATION')) {

        /**
         * Update the databases "pre" migration
         */
        include_once '../lang/english/trad4all.inc.php';

        if ($languageForm != 'english') {
            // languageForm has been escaped in index.php
            include_once '../lang/' . $languageForm . '/trad4all.inc.php';
        }

        // Get the main queries list (m_q_list)
        $sqlFile = 'migrate-db-' . $oldFileVersion . '-' . $newFileVersion . '-pre.sql';
        $mainQueriesList = get_sql_file_contents($sqlFile, 'main');

        if (count($mainQueriesList) > 0) {
            // Now use the $mainQueriesList
            /**
             * We connect to the right DB first to make sure we can use the queries
             * without a database name
             */
            if (strlen($dbNameForm) > 40) {
                Log::error('Database name ' . $dbNameForm . ' is too long, skipping');
            } elseif (!in_array($dbNameForm, $dblist)) {
                Log::error('Database ' . $dbNameForm . ' was not found, skipping');
            } else {
                foreach ($mainQueriesList as $query) {
                    if ($onlyTest) {
                        Log::notice("iDatabase::query($dbNameForm,$query)");
                    } else {
                        $res = iDatabase::query($query);
                        if ($res === false) {
                            Log::error('Error in ' . $query . ': ' . iDatabase::error());
                        }
                    }
                }
            }
        }

        if (INSTALL_TYPE_UPDATE == 'update') {
            // Updating track tables with c_id -> moved to migrate-db

        }
    }

    // Get the courses databases queries list (c_q_list)

    $sqlFile = 'migrate-db-'.$oldFileVersion.'-'.$newFileVersion.'-pre.sql';
    $courseQueriesList = get_sql_file_contents($sqlFile, 'course');
    Log::notice('Starting migration: '.$oldFileVersion.' - '.$newFileVersion);

    if (count($courseQueriesList) > 0) {
        // Get the courses list
        if (strlen($dbNameForm) > 40) {
            Log::error('Database name '.$dbNameForm.' is too long, skipping');
        } elseif (!in_array($dbNameForm, $dblist)) {
            Log::error('Database '.$dbNameForm.' was not found, skipping');
        } else {
            $res = iDatabase::query(
                "SELECT id, code, db_name, directory, course_language, id as real_id "
                ." FROM course WHERE target_course_code IS NULL ORDER BY code"
            );

            if ($res === false) {
                die('Error while querying the courses list in update_db-1.9.0-1.10.0.inc.php');
            }
            $errors = array();

            if (iDatabase::num_rows($res) > 0) {
                $i = 0;
                $list = array();
                while ($row = iDatabase::fetch_array($res)) {
                    $list[] = $row;
                    $i++;
                }

                foreach ($list as $rowCourse) {
                    Log::notice('Course db ' . $rowCourse['db_name']);

                    // Now use the $c_q_list
                    foreach ($courseQueriesList as $query) {
                        if ($singleDbForm) {
                            $query = preg_replace('/^(UPDATE|ALTER TABLE|CREATE TABLE|DROP TABLE|INSERT INTO|DELETE FROM)\s+(\w*)(.*)$/', "$1 $prefix{$rowCourse['db_name']}_$2$3", $query);
                        }
                        if ($onlyTest) {
                            Log::notice("iDatabase::query(".$rowCourse['db_name'].",$query)");
                        } else {
                            $res = iDatabase::query($query);
                            if ($res === false) {
                                Log::error('Error in '.$query.': '.iDatabase::error());
                            }
                        }
                    }

                    Log::notice('<<<------- end  -------->>');
                }
            }
        }
    }

    // Get the main queries *POST* list (m_q_list)
    $sqlFile = 'migrate-db-' . $oldFileVersion . '-' . $newFileVersion . '-post.sql';
    $mainQueriesList = get_sql_file_contents($sqlFile, 'main');

    if (count($mainQueriesList) > 0) {
        // Now use the $mainQueriesList
        /**
         * We connect to the right DB first to make sure we can use the queries
         * without a database name
         */
        if (strlen($dbNameForm) > 40) {
            Log::error('Database name ' . $dbNameForm . ' is too long, skipping');
        } elseif (!in_array($dbNameForm, $dblist)) {
            Log::error('Database ' . $dbNameForm . ' was not found, skipping');
        } else {
            foreach ($mainQueriesList as $query) {
                if ($onlyTest) {
                    Log::notice("iDatabase::query($dbNameForm,$query)");
                } else {
                    $res = iDatabase::query($query);
                    if ($res === false) {
                        Log::error('Error in ' . $query . ': ' . iDatabase::error());
                    }
                }
            }
        }
    }

} else {
    echo 'You are not allowed here !' . __FILE__;
}
