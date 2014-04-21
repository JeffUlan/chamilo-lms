<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class GroupRelTag extends \Entity
{
    /**
     * @return \Entity\Repository\GroupRelTagRepository
     */
     public static function repository(){
        return \Entity\Repository\GroupRelTagRepository::instance();
    }

    /**
     * @return \Entity\GroupRelTag
     */
     public static function create(){
        return new self();
    }

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var integer $tag_id
     */
    protected $tag_id;

    /**
     * @var integer $group_id
     */
    protected $group_id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Set tag_id
     *
     * @param integer $value
     * @return GroupRelTag
     */
    public function set_tag_id($value)
    {
        $this->tag_id = $value;
        return $this;
    }

    /**
     * Get tag_id
     *
     * @return integer 
     */
    public function get_tag_id()
    {
        return $this->tag_id;
    }

    /**
     * Set group_id
     *
     * @param integer $value
     * @return GroupRelTag
     */
    public function set_group_id($value)
    {
        $this->group_id = $value;
        return $this;
    }

    /**
     * Get group_id
     *
     * @return integer 
     */
    public function get_group_id()
    {
        return $this->group_id;
    }
}