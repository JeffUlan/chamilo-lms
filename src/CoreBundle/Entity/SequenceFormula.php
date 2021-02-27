<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class SequenceFormula.
 *
 * @ORM\Table(name="sequence_formula")
 * @ORM\Entity
 */
class SequenceFormula
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="SequenceMethod")
     * @ORM\JoinColumn(name="sequence_method_id", referencedColumnName="id")
     *
     * @var null|\Chamilo\CoreBundle\Entity\SequenceMethod
     */
    protected $method;

    /**
     * @ORM\ManyToOne(targetEntity="SequenceVariable")
     * @ORM\JoinColumn(name="sequence_variable_id", referencedColumnName="id")
     *
     * @var null|\Chamilo\CoreBundle\Entity\SequenceVariable
     */
    protected $variable;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return SequenceFormula
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * @return SequenceFormula
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;

        return $this;
    }
}
