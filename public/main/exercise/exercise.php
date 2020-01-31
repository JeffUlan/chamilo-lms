<?php

/* For licensing terms, see /license.txt */

use Chamilo\CoreBundle\Framework\Container;
use Chamilo\CourseBundle\Entity\CExerciseCategory;
use Chamilo\CourseBundle\Entity\CQuiz;
use Chamilo\CourseBundle\Entity\CShortcut;

/**
 * Exercise list: This script shows the list of exercises for administrators and students.
 *
 * @author Olivier Brouckaert, original author
 * @author Wolfgang Schneider, code/html cleanup
 * @author Julio Montoya <gugli100@gmail.com>, lots of cleanup + several improvements
 * Modified by hubert.borderiou (question category)
 */
require_once __DIR__.'/../inc/global.inc.php';
$current_course_tool = TOOL_QUIZ;

// Setting the tabs
$this_section = SECTION_COURSES;

api_protect_course_script(true);

$limitTeacherAccess = api_get_configuration_value('limit_exercise_teacher_access');

$check = Security::get_existing_token('get');

$currentUrl = api_get_self().'?'.api_get_cidreq();

$is_allowedToEdit = api_is_allowed_to_edit(null, true);
$is_tutor = api_is_allowed_to_edit(true);
$is_tutor_course = api_is_course_tutor();
$courseInfo = api_get_course_info();
$courseId = $courseInfo['real_id'];
$userInfo = api_get_user_info();
$userId = $userInfo['id'];
$sessionId = api_get_session_id();
$isDrhOfCourse = CourseManager::isUserSubscribedInCourseAsDrh(
    $userId,
    $courseInfo
);

$TBL_DOCUMENT = Database::get_course_table(TABLE_DOCUMENT);
$TBL_EXERCISE_QUESTION = Database::get_course_table(TABLE_QUIZ_TEST_QUESTION);
$TBL_EXERCISES = Database::get_course_table(TABLE_QUIZ_TEST);
$TBL_TRACK_EXERCISES = Database::get_main_table(TABLE_STATISTIC_TRACK_E_EXERCISES);

// Clear the exercise session
Exercise::cleanSessionVariables();

//General POST/GET/SESSION/COOKIES parameters recovery
$origin = api_get_origin();
$exerciseId = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : null;
$file = isset($_REQUEST['file']) ? Database::escape_string($_REQUEST['file']) : null;
$learnpath_id = isset($_REQUEST['learnpath_id']) ? (int) $_REQUEST['learnpath_id'] : null;
$learnpath_item_id = isset($_REQUEST['learnpath_item_id']) ? (int) $_REQUEST['learnpath_item_id'] : null;
$categoryId = isset($_REQUEST['category_id']) ? (int) $_REQUEST['category_id'] : 0;
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$keyword = isset($_REQUEST['keyword']) ? Security::remove_XSS($_REQUEST['keyword']) : '';

$exerciseRepo = Container::getExerciseRepository();
$exerciseEntity = null;
if (!empty($exerciseId)) {
    /** @var CQuiz $exerciseEntity */
    $exerciseEntity = $exerciseRepo->find($exerciseId);
}

if (api_is_in_gradebook()) {
    $interbreadcrumb[] = [
        'url' => Category::getUrl(),
        'name' => get_lang('Assessments'),
    ];
}

$nameTools = get_lang('Tests');

