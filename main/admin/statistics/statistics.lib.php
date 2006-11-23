<?php
// $Id: index.php 8216 2006-11-3 18:03:15 NushiFirefox $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2006 Bart Mollet <bart.mollet@hogent.be>

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
==============================================================================
*/
/**
==============================================================================
* This class provides some functions for statistics
* @package dokeos.statistics
==============================================================================
*/
class Statistics
{
	/**
	 * Converts a number of bytes in a formatted string
	 * @param int $size
	 * @return string Formatted file size
	 */
	function make_size_string($size) {
		if ($size < pow(2,10)) return $size." bytes";
		if ($size >= pow(2,10) && $size < pow(2,20)) return round($size / pow(2,10), 0)." KB";
		if ($size >= pow(2,20) && $size < pow(2,30)) return round($size / pow(2,20), 1)." MB";
		if ($size > pow(2,30)) return round($size / pow(2,30), 2)." GB";
	}
	/**
	 * Count courses
	 * @param string $category_code  Code of a course category. Default: count
	 * all courses.
	 * @return int Number of courses counted
	 */
	function count_courses($category_code = NULL)
	{
		$course_table = Database :: get_main_table(MAIN_COURSE_TABLE);
		$sql = "SELECT COUNT(*) AS number FROM ".$course_table." ";
		if (isset ($category_code))
		{
			$sql .= " WHERE category_code = '".mysql_real_escape_string($category_code)."'";
		}
		$res = api_sql_query($sql, __FILE__, __LINE__);
		$obj = mysql_fetch_object($res);
		return $obj->number;
	}
	/**
	 * Count users
	 * @param int $status COURSEMANAGER or STUDENT
	 * @param string $category_code  Code of a course category. Default: count
	 * all users.
	 * @return int Number of users counted
	 */
	function count_users($status, $category_code = NULL, $count_invisible_courses = true)
	{
		$course_user_table = Database :: get_main_table(MAIN_COURSE_USER_TABLE);
		$course_table = Database :: get_main_table(MAIN_COURSE_TABLE);
		$sql = "SELECT COUNT(DISTINCT(cu.user_id)) AS number FROM $course_user_table cu, $course_table c WHERE cu.status = $status AND cu.course_code = c.code";
		if (isset ($category_code))
		{
			$sql = "SELECT COUNT(DISTINCT(cu.user_id)) AS number FROM $course_user_table cu, $course_table c WHERE cu.status = $status AND c.code = cu.course_code AND c.category_code = '".mysql_real_escape_string($category_code)."'";
		}
		$res = api_sql_query($sql, __FILE__, __LINE__);
		$obj = mysql_fetch_object($res);
		return $obj->number;
	}
	/**
	 * Get all course categories
	 * @return array All course categories (code => name)
	 */
	function get_course_categories()
	{
		$category_table = Database :: get_main_table(MAIN_CATEGORY_TABLE);
		$sql = "SELECT * FROM $category_table ORDER BY tree_pos";
		$res = api_sql_query($sql, __FILE__, __LINE__);
		$categories = array ();
		while ($category = mysql_fetch_object($res))
		{
			$categories[$category->code] = $category->name;
		}
		return $categories;
	}
	/**
	 * Rescale data
	 * @param array $data The data that should be rescaled
	 * @param int $max The maximum value in the rescaled data (default = 500);
	 * @return array The rescaled data, same key as $data
	 */
	function rescale($data, $max = 500)
	{
		$data_max = 1;
		foreach ($data as $index => $value)
		{
			$data_max = ($data_max < $value ? $value : $data_max);
		}
		reset($data);
		$result = array ();
		$delta = $max / $data_max;
		foreach ($data as $index => $value)
		{
			$result[$index] = (int) round($value * $delta);
		}
		return $result;
	}
	/**
	 * Show statistics
	 * @param string $title The title
	 * @param array $stats
	 * @param bool $show_total
	 * @param bool $is_file_size
	 */
	function print_stats($title, $stats, $show_total = true, $is_file_size = false)
	{
		$total = 0;
		$data = Statistics::rescale($stats);
		echo '<table class="data_table" cellspacing="0" cellpadding="3">
			  		  <tr><th colspan="'.($show_total ? '4' : '3').'">'.$title.'</th></tr>';
		$i = 0;
		foreach($stats as $subtitle => $number)
		{
			$total += $number;
		}
		foreach ($stats as $subtitle => $number)
		{
			$i = $i % 13;
			$short_subtitle = str_replace(get_lang('Statistics_Departement'),'',$subtitle);
			$short_subtitle = str_replace(get_lang('Statistics_Central_Administration'),'',$short_subtitle);
			$short_subtitle = trim($short_subtitle);
			if (strlen($subtitle) > 30)
			{
				$short_subtitle = '<acronym title="'.$subtitle.'">'.substr($short_subtitle, 0, 27).'...</acronym>';
			}
			if(!$is_file_size)
			{
				$number_label = number_format($number, 0, ',', '.');
			}
			else
			{
				$number_label = Statistics::make_size_string($number);
			}
			echo '<tr class="row_'.($i%2 == 0 ? 'odd' : 'even').'">
								<td width="150">'.$short_subtitle.'</td>
								<td width="550">
						 			<img src="../../img/bar_1u.gif" width="'.$data[$subtitle].'" height="10"/>
								</td>
								<td align="right">'.$number_label.'</td>';
			if($show_total)
			{
				echo '<td align="right"> '.number_format(100*$number/$total, 1, ',', '.').'%</td>';
			}
			echo '</tr>';
			$i ++;
		}
		if ($show_total)
		{
			if(!$is_file_size)
			{
				$total_label = number_format($total, 0, ',', '.');
			}
			else
			{
				$total_label = Statistics::make_size_string($total);
			}
			echo '<tr><th  colspan="4" align="right">'.get_lang('Total').': '.$total_label.'</td></tr>';
		}
		echo '</table>';
	}
	/**
	 * Show some stats about the number of logins
	 * @param string $type month, hour or day
	 */
	function print_login_stats($type)
	{
		switch($type)
		{
			case 'month':
				$period = get_lang('PeriodMonth');
				$sql = "SELECT DATE_FORMAT( login_date, '%Y %b' ) AS stat_date , count( login_id ) AS number_of_logins FROM dokeos_stats.track_e_login GROUP BY stat_date ORDER BY login_date ";
				break;
			case 'hour':
				$period = get_lang('PeriodHour');
				$sql = "SELECT DATE_FORMAT( login_date, '%H' ) AS stat_date , count( login_id ) AS number_of_logins FROM dokeos_stats.track_e_login GROUP BY stat_date ORDER BY stat_date ";
				break;
			case 'day':
				$period = get_lang('PeriodDay');
				$sql = "SELECT DATE_FORMAT( login_date, '%a' ) AS stat_date , count( login_id ) AS number_of_logins FROM dokeos_stats.track_e_login GROUP BY stat_date ORDER BY DATE_FORMAT( login_date, '%w' ) ";
				break;
		}
		$res = api_sql_query($sql,__FILE__,__LINE__);
		$result = array();
		while($obj = mysql_fetch_object($res))
		{
			$result[$obj->stat_date] = $obj->number_of_logins;
		}
		Statistics::print_stats(get_lang('Logins').' ('.$period.')',$result,true);
	}
	/**
	 * Print the number of recent logins
	 */
	function print_recent_login_stats()
	{
		$total_logins = array();
		$table = Database::get_statistic_table(STATISTIC_TRACK_E_LASTACCESS_TABLE);
		$sql[get_lang('Thisday')] = "SELECT count(DISTINCT access_user_id) AS number FROM $table WHERE DATE_ADD(access_date, INTERVAL 1 DAY) >= NOW()";
		$sql[get_lang('Last7days')] = "SELECT count(DISTINCT access_user_id) AS number  FROM $table WHERE DATE_ADD(access_date, INTERVAL 7 DAY) >= NOW()";
		$sql[get_lang('Last31days')] = "SELECT count(DISTINCT access_user_id) AS number  FROM $table WHERE DATE_ADD(access_date, INTERVAL 31 DAY) >= NOW()";
		$sql[get_lang('Total')] = "SELECT count(DISTINCT access_user_id) AS number  FROM $table";
		foreach($sql as $index => $query)
		{
			$res = api_sql_query($query,__FILE__,__LINE__);
			$obj = mysql_fetch_object($res);
			$total_logins[$index] = $obj->number;
		}
		Statistics::print_stats(get_lang('Logins'),$total_logins,false);
	}
	/**
	 * Show some stats about the accesses to the different course tools
	 */
	function print_tool_stats()
	{
		$tools = array('announcement','assignment','calendar_event','chat','conference','course_description','document','dropbox','group','learnpath','link','quiz','student_publication','user','bb_forum');
		$sql = "SELECT access_tool, count( access_id ) AS number_of_logins FROM dokeos_stats.track_e_access WHERE access_tool IN ('".implode("','",$tools)."') GROUP BY access_tool ";
		$res = api_sql_query($sql,__FILE__,__LINE__);
		$result = array();
		while($obj = mysql_fetch_object($res))
		{
			$result[$obj->access_tool] = $obj->number_of_logins;
		}
		Statistics::print_stats(get_lang('Statistics_Acces_to_coursemodules_hits'),$result,true);
		$sql = "SELECT access_tool, count( access_id ) AS number_of_logins FROM dokeos_stats.track_e_lastaccess WHERE access_tool IN ('".implode("','",$tools)."') GROUP BY access_tool ";
		$res = api_sql_query($sql,__FILE__,__LINE__);
		$result = array();
		while($obj = mysql_fetch_object($res))
		{
			$result[$obj->access_tool] = $obj->number_of_logins;
		}
		Statistics::print_stats(get_lang('Statistics_Acces_to_coursemodules_use'),$result,true);
	}



