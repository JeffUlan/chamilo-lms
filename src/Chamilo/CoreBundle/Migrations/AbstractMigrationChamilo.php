<?php

namespace Chamilo\CoreBundle\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManager;

/**
 * Class AbstractMigrationChamilo
 * @package Chamilo\CoreBundle\Migrations
 */
class AbstractMigrationChamilo extends AbstractMigration
{
    private $manager;

    public function setEntityManager(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->manager;
    }
}
