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
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\ORM;

/**
 * Represents a native SQL query.
 *
 * @author Roman Borschel <roman@code-factory.org>
 * @since 2.0
 */
final class NativeQuery extends AbstractQuery
{
    private $_sql;

    /**
     * Sets the SQL of the query.
     *
     * @param string $sql
     * @return NativeQuery This query instance.
     */
    public function setSQL($sql)
    {
        $this->_sql = $sql;

        return $this;
    }

    /**
     * Gets the SQL query.
     *
     * @return mixed The built SQL query or an array of all SQL queries.
     * @override
     */
    public function getSQL()
    {
        return $this->_sql;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doExecute()
    {
        $params = $this->_params;
        $types  = $this->_paramTypes;

        if ($params && is_int(key($params))) {
            ksort($params);
            ksort($types);

            $params = array_values($params);
            $types  = array_values($types);
        }

        return $this->_em->getConnection()->executeQuery(
            $this->_sql, $params, $types, $this->_queryCacheProfile
        );
    }
}