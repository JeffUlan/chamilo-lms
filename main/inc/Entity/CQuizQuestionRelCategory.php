<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CQuizQuestionRelCategory
 *
 * @ORM\Table(name="c_quiz_question_rel_category")
 * @ORM\Entity
 */
class CQuizQuestionRelCategory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="iid", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $iid;

    /**
     * @var integer
     *
     * @ORM\Column(name="c_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $cId;

    /**
     * @var integer
     *
     * @ORM\Column(name="question_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $questionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="category_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $categoryId;

    /** Relationships */

    /**
     * @ORM\ManyToOne(targetEntity="CQuizCategory", fetch="EAGER" )
     * @ORM\JoinColumn(name="category_id", referencedColumnName="iid")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="CQuizQuestion", fetch="EAGER" )
     * @ORM\JoinColumn(name="question_id", referencedColumnName="iid")
     */
    private $question;

    public function __construct(CQuizCategory $category, CQuizQuestion $question)
    {
        $this->category = $category;
        $this->question = $question;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getQuestion()
    {
        return $this->question;
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
     * Set cId
     *
     * @param integer $cId
     * @return CQuizQuestionRelCategory
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
     * Set questionId
     *
     * @param integer $questionId
     * @return CQuizQuestionRelCategory
     */
    public function setQuestionId($questionId)
    {
        $this->questionId = $questionId;

        return $this;
    }

    /**
     * Get questionId
     *
     * @return integer
     */
    public function getQuestionId()
    {
        return $this->questionId;
    }

    /**
     * Set categoryId
     *
     * @param integer $categoryId
     * @return CQuizQuestionRelCategory
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }
}
