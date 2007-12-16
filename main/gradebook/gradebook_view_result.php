<?php
// $Id: gradebook_view_result.php 1020 2007-05-11 08:20:27Z stijn $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2006 Dokeos S.A.
	Copyright (c) 2006 Ghent University (UGent)
	Copyright (c) various contributors

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
$language_file= 'gradebook';
$cidReset= true;
include_once ('../inc/global.inc.php');
include_once ('lib/be.inc.php');
include_once ('lib/gradebook_functions.inc.php');
include_once ('lib/fe/displaygradebook.php');
include_once ('lib/fe/evalform.class.php');
include_once ('lib/fe/dataform.class.php');
include_once (api_get_path(LIBRARY_PATH) . 'fileManage.lib.php');
include_once (api_get_path(LIBRARY_PATH) . 'export.lib.inc.php');
include_once (api_get_path(LIBRARY_PATH) . 'import.lib.php');
include_once ('lib/results_data_generator.class.php');
include_once ('lib/fe/resulttable.class.php');
include_once ('lib/fe/exportgradebook.php');
include_once ('lib/scoredisplay.class.php');
include_once (api_get_path(LIBRARY_PATH).'ezpdf/class.ezpdf.php');
api_block_anonymous_users();
block_students();
$interbreadcrumb[]= array (
	'url' => 'gradebook.php',
	'name' => get_lang('Gradebook'
));
//load the evaluation & category
$displayscore = Scoredisplay :: instance();
$eval= Evaluation :: load($_GET['selecteval']);
$overwritescore= 0;
if ($eval[0]->get_category_id() < 0)
{
	// if category id is negative, then the evaluation's origin is a link
	$link= LinkFactory :: get_evaluation_link($eval[0]->get_id());
	$currentcat= Category :: load($link->get_category_id());
} else
	$currentcat= Category :: load($eval[0]->get_category_id());
//load the result with the evaluation id
function overwritescore($resid, $importscore, $eval_max)
{
	$result= Result :: load($resid);
	if ($importscore > $eval_max)
	{
		header('Location: gradebook_view_result.php?selecteval=' . $_GET['selecteval'] . '&overwritemax=');
		exit;
	}
	$result[0]->set_score($importscore);
	$result[0]->save();
	unset ($result);
}
if (isset ($_GET['selecteval']))
{
	$allresults= Result :: load(null, null, $_GET['selecteval']);
	$iscourse= $currentcat[0]->get_course_code() == null ? 1 : 0;
}
/**
 * XML-parser: handle start of element
 */
function element_start($parser, $data)
{
	global $user;
	global $current_tag;
	switch ($data)
	{
		case 'Result' :
			$user= array ();
			break;
		default :
			$current_tag= $data;
	}
}
/**
 * XML-parser: handle end of element
 */
function element_end($parser, $data)
{
	global $user;
	global $users;
	global $current_value;
	switch ($data)
	{
		case 'Result' :
			$users[]= $user;
			break;
		default :
			$user[$data]= $current_value;
			break;
	}
}
/**
 * XML-parser: handle character data
 */
function character_data($parser, $data)
{
	global $current_value;
	$current_value= $data;
}
/**
 * Read the XML-file
 * @param string $file Path to the XML-file
 * @return array All userinformation read from the file
 */
