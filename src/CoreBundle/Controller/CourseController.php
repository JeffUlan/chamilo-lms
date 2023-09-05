<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Controller;

use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Entity\ExtraField;
use Chamilo\CoreBundle\Entity\Session;
use Chamilo\CoreBundle\Entity\Tag;
use Chamilo\CoreBundle\Entity\Tool;
use Chamilo\CoreBundle\Entity\User;
use Chamilo\CoreBundle\Framework\Container;
use Chamilo\CoreBundle\Repository\ExtraFieldValuesRepository;
use Chamilo\CoreBundle\Repository\LanguageRepository;
use Chamilo\CoreBundle\Repository\LegalRepository;
use Chamilo\CoreBundle\Repository\Node\IllustrationRepository;
use Chamilo\CoreBundle\Repository\TagRepository;
use Chamilo\CoreBundle\Security\Authorization\Voter\CourseVoter;
use Chamilo\CoreBundle\Settings\SettingsManager;
use Chamilo\CoreBundle\Tool\AbstractTool;
use Chamilo\CoreBundle\Tool\ToolChain;
use Chamilo\CourseBundle\Controller\ToolBaseController;
use Chamilo\CourseBundle\Entity\CCourseDescription;
use Chamilo\CourseBundle\Entity\CTool;
use Chamilo\CourseBundle\Entity\CToolIntro;
use Chamilo\CourseBundle\Repository\CCourseDescriptionRepository;
use Chamilo\CourseBundle\Repository\CShortcutRepository;
use Chamilo\CourseBundle\Repository\CToolRepository;
use Chamilo\CourseBundle\Settings\SettingsCourseManager;
use Chamilo\CourseBundle\Settings\SettingsFormFactory;
use CourseManager;
use Database;
use Display;
use Doctrine\ORM\EntityManagerInterface;
use Event;
use Exercise;
use ExtraFieldValue;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use UserManager;

/**
 * @author Julio Montoya <gugli100@gmail.com>
 */
#[Route('/course')]
class CourseController extends ToolBaseController
{
    public function __construct(
        private readonly SerializerInterface $serializer
    ) {
    }

    #[Route('/{cid}/checkLegal.json', name: 'chamilo_core_course_check_legal_json')]
    public function checkTermsAndConditionJson(
        Request $request,
        LegalRepository $legalTermsRepo,
        LanguageRepository $languageRepository,
        ExtraFieldValuesRepository $extraFieldValuesRepository,
        SettingsManager $settingsManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $course = $this->getCourse();
        $responseData = [
            'redirect' => false,
            'url' => '#',
        ];

        if ($user && $user->hasRole('ROLE_STUDENT') &&
            'true' === $settingsManager->getSetting('allow_terms_conditions') &&
            'course' === $settingsManager->getSetting('load_term_conditions_section')
        ) {
            $termAndConditionStatus = false;
            $extraValue = $extraFieldValuesRepository->findLegalAcceptByItemId($user->getId());
            if (!empty($extraValue['value'])) {
                $result = $extraValue['value'];
                $userConditions = explode(':', $result);
                $version = $userConditions[0];
                $langId = (int) $userConditions[1];
                $realVersion = $legalTermsRepo->getLastVersion($langId);
                $termAndConditionStatus = ($version >= $realVersion);
            }

            if (false === $termAndConditionStatus) {
                $request->getSession()->set('term_and_condition', ['user_id' => $user->getId()]);
            } else {
                $request->getSession()->remove('term_and_condition');
            }

            $termsAndCondition = $request->getSession()->get('term_and_condition');
            if (null !== $termsAndCondition) {
                $redirect = true;
                $allow = 'true' === Container::getSettingsManager()
                    ->getSetting('course.allow_public_course_with_no_terms_conditions')
                ;

                if (true === $allow &&
                    null !== $course->getVisibility() &&
                    Course::OPEN_WORLD === $course->getVisibility()
                ) {
                    $redirect = false;
                }
                if ($redirect && !$this->isGranted('ROLE_ADMIN')) {
                    $url = '/main/auth/inscription.php';
                    $responseData = [
                        'redirect' => $redirect,
                        'url' => $url,
                    ];
                }
            }
        }

        $json = $this->serializer->serialize(
            $responseData,
            'json',
            [
                'groups' => ['course:read', 'ctool:read', 'tool:read', 'cshortcut:read'],
            ]
        );

        return new Response(
            $json,
            Response::HTTP_OK,
            [
                'Content-type' => 'application/json',
            ]
        );
    }

