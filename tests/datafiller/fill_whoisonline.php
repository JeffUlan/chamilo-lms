<?php
/**
 * This script contains a data filling procedure for users
 * @author Julio Montoya <gugli100@gmail.com>
 *
 */

/**
 * Loads the data and injects it into the Dokeos database, using the Dokeos
 * internal functions.
 * @return  array  List of user IDs for the users that have just been inserted
 */
function fill_whoisonline()
{
	$table_e_online = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ONLINE);
	$max = 100;

	//Cleaning the table
	$sql = "TRUNCATE $table_e_online";
	$rs = Database::query($sql);
	//filling the table
	for ($i=1;$i <=$max;$i++) {
		$date = api_get_utc_datetime();
		$sql = "INSERT INTO	$table_e_online (login_id, login_user_id, login_date, user_ip, c_id, session_id, access_url_id)
				VALUES ('$i', '$i', '$date', '127.0.0.1', '', '0','1')";
		$rs = Database::query($sql);
	}
}
