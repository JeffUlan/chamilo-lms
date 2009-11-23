<?php
/* For licensing terms, see /dokeos_license.txt */
/**
==============================================================================
*	This library provides functions for the access_url management.
*	Include/require it in your code to use its functionality.
*	@author Julio Montoya <gugli100@gmail.com>
*	@package dokeos.library
==============================================================================
*/

define('GROUP_PERMISSION_OPEN'	, '1');
define('GROUP_PERMISSION_CLOSED', '2');

define('GROUP_USER_PERMISSION_ADMIN'	,'1');
define('GROUP_USER_PERMISSION_READER'	,'2');

class GroupPortalManager
{
	/**
	  * Creates a new group
	  *
	  * @author Julio Montoya <gugli100@gmail.com>,
	  *
	  * @param	string	The URL of the site
 	  * @param	string  The description of the site
 	  * @param	int		is active or not
	  * @param  int     the user_id of the owner
	  * @return boolean if success
	  */
	function add($name, $description, $url, $visibility, $picture='')
	{
		$tms	= time();
		$table 	= Database :: get_main_table(TABLE_MAIN_GROUP);
		$sql 	= "INSERT INTO $table
                SET name 	= '".Database::escape_string($name)."',
                description = '".Database::escape_string($description)."',
                picture_uri = '".Database::escape_string($picture)."',
                url 		= '".Database::escape_string($url)."',
                visibility 	= '".Database::escape_string($visibility)."',
                created_on = FROM_UNIXTIME(".$tms."), 
                updated_on = FROM_UNIXTIME(".$tms.")";
		$result = Database::query($sql, __FILE__, __LINE__);
		$return = Database::insert_id();
		return $return;
	}

	/**
	* Updates a group
	* @author Julio Montoya <gugli100@gmail.com>,
	*
	* @param	int 	The id
	* @param	string  The description of the site
	* @param	int		is active or not
	* @param	int     the user_id of the owner
	* @return 	boolean if success
	*/
	function update($group_id, $name, $description, $url, $visibility, $picture_uri)
	{
		$group_id = intval($group_id);
		$table = Database::get_main_table(TABLE_MAIN_GROUP);
		$tms = time();
		$sql = "UPDATE $table
             	SET name 	= '".Database::escape_string($name)."',
                description = '".Database::escape_string($description)."',
                picture_uri = '".Database::escape_string($picture_uri)."',
                url 		= '".Database::escape_string($url)."',
                visibility 	= '".Database::escape_string($visibility)."',
                updated_on 	= FROM_UNIXTIME(".$tms.")
                WHERE id = '$group_id'";
		$result = Database::query($sql, __FILE__, __LINE__);
		return $result;
	}


	/**
	* Deletes a group
	* @author Julio Montoya
	* @param int id
	* @return boolean true if success
	* */
	function delete($id)
	{
		$id = intval($id);
		$table = Database :: get_main_table(TABLE_MAIN_GROUP);
		$sql= "DELETE FROM $table WHERE id = ".Database::escape_string($id);
		$result = Database::query($sql,  __FILE__, __LINE__);
		return $result;
	}

	/**
	 *
	 * */
	function url_exist($url)
	{
		$table_access_url= Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
		$sql = "SELECT id FROM $table_access_url WHERE url = '".Database::escape_string($url)."' ";
		$res = Database::query($sql,__FILE__,__LINE__);
		$num = Database::num_rows($res);
		return $num;
	}

	/**
	 *
	 * */
	function url_id_exist($url)
	{
		$table_access_url= Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
		$sql = "SELECT id FROM $table_access_url WHERE id = '".Database::escape_string($url)."' ";
		$res = Database::query($sql,__FILE__,__LINE__);
		$num = Database::num_rows($res);
		return $num;
	}

	/**
	 * This function get the quantity of URLs
	 * @author Julio Montoya
	 * @return int count of urls
	 * */
	function url_count()
	{
		$table_access_url= Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
		$sql = "SELECT count(id) as count_result FROM $table_access_url";
		$res = Database::query($sql, __FILE__, __LINE__);
		$url = Database::fetch_array($res,'ASSOC');
		$result = $url['count_result'];
		return $result;
	}

	/**
	 * Gets the id, url, description, and active status of ALL URLs
	 * @author Julio Montoya
	 * @return array
	 * */
	function get_url_data()
	{
		$table_access_url= Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
		$sql = "SELECT id, url, description, active  FROM $table_access_url";
		$res = Database::query($sql, __FILE__, __LINE__);
		$urls = array ();
		while ($url = Database::fetch_array($res)) {
			$urls[] = $url;
		}
		return $urls;
	}
	
	/**
	 * Gets data of all groups
	 * @author Julio Montoya
	 * @param int	visibility
	 * @param int	from which record the results will begin (use for pagination)
	 * @param int	number of items
	 * @return array	
	 * */
	function get_all_group_data($visibility = GROUP_PERMISSION_OPEN, $from=0, $number_of_items=10)	
	{
		$table	= Database :: get_main_table(TABLE_MAIN_GROUP);
		$visibility = intval($visibility);
		$user_condition = '';		
		$sql = "SELECT name, description, picture_uri FROM $table WHERE visibility = $visibility ";
		$res = Database::query($sql, __FILE__, __LINE__);
		$data = array ();
		while ($item = Database::fetch_array($res)) {
			$data[] = $item;
		}
		return $data;
	}
	
