<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CCourseDescription.
 *
 * @ORM\Table(name="c_course_description", indexes={@ORM\Index(name="session_id", columns={"session_id"})})
 * @ORM\Entity
 */
class CCourseDescription
{
    const TYPE_DESCRIPTION = 1;
    const TYPE_OBJECTIVES = 2;
    const TYPE_TOPICS = 3;
    const TYPE_METHODOLOGY = 4;
    const TYPE_COURSE_MATERIAL = 5;
    const TYPE_RESOURCES = 6;
    const TYPE_ASSESSMENT = 7;
    const TYPE_CUSTOM = 8;

    /**
     * @var int
     *
     * @ORM\Column(name="iid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $iid;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=true)
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="c_id", type="integer")
     */
    private $cId;

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
     * @var int
     *
     * @ORM\Column(name="session_id", type="integer", nullable=true)
     */
    private $sessionId;

    /**
     * @var int
     *
     * @ORM\Column(name="description_type", type="integer", nullable=false)
     */
    private $descriptionType;

    /**
     * @var int
     *
     * @ORM\Column(name="progress", type="integer", nullable=false)
     */
    private $progress;

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return CCourseDescription
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return CCourseDescription
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set sessionId.
     *
     * @param int $sessionId
     *
     * @return CCourseDescription
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId.
     *
     * @return int
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set descriptionType.
     *
     * @param int $descriptionType
     *
     * @return CCourseDescription
     */
    public function setDescriptionType($descriptionType)
    {
        $this->descriptionType = $descriptionType;

        return $this;
    }

    /**
     * Get descriptionType.
     *
     * @return int
     */
    public function getDescriptionType()
    {
        return $this->descriptionType;
    }

    /**
     * Set progress.
     *
     * @param int $progress
     *
     * @return CCourseDescription
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * Get progress.
     *
     * @return int
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return CCourseDescription
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cId.
     *
     * @param int $cId
     *
     * @return CCourseDescription
     */
    public function setCId($cId)
    {
        $this->cId = $cId;

        return $this;
    }

    /**
     * Get cId.
     *
     * @return int
     */
    public function getCId()
    {
        return $this->cId;
    }

    /**
     * @return int
     */
    public function getIid()
    {
        return $this->iid;
    }

    /**
     * @param int $iid
     *
     * @return CCourseDescription
     */
    public function setIid($iid)
    {
        $this->iid = $iid;

        return $this;
    }
}
