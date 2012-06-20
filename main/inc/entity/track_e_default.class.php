<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class TrackEDefault extends \Entity
{
    /**
     * @return \Entity\Repository\TrackEDefaultRepository
     */
     public static function repository(){
        return \Entity\Repository\TrackEDefaultRepository::instance();
    }

    /**
     * @return \Entity\TrackEDefault
     */
     public static function create(){
        return new self();
    }

    /**
     * @var integer $default_id
     */
    protected $default_id;

    /**
     * @var integer $default_user_id
     */
    protected $default_user_id;

    /**
     * @var string $default_cours_code
     */
    protected $default_cours_code;

    /**
     * @var datetime $default_date
     */
    protected $default_date;

    /**
     * @var string $default_event_type
     */
    protected $default_event_type;

    /**
     * @var string $default_value_type
     */
    protected $default_value_type;

    /**
     * @var text $default_value
     */
    protected $default_value;

    /**
     * @var integer $c_id
     */
    protected $c_id;


    /**
     * Get default_id
     *
     * @return integer 
     */
    public function get_default_id()
    {
        return $this->default_id;
    }

    /**
     * Set default_user_id
     *
     * @param integer $value
     * @return TrackEDefault
     */
    public function set_default_user_id($value)
    {
        $this->default_user_id = $value;
        return $this;
    }

    /**
     * Get default_user_id
     *
     * @return integer 
     */
    public function get_default_user_id()
    {
        return $this->default_user_id;
    }

    /**
     * Set default_cours_code
     *
     * @param string $value
     * @return TrackEDefault
     */
    public function set_default_cours_code($value)
    {
        $this->default_cours_code = $value;
        return $this;
    }

    /**
     * Get default_cours_code
     *
     * @return string 
     */
    public function get_default_cours_code()
    {
        return $this->default_cours_code;
    }

    /**
     * Set default_date
     *
     * @param datetime $value
     * @return TrackEDefault
     */
    public function set_default_date($value)
    {
        $this->default_date = $value;
        return $this;
    }

    /**
     * Get default_date
     *
     * @return datetime 
     */
    public function get_default_date()
    {
        return $this->default_date;
    }

    /**
     * Set default_event_type
     *
     * @param string $value
     * @return TrackEDefault
     */
    public function set_default_event_type($value)
    {
        $this->default_event_type = $value;
        return $this;
    }

    /**
     * Get default_event_type
     *
     * @return string 
     */
    public function get_default_event_type()
    {
        return $this->default_event_type;
    }

    /**
     * Set default_value_type
     *
     * @param string $value
     * @return TrackEDefault
     */
    public function set_default_value_type($value)
    {
        $this->default_value_type = $value;
        return $this;
    }

    /**
     * Get default_value_type
     *
     * @return string 
     */
    public function get_default_value_type()
    {
        return $this->default_value_type;
    }

    /**
     * Set default_value
     *
     * @param text $value
     * @return TrackEDefault
     */
    public function set_default_value($value)
    {
        $this->default_value = $value;
        return $this;
    }

    /**
     * Get default_value
     *
     * @return text 
     */
    public function get_default_value()
    {
        return $this->default_value;
    }

    /**
     * Set c_id
     *
     * @param integer $value
     * @return TrackEDefault
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
}