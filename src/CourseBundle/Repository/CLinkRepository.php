<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Repository;

use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Entity\ResourceNode;
use Chamilo\CoreBundle\Entity\Session;
use Chamilo\CoreBundle\Entity\User;
use Chamilo\CoreBundle\Repository\ResourceRepository;
use Chamilo\CourseBundle\Entity\CGroup;
use Chamilo\CourseBundle\Entity\CLink;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class CLinkRepository extends ResourceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CLink::class);
    }
}
