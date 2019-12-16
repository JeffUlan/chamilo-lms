<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V111;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20160715122300
 * Add association mapping between Session and CStudentPublication.
 */
class Version20160715122300 extends AbstractMigrationChamilo
{
    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE c_student_publication CHANGE session_id session_id INT DEFAULT NULL');
        $this->addSql('UPDATE c_student_publication SET session_id = NULL WHERE session_id = 0');
        // Fix not existing session id
        $this->addSql('DELETE FROM c_student_publication WHERE session_id not in (SELECT id FROM session)');
        $this->addSql('ALTER TABLE c_student_publication ADD CONSTRAINT fk_session FOREIGN KEY (session_id) REFERENCES session (id)');
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function down(Schema $schema)
    {
        $studentPublication = $schema->getTable('c_student_publication');
        $studentPublication->removeForeignKey('fk_session');
        $studentPublication->getColumn('session_id')->setNotnull(true);
    }
}
