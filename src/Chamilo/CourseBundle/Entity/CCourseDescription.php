<?php

namespace Chamilo\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CCourseDescription
 *
 * @ORM\Table(name="c_course_description", indexes={@ORM\Index(name="session_id", columns={"session_id"})})
 * @ORM\Entity
 */
class CCourseDescription
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var integer
     *
     * @ORM\Column(name="session_id", type="integer", nullable=true)
     */
    private $sessionId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="description_type", type="boolean", nullable=false)
     */
    private $descriptionType;

    /**
     * @var integer
     *
     * @ORM\Column(name="progress", type="integer", nullable=false)
     */
    private $progress;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="c_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $cId;



    /**
     * Set title
     *
     * @param string $title
     * @return CCourseDescription
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return CCourseDescription
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set sessionId
     *
     * @param integer $sessionId
     * @return CCourseDescription
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId
     *
     * @return integer
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set descriptionType
     *
     * @param boolean $descriptionType
     * @return CCourseDescription
     */
    public function setDescriptionType($descriptionType)
    {
        $this->descriptionType = $descriptionType;

        return $this;
    }

    /**
     * Get descriptionType
     *
     * @return boolean
     */
    public function getDescriptionType()
    {
        return $this->descriptionType;
    }

    /**
     * Set progress
     *
     * @param integer $progress
     * @return CCourseDescription
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * Get progress
     *
     * @return integer
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return CCourseDescription
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set cId
     *
     * @param integer $cId
     * @return CCourseDescription
     */
    public function setCId($cId)
    {
        $this->cId = $cId;

        return $this;
    }

    /**
     * Get cId
     *
     * @return integer
     */
    public function getCId()
    {
        return $this->cId;
    }
}
