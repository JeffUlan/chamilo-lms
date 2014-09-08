<?php

namespace Chamilo\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ExtraFieldValues
 *
 * @ORM\MappedSuperclass
 */
class ExtraFieldValues
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="field_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $fieldId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tms", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $tms;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="string", precision=0, scale=0, nullable=false, unique=false)
     */
    private $userId;

    /**
     * @var string
     * @Gedmo\Versioned
     *
     * @ORM\Column(name="comment", type="string", precision=0, scale=0, nullable=true, unique=false)
     */
    private $comment;

    /**
     * @ORM\OneToOne(targetEntity="Chamilo\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @param $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user  = $user;

        return $this;
    }

    /**
     * @param $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

     /**
     * Set comment
     *
     * @param string $comment
     * @return ExtraFieldValues
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fieldId
     *
     * @param integer $fieldId
     * @return ExtraFieldValues
     */
    public function setFieldId($fieldId)
    {
        $this->fieldId = $fieldId;

        return $this;
    }

    /**
     * Get fieldId
     *
     * @return integer
     */
    public function getFieldId()
    {
        return $this->fieldId;
    }

    /**
     * Set tms
     *
     * @param \DateTime $tms
     * @return ExtraFieldValues
     */
    public function setTms($tms)
    {
        $this->tms = $tms;

        return $this;
    }

    /**
     * Get tms
     *
     * @return \DateTime
     */
    public function getTms()
    {
        return $this->tms;
    }

     /**
     * Set setUserId
     *
     * @param integer $id
     * @return QuestionFieldValues
     */
    public function setUserId($id)
    {
        $this->userId = $id;
        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
