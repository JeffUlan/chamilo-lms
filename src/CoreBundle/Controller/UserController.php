<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Controller;

use Chamilo\ThemeBundle\Model\UserInterface;
use Chamilo\UserBundle\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 *
 * @Route("/user")
 *
 * @author Julio Montoya <gugli100@gmail.com>
 */
class UserController extends BaseController
{
    /**
     * @Route("/{username}", methods={"GET"}, name="chamilo_core_user_profile")
     *
     * @param string $username
     */
    public function profileAction($username, UserRepository $userRepository)
    {
        $user = $userRepository->findByUsername($username);

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw $this->createAccessDeniedException('This user does not have access to this section');
        }

        return $this->render('@ChamiloCore/User/profile.html.twig', ['user' => $user]);
    }
}
