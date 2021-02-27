<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * SkillRelCourse.
 *
 * @ORM\Table(name="skill_rel_course")
 * @ORM\Entity
 */
class SkillRelCourse
{
    use TimestampableEntity;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Skill", inversedBy="courses")
     * @ORM\JoinColumn(name="skill_id", referencedColumnName="id")
     */
    protected Skill $skill;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Course", inversedBy="skills", cascade={"persist"})
     * @ORM\JoinColumn(name="c_id", referencedColumnName="id", nullable=false)
     */
    protected Course $course;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Session", inversedBy="skills", cascade={"persist"})
     * @ORM\JoinColumn(name="session_id", referencedColumnName="id", nullable=false)
     */
    protected Session $session;

    public function __construct()
    {
        $this->createdAt = new DateTime('now');
        $this->updatedAt = new DateTime('now');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Skill
     */
    public function getSkill()
    {
        return $this->skill;
    }

    /**
     * @param Skill $skill
     *
     * @return SkillRelCourse
     */
    public function setSkill($skill)
    {
        $this->skill = $skill;

        return $this;
    }

    /**
     * @return Course
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param Course $course
     *
     * @return SkillRelCourse
     */
    public function setCourse($course)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param Session $session
     *
     * @return SkillRelCourse
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     *
     * @return SkillRelCourse
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

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
     * @param DateTime $updatedAt
     *
     * @return SkillRelCourse
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
