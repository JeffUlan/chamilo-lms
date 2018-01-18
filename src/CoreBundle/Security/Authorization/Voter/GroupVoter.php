<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Security\Authorization\Voter;

use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CourseBundle\Entity\CGroupInfo;
use Chamilo\CoreBundle\Entity\Manager\CourseManager;
use Chamilo\CourseBundle\Entity\Manager\GroupManager;
use Chamilo\CoreBundle\Entity\Session;
use Chamilo\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class GroupVoter
 * @package Chamilo\CoreBundle\Security\Authorization\Voter
 */
class GroupVoter extends Voter
{
    const VIEW = 'VIEW';
    const EDIT = 'EDIT';
    const DELETE = 'DELETE';

    private $entityManager;
    private $courseManager;
    private $groupManager;
    private $container;

    /**
     * @param EntityManager $entityManager
     * @param CourseManager $courseManager
     * @param GroupManager $groupManager ,
     * @param ContainerInterface $container
     */
    public function __construct(
        EntityManager $entityManager,
        CourseManager $courseManager,
        GroupManager $groupManager,
        ContainerInterface $container
    ) {
        $this->entityManager = $entityManager;
        $this->courseManager = $courseManager;
        $this->groupManager = $groupManager;
        $this->container = $container;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return CourseManager
     */
    public function getCourseManager()
    {
        return $this->courseManager;
    }

    /**
     * @return GroupManager
     */
    public function getGroupManager()
    {
        return $this->groupManager;
    }

    /**
    * @inheritdoc
    */
    protected function supports($attribute, $subject)
    {
        $options = [
            self::VIEW,
            self::EDIT,
            self::DELETE
        ];

        // if the attribute isn't one we support, return false
        if (!in_array($attribute, $options)) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof CGroupInfo) {
            return false;
        }

        return true;
    }

    /**
    * @inheritdoc
    */
    protected function voteOnAttribute($attribute, $group, TokenInterface $token)
    {
        $user = $token->getUser();

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($group == false) {
            return false;
        }

        $authChecker = $this->container->get('security.authorization_checker');

        // Admins have access to everything
        if ($authChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $groupInfo = [
            'id' => $group->getId(),
            'session_id' => 0,
            'status' => $group->getStatus(),
        ];

        // Legacy
        return \GroupManager::userHasAccessToBrowse($user->getId(), $groupInfo);


        switch ($attribute) {
            case self::VIEW:
                if (!$group->hasUserInCourse($user, $course)) {
                    $user->addRole('ROLE_CURRENT_SESSION_COURSE_STUDENT');

                    return true;
                }

                break;
            case self::EDIT:
            case self::DELETE:
                if (!$session->hasCoachInCourseWithStatus($user, $course)) {
                    $user->addRole('ROLE_CURRENT_SESSION_COURSE_TEACHER');

                    return true;
                }
                break;
        }
        dump("You don't have access to this group!!");

        return false;
    }
}
