<?php
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2008 Dokeos Latinoamerica SAC
	Copyright (c) 2006 Dokeos SPRL
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
class DisplayGradebook
{
	/**
	* Displays the header for the result page containing the navigation tree and links
	* @param $evalobj
	* @param $selectcat
	* @param $shownavbar 1=show navigation bar
	* @param $forpdf only output for pdf file
	*/
	function display_header_result($evalobj, $selectcat, $shownavbar) {
		if ($shownavbar == '1') {
			$header= '<table border="0" cellpadding="5"><tr><td>';
			$header .= '<a href="'.$_SESSION['gradebook_dest'].'?selectcat=' . $selectcat . '"><img src="../img/lp_leftarrow.gif" alt="' . get_lang('BackToOverview') . '" align="absmiddle"/> ' . get_lang('BackToOverview') . '</a></td>';
			if ($evalobj->get_course_code() == null) {
				$header .= '<td><a href="gradebook_add_user.php?selecteval=' . $evalobj->get_id() . '"><img src="../img/add_user_big.gif" alt="' . get_lang('AddStudent') . '" align="absmiddle" /> ' . get_lang('AddStudent') . '</a></td>';
			}
			elseif (!$evalobj->has_results()) {
				$header .= '<td><a href="gradebook_add_result.php?selectcat=' . $selectcat . '&selecteval=' . $evalobj->get_id() . '"><img src="../img/filenew.gif" alt="' . get_lang('AddResult') . '" align="absmiddle"/> ' . get_lang('AddResult') . '</a></td>';
			}
			$header .= '<td><a href="' . api_get_self() . '?&selecteval=' . $evalobj->get_id() . '&import="><img src="../img/calendar_down.gif" border="0" alt="" />' . ' ' . get_lang('ImportResult') . '</a></td>';
			if ($evalobj->has_results()) {
				$header .= '<td><a href="' . api_get_self() . '?&selecteval=' . $evalobj->get_id() . '&export="><img src="../img/calendar_up.gif" border="0" alt="" />' . ' ' . get_lang('ExportResult') . '</a></td>';
				$header .= '<td><a href="gradebook_edit_result.php?selecteval=' . $evalobj->get_id() .'"><img src="../img/works.gif" alt="' . get_lang('EditResult') . '" align="absmiddle" /> ' . get_lang('EditResult') . '</a></td>';
				$header .= '<td><a href="' . api_get_self() . '?&selecteval=' . $evalobj->get_id() . '&deleteall=" onclick="return confirmationall();"><img src="../img/delete.gif" border="0" alt="" />' . ' ' . get_lang('DeleteResult') . '</a></td>';
			}
			$header .= '<td><a href="' . api_get_self() . '?print=&selecteval=' . $evalobj->get_id() . '" target="_blank"><img src="../img/printmgr.gif" alt="' . get_lang('Print') . '" /> ' . get_lang('Print') . '</a>';
			$header .= '</td></tr></table>';
		}
		if ($evalobj->is_visible() == '1') {
			$visible= get_lang('Yes');
		} else {
			$visible= get_lang('No');
		}

		$scoredisplay = ScoreDisplay :: instance();
		if (($evalobj->has_results())){ // TODO this check needed ?

			$score= $evalobj->calc_score();
			if ($score != null)
				$average= get_lang('Average') . ' :<b> ' .$scoredisplay->display_score($score,SCORE_AVERAGE) . '</b>';
		}
		if (!$evalobj->get_description() == '') {
			$description= get_lang('Description') . ' :<b> ' . $evalobj->get_description() . '</b><br>';
		}
		if ($evalobj->get_course_code() == null) {
			$course= get_lang('CourseIndependent');
		} else {
			$course= get_course_name_from_code($evalobj->get_course_code());
		}

		$evalinfo= '<table width="100%" border="0"><tr><td>';
		$evalinfo .= get_lang('EvaluationName') . ' :<b> ' . $evalobj->get_name() . ' </b>(' . date('j/n/Y g:i', $evalobj->get_date()) . ')<br>' . get_lang('Course') . ' :<b> ' . $course . '</b><br>' . get_lang('Weight') . ' :<b> ' . $evalobj->get_weight() . '</b><br>' . get_lang('Max') . ' :<b> ' . $evalobj->get_max() . '</b><br>' . $description . get_lang('Visible') . ' :<b> ' . $visible . '</b><br>' . $average;
		if (!$evalobj->has_results())
			$evalinfo .= '<br /><i>' . get_lang('NoResultsInEvaluation') . '</i>';
		elseif ($scoredisplay->is_custom() && api_get_self() != '/dokeos/main/gradebook/gradebook_statistics.php')
			$evalinfo .= '<br /><br /><a href="gradebook_statistics.php?selecteval='.Security::remove_XSS($_GET['selecteval']).'"> '. get_lang('ViewStatistics') . '</a>';
		$evalinfo .= '</td><td><img style="left:0;position:relative" src="../img/tutorial.gif"></img></td></table>';
		Display :: display_normal_message($evalinfo,false);
		echo $header;

	}
	/**
	* Displays the header for the flatview page containing filters
	* @param $catobj
	* @param $showeval
	* @param $showlink
	*/
	function display_header_flatview($catobj, $showeval, $showlink,$simple_search_form) {
		$header= '<table border="0" cellpadding="5">';
		$header .= '<td style="vertical-align: top;"><a href="'.$_SESSION['gradebook_dest'].'?selectcat=' . Security::remove_XSS($_GET['selectcat']) . '"><< ' . get_lang('BackToOverview') . '</a></td>';
		$header .= '<td style="vertical-align: top;">' . get_lang('FilterCategory') . '</td><td style="vertical-align: top;"><form name="selector"><select name="selectcat" onchange="document.selector.submit()">';
		$cats= Category :: load();
		$tree= $cats[0]->get_tree();
		unset ($cats);
		foreach ($tree as $cat) {
			for ($i= 0; $i < $cat[2]; $i++) {
				$line .= '&mdash;';
			}
			if ($_GET['selectcat'] == $cat[0]) {
				$header .= '<option selected="selected" value=' . $cat[0] . '>' . $line . ' ' . $cat[1] . '</option>';
			} else {
				$header .= '<option value=' . $cat[0] . '>' . $line . ' ' . $cat[1] . '</option>';
			}
			$line= '';
		}
		$header .= '</td></select></form>';
		if (!$catobj->get_id() == '0') {
			$header .= '<td style="vertical-align: top;"><a href="' . api_get_self() . '?selectcat=' . $catobj->get_parent_id() . '"><img src="../img/folder_up.gif" border="0" alt="'.get_lang('Up').'" /></a></td>';
		}
		$header .= '<td style="vertical-align: top;">'.$simple_search_form->toHtml().'</td>';
		$header .= '<td style="vertical-align: top;"><a href="' . api_get_self() . '?exportpdf=&offset='.Security::remove_XSS($_GET['offset']).'&search=' . Security::remove_XSS($_GET['search']).'&selectcat=' . $catobj->get_id() . '"><img src=../img/calendar_up.gif alt=' . get_lang('ExportPDF') . '/> ' . get_lang('ExportPDF') . '</a>';
		$header .= '<td style="vertical-align: top;"><a href="' . api_get_self() . '?print=&selectcat=' . $catobj->get_id() . '" target="_blank"><img src="../img/printmgr.gif" alt=' . get_lang('Print') . '/> ' . get_lang('Print') . '</a>';
		$header .= '</td></tr></table>';
		if (!$catobj->get_id() == '0') {
			$header .= '<table border="0" cellpadding="5"><tr><td><form name="itemfilter" method="post" action="' . api_get_self() . '?selectcat=' . $catobj->get_id() . '"><input type="checkbox" name="showeval" onclick="document.itemfilter.submit()" ' . (($showeval == '1') ? 'checked' : '') . '>Show Evaluations &nbsp;';
			$header .= '<input type="checkbox" name="showlink" onclick="document.itemfilter.submit()" ' . (($showlink == '1') ? 'checked' : '') . '>'.get_lang('ShowLinks').'</form></td></tr></table>';
		}
		if (isset ($_GET['search'])) {
			$header .= '<b>'.get_lang('SearchResults').' :</b>';
		}
		echo $header;
	}