    #[Route('/{cid}/home.json', name: 'chamilo_core_course_home_json')]
    #[Entity('course', expr: 'repository.find(cid)')]
    public function indexJson(
        Request $request,
        CToolRepository $toolRepository,
        CShortcutRepository $shortcutRepository,
        ToolChain $toolChain,
        EntityManagerInterface $em
    ): Response {
        $requestData = json_decode($request->getContent(), true);
        // Sort behaviour
        if (!empty($requestData) && isset($requestData['toolItem'])) {
            $index = $requestData['index'];
            $toolItem = $requestData['toolItem'];
            $toolId = (int) $toolItem['ctool']['iid'];

            /** @var CTool $cTool */
            $cTool = $em->find(CTool::class, $toolId);

            if ($cTool) {
                $cTool->setPosition($index + 1);
                $em->persist($cTool);
                $em->flush();
            }
        }

        $course = $this->getCourse();
        $sessionId = $this->getSessionId();

        if (null === $course) {
            throw $this->createAccessDeniedException();
        }

        if (empty($sessionId)) {
            $this->denyAccessUnlessGranted(CourseVoter::VIEW, $course);
        }

        $session = $request->getSession();

        $userId = 0;
        /** @var ?User $user */
        $user = $this->getUser();
        if (null !== $user) {
            $userId = $user->getId();
        }

        $courseCode = $course->getCode();
        $courseId = $course->getId();

        if ($user && $user->hasRole('ROLE_INVITEE')) {
            $isInASession = $sessionId > 0;
            $isSubscribed = CourseManager::is_user_subscribed_in_course(
                $userId,
                $courseCode,
                $isInASession,
                $sessionId
            );

            if (!$isSubscribed) {
                throw $this->createAccessDeniedException();
            }
        }

        $courseSession = $this->getCourseSession();

        $isSpecialCourse = CourseManager::isSpecialCourse($courseId);

        if ($user && $isSpecialCourse && (isset($_GET['autoreg']) && 1 === (int) $_GET['autoreg']) &&
            CourseManager::subscribeUser($userId, $courseId, STUDENT)
        ) {
            $session->set('is_allowed_in_course', true);
        }

        $logInfo = [
            'tool' => 'course-main',
        ];
        Event::registerLog($logInfo);

        $qb = $toolRepository->getResourcesByCourse($course, $this->getSession());

        $qb->addSelect('tool');
        $qb->innerJoin('resource.tool', 'tool');
        $skipTools = ['course_tool', 'chat', 'notebook', 'wiki', 'course_homepage'];
        $qb->andWhere($qb->expr()->notIn('resource.name', $skipTools));
        $qb->addOrderBy('resource.position', 'ASC');

        $result = $qb->getQuery()->getResult();
        $tools = [];
        $isCourseTeacher = $this->isGranted('ROLE_CURRENT_COURSE_TEACHER');

        /** @var CTool $item */
        foreach ($result as $item) {
            $toolModel = $toolChain->getToolFromName($item->getTool()->getName());

            if (!$isCourseTeacher && 'admin' === $toolModel->getCategory()) {
                continue;
            }

            $tools[] = [
                'ctool' => $item,
                'tool' => $toolModel,
                'url' => $this->generateToolUrl($toolModel),
                'category' => $toolModel->getCategory(),
            ];
        }

        // Get session-career diagram
        $diagram = '';
        /*$allow = api_get_configuration_value('allow_career_diagram');
        if (true === $allow) {
            $htmlHeadXtra[] = api_get_js('jsplumb2.js');
            $extra = new ExtraFieldValue('session');
            $value = $extra->get_values_by_handler_and_field_variable(
                api_get_session_id(),
                'external_career_id'
            );

            if (!empty($value) && isset($value['value'])) {
                $careerId = $value['value'];
                $extraFieldValue = new ExtraFieldValue('career');
                $item = $extraFieldValue->get_item_id_from_field_variable_and_field_value(
                    'external_career_id',
                    $careerId,
                    false,
                    false,
                    false
                );

                if (!empty($item) && isset($item['item_id'])) {
                    $careerId = $item['item_id'];
                    $career = new Career();
                    $careerInfo = $career->get($careerId);
                    if (!empty($careerInfo)) {
                        $extraFieldValue = new ExtraFieldValue('career');
                        $item = $extraFieldValue->get_values_by_handler_and_field_variable(
                            $careerId,
                            'career_diagram',
                            false,
                            false,
                            0
                        );

                        if (!empty($item) && isset($item['value']) && !empty($item['value'])) {
                            // @var Graph $graph
                            $graph = UnserializeApi::unserialize('career', $item['value']);
                            $diagram = Career::renderDiagram($careerInfo, $graph);
                        }
                    }
                }
            }
        }*/

        // Deleting the objects
        $session->remove('toolgroup');
        $session->remove('_gid');
        $session->remove('oLP');
        $session->remove('lpobject');

        api_remove_in_gradebook();
        Exercise::cleanSessionVariables();

        $shortcuts = [];
        if (null !== $user) {
            $shortcutQuery = $shortcutRepository->getResources($course->getResourceNode());
            $shortcuts = $shortcutQuery->getQuery()->getResult();
        }
        $responseData = [
            'course' => $course,
            'session' => $courseSession,
            'shortcuts' => $shortcuts,
            'diagram' => $diagram,
            'tools' => $tools,
        ];

        $json = $this->serializer->serialize(
            $responseData,
            'json',
            [
                'groups' => ['course:read', 'ctool:read', 'tool:read', 'cshortcut:read'],
            ]
        );

        return new Response(
            $json,
            Response::HTTP_OK,
            [
                'Content-type' => 'application/json',
            ]
        );
    }

