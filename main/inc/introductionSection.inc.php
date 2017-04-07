<?php
/* For licensing terms, see /license.txt */

use Chamilo\CourseBundle\Entity\CToolIntro;

/**
 * The INTRODUCTION MICRO MODULE is used to insert and edit
 * an introduction section on a Chamilo module or on the course homepage.
 * It can be inserted on any Chamilo module, provided the corresponding setting
 * is enabled in the administration section.
 *
 * The introduction content are stored in a table called "tool_intro"
 * in the course Database. Each module introduction has an Id stored in
 * the table, which matches a specific module.
 *
 * '(c_)tool_intro' table description
 *   c_id: int
 *   id : int
 *   intro_text :text
 *   session_id: int
 *
 * usage :
 *
 * $moduleId = 'XX'; // specifying the module tool (string value)
 * include(introductionSection.inc.php);
 *
 * This script is also used since Chamilo 1.9 to show course progress (from the
 * course_progress module)
 *
 * @package chamilo.include
 */

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
    $form = new FormValidator(
        'introduction_text',
        'post',
        api_get_self().'?'.api_get_cidreq()
    );
} else {
    $form = new FormValidator('introduction_text');
}

$config = array(
    'ToolbarSet' => 'IntroductionTool',
    'Width' => '100%',
    'Height' => '300'
);

$form->addHtmlEditor('intro_content', null, false, false, $config, true);
$form->addButtonSave(get_lang('SaveIntroText'), 'intro_cmdUpdate');

/* INTRODUCTION MICRO MODULE - COMMANDS SECTION (IF ALLOWED) */
$course_id = api_get_course_int_id();

if ($intro_editAllowed) {
    /* Replace command */
    if ($intro_cmdUpdate) {
        if ($form->validate()) {
            $form_values = $form->exportValues();
            $intro_content = $form_values['intro_content'];

            $criteria = [
                'cId' => $course_id,
                'id' => $moduleId,
                'sessionId' => $session_id,
            ];

            if (!empty($intro_content)) {
                /** @var CToolIntro $toolIntro */
                $toolIntro = Database::getManager()
                    ->getRepository('ChamiloCourseBundle:CToolIntro')
                    ->findOneBy($criteria);

                if ($toolIntro) {
                    $toolIntro->setIntroText($intro_content);
                } else {
                    $toolIntro = new CToolIntro();
                    $toolIntro
                        ->setSessionId($session_id)
                        ->setCId($course_id)
                        ->setIntroText($intro_content)
                        ->setId($moduleId);
                }
                Database::getManager()->persist($toolIntro);
                Database::getManager()->flush();
                Display::addFlash(
                    Display::return_message(
                        get_lang('IntroductionTextUpdated'),
                        'confirmation',
                        false
                    )
                );
            } else {
                // got to the delete command
                $intro_cmdDel = true;
            }
        } else {
            $intro_cmdEdit = true;
        }
    }

    /* Delete Command */
    if ($intro_cmdDel) {
        $sql = "DELETE FROM $TBL_INTRODUCTION
                WHERE
                    c_id = $course_id AND
                    id='".Database::escape_string($moduleId)."' AND
                    session_id='".intval($session_id)."'";
        Database::query($sql);
        Display::addFlash(
            Display::return_message(get_lang('IntroductionTextDeleted'), 'confirmation')
        );
    }
}

/* INTRODUCTION MICRO MODULE - DISPLAY SECTION */

/* Retrieves the module introduction text, if exist */
/* @todo use a lib to query the $TBL_INTRODUCTION table */
// Getting course intro
$intro_content = '';
$sql = "SELECT intro_text FROM $TBL_INTRODUCTION
        WHERE
            c_id = $course_id AND
            id = '".Database::escape_string($moduleId)."' AND
            session_id = 0";

$intro_dbQuery = Database::query($sql);
if (Database::num_rows($intro_dbQuery) > 0) {
    $intro_dbResult = Database::fetch_array($intro_dbQuery);
    $intro_content = $intro_dbResult['intro_text'];
}

