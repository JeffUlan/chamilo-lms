<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Security\Authorization\Voter;

use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Entity\ResourceLink;
use Chamilo\CoreBundle\Entity\ResourceNode;
use Chamilo\CoreBundle\Entity\ResourceRight;
use Chamilo\CoreBundle\Entity\Session;
use Chamilo\CourseBundle\Entity\CGroup;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\GenericResource;
use Laminas\Permissions\Acl\Role\GenericRole as Role;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ResourceNodeVoter extends Voter
{
    public const VIEW = 'VIEW';
    public const CREATE = 'CREATE';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';
    public const EXPORT = 'EXPORT';

    public const ROLE_CURRENT_COURSE_TEACHER = 'ROLE_CURRENT_COURSE_TEACHER';
    public const ROLE_CURRENT_COURSE_STUDENT = 'ROLE_CURRENT_COURSE_STUDENT';
    public const ROLE_CURRENT_COURSE_GROUP_TEACHER = 'ROLE_CURRENT_COURSE_GROUP_TEACHER';
    public const ROLE_CURRENT_COURSE_GROUP_STUDENT = 'ROLE_CURRENT_COURSE_GROUP_STUDENT';
    public const ROLE_CURRENT_COURSE_SESSION_TEACHER = 'ROLE_CURRENT_COURSE_SESSION_TEACHER';
    public const ROLE_CURRENT_COURSE_SESSION_STUDENT = 'ROLE_CURRENT_COURSE_SESSION_STUDENT';

    private $requestStack;
    private $security;

    public function __construct(Security $security, RequestStack $requestStack)
    {
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public static function getReaderMask(): int
    {
        $builder = new MaskBuilder();
        $builder
            ->add(self::VIEW)
        ;

        return $builder->get();
    }

    public static function getEditorMask(): int
    {
        $builder = new MaskBuilder();
        $builder
            ->add(self::VIEW)
            ->add(self::EDIT)
        ;

        return $builder->get();
    }

    protected function supports(string $attribute, $subject): bool
    {
        $options = [
            self::VIEW,
            self::CREATE,
            self::EDIT,
            self::DELETE,
            self::EXPORT,
        ];

        error_log('resourceNode supports');
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, $options)) {
            return false;
        }

        // only vote on ResourceNode objects inside this voter
        if (!$subject instanceof ResourceNode) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        error_log('resourceNode voteOnAttribute');
        // Make sure there is a user object (i.e. that the user is logged in)
        // Update. No, anons can enter a node depending in the visibility.
        // $user = $token->getUser();
        /*if (!$user instanceof UserInterface) {
            return false;
        }*/

        /** @var ResourceNode $resourceNode */
        $resourceNode = $subject;
        $resourceTypeName = $resourceNode->getResourceType()->getName();

        // Illustrations are always visible.
        if ('illustrations' === $resourceTypeName) {
            return true;
        }

        // Courses are also a Resource but courses are protected using the CourseVoter, not by ResourceNodeVoter.
        if ('courses' === $resourceTypeName) {
            return true;
        }

        // Checking admin role.
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // @todo
        switch ($attribute) {
            case self::VIEW:
            case self::EDIT:
                break;
        }

        $user = $token->getUser();
        // Check if I'm the owner.
        $creator = $resourceNode->getCreator();
        if ($creator instanceof UserInterface &&
            $user instanceof UserInterface &&
            $user->getUsername() === $creator->getUsername()
        ) {
            return true;
        }

        // Checking links connected to this resource.
        $request = $this->requestStack->getCurrentRequest();

        $courseId = (int) $request->get('cid');
        $sessionId = (int) $request->get('sid');
        $groupId = (int) $request->get('gid');

        $links = $resourceNode->getResourceLinks();

        //$courseManager = $this->entityManager->getRepository(Course::class);
        //$sessionManager = $this->entityManager->getRepository(Session::class);

        $linkFound = 0;
        $link = null;
        // @todo implement view, edit, delete.
        /** @var ResourceLink $link */
        foreach ($links as $link) {
            // Block access if visibility is deleted. Creator and admin have already access.
            if (ResourceLink::VISIBILITY_DELETED === $link->getVisibility()) {
                continue;
            }

            // Check if resource was sent to the current user.
            $linkUser = $link->getUser();
            if ($linkUser instanceof UserInterface &&
                $user instanceof UserInterface &&
                $linkUser->getUsername() === $user->getUsername()) {
                $linkFound = 2;
                break;
            }

            $linkCourse = $link->getCourse();

            // Course found, but courseId not set, skip course checking.
            if ($linkCourse instanceof Course && empty($courseId)) {
                continue;
            }

            $linkSession = $link->getSession();
            $linkGroup = $link->getGroup();
            //$linkUserGroup = $link->getUserGroup();

            // @todo Check if resource was sent to a usergroup

            // Check if resource was sent inside a group in a course session.
            if (null === $linkUser &&
                $linkGroup instanceof CGroup && !empty($groupId) &&
                $linkSession instanceof Session && !empty($sessionId) &&
                $linkCourse instanceof Course && !empty($courseId)
            ) {
                if ($linkCourse->getId() === $courseId &&
                    $linkSession->getId() === $sessionId &&
                    $linkGroup->getIid() === $groupId
                ) {
                    $linkFound = 3;

                    break;
                }
            }

            // Check if resource was sent inside a group in a base course.
            if (null === $linkUser &&
                empty($sessionId) &&
                $linkGroup instanceof CGroup && !empty($groupId) &&
                $linkCourse instanceof Course && !empty($courseId)
            ) {
                if ($linkCourse->getId() === $courseId &&
                    $linkGroup->getIid() === $groupId
                ) {
                    $linkFound = 4;

                    break;
                }
            }

            // Check if resource was sent to a course inside a session.
            if (null === $linkUser &&
                $linkSession instanceof Session && !empty($sessionId) &&
                $linkCourse instanceof Course && !empty($courseId)
            ) {
                if ($linkCourse->getId() === $courseId &&
                    $linkSession->getId() === $sessionId
                ) {
                    $linkFound = 5;

                    break;
                }
            }

            // Check if resource was sent to a course.
            if (null === $linkUser &&
                $linkCourse instanceof Course && !empty($courseId)) {
                if ($linkCourse->getId() === $courseId) {
                    $linkFound = 6;

                    break;
                }
            }

            /*if (ResourceLink::VISIBILITY_PUBLISHED === $link->getVisibility()) {
                $linkFound = true;

                break;
            }*/
        }
        //var_dump($linkFound, $link->getId(), $link->getVisibility()); exit;

        // No link was found.
        if (0 === $linkFound) {
            return false;
        }

        // Getting rights from the link
        $rightFromResourceLink = $link->getResourceRight();
        $allowAnonsToSee = false;
        $rights = [];
        if ($rightFromResourceLink->count() > 0) {
            // Taken rights from the link.
            $rights = $rightFromResourceLink;
        } else {
            // Taken the rights from the default tool
            //$rights = $link->getResourceNode()->getTool()->getToolResourceRight();
            //$rights = $link->getResourceNode()->getResourceType()->getTool()->getToolResourceRight();

            // By default the rights are:
            // Teachers: CRUD.
            // Students: Only read.
            // Anons: Only read.
            $readerMask = self::getReaderMask();
            $editorMask = self::getEditorMask();

            if ($courseId && $link->hasCourse() && $link->getCourse()->getId() === $courseId) {
                // If teacher.
                if ($this->security->isGranted(self::ROLE_CURRENT_COURSE_TEACHER)) {
                    $resourceRight = new ResourceRight();
                    $resourceRight
                        ->setMask($editorMask)
                        ->setRole(self::ROLE_CURRENT_COURSE_TEACHER);
                    $rights[] = $resourceRight;
                }

                // If student.
                if ($this->security->isGranted(self::ROLE_CURRENT_COURSE_STUDENT) &&
                    ResourceLink::VISIBILITY_PUBLISHED === $link->getVisibility()
                ) {
                    $resourceRight = new ResourceRight();
                    $resourceRight
                        ->setMask($readerMask)
                        ->setRole(self::ROLE_CURRENT_COURSE_STUDENT);
                    $rights[] = $resourceRight;
                }

                // For everyone.
                if (ResourceLink::VISIBILITY_PUBLISHED === $link->getVisibility() &&
                    $link->getCourse()->isPublic()
                ) {
                    $allowAnonsToSee = true;
                    $resourceRight = new ResourceRight();
                    $resourceRight
                        ->setMask($readerMask)
                        ->setRole('IS_AUTHENTICATED_ANONYMOUSLY');
                    $rights[] = $resourceRight;
                }
            }

            if (!empty($groupId)) {
                /*var_dump($groupId);
                foreach ($user->getRoles() as $role) {
                    var_dump($role);
                }*/
                if ($this->security->isGranted(self::ROLE_CURRENT_COURSE_GROUP_TEACHER)) {
                    $resourceRight = new ResourceRight();
                    $resourceRight
                        ->setMask($editorMask)
                        ->setRole(self::ROLE_CURRENT_COURSE_GROUP_TEACHER);
                    $rights[] = $resourceRight;
                }

                if ($this->security->isGranted(self::ROLE_CURRENT_COURSE_GROUP_STUDENT)) {
                    $resourceRight = new ResourceRight();
                    $resourceRight
                        ->setMask($readerMask)
                        ->setRole(self::ROLE_CURRENT_COURSE_GROUP_STUDENT);
                    $rights[] = $resourceRight;
                }
            }

            if (!empty($sessionId)) {
                if ($this->security->isGranted(self::ROLE_CURRENT_COURSE_SESSION_TEACHER)) {
                    $resourceRight = new ResourceRight();
                    $resourceRight
                        ->setMask($editorMask)
                        ->setRole(self::ROLE_CURRENT_COURSE_SESSION_TEACHER);
                    $rights[] = $resourceRight;
                }

                if ($this->security->isGranted(self::ROLE_CURRENT_COURSE_SESSION_STUDENT)) {
                    $resourceRight = new ResourceRight();
                    $resourceRight
                        ->setMask($readerMask)
                        ->setRole(self::ROLE_CURRENT_COURSE_SESSION_STUDENT);
                    $rights[] = $resourceRight;
                }
            }

            if (empty($rights) && ResourceLink::VISIBILITY_PUBLISHED === $link->getVisibility()) {
                // Give just read access.
                $resourceRight = new ResourceRight();
                $resourceRight
                    ->setMask($readerMask)
                    ->setRole('ROLE_USER')
                ;
                $rights[] = $resourceRight;
            }
        }
        //exit;
        //var_dump($allowAnonsToSee);
        /*foreach ($rights as $right) {
            var_dump($right->getRole());
        }*/

        //exit;

        // Asked mask
        $mask = new MaskBuilder();
        $mask->add($attribute);
        $askedMask = $mask->get();

        // Creating roles
        // @todo move this in a service
        $anon = new Role('IS_AUTHENTICATED_ANONYMOUSLY');
        $userRole = new Role('ROLE_USER');
        $student = new Role('ROLE_STUDENT');
        $teacher = new Role('ROLE_TEACHER');

        $currentStudent = new Role(self::ROLE_CURRENT_COURSE_STUDENT);
        $currentTeacher = new Role(self::ROLE_CURRENT_COURSE_TEACHER);

        $currentStudentGroup = new Role(self::ROLE_CURRENT_COURSE_GROUP_STUDENT);
        $currentTeacherGroup = new Role(self::ROLE_CURRENT_COURSE_GROUP_TEACHER);

        $currentStudentSession = new Role(self::ROLE_CURRENT_COURSE_SESSION_STUDENT);
        $currentTeacherSession = new Role(self::ROLE_CURRENT_COURSE_SESSION_TEACHER);

        $superAdmin = new Role('ROLE_SUPER_ADMIN');
        $admin = new Role('ROLE_ADMIN');

        // Setting Simple ACL.
        $acl = new Acl();
        $acl
            ->addRole($anon)
            ->addRole($userRole)
            ->addRole($student)
            ->addRole($teacher)

            ->addRole($currentStudent)
            ->addRole($currentTeacher, self::ROLE_CURRENT_COURSE_STUDENT)

            ->addRole($currentStudentSession)
            ->addRole($currentTeacherSession, self::ROLE_CURRENT_COURSE_SESSION_STUDENT)

            ->addRole($currentStudentGroup)
            ->addRole($currentTeacherGroup, self::ROLE_CURRENT_COURSE_GROUP_STUDENT)

            ->addRole($superAdmin)
            ->addRole($admin)
        ;

        // Add a security resource.
        $linkId = $link->getId();
        $acl->addResource(new GenericResource($linkId));

        // Check all the right this link has.
        // Set rights from the ResourceRight.
        foreach ($rights as $right) {
            $acl->allow($right->getRole(), null, $right->getMask());
        }

        // Role and permissions settings
        // Student can just view (read)
        //$acl->allow($student, null, self::getReaderMask());

        // Teacher can view/edit
        /*$acl->allow(
            $teacher,
            null,
            [
                self::getReaderMask(),
                self::getEditorMask(),
            ]
        );*/

        // Anons can see.
        if ($allowAnonsToSee) {
            $acl->allow($anon, null, self::getReaderMask());
        }

        // Admin can do everything
        $acl->allow($admin);
        $acl->allow($superAdmin);

        if ($token instanceof AnonymousToken) {
            if ($acl->isAllowed('IS_AUTHENTICATED_ANONYMOUSLY', $linkId, $askedMask)) {
                return true;
            }

            return false;
        }

        foreach ($user->getRoles() as $role) {
            //var_dump($role, $acl->isAllowed($role, $linkId, $askedMask));
            if ($acl->isAllowed($role, $linkId, $askedMask)) {
                return true;
            }
        }

        //dump('not allowed to '.$attribute);
        return false;
    }
}
