<?php
/* For licensing terms, see /license.txt */

/**
 * The Advanced Session allow add sessions' extra fields 
 * @author Angel Fernando Quiroz Campos <angel.quiroz@beeznest.com>
 * @package chamilo.plugin.advancedSessions
 */
class AdvancedSessionsPlugin extends Plugin
{

    const FIELD_NAME = 'as_description';
    const FIELD_TITLE = 'ASDescription';

    /**
     * Class constructor
     */
    protected function __construct()
    {
        parent::__construct('1.0', 'Angel Fernando Quiroz Campos');
    }

    /**
     * Instance the plugin
     * @staticvar null $result
     * @return Tour
     */
    static function create()
    {
        static $result = null;

        return $result ? $result : $result = new self();
    }

    /**
     * Install the plugin
     * @return void
     */
    public function install()
    {
        $this->createSessionFields();
    }

    /**
     * Uninstall the plugin
     * @return void
     */
    public function uninstall()
    {
        $this->removeSessionFields();
    }

    private function createSessionFields()
    {
        SessionManager::create_session_extra_field(self::FIELD_NAME, ExtraField::FIELD_TYPE_TEXTAREA, self::FIELD_TITLE);
    }

    private function removeSessionFields()
    {
        $sessionField = new ExtraField('session');
        $fieldInfo = $sessionField->get_handler_field_info_by_field_variable(self::FIELD_NAME);

        if (!empty($fieldInfo)) {
            $sessionField->delete($fieldInfo['id']);
        }
    }

}
