<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class IndexController
 * author Julio Montoya <gugli100@gmail.com>.
 */
class IndexController extends BaseController
{
    /**
     * The Chamilo index home page.
     *
     * @Route("/", name="home", methods={"GET", "POST"}, options={"expose"=true})
     */
    public function indexAction(SerializerInterface $serializer): Response
    {
        return $this->render(
            '@ChamiloCore/Index/vue.html.twig',
            [
                'content' => '',
            ]
        );
    }

    public function resources(SerializerInterface $serializer): Response
    {
        return $this->render(
            '@ChamiloCore/Index/vue.html.twig',
            [
                'content' => '',
            ]
        );
    }

    /**
     * Toggle the student view action.
     *
     * @Route("/toggle_student_view", methods={"GET"})
     *
     * @Security("has_role('ROLE_TEACHER')")
     */
    public function toggleStudentViewAction(Request $request): Response
    {
        if (!api_is_allowed_to_edit(false, false, false, false)) {
            return '';
        }
        $studentView = $request->getSession()->get('studentview');
        if (empty($studentView) || 'studentview' === $studentView) {
            $request->getSession()->set('studentview', 'teacherview');

            return 'teacherview';
        } else {
            $request->getSession()->set('studentview', 'studentview');

            return 'studentview';
        }
    }
}
