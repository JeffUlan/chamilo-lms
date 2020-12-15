<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V200;

use Chamilo\CoreBundle\Entity\AccessUrl;
use Chamilo\CoreBundle\Entity\User;
use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Chamilo\CoreBundle\Repository\Node\AccessUrlRepository;
use Chamilo\CoreBundle\Repository\Node\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\Uid\Uuid;

final class Version20201212114910 extends AbstractMigrationChamilo
{
    public function getDescription(): string
    {
        return 'Migrate access_url, users';
    }

    public function up(Schema $schema): void
    {
        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');
        $em = $doctrine->getManager();
        /** @var Connection $connection */
        $connection = $em->getConnection();

        $urlRepo = $container->get(AccessUrlRepository::class);
        $userRepo = $container->get(UserRepository::class);

        $userList = [];
        // Adding first admin as main creator also adding to the resource node tree.
        $admin = $this->getAdmin();

        $this->abortIf(null === $admin, 'Admin not found in the system');

        $adminId = $admin->getId();
        $userList[$adminId] = $admin;
        if (false === $admin->hasResourceNode()) {
            $resourceNode = $userRepo->addUserToResourceNode($adminId, $adminId);
            $em->persist($resourceNode);
        }

        // Adding portals (AccessUrl) to the resource node tree.
        $urls = $urlRepo->findAll();
        /** @var AccessUrl $url */
        foreach ($urls as $url) {
            if (false === $url->hasResourceNode()) {
                $urlRepo->addResourceNode($url, $admin);
                $em->persist($url);
            }
        }
        $em->flush();

        // Adding users to the resource node tree.
        $batchSize = self::BATCH_SIZE;
        $counter = 1;
        $q = $em->createQuery('SELECT u FROM Chamilo\CoreBundle\Entity\User u');
        /** @var User $userEntity */
        foreach ($q->toIterable() as $userEntity) {
            if ($userEntity->hasResourceNode()) {
                continue;
            }
            $userEntity->setUuid(Uuid::v4());
            $creatorId = $userEntity->getCreatorId();
            $creator = null;
            if (isset($userList[$adminId])) {
                $creator = $userList[$adminId];
            } else {
                $creator = $userRepo->find($creatorId);
                $userList[$adminId] = $creator;
            }
            if (null === $creator) {
                $creator = $admin;
            }

            $resourceNode = $userRepo->addUserToResourceNode($adminId, $creator->getId());
            $em->persist($resourceNode);
            if (0 === $counter % $batchSize) {
                $em->flush();
                $em->clear(); // Detaches all objects from Doctrine!
            }
            $counter++;
        }
        $em->flush();
        $em->clear();

        $table = $schema->getTable('user');
        if (false === $table->hasIndex('UNIQ_8D93D649D17F50A6')) {
            $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D17F50A6 ON user (uuid);');
        }
    }
}
