<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FieldRelTag.
 *
 * @ORM\Table(
 *     name="extra_field_rel_tag",
 *     indexes={
 *         @ORM\Index(name="field", columns={"field_id"}),
 *         @ORM\Index(name="item", columns={"item_id"}),
 *         @ORM\Index(name="tag", columns={"tag_id"}),
 *         @ORM\Index(name="field_item_tag", columns={"field_id", "item_id", "tag_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Chamilo\CoreBundle\Repository\ExtraFieldRelTagRepository")
 */
class ExtraFieldRelTag
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected int $id;

    /**
     * @ORM\Column(name="field_id", type="integer", nullable=false)
     */
    protected int $fieldId;

    /**
     * @ORM\Column(name="tag_id", type="integer", nullable=false)
     */
    protected int $tagId;

    /**
     * @ORM\Column(name="item_id", type="integer", nullable=false)
     */
    protected int $itemId;

    /**
     * Set fieldId.
     *
     * @param int $fieldId
     *
     * @return ExtraFieldRelTag
     */
    public function setFieldId($fieldId)
    {
        $this->fieldId = $fieldId;

        return $this;
    }

    /**
     * Set tagId.
     *
     * @param int $tagId
     *
     * @return ExtraFieldRelTag
     */
    public function setTagId($tagId)
    {
        $this->tagId = $tagId;

        return $this;
    }

    /**
     * Set itemId.
     *
     * @param int $itemId
     *
     * @return ExtraFieldRelTag
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get fieldId.
     *
     * @return int
     */
    public function getFieldId()
    {
        return $this->fieldId;
    }

    /**
     * Get tagId.
     *
     * @return int
     */
    public function getTagId()
    {
        return $this->tagId;
    }

    /**
     * Get itemId.
     *
     * @return int
     */
    public function getItemId()
    {
        return $this->itemId;
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
}