    /**
     * Redirects the page to a tool, following the tools settings.
     */
    #[Route('/{cid}/tool/{toolName}', name: 'chamilo_core_course_redirect_tool')]
    public function redirectTool(string $toolName, CToolRepository $repo, ToolChain $toolChain): RedirectResponse
    {
        /** @var CTool|null $tool */
        $tool = $repo->findOneBy([
            'name' => $toolName,
        ]);

        if (null === $tool) {
            throw new NotFoundHttpException($this->trans('Tool not found'));
        }

        $tool = $toolChain->getToolFromName($tool->getTool()->getName());
        $link = $tool->getLink();

        if (null === $this->getCourse()) {
            throw new NotFoundHttpException($this->trans('Course not found'));
        }

        if (strpos($link, 'nodeId')) {
            $nodeId = (string) $this->getCourse()->getResourceNode()->getId();
            $link = str_replace(':nodeId', $nodeId, $link);
        }

        $url = $link.'?'.$this->getCourseUrlQuery();

        return $this->redirect($url);
    }

    /*public function redirectToShortCut(string $toolName, CToolRepository $repo, ToolChain $toolChain): RedirectResponse
    {
        $tool = $repo->findOneBy([
            'name' => $toolName,
        ]);

        if (null === $tool) {
            throw new NotFoundHttpException($this->trans('Tool not found'));
        }

        $tool = $toolChain->getToolFromName($tool->getTool()->getName());
        $link = $tool->getLink();

        if (strpos($link, 'nodeId')) {
            $nodeId = (string) $this->getCourse()->getResourceNode()->getId();
            $link = str_replace(':nodeId', $nodeId, $link);
        }

        $url = $link.'?'.$this->getCourseUrlQuery();

        return $this->redirect($url);
    }*/

