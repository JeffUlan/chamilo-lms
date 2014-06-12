<?php

namespace ChamiloLMS\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PersonalAgendaRepeat
 *
 * @ORM\Table(name="personal_agenda_repeat")
 * @ORM\Entity
 */
class PersonalAgendaRepeat
{
    /**
     * @var integer
     *
     * @ORM\Column(name="cal_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $calId;

    /**
     * @var string
     *
     * @ORM\Column(name="cal_type", type="string", length=20, precision=0, scale=0, nullable=true, unique=false)
     */
    private $calType;

    /**
     * @var integer
     *
     * @ORM\Column(name="cal_end", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $calEnd;

    /**
     * @var integer
     *
     * @ORM\Column(name="cal_frequency", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $calFrequency;

    /**
     * @var string
     *
     * @ORM\Column(name="cal_days", type="string", length=7, precision=0, scale=0, nullable=true, unique=false)
     */
    private $calDays;


    /**
     * Get calId
     *
     * @return integer 
     */
    public function getCalId()
    {
        return $this->calId;
    }

    /**
     * Set calType
     *
     * @param string $calType
     * @return PersonalAgendaRepeat
     */
    public function setCalType($calType)
    {
        $this->calType = $calType;

        return $this;
    }

    /**
     * Get calType
     *
     * @return string 
     */
    public function getCalType()
    {
        return $this->calType;
    }

    /**
     * Set calEnd
     *
     * @param integer $calEnd
     * @return PersonalAgendaRepeat
     */
    public function setCalEnd($calEnd)
    {
        $this->calEnd = $calEnd;

        return $this;
    }

    /**
     * Get calEnd
     *
     * @return integer 
     */
    public function getCalEnd()
    {
        return $this->calEnd;
    }

    /**
     * Set calFrequency
     *
     * @param integer $calFrequency
     * @return PersonalAgendaRepeat
     */
    public function setCalFrequency($calFrequency)
    {
        $this->calFrequency = $calFrequency;

        return $this;
    }

    /**
     * Get calFrequency
     *
     * @return integer 
     */
    public function getCalFrequency()
    {
        return $this->calFrequency;
    }

    /**
     * Set calDays
     *
     * @param string $calDays
     * @return PersonalAgendaRepeat
     */
    public function setCalDays($calDays)
    {
        $this->calDays = $calDays;

        return $this;
    }

    /**
     * Get calDays
     *
     * @return string 
     */
    public function getCalDays()
    {
        return $this->calDays;
    }
}

