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

namespace Doctrine\DBAL\Driver\OCI8;

use Doctrine\DBAL\Platforms;

/**
 * A Doctrine DBAL driver for the Oracle OCI8 PHP extensions.
 *
 * @author Roman Borschel <roman@code-factory.org>
 * @since 2.0
 */
class Driver implements \Doctrine\DBAL\Driver
{
    /**
     * {@inheritdoc}
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        return new OCI8Connection(
            $username,
            $password,
            $this->_constructDsn($params),
            isset($params['charset']) ? $params['charset'] : null,
            isset($params['sessionMode']) ? $params['sessionMode'] : OCI_DEFAULT,
            isset($params['persistent']) ? $params['persistent'] : false
        );
    }

    /**
     * Constructs the Oracle DSN.
     *
     * @param array $params
     *
     * @return string The DSN.
     */
    protected function _constructDsn(array $params)
    {
        $dsn = '';
        if (isset($params['host']) && $params['host'] != '') {
            $dsn .= '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)' .
                   '(HOST=' . $params['host'] . ')';

            if (isset($params['port'])) {
                $dsn .= '(PORT=' . $params['port'] . ')';
            } else {
                $dsn .= '(PORT=1521)';
            }

            if (isset($params['service']) && $params['service'] == true) {
                $dsn .= '))(CONNECT_DATA=(SERVICE_NAME=' . $params['dbname'] . '))';
            } else {
                $dsn .= '))(CONNECT_DATA=(SID=' . $params['dbname'] . '))';
            }
            if (isset($params['pooled']) && $params['pooled'] == true) {
                $dsn .= '(SERVER=POOLED)';
            }
            $dsn .= ')';
        } else {
            $dsn .= $params['dbname'];
        }

        return $dsn;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new \Doctrine\DBAL\Platforms\OraclePlatform();
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemaManager(\Doctrine\DBAL\Connection $conn)
    {
        return new \Doctrine\DBAL\Schema\OracleSchemaManager($conn);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'oci8';
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabase(\Doctrine\DBAL\Connection $conn)
    {
        $params = $conn->getParams();

        return $params['user'];
    }
}