// Simple actions
if ($is_allowedToEdit && !empty($action)) {
    $objExerciseTmp = new Exercise();
    $exercise_action_locked = api_resource_is_locked_by_gradebook(
        $exerciseId,
        LINK_EXERCISE
    );
    $result = $objExerciseTmp->read($exerciseId);

    if (empty($result)) {
        api_not_allowed();
    }

    switch ($action) {
        case 'add_shortcut':
            $repo = Container::getShortcutRepository();
            $shortcut = new CShortcut();
            $shortcut->setName($objExerciseTmp->get_formated_title());
            $shortcut->setShortCutNode($exerciseEntity->getResourceNode());

            $courseEntity = api_get_course_entity(api_get_course_int_id());
            $repo->addResourceNode($shortcut, api_get_user_entity(api_get_user_id()), $courseEntity);
            $repo->getEntityManager()->flush();

            Display::addFlash(Display::return_message(get_lang('Updated')));

            break;
        case 'remove_shortcut':
            $repo = Container::getShortcutRepository();
            $shortcut = $repo->getShortcutFromResource($exerciseEntity);
            if (null !== $shortcut) {
                $repo->getEntityManager()->remove($shortcut);
                $repo->getEntityManager()->flush();
            }

            Display::addFlash(Display::return_message(get_lang('Deleted')));

            break;
        case 'enable_launch':
            $objExerciseTmp->cleanCourseLaunchSettings();
            $objExerciseTmp->enableAutoLaunch();
            Display::addFlash(Display::return_message(get_lang('Updated')));

            break;
        case 'disable_launch':
            $objExerciseTmp->cleanCourseLaunchSettings();

            break;
        case 'delete':
            $result = $objExerciseTmp->delete();
            if ($result) {
                Display::addFlash(Display::return_message(get_lang('Deleted'), 'confirmation'));
            }

            break;
        case 'enable':
            if ($limitTeacherAccess && !api_is_platform_admin()) {
                // Teacher change exercise
                break;
            }
            $exerciseRepo->setVisibilityPublished($exerciseEntity);
            Display::addFlash(Display::return_message(get_lang('The visibility has been changed.'), 'confirmation'));

            break;
        case 'disable':
            if ($limitTeacherAccess && !api_is_platform_admin()) {
                // Teacher change exercise
                break;
            }

            $exerciseRepo->setVisibilityDraft($exerciseEntity);
            Display::addFlash(Display::return_message(get_lang('The visibility has been changed.'), 'confirmation'));

            break;
        case 'disable_results':
            //disable the results for the learners
            $objExerciseTmp->disable_results();
            $objExerciseTmp->save();
            Display::addFlash(Display::return_message(get_lang('Results disabled for learners'), 'confirmation'));

            break;
        case 'enable_results':
            //disable the results for the learners
            $objExerciseTmp->enable_results();
            $objExerciseTmp->save();
            Display::addFlash(Display::return_message(get_lang('Results enabled for learners'), 'confirmation'));

            break;
        case 'clean_results':
            if ($limitTeacherAccess && !api_is_platform_admin()) {
                // Teacher change exercise
                break;
            }

            // Clean student results
            if (false == $exercise_action_locked) {
                $quantity_results_deleted = $objExerciseTmp->cleanResults(true);
                $title = $objExerciseTmp->selectTitle();

                Display::addFlash(
                    Display::return_message(
                        $title.': '.sprintf(
                            get_lang('%d results cleaned'),
                            $quantity_results_deleted
                        ),
                        'confirmation'
                    )
                );
            }

            break;
        case 'copy_exercise': //copy an exercise
            api_set_more_memory_and_time_limits();
            $objExerciseTmp->copyExercise();
            Display::addFlash(Display::return_message(
                get_lang('Test copied'),
                'confirmation'
            ));

            break;
        case 'clean_all_test':
            if ($check) {
                if ($limitTeacherAccess && !api_is_platform_admin()) {
                    api_not_allowed(true);
                }

                // list of exercises in a course/session
                // we got variable $courseId $courseInfo session api_get_session_id()
                $exerciseList = ExerciseLib::get_all_exercises_for_course_id(
                    $courseInfo,
                    $sessionId,
                    $courseId,
                    false
                );

                $quantity_results_deleted = 0;
                foreach ($exerciseList as $exeItem) {
                    // delete result for test, if not in a gradebook
                    $exercise_action_locked = api_resource_is_locked_by_gradebook($exeItem['id'], LINK_EXERCISE);
                    if (false == $exercise_action_locked) {
                        $objExerciseTmp = new Exercise();
                        if ($objExerciseTmp->read($exeItem['id'])) {
                            $quantity_results_deleted += $objExerciseTmp->cleanResults(true);
                        }
                    }
                }

                Display::addFlash(Display::return_message(
                    sprintf(
                        get_lang('%d results cleaned'),
                        $quantity_results_deleted
                    ),
                    'confirm'
                ));

                header('Location: '.$currentUrl);
                exit;
            }

            break;
        case 'exportqti2':
            if ($limitTeacherAccess && !api_is_platform_admin()) {
                api_not_allowed(true);
            }
            require_once api_get_path(SYS_CODE_PATH).'exercise/export/qti2/qti2_export.php';
            $export = export_exercise_to_qti($exerciseId, true);

            $xmlReader = new XMLReader();
            $xmlReader->xml($export);
            $xmlReader->setParserProperty(XMLReader::VALIDATE, true);
            $isValid = $xmlReader->isValid();

            if ($isValid) {
                $name = 'qti2_export_'.$exerciseId.'.zip';
                $zip = api_create_zip($name);
                $zip->addFile('qti2export_'.$exerciseId.'.xml', $export);
                $zip->finish();
                exit;
            } else {
                Display::addFlash(Display::return_message(get_lang('There was an error writing the XML file. Please ask the administrator to check the error logs.'), 'error'));
                header('Location: '.$currentUrl);
                exit;
            }

            break;
        case 'up_category':
        case 'down_category':
            $categoryIdFromGet = isset($_REQUEST['category_id_edit']) ? $_REQUEST['category_id_edit'] : 0;
            $em = Database::getManager();
            $repo = $em->getRepository('ChamiloCourseBundle:CExerciseCategory');
            $category = $repo->find($categoryIdFromGet);
            $currentPosition = $category->getPosition();

            if ('up_category' === $action) {
                $currentPosition--;
            } else {
                $currentPosition++;
            }
            $category->setPosition($currentPosition);
            $em->persist($category);
            $em->flush();
            Display::addFlash(Display::return_message(get_lang('Update successful')));

            header('Location: '.$currentUrl);
            exit;

            break;
    }

    // destruction of Exercise
    unset($objExerciseTmp);
    Security::clear_token();
    header('Location: '.$currentUrl);
    exit;
}

