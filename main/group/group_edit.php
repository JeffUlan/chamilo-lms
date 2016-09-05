<?php
/* For licensing terms, see /license.txt */

/**
 *	This script displays an area where teachers can edit the group properties and member list.
 *	Groups are also often called "teams" in the Dokeos code.
 *
 *	@author various contributors
 *	@author Roan Embrechts (VUB), partial code cleanup, initial virtual course support
 *	@package chamilo.group
 *	@todo course admin functionality to create groups based on who is in which course (or class).
 */

require_once '../inc/global.inc.php';
$this_section = SECTION_COURSES;
$current_course_tool  = TOOL_GROUP;

// Notice for unauthorized people.
api_protect_course_script(true);

$group_id = api_get_group_id();
$current_group = GroupManager :: get_group_properties($group_id);

$nameTools = get_lang('EditGroup');
$interbreadcrumb[] = array ('url' => 'group.php?'.api_get_cidreq(), 'name' => get_lang('Groups'));
$interbreadcrumb[] = array ('url' => 'group_space.php?'.api_get_cidreq(), 'name' => $current_group['name']);

$is_group_member = GroupManager :: is_tutor_of_group(api_get_user_id(), $current_group['iid']);

if (!api_is_allowed_to_edit(false, true) && !$is_group_member) {
    api_not_allowed(true);
}

/*	FUNCTIONS */

/**
 *  List all users registered to the course
 */
function search_members_keyword($firstname, $lastname, $username, $official_code, $keyword)
{
    if (api_strripos($firstname, $keyword) !== false ||
        api_strripos($lastname, $keyword) !== false ||
        api_strripos($username, $keyword) !== false ||
        api_strripos($official_code, $keyword) !== false
    ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Function to sort users after getting the list in the DB.
 * Necessary because there are 2 or 3 queries. Called by usort()
 */
function sort_users($user_a, $user_b)
{
    if (api_sort_by_first_name()) {
        $cmp = api_strcmp($user_a['firstname'], $user_b['firstname']);
        if ($cmp !== 0) {
            return $cmp;
        } else {
            $cmp = api_strcmp($user_a['lastname'], $user_b['lastname']);
            if ($cmp !== 0) {
                return $cmp;
            } else {
                return api_strcmp($user_a['username'], $user_b['username']);
            }
        }
    } else {
        $cmp = api_strcmp($user_a['lastname'], $user_b['lastname']);
        if ($cmp !== 0) {
            return $cmp;
        } else {
            $cmp = api_strcmp($user_a['firstname'], $user_b['firstname']);
            if ($cmp !== 0) {
                return $cmp;
            } else {
                return api_strcmp($user_a['username'], $user_b['username']);
            }
        }
    }
}

/**
 * Function to check the given max number of members per group
 */
function check_max_number_of_members($value)
{
    $max_member_no_limit = $value['max_member_no_limit'];
    if ($max_member_no_limit == GroupManager::MEMBER_PER_GROUP_NO_LIMIT) {
        return true;
    }
    $max_member = $value['max_member'];
    return is_numeric($max_member);
}

/**
 * Function to check if the number of selected group members is valid
 */
function check_group_members($value)
{
    if ($value['max_member_no_limit'] == GroupManager::MEMBER_PER_GROUP_NO_LIMIT) {
        return true;
    }
    if (isset($value['max_member']) && isset($value['group_members']) && $value['max_member'] < count($value['group_members'])) {
        return array ('group_members' => get_lang('GroupTooMuchMembers'));
    }
    return true;
}

/*	MAIN CODE */

$htmlHeadXtra[] = '<script>
$(document).ready( function() {
    $("#max_member").on("focus", function() {
        $("#max_member_selected").attr("checked", true);
    });
});
 </script>';

// Build form
$form = new FormValidator('group_edit', 'post', api_get_self().'?'.api_get_cidreq());

$form->addElement('header', $nameTools);
$form->addElement('hidden', 'action');
$form->addElement('hidden', 'referer');

// Group name
$form->addText('name', get_lang('GroupName'));

// Description
$form->addElement('textarea', 'description', get_lang('Description'), array ('rows' => 6));

$complete_user_list = GroupManager :: fill_groups_list($current_group['iid']);
usort($complete_user_list, 'sort_users');

$possible_users = array();
foreach ($complete_user_list as $index => $user) {
    $possible_users[$user['user_id']] = api_get_person_name($user['firstname'], $user['lastname']).' ('.$user['username'].')';
}

// Group tutors
$group_tutor_list = GroupManager :: get_subscribed_tutors($current_group['iid']);
$selected_users = array();
$selected_tutors = array();
foreach ($group_tutor_list as $index => $user) {
    $selected_tutors[] = $user['user_id'];
}

$group_tutors_element = $form->addElement(
    'advmultiselect',
    'group_tutors',
    get_lang('GroupTutors'),
    $possible_users,
    'style="width: 280px;"'
);

// Group members
$group_member_list = GroupManager :: get_subscribed_users($current_group['iid']);

$selected_users = array ();
foreach ($group_member_list as $index => $user) {
    $selected_users[] = $user['user_id'];
}

// possible : number_groups_left > 0 and is group member
$possible_users = array();
foreach ($complete_user_list as $index => $user) {
     if ($user['number_groups_left'] > 0 || in_array($user['user_id'], $selected_users)) {
        $possible_users[$user['user_id']] = api_get_person_name($user['firstname'], $user['lastname']).' ('.$user['username'].')';
     }
}

$group_members_element = $form->addElement('advmultiselect', 'group_members', get_lang('GroupMembers'), $possible_users, 'style="width: 280px;"');
$form->addFormRule('check_group_members');

// Members per group
$form->addElement(
    'radio',
    'max_member_no_limit',
    get_lang('GroupLimit'),
    get_lang('NoLimit'),
    GroupManager::MEMBER_PER_GROUP_NO_LIMIT
);
$group = array();
$group[] = $form->createElement('radio', 'max_member_no_limit', null, get_lang('MaximumOfParticipants'), 1, array('id' => 'max_member_selected'));
$group[] = $form->createElement('text', 'max_member', null, array('class' => 'span1', 'id' => 'max_member'));
$group[] = $form->createElement('static', null, null, get_lang('GroupPlacesThis'));
$form->addGroup($group, 'max_member_group', null, null, false);
$form->addRule('max_member_group', get_lang('InvalidMaxNumberOfMembers'), 'callback', 'check_max_number_of_members');

// Self registration
$group = array(
    $form->createElement('checkbox', 'self_registration_allowed', get_lang('GroupSelfRegistration'), get_lang('GroupAllowStudentRegistration'), 1),
    $form->createElement('checkbox', 'self_unregistration_allowed', null, get_lang('GroupAllowStudentUnregistration'), 1)
);
$form->addGroup(
    $group,
    '',
    Display::return_icon('user.png', get_lang('GroupSelfRegistration')) . ' ' . get_lang('GroupSelfRegistration'),
    null,
    false
);

// Documents settings
$group = array();
$group[] = $form->createElement('radio', 'doc_state', get_lang('GroupDocument'), get_lang('NotAvailable'), GroupManager::TOOL_NOT_AVAILABLE);
$group[] = $form->createElement('radio', 'doc_state', null, get_lang('Public'), GroupManager::TOOL_PUBLIC);
$group[] = $form->createElement('radio', 'doc_state', null, get_lang('Private'), GroupManager::TOOL_PRIVATE);
$form->addGroup(
    $group,
    '',
    Display::return_icon('folder.png', get_lang('GroupDocument')).' '.get_lang('GroupDocument'),
    null,
    false
);

// Work settings
$group = array();
$group[] = $form->createElement('radio', 'work_state', get_lang('GroupWork'), get_lang('NotAvailable'), GroupManager::TOOL_NOT_AVAILABLE);
$group[] = $form->createElement('radio', 'work_state', null, get_lang('Public'), GroupManager::TOOL_PUBLIC);
$group[] = $form->createElement('radio', 'work_state', null, get_lang('Private'), GroupManager::TOOL_PRIVATE);
$form->addGroup(
    $group,
    '',
    Display::return_icon('work.png', get_lang('GroupWork')) . ' ' . get_lang('GroupWork'),
    null,
    false
);

// Calendar settings
$group = array();
$group[] = $form->createElement('radio', 'calendar_state', get_lang('GroupCalendar'), get_lang('NotAvailable'), GroupManager::TOOL_NOT_AVAILABLE);
$group[] = $form->createElement('radio', 'calendar_state', null, get_lang('Public'), GroupManager::TOOL_PUBLIC);
$group[] = $form->createElement('radio', 'calendar_state', null, get_lang('Private'), GroupManager::TOOL_PRIVATE);
$form->addGroup(
    $group,
    '',
    Display::return_icon('agenda.png', get_lang('GroupCalendar')) . ' ' . get_lang('GroupCalendar'),
    null,
    false
);

// Announcements settings
$group = array();
$group[] = $form->createElement('radio', 'announcements_state', get_lang('GroupAnnouncements'), get_lang('NotAvailable'), GroupManager::TOOL_NOT_AVAILABLE);
$group[] = $form->createElement('radio', 'announcements_state', null, get_lang('Public'), GroupManager::TOOL_PUBLIC);
$group[] = $form->createElement('radio', 'announcements_state', null, get_lang('Private'), GroupManager::TOOL_PRIVATE);
$form->addGroup(
    $group,
    '',
    Display::return_icon('announce.png', get_lang('GroupAnnouncements')) . ' ' . get_lang('GroupAnnouncements'),
    null,
    false
);

//Forum settings
$group = array();
$group[] = $form->createElement('radio', 'forum_state', get_lang('GroupForum'), get_lang('NotAvailable'), GroupManager::TOOL_NOT_AVAILABLE);
$group[] = $form->createElement('radio', 'forum_state', null, get_lang('Public'), GroupManager::TOOL_PUBLIC);
$group[] = $form->createElement('radio', 'forum_state', null, get_lang('Private'), GroupManager::TOOL_PRIVATE);
$form->addGroup(
    $group,
    '',
    Display::return_icon('forum.png', get_lang('GroupForum')) . ' ' . get_lang('GroupForum'),
    null,
    false
);

// Wiki settings
$group = array(
    $form->createElement('radio', 'wiki_state', get_lang('GroupWiki'), get_lang('NotAvailable'), GroupManager::TOOL_NOT_AVAILABLE),
    $form->createElement('radio', 'wiki_state', null, get_lang('Public'), GroupManager::TOOL_PUBLIC),
    $form->createElement('radio', 'wiki_state', null, get_lang('Private'), GroupManager::TOOL_PRIVATE)
);
$form->addGroup(
    $group,
    '',
    Display::return_icon('wiki.png', get_lang('GroupWiki')) . ' ' . get_lang('GroupWiki'),
    null,
    false
);

// Chat settings
$group = array(
    $form->createElement('radio', 'chat_state', get_lang('Chat'), get_lang('NotAvailable'), GroupManager::TOOL_NOT_AVAILABLE),
    $form->createElement('radio', 'chat_state', null, get_lang('Public'), GroupManager::TOOL_PUBLIC),
    $form->createElement('radio', 'chat_state', null, get_lang('Private'), GroupManager::TOOL_PRIVATE)
);
$form->addGroup(
    $group,
    '',
    Display::return_icon('chat.png', get_lang('Chat')).' '.get_lang('Chat'),
    null,
    false
);

// submit button
$form->addButtonSave(get_lang('SaveSettings'), 'submit');

if ($form->validate()) {
    $values = $form->exportValues();
    if ($values['max_member_no_limit'] == GroupManager::MEMBER_PER_GROUP_NO_LIMIT) {
        $max_member = GroupManager::MEMBER_PER_GROUP_NO_LIMIT;
    } else {
        $max_member = $values['max_member'];
    }
    $self_registration_allowed   = isset($values['self_registration_allowed']) ? 1 : 0;
    $self_unregistration_allowed = isset($values['self_unregistration_allowed']) ? 1 : 0;

    GroupManager::set_group_properties(
        $current_group['id'],
        $values['name'],
        $values['description'],
        $max_member,
        $values['doc_state'],
        $values['work_state'],
        $values['calendar_state'],
        $values['announcements_state'],
        $values['forum_state'],
        $values['wiki_state'],
        $values['chat_state'],
        $self_registration_allowed,
        $self_unregistration_allowed
    );

    // Storing the tutors (we first remove all the tutors and then add only those who were selected)
    GroupManager :: unsubscribe_all_tutors($current_group['iid']);
    if (isset($_POST['group_tutors']) && count($_POST['group_tutors']) > 0) {
        GroupManager :: subscribe_tutors($values['group_tutors'], $current_group['iid']);
    }

    // Storing the users (we first remove all users and then add only those who were selected)
    GroupManager :: unsubscribe_all_users($current_group['iid']);
    if (isset ($_POST['group_members']) && count($_POST['group_members']) > 0) {
        GroupManager :: subscribe_users($values['group_members'], $current_group['iid']);
    }

    // Returning to the group area (note: this is inconsistent with the rest of chamilo)
    $cat = GroupManager :: get_category_from_group($current_group['iid']);
    if (isset($_POST['group_members']) &&
        count($_POST['group_members']) > $max_member &&
        $max_member != GroupManager::MEMBER_PER_GROUP_NO_LIMIT
    ) {
        Display::addFlash(Display::return_message(get_lang('GroupTooMuchMembers'), 'warning'));
        header('Location: group.php?'.api_get_cidreq(true, false));
    } else {
        Display::addFlash(Display::return_message(get_lang('GroupSettingsModified'), 'success'));
        header('Location: group.php?'.api_get_cidreq(true, false).'&category='.$cat['id']);
    }
    exit;
}

$defaults = $current_group;
$defaults['group_members'] = $selected_users;
$defaults['group_tutors'] = $selected_tutors;
$action = isset($_GET['action']) ? $_GET['action'] : '';
$defaults['action'] = $action;
if ($defaults['maximum_number_of_students'] == GroupManager::MEMBER_PER_GROUP_NO_LIMIT) {
    $defaults['max_member_no_limit'] = GroupManager::MEMBER_PER_GROUP_NO_LIMIT;
} else {
    $defaults['max_member_no_limit'] = 1;
    $defaults['max_member'] = $defaults['maximum_number_of_students'];
}

if (!empty($_GET['keyword']) && !empty($_GET['submit'])) {
    $keyword_name = Security::remove_XSS($_GET['keyword']);
    echo '<br/>'.get_lang('SearchResultsFor').' <span style="font-style: italic ;"> '.$keyword_name.' </span><br>';
}

Display :: display_header($nameTools, 'Group');

$form->setDefaults($defaults);
$form->display();

Display :: display_footer();
