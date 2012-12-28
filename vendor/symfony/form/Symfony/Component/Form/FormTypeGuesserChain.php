<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form;

use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class FormTypeGuesserChain implements FormTypeGuesserInterface
{
    private $guessers = array();

    /**
     * Constructor.
     *
     * @param array $guessers Guessers as instances of FormTypeGuesserInterface
     *
     * @throws UnexpectedTypeException if any guesser does not implement FormTypeGuesserInterface
     */
    public function __construct(array $guessers)
    {
        foreach ($guessers as $guesser) {
            if (!$guesser instanceof FormTypeGuesserInterface) {
                throw new UnexpectedTypeException($guesser, 'Symfony\Component\Form\FormTypeGuesserInterface');
            }

            if ($guesser instanceof self) {
                $this->guessers = array_merge($this->guessers, $guesser->guessers);
            } else {
                $this->guessers[] = $guesser;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function guessType($class, $property)
    {
        return $this->guess(function ($guesser) use ($class, $property) {
            return $guesser->guessType($class, $property);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function guessRequired($class, $property)
    {
        return $this->guess(function ($guesser) use ($class, $property) {
            return $guesser->guessRequired($class, $property);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function guessMaxLength($class, $property)
    {
        return $this->guess(function ($guesser) use ($class, $property) {
            return $guesser->guessMaxLength($class, $property);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function guessMinLength($class, $property)
    {
        return $this->guess(function ($guesser) use ($class, $property) {
            return $guesser->guessMinLength($class, $property);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function guessPattern($class, $property)
    {
        return $this->guess(function ($guesser) use ($class, $property) {
            return $guesser->guessPattern($class, $property);
        });
    }

    /**
     * Executes a closure for each guesser and returns the best guess from the
     * return values
     *
     * @param \Closure $closure The closure to execute. Accepts a guesser
     *                            as argument and should return a Guess instance
     *
     * @return Guess The guess with the highest confidence
     */
    private function guess(\Closure $closure)
    {
        $guesses = array();

        foreach ($this->guessers as $guesser) {
            if ($guess = $closure($guesser)) {
                $guesses[] = $guess;
            }
        }

        return Guess::getBestGuess($guesses);
    }
}
