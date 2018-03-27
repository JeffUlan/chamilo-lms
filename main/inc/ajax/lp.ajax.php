<?php
/* For licensing terms, see /license.txt */

use ChamiloSession as Session;

/**
 * Responses to AJAX calls.
 */
require_once __DIR__.'/../global.inc.php';
api_protect_course_script(true);

$debug = false;
$action = isset($_REQUEST['a']) ? $_REQUEST['a'] : '';

$course_id = api_get_course_int_id();
$sessionId = api_get_session_id();

if ($debug) {
    error_log('----------lp.ajax-------------- action '.$action);
}

switch ($action) {
    case 'get_documents':
        $courseInfo = api_get_course_info();
        $folderId = isset($_GET['folder_id']) ? $_GET['folder_id'] : null;
        if (empty($folderId)) {
            exit;
        }
        $lpId = isset($_GET['lp_id']) ? $_GET['lp_id'] : null;
        $url = isset($_GET['url']) ? $_GET['url'] : null;

        echo DocumentManager::get_document_preview(
            $courseInfo,
            $lpId,
            null,
            api_get_session_id(),
            true,
            null,
            $url,
            true,
            false,
            $folderId
        );

        break;
    case 'add_lp_item':
        if (api_is_allowed_to_edit(null, true)) {
            /** @var learnpath $learningPath */
            $learningPath = Session::read('oLP');
            if ($learningPath) {
                // Updating the lp.modified_on
                $learningPath->set_modified_on();
                $title = $_REQUEST['title'];
                if ($_REQUEST['type'] == TOOL_QUIZ) {
                    $title = Exercise::format_title_variable($title);
                }

                $parentId = isset($_REQUEST['parent_id']) ? $_REQUEST['parent_id'] : '';
                $previousId = isset($_REQUEST['previous_id']) ? $_REQUEST['previous_id'] : '';

                echo $learningPath->add_item(
                    $parentId,
                    $previousId,
                    $_REQUEST['type'],
                    $_REQUEST['id'],
                    $title,
                    null
                );
            }
        }
        break;
    case 'update_lp_item_order':
        if (api_is_allowed_to_edit(null, true)) {
            $new_order = $_POST['new_order'];
            $sections = explode('^', $new_order);
            $new_array = [];

            // We have to update parent_item_id, previous_item_id, next_item_id, display_order in the database
            $LP_item_list = new LP_item_order_list();
            foreach ($sections as $items) {
                if (!empty($items)) {
                    list($id, $parent_id) = explode('|', $items);
                    $item = new LP_item_order_item($id, $parent_id);
                    $LP_item_list->add($item);
                }
            }

            $tab_parents_id = $LP_item_list->get_list_of_parents();

            foreach ($tab_parents_id as $parent_id) {
                $Same_parent_LP_item_list = $LP_item_list->get_item_with_same_parent($parent_id);
                $previous_item_id = 0;
                for ($i = 0; $i < count($Same_parent_LP_item_list->list); $i++) {
                    $item_id = $Same_parent_LP_item_list->list[$i]->id;
                    // display_order
                    $display_order = $i + 1;
                    $LP_item_list->set_parameters_for_id($item_id, $display_order, "display_order");
                    // previous_item_id
                    $LP_item_list->set_parameters_for_id($item_id, $previous_item_id, "previous_item_id");
                    $previous_item_id = $item_id;
                    // next_item_id
                    $next_item_id = 0;
                    if ($i < count($Same_parent_LP_item_list->list) - 1) {
                        $next_item_id = $Same_parent_LP_item_list->list[$i + 1]->id;
                    }
                    $LP_item_list->set_parameters_for_id($item_id, $next_item_id, "next_item_id");
                }
            }

            $table = Database::get_course_table(TABLE_LP_ITEM);

            foreach ($LP_item_list->list as $LP_item) {
                $params = [];
                $params['display_order'] = $LP_item->display_order;
                $params['previous_item_id'] = $LP_item->previous_item_id;
                $params['next_item_id'] = $LP_item->next_item_id;
                $params['parent_item_id'] = $LP_item->parent_item_id;

                Database::update(
                    $table,
                    $params,
                    [
                        'id = ? AND c_id = ? ' => [
                            intval($LP_item->id),
                            $course_id,
                        ],
                    ]
                );
            }
            echo Display::return_message(get_lang('Saved'), 'confirm');
        }
        break;
    case 'record_audio':
        if (api_is_allowed_to_edit(null, true) == false) {
            exit;
        }
        /** @var Learnpath $lp */
        $lp = Session::read('oLP');
        $course_info = api_get_course_info();

        $lpPathInfo = $lp->generate_lp_folder($course_info);

        if (empty($lpPathInfo)) {
            exit;
        }

        foreach (['video', 'audio'] as $type) {
            if (isset($_FILES["${type}-blob"])) {
                $fileName = $_POST["${type}-filename"];
                //$file = $_FILES["${type}-blob"]["tmp_name"];
                $file = $_FILES["${type}-blob"];

                $fileInfo = pathinfo($fileName);

                $file['name'] = 'rec_'.date('Y-m-d_His').'_'.uniqid().'.'.$fileInfo['extension'];
                $file['file'] = $file;

                $result = DocumentManager::upload_document(
                    $file,
                    '/audio',
                    $file['name'],
                    null,
                    0,
                    'overwrite',
                    false,
                    false
                );

                if (!empty($result) && is_array($result)) {
                    $newDocId = $result['id'];
                    $courseId = $result['c_id'];

                    $lp->set_modified_on();

                    $lpItem = new learnpathItem($_REQUEST['lp_item_id']);
                    $lpItem->add_audio_from_documents($newDocId);
                    $data = DocumentManager::get_document_data_by_id($newDocId, $course_info['code']);
                    echo $data['document_url'];
                    exit;
                }
            }
        }

        break;
    case 'get_forum_thread':
        $lpId = isset($_GET['lp']) ? intval($_GET['lp']) : 0;
        $lpItemId = isset($_GET['lp_item']) ? intval($_GET['lp_item']) : 0;
        $sessionId = api_get_session_id();

        if (empty($lpId) || empty($lpItemId)) {
            echo json_encode([
                'error' => true,
            ]);

            break;
        }

        $learningPath = learnpath::getLpFromSession(
            api_get_course_id(),
            $lpId,
            api_get_user_id()
        );
        $lpItem = $learningPath->getItem($lpItemId);

        if (empty($lpItem)) {
            echo json_encode([
                'error' => true,
            ]);
            break;
        }

        $lpHasForum = $learningPath->lpHasForum();

        if (!$lpHasForum) {
            echo json_encode([
                'error' => true,
            ]);
            break;
        }

        $forum = $learningPath->getForum($sessionId);

        if (empty($forum)) {
            require_once '../../forum/forumfunction.inc.php';
            $forumCategory = getForumCategoryByTitle(
                get_lang('LearningPaths'),
                $course_id,
                $sessionId
            );

            if (empty($forumCategory)) {
                $forumCategoryId = store_forumcategory(
                    [
                        'lp_id' => 0,
                        'forum_category_title' => get_lang('LearningPaths'),
                        'forum_category_comment' => null,
                    ],
                    [],
                    false
                );
            } else {
                $forumCategoryId = $forumCategory['cat_id'];
            }

            $forumId = $learningPath->createForum($forumCategoryId);
        } else {
            $forumId = $forum['forum_id'];
        }

        $lpItemHasThread = $lpItem->lpItemHasThread($course_id);

        if (!$lpItemHasThread) {
            echo json_encode([
                'error' => true,
            ]);
            break;
        }

        $forumThread = $lpItem->getForumThread($course_id, $sessionId);
        if (empty($forumThread)) {
            $lpItem->createForumThread($forumId);
            $forumThread = $lpItem->getForumThread($course_id, $sessionId);
        }

        $forumThreadId = $forumThread['thread_id'];

        echo json_encode([
            'error' => false,
            'forumId' => intval($forum['forum_id']),
            'threadId' => intval($forumThreadId),
        ]);
        break;
    case 'update_gamification':
        $lp = Session::read('oLP');

        $jsonGamification = [
            'stars' => 0,
            'score' => 0,
        ];

        if ($lp) {
            $score = $lp->getCalculateScore($sessionId);

            $jsonGamification['stars'] = $lp->getCalculateStars($sessionId);
            $jsonGamification['score'] = sprintf(get_lang('XPoints'), $score);
        }

        echo json_encode($jsonGamification);
        break;
    case 'check_item_position':
        $lp = Session::read('oLP');
        $lpItemId = isset($_GET['lp_item']) ? intval($_GET['lp_item']) : 0;
        if ($lp) {
            $position = $lp->isFirstOrLastItem($lpItemId);
        }

        echo json_encode($position);

        break;
    case 'get_parent_names':
        $newItemId = isset($_GET['new_item']) ? intval($_GET['new_item']) : 0;

        if (!$newItemId) {
            break;
        }

        /** @var \learnpath $lp */
        $lp = Session::read('oLP');
        $parentNames = $lp->getCurrentItemParentNames($newItemId);
        $response = '';
        foreach ($parentNames as $parentName) {
            $response .= '<p class="h5 hidden-xs hidden-md">'.$parentName.'</p>';
        }

        echo $response;
        break;
    default:
        echo '';
}
exit;

/*
 * Classes to create a special data structure to manipulate LP Items
 * used only in this file
 * @todo move in a file
 * @todo use PSR
 */

class LP_item_order_list
{
    public $list = [];

    public function __construct()
    {
        $this->list = [];
    }

    public function add($in_LP_item_order_item)
    {
        $this->list[] = $in_LP_item_order_item;
    }

    public function get_item_with_same_parent($in_parent_id)
    {
        $out_res = new LP_item_order_list();
        for ($i = 0; $i < count($this->list); $i++) {
            if ($this->list[$i]->parent_item_id == $in_parent_id) {
                $out_res->add($this->list[$i]);
            }
        }

        return $out_res;
    }

    public function get_list_of_parents()
    {
        $tab_out_res = [];
        foreach ($this->list as $LP_item) {
            if (!in_array($LP_item->parent_item_id, $tab_out_res)) {
                $tab_out_res[] = $LP_item->parent_item_id;
            }
        }

        return $tab_out_res;
    }

    public function set_parameters_for_id($in_id, $in_value, $in_parameters)
    {
        for ($i = 0; $i < count($this->list); $i++) {
            if ($this->list[$i]->id == $in_id) {
                $this->list[$i]->$in_parameters = $in_value;
                break;
            }
        }
    }
}

class LP_item_order_item
{
    public $id = 0;
    public $parent_item_id = 0;
    public $previous_item_id = 0;
    public $next_item_id = 0;
    public $display_order = 0;

    public function __construct($in_id = 0, $in_parent_id = 0)
    {
        $this->id = $in_id;
        $this->parent_item_id = $in_parent_id;
    }
}
