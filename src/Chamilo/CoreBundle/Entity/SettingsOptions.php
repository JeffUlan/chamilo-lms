<?php

namespace Chamilo\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SettingsOptions
 *
 * @ORM\Table(name="settings_options", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"}), @ORM\UniqueConstraint(name="unique_setting_option", columns={"variable", "value"})})
 * @ORM\Entity
 */
class SettingsOptions
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="variable", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $variable;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="display_text", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $displayText;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set variable
     *
     * @param string $variable
     * @return SettingsOptions
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;

        return $this;
    }

    /**
     * Get variable
     *
     * @return string
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return SettingsOptions
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set displayText
     *
     * @param string $displayText
     * @return SettingsOptions
     */
    public function setDisplayText($displayText)
    {
        $this->displayText = $displayText;

        return $this;
    }

    /**
     * Get displayText
     *
     * @return string
     */
    public function getDisplayText()
    {
        return $this->displayText;
    }
}
