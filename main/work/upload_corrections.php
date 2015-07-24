<?php
/* For licensing terms, see /license.txt */

use ChamiloSession as Session;
use Symfony\Component\Finder\Finder;

require_once '../inc/global.inc.php';
$current_course_tool  = TOOL_STUDENTPUBLICATION;

api_protect_course_script(true);

// Including necessary files
require_once 'work.lib.php';

$this_section = SECTION_COURSES;

$workId = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : null;

$is_allowed_to_edit = api_is_allowed_to_edit();
$course_id = api_get_course_int_id();
$user_id = api_get_user_id();
$userInfo = api_get_user_info();
$session_id = api_get_session_id();
$courseInfo = api_get_course_info();
$course_code = $courseInfo['code'];
$group_id = api_get_group_id();

if (empty($workId)) {
    api_not_allowed(true);
}

protectWork($courseInfo, $workId);

$workInfo = get_work_data_by_id($workId);

$student_can_edit_in_session = api_is_allowed_to_session_edit(false, true);

$homework = get_work_assignment_by_id($workInfo['id']);
$validationStatus = getWorkDateValidationStatus($homework);

$interbreadcrumb[] = array('url' => api_get_path(WEB_CODE_PATH).'work/work.php?'.api_get_cidreq(), 'name' => get_lang('StudentPublications'));
$interbreadcrumb[] = array('url' => api_get_path(WEB_CODE_PATH).'work/work_list.php?'.api_get_cidreq().'&id='.$workId, 'name' =>  $workInfo['title']);
$interbreadcrumb[] = array('url' => '#', 'name'  => get_lang('UploadCorrections'));

$form = new FormValidator(
    'form',
    'POST',
    api_get_self()."?".api_get_cidreq()."&id=".$workId,
    '',
    array('enctype' => "multipart/form-data")
);
$form->addElement('header', get_lang('UploadCorrections'));
$form->addElement('file', 'file', get_lang('UploadADocument'));
$form->add_real_progress_bar('uploadWork', 'file');
$form->addRule('file', get_lang('ThisFieldIsRequired'), 'required');
$form->addElement('hidden', 'id', $workId);
$form->addButtonUpload(get_lang('Upload'));

$error_message = null;

$succeed = false;
if ($form->validate()) {

    $values = $form->getSubmitValues();

    $upload = process_uploaded_file($_FILES['file'], false);

    if ($upload) {

        $zip = new PclZip($_FILES['file']['tmp_name']);

        // Check the zip content (real size and file extension)
        $zipFileList = (array)$zip->listContent();

        $realSize = 0;
        foreach ($zipFileList as & $this_content) {
            $realSize += $this_content['size'];
        }

        $maxSpace = DocumentManager::get_course_quota();

        if (!DocumentManager::enough_space($realSize, $maxSpace)) {
            Display::addFlash(
                Display::return_message(
                    get_lang('UplNotEnoughSpace'),
                    'warning'
                )
            );
        }

        $folder = api_get_unique_id();
        $destinationDir = api_get_path(SYS_ARCHIVE_PATH).$folder;
        mkdir($destinationDir, api_get_permissions_for_new_directories(), true);

        /*	Uncompress zip file*/
        // We extract using a callback function that "cleans" the path
        $zip->extract(
            PCLZIP_OPT_PATH,
            $destinationDir,
            PCLZIP_CB_PRE_EXTRACT,
            'clean_up_files_in_zip',
            PCLZIP_OPT_REPLACE_NEWER
        );

        $result = get_work_user_list(null, null, null, null, $workId);

        if (empty($result)) {
            Display::addFlash(
                Display::return_message(
                    get_lang('NoDataAvailable'),
                    'warning'
                )
            );
        }

        $finalResult = [];
        foreach ($result as $item) {
            $title = $item['title_clean'];
            $title = api_replace_dangerous_char($title);
            $finalResult[$title] = $item['id'];
        }

        $coursePath = api_get_path(SYS_COURSE_PATH).$courseInfo['path'].'/';
        $workDir = api_get_path(SYS_COURSE_PATH).$courseInfo['path'].'/work/';
        $workDir .= basename($workInfo['url']).'/';

        $finder = new Finder();
        $finder->files()->in($destinationDir);
        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $fileName = $file->getBasename();
            $fileName = substr($fileName, 20, strlen($fileName));
            $pos = strpos($fileName, '-') + 1;
            $fileName = substr($fileName, $pos, strlen($fileName));
            if (isset($finalResult[$fileName])) {
                $workStudentId = $finalResult[$fileName];
                $workStudent = get_work_data_by_id($workStudentId);
                if ($workStudent) {
                    if (!empty($workStudent['url_correction'])) {
                        $correctionFilePath = $coursePath.$workStudent['url_correction'];
                        $correctionTitle = $workStudent['title_correction'];
                    } else {
                        if (!empty($workStudent['url'])) {
                            $correctionFilePath = $coursePath.$workStudent['url'].'_correction';
                            $correctionTitle = $fileName;
                        } else {
                            //$correctionFilePath = $workDir.api_get_unique_id().'_correction';
                        }
                    }

                    $table = Database:: get_course_table(TABLE_STUDENT_PUBLICATION);

                    if (!empty($correctionFilePath)) {

                        $result = copy(
                            $file->getRealPath(),
                            $correctionFilePath
                        );

                        $correctionTitle = Database::escape_string(
                            $correctionTitle
                        );

                        $correctionFilePath = Database::escape_string(
                            'work/'.basename($workInfo['url']).'/'.basename($correctionFilePath)
                        );

                        if ($result) {
                            $sql = "UPDATE $table SET
                                        url_correction = '".$correctionFilePath."',
                                        title_correction = '".$correctionTitle."'
                                    WHERE iid = $workStudentId";
                            Database::query($sql);
                        }
                    }
                }
            }
        }

        Display::addFlash(
            Display::return_message(
                get_lang('Uploaded')
            )
        );
    }

    header('Location: '.api_get_path(WEB_CODE_PATH).'work/work_list_all.php?'.api_get_cidreq().'&id='.$workId);
    exit;
}

$htmlHeadXtra[] = to_javascript_work();
Display :: display_header(null);

if (!empty($workId)) {
    echo $validationStatus['message'];
    if ($is_allowed_to_edit) {
        $form->display();
    } else {
        Display::display_error_message(get_lang('ActionNotAllowed'));
    }
} else {
    Display::display_error_message(get_lang('ActionNotAllowed'));
}

Display :: display_footer();
