<?php
/* For licensing terms, see /license.txt */

/**
 * This file was originally the copy of document.php, but many modifications happened since then ;
 * the direct file view is not any more needed, if the user uploads a SCORM zip file, a directory
 * will be automatically created for it, and the files will be uncompressed there for example ;
 *
 * @package chamilo.learnpath
 * @author Yannick Warnier <ywarnier@beeznest.org>
 */
use \ChamiloSession as Session;

$this_section = SECTION_COURSES;
//@todo who turns on $lp_controller_touched?
if (empty($lp_controller_touched) || $lp_controller_touched != 1) {
    header('location: lp_controller.php?action=list');
}

require_once 'back_compat.inc.php';
$courseDir = api_get_course_path().'/scorm';
$baseWordDir = $courseDir;

require_once 'learnpathList.class.php';
require_once 'learnpath.class.php';
require_once 'learnpathItem.class.php';

/**
 * Display initialisation and security checks
 */
// Extra javascript functions for in html head:
$htmlHeadXtra[] =
    "<script>
function confirmation(name) {
    if (confirm(\" ".trim(get_lang('AreYouSureToDelete'))." \"+name+\"?\"))
        {return true;}
    else
        {return false;}
}
</script>";
$nameTools = get_lang('LearningPaths');
event_access_tool(TOOL_LEARNPATH);

api_protect_course_script();
//if (!$is_allowed_in_course) api_not_allowed();

/**
 * Display
 */
/* Require the search widget and prepare the header with its stuff. */
if (api_get_setting('search_enabled') == 'true') {
    require api_get_path(LIBRARY_PATH).'search/search_widget.php';
    search_widget_prepare($htmlHeadXtra);
}
Display::display_header($nameTools, 'Path');
$current_session = api_get_session_id();

/* Introduction section (editable by course admins) */

Display::display_introduction_section(TOOL_LEARNPATH, array(
    'CreateDocumentWebDir' => api_get_path(WEB_COURSE_PATH).api_get_course_path().'/document/',
    'CreateDocumentDir' => '../../courses/'.api_get_course_path().'/document/',
    'BaseHref' => api_get_path(WEB_COURSE_PATH).api_get_course_path().'/'
));

$is_allowed_to_edit = api_is_allowed_to_edit(null, true);

if ($is_allowed_to_edit) {
    if (!empty($dialog_box)) {
        switch ($_GET['dialogtype']) {
            case 'confirmation':
                Display::display_confirmation_message($dialog_box);
                break;
            case 'error':
                Display::display_error_message($dialog_box);
                break;
            case 'warning':
                Display::display_warning_message($dialog_box);
                break;
            default:
                Display::display_normal_message($dialog_box);
                break;
        }
    }
    if (api_failure::get_last_failure()) {
        Display::display_normal_message(api_failure::get_last_failure());
    }

    echo '<div class="actions">';
    echo '<a href="'.api_get_self().'?'.api_get_cidreq().'&action=add_lp">'.Display::return_icon('new_learnpath.png', get_lang('LearnpathAddLearnpath'), '', ICON_SIZE_MEDIUM).'</a>'.
        str_repeat('&nbsp;', 3).
        '<a href="../upload/index.php?'.api_get_cidreq().'&curdirpath=/&tool='.TOOL_LEARNPATH.'">'.Display::return_icon('import_scorm.png', get_lang('UploadScorm'), '', ICON_SIZE_MEDIUM).'</a>';
    if (api_get_setting('service_ppt2lp', 'active') == 'true') {
        echo str_repeat('&nbsp;', 3).'<a href="../upload/upload_ppt.php?'.api_get_cidreq().'&curdirpath=/&tool='.TOOL_LEARNPATH.'">
		'.Display::return_icon('import_powerpoint.png', get_lang('PowerPointConvert'), '', ICON_SIZE_MEDIUM).'</a>';
    }
    echo '</div>';
}

$token = Security::get_token();

/* DISPLAY SCORM LIST */
$userId = api_get_user_id();
$userInfo = api_get_user_info();

$list = new LearnpathList($userId);
$flat_list = $list->get_flat_list();

if (!empty($flat_list)) {
    echo '<table class="data_table">';
    echo '<tr>';

    if ($is_allowed_to_edit) {
        echo '<th width="35%">'.get_lang('Title').'</th>';
        echo '<th>'.get_lang('PublicationDate').'</th>';
        echo '<th>'.get_lang('ExpirationDate').'</th>';
        echo '<th>'.get_lang('Progress')."</th>";
        echo '<th width="300px">'.get_lang('AuthoringOptions')."</th>";
    } else {
        echo '<th width="50%">'.get_lang('Title').'</th>';
        if (!api_is_invited_user()) {
            echo '<th>'.get_lang('Progress')."</th>";
        }
        echo '<th>'.get_lang('Actions')."</th>";
    }
    echo '</tr>';

    $test_mode = api_get_setting('server_type');
    $max = count($flat_list);
    $counter = 0;
    $current = 0;
    $autolunch_exists = false;
    foreach ($flat_list as $id => $details) {

        // Validation when belongs to a session.
        $session_img = api_get_session_image($details['lp_session'], $userInfo['status']);

        if (!$is_allowed_to_edit && $details['lp_visibility'] == 0) {
            // This is a student and this path is invisible, skip.
            continue;
        }

        // Check if the learnpath is visible for student.
        if (!$is_allowed_to_edit && !learnpath::is_lp_visible_for_student($id, $userId)) {
            continue;
        }

        $start_time = $end_time = '';
        if (!$is_allowed_to_edit) {
            $time_limits = false;

            //This is an old LP (from a migration 1.8.7) so we do nothing
            if ((empty($details['created_on']) || $details['created_on'] == '0000-00-00 00:00:00') &&
                (empty($details['modified_on']) || $details['modified_on'] == '0000-00-00 00:00:00')
            ) {
                $time_limits = false;
            }

            //Checking if expired_on is ON
            if ($details['expired_on'] != '' && $details['expired_on'] != '0000-00-00 00:00:00') {
                $time_limits = true;
            }

            if ($time_limits) {
                // Check if start time
                if (!empty($details['publicated_on']) && $details['publicated_on'] != '0000-00-00 00:00:00' &&
                    !empty($details['expired_on']) && $details['expired_on'] != '0000-00-00 00:00:00') {
                    $start_time = api_strtotime($details['publicated_on'], 'UTC');
                    $end_time = api_strtotime($details['expired_on'], 'UTC');
                    $now = time();
                    $is_actived_time = false;

                    if ($now > $start_time && $end_time > $now) {
                        $is_actived_time = true;
                    }

                    if (!$is_actived_time) {
                        continue;
                    }
                }
            }
            $start_time = $end_time = '';
        } else {
            if (!empty($details['publicated_on'])) {
                $start_time = api_convert_and_format_date($details['publicated_on'], DATE_TIME_FORMAT_LONG);
            }
            if (!empty($details['expired_on'])) {
                $end_time = api_convert_and_format_date($details['expired_on'], DATE_TIME_FORMAT_LONG);
            }
        }

        $counter++;
        if (($counter % 2) == 0) {
            $oddclass = 'row_odd';
        } else {
            $oddclass = 'row_even';
        }

        $url_start_lp = 'lp_controller.php?'.api_get_cidreq().'&action=view&lp_id='.$id;
        $name = Security::remove_XSS($details['lp_name']);
        $extra = null;

        if ($is_allowed_to_edit) {
            $url_start_lp .= '&isStudentView=true';
            $studentVisibility = learnpath::is_lp_visible_for_student($id, $userId);
            $dsp_desc = '<em>'.$details['lp_maker'].'</em>   '.( $studentVisibility ? '' : ' - ('.get_lang('LPNotVisibleToStudent').')');
            $extra = '<div class ="lp_content_type_label">'.$dsp_desc.'</div>';
        }

        $my_title = $name;
        $icon_learnpath = Display::return_icon('learnpath.png', get_lang('LPName'), '', ICON_SIZE_SMALL);

        if ($details['lp_visibility'] == 0) {
            $my_title = Display::tag('font', $name, array('class' => 'invisible'));
            $icon_learnpath = Display::return_icon('learnpath_na.png', get_lang('LPName'), '', ICON_SIZE_SMALL);
        }

        $dsp_line = '<tr align="center" class="'.$oddclass.'">'.
            '<td align="left" valign="top">'.$icon_learnpath.'
                     <a href="'.$url_start_lp.'">'.$my_title.'</a>'.$session_img.$extra."</td>";

        $dsp_desc = '';
        $dsp_export = '';
        $dsp_build = '';
        $dsp_delete = '';
        $dsp_visible = '';
        $dsp_default_view = '';
        $dsp_debug = '';
        $dsp_order = '';

        $progress = 0;

        if (!api_is_invited_user()) {
            $progress = learnpath::getProgress(
                $id,
                $userId,
                api_get_course_int_id(),
                api_get_session_id()
            );
        }

        if ($is_allowed_to_edit) {
            $dsp_progress = '<td><center>'.$progress.'</center></td>';
        } else {
            $dsp_progress = "";

            if (!api_is_invited_user()) {
                $dsp_progress = '<td>'.learnpath::get_progress_bar($progress, '%').'</td>';
            }
        }

        $dsp_edit = '<td class="td_actions">';
        $dsp_edit_close = '</td>';

        $token_parameter = "&sec_token=$token";
        $dsp_edit_lp = null;
        $dsp_publish = null;
        $dsp_reinit = null;
        $dsp_disk = null;
        $copy = null;
        $lp_auto_lunch_icon = null;

        if ($is_allowed_to_edit) {

            // EDIT LP
            if ($current_session == $details['lp_session']) {
                $dsp_edit_lp = '<a href="lp_controller.php?'.api_get_cidreq().'&action=edit&lp_id='.$id.'">'.
                    Display::return_icon('settings.png', get_lang('CourseSettings'), '', ICON_SIZE_SMALL).'</a>';
            } else {
                $dsp_edit_lp = Display::return_icon('settings_na.png', get_lang('CourseSettings'), '', ICON_SIZE_SMALL);
            }

            // BUILD
            if ($current_session == $details['lp_session']) {
                if ($details['lp_type'] == 1 || $details['lp_type'] == 2) {
                    $dsp_build = '<a href="lp_controller.php?'.api_get_cidreq().'&amp;action=add_item&amp;type=step&amp;lp_id='.$id.'">'.
                        Display::return_icon('edit.png', get_lang('LearnpathEditLearnpath'), '', ICON_SIZE_SMALL).'</a>';
                } else {
                    $dsp_build = Display::return_icon('edit_na.png', get_lang('LearnpathEditLearnpath'), '', ICON_SIZE_SMALL);
                }
            } else {
                $dsp_build = Display::return_icon('edit_na.png', get_lang('LearnpathEditLearnpath'), '', ICON_SIZE_SMALL);
            }

            /* VISIBILITY COMMAND */

            /*  Session test not necessary if we want to show base course learning
                paths inside the session.
                See http://support.chamilo.org/projects/chamilo-18/wiki/Tools_and_sessions).
            */
            if ($details['lp_visibility'] == 0) {
                $dsp_visible = "<a href=\"".api_get_self()."?".api_get_cidreq()."&lp_id=$id&action=toggle_visible&new_status=1\">".Display::return_icon('invisible.png', get_lang('Show'), '', ICON_SIZE_SMALL)."</a>";
            } else {
                $dsp_visible = "<a href='".api_get_self()."?".api_get_cidreq()."&lp_id=$id&action=toggle_visible&new_status=0'>".Display::return_icon('visible.png', get_lang('Hide'), '', ICON_SIZE_SMALL)."</a>";
            }

            /* PUBLISH COMMAND */
            if ($current_session == $details['lp_session']) {
                if ($details['lp_published'] == "i") {
                    $dsp_publish = "<a href=\"".api_get_self()."?".api_get_cidreq()."&lp_id=$id&action=toggle_publish&new_status=v\">".
                        Display::return_icon('lp_publish_na.png', get_lang('LearnpathPublish'), '', ICON_SIZE_SMALL)."</a>";
                } else {
                    $dsp_publish = "<a href='".api_get_self()."?".api_get_cidreq()."&lp_id=$id&action=toggle_publish&new_status=i'>".Display::return_icon('lp_publish.png', get_lang('LearnpathDoNotPublish'), '', ICON_SIZE_SMALL)."</a>";
                }
            } else {
                $dsp_publish = Display::return_icon('lp_publish_na.png', get_lang('LearnpathDoNotPublish'), '', ICON_SIZE_SMALL);
            }

            /*  MULTIPLE ATTEMPTS OR SERIOUS GAME MODE

              SERIOUSGAME MODE is a special mode where :
             * If a user exits the learning path before finishing it, he comes back where he left next time he tries
             * When lp status is completed, user can still modify the attempt (adds/time change score, and browse it)
             * It is thus a mix betwenn multiple attempt and mono attempt
             */
            if ($current_session == $details['lp_session']) {
                if ($details['seriousgame_mode'] == 1 && $details['lp_prevent_reinit'] == 1) { //seriousgame mode | next = single
                    $dsp_reinit = '<a href="lp_controller.php?'.api_get_cidreq().'&action=switch_attempt_mode&lp_id='.$id.'">'.
                        Display::return_icon('reload.png', get_lang('PreventMultipleAttempts'), '', ICON_SIZE_SMALL).
                        '</a>';
                }
                if ($details['seriousgame_mode'] == 0 && $details['lp_prevent_reinit'] == 1) { //single mode | next = multiple
                    $dsp_reinit = '<a href="lp_controller.php?'.api_get_cidreq().'&action=switch_attempt_mode&lp_id='.$id.'">'.
                        Display::return_icon('reload_na.png', get_lang('AllowMultipleAttempts'), '', ICON_SIZE_SMALL).
                        '</a>';
                }
                if ($details['seriousgame_mode'] == 0 && $details['lp_prevent_reinit'] == 0) { //multiple mode | next = seriousgame
                    $dsp_reinit = '<a href="lp_controller.php?'.api_get_cidreq().'&action=switch_attempt_mode&lp_id='.$id.'">'.Display::return_icon('reload.png', get_lang('AllowMultipleAttempts'), '', ICON_SIZE_SMALL).
                        '</a>';
                }
            } else {
                $dsp_reinit = Display::return_icon('reload_na.png', get_lang('AllowMultipleAttempts'), '', ICON_SIZE_SMALL);
            }

            /* SCREEN LP VIEW */
            if ($current_session == $details['lp_session']) {

                switch ($details['lp_view_mode']) {
                    case 'fullscreen':
                        $dsp_default_view = '<a href="lp_controller.php?'.api_get_cidreq().'&action=switch_view_mode&lp_id='.$id.$token_parameter.'">'.
                            Display::return_icon('view_fullscreen.png', get_lang('ViewModeFullScreen'), '', ICON_SIZE_SMALL).'</a>';
                        break;
                    case 'embedded':
                        $dsp_default_view = '<a href="lp_controller.php?'.api_get_cidreq().'&action=switch_view_mode&lp_id='.$id.$token_parameter.'">'.
                            Display::return_icon('view_left_right.png', get_lang('ViewModeEmbedded'), '', ICON_SIZE_SMALL).'</a>';
                        break;
                    case 'embedframe':
                        $dsp_default_view = '<a href="lp_controller.php?'.api_get_cidreq().'&action=switch_view_mode&lp_id='.$id.$token_parameter.'">'.
                            Display::return_icon('view_nofullscreen.png', get_lang('ViewModeEmbedFrame'), '', ICON_SIZE_SMALL).'</a>';
                        break;
                    case 'impress':
                        $dsp_default_view = '<a href="lp_controller.php?'.api_get_cidreq().'&action=switch_view_mode&lp_id='.$id.$token_parameter.'">'.
                            Display::return_icon('window_list_slide.png', get_lang('ViewModeImpress'), '', ICON_SIZE_SMALL).'</a>';
                        break;
                }
            } else {
                if ($details['lp_view_mode'] == 'fullscreen') {
                    $dsp_default_view = Display::return_icon('view_fullscreen_na.png', get_lang('ViewModeEmbedded'), '', ICON_SIZE_SMALL);
                } else {
                    $dsp_default_view = Display::return_icon('view_left_right_na.png', get_lang('ViewModeEmbedded'), '', ICON_SIZE_SMALL);
                }
            }

            /*  DEBUG  */
            if ($test_mode == 'test' or api_is_platform_admin()) {
                if ($details['lp_scorm_debug'] == 1) {
                    $dsp_debug = '<a href="lp_controller.php?'.api_get_cidreq().'&action=switch_scorm_debug&lp_id='.$id.'">'.
                        Display::return_icon('bug.png', get_lang('HideDebug'), '', ICON_SIZE_SMALL).'</a>';
                } else {
                    $dsp_debug = '<a href="lp_controller.php?'.api_get_cidreq().'&action=switch_scorm_debug&lp_id='.$id.'">'.
                        Display::return_icon('bug_na.png', get_lang('ShowDebug'), '', ICON_SIZE_SMALL).'</a>';
                }
            }

            /* Export */
            if ($details['lp_type'] == 1) {
                $dsp_disk = Display::url(
                    Display::return_icon('cd.gif', get_lang('Export'), array(), ICON_SIZE_SMALL),
                    api_get_self()."?".api_get_cidreq()."&action=export&lp_id=$id"
                );
            } elseif ($details['lp_type'] == 2) {
                $dsp_disk = Display::url(
                    Display::return_icon('cd.gif', get_lang('Export'), array(), ICON_SIZE_SMALL),
                    api_get_self()."?".api_get_cidreq()."&action=export&lp_id=$id&export_name=".replace_dangerous_char($name, 'strict').".zip"
                );
            } else {
                $dsp_disk = Display::return_icon('cd_gray.gif', get_lang('Export'), array(), ICON_SIZE_SMALL);
            }

            // Copy
            $copy = Display::url(
                Display::return_icon(
                    'cd_copy.png',
                    get_lang('Copy'),
                    array(),
                    ICON_SIZE_SMALL
                ),
                api_get_self() . "?" . api_get_cidreq() . "&action=copy&lp_id=$id"
            );

            /* Auto Lunch LP code */
            if (api_get_course_setting('enable_lp_auto_launch') == 1) {
                if ($details['autolaunch'] == 1 && $autolunch_exists == false) {
                    $autolunch_exists = true;
                    $lp_auto_lunch_icon = '<a href="'.api_get_self().'?'.api_get_cidreq().'&action=auto_launch&status=0&lp_id='.$id.'">
                        <img src="../img/launch.png" border="0" title="'.get_lang('DisableLPAutoLaunch').'" /></a>';
                } else {
                    $lp_auto_lunch_icon = '<a href="'.api_get_self().'?'.api_get_cidreq().'&action=auto_launch&status=1&lp_id='.$id.'">
                        <img src="../img/launch_na.png" border="0" title="'.get_lang('EnableLPAutoLaunch').'" /></a>';
                }
            }

            // Export to PDF
            $export_icon = ' <a href="'.api_get_self().'?'.api_get_cidreq().'&action=export_to_pdf&lp_id='.$id.'">
				  '.Display::return_icon('pdf.png', get_lang('ExportToPDFOnlyHTMLAndImages'), '', ICON_SIZE_SMALL).'</a>';

            /* Delete */
            if ($current_session == $details['lp_session']) {
                $dsp_delete = "<a href=\"lp_controller.php?".api_get_cidreq()."&action=delete&lp_id=$id\" ".
                    "onclick=\"javascript: return confirmation('".addslashes($name)."');\">".
                    Display::return_icon('delete.png', get_lang('LearnpathDeleteLearnpath'), '', ICON_SIZE_SMALL).'</a>';
            } else {
                $dsp_delete = Display::return_icon('delete_na.png', get_lang('LearnpathDeleteLearnpath'), '', ICON_SIZE_SMALL);
            }

            /* COLUMN ORDER	 */
            // Only active while session mode is not active
            if ($current_session == 0) {
                if ($details['lp_display_order'] == 1 && $max != 1) {
                    $dsp_order .= '<a href="lp_controller.php?'.api_get_cidreq().'&action=move_lp_down&lp_id='.$id.'">
                         '.Display::return_icon('down.png', get_lang('MoveDown'), '', ICON_SIZE_SMALL).'</a>';
                } elseif ($current == $max - 1 && $max != 1) {
                    $dsp_order .= '<a href="lp_controller.php?'.api_get_cidreq().'&action=move_lp_up&lp_id='.$id.'">
					'.Display::return_icon('up.png', get_lang('MoveUp'), '', ICON_SIZE_SMALL).'</a>';
                } elseif ($max == 1) {
                    $dsp_order = '';
                } else {
                    $dsp_order .= '<a href="lp_controller.php?'.api_get_cidreq().'&action=move_lp_down&lp_id='.$id.'">'.
                        Display::return_icon('down.png', get_lang('MoveDown'), '', ICON_SIZE_SMALL).'</a>';
                    $dsp_order .= '<a href="lp_controller.php?'.api_get_cidreq().'&action=move_lp_up&lp_id='.$id.'">'.
                        Display::return_icon('up.png', get_lang('MoveUp'), '', ICON_SIZE_SMALL).'</a>';
                }
            }
            if ($is_allowed_to_edit) {
                $start_time = Display::tag('td', Display::div($start_time, array('class' => 'small')));
                $end_time = Display::tag('td', Display::div($end_time, array('class' => 'small')));
            } else {
                $start_time = $end_time = '';
            }
        } else {
            // Student
            $export_icon = ' <a href="'.api_get_self().'?'.api_get_cidreq().'&action=export_to_pdf&lp_id='.$id.'">'.
                Display::return_icon('pdf.png', get_lang('ExportToPDF'), '', ICON_SIZE_SMALL).'</a>';
        }

        global $_configuration;
        if (isset($_configuration['hide_scorm_export_link']) && $_configuration['hide_scorm_export_link']) {
            $dsp_disk = null;
        }

        if (isset($_configuration['hide_scorm_copy_link']) && $_configuration['hide_scorm_copy_link']) {
            $copy = null;
        }

        if (isset($_configuration['hide_scorm_pdf_link']) && $_configuration['hide_scorm_pdf_link']) {
            $export_icon = null;
        }

        echo $dsp_line.$start_time.$end_time.$dsp_progress.$dsp_desc.$dsp_export.$dsp_edit.$dsp_build.$dsp_edit_lp.$dsp_visible.$dsp_publish.$dsp_reinit.
            $dsp_default_view.$dsp_debug.$dsp_disk.$copy.$lp_auto_lunch_icon.$export_icon.$dsp_delete.$dsp_order.$dsp_edit_close;

        echo "</tr>";
        //counter for number of elements treated
        $current++;

    } // end foreach ($flat_list)
    echo "</table>";
} else {
    if ($is_allowed_to_edit) {
        echo '<div id="no-data-view">';
        echo '<h2>'.get_lang('LearningPaths').'</h2>';
        echo Display::return_icon('scorms.png', '', array(), 64);
        echo '<div class="controls">';
        echo Display::url(get_lang('LearnpathAddLearnpath'), api_get_self().'?'.api_get_cidreq().'&action=add_lp', array('class' => 'btn'));
        echo '</div>';
        echo '</div>';
    }
}
$course_info = api_get_course_info();
learnpath::generate_learning_path_folder($course_info);

// Deleting the objects
Session::erase('oLP');
Session::erase('lpobject');
DocumentManager::removeGeneratedAudioTempFile();
Display::display_footer();
