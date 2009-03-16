<?php // $Id: $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2008 Dokeos Latinoamerica SAC
	Copyright (c) 2006 Dokeos SPRL
	Copyright (c) 2006 Ghent University (UGent)
	Copyright (c) various contributors
	Copyright (c) Isaac Flores Paz <florespaz@bidsoftperu.com>
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
$language_file = 'gradebook';
//$cidReset = true;
require_once ('../inc/global.inc.php');
require_once ('lib/be.inc.php');
require_once ('lib/gradebook_functions.inc.php');
require_once ('lib/fe/evalform.class.php');
api_block_anonymous_users();
block_students();

$interbreadcrumb[] = array (
	'url' => $_SESSION['gradebook_dest'].'?selectcat='.Security::remove_XSS($_GET['selectcat']),
	'name' => get_lang('Gradebook'
));
Display :: display_header('');
/*
$t_linkeval_log = Database :: get_main_table(TABLE_MAIN_GRADEBOOK_LINKEVAL_LOG);
$t_user=	 Database :: get_main_table(TABLE_MAIN_USER);
$evaledit = Evaluation :: load($_GET['visiblelog']);
$sql="SELECT le.name,le.description,le.date_log,le.weight,le.visible,le.type,us.username from ".$t_linkeval_log." le inner join ".$t_user." us on le.user_id_log=us.user_id where id_linkeval_log=".$evaledit[0]->get_id()." and type='evaluation';";
$result=api_sql_query($sql);
	echo '<table width="100%" border="0" >';
		echo '<tr>';
		echo '<td align="center" class="gradebook-table-header"><strong>'.get_lang('GradebookNameLog').'</strong></td>';
		echo '<td align="center" class="gradebook-table-header"><strong>'.get_lang('GradebookDescriptionLog').'</strong></td>';
		echo '<td align="center" class="gradebook-table-header"><strong>'.get_lang('Date').'</strong></td>';
		echo '<td align="center" class="gradebook-table-header"><strong>'.get_lang('Weight').'</strong></td>';
		echo '<td align="center" class="gradebook-table-header"><strong>'.get_lang('GradebookVisibilityLog').'</strong></td>';
		echo '<td align="center" class="gradebook-table-header"><strong>'.get_lang('ResourceType').'</strong></td>';
		echo '<td align="center" class="gradebook-table-header"><strong>'.get_lang('GradebookWhoChangedItLog').'</strong></td>';
		echo '</tr>';
	while($row=Database::fetch_array($result)) {
	echo '<tr>';
		echo '<td align="center" Class="gradebook-table-body">'.$row[0].'</td>';
		echo '<td align="center" class="gradebook-table-body">'.$row[1].'</td>';
		echo '<td align="center" class="gradebook-table-body">'.date('d-m-Y H:i:s',$row[2]).'</td>';
		echo '<td align="center" class="gradebook-table-body">'.$row[3].'</td>';
		if (1 == $row[4]) {
			$visib=get_lang('GradebookVisible');
		} else {
			$visib=get_lang('GradebookInvisible');
		}
		echo '<td align="center" Class="gradebook-table-body">'.$visib.'</td>';
		echo '<td align="center" class="gradebook-table-body">'.$row[5].'</td>';
		echo '<td align="center" class="gradebook-table-body">'.$row[6].'</td>';
	echo '</tr>';
}
echo '</table>';*/


$t_linkeval_log = Database :: get_main_table(TABLE_MAIN_GRADEBOOK_LINKEVAL_LOG);
$t_user=	 Database :: get_main_table(TABLE_MAIN_USER);
$visible_log=Security::remove_XSS($_GET['visiblelog']);
$evaledit = Evaluation :: load($visible_log);
$sql="SELECT le.name,le.description,le.date_log,le.weight,le.visible,le.type,us.username from ".$t_linkeval_log." le inner join ".$t_user." us on le.user_id_log=us.user_id where id_linkeval_log=".$evaledit[0]->get_id()." and type='evaluation';";
$result=api_sql_query($sql);
$list_info=array();
while ($row=Database::fetch_row($result)) {
	$list_info[]=$row;
}

foreach($list_info as $key => $info_log) {
	$list_info[$key][2]=($info_log[2]) ? date('d-m-Y H:i:s',$info_log[2]) : '0000-00-00 00:00:00';
	$list_info[$key][4]=($info_log[4]==1) ? get_lang('GradebookVisible') : get_lang('GradebookInvisible');
}

$parameters=array('visiblelog'=>Security::remove_XSS($_GET['visiblelog']),'selectcat'=>Security::remove_XSS($_GET['selectcat']));
$table = new SortableTableFromArrayConfig($list_info, 1, count($list_info));
$table->set_additional_parameters($parameters);
$table->set_header(0, get_lang('GradebookNameLog'));
$table->set_header(1, get_lang('GradebookDescriptionLog'));
$table->set_header(2, get_lang('Date'));
$table->set_header(3, get_lang('Weight'));
$table->set_header(4, get_lang('GradebookVisibilityLog'));
$table->set_header(5, get_lang('ResourceType'));
$table->set_header(6, get_lang('GradebookWhoChangedItLog'));
$table->display();
Display :: display_footer();