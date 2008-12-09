<?php // $Id: $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2006-2008 Dokeos SPRL
	Copyright (c) 2006 Ghent University (UGent)
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
$language_file= 'gradebook';
$cidReset= true;
require_once ('../inc/global.inc.php');
$this_section = SECTION_MYGRADEBOOK;
require_once ('lib/be.inc.php');
require_once ('lib/scoredisplay.class.php');
require_once ('lib/gradebook_functions.inc.php');
require_once ('lib/fe/catform.class.php');
require_once ('lib/fe/evalform.class.php');
require_once ('lib/fe/linkform.class.php');
require_once ('lib/gradebook_data_generator.class.php');
require_once ('lib/fe/gradebooktable.class.php');
require_once ('lib/fe/displaygradebook.php');

api_block_anonymous_users();

if (!api_is_allowed_to_edit()) {
	header('Location: /index.php');
}
// --------------------------------------------------------------------------------
// -                       DISPLAY HEADERS AND MESSAGES                           -
// --------------------------------------------------------------------------------

if (!isset($_GET['exportpdf']) and !isset($_GET['export_certificate'])) {
	if (isset ($_GET['studentoverview'])) {
		$interbreadcrumb[]= array (
			'url' => 'gradebook.php?selectcat=' . Security::remove_XSS($_GET['selectcat']),
			'name' => get_lang('Gradebook')
		);
		Display :: display_header(get_lang('FlatView'));
	} elseif (isset ($_GET['search'])) {
		$interbreadcrumb[]= array (
			'url' => 'gradebook.php?selectcat=' . Security::remove_XSS($_GET['selectcat']),
			'name' => get_lang('Gradebook')
		);
		Display :: display_header(get_lang('SearchResults'));
	} else {
		$interbreadcrumb[]= array (
			'url' => 'index.php?',
			'name' => get_lang('Gradebook'));
		
		$interbreadcrumb[]= array (
			'url' => 'index.php?&selectcat='.Security::remove_XSS($_GET['selectcat']),
			'name' => get_lang('Details'));

		Display :: display_header('');

	}
}


$table_link = Database::get_main_table(TABLE_MAIN_GRADEBOOK_LINK);
$table_evaluation = Database::get_main_table(TABLE_MAIN_GRADEBOOK_EVALUATION);
$table_forum_thread=Database::get_course_table(TABLE_FORUM_THREAD);
/*
if($_SERVER['REQUEST_METHOD']=='POST'):
	foreach($_POST['link'] as $key => $value){
		api_sql_query('UPDATE '.$table_link.' SET weight = '."'".$value."'".' WHERE id = '.$key);
	}
	foreach($_POST['evaluation'] as $key => $value){
		api_sql_query('UPDATE '.$table_evaluation.' SET weight = '."'".$value."'".' WHERE id = '.$key);
	}
	Display :: display_normal_message(get_lang('GradebookWeightUpdated')) . '<br /><br />';
endif;*/
/*
define('LINK_EXERCISE',1);
define('LINK_DROPBOX',2);
define('LINK_STUDENTPUBLICATION',3);
define('LINK_LEARNPATH',4);
define('LINK_FORUM_THREAD',5);
*/
$table_evaluated[1] = array(TABLE_QUIZ_TEST, 'title', 'id', get_lang('Exercise'));
$table_evaluated[2] = array(TABLE_DROPBOX_FILE, 'name','id', get_lang('Dropbox'));
$table_evaluated[3] = array(TABLE_STUDENT_PUBLICATION, 'url','id', get_lang('Student_publication'));
$table_evaluated[4] = array(TABLE_LP_MAIN, 'name','id', get_lang('Learnpath'));
$table_evaluated[5] = array(TABLE_FORUM_THREAD, 'thread_title_qualify', 'thread_id', get_lang('Forum'));


