<?php
/* For licensing terms, see /license.txt */

use APY\DataGridBundle\Grid\Action\MassAction;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Row;
use APY\DataGridBundle\Grid\Source\Entity;
use Chamilo\CoreBundle\Entity\Resource\ResourceLink;
use Chamilo\CoreBundle\Framework\Container;
use Chamilo\CoreBundle\Security\Authorization\Voter\ResourceNodeVoter;
use Chamilo\CourseBundle\Entity\CQuizQuestionCategory;
use ChamiloSession as Session;

/**
 * Class TestCategory.
 * Manage question categories inside an exercise.
 *
 * @author hubert.borderiou
 * @author Julio Montoya - several fixes
 *
 * @todo rename to ExerciseCategory
 */
class TestCategory
{
    public $id;
    public $name;
    public $description;

    /**
     * Constructor of the class Category.
     */
    public function __construct()
    {
        $this->name = '';
        $this->description = '';
    }

    /**
     * return the TestCategory object with id=in_id.
     *
     * @param int $id
     * @param int $courseId
     *
     * @return TestCategory
     */
    public function getCategory($id, $courseId = 0)
    {
        $table = Database::get_course_table(TABLE_QUIZ_QUESTION_CATEGORY);
        $id = (int) $id;
        $courseId = empty($courseId) ? api_get_course_int_id() : (int) $courseId;
        $sql = "SELECT * FROM $table
                WHERE id = $id AND c_id = ".$courseId;
        $res = Database::query($sql);

        if (Database::num_rows($res)) {
            $row = Database::fetch_array($res);

            $this->id = $row['id'];
            $this->name = $row['title'];
            $this->description = $row['description'];

            return $this;
        }

        return false;
    }

    /**
     * Save TestCategory in the database if name doesn't exists.
     *
     * @param int $courseId
     *
     * @return bool
     */
    public function save($courseId = 0)
    {
        $courseId = empty($courseId) ? api_get_course_int_id() : (int) $courseId;
        $courseInfo = api_get_course_info_by_id($courseId);
        if (empty($courseInfo)) {
            return false;
        }

        $table = Database::get_course_table(TABLE_QUIZ_QUESTION_CATEGORY);

        // check if name already exists
        $sql = "SELECT count(*) AS nb FROM $table
                WHERE title = '".Database::escape_string($this->name)."' AND c_id = $courseId";
        $result = Database::query($sql);
        $row = Database::fetch_array($result);
        // lets add in BDD if not the same name
        if ($row['nb'] <= 0) {
            $repo = Container::getQuestionCategoryRepository();
            $category = new CQuizQuestionCategory();
            $category
                ->setTitle($this->name)
                ->setCourse($courseInfo['entity'])
                ->setDescription($this->description);
            $em = $repo->getEntityManager();
            $em->persist($category);
            $em->flush();

            if ($category) {
                $newId = $category->getIid();
                $sql = "UPDATE $table SET id = iid WHERE iid = $newId";
                Database::query($sql);
                $repo->addResourceToCourse(
                    $category,
                    ResourceLink::VISIBILITY_PUBLISHED,
                    api_get_user_entity(api_get_user_id()),
                    $courseInfo['entity'],
                    api_get_session_entity(),
                    api_get_group_entity()
                );

                return $newId;
            }
        }

        return false;
    }

    /**
     * Removes the category from the database
     * if there were question in this category, the link between question and category is removed.
     *
     * @param int $id
     *
     * @return bool
     */
    public function removeCategory($id)
    {
        $tbl_question_rel_cat = Database::get_course_table(TABLE_QUIZ_QUESTION_REL_CATEGORY);
        $id = (int) $id;
        $course_id = api_get_course_int_id();
        $category = $this->getCategory($id, $course_id);

        if ($category) {
            // remove link between question and category
            $sql = "DELETE FROM $tbl_question_rel_cat
                    WHERE category_id = $id AND c_id=".$course_id;
            Database::query($sql);

            $repo = Container::getQuestionCategoryRepository();
            $category = $repo->find($id);
            $repo->hardDelete($category);

            return true;
        }

        return false;
    }

