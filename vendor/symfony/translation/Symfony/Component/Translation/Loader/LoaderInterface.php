<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Loader;

use Symfony\Component\Translation\MessageCatalogue;

/**
 * LoaderInterface is the interface implemented by all translation loaders.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
interface LoaderInterface
{
    /**
     * Loads a locale.
     *
     * @param mixed  $resource A resource
     * @param string $locale   A locale
     * @param string $domain   The domain
     *
     * @return MessageCatalogue A MessageCatalogue instance
     *
     * @api
     */
    public function load($resource, $locale, $domain = 'messages');
}
