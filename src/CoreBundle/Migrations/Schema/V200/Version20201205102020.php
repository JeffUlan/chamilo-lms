<?php

declare(strict_types=1);

namespace Chamilo\CoreBundle\Migrations\Schema\V200;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

final class Version20201205102020 extends AbstractMigrationChamilo
{
    public function up(Schema $schema) : void
    {
        // From configuration.dist.php 1.11.x
        $this->addSql('ALTER TABLE c_dropbox_file CHANGE filename filename VARCHAR(190) NOT NULL');
        $this->addSql('ALTER TABLE course_category CHANGE name name LONGTEXT NOT NULL;');
        $this->addSql('ALTER TABLE c_course_description CHANGE title title LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE c_thematic CHANGE title title LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE c_quiz CHANGE title title LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE c_lp_category CHANGE name name LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE c_glossary CHANGE name name LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE c_tool CHANGE name name LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE portfolio CHANGE title title LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE block CHANGE path path VARCHAR(190) NOT NULL');
    }
}
