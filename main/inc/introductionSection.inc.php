<?php
/* For licensing terms, see /license.txt */
/**
 * The INTRODUCTION MICRO MODULE is used to insert and edit
 * an introduction section on a Chamilo Module. It can be inserted on any
 * Chamilo Module, provided a connection to a course Database is already active.
 *
 * The introduction content are stored on a table called "introduction"
 * in the course Database. Each module introduction has an Id stored on
 * the table. It is this id that can make correspondance to a specific module.
 *
 * 'introduction' table description
 *   id : int
 *   intro_text :text
 *
 *
 * usage :
 *
 * $moduleId = XX // specifying the module Id
 * include(moduleIntro.inc.php);
 *
 *	@package chamilo.include
 */

/*	Constants and variables */

$TBL_INTRODUCTION = Database::get_course_table(TABLE_TOOL_INTRO);
$intro_editAllowed = $is_allowed_to_edit;
$session_id = api_get_session_id();

$introduction_section = '';

global $charset;
$intro_cmdEdit = empty($_GET['intro_cmdEdit']) ? '' : $_GET['intro_cmdEdit'];
$intro_cmdUpdate = isset($_POST['intro_cmdUpdate']);
$intro_cmdDel = empty($_GET['intro_cmdDel']) ? '' : $_GET['intro_cmdDel'];
$intro_cmdAdd = empty($_GET['intro_cmdAdd']) ? '' : $_GET['intro_cmdAdd'];
$courseId = api_get_course_id();

if (!empty($courseId)) {
    $form = new FormValidator('introduction_text', 'post', api_get_self().'?'.api_get_cidreq());
} else {
    $form = new FormValidator('introduction_text');
}

$renderer =& $form->defaultRenderer();
$renderer->setElementTemplate('<div style="width: 80%; margin: 0px auto; padding-bottom: 10px; ">{element}</div>');

$toolbar_set = 'Introduction';
$width = '100%';
$height = '300';

// The global variable $fck_attribute has been deprecated. It stays here for supporting old external code.
global $fck_attribute;
if (is_array($fck_attribute)) {
    if (isset($fck_attribute['ToolbarSet'])) {
        $toolbar_set = $fck_attribute['ToolbarSet'];
    }
    if (isset($fck_attribute['Width'])) {
        $toolbar_set = $fck_attribute['Width'];
    }
    if (isset($fck_attribute['Height'])) {
        $toolbar_set = $fck_attribute['Height'];
    }
}

if (is_array($editor_config)) {
    if (!isset($editor_config['ToolbarSet'])) {
        $editor_config['ToolbarSet'] = $toolbar_set;
    }
    if (!isset($editor_config['Width'])) {
        $editor_config['Width'] = $width;
    }
    if (!isset($editor_config['Height'])) {
        $editor_config['Height'] = $height;
    }
} else {
    $editor_config = array('ToolbarSet' => $toolbar_set, 'Width' => $width, 'Height' => $height);
}

$form->add_html_editor('intro_content', null, null, false, $editor_config);
$form->addElement('style_submit_button', 'intro_cmdUpdate', get_lang('SaveIntroText'), 'class="save"');

/*	INTRODUCTION MICRO MODULE - COMMANDS SECTION (IF ALLOWED) */
$course_id = api_get_course_int_id();

if ($intro_editAllowed) {
    $moduleId = Database::escape_string($moduleId);

    /* Replace command */
    if ($intro_cmdUpdate) {
        if ($form->validate()) {
            $form_values = $form->exportValues();
            $intro_content = Security::remove_XSS(stripslashes(api_html_entity_decode($form_values['intro_content'])), COURSEMANAGERLOWSECURITY);
            if (!empty($intro_content)) {
                $sql = "REPLACE $TBL_INTRODUCTION SET c_id = $course_id, id='$moduleId',intro_text='".Database::escape_string($intro_content)."', session_id='".intval($session_id)."'";
                Database::query($sql);
                $introduction_section .= Display::return_message(get_lang('IntroductionTextUpdated'),'confirmation', false);
            } else {
                $intro_cmdDel = true;	// got to the delete command
            }
        } else {
            $intro_cmdEdit = true;
        }
    }

    /* Delete Command */
    if ($intro_cmdDel) {
        Database::query("DELETE FROM $TBL_INTRODUCTION WHERE c_id = $course_id AND id='".$moduleId."' AND session_id='".intval($session_id)."'");
        $introduction_section .= Display::return_message(get_lang('IntroductionTextDeleted'), 'confirmation');
    }
}


