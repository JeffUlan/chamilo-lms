<?php

/**
 * HTML class for a text field
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category    HTML
 * @package     HTML_QuickForm
 * @author      Adam Daniel <adaniel1@eesus.jnj.com>
 * @author      Bertrand Mansion <bmansion@mamasam.com>
 * @copyright   2001-2009 The PHP Group
 * @license     http://www.php.net/license/3_01.txt PHP License 3.01
 * @version     CVS: $Id: text.php,v 1.7 2009/04/04 21:34:04 avb Exp $
 * @link        http://pear.php.net/package/HTML_QuickForm
 */

/**
 * HTML class for a text field
 *
 * @category    HTML
 * @package     HTML_QuickForm
 * @author      Adam Daniel <adaniel1@eesus.jnj.com>
 * @author      Bertrand Mansion <bmansion@mamasam.com>
 * @version     Release: 3.2.11
 * @since       1.0
 */
class HTML_QuickForm_text extends HTML_QuickForm_input
{
    private $inputSize;

    /**
     * Class constructor
     *
     * @param string $elementName    (optional)Input field name attribute
     * @param string $elementLabel   (optional)Input field label
     * @param mixed  $attributes     (optional)Either a typical HTML attribute string
     *                                      or an associative array
     * @since     1.0
     * @access    public
     * @return    void
     */
    public function __construct(
        $elementName = null,
        $elementLabel = null,
        $attributes = array()
    ) {
        if (is_array($attributes) || empty($attributes)) {
            $attributes['class'] = 'form-control';
        }
        $inputSize = isset($attributes['input-size']) ? $attributes['input-size'] : null;
        $this->setInputSize($inputSize);
        $icon = isset($attributes['icon']) ? $attributes['icon'] : null;
        $this->setIcon($icon);

        if (!empty($inputSize)) {
            unset($attributes['input-size']);
        }

        if (!empty($icon)) {
            unset($attributes['icon']);
        }

        parent::__construct($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;

        $this->setType('text');

        $renderer = FormValidator::getDefaultRenderer();
        //$renderer->setElementTemplate($this->getTemplate(), $elementName);
    }

    /**
     * Show an icon at the left side of an input
     * @return string
     */
    public function getIconToHtml()
    {
        $icon = $this->getIcon();

        if (empty($icon)) {
            return '';
        }

        return '
                <div class="input-group-addon">
                <i class="fa fa-'.$icon.'"></i>
                </div>';
    }

    /**
     * @param string $layout
     *
     * @return string
     */
    public function getTemplate($layout)
    {
        $size = $this->getInputSize();
        $size = empty($size) ? '8' : $size;

        switch ($layout) {
            case FormValidator::LAYOUT_INLINE:
                return '
                <div class="form-group {error_class}">
                    <label {label-for} >
                        <!-- BEGIN required --><span class="form_required">*</span><!-- END required -->
                        {label}
                    </label>
                    {element}
                </div>';
                break;
            case FormValidator::LAYOUT_HORIZONTAL:
                return '
                <div class="form-group {error_class}">
                    <label {label-for} class="col-sm-2 control-label" >
                        <!-- BEGIN required --><span class="form_required">*</span><!-- END required -->
                        {label}
                    </label>
                    <div class="col-sm-'.$size.'">
                        {icon}
                        {element}

                        <!-- BEGIN label_2 -->
                            <p class="help-block">{label_2}</p>
                        <!-- END label_2 -->

                        <!-- BEGIN error -->
                            <span class="help-inline">{error}</span>
                        <!-- END error -->
                    </div>
                    <div class="col-sm-2">
                        <!-- BEGIN label_3 -->
                            {label_3}
                        <!-- END label_3 -->
                    </div>
                </div>';
                break;
            case FormValidator::LAYOUT_BOX_NO_LABEL:
                return '
                        <div class="input-group">
                            {icon}
                            {element}
                        </div>';
                break;
        }
    }

    /**
     * @return null
     */
    public function getInputSize()
    {
        return $this->inputSize;
    }

    /**
     * @param null $inputSize
     */
    public function setInputSize($inputSize)
    {
        $this->inputSize = $inputSize;
    }

    /**
     * Sets size of text field
     *
     * @param     string    $size  Size of text field
     * @since     1.3
     * @access    public
     * @return    void
     */
    public function setSize($size)
    {
        $this->updateAttributes(array('size' => $size));
    }

    /**
     * Sets maxlength of text field
     *
     * @param     string    $maxlength  Maximum length of text field
     * @since     1.3
     * @access    public
     * @return    void
     */
    public function setMaxlength($maxlength)
    {
        $this->updateAttributes(array('maxlength' => $maxlength));
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } else {
            return '<input' . $this->_getAttrString($this->_attributes) . ' />';
        }
    }
}
