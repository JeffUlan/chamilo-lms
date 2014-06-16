<?php

namespace ChamiloLMS\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SpecificFieldValues
 *
 * @ORM\Table(name="specific_field_values")
 * @ORM\Entity
 */
class SpecificFieldValues
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
     * @var string
     *
     * @ORM\Column(name="course_code", type="string", length=40, precision=0, scale=0, nullable=false, unique=false)
     */
    private $courseCode;

    /**
     * @var string
     *
     * @ORM\Column(name="tool_id", type="string", length=100, precision=0, scale=0, nullable=false, unique=false)
     */
    private $toolId;

    /**
     * @var integer
     *
     * @ORM\Column(name="ref_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $refId;

    /**
     * @var integer
     *
     * @ORM\Column(name="field_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $fieldId;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=200, precision=0, scale=0, nullable=false, unique=false)
     */
    private $value;


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
     * Set courseCode
     *
     * @param string $courseCode
     * @return SpecificFieldValues
     */
    public function setCourseCode($courseCode)
    {
        $this->courseCode = $courseCode;

        return $this;
    }

    /**
     * Get courseCode
     *
     * @return string
     */
    public function getCourseCode()
    {
        return $this->courseCode;
    }

    /**
     * Set toolId
     *
     * @param string $toolId
     * @return SpecificFieldValues
     */
    public function setToolId($toolId)
    {
        $this->toolId = $toolId;

        return $this;
    }

    /**
     * Get toolId
     *
     * @return string
     */
    public function getToolId()
    {
        return $this->toolId;
    }

    /**
     * Set refId
     *
     * @param integer $refId
     * @return SpecificFieldValues
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;

        return $this;
    }

    /**
     * Get refId
     *
     * @return integer
     */
    public function getRefId()
    {
        return $this->refId;
    }

    /**
     * Set fieldId
     *
     * @param integer $fieldId
     * @return SpecificFieldValues
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
     * Set value
     *
     * @param string $value
     * @return SpecificFieldValues
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
