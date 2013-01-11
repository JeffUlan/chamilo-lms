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

namespace Doctrine\ORM\Tools;

/**
 * Class to generate entity repository classes
 *
 *
 * @link    www.doctrine-project.org
 * @since   2.0
 * @author  Benjamin Eberlei <kontakt@beberlei.de>
 * @author  Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author  Jonathan Wage <jonwage@gmail.com>
 * @author  Roman Borschel <roman@code-factory.org>
 */
class EntityRepositoryGenerator
{
    protected static $_template =
'<?php

<namespace>

use Doctrine\ORM\EntityRepository;

/**
 * <className>
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class <className> extends EntityRepository
{
}
';

    /**
     * @param string $fullClassName
     *
     * @return string
     */
    public function generateEntityRepositoryClass($fullClassName)
    {
        $className = substr($fullClassName, strrpos($fullClassName, '\\') + 1, strlen($fullClassName));

        $variables = array(
            '<namespace>' => $this->generateEntityRepositoryNamespace($fullClassName),
            '<className>' => $className
        );

        return str_replace(array_keys($variables), array_values($variables), self::$_template);
    }

    /**
     * Generates the namespace statement, if class do not have namespace, return empty string instead.
     * 
     * @param string $fullClassName The full repository class name.
     *
     * @return string $namespace
     */
    private function generateEntityRepositoryNamespace($fullClassName)
    {
        $namespace = substr($fullClassName, 0, strrpos($fullClassName, '\\'));
        
        return $namespace ? 'namespace ' . $namespace . ';' : '';
    }

    /**
     * @param string $fullClassName
     * @param string $outputDirectory
     *
     * @return void
     */
    public function writeEntityRepositoryClass($fullClassName, $outputDirectory)
    {
        $code = $this->generateEntityRepositoryClass($fullClassName);

        $path = $outputDirectory . DIRECTORY_SEPARATOR
              . str_replace('\\', \DIRECTORY_SEPARATOR, $fullClassName) . '.php';
        $dir = dirname($path);

        if ( ! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if ( ! file_exists($path)) {
            file_put_contents($path, $code);
        }
    }
}