	/**
	 * Gets the group data
	 * 
	 * 
	 */
	function get_group_data($group_id)	
	{
		$table	= Database :: get_main_table(TABLE_MAIN_GROUP);
		$group_id = intval($group_id);
		$user_condition = '';		
		$sql = "SELECT name, description, picture_uri, visibility FROM $table WHERE id = $group_id ";
		$res = Database::query($sql, __FILE__, __LINE__);
		$item = array(); 
		if (Database::num_rows($res)>0) {
			$item = Database::fetch_array($res,'ASSOC');
		}
		return $item;
	}
	
	/**
	 * Gets the tags from a given group
	 * @param int	group id
	 * @param bool show group links or not 
	 * 
	 */
	function get_group_tags($group_id, $show_tag_links = true)	
	{
		$tag					= Database :: get_main_table(TABLE_MAIN_TAG);
		$table_group_rel_tag	= Database :: get_main_table(TABLE_MAIN_GROUP_REL_TAG);
		$group_id 				= intval($group_id);		
		$user_condition 		= '';
				
		$sql = "SELECT tag FROM $tag t INNER JOIN $table_group_rel_tag gt ON (gt.tag_id= t.id) WHERE gt.group_id = $group_id ";
		$res = Database::query($sql, __FILE__, __LINE__);
		$tags = array(); 
		if (Database::num_rows($res)>0) {
			while ($row = Database::fetch_array($res,'ASSOC')) {
					$tags[] = $row;	
			}
		}
	
		if ($show_tag_links == true) {
			if (is_array($tags) && count($tags)>0) {
				foreach ($tags as $tag) {
					$tag_tmp[] = '<a href="/main/social/search.php?q='.$tag['tag'].'">'.$tag['tag'].'</a>';
				}		
				if (is_array($tags) && count($tags)>0) {							
					$tags= implode(', ',$tag_tmp);
				}
			} else {
				$tags = '';
			}
		}		
		return $tags;
	}	

	

	/**
	 * Gets the id, url, description, and active status of ALL URLs
	 * @author Julio Montoya
	 * @return array
	 * */
	function get_url_data_from_id($url_id)
	{		
		$table_access_url= Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
		$sql = "SELECT id, url, description, active FROM $table_access_url WHERE id = ".Database::escape_string($url_id);
		$res = Database::query($sql, __FILE__, __LINE__);
		$row = Database::fetch_array($res);
		return $row;
	}

	/** Gets the inner join of users and group table
	 * @author Julio Montoya
	 * @return int  access url id
	 * @return array   Database::store_result of the result
	 * */
	function get_groups_by_user($user_id='', $relation_type = GROUP_USER_PERMISSION_READER, $with_image = false)
	{
		$where = '';
		$table_group_rel_user	= Database::get_main_table(TABLE_MAIN_USER_REL_GROUP);
		$tbl_group				= Database::get_main_table(TABLE_MAIN_GROUP);
		$user_id 				= intval($user_id);
		
		if ($relation_type == 0) {			
			$where_relation_condition = '';
		} else {
			$relation_type 			= intval($relation_type);
			$where_relation_condition = "AND gu.relation_type = $relation_type ";
		}
		
		$sql = "SELECT g.picture_uri, g.name, g.description, g.id  
				FROM $tbl_group g
				INNER JOIN $table_group_rel_user gu
				ON gu.group_id = g.id WHERE gu.user_id = $user_id $where_relation_condition ORDER BY created_on desc ";
				
		$result=Database::query($sql,__FILE__,__LINE__);
		$array = array();
		while ($row = Database::fetch_array($result, 'ASSOC')) {
				if ($with_image == true) {
					$picture = self::get_picture_group($row['id'], $row['picture_uri'],80);
					$img = '<img src="'.$picture['file'].'" />';
					$row['picture_uri'] = $img;
				}
				$array[$row['id']] = $row;			
		}
		return $array;
	}
	
	
		/** Gets the inner join of users and group table
	 * @author Julio Montoya
	 * @return int  access url id
	 * @return array   Database::store_result of the result
	 * */
	function get_groups_by_popularity($num = 10, $with_image = false)
	{
		$where = '';
		$table_group_rel_user	= Database::get_main_table(TABLE_MAIN_USER_REL_GROUP);
		$tbl_group				= Database::get_main_table(TABLE_MAIN_GROUP);	
		if (empty($num)) {
			$num = 10;
		} else {
			$num = intval($num);
		}
		
		$sql = "SELECT count(user_id) as count, g.picture_uri, g.name, g.description, g.id  
				FROM $tbl_group g
				INNER JOIN $table_group_rel_user gu
				ON gu.group_id = g.id GROUP BY g.id ORDER BY count DESC LIMIT $num";
				
		$result=Database::query($sql,__FILE__,__LINE__);
		$array = array();
		while ($row = Database::fetch_array($result, 'ASSOC')) {
				if ($with_image == true) {
					$picture = self::get_picture_group($row['id'], $row['picture_uri'],80);
					$img = '<img src="'.$picture['file'].'" />';
					$row['picture_uri'] = $img;
				}
				$array[$row['id']] = $row;			
		}
		return $array;
	}
	
