<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Security;

use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;

class Encoder
{
    //private $passwordEncrypt;

    public function __construct()
    {
        /*$passwordEncrypt = str_replace("'", '', trim($passwordEncrypt));
        $this->passwordEncrypt = $passwordEncrypt;*/
    }

    /*
     * @param string $raw
     * @param string $salt
     *
     * @return string
     */
    /*public function encodePassword($raw, $salt)
    {
        $defaultEncoder = $this->getEncoder();

        return $defaultEncoder->encodePassword($raw, $salt);
    }*/

    /*
     * @param string $encoded
     * @param string $raw
     * @param string $salt
     *
     * @return bool
     */
    /*public function isPasswordValid($encoded, $raw, $salt)
    {
        if ($this->isPasswordTooLong($raw)) {
            return false;
        }

        $encoder = $this->getEncoder();

        return $encoder->isPasswordValid($encoded, $raw, $salt);
    }*/

    /*private function getEncoder(): void
    {
        switch ($this->passwordEncrypt) {
            case 'none':
                $defaultEncoder = new PlaintextPasswordEncoder();

                break;
            case 'bcrypt':
                $defaultEncoder = new BCryptPasswordEncoder(4);

                break;
            case 'sha1':
            case 'md5':
                $defaultEncoder = new MessageDigestPasswordEncoder($this->passwordEncrypt, false, 1);

                break;
        }

        //return $defaultEncoder;
    }*/
}
