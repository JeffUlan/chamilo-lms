<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity\Listener;

use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Entity\Message;
use Doctrine\ORM\Event\LifecycleEventArgs;

class MessageListener
{
    /**
     * This code is executed when a new course is created.
     *
     * new object : prePersist
     * edited object: preUpdate
     */
    public function prePersist(Message $message, LifecycleEventArgs $args): void
    {
    }

    public function postPersist(Message $message, LifecycleEventArgs $args): void
    {
        if ($message) {
            // Creates an outbox version, if message is sent in the inbox.
            if (Message::MESSAGE_TYPE_INBOX === $message->getMsgStatus()) {
                $messageSent = clone $message;
                $messageSent->setMsgStatus(Message::MESSAGE_TYPE_OUTBOX);

                $args->getEntityManager()->persist($messageSent);
                $args->getEntityManager()->flush();
            }
        }
    }

    public function preUpdate(Course $course, LifecycleEventArgs $args): void
    {
    }
}