// Getting session intro
if (!empty($session_id)) {
    $sql = "SELECT intro_text FROM $TBL_INTRODUCTION
            WHERE
                c_id = $course_id AND
                id = '".Database::escape_string($moduleId)."' AND
                session_id = '".intval($session_id)."'";
    $intro_dbQuery = Database::query($sql);
    $introSessionContent = '';
    if (Database::num_rows($intro_dbQuery) > 0) {
        $intro_dbResult = Database::fetch_array($intro_dbQuery);
        $introSessionContent = $intro_dbResult['intro_text'];
    }
    // If the course session intro exists replace it.
    if (!empty($introSessionContent)) {
        $intro_content = $introSessionContent;
    }
}

// Default behaviour show iframes.
$userStatus = COURSEMANAGERLOWSECURITY;

// Allows to do a remove_XSS in course introduction with user status COURSEMANAGERLOWSECURITY
// Block embed type videos (like vimeo, wistia, etc) - see BT#12244 BT#12556
if (api_get_configuration_value('course_introduction_html_strict_filtering')) {
    $userStatus = COURSEMANAGER;
}

$intro_content = Security::remove_XSS($intro_content, $userStatus);

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
    $introduction_section .= $form->returnForm();
    $introduction_section .= '</div>';
}

$thematic_description_html = '';
$thematicItemTwo = '';

