<?php

/**
 * HTML class for a textarea type field
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @author      Adam Daniel <adaniel1@eesus.jnj.com>
 * @author      Bertrand Mansion <bmansion@mamasam.com>
 * @copyright   2001-2009 The PHP Group
 * @license     http://www.php.net/license/3_01.txt PHP License 3.01
 * @version     CVS: $Id: textarea.php,v 1.13 2009/04/04 21:34:04 avb Exp $
 * @link        http://pear.php.net/package/HTML_QuickForm
 * @version     Release: 3.2.11
 * @since       1.0
 */
class HTML_QuickForm_textarea extends HTML_QuickForm_element
{
    /**
     * Field value
     * @var       string
     */
    public $_value;

    /**
     * @param string       $elementName Input field name attribute
     * @param string|array $label       Label(s) for a field
     * @param mixed        $attributes  Either a typical HTML attribute string or an associative array
     */
    public function __construct(
        $elementName = null,
        $label = null,
        $attributes = null
    ) {
        $columnsSize = $attributes['cols-size'] ?? null;
        $this->setColumnsSize($columnsSize);
        parent::__construct($elementName, $label, $attributes);

        $id = $this->getAttribute('id');

        if (empty($id)) {
            $name = $this->getAttribute('name');
            $this->setAttribute('id', uniqid($name.'_', false));
        }

        $this->_persistantFreeze = true;
        $this->_type = 'textarea';
        $this->_value = null;
    }

    /**
     * Sets the input field name
     *
     * @param string $name Input field name attribute
     *
     * @return    void
     * @since     1.0
     */
    public function setName($name)
    {
        $this->updateAttributes(['name' => $name]);
    }

    /**
     * Returns the element name
     *
     * @return    string
     * @since     1.0
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     * Returns the value of the form element
     *
     * @return    string
     * @since     1.0
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Sets value for textarea element
     *
     * @param string $value Value for textarea element
     *
     * @return    void
     * @since     1.0
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Sets height in rows for textarea element
     *
     * @param string $rows Height expressed in rows
     *
     * @return    void
     * @since     1.0
     */
    public function setRows($rows)
    {
        $this->updateAttributes(['rows' => $rows]);
    }

    /**
     * Sets width in cols for textarea element
     *
     * @param string $cols Width expressed in cols
     *
     * @return    void
     * @since     1.0
     */
    public function setCols($cols)
    {
        $this->updateAttributes(['cols' => $cols]);
    }

    public function getTemplate(string $layout): string
    {
        if (FormValidator::LAYOUT_HORIZONTAL === $layout) {
            return '
                <div class="field">
                    <div class="p-float-label">
                        {element}
                        {icon}
                        <label {label-for}>
                            <!-- BEGIN required --><span class="form_required">*</span><!-- END required -->
                            {label}
                        </label>
                    </div>
                    <!-- BEGIN label_2 -->
                        <small>{label_2}</small>
                    <!-- END label_2 -->

                     <!-- BEGIN label_3 -->
                        <small>{label_3}</small>
                    <!-- END label_3 -->

                    <!-- BEGIN error -->
                        <small class="p-error">{error}</small>
                    <!-- END error -->
                </div>';
        }

        return parent::getTemplate($layout);
    }

    public function toHtml()
    {
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        }

        if (!isset($this->_attributes['class'])) {
            $this->_attributes['class'] = '';
        }

        if (FormValidator::LAYOUT_HORIZONTAL === $this->getLayout()) {
            $this->_attributes['class'] .= 'p-inputtextarea p-inputtext p-component p-filled';
        }

        return $this->_getTabs().
            '<textarea'.$this->_getAttrString($this->_attributes).'>'.
            // because we wrap the form later we don't want the text indented
            // Modified by Ivan Tcholakov, 16-MAR-2010.
            //preg_replace("/(\r\n|\n|\r)/", '&#010;', htmlspecialchars($this->_value)) .
            preg_replace("/(\r\n|\n|\r)/", '&#010;', $this->getCleanValue()).
            //
            '</textarea>';
    }

    /**
     * Returns the value of field without HTML tags (in this case, value is changed to a mask)
     */
    public function getFrozenHtml()
    {
        $value = $this->getCleanValue();
        if ($this->getAttribute('wrap') === 'off') {
            $html = $this->_getTabs().'<pre>'.$value."</pre>\n";
        } else {
            $html = nl2br($value)."\n";
        }

        return $html.$this->_getPersistantData();
    }
}