		/**
	* Displays the header for the flatview page containing filters
	* @param $catobj
	* @param $showeval
	* @param $showlink
	*/
	function display_header_reduce_flatview($catobj, $showeval, $showlink,$simple_search_form) {
		$header= '<table border="0" cellpadding="5">';
		$header .= '<td style="vertical-align: top;"><a href="index.php?'.api_get_cidreq().'"><< ' . get_lang('BackToOverview') . '</a></td>';


//		$header .= '<td style="vertical-align: top;"><a href="' . api_get_self() . '?exportpdf=&offset='.Security::remove_XSS($_GET['offset']).'&search=' . Security::remove_XSS($_GET['search']).'&selectcat=' . $catobj->get_id() . '"><img src=../img/calendar_up.gif alt=' . get_lang('ExportPDF') . '/> ' . get_lang('ExportPDF') . '</a>';

		// this MUST be a GET variable not a POST
		if (isset($_GET['show'])) {
			$show=Security::remove_XSS($_GET['show']);
		} else {
			$show='';
		}
		echo '<form id="form1a" name="form1a" method="post" action="'.api_get_self().'?show='.$show.'">';
		echo '<input type="hidden" name="export_report" value="export_report">';
		echo '<input type="hidden" name="selectcat" value="'.$catobj->get_id() .'">';

		echo '<input type="hidden" name="export_format" value="csv">';
		echo '</form>';

		echo '<form id="form1b" name="form1b" method="post" action="'.api_get_self().'?show='.$show.'">';
		echo '<input type="hidden" name="export_report" value="export_report">';
		echo '<input type="hidden" name="selectcat" value="'.$catobj->get_id() .'">';
		echo '<input type="hidden" name="export_format" value="xls">';
		echo '</form>';

		$header .= '<td style="vertical-align: top;"><a class="quiz_export_link" href="#" onclick="document.form1a.submit();"><img align="absbottom" src="'.api_get_path(WEB_IMG_PATH).'excel.gif" alt="'.get_lang('ExportAsCSV').'">&nbsp;'.get_lang('ExportAsCSV').'</a></td>';
		$header .= '<td style="vertical-align: top;"><a class="quiz_export_link" href="#" onclick="document.form1b.submit();"><img align="absbottom" src="'.api_get_path(WEB_IMG_PATH).'excel.gif" alt="'.get_lang('ExportAsXLS').'">&nbsp;'.get_lang('ExportAsXLS').'</a></td>';

		$header .= '<td style="vertical-align: top;"><a href="' . api_get_self() . '?print=&selectcat=' . $catobj->get_id() . '" target="_blank"><img src="../img/printmgr.gif" alt=' . get_lang('Print') . '/> ' . get_lang('Print') . '</a>';
		$header .= '</td></tr></table>';

		if (!$catobj->get_id() == '0') {
			//this is necessary?
			//$header .= '<table border="0" cellpadding="5"><tr><td><form name="itemfilter" method="post" action="' . api_get_self() . '?selectcat=' . $catobj->get_id() . '"><input type="checkbox" name="showeval" onclick="document.itemfilter.submit()" ' . (($showeval == '1') ? 'checked' : '') . '>Show Evaluations &nbsp;';
			//$header .= '<input type="checkbox" name="showlink" onclick="document.itemfilter.submit()" ' . (($showlink == '1') ? 'checked' : '') . '>'.get_lang('ShowLinks').'</form></td></tr></table>';
		}
		if (isset ($_GET['search'])) {
			$header .= '<b>'.get_lang('SearchResults').' :</b>';
		}
		echo $header;
	}

