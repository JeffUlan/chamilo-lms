<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Framework;

use Chamilo\CoreBundle\Component\Editor\Editor;
use Chamilo\CoreBundle\Manager\SettingsManager;
use Chamilo\CoreBundle\Repository\CourseCategoryRepository;
use Chamilo\CoreBundle\Repository\Node\AccessUrlRepository;
use Chamilo\CoreBundle\Repository\Node\CourseRepository;
use Chamilo\CoreBundle\Repository\Node\IllustrationRepository;
use Chamilo\CoreBundle\Repository\Node\UserRepository;
use Chamilo\CoreBundle\Repository\SequenceRepository;
use Chamilo\CoreBundle\Repository\SequenceResourceRepository;
use Chamilo\CoreBundle\Repository\SessionRepository;
use Chamilo\CoreBundle\ToolChain;
use Chamilo\CourseBundle\Repository\CAnnouncementAttachmentRepository;
use Chamilo\CourseBundle\Repository\CAnnouncementRepository;
use Chamilo\CourseBundle\Repository\CAttendanceRepository;
use Chamilo\CourseBundle\Repository\CBlogRepository;
use Chamilo\CourseBundle\Repository\CCalendarEventAttachmentRepository;
use Chamilo\CourseBundle\Repository\CCalendarEventRepository;
use Chamilo\CourseBundle\Repository\CCourseDescriptionRepository;
use Chamilo\CourseBundle\Repository\CDocumentRepository;
use Chamilo\CourseBundle\Repository\CExerciseCategoryRepository;
use Chamilo\CourseBundle\Repository\CForumAttachmentRepository;
use Chamilo\CourseBundle\Repository\CForumCategoryRepository;
use Chamilo\CourseBundle\Repository\CForumForumRepository;
use Chamilo\CourseBundle\Repository\CForumPostRepository;
use Chamilo\CourseBundle\Repository\CForumThreadRepository;
use Chamilo\CourseBundle\Repository\CGlossaryRepository;
use Chamilo\CourseBundle\Repository\CGroupCategoryRepository;
use Chamilo\CourseBundle\Repository\CGroupRepository;
use Chamilo\CourseBundle\Repository\CLinkCategoryRepository;
use Chamilo\CourseBundle\Repository\CLinkRepository;
use Chamilo\CourseBundle\Repository\CLpCategoryRepository;
use Chamilo\CourseBundle\Repository\CLpRepository;
use Chamilo\CourseBundle\Repository\CNotebookRepository;
use Chamilo\CourseBundle\Repository\CQuizQuestionCategoryRepository;
use Chamilo\CourseBundle\Repository\CQuizQuestionRepository;
use Chamilo\CourseBundle\Repository\CQuizRepository;
use Chamilo\CourseBundle\Repository\CShortcutRepository;
use Chamilo\CourseBundle\Repository\CStudentPublicationAssignmentRepository;
use Chamilo\CourseBundle\Repository\CStudentPublicationCommentRepository;
use Chamilo\CourseBundle\Repository\CStudentPublicationCorrectionRepository;
use Chamilo\CourseBundle\Repository\CStudentPublicationRepository;
use Chamilo\CourseBundle\Repository\CSurveyRepository;
use Chamilo\CourseBundle\Repository\CThematicAdvanceRepository;
use Chamilo\CourseBundle\Repository\CThematicPlanRepository;
use Chamilo\CourseBundle\Repository\CThematicRepository;
use Chamilo\CourseBundle\Repository\CWikiRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class Container
 * This class is a way to access Symfony2 services in legacy Chamilo code.
 */
class Container
{
    /**
     * @var ContainerInterface
     */
    public static $container;
    public static $session;
    public static $request;
    public static $configuration;
    public static $environment;
    public static $urlGenerator;
    public static $checker;
    /** @var TranslatorInterface */
    public static $translator;
    public static $mailer;
    public static $template;

    public static $rootDir;
    public static $logDir;
    public static $tempDir;
    public static $dataDir;
    public static $courseDir;
    public static $assets;
    public static $htmlEditor;
    public static $twig;
    public static $roles;
    /** @var string */
    public static $legacyTemplate = '@ChamiloCore/Layout/layout_one_col.html.twig';
    //private static $settingsManager;
    //private static $userManager;
    //private static $siteManager;

    /**
     * @param ContainerInterface $container
     */
    public static function setContainer($container)
    {
        self::$container = $container;
    }

    /**
     * @param string $parameter
     */
    public static function getParameter($parameter)
    {
        if (self::$container->hasParameter($parameter)) {
            return self::$container->getParameter($parameter);
        }

        return false;
    }

    /**
     * @return string
     */
    public static function getEnvironment()
    {
        return self::$container->get('kernel')->getEnvironment();
    }

    /**
     * @return RoleHierarchy
     */
    public static function getRoles()
    {
        return self::$container->get('security.role_hierarchy');
    }

