<?php // $Id: document.php 16494 2008-10-10 22:07:36Z yannoo $
 
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2008 Dokeos SPRL
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) various contributors

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, rue du Corbeau, 108, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/

/**
 * Blog class
 * Contains several functions dealing with displaying,
 * editing,... of a blog
 *
 * @version 1.0
 * @package dokeos.blogs
 * @author Toon Keppens <toon@vi-host.net>
 *
 */
class Blog
{
	/**
	 * Get the title of a blog
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 *
	 * @return String Blog Title
	 */
	function get_blog_title($blog_id)
	{
		if(is_numeric($blog_id))
		{
			// init
			$tbl_blogs = Database::get_course_table(TABLE_BLOGS);

			$sql = "
				SELECT `blog_name`
				FROM " . $tbl_blogs . "
				WHERE `blog_id` = " . mysql_real_escape_string((int)$blog_id);

			$result = api_sql_query($sql, __FILE__, __LINE__);
			$blog = mysql_fetch_array($result);

			return stripslashes($blog['blog_name']);
		}
	}


	/**
	 * Get the description of a blog
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 *
	 * @return String Blog description
	 */
	function get_blog_subtitle($blog_id)
	{
		// init
		$tbl_blogs = Database::get_course_table(TABLE_BLOGS);
		$sql = "SELECT blog_subtitle FROM $tbl_blogs WHERE blog_id ='".mysql_real_escape_string((int)$blog_id)."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		$blog = mysql_fetch_array($result);

		return stripslashes($blog['blog_subtitle']);
	}


	/**
	 * Get the users of a blog
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 *
	 * @return Array Returns an array with [userid]=>[username]
	 */
	function get_blog_users($blog_id)
	{
		// Database table definitions
		$tbl_users = Database::get_main_table(TABLE_MAIN_USER);
		$tbl_blogs = Database::get_course_table(TABLE_BLOGS);
		$tbl_blogs_rel_user = Database::get_course_table(TABLE_BLOGS_REL_USER);

		// Get blog members
		$sql = "
			SELECT
				user.user_id,
				user.firstname,
				user.lastname
			FROM " . $tbl_blogs_rel_user . " blogs_rel_user
			INNER JOIN " . $tbl_users . " user ON blogs_rel_user.user_id = user.user_id
			WHERE blogs_rel_user.blog_id = '" . mysql_real_escape_string((int)$blog_id)."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);

		$blog_members = array ();

		while($user = mysql_fetch_array($result))
		{
			$blog_members[$user['user_id']] = $user['lastname']." " . $user['firstname'];
		}

		return $blog_members;
	}

	/**
	 * Creates a new blog in the given course
	 * @author Toon Keppens
	 *
	 * @param Integer $course_id Id
	 * @param String $title
	 * @param Text $description
	 *
	 * @return void
	 */
	function create_blog($title, $subtitle)
	{
		global $_user;

		// Tabel definitions
		$tbl_blogs 			= Database::get_course_table(TABLE_BLOGS);
		$tbl_tool 			= Database::get_course_table(TABLE_TOOL_LIST);
		$tbl_blogs_posts 	= Database::get_course_table(TABLE_BLOGS_POSTS);
		$tbl_blogs_tasks 	= Database::get_course_table(TABLE_BLOGS_TASKS);

		// Create the blog
		$sql = "INSERT INTO $tbl_blogs (`blog_name`, `blog_subtitle`, `date_creation`, `visibility` )
					VALUES ('".mysql_real_escape_string($title)."', '".mysql_real_escape_string($subtitle)."', NOW(), '1');";
		api_sql_query($sql, __FILE__, __LINE__);
		$this_blog_id = Database::get_last_insert_id();

		// Make first post. :)
		$sql = "INSERT INTO $tbl_blogs_posts (`title`, `full_text`, `date_creation`, `blog_id`, `author_id` )
					VALUES ('".get_lang("Welcome")."', '" . get_lang('FirstPostText')."', NOW(), '".mysql_real_escape_string((int)$this_blog_id)."', '".mysql_real_escape_string((int)$_user['user_id'])."');";
		api_sql_query($sql, __FILE__, __LINE__);

		// Put it on course homepage
		$sql = "INSERT INTO $tbl_tool (name, link, image, visibility, admin, address, added_tool)
					VALUES ('".mysql_real_escape_string($title)."','blog/blog.php?blog_id=".(int)$this_blog_id."','blog.gif','1','0','pastillegris.gif',0)";
		api_sql_query($sql, __FILE__, __LINE__);

		// Subscribe the teacher to this blog
		Blog::set_user_subscribed((int)$this_blog_id,(int)$_user['user_id']);

		return void;
	}

	/**
	 * Update title and subtitle of a blog in the given course
	 * @author Toon Keppens
	 *
	 * @param Integer $course_id Id
	 * @param String $title
	 * @param Text $description
	 *
	 * @return void
	 */
	function edit_blog($blog_id, $title, $subtitle)
	{
		global $_user;

		// Table definitions
		$tbl_blogs = Database::get_course_table(TABLE_BLOGS);
		$tbl_tool = Database::get_course_table(TABLE_TOOL_LIST);

		// Update the blog
		$sql = "UPDATE $tbl_blogs SET blog_name = '".mysql_real_escape_string($title)."',	blog_subtitle = '".mysql_real_escape_string($subtitle)."' WHERE blog_id ='".mysql_real_escape_string((int)$blog_id)."' LIMIT 1";
		api_sql_query($sql, __FILE__, __LINE__);
		$this_blog_id = Database::get_last_insert_id();

		// Update course homepage link
		$sql = "UPDATE $tbl_tool SET name = '".mysql_real_escape_string($title)."' WHERE link = 'blog/blog.php?blog_id=".mysql_real_escape_string((int)$blog_id)."' LIMIT 1";
		api_sql_query($sql, __FILE__, __LINE__);

		return void;
	}

	/**
	 * Deletes a blog and it's posts from the course database
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 *
	 * @return void
	 */
	function delete_blog($blog_id)
	{
		// Init
		$tbl_blogs 			= Database::get_course_table(TABLE_BLOGS);
		$tbl_blogs_posts 	= Database::get_course_table(TABLE_BLOGS_POSTS);
		$tbl_blogs_comment 	= Database::get_course_table(TABLE_BLOGS_COMMENTS);
		$tbl_blogs_tasks 	= Database::get_course_table(TABLE_BLOGS_TASKS);
		$tbl_tool 			= Database::get_course_table(TABLE_TOOL_LIST);
		$tbl_blogs_rating 	= Database::get_course_table(TABLE_BLOGS_RATING);
		$tbl_blogs_attachment = Database::get_course_table(TABLE_BLOGS_ATTACHMENT);		
		
		// Delete posts from DB and the attachments 
		delete_all_blog_attachment($blog_id);
		
		//Delete comments
		$sql = "DELETE FROM $tbl_blogs_comment WHERE blog_id ='".(int)$blog_id."'";
   		api_sql_query($sql, __FILE__, __LINE__);	
   					
		// Delete posts
   		$sql = "DELETE FROM $tbl_blogs_posts WHERE blog_id ='".(int)$blog_id."'";
   		api_sql_query($sql, __FILE__, __LINE__); 		
				
		// Delete tasks
		$sql = "DELETE FROM $tbl_blogs_tasks WHERE blog_id ='".(int)$blog_id."'";
		api_sql_query($sql, __FILE__, __LINE__);

		// Delete ratings
		$sql = "DELETE FROM $tbl_blogs_rating WHERE blog_id ='".(int)$blog_id."'";
		api_sql_query($sql, __FILE__, __LINE__);

		// Delete blog
		$sql ="DELETE FROM $tbl_blogs WHERE blog_id ='".(int)$blog_id."'";
		api_sql_query($sql, __FILE__, __LINE__);

		// Delete from course homepage
		$sql = "DELETE FROM $tbl_tool WHERE link = 'blog/blog.php?blog_id=".(int)$blog_id."'";
		api_sql_query($sql, __FILE__, __LINE__);
	
		return void;
	}

	/**
	 * Creates a new post in a given blog
	 * @author Toon Keppens
	 *
	 * @param String $title
	 * @param String $full_text
	 * @param Integer $blog_id
	 *
	 * @return void
	 */
	function create_post($title, $full_text, $file_comment, $blog_id)
	{
		global $_user;
		global $_course;
		global $blog_table_attachment;
		
		$upload_ok=true;
		$has_attachment=false;

		if(!empty($_FILES['user_upload']['name']))
		{
			require_once('fileUpload.lib.php'); 
			$upload_ok = process_uploaded_file($_FILES['user_upload']);
			$has_attachment=true;
		}
		
		if($upload_ok)
		{	
			// Table Definitions
			$tbl_blogs_posts = Database::get_course_table(TABLE_BLOGS_POSTS);
	
			// Create the post
			$sql = "INSERT INTO " . $tbl_blogs_posts." (`title`, `full_text`, `date_creation`, `blog_id`, `author_id` )
					VALUES ('".Database::escape_string($title)."', '".Database::escape_string($full_text)."', NOW(), '".(int)$blog_id."', '".(int)$_user['user_id']."');";
						
			api_sql_query($sql, __FILE__, __LINE__);
			$last_post_id=Database::insert_id();
			
			if ($has_attachment)
			{			
				$courseDir   = $_course['path'].'/upload/blog';
				$sys_course_path = api_get_path(SYS_COURSE_PATH);		
				$updir = $sys_course_path.$courseDir;
							
				// Try to add an extension to the file if it hasn't one
				$new_file_name = add_ext_on_mime(stripslashes($_FILES['user_upload']['name']), $_FILES['user_upload']['type']);	
			
				// user's file name
				$file_name =$_FILES['user_upload']['name'];
							
				if (!filter_extension($new_file_name)) 
				{
					Display :: display_error_message(get_lang('UplUnableToSaveFileFilteredExtension'));				
				}
				else
				{
					$new_file_name = uniqid('');						
					$new_path=$updir.'/'.$new_file_name;
					$result= @move_uploaded_file($_FILES['user_upload']['tmp_name'], $new_path);
					$comment=Database::escape_string($file_comment);				
									
					// Storing the attachments if any
					if ($result)
					{					
						$sql='INSERT INTO '.$blog_table_attachment.'(filename,comment, path, post_id,size, blog_id,comment_id) '.
							 "VALUES ( '".Database::escape_string($file_name)."', '".Database::escape_string($comment)."', '".Database::escape_string($new_file_name)."' , '".$last_post_id."', '".$_FILES['user_upload']['size']."',  '".$blog_id."', '0' )";						
						$result=api_sql_query($sql, __LINE__, __FILE__);					
						$message.=' / '.get_lang('AttachmentUpload');			
					}			
				}			 
			}
		}
		else
		{
			Display::display_error_message(get_lang('UplNoFileUploaded'));
		}	

		return void;
	}

	/**
	 * Edits a post in a given blog
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 * @param String $title
	 * @param String $full_text
	 * @param Integer $blog_id
	 *
	 * @return void
	 */
	function edit_post($post_id, $title, $full_text, $blog_id)
	{
		// Init
		$tbl_blogs_posts = Database::get_course_table(TABLE_BLOGS_POSTS);

		// Create the post
		$sql = "UPDATE $tbl_blogs_posts SET title = '" . mysql_real_escape_string($title)."', full_text = '" . mysql_real_escape_string($full_text)."' WHERE post_id ='".(int)$post_id."' AND blog_id ='".(int)$blog_id."' LIMIT 1 ;";
		api_sql_query($sql, __FILE__, __LINE__);

		return void;
	}

	/**
	 * Deletes an article and it's comments
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 * @param Integer $post_id
	 *
	 * @return void
	 */
	function delete_post($blog_id, $post_id)
	{
		// Init
		$tbl_blogs_posts = Database::get_course_table(TABLE_BLOGS_POSTS);
		$tbl_blogs_comments = Database::get_course_table(TABLE_BLOGS_COMMENTS);
		$tbl_blogs_rating = Database::get_course_table(TABLE_BLOGS_RATING);

		// Delete ratings on this comment
		$sql = "DELETE FROM $tbl_blogs_rating WHERE blog_id = '".(int)$blog_id."' AND item_id = '".(int)$post_id."' AND rating_type = 'post'";
		api_sql_query($sql, __FILE__, __LINE__);

		// Delete the post
		$sql = "DELETE FROM $tbl_blogs_posts WHERE `post_id` = '".(int)$post_id."'";
		api_sql_query($sql, __FILE__, __LINE__);

		// Delete the comments
		$sql = "DELETE FROM $tbl_blogs_comments WHERE `post_id` = '".(int)$post_id."' AND `blog_id` = '".(int)$blog_id."'";
		api_sql_query($sql, __FILE__, __LINE__);
					
		// Delete posts and attachments
		delete_all_blog_attachment($blog_id,$post_id);	

		return void;
	}

	/**
	 * Creates a comment on a post in a given blog
	 * @author Toon Keppens
	 *
	 * @param String $title
	 * @param String $full_text
	 * @param Integer $blog_id
	 * @param Integer $post_id
	 * @param Integer $parent_id
	 *
	 * @return void
	 */
	function create_comment($title, $full_text, $file_comment,$blog_id, $post_id, $parent_id, $task_id = 'NULL')
	{
		global $_user;		
		global $_course;				
		global $blog_table_attachment;
		
		$upload_ok=true;
		$has_attachment=false;

		if(!empty($_FILES['user_upload']['name']))
		{
			require_once('fileUpload.lib.php'); 
			$upload_ok = process_uploaded_file($_FILES['user_upload']);
			$has_attachment=true;
		}
		
		if($upload_ok)
		{	
			// Table Definition
			$tbl_blogs_comments = Database::get_course_table(TABLE_BLOGS_COMMENTS);
	
			// Create the comment
			$sql = "INSERT INTO $tbl_blogs_comments (`title`, `comment`, `author_id`, `date_creation`, `blog_id`, `post_id`, `parent_comment_id`, `task_id` )
						VALUES ('".mysql_real_escape_string($title)."', '".mysql_real_escape_string($full_text)."', '".(int)$_user['user_id']."', NOW(), '".(int)$blog_id."', '".(int)$post_id."', '".(int)$parent_id."', '".(int)$task_id."')";
			api_sql_query($sql, __FILE__, __LINE__);
	
			// Empty post values, or they are shown on the page again
			$_POST['comment_title'] = "";
			$_POST['comment_text'] = "";
			
			$last_id=Database::insert_id();
			
			if ($has_attachment)
			{			
				$courseDir   = $_course['path'].'/upload/blog';
				$sys_course_path = api_get_path(SYS_COURSE_PATH);		
				$updir = $sys_course_path.$courseDir;
							
				// Try to add an extension to the file if it hasn't one
				$new_file_name = add_ext_on_mime(stripslashes($_FILES['user_upload']['name']), $_FILES['user_upload']['type']);	
			
				// user's file name 
				$file_name =$_FILES['user_upload']['name'];
							
				if (!filter_extension($new_file_name)) 
				{
					Display :: display_error_message(get_lang('UplUnableToSaveFileFilteredExtension'));				
				}
				else
				{
					$new_file_name = uniqid('');						
					$new_path=$updir.'/'.$new_file_name;
					$result= @move_uploaded_file($_FILES['user_upload']['tmp_name'], $new_path);
					$comment=Database::escape_string($file_comment);				
									
					// Storing the attachments if any
					if ($result)
					{					
						$sql='INSERT INTO '.$blog_table_attachment.'(filename,comment, path, post_id,size,blog_id,comment_id) '.
							 "VALUES ( '".Database::escape_string($file_name)."', '".Database::escape_string($comment)."', '".Database::escape_string($new_file_name)."' , '".$post_id."', '".$_FILES['user_upload']['size']."',  '".$blog_id."', '".$last_id."'  )";						
						$result=api_sql_query($sql, __LINE__, __FILE__);					
						$message.=' / '.get_lang('AttachmentUpload');			
					}			
				}			 
			}
		}
		
		
	
		return void;
	}

