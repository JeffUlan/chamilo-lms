<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class DropboxPerson extends \CourseEntity
{
    /**
     * @return \Entity\Repository\DropboxPersonRepository
     */
     public static function repository(){
        return \Entity\Repository\DropboxPersonRepository::instance();
    }

    /**
     * @return \Entity\DropboxPerson
     */
     public static function create(){
        return new self();
    }

    /**
     * @var integer $c_id
     */
    protected $c_id;

    /**
     * @var integer $file_id
     */
    protected $file_id;

    /**
     * @var integer $user_id
     */
    protected $user_id;


    /**
     * Set c_id
     *
     * @param integer $value
     * @return DropboxPerson
     */
    public function set_c_id($value)
    {
        $this->c_id = $value;
        return $this;
    }

    /**
     * Get c_id
     *
     * @return integer 
     */
    public function get_c_id()
    {
        return $this->c_id;
    }

    /**
     * Set file_id
     *
     * @param integer $value
     * @return DropboxPerson
     */
    public function set_file_id($value)
    {
        $this->file_id = $value;
        return $this;
    }

    /**
     * Get file_id
     *
     * @return integer 
     */
    public function get_file_id()
    {
        return $this->file_id;
    }

    /**
     * Set user_id
     *
     * @param integer $value
     * @return DropboxPerson
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