Event::event_access_tool(TOOL_QUIZ);

$logInfo = [
    'tool' => TOOL_QUIZ,
    'tool_id' => (int) $exerciseId,
    'action' => isset($_REQUEST['learnpath_id']) ? 'learnpath_id' : '',
    'action_details' => isset($_REQUEST['learnpath_id']) ? (int) $_REQUEST['learnpath_id'] : '',
];
Event::registerLog($logInfo);

if ('learnpath' !== $origin) {
    //so we are not in learnpath tool
    Display::display_header($nameTools, get_lang('Test'));
    if (isset($_GET['message']) && in_array($_GET['message'], ['ExerciseEdited'])) {
        echo Display::return_message(get_lang('TestEdited'), 'confirmation');
    }
} else {
    Display::display_reduced_header();
}
Display::display_introduction_section(TOOL_QUIZ);

// Selects $limit exercises at the same time
// maximum number of exercises on a same page
$limit = Exercise::PAGINATION_ITEMS_PER_PAGE;

$token = Security::get_token();
if ($is_allowedToEdit && 'learnpath' !== $origin) {
    $actionsLeft = '<a href="'.api_get_path(WEB_CODE_PATH).'exercise/exercise_admin.php?'.api_get_cidreq().'">'.
        Display::return_icon('new_exercice.png', get_lang('Create a new test'), '', ICON_SIZE_MEDIUM).'</a>';
    $actionsLeft .= '<a href="'.api_get_path(WEB_CODE_PATH).'exercise/question_create.php?'.api_get_cidreq().'">'.
        Display::return_icon('new_question.png', get_lang('Add a question'), '', ICON_SIZE_MEDIUM).'</a>';

    if (api_get_configuration_value('allow_exercise_categories')) {
        $actionsLeft .= '<a href="'.api_get_path(WEB_CODE_PATH).'exercise/category.php?'.api_get_cidreq().'">';
        $actionsLeft .= Display::return_icon('folder.png', get_lang('Category'), '', ICON_SIZE_MEDIUM);
        $actionsLeft .= '</a>';
    }

    // Question category
    $actionsLeft .= '<a href="'.api_get_path(WEB_CODE_PATH).'exercise/tests_category.php?'.api_get_cidreq().'">';
    $actionsLeft .= Display::return_icon('green_open.png', get_lang('Questions category'), '', ICON_SIZE_MEDIUM);
    $actionsLeft .= '</a>';
    $actionsLeft .= '<a href="'.api_get_path(WEB_CODE_PATH).'exercise/question_pool.php?'.api_get_cidreq().'">';
    $actionsLeft .= Display::return_icon('database.png', get_lang('Recycle existing questions'), '', ICON_SIZE_MEDIUM);
    $actionsLeft .= '</a>';

    //echo Display::url(Display::return_icon('looknfeel.png', get_lang('Media')), 'media.php?' . api_get_cidreq());
    // end question category
    /*$actionsLeft .= '<a href="'.api_get_path(WEB_CODE_PATH).'exercise/hotpotatoes.php?'.api_get_cidreq().'">'.
        Display::return_icon('import_hotpotatoes.png', get_lang('Import Hotpotatoes'), '', ICON_SIZE_MEDIUM).'</a>';*/
    // link to import qti2 ...
    $actionsLeft .= '<a href="'.api_get_path(WEB_CODE_PATH).'exercise/qti2.php?'.api_get_cidreq().'">'.
        Display::return_icon('import_qti2.png', get_lang('Import exercises Qti2'), '', ICON_SIZE_MEDIUM).'</a>';
    $actionsLeft .= '<a href="'.api_get_path(WEB_CODE_PATH).'exercise/aiken.php?'.api_get_cidreq().'">'.
        Display::return_icon('import_aiken.png', get_lang('Import Aiken quiz'), '', ICON_SIZE_MEDIUM).'</a>';
    $actionsLeft .= '<a href="'.api_get_path(WEB_CODE_PATH).'exercise/upload_exercise.php?'.api_get_cidreq().'">'.
        Display::return_icon('import_excel.png', get_lang('Import quiz from Excel'), '', ICON_SIZE_MEDIUM).'</a>';

    $cleanAll = Display::url(
        Display::return_icon(
            'clean_all.png',
            get_lang('Are you sure to delete all test\'s results ?'),
            '',
            ICON_SIZE_MEDIUM
        ),
        '#',
        [
            'data-item-question' => addslashes(get_lang('Clear all learners results for every exercises ?')),
            'data-href' => api_get_path(WEB_CODE_PATH).'exercise/exercise.php?'.api_get_cidreq().'&action=clean_all_test&sec_token='.$token,
            'data-toggle' => 'modal',
            'data-target' => '#confirm-delete',
        ]
    );

    if ($limitTeacherAccess) {
        if (api_is_platform_admin()) {
            $actionsLeft .= $cleanAll;
        }
    } else {
        $actionsLeft .= $cleanAll;
    }
    $actionsRight = '';
}

