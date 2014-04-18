<?php
/* For licensing terms, see /license.txt */
/**
 * Class TicketPlugin definition file
 * @package chamilo.plugin.ticket
 */
/**
 * Class TicketPlugin
 */
class TicketPlugin extends Plugin
{
    /**
     * Set the result
     * @staticvar null $result
     * @return type
     */
    static function create()
    {
        static $result = null;
        return $result ? $result : $result = new self();
    }
    protected function __construct()
    {
        parent::__construct('1.0', 'Kenny Rodas Chavez, Genesis Lopez, Francis Gonzales, Yannick Warnier', array('tool_enable' => 'boolean'));
    }

    /**
     * Install the ticket plugin
     */
    public function install()
    {
        // Create database tables and insert a Tab
        require_once api_get_path(SYS_PLUGIN_PATH) . PLUGIN_NAME . '/database.php';

    }
    /**
     * Uninstall the ticket plugin
     */
    public function uninstall()
    {
        $tblSettings = Database::get_main_table(TABLE_MAIN_SETTINGS_CURRENT);
        $t_options = Database::get_main_table(TABLE_MAIN_SETTINGS_OPTIONS);
        $t_tool = Database::get_course_table(TABLE_TOOL_LIST);
        $tblTicketTicket = Database::get_main_table(TABLE_TICKET_TICKET);
        $tblTicketStatus = Database::get_main_table(TABLE_TICKET_STATUS);
        $tblTicketProject = Database::get_main_table(TABLE_TICKET_PROJECT);
        $tblTicketPriority = Database::get_main_table(TABLE_TICKET_PRIORITY);
        $tblTicketMesAttch = Database::get_main_table(TABLE_TICKET_MESSAGE_ATTACHMENTS);
        $tblTicketMessage = Database::get_main_table(TABLE_TICKET_MESSAGE);
        $tblTicketCategory = Database::get_main_table(TABLE_TICKET_CATEGORY);
        $tblTicketAssgLog = Database::get_main_table(TABLE_TICKET_ASSIGNED_LOG);
        $settings = $this->get_settings();
        $plugSetting = current($settings);
        
        //Delete settings
        $sql = "DELETE FROM $tblSettings WHERE variable = 'ticket_tool_enable'";
        Database::query($sql);
        
        $sql = "DROP TABLE IF EXISTS $tblTicketTicket";
        Database::query($sql);
        $sql = "DROP TABLE IF EXISTS $tblTicketStatus";
        Database::query($sql);
        $sql = "DROP TABLE IF EXISTS $tblTicketProject";
        Database::query($sql);
        $sql = "DROP TABLE IF EXISTS $tblTicketPriority";
        Database::query($sql);
        $sql = "DROP TABLE IF EXISTS $tblTicketMesAttch";
        Database::query($sql);
        $sql = "DROP TABLE IF EXISTS $tblTicketMessage";
        Database::query($sql);
        $sql = "DROP TABLE IF EXISTS $tblTicketCategory";
        Database::query($sql);
        $sql = "DROP TABLE IF EXISTS $tblTicketAssgLog";
        Database::query($sql);
        $sql = "DROP TABLE IF EXISTS $tblTicketTicket";
        Database::query($sql);
        
        $this->deleteTab($plugSetting['comment']);
    }
}