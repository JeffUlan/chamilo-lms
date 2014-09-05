<?php

namespace Chamilo\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Entity\Session;
use Chamilo\CoreBundle\Entity\Group;

/**
 * ItemPropertyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemPropertyRepository extends EntityRepository
{
    /**
     *
     * Get users subscribed to a item LP, Document, etc (item_property)
     *
     * @param $tool learnpath | document | etc
     * @param $itemId
     * @param Course $course
     * @param int $sessionId
     * @param int $groupId
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getUsersSubscribedToItem($tool, $itemId, Course $course, Session $session = null, Group $group = null)
    {
        $criteria = array(
            'tool' => $tool,
            'lasteditType' => 'LearnpathSubscription',
            'ref' => $itemId,
            'course' => $course,
            'session' => $session,
            'group' => $group
        );
        return $this->findBy($criteria);
        /*
        $qb = $this->createQueryBuilder('i')
            ->select('i');
        $wherePart = $qb->expr()->andx();

        //Selecting courses for users
        $qb->innerJoin('i.user', 'u');

        $wherePart->add($qb->expr()->eq('i.tool', $qb->expr()->literal($tool)));
        $wherePart->add($qb->expr()->eq('i.lasteditType', $qb->expr()->literal('LearnpathSubscription')));
        $wherePart->add($qb->expr()->eq('i.ref', $itemId));
        $wherePart->add($qb->expr()->eq('i.cId', $course->getId()));
        $wherePart->add($qb->expr()->eq('i.idSession', $sessionId));
        $wherePart->add($qb->expr()->eq('i.toGroupId', $groupId));

        $qb->where($wherePart);
        $q = $qb->getQuery();
        //var_dump($q->getSQL());
        return $q->execute();*/
    }

    /**
     * Get Groups subscribed to a item: LP, Doc, etc
     * @param $tool learnpath | document | etc
     * @param $itemId
     * @param Course $course
     * @param Session $session
     * @return array
     */
    public function getGroupsSubscribedToItem($tool, $itemId, Course $course, Session $session = null)
    {
        $criteria = array(
            'tool' => $tool,
            'lasteditType' => 'LearnpathSubscription',
            'ref' => $itemId,
            'course' => $course,
            'session' => $session,
            'toUserId' => null,
        );
        return $this->findBy($criteria);
    }

    /**
     * Subscribe groups to a LP, doc (itemproperty)
     * @param $tool learnpath | document | etc
     * @param Course $course
     * @param Session $session
     * @param $itemId
     * @param array $newList
     */
    public function subscribeGroupsToItem($tool, Course $course, Session $session = null, $itemId, $newList = array())
    {
        $em = $this->getEntityManager();
        $groupsSubscribedToItem = $this->getGroupsSubscribedToItem($tool, $itemId, $course, $session);

        $alreadyAdded = array();
        if ($groupsSubscribedToItem) {
            foreach ($groupsSubscribedToItem as $itemProperty) {
                $alreadyAdded[] = $itemProperty->getToGroupId();
            }
        }

        $toDelete = $alreadyAdded;

        if (!empty($newList)) {
            $toDelete = array_diff($alreadyAdded, $newList);
        }

        if ($toDelete) {
            $this->unsubscribeGroupsToItem($tool, $course, $session, $itemId, $toDelete, true);
        }

        foreach ($newList as $groupId) {
            if (!in_array($groupId, $alreadyAdded)) {
                $item = new \Entity\CItemProperty($course);
                $groupObj = $em->find('Chamilo\CoreBundle\Entity\CGroupInfo', $groupId);
                $item->setGroup($groupObj);
                $item->setTool($tool);
                $item->setRef($itemId);

                if (!empty($session)) {
                    $item->setSession($session);
                }
                $item->setLasteditType('LearnpathSubscription');
                $item->setVisibility('1');
                $em->persist($item); //$em is an instance of EntityManager
            }

            //Adding users from this group to the item
            $users = \GroupManager::get_members_and_tutors($groupId);
            $newUserList = array();
            if (!empty($users)) {
                foreach ($users as $user) {
                    $newUserList[] = $user['user_id'];
                }
                $this->subscribeUsersToItem(
                    'learnpath',
                    $course,
                    $session,
                    $itemId,
                    $newUserList
                );
            }
        }
        $em->flush();
    }

    /**
     * Unsubscribe groups to item
     * @param $tool
     * @param Course $course
     * @param Session $session
     * @param $itemId
     * @param $groups
     */
    function unsubscribeGroupsToItem($tool, Course $course, Session $session = null, $itemId, $groups, $unsubscribeUserToo = false)
    {
        if (!empty($groups)) {
            $em = $this->getEntityManager();

            foreach ($groups as $groupId) {
                $item = $this->findOneBy(array(
                    'tool' => $tool,
                    'session' => $session,
                    'ref' => $itemId,
                    'toGroupId' => $groupId
                ));
                if ($item) {
                    $em->remove($item);
                }

                if ($unsubscribeUserToo) {

                    //Adding users from this group to the item
                    $users = \GroupManager::get_members_and_tutors($groupId);
                    $newUserList = array();
                    if (!empty($users)) {
                        foreach($users as $user) {
                            $newUserList[] = $user['user_id'];
                        }
                        $this->unsubcribeUsersToItem(
                            'learnpath',
                            $course,
                            $session,
                            $itemId,
                            $newUserList
                        );
                    }
                }
            }
            $em->flush();
        }
    }

    /**
     * Subscribe users to a LP, doc (itemproperty)
     *
     * @param $tool
     * @param Course $course
     * @param Session $session
     * @param $itemId
     * @param array $newUserList
     */
    public function subscribeUsersToItem($tool, Course $course, Session $session = null, $itemId, $newUserList = array())
    {
        $em = $this->getEntityManager();
        $user = $em->getRepository('Chamilo\UserBundle\Entity\User');

        $usersSubscribedToItem = $this->getUsersSubscribedToItem($tool, $itemId, $course, $session);

        $alreadyAddedUsers = array();
        if ($usersSubscribedToItem) {
            foreach ($usersSubscribedToItem as $itemProperty) {
                $alreadyAddedUsers[] = $itemProperty->getToUserId();
            }
        }

        $usersToDelete = $alreadyAddedUsers;

        if (!empty($newUserList)) {
            $usersToDelete = array_diff($alreadyAddedUsers, $newUserList);
        }

        if ($usersToDelete) {
            $this->unsubcribeUsersToItem($tool, $course, $session, $itemId, $usersToDelete);
        }

        foreach ($newUserList as $userId) {
            if (!in_array($userId, $alreadyAddedUsers)) {
                $userObj = $user->find($userId);

                $item = new \Entity\CItemProperty($course);
                $item->setUser($userObj);
                $item->setTool($tool);
                $item->setRef($itemId);
                if (!empty($session)) {
                    $item->setSession($session);
                }
                $item->setLasteditType('LearnpathSubscription');
                $item->setVisibility('1');
                $em->persist($item); //$em is an instance of EntityManager
            }
        }
        $em->flush();
    }

    /**
     * Unsubscribe users to item
     *
     * @param $tool
     * @param Course $course
     * @param Session $session
     * @param $itemId
     * @param $usersToDelete
     */
    public function unsubcribeUsersToItem($tool, Course $course, Session $session = null, $itemId, $usersToDelete)
    {
        $em = $this->getEntityManager();

        if (!empty($usersToDelete)) {
            foreach ($usersToDelete as $userId) {
                $item = $this->findOneBy(
                    array(
                        'tool' => $tool,
                        'session' => $session,
                        'ref' => $itemId,
                        'toUserId' => $userId
                    )
                );
                if ($item) {
                    $em->remove($item);
                }
            }
            $em->flush();
        }
    }
}