	/**
	 * Deletes a comment from a blogpost
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 * @param Integer $comment_id
	 *
	 * @return void
	 */
	function delete_comment($blog_id, $post_id, $comment_id)
	{
		// Init
		$tbl_blogs_comments = Database::get_course_table(TABLE_BLOGS_COMMENTS);
		$tbl_blogs_rating = Database::get_course_table(TABLE_BLOGS_RATING);
		
		delete_all_blog_attachment($blog_id,$post_id,$comment_id);
		
		// Delete ratings on this comment
		$sql = "DELETE FROM $tbl_blogs_rating WHERE blog_id = '".(int)$blog_id."' AND item_id = '".(int)$comment_id."' AND rating_type = 'comment'";
		api_sql_query($sql, __FILE__, __LINE__);

		// select comments that have the selected comment as their parent
		$sql = "SELECT comment_id FROM $tbl_blogs_comments WHERE parent_comment_id = '".(int)$comment_id."'";		
		$result = api_sql_query($sql, __FILE__, __LINE__);
			
		// Delete them recursively
		while($comment = mysql_fetch_array($result))
		{					
			Blog::delete_comment($blog_id,$post_id,$comment['comment_id']);					
		}		

		// Finally, delete the selected comment to
		$sql = "DELETE FROM $tbl_blogs_comments WHERE `comment_id` = '".(int)$comment_id."'";				
		api_sql_query($sql, __FILE__, __LINE__);
		return void;
	}

	/**
	 * Creates a new task in a blog
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 * @param String $title
	 * @param String $description
	 * @param String $color
	 *
	 * @return void
	 */
	function create_task($blog_id, $title, $description, $articleDelete, $articleEdit, $commentsDelete, $color)
	{
		// Init
		$tbl_blogs_tasks = Database::get_course_table(TABLE_BLOGS_TASKS);
		$tbl_tasks_permissions = Database::get_course_table(TABLE_BLOGS_TASKS_PERMISSIONS);

		// Create the task
		$sql = "INSERT INTO $tbl_blogs_tasks (`blog_id`, `title`, `description`, `color`, `system_task` )
					VALUES ('".(int)$blog_id."', '" . mysql_real_escape_string($title)."', '" . mysql_real_escape_string($description)."', '" . mysql_real_escape_string($color)."', '0');";
		api_sql_query($sql, __FILE__, __LINE__);

		$task_id = mysql_insert_id();
		$tool = 'BLOG_' . $blog_id;

		if($articleDelete == 'on')
		{
			$sql = "
				INSERT INTO " . $tbl_tasks_permissions . " (
					`task_id`,
					`tool`,
					`action`
				) VALUES (
					'" . (int)$task_id . "',
					'" . mysql_real_escape_string($tool) . "',
					'article_delete'
				)";

			api_sql_query($sql, __FILE__, __LINE__);
		}

		if($articleEdit == 'on')
		{
			$sql = "
				INSERT INTO " . $tbl_tasks_permissions . " (
					`task_id`,
					`tool`,
					`action`
				) VALUES (
					'" . (int)$task_id . "',
					'" . mysql_real_escape_string($tool) . "',
					'article_edit'
				)";

			api_sql_query($sql, __FILE__, __LINE__);
		}

		if($commentsDelete == 'on')
		{
			$sql = "
				INSERT INTO " . $tbl_tasks_permissions . " (
					`task_id`,
					`tool`,
					`action`
				) VALUES (
					'" . (int)$task_id . "',
					'" . mysql_real_escape_string($tool) . "',
					'article_comments_delete'
				)";

			api_sql_query($sql, __FILE__, __LINE__);
		}

		return void;
	}

	/**
	 * Edit a task in a blog
	 * @author Toon Keppens
	 *
	 * @param Integer $task_id
	 * @param String $title
	 * @param String $description
	 * @param String $color
	 *
	 * @return void
	 */
	function edit_task($blog_id, $task_id, $title, $description, $articleDelete, $articleEdit, $commentsDelete, $color)
	{
		// Init
		$tbl_blogs_tasks = Database::get_course_table(TABLE_BLOGS_TASKS);
		$tbl_tasks_permissions = Database::get_course_table(TABLE_BLOGS_TASKS_PERMISSIONS);

		// Create the task
		$sql = "UPDATE $tbl_blogs_tasks SET
					title = '".mysql_real_escape_string($title)."',
					description = '".mysql_real_escape_string($description)."',
					color = '".mysql_real_escape_string($color)."'
				WHERE task_id ='".(int)$task_id."' LIMIT 1";
		api_sql_query($sql, __FILE__, __LINE__);

		$tool = 'BLOG_' . $blog_id;

		$sql = "
			DELETE FROM " . $tbl_tasks_permissions . "
			WHERE `task_id` = '" . (int)$task_id."'";

		api_sql_query($sql, __FILE__, __LINE__);

		if($articleDelete == 'on')
		{
			$sql = "
				INSERT INTO " . $tbl_tasks_permissions . " (
					`task_id`,
					`tool`,
					`action`
				) VALUES (
					'" . (int)$task_id . "',
					'" . mysql_real_escape_string($tool) . "',
					'article_delete'
				)";

			api_sql_query($sql, __FILE__, __LINE__);
		}

		if($articleEdit == 'on')
		{
			$sql = "
				INSERT INTO " . $tbl_tasks_permissions . " (
					`task_id`,
					`tool`,
					`action`
				) VALUES (
					'" . (int)$task_id . "',
					'" . mysql_real_escape_string($tool) . "',
					'article_edit'
				)";

			api_sql_query($sql, __FILE__, __LINE__);
		}

		if($commentsDelete == 'on')
		{
			$sql = "
				INSERT INTO " . $tbl_tasks_permissions . " (
					`task_id`,
					`tool`,
					`action`
				) VALUES (
					'" . (int)$task_id . "',
					'" . mysql_real_escape_string($tool) . "',
					'article_comments_delete'
				)";

			api_sql_query($sql, __FILE__, __LINE__);
		}

		return void;
	}

	/**
	 * Deletes a task from a blog
	 *
	 * @param Integer $blog_id
	 * @param Integer $task_id
	 */
	function delete_task($blog_id, $task_id)
	{
		// Init
		$tbl_blogs_tasks = Database::get_course_table(TABLE_BLOGS_TASKS);

		// Delete posts
		$sql = "DELETE FROM $tbl_blogs_tasks WHERE `blog_id` = '".(int)$blog_id."' AND `task_id` = '".(int)$task_id."'";
		api_sql_query($sql, __FILE__, __LINE__);

		return void;
	}

	/**
	 * Deletes an assigned task from a blog
	 *
	 * @param Integer $blog_id
	 * @param Integer $assignment_id
	 */
	function delete_assigned_task($blog_id, $assignment_id)
	{
		// Init
		$tbl_blogs_tasks_rel_user = Database::get_course_table(TABLE_BLOGS_TASKS_REL_USER);
		$parameters = explode('|',$assignment_id);
		$task_id = $parameters[0];
		$user_id = $parameters[1];

		// Delete posts
		$sql = "DELETE FROM $tbl_blogs_tasks_rel_user WHERE `blog_id` = '".(int)$blog_id."' AND `task_id` = '".(int)$task_id."' AND `user_id` = '".(int)$user_id."'";
		api_sql_query($sql, __FILE__, __LINE__);

		return void;
	}

	/**
	 * Get personal task list
	 * @author Toon Keppens
	 *
	 * @return Returns an unsorted list (<ul>) with the users' tasks
	 */
	function get_personal_task_list()
	{
		global $_user;

		// Init
		$tbl_blogs = Database::get_course_table(TABLE_BLOGS);
		$tbl_blogs_tasks_rel_user = Database::get_course_table(TABLE_BLOGS_TASKS_REL_USER);
		$tbl_blogs_tasks = Database::get_course_table(TABLE_BLOGS_TASKS);

		if($_user['user_id'])
		{
			$sql = "SELECT task_rel_user.*, task.title, blog.blog_name FROM $tbl_blogs_tasks_rel_user task_rel_user
			INNER JOIN $tbl_blogs_tasks task ON task_rel_user.task_id = task.task_id
			INNER JOIN $tbl_blogs blog ON task_rel_user.blog_id = blog.blog_id
			AND blog.blog_id = ".intval($_GET['blog_id'])."
			WHERE task_rel_user.user_id = ".(int)$_user['user_id']." ORDER BY `target_date` ASC";
			$result = api_sql_query($sql, __FILE__, __LINE__);

			if(mysql_numrows($result) > 0)
			{
				echo '<ul>';
				while($mytask = mysql_fetch_array($result))
				{
					echo '<li><a href="blog.php?action=execute_task&amp;blog_id=' . $mytask['blog_id'] . '&amp;task_id='.stripslashes($mytask['task_id']) . '" title="[Blog: '.stripslashes($mytask['blog_name']) . '] ' . get_lang('ExecuteThisTask') . '">'.stripslashes($mytask['title']) . '</a></li>';
				}
				echo '<ul>';
			}
			else
			{
				echo get_lang('NoTasks');
			}
		}
		else
		{
			echo get_lang('NoTasks');
		}

	}

	/**
	 * Changes the visibility of a blog
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 *
	 * @return void
	 */
	function change_blog_visibility($blog_id)
	{
		// Init
		$tbl_blogs = Database::get_course_table(TABLE_BLOGS);
		$tbl_tool = Database::get_course_table(TABLE_TOOL_LIST);

		// Get blog properties
		$sql = "SELECT blog_name, visibility FROM $tbl_blogs WHERE blog_id='".(int)$blog_id."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		$blog = mysql_fetch_array($result);
		$visibility = $blog['visibility'];
		$title = $blog['blog_name'];

		if($visibility == 1)
		{
			// Change visibility state, remove from course home.
			$sql = "UPDATE $tbl_blogs SET `visibility` = '0' WHERE `blog_id` ='".(int)$blog_id."' LIMIT 1";
			$result = api_sql_query($sql, __FILE__, __LINE__);

			$sql = "DELETE FROM $tbl_tool WHERE name = '".mysql_real_escape_string($title)."' LIMIT 1";
			$result = api_sql_query($sql, __FILE__, __LINE__);
		}
		else
		{
			// Change visibility state, add to course home.
			$sql = "UPDATE $tbl_blogs SET `visibility` = '1' WHERE `blog_id` ='".(int)$blog_id."' LIMIT 1";
			$result = api_sql_query($sql, __FILE__, __LINE__);

			$sql = "INSERT INTO $tbl_tool (`name`, `link`, `image`, `visibility`, `admin`, `address`, `added_tool`, `target` )
					VALUES ('".mysql_real_escape_string($title)."', 'blog/blog.php?blog_id=".(int)$blog_id."', 'blog.gif', '1', '0', 'pastillegris.gif', '0', '_self')";
			$result = api_sql_query($sql, __FILE__, __LINE__);
		}

		return void;
	}


	/**
	 * Shows the posts of a blog
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 */
	function display_blog_posts($blog_id, $filter = '1=1', $max_number_of_posts = 20)
	{
		// Init
		$tbl_blogs_posts = Database::get_course_table(TABLE_BLOGS_POSTS);
		$tbl_blogs_comments = Database::get_course_table(TABLE_BLOGS_COMMENTS);
		$tbl_users = Database::get_main_table(TABLE_MAIN_USER);
		global $dateFormatLong;

		// Get posts and authors
		$sql = "SELECT post.*, user.lastname, user.firstname FROM $tbl_blogs_posts post
					INNER JOIN $tbl_users user ON post.author_id = user.user_id
					WHERE post.blog_id = '".(int)$blog_id."'
					AND $filter
					ORDER BY post_id DESC LIMIT 0,".(int)$max_number_of_posts;
		$result = api_sql_query($sql, __FILE__, __LINE__);

		// Display
		if(mysql_num_rows($result) > 0)
		{
			while($blog_post = mysql_fetch_array($result))
			{
				// Get number of comments
				$sql = "SELECT COUNT(1) as number_of_comments FROM $tbl_blogs_comments WHERE blog_id = '".(int)$blog_id."' AND post_id = '" . (int)$blog_post['post_id']."'";
				$tmp = api_sql_query($sql, __FILE__, __LINE__);
				$blog_post_comments = mysql_fetch_array($tmp);

				// Prepare data
				$blog_post_id = $blog_post['post_id'];
				$blog_post_text = make_clickable(stripslashes($blog_post['full_text']));
				$blog_post_date = ucfirst(format_locale_date($dateFormatLong,strtotime($blog_post['date_creation'])));
				$blog_post_time = date('H:i',strtotime($blog_post['date_creation']));

				// Create an introduction text (but keep FULL sentences)
				$limit = 100; //nmbr of words in introduction text
				$introduction_text = "";
				$words = 0;
				$tok = strtok(make_clickable(stripslashes($blog_post['full_text'])), " ");
				//original
				//$tok = strtok(make_clickable(stripslashes(strip_tags($blog_post['full_text'],"<br><p><ol><ul><li><img>"))), " ");
				while($tok)
				{
					$introduction_text .= " $tok";
					$words++;
					// check if the number of words is larger than our limit AND if this token ends with a ! . or ? (meaning end of sentance).
					if(($words >= $limit) && ((substr($tok, -1) == "!")||(substr($tok, -1) == ".")||(substr($tok, -1) == "?")))
					{
						break;
					}
					$tok = strtok(" ");
				}
				
				if($words >= $limit)
				{
					$readMoreLink = ' <span class="link" onclick="document.getElementById(\'blogpost_text_' . $blog_post_id . '\').style.display=\'block\'; document.getElementById(\'blogpost_introduction_' . $blog_post_id . '\').style.display=\'none\'">' . get_lang('ReadMore') . '</span>';
				}
				else
				{
					$readMoreLink = '';
				}
				
				$introduction_text=stripslashes($introduction_text);

				echo '<div class="blogpost">'."\n";
				echo '<span class="blogpost_title"><a href="blog.php?action=view_post&amp;blog_id=' . $blog_id . '&amp;post_id=' . $blog_post['post_id'] . '#add_comment" title="' . get_lang('ReadPost') . '" >'.stripslashes($blog_post['title']) . '</a></span>'."\n";
				echo '<span class="blogpost_date"><a href="blog.php?action=view_post&amp;blog_id=' . $blog_id . '&amp;post_id=' . $blog_post['post_id'] . '#add_comment" title="' . get_lang('ReadPost') . '" >' . $blog_post_date . ' (' . $blog_post_time . ')</a></span>'."\n";
				echo '<span class="blogpost_introduction" id="blogpost_introduction_' . $blog_post_id . '">' . $introduction_text . $readMoreLink . '</span>'."\n";
				echo '<span class="blogpost_text" id="blogpost_text_' . $blog_post_id . '" style="display: none">' . $blog_post_text . '</span>'."\n";
				$file_name_array=get_blog_attachment($blog_id,$blog_post_id,0);
		
				if (!empty($file_name_array))
				{								
					echo '<br /><br />';
					echo Display::return_icon('attachment.gif',get_lang('Attachment'));
					echo '<a href="download.php?file=';		
					echo $file_name_array['path'];	
					echo ' "> '.$file_name_array['filename'].' </a><br />';
					echo '</span>';														
				}				
				echo '<span class="blogpost_info">' . get_lang('Author') . ': ' . $blog_post['lastname'] . ' ' . $blog_post['firstname'] . ' - <a href="blog.php?action=view_post&amp;blog_id=' . $blog_id . '&amp;post_id=' . $blog_post['post_id'] . '#add_comment" title="' . get_lang('ReadPost') . '" >' . get_lang('Comments') . ': ' . $blog_post_comments['number_of_comments'] . '</a></span>'."\n";
				echo '</div>'."\n";
			}					
		}
		else	
		{
			if($filter == '1=1')
			{
				echo get_lang('NoArticles');
			}
			else
			{
				echo get_lang('NoArticleMatches');
			}
		}
}

