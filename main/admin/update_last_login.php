<?php

require_once '../inc/global.inc.php';
api_protect_admin_script();
exit;
if (isset($_configuration['save_user_last_login'])) {
    $tableUser = Database::get_main_table(TABLE_MAIN_USER);

    $userInfo = api_get_user_info(api_get_user_id());
    if (!empty($userInfo['last_login'])) {
        echo "<br />Script was already executed";
        exit;
    }

    if (!isset($userInfo['last_login'])) {
        $sql = "SELECT login_user_id, MAX(login_date) login_date from track_e_login group by login_user_id";
        echo "Executing: <br />$sql<br /> Updating <br />";
        $result = Database::query($sql);
        while ($row = Database::fetch_array($result)) {
            $date = $row['login_date'];
            $userId = $row['login_user_id'];
            $sql = "UPDATE $tableUser SET last_login ='$date' WHERE user_id = $userId";
            echo "<br />Updating: <br />$sql";
            Database::query($sql);
        }
    } else {
        $sql = "ALTER TABLE $tableUser ADD COLUMN last_login DATETIME";
        $result = Database::query($sql);
        echo "last_login does not exits creating with: <br/> $sql";
    }
}