if ($is_allowedToEdit) {
    echo Display::toolbarAction(
        'toolbarUser',
        [$actionsLeft, '', $actionsRight],
        [6, 1, 5]
    );
}

if (false === api_get_configuration_value('allow_exercise_categories')) {
    echo Exercise::exerciseGridResource(0, $keyword);
} else {
    if (empty($categoryId)) {
        echo Display::page_subheader(get_lang('General'));
        echo Exercise::exerciseGridResource(0, $keyword);
        $counter = 0;
        $manager = new ExerciseCategoryManager();
        $categories = $manager->getCategories($courseId);
        $modifyUrl = api_get_self().'?'.api_get_cidreq();
        $total = count($categories);
        $upIcon = Display::return_icon('up.png', get_lang('Move up'));
        $downIcon = Display::return_icon('down.png', get_lang('Move down'));
        /** @var CExerciseCategory $category */
        foreach ($categories as $category) {
            $categoryIdItem = $category->getId();
            $up = '';
            $down = '';
            if ($is_allowedToEdit) {
                $up = Display::url($upIcon, $modifyUrl.'&action=up_category&category_id_edit='.$categoryIdItem);
                if (0 === $counter) {
                    $up = Display::url(Display::return_icon('up_na.png'), '#');
                }
                $down = Display::url($downIcon, $modifyUrl.'&action=down_category&category_id_edit='.$categoryIdItem);
                $counter++;
                if ($total === $counter) {
                    $down = Display::url(Display::return_icon('down_na.png'), '#');
                }
            }

            echo Display::page_subheader($category->getName().$up.$down);
            echo Exercise::exerciseGridResource($category->getId(), $keyword);
        }
    } else {
        $manager = new ExerciseCategoryManager();
        $category = $manager->get($categoryId);
        echo Display::page_subheader($category['name']);
        echo Exercise::exerciseGridResource($category['id'], $keyword);
    }
}

if ('learnpath' !== $origin) {
    // We are not in learnpath tool
    Display::display_footer();
}
