<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\Tests\CoreBundle\Repository;

use Chamilo\CoreBundle\Entity\Message;
use Chamilo\CoreBundle\Repository\MessageRepository;
use Chamilo\Tests\ChamiloTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \MessageRepository
 */
class MessageRepositoryTest extends WebTestCase
{
    use ChamiloTestTrait;

    public function testCreateMessage(): void
    {
        self::bootKernel();
        $repo = self::getContainer()->get(MessageRepository::class);

        $message =
            (new Message())
                ->setTitle('hello')
                ->setContent('content')
                ->setMsgStatus(Message::MESSAGE_TYPE_INBOX)
                ->setUserSender($this->getUser('admin'))
        ;

        $this->assertHasNoEntityViolations($message);

        $repo->update($message);

        $count = $repo->count([]);

        $this->assertSame(1, $count);
    }
}