    /**
     * @param                                                   $primaryKeys
     * @param                                                   $allPrimaryKeys
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     * @param                                                   $parameters
     */
    public function deleteResource(
        $primaryKeys,
        $allPrimaryKeys,
        Symfony\Component\HttpFoundation\Session\Session $session,
        $parameters
    ) {
        $repo = Container::getQuestionCategoryRepository();
        $translator = Container::getTranslator();
        foreach ($primaryKeys as $id) {
            $category = $repo->find($id);
            $repo->hardDelete($category);
        }

        Display::addFlash(Display::return_message($translator->trans('Deleted')));
        header('Location:'.api_get_self().'?'.api_get_cidreq());
        exit;
    }

    /**
     * Modify category name or description of category with id=in_id.
     *
     * @param int $courseId
     *
     * @return bool
     */
    public function modifyCategory($courseId = 0)
    {
        $courseId = empty($courseId) ? api_get_course_int_id() : (int) $courseId;
        $courseInfo = api_get_course_info_by_id($courseId);
        if (empty($courseInfo)) {
            return false;
        }

        $repo = Container::getQuestionCategoryRepository();
        /** @var CQuizQuestionCategory $category */
        $category = $repo->find($this->id);
        if ($category) {
            $category
                ->setTitle($this->name)
                ->setDescription($this->description)
            ;

            $repo->getEntityManager()->persist($category);
            $repo->getEntityManager()->flush();

            return true;
        }

        return false;
    }

    /**
     * Return the TestCategory id for question with question_id = $questionId
     * In this version, a question has only 1 TestCategory.
     * Return the TestCategory id, 0 if none.
     *
     * @param int $questionId
     * @param int $courseId
     *
     * @return int
     */
    public static function getCategoryForQuestion($questionId, $courseId = 0)
    {
        $courseId = (int) $courseId;
        $questionId = (int) $questionId;

        if (empty($courseId)) {
            $courseId = api_get_course_int_id();
        }

        if (empty($courseId) || empty($questionId)) {
            return 0;
        }

        $table = Database::get_course_table(TABLE_QUIZ_QUESTION_REL_CATEGORY);
        $sql = "SELECT category_id
                FROM $table
                WHERE question_id = $questionId AND c_id = $courseId";
        $res = Database::query($sql);
        $result = 0;
        if (Database::num_rows($res) > 0) {
            $data = Database::fetch_array($res);
            $result = (int) $data['category_id'];
        }

        return $result;
    }

    /**
     * Return the category name for question with question_id = $questionId
     * In this version, a question has only 1 category.
     *
     * @param $questionId
     * @param int $courseId
     *
     * @return string
     */
    public static function getCategoryNameForQuestion($questionId, $courseId = 0)
    {
        if (empty($courseId)) {
            $courseId = api_get_course_int_id();
        }
        $courseId = (int) $courseId;
        $categoryId = self::getCategoryForQuestion($questionId, $courseId);
        $table = Database::get_course_table(TABLE_QUIZ_QUESTION_CATEGORY);
        $sql = "SELECT title 
                FROM $table
                WHERE id = $categoryId AND c_id = $courseId";
        $res = Database::query($sql);
        $data = Database::fetch_array($res);
        $result = '';
        if (Database::num_rows($res) > 0) {
            $result = $data['title'];
        }

        return $result;
    }

    /**
     * Return the list of differents categories ID for a test in the current course
     * input : test_id
     * return : array of category id (integer)
     * hubert.borderiou 07-04-2011.
     *
     * @param int $exerciseId
     * @param int $courseId
     *
     * @return array
     */
    public static function getListOfCategoriesIDForTest($exerciseId, $courseId = 0)
    {
        // parcourir les questions d'un test, recup les categories uniques dans un tableau
        $exercise = new Exercise($courseId);
        $exercise->read($exerciseId, false);
        $categoriesInExercise = $exercise->getQuestionWithCategories();
        // the array given by selectQuestionList start at indice 1 and not at indice 0 !!! ???
        $categories = [];
        if (!empty($categoriesInExercise)) {
            foreach ($categoriesInExercise as $category) {
                $categories[$category['id']] = $category;
            }
        }

        return $categories;
    }