	/**
	 * Display the search results
	 *
	 * @param Integer $blog_id
	 * @param String $query_string
	 */
	function display_search_results($blog_id, $query_string)
	{
		// Init
		$query_string_parts = explode(' ',$query_string);
		$query_string = array();
		foreach ($query_string_parts as $query_part)
		{
			$query_string[] = " full_text LIKE '%" . $query_part."%' OR title LIKE '%" . $query_part."%' ";
		}
		$query_string = '('.implode('OR',$query_string) . ')';

		// Display the posts
		echo '<span class="blogpost_title">' . get_lang('SearchResults') . '</span>';
		Blog::display_blog_posts($blog_id, $query_string);
	}

	/**
	 * Display posts from a certain date
	 *
	 * @param Integer $blog_id
	 * @param String $query_string
	 */
	function display_day_results($blog_id, $query_string)
	{
		// Init
		$date_output = $query_string;
		$date = explode('-',$query_string);
		$query_string = ' DAYOFMONTH(`date_creation`) =' . $date[2] . ' AND MONTH(`date_creation`) =' . $date[1] . ' AND YEAR(`date_creation`) =' . $date[0];
		global $dateFormatLong;

		// Put date in correct output format
		$date_output = ucfirst(format_locale_date($dateFormatLong,strtotime($date_output)));

		// Display the posts
		echo '<span class="blogpost_title">' . get_lang('PostsOf') . ': ' . $date_output . '</span>';
		Blog::display_blog_posts($blog_id, $query_string);
	}

	/**
	 * Displays a post and his comments
	 *
	 * @param Integer $blog_id
	 * @param Integer $post_id
	 */
	function display_post($blog_id, $post_id)
	{
		// Init
		$tbl_blogs_posts = Database::get_course_table(TABLE_BLOGS_POSTS);
		$tbl_blogs_comments = Database::get_course_table(TABLE_BLOGS_COMMENTS);
		$tbl_users = Database::get_main_table(TABLE_MAIN_USER);

		global $charset,$dateFormatLong;

		// Get posts and author
		$sql = "SELECT post.*, user.lastname, user.firstname FROM $tbl_blogs_posts post
					INNER JOIN $tbl_users user ON post.author_id = user.user_id
					WHERE post.blog_id = '".(int)$blog_id."'
					AND post.post_id = '".(int)$post_id."'
					ORDER BY post_id DESC";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		$blog_post = mysql_fetch_array($result);

		// Get number of comments
		$sql = "SELECT COUNT(1) as number_of_comments FROM $tbl_blogs_comments WHERE blog_id = '".(int)$blog_id."' AND post_id = '".(int)$post_id."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		$blog_post_comments = mysql_fetch_array($result);

		// Prepare data
		$blog_post_text = make_clickable(stripslashes($blog_post['full_text']));
		$blog_post_date = ucfirst(format_locale_date($dateFormatLong,strtotime($blog_post['date_creation'])));
		$blog_post_time = date('H:m',strtotime($blog_post['date_creation']));
		$blog_post_actions = "";

		$task_id = (isset($_GET['task_id']) && is_numeric($_GET['task_id'])) ? $_GET['task_id'] : 0;

		if(api_is_allowed('BLOG_' . $blog_id, 'article_edit', $task_id))
			$blog_post_actions .= '<a href="blog.php?action=edit_post&amp;blog_id=' . $blog_id . '&amp;post_id=' . $post_id . '&amp;article_id=' . $blog_post['post_id'] . '&amp;task_id=' . $task_id . '" title="' . get_lang('EditThisPost') . '"><img src="../img/edit.gif" /></a>';

		if(api_is_allowed('BLOG_' . $blog_id, 'article_delete', $task_id))
			$blog_post_actions .= '<a href="blog.php?action=view_post&amp;blog_id=' . $blog_id . '&amp;post_id=' . $post_id . '&amp;do=delete_article&amp;article_id=' . $blog_post['post_id'] . '&amp;task_id=' . $task_id . '" title="' . get_lang('DeleteThisArticle') . '" onclick="javascript:if(!confirm(\''.addslashes(htmlentities(get_lang("ConfirmYourChoice"),ENT_QUOTES,$charset)). '\')) return false;"><img src="../img/delete.gif" border="0" /></a>';

		if(api_is_allowed('BLOG_' . $blog_id, 'article_rate'))
			$rating_select = Blog::display_rating_form('post',$blog_id,$post_id);

		$blog_post_text=stripslashes($blog_post_text);
		
		// Display post
		echo '<div class="blogpost">';
		echo '<span class="blogpost_title"><a href="blog.php?action=view_post&amp;blog_id=' . $blog_id . '&amp;post_id=' . $blog_post['post_id'] . '" title="' . get_lang('ReadPost') . '" >'.stripslashes($blog_post['title']) . '</a></span>';
		echo '<span class="blogpost_date">' . $blog_post_date . ' (' . $blog_post_time . ')</span>';
		echo '<span class="blogpost_text">' . $blog_post_text . '</span><br />';
		
		$file_name_array=get_blog_attachment($blog_id,$post_id);
		
		if (!empty($file_name_array))
		{			
			echo ' <br />';
			echo Display::return_icon('attachment.gif',get_lang('Attachment'));
			echo '<a href="download.php?file=';		
			echo $file_name_array['path'];	
			echo ' "> '.$file_name_array['filename'].' </a>';					
			echo '</span>';		
			echo '<span class="attachment_comment">';	
			echo $file_name_array['comment'];
			echo '</span>';	
			echo '<br />';
		}			
			
		echo '<span class="blogpost_info">' . get_lang('Author') . ': ' . $blog_post['lastname'] . ' ' . $blog_post['firstname'] . ' - ' . get_lang('Comments') . ': ' . $blog_post_comments['number_of_comments'] . ' - ' . get_lang('Rating') . ': '.Blog::display_rating('post',$blog_id,$post_id) . $rating_select . '</span>';
		echo '<span class="blogpost_actions">' . $blog_post_actions . '</span>';
		echo '</div>';

		// Display comments if there are any
		if($blog_post_comments['number_of_comments'] > 0)
		{
			echo '<div class="comments">';
				echo '<span class="blogpost_title">' . get_lang('Comments') . '</span><br />';
				Blog::get_threaded_comments(0, 0, $blog_id, $post_id, $task_id);
			echo '</div>';
		}

		// Display comment form
		if(api_is_allowed('BLOG_' . $blog_id, 'article_comments_add'))
		{
			Blog::display_new_comment_form($blog_id, $post_id, $blog_post['title']);
		}
	}

	/**
	 * Adds rating to a certain post or comment
	 * @author Toon Keppens
	 *
	 * @param String $type
	 * @param Integer $blog_id
	 * @param Integer $item_id
	 * @param Integer $rating
	 *
	 * @return Boolean success
	 */
	function add_rating($type, $blog_id, $item_id, $rating)
	{
		global $_user;

		// Init
		$tbl_blogs_rating = Database::get_course_table(TABLE_BLOGS_RATING);

		// Check if the user has already rated this post/comment
		$sql = "SELECT rating_id FROM $tbl_blogs_rating
					WHERE blog_id = '".(int)$blog_id."'
					AND item_id = '".(int)$item_id."'
					AND rating_type = '".mysql_real_escape_string($type)."'
					AND user_id = '".(int)$_user['user_id']."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);

		if(mysql_num_rows($result) == 0) // Add rating
		{
			$sql = "INSERT INTO $tbl_blogs_rating ( `blog_id`, `rating_type`, `item_id`, `user_id`, `rating` )
						VALUES ('".(int)$blog_id."', '".mysql_real_escape_string($type)."', '".(int)$item_id."', '".(int)$_user['user_id']."', '".mysql_real_escape_string($rating)."')";
			$result = api_sql_query($sql, __FILE__, __LINE__);
			return true;
		}
		else // Return
		{
			return false;
		}
	}


	function display_rating($type, $blog_id, $item_id)
	{
		$tbl_blogs_rating = Database::get_course_table(TABLE_BLOGS_RATING);

		// Calculate rating
		$sql = "SELECT AVG(rating) as rating FROM $tbl_blogs_rating WHERE blog_id = '".(int)$blog_id."' AND item_id = '".(int)$item_id."' AND rating_type = '".mysql_real_escape_string($type)."' ";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		$result = mysql_fetch_array($result);
		return round($result['rating'], 2);
	}

	/**
	 * Shows the rating form if not already rated by that user
	 * @author Toon Keppens
	 *
	 * @param String $type
	 * @param Integer $blog_id
	 * @param Integer $item_id
	 *
	 */
	function display_rating_form($type, $blog_id, $post_id, $comment_id = NULL)
	{
		global $_user;

		// Init
		$tbl_blogs_rating = Database::get_course_table(TABLE_BLOGS_RATING);

		if($type == 'post')
		{
			// Check if the user has already rated this post
			$sql = "SELECT rating_id FROM $tbl_blogs_rating
					WHERE blog_id = '".(int)$blog_id."'
					AND item_id = '".(int)$post_id."'
					AND rating_type = '".mysql_real_escape_string($type)."'
					AND user_id = '".(int)$_user['user_id']."'";
			$result = api_sql_query($sql, __FILE__, __LINE__);

			if(mysql_num_rows($result) == 0) // Add rating
			{
				return ' - ' . get_lang('RateThis') . ': <form method="get" action="blog.php" style="display: inline" id="frm_rating_' . $type . '_' . $post_id . '" name="frm_rating_' . $type . '_' . $post_id . '"><select name="rating" onchange="document.forms[\'frm_rating_' . $type . '_' . $post_id . '\'].submit()"><option value="">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select><input type="hidden" name="action" value="view_post" /><input type="hidden" name="type" value="' . $type . '" /><input type="hidden" name="do" value="rate" /><input type="hidden" name="blog_id" value="' . $blog_id . '" /><input type="hidden" name="post_id" value="' . $post_id . '" /></form>';
			}
			else // Return
			{
				return '';
			}
		}
		if($type = 'comment')
		{
			// Check if the user has already rated this comment
			$sql = "SELECT rating_id FROM $tbl_blogs_rating
					WHERE blog_id = '".(int)$blog_id ."'
					AND item_id = '".(int)$comment_id."'
					AND rating_type = '".mysql_real_escape_string($type)."'
					AND user_id = '".(int)$_user['user_id']."'";
			$result = api_sql_query($sql, __FILE__, __LINE__);

			if(mysql_num_rows($result) == 0) // Add rating
			{
				return ' - ' . get_lang('RateThis') . ': <form method="get" action="blog.php" style="display: inline" id="frm_rating_' . $type . '_' . $comment_id . '" name="frm_rating_' . $type . '_' . $comment_id . '"><select name="rating" onchange="document.forms[\'frm_rating_' . $type . '_' . $comment_id . '\'].submit()"><option value="">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select><input type="hidden" name="action" value="view_post" /><input type="hidden" name="type" value="' . $type . '" /><input type="hidden" name="do" value="rate" /><input type="hidden" name="blog_id" value="' . $blog_id . '" /><input type="hidden" name="post_id" value="' . $post_id . '" /><input type="hidden" name="comment_id" value="' . $comment_id . '" /></form>';
			}
			else // Return
			{
				return '';
			}
		}
	}