function parse_xml_data($file)
{
	global $current_tag;
	global $current_value;
	global $user;
	global $users;
	$users= array ();
	$parser= xml_parser_create();
	xml_set_element_handler($parser, 'element_start', 'element_end');
	xml_set_character_data_handler($parser, "character_data");
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
	xml_parse($parser, file_get_contents($file));
	xml_parser_free($parser);
	return $users;
}
if (isset ($_GET['editres']))
{
	$resultedit= Result :: load($_GET['editres']);
	$edit_res_form= new EvalForm(EvalForm :: TYPE_RESULT_EDIT, $eval[0], $resultedit[0], 'edit_result_form', null, api_get_self() . '?editres=' . $resultedit[0]->get_id() . '&selecteval=' . $_GET['selecteval']);
	if ($edit_res_form->validate())
	{
		$values= $edit_res_form->exportValues();
		$result= new Result();
		$result->set_id($_GET['editres']);
		$result->set_user_id($values['hid_user_id']);
		$result->set_evaluation_id($_GET['selecteval']);
		if ((!empty ($values['score'])) || ($values['score'] == '0'))
			$result->set_score($values['score']);
		$result->save();
		unset ($result);
		header('Location: gradebook_view_result.php?selecteval=' . $_GET['selecteval'] . '&editresmessage=');
		exit;
	}
}
if (isset ($_GET['import']))
{
	$interbreadcrumb[]= array (
		'url' => 'gradebook_view_result.php?selecteval=' . $_GET['selecteval'],
		'name' => get_lang('ViewResult'
	));
	$import_result_form= new DataForm(DataForm :: TYPE_IMPORT, 'import_result_form', null, api_get_self() . '?import=&selecteval=' . $_GET['selecteval']);
	if (!$import_result_form->validate())
		Display :: display_header(get_lang('Import'));
	if ($_POST['formSent'])
	{
		if (!empty ($_FILES['import_file']['name']))
		{
			$values= $import_result_form->exportValues();
			$file_type= $_POST['file_type'];
			$file_name= $_FILES['import_file']['tmp_name'];
			if ($file_type == 'csv')
			{
				$results= Import :: csv_to_array($file_name);
			} else
			{
				$results= parse_xml_data($file_name);
			}
			//var_dump($results);
			$nr_results_added= 0;
			foreach ($results as $index => $importedresult)
			{
				//check username & score
				$added= '0';
				foreach ($allresults as $allresult)
				{
					if (($importedresult['user_id'] == $allresult->get_user_id()))
					{
						if ($importedresult['score'] != $allresult->get_score())
						{
							if (!isset ($values['overwrite']))
							{
								header('Location: gradebook_view_result.php?selecteval=' . $_GET['selecteval'] . '&import_score_error=' . $importedresult['user_id']);
								exit;
								break;
							} else
							{
								overwritescore($allresult->get_id(), $importedresult['score'], $eval[0]->get_max());
								$overwritescore++;
								$added= '1';
							}
						} else
							$added= '1';
					}
				}
				if ($importedresult['user_id'] == null)
				{
					header('Location: gradebook_view_result.php?selecteval=' . $_GET['selecteval'] . '&incorrectdata=');
					exit;
				}
				$userinfo= get_user_info_from_id($importedresult['user_id']);
				if ($userinfo['lastname'] != $importedresult['lastname'] || $userinfo['firstname'] != $importedresult['firstname'] || $userinfo['official_code'] != $importedresult['official_code'])
				{
					if (!isset ($values['ignoreerrors']))
					{
						header('Location: gradebook_view_result.php?selecteval=' . $_GET['selecteval'] . '&import_user_error=' . $importedresult['user_id']);
						exit;
					}
				}
				if ($added != '1')
				{
					if ($importedresult['score'] > $eval[0]->get_max())
					{
						header('Location: gradebook_view_result.php?selecteval=' . $_GET['selecteval'] . '&overwritemax=');
						exit;
					}
					$result= new Result();
					$result->set_user_id($importedresult['user_id']);
					if (!empty ($importedresult['score']))
					{
						$result->set_score($importedresult['score']);
					}
					if (!empty ($importedresult['date']))
					{
						$result->set_date(strtotime($importedresult['date']));
					} else
					{
						$result->set_date(time());
					}
					$result->set_evaluation_id($_GET['selecteval']); //var_dump($result);
					$result->add();
					$nr_results_added++;
				}
			}
		} else
		{
			header('Location: ' . api_get_self() . '?import=&selecteval=' . $_GET['selecteval'] . '&importnofile=');
			exit;
		}
		if ($overwritescore != 0)
		{
			header('Location: ' . api_get_self() . '?selecteval=' . $_GET['selecteval'] . '&importoverwritescore=' . $overwritescore);
			exit;
		}
		if ($nr_results_added == 0)
		{
			header('Location: ' . api_get_self() . '?selecteval=' . $_GET['selecteval'] . '&nothingadded=');
			exit;
		}
		header('Location: ' . api_get_self() . '?selecteval=' . $_GET['selecteval'] . '&importok=');
		exit;
	}
}
if (isset ($_GET['export']))
{
	$interbreadcrumb[]= array (
		'url' => 'gradebook_view_result.php?selecteval=' . $_GET['selecteval'],
		'name' => get_lang('ViewResult'
	));
	$export_result_form= new DataForm(DataForm :: TYPE_EXPORT, 'export_result_form', null, api_get_self() . '?export=&selecteval=' . $_GET['selecteval'], '_blank');
	if (!$export_result_form->validate())
		Display :: display_header(get_lang('Export'));
	if ($export_result_form->validate())
	{
		$export= $export_result_form->exportValues();
		$file_type= $export['file_type'];
		$filename= 'export_results_' . date('Y-m-d_H-i-s');
		$results= Result :: load(null, null, $_GET['selecteval']);
		$data= array (); //when file type is csv, add a header to the output file
		if ($file_type == 'csv')
		{
			$alldata[]= array (
				'user_id',
				'official_code',
				'lastname',
				'firstname',
				'score',
				'date'
			);
		}
		if ($file_type == 'pdf')
		{
			if (($eval[0]->has_results()))
			{
				$score= $eval[0]->calc_score();
				if ($score != null)
					$average= get_lang('Average') . ' : ' . round(100 * ($score[0] / $score[1])) . ' %';
			}
			if ($eval[0]->get_course_code() == null)
				$course= get_lang('CourseIndependent');
			else
				$course= get_course_name_from_code($eval[0]->get_course_code());
			$pdf= new Cezpdf();
			$pdf->selectFont(api_get_path(LIBRARY_PATH).'ezpdf/fonts/Helvetica.afm');
			$pdf->ezSetMargins(30, 30, 50, 30);
			$pdf->ezSetY(800);
			$pdf->ezText(get_lang('EvaluationName') . ' : ' . $eval[0]->get_name() . ' (' . date('j/n/Y g:i', $eval[0]->get_date()) . ')', 12, array (
				'justification' => 'left'
			));
			$pdf->ezText(get_lang('Description') . ' : ' . $eval[0]->get_description());
			$pdf->ezText(get_lang('Course') . ' : ' . $course, 12, array (
				'justification' => 'left'
			));
			$pdf->ezText(get_lang('Weight') . ' : ' . $eval[0]->get_weight(), 12, array (
				'justification' => 'left'
			));
			$pdf->ezText(get_lang('Max') . ' : ' . $eval[0]->get_max(), 12, array (
				'justification' => 'left'
			));
			$pdf->ezText($average, 12, array (
				'justification' => 'left'
			));
			
			$datagen = new ResultsDataGenerator ($eval[0],$allresults);
			$data_array = $datagen->get_data(ResultsDataGenerator :: RDG_SORT_LASTNAME,0,null,true);	
			$newarray = array();
			foreach ($data_array as $data)
			{
				$newitem = array();
				$newitem[] = $data['lastname'];
				$newitem[] = $data['firstname'];
				$newitem[] = $data['score'];
				if ($displayscore->is_custom())
					$newitem[] = $data['display'];
				$newarray[] = $newitem;
			}
			$pdf->ezSetY(650);
			if ($displayscore->is_custom())
				$header_names = array(get_lang('LastName'),get_lang('FirstName'),get_lang('Score'),get_lang('Display'));
			else
				$header_names = array(get_lang('LastName'),get_lang('FirstName'),get_lang('Score'));
		
			$pdf->ezTable($newarray,$header_names,'',array('showHeadings'=>1,'shaded'=>1,'showLines'=>1,'rowGap'=>3,'width'=> 500));
			$pdf->ezStream();
			exit;
		}
		foreach ($results as $result)
		{
			$userinfo= get_user_info_from_id($result->get_user_id());
			$data['user_id']= $result->get_user_id();
			$data['official_code']= $userinfo['official_code'];
			$data['lastname']= $userinfo['lastname'];
			$data['firstname']= $userinfo['firstname'];
			$data['score']= $result->get_score();
			$data['date']= date('Y-n-j g:i', $result->get_date());
			$alldata[]= $data;
		}
		switch ($file_type)
		{
			case 'xml' :
				Export :: export_table_xml($alldata, $filename, 'Result', 'XMLResults');
				break;
			case 'csv' :
				Export :: export_table_csv($alldata, $filename);
				break;
		}
	}
}
if (isset ($_GET['resultdelete']))
{
	$result= Result :: load($_GET['resultdelete']);
	$result[0]->delete();
	header('Location: gradebook_view_result.php?deleteresult=&selecteval=' . $_GET['selecteval']);
	exit;
}
if (isset ($_POST['action']))
{
	$number_of_selected_items= count($_POST['id']);
	if ($number_of_selected_items == '0')
		Display :: display_warning_message(get_lang('NoItemsSelected'),false);
	else
	{
		switch ($_POST['action'])
		{
			case 'delete' :
				$number_of_deleted_results= 0;
				foreach ($_POST['id'] as $indexstr)
				{
					$result= Result :: load($indexstr);
					$result[0]->delete();
					$number_of_deleted_results++;
				}
				header('Location: gradebook_view_result.php?massdelete=&selecteval=' . $_GET['selecteval']);
				exit;
				break;
		}
	}
} // TODO - what if selecteval not set ?
$addparams= array (
'selecteval' => $eval[0]->get_id());
if (isset ($_GET['print']))
{
	$datagen = new ResultsDataGenerator ($eval[0],$allresults);
	$data_array = $datagen->get_data(ResultsDataGenerator :: RDG_SORT_LASTNAME,0,null,true);	
			if ($displayscore->is_custom())
				$header_names = array(get_lang('LastName'),get_lang('FirstName'),get_lang('Score'),get_lang('Display'));
			else
				$header_names = array(get_lang('LastName'),get_lang('FirstName'),get_lang('Score'));
	$newarray = array();
	foreach ($data_array as $data)
		$newarray[] = array_slice($data, 2);		

	echo print_table($newarray, $header_names,get_lang('ViewResult'), $eval[0]->get_name());
	exit;
} else
	$resulttable= new ResultTable($eval[0], $allresults, $iscourse, $addparams);
