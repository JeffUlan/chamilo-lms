<?php

/* For licensing terms, see /license.txt */

declare(strict_types=1);

namespace Chamilo\CoreBundle\DataProvider\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
//use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Chamilo\CoreBundle\Entity\Message;
use Chamilo\CoreBundle\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

final class MessageExtension implements QueryCollectionExtensionInterface //, QueryItemExtensionInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        /*if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }*/
        /*
        if ('collection_query' === $operationName) {
            if (null === $user = $this->security->getUser()) {
                throw new AccessDeniedException('Access Denied.');
            }

            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.user = :current_user', $rootAlias));
            $queryBuilder->setParameter('current_user', $user);
        }*/

        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = []): void
    {
        //error_log('applyToItem');
        //$this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $qb, string $resourceClass): void
    {
        if (Message::class !== $resourceClass) {
            return;
        }

        /*if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }*/

        /** @var User $user */
        $user = $this->security->getUser();
        $alias = $qb->getRootAliases()[0];

        $qb->innerJoin("$alias.receivers", 'r');
        /*$qb->andWhere(
            $qb->expr()->orX(
                $qb->andWhere(
                    $qb->expr()->eq("$alias.sender", $user->getId()),
                    $qb->expr()->eq("$alias.msgType", Message::MESSAGE_TYPE_OUTBOX)
                ),
                $qb->andWhere(
                    $qb->expr()->in("r", $user->getId()),
                    $qb->expr()->eq("$alias.msgType", Message::MESSAGE_TYPE_INBOX)
                )
            ),
        );*/

        $qb->andWhere("
            ($alias.sender = :current AND $alias.msgType = :outbox) OR 
            (r IN (:currentList) AND $alias.msgType = :inbox) OR
            (r IN (:currentList) AND $alias.msgType = :invitation) OR
            (r IN (:currentList) AND $alias.msgType = :promoted) OR
            (r IN (:currentList) AND $alias.msgType = :wallPost) OR
            (r IN (:currentList) AND $alias.msgType = :conversation) 
        ");

        $qb->setParameters([
            'current' => $user,
            'currentList' => [$user->getId()],
            'inbox' => Message::MESSAGE_TYPE_INBOX,
            'outbox' => Message::MESSAGE_TYPE_OUTBOX,
            'invitation' => Message::MESSAGE_TYPE_INVITATION,
            'promoted' => Message::MESSAGE_TYPE_PROMOTED,
            'wallPost' => Message::MESSAGE_TYPE_WALL,
            'conversation' => Message::MESSAGE_TYPE_CONVERSATION,
        ]);
    }
}
