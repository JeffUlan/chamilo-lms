<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core;

final class AuthenticationEvents
{
    const AUTHENTICATION_SUCCESS = 'security.authentication.success';

    const AUTHENTICATION_FAILURE = 'security.authentication.failure';
}
