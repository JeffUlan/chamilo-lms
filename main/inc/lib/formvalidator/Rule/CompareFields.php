<?php
/* For licensing terms, see /license.txt */

/**
 * QuickForm rule to check a date.
 */
class HTML_QuickForm_Compare_Fields extends HTML_QuickForm_Rule_Compare
{
    /**
     * Function to check an array of fields.
     *
     * @param   array of field names
     * @param   string operator ==, >=, etc
     * @param   string the value to compare
     *
     * @return bool True if date is valid
     */
    public function validate($values = [], $operator_and_max_value = null)
    {
        if (is_array($values) && !empty($values) && !empty($operator_and_max_value)) {
            $final_value = 0;
            foreach ($values as $value) {
                $value = (float) $value;
                $final_value += $value;
            }
            $params = explode('@', $operator_and_max_value);
            $operator = $params[0];
            $max_value = $params[1];

            return parent::validate([$final_value, $max_value], $operator);
        }

        return false;
    }
}
