<?php

namespace ChamiloLMS\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CQuizQuestion
 *
 * @ORM\Table(name="c_quiz_question", indexes={@ORM\Index(name="idx_c_q_qst_cpt", columns={"c_id", "parent_id", "type"})})
 * @ORM\Entity(repositoryClass="ChamiloLMS\CourseBundle\Entity\Repository\CQuizQuestionRepository")
 */
class CQuizQuestion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="iid", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $iid;

    /**
     * @var integer
     *
     * @ORM\Column(name="c_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $cId;


    /**
     * @var string
     *
     * @ORM\Column(name="question", type="text", precision=0, scale=0, nullable=false, unique=false)
     */
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", precision=0, scale=0, nullable=true, unique=false)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="ponderation", type="float", precision=0, scale=0, nullable=false, unique=false)
     */
    private $ponderation;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $position;

    /**
     * @var boolean
     *
     * @ORM\Column(name="type", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=50, precision=0, scale=0, nullable=true, unique=false)
     */
    private $picture;

    /**
     * @var integer
     *
     * @ORM\Column(name="level", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $level;

    /**
     * @var string
     *
     * @ORM\Column(name="extra", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $extra;

    /**
     * @var string
     *
     * @ORM\Column(name="question_code", type="string", length=10, precision=0, scale=0, nullable=true, unique=false)
     */
    private $questionCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="parent_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $parentId;

    /**
     * @ORM\OneToMany(targetEntity="CQuizQuestionRelCategory", mappedBy="question")
     **/
    private $quizQuestionRelCategoryList;

    /**
     * @ORM\OneToMany(targetEntity="ChamiloLMS\CoreBundle\Entity\QuestionFieldValues", mappedBy="question")
     */
    private $extraFields;

    public function __construct()
    {
        $this->quizQuestionRelCategoryList = new ArrayCollection();
        $this->extraFields = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getCategories()
    {
        return $this->quizQuestionRelCategoryList;
    }

    /**
     * Set cId
     *
     * @param integer $cId
     * @return CQuizQuestion
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

    /**
     * Set id
     *
     * @param integer $id
     * @return CQuizQuestionCategory
     */
    public function setIid($id)
    {
        $this->iid = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getIid()
    {
        return $this->iid;
    }


    /**
     * Set question
     *
     * @param string $question
     * @return CQuizQuestion
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return CQuizQuestion
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set ponderation
     *
     * @param float $ponderation
     * @return CQuizQuestion
     */
    public function setPonderation($ponderation)
    {
        $this->ponderation = $ponderation;

        return $this;
    }

    /**
     * Get ponderation
     *
     * @return float
     */
    public function getPonderation()
    {
        return $this->ponderation;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return CQuizQuestion
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set type
     *
     * @param boolean $type
     * @return CQuizQuestion
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return boolean
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set picture
     *
     * @param string $picture
     * @return CQuizQuestion
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set level
     *
     * @param integer $level
     * @return CQuizQuestion
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set extra
     *
     * @param string $extra
     * @return CQuizQuestion
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * Get extra
     *
     * @return string
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * Set questionCode
     *
     * @param string $questionCode
     * @return CQuizQuestion
     */
    public function setQuestionCode($questionCode)
    {
        $this->questionCode = $questionCode;

        return $this;
    }

    /**
     * Get questionCode
     *
     * @return string
     */
    public function getQuestionCode()
    {
        return $this->questionCode;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     * @return CQuizQuestion
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parentId;
    }
}