function get_table_type_course($type,$course) {
	global $_configuration;
	global $table_evaluated;
	return Database::get_course_table($table_evaluated[$type][0],$_configuration['db_prefix'].$course);
}
$submitted=isset($_POST['submitted'])?$_POST['submitted']:'';
if($submitted==1) {
	Display :: display_normal_message(get_lang('GradebookWeightUpdated')) . '<br /><br />';
	if (isset($_POST['evaluation'])) {
		require_once 'lib/be/evaluation.class.php';
		$eval_log = new Evaluation();
	}
	
	if(isset($_POST['link'])){
		require_once 'lib/be/abstractlink.class.php';
		//$eval_link_log = new AbstractLink();
	}
	
}

$category_id = (int)$_GET['selectcat'];
$output='';
$sql='SELECT * FROM '.$table_link.' WHERE category_id = '.$category_id;
$result = api_sql_query($sql,__FILE__,__LINE__);
	while($row = Database ::fetch_array($result)){
	
		//update only if value changed
		if(isset($_POST['link'][$row['id']]) && $_POST['link'][$row['id']] != $row['weight']) {
			api_sql_query('UPDATE '.$table_link.' SET weight = '."'".trim($_POST['link'][$row['id']])."'".' WHERE id = '.$row['id'],__FILE__,__LINE__);
			api_sql_query('UPDATE '.$table_forum_thread.' SET thread_weight='.$_POST['link'][$row['id']].' WHERE thread_id='.$row['ref_id']);
			AbstractLink::add_link_log($row['id']);
			$row['weight'] = trim($_POST['link'][$row['id']]);
		}

		$tempsql = api_sql_query('SELECT * FROM '.get_table_type_course($row['type'],$row['course_code']).' WHERE '.$table_evaluated[$row['type']][2].' = '.$row['ref_id']);
		$resource_name = Database ::fetch_array($tempsql);	
	
		$output.= '<tr><td> [ '.$table_evaluated[$row['type']][3].' ] '.$resource_name[$table_evaluated[$row['type']][1]].'</td><td><input size="10" type="text" name="link['.$row['id'].']" value="'.$row['weight'].'"/></td></tr>';	
	}

	$sql = api_sql_query('SELECT * FROM '.$table_evaluation.' WHERE category_id = '.$category_id,__FILE__,__LINE__);
	while($row = Database ::fetch_array($sql)) {
	
		//update only if value changed
		if(isset($_POST['evaluation'][$row['id']]) && $_POST['evaluation'][$row['id']] != $row['weight']) {
			api_sql_query('UPDATE '.$table_evaluation.' SET weight = '."'".trim($_POST['evaluation'][$row['id']])."'".' WHERE id = '.$row['id'],__FILE__,__LINE__);
			AbstractLink::add_link_log($row['id']);
			$row['weight'] = trim($_POST['evaluation'][$row['id']]);
		}

	$output.= '<tr><td> [ '.get_lang('Evaluation').$table_evaluated[$row['type']][3].' ] '.$row['name'].'</td><td><input type="text" size="10" name="evaluation['.$row['id'].']" value="'.$row['weight'].'"/></td></tr>';	
}
?>
<a href="/main/gradebook/index.php?<?php echo api_get_cidreq() ?>&selectcat=<?php echo $category_id ?>"><< <?php echo get_lang('Back') ?>
<!--<a href="/main/gradebook/index.php?<?php echo api_get_cidreq() ?>&selectcat=<?php echo $category_id ?>"><< <?php echo get_lang('Back') ?></a>-->
<form method="post" action="gradebook_edit_all.php?<?php echo api_get_cidreq() ?>&selectcat=<?php echo $category_id?>">
<table class="data_table">
		 <tr class="row_odd">
		  <th><?php echo get_lang('Resource'); ?></th>
		  <th><?php echo get_lang('Weight'); ?></th>
		 </tr>
		 <?php echo $output ?>
 </table>
 <input type="hidden" name="submitted" value="1" />
  <input type="submit" name="name" value="<?php echo get_lang('Save') ?>"/>
</form>
<?php
Display :: display_footer();
?>