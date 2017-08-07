<?php
/* For licensing terms, see /license.txt */

use Chamilo\CoreBundle\Entity\GradebookCategory;
use Doctrine\Common\Collections\Criteria;
use Knp\Component\Pager\Paginator;

require_once __DIR__.'/../inc/global.inc.php';

api_protect_admin_script();

$allow = api_get_configuration_value('gradebook_dependency');
if ($allow == false) {
    api_not_allowed(true);
}

$em = Database::getManager();
$repo = $em->getRepository('ChamiloCoreBundle:GradebookCategory');

$page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
$categoryId = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 1;
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';

if (empty($keyword)) {
    $gradeBookList = $repo->findAll();
} else {
    $criteria = new \Doctrine\Common\Collections\Criteria();
    $criteria
        ->where(
            Criteria::expr()->orX(
                Criteria::expr()->contains('courseCode', $keyword),
                Criteria::expr()->contains('name', $keyword)
            )
        );
    $gradeBookList = $repo->matching($criteria);
}

$currentUrl = api_get_self().'?';
$table = Database::get_main_table(TABLE_MAIN_GRADEBOOK_CATEGORY);
$contentForm = '';

switch ($action) {
    case 'add':
        $form = new FormValidator(
            'category_add',
            'post',
            $currentUrl.'&action=add'
        );
        $form->addText('name', get_lang('Name'));
        $form->addText('weight', get_lang('Weight'));
        $form->addSelectAjax(
            'course_id',
            get_lang('Course'),
            null,
            [
                'url' => api_get_path(WEB_AJAX_PATH).'course.ajax.php?a=search_course'
            ]
        );

        $form->addSelectAjax(
            'depends',
            get_lang('DependsOnGradebook'),
            null,
            [
                'url' => api_get_path(WEB_AJAX_PATH).'course.ajax.php?a=search_course',
                'multiple' => 'multiple'
            ]
        );

        $form->addText(
            'minimum',
            get_lang('MinimumGradebookToValidate'),
            false
        );

        $form->addButtonSave(get_lang('Add'));
        $contentForm = $form->returnForm();
        if ($form->validate()) {
            $values = $form->getSubmitValues();
            $courseId = isset($values['course_id']) ? $values['course_id'] : 0;
            $courseInfo = api_get_course_info_by_id($courseId);
            $courseCode = $courseInfo['code'];
            $criteria = ['courseCode' => $courseCode];
            $exists = $repo->findBy($criteria);
            if (empty($exists) || empty($courseId)) {
                if (empty($courseId)) {
                    $courseCode = '';
                }
                $category = new GradebookCategory();
                $category
                    ->setName($values['name'])
                    ->setWeight($values['weight'])
                    ->setVisible(1)
                    ->setLocked(0)
                    ->setGenerateCertificates(0)
                    ->setIsRequirement(false)
                    ->setCourseCode($courseCode)
                    ->setUserId(api_get_user_id());
                $em->persist($category);
                $em->flush();
                if ($category->getId()) {
                    $params = [];
                    if (!empty($values['depends'])) {
                        $depends = $values['depends'];
                        $depends = array_map('intval', $depends);
                        $value = serialize($depends);
                        $params['depends'] = $value;
                    }

                    if (!empty($values['minimum'])) {
                        $params['minimum_to_validate'] = (int) $values['minimum'];
                    }
                    if (!empty($params)) {
                        Database::update(
                            $table,
                            $params,
                            ['id = ?' => $category->getId()]
                        );
                    }
                    Display::addFlash(Display::return_message(get_lang('Added')));
                    header('Location: '.$currentUrl);
                    exit;
                }
            } else {
                Display::addFlash(Display::return_message(get_lang('CategoryExists')));
            }
        }
        break;
    case 'edit':
        /** @var GradebookCategory $category */
        $category = $repo->find($categoryId);
        if (!empty($category)) {
            $form = new FormValidator(
                'category_edit',
                'post',
                $currentUrl.'&action=edit&id='.$categoryId
            );
            $form->addText('name', get_lang('Name'));
            $form->addText('weight', get_lang('Weight'));
            $form->addText(
                'minimum',
                get_lang('MinimumGradebookToValidate'),
                false
            );
            $form->addLabel(get_lang('CourseCode'), $category->getCourseCode());

             $sql = "SELECT depends, minimum_to_validate 
                    FROM $table WHERE id = ".$categoryId;
            $result = Database::query($sql);
            $categoryData = Database::fetch_array($result, 'ASSOC');

            $options = [];
            if (!empty($categoryData['depends'])) {
                $list = unserialize($categoryData['depends']);
                foreach ($list as $itemId) {
                    $courseInfo = api_get_course_info_by_id($itemId);
                    $options[$itemId] = $courseInfo['name'];
                }
            }

            $form->addSelectAjax(
                'depends',
                get_lang('DependsOnGradebook'),
                $options,
                [
                    'url' => api_get_path(WEB_AJAX_PATH).'course.ajax.php?a=search_course',
                    'multiple' => 'multiple'
                ]
            );

            $form->addButtonSave(get_lang('Edit'));
            $defaults = [
                'name' => $category->getName(),
                'weight' => $category->getWeight(),
                'depends' => array_keys($options),
                'minimum' => $categoryData['minimum_to_validate']
            ];
            $form->setDefaults($defaults);
            $contentForm = $form->returnForm();
            if ($form->validate()) {
                $values = $form->getSubmitValues();
                $category->setName($values['name']);
                $category->setWeight($values['weight']);
                $em->merge($category);
                $em->flush();

                if (!empty($values['depends'])) {
                    $depends = $values['depends'];
                    $depends = array_map('intval', $depends);
                    $value = serialize($depends);
                    $params['depends'] = $value;
                }

                if (!empty($values['minimum'])) {
                    $params['minimum_to_validate'] = (int) $values['minimum'];
                }
                if (!empty($params)) {
                    Database::update(
                        $table,
                        $params,
                        ['id = ?' => $category->getId()]
                    );
                }

                Display::addFlash(Display::return_message(get_lang('Updated')));
                header('Location: '.$currentUrl);
                exit;
            }
        }
        break;
}

$paginator = new Paginator;
$pagination = $paginator->paginate(
    $gradeBookList,
    $page,
    5
);

// pagination.tpl needs current_url with out "page" param
$pagination->setCustomParameters(['current_url' => $currentUrl]);

$tpl = new Template(get_lang('Gradebook'));
$toolbar = Display::url(
    Display::return_icon('add.png', get_lang('Add'), [], ICON_SIZE_MEDIUM),
    $currentUrl.'&action=add'
);

$searchForm = new FormValidator(
    'course_filter',
    'get',
    '',
    '',
    array(),
    FormValidator::LAYOUT_INLINE
);
$searchForm->addText('keyword', '', false);
$searchForm->addButtonSearch(get_lang('Search'));


$pagination->renderer = function ($data) use ($tpl) {
    foreach ($data as $key => $value) {
        $tpl->assign($key, $value);
    }
    $layout = $tpl->get_template('admin/pagination.tpl');
    $content = $tpl->fetch($layout);

    return $content;
};

$tpl->assign('current_url', $currentUrl);
$tpl->assign(
    'actions',
    Display::toolbarAction(
        'toolbar',
        [$toolbar, $searchForm->returnForm()],
        [1, 4]
    )
);
$tpl->assign('form', $contentForm);
$tpl->assign('gradebook_list', $pagination);
$layout = $tpl->get_template('admin/gradebook_list.tpl');
$tpl->display($layout);
