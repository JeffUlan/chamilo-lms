<?php
/* For license terms, see /license.txt */
/**
 * Functions
 * @package chamilo.plugin.buycourses
 */
/**
 * Init
 */
require_once '../../../main/inc/global.inc.php';
require_once '../config.php';
require_once api_get_path(LIBRARY_PATH) . 'plugin.class.php';

/**
 * Checks if a session or a course is already bought
 * @param string Session id or course code
 * @param int User id
 * @param string What has to be checked
 * @todo fix this function because TABLE_MAIN_COURSE_USER needs a c_id not a course_code
 * @return boolean True if it is already bought, and false otherwise
 */
function checkUserBuy($parameter, $user, $type = 'COURSE')
{
    $sql = "SELECT 1 FROM %s WHERE %s ='" . Database::escape_string($parameter) . "' AND %s ='" . intval($user) . "';";
    $sql = $type === 'SESSION' ?
        sprintf($sql, Database::get_main_table(TABLE_MAIN_SESSION_USER), 'session_id', 'user_id') :
        sprintf($sql, Database::get_main_table(TABLE_MAIN_COURSE_USER), 'c_id', 'user_id');
    $result = Database::query($sql);
    if (Database::affected_rows($result) > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if a session or a course has already a transfer
 * @param string Session id or course code
 * @param int User id
 * @param string What has to be checked
 * @return boolean True if it has already a transfer, and false otherwise
 */
function checkUserBuyTransfer($parameter, $user, $type = 'COURSE')
{
    $sql = "SELECT 1 FROM %s WHERE %s ='" . Database::escape_string($parameter) . "' AND user_id ='" . intval($user) . "';";
    $sql = $type === 'SESSION' ?
        sprintf($sql, Database::get_main_table(TABLE_BUY_SESSION_TEMPORARY), 'session_id') :
        sprintf($sql, Database::get_main_table(TABLE_BUY_COURSE_TEMPORAL), 'course_code');
    $result = Database::query($sql);
    if (Database::affected_rows($result) > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * Returns an array with all the categories
 * @return array All the categories
 */
function buyCourseListCategories()
{
    $tblCourseCategory = Database::get_main_table(TABLE_MAIN_CATEGORY);
    $sql = "SELECT code, name FROM $tblCourseCategory";
    $res = Database::query($sql);
    $aux = array();
    while ($row = Database::fetch_assoc($res)) {
        $aux[] = $row;
    }

    return $aux;
}

/**
 * Return an icon representing the visibility of the course
 * @param int $option The course visibility
 * @return string HTML string of the visibility icon
 */
function getCourseVisibilityIcon($option)
{
    $style = 'margin-bottom:-5px;margin-right:5px;';
    switch ($option) {
        case 0:
            return Display::return_icon('bullet_red.gif', get_plugin_lang('CourseVisibilityClosed', 'BuyCoursesPlugin'), array('style' => $style));
            break;
        case 1:
            return Display::return_icon('bullet_orange.gif', get_plugin_lang('Private', 'BuyCoursesPlugin'), array('style' => $style));
            break;
        case 2:
            return Display::return_icon('bullet_green.gif', get_plugin_lang('OpenToThePlatform', 'BuyCoursesPlugin'), array('style' => $style));
            break;
        case 3:
            return Display::return_icon('bullet_blue.gif', get_plugin_lang('OpenToTheWorld', 'BuyCoursesPlugin'), array('style' => $style));
            break;
        default:
            return Display::return_icon('bullet_grey.gif', get_plugin_lang('CourseVisibilityHidden', 'BuyCoursesPlugin'), array('style' => $style));
    }
}
/**
 * Gets the list of accounts from the buy_course_transfer table
 * @return array The list of accounts
 */
function listAccounts()
{
    $tableBuyCourseTransfer = Database::get_main_table(TABLE_BUY_COURSE_TRANSFER);
    $sql = "SELECT * FROM $tableBuyCourseTransfer";
    $res = Database::query($sql);
    $aux = array();
    while ($row = Database::fetch_assoc($res)) {
        $aux[] = $row;
    }

    return $aux;
}
/**
 * Find the first enabled currency (there should be only one)
 * @result string The code of the active currency
 */
function findCurrency()
{
    $tableBuyCourseCountry = Database::get_main_table(TABLE_BUY_COURSE_COUNTRY);
    $sql = "SELECT * FROM $tableBuyCourseCountry WHERE status='1';";
    $res = Database::query($sql);
    $row = Database::fetch_assoc($res);

    return $row['currency_code'];
}
/**
 * Extended information about the session (from the session table as well as
 * the buy_session table)
 * @param string $code The session code
 * @return array Info about the session
 */
function sessionInfo($code)
{
    $tableBuySession = Database::get_main_table(TABLE_BUY_SESSION);
    $tableSession = Database::get_main_table(TABLE_MAIN_SESSION);
    $tableBuySessionRelCourse = Database::get_main_table(TABLE_BUY_SESSION_COURSE);
    $tableSessionRelCourse = Database::get_main_table(TABLE_MAIN_SESSION_COURSE);
    $tableBuyCourse = Database::get_main_table(TABLE_BUY_COURSE);
    $tableCourse = Database::get_main_table(TABLE_MAIN_COURSE);
    $tableSessionRelUser = Database::get_main_table(TABLE_MAIN_SESSION_USER);
    $tableBuySessionTemporal = Database::get_main_table(TABLE_BUY_SESSION_TEMPORARY);
    $currentUserId = api_get_user_id();

    $code = Database::escape_string($code);
    $sql = "SELECT a.session_id, a.visible, a.price, b.*
        FROM $tableBuySession a, $tableSession b
        WHERE a.session_id=b.id
        AND a.visible = 1
        AND b.id = '".$code."';";
    $res = Database::query($sql);
    $rowSession = Database::fetch_assoc($res);
    $sqlSessionCourse = "SELECT DISTINCT a.session_id, a.course_code, a.nbr_users
    FROM $tableBuySessionRelCourse a, $tableSessionRelCourse b
    WHERE a.session_id = b.session_id AND a.session_id = " . $rowSession['session_id'] . ";";
    $resSessionCourse = Database::query($sqlSessionCourse);
    $aux = array();

    // loop through courses of current session
    while ($rowSessionCourse = Database::fetch_assoc($resSessionCourse)) {
        // get course of current session
        $sql = "SELECT a.course_id, a.session_id, a.visible, a.price, b.*
        FROM $tableBuyCourse a, $tableCourse b
        WHERE a.code = b.code AND a.code = '".$rowSessionCourse['course_code']."' AND a.visible = 1;";
        $res = Database::query($sql);
        // loop inside a course of current session
        while ($row = Database::fetch_assoc($res)) {
            //check teacher
            $sql = "SELECT lastname, firstname
            FROM course_rel_user a, user b
            WHERE a.c_id='".$row['id']."'
            AND a.status <> 6
            AND a.user_id=b.id;";
            $tmp = Database::query($sql);
            $rowTmp = Database::fetch_assoc($tmp);
            $row['teacher'] = $rowTmp['firstname'].' '.$rowTmp['lastname'];

            //check images
            if (file_exists(api_get_path(SYS_COURSE_PATH).$row['directory']."/course-pic.png")) {
                $row['course_img'] = "courses/".$row['directory']."/course-pic.png";
            } else {
                $row['course_img'] = "main/img/session_default.png";
            }
            $row['price'] = number_format($row['price'], 2, '.', ' ');
            $aux[] = $row;
        }
    }
    //check if the user is enrolled in the current session
    if ($currentUserId > 0) {
        $sql = "SELECT 1 FROM $tableSessionRelUser
                WHERE user_id = $currentUserId";
        $result = Database::query($sql);
        if (Database::affected_rows($result) > 0) {
            $rowSession['enrolled'] = "YES";
        } else {
            $sql = "SELECT 1 FROM $tableBuySessionTemporal
                    WHERE user_id='".$currentUserId."';";
            $result = Database::query($sql);

            if (Database::affected_rows($result) > 0) {
                $rowSession['enrolled'] = "TMP";
            } else {
                $rowSession['enrolled'] = "NO";
            }
        }
    } else {
        $sql = "SELECT 1 FROM $tableBuySessionTemporal
            WHERE user_id='".$currentUserId."';";
        $result = Database::query($sql);
        if (Database::affected_rows($result) > 0) {
            $rowSession['enrolled'] = "TMP";
        } else {
            $rowSession['enrolled'] = "NO";
        }
    }
    // add courses to current session
    $rowSession['courses'] = $aux;
    return $rowSession;
}
/**
 * Extended information about the course (from the course table as well as
 * the buy_course table)
 * @param string $code The course code
 * @return array Info about the course
 */
function courseInfo($code)
{
    $tableBuyCourse = Database::get_main_table(TABLE_BUY_COURSE);
    $tableCourseRelUser = Database::get_main_table(TABLE_MAIN_COURSE_USER);
    $tableUser = Database::get_main_table(TABLE_MAIN_USER);
    $currentUserId = api_get_user_id();
    $code = Database::escape_string($code);
    $sql = "SELECT a.course_id, a.visible, a.price, b.*
            FROM $tableBuyCourse a, course b
            WHERE
                a.course_id=b.id AND
                a.visible = 1 AND
                b.id = '" . $code . "'";
    $res = Database::query($sql);
    $row = Database::fetch_assoc($res);
    // Check teacher
    $sql = "SELECT lastname, firstname
        FROM $tableCourseRelUser a, $tableUser b
        WHERE
            a.c_id = '" . $row['id'] . "' AND
            a.status <> 6 AND
            a.user_id = b.user_id;";
    $tmp = Database::query($sql);
    $rowTmp = Database::fetch_assoc($tmp);
    $row['teacher'] = $rowTmp['firstname'] . ' ' . $rowTmp['lastname'];
    //Check if student is enrolled
    if ($currentUserId > 0) {
        $sql = "SELECT 1 FROM $tableCourseRelUser
                WHERE
                    c_id ='" . $row['id'] . "' AND
                    user_id='" . $currentUserId . "';";
        $result = Database::query($sql);
        if (Database::affected_rows($result) > 0) {
            $row['enrolled'] = "YES";
        } else {
            $row['enrolled'] = "NO";
        }
    } else {
        $row['enrolled'] = "NO";
    }
    //check img
    if (file_exists(api_get_path(SYS_COURSE_PATH) . $row['code'] . "/course-pic.png")) {
        $row['course_img'] = "courses/" . $row['code'] . "/course-pic.png";
    } else {
        $row['course_img'] = "main/img/session_default.png";
    }
    $row['price'] = number_format($row['price'], 2, '.', ' ');

    return $row;
}
/**
 * Generates a random text (used for order references)
 * @param int $long
 * @param bool $minWords
 * @param bool $maxWords
 * @param bool $number
 * @return string A random text
 */
function randomText($long = 6, $minWords = true, $maxWords = true, $number = true)
{
    $salt = $minWords ? 'abchefghknpqrstuvwxyz' : '';
    $salt .= $maxWords ? 'ACDEFHKNPRSTUVWXYZ' : '';
    $salt .= $number ? (strlen($salt) ? '2345679' : '0123456789') : '';

    if (strlen($salt) == 0) {
        return '';
    }

    $i = 0;
    $str = '';

    srand((double)microtime() * 1000000);

    while ($i < $long) {
        $number = rand(0, strlen($salt) - 1);
        $str .= substr($salt, $number, 1);
        $i++;
    }

    return $str;
}
/**
 * Generates an order reference
 * @result string A reference number
 */
function calculateReference($bcCodetext)
{
    $tableBuyTemporal = $bcCodetext === 'THIS_IS_A_SESSION' ?
        Database::get_main_table(TABLE_BUY_SESSION_TEMPORARY) :
        Database::get_main_table(TABLE_BUY_COURSE_TEMPORAL);
    $sql = "SELECT MAX(cod) as cod FROM $tableBuyTemporal";
    $res = Database::query($sql);
    $row = Database::fetch_assoc($res);
    $reference = ($row['cod'] != '') ? $row['cod'] : '1';
    $randomText = randomText();
    $reference .= $randomText;
    return $reference;
}
/**
 * Gets a list of pending orders
 * @result array List of orders
 * @todo Enable pagination
 */
function pendingList($bcCodetext)
{
    $tableBuyTemporal = $bcCodetext === 'THIS_IS_A_SESSION' ?
        Database::get_main_table(TABLE_BUY_SESSION_TEMPORARY) :
        Database::get_main_table(TABLE_BUY_COURSE_TEMPORAL);
    $sql = "SELECT * FROM $tableBuyTemporal;";
    $res = Database::query($sql);
    $aux = array();
    while ($row = Database::fetch_assoc($res)) {
        $aux[] = $row;
    }
    return $aux;
}
