<?php

/**
 * Abstract base class for QuickForm validation rules
 */
require_once 'HTML/QuickForm/Rule.php';

/**
 * Validate telephones
 *
 */
class HTML_QuickForm_Rule_Mobile_Phone_Number extends HTML_QuickForm_Rule
{

    /**
     * Validates mobile phone number
     *
     * @param string $mobilePhoneNumber
     * @return boolean Returns true if valid, false otherwise.
     */
    function validate($mobilePhoneNumber)
    {
        $rule = "/^\d{11}$/";
        return preg_match($rule, $mobilePhoneNumber);
    }
}