if ($tool == TOOL_COURSE_HOMEPAGE && !isset($_GET['intro_cmdEdit'])) {
    // Only show this if we're on the course homepage and we're not currently editing
    $thematic = new Thematic();
    $displayMode = api_get_course_setting('display_info_advance_inside_homecourse');
    $class1 = '';
    if ($displayMode == '1') {
        // Show only the current course progress step
        // $information_title = get_lang('InfoAboutLastDoneAdvance');
        $last_done_advance =  $thematic->get_last_done_thematic_advance();
        $thematic_advance_info = $thematic->get_thematic_advance_list($last_done_advance);
        $subTitle1 = get_lang('CurrentTopic');
        $class1 = ' current';
    } else if ($displayMode == '2') {
        // Show only the two next course progress steps
        // $information_title = get_lang('InfoAboutNextAdvanceNotDone');
        $last_done_advance = $thematic->get_next_thematic_advance_not_done();
        $next_advance_not_done = $thematic->get_next_thematic_advance_not_done(2);
        $thematic_advance_info = $thematic->get_thematic_advance_list($last_done_advance);
        $thematic_advance_info2 = $thematic->get_thematic_advance_list($next_advance_not_done);
        $subTitle1 = $subTitle2 = get_lang('NextTopic');
    } else if ($displayMode == '3') {
        // Show the current and next course progress steps
        // $information_title = get_lang('InfoAboutLastDoneAdvanceAndNextAdvanceNotDone');
        $last_done_advance =  $thematic->get_last_done_thematic_advance();
        $next_advance_not_done = $thematic->get_next_thematic_advance_not_done();
        $thematic_advance_info = $thematic->get_thematic_advance_list($last_done_advance);
        $thematic_advance_info2 = $thematic->get_thematic_advance_list($next_advance_not_done);
        $subTitle1 = get_lang('CurrentTopic');
        $subTitle2 = get_lang('NextTopic');
        $class1 = ' current';
    }

    if (!empty($thematic_advance_info)) {
        $thematic_advance = get_lang('CourseThematicAdvance');
        $thematicScore = $thematic->get_total_average_of_thematic_advances() . '%';
        $thematicUrl = api_get_path(WEB_CODE_PATH).'course_progress/index.php?action=thematic_details&'.api_get_cidreq();
        $thematic_info = $thematic->get_thematic_list(
            $thematic_advance_info['thematic_id']
        );

        $thematic_advance_info['start_date'] = api_get_local_time(
            $thematic_advance_info['start_date']
        );
        $thematic_advance_info['start_date'] = api_format_date(
            $thematic_advance_info['start_date'],
            DATE_TIME_FORMAT_LONG
        );
        $userInfo = api_get_user_info();
        $courseInfo = api_get_course_info();
        $titleThematic = $thematic_advance .' : '. $courseInfo['name'] . ' <b>( '. $thematicScore .' )</b>';

        $infoUser = '<div class="thematic-avatar"><img src="' . $userInfo['avatar'] . '" class="img-circle img-responsive"></div>';
        $infoUser .= '<div class="progress">
                        <div class="progress-bar progress-bar-danger" role="progressbar" style="width: ' . $thematicScore . ';">
                        '.$thematicScore.'
                        </div>
                    </div>';

        $thematicItemOne = '
        <div class="col-md-6 items-progress">
            <div class="thematic-cont '.$class1.'">
            <div class="topics">' . $subTitle1 . '</div>
            <h4 class="title-topics">' . Display::returnFontAwesomeIcon('book') . $thematic_info['title'] . '</h4>
            <p class="date">' .  Display::returnFontAwesomeIcon('calendar-o') . $thematic_advance_info['start_date'] . '</p>
            <div class="views">' . Display::returnFontAwesomeIcon('file-text-o')  . strip_tags($thematic_advance_info['content']). '</div>
            <p class="time">'. Display::returnFontAwesomeIcon('clock-o') . get_lang('DurationInHours') . ' : ' . $thematic_advance_info['duration'] . ' - <a href="' . $thematicUrl . '">' . get_lang('SeeDetail') . '</a></p>
            </div>
        </div>';

        if (!empty($thematic_advance_info2)) {
            $thematic_info2 = $thematic->get_thematic_list($thematic_advance_info2['thematic_id']);
            $thematic_advance_info2['start_date'] = api_get_local_time($thematic_advance_info2['start_date']);
            $thematic_advance_info2['start_date'] = api_format_date($thematic_advance_info2['start_date'], DATE_TIME_FORMAT_LONG);

            $thematicItemTwo = '
                <div class="col-md-6 items-progress">
                    <div class="thematic-cont">
                    <div class="topics">'.$subTitle2.'</div>
                    <h4 class="title-topics">'. Display::returnFontAwesomeIcon('book')  . $thematic_info2['title'].'</h4>
                    <p class="date">' . Display::returnFontAwesomeIcon('calendar-o') .$thematic_advance_info2['start_date'].'</p>
                    <div class="views">' . Display::returnFontAwesomeIcon('file-text-o')  . strip_tags($thematic_advance_info2['content']).'</div>
                    <p class="time">'. Display::returnFontAwesomeIcon('clock-o') .get_lang('DurationInHours').' : '.$thematic_advance_info2['duration'].' - <a href="'.$thematicUrl.'">'.get_lang('SeeDetail').'</a></p>
                    </div>
                </div>';
        }
        $thematicPanel = '<div class="row">';
        $thematicPanel .= '<div class="col-md-2">' . $infoUser . '</div>';
        $thematicPanel .= '<div class="col-md-10"><div class="row">' . $thematicItemOne . $thematicItemTwo . '</div></div>';
        $thematicPanel .= '</div>';
        $thematicPanel .= '<div class="separate">
                        <a href="' . $thematicUrl . '" class="btn btn-default btn-block">' . get_lang('ShowFullCourseAdvance') . '</a>
                    </div>';
        $thematicProgress = Display::panelCollapse($titleThematic, $thematicPanel, 'thematic', null, 'accordion-thematic', 'collapse-thematic', false);
    }
}
$introduction_section .= '<div class="row">';
if (!empty($thematic_advance_info)) {
    $introduction_section .= '<div class="col-md-12">';
    $introduction_section .= $thematic_description_html;
    $introduction_section .= $thematicProgress;
    $introduction_section .= '</div>';
}
$editIconButton = '';
if (api_is_allowed_to_edit() && empty($session_id)) {
    $editIconButton = Display::url(
        '<em class="fa fa-wrench"></em> ',
        api_get_path(WEB_CODE_PATH).'course_info/tools.php?'.api_get_cidreq(),
        ['class' => 'btn btn-default', 'title' => get_lang('CustomizeIcons') ]
    );
}

