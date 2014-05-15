<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class OpenidAssociation extends \Entity
{
    /**
     * @return \Entity\Repository\OpenidAssociationRepository
     */
     public static function repository(){
        return \Entity\Repository\OpenidAssociationRepository::instance();
    }

    /**
     * @return \Entity\OpenidAssociation
     */
     public static function create(){
        return new self();
    }

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var text $idp_endpoint_uri
     */
    protected $idp_endpoint_uri;

    /**
     * @var string $session_type
     */
    protected $session_type;

    /**
     * @var text $assoc_handle
     */
    protected $assoc_handle;

    /**
     * @var text $assoc_type
     */
    protected $assoc_type;

    /**
     * @var bigint $expires_in
     */
    protected $expires_in;

    /**
     * @var text $mac_key
     */
    protected $mac_key;

    /**
     * @var bigint $created
     */
    protected $created;


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
     * Set idp_endpoint_uri
     *
     * @param text $value
     * @return OpenidAssociation
     */
    public function set_idp_endpoint_uri($value)
    {
        $this->idp_endpoint_uri = $value;
        return $this;
    }

    /**
     * Get idp_endpoint_uri
     *
     * @return text 
     */
    public function get_idp_endpoint_uri()
    {
        return $this->idp_endpoint_uri;
    }

    /**
     * Set session_type
     *
     * @param string $value
     * @return OpenidAssociation
     */
    public function set_session_type($value)
    {
        $this->session_type = $value;
        return $this;
    }

    /**
     * Get session_type
     *
     * @return string 
     */
    public function get_session_type()
    {
        return $this->session_type;
    }

    /**
     * Set assoc_handle
     *
     * @param text $value
     * @return OpenidAssociation
     */
    public function set_assoc_handle($value)
    {
        $this->assoc_handle = $value;
        return $this;
    }

    /**
     * Get assoc_handle
     *
     * @return text 
     */
    public function get_assoc_handle()
    {
        return $this->assoc_handle;
    }

    /**
     * Set assoc_type
     *
     * @param text $value
     * @return OpenidAssociation
     */
    public function set_assoc_type($value)
    {
        $this->assoc_type = $value;
        return $this;
    }

    /**
     * Get assoc_type
     *
     * @return text 
     */
    public function get_assoc_type()
    {
        return $this->assoc_type;
    }

    /**
     * Set expires_in
     *
     * @param bigint $value
     * @return OpenidAssociation
     */
    public function set_expires_in($value)
    {
        $this->expires_in = $value;
        return $this;
    }

    /**
     * Get expires_in
     *
     * @return bigint 
     */
    public function get_expires_in()
    {
        return $this->expires_in;
    }

    /**
     * Set mac_key
     *
     * @param text $value
     * @return OpenidAssociation
     */
    public function set_mac_key($value)
    {
        $this->mac_key = $value;
        return $this;
    }

    /**
     * Get mac_key
     *
     * @return text 
     */
    public function get_mac_key()
    {
        return $this->mac_key;
    }

    /**
     * Set created
     *
     * @param bigint $value
     * @return OpenidAssociation
     */
    public function set_created($value)
    {
        $this->created = $value;
        return $this;
    }

    /**
     * Get created
     *
     * @return bigint 
     */
    public function get_created()
    {
        return $this->created;
    }
}