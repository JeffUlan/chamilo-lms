<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Chamilo\CoreBundle\Traits\CourseTrait;
use Chamilo\CoreBundle\Traits\UserTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Questions per quiz user attempts.
 *
 * @ORM\Table(
 *     name="track_e_attempt",
 *     indexes={
 *         @ORM\Index(name="course", columns={"c_id"}),
 *         @ORM\Index(name="exe_id", columns={"exe_id"}),
 *         @ORM\Index(name="user_id", columns={"user_id"}),
 *         @ORM\Index(name="question_id", columns={"question_id"}),
 *         @ORM\Index(name="session_id", columns={"session_id"}),
 *         @ORM\Index(name="idx_track_e_attempt_tms", columns={"tms"}),
 *     }
 * )
 * @ORM\Entity
 */
class TrackEAttempt
{
    use CourseTrait;
    use UserTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected int $id;

    /**
     * @ORM\Column(name="exe_id", type="integer", nullable=false)
     */
    #[Assert\NotBlank]
    protected int $exeId;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\User", inversedBy="trackEAttempts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    #[Assert\NotBlank]
    protected User $user;

    /**
     * @ORM\Column(name="question_id", type="integer", nullable=false)
     */
    #[Assert\NotBlank]
    protected ?int $questionId = null;

    /**
     * @ORM\Column(name="answer", type="text", nullable=false)
     */
    protected string $answer;

    /**
     * @ORM\Column(name="teacher_comment", type="text", nullable=false)
     */
    protected string $teacherComment;

    /**
     * @ORM\Column(name="marks", type="float", precision=6, scale=2, nullable=false)
     */
    protected float $marks;

    /**
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    protected ?int $position = null;

    /**
     * @ORM\Column(name="tms", type="datetime", nullable=false)
     */
    protected DateTime $tms;

    /**
     * @ORM\Column(name="session_id", type="integer", nullable=false)
     */
    protected int $sessionId;

    /**
     * @ORM\Column(name="filename", type="string", length=255, nullable=true)
     */
    protected ?string $filename = null;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Course", inversedBy="trackEAttempts")
     * @ORM\JoinColumn(name="c_id", referencedColumnName="id")
     */
    protected Course $course;

    /**
     * @ORM\Column(name="seconds_spent", type="integer")
     */
    protected int $secondsSpent;

    public function __construct()
    {
        $this->teacherComment = '';
        $this->secondsSpent = 0;
    }

    /**
     * Set exeId.
     *
     * @return TrackEAttempt
     */
    public function setExeId(int $exeId)
    {
        $this->exeId = $exeId;

        return $this;
    }

    /**
     * Get exeId.
     *
     * @return int
     */
    public function getExeId()
    {
        return $this->exeId;
    }

    /**
     * Set questionId.
     *
     * @return TrackEAttempt
     */
    public function setQuestionId(int $questionId)
    {
        $this->questionId = $questionId;

        return $this;
    }

    /**
     * Get questionId.
     *
     * @return int
     */
    public function getQuestionId()
    {
        return $this->questionId;
    }

    /**
     * Set answer.
     *
     * @return TrackEAttempt
     */
    public function setAnswer(string $answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer.
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set teacherComment.
     *
     * @return TrackEAttempt
     */
    public function setTeacherComment(string $teacherComment)
    {
        $this->teacherComment = $teacherComment;

        return $this;
    }

    /**
     * Get teacherComment.
     *
     * @return string
     */
    public function getTeacherComment()
    {
        return $this->teacherComment;
    }

    /**
     * Set marks.
     *
     * @return TrackEAttempt
     */
    public function setMarks(float $marks)
    {
        $this->marks = $marks;

        return $this;
    }

    /**
     * Get marks.
     *
     * @return float
     */
    public function getMarks()
    {
        return $this->marks;
    }

    /**
     * Set position.
     *
     * @return TrackEAttempt
     */
    public function setPosition(int $position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set tms.
     *
     * @return TrackEAttempt
     */
    public function setTms(DateTime $tms)
    {
        $this->tms = $tms;

        return $this;
    }

    /**
     * Get tms.
     *
     * @return DateTime
     */
    public function getTms()
    {
        return $this->tms;
    }

    /**
     * Set sessionId.
     *
     * @return TrackEAttempt
     */
    public function setSessionId(int $sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId.
     *
     * @return int
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set filename.
     *
     * @return TrackEAttempt
     */
    public function setFilename(string $filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getSecondsSpent(): int
    {
        return $this->secondsSpent;
    }

    public function setSecondsSpent(int $secondsSpent): self
    {
        $this->secondsSpent = $secondsSpent;

        return $this;
    }
}
