<?php
/* For licensing terms, see /license.txt */
/**
 * Special report for corporate users
 * @package chamilo.reporting
 */
/**
 * Code
 */
$language_file = array('admin', 'gradebook', 'tracking');
$cidReset = true;
//require_once '../inc/global.inc.php';

api_protect_admin_script();

$interbreadcrumb[] = array('url' => 'index.php', 'name' => get_lang('MySpace'));

$tool_name = get_lang('Report');

$this_section = SECTION_TRACKING;

$htmlHeadXtra[] = api_get_jqgrid_js();

//jqgrid will use this URL to do the selects
$url = api_get_path(WEB_AJAX_PATH).'model.ajax.php?a=get_user_course_report_resumed';

$extra_fields = UserManager::get_extra_fields(0, 100, null, null, true, true);

//The order is important you need to check the the $column variable in the model.ajax.php file
$columns = array(get_lang('Company'), get_lang('TrainingHoursAccumulated'), get_lang('CountOfSubscriptions'), get_lang('CountOfUsers'), get_lang('AverageHoursPerStudent'), get_lang('CountCertificates'));

//Column config
$column_model = array(
    array('name' => 'extra_ruc', 'index' => 'extra_ruc', 'width' => '100', 'align' => 'left', 'sortable' => 'false'),
    array('name' => 'training_hours', 'index' => 'training_hours', 'width' => '100', 'align' => 'left'),
    array('name' => 'count_users', 'index' => 'count_users', 'width' => '100', 'align' => 'left', 'sortable' => 'false'),
    array('name' => 'count_users_registered', 'index' => 'count_users_registered', 'width' => '100', 'align' => 'left', 'sortable' => 'false'),
    array('name' => 'average_hours_per_user', 'index' => 'average_hours_per_user', 'width' => '100', 'align' => 'left', 'sortable' => 'false'),
    array('name' => 'count_certificates', 'index' => 'count_certificates', 'width' => '100', 'align' => 'left', 'sortable' => 'false'),
);

//Autowidth
$extra_params['autowidth'] = 'true';
//height auto
$extra_params['height'] = 'auto';

$htmlHeadXtra[] = '<script>
$(function() {
    '.Display::grid_js('user_course_report', $url, $columns, $column_model, $extra_params, array(), null, true).'
    jQuery("#user_course_report").jqGrid("navGrid","#user_course_report_pager",{view:false, edit:false, add:false, del:false, search:false, excel:true});
    jQuery("#user_course_report").jqGrid("navButtonAdd","#user_course_report_pager",{
           caption:"",
           onClickButton : function () {
               jQuery("#user_course_report").jqGrid("excelExport",{"url":"'.$url.'&export_format=xls"});
           }
    });
});
</script>';
$content = Display::grid_html('user_course_report');

$app['title'] = $tool_name;
$tpl = $app['template'];
$tpl->assign('content', $content);
$tpl->display_one_col_template();