	/** Gets the last groups created
	 * @author Julio Montoya
	 * @return int  access url id
	 * @return array   Database::store_result of the result
	 * */
	function get_groups_by_age($num = 10, $with_image = false)
	{
		$where = '';
		$table_group_rel_user	= Database::get_main_table(TABLE_MAIN_USER_REL_GROUP);
		$tbl_group				= Database::get_main_table(TABLE_MAIN_GROUP);

		if (empty($num)) {
			$num = 10;
		} else {
			$num = intval($num);
		}
		$sql = "SELECT g.picture_uri, g.name, g.description, g.id  
				FROM $tbl_group g
				INNER JOIN $table_group_rel_user gu
				ON gu.group_id = g.id ORDER BY created_on desc LIMIT $num ";
				
		$result=Database::query($sql,__FILE__,__LINE__);
		$array = array();
		while ($row = Database::fetch_array($result, 'ASSOC')) {
				if ($with_image == true) {
					$picture = self::get_picture_group($row['id'], $row['picture_uri'],80);
					$img = '<img src="'.$picture['file'].'" />';
					$row['picture_uri'] = $img;
				}
				$array[$row['id']] = $row;			
		}
		return $array;
	}
	
	
	function get_users_by_group($group_id='', $with_image = false)
	{
		$where = '';
		$table_group_rel_user	= Database::get_main_table(TABLE_MAIN_USER_REL_GROUP);
		$tbl_user				= Database::get_main_table(TABLE_MAIN_USER);
		$group_id 				= intval($group_id);
		
		if ($relation_type == 0) {			
			$where_relation_condition = '';
		} else {
			$relation_type 			= intval($relation_type);
			$where_relation_condition = "AND gu.relation_type = $relation_type ";
		}
		
		$sql="SELECT u.user_id, u.firstname, u.lastname, picture_uri, relation_type FROM $tbl_user u
			INNER JOIN $table_group_rel_user gu
			ON (gu.user_id = u.user_id) WHERE gu.group_id= $group_id $where_relation_condition ORDER BY relation_type, firstname";
			
		$result=Database::query($sql,__FILE__,__LINE__);
		$array = array();
		while ($row = Database::fetch_array($result, 'ASSOC')) {
				if ($with_image == true) {
					$picture = UserManager::get_picture_user($row['user_id'], $row['picture_uri'],80,'medium_');
					$img = '<img src="'.$picture['file'].'" />';
					$row['picture_uri'] = $img;
				}
				$array[$row['user_id']] = $row;			
		}
		return $array;
	}
	


	 /** Gets the inner join of access_url and the course table
	 * @author Julio Montoya
	 * @return int  access url id
	 * @return array   Database::store_result of the result
	 * */
	function get_url_rel_course_data($access_url_id='')
	{
		$where ='';
		$table_url_rel_course	= Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
		$tbl_course 			= Database :: get_main_table(TABLE_MAIN_COURSE);

		if (!empty($access_url_id))
			$where ="WHERE $table_url_rel_course.access_url_id = ".Database::escape_string($access_url_id);

		$sql="SELECT course_code, title, access_url_id
				FROM $tbl_course u
				INNER JOIN $table_url_rel_course
				ON $table_url_rel_course.course_code = code
				$where
				ORDER BY title, code";

		$result=Database::query($sql,__FILE__,__LINE__);
		$courses=Database::store_result($result);
		return $courses;
	}

	/** Gets the inner join of access_url and the session table
	 * @author Julio Montoya
	 * @return int  access url id
	 * @return array   Database::store_result of the result
	 * */
	function get_url_rel_session_data($access_url_id='')
	{
		$where ='';
		$table_url_rel_session	= Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_SESSION);
		$tbl_session 			= Database :: get_main_table(TABLE_MAIN_SESSION);

		if (!empty($access_url_id))
			$where ="WHERE $table_url_rel_session.access_url_id = ".Database::escape_string($access_url_id);

		$sql="SELECT id, name, access_url_id
				FROM $tbl_session u
				INNER JOIN $table_url_rel_session
				ON $table_url_rel_session.session_id = id
				$where
				ORDER BY name, id";

