<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\Tests\ORM\Query;

use Doctrine\DBAL\Types\Type as DBALType;

require_once __DIR__ . '/../../TestInit.php';

/**
 * Test case for testing the saving and referencing of query identifiers.
 *
 * @author      Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author      Janne Vanhala <jpvanhal@cc.hut.fi>
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        http://www.phpdoctrine.org
 * @since       2.0
 * @version     $Revision$
 * @todo        1) [romanb] We  might want to split the SQL generation tests into multiple
 *              testcases later since we'll have a lot of them and we might want to have special SQL
 *              generation tests for some dbms specific SQL syntaxes.
 */
class UpdateSqlGenerationTest extends \Doctrine\Tests\OrmTestCase
{
    private $_em;

    protected function setUp() {
        if (DBALType::hasType('negative_to_positive')) {
            DBALType::overrideType('negative_to_positive', 'Doctrine\Tests\DbalTypes\NegativeToPositiveType');
        } else {
            DBALType::addType('negative_to_positive', 'Doctrine\Tests\DbalTypes\NegativeToPositiveType');
        }

        $this->_em = $this->_getTestEntityManager();
    }

    public function assertSqlGeneration($dqlToBeTested, $sqlToBeConfirmed)
    {
        try {
            $query = $this->_em->createQuery($dqlToBeTested);
            parent::assertEquals($sqlToBeConfirmed, $query->getSql());
            $query->free();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testSupportsQueriesWithoutWhere()
    {
        $this->assertSqlGeneration(
            'UPDATE Doctrine\Tests\Models\CMS\CmsUser u SET u.name = ?1',
            'UPDATE cms_users SET name = ?'
        );
    }

    public function testSupportsMultipleFieldsWithoutWhere()
    {
        $this->assertSqlGeneration(
            'UPDATE Doctrine\Tests\Models\CMS\CmsUser u SET u.name = ?1, u.username = ?2',
            'UPDATE cms_users SET name = ?, username = ?'
        );
    }

    public function testSupportsWhereClauses()
    {
        $this->assertSqlGeneration(
            'UPDATE Doctrine\Tests\Models\CMS\CmsUser u SET u.name = ?1 WHERE u.id = ?2',
            'UPDATE cms_users SET name = ? WHERE id = ?'
        );
    }

    public function testSupportsWhereClausesOnTheUpdatedField()
    {
        $this->assertSqlGeneration(
            'UPDATE Doctrine\Tests\Models\CMS\CmsUser u SET u.name = ?1 WHERE u.name = ?2',
            'UPDATE cms_users SET name = ? WHERE name = ?'
        );
    }

    public function testSupportsMultipleWhereClause()
    {
        $this->assertSqlGeneration(
            'UPDATE Doctrine\Tests\Models\CMS\CmsUser u SET u.name = ?1 WHERE u.name = ?2 AND u.status = ?3',
            'UPDATE cms_users SET name = ? WHERE name = ? AND status = ?'
        );
    }

    public function testSupportsInClause()
    {
        $this->assertSqlGeneration(
            'UPDATE Doctrine\Tests\Models\CMS\CmsUser u SET u.name = ?1 WHERE u.id IN (1, 3, 4)',
            'UPDATE cms_users SET name = ? WHERE id IN (1, 3, 4)'
        );
    }

    public function testSupportsParametrizedInClause()
    {
        $this->assertSqlGeneration(
            'UPDATE Doctrine\Tests\Models\CMS\CmsUser u SET u.name = ?1 WHERE u.id IN (?2, ?3, ?4)',
            'UPDATE cms_users SET name = ? WHERE id IN (?, ?, ?)'
        );
    }

    public function testSupportsNotInClause()
    {
        $this->assertSqlGeneration(
            'UPDATE Doctrine\Tests\Models\CMS\CmsUser u SET u.name = ?1 WHERE u.id NOT IN (1, 3, 4)',
            'UPDATE cms_users SET name = ? WHERE id NOT IN (1, 3, 4)'
        );
    }

    public function testSupportsGreaterThanClause()
    {
        $this->assertSqlGeneration(
            'UPDATE Doctrine\Tests\Models\CMS\CmsUser u SET u.status = ?1 WHERE u.id > ?2',
            'UPDATE cms_users SET status = ? WHERE id > ?'
        );
    }

    public function testSupportsGreaterThanOrEqualToClause()
    {
        $this->assertSqlGeneration(
            'UPDATE Doctrine\Tests\Models\CMS\CmsUser u SET u.status = ?1 WHERE u.id >= ?2',
            'UPDATE cms_users SET status = ? WHERE id >= ?'
        );
    }

    public function testSupportsLessThanClause()
    {
        $this->assertSqlGeneration(
            'UPDATE Doctrine\Tests\Models\CMS\CmsUser u SET u.status = ?1 WHERE u.id < ?2',
            'UPDATE cms_users SET status = ? WHERE id < ?'
        );
    }

    public function testSupportsLessThanOrEqualToClause()
    {
        $this->assertSqlGeneration(
            'UPDATE Doctrine\Tests\Models\CMS\CmsUser u SET u.status = ?1 WHERE u.id <= ?2',
            'UPDATE cms_users SET status = ? WHERE id <= ?'
        );
    }

    public function testSupportsBetweenClause()
    {
        $this->assertSqlGeneration(
            'UPDATE Doctrine\Tests\Models\CMS\CmsUser u SET u.status = ?1 WHERE u.id BETWEEN :from AND :to',
            'UPDATE cms_users SET status = ? WHERE id BETWEEN ? AND ?'
        );
    }

    public function testSingleValuedAssociationFieldInWhere()
    {
        $this->assertSqlGeneration(
            "UPDATE Doctrine\Tests\Models\CMS\CmsPhonenumber p SET p.phonenumber = 1234 WHERE p.user = ?1",
            "UPDATE cms_phonenumbers SET phonenumber = 1234 WHERE user_id = ?"
        );
    }

    public function testSingleValuedAssociationFieldInSetClause()
    {
        $this->assertSqlGeneration(
            "update Doctrine\Tests\Models\CMS\CmsComment c set c.article = null where c.article=?1",
            "UPDATE cms_comments SET article_id = NULL WHERE article_id = ?"
        );
    }

    /**
     * @group DDC-980
     */
    public function testSubselectTableAliasReferencing()
    {
        $this->assertSqlGeneration(
            "UPDATE Doctrine\Tests\Models\CMS\CmsUser u SET u.status = 'inactive' WHERE SIZE(u.groups) = 10",
            "UPDATE cms_users SET status = 'inactive' WHERE (SELECT COUNT(*) FROM cms_users_groups c0_ WHERE c0_.user_id = cms_users.id) = 10"
        );
    }

    public function testCustomTypeValueSqlCompletelyIgnoredInUpdateStatements()
    {
        $this->assertSqlGeneration(
            'UPDATE Doctrine\Tests\Models\CustomType\CustomTypeParent p SET p.customInteger = 1 WHERE p.id = 1',
            'UPDATE customtype_parents SET customInteger = 1 WHERE id = 1'
        );
    }

    public function testUpdateWithSubselectAsNewValue()
    {
        $this->assertSqlGeneration(
            "UPDATE Doctrine\Tests\Models\Company\CompanyFixContract fc SET fc.fixPrice = (SELECT ce2.salary FROM Doctrine\Tests\Models\Company\CompanyEmployee ce2 WHERE ce2.id = 2) WHERE fc.id = 1",
            "UPDATE company_contracts SET fixPrice = (SELECT c0_.salary FROM company_employees c0_ INNER JOIN company_persons c1_ ON c0_.id = c1_.id LEFT JOIN company_managers c2_ ON c0_.id = c2_.id WHERE c1_.id = 2) WHERE (id = 1) AND discr IN ('fix')"
        );
    }
}
