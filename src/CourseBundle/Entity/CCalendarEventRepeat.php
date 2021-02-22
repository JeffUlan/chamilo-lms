<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CCalendarEventRepeat.
 *
 * @ORM\Table(
 *  name="c_calendar_event_repeat",
 *  indexes={
 *  }
 * )
 * @ORM\Entity
 */
class CCalendarEventRepeat
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
     * @ORM\ManyToOne(targetEntity="Chamilo\CourseBundle\Entity\CCalendarEvent", inversedBy="repeatEvents")
     * @ORM\JoinColumn(name="cal_id", referencedColumnName="iid")
     */
    protected CCalendarEvent $event;

    /**
     * @var string
     *
     * @ORM\Column(name="cal_type", type="string", length=20, nullable=true)
     */
    protected $calType;

    /**
     * @var int
     *
     * @ORM\Column(name="cal_end", type="integer", nullable=true)
     */
    protected $calEnd;

    /**
     * @var int
     *
     * @ORM\Column(name="cal_frequency", type="integer", nullable=true)
     */
    protected $calFrequency;

    /**
     * @var string
     *
     * @ORM\Column(name="cal_days", type="string", length=7, nullable=true)
     */
    protected $calDays;

    /**
     * Set calType.
     *
     * @param string $calType
     */
    public function setCalType($calType): self
    {
        $this->calType = $calType;

        return $this;
    }

    /**
     * Get calType.
     *
     * @return string
     */
    public function getCalType()
    {
        return $this->calType;
    }

    /**
     * Set calEnd.
     *
     * @param int $calEnd
     */
    public function setCalEnd($calEnd): self
    {
        $this->calEnd = $calEnd;

        return $this;
    }

    /**
     * Get calEnd.
     *
     * @return int
     */
    public function getCalEnd()
    {
        return $this->calEnd;
    }

    /**
     * Set calFrequency.
     *
     * @param int $calFrequency
     */
    public function setCalFrequency($calFrequency): self
    {
        $this->calFrequency = $calFrequency;

        return $this;
    }

    /**
     * Get calFrequency.
     *
     * @return int
     */
    public function getCalFrequency()
    {
        return $this->calFrequency;
    }

    /**
     * Set calDays.
     *
     * @param string $calDays
     */
    public function setCalDays($calDays): self
    {
        $this->calDays = $calDays;

        return $this;
    }

    /**
     * Get calDays.
     *
     * @return string
     */
    public function getCalDays()
    {
        return $this->calDays;
    }
}
