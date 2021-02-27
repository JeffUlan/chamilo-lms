<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CSurveyQuestion.
 *
 * @ORM\Table(
 *  name="c_survey_question",
 *  indexes={
 *     @ORM\Index(name="course", columns={"c_id"}),
 *  }
 * )
 * @ORM\Entity
 */
class CSurveyQuestion
{
    /**
     * @var int
     *
     * @ORM\Column(name="iid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $iid;

    /**
     * @var CSurveyQuestion
     *
     * @ORM\ManyToOne(targetEntity="CSurveyQuestion", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="iid")
     */
    protected $parent;

    /**
     * @var ArrayCollection|CSurveyQuestion[]
     * @ORM\OneToMany(targetEntity="CSurveyQuestion", mappedBy="parentEvent")
     */
    protected $children;

    /**
     * @var CSurveyQuestionOption
     *
     * @ORM\ManyToOne(targetEntity="Chamilo\CourseBundle\Entity\CSurveyQuestionOption")
     * @ORM\JoinColumn(name="parent_option_id", referencedColumnName="iid")
     */
    protected $parentOption;

    /**
     * @var int
     *
     * @ORM\Column(name="c_id", type="integer")
     */
    protected $cId;

    /**
     * @var int
     *
     * @ORM\Column(name="survey_id", type="integer", nullable=false)
     */
    protected $surveyId;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="survey_question", type="text", nullable=false)
     */
    protected string $surveyQuestion;

    /**
     * @ORM\Column(name="survey_question_comment", type="text", nullable=false)
     */
    protected ?string $surveyQuestionComment;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=250, nullable=false)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="display", type="string", length=10, nullable=false)
     */
    protected $display;

    /**
     * @var int
     *
     * @ORM\Column(name="sort", type="integer", nullable=false)
     */
    protected $sort;

    /**
     * @var int
     *
     * @ORM\Column(name="shared_question_id", type="integer", nullable=true)
     */
    protected $sharedQuestionId;

    /**
     * @var int
     *
     * @ORM\Column(name="max_value", type="integer", nullable=true)
     */
    protected $maxValue;

    /**
     * @var int
     *
     * @ORM\Column(name="survey_group_pri", type="integer", nullable=false)
     */
    protected $surveyGroupPri;

    /**
     * @var int
     *
     * @ORM\Column(name="survey_group_sec1", type="integer", nullable=false)
     */
    protected $surveyGroupSec1;

    /**
     * @var int
     *
     * @ORM\Column(name="survey_group_sec2", type="integer", nullable=false)
     */
    protected $surveyGroupSec2;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_required", type="boolean", options={"default": false})
     */
    protected $isMandatory = false;

    public function __construct()
    {
        $this->surveyGroupPri = 0;
        $this->surveyGroupSec1 = 0;
        $this->surveyGroupSec2 = 0;
    }

    public function getIid(): int
    {
        return $this->iid;
    }

    /**
     * Set surveyId.
     *
     * @param int $surveyId
     *
     * @return CSurveyQuestion
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;

        return $this;
    }

    /**
     * Get surveyId.
     *
     * @return int
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }

    /**
     * Set surveyQuestion.
     *
     * @param string $surveyQuestion
     *
     * @return CSurveyQuestion
     */
    public function setSurveyQuestion($surveyQuestion)
    {
        $this->surveyQuestion = $surveyQuestion;

        return $this;
    }

    /**
     * Get surveyQuestion.
     *
     * @return string
     */
    public function getSurveyQuestion()
    {
        return $this->surveyQuestion;
    }

    /**
     * Set surveyQuestionComment.
     *
     * @param string $surveyQuestionComment
     *
     * @return CSurveyQuestion
     */
    public function setSurveyQuestionComment($surveyQuestionComment)
    {
        $this->surveyQuestionComment = $surveyQuestionComment;

        return $this;
    }

    /**
     * Get surveyQuestionComment.
     *
     * @return string
     */
    public function getSurveyQuestionComment()
    {
        return $this->surveyQuestionComment;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return CSurveyQuestion
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set display.
     *
     * @param string $display
     *
     * @return CSurveyQuestion
     */
    public function setDisplay($display)
    {
        $this->display = $display;

        return $this;
    }

    /**
     * Get display.
     *
     * @return string
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * Set sort.
     *
     * @param int $sort
     *
     * @return CSurveyQuestion
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort.
     *
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    public function setSharedQuestionId(int $sharedQuestionId): self
    {
        $this->sharedQuestionId = $sharedQuestionId;

        return $this;
    }

    /**
     * Get sharedQuestionId.
     *
     * @return int
     */
    public function getSharedQuestionId()
    {
        return $this->sharedQuestionId;
    }

    /**
     * Set maxValue.
     *
     * @param int $maxValue
     *
     * @return CSurveyQuestion
     */
    public function setMaxValue($maxValue)
    {
        $this->maxValue = $maxValue;

        return $this;
    }

    /**
     * Get maxValue.
     *
     * @return int
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * Set surveyGroupPri.
     *
     * @param int $surveyGroupPri
     *
     * @return CSurveyQuestion
     */
    public function setSurveyGroupPri($surveyGroupPri)
    {
        $this->surveyGroupPri = $surveyGroupPri;

        return $this;
    }

    /**
     * Get surveyGroupPri.
     *
     * @return int
     */
    public function getSurveyGroupPri()
    {
        return $this->surveyGroupPri;
    }

    /**
     * Set surveyGroupSec1.
     *
     * @param int $surveyGroupSec1
     *
     * @return CSurveyQuestion
     */
    public function setSurveyGroupSec1($surveyGroupSec1)
    {
        $this->surveyGroupSec1 = $surveyGroupSec1;

        return $this;
    }

    /**
     * Get surveyGroupSec1.
     *
     * @return int
     */
    public function getSurveyGroupSec1()
    {
        return $this->surveyGroupSec1;
    }

    /**
     * Set surveyGroupSec2.
     *
     * @param int $surveyGroupSec2
     *
     * @return CSurveyQuestion
     */
    public function setSurveyGroupSec2($surveyGroupSec2)
    {
        $this->surveyGroupSec2 = $surveyGroupSec2;

        return $this;
    }

    /**
     * Get surveyGroupSec2.
     *
     * @return int
     */
    public function getSurveyGroupSec2()
    {
        return $this->surveyGroupSec2;
    }

    /**
     * Set cId.
     *
     * @param int $cId
     *
     * @return CSurveyQuestion
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

    public function isMandatory(): bool
    {
        return $this->isMandatory;
    }

    public function setIsMandatory(bool $isMandatory): self
    {
        $this->isMandatory = $isMandatory;

        return $this;
    }

    public function getParent(): self
    {
        return $this->parent;
    }

    public function setParent(self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return ArrayCollection|CSurveyQuestion[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param ArrayCollection|CSurveyQuestion[] $children
     */
    public function setChildren($children): self
    {
        $this->children = $children;

        return $this;
    }

    public function getParentOption(): CSurveyQuestionOption
    {
        return $this->parentOption;
    }

    public function setParentOption(CSurveyQuestionOption $parentOption): self
    {
        $this->parentOption = $parentOption;

        return $this;
    }
}
