<?php
/* For licensing terms, see /license.txt */

use ChamiloSession as Session;

/**
 * This file allows creating new svg and png documents with an online editor.
 *
 * @package chamilo.document
 *
 * @author Juan Carlos Raña Trabado
 * @since 30/january/2011
*/
require_once __DIR__.'/../inc/global.inc.php';

api_protect_course_script();
api_block_anonymous_users();

if ($_user['user_id'] != api_get_user_id() || api_get_user_id() == 0 || $_user['user_id'] == 0) {
    api_not_allowed();
    die();
}

if (!isset($_GET['title']) || !isset($_GET['type']) || !isset($_GET['image'])) {
    api_not_allowed();
    die();
}

$paintDir = Session::read('paint_dir');
if (empty($paintDir)) {
    api_not_allowed();
    die();
}

//pixlr return

$filename = Security::remove_XSS($_GET['title']); //The user preferred file name of the image.
$extension = Security::remove_XSS($_GET['type']); //The image type, "pdx", "jpg", "bmp" or "png".
$urlcontents = Security::remove_XSS($_GET['image']); //A URL to the image on Pixlr.com server or the raw file post of the saved image.

//make variables

$title = Database::escape_string(str_replace('_', ' ', $filename));
$current_session_id = api_get_session_id();
$groupId = api_get_group_id();
$groupInfo = GroupManager::get_group_properties($groupId);
$relativeUrlPath = Session::read('paint_dir');
$dirBaseDocuments = api_get_path(SYS_COURSE_PATH).$_course['path'].'/document';
$saveDir = $dirBaseDocuments.$paintDir;
$contents = file_get_contents($urlcontents);

//Security. Verify that the URL is pointing to a file @ pixlr.com domain or an ip @ pixlr.com. Comment because sometimes return a ip number
/*
if (strpos($urlcontents, "pixlr.com") === 0){
    echo "Invalid referrer";
    exit;
}
*/

//Security. Allway get from pixlr.com. Comment because for now this does not run
/*
$urlcontents1='http://pixlr.com/';
$urlcontents2 = strstr($urlcontents, '_temp');
$urlcontents_to_save=$urlcontents1.$urlcontents2;
$contents = file_get_contents($urlcontents_to_save);//replace line 45.
*/

//a bit title security
$filename = addslashes(trim($filename));
$filename = Security::remove_XSS($filename);
$filename = api_replace_dangerous_char($filename);
$filename = disable_dangerous_file($filename);

if (strlen(trim($filename)) == 0) {
    echo "The title is empty"; //if title is empty, headers Content-Type = application/octet-stream, then not create a new title here please
    exit;
}

//check file_get_contents
if ($contents === false) {
    echo "I cannot read: ".$urlcontents;
    exit;
}

// Extension security
if ($extension != 'jpg' && $extension != 'png' && $extension != 'pxd') {
    die();
}
if ($extension == 'pxd') {
    echo "pxd file type does not supported"; // not secure because check security headers and finfo() return  Content-Type = application/octet-stream
    exit;
}

//Verify that the file is an image. Headers method
$headers = get_headers($urlcontents, 1);
$content_type = explode("/", $headers['Content-Type']);
if ($content_type[0] != "image") {
    echo "Invalid file type";
    exit;
}

//Verify that the file is an image. Fileinfo method
$finfo = new finfo(FILEINFO_MIME);
$current_mime = $finfo->buffer($contents);

if (strpos($current_mime, 'image') === false) {
    echo "Invalid mime type file";
    exit;
}


//path, file and title
$paintFileName = $filename.'.'.$extension;
$title = $title.'.'.$extension;

if ($currentTool == 'document/createpaint') {
    //check save as and prevent rewrite an older file with same name
    if (0 != $groupId) {
        $group_properties = GroupManager :: get_group_properties($groupId);
        $groupPath = $group_properties['directory'];
    } else {
        $groupPath = '';
    }

    if (file_exists($saveDir.'/'.$filename.'.'.$extension)) {
        $i = 1;
        while (file_exists($saveDir.'/'.$filename.'_'.$i.'.'.$extension)) {
            $i++;
        }
        $paintFileName = $filename.'_'.$i.'.'.$extension;
        $title = $filename.'_'.$i.'.'.$extension;
    }

    //
    $documentPath = $saveDir.'/'.$paintFileName;
    //add new document to disk
    file_put_contents($documentPath, $contents);
    //add document to database
    $doc_id = add_document($_course, $relativeUrlPath.'/'.$paintFileName, 'file', filesize($documentPath), $title);
    api_item_property_update(
        $_course,
        TOOL_DOCUMENT,
        $doc_id,
        'DocumentAdded',
        $_user['user_id'],
        $groupInfo,
        null,
        null,
        null,
        $current_session_id
    );
} elseif ($currentTool == 'document/editpaint') {
    $documentPath = $saveDir.'/'.$paintFileName;
    //add new document to disk
    file_put_contents($documentPath, $contents);

    $paintFile = Session::read('paint_file');

    //check path
    if (empty($paintFile)) {
        api_not_allowed();
        die();
    }
    if ($paintFile == $paintFileName) {
        $document_id = DocumentManager::get_document_id($_course, $relativeUrlPath.'/'.$paintFileName);
        update_existing_document($_course, $document_id, filesize($documentPath), null);
        api_item_property_update(
            $_course,
            TOOL_DOCUMENT,
            $document_id,
            'DocumentUpdated',
            $_user['user_id'],
            $groupInfo,
            null,
            null,
            null,
            $current_session_id
        );
    } else {
        //add a new document
        $doc_id = add_document(
            $_course,
            $relativeUrlPath.'/'.$paintFileName,
            'file',
            filesize($documentPath),
            $title
        );
        api_item_property_update(
            $_course,
            TOOL_DOCUMENT,
            $doc_id,
            'DocumentAdded',
            $_user['user_id'],
            $groupInfo,
            null,
            null,
            null,
            $current_session_id
        );
    }
}


//delete temporal file
$temp_file_2delete = Session::read('temp_realpath_image');
unlink($temp_file_2delete);

//Clean sessions and return to Chamilo file list
Session::erase('paint_dir');
Session::erase('paint_file');
Session::erase('temp_realpath_image');

$exit = Session::read('exit_pixlr');
if (empty($exit)) {
    $location = api_get_path(WEB_CODE_PATH).'document/document.php?'.api_get_cidreq();
    echo '<script>window.parent.location.href="'.$location.'"</script>';
    api_not_allowed(true);
} else {
    echo '<div align="center" style="padding-top:150; font-family:Arial, Helvetica, Sans-serif;font-size:25px;color:#aaa;font-weight:bold;">'.get_lang('PleaseStandBy').'</div>';
    $location = api_get_path(WEB_CODE_PATH).'document/document.php?id='.Security::remove_XSS($exit).'&'.api_get_cidreq();
    echo '<script>window.parent.location.href="'.$location.'"</script>';
    Session::erase('exit_pixlr');
}
