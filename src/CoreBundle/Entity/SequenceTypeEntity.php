<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class SequenceTypeEntity.
 *
 * @ORM\Table(name="sequence_type_entity")
 * @ORM\Entity
 */
class SequenceTypeEntity
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    protected int $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected string $name;

    /**
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    protected string $description;

    /**
     * @ORM\Column(name="ent_table", type="string", length=255, nullable=false)
     */
    protected string $entityTable;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return SequenceTypeEntity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return SequenceTypeEntity
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityTable()
    {
        return $this->entityTable;
    }

    /**
     * @param string $entityTable
     *
     * @return SequenceTypeEntity
     */
    public function setEntityTable($entityTable)
    {
        $this->entityTable = $entityTable;

        return $this;
    }
}