    /**
     * Edit configuration with given namespace.
     */
    #[Route('/{cid}/settings/{namespace}', name: 'chamilo_core_course_settings')]
    #[Entity('course', expr: 'repository.find(cid)')]
    public function updateSettings(
        Request $request,
        Course $course,
        string $namespace,
        SettingsCourseManager $manager,
        SettingsFormFactory $formFactory
    ): Response {
        $this->denyAccessUnlessGranted(CourseVoter::VIEW, $course);

        $schemaAlias = $manager->convertNameSpaceToService($namespace);
        $settings = $manager->load($namespace);

        $form = $formFactory->create($schemaAlias);

        $form->setData($settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $messageType = 'success';

            try {
                $manager->setCourse($course);
                $manager->save($form->getData());
                $message = $this->trans('Update');
            } catch (ValidatorException $validatorException) {
                $message = $this->trans($validatorException->getMessage());
                $messageType = 'error';
            }
            $this->addFlash($messageType, $message);

            if ($request->headers->has('referer')) {
                return $this->redirect($request->headers->get('referer'));
            }
        }

        $schemas = $manager->getSchemas();

        return $this->render(
            '@ChamiloCore/Course/settings.html.twig',
            [
                'course' => $course,
                'schemas' => $schemas,
                'settings' => $settings,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route('/{id}/about', name: 'chamilo_core_course_about')]
    public function about(
        Course $course,
        IllustrationRepository $illustrationRepository,
        CCourseDescriptionRepository $courseDescriptionRepository,
        EntityManagerInterface $em
    ): Response {
        $courseId = $course->getId();
        /** @var ?User $user */
        $user = $this->getUser();

        $fieldsRepo = $em->getRepository(ExtraField::class);
        /** @var TagRepository $tagRepo */
        $tagRepo = $em->getRepository(Tag::class);

        $courseDescriptions = $courseDescriptionRepository->getResourcesByCourse($course)->getQuery()->getResult();

        $courseValues = new ExtraFieldValue('course');

        $urlCourse = api_get_path(WEB_PATH).sprintf('course/%s/about', $courseId);
        $courseTeachers = $course->getTeachersSubscriptions();
        $teachersData = [];

        foreach ($courseTeachers as $teacherSubscription) {
            $teacher = $teacherSubscription->getUser();
            $userData = [
                'complete_name' => UserManager::formatUserFullName($teacher),
                'image' => $illustrationRepository->getIllustrationUrl($teacher),
                'diploma' => $teacher->getDiplomas(),
                'openarea' => $teacher->getOpenarea(),
            ];

            $teachersData[] = $userData;
        }
        /** @var ExtraField $tagField */
        $tagField = $fieldsRepo->findOneBy([
            'itemType' => ExtraField::COURSE_FIELD_TYPE,
            'variable' => 'tags',
        ]);

        $courseTags = [];
        if (null !== $tagField) {
            $courseTags = $tagRepo->getTagsByItem($tagField, $courseId);
        }

        $courseDescription = $courseObjectives = $courseTopics = $courseMethodology = '';
        $courseMaterial = $courseResources = $courseAssessment = '';
        $courseCustom = [];
        foreach ($courseDescriptions as $descriptionTool) {
            switch ($descriptionTool->getDescriptionType()) {
                case CCourseDescription::TYPE_DESCRIPTION:
                    $courseDescription = $descriptionTool->getContent();

                    break;
                case CCourseDescription::TYPE_OBJECTIVES:
                    $courseObjectives = $descriptionTool;

                    break;
                case CCourseDescription::TYPE_TOPICS:
                    $courseTopics = $descriptionTool;

                    break;
                case CCourseDescription::TYPE_METHODOLOGY:
                    $courseMethodology = $descriptionTool;

                    break;
                case CCourseDescription::TYPE_COURSE_MATERIAL:
                    $courseMaterial = $descriptionTool;

                    break;
                case CCourseDescription::TYPE_RESOURCES:
                    $courseResources = $descriptionTool;

                    break;
                case CCourseDescription::TYPE_ASSESSMENT:
                    $courseAssessment = $descriptionTool;

                    break;
                case CCourseDescription::TYPE_CUSTOM:
                    $courseCustom[] = $descriptionTool;

                    break;
            }
        }

        $topics = [
            'objectives' => $courseObjectives,
            'topics' => $courseTopics,
            'methodology' => $courseMethodology,
            'material' => $courseMaterial,
            'resources' => $courseResources,
            'assessment' => $courseAssessment,
            'custom' => array_reverse($courseCustom),
        ];

        $subscriptionUser = false;

        if ($user) {
            $subscriptionUser = CourseManager::is_user_subscribed_in_course($user->getId(), $course->getCode());
        }

        /*$allowSubscribe = false;
        if ($course->getSubscribe() || api_is_platform_admin()) {
            $allowSubscribe = true;
        }
        $plugin = \BuyCoursesPlugin::create();
        $checker = $plugin->isEnabled();
        $courseIsPremium = null;
        if ($checker) {
            $courseIsPremium = $plugin->getItemByProduct(
                $courseId,
                \BuyCoursesPlugin::PRODUCT_TYPE_COURSE
            );
        }*/

        $image = Container::getIllustrationRepository()->getIllustrationUrl($course, 'course_picture_medium');

        $params = [
            'course' => $course,
            'description' => $courseDescription,
            'image' => $image,
            'syllabus' => $topics,
            'tags' => $courseTags,
            'teachers' => $teachersData,
            'extra_fields' => $courseValues->getAllValuesForAnItem(
                $course->getId(),
                null,
                true
            ),
            'subscription' => $subscriptionUser,
            'url' => '',
            'is_premium' => '',
            'token' => '',
        ];

        $metaInfo = '<meta property="og:url" content="'.$urlCourse.'" />';
        $metaInfo .= '<meta property="og:type" content="website" />';
        $metaInfo .= '<meta property="og:title" content="'.$course->getTitle().'" />';
        $metaInfo .= '<meta property="og:description" content="'.strip_tags($courseDescription).'" />';
        $metaInfo .= '<meta property="og:image" content="'.$image.'" />';

        $htmlHeadXtra[] = $metaInfo;
        $htmlHeadXtra[] = api_get_asset('readmore-js/readmore.js');

        return $this->render('@ChamiloCore/Course/about.html.twig', $params);
    }

    #[Route('/{id}/welcome', name: 'chamilo_core_course_welcome')]
    public function welcome(Course $course): Response
    {
        return $this->render('@ChamiloCore/Course/welcome.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/{id}/getToolIntro', name: 'chamilo_core_course_gettoolintro')]
    public function getToolIntro(Request $request, Course $course, EntityManagerInterface $em): Response
    {
        $sessionId = (int) $request->get('sid');
        error_log('Session id -> '.$sessionId);

        //$session = $this->getSession();
        $responseData = [];
        $ctoolRepo = $em->getRepository(CTool::class);
        $sessionRepo = $em->getRepository(Session::class);
        $createInSession = false;

        $session = null;
        if (!empty($sessionId)) {
            $session = $sessionRepo->find($sessionId);
        }

        $qb = $em->createQueryBuilder();
        $query = $qb->select('ct')
            ->from('Chamilo\CourseBundle\Entity\CTool', 'ct')
            ->where('ct.course = :c_id')
            ->andWhere('ct.name = :name')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('ct.session', ':session_id'),
                    $qb->expr()->isNull('ct.session')
                )
            )
            ->setParameters([
                'c_id' => $course->getId(),
                'name' => 'course_homepage',
                'session_id' => 0,
            ])
            ->getQuery()
        ;

        $ctool = $query->getOneOrNullResult();

        if ($session) {
            error_log('Session actual -> '.$session->getId());
            error_log('Course actual -> '.$course->getId());
            $ctoolSession = $ctoolRepo->findOneBy(['name' => 'course_homepage', 'course' => $course, 'session' => $session]);
            if (!$ctoolSession) {
                $createInSession = true;
            } else {
                $createInSession = false;
                $ctool = $ctoolSession;
            }
        }

        if ($ctool) {
            $ctoolintroRepo = $em->getRepository(CToolIntro::class);
            /** @var CToolIntro $ctoolintro */
            $ctoolintro = $ctoolintroRepo->findOneBy(['courseTool' => $ctool]);
            if ($ctoolintro) {
                $responseData = [
                    'iid' => $ctoolintro->getIid(),
                    'introText' => $ctoolintro->getIntroText(),
                    'createInSession' => $createInSession,
                    'cToolId' => $ctool->getIid(),
                ];
            }
            $responseData['c_tool'] = $ctool;
        }

        return new JsonResponse($responseData);
    }

    #[Route('/{id}/addToolIntro', name: 'chamilo_core_course_addtoolintro')]
    public function addToolIntro(Request $request, Course $course, EntityManagerInterface $em): Response
    {
        $data = $request->getContent();
        $data = json_decode($data);
        $ctoolintroId = $data->iid;
        $sessionId = $data->sid;

        $sessionRepo = $em->getRepository(Session::class);
        $session = null;
        if (!empty($sessionId)) {
            $session = $sessionRepo->find($sessionId);
        }

        $ctool = $em->getRepository(CTool::class);
        $check = $ctool->findOneBy(['name' => 'course_homepage', 'session' => $session]);
        if (!$check) {
            $toolRepo = $em->getRepository(Tool::class);
            $toolEntity = $toolRepo->findOneBy(['name' => 'course_homepage']);
            $courseTool = (new CTool())
                ->setTool($toolEntity)
                ->setName('course_homepage')
                ->setCourse($course)
                ->setPosition(1)
                ->setVisibility(true)
                ->setParent($course)
                ->setCreator($course->getCreator())
                ->setSession($session)
                ->addCourseLink($course)
            ;
            $em->persist($courseTool);
            $em->flush();
            if ($courseTool && !empty($ctoolintroId)) {
                $ctoolintroRepo = Container::getToolIntroRepository();
                /** @var CToolIntro $ctoolintro */
                $ctoolintro = $ctoolintroRepo->find($ctoolintroId);
                $ctoolintro->setCourseTool($courseTool);
                $ctoolintroRepo->update($ctoolintro);
            }
        }
        $responseData = [];
        $json = $this->serializer->serialize(
            $responseData,
            'json',
            [
                'groups' => ['course:read', 'ctool:read', 'tool:read', 'cshortcut:read'],
            ]
        );

        return new JsonResponse($responseData);
    }

