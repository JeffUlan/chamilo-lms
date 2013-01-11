<?php

namespace Entity\Repository;

use Doctrine\ORM\EntityRepository;


/**
 * PagesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PagesRepository extends EntityRepository
{
    public function getLatestPages($limit = null)
    {
        $qb = $this->createQueryBuilder('b')
                   ->select('b')
                   ->addOrderBy('b.created', 'DESC');
        return $qb;
        if (false === is_null($limit))
            $qb->setMaxResults($limit);

        return $qb->getQuery()
                  ->getResult();
    }
}