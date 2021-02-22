<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Repository;

use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Entity\ResourceNode;
use Chamilo\CoreBundle\Entity\Session;
use Chamilo\CoreBundle\Entity\User;
use Chamilo\CoreBundle\Form\Resource\CLinkType;
use Chamilo\CoreBundle\Repository\GridInterface;
use Chamilo\CoreBundle\Repository\ResourceRepository;
use Chamilo\CourseBundle\Entity\CGroup;
use Chamilo\CourseBundle\Entity\CLink;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;

/**
 * Class CLinkRepository.
 */
final class CLinkRepository extends ResourceRepository implements GridInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CLink::class);
    }

    public function getResources(User $user, ResourceNode $parentNode, Course $course = null, Session $session = null, CGroup $group = null): QueryBuilder
    {
        return $this->getResourcesByCourse($course, $session, $group, $parentNode);
    }

    public function setResourceProperties(FormInterface $form, $course, $session, $fileType)
    {
        /** @var CLink $newResource */
        $newResource = $form->getData();
        $newResource
            ->setDisplayOrder(0)
            ->setOnHomepage('0')
        ;

        return $newResource;
    }

    public function getResourceFormType(): string
    {
        return CLinkType::class;
    }
}
