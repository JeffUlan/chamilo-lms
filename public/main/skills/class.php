<?php
/* For licensing terms, see /license.txt */

/**
 * Show information about the OpenBadge class.
 *
 * @author Angel Fernando Quiroz Campos <angel.quiroz@beeznest.com>
 */
require_once __DIR__.'/../inc/global.inc.php';

$skillId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$objSkill = new SkillModel();
$skill = $objSkill->get($skillId);
$json = [];

if ($skill) {
    $json = [
        'name' => $skill['name'],
        'description' => $skill['description'],
        'image' => api_get_path(WEB_UPLOAD_PATH)."badges/{$skill['icon']}",
        'criteria' => api_get_path(WEB_CODE_PATH)."skills/criteria.php?id=$skillId",
        'issuer' => api_get_path(WEB_CODE_PATH).'skills/issuer.php',
    ];
}

header('Content-Type: application/json');

echo json_encode($json);
