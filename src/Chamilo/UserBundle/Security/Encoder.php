<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\UserBundle\Security;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;

/**
 * Class Encoder
 * @package Chamilo\UserBundle\Security
 */
class Encoder implements PasswordEncoderInterface
{
    protected $method;

    /**
     * @param $method
     */
    public function __construct($method)
    {
        $this->method = $method;
    }

    /**
     * @param string $raw
     * @param string $salt
     * @return string
     */
    public function encodePassword($raw, $salt)
    {
        switch ($this->method) {
            case 'none':
                $defaultEncoder = new PlaintextPasswordEncoder();
                break;
            case 'bcrypt':
                $defaultEncoder = new BCryptPasswordEncoder(4);
                break;
            case 'sha1':
            case 'md5':
                $defaultEncoder = new MessageDigestPasswordEncoder($this->method, false, 1);
                break;
        }

        return $defaultEncoder->encodePassword($raw, $salt);
    }

    /**
     * @param string $encoded
     * @param string $raw
     * @param string $salt
     * @return bool
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded === $this->encodePassword($raw, $salt);
    }
}
