<?php
/**
 * DependencyResolverInterface.php
 * publisher
 * Date: 18.04.14
 */

namespace Chamilo\AdminThemeBundle\Util;


interface DependencyResolverInterface
{
    /**
     * @param $items
     *
     * @return $this
     */
    public function register($items);

    /**
     * @return array
     */
    public function resolveAll();
}
