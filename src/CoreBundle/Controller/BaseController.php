<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Controller;

use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Entity\Session;
use Knp\Menu\FactoryInterface as MenuFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Each entity controller must extends this class.
 *
 * @abstract
 */
abstract class BaseController extends AbstractController
{
    /**
     * @return NotFoundHttpException
     */
    public function abort()
    {
        return new NotFoundHttpException();
    }

    /**
     * Translator shortcut.
     *
     * @param string $variable
     *
     * @return string
     */
    public function trans($variable)
    {
        return $this->container->get('translator')->trans($variable);
    }

    /**
     * @return MenuFactoryInterface
     */
    public function getMenuFactory()
    {
        return $this->container->get('knp_menu.factory');
    }

    /**
     * Gets the current Chamilo course based in the "_real_cid" session variable.
     *
     * @return Course
     */
    public function getCourse()
    {
        $courseId = $this->getRequest()->getSession()->get('_real_cid', 0);

        return $this->getDoctrine()->getManager()->find('ChamiloCoreBundle:Course', $courseId);
    }

    /**
     * Gets the current Chamilo session based in the "id_session" $_SESSION variable.
     *
     * @return Session|null
     */
    public function getCourseSession()
    {
        $sessionId = $this->getRequest()->getSession()->get('id_session', 0);

        if (empty($sessionId)) {
            return null;
        }

        return $this->getDoctrine()->getManager()->find('ChamiloCoreBundle:Session', $sessionId);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request|null
     */
    public function getRequest()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        return $request;
    }
}