		$result=Database::query($sql,__FILE__,__LINE__);
		$sessions=Database::store_result($result);
		return $sessions;
	}



	/**
	 * Sets the status of an URL 1 or 0
	 * @author Julio Montoya
	 * @param string lock || unlock
	 * @param int url id
	 * */
	function set_url_status($status, $url_id)
	{
		$url_table = Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
		if ($status=='lock') {
			$status_db='0';
		}
		if ($status=='unlock') {
			$status_db='1';
		}
		if(($status_db=='1' OR $status_db=='0') AND is_numeric($url_id)) {
			$sql="UPDATE $url_table SET active='".Database::escape_string($status_db)."' WHERE id='".Database::escape_string($url_id)."'";
			$result = Database::query($sql, __FILE__, __LINE__);
		}
	}

	/**
	* Gets the relationship between a group and a User 
	* @author Julio Montoya
	* @param int user id
	* @param int group_id
	* @return int 0 if there are not relationship otherwise return GROUP_USER_PERMISSION_ADMIN or GROUP_USER_PERMISSION_READER constants
	* */
	
	function get_user_group_role($user_id, $group_id)
	{
		$table_group_rel_user= Database :: get_main_table(TABLE_MAIN_USER_REL_GROUP);
		$return_value = 0;
		if (!empty($user_id) && !empty($group_id)) {
			$sql	= "SELECT relation_type FROM $table_group_rel_user WHERE group_id = ".intval($group_id)." AND  user_id = ".intval($user_id)." ";
			$result = Database::query($sql,  __FILE__, __LINE__);		
			if (Database::num_rows($result)>0) {	
				$row = Database::fetch_array($result,'ASSOC');
				$return_value = $row['relation_type'];
			}			
		}
		return $return_value;
	}

	/**
	* Checks the relationship between an URL and a Course (return the num_rows)
	* @author Julio Montoya
	* @param int user id
	* @param int url id
	* @return boolean true if success
	* */
	function relation_url_course_exist($course_id, $url_id)
	{
		$table_url_rel_course= Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
		$sql= "SELECT course_code FROM $table_url_rel_course WHERE access_url_id = ".Database::escape_string($url_id)." AND course_code = '".Database::escape_string($course_id)."'";
		$result = Database::query($sql,  __FILE__, __LINE__);
		$num = Database::num_rows($result);
		return $num;
	}


	/**
	* Checks the relationship between an URL and a Session (return the num_rows)
	* @author Julio Montoya
	* @param int user id
	* @param int url id
	* @return boolean true if success
	* */
	function relation_url_session_exist($session_id, $url_id)
	{
		$table_url_rel_session= Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_SESSION);
		$sql= "SELECT session_id FROM $table_url_rel_session WHERE access_url_id = ".Database::escape_string($url_id)." AND session_id = ".Database::escape_string($session_id);
		$result = Database::query($sql,  __FILE__, __LINE__);
		$num = Database::num_rows($result);
		return $num;
	}




	/**
	 * Add a group of courses into a group of URLs
	 * @author Julio Montoya
	 * @param  array of course ids
	 * @param  array of url_ids
	 * */
	function add_courses_to_urls($course_list,$url_list)
	{
		$table_url_rel_course= Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
		$result_array=array();

		if (is_array($course_list) && is_array($url_list)){
			foreach ($url_list as $url_id) {
				foreach ($course_list as $course_code) {
					$count = UrlManager::relation_url_course_exist($course_code,$url_id);
					if ($count==0) {
						$sql = "INSERT INTO $table_url_rel_course
		               			SET course_code = '".Database::escape_string($course_code)."', access_url_id = ".Database::escape_string($url_id);
						$result = Database::query($sql, __FILE__, __LINE__);
						if($result)
							$result_array[$url_id][$course_code]=1;
						else
							$result_array[$url_id][$course_code]=0;
					}
				}
			}
		}
		return 	$result_array;
	}


	/**
	 * Add a group of sessions into a group of URLs
	 * @author Julio Montoya
	 * @param  array of session ids
	 * @param  array of url_ids
	 * */
	function add_sessions_to_urls($session_list,$url_list)
	{
		$table_url_rel_session= Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_SESSION);
		$result_array=array();

		if (is_array($session_list) && is_array($url_list)){
			foreach ($url_list as $url_id) {
				foreach ($session_list as $session_id) {
					$count = UrlManager::relation_url_session_exist($session_id,$url_id);
					if ($count==0) {
						$sql = "INSERT INTO $table_url_rel_session
		               			SET session_id = ".Database::escape_string($session_id).", access_url_id = ".Database::escape_string($url_id);
						$result = Database::query($sql, __FILE__, __LINE__);
						if($result)
							$result_array[$url_id][$session_id]=1;
						else
							$result_array[$url_id][$session_id]=0;
					}
				}
			}
		}
		return 	$result_array;
	}



	/**
	 * Add a user into a url
	 * @author Julio Montoya
	 * @param  user_id
	 * @param  url_id
	 * @return boolean true if success
	 * */
	function add_user_to_group($user_id, $group_id, $relation_type = GROUP_USER_PERMISSION_READER)
	{
		$table_url_rel_group = Database :: get_main_table(TABLE_MAIN_USER_REL_GROUP);
		if (!empty($user_id) && !empty($group_id)) {			
			$role = self::get_user_group_role($user_id,$group_id);
			if ($role==0) {
				$sql = "INSERT INTO $table_url_rel_group
           				SET user_id = ".intval($user_id).", group_id = ".intval($group_id).", relation_type = ".intval($relation_type);
				$result = Database::query($sql, __FILE__, __LINE__);
			}
		}
		return $result;
	}
	
	
	/**
	 * Add a group of users into a group of URLs
	 * @author Julio Montoya
	 * @param  array of user_ids
	 * @param  array of url_ids
	 * */
	function add_users_to_groups($user_list, $group_list, $relation_type = GROUP_USER_PERMISSION_READER) {
		$table_url_rel_group = Database :: get_main_table(TABLE_MAIN_USER_REL_GROUP);
		$result_array = array();
		$relation_type = intval($relation_type);
		
		if (is_array($user_list) && is_array($group_list)) {
			foreach ($group_list as $group_id) {
				foreach ($user_list as $user_id) {
					$role = self::get_user_group_role($user_id,$group_id);
					if ($role == 0) {
						$sql = "INSERT INTO $table_url_rel_group
		               			SET user_id = ".intval($user_id).", group_id = ".intval($group_id).", relation_type = ".intval($relation_type)."";
		      		               	
						$result = Database::query($sql, __FILE__, __LINE__);
						if ($result)
							$result_array[$group_id][$user_id]=1;
						else
							$result_array[$group_id][$user_id]=0;
					}
				}
			}
		}
		return 	$result_array;
	}



	/**
	* Deletes an url and user relationship
	* @author Julio Montoya
	* @param int user id
	* @param int url id
	* @return boolean true if success
	* */
	function delete_users($group_id)
	{
		$table_= Database :: get_main_table(TABLE_MAIN_USER_REL_GROUP);
		$sql= "DELETE FROM $table_ WHERE group_id = ".intval($group_id);
		$result = Database::query($sql,  __FILE__, __LINE__);
		return $result;
	}

	/**
	* Deletes an url and course relationship
	* @author Julio Montoya
	* @param  char  course code
	* @param  int url id
	* @return boolean true if success
	* */
	function delete_url_rel_course($course_code, $url_id)
	{
		$table_url_rel_course= Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
		$sql= "DELETE FROM $table_url_rel_course WHERE course_code = '".Database::escape_string($course_code)."' AND access_url_id=".Database::escape_string($url_id)."  ";
		$result = Database::query($sql,  __FILE__, __LINE__);
		return $result;
	}

	/**
	* Deletes an url and session relationship
	* @author Julio Montoya
	* @param  char  course code
	* @param  int url id
	* @return boolean true if success
	* */
	function delete_url_rel_session($session_id, $url_id)
	{
		$table_url_rel_session = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_SESSION);
		$sql= "DELETE FROM $table_url_rel_session WHERE session_id = ".Database::escape_string($session_id)." AND access_url_id=".Database::escape_string($url_id)."  ";
		$result = Database::query($sql,  __FILE__, __LINE__);
		return $result;
	}


	/**
	 * Updates the access_url_rel_user table  with a given user list
	 * @author Julio Montoya
	 * @param array user list
	 * @param int access_url_id
	 * */
	function update_urls_rel_user($user_list,$access_url_id)
	{
		$table_access_url	= Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
		$table_url_rel_user	= Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);

		$sql = "SELECT user_id FROM $table_url_rel_user WHERE access_url_id=".Database::escape_string($access_url_id);
		$result = Database::query($sql,__FILE__,__LINE__ );
		$existingUsers = array();

		while($row = Database::fetch_array($result)){
			$existingUsers[] = $row['user_id'];
		}

		//adding users
		foreach($user_list as $enreg_user) {
			if(!in_array($enreg_user, $existingUsers)) {
				UrlManager::add_user_to_url($enreg_user,$access_url_id);
			}
		}
		//deleting old users
		foreach($existingUsers as $existing_user) {
			if(!in_array($existing_user, $user_list)) {
				UrlManager::delete_url_rel_user($existing_user,$access_url_id);
			}
		}
	}


	/**
	 * Updates the access_url_rel_course table  with a given user list
	 * @author Julio Montoya
	 * @param array user list
	 * @param int access_url_id
	 * */
	function update_urls_rel_course($course_list,$access_url_id)
	{
		$table_course			= Database :: get_main_table(TABLE_MAIN_COURSE);
		$table_url_rel_course	= Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);

		$sql = "SELECT course_code FROM $table_url_rel_course WHERE access_url_id=".Database::escape_string($access_url_id);
		$result = Database::query($sql,__FILE__,__LINE__ );
		$existing_courses = array();

		while($row = Database::fetch_array($result)){
			$existing_courses[] = $row['course_code'];
		}

		//adding courses
		foreach($course_list as $course) {
			if(!in_array($course, $existing_courses)) {
				UrlManager::add_course_to_url($course,$access_url_id);
			}
		}

		//deleting old courses
		foreach($existing_courses as $existing_course) {
			if(!in_array($existing_course, $course_list)) {
				UrlManager::delete_url_rel_course($existing_course,$access_url_id);
			}
		}
	}

	/**
	 * Updates the access_url_rel_session table with a given user list
	 * @author Julio Montoya
	 * @param array user list
	 * @param int access_url_id
	 * */
	function update_urls_rel_session($session_list,$access_url_id)
	{
		$table_session	= Database :: get_main_table(TABLE_MAIN_SESSION);
		$table_url_rel_session	= Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_SESSION);

		$sql = "SELECT session_id FROM $table_url_rel_session WHERE access_url_id=".Database::escape_string($access_url_id);
		$result = Database::query($sql,__FILE__,__LINE__ );
		$existing_sessions = array();

		while($row = Database::fetch_array($result)){
			$existing_sessions[] = $row['session_id'];
		}

		//adding users
		foreach($session_list as $session) {
			if(!in_array($session, $existing_sessions)) {
				UrlManager::add_session_to_url($session,$access_url_id);
			}
		}

		//deleting old users
		foreach($existing_sessions as $existing_session) {
			if(!in_array($existing_session, $session_list)) {
				UrlManager::delete_url_rel_session($existing_session,$access_url_id);
			}
		}
	}


	function get_access_url_from_user($user_id) {
		$table_url_rel_user	= Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
		$table_url	= Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
		$sql = "SELECT url, access_url_id FROM $table_url_rel_user url_rel_user INNER JOIN $table_url u
			    ON (url_rel_user.access_url_id = u.id)
			    WHERE user_id = ".Database::escape_string($user_id);
		$result = Database::query($sql,  __FILE__, __LINE__);
		$url_list = Database::store_result($result);
		return $url_list;
	}

	/**
	 *
	 * */
	function get_url_id($url)
	{
		$table_access_url= Database :: get_main_table(TABLE_MAIN_ACCESS_URL);
		$sql = "SELECT id FROM $table_access_url WHERE url = '".Database::escape_string($url)."'";
		$result = Database::query($sql);
		$access_url_id = Database::result($result, 0, 0);
		return $access_url_id;
	}
	
	
	public static function get_all_group_tags($tag, $from=0, $number_of_items=10) {
		// database table definition
		
		$group_table 			= Database::get_main_table(TABLE_MAIN_GROUP);
		$table_tag				= Database::get_main_table(TABLE_MAIN_TAG);
		$table_group_tag_values	= Database::get_main_table(TABLE_MAIN_GROUP_REL_TAG);
		
		//default field_id == 1
		
		$field_id = 5;

		$tag = Database::escape_string($tag);
		$from = intval($from);
    	$number_of_items = intval($number_of_items);

		// all the information of the field
		$sql = "SELECT g.id, g.name, g.description, g.picture_uri FROM $table_tag t INNER JOIN $table_group_tag_values tv ON (tv.tag_id=t.id)
					 INNER JOIN $group_table g ON(tv.group_id =g.id)
				WHERE tag LIKE '$tag%' AND field_id= $field_id ORDER BY tag";
				
		$sql .= " LIMIT $from,$number_of_items";	
					
		$result = Database::query($sql, __FILE__, __LINE__);
		$return = array();
		if (Database::num_rows($result)> 0) {
			while ($row = Database::fetch_array($result,'ASSOC')) {
				$return[$row['id']] = $row;
			}
		}
		 
		$keyword = $tag;
		$sql = "SELECT  g.id, g.name, g.description, g.url, g.picture_uri FROM $group_table g";
		
		//@todo implement groups + multiple urls 
		
		/*
		global $_configuration;
		if ($_configuration['multiple_access_urls']==true && api_get_current_access_url_id()!=-1) {
			$access_url_rel_user_table= Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_USER);
			$sql.= " INNER JOIN $access_url_rel_user_table url_rel_user ON (u.user_id=url_rel_user.user_id)";
		}*/
		
		//@todo implement visibility
		
		if (isset ($keyword)) {
			$keyword = Database::escape_string($keyword);		 
			$sql .= " WHERE (g.name LIKE '%".$keyword."%' OR g.description LIKE '%".$keyword."%'  OR  g.url LIKE '%".$keyword."%' )";
		}
	
		$direction = 'ASC';
	    if (!in_array($direction, array('ASC','DESC'))) {
	    	$direction = 'ASC';
	    }
	    
	    $column = intval($column);
	    $from = intval($from);
	    $number_of_items = intval($number_of_items);
	
		//$sql .= " ORDER BY col$column $direction ";
		$sql .= " LIMIT $from,$number_of_items";

		$res = Database::query($sql, __FILE__, __LINE__);
		if (Database::num_rows($res)> 0) {
			while ($row = Database::fetch_array($res,'ASSOC')) { 
				if (!in_array($row['id'], $return)) {			
					$return[$row['id']] = $row;
				}
			}
		}
		return $return;
	}
	
	
	/**
	 * Creates new group pictures in various sizes of a user, or deletes user pfotos.
	 * Note: This method relies on configuration setting from dokeos/main/inc/conf/profile.conf.php
	 * @param	int	The group id 
	 * @param	string $file			The common file name for the newly created pfotos. It will be checked and modified for compatibility with the file system.
	 * If full name is provided, path component is ignored.
	 * If an empty name is provided, then old user photos are deleted only, @see UserManager::delete_user_picture() as the prefered way for deletion.
	 * @param	string		$source_file	The full system name of the image from which user photos will be created.
	 * @return	string/bool	Returns the resulting common file name of created images which usually should be stored in database.
	 * When deletion is recuested returns empty string. In case of internal error or negative validation returns FALSE.
	 */
	public static function update_group_picture($group_id, $file = null, $source_file = null) {

		// Validation 1.
		if (empty($group_id)) {
			return false;
		}
		$delete = empty($file);
		if (empty($source_file)) {
			$source_file = $file;
		}

		// Configuration options about user photos.
		require_once api_get_path(CONFIGURATION_PATH).'profile.conf.php';

		// User-reserved directory where photos have to be placed.
		$path_info = self::get_group_picture_path_by_id($group_id, 'system', true);
		$path = $path_info['dir'];
		// If this directory does not exist - we create it.
		if (!file_exists($path)) {
			$perm = api_get_setting('permissions_for_new_directories');
			$perm = octdec(!empty($perm) ? $perm : '0770');
			@mkdir($path, $perm, true);
		}

		// The old photos (if any).
		$old_file = $path_info['file'];

		// Let us delete them.
		if (!empty($old_file)) {
			if (KEEP_THE_OLD_IMAGE_AFTER_CHANGE) {
				$prefix = 'saved_'.date('Y_m_d_H_i_s').'_'.uniqid('').'_';
				@rename($path.'small_'.$old_file, $path.$prefix.'small_'.$old_file);
				@rename($path.'medium_'.$old_file, $path.$prefix.'medium_'.$old_file);
				@rename($path.'big_'.$old_file, $path.$prefix.'big_'.$old_file);
				@rename($path.$old_file, $path.$prefix.$old_file);
			} else {
				@unlink($path.'small_'.$old_file);
				@unlink($path.'medium_'.$old_file);
				@unlink($path.'big_'.$old_file);
				@unlink($path.$old_file);
			}
		}

		// Exit if only deletion has been requested. Return an empty picture name.
		if ($delete) {
			return '';
		}

		// Validation 2.
		$allowed_types = array('jpg', 'jpeg', 'png', 'gif');
		$file = str_replace('\\', '/', $file);
		$filename = (($pos = strrpos($file, '/')) !== false) ? substr($file, $pos + 1) : $file;
		$extension = strtolower(substr(strrchr($filename, '.'), 1));
		if (!in_array($extension, $allowed_types)) {
			return false;
		}

		// This is the common name for the new photos.
		if (KEEP_THE_NAME_WHEN_CHANGE_IMAGE && !empty($old_file)) {
			$old_extension = strtolower(substr(strrchr($old_file, '.'), 1));
			$filename = in_array($old_extension, $allowed_types) ? substr($old_file, 0, -strlen($old_extension)) : $old_file;
			$filename = (substr($filename, -1) == '.') ? $filename.$extension : $filename.'.'.$extension;
		} else {
			$filename = replace_dangerous_char($filename);
			if (PREFIX_IMAGE_FILENAME_WITH_UID) {
				$filename = uniqid('').'_'.$filename;
			}
			// We always prefix user photos with user ids, so on setting
			// api_get_setting('split_users_upload_directory') === 'true'
			// the correspondent directories to be found successfully.
			$filename = $group_id.'_'.$filename;
		}

		// Storing the new photos in 4 versions with various sizes.

		$picture_info = @getimagesize($source_file);
		$type = $picture_info[2];
		$small = self::resize_picture($source_file, 22);
		$medium = self::resize_picture($source_file, 85);
		$normal = self::resize_picture($source_file, 200);
		$big = new image($source_file); // This is the original picture.

		$ok = false;
		$detected = array(1 => 'GIF', 2 => 'JPG', 3 => 'PNG');
		if (in_array($type, array_keys($detected))) {
			$ok = $small->send_image($detected[$type], $path.'small_'.$filename)
				&& $medium->send_image($detected[$type], $path.'medium_'.$filename)
				&& $normal->send_image($detected[$type], $path.$filename)
				&& $big->send_image($detected[$type], $path.'big_'.$filename);
		}
		return $ok ? $filename : false;
	}
	
	/**
	 * Gets the group picture URL or path from group ID (returns an array).
	 * The return format is a complete path, enabling recovery of the directory
	 * with dirname() or the file with basename(). This also works for the
	 * functions dealing with the user's productions, as they are located in
	 * the same directory.
	 * @param	integer	User ID
	 * @param	string	Type of path to return (can be 'none', 'system', 'rel', 'web')
	 * @param	bool	Whether we want to have the directory name returned 'as if' there was a file or not (in the case we want to know which directory to create - otherwise no file means no split subdir)
	 * @param	bool	If we want that the function returns the /main/img/unknown.jpg image set it at true
	 * @return	array 	Array of 2 elements: 'dir' and 'file' which contain the dir and file as the name implies if image does not exist it will return the unknow image if anonymous parameter is true if not it returns an empty er's
	 */
	public static function get_group_picture_path_by_id($id, $type = 'none', $preview = false, $anonymous = false) {

		switch ($type) {
			case 'system': // Base: absolute system path.
				$base = api_get_path(SYS_CODE_PATH);
				break;
			case 'rel': // Base: semi-absolute web path (no server base).
				$base = api_get_path(REL_CODE_PATH);
				break;
			case 'web': // Base: absolute web path.
				$base = api_get_path(WEB_CODE_PATH);
				break;
			case 'none':
			default: // Base: empty, the result path below will be relative.
				$base = '';
		}

		if (empty($id) || empty($type)) {
			return $anonymous ? array('dir' => $base.'img/', 'file' => 'unknown.jpg') : array('dir' => '', 'file' => '');
		}

		$id = intval($id);

		$group_table = Database :: get_main_table(TABLE_MAIN_GROUP);
		$sql = "SELECT picture_uri FROM $group_table WHERE id=".$id;
		$res = Database::query($sql, __FILE__, __LINE__);

		if (!Database::num_rows($res)) {
			return $anonymous ? array('dir' => $base.'img/', 'file' => 'unknown.jpg') : array('dir' => '', 'file' => '');
		}

		$user = Database::fetch_array($res);
		$picture_filename = trim($user['picture_uri']);

		if (api_get_setting('split_users_upload_directory') === 'true') {
			if (!empty($picture_filename)) {
				$dir = $base.'upload/users/groups/'.substr($picture_filename, 0, 1).'/'.$id.'/';
			} elseif ($preview) {
				$dir = $base.'upload/users/groups/'.substr((string)$id, 0, 1).'/'.$id.'/';
			} else {
				$dir = $base.'upload/users/groups/'.$id.'/';
			}
		} else {
			$dir = $base.'upload/users/groups/'.$id.'/';
		}
		if (empty($picture_filename) && $anonymous) {
			return array('dir' => $base.'img/', 'file' => 'unknown.jpg');
		}
		return array('dir' => $dir, 'file' => $picture_filename);
	}
	
	/**
	 * Resize a picture
	 *
	 * @param  string file picture
	 * @param  int size in pixels
	 * @return obj image object
	 */
	public static function resize_picture($file, $max_size_for_picture) {
		if (!class_exists('image')) {
			require_once api_get_path(LIBRARY_PATH).'image.lib.php';
		}
	 	$temp = new image($file);
	 	$picture_infos = api_getimagesize($file);
		if ($picture_infos[0] > $max_size_for_picture) {
			$thumbwidth = $max_size_for_picture;
			if (empty($thumbwidth) or $thumbwidth == 0) {
				$thumbwidth = $max_size_for_picture;
			}
			$new_height = round(($thumbwidth / $picture_infos[0]) * $picture_infos[1]);
			if ($new_height > $max_size_for_picture)
			$new_height = $thumbwidth;
			$temp->resize($thumbwidth, $new_height, 0);
		}
		return $temp;
	}
	
	/**
     * Gets the current group image
     * @param string group id
     * @param string picture group name
     * @param string height
     * @param string picture size it can be small_,  medium_  or  big_
     * @param string style css
     * @return array with the file and the style of an image i.e $array['file'] $array['style']
     */
   public static function get_picture_group($id, $picture_file, $height, $size_picture = 'medium_', $style = '') {
    	$patch_profile = 'upload/users/groups/';
    	$picture = array();
    	$picture['style'] = $style;
    	if ($picture_file == 'unknown.jpg') {
    		$picture['file'] = api_get_path(WEB_CODE_PATH).'img/'.$picture_file;
    		return $picture;
    	}
        $image_array_sys = self::get_group_picture_path_by_id($id, 'system', false, true);
        $image_array = self::get_group_picture_path_by_id($id, 'web', false, true);
        $file = $image_array_sys['dir'].$size_picture.$picture_file;
    	if (file_exists($file)) {
            $picture['file'] = $image_array['dir'].$size_picture.$picture_file;
			$picture['style'] = '';
			if ($height > 0) {
				$dimension = api_getimagesize($picture['file']);
				$margin = (($height - $dimension[1]) / 2);
				//@ todo the padding-top should not be here
				$picture['style'] = ' style="padding-top:'.$margin.'px; width:'.$dimension[0].'px; height:'.$dimension[1].';" ';
			}
		} else {
			//$file = api_get_path(SYS_CODE_PATH).$patch_profile.$user_id.'/'.$picture_file;
            $file = $image_array_sys['dir'].$picture_file;
			if (file_exists($file) && !is_dir($file)) {
				$picture['file'] = $image_array['dir'].$picture_file;
			} else {
				$picture['file'] = api_get_path(WEB_CODE_PATH).'img/unknown_group.png';
			}
		}
		return $picture;
    }
    
	public static function delete_group_picture($user_id) {
		return self::update_group_picture($user_id);
	}	
}
?>