$toolbar = '';
$textIntro = '';
if ($intro_dispCommand) {
    if (empty($intro_content)) {
        // Displays "Add intro" commands
        $toolbar = '<div class="btn-group pull-right" role="group">';
        if (!empty($courseId)) {
            $textIntro  = '<a class="btn btn-default" title="' . addslashes(get_lang('AddIntro')) . '" href="'.api_get_self().'?' . api_get_cidreq().'&intro_cmdAdd=1">';
            $textIntro .= '<em class="fa fa-file-text"></em> ';
            $textIntro .= "</a>";
            $toolbar .= $textIntro . $editIconButton;
        } else {
            $toolbar .= '<a class="btn btn-default" href="' . api_get_self() . '?intro_cmdAdd=1">' . get_lang('AddIntro') . '</a>';
            $toolbar .= $editIconButton;
        }
        $toolbar .= '</div>';

    } else {
        // Displays "edit intro && delete intro" commands
        $toolbar .= '<div class="btn-group pull-right" rol="group">';
        if (!empty($courseId)) {
            $toolbar .=
                '<a  class="btn btn-default" href="'.api_get_self().'?'.api_get_cidreq().'&intro_cmdEdit=1" title="'.get_lang('Modify').'">
                <em class="fa fa-pencil"></em></a>';
            $toolbar .= $editIconButton;
            $toolbar .="<a class=\"btn btn-default\" href=\"".api_get_self()."?".api_get_cidreq()."&intro_cmdDel=1\" onclick=\"javascript:
                if(!confirm('".addslashes(api_htmlentities(get_lang('ConfirmYourChoice'), ENT_QUOTES, $charset)).
                "')) return false;\"><em class=\"fa fa-trash-o\"></em></a>";

        } else {
            $toolbar .=
                '<a class="btn btn-default" href="'.api_get_self().'?intro_cmdEdit=1" title="'.get_lang('Modify').'">
                <em class="fa fa-pencil"></em>
                </a>"';
            $toolbar .= $editIconButton;
            $toolbar .= "<a class=\"btn btn-default\" href=\"".api_get_self()."?".api_get_cidreq()."&intro_cmdDel=1\" onclick=\"javascript:
                if(!confirm('".addslashes(api_htmlentities(get_lang('ConfirmYourChoice'), ENT_QUOTES, $charset)).
                "')) return false;\"><em class=\"fa fa-trash-o\"></em></a>";
        }
        $toolbar .=  "</div>";
        // Fix for chrome XSS filter for videos in iframes - BT#7930
        $browser = api_get_navigator();
        if (strpos($introduction_section, '<iframe') !== false && $browser['name'] == 'Chrome') {
            header('X-XSS-Protection: 0');
        }
    }
}

$introduction_section .= '<div class="col-md-12">';

if ($intro_dispDefault) {
    if (!empty($intro_content)) {
        $introduction_section .= '<div class="page-course">';
        $introduction_section .= $intro_content;
        $introduction_section .= '</div>';
    } else {
        if (api_is_allowed_to_edit()) {
            $introduction_section .= '<div class="help-course">';
            $introduction_section .= get_lang('AddCustomCourseIntro') . ' ' . $textIntro;
            $introduction_section .= '</div>';
        }
    }
}

$introduction_section .= $toolbar;
$introduction_section .= '</div>';
$introduction_section .= '</div>';

$browser = api_get_navigator();

if (strpos($introduction_section, '<iframe') !== false && $browser['name'] == 'Chrome') {
    header("X-XSS-Protection: 0");
}