	/**
	 * Shows the number of users having their picture uploaded in Dokeos.
	 */
	function print_user_pictures_stats()
	{
		$user_table = Database :: get_main_table(MAIN_USER_TABLE);
		$sql = "SELECT COUNT(*) AS n FROM $user_table";
		$res = api_sql_query($sql,__FILE__,__LINE__);
		$count1 = mysql_fetch_object($res);
		$sql = "SELECT COUNT(*) AS n FROM $user_table WHERE LENGTH(picture_uri) > 0";
		$res = api_sql_query($sql,__FILE__,__LINE__);
		$count2 = mysql_fetch_object($res);
		$result[get_lang('No')] = $count1->n;
		$result[get_lang('Yes')] = $count2->n;
		Statistics::print_stats(get_lang('CountUsers').' ('.get_lang('UserPicture').')',$result,true);

	#	echo $count2->n.' ' & get_lang('Statistics_user_who_have_picture_in_dokeos') & '('.number_format(($count2->n/$count1->n*100), 0, ',', '.').'%)';
	}

	/**
	 * Shows statistics about the time of last visit to each course.
	 */
	function print_course_last_visit()
	{
		$columns[0] = 'access_cours_code';
		$columns[1] = 'access_date';

		$sql_order[SORT_ASC] = 'ASC';
		$sql_order[SORT_DESC] = 'DESC';

		$per_page = isset($_GET['per_page']) ? $_GET['per_page'] : 10;
		$page_nr = isset($_GET['page_nr']) ? $_GET['page_nr'] : 1;
		$column = isset($_GET['column']) ? $_GET['column'] : 0;
		$date_diff = isset($_GET['date_diff']) ? $_GET['date_diff'] : 60;
		$direction = isset($_GET['direction']) ? $_GET['direction'] : SORT_ASC;
		?>
		<form method="get" action="index.php">
		<input type="hidden" name="action" value="courselastvisit"/>
		<input type="text" name="date_diff" value="<?php echo $date_diff; ?>"/>
		<input type="submit" value="<?php echo get_lang('Search'); ?>"/>
		</form>
		<?php
		$table = Database::get_statistic_table(STATISTIC_TRACK_E_LASTACCESS_TABLE);
		$sql = "SELECT * FROM $table GROUP BY access_cours_code HAVING access_cours_code <> '' AND DATEDIFF( NOW() , access_date ) >= ". $date_diff;
		$res = api_sql_query($sql,__FILE__,__LINE__);
		$number_of_courses = mysql_num_rows($res);
		$sql .= ' ORDER BY '.$columns[$column].' '.$sql_order[$direction];
		$from = ($page_nr -1) * $per_page;
		$sql .= ' LIMIT '.$from.','.$per_page;
		echo '<p>'.get_lang('LastAccess').' &gt;= '.$date_diff.' '.get_lang('Days').'</p>';
		$res = api_sql_query($sql, __FILE__, __LINE__);
		if (mysql_num_rows($res) > 0)
		{
			$courses = array ();
			while ($obj = mysql_fetch_object($res))
			{
				$course = array ();
				$course[]= '<a href="'.api_get_path(WEB_PATH).'courses/'.$obj->access_cours_code.'">'.$obj->access_cours_code.' <a>';
				$course[] = $obj->access_date;
				$courses[] = $course;
			}

			$table_header[] = array ("Coursecode", true);
			$table_header[] = array ("Last login", true);
			Display :: display_sortable_table($table_header, $courses, array ('column'=>$column,'direction'=>$direction), array (), $parameters);
			foreach(array_merge($parameters,$_GET) as $id => $value)
			{
				if ($id!='selectall'){
					$link .= $id.'='.$value.'&amp;';
				}
			}

		}
		else
		{
			echo get_lang('NoSearchResults');
		}
	}

}
?>