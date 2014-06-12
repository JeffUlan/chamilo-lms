<?php

namespace ChamiloLMS\CourseBundle\Entity;

<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * CSurveyAnswer
 *
 * @ORM\Table(name="c_survey_answer")
 * @ORM\Entity
 */
class CSurveyAnswer
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
     */
    private $cId;

    /**
     * @var integer
     *
     * @ORM\Column(name="answer_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $answerId;

    /**
     * @var integer
     *
     * @ORM\Column(name="survey_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $surveyId;

    /**
     * @var integer
     *
     * @ORM\Column(name="question_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $questionId;

    /**
     * @var string
     *
     * @ORM\Column(name="option_id", type="text", precision=0, scale=0, nullable=false, unique=false)
     */
    private $optionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=250, precision=0, scale=0, nullable=false, unique=false)
     */
    private $user;


    /**
     * Get iid
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
     * @return CSurveyAnswer
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
     * Set answerId
     *
     * @param integer $answerId
     * @return CSurveyAnswer
     */
    public function setAnswerId($answerId)
    {
        $this->answerId = $answerId;

        return $this;
    }

    /**
     * Get answerId
     *
     * @return integer 
     */
    public function getAnswerId()
    {
        return $this->answerId;
    }

    /**
     * Set surveyId
     *
     * @param integer $surveyId
     * @return CSurveyAnswer
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;

        return $this;
    }

    /**
     * Get surveyId
     *
     * @return integer 
     */
    public function getSurveyId()
    {
        return $this->surveyId;
    }

    /**
     * Set questionId
     *
     * @param integer $questionId
     * @return CSurveyAnswer
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
     * Set optionId
     *
     * @param string $optionId
     * @return CSurveyAnswer
     */
    public function setOptionId($optionId)
    {
        $this->optionId = $optionId;

        return $this;
    }

    /**
     * Get optionId
     *
     * @return string 
     */
    public function getOptionId()
    {
        return $this->optionId;
    }

    /**
     * Set value
     *
     * @param integer $value
     * @return CSurveyAnswer
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set user
     *
     * @param string $user
     * @return CSurveyAnswer
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }
}

