<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Repository;

use Chamilo\CoreBundle\Entity\ExtraFieldValues;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * ExtraFieldValuesRepository class.
 *
 * @author Angel Fernando Quiroz Campos <angel.quiroz@beeznest.com>
 */
class ExtraFieldValuesRepository extends ServiceEntityRepository
{
    /**
     * ExtraFieldValuesRepository constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExtraFieldValues::class);
    }

    /**
     * Get the extra field values for visible extra fields.
     *
     * @param int $extraFieldType The type of extra field
     * @param int $itemId         The item ID
     *
     * @return array
     */
    public function getVisibleValues($extraFieldType, $itemId)
    {
        $queryBuilder = $this->createQueryBuilder('fv');

        $queryBuilder
            ->innerJoin(
                'ChamiloCoreBundle:ExtraField',
                'f',
                Join::WITH,
                'fv.field = f.id'
            )
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('f.extraFieldType', (int) $extraFieldType),
                    $queryBuilder->expr()->eq('fv.itemId', (int) $itemId),
                    $queryBuilder->expr()->eq('f.visibleToSelf', true)
                )
            )
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}