    private function autoLaunch(): void
    {
        $autoLaunchWarning = '';
        $showAutoLaunchLpWarning = false;
        $course_id = api_get_course_int_id();
        $lpAutoLaunch = api_get_course_setting('enable_lp_auto_launch');
        $session_id = api_get_session_id();
        $allowAutoLaunchForCourseAdmins =
            api_is_platform_admin() ||
            api_is_allowed_to_edit(true, true) ||
            api_is_coach();

        if (!empty($lpAutoLaunch)) {
            if (2 === $lpAutoLaunch) {
                // LP list
                if ($allowAutoLaunchForCourseAdmins) {
                    $showAutoLaunchLpWarning = true;
                } else {
                    $session_key = 'lp_autolaunch_'.$session_id.'_'.$course_id.'_'.api_get_user_id();
                    if (!isset($_SESSION[$session_key])) {
                        // Redirecting to the LP
                        $url = api_get_path(WEB_CODE_PATH).'lp/lp_controller.php?'.api_get_cidreq();
                        $_SESSION[$session_key] = true;
                        header(sprintf('Location: %s', $url));
                        exit;
                    }
                }
            } else {
                $lp_table = Database::get_course_table(TABLE_LP_MAIN);
                $condition = '';
                if (!empty($session_id)) {
                    $condition = api_get_session_condition($session_id);
                    $sql = "SELECT id FROM {$lp_table}
                            WHERE c_id = {$course_id} AND autolaunch = 1 {$condition}
                            LIMIT 1";
                    $result = Database::query($sql);
                    // If we found nothing in the session we just called the session_id =  0 autolaunch
                    if (0 === Database::num_rows($result)) {
                        $condition = '';
                    }
                }

                $sql = "SELECT iid FROM {$lp_table}
                        WHERE c_id = {$course_id} AND autolaunch = 1 {$condition}
                        LIMIT 1";
                $result = Database::query($sql);
                if (Database::num_rows($result) > 0) {
                    $lp_data = Database::fetch_array($result);
                    if (!empty($lp_data['iid'])) {
                        if ($allowAutoLaunchForCourseAdmins) {
                            $showAutoLaunchLpWarning = true;
                        } else {
                            $session_key = 'lp_autolaunch_'.$session_id.'_'.api_get_course_int_id().'_'.api_get_user_id();
                            if (!isset($_SESSION[$session_key])) {
                                // Redirecting to the LP
                                $url = api_get_path(WEB_CODE_PATH).
                                    'lp/lp_controller.php?'.api_get_cidreq().'&action=view&lp_id='.$lp_data['iid'];

                                $_SESSION[$session_key] = true;
                                header(sprintf('Location: %s', $url));
                                exit;
                            }
                        }
                    }
                }
            }
        }

        if ($showAutoLaunchLpWarning) {
            $autoLaunchWarning = get_lang(
                'The learning path auto-launch setting is ON. When learners enter this course, they will be automatically redirected to the learning path marked as auto-launch.'
            );
        }

        $forumAutoLaunch = (int) api_get_course_setting('enable_forum_auto_launch');
        if (1 === $forumAutoLaunch) {
            if ($allowAutoLaunchForCourseAdmins) {
                if (empty($autoLaunchWarning)) {
                    $autoLaunchWarning = get_lang(
                        "The forum's auto-launch setting is on. Students will be redirected to the forum tool when entering this course."
                    );
                }
            } else {
                $url = api_get_path(WEB_CODE_PATH).'forum/index.php?'.api_get_cidreq();
                header(sprintf('Location: %s', $url));
                exit;
            }
        }

        if (('true' === api_get_setting('exercise.allow_exercise_auto_launch'))) {
            $exerciseAutoLaunch = (int) api_get_course_setting('enable_exercise_auto_launch');
            if (2 === $exerciseAutoLaunch) {
                if ($allowAutoLaunchForCourseAdmins) {
                    if (empty($autoLaunchWarning)) {
                        $autoLaunchWarning = get_lang(
                            'TheExerciseAutoLaunchSettingIsONStudentsWillBeRedirectToTheExerciseList'
                        );
                    }
                } else {
                    // Redirecting to the document
                    $url = api_get_path(WEB_CODE_PATH).'exercise/exercise.php?'.api_get_cidreq();
                    header(sprintf('Location: %s', $url));
                    exit;
                }
            } elseif (1 === $exerciseAutoLaunch) {
                if ($allowAutoLaunchForCourseAdmins) {
                    if (empty($autoLaunchWarning)) {
                        $autoLaunchWarning = get_lang(
                            'TheExerciseAutoLaunchSettingIsONStudentsWillBeRedirectToAnSpecificExercise'
                        );
                    }
                } else {
                    // Redirecting to an exercise
                    $table = Database::get_course_table(TABLE_QUIZ_TEST);
                    $condition = '';
                    if (!empty($session_id)) {
                        $condition = api_get_session_condition($session_id);
                        $sql = "SELECT iid FROM {$table}
                                WHERE c_id = {$course_id} AND autolaunch = 1 {$condition}
                                LIMIT 1";
                        $result = Database::query($sql);
                        // If we found nothing in the session we just called the session_id = 0 autolaunch
                        if (0 === Database::num_rows($result)) {
                            $condition = '';
                        }
                    }

                    $sql = "SELECT iid FROM {$table}
                            WHERE c_id = {$course_id} AND autolaunch = 1 {$condition}
                            LIMIT 1";
                    $result = Database::query($sql);
                    if (Database::num_rows($result) > 0) {
                        $row = Database::fetch_array($result);
                        $exerciseId = $row['iid'];
                        $url = api_get_path(WEB_CODE_PATH).
                            'exercise/overview.php?exerciseId='.$exerciseId.'&'.api_get_cidreq();
                        header(sprintf('Location: %s', $url));
                        exit;
                    }
                }
            }
        }

        $documentAutoLaunch = (int) api_get_course_setting('enable_document_auto_launch');
        if (1 === $documentAutoLaunch) {
            if ($allowAutoLaunchForCourseAdmins) {
                if (empty($autoLaunchWarning)) {
                    $autoLaunchWarning = get_lang(
                        'The document auto-launch feature configuration is enabled. Learners will be automatically redirected to document tool.'
                    );
                }
            } else {
                // Redirecting to the document
                $url = api_get_path(WEB_CODE_PATH).'document/document.php?'.api_get_cidreq();
                header("Location: $url");
                exit;
            }
        }

        /*  SWITCH TO A DIFFERENT HOMEPAGE VIEW
         the setting homepage_view is adjustable through
         the platform administration section */
        if (!empty($autoLaunchWarning)) {
            $this->addFlash(
                'warning',
                Display::return_message(
                    $autoLaunchWarning,
                    'warning'
                )
            );
        }
    }

    private function generateToolUrl(AbstractTool $tool): string
    {
        $link = $tool->getLink();
        $course = $this->getCourse();

        if (strpos($link, 'nodeId')) {
            $nodeId = (string) $course->getResourceNode()->getId();
            $link = str_replace(':nodeId', $nodeId, $link);
        }

        return $link.'?'.$this->getCourseUrlQuery();
    }
}
