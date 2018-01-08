<?php
/* For licensing terms, see /license.txt */

/**
 * Class MaintenanceModePlugin
 */
class MaintenanceModePlugin extends Plugin
{
    /**
     * @return $this
     */
    public static function create()
    {
        static $result = null;
        return $result ? $result : $result = new self();
    }

    /**
     * MaintenanceModePlugin constructor.
     */
    public function __construct()
    {
        parent::__construct(
            '0.1',
            'Julio Montoya',
            [
                'tool_enable' => 'boolean'
            ]
        );
    }
}
