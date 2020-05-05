<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Controller;

use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Entity\ExtraField;
use Chamilo\CoreBundle\Framework\Container;
use Chamilo\CourseBundle\Entity\CCourseDescription;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use UserManager;

/**
 * Class CourseController.
 *
 * @Route("/course")
 */
class CourseController extends AbstractController
{
    /**
     * Redirects legacy /courses/ABC/index.php to /courses/1/ (where 1 is the course id) see CourseHomeController.
     *
     * @Route("/{courseCode}/index.php", name="chamilo_core_course_home_redirect")
     *
     * @Entity("course", expr="repository.findOneByCode(courseCode)")
     */
    public function homeRedirectAction(Course $course): Response
    {
        return $this->redirectToRoute('chamilo_core_course_home', ['cid' => $course->getId()]);
    }

    /**
     * @Route("/{cid}/welcome", name="chamilo_core_course_welcome")
     *
     * @Entity("course", expr="repository.find(cid)")
     */
    public function welcomeAction(Course $course): Response
    {
        return $this->render('@ChamiloCore/Course/welcome.html.twig', ['course' => $course]);
    }

    /**
     * @Route("/{cid}/about", name="chamilo_core_course_about")
     *
     * @Entity("course", expr="repository.find(cid)")
     */
    public function aboutAction(Course $course): Response
    {
        $courseId = $course->getId();
        $userId = $this->getUser()->getId();

        $em = $this->getDoctrine()->getManager();

        $fieldsRepo = $em->getRepository('ChamiloCoreBundle:ExtraField');
        $fieldTagsRepo = $em->getRepository('ChamiloCoreBundle:ExtraFieldRelTag');

        /** @var CCourseDescription $courseDescription */
        $courseDescriptionTools = $em->getRepository('ChamiloCourseBundle:CCourseDescription')
            ->findBy(
                [
                    'cId' => $course->getId(),
                    'sessionId' => 0,
                ],
                [
                    'id' => 'DESC',
                    'descriptionType' => 'ASC',
                ]
            );

        $courseValues = new \ExtraFieldValue('course');

        $urlCourse = api_get_path(WEB_PATH)."course/$courseId/about";
        $courseTeachers = $course->getTeachers();
        $teachersData = [];

        foreach ($courseTeachers as $teacherSubscription) {
            $teacher = $teacherSubscription->getUser();
            $userData = [
                'complete_name' => UserManager::formatUserFullName($teacher),
                'image' => UserManager::getUserPicture(
                    $teacher->getId(),
                    USER_IMAGE_SIZE_ORIGINAL
                ),
                'diploma' => $teacher->getDiplomas(),
                'openarea' => $teacher->getOpenarea(),
            ];

            $teachersData[] = $userData;
        }

        $tagField = $fieldsRepo->findOneBy([
            'extraFieldType' => ExtraField::COURSE_FIELD_TYPE,
            'variable' => 'tags',
        ]);

        $courseTags = [];

        if (null !== $tagField) {
            $courseTags = $fieldTagsRepo->getTags($tagField, $courseId);
        }

        $courseDescription = $courseObjectives = $courseTopics = $courseMethodology = $courseMaterial = $courseResources = $courseAssessment = '';
        $courseCustom = [];
        foreach ($courseDescriptionTools as $descriptionTool) {
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

        $subscriptionUser = \CourseManager::is_user_subscribed_in_course($userId, $course->getCode());

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
        ];

        $metaInfo = '<meta property="og:url" content="'.$urlCourse.'" />';
        $metaInfo .= '<meta property="og:type" content="website" />';
        $metaInfo .= '<meta property="og:title" content="'.$course->getTitle().'" />';
        $metaInfo .= '<meta property="og:description" content="'.strip_tags($courseDescription).'" />';
        $metaInfo .= '<meta property="og:image" content="'.$image.'" />';

        $htmlHeadXtra[] = $metaInfo;
        $htmlHeadXtra[] = api_get_asset('readmore-js/readmore.js');

        return $this->render('@ChamiloCore/Course/about.html.twig', [$params]);
    }
}