$htmlHeadXtra[]= '<script type="text/javascript">
function confirmationuser ()
{
	if (confirm("' . get_lang('DeleteUser') . '?"))
		{return true;}
	else
		{return false;}
}
function confirmationall ()
{
	if (confirm("' . get_lang('DeleteAll') . '?"))
		{return true;}
	else
		{return false;}
}
</script>';
if (isset ($_GET['deleteall']))
{
	$eval[0]->delete_results();
	header('Location: gradebook_view_result.php?allresdeleted=&selecteval=' . $_GET['selecteval']);
	exit;
}
if ((!isset ($_GET['export'])) && (!isset ($_GET['import'])))
	Display :: display_header(get_lang('ViewResult'));
if (isset ($_GET['addresultnostudents']))
	Display :: display_warning_message(get_lang('AddResultNoStudents'),false);
if (isset ($_GET['editresmessage']))
	Display :: display_confirmation_message(get_lang('ResultEdited'),false);
if (isset ($_GET['addresult']))
	Display :: display_confirmation_message(get_lang('ResultAdded'),false);
if (isset ($_GET['adduser']))
	Display :: display_confirmation_message(get_lang('UserAdded'),false);
if (isset ($_GET['deleteresult']))
	Display :: display_confirmation_message(get_lang('ResultDeleted'),false);
if (isset ($_GET['editallresults']))
	Display :: display_confirmation_message(get_lang('AllResultsEdited'),false);
if (isset ($_GET['importok']))
	Display :: display_confirmation_message(get_lang('ImportOk'),false);
if (isset ($_GET['importnofile']))
	Display :: display_warning_message(get_lang('ImportNoFile'),false);
if (isset ($_GET['incorrectdata']))
	Display :: display_warning_message(get_lang('IncorrectData'),false);
if (isset ($_GET['nothingadded']))
	Display :: display_warning_message(get_lang('NothingAdded'),false);
if (isset ($_GET['massdelete']))
	Display :: display_confirmation_message(get_lang('ResultsDeleted'),false);
if (isset ($_GET['nouser']))
	Display :: display_warning_message(get_lang('NoUser'),false);
if (isset ($_GET['overwritemax']))
	Display :: display_warning_message(get_lang('OverWriteMax'),false);
if (isset ($_GET['importoverwritescore']))
	Display :: display_confirmation_message(get_lang('ImportOverwriteScore') . ' ' . $_GET['importoverwritescore']);
if (isset ($_GET['import_user_error']))
{
	$userinfo= get_user_info_from_id($_GET['import_user_error']);
	Display :: display_warning_message(get_lang('UserInfoDoesNotMatch') . '<br /><br /><b>' . $userinfo['lastname'] . ' ' . $userinfo['firstname'] . '</b>');
}
if (isset ($_GET['allresdeleted']))
	Display :: display_confirmation_message(get_lang('AllResultDeleted'));
if (isset ($_GET['import_score_error']))
{
	$userinfo= get_user_info_from_id($_GET['import_score_error']);
	Display :: display_warning_message(get_lang('ScoreDoesNotMatch') . '<br /><br /><b>' . $userinfo['lastname'] . ' ' . $userinfo['firstname'] . '</b>');
}
if ($file_type == null)
{ //show the result header
	if (isset ($export_result_form) && !(isset ($edit_res_form)))
	{
		echo '<div class ="normal-message">';
		echo $export_result_form->display();
		echo '</div>';
		DisplayGradebook :: display_header_result($eval[0], $currentcat[0]->get_id(), 1);
	} else
	{
		if (isset ($import_result_form))
		{
			echo '<div class ="normal-message">';
			echo $import_result_form->display();
			echo '</div>';
		}
		if (isset ($edit_res_form))
		{
			Display :: display_normal_message($edit_res_form->toHtml(),false);
		}
		DisplayGradebook :: display_header_result($eval[0], $currentcat[0]->get_id(), 1);
	}
	$resulttable->display();
	Display :: display_footer();
}
?>
