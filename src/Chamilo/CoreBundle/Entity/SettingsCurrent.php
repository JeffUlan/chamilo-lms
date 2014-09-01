<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Bundle\SettingsBundle\Model\ParameterInterface;

/**
 * SettingsCurrent
 *
 * @ORM\Table(name="settings_current", uniqueConstraints={@ORM\UniqueConstraint(name="unique_setting", columns={"variable", "subkey", "category", "access_url"})}, indexes={@ORM\Index(name="access_url", columns={"access_url"}), @ORM\Index(name="idx_settings_current_au_cat", columns={"access_url", "category"})})
 * @ORM\Entity(repositoryClass="Chamilo\CoreBundle\Entity\Repository\SettingsCurrentRepository")
 */
class SettingsCurrent implements ParameterInterface
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
     * @ORM\Column(name="subkey", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $subkey;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="selected_value", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $selectedValue;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="scope", type="string", length=50, precision=0, scale=0, nullable=true, unique=false)
     */
    private $scope;

    /**
     * @var string
     *
     * @ORM\Column(name="subkeytext", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $subkeytext;

    /**
     * @var integer
     *
     * @ORM\Column(name="access_url", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $accessUrl;

    /**
     * @var integer
     *
     * @ORM\Column(name="access_url_changeable", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $accessUrlChangeable;

    /**
     * @var integer
     *
     * @ORM\Column(name="access_url_locked", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $accessUrlLocked;

    public function __construct()
    {
        $this->accessUrl = 1;
        $this->accessUrlLocked = 0;
        $this->access_url_changeable = 1;
    }

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
     * @return SettingsCurrent
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
     * Set subkey
     *
     * @param string $subkey
     * @return SettingsCurrent
     */
    public function setSubkey($subkey)
    {
        $this->subkey = $subkey;

        return $this;
    }

    /**
     * Get subkey
     *
     * @return string
     */
    public function getSubkey()
    {
        return $this->subkey;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return SettingsCurrent
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return SettingsCurrent
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set selectedValue
     *
     * @param string $selectedValue
     * @return SettingsCurrent
     */
    public function setSelectedValue($selectedValue)
    {
        $this->selectedValue = $selectedValue;

        return $this;
    }

    /**
     * Get selectedValue
     *
     * @return string
     */
    public function getSelectedValue()
    {
        return $this->selectedValue;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return SettingsCurrent
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return SettingsCurrent
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set scope
     *
     * @param string $scope
     * @return SettingsCurrent
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Set subkeytext
     *
     * @param string $subkeytext
     * @return SettingsCurrent
     */
    public function setSubkeytext($subkeytext)
    {
        $this->subkeytext = $subkeytext;

        return $this;
    }

    /**
     * Get subkeytext
     *
     * @return string
     */
    public function getSubkeytext()
    {
        return $this->subkeytext;
    }

    /**
     * Set accessUrl
     *
     * @param integer $accessUrl
     * @return SettingsCurrent
     */
    public function setAccessUrl($accessUrl)
    {
        $this->accessUrl = $accessUrl;

        return $this;
    }

    /**
     * Get accessUrl
     *
     * @return integer
     */
    public function getAccessUrl()
    {
        return $this->accessUrl;
    }

    /**
     * Set accessUrlChangeable
     *
     * @param integer $accessUrlChangeable
     * @return SettingsCurrent
     */
    public function setAccessUrlChangeable($accessUrlChangeable)
    {
        $this->accessUrlChangeable = $accessUrlChangeable;

        return $this;
    }

    /**
     * Get accessUrlChangeable
     *
     * @return integer
     */
    public function getAccessUrlChangeable()
    {
        return $this->accessUrlChangeable;
    }

    /**
     * Set accessUrlLocked
     *
     * @param integer $accessUrlLocked
     * @return SettingsCurrent
     */
    public function setAccessUrlLocked($accessUrlLocked)
    {
        $this->accessUrlLocked = $accessUrlLocked;

        return $this;
    }

    /**
     * Get accessUrlLocked
     *
     * @return integer
     */
    public function getAccessUrlLocked()
    {
        return $this->accessUrlLocked;
    }

    /**
    * {@inheritdoc}
    */
    public function getNamespace()
    {
        return $this->getCategory();
    }

    /**
     * {@inheritdoc}
     */
    public function setNamespace($namespace)
    {
        $this->setCategory($namespace);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getVariable();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->setTitle($name);
        $this->setVariable($name);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getSelectedValue();
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->setSelectedValue($value);
        return $this;
    }
}
