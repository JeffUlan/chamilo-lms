<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CAttendanceCalendar.
 *
 * @ORM\Table(
 *  name="c_attendance_calendar",
 *  indexes={
 *      @ORM\Index(name="attendance_id", columns={"attendance_id"}),
 *      @ORM\Index(name="done_attendance", columns={"done_attendance"})
 *  }
 * )
 * @ORM\Entity
 */
class CAttendanceCalendar
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
     * @var int
     *
     * @ORM\Column(name="attendance_id", type="integer", nullable=false)
     */
    protected $attendanceId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_time", type="datetime", nullable=false)
     */
    protected $dateTime;

    /**
     * @var bool
     *
     * @ORM\Column(name="done_attendance", type="boolean", nullable=false)
     */
    protected $doneAttendance;

    public function getIid(): int
    {
        return $this->iid;
    }

    /**
     * Set attendanceId.
     *
     * @param int $attendanceId
     *
     * @return CAttendanceCalendar
     */
    public function setAttendanceId($attendanceId)
    {
        $this->attendanceId = $attendanceId;

        return $this;
    }

    /**
     * Get attendanceId.
     *
     * @return int
     */
    public function getAttendanceId()
    {
        return $this->attendanceId;
    }

    /**
     * Set dateTime.
     *
     * @param \DateTime $dateTime
     *
     * @return CAttendanceCalendar
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime.
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set doneAttendance.
     *
     * @param bool $doneAttendance
     *
     * @return CAttendanceCalendar
     */
    public function setDoneAttendance($doneAttendance)
    {
        $this->doneAttendance = $doneAttendance;

        return $this;
    }

    /**
     * Get doneAttendance.
     *
     * @return bool
     */
    public function getDoneAttendance()
    {
        return $this->doneAttendance;
    }
}
