<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class LpIvObjective extends \CourseEntity
{
    /**
     * @return \Entity\Repository\LpIvObjectiveRepository
     */
     public static function repository(){
        return \Entity\Repository\LpIvObjectiveRepository::instance();
    }

    /**
     * @return \Entity\LpIvObjective
     */
     public static function create(){
        return new self();
    }

    /**
     * @var integer $c_id
     */
    protected $c_id;

    /**
     * @var bigint $id
     */
    protected $id;

    /**
     * @var bigint $lp_iv_id
     */
    protected $lp_iv_id;

    /**
     * @var integer $order_id
     */
    protected $order_id;

    /**
     * @var string $objective_id
     */
    protected $objective_id;

    /**
     * @var float $score_raw
     */
    protected $score_raw;

    /**
     * @var float $score_max
     */
    protected $score_max;

    /**
     * @var float $score_min
     */
    protected $score_min;

    /**
     * @var string $status
     */
    protected $status;


    /**
     * Set c_id
     *
     * @param integer $value
     * @return LpIvObjective
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
     * Set id
     *
     * @param bigint $value
     * @return LpIvObjective
     */
    public function set_id($value)
    {
        $this->id = $value;
        return $this;
    }

    /**
     * Get id
     *
     * @return bigint 
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Set lp_iv_id
     *
     * @param bigint $value
     * @return LpIvObjective
     */
    public function set_lp_iv_id($value)
    {
        $this->lp_iv_id = $value;
        return $this;
    }

    /**
     * Get lp_iv_id
     *
     * @return bigint 
     */
    public function get_lp_iv_id()
    {
        return $this->lp_iv_id;
    }

    /**
     * Set order_id
     *
     * @param integer $value
     * @return LpIvObjective
     */
    public function set_order_id($value)
    {
        $this->order_id = $value;
        return $this;
    }

    /**
     * Get order_id
     *
     * @return integer 
     */
    public function get_order_id()
    {
        return $this->order_id;
    }

    /**
     * Set objective_id
     *
     * @param string $value
     * @return LpIvObjective
     */
    public function set_objective_id($value)
    {
        $this->objective_id = $value;
        return $this;
    }

    /**
     * Get objective_id
     *
     * @return string 
     */
    public function get_objective_id()
    {
        return $this->objective_id;
    }

    /**
     * Set score_raw
     *
     * @param float $value
     * @return LpIvObjective
     */
    public function set_score_raw($value)
    {
        $this->score_raw = $value;
        return $this;
    }

    /**
     * Get score_raw
     *
     * @return float 
     */
    public function get_score_raw()
    {
        return $this->score_raw;
    }

    /**
     * Set score_max
     *
     * @param float $value
     * @return LpIvObjective
     */
    public function set_score_max($value)
    {
        $this->score_max = $value;
        return $this;
    }

    /**
     * Get score_max
     *
     * @return float 
     */
    public function get_score_max()
    {
        return $this->score_max;
    }

    /**
     * Set score_min
     *
     * @param float $value
     * @return LpIvObjective
     */
    public function set_score_min($value)
    {
        $this->score_min = $value;
        return $this;
    }

    /**
     * Get score_min
     *
     * @return float 
     */
    public function get_score_min()
    {
        return $this->score_min;
    }

    /**
     * Set status
     *
     * @param string $value
     * @return LpIvObjective
     */
    public function set_status($value)
    {
        $this->status = $value;
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function get_status()
    {
        return $this->status;
    }
}