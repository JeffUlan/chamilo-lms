<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class Admin extends \Entity
{
    /**
     * @return \Entity\Repository\AdminRepository
     */
     public static function repository(){
        return \Entity\Repository\AdminRepository::instance();
    }

    /**
     * @return \Entity\Admin
     */
     public static function create(){
        return new self();
    }

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var integer $user_id
     */
    protected $user_id;


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
     * Set user_id
     *
     * @param integer $value
     * @return Admin
     */
    public function set_user_id($value)
    {
        $this->user_id = $value;
        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function get_user_id()
    {
        return $this->user_id;
    }
}