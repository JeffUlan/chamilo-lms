<?php
/**
 * Contains the SQL for the tickets management plugin database structure
 */


$objPlugin = new TicketPlugin();

$table = Database::get_main_table(TABLE_TICKET_ASSIGNED_LOG);
$sql = "CREATE TABLE IF NOT EXISTS ".$table." (
        iid int unsigned not null,
        ticket_id int UNSIGNED DEFAULT NULL,
        user_id int UNSIGNED DEFAULT NULL,
        assigned_date datetime DEFAULT NULL,
        sys_insert_user_id int UNSIGNED DEFAULT NULL,
        PRIMARY KEY PK_ticket_assigned_log (iid),
        KEY FK_ticket_assigned_log (ticket_id))";
Database::query($sql);

//Category
$table = Database::get_main_table(TABLE_TICKET_CATEGORY);
$sql = "CREATE TABLE IF NOT EXISTS ".$table." (
        iid int unsigned not null,
        category_id char(3) NOT NULL,
        project_id char(3) NOT NULL,
        name varchar(100) NOT NULL,
        description varchar(255) NOT NULL,
        total_tickets int UNSIGNED NOT NULL DEFAULT '0',
        course_required char(1) NOT NULL,
        sys_insert_user_id int UNSIGNED DEFAULT NULL,
        sys_insert_datetime datetime DEFAULT NULL,
        sys_lastedit_user_id int UNSIGNED DEFAULT NULL,
        sys_lastedit_datetime datetime DEFAULT NULL,
        PRIMARY KEY (iid))";
Database::query($sql);

//Default Categories
$categoRow = array(
    $objPlugin->get_lang('Enrollment') => $objPlugin->get_lang('TicketsAboutEnrollment'),
    $objPlugin->get_lang('GeneralInformation') => $objPlugin->get_lang('TicketsAboutGeneralInformation'),
    $objPlugin->get_lang('RequestAndTramits') => $objPlugin->get_lang('TicketsAboutRequestAndTramits'),
    $objPlugin->get_lang('AcademicIncidence') => $objPlugin->get_lang('TicketsAboutAcademicIncidence'),
    $objPlugin->get_lang('VirtualCampus') => $objPlugin->get_lang('TicketsAboutVirtualCampus'),
    $objPlugin->get_lang('OnlineEvaluation') => $objPlugin->get_lang('TicketsAboutOnlineEvaluation')
);
$i = 1;
foreach ($categoRow as $category => $description) {
    //Online evaluation requires a course
    if ($i == 6) {
        $attributes = array(
            'iid' => $i, 
            'category_id' => $i,
            'name' => $category,
            'description' => $description,
            'project_id' => 1,
            'course_required' => 1
        );
    } else {
        $attributes = array(
            'iid' => $i, 
            'category_id' => $i,
            'project_id' => 1,
            'description' => $description,
            'name' => $category
        );
    }
    
    Database::insert($table, $attributes);
    $i++;
}
//END default categories
$table = Database::get_main_table(TABLE_TICKET_MESSAGE);
$sql = "CREATE TABLE IF NOT EXISTS ".$table." (
        iid int UNSIGNED NOT NULL AUTO_INCREMENT,
        message_id int UNSIGNED NOT NULL,
        ticket_id int UNSIGNED NOT NULL,
        subject varchar(150) DEFAULT NULL,
        message text NOT NULL,
        status char(3) NOT NULL,
        ip_address varchar(16) DEFAULT NULL,
        sys_insert_user_id int UNSIGNED DEFAULT NULL,
        sys_insert_datetime datetime DEFAULT NULL,
        sys_lastedit_user_id int UNSIGNED DEFAULT NULL,
        sys_lastedit_datetime datetime DEFAULT NULL,
        PRIMARY KEY (iid),
        KEY FK_tick_message (ticket_id) )";
Database::query($sql);

$table = Database::get_main_table(TABLE_TICKET_MESSAGE_ATTACHMENTS);
$sql = "CREATE TABLE IF NOT EXISTS ".$table." (
        iid int UNSIGNED NOT NULL AUTO_INCREMENT,
        message_attch_id char(2) NOT NULL,
        message_id char(2) NOT NULL,
        ticket_id int UNSIGNED NOT NULL,
        path varchar(255) NOT NULL,
        filename varchar(255) NOT NULL,
        size varchar(25) DEFAULT NULL,
        sys_insert_user_id int UNSIGNED DEFAULT NULL,
        sys_insert_datetime datetime DEFAULT NULL,
        sys_lastedit_user_id int UNSIGNED DEFAULT NULL,
        sys_lastedit_datetime datetime DEFAULT NULL,
        PRIMARY KEY (iid),
        KEY ticket_message_id_fk (message_id))";
Database::query($sql);

//Priority
$table = Database::get_main_table(TABLE_TICKET_PRIORITY);
$sql = "CREATE TABLE IF NOT EXISTS ".$table." (
        iid int UNSIGNED NOT NULL AUTO_INCREMENT,
        priority_id char(3) NOT NULL,
        priority varchar(20) DEFAULT NULL,
        priority_desc varchar(250) DEFAULT NULL,
        priority_color varchar(25) DEFAULT NULL,
        priority_urgency tinyint DEFAULT NULL,
        sys_insert_user_id int UNSIGNED DEFAULT NULL,
        sys_insert_datetime datetime DEFAULT NULL,
        sys_lastedit_user_id int UNSIGNED DEFAULT NULL,
        sys_lastedit_datetime datetime DEFAULT NULL,
        PRIMARY KEY (iid))";
Database::query($sql);
//Default Priorities
$defaultPriorities = array(
    'NRM' => get_lang('Normal'),
    'HGH' => get_lang('High'),
    'LOW' => get_lang('Low')
);
$i = 1;
foreach ($defaultPriorities as $pId => $priority) {
    $attributes = array(
        'iid' => $i,
        'priority_id' => $pId,
        'priority_desc' => $priority
    );
    Database::insert($table, $attributes);
    $i++;
}
//End

$table = Database::get_main_table(TABLE_TICKET_PROJECT);
$sql = "CREATE TABLE IF NOT EXISTS ".$table." (
        iid int UNSIGNED NOT NULL AUTO_INCREMENT,
        project_id char(3) NOT NULL,
        name varchar(50) DEFAULT NULL,
        description varchar(250) DEFAULT NULL,
        email varchar(50) DEFAULT NULL,
        other_area tinyint NOT NULL DEFAULT '0',
        sys_insert_user_id int UNSIGNED DEFAULT NULL,
        sys_insert_datetime datetime DEFAULT NULL,
        sys_lastedit_user_id int UNSIGNED DEFAULT NULL,
        sys_lastedit_datetime datetime DEFAULT NULL,
        PRIMARY KEY (iid))";
Database::query($sql);
//Default Project Table Ticket
$attributes = array(
    'iid' => 1,
    'project_id' => 1,
    'name' => 'Ticket System'
);
Database::insert($table, $attributes);
//END

//STATUS
$table = Database::get_main_table(TABLE_TICKET_STATUS);
$sql = "CREATE TABLE IF NOT EXISTS ".$table." (
        iid int UNSIGNED NOT NULL AUTO_INCREMENT,
        status_id char(3) NOT NULL,
        name varchar(100) NOT NULL,
        description varchar(255) DEFAULT NULL,
        PRIMARY KEY (iid))";
Database::query($sql);
//Default status
$defaultStatus = array(
    'NAT' => get_lang('New'),
    'PND' => $objPlugin->get_lang('Pending'),
    'XCF' => $objPlugin->get_lang('Unconfirmed'),
    'CLS' => get_lang('Close'),
    'REE' => get_lang('Forwarded')
);

$i = 1;
foreach ($defaultStatus as $abr => $status) {
    $attributes = array(
        'iid' => $i,
        'status_id' => $abr,
        'name' => $status
    );
    Database::insert($table, $attributes);
    $i ++;
}
//END

$table = Database::get_main_table(TABLE_TICKET_TICKET);
$sql = "CREATE TABLE IF NOT EXISTS ".$table." (
        ticket_id int UNSIGNED NOT NULL AUTO_INCREMENT,
        ticket_code char(12) DEFAULT NULL,
        project_id char(3) DEFAULT NULL,
        category_id char(3) NOT NULL,
        priority_id char(3) NOT NULL,
        course_id int UNSIGNED NOT NULL,
        request_user int UNSIGNED NOT NULL,
        personal_email varchar(150) DEFAULT NULL,
        assigned_last_user int UNSIGNED NOT NULL DEFAULT '0',
        status_id char(3) NOT NULL,
        total_messages int UNSIGNED NOT NULL DEFAULT '0',
        keyword varchar(250) DEFAULT NULL,
        source char(3) NOT NULL,
        start_date datetime NOT NULL,
        end_date datetime DEFAULT NULL,
        sys_insert_user_id int UNSIGNED DEFAULT NULL,
        sys_insert_datetime datetime DEFAULT NULL,
        sys_lastedit_user_id int UNSIGNED DEFAULT NULL,
        sys_lastedit_datetime datetime DEFAULT NULL,
        PRIMARY KEY (ticket_id),
        UNIQUE KEY UN_ticket_code (ticket_code),
        KEY FK_ticket_priority (priority_id),
        KEY FK_ticket_category (project_id,category_id))";
Database::query($sql);

// Menu main tabs
$objPlugin->addTab('Ticket', '/plugin/ticket/src/myticket.php');

$objPlugin->addExtraSettings('allow_add', '1');
