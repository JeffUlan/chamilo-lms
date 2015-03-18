<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * HTML class for a password type field
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
 * @version     CVS: $Id: password.php,v 1.8 2009/04/04 21:34:04 avb Exp $
 * @link        http://pear.php.net/package/HTML_QuickForm
 */

/**
 * HTML class for a password type field
 *
 * @category    HTML
 * @package     HTML_QuickForm
 * @author      Adam Daniel <adaniel1@eesus.jnj.com>
 * @author      Bertrand Mansion <bmansion@mamasam.com>
 * @version     Release: 3.2.11
 * @since       1.0
 */
class HTML_QuickForm_password extends HTML_QuickForm_text
{
    // {{{ constructor

    /**
     * Class constructor
     *
     * @param     string    $elementName    (optional)Input field name attribute
     * @param     string    $elementLabel   (optional)Input field label
     * @param     mixed     $attributes     (optional)Either a typical HTML attribute string
     *                                      or an associative array
     * @since     1.0
     * @access    public
     * @return    void
     * @throws
     */
    public function __construct($elementName=null, $elementLabel=null, $attributes=null)
    {
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] : 'form-control';
        parent::__construct($elementName, $elementLabel, $attributes);
        $this->setType('password');
    }

    /**
     * Sets size of password element
     *
     * @param     string    $size  Size of password field
     * @since     1.0
     * @access    public
     * @return    void
     */
    public function setSize($size)
    {
        $this->updateAttributes(array('size'=>$size));
    }

    /**
     * Sets maxlength of password element
     *
     * @param     string    $maxlength  Maximum length of password field
     * @since     1.0
     * @access    public
     * @return    void
     */
    public function setMaxlength($maxlength)
    {
        $this->updateAttributes(array('maxlength'=>$maxlength));
    }

    /**
     * Returns the value of field without HTML tags (in this case, value is changed to a mask)
     *
     * @since     1.0
     * @access    public
     * @return    string
     * @throws
     */
    public function getFrozenHtml()
    {
        $value = $this->getValue();
        return ('' != $value? '**********': '&nbsp;') .
               $this->_getPersistantData();
    }
}
