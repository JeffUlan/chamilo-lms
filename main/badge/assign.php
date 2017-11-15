<?php
/* For licensing terms, see /license.txt */

use \Skill as SkillManager;
use Chamilo\CoreBundle\Entity\Skill;
use Chamilo\CoreBundle\Entity\SkillRelUser;
use Chamilo\UserBundle\Entity\User;

/**
 * Page for assign skills to a user
 *
 * @autor: Jose Loguercio <jose.loguercio@beeznest.com>
 * @package chamilo.badge
 */

require_once __DIR__.'/../inc/global.inc.php';

$userId = isset($_REQUEST['user']) ? (int) $_REQUEST['user'] : 0;

if (empty($userId)) {
    api_not_allowed(true);
}

SkillManager::isAllow($userId);

$entityManager = Database::getManager();
$skillRepo = $entityManager->getRepository('ChamiloCoreBundle:Skill');
$skillRelSkill = $entityManager->getRepository('ChamiloCoreBundle:SkillRelSkill');
$skillLevelRepo = $entityManager->getRepository('ChamiloSkillBundle:Level');
$skillUserRepo = $entityManager->getRepository('ChamiloCoreBundle:SkillRelUser');
$user = api_get_user_entity($userId);

if (!$user) {
    Display::addFlash(
        Display::return_message(get_lang('NoUser'), 'error')
    );

    header('Location: '.api_get_path(WEB_PATH));
    exit;
}

$skills = $skillRepo->findBy([
    'status' => Skill::STATUS_ENABLED
]);

$url = api_get_path(WEB_CODE_PATH).'badge/assign.php?user='.$userId.'&id=';

$htmlHeadXtra[] = '<script>
$( document ).ready(function() {
    $("#skill").on("change", function() {
        $(location).attr("href", "'. $url.'"+$(this).val());
    });
});
</script>';

$skillsOptions = [];
$acquiredLevel = [];
$formDefaultValues = [];

foreach ($skills as $skill) {
    $skillsOptions[$skill->getId()] = $skill->getName();
}

$skillId = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : key($skillsOptions);
$profile = $skillRepo->find($skillId)->getProfile();

if (!$profile) {
    $skillRelSkill = new SkillRelSkill();
    $parents = $skillRelSkill->getSkillParents($skillId);

    krsort($parents);

    foreach ($parents as $parent) {
        $skillParentId = $parent['skill_id'];
        $profile = $skillRepo->find($skillParentId)->getProfile();

        if ($profile) {
            break;
        }

        if (!$profile && $parent['parent_id'] == 0) {
            $profile = $skillLevelRepo->findAll();
            $profile = isset($profile[0]) ? $profile[0] : false;
        }
    }
}

if ($profile) {
    $profileId = $profile->getId();
    $levels = $skillLevelRepo->findBy([
        'profile' => $profileId
    ]);
    $profileLevels = [];

    foreach ($levels as $level) {
        $profileLevels[$level->getPosition()][$level->getId()] = $level->getName();
    }

    ksort($profileLevels); // Sort the array by Position.

    foreach ($profileLevels as $profileLevel) {
        $profileId = key($profileLevel);
        $acquiredLevel[$profileId] = $profileLevel[$profileId];
    }

}

$formDefaultValues = ['skill' => $skillId];

$form = new FormValidator('assign_skill');
$form->addHeader(get_lang('AssignSkill'));
$form->addText('user_name', get_lang('UserName'), false);
$form->addSelect('skill', get_lang('Skill'), $skillsOptions, ['id' => 'skill']);
$form->addHidden('user', $user->getId());
$form->addHidden('id', $skillId);
$form->addRule('skill', get_lang('ThisFieldIsRequired'), 'required');
$form->addSelect('acquired_level', get_lang('AcquiredLevel'), $acquiredLevel);
$form->addRule('acquired_level', get_lang('ThisFieldIsRequired'), 'required');
$form->addTextarea('argumentation', get_lang('Argumentation'), ['rows' => 6]);
$form->addRule('argumentation', get_lang('ThisFieldIsRequired'), 'required');
$form->addRule(
    'argumentation',
    sprintf(get_lang('ThisTextShouldBeAtLeastXCharsLong'), 10),
    'mintext',
    10
);
$form->applyFilter('argumentation', 'trim');
$form->addButtonSave(get_lang('Save'));
$form->setDefaults($formDefaultValues);

if ($form->validate()) {
    $values = $form->exportValues();
    $skill = $skillRepo->find($values['skill']);

    if (!$skill) {
        Display::addFlash(
            Display::return_message(get_lang('SkillNotFound'), 'error')
        );

        header('Location: '.api_get_self().'?'.http_build_query(['user' => $user->getId()]));
        exit;
    }

    if ($user->hasSkill($skill)) {
        Display::addFlash(
            Display::return_message(
                sprintf(
                    get_lang('TheUserXHasAlreadyAchievedTheSkillY'),
                    $user->getCompleteName(),
                    $skill->getName()
                ),
                'warning'
            )
        );

        header('Location: '.api_get_self().'?'.http_build_query(['user' => $user->getId()]));
        exit;
    }

    $skillUser = new SkillRelUser();
    $skillUser->setUser($user);
    $skillUser->setSkill($skill);
    $level = $skillLevelRepo->find(intval($values['acquired_level']));
    $skillUser->setAcquiredLevel($level);
    $skillUser->setArgumentation($values['argumentation']);
    $skillUser->setArgumentationAuthorId(api_get_user_id());
    $skillUser->setAcquiredSkillAt(new DateTime());
    $skillUser->setAssignedBy(0);

    $entityManager->persist($skillUser);
    $entityManager->flush();

    Display::addFlash(
        Display::return_message(
            sprintf(
                get_lang('SkillXAssignedToUserY'),
                $skill->getName(),
                $user->getCompleteName()
            ),
            'success'
        )
    );

    header('Location: '.api_get_path(WEB_PATH)."badge/{$skillUser->getId()}");
    exit;
}

$form->setDefaults(['user_name' => $user->getCompleteNameWithUsername()]);
$form->freeze(['user_name']);

if (api_is_drh()) {
    $interbreadcrumb[] = array(
        'url' => api_get_path(WEB_CODE_PATH).'mySpace/index.php',
        "name" => get_lang('MySpace')
    );
    if ($user->getStatus() == COURSEMANAGER) {
        $interbreadcrumb[] = array(
            "url" => api_get_path(WEB_CODE_PATH).'mySpace/teachers.php',
            'name' => get_lang('Teachers')
        );
    } else {
        $interbreadcrumb[] = array(
            "url" => api_get_path(WEB_CODE_PATH).'mySpace/student.php',
            'name' => get_lang('MyStudents')
        );
    }
    $interbreadcrumb[] = array(
        'url' => api_get_path(WEB_CODE_PATH).'mySpace/myStudents.php?student='.$userId,
        'name' => $user->getCompleteName()
    );
} else {
    $interbreadcrumb[] = array(
        'url' => api_get_path(WEB_CODE_PATH).'admin/index.php',
        'name' => get_lang('PlatformAdmin')
    );
    $interbreadcrumb[] = array(
        'url' => api_get_path(WEB_CODE_PATH).'admin/user_list.php',
        'name' => get_lang('UserList')
    );
    $interbreadcrumb[] = array(
        'url' => api_get_path(WEB_CODE_PATH).'admin/user_information.php?user_id='.$userId,
        'name' => $user->getCompleteName()
    );
}

$template = new Template(get_lang('AddSkill'));
$template->assign('content', $form->returnForm());
$template->display_one_col_template();