/*	INTRODUCTION MICRO MODULE - DISPLAY SECTION */

/* Retrieves the module introduction text, if exist */
/* @todo use a lib to query the $TBL_INTRODUCTION table */
// Getting course intro
$intro_content = null;
$sql = "SELECT intro_text FROM $TBL_INTRODUCTION
        WHERE c_id = $course_id AND id='".Database::escape_string($moduleId)."' AND session_id = 0";
$intro_dbQuery = Database::query($sql);
if (Database::num_rows($intro_dbQuery) > 0) {
    $intro_dbResult = Database::fetch_array($intro_dbQuery);
    $intro_content = $intro_dbResult['intro_text'];
}

// Getting session intro
if (!empty($session_id)) {
    $sql = "SELECT intro_text FROM $TBL_INTRODUCTION
        WHERE c_id = $course_id AND id='".Database::escape_string($moduleId)."' AND session_id = '".intval($session_id)."'";
    $intro_dbQuery = Database::query($sql);
    $introSessionContent = null;
    if (Database::num_rows($intro_dbQuery) > 0) {
        $intro_dbResult = Database::fetch_array($intro_dbQuery);
        $introSessionContent = $intro_dbResult['intro_text'];
    }
    // If the course session intro exists replace it.
    if (!empty($introSessionContent)) {
        $intro_content = $introSessionContent;
    }
}

/* Determines the correct display */

if ($intro_cmdEdit || $intro_cmdAdd) {
	$intro_dispDefault = false;
	$intro_dispForm = true;
	$intro_dispCommand = false;
} else {
	$intro_dispDefault = true;
	$intro_dispForm = false;

	if ($intro_editAllowed) {
		$intro_dispCommand = true;
	} else {
		$intro_dispCommand = false;
	}
}

/* Executes the display */

// display thematic advance inside a postit
if ($intro_dispForm) {
	$default['intro_content'] = $intro_content;
	$form->setDefaults($default);
	$introduction_section .= '<div id="courseintro" style="width: 98%">';
	$introduction_section .= $form->return_form();
	$introduction_section .= '</div>';
}

$thematic_description_html = '';

