<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Repository;

use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Entity\ResourceInterface;
use Chamilo\CoreBundle\Entity\Session;
use Chamilo\CoreBundle\Repository\ResourceRepository;
use Chamilo\CourseBundle\Entity\CForum;
use Chamilo\CourseBundle\Entity\CForumCategory;
use Doctrine\Persistence\ManagerRegistry;

class CForumCategoryRepository extends ResourceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CForumCategory::class);
    }

    public function getForumCategoryByTitle(string $title, Course $course, Session $session = null): ?ResourceInterface
    {
        return $this->findCourseResourceByTitle(
            $title,
            $course->getResourceNode(),
            $course,
            $session
        );
    }

    /*public function getResources(User $user, ResourceNode $parentNode, Course $course = null, Session $session = null, CGroup $group = null): QueryBuilder
    {
        return $this->getResourcesByCourse($course, $session, $group, $parentNode);
    }*/

    public function delete(ResourceInterface $resource): void
    {
        /** @var CForumCategory $resource */
        $forums = $resource->getForums();
        $repo = $this->getEntityManager()->getRepository(CForum::class);
        if (!empty($forums)) {
            foreach ($forums as $forum) {
                /** @var CForum $forum */
                $repo->delete($forum);
            }
        }

        parent::delete($resource);
    }
}
