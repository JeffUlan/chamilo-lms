<?php

/**
 * This file is part of the DigitalOcean library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DigitalOcean\Tests\CLI\Droplets;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use DigitalOcean\Tests\TestCase;
use DigitalOcean\CLI\Droplets\PowerOffCommand;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class PowerOffCommandTest extends TestCase
{
    protected $application;
    protected $command;
    protected $commandTester;

    protected function setUp()
    {
        $this->application = new Application();

        $result = (object) array(
            'status'   => 'OK',
            'event_id' => 1234,
        );

        $PowerOffCommand = $this->getMock('\DigitalOcean\CLI\Droplets\PowerOffCommand', array('getDigitalOcean'));
        $PowerOffCommand
            ->expects($this->any())
            ->method('getDigitalOcean')
            ->will($this->returnValue($this->getMockDigitalOcean('droplets', $this->getMockDroplets('powerOff', $result))));

        $this->application->add($PowerOffCommand);

        $this->command = $this->application->find('droplets:power-off');

        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments.
     */
    public function testExecuteNotEnoughArguments()
    {
        $this->commandTester->execute(array(
            'command' => $this->command->getName(),
        ));
    }

    public function testExecuteCheckStatus()
    {
        $this->commandTester->execute(array(
            'command' => $this->command->getName(),
            'id'      => 123,
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/\| OK     \|/', $this->commandTester->getDisplay());
    }

    public function testExecuteCheckEventId()
    {
        $this->commandTester->execute(array(
            'command' => $this->command->getName(),
            'id'      => 123,
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/\| 1234     \|/', $this->commandTester->getDisplay());
    }
}
