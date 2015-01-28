<?php
/* For licensing terms, see /license.txt */
/**
 * Show information about Mozilla OpenBadges
 * @author Angel Fernando Quiroz Campos <angel.quiroz@beeznest.com>
 * @package chamilo.admin.openbadges
 */
$cidReset = true;

require_once '../inc/global.inc.php';
require_once '../inc/lib/fileUpload.lib.php';

if (!api_is_platform_admin()) {
    api_not_allowed(true);
}

$this_section = SECTION_PLATFORM_ADMIN;

$skillId = intval($_GET['id']);

$objSkill = new Skill();
$skill = $objSkill->get($skillId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $params = array(
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'criteria' => $_POST['criteria'],
        'id' => $skillId
    );

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $dirPermissions = api_get_permissions_for_new_directories();
        $sysCodePath = api_get_path(SYS_CODE_PATH);

        $fileDir = "upload/data/badges/";
        $fileName = sha1($_POST['name']) . ".png";

        if (!file_exists($sysCodePath . $fileDir)) {
            mkdir($sysCodePath . $fileDir, $dirPermissions, true);
        }
        
        if (!empty($skill['icon'])) {
            $iconFileAbsoulePath = $sysCodePath . $skill['icon'];
            $iconDirAbsolutePath = $sysCodePath . $fileDir;
            
            if (Security::check_abs_path($iconFileAbsoulePath, $iconDirAbsolutePath)) {
                unlink($sysCodePath . $skill['icon']);
            }
        }

        $imageExtraField = new Image($_FILES['image']['tmp_name']);
        $imageExtraField->send_image($sysCodePath . $fileDir . $fileName, -1, 'png');

        $params['icon'] = $fileDir . $fileName;
    }

    $objSkill->update($params);

    header('Location: ' . api_get_path(WEB_CODE_PATH) . 'admin/skill_badge_list.php');
    exit;
}

$interbreadcrumb = array(
    array(
        'url' => api_get_path(WEB_CODE_PATH) . 'admin/index.php',
        'name' => get_lang('Administration')
    ),
    array(
        'url' => api_get_path(WEB_CODE_PATH) . 'admin/skill_badge.php',
        'name' => get_lang('Badges')
    )
);

$tpl = new Template(get_lang('CreateBadge'));
$tpl->assign('platformAdminEmail', get_setting('emailAdministrator'));
$tpl->assign('skill', $skill);

$contentTemplate = $tpl->get_template('skill/badge_create.tpl');

$tpl->display($contentTemplate);