    /**
     * @return string
     */
    public static function getLogDir()
    {
        return self::$container->get('kernel')->getLogDir();
    }

    /**
     * @return string
     */
    public static function getCacheDir()
    {
        return self::$container->get('kernel')->getCacheDir().'/';
    }

    /**
     * @return string
     */
    public static function getProjectDir()
    {
        if (isset(self::$container)) {
            return self::$container->get('kernel')->getProjectDir().'/';
        }

        return str_replace('\\', '/', realpath(__DIR__.'/../../../')).'/';
    }

    /**
     * @return string
     */
    public static function isInstalled()
    {
        return self::$container->get('kernel')->isInstalled();
    }

    /**
     * @return Environment
     */
    public static function getTwig()
    {
        return self::$container->get('twig');
    }

    /**
     * @return Environment
     */
    public static function getTemplating()
    {
        return self::$container->get('twig');
    }

    /**
     * @return Editor
     */
    public static function getHtmlEditor()
    {
        return self::$container->get('chamilo_core.html_editor');
    }

    /**
     * @return object|Request
     */
    public static function getRequest()
    {
        if (null === self::$container) {
            return null;
        }

        if (!empty(self::$request)) {
            return self::$request;
        }

        return self::$container->get('request_stack');
    }

    /**
     * @param Request $request
     */
    public static function setRequest($request)
    {
        self::$request = $request;
    }

    /**
     * @return Session|false
     */
    public static function getSession()
    {
        if (self::$container && self::$container->has('session')) {
            return self::$container->get('session');
        }

        return false;
    }

    /**
     * @return AuthorizationChecker
     */
    public static function getAuthorizationChecker()
    {
        return self::$container->get('security.authorization_checker');
    }

    /**
     * @return object|\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage
     */
    public static function getTokenStorage()
    {
        return self::$container->get('security.token_storage');
    }

    /**
     * @return TranslatorInterface
     */
    public static function getTranslator()
    {
        if (isset(self::$translator)) {
            return self::$translator;
        }

        if (self::$container) {
            return self::$container->get('translator');
        }

        return false;
    }

    public static function getMailer()
    {
        return self::$container->get('Symfony\Component\Mailer\Mailer');
    }

    public static function getSettingsManager(): SettingsManager
    {
        return self::$container->get('chamilo.settings.manager');
    }

    public static function getCourseSettingsManager(): \Chamilo\CourseBundle\Manager\SettingsManager
    {
        return self::$container->get('Chamilo\CourseBundle\Manager\SettingsManager');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEntityManager()
    {
        return \Database::getManager();
    }

    public static function getUserManager()
    {
        return self::$container->get(UserRepository::class);
    }

    public static function getAttendanceRepository(): CAttendanceRepository
    {
        return self::$container->get(CAttendanceRepository::class);
    }

    public static function getAnnouncementRepository(): CAnnouncementRepository
    {
        return self::$container->get(CAnnouncementRepository::class);
    }

    public static function getAccessUrlRepository(): AccessUrlRepository
    {
        return self::$container->get(AccessUrlRepository::class);
    }

    public static function getAnnouncementAttachmentRepository(): CAnnouncementAttachmentRepository
    {
        return self::$container->get(CAnnouncementAttachmentRepository::class);
    }

    public static function getCourseRepository(): CourseRepository
    {
        return self::$container->get(CourseRepository::class);
    }

    public static function getSessionRepository(): SessionRepository
    {
        return self::$container->get(SessionRepository::class);
    }

    public static function getCourseCategoryRepository(): CourseCategoryRepository
    {
        return self::$container->get(CourseCategoryRepository::class);
    }

    public static function getCourseDescriptionRepository(): CCourseDescriptionRepository
    {
        return self::$container->get(CCourseDescriptionRepository::class);
    }

    public static function getGlossaryRepository(): CGlossaryRepository
    {
        return self::$container->get(CGlossaryRepository::class);
    }

    public static function getCalendarEventRepository(): CCalendarEventRepository
    {
        return self::$container->get(CCalendarEventRepository::class);
    }

    public static function getCalendarEventAttachmentRepository(): CCalendarEventAttachmentRepository
    {
        return self::$container->get(CCalendarEventAttachmentRepository::class);
    }

    public static function getDocumentRepository(): CDocumentRepository
    {
        return self::$container->get(CDocumentRepository::class);
    }

    public static function getQuizRepository(): CQuizRepository
    {
        return self::$container->get(CQuizRepository::class);
    }

    public static function getExerciseCategoryRepository(): CExerciseCategoryRepository
    {
        return self::$container->get(CExerciseCategoryRepository::class);
    }

    public static function getForumRepository(): CForumForumRepository
    {
        return self::$container->get(CForumForumRepository::class);
    }

    public static function getForumCategoryRepository(): CForumCategoryRepository
    {
        return self::$container->get(CForumCategoryRepository::class);
    }

    public static function getForumPostRepository(): CForumPostRepository
    {
        return self::$container->get(CForumPostRepository::class);
    }

    public static function getForumAttachmentRepository(): CForumAttachmentRepository
    {
        return self::$container->get(CForumAttachmentRepository::class);
    }

    public static function getForumThreadRepository(): CForumThreadRepository
    {
        return self::$container->get(CForumThreadRepository::class);
    }

    public static function getGroupRepository(): CGroupRepository
    {
        return self::$container->get(CGroupRepository::class);
    }

    public static function getGroupCategoryRepository(): CGroupCategoryRepository
    {
        return self::$container->get(CGroupCategoryRepository::class);
    }

    public static function getQuestionRepository(): CQuizQuestionRepository
    {
        return self::$container->get(CQuizQuestionRepository::class);
    }

    public static function getQuestionCategoryRepository(): CQuizQuestionCategoryRepository
    {
        return self::$container->get(CQuizQuestionCategoryRepository::class);
    }

    public static function getLinkRepository(): CLinkRepository
    {
        return self::$container->get(CLinkRepository::class);
    }

    public static function getLinkCategoryRepository(): CLinkCategoryRepository
    {
        return self::$container->get(CLinkCategoryRepository::class);
    }

    public static function getLpRepository(): CLpRepository
    {
        return self::$container->get(CLpRepository::class);
    }

    public static function getLpCategoryRepository(): CLpCategoryRepository
    {
        return self::$container->get(CLpCategoryRepository::class);
    }

    public static function getNotebookRepository(): CNotebookRepository
    {
        return self::$container->get(CNotebookRepository::class);
    }

    public static function getUserRepository(): UserRepository
    {
        return self::$container->get(UserRepository::class);
    }

    public static function getIllustrationRepository(): IllustrationRepository
    {
        return self::$container->get(IllustrationRepository::class);
    }

    public static function getShortcutRepository(): CShortcutRepository
    {
        return self::$container->get(CShortcutRepository::class);
    }

    public static function getStudentPublicationRepository(): CStudentPublicationRepository
    {
        return self::$container->get(CStudentPublicationRepository::class);
    }

    public static function getStudentPublicationAssignmentRepository(): CStudentPublicationAssignmentRepository
    {
        return self::$container->get(CStudentPublicationAssignmentRepository::class);
    }

    public static function getStudentPublicationCommentRepository(): CStudentPublicationCommentRepository
    {
        return self::$container->get(CStudentPublicationCommentRepository::class);
    }

    public static function getStudentPublicationCorrectionRepository(): CStudentPublicationCorrectionRepository
    {
        return self::$container->get(CStudentPublicationCorrectionRepository::class);
    }

    public static function getSequenceResourceRepository(): SequenceResourceRepository
    {
        return self::$container->get(SequenceResourceRepository::class);
    }

    public static function getSequenceRepository(): SequenceRepository
    {
        return self::$container->get(SequenceRepository::class);
    }

    public static function getSurveyRepository(): CSurveyRepository
    {
        return self::$container->get(CSurveyRepository::class);
    }

    public static function getThematicRepository(): CThematicRepository
    {
        return self::$container->get(CThematicRepository::class);
    }

    public static function getThematicPlanRepository(): CThematicPlanRepository
    {
        return self::$container->get(CThematicPlanRepository::class);
    }

    public static function getThematicAdvanceRepository(): CThematicAdvanceRepository
    {
        return self::$container->get(CThematicAdvanceRepository::class);
    }

    public static function getBlogRepository(): CBlogRepository
    {
        return self::$container->get(CBlogRepository::class);
    }

    public static function getWikiRepository(): CBlogRepository
    {
        return self::$container->get(CWikiRepository::class);
    }

    /**
     * @return \Symfony\Component\Form\FormFactory
     */
    public static function getFormFactory()
    {
        return self::$container->get('form.factory');
    }

    /**
     * @param string $message
     * @param string $type    error|success|warning|danger
     */
    public static function addFlash($message, $type = 'success')
    {
        $session = self::getSession();
        $session->getFlashBag()->add($type, $message);
    }

    /**
     * @return Router
     */
    public static function getRouter()
    {
        return self::$container->get('router');
    }

    /**
     * @return ToolChain
     */
    public static function getToolChain()
    {
        return self::$container->get(ToolChain::class);
    }

    /**
     * @param ContainerInterface $container
     * @param bool               $setSession
     */
    public static function setLegacyServices($container, $setSession = true)
    {
        \Database::setConnection($container->get('doctrine.dbal.default_connection'));
        $em = $container->get('doctrine.orm.entity_manager');
        \Database::setManager($em);
        \CourseManager::setEntityManager($em);
        \CourseManager::setCourseSettingsManager($container->get('Chamilo\CourseBundle\Manager\SettingsManager'));
        // Setting course tool chain (in order to create tools to a course)
        \CourseManager::setToolList($container->get(ToolChain::class));
        if ($setSession) {
            self::$session = $container->get('session');
        }
    }
}