	/**
	 * Displays the header for the gradebook containing the navigation tree and links
	 * @param category_object $currentcat
	 * @param int $showtree '1' will show the browse tree and naviation buttons
	 * @param boolean $is_course_admin
	 * @param boolean $is_platform_admin
     * @param boolean Whether to show or not the link to add a new qualification (we hide it in case of the course-embedded tool where we have only one calification per course or session)
     * @param boolean Whether to show or not the link to add a new item inside the qualification (we hide it in case of the course-embedded tool where we have only one calification per course or session)
     * @return void Everything is printed on screen upon closing
	 */
	function display_header_gradebook($catobj, $showtree, $selectcat, $is_course_admin, $is_platform_admin, $simple_search_form, $show_add_qualification = true, $show_add_link = true) {
		//student
		$objcat=new Category();
		$objdat=new Database();

		$course_id=$objdat->get_course_by_category($selectcat);
		$message_resource=$objcat->show_message_resource_delete($course_id);
		if (!$is_course_admin) {
			$user_id = api_get_user_id();
			$user= get_user_info_from_id($user_id);

			$catcourse= Category :: load($catobj->get_id());
			$scoredisplay = ScoreDisplay :: instance();
			$scorecourse = $catcourse[0]->calc_score($user_id);

			// generating the total score for a course
			$allevals= $catcourse[0]->get_evaluations($user_id,true);
			$alllinks= $catcourse[0]->get_links($user_id,true);
			$evals_links = array_merge($allevals, $alllinks);
			$item_value=0;
			$item_total=0;
			for ($count=0; $count < count($evals_links); $count++) {
				$item = $evals_links[$count];
				$score = $item->calc_score($user_id);
				$my_score_denom=($score[1]==0) ? 1 : $score[1];
				$item_value+=$score[0]/$my_score_denom*$item->get_weight();
				$item_total+=$item->get_weight();
				//$row[] = $scoredisplay->display_score($score,SCORE_DIV_PERCENT);
			}
			$item_value = number_format($item_value, 2, '.', ' ');
			$total_score=array($item_value,$item_total);
			$scorecourse_display = $scoredisplay->display_score($total_score,SCORE_DIV_PERCENT);
			//----------------------


			//$scorecourse_display = (isset($scorecourse) ? $scoredisplay->display_score($scorecourse,SCORE_AVERAGE) : get_lang('NoResultsAvailable'));
			$cattotal = Category :: load(0);
			$scoretotal= $cattotal[0]->calc_score(api_get_user_id());
			$scoretotal_display = (isset($scoretotal) ? $scoredisplay->display_score($scoretotal,SCORE_PERCENT) : get_lang('NoResultsAvailable'));
			$scoreinfo = get_lang('StatsStudent') . ' :<b> '.$user['lastname'].' '.$user['firstname'].'</b><br />';


			if ((!$catobj->get_id() == '0') && (!isset ($_GET['studentoverview'])) && (!isset ($_GET['search']))) {
				$scoreinfo.= '<br />'.get_lang('Total') . ' : <b>' . $scorecourse_display . '</b>';
			}
			//$scoreinfo.= '<br />'.get_lang('Total') . ' : <b>' . $scoretotal_display . '</b>';
			Display :: display_normal_message($scoreinfo,false);
		}
		// show navigation tree and buttons?
		$header='';
		$header .= '<table border=0 cellpadding=5>';
		if (($showtree == '1') || (isset ($_GET['studentoverview']))) {

			$header .= '<tr><td style="vertical-align: top;">' . get_lang('CurrentCategory') . '</td><td style="vertical-align: top;"><form name="selector"><select name="selectcat" onchange="document.selector.submit()">';
			$cats= Category :: load();

			$tree= $cats[0]->get_tree();
			unset ($cats);
			foreach ($tree as $cat) {
				for ($i= 0; $i < $cat[2]; $i++) {
					$line .= '&mdash;';
				}
				$line=isset($line) ? $line : '';
				if (isset($_GET['selectcat']) && $_GET['selectcat'] == $cat[0]) {
					$header .= '<option selected value=' . $cat[0] . '>' . $line . ' ' . $cat[1] . '</option>';
				} else {
					$header .= '<option value=' . $cat[0] . '>' . $line . ' ' . $cat[1] . '</option>';
				}
				$line= '';
			}
			$header .= '</select></form></td>';
			if (!$selectcat == '0') {
				$header .= '<td style="vertical-align: top;"><a href="' . api_get_self() . '?selectcat=' . $catobj->get_parent_id() . '"><img src="../img/folder_up.gif" border="0" alt="" /></a></td>';
			}
            if (!empty($simple_search_form) && $message_resource===false) {
			    $header .= '<td style="vertical-align: top;">'.$simple_search_form->toHtml().'</td>';
            } else {
            	$header .= '<td></td>';
            }
			if ($is_course_admin && $message_resource===false && $_GET['selectcat']!=0) {
				/*$header .= '<td style="vertical-align: top;"><a href="gradebook_flatview.php?'.api_get_cidreq().'&selectcat=' . $catobj->get_id() . '"><img src="../img/stats_access.gif" alt="' . get_lang('FlatView') . '" /> ' . get_lang('FlatView') . '</a>';
				if ($is_course_admin && $message_resource===false) {
					$header .= '<td style="vertical-align: top;"><a href="gradebook_scoring_system.php?'.api_get_cidreq().'&selectcat=' . $catobj->get_id() .'"><img src="../img/acces_tool.gif" alt="' . get_lang('ScoreEdit') . '" /> ' . get_lang('ScoreEdit') . '</a>';
				}*/
			} elseif (!(isset ($_GET['studentoverview']))) {
				if ( $message_resource===false ) {
					//$header .= '<td style="vertical-align: top;"><a href="'.api_get_self().'?'.api_get_cidreq().'&studentoverview=&selectcat=' . $catobj->get_id() . '"><img src="../img/stats_access.gif" alt="' . get_lang('FlatView') . '" /> ' . get_lang('FlatView') . '</a>';
				}
			} else {
				$header .= '<td style="vertical-align: top;"><a href="'.api_get_self().'?'.api_get_cidreq().'&studentoverview=&exportpdf=&selectcat=' . $catobj->get_id() . '" target="_blank"><img src="../img/calendar_up.gif" alt="' . get_lang('ExportPDF') . '" /> ' . get_lang('ExportPDF') . '</a>';
			}
			$header .= '</td></tr>';
		}
		$header.='</table>';

		// for course admin & platform admin add item buttons are added to the header
		$header .= '<table border="0" cellpadding="0"><tr><td>';
		if (($is_course_admin) && (!isset ($_GET['search']))) {
			if ($selectcat == '0') {
                if ($show_add_qualification === true) {
				   // $header .= '<a href="gradebook_add_cat.php?'.api_get_cidreq().'&selectcat=0"><img src="../img/folder_new.gif" alt="' . get_lang('NewCategory') . '" /> ' . get_lang('NewCategory') . '</a></td>';
                }
                if ($show_add_link === true) {
				    //$header .= '<td><a href="gradebook_add_eval.php?'.api_get_cidreq().'"><img src="../img/filenew.gif" alt="' . get_lang('NewEvaluation') . '" /> ' . get_lang('NewEvaluation') . '</a>';
                }
			} else {
                if ($show_add_qualification === true && $message_resource===false) {
    				//$header .= '<a href="gradebook_add_cat.php?'.api_get_cidreq().'&selectcat=' . $catobj->get_id() . '" ><img src="../img/folder_new.gif" alt="' . get_lang('NewSubCategory') . '" align="absmiddle" /> ' . get_lang('NewSubCategory') . '</a></td>';
                }
               $my_category=$catobj->shows_all_information_an_category($catobj->get_id());
				$my_api_cidreq = api_get_cidreq();
				if ($my_api_cidreq=='') {
					$my_api_cidreq='cidReq='.$my_category['course_code'];
				}
                if ($show_add_link === true && $message_resource==false) {
    				$header .= '<td><a href="gradebook_add_eval.php?'.$my_api_cidreq.'&selectcat=' . $catobj->get_id() . '" ><img src="../img/filenew.gif" alt="' . get_lang('NewEvaluation') . '" align="absmiddle" /> ' . get_lang('NewEvaluation') . '</a>&nbsp;';
                    $cats= Category :: load($selectcat);
                    if ($cats[0]->get_course_code() != null && $message_resource===false) {
                        //$header .= '<td><a href="gradebook_add_link.php?'.api_get_cidreq().'&selectcat=' . $catobj->get_id() . '"><img src="../img/link.gif" alt="' . get_lang('MakeLink') . '" align="absmiddle" /> ' . get_lang('MakeLink') . '</a>';
                        $header .= '<td><a href="gradebook_add_link.php?'.$my_api_cidreq.'&selectcat=' . $catobj->get_id() . '"><img src="../img/link.gif" alt="' . get_lang('MakeLink') . '" align="absmiddle" /> ' . get_lang('MakeLink') . '</a>&nbsp;';

                    } else {
                        $header .= '<td><a href="gradebook_add_link_select_course.php?'.$my_api_cidreq.'&selectcat=' . $catobj->get_id() . '"><img src="../img/link.gif" alt="' . get_lang('MakeLink') . '" align="absmiddle" /> ' . get_lang('MakeLink') . '</a>&nbsp;';
                    }
                }
                if ($message_resource===false ) {
                	$myname=$catobj->shows_all_information_an_category($catobj->get_id());
                 	$header .= '<td><a href="gradebook_edit_all.php?'.$my_api_cidreq.'&selectcat=' . $catobj->get_id() . '"><img src="../img/quiz.gif" alt="' . get_lang('EditAllWeights') . '" align="absmiddle"/> ' . get_lang('EditAllWeights') . '</a>';
                	$my_course_id=api_get_course_id();
                	if (!isset($my_course_id)) {
	                	$header .= '<td style="vertical-align: top;"><a href="gradebook_flatview.php?'.$my_api_cidreq.'&selectcat=' . $catobj->get_id() . '"><img src="../img/stats_access.gif" alt="' . get_lang('FlatView') . '" align="absmiddle"/> ' . get_lang('FlatView') . '</a>';
						if ($is_course_admin && $message_resource===false) {
							$header .= '<td style="vertical-align: top;"><a href="gradebook_scoring_system.php?'.$my_api_cidreq.'&selectcat=' . $catobj->get_id() .'"><img src="../img/acces_tool.gif" alt="' . get_lang('ScoreEdit') . '" align="absmiddle"/> ' . get_lang('ScoreEdit') . '</a>';
						}
					}
                }
			}
		} elseif (isset ($_GET['search'])) {
			$header .= '<b>'.get_lang('SearchResults').' :</b>';
		}
		$header .= '</td></tr></table>';
		echo $header;
	}

