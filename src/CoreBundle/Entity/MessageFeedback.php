<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Chamilo\CoreBundle\Traits\UserTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class MessageFeedback.
 *
 * @ORM\Table(
 *     name="message_feedback",
 *     indexes={
 *         @Index(name="idx_message_feedback_uid_mid", columns={"message_id", "user_id"})
 *     }
 * )
 * @ORM\Entity()
 */
class MessageFeedback
{
    use UserTrait;

    /**
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     */
    protected int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Message", inversedBy="likes")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Message $message;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected User $user;

    /**
     * @ORM\Column(name="liked", type="boolean", options={"default":false})
     */
    protected bool $liked;

    /**
     * @ORM\Column(name="disliked", type="boolean", options={"default":false})
     */
    protected bool $disliked;

    /**
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    protected DateTime $updatedAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return MessageFeedback
     */
    public function setMessage(Message $message)
    {
        $this->message = $message;

        return $this;
    }

    public function isLiked(): bool
    {
        return $this->liked;
    }

    /**
     * @param bool $liked
     *
     * @return MessageFeedback
     */
    public function setLiked($liked)
    {
        $this->liked = $liked;

        return $this;
    }

    public function isDisliked(): bool
    {
        return $this->disliked;
    }

    /**
     * @param bool $disliked
     *
     * @return MessageFeedback
     */
    public function setDisliked($disliked)
    {
        $this->disliked = $disliked;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return MessageFeedback
     */
    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
