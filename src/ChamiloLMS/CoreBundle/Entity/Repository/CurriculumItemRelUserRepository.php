<?php

namespace ChamiloLMS\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\Criteria;

/**
 * CurriculumItemRelUserRepository
 *
 */
class CurriculumItemRelUserRepository extends EntityRepository
{
    /**
     * Get all users that are registered in the course. No matter the status
     *
     * @param \ChamiloLMS\CoreBundle\Entity\CurriculumItem $course
     * @return bool
     */
    public function isAllowToInsert(\ChamiloLMS\CoreBundle\Entity\CurriculumItem $item, \ChamiloLMS\UserBundle\Entity\User $user)
    {
        $max = $item->getMaxRepeat();
        $count = $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where('a.itemId = :itemId')
            ->andWhere('a.userId = :userId')
            ->setParameters(
                array(
                    'itemId' => $item->getId(),
                    'userId' => $user->getUserId()
                )
            )
            ->getQuery()
            ->getSingleScalarResult();
        return $count <= $max ? true : false;

    }
}