	function display_reduce_header_gradebook($catobj,$is_course_admin, $is_platform_admin, $simple_search_form, $show_add_qualification = true, $show_add_link = true) {
		//student
		if (!$is_course_admin) {
			/*$user= get_user_info_from_id(api_get_user_id());
			$catcourse= Category :: load($catobj->get_id());
			$scoredisplay = ScoreDisplay :: instance();
			$scorecourse = $catcourse[0]->calc_score(api_get_user_id());
			$scorecourse_display = (isset($scorecourse) ? $scoredisplay->display_score($scorecourse,SCORE_AVERAGE) : get_lang('NoResultsAvailable'));
			$cattotal = Category :: load(0);
			$scoretotal= $cattotal[0]->calc_score(api_get_user_id());
			$scoretotal_display = (isset($scoretotal) ? $scoredisplay->display_score($scoretotal,SCORE_PERCENT) : get_lang('NoResultsAvailable'));
			$scoreinfo = get_lang('StatsStudent') . ' :<b> '.$user['lastname'].' '.$user['firstname'].'</b><br />';
			if ((!$catobj->get_id() == '0') && (!isset ($_GET['studentoverview'])) && (!isset ($_GET['search'])))
				$scoreinfo.= '<br />'.get_lang('TotalForThisCategory') . ' : <b>' . $scorecourse_display . '</b>';
			$scoreinfo.= '<br />'.get_lang('Total') . ' : <b>' . $scoretotal_display . '</b>';
			Display :: display_normal_message($scoreinfo,false);
			*/
		}
			// show navigation tree and buttons?
			$header = '<table border=0 cellpadding=5 >';

			if ($is_course_admin) {
				$header .= '<td style="vertical-align: top;"><a href="gradebook_flatview.php?'.api_get_cidreq().'&selectcat=' . $catobj->get_id() . '"><img src="../img/stats_access.gif" alt="' . get_lang('FlatView') . '" /> ' . get_lang('FlatView') . '</a>';
				if ($is_platform_admin || $is_course_admin)
					$header .= '<td style="vertical-align: top;"><a href="gradebook_scoring_system.php?'.api_get_cidreq().'&selectcat=' . $catobj->get_id() .'"><img src="../img/acces_tool.gif" alt="' . get_lang('ScoreEdit') . '" /> ' . get_lang('ScoreEdit') . '</a>';
			} elseif (!(isset ($_GET['studentoverview']))) {
				$header .= '<td style="vertical-align: top;"><a href="'.api_get_self().'?'.api_get_cidreq().'&studentoverview=&selectcat=' . $catobj->get_id() . '"><img src="../img/stats_access.gif" alt="' . get_lang('FlatView') . '" /> ' . get_lang('FlatView') . '</a>';
			} else {
				$header .= '<td style="vertical-align: top;"><a href="'.api_get_self().'?'.api_get_cidreq().'&studentoverview=&exportpdf=&selectcat=' . $catobj->get_id() . '" target="_blank"><img src="../img/calendar_up.gif" alt="' . get_lang('ExportPDF') . '" /> ' . get_lang('ExportPDF') . '</a>';
			}
			$header .= '</td></tr>';
		$header.='</table>';
		echo $header;
	}


