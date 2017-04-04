<?php
/* For licensing terms, see /license.txt */

/**
 * View (MVC patter) for thematic control
 * @author Christian Fasanando <christian1827@gmail.com>
 * @author Julio Montoya <gugli100@gmail.com> Bug fixing
 * @package chamilo.course_progress
 */

// protect a course script
api_protect_course_script(true);

$token = Security::get_token();
$url_token = "&sec_token=".$token;
$user_info = api_get_user_info();
$params = '&'.api_get_cidreq();

$tpl = new Template(get_lang('ThematicControl'));

$toolbar = null;
if (api_is_allowed_to_edit(null, true)) {
    
    switch ($action) {
        case 'thematic_add':
        case 'thematic_import_select':
            $actionLeft = '<a href="index.php?'.api_get_cidreq().'">'.
                Display::return_icon('back.png', get_lang('BackTo').' '.get_lang('ThematicDetails'), '', ICON_SIZE_MEDIUM).'</a>';
            break;
        case 'thematic_list':
            $actionLeft = '<a href="index.php?'.api_get_cidreq().'&action=thematic_add'.$url_token.'">'.
                Display::return_icon('new_course_progress.png', get_lang('NewThematicSection'), '', ICON_SIZE_MEDIUM).'</a>';
            break;
        case 'thematic_details':
            $actionLeft = '<a href="index.php?'.api_get_cidreq().'&action=thematic_add'.$url_token.'">'.
                Display::return_icon('new_course_progress.png', get_lang('NewThematicSection'), '', ICON_SIZE_MEDIUM).'</a>';
            $actionLeft .= '<a href="index.php?'.api_get_cidreq().'&action=thematic_import_select'.$url_token.'">'.
                Display::return_icon('import_csv.png', get_lang('ImportThematic'), '', ICON_SIZE_MEDIUM).'</a>';
            $actionLeft .= '<a href="index.php?'.api_get_cidreq().'&action=thematic_export'.$url_token.'">'.
                Display::return_icon('export_csv.png', get_lang('ExportThematic'), '', ICON_SIZE_MEDIUM).'</a>';
            $actionLeft .= '<a href="index.php?'.api_get_cidreq().'&action=thematic_export_pdf'.$url_token.'">'.
                Display::return_icon('pdf.png', get_lang('ExportToPDF'), '', ICON_SIZE_MEDIUM).'</a>';
            break;
        default:
            $actionLeft = '<a href="index.php?'.api_get_cidreq().'&action=thematic_add'.$url_token.'">'.
                Display::return_icon('new_course_progress.png', get_lang('NewThematicSection'), '', ICON_SIZE_MEDIUM).'</a>';
    }
    
    $toolbar= Display::toolbarAction('thematic-bar', array(0 => $actionLeft));
}