    /**
     * @return array
     */
    public static function getListOfCategoriesIDForTestObject(Exercise $exercise)
    {
        // parcourir les questions d'un test, recup les categories uniques dans un tableau
        $categories_in_exercise = [];
        $question_list = $exercise->getQuestionOrderedListByName();

        // the array given by selectQuestionList start at indice 1 and not at indice 0 !!! ???
        foreach ($question_list as $questionInfo) {
            $question_id = $questionInfo['question_id'];
            $category_list = self::getCategoryForQuestion($question_id);
            if (is_numeric($category_list)) {
                $category_list = [$category_list];
            }

            if (!empty($category_list)) {
                $categories_in_exercise = array_merge($categories_in_exercise, $category_list);
            }
        }
        if (!empty($categories_in_exercise)) {
            $categories_in_exercise = array_unique(array_filter($categories_in_exercise));
        }

        return $categories_in_exercise;
    }

    /**
     * Return the list of different categories NAME for a test.
     *
     * @param int $exerciseId
     * @param bool
     *
     * @return array
     *
     * @author function rewrote by jmontoya
     */
    public static function getListOfCategoriesNameForTest($exerciseId, $grouped_by_category = true)
    {
        $result = [];
        $categories = self::getListOfCategoriesIDForTest($exerciseId);

        foreach ($categories as $catInfo) {
            $categoryId = $catInfo['id'];
            if (!empty($categoryId)) {
                $result[$categoryId] = [
                    'title' => $catInfo['title'],
                    //'parent_id' =>  $catInfo['parent_id'],
                    'parent_id' => '',
                    'c_id' => $catInfo['c_id'],
                ];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function getListOfCategoriesForTest(Exercise $exercise)
    {
        $result = [];
        $categories = self::getListOfCategoriesIDForTestObject($exercise);
        foreach ($categories as $cat_id) {
            $cat = new TestCategory();
            $cat = (array) $cat->getCategory($cat_id);
            $cat['iid'] = $cat['id'];
            $cat['title'] = $cat['name'];
            $result[$cat['id']] = $cat;
        }

        return $result;
    }

    /**
     * return the number of question of a category id in a test.
     *
     * @param int $exerciseId
     * @param int $categoryId
     *
     * @return int
     *
     * @author hubert.borderiou 07-04-2011
     */
    public static function getNumberOfQuestionsInCategoryForTest($exerciseId, $categoryId)
    {
        $nbCatResult = 0;
        $quiz = new Exercise();
        $quiz->read($exerciseId);
        $questionList = $quiz->selectQuestionList();
        // the array given by selectQuestionList start at indice 1 and not at indice 0 !!! ? ? ?
        for ($i = 1; $i <= count($questionList); $i++) {
            if (self::getCategoryForQuestion($questionList[$i]) == $categoryId) {
                $nbCatResult++;
            }
        }

        return $nbCatResult;
    }

    /**
     * return the number of question for a test using random by category
     * input  : test_id, number of random question (min 1).
     *
     * @param int $exerciseId
     * @param int $random
     *
     * @return int
     *             hubert.borderiou 07-04-2011
     *             question without categories are not counted
     */
    public static function getNumberOfQuestionRandomByCategory($exerciseId, $random)
    {
        $count = 0;
        $categories = self::getListOfCategoriesIDForTest($exerciseId);
        foreach ($categories as $category) {
            if (empty($category['id'])) {
                continue;
            }

            $nbQuestionInThisCat = self::getNumberOfQuestionsInCategoryForTest(
                $exerciseId,
                $category['id']
            );

            if ($nbQuestionInThisCat > $random) {
                $count += $random;
            } else {
                $count += $nbQuestionInThisCat;
            }
        }

        return $count;
    }

    /**
     * @return array
     */
    public static function getCategoriesForSelect()
    {
        $courseId = api_get_course_int_id();
        $categories = self::getCategories($courseId);

        $result = ['0' => get_lang('GeneralSelected')];
        /** @var CQuizQuestionCategory $category */
        foreach ($categories as $category) {
            $result[$category->getId()] = $category->getTitle();
        }

        return $result;
    }

    /**
     * Returns an array of question ids for each category
     * $categories[1][30] = 10, array with category id = 1 and question_id = 10
     * A question has "n" categories.
     *
     * @param int   $exerciseId
     * @param array $check_in_question_list
     * @param array $categoriesAddedInExercise
     *
     * @return array
     */
    public static function getQuestionsByCat(
        $exerciseId,
        $check_in_question_list = [],
        $categoriesAddedInExercise = []
    ) {
        $tableQuestion = Database::get_course_table(TABLE_QUIZ_QUESTION);
        $TBL_EXERCICE_QUESTION = Database::get_course_table(TABLE_QUIZ_TEST_QUESTION);
        $TBL_QUESTION_REL_CATEGORY = Database::get_course_table(TABLE_QUIZ_QUESTION_REL_CATEGORY);
        $categoryTable = Database::get_course_table(TABLE_QUIZ_QUESTION_CATEGORY);
        $exerciseId = (int) $exerciseId;
        $courseId = api_get_course_int_id();

        $sql = "SELECT DISTINCT qrc.question_id, qrc.category_id
                FROM $TBL_QUESTION_REL_CATEGORY qrc
                INNER JOIN $TBL_EXERCICE_QUESTION eq
                ON (eq.question_id = qrc.question_id AND qrc.c_id = eq.c_id)
                INNER JOIN $categoryTable c
                ON (c.id = qrc.category_id AND c.c_id = eq.c_id)
                INNER JOIN $tableQuestion q
                ON (q.id = qrc.question_id AND q.c_id = eq.c_id)
                WHERE
                    exercice_id = $exerciseId AND
                    qrc.c_id = $courseId
                ";

        $res = Database::query($sql);
        $categories = [];
        while ($data = Database::fetch_array($res)) {
            if (!empty($check_in_question_list)) {
                if (!in_array($data['question_id'], $check_in_question_list)) {
                    continue;
                }
            }

            if (!isset($categories[$data['category_id']]) ||
                !is_array($categories[$data['category_id']])
            ) {
                $categories[$data['category_id']] = [];
            }

            $categories[$data['category_id']][] = $data['question_id'];
        }

        if (!empty($categoriesAddedInExercise)) {
            $newCategoryList = [];
            foreach ($categoriesAddedInExercise as $category) {
                $categoryId = $category['category_id'];
                if (isset($categories[$categoryId])) {
                    $newCategoryList[$categoryId] = $categories[$categoryId];
                }
            }

            $checkQuestionsWithNoCategory = false;
            foreach ($categoriesAddedInExercise as $category) {
                if (empty($category['category_id'])) {
                    // Check
                    $checkQuestionsWithNoCategory = true;
                    break;
                }
            }

            // Select questions that don't have any category related
            if ($checkQuestionsWithNoCategory) {
                $originalQuestionList = $check_in_question_list;
                foreach ($originalQuestionList as $questionId) {
                    $categoriesFlatten = array_flatten($categories);
                    if (!in_array($questionId, $categoriesFlatten)) {
                        $newCategoryList[0][] = $questionId;
                    }
                }
            }
            $categories = $newCategoryList;
        }

        return $categories;
    }

    /**
     * Returns an array of $numberElements from $array.
     *
     * @param array
     * @param int
     *
     * @return array
     */
    public static function getNElementsFromArray($array, $numberElements)
    {
        $list = $array;
        shuffle($list);
        if ($numberElements < count($list)) {
            $list = array_slice($list, 0, $numberElements);
        }

        return $list;
    }

    /**
     * @param int $questionId
     * @param int $displayCategoryName
     */
    public static function displayCategoryAndTitle($questionId, $displayCategoryName = 1)
    {
        echo self::returnCategoryAndTitle($questionId, $displayCategoryName);
    }

    /**
     * @param int $questionId
     * @param int $in_display_category_name
     *
     * @return string|null
     */
    public static function returnCategoryAndTitle($questionId, $in_display_category_name = 1)
    {
        $is_student = !(api_is_allowed_to_edit(null, true) || api_is_session_admin());
        $objExercise = Session::read('objExercise');
        if (!empty($objExercise)) {
            $in_display_category_name = $objExercise->display_category_name;
        }
        $content = null;
        if (self::getCategoryNameForQuestion($questionId) != '' &&
            ($in_display_category_name == 1 || !$is_student)
        ) {
            $content .= '<div class="page-header">';
            $content .= '<h4>'.get_lang('Category').": ".self::getCategoryNameForQuestion($questionId).'</h4>';
            $content .= "</div>";
        }

        return $content;
    }

    /**
     * sortTabByBracketLabel ($tabCategoryQuestions)
     * key of $tabCategoryQuestions are the category id (0 for not in a category)
     * value is the array of question id of this category
     * Sort question by Category.
     */
    public static function sortTabByBracketLabel($in_tab)
    {
        $tabResult = [];
        $tabCatName = []; // tab of category name
        foreach ($in_tab as $cat_id => $tabquestion) {
            $category = new TestCategory();
            $category = $category->getCategory($cat_id);
            $tabCatName[$cat_id] = $category->name;
        }
        reset($in_tab);
        // sort table by value, keeping keys as they are
        asort($tabCatName);
        // keys of $tabCatName are keys order for $in_tab
        foreach ($tabCatName as $key => $val) {
            $tabResult[$key] = $in_tab[$key];
        }

        return $tabResult;
    }

    /**
     * Return the number max of question in a category
     * count the number of questions in all categories, and return the max.
     *
     * @param int $exerciseId
     *
     * @author - hubert borderiou
     *
     * @return int
     */
    public static function getNumberMaxQuestionByCat($exerciseId)
    {
        $res_num_max = 0;
        // foreach question
        $categories = self::getListOfCategoriesIDForTest($exerciseId);
        foreach ($categories as $category) {
            if (empty($category['id'])) {
                continue;
            }

            $nbQuestionInThisCat = self::getNumberOfQuestionsInCategoryForTest(
                $exerciseId,
                $category['id']
            );

            if ($nbQuestionInThisCat > $res_num_max) {
                $res_num_max = $nbQuestionInThisCat;
            }
        }

        return $res_num_max;
    }

    /**
     * Returns a category summary report.
     *
     * @param int   $exerciseId
     * @param array $category_list
     *                             pre filled array with the category_id, score, and weight
     *                             example: array(1 => array('score' => '10', 'total' => 20));
     *
     * @return string
     */
    public static function get_stats_table_by_attempt(
        $exerciseId,
        $category_list = []
    ) {
        if (empty($category_list)) {
            return null;
        }
        $category_name_list = self::getListOfCategoriesNameForTest($exerciseId);

        $table = new HTML_Table(['class' => 'table table-bordered', 'id' => 'category_results']);
        $table->setHeaderContents(0, 0, get_lang('Categories'));
        $table->setHeaderContents(0, 1, get_lang('Absolute score'));
        $table->setHeaderContents(0, 2, get_lang('Relative score'));
        $row = 1;

        $none_category = [];
        if (isset($category_list['none'])) {
            $none_category = $category_list['none'];
            unset($category_list['none']);
        }

        $total = [];
        if (isset($category_list['total'])) {
            $total = $category_list['total'];
            unset($category_list['total']);
        }
        if (count($category_list) > 1) {
            foreach ($category_list as $category_id => $category_item) {
                $table->setCellContents($row, 0, $category_name_list[$category_id]);
                $table->setCellContents(
                    $row,
                    1,
                    ExerciseLib::show_score(
                        $category_item['score'],
                        $category_item['total'],
                        false
                    )
                );
                $table->setCellContents(
                    $row,
                    2,
                    ExerciseLib::show_score(
                        $category_item['score'],
                        $category_item['total'],
                        true,
                        false,
                        true
                    )
                );
                $row++;
            }

            if (!empty($none_category)) {
                $table->setCellContents($row, 0, get_lang('none'));
                $table->setCellContents(
                    $row,
                    1,
                    ExerciseLib::show_score(
                        $none_category['score'],
                        $none_category['total'],
                        false
                    )
                );
                $table->setCellContents(
                    $row,
                    2,
                    ExerciseLib::show_score(
                        $none_category['score'],
                        $none_category['total'],
                        true,
                        false,
                        true
                    )
                );
                $row++;
            }
            if (!empty($total)) {
                $table->setCellContents($row, 0, get_lang('Total'));
                $table->setCellContents(
                    $row,
                    1,
                    ExerciseLib::show_score(
                        $total['score'],
                        $total['total'],
                        false
                    )
                );
                $table->setCellContents(
                    $row,
                    2,
                    ExerciseLib::show_score(
                        $total['score'],
                        $total['total'],
                        true,
                        false,
                        true
                    )
                );
            }

            return $table->toHtml();
        }

        return '';
    }

    /**
     * @param Exercise $exercise
     * @param int      $courseId
     * @param string   $order
     * @param bool     $shuffle
     * @param bool     $excludeCategoryWithNoQuestions
     *
     * @return array
     */
    public function getCategoryExerciseTree(
        $exercise,
        $courseId,
        $order = null,
        $shuffle = false,
        $excludeCategoryWithNoQuestions = true
    ) {
        if (empty($exercise)) {
            return [];
        }

        $courseId = (int) $courseId;
        $table = Database::get_course_table(TABLE_QUIZ_REL_CATEGORY);
        $categoryTable = Database::get_course_table(TABLE_QUIZ_QUESTION_CATEGORY);
        $exercise->id = (int) $exercise->id;

        $sql = "SELECT * FROM $table qc
                LEFT JOIN $categoryTable c
                ON (qc.c_id = c.c_id AND c.id = qc.category_id)
                WHERE qc.c_id = $courseId AND exercise_id = {$exercise->id} ";

        if (!empty($order)) {
            $order = Database::escape_string($order);
            $sql .= "ORDER BY $order";
        }

        $categories = [];
        $result = Database::query($sql);
        if (Database::num_rows($result)) {
            while ($row = Database::fetch_array($result, 'ASSOC')) {
                if ($excludeCategoryWithNoQuestions) {
                    if ($row['count_questions'] == 0) {
                        continue;
                    }
                }
                if (empty($row['title']) && empty($row['category_id'])) {
                    $row['title'] = get_lang('General');
                }
                $categories[$row['category_id']] = $row;
            }
        }

        if ($shuffle) {
            shuffle_assoc($categories);
        }

        return $categories;
    }

    /**
     * @param FormValidator $form
     * @param string        $action
     */
    public function getForm(&$form, $action = 'new')
    {
        switch ($action) {
            case 'new':
                $header = get_lang('Add category');
                $submit = get_lang('Add test category');
                break;
            case 'edit':
                $header = get_lang('Edit this category');
                $submit = get_lang('Edit category');
                break;
        }

        // Setting the form elements
        $form->addElement('header', $header);
        $form->addElement('hidden', 'category_id');
        $form->addElement(
            'text',
            'category_name',
            get_lang('Category name'),
            ['class' => 'span6']
        );
        $form->add_html_editor(
            'category_description',
            get_lang('Category description'),
            false,
            false,
            [
                'ToolbarSet' => 'test_category',
                'Width' => '90%',
                'Height' => '200',
            ]
        );
        $category_parent_list = [];

        $options = [
                '1' => get_lang('Visible'),
                '0' => get_lang('Hidden'),
        ];
        $form->addElement(
            'select',
            'visibility',
            get_lang('Visibility'),
            $options
        );
        $script = null;
        if (!empty($this->parent_id)) {
            $parent_cat = new TestCategory();
            $parent_cat = $parent_cat->getCategory($this->parent_id);
            $category_parent_list = [$parent_cat->id => $parent_cat->name];
            $script .= '<script>$(function() { $("#parent_id").trigger("addItem",[{"title": "'.$parent_cat->name.'", "value": "'.$parent_cat->id.'"}]); });</script>';
        }
        $form->addElement('html', $script);

        $form->addElement('select', 'parent_id', get_lang('Parent'), $category_parent_list, ['id' => 'parent_id']);
        $form->addElement('style_submit_button', 'SubmitNote', $submit, 'class="add"');

        // setting the defaults
        $defaults = [];
        $defaults["category_id"] = $this->id;
        $defaults["category_name"] = $this->name;
        $defaults["category_description"] = $this->description;
        $defaults["parent_id"] = $this->parent_id;
        $defaults["visibility"] = $this->visibility;
        $form->setDefaults($defaults);

        // setting the rules
        $form->addRule('category_name', get_lang('Required field'), 'required');
    }

    /**
     * Returns the category form.
     *
     * @return string
     */
    public function returnCategoryForm(Exercise $exercise)
    {
        $categories = $this->getListOfCategoriesForTest($exercise);
        $saved_categories = $exercise->getCategoriesInExercise();
        $return = null;

        if (!empty($categories)) {
            $nbQuestionsTotal = $exercise->getNumberQuestionExerciseCategory();
            $exercise->setCategoriesGrouping(true);
            $real_question_count = count($exercise->getQuestionList());

            $warning = null;
            if ($nbQuestionsTotal != $real_question_count) {
                $warning = Display::return_message(
                    get_lang('Make sure you have enough questions in your categories.'),
                    'warning'
                );
            }

            $return .= $warning;
            $return .= '<table class="data_table">';
            $return .= '<tr>';
            $return .= '<th height="24">'.get_lang('Categories').'</th>';
            $return .= '<th width="70" height="24">'.get_lang('N°').'</th></tr>';

            $emptyCategory = [
                'id' => '0',
                'name' => get_lang('General'),
                'description' => '',
                'iid' => '0',
                'title' => get_lang('General'),
            ];

            $categories[] = $emptyCategory;

            foreach ($categories as $category) {
                $cat_id = $category['iid'];
                $return .= '<tr>';
                $return .= '<td>';
                $return .= Display::div($category['name']);
                $return .= '</td>';
                $return .= '<td>';
                $value = isset($saved_categories) && isset($saved_categories[$cat_id]) ? $saved_categories[$cat_id]['count_questions'] : -1;
                $return .= '<input name="category['.$cat_id.']" value="'.$value.'" />';
                $return .= '</td>';
                $return .= '</tr>';
            }

            $return .= '</table>';
            $return .= get_lang('-1 = All questions will be selected.');
        }

        return $return;
    }

    /**
     * Return true if a category already exists with the same name.
     *
     * @param string $name
     * @param int    $courseId
     *
     * @return bool
     */
    public static function categoryTitleExists($name, $courseId)
    {
        $repo = Container::getQuestionCategoryRepository();
        $criteria = [
            'title' => $name,
            'course' => $courseId,
        ];

        return $repo->getRepository()->count($criteria) > 0;
    }

    /**
     * Return the id of the test category with title = $in_title.
     *
     * @param string $title
     * @param int    $courseId
     *
     * @return int is id of test category
     */
    public static function get_category_id_for_title($title, $courseId = 0)
    {
        $out_res = 0;
        if (empty($courseId)) {
            $courseId = api_get_course_int_id();
        }
        $courseId = (int) $courseId;
        $tbl_cat = Database::get_course_table(TABLE_QUIZ_QUESTION_CATEGORY);
        $sql = "SELECT id FROM $tbl_cat
                WHERE c_id = $courseId AND title = '".Database::escape_string($title)."'";
        $res = Database::query($sql);
        if (Database::num_rows($res) > 0) {
            $data = Database::fetch_array($res);
            $out_res = $data['id'];
        }

        return $out_res;
    }

    /**
     * Add a relation between question and category in table c_quiz_question_rel_category.
     *
     * @param int $categoryId
     * @param int $questionId
     * @param int $courseId
     *
     * @return string|false
     */
    public static function addCategoryToQuestion($categoryId, $questionId, $courseId)
    {
        $table = Database::get_course_table(TABLE_QUIZ_QUESTION_REL_CATEGORY);
        // if question doesn't have a category
        // @todo change for 1.10 when a question can have several categories
        if (self::getCategoryForQuestion($questionId, $courseId) == 0 &&
            $questionId > 0 &&
            $courseId > 0
        ) {
            $sql = "INSERT INTO $table (c_id, question_id, category_id)
                    VALUES (".intval($courseId).", ".intval($questionId).", ".intval($categoryId).")";
            Database::query($sql);
            $id = Database::insert_id();

            return $id;
        }

        return false;
    }

    /**
     * @param int $courseId
     * @param int $sessionId
     *
     * @return array
     */
    public static function getCategories($courseId, $sessionId = 0)
    {
        if (empty($courseId)) {
            return [];
        }

        $sessionId = (int) $sessionId;
        $courseId = (int) $courseId;
        $sessionEntity = null;
        if (!empty($sessionId)) {
            $sessionEntity = api_get_session_entity($sessionId);
        }

        $courseEntity = api_get_course_entity($courseId);
        $repo = Container::getQuestionCategoryRepository();
        $resources = $repo->getResourcesByCourse($courseEntity, $sessionEntity);

        return $resources->getQuery()->getResult();
    }

    /**
     * @param int $courseId
     * @param int $sessionId
     *
     * @return string
     */
    public function displayCategories($courseId, $sessionId = 0)
    {
        $course = api_get_course_entity($courseId);
        $session = api_get_session_entity($sessionId);

        // 1. Set entity
        $source = new Entity('ChamiloCourseBundle:CQuizQuestionCategory');
        $repo = Container::getQuestionCategoryRepository();

        // 2. Get query builder from repo.
        $qb = $repo->getResourcesByCourse($course, $session);

        // 3. Set QueryBuilder to the source.
        $source->initQueryBuilder($qb);

        // 4. Get the grid builder.
        $builder = Container::$container->get('apy_grid.factory');

        // 5. Set parameters and properties.
        $grid = $builder->createBuilder(
            'grid',
            $source,
            [
                'persistence' => false,
                'route' => 'home',
                'filterable' => true,
                'sortable' => true,
                'max_per_page' => 10,
            ]
        )->add(
            'id',
            'number',
            [
                'title' => '#',
                'primary' => true,
                'visible' => false,
            ]
        )->add(
            'title',
            'text',
            [
                'title' => get_lang('Name'),
            ]
        );

        $grid = $grid->getGrid();

        // 7. Add actions
        if (Container::getAuthorizationChecker()->isGranted(ResourceNodeVoter::ROLE_CURRENT_COURSE_TEACHER)) {
            // Add row actions
            $myRowAction = new RowAction(
                get_lang('Edit'),
                'legacy_main',
                false,
                '_self',
                ['class' => 'btn btn-secondary']
            );
            $myRowAction->setRouteParameters(
                [
                    'id',
                    'name' => 'exercise/tests_category.php',
                    'cidReq' => $course->getCode(),
                    'action' => 'editcategory',
                ]
            );

            $myRowAction->addManipulateRender(
                function (RowAction $action, Row $row) use ($session, $repo) {
                    return $repo->rowCanBeEdited($session, $action, $row);
                }
            );

            $grid->addRowAction($myRowAction);

            $myRowAction = new RowAction(
                get_lang('Delete'),
                'legacy_main',
                true,
                '_self',
                ['class' => 'btn btn-danger', 'form_delete' => true]
            );
            $myRowAction->setRouteParameters(
                [
                    'id',
                    'name' => 'exercise/tests_category.php',
                    'cidReq' => $course->getCode(),
                    'action' => 'deletecategory',
                ]
            );

            $myRowAction->addManipulateRender(
                function (RowAction $action, Row $row) use ($session, $repo) {
                    return $repo->rowCanBeEdited($session, $action, $row);
                }
            );

            $grid->addRowAction($myRowAction);

            if (empty($session)) {
                // Add mass actions
                $deleteMassAction = new MassAction(
                    'Delete',
                    ['TestCategory', 'deleteResource'],
                    true,
                    [],
                    ResourceNodeVoter::ROLE_CURRENT_COURSE_TEACHER
                );
                $grid->addMassAction($deleteMassAction);
            }
        }

        // 8. Set route and request
        $grid
            ->setRouteUrl(api_get_self().'?'.api_get_cidreq())
            ->handleRequest(Container::getRequest())
        ;

        $html = Container::$container->get('twig')->render(
            '@ChamiloTheme/Resource/grid.html.twig',
            ['grid' => $grid]
        );

        return $html;
    }
}