	function display_header_user($userid) {
 		$user= get_user_info_from_id($userid);
        //User picture size is calculated from SYSTEM path
        $image_syspath = UserManager::get_user_picture_path_by_id($userid,'system',false,true);
        $image_size = getimagesize($image_syspath['dir'].$image_syspath['file']);
        //Web path
        $image_path = UserManager::get_user_picture_path_by_id($userid,'web',false,true);
        $image_file = $image_path['dir'].$image_path['file'];
 		$img_attributes= 'src="' . $image_file . '?rand=' . time() . '" ' . 'alt="' . $user['lastname'] . ' ' . $user['firstname'] . '" ';
		if ($image_size[0] > 200) {
		 //limit display width to 200px
 			$img_attributes .= 'width="200" ';
		}
		$cattotal= Category :: load(0);
		$info = '<table width="100%" border=0 cellpadding=5><tr><td width="80%">';
		$info.= get_lang('Name') . ' : <b>' . $user['lastname'] . ' ' . $user['firstname'] . '</b> ( <a href="user_info.php?userid=' . $userid . '&selecteval=' . Security::remove_XSS($_GET['selecteval']) . '">' . get_lang('MoreInfo') . '...</a> )<br>';
		$info.= get_lang('Email') . ' : <b><a href="mailto:' . $user['email'] . '">' . $user['email'] . '</a></b><br><br>';
		$scoredisplay = ScoreDisplay :: instance();
		$score_stud= $cattotal[0]->calc_score($userid);
		$score_stud_display = (isset($score_stud) ? $scoredisplay->display_score($score_stud,SCORE_PERCENT) : get_lang('NoResultsAvailable') );
		$score_avg= $cattotal[0]->calc_score();
		$score_avg_display = (isset($score_avg) ? $scoredisplay->display_score($score_avg,SCORE_AVERAGE) : get_lang('NoResultsAvailable') );
		$info.= get_lang('TotalUser') . ' : <b>' . $score_stud_display . '</b><br>';
		$info.= get_lang('AverageTotal') . ' : <b>' . $score_avg_display . '</b>';
		$info.= '</td><td>';
		$info.= '<img ' . $img_attributes . '/></td></tr></table>';
		echo Display :: display_normal_message($info,false);
	}
}