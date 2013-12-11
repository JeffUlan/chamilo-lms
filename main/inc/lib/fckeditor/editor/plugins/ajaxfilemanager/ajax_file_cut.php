<?php
/* For licensing terms, see /license.txt */
/**
 * cut file
 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
 * @link www.phpletter.com
 * @since 22/May/2007
 *
 */

require_once '../../../../../../inc/global.inc.php';
require_once api_get_path(LIBRARY_PATH).'fckeditor/editor/plugins/ajaxfilemanager/inc/config.php';

$error = "";
$info  = '';
if (CONFIG_SYS_VIEW_ONLY || !CONFIG_OPTIONS_CUT) {
    $error = SYS_DISABLED;
} elseif (!isset($_POST['selectedDoc']) || !is_array($_POST['selectedDoc']) || sizeof($_POST['selectedDoc']) < 1) {
    $error = ERR_NOT_DOC_SELECTED_FOR_CUT;
} elseif (empty($_POST['currentFolderPath']) || !isUnderRoot($_POST['currentFolderPath'])) {
    $error = ERR_FOLDER_PATH_NOT_ALLOWED;
} else {
    require_once(CLASS_SESSION_ACTION);
    $sessionAction = new SessionAction();
    $sessionAction->setAction($_POST['action_value']);
    $sessionAction->setFolder($_POST['currentFolderPath']);
    $sessionAction->set($_POST['selectedDoc']);
    $info = ',num:'.sizeof($_POST['selectedDoc']);
}
echo "{error:'".$error."'\n".$info."}";
