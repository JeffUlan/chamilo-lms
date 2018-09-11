<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Controller;

use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Form\Type\CourseType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CourseController.
 *
 * @Route("/course")
 *
 * @package Chamilo\CoreBundle\Controller
 */
class CourseController extends Controller
{
    /**
     * @Route("/add")
     *
     * @Security("has_role('ROLE_TEACHER')")
     *
     * @Template
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        $form = $this->createForm(new CourseType());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $course = $form->getData();
            $em->persist($course);
            $em->flush();

            $this->addFlash('sonata_flash_success', 'Course created');

            return $this->redirectToRoute(
                'chamilo_core_course_welcome',
                ['course' => $course]
            );
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/welcome/{course}")
     * @ParamConverter(
     *      "course",
     *      class="ChamiloCoreBundle:Course",
     *      options={"repository_method" = "findOneByCode"}
     * )
     * @Template
     */
    public function welcomeAction(Course $course)
    {
        return ['course' => $course];
    }
}