	/**
	 * This functions gets all replys to a post, threaded.
	 *
	 * @param Integer $current
	 * @param Integer $current_level
	 * @param Integer $blog_id
	 * @param Integer $post_id
	 */
	function get_threaded_comments($current = 0, $current_level = 0, $blog_id, $post_id, $task_id = 0)
	{
		// Init
		$tbl_blogs_comments = Database::get_course_table(TABLE_BLOGS_COMMENTS);
		$tbl_users = Database::get_main_table(TABLE_MAIN_USER);
		$tbl_blogs_tasks = Database::get_course_table(TABLE_BLOGS_TASKS);
		global $charset,$dateFormatLong;

		// Select top level comments
		$next_level = $current_level + 1;
		$sql = "SELECT comments.*, user.lastname, user.firstname, task.color
					FROM $tbl_blogs_comments comments
						INNER JOIN $tbl_users user ON comments.author_id = user.user_id
						LEFT JOIN $tbl_blogs_tasks task ON comments.task_id = task.task_id
					WHERE parent_comment_id = $current
						AND comments.blog_id = '".(int)$blog_id."'
						AND comments.post_id = '".(int)$post_id."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);

		while($comment = mysql_fetch_array($result))
		{
			// Select the children recursivly
			$tmp = "SELECT comments.*, user.lastname, user.firstname FROM $tbl_blogs_comments comments
					INNER JOIN $tbl_users user ON comments.author_id = user.user_id
					WHERE comment_id = $current
					AND blog_id = '".(int)$blog_id."'
					AND post_id = '".(int)$post_id."'";
			$tmp = api_sql_query($tmp, __FILE__, __LINE__);
			$tmp = mysql_fetch_array($tmp);
			$parent_cat = $tmp['parent_comment_id'];
			$border_color = '';

			// Prepare data
			$comment_text = make_clickable(stripslashes($comment['comment']));
			$blog_comment_date = ucfirst(format_locale_date($dateFormatLong,strtotime($comment['date_creation'])));
			$blog_comment_time = date('H:i',strtotime($comment['date_creation']));
			$blog_comment_actions = "";
			if(api_is_allowed('BLOG_' . $blog_id, 'article_comments_delete', $task_id)) { $blog_comment_actions .= '<a href="blog.php?action=view_post&amp;blog_id=' . $blog_id . '&amp;post_id=' . $post_id . '&amp;do=delete_comment&amp;comment_id=' . $comment['comment_id'] . '&amp;task_id=' . $task_id . '" title="' . get_lang('DeleteThisComment') . '" onclick="javascript:if(!confirm(\''.addslashes(htmlentities(get_lang("ConfirmYourChoice"),ENT_QUOTES,$charset)). '\')) return false;"><img src="../img/delete.gif" border="0" /></a>'; }
			if(api_is_allowed('BLOG_' . $blog_id, 'article_comments_rate')) { $rating_select = Blog::display_rating_form('comment', $blog_id, $post_id, $comment['comment_id']); }

			if(!is_null($comment['task_id']))
			{
				$border_color = ' border-left: 3px solid #' . $comment['color'];
			}
			
			$comment_text=stripslashes($comment_text);

			// Output...
			$margin = $current_level * 30;
			echo '<div class="blogpost_comment" style="margin-left: ' . $margin . 'px;' . $border_color . '">';
				echo '<span class="blogpost_comment_title"><a href="#add_comment" onclick="document.getElementById(\'comment_parent_id\').value=\'' . $comment['comment_id'] . '\'; document.getElementById(\'comment_title\').value=\'Re: '.addslashes($comment['title']) . '\'" title="' . get_lang('ReplyToThisComment') . '" >'.stripslashes($comment['title']) . '</a></span>';
				echo '<span class="blogpost_comment_date">' . $blog_comment_date . ' (' . $blog_comment_time . ')</span>';
				echo '<span class="blogpost_text">' . $comment_text . '</span>';
				
				$file_name_array=get_blog_attachment($blog_id,$post_id, $comment['comment_id']);
				if (!empty($file_name_array))
				{								
					echo '<br /><br />';
					echo Display::return_icon('attachment.gif',get_lang('Attachment'));
					echo '<a href="download.php?file=';		
					echo $file_name_array['path'];	
					echo ' "> '.$file_name_array['filename'].' </a>';	
					echo '<span class="attachment_comment">';	
					echo $file_name_array['comment'];
					echo '</span><br />';								
				}				
				
				echo '<span class="blogpost_comment_info">' . get_lang('Author') . ': ' . $comment['lastname'] . ' ' . $comment['firstname'] . ' - ' . get_lang('Rating') . ': '.Blog::display_rating('comment', $blog_id, $comment['comment_id']) . $rating_select . '</span>';
				echo '<span class="blogpost_actions">' . $blog_comment_actions . '</span>';
			echo '</div>';

			// Go further down the tree.
			Blog::get_threaded_comments( $comment['comment_id'], $next_level, $blog_id, $post_id);
		}
	}

