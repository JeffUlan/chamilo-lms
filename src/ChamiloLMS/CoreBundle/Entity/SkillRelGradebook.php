<?php

namespace ChamiloLMS\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SkillRelGradebook
 *
 * @ORM\Table(name="skill_rel_gradebook")
 * @ORM\Entity
 */
class SkillRelGradebook
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
     * @ORM\Column(name="gradebook_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $gradebookId;

    /**
     * @var integer
     *
     * @ORM\Column(name="skill_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $skillId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10, precision=0, scale=0, nullable=false, unique=false)
     */
    private $type;


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
     * Set gradebookId
     *
     * @param integer $gradebookId
     * @return SkillRelGradebook
     */
    public function setGradebookId($gradebookId)
    {
        $this->gradebookId = $gradebookId;

        return $this;
    }

    /**
     * Get gradebookId
     *
     * @return integer
     */
    public function getGradebookId()
    {
        return $this->gradebookId;
    }

    /**
     * Set skillId
     *
     * @param integer $skillId
     * @return SkillRelGradebook
     */
    public function setSkillId($skillId)
    {
        $this->skillId = $skillId;

        return $this;
    }

    /**
     * Get skillId
     *
     * @return integer
     */
    public function getSkillId()
    {
        return $this->skillId;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return SkillRelGradebook
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
