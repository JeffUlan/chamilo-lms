<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Exception;

/**
 * CredentialsExpiredException is thrown when the user account credentials have expired.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CredentialsExpiredException extends AccountStatusException
{
}