if ($action == 'thematic_list') {
    $table = new SortableTable(
        'thematic_list',
        array('Thematic', 'get_number_of_thematics'),
        array('Thematic', 'get_thematic_data')
    );

    $parameters['action'] = $action;
    $table->set_additional_parameters($parameters);
    $table->set_header(0, '', false, array('style' => 'width:20px;'));
    $table->set_header(1, get_lang('Title'), false);
    if (api_is_allowed_to_edit(null, true)) {
        $table->set_header(
            2,
            get_lang('Actions'),
            false,
            array('style' => 'text-align:center;width:40%;')
        );
        $table->set_form_actions(array('thematic_delete_select' => get_lang('DeleteAllThematics')));
    }
    $table->display();

} elseif ($action == 'thematic_details') {
    if (isset($_GET['thematic_plan_save_message']) && $_GET['thematic_plan_save_message'] == 'ok') {
        Display::addFlash(Display::return_message(get_lang('ThematicSectionHasBeenCreatedSuccessfull'), 'confirmation', false));
    }

    if (isset($last_id) && $last_id) {
        $link_to_thematic_plan = '<a href="index.php?'.api_get_cidreq().'&action=thematic_plan_list&thematic_id='.$last_id.'">'.
            Display::return_icon('lesson_plan.png', get_lang('ThematicPlan'), array('style' => 'vertical-align:middle;float:none;'), ICON_SIZE_SMALL).'</a>';
        $link_to_thematic_advance = '<a href="index.php?'.api_get_cidreq().'&action=thematic_advance_list&thematic_id='.$last_id.'">'.
            Display::return_icon('lesson_plan_calendar.png', get_lang('ThematicAdvance'), array('style' => 'vertical-align:middle;float:none;'), ICON_SIZE_SMALL).'</a>';
        Display::addFlash(Display::return_message(
            get_lang('ThematicSectionHasBeenCreatedSuccessfull').'<br />'.sprintf(get_lang('NowYouShouldAddThematicPlanXAndThematicAdvanceX'), $link_to_thematic_plan, $link_to_thematic_advance),
            'confirmation',
            false
        ));
    }
    if (empty($thematic_id)) {
        // display information
        $text = '<strong>'.get_lang('Information').': </strong>';
        $text .= get_lang('ThematicDetailsDescription');
        $message = Display::return_message($text, 'info', false);
    }
    $list = [];
    // Display thematic data

    if (!empty($thematic_data)) {
        // display progress
        foreach ($thematic_data as $thematic) {
            
            $list['id'] = $thematic['id'];
            $list['id_course'] = $thematic['c_id'];
            $list['id_session'] = $thematic['session_id'];
            $list['title'] = Security::remove_XSS($thematic['title'], STUDENT);
            $list['content'] = Security::remove_XSS($thematic['content'], STUDENT);
            $list['display_orden'] = $thematic['display_order'];
            $list['active'] = $thematic['active'];
            
            $my_thematic_id = $thematic['id'];

            $session_star = '';
            if (api_is_allowed_to_edit(null, true)) {
                if (api_get_session_id() == $thematic['session_id']) {
                    $session_star = api_get_session_image(api_get_session_id(), $user_info['status']);
                }
            }

            $tpl->assign('session_star', $session_star);

            //@todo add a validation in order to load or not course thematics in the session thematic
            $toolbarThematic = '';
            if (api_is_allowed_to_edit(null, true)) {
                // Thematic title
                $toolbarThematic = Display::url(
                    Display::return_icon('cd.png', get_lang('Copy'), null, ICON_SIZE_TINY),
                    'index.php?'.api_get_cidreq().'&action=thematic_copy&thematic_id='.$my_thematic_id.$params.$url_token,
                        array('class'=> 'btn btn-default')
                );
                if (api_get_session_id() == 0) {
                    if ($thematic['display_order'] > 1) {
                        $toolbarThematic .= ' <a class="btn btn-default" href="'.api_get_self().'?action=moveup&'.api_get_cidreq().'&thematic_id='.$my_thematic_id.$params.$url_token.'">'.Display::return_icon('up.png', get_lang('Up'), '', ICON_SIZE_TINY).'</a>';
                    } else {
                        $toolbarThematic .= '<div class="btn btn-default">'.Display::return_icon('up_na.png', '&nbsp;', '', ICON_SIZE_TINY) . '</div>';
                    }
                    if (isset($thematic['max_thematic_item']) && $thematic['display_order'] < $thematic['max_thematic_item']) {
                        $toolbarThematic .= ' <a class="btn btn-default" href="'.api_get_self().'?action=movedown&a'.api_get_cidreq().'&thematic_id='.$my_thematic_id.$params.$url_token.'">'.Display::return_icon('down.png', get_lang('Down'), '', ICON_SIZE_TINY).'</a>';
                    } else {
                        $toolbarThematic .= '<div class="btn btn-default">'.Display::return_icon('down_na.png', '&nbsp;', '', ICON_SIZE_TINY) . '</div>';
                    }
                }
                if (api_get_session_id() == $thematic['session_id']) {
                    $toolbarThematic .= Display::url(
                        Display::return_icon('pdf.png', get_lang('ExportToPDF'), null, ICON_SIZE_TINY),
                        api_get_self().'?'.api_get_cidreq()."$url_token&".http_build_query([
                            'action' => 'export_single_thematic',
                            'thematic_id' => $my_thematic_id
                        ]),
                        array('class' => 'btn btn-default')
                    );
                    $toolbarThematic .= '<a class="btn btn-default" href="index.php?'.api_get_cidreq().'&action=thematic_edit&thematic_id='
                        .$my_thematic_id.$params.$url_token.'">'
                        .Display::return_icon('edit.png', get_lang('Edit'), '', ICON_SIZE_TINY).'</a>';
                    $toolbarThematic .= '<a class="btn btn-default" onclick="javascript:if(!confirm(\''
                        .get_lang('AreYouSureToDelete')
                        .'\')) return false;" href="index.php?'.api_get_cidreq().'&action=thematic_delete&thematic_id='
                        .$my_thematic_id.$params.$url_token.'">'
                        .Display::return_icon('delete.png', get_lang('Delete'), '', ICON_SIZE_TINY).'</a>';
                }
            }
            if (empty($thematic_plan_div[$thematic['id']])) {
                $list['thematic_plan'] = null;
            } else {
                $list['thematic_plan'] = $thematic_plan_div[$thematic['id']];
            }
            $list['thematic_advance'] = $thematic_advance_data[$thematic['id']];
            $list['last_done'] = $last_done_thematic_advance;
            $list['toolbar'] = $toolbarThematic;
            $listThematic[] = $list;

            $tpl->assign('data', $listThematic);
        } //End for
    }
} elseif ($action == 'thematic_add' || $action == 'thematic_edit') {
    // Display form
    $form = new FormValidator('thematic_add', 'POST', 'index.php?action=thematic_add&'.api_get_cidreq());
    if ($action == 'thematic_edit') {
        $form->addElement('header', '', get_lang('EditThematicSection'));
    }

    $form->addElement('hidden', 'sec_token', $token);
    $form->addElement('hidden', 'action', $action);

    if (!empty($thematic_id)) {
        $form->addElement('hidden', 'thematic_id', $thematic_id);
    }

    $form->addText('title', get_lang('Title'), true, array('size' => '50'));
    $form->addHtmlEditor('content', get_lang('Content'), false, false, array('ToolbarSet' => 'TrainingDescription', 'Height' => '150'));
    $form->addButtonSave(get_lang('Save'));

    $show_form = true;

    if (!empty($thematic_data)) {
        if (api_get_session_id()) {
            if ($thematic_data['session_id'] != api_get_session_id()) {
                $show_form = false;
                Display::display_error_message(get_lang('NotAllowedClickBack'), false);
            }
        }
        // set default values
        $default['title'] = $thematic_data['title'];
        $default['content'] = $thematic_data['content'];
        $form->setDefaults($default);
    }

    // error messages
    if (isset($error)) {
        Display::display_error_message(get_lang('FormHasErrorsPleaseComplete'), false);
    }
    if ($show_form) {
        $form->display();
    }
} elseif ($action == 'thematic_import_select') {
    // Create form to upload csv file.
    $form = new FormValidator('thematic_import', 'POST', 'index.php?action=thematic_import&'.api_get_cidreq().$url_token);
    $form->addElement('header', get_lang('ImportThematic'));
    $form->addElement('file', 'file');
    $form->addElement('checkbox', 'replace', null, get_lang('DeleteAllThematic'));
    $form->addButtonImport(get_lang('Import'), 'SubmitImport');
    $form->display();
}

$tpl->assign('actions', $toolbar);
$tpl->assign('message', $message);
$tpl->assign('score_progress', $total_average_of_advances);
$thematicLayout = $tpl->get_template('course_progress/progress.tpl');
$tpl->display($thematicLayout);
