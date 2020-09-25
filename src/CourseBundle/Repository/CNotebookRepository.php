<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Repository;

use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Entity\Session;
use Chamilo\CoreBundle\Entity\User;
use Chamilo\CoreBundle\Repository\ResourceRepository;

/**
 * Class CNotebookRepository.
 */
class CNotebookRepository extends ResourceRepository
{
    /**
     * Get the user notebooks in a course.
     *
     * @param string $orderField
     * @param string $orderDirection
     *
     * @return array
     */
    public function findByUser(
        User $user,
        Course $course,
        Session $session = null,
        $orderField = 'creation_date',
        $orderDirection = 'DESC'
    ) {
        switch ($orderField) {
            case 'creation_date':
                $orderField = 'N.creationDate';

                break;
            case 'update_date':
                $orderField = 'N.updateDate';

                break;
            case 'title':
                $orderField = 'N.title';

                break;
        }

        $qb = $this->createQueryBuilder('N');
        $qb
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('N.userId', $user->getId()),
                    $qb->expr()->eq('N.cId', $course->getId())
                )
            );

        if ($session) {
            $qb->andWhere(
                $qb->expr()->eq('N.sessionId', $session->getId())
            );
        } else {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('N.sessionId', 0),
                    $qb->expr()->isNull('N.sessionId')
                )
            );
        }

        if ('N.updateDate' === $orderField) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->neq('N.updateDate', ''),
                    $qb->expr()->isNotNull('N.updateDate')
                )
            );
        }

        $qb->orderBy($orderField, $orderDirection);

        return $qb->getQuery()->getResult();
    }
}
