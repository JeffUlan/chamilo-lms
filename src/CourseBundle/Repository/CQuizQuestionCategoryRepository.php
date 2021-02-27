<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Repository;

use Chamilo\CoreBundle\Repository\ResourceRepository;
use Chamilo\CourseBundle\Entity\CQuizQuestionCategory;
use Doctrine\Persistence\ManagerRegistry;

final class CQuizQuestionCategoryRepository extends ResourceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CQuizQuestionCategory::class);
    }
}
