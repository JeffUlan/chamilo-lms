<?php

namespace Entity\Repository;

use Doctrine\Common\Collections\Criteria;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * Class BranchSyncRepository
 * @package Entity\Repository
 */
class BranchSyncRepository extends NestedTreeRepository
{
    /**
     * @param string $keyword
     * @return mixed
     */
    public function searchByKeyword($keyword)
    {
        $qb = $this->createQueryBuilder('a');

        //Selecting user info
        $qb->select('DISTINCT b');

        $qb->from('Entity\BranchSync', 'b');

        //Selecting courses for users
        //$qb->innerJoin('u.courses', 'c');

        //@todo check app settings
        $qb->add('orderBy', 'b.branchName ASC');
        $qb->where('b.branchName LIKE :keyword');
        $qb->setParameter('keyword', "%$keyword%");
        $q = $qb->getQuery();
        return $q->execute();
    }
}