if ($tool == TOOL_COURSE_HOMEPAGE && !isset($_GET['intro_cmdEdit'])) {

	$thematic = new Thematic();
	if (api_get_course_setting('display_info_advance_inside_homecourse') == '1') {
		$information_title = get_lang('InfoAboutLastDoneAdvance');
		$last_done_advance =  $thematic->get_last_done_thematic_advance();
		$thematic_advance_info = $thematic->get_thematic_advance_list($last_done_advance);
	} else if(api_get_course_setting('display_info_advance_inside_homecourse') == '2') {
		$information_title = get_lang('InfoAboutNextAdvanceNotDone');
		$next_advance_not_done = $thematic->get_next_thematic_advance_not_done();
		$thematic_advance_info = $thematic->get_thematic_advance_list($next_advance_not_done);
	} else if(api_get_course_setting('display_info_advance_inside_homecourse') == '3') {
		$information_title = get_lang('InfoAboutLastDoneAdvanceAndNextAdvanceNotDone');
		$last_done_advance =  $thematic->get_last_done_thematic_advance();
		$next_advance_not_done = $thematic->get_next_thematic_advance_not_done();
		$thematic_advance_info = $thematic->get_thematic_advance_list($last_done_advance);
		$thematic_advance_info2 = $thematic->get_thematic_advance_list($next_advance_not_done);
	}

	if (!empty($thematic_advance_info)) {

		/*$thematic_advance = get_lang('CourseThematicAdvance').'&nbsp;'.$thematic->get_total_average_of_thematic_advances().'%';*/
		$thematic_advance = get_lang('CourseThematicAdvance');
		$thematicScore = $thematic->get_total_average_of_thematic_advances().'%';
		$thematicUrl = api_get_path(WEB_CODE_PATH).'course_progress/index.php?action=thematic_details&'.api_get_cidreq();
		if (api_is_allowed_to_edit(null, true)) {
			//$thematic_advance = '<a href="'.api_get_path(WEB_CODE_PATH).'course_progress/index.php?action=thematic_details&'.api_get_cidreq().'">'.get_lang('CourseThematicAdvance').'&nbsp;'.$thematic->get_total_average_of_thematic_advances().'%</a>';
		}
		$thematic_info = $thematic->get_thematic_list($thematic_advance_info['thematic_id']);

		$thematic_advance_info['start_date'] = api_get_local_time($thematic_advance_info['start_date']);
		$thematic_advance_info['start_date'] = api_format_date($thematic_advance_info['start_date'], DATE_TIME_FORMAT_LONG);
		$userInfo = $_SESSION['_user'];
		$courseInfo = api_get_course_info();
		//die('<pre>'.print_r($courseInfo,1).'</pre>');
		
		$thematic_description_html = '<div class="thematic-postit">
									  <div class="row-fluid"><div class="span12">
			    	                  <div class="accordion" id="progress-bar-course">
						              		<div class="accordion-group">
						              		<div class="accordion-heading">
									  		<div class="title-accordion">
									  <div class="row-fluid score-thematic">
									  <div class="span8">';
		$thematic_description_html.='<div class="span6 name-student"><h2>'.$userInfo['firstName'].'</h2><h3>'.$userInfo['lastName'].'</h3></div>
									<div class="span2 score"><h1>'.$thematicScore.'</h1></div>
									 <div class="span4"><h3>'.$thematic_advance.'</h3><p>'.$courseInfo['name'].'</p></div></div>';
		$thematic_description_html.='<div class="span4">
									 <a id="thematic-show" class="btn btn-small accordion-toggle btn-hidden-thematic" href="#pross" data-toggle="collapse" data-parent="#progress-bar-course">
								     '.get_lang('ClickToHideDetails').'
								     </a>
								     <a id="thematic-hidden" class="btn btn-small btn-primary accordion-toggle btn-show-thematic" href="#pross" data-toggle="collapse" data-parent="#progress-bar-course" style="display:none;">
									 '.get_lang('ClickToShowDetails').'
								     </a>
								     </div>
								     </div>
									</div>	 
									</div>';
		$thematic_description_html.='<div class="accordion-body collapse" id="pross" style="height:auto;">
									<div class="accordion-inner">
									<div class="row-fluid">
									<div class="span4">
										<div class="row-fluid">
										<div class="span4">
											<div class="thumbnail">
												<img src="'.$userInfo['avatar'].'" class="img-polaroid">
											</div>
										</div>
									<div class="span8">
											<div class="info-progress">
												<div class="tittle-score">'.$thematic_advance.'&nbsp;'.$thematicScore.'</div>
													<div class="progress progress-striped">
	    												<div class="bar" style="width: '.$thematicScore.';"></div>
	   												</div>
	   												<a href="'.$thematicUrl.'" class="btn btn-info">'.get_lang('ShowFullCourseAdvance').'</a>
   												</div>
											</div>
										</div>
									</div>';
		$thematic_description_html.='<div class="span8">
										<div class="topics">'.get_lang('NextTopics').'</div>
										<div class="row-fluid">';
		$thematic_description_html.='<div class="span6 items-progress">
										<p class="date">'.$thematic_advance_info['start_date'].'</p>
										<h3 class="title">'.$thematic_advance_info['content'].'</h3>
										<p class="time">'.get_lang('DurationInHours').' : '.$thematic_advance_info['duration'].' <a href="'.$thematicUrl.'">'.get_lang('ShowDetails').'</a></p>
									</div>';




		/*$thematic_description_html = '<div class="thematic-postit">
								  	  <div class="thematic-postit-top"><h3><a class="thematic-postit-head" style="" href="#"> '.$thematic_advance.'</h3></a></div>
								  	  <div class="thematic-postit-center" style="display:none">';
		$thematic_description_html .= '<div><strong>'.$thematic_info['title'].'</strong></div>';
		$thematic_description_html .= '<div style="font-size:8pt;"><strong>'.$thematic_advance_info['start_date'].'</strong></div>';
		$thematic_description_html .= '<div>'.$thematic_advance_info['content'].'</div>';
		$thematic_description_html .= '<div>'.get_lang('DurationInHours').' : '.$thematic_advance_info['duration'].'</div>';
*/

		if (!empty($thematic_advance_info2)){
			$thematic_info2 = $thematic->get_thematic_list($thematic_advance_info2['thematic_id']);

			$thematic_advance_info2['start_date'] = api_get_local_time($thematic_advance_info2['start_date']);
			$thematic_advance_info2['start_date'] = api_format_date($thematic_advance_info2['start_date'], DATE_TIME_FORMAT_LONG);

			$thematic_description_html.='<div class="span6 items-progress">
										<p class="date">'.$thematic_advance_info2['start_date'].'</p>
										<h3 class="title">'.$thematic_advance_info2['content'].'</h3>
										<p class="time">'.get_lang('DurationInHours').' : '.$thematic_advance_info2['duration'].' <a href="'.$thematicUrl.'">'.get_lang('ShowDetails').'</a></p>
									</div>';


			/*$thematic_description_html .= '<div><strong>'.$thematic_info2['title'].'</strong></div>';
			$thematic_description_html .= '<div style="font-size:8pt;"><strong>'.$thematic_advance_info2['start_date'].'</strong></div>';
			$thematic_description_html .= '<div>'.$thematic_advance_info2['content'].'</div>';
			$thematic_description_html .= '<div>'.get_lang('DurationInHours').' : '.$thematic_advance_info2['duration'].'</div>';
			$thematic_description_html .= '<br />';*/
		}
		$thematic_description_html.='</div></div></div></div></div></div></div></div></div></div>';
		/*$thematic_description_html .= '</div>
								  <div class="thematic-postit-bottom"></div>
								  </div>';*/
	}
}

$introduction_section .= '<div class="row course-tools-intro"><div class="span12">';
$introduction_section .=  $thematic_description_html;
$introduction_section .=  '</div>';

$introduction_section .=  '<div class="home-course-intro span12"><div class="page-course">';
if ($intro_dispDefault) {

	$intro_content = $intro_content;

	if (!empty($intro_content))	{
		$introduction_section.='<div class="page-course-intro">';
		$introduction_section .=  $intro_content;
		$introduction_section.='</div>';
	}
}
$introduction_section .=  '</div></div>';

if ($intro_dispCommand) {
	if (empty($intro_content)) {
		// Displays "Add intro" commands
		$introduction_section .=  '<div id="courseintro_empty">';
		if (!empty ($GLOBALS['_cid'])) {
			$introduction_section .=  "<a href=\"".api_get_self()."?".api_get_cidreq()."&amp;intro_cmdAdd=1\">";
            $introduction_section .=  Display::return_icon('introduction_add.gif', get_lang('AddIntro')).' ';
			$introduction_section .=  "</a>";
		} else {
			$introduction_section .= "<a href=\"".api_get_self()."?intro_cmdAdd=1\">\n".get_lang('AddIntro')."</a>";
		}
		$introduction_section .= "</div>";

	} else {
		// Displays "edit intro && delete intro" commands
		$introduction_section .=  '<div id="courseintro_empty">';
		if (!empty ($GLOBALS['_cid'])) {
			$introduction_section .=  "<a href=\"".api_get_self()."?".api_get_cidreq()."&amp;intro_cmdEdit=1\">".Display::return_icon('edit.png',get_lang('Modify'),'',ICON_SIZE_SMALL)."</a>";
			$introduction_section .=  "<a href=\"".api_get_self()."?".api_get_cidreq()."&amp;intro_cmdDel=1\" onclick=\"javascript:if(!confirm('".addslashes(api_htmlentities(get_lang('ConfirmYourChoice'),ENT_QUOTES,$charset))."')) return false;\">".Display::return_icon('delete.png',get_lang('Delete'),'',ICON_SIZE_SMALL)."</a>";
		} else {
			$introduction_section .=  "<a href=\"".api_get_self()."?intro_cmdEdit=1\">".Display::return_icon('edit.png',get_lang('Modify'),'',ICON_SIZE_SMALL)."</a>";
			$introduction_section .=  "<a href=\"".api_get_self()."?intro_cmdDel=1\" onclick=\"javascript:if(!confirm('".addslashes(api_htmlentities(get_lang('ConfirmYourChoice'),ENT_QUOTES,$charset))."')) return false;\">".Display::return_icon('delete.png',get_lang('Delete'),'',ICON_SIZE_SMALL)."</a>";
		}
		$introduction_section .=  "</div>";
        // Fix for chrome XSS filter for videos in iframes - BT#7930
        $browser = api_get_navigator();
        if (strpos($introduction_section, '<iframe') !== false && $browser['name'] == 'Chrome') {
            header('X-XSS-Protection: 0');
        }
	}
}
$introduction_section .=  '</div>';

$browser = api_get_navigator();

if (strpos($introduction_section, '<iframe') !== false && $browser['name'] == 'Chrome') {
    header("X-XSS-Protection: 0");
}
