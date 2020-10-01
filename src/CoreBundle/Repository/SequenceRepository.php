<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Repository;

use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Entity\Sequence;
use Chamilo\CoreBundle\Entity\SequenceResource;
use Chamilo\CoreBundle\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class SequenceRepository
 * The functions inside this class should return an instance of QueryBuilder.
 */
class SequenceRepository extends ServiceEntityRepository
{
    /**
     * CourseCategoryRepository constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sequence::class);
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public static function getItems($type)
    {
        $list = [];

        switch ($type) {
            case SequenceResource::COURSE_TYPE:
                $courseListFromDatabase = \CourseManager::get_course_list();

                if (!empty($courseListFromDatabase)) {
                    foreach ($courseListFromDatabase as $item) {
                        $list[$item['id']] = $item['title'].' ('.$item['id'].')';
                    }
                }

                break;
            case SequenceResource::SESSION_TYPE:
                $sessionList = \SessionManager::get_sessions_list();
                if (!empty($sessionList)) {
                    foreach ($sessionList as $sessionItem) {
                        $list[$sessionItem['id']] = $sessionItem['name'].' ('.$sessionItem['id'].')';
                    }
                }

                break;
        }

        return $list;
    }

    public function getItem($itemId, $type)
    {
        $resource = null;
        switch ($type) {
            case SequenceResource::COURSE_TYPE:
                $repo = $this->getEntityManager()->getRepository(Course::class);

                break;
            case SequenceResource::SESSION_TYPE:
                $repo = $this->getEntityManager()->getRepository(Session::class);

                break;
        }

        if ($repo) {
            $resource = $repo->find($itemId);
        }

        return $resource;
    }

    /**
     * @param int $id
     */
    public function removeSequence($id)
    {
        $sequence = $this->find($id);
        $em = $this->getEntityManager();
        $em
            ->createQuery('DELETE FROM ChamiloCoreBundle:SequenceResource sr WHERE sr.sequence = :seq')
            ->execute(['seq' => $sequence]);

        $em->remove($sequence);
        $em->flush();
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function findAllToSelect($type)
    {
        $qb = $this->createQueryBuilder('r');
        $qb
            ->leftJoin('ChamiloCoreBundle:SequenceResource', 'sr', Join::WITH, 'sr.sequence = r');

        $qb
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull('r.graph'),
                    $qb->expr()->eq('sr.type', $type)
                )
            )
            ->orderBy('r.name');

        return $qb->getQuery()->getResult();
    }
}
