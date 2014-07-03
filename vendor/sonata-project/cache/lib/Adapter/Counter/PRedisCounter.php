<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Cache\Adapter\Counter;

use Sonata\Cache\Counter;
use Predis\Client;

class PRedisCounter extends BaseCounter
{
    protected $options;

    protected $parameters;

    protected $client;

    /**
     * @param array $parameters
     * @param array $options
     */
    public function __construct(array $parameters = array(), array $options = array())
    {
        $this->parameters = $parameters;
        $this->options    = $options;
    }

    /**
     * @return Client
     */
    private function getClient()
    {
        if (!$this->client) {
            $this->client = new Client($this->parameters, $this->options);
        }

        return $this->client;
    }

    /**
     * {@inheritdoc}
     */
    public function increment($counter, $number = 1)
    {
        $counter = $this->transform($counter);

        if ($this->getClient()->get($counter->getName()) === null) {
            $this->getClient()->set($counter->getName(), $value = $counter->getValue() + $number);
        } else {
            $value = $this->getClient()->incrby($counter->getName(), $number);
        }

        return Counter::create($counter->getName(), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function decrement($counter, $number = 1)
    {
        $counter = $this->transform($counter);

        if ($this->getClient()->get($counter->getName()) === null) {
            $this->getClient()->set($counter->getName(), $value = $counter->getValue() - $number);
        } else {
            $value = $this->getClient()->decrby($counter->getName(), $number);
        }

        return Counter::create($counter->getName(), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function set(Counter $counter)
    {
        $this->getClient()->set($counter->getName(), $counter->getValue());

        return $counter;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return Counter::create($name, (int) $this->getClient()->get($name));
    }
}