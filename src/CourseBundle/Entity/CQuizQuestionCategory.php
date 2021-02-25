<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Entity;

use Chamilo\CoreBundle\Entity\AbstractResource;
use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Entity\ResourceInterface;
use Chamilo\CoreBundle\Entity\Session;
use Chamilo\CourseBundle\Traits\ShowCourseResourcesInSessionTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CQuizQuestionCategory.
 *
 * @ORM\Table(
 *  name="c_quiz_question_category",
 *  indexes={
 *      @ORM\Index(name="course", columns={"c_id"})
 *  }
 * )
 * @ORM\Entity
 */
class CQuizQuestionCategory extends AbstractResource implements ResourceInterface
{
    use ShowCourseResourcesInSessionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="iid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $iid;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    protected string $title;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected ?string $description;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Course")
     * @ORM\JoinColumn(name="c_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Course $course;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Session", cascade={"persist"})
     * @ORM\JoinColumn(name="session_id", referencedColumnName="id", nullable=true)
     */
    protected ?Session $session;

    /**
     * @var Collection|CQuizQuestion[]
     *
     * @ORM\ManyToMany(targetEntity="Chamilo\CourseBundle\Entity\CQuizQuestion", mappedBy="categories")
     */
    protected $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function addQuestion(CQuizQuestion $question): void
    {
        if ($this->questions->contains($question)) {
            return;
        }

        $this->questions->add($question);
        $question->addCategory($this);
    }

    public function removeQuestion(CQuizQuestion $question): void
    {
        if (!$this->questions->contains($question)) {
            return;
        }

        $this->questions->removeElement($question);
        $question->removeCategory($this);
    }

    public function getIid(): int
    {
        return $this->iid;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function setCourse(Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    /**
     * @param Session $session
     */
    public function setSession($session): self
    {
        $this->session = $session;

        return $this;
    }

    public function hasSession(): bool
    {
        return null !== $this->session;
    }

    /**
     * @ORM\PostPersist()
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        // Update id with iid value
        /*$em = $args->getEntityManager();
        $em->persist($this);
        $em->flush();*/
    }

    /**
     * @return CQuizQuestion[]|Collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @param CQuizQuestion[]|Collection $questions
     */
    public function setQuestions($questions): self
    {
        $this->questions = $questions;

        return $this;
    }

    public function getResourceIdentifier(): int
    {
        return $this->getIid();
    }

    public function getResourceName(): string
    {
        return $this->getTitle();
    }

    public function setResourceName(string $name): self
    {
        return $this->setTitle($name);
    }
}