	/**
	 * Displays the form to create a new post
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 */
	function display_form_new_post($blog_id)
	{
		if(api_is_allowed('BLOG_' . $blog_id, 'article_add'))
		{
			echo '<script type="text/javascript">
					function FCKeditor_OnComplete( editorInstance )
					{
					  editorInstance.Events.AttachEvent( \'OnSelectionChange\', check_for_title ) ;
					}

					function check_for_title()
					{
						// This functions shows that you can interact directly with the editor area
						// DOM. In this way you have the freedom to do anything you want with it.

						// Get the editor instance that we want to interact with.
						var oEditor = FCKeditorAPI.GetInstance(\'post_full_text\') ;

						// Get the Editor Area DOM (Document object).
						var oDOM = oEditor.EditorDocument ;

						var iLength ;
						var contentText ;
						var contentTextArray;
						var bestandsnaamNieuw = "";
						var bestandsnaamOud = "";

						// The are two diffent ways to get the text (without HTML markups).
						// It is browser specific.

						if( document.all )		// If Internet Explorer.
						{
							contentText = oDOM.body.innerText ;
						}
						else					// If Gecko.
						{
							var r = oDOM.createRange() ;
							r.selectNodeContents( oDOM.body ) ;
							contentText = r.toString() ;
						}

						// Compose title if there is none
						contentTextArray = contentText.split(\' \') ;
						var x=0;
						for(x=0; (x<5 && x<contentTextArray.length); x++)
						{
							if(x < 4)
							{
								bestandsnaamNieuw += contentTextArray[x] + \' \';
							}
							else
							{
								bestandsnaamNieuw += contentTextArray[x] + \'...\';
							}
						}

						if(document.getElementById(\'post_title_edited\').value == "false")
						{
							document.getElementById(\'post_title\').value = bestandsnaamNieuw;
						}
					}

					function trim(s) {
					 while(s.substring(0,1) == \' \') {
					  s = s.substring(1,s.length);
					 }
					 while(s.substring(s.length-1,s.length) == \' \') {
					  s = s.substring(0,s.length-1);
					 }
					 return s;
					}

					function check_if_still_empty()
					{
						if(trim(document.getElementById(\'post_title\').value) != "")
						{
							document.getElementById(\'post_title_edited\').value = "true";
						}
					}

			</script>';


			echo '<form name="add_post" enctype="multipart/form-data"  method="post" action="blog.php?blog_id=' . $blog_id . '">
				 <div class="form_header">' . get_lang('NewPost') . '</div> 
						<table width="100%" border="0" cellspacing="2" cellpadding="0">
							<tr>
						   <td width="80" valign="top">' . get_lang('Title') . ':&nbsp;&nbsp;</td>
						   <td><input name="post_title" id="post_title" type="text" size="60" onblur="check_if_still_empty()" />' .
						   		'<input type="hidden" name="post_title_edited" id="post_title_edited" value="false" /><br /><br /></td>
							</tr>
							<tr>
						   <td valign="top">' . get_lang('PostFullText') . ':&nbsp;&nbsp;</td>
						   <td>';
									$oFCKeditor = new FCKeditor('post_full_text') ;
									$oFCKeditor->BasePath	= api_get_path(WEB_PATH) . 'main/inc/lib/fckeditor/' ;
									$oFCKeditor->Height		= '350';
									$oFCKeditor->Width		= '98%';
									$oFCKeditor->Value		= isset($_POST['post_full_text'])?stripslashes($_POST['post_full_text']):'';
									$oFCKeditor->Config['CustomConfigurationsPath'] = api_get_path(REL_PATH)."main/inc/lib/fckeditor/myconfig.js";
									$oFCKeditor->Config['IMUploadPath'] = "upload/blog/";
									$oFCKeditor->ToolbarSet = "Blog";

									$TBL_LANGUAGES = Database::get_main_table(TABLE_MAIN_LANGUAGE);
									$sql="SELECT isocode FROM ".$TBL_LANGUAGES." WHERE english_name='".mysql_real_escape_string($_SESSION["_course"]["language"])."'";
									$result_sql=api_sql_query($sql);
									$isocode_language=mysql_result($result_sql,0,0);
									$oFCKeditor->Config['DefaultLanguage'] = $isocode_language;
									
									$oFCKeditor->Config['InDocument'] = false;		
									$oFCKeditor->Config['CreateDocumentDir'] = '../../courses/'.api_get_course_path().'/document/';
		 
		

									$oFCKeditor->Create() ;
			echo '			 <br /></td>
							</tr> 
							<tr><td><b>'.get_lang('AddAnAttachment').'</b></td></tr>	
							<tr><td width="80" valign="top">' . ucwords(get_lang('FileName') ). ':&nbsp;&nbsp;</td>
						    <td><input type="file" name="user_upload"/></td><br></tr>						    
						    <tr><td width="80" valign="top">' . get_lang('FileComment'). ':&nbsp;&nbsp;</td>
						    <td><br /><textarea name="post_file_comment" cols="34" /></textarea></td></tr>
							<tr>
								<td >&nbsp;</td>
								<td>
								 <input type="hidden" name="action" value="" />
								 <input type="hidden" name="new_post_submit" value="true" />
								 <input type="submit" name="Submit" value="' . get_lang('Ok') . '" />
								</td>
							</tr>
						</table>
					</form>';
		}
		else
		{
			api_not_allowed();
		}
	}

	/**
	 * Displays the form to edit a post
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 */
	function display_form_edit_post($blog_id, $post_id)
	{
		// Init
		$tbl_blogs_posts = Database::get_course_table(TABLE_BLOGS_POSTS);
		$tbl_users = Database::get_main_table(TABLE_MAIN_USER);

		// Get posts and author
		$sql = "SELECT post.*, user.lastname, user.firstname FROM $tbl_blogs_posts post
				INNER JOIN $tbl_users user ON post.author_id = user.user_id
				WHERE post.blog_id = '".(int)$blog_id ."'
				AND post.post_id = '".(int)$post_id."'
				ORDER BY post_id DESC";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		$blog_post = mysql_fetch_array($result);

		// Prepare data
		$blog_post_text = stripslashes($blog_post['full_text']);

		echo '<form name="edit_post" method="post" action="blog.php?blog_id=' . $blog_id . '">
			 <span class="blogpost_title">' . get_lang('EditPost') . '</span>
					<table width="100%" border="0" cellspacing="2" cellpadding="0">
						<tr>
					   <td width="80" valign="top">' . get_lang('Title') . ':&nbsp;&nbsp;</td>
					   <td><input name="post_title" id="post_title" type="text" size="60" value="'.stripslashes($blog_post['title']) . '" /><br /><br /></td>
						</tr>
						<tr>
					   <td valign="top">' . get_lang('PostFullText') . ':&nbsp;&nbsp;</td>
					   <td>';
								$oFCKeditor = new FCKeditor('post_full_text') ;
								$oFCKeditor->BasePath	= api_get_path(WEB_PATH) . 'main/inc/lib/fckeditor/' ;
								$oFCKeditor->Height		= '350';
								$oFCKeditor->Width		= '98%';
								$oFCKeditor->Value		= isset($_POST['post_full_text'])?stripslashes($_POST['post_full_text']):$blog_post_text;
								$oFCKeditor->Config['CustomConfigurationsPath'] = api_get_path(REL_PATH)."main/inc/lib/fckeditor/myconfig.js";
								$oFCKeditor->Config['IMUploadPath'] = "upload/blog/";
								$oFCKeditor->ToolbarSet = "Blog";

								$TBL_LANGUAGES = Database::get_main_table(TABLE_MAIN_LANGUAGE);
								$sql="SELECT isocode FROM ".$TBL_LANGUAGES." WHERE english_name='".mysql_real_escape_string($_SESSION["_course"]["language"])."'";
								$result_sql=api_sql_query($sql);
								$isocode_language=mysql_result($result_sql,0,0);
								$oFCKeditor->Config['DefaultLanguage'] = $isocode_language;
								
								$oFCKeditor->Config['InDocument'] = false;		
								$oFCKeditor->Config['CreateDocumentDir'] = '../../courses/'.api_get_course_path().'/document/';
		
		

								$oFCKeditor->Create() ;
		echo '			 <br /></td>
						</tr>
						<tr>
							<td >&nbsp;</td>
							<td>
							 <input type="hidden" name="action" value="" />
							 <input type="hidden" name="edit_post_submit" value="true" />
							 <input type="hidden" name="post_id" value="' . (int)$_GET['post_id'] . '" />
							 <input type="submit" name="Submit" value="' . get_lang('Ok') . '" />
							</td>
						</tr>
					</table>
				</form>';
	}

	/**
	 * Displays a list of tasks in this blog
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 */
	function display_task_list($blog_id)
	{
		global $charset;
		if(api_is_allowed('BLOG_' . $blog_id, 'article_add'))
		{
			// Init
			$tbl_blogs_tasks = Database::get_course_table(TABLE_BLOGS_TASKS);
			$counter = 0;
			global $color2;

			echo '<div class="actions">';
			echo '<a href="' .api_get_self(). '?action=manage_tasks&amp;blog_id=' . $blog_id . '&amp;do=add"><img src="../img/blog.gif" border="0" align="middle" alt="scormbuilder" />' . get_lang('AddTasks') . '</a> ';
			echo '<a href="' .api_get_self(). '?action=manage_tasks&amp;blog_id=' . $blog_id . '&amp;do=assign"><img src="../img/blog.gif" border="0" align="middle" alt="scormbuilder" />' . get_lang('AssignTasks') . '</a>';
			?>
				<a href="<?php echo api_get_self(); ?>?action=manage_rights&amp;blog_id=<?php echo $blog_id ?>" title="<?php echo get_lang('ManageRights') ?>"><?php echo get_lang('RightsManager') ?></a>
			<?php	
			echo '</div>';
			
			echo '<span class="blogpost_title">' . get_lang('TaskList') . '</span><br />';
			echo "<table class=\"data_table\">";
			echo	"<tr bgcolor=\"$color2\" align=\"center\" valign=\"top\">",
					 "<th width='240'><b>",get_lang('Title'),"</b></th>\n",
					 "<th><b>",get_lang('Description'),"</b></th>\n",
					 "<th><b>",get_lang('Color'),"</b></th>\n",
					 "<th width='50'><b>",get_lang('Modify'),"</b></th>\n",
				"</tr>\n";


			$sql = "
				SELECT
					`blog_id`,
					`task_id`,
					`blog_id`,
					`title`,
					`description`,
					`color`,
					`system_task`
				FROM " . $tbl_blogs_tasks . "
				WHERE `blog_id` = " . (int)$blog_id . "
				ORDER BY
					`system_task`,
					`title`";
			$result = api_sql_query($sql, __FILE__, __LINE__);


			while($task = mysql_fetch_array($result))
			{
				$counter++;
				$css_class = (($counter % 2) == 0) ? "row_odd" : "row_even";
				$delete_icon = ($task['system_task'] == '1') ? "delete_na.gif" : "delete.gif";
				$delete_title = ($task['system_task'] == '1') ? get_lang('DeleteSystemTask') : get_lang('DeleteTask');
				$delete_link = ($task['system_task'] == '1') ? '#' : api_get_self() . '?action=manage_tasks&amp;blog_id=' . $task['blog_id'] . '&amp;do=delete&amp;task_id=' . $task['task_id'];
				$delete_confirm = ($task['system_task'] == '1') ? '' : 'onclick="javascript:if(!confirm(\''.addslashes(htmlentities(get_lang("ConfirmYourChoice"),ENT_QUOTES,$charset)). '\')) return false;"';

				echo	'<tr class="' . $css_class . '" valign="top">',
							 '<td width="240">' . stripslashes($task['title']) . '</td>',
							 '<td>' . stripslashes($task['description']) . '</td>',
							 '<td><span style="background-color: #' . $task['color'] . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>',
							 '<td width="50">',
							 	'<a href="' .api_get_self(). '?action=manage_tasks&amp;blog_id=' . $task['blog_id'] . '&amp;do=edit&amp;task_id=' . $task['task_id'] . '">',
								'<img src="../img/edit.gif" border="0" title="' . get_lang('EditTask') . '" />',
								"</a>\n",
								'<a href="' . $delete_link . '"',
								$delete_confirm,
								'><img src="../img/' . $delete_icon . '" border="0" title="' . $delete_title . '" />',
								"</a>\n",
							 '</td>',
						'</tr>';
			}
			echo "</table>";
		}
	}

	/**
	 * Displays a list of tasks assigned to a user in this blog
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 */
	function display_assigned_task_list($blog_id)
	{
		// Init
		$tbl_users = Database::get_main_table(TABLE_MAIN_USER);
		$tbl_blogs_tasks = Database::get_course_table(TABLE_BLOGS_TASKS);
		$tbl_blogs_tasks_rel_user = Database::get_course_table(TABLE_BLOGS_TASKS_REL_USER);
		$counter = 0;
		global $charset,$color2;

		echo '<span class="blogpost_title">' . get_lang('AssignedTasks') . '</span><br />';
		echo "<table class=\"data_table\">";
		echo	"<tr bgcolor=\"$color2\" align=\"center\" valign=\"top\">",
				 "<th width='240'><b>",get_lang('Member'),"</b></th>\n",
				 "<th><b>",get_lang('Task'),"</b></th>\n",
				 "<th><b>",get_lang('Description'),"</b></th>\n",
				 "<th><b>",get_lang('TargetDate'),"</b></th>\n",
				 "<th width='50'><b>",get_lang('Modify'),"</b></th>\n",
			"</tr>\n";


		$sql = "SELECT task_rel_user.*, task.title, user.firstname, user.lastname, task.description FROM $tbl_blogs_tasks_rel_user task_rel_user
		INNER JOIN $tbl_blogs_tasks task ON task_rel_user.task_id = task.task_id
		INNER JOIN $tbl_users user ON task_rel_user.user_id = user.user_id
		WHERE task_rel_user.blog_id = '".(int)$blog_id."' ORDER BY `target_date` ASC";
		$result = api_sql_query($sql, __FILE__, __LINE__);


		while($assignment = mysql_fetch_array($result))
		{
			$counter++;
			$css_class = (($counter % 2)==0) ? "row_odd" : "row_even";
			$delete_icon = ($task['system_task'] == '1') ? "delete_na.gif" : "delete.gif";
			$delete_title = ($task['system_task'] == '1') ? get_lang('DeleteSystemTask') : get_lang('DeleteTask');
			$delete_link = ($task['system_task'] == '1') ? '#' : api_get_self() . '?action=manage_tasks&amp;blog_id=' . $task['blog_id'] . '&amp;do=delete&amp;task_id=' . $task['task_id'];
			$delete_confirm = ($task['system_task'] == '1') ? '' : 'onclick="javascript:if(!confirm(\''.addslashes(htmlentities(get_lang("ConfirmYourChoice"),ENT_QUOTES,$charset)). '\')) return false;"';

			echo	'<tr class="' . $css_class . '" valign="top">',
						 '<td width="240">' . $assignment['firstname'] . ' ' . $assignment['lastname'] . '</td>',
						 '<td>'.stripslashes($assignment['title']) . '</td>',
						 '<td>'.stripslashes($assignment['description']) . '</td>',
						 '<td>' . $assignment['target_date'] . '</td>',
						 '<td width="50">',
						 	'<a href="' .api_get_self(). '?action=manage_tasks&amp;blog_id=' . $assignment['blog_id'] . '&amp;do=edit_assignment&amp;assignment_id=' . $assignment['task_id'] . '|' . $assignment['user_id'] . '">',
							'<img src="../img/edit.gif" border="0" title="' . get_lang('EditTask') . '" />',
							"</a>\n",
							'<a href="' .api_get_self(). '?action=manage_tasks&amp;blog_id=' . $assignment['blog_id'] . '&amp;do=delete_assignment&amp;assignment_id=' . $assignment['task_id'] . '|' . $assignment['user_id'] . '" ',
							'onclick="javascript:if(!confirm(\''.addslashes(htmlentities(get_lang("ConfirmYourChoice"),ENT_QUOTES,$charset)). '\')) return false;"',
							'<img src="../img/' . $delete_icon . '" border="0" title="' . $delete_title . '" />',
							"</a>\n",
						 '</td>',
					'</tr>';
		}
		echo "</table>";
	}

	/**
	 * Displays new task form
	 * @author Toon Keppens
	 *
	 */
	function display_new_task_form($blog_id)
	{
		// Init
		$colors = array('FFFFFF','FFFF99','FFCC99','FF9933','FF6699','CCFF99','CC9966','66FF00', '9966FF', 'CF3F3F', '990033','669933','0033FF','003366','000000');

		// Display
		echo '<form name="add_task" method="post" action="blog.php?action=manage_tasks&amp;blog_id=' . $blog_id . '">
					<div class="form_header">' . get_lang('AddTask') . '</div>
					<table width="100%" border="0" cellspacing="2">
						<tr>
					   <td align="right">' . get_lang('Title') . ':&nbsp;&nbsp;</td>
					   <td><input name="task_name" type="text" size="70" /></td>
						</tr>
						<tr>
					   <td align="right">' . get_lang('Description') . ':&nbsp;&nbsp;</td>
					   <td><input name="task_description" type="text" size="70" /></td>
						</tr>';

						/* edit by Kevin Van Den Haute (kevin@develop-it.be) */
						echo "\t" . '<tr>' . "\n";
							echo "\t\t" . '<td style="text-align:right; vertical-align:top;">Task management:&nbsp;&nbsp;</td>' . "\n";
							echo "\t\t" . '<td>' . "\n";
								echo "\t\t\t" . '<table class="data_table" cellspacing="0" style="border-collapse:collapse; width:446px;">';
									echo "\t\t\t\t" . '<tr>' . "\n";
										echo "\t\t\t\t\t" . '<th colspan="2" style="width:223px;">' . get_lang('ArticleManager') . '</th>' . "\n";
										echo "\t\t\t\t\t" . '<th width:223px;>' . get_lang('CommentManager') . '</th>' . "\n";
									echo "\t\t\t\t" . '</tr>' . "\n";
									echo "\t\t\t\t" . '<tr>' . "\n";
										echo "\t\t\t\t\t" . '<th style="width:111px;"><label for="articleDelete">' . get_lang('Delete') . '</label></th>' . "\n";
										echo "\t\t\t\t\t" . '<th style="width:112px;"><label for="articleEdit">' . get_lang('Edit') . '</label></th>' . "\n";
										echo "\t\t\t\t\t" . '<th style="width:223px;"><label for="commentsDelete">' . get_lang('Delete') . '</label></th>' . "\n";
									echo "\t\t\t\t" . '</tr>' . "\n";
									echo "\t\t\t\t" . '<tr>' . "\n";
										echo "\t\t\t\t\t" . '<td style="text-align:center;"><input id="articleDelete" name="chkArticleDelete" type="checkbox" /></td>' . "\n";
										echo "\t\t\t\t\t" . '<td style="text-align:center;"><input id="articleEdit" name="chkArticleEdit" type="checkbox" /></td>' . "\n";
										echo "\t\t\t\t\t" . '<td style="border:1px dotted #808080; text-align:center;"><input id="commentsDelete" name="chkCommentsDelete" type="checkbox" /></td>' . "\n";
									echo "\t\t\t\t" . '</tr>' . "\n";
								echo "\t\t\t" . '</table>' . "\n";
							echo "\t\t" . '</td>' . "\n";
						echo "\t" . '</tr>' . "\n";
						/* end of edit */

		echo '			<tr>
					   <td align="right">' . get_lang('Color') . ':&nbsp;&nbsp;</td>
					   <td>
					   	<select name="task_color" id="color" style="width: 150px; background-color: #eeeeee" onchange="document.getElementById(\'color\').style.backgroundColor=\'#\'+document.getElementById(\'color\').value" onkeypress="document.getElementById(\'color\').style.backgroundColor=\'#\'+document.getElementById(\'color\').value">';
								foreach ($colors as $color)
								{
									$style = 'style="background-color: #' . $color . '"';
									echo '<option value="' . $color . '" ' . $style . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>';
								}
		echo '			   </select>
						  </td>
						</tr>
						<tr>
							<td align="right">&nbsp;</td>
							<input type="hidden" name="action" value="" />
							<input type="hidden" name="new_task_submit" value="true" />
							<td><br /><input type="submit" name="Submit" value="' . get_lang('Ok') . '" /></td>
						</tr>
					</table>
				</form>';
	}
	

	/**
	 * Displays edit task form
	 * @author Toon Keppens
	 *
	 */
	function display_edit_task_form($blog_id, $task_id)
	{
		// Init
		$tbl_blogs_tasks = Database::get_course_table(TABLE_BLOGS_TASKS);
		$colors = array('FFFFFF','FFFF99','FFCC99','FF9933','FF6699','CCFF99','CC9966','66FF00', '9966FF', 'CF3F3F', '990033','669933','0033FF','003366','000000');

		$sql = "SELECT blog_id, task_id, title, description, color FROM $tbl_blogs_tasks WHERE task_id = '".(int)$task_id."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		$task = mysql_fetch_array($result);

		// Display
		echo '<form name="edit_task" method="post" action="blog.php?action=manage_tasks&amp;blog_id=' . $blog_id . '">
					<div class="form_header">' . get_lang('EditTask') . '</div>
					<table width="100%" border="0" cellspacing="2">
						<tr>
					   <td align="right">' . get_lang('Title') . ':&nbsp;&nbsp;</td>
					   <td><input name="task_name" type="text" size="70" value="'.stripslashes($task['title']) . '" /></td>
						</tr>
						<tr>
					   <td align="right">' . get_lang('Description') . ':&nbsp;&nbsp;</td>
					   <td><input name="task_description" type="text" size="70" value="'.stripslashes($task['description']) . '" /></td>
						</tr>';

						/* edit by Kevin Van Den Haute (kevin@develop-it.be) */
						$tbl_tasks_permissions = Database::get_course_table(TABLE_BLOGS_TASKS_PERMISSIONS);

						$sql = "
							SELECT
								`id`,
								`action`
							FROM " . $tbl_tasks_permissions . "
							WHERE `task_id` = '" . (int)$task_id."'";
						$result = api_sql_query($sql, __FILE__, __LINE__);

						$arrPermissions = array();

						while($row = @mysql_fetch_array($result))
							$arrPermissions[] = $row['action'];

						echo "\t" . '<tr>' . "\n";
							echo "\t\t" . '<td style="text-align:right; vertical-align:top;">Task management:&nbsp;&nbsp;</td>' . "\n";
							echo "\t\t" . '<td>' . "\n";
								echo "\t\t\t" . '<table  class="data_table" cellspacing="0" style="border-collapse:collapse; width:446px;">';
									echo "\t\t\t\t" . '<tr>' . "\n";
										echo "\t\t\t\t\t" . '<th colspan="2" style="width:223px;">' . get_lang('ArticleManager') . '</th>' . "\n";
										echo "\t\t\t\t\t" . '<th width:223px;>' . get_lang('CommentManager') . '</th>' . "\n";
									echo "\t\t\t\t" . '</tr>' . "\n";
									echo "\t\t\t\t" . '<tr>' . "\n";
										echo "\t\t\t\t\t" . '<th style="width:111px;"><label for="articleDelete">' . get_lang('Delete') . '</label></th>' . "\n";
										echo "\t\t\t\t\t" . '<th style="width:112px;"><label for="articleEdit">' . get_lang('Edit') . '</label></th>' . "\n";
										echo "\t\t\t\t\t" . '<th style="width:223px;"><label for="commentsDelete">' . get_lang('Delete') . '</label></th>' . "\n";
									echo "\t\t\t\t" . '</tr>' . "\n";
									echo "\t\t\t\t" . '<tr>' . "\n";
										echo "\t\t\t\t\t" . '<td style="text-align:center;"><input ' . ((in_array('article_delete', $arrPermissions)) ? 'checked ' : '') . 'id="articleDelete" name="chkArticleDelete" type="checkbox" /></td>' . "\n";
										echo "\t\t\t\t\t" . '<td style="text-align:center;"><input ' . ((in_array('article_edit', $arrPermissions)) ? 'checked ' : '') . 'id="articleEdit" name="chkArticleEdit" type="checkbox" /></td>' . "\n";
										echo "\t\t\t\t\t" . '<td style="text-align:center;"><input ' . ((in_array('article_comments_delete', $arrPermissions)) ? 'checked ' : '') . 'id="commentsDelete" name="chkCommentsDelete" type="checkbox" /></td>' . "\n";
									echo "\t\t\t\t" . '</tr>' . "\n";
								echo "\t\t\t" . '</table>' . "\n";
							echo "\t\t" . '</td>' . "\n";
						echo "\t" . '</tr>' . "\n";
						/* end of edit */

						echo '<tr>
					   <td align="right">' . get_lang('Color') . ':&nbsp;&nbsp;</td>
					   <td>
					   	<select name="task_color" id="color" style="width: 150px; background-color: #' . $task['color'] . '" onchange="document.getElementById(\'color\').style.backgroundColor=\'#\'+document.getElementById(\'color\').value" onkeypress="document.getElementById(\'color\').style.backgroundColor=\'#\'+document.getElementById(\'color\').value">';
								foreach ($colors as $color)
								{
									$selected = ($color == $task['color']) ? ' selected' : '';
									$style = 'style="background-color: #' . $color . '"';
									echo '<option value="' . $color . '" ' . $style . ' ' . $selected . ' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>';
								}
		echo '			   </select>
						  </td>
						</tr>
						<tr>
							<td align="right">&nbsp;</td>
							<td><br /><input type="hidden" name="action" value="" />
							<input type="hidden" name="edit_task_submit" value="true" />
							<input type="hidden" name="task_id" value="' . $task['task_id'] . '" />
							<input type="hidden" name="blog_id" value="' . $task['blog_id'] . '" />
							<input type="submit" name="Submit" value="' . get_lang('Ok') . '" /></td>
						</tr>
					</table>
				</form>';
	}

	/**
	 * Displays assign task form
	 * @author Toon Keppens
	 *
	 */
	function display_assign_task_form($blog_id)
	{
		// Init
		$tbl_users = Database::get_main_table(TABLE_MAIN_USER);
		$tbl_blogs_rel_user = Database::get_course_table(TABLE_BLOGS_REL_USER);
		$tbl_blogs_tasks = Database::get_course_table(TABLE_BLOGS_TASKS);
		$day	= date("d");
		$month	= date("m");
		$year	= date("Y");
		global $MonthsLong;

		// Get users in this blog / make select list of it
		$sql = "SELECT user.user_id, user.firstname, user.lastname FROM $tbl_users user
				INNER JOIN $tbl_blogs_rel_user blogs_rel_user
				ON user.user_id = blogs_rel_user.user_id
				WHERE blogs_rel_user.blog_id = '".(int)$blog_id."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		$select_user_list = '<select name="task_user_id">';
		while($user = mysql_fetch_array($result))
		{
			$select_user_list .= '<option value="' . $user['user_id'] . '">' . $user['firstname']." " . $user['lastname'] . '</option>';
		}
		$select_user_list .= '</select>';


		// Get tasks in this blog / make select list of it
		$sql = "
			SELECT
				`blog_id`,
				`task_id`,
				`blog_id`,
				`title`,
				`description`,
				`color`,
				`system_task`
			FROM " . $tbl_blogs_tasks . "
			WHERE `blog_id` = " . (int)$blog_id . "
			ORDER BY
				`system_task`,
				`title`";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		$select_task_list = '<select name="task_task_id">';

		while($task = mysql_fetch_array($result))
		{
			$select_task_list .= '<option value="' . $task['task_id'] . '">'.stripslashes($task['title']) . '</option>';
		}
		$select_task_list .= '</select>';

		// Display
		echo '<form name="assign_task" method="post" action="blog.php?action=manage_tasks&amp;blog_id=' . $blog_id . '">
					<div class="form_header">' . get_lang('AssignTask') . '</div>
					<table width="100%" border="0" cellspacing="2" cellpadding="0">
						<tr>
					   <td align="right">' . get_lang('SelectUser') . ':&nbsp;&nbsp;</td>
					   <td>' . $select_user_list . '</td>
						</tr>
						<tr>
					   <td align="right">' . get_lang('SelectTask') . ':&nbsp;&nbsp;</td>
					   <td>' . $select_task_list . '</td>
						</tr>
						<tr>
					   <td align="right">' . get_lang('SelectTargetDate') . ':&nbsp;&nbsp;</td>
					   <td>
					    <select name="task_day">';
								for($i=1; $i<=31; $i++)
								{
									// values need to have double digits
									$value = ($i <= 9 ? "0" . $i : $i);

									// the current day is indicated with [] around the date
									if($value==$day)
									{ echo "\t\t\t\t <option value=\"" . $value."\" selected> " . $i." </option>\n";}
									else
									{ echo "\t\t\t\t <option value=\"" . $value."\">" . $i."</option>\n"; }
								}
							echo '</select>

							<select name="task_month">';
								for($i=1; $i<=12; $i++)
								{
									// values need to have double digits
									$value = ($i <= 9 ? "0" . $i : $i);

									if($value==$month)
									{ echo "\t\t\t\t <option value=\"" . $value."\" selected>" . $MonthsLong[$i-1]."</option>\n"; }
									else
									{ echo "\t\t\t\t <option value=\"" . $value."\">" . $MonthsLong[$i-1]."</option>\n"; }
								}
							echo '</select>

							<select name="task_year">
								<option value="'.($year-1) . '">'.($year-1) . '</option>
								<option value="' . $year . '" selected> ' . $year . ' </option>';
								for($i=1; $i<=5; $i++)
								{
									$value=$year+$i;
									echo "\t\t\t\t<option value=\"" . $value."\">" . $value."</option>\n";
								}
							echo '</select>
							<a title="Kalender" href="javascript:openCalendar(\'assign_task\', \'task_\')"><img src="../img/calendar_select.gif" border="0" align="absmiddle"/></a>
						 </td>
						</tr>
						<tr>
							<td align="right">&nbsp;</td>
							<input type="hidden" name="action" value="" />
							<input type="hidden" name="assign_task_submit" value="true" />
							<td><br /><input type="submit" name="Submit" value="' . get_lang('Ok') . '" /></td>
						</tr>
					</table>
				</form>';
	}

		/**
	 * Displays assign task form
	 * @author Toon Keppens
	 *
	 */
	function display_edit_assigned_task_form($blog_id, $assignment_id)
	{
		$parameters = explode('|', $assignment_id);
		$task_id = $parameters[0];
		$user_id = $parameters[1];

		/* ------------- */
		// Init
		$tbl_users 					= Database::get_main_table(TABLE_MAIN_USER);
		$tbl_blogs_rel_user 		= Database::get_course_table(TABLE_BLOGS_REL_USER);
		$tbl_blogs_tasks 			= Database::get_course_table(TABLE_BLOGS_TASKS);
		$tbl_blogs_tasks_rel_user 	= Database::get_course_table(TABLE_BLOGS_TASKS_REL_USER);

		$year	= date("Y");
		global $MonthsLong;

		/*// Get assignd tasks of user
		$sql = "
			SELECT task_id
			FROM $tbl_blogs_tasks_rel_user
			WHERE
				user_id = $user_id AND
				blog_id = $blog_id";

		$result = api_sql_query($sql, __FILE__, __LINE__);

		$arrUserTasks = array();*/

		while($row = mysql_fetch_assoc($result))
		{
			$arrUserTasks[] = $row['task_id'];
		}

		// Get assignd date;
		$sql = "
			SELECT target_date
			FROM $tbl_blogs_tasks_rel_user
			WHERE blog_id = '".(int)$blog_id."'
			AND	user_id = '".(int)$user_id."'
			AND	task_id = '".(int)$task_id."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		$row = mysql_fetch_assoc($result);

		$old_date = $row['target_date'];
		$date = explode('-', $row['target_date']);

		// Get users in this blog / make select list of it
		$sql = "
			SELECT user.user_id, user.firstname, user.lastname
			FROM $tbl_users user
			INNER JOIN $tbl_blogs_rel_user blogs_rel_user on user.user_id = blogs_rel_user.user_id
			WHERE blogs_rel_user.blog_id = '".(int)$blog_id."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);

		$select_user_list = '<select name="task_user_id">';

		while($user = mysql_fetch_array($result))
		{
			$select_user_list .= '<option ' . (($user_id == $user['user_id']) ? 'selected="selected "' : ' ') . 'value="' . $user['user_id'] . '">' . $user['firstname']." " . $user['lastname'] . '</option>';
		}

		$select_user_list .= '</select>';

		// Get tasks in this blog / make select list of it
		$sql = "
			SELECT
				`blog_id`,
				`task_id`,
				`title`,
				`description`,
				`color`,
				`system_task`
			FROM " . $tbl_blogs_tasks . "
			WHERE `blog_id` = " . (int)$blog_id . "
			ORDER BY
				`system_task`,
				`title`";
		$result = api_sql_query($sql, __FILE__, __LINE__);

		$select_task_list = '<select name="task_task_id">';

		while($task = mysql_fetch_array($result))
		{
			//if(!in_array($task['task_id'], $arrUserTasks) || $task_id == $task['task_id'])
				$select_task_list .= '<option ' . (($task_id == $task['task_id']) ? 'selected="selected "' : ' ') . 'value="' . $task['task_id'] . '">'.stripslashes($task['title']) . '</option>';
		}

		$select_task_list .= '</select>';

		// Display
		echo '<form name="assign_task" method="post" action="blog.php?action=manage_tasks&amp;blog_id=' . $blog_id . '">
				<table width="100%" border="0" cellspacing="2" cellpadding="0" style="background-color: #f6f6f6; border: 1px solid #dddddd">
				  <tr>
				  	<td width="200"></td>
				  	<td><b>' . get_lang('AssignTask') . '</b><br /><br /></td>
				  </tr>
					<tr>
				   <td align="right">' . get_lang('SelectUser') . ':&nbsp;&nbsp;</td>
				   <td>' . $select_user_list . '</td>
					</tr>
					<tr>
				   <td align="right">' . get_lang('SelectTask') . ':&nbsp;&nbsp;</td>
				   <td>' . $select_task_list . '</td>
					</tr>
					<tr>
				   <td align="right">' . get_lang('SelectTargetDate') . ':&nbsp;&nbsp;</td>
				   <td>
				    <select name="task_day">';

							for($i=1; $i<=31; $i++)
							{
								// values need to have double digits
								$value = ($i <= 9 ? "0" . $i : $i);

								echo "\t\t\t\t<option " . (($date[2] == $value) ? 'selected="selected "' : ' ') . "value=\"" . $value . "\">" . $i . "</option>\n";
							}

						echo '</select>

						<select name="task_month">';

							for($i=1; $i<=12; $i++)
							{
								// values need to have double digits
								$value = ($i <= 9 ? "0" . $i : $i);

								echo "\t\t\t\t<option " . (($date[1] == $value) ? 'selected="selected "' : ' ') . "value=\"" . $value . "\">" . $MonthsLong[$i-1]."</option>\n";
							}

						echo '</select>

						<select name="task_year">
							<option value="' . ($year - 1) . '">' . ($year - 1) . '</option>
							<option value="' . $year . '" selected> ' . $year . ' </option>';

							for($i=1; $i<=5; $i++)
							{
								$value = $year + $i;

								echo "\t\t\t\t<option " . (($date[0] == $value) ? 'selected="selected "' : ' ') . "value=\"" . $value . "\">" . $value . "</option>\n";
							}

						echo '</select>
						<a title="Kalender" href="javascript:openCalendar(\'assign_task\', \'task_\')"><img src="../img/calendar_select.gif" border="0" align="absmiddle"/></a>
					 </td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<input type="hidden" name="action" value="" />
						<input type="hidden" name="old_task_id" value="' . $task_id . '" />
						<input type="hidden" name="old_user_id" value="' . $user_id . '" />
						<input type="hidden" name="old_target_date" value="' . $old_date . '" />
						<input type="hidden" name="assign_task_edit_submit" value="true" />
						<td><br /><input type="submit" name="Submit" value="' . get_lang('Ok') . '" /></td>
					</tr>
				</table>
			</form>';
	}

	/**
	 * Assigns a task to a user in a blog
	 *
	 * @param Integer $blog_id
	 * @param Integer $user_id
	 * @param Integer $task_id
	 * @param Date $target_date
	 */
	function assign_task($blog_id, $user_id, $task_id, $target_date)
	{
		// Init
		$tbl_blogs_tasks_rel_user = Database::get_course_table(TABLE_BLOGS_TASKS_REL_USER);

		$sql = "
			SELECT COUNT(*) as 'number'
			FROM " . $tbl_blogs_tasks_rel_user . "
			WHERE `blog_id` = " . (int)$blog_id . "
			AND	`user_id` = " . (int)$user_id . "
			AND	`task_id` = " . (int)$task_id . "
		";

		$result = @api_sql_query($sql, __FILE__, __LINE__);
		$row = mysql_fetch_assoc($result);

		if($row['number'] == 0)
		{
			$sql = "
				INSERT INTO " . $tbl_blogs_tasks_rel_user . " (
					`blog_id`,
					`user_id`,
					`task_id`,
					`target_date`
				) VALUES (
					'" . (int)$blog_id . "',
					'" . (int)$user_id . "',
					'" . (int)$task_id . "',
					'" . mysql_real_escape_string($target_date) . "'
				)";

			$result = @api_sql_query($sql, __FILE__, __LINE__);
		}
	}

	function edit_assigned_task($blog_id, $user_id, $task_id, $target_date, $old_user_id, $old_task_id, $old_target_date)
	{
		// Init
		$tbl_blogs_tasks_rel_user = Database::get_course_table(TABLE_BLOGS_TASKS_REL_USER);

		$sql = "
			SELECT COUNT(*) as 'number'
			FROM " . $tbl_blogs_tasks_rel_user . "
			WHERE
				`blog_id` = " . (int)$blog_id . " AND
				`user_id` = " . (int)$user_id . " AND
				`task_id` = " . (int)$task_id . "
		";

		$result = @api_sql_query($sql, __FILE__, __LINE__);
		$row = mysql_fetch_assoc($result);

		if($row['number'] == 0 || ($row['number'] != 0 && $task_id == $old_task_id && $user_id == $old_user_id))
		{
			$sql = "
				UPDATE " . $tbl_blogs_tasks_rel_user . "
				SET
					`user_id` = " . (int)$user_id . ",
					`task_id` = " . (int)$task_id . ",
					`target_date` = '" . mysql_real_escape_string($target_date) . "'
				WHERE
					`blog_id` = " . (int)$blog_id . " AND
					`user_id` = " . (int)$old_user_id . " AND
					`task_id` = " . (int)$old_task_id . " AND
					`target_date` = '" . mysql_real_escape_string($old_target_date) . "'
			";

			$result = @api_sql_query($sql, __FILE__, __LINE__);
		}
	}

	/**
	 * Displays a list with posts a user can select to execute his task.
	 *
	 * @param Integer $blog_id
	 * @param unknown_type $task_id
	 */
	function display_select_task_post($blog_id, $task_id)
	{
		// Init
		$tbl_blogs_tasks = Database::get_course_table(TABLE_BLOGS_TASKS);
		$tbl_blogs_posts = Database::get_course_table(TABLE_BLOGS_POSTS);
		$tbl_users = Database::get_main_table(TABLE_MAIN_USER);

		$sql = "
			SELECT title, description
			FROM $tbl_blogs_tasks
			WHERE task_id = '".(int)$task_id."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		$row = mysql_fetch_assoc($result);
		// Get posts and authors
		$sql = "
			SELECT
				post.*,
				user.lastname,
				user.firstname
			FROM $tbl_blogs_posts post
			INNER JOIN $tbl_users user ON post.author_id = user.user_id
			WHERE post.blog_id = '".(int)$blog_id."'
			ORDER BY post_id DESC
			LIMIT 0, 100";
		$result = api_sql_query($sql, __FILE__, __LINE__);

		// Display
		echo '<span class="blogpost_title">' . get_lang('SelectTaskArticle') . ' "' . stripslashes($row['title']) . '"</span>';
		echo '<span style="font-style: italic;"">'.stripslashes($row['description']) . '</span><br><br>';
		

		if(mysql_num_rows($result) > 0)
		{
			while($blog_post = mysql_fetch_array($result))
			{
				echo '<a href="blog.php?action=execute_task&amp;blog_id=' . $blog_id . '&amp;task_id=' . $task_id . '&amp;post_id=' . $blog_post['post_id'] . '#add_comment">'.stripslashes($blog_post['title']) . '</a>, ' . get_lang('WrittenBy') . ' ' . $blog_post['firstname'] . ' '.stripslashes($blog_post['lastname']) . '<br />';
			}
		}
		else
			echo get_lang('NoArticles');
	}

	/**
	 * Subscribes a user to a given blog
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 * @param Integer $user_id
	 */
	function set_user_subscribed($blog_id,$user_id)
	{
		// Init
		$tbl_blogs_rel_user 	= Database::get_course_table(TABLE_BLOGS_REL_USER);
		$tbl_user_permissions 	= Database::get_course_table(TABLE_PERMISSION_USER);

		// Subscribe the user
		$sql = "INSERT INTO $tbl_blogs_rel_user ( `blog_id`, `user_id` ) VALUES ('".(int)$blog_id."', '".(int)$user_id."');";
		$result = api_sql_query($sql, __FILE__, __LINE__);

		// Give this user basic rights
		$sql="INSERT INTO $tbl_user_permissions (user_id,tool,action) VALUES ('".(int)$user_id."','BLOG_" . (int)$blog_id."','article_add')";
		$result = api_sql_query($sql, __LINE__, __FILE__);
		$sql="INSERT INTO $tbl_user_permissions (user_id,tool,action) VALUES ('".(int)$user_id."','BLOG_" . (int)$blog_id."','article_comments_add')";
		$result = api_sql_query($sql, __LINE__, __FILE__);
	}

	/**
	 * Unsubscribe a user from a given blog
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 * @param Integer $user_id
	 */
	function set_user_unsubscribed($blog_id, $user_id)
	{
		// Init
		$tbl_blogs_rel_user 	= Database::get_course_table(TABLE_BLOGS_REL_USER);
		$tbl_user_permissions 	= Database::get_course_table(TABLE_PERMISSION_USER);

		// Unsubscribe the user
		$sql = "DELETE FROM $tbl_blogs_rel_user WHERE `blog_id` = '".(int)$blog_id."' AND `user_id` = '".(int)$user_id."'";
		$result = @api_sql_query($sql, __FILE__, __LINE__);

		// Remove this user's permissions.
		$sql = "DELETE FROM $tbl_user_permissions WHERE user_id = '".(int)$user_id."'";
		$result = api_sql_query($sql, __LINE__, __FILE__);
	}

	/**
	 * Displays the form to register users in a blog (in a course)
	 * The listed users are users subcribed in the course.
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 *
	 * @return Html Form with sortable table with users to subcribe in a blog, in a course.
	 */
	function display_form_user_subscribe($blog_id)
	{
		// Init
		global $_course;
		$currentCourse = $_course['sysCode'];
		$tbl_users 			= Database::get_main_table(TABLE_MAIN_USER);
		$tbl_blogs_rel_user = Database::get_course_table(TABLE_BLOGS_REL_USER);
		$table_course_user 	= Database::get_main_table(TABLE_MAIN_COURSE_USER);
		echo '<span class="blogpost_title">' . get_lang('SubscribeMembers') . '</span>';
		$properties["width"] = "100%";

		// Get blog members' id.
		$sql = "SELECT user.user_id FROM $tbl_users user
				INNER JOIN $tbl_blogs_rel_user blogs_rel_user
				ON user.user_id = blogs_rel_user.user_id
				WHERE blogs_rel_user.blog_id = '".(int)$blog_id."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		
		$blog_member_ids = array ();
		while($user = mysql_fetch_array($result))
		{
			$blog_member_ids[] = $user['user_id'];
		}

		// Set table headers
		$column_header[] = array ('', false, '');
		$column_header[] = array (get_lang('LastName'), true, '');
		$column_header[] = array (get_lang('FirstName'), true, '');
		$column_header[] = array (get_lang('Email'), true, '');
		$column_header[] = array (get_lang('Register'), false, '');
		
		include_once (api_get_path(LIBRARY_PATH)."/course.lib.php");
		include_once (api_get_path(LIBRARY_PATH)."/usermanager.lib.php");
		
		if(isset($_SESSION['session_id'])){
			$session_id = $_SESSION['session_id'];
		}
		else{
			$session_id = 0;
		}
		
		$student_list = CourseManager :: get_student_list_from_course_code($currentCourse, true, $session_id);
		
		$user_data = array ();

		// Add users that are not in this blog to the list.
		foreach($student_list as $key=>$user)
		{
			if(isset($user['id_user']))
			{
				$user['user_id'] = $user['id_user'];
			}
			if(!in_array($user['user_id'],$blog_member_ids)) {
				$a_infosUser = UserManager :: get_user_info_by_id($user['user_id']);
				$row = array ();
				$row[] = '<input type="checkbox" name="user[]" value="' . $a_infosUser['user_id'] . '" '.(($_GET['selectall'] == "subscribe") ? ' checked="checked" ' : '') . '/>';
				$row[] = $a_infosUser["lastname"];
				$row[] = $a_infosUser["firstname"];
				$row[] = Display::encrypted_mailto_link($a_infosUser["email"]);
				//Link to register users
				if($a_infosUser["user_id"] != $_SESSION['_user']['user_id'])
				{
					$row[] = "<a href=\"" .api_get_self()."?action=manage_members&amp;blog_id=$blog_id&amp;register=yes&amp;user_id=" . $a_infosUser["user_id"]."\">" . get_lang('Register')."</a>";
				}
				else
				{
					$row[] = '';
				}
				$user_data[] = $row;
			}
		}

		// Display
		$query_vars['action'] = 'manage_members';
		$query_vars['blog_id'] = $blog_id;
		echo '<form method="post" action="blog.php?action=manage_members&amp;blog_id=' . $blog_id . '">';
			Display::display_sortable_table($column_header, $user_data,null,null,$query_vars);
			$link = '';
			$link .= isset ($_GET['action']) ? 'action=' . $_GET['action'] . '&amp;' : '';
			$link .= "blog_id=$blog_id&amp;";
			$link .= isset ($_GET['page_nr']) ? 'page_nr=' . (int)$_GET['page_nr'] . '&amp;' : '';
			$link .= isset ($_GET['per_page']) ? 'per_page=' . (int)$_GET['per_page'] . '&amp;' : '';
			$link .= isset ($_GET['column']) ? 'column=' . (int)$_GET['column'] . '&amp;' : '';
			$link .= isset ($_GET['direction']) ? 'direction=' . $_GET['direction'] . '&amp;' : '';;
			echo '<a href="blog.php?' . $link . 'selectall=subscribe">' . get_lang('SelectAll') . '</a> - ';
			echo '<a href="blog.php?' . $link . '">' . get_lang('UnSelectAll') . '</a> ';
			echo get_lang('WithSelected') . ' : ';
			echo '<select name="action">';
			echo '<option value="select_subscribe">' . get_lang('Register') . '</option>';
			echo '</select>';
			echo '<input type="hidden" name="register" value="true" />';
			echo '<input type="submit" value="' . get_lang('Ok') . '"/>';
		echo '</form>';
	}


	/**
	 * Displays the form to register users in a blog (in a course)
	 * The listed users are users subcribed in the course.
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 *
	 * @return Html Form with sortable table with users to unsubcribe from a blog.
	 */
	function display_form_user_unsubscribe($blog_id)
	{
		global $_user;

		// Init
		$tbl_users 			= Database::get_main_table(TABLE_MAIN_USER);
		$tbl_blogs_rel_user = Database::get_course_table(TABLE_BLOGS_REL_USER);

		echo '<span class="blogpost_title">' . get_lang('UnsubscribeMembers') . '</span>';

		$properties["width"] = "100%";
		//table column titles
		$column_header[] = array ('', false, '');
		$column_header[] = array (get_lang('LastName'), true, '');
		$column_header[] = array (get_lang('FirstName'), true, '');
		$column_header[] = array (get_lang('Email'), true, '');
		$column_header[] = array (get_lang('TaskManager'), true, '');
		$column_header[] = array (get_lang('UnRegister'), false, '');

		$sql_query = "SELECT user.user_id, user.lastname, user.firstname, user.email
			FROM $tbl_users user
			INNER JOIN $tbl_blogs_rel_user blogs_rel_user
			ON user.user_id = blogs_rel_user.user_id
			WHERE blogs_rel_user.blog_id = '".(int)$blog_id."'";

		//$sql_result = api_sql_query($sql_query, __FILE__, __LINE__);

		$sql_result = mysql_query($sql_query) or die(mysql_error());

		$user_data = array ();

		while($myrow = mysql_fetch_array($sql_result))
		{
			$row = array ();
			$row[] = '<input type="checkbox" name="user[]" value="' . $myrow['user_id'] . '" '.(($_GET['selectall'] == "unsubscribe") ? ' checked="checked" ' : '') . '/>';
			$row[] = $myrow["lastname"];
			$row[] = $myrow["firstname"];
			$row[] = Display::encrypted_mailto_link($myrow["email"]);

			$sql = "SELECT bt.title task
			FROM " . Database::get_course_table(TABLE_BLOGS_TASKS_REL_USER) . " `btu`
			INNER JOIN " . Database::get_course_table(TABLE_BLOGS_TASKS) . " `bt` ON `btu`.`task_id` = `bt`.`task_id`
			WHERE btu.blog_id = $blog_id AND btu.user_id = " . $myrow['user_id'] . "";

			$sql_res = mysql_query($sql) or die(mysql_error());

			$task = '';

			while($r = mysql_fetch_array($sql_res))
			{
				$task .= stripslashes($r['task']) . ', ';
			}

			echo $task;

			$task = (strlen(trim($task)) != 0) ? substr($task, 0, strlen($task) - 2) : 'reader';



			$row[] = $task;
			//Link to register users

			if($myrow["user_id"] != $_user['user_id'])
			{
				$row[] = "<a href=\"" .api_get_self()."?action=manage_members&amp;blog_id=$blog_id&amp;unregister=yes&amp;user_id=" . $myrow[user_id]."\">" . get_lang('UnRegister')."</a>";
			}
			else
			{
				$row[] = '';
			}

			$user_data[] = $row;
		}

		$query_vars['action'] = 'manage_members';
		$query_vars['blog_id'] = $blog_id;
		echo '<form method="post" action="blog.php?action=manage_members&amp;blog_id=' . $blog_id . '">';
		Display::display_sortable_table($column_header, $user_data,null,null,$query_vars);
		$link = '';
		$link .= isset ($_GET['action']) ? 'action=' . $_GET['action'] . '&amp;' : '';
		$link .= "blog_id=$blog_id&amp;";
		$link .= isset ($_GET['page_nr']) ? 'page_nr=' . (int)$_GET['page_nr'] . '&amp;' : '';
		$link .= isset ($_GET['per_page']) ? 'per_page=' . (int)$_GET['per_page'] . '&amp;' : '';
		$link .= isset ($_GET['column']) ? 'column=' . (int)$_GET['column'] . '&amp;' : '';
		$link .= isset ($_GET['direction']) ? 'direction=' . $_GET['direction'] . '&amp;' : '';;
		echo '<a href="blog.php?' . $link . 'selectall=unsubscribe">' . get_lang('SelectAll') . '</a> - ';
		echo '<a href="blog.php?' . $link . '">' . get_lang('UnSelectAll') . '</a> ';
		echo get_lang('WithSelected') . ' : ';
		echo '<select name="action">';
		echo '<option value="select_unsubscribe">' . get_lang('UnRegister') . '</option>';
		echo '</select>';
		echo '<input type="hidden" name="unregister" value="true" />';
		echo '<input type="submit" value="' . get_lang('Ok') . '"/>';
		echo '</form>';
	}

	/**
	 * Displays a matrix with selectboxes. On the left: users, on top: possible rights.
	 * The blog admin can thus select what a certain user can do in the current blog
	 *
	 * @param Integer $blog_id
	 */
	function display_form_user_rights($blog_id)
	{
		// Init
		$tbl_users 			= Database::get_main_table(TABLE_MAIN_USER);
		$tbl_blogs_rel_user = Database::get_course_table(TABLE_BLOGS_REL_USER);

		echo '<span class="blogpost_title">' . get_lang('RightsManager') . '</span>';

		// Integration of patricks permissions system.
		include_once('../permissions/blog_permissions.inc.php');
	}

	/**
	 * Displays the form to create a new post
	 * @author Toon Keppens
	 *
	 * @param Integer $blog_id
	 */
	function display_new_comment_form($blog_id, $post_id, $title)
	{
		echo '<form name="add_post" enctype="multipart/form-data" method="post" action="blog.php?action=view_post&amp;blog_id=' . $blog_id . '&amp;post_id=' . $post_id . '">
				<div class="form_header">'.(isset($_GET['task_id']) ? get_lang('ExecuteThisTask') : get_lang('NewComment')) . '</div>
					<table width="100%" border="0" cellspacing="2" cellpadding="0" class="new_comment">
						<tr>
					   <td width="100" valign="top">' . get_lang('Title') . ':&nbsp;&nbsp;</td>
					   <td><input name="comment_title" id="comment_title" type="text" size="60" value="Re: '.stripslashes($title) . '" /><br /><br /></td>
						</tr>
						<tr>
					   <td valign="top">' . get_lang('Comment') . ':&nbsp;&nbsp;</td>
					   <td>';
									$oFCKeditor = new FCKeditor('comment_text') ;
									$oFCKeditor->BasePath	= api_get_path(WEB_PATH) . 'main/inc/lib/fckeditor/' ;
									$oFCKeditor->Height		= '200';
									$oFCKeditor->Width		= '97%';
									$oFCKeditor->Value		= isset($_POST['comment_text'])?stripslashes($_POST['comment_text']):'';
									$oFCKeditor->Config['CustomConfigurationsPath'] = api_get_path(REL_PATH)."main/inc/lib/fckeditor/myconfig.js";
									$oFCKeditor->Config['IMUploadPath'] = "upload/blog/";
									$oFCKeditor->ToolbarSet = "Blog";

									$TBL_LANGUAGES = Database::get_main_table(TABLE_MAIN_LANGUAGE);
									$sql="SELECT isocode FROM ".$TBL_LANGUAGES." WHERE english_name='".mysql_real_escape_string($_SESSION["_course"]["language"])."'";
									$result_sql=api_sql_query($sql);
									$isocode_language=mysql_result($result_sql,0,0);
									$oFCKeditor->Config['DefaultLanguage'] = $isocode_language;
									
									$oFCKeditor->Config['InDocument'] = false;		
									$oFCKeditor->Config['CreateDocumentDir'] = '../../courses/'.api_get_course_path().'/document/';
		
		
									
									$oFCKeditor->Create() ;
		echo '			 <br /></td>
						</tr>
							 
							<tr><td><b>'.get_lang('AddAnAttachment').'</b><br /><br /></td></tr>	
							<tr><td width="80" valign="top">' . ucwords(get_lang('FileName') ). ':&nbsp;&nbsp;</td>
						    <td><input type="file" name="user_upload"/></td><br></tr>						    
						    <tr><td width="80" valign="top">' .get_lang('FileComment'). ':&nbsp;&nbsp;</td>
						    <td><br /><textarea name="post_file_comment" cols="34" /></textarea></td></tr>
							<tr>	
								
								
								
						<tr>
							<td >&nbsp;</td>
							<td>
							 <input type="hidden" name="action" value="" />
							 <input type="hidden" name="comment_parent_id" id="comment_parent_id" value="0" />';
									if(isset($_GET['task_id']))
									{
										echo ' <input type="hidden" name="new_task_execution_submit" value="true" />';
										echo ' <input type="hidden" name="task_id" value="' . (int)$_GET['task_id'] . '" />';
									}
									else
									{
										echo ' <input type="hidden" name="new_comment_submit" value="true" />';
									}
		echo '					<input type="submit" name="Submit" value="' . get_lang('Ok') . '" />
							</td>
						</tr>
					</table>
				</form>';
	}


	/**
	 * show the calender of the given month
	 * @author Patrick Cool
	 * @author Toon Keppens
	 *
	 * @param Array $blogitems an array containing all the blog items for the given month
	 * @param Integer $month: the integer value of the month we are viewing
	 * @param Integer $year: the 4-digit year indication e.g. 2005
	 * @param String $monthName: the language variable for the mont name
	 *
	 * @return html code
	*/
	function display_minimonthcalendar($month, $year, $blog_id)
	{
		// Init
		global $_user;
		global $DaysShort;
		global $MonthsLong;

		$posts = array();
		$tasks = array();

		$tbl_users = Database::get_main_table(TABLE_MAIN_USER);
		$tbl_blogs_posts = Database::get_course_table(TABLE_BLOGS_POSTS);
		$tbl_blogs_tasks = Database::get_course_table(TABLE_BLOGS_TASKS);
		$tbl_blogs_tasks_rel_user = Database::get_course_table(TABLE_BLOGS_TASKS_REL_USER);
		$tbl_blogs = Database::get_course_table(TABLE_BLOGS);

		//Handle leap year
		$numberofdays = array (0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

		if(($year % 400 == 0) or ($year % 4 == 0 and $year % 100 <> 0))
			$numberofdays[2] = 29;

		//Get the first day of the month
		$dayone = getdate(mktime(0, 0, 0, $month, 1, $year));		
		$monthName = $MonthsLong[$month-1];

		//Start the week on monday
		$startdayofweek = $dayone['wday'] <> 0 ? ($dayone['wday'] - 1) : 6;
		$backwardsURL = api_get_self()."?blog_id=" . (int)$_GET['blog_id']."&amp;filter=" . $_GET['filter']."&amp;month=". ($month == 1 ? 12 : $month -1)."&amp;year=". ($month == 1 ? $year -1 : $year);
		$forewardsURL = api_get_self()."?blog_id=" . (int)$_GET['blog_id']."&amp;filter=" . $_GET['filter']."&amp;month=". ($month == 12 ? 1 : $month +1)."&amp;year=". ($month == 12 ? $year +1 : $year);

		// Get posts for this month
		$sql = "SELECT post.*, DAYOFMONTH(`date_creation`) as post_day, user.lastname, user.firstname FROM $tbl_blogs_posts post
				INNER JOIN $tbl_users user
				ON post.author_id = user.user_id
				WHERE post.blog_id = '".(int)$blog_id."'
				AND MONTH(date_creation) = '".(int)$month."'
				AND YEAR(date_creation) = '".(int)$year."'
				ORDER BY date_creation";
		$result = api_sql_query($sql, __FILE__, __LINE__);

		// We will create an array of days on which there are posts.
		if( Database::num_rows($result) > 0)
		{
			while($blog_post = mysql_fetch_array($result))
			{
				// If the day of this post is not yet in the array, add it.
				if(!in_array($blog_post['post_day'], $posts))
					$posts[] = $blog_post['post_day'];
			}
		}

		// Get tasks for this month
		if($_user['user_id'])
		{
			$sql = "
				SELECT
					task_rel_user.*,
					DAYOFMONTH(`target_date`) as task_day,
					task.title,
					blog.blog_name
				FROM $tbl_blogs_tasks_rel_user task_rel_user
				INNER JOIN $tbl_blogs_tasks task ON task_rel_user.task_id = task.task_id
				INNER JOIN $tbl_blogs blog ON task_rel_user.blog_id = blog.blog_id
				WHERE task_rel_user.user_id = '".(int)$_user['user_id']."'
				AND	MONTH(`target_date`) = '".(int)$month."'
				AND	YEAR(`target_date`) = '".(int)$year."'
				ORDER BY `target_date` ASC";
			$result = api_sql_query($sql, __FILE__, __LINE__);

			if(mysql_numrows($result) > 0)
			{
				while($mytask = mysql_fetch_array($result))
				{

					$tasks[$mytask['task_day']][$mytask['task_id']]['task_id'] = $mytask['task_id'];
					$tasks[$mytask['task_day']][$mytask['task_id']]['title'] = $mytask['title'];
					$tasks[$mytask['task_day']][$mytask['task_id']]['blog_id'] = $mytask['blog_id'];
					$tasks[$mytask['task_day']][$mytask['task_id']]['blog_name'] = $mytask['blog_name'];
					$tasks[$mytask['task_day']][$mytask['task_id']]['day'] = $mytask['task_day'];
					//echo '<li><a href="blog.php?action=execute_task&amp;blog_id=' . $mytask['blog_id'] . '&amp;task_id='.stripslashes($mytask['task_id']) . '" title="[Blog: ' . $mytask['blog_name'] . '] ' . get_lang('ExecuteThisTask') . '">'.stripslashes($mytask['title']) . '</a></li>';
				}
			}
		}

		echo 	'<table id="smallcalendar" class="data_table">',
				"<tr id=\"title\">\n",
				"<th width=\"10%\"><a href=\"", $backwardsURL, "\">&laquo;</a></th>\n",
				"<th align=\"center\" width=\"80%\" colspan=\"5\">", $monthName, " ", $year, "</th>\n",
				"<th width=\"10%\" align=\"right\"><a href=\"", $forewardsURL, "\">&raquo;</a></th>\n", "</tr>\n";

		echo "<tr>\n";

		for($ii = 1; $ii < 8; $ii ++)
			echo "<td class=\"weekdays\">", $DaysShort[$ii % 7], "</td>\n";

		echo "</tr>\n";

		$curday = -1;
		$today = getdate();

		while($curday <= $numberofdays[$month])
		{
			echo "<tr>\n";

			for($ii = 0; $ii < 7; $ii ++)
			{
				if(($curday == -1) && ($ii == $startdayofweek))
					$curday = 1;

				if(($curday > 0) && ($curday <= $numberofdays[$month]))
				{
					$bgcolor = $ii < 5 ? $class="class=\"days_week\"" : $class="class=\"days_weekend\"";
					$dayheader = "$curday";

					if(($curday == $today[mday]) && ($year == $today[year]) && ($month == $today[mon]))
					{
						$dayheader = "$curday";
						$class = "class=\"days_today\"";
					}

					echo "\t<td " . $class.">";

					// If there are posts on this day, create a filter link.
					if(in_array($curday, $posts))
						echo '<a href="blog.php?blog_id=' . $blog_id . '&amp;filter=' . $year . '-' . $month . '-' . $curday . '&amp;month=' . $month . '&amp;year=' . $year . '" title="' . get_lang('ViewPostsOfThisDay') . '">' . $curday . '</a>';
					else
						echo $dayheader;
			
					if (count($tasks) > 0) 
					{
						if (is_array($tasks[$curday])) 
						{
							// Add tasks to calendar
							foreach ($tasks[$curday] as $task)
							{
								echo '<a href="blog.php?action=execute_task&amp;blog_id=' . $task['blog_id'] . '&amp;task_id='.stripslashes($task['task_id']) . '" title="� ' . $task['title'] . ' � ' . get_lang('InBlog') . ' � ' . $task['blog_name'] . ' � - ' . get_lang('ExecuteThisTask') . '"><img src="../img/blog_task.gif" alt="Task" /></a>';
							}
						}
					}
					
					echo "</td>\n";

					$curday ++;
				}
				else
					echo "<td>&nbsp;</td>\n";
			}

			echo "</tr>\n";
		}

		echo "</table>\n";
	}

	/**
	 * Blog admin | Display the form to add a new blog.
	 *
	 */
	function display_new_blog_form()
	{
		echo '<form name="add_blog" method="post" action="blog_admin.php">
					<table width="100%" border="0" cellspacing="2" cellpadding="0" class="newBlog">
					  <tr>
					  	<td></td>
					  	<td><b>' . get_lang('AddBlog') . '</b><br /><br /></td>
					  </tr>
						<tr>
					   <td align="right">' . get_lang('Title') . ':&nbsp;&nbsp;</td>
					   <td><input name="blog_name" type="text" size="100" /></td>
						</tr>
						<tr>
					   <td align="right">' . get_lang('Subtitle') . ':&nbsp;&nbsp;</td>
					   <td><input name="blog_subtitle" type="text" size="100" /></td>
						</tr>
						<tr>
							<td align="right">&nbsp;</td>
							<input type="hidden" name="action" value="" />
							<input type="hidden" name="new_blog_submit" value="true" />
							<td><br /><input type="submit" name="Submit" value="' . get_lang('Ok') . '" /></td>
						</tr>
					</table>
				</form>';
	}

	/**
	 * Blog admin | Display the form to edit a blog.
	 *
	 */
	function display_edit_blog_form($blog_id)
	{
		// Init
		$tbl_blogs = Database::get_course_table(TABLE_BLOGS);

		$sql = "SELECT blog_id, blog_name, blog_subtitle FROM $tbl_blogs WHERE blog_id = '".(int)$blog_id."'";
		$result = api_sql_query($sql, __FILE__, __LINE__);
		$blog = mysql_fetch_array($result);

		echo '<form name="edit_blog" method="post" action="blog_admin.php">
					<table width="100%" border="0" cellspacing="2" cellpadding="0" class="newBlog">
					  <tr>
					  	<td></td>
					  	<td><b>' . get_lang('EditBlog') . '</b><br /><br /></td>
					  </tr>
						<tr>
					   <td align="right">' . get_lang('Title') . ':&nbsp;&nbsp;</td>
					   <td><input name="blog_name" type="text" size="100" value="' . $blog['blog_name'] . '" /></td>
						</tr>
						<tr>
					   <td align="right">' . get_lang('Subtitle') . ':&nbsp;&nbsp;</td>
					   <td><input name="blog_subtitle" type="text" size="100" value="' . $blog['blog_subtitle'] . '" /></td>
						</tr>
						<tr>
							<td align="right">&nbsp;</td>
							<input type="hidden" name="action" value="" />
							<input type="hidden" name="edit_blog_submit" value="true" />
							<input type="hidden" name="blog_id" value="' . $blog['blog_id'] . '" />
							<td><br /><input type="submit" name="Submit" value="' . get_lang('Ok') . '" /></td>
						</tr>
					</table>
				</form>';
	}

	/**
	 * Blog admin | Returns table with blogs in this course
	 */
	function display_blog_list()
	{
		global $charset;
		// Init
		$counter = 0;
		$tbl_blogs = Database::get_course_table(TABLE_BLOGS);


		$sql = "SELECT `blog_id`, `blog_name`, `blog_subtitle`, `visibility` FROM $tbl_blogs ORDER BY `blog_name`";
		$result = api_sql_query($sql, __FILE__, __LINE__);

		while($blog = mysql_fetch_array($result))
		{
			$counter++;
			$css_class = (($counter % 2)==0) ? "row_odd" : "row_even";
			$visibility_icon = ($blog['visibility'] == '0') ? "invisible.gif" : "visible.gif";
			$visibility_class = ($blog['visibility'] == '0') ? ' class="invisible"' : "";
			$visibility_set  = ($blog['visibility'] == '0') ? 1 : 0;

			echo	'<tr class="' . $css_class . '" valign="top">',
						 '<td width="290"' . $visibility_class . '>'.stripslashes($blog['blog_name']) . '</td>',
						 '<td' . $visibility_class . '>'.stripslashes($blog['blog_subtitle']) . '</td>',
						 '<td width="200">',
						 	'<a href="' .api_get_self(). '?action=edit&amp;blog_id=' . $blog['blog_id'] . '">',
							'<img src="../img/edit.gif" border="0" title="' . get_lang('EditBlog') . '" />',
							"</a>\n",
							'<a href="' .api_get_self(). '?action=delete&amp;blog_id=' . $blog['blog_id'] . '" ',
							'onclick="javascript:if(!confirm(\''.addslashes(htmlentities(get_lang("ConfirmYourChoice"),ENT_QUOTES,$charset)). '\')) return false;"',
							'<img src="../img/delete.gif" border="0" title="' . get_lang('DeleteBlog') . '" />',
							"</a>\n",
							'<a href="' .api_get_self(). '?action=visibility&amp;blog_id=' . $blog['blog_id'] . '">',
							'<img src="../img/' . $visibility_icon . '" border="0" title="' . get_lang('Visible') . '" />',
							"</a>\n",
						 '</td>',
					'</tr>';
		}
	}
}



/**
 * Show a list with all the attachments according the parameter's
 * @param the blog's id
 * @param the post's id 
 * @param the comment's id
 * @return array with the post info according the parameters  
 * @author Julio Montoya Dokeos
 * @version avril 2008, dokeos 1.8.5
 */ 
function get_blog_attachment($blog_id, $post_id=null,$comment_id=null)
{	
	global $blog_table_attachment;
	$row=array();
	$where='';
	
	if (!empty ($post_id))
	{
		$where.=' AND post_id ="'.$post_id.'" ';
	}
		
	if (!empty ($comment_id) )
	{
		if (!empty ($post_id) )
		{
			$where.= ' AND ';
		}
		$where.=' comment_id ="'.$comment_id.'" ';
	}
	
	$sql = 'SELECT path, filename, comment FROM '. $blog_table_attachment.' WHERE blog_id ="'.$blog_id.'"  '.$where;
	
	$result=api_sql_query($sql, __FILE__, __LINE__);
	if (Database::num_rows($result)!=0)
	{
		$row=Database::fetch_array($result);
	}
	return $row;	
}

/**
 * Delete the all the attachments according the parameters.
 * @param the blog's id
 * @param the post's id 
 * @param the comment's id
 * @author Julio Montoya Dokeos
 * @version avril 2008, dokeos 1.8.5
 */ 

function delete_all_blog_attachment($blog_id,$post_id=null,$comment_id=null)
{	
	global $blog_table_attachment;
	global $_course;
	
	// delete files in DB	
	if (!empty ($post_id))
	{
		$where.=' AND post_id ="'.$post_id.'" ';
	}
		
	if (!empty ($comment_id) )
	{
		if (!empty ($post_id) )
		{
			$where.= ' AND ';
		}
		$where.=' comment_id ="'.$comment_id.'" ';
	}
			
	// delete all files in directory
	$courseDir   = $_course['path'].'/upload/blog';
	$sys_course_path = api_get_path(SYS_COURSE_PATH);		
	$updir = $sys_course_path.$courseDir;
	
	$sql= 'SELECT path FROM '.$blog_table_attachment.' WHERE blog_id ="'.$blog_id.'"  '.$where;	
	$result=api_sql_query($sql, __FILE__, __LINE__);
	
	while ($row=Database::fetch_row($result))
	{
		$file=$updir.'/'.$row[0];											
		if (Security::check_abs_path($file,$updir) )
		{			
			@ unlink($file);
		}		
	}	
	$sql = 'DELETE FROM '. $blog_table_attachment.' WHERE blog_id ="'.$blog_id.'"  '.$where;	
	api_sql_query($sql, __FILE__, __LINE__);		
}


?>
