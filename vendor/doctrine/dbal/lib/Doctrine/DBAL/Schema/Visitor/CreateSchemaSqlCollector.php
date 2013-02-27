<?php
/*
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
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\DBAL\Schema\Visitor;

use Doctrine\DBAL\Platforms\AbstractPlatform,
    Doctrine\DBAL\Schema\Table,
    Doctrine\DBAL\Schema\Schema,
    Doctrine\DBAL\Schema\ForeignKeyConstraint,
    Doctrine\DBAL\Schema\Constraint,
    Doctrine\DBAL\Schema\Sequence;

class CreateSchemaSqlCollector extends AbstractVisitor
{
    /**
     * @var array
     */
    private $createTableQueries = array();

    /**
     * @var array
     */
    private $createSequenceQueries = array();

    /**
     * @var array
     */
    private $createFkConstraintQueries = array();

    /**
     *
     * @var \Doctrine\DBAL\Platforms\AbstractPlatform
     */
    private $platform = null;

    /**
     * @param AbstractPlatform $platform
     */
    public function __construct(AbstractPlatform $platform)
    {
        $this->platform = $platform;
    }

    /**
     * Generate DDL Statements to create the accepted table with all its dependencies.
     *
     * @param Table $table
     */
    public function acceptTable(Table $table)
    {
        $namespace = $this->getNamespace($table);

        $this->createTableQueries[$namespace] = array_merge(
            $this->createTableQueries[$namespace],
            $this->platform->getCreateTableSQL($table)
        );
    }

    /**
     * @param Table $localTable
     * @param ForeignKeyConstraint $fkConstraint
     */
    public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint)
    {
        $namespace = $this->getNamespace($localTable);

        if ($this->platform->supportsForeignKeyConstraints()) {
            $this->createFkConstraintQueries[$namespace] = array_merge(
                $this->createFkConstraintQueries[$namespace],
                (array) $this->platform->getCreateForeignKeySQL(
                    $fkConstraint, $localTable
                )
            );
        }
    }

    /**
     * @param Sequence $sequence
     */
    public function acceptSequence(Sequence $sequence)
    {
        $namespace = $this->getNamespace($sequence);

        $this->createSequenceQueries[$namespace] = array_merge(
            $this->createSequenceQueries[$namespace],
            (array)$this->platform->getCreateSequenceSQL($sequence)
        );
    }

    private function getNamespace($asset)
    {
        $namespace = $asset->getNamespaceName() ?: 'default';
        if ( !isset($this->createTableQueries[$namespace])) {
            $this->createTableQueries[$namespace] = array();
            $this->createSequenceQueries[$namespace] = array();
            $this->createFkConstraintQueries[$namespace] = array();
        }

        return $namespace;
    }

    /**
     * @return array
     */
    public function resetQueries()
    {
        $this->createTableQueries = array();
        $this->createSequenceQueries = array();
        $this->createFkConstraintQueries = array();
    }

    /**
     * Get all queries collected so far.
     *
     * @return array
     */
    public function getQueries()
    {
        $sql = array();

        foreach (array_keys($this->createTableQueries) as $namespace) {
            if ($this->platform->supportsSchemas()) {
                // TODO: Create Schema here
            }
        }

        foreach ($this->createTableQueries as $schemaSql) {
            $sql = array_merge($sql, $schemaSql);
        }

        foreach ($this->createSequenceQueries as $schemaSql) {
            $sql = array_merge($sql, $schemaSql);
        }

        foreach ($this->createFkConstraintQueries as $schemaSql) {
            $sql = array_merge($sql, $schemaSql);
        }

        return $sql;
    }
}
