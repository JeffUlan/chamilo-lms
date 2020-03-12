<?php

/* For licensing terms, see /license.txt */

use Chamilo\CoreBundle\Framework\Container;
use Chamilo\CourseBundle\Entity\CAnnouncement;

/**
 * Announcement Email.
 *
 * @author Laurent Opprecht <laurent@opprecht.info> for the Univesity of Geneva
 * @author Julio Montoya <gugli100@gmail.com> Adding session support
 */
class AnnouncementEmail
{
    public $session_id;
    public $logger;
    protected $course;
    /** @var CAnnouncement */
    protected $announcement;

    public function __construct(array $courseInfo, $sessionId, CAnnouncement $announcement, Monolog\Logger $logger = null)
    {
        if (empty($courseInfo)) {
            $courseInfo = api_get_course_info();
        }

        $this->course = $courseInfo;
        $this->session_id = empty($sessionId) ? api_get_session_id() : (int) $sessionId;
        $this->announcement = $announcement;
        $this->logger = $logger;
    }

    /**
     * Returns either all course users or all session users depending on whether
     * session is turned on or not.
     *
     * @return array
     */
    public function all_users()
    {
        $courseCode = $this->course['code'];
        if (empty($this->session_id)) {
            $group_id = api_get_group_id();
            if (empty($group_id)) {
                $userList = CourseManager::get_user_list_from_course_code($courseCode);
            } else {
                $userList = GroupManager::get_users($group_id);
                $new_user_list = [];
                foreach ($userList as $user) {
                    $new_user_list[] = ['user_id' => $user];
                }
                $userList = $new_user_list;
            }
        } else {
            $userList = CourseManager::get_user_list_from_course_code(
                $courseCode,
                $this->session_id
            );
        }

        return $userList;
    }

    /**
     * Returns users and groups an announcement item has been sent to.
     *
     * @return array Array of users and groups to whom the element has been sent
     */
    public function sent_to_info()
    {
        return AnnouncementManager::getSenders($this->announcement);

        $sql = "SELECT to_group_id, to_user_id
                FROM $table
                WHERE
                    c_id = $course_id AND
                    tool = '$tool' AND
                    ref = $id
                    $sessionCondition";

        $rs = Database::query($sql);

        while ($row = Database::fetch_array($rs, 'ASSOC')) {
            // if to_user_id <> 0 then it is sent to a specific user
            $user_id = $row['to_user_id'];
            if (!empty($user_id)) {
                $result['users'][] = (int) $user_id;
                // If user is set then skip the group
                continue;
            }

            // if to_group_id is null then it is sent to a specific user
            // if to_group_id = 0 then it is sent to everybody
            $group_id = $row['to_group_id'];
            if (!empty($group_id)) {
                $result['groups'][] = (int) $group_id;
            }
        }

        return $result;
    }

    /**
     * Returns the list of user info to which an announcement was sent.
     * This function returns a list of actual users even when recipient
     * are groups.
     *
     * @return array
     */
    public function sent_to()
    {
        $sent_to = $this->sent_to_info();
        $users = $sent_to['users'];
        $users = $users ? $users : [];
        $groups = $sent_to['groups'];

        if ($users) {
            $users = UserManager::get_user_list_by_ids($users, true);
        }

        if (!empty($groups)) {
            $groupUsers = GroupManager::get_groups_users($groups);
            $groupUsers = UserManager::get_user_list_by_ids($groupUsers, true);

            if (!empty($groupUsers)) {
                $users = array_merge($users, $groupUsers);
            }
        }

        if (empty($users)) {
            if (!empty($this->logger)) {
                $this->logger->addInfo('User list is empty. No users found. Trying all_users()');
            }
            $users = self::all_users();
        }

        // Clean users just in case
        $newListUsers = [];
        if (!empty($users)) {
            foreach ($users as $user) {
                $newListUsers[$user['user_id']] = ['user_id' => $user['user_id']];
            }
        }

        return $newListUsers;
    }

    /**
     * Email subject.
     *
     * @param bool $directMessage
     *
     * @return string
     */
    public function subject($directMessage = false)
    {
        $title = $this->announcement->getTitle();
        if ($directMessage) {
            $result = $title;
        } else {
            $result = $this->course['title'].' - '.$title;
        }

        $result = stripslashes($result);

        return $result;
    }

    /**
     * Email message.
     *
     * @param int $receiverUserId
     *
     * @return string
     */
    public function message($receiverUserId)
    {
        $content = $this->announcement->getContent();
        $session_id = $this->session_id;
        $courseCode = $this->course['code'];

        $content = AnnouncementManager::parseContent(
            $receiverUserId,
            $content,
            $courseCode,
            $session_id
        );

        // Build the link by hand because api_get_cidreq() doesn't accept course params
        $course_param = 'cid='.$this->course['real_id'].'&sid='.$session_id.'&gid='.api_get_group_id();
        $course_name = $this->course['title'];

        $result = "<div>$content</div>";

        // Adding attachment
        $attachments = $this->announcement->getAttachments();
        if (!empty($attachments)) {
            $repo = Container::getAnnouncementAttachmentRepository();
            foreach ($attachments as $attachment) {
                $url = $repo->getResourceFileDownloadUrl($attachment);
                $result .= '<br />';
                $result .= Display::url(
                    $attachment->getFilename(),
                    $url
                );
                $result .= '<br />';
            }
        }

        $result .= '<hr />';
        $userInfo = api_get_user_info();
        if (!empty($userInfo)) {
            $result .= '<a href="mailto:'.$userInfo['mail'].'">'.$userInfo['complete_name'].'</a><br/>';
        }
        $result .= '<a href="'.api_get_path(WEB_CODE_PATH).'announcements/announcements.php?'.$course_param.'">'.$course_name.'</a><br/>';

        return $result;
    }

    /**
     * Returns the one file that can be attached to an announcement.
     *
     * @return array
     */
    public function attachment()
    {
        $result = [];
        $table = Database::get_course_table(TABLE_ANNOUNCEMENT_ATTACHMENT);
        $id = $this->announcement->getIid();
        $course_id = $this->course['real_id'];
        $sql = "SELECT * FROM $table
                WHERE c_id = $course_id AND announcement_id = $id ";
        $rs = Database::query($sql);
        while ($row = Database::fetch_array($rs)) {
            $result[] = $row;
        }

        $result = $result ? reset($result) : [];

        return $result;
    }

    /**
     * Send announcement by email to myself.
     */
    public function sendAnnouncementEmailToMySelf()
    {
        $userId = api_get_user_id();
        $subject = $this->subject();
        $message = $this->message($userId);
        MessageManager::send_message_simple(
            $userId,
            $subject,
            $message,
            api_get_user_id(),
            false,
            true
        );
    }

    /**
     * Send emails to users.
     *
     * @param bool $sendToUsersInSession
     * @param bool $sendToDrhUsers       send a copy of the message to the DRH users
     * @param int  $senderId             related to the main user
     * @param bool $directMessage
     *
     * @return array
     */
    public function send($sendToUsersInSession = false, $sendToDrhUsers = false, $senderId = 0, $directMessage = false)
    {
        $senderId = empty($senderId) ? api_get_user_id() : (int) $senderId;
        $subject = $this->subject($directMessage);

        // Send email one by one to avoid antispam
        $users = $this->sent_to();

        $batchSize = 20;
        $counter = 1;
        $em = Database::getManager();

        if (empty($users) && !empty($this->logger)) {
            $this->logger->addInfo('User list is empty. No emails will be sent.');
        }
        $messageSentTo = [];
        foreach ($users as $user) {
            $message = $this->message($user['user_id']);
            $wasSent = MessageManager::messageWasAlreadySent($senderId, $user['user_id'], $subject, $message);
            if (false === $wasSent) {
                if (!empty($this->logger)) {
                    $this->logger->addInfo(
                        'Announcement: #'.$this->announcement->getIid().'. Send email to user: #'.$user['user_id']
                    );
                }

                $messageSentTo[] = $user['user_id'];
                MessageManager::send_message_simple(
                    $user['user_id'],
                    $subject,
                    $message,
                    $senderId,
                    $sendToDrhUsers,
                    true
                );
            } else {
                if (!empty($this->logger)) {
                    $this->logger->addInfo(
                        'Message "'.$subject.'" was already sent. Announcement: #'.$this->announcement->getIid().'.
                        User: #'.$user['user_id']
                    );
                }
            }

            if (0 === ($counter % $batchSize)) {
                $em->flush();
                $em->clear();
            }
            $counter++;
        }

        if ($sendToUsersInSession) {
            $sessionList = SessionManager::get_session_by_course($this->course['real_id']);
            if (!empty($sessionList)) {
                foreach ($sessionList as $sessionInfo) {
                    $sessionId = $sessionInfo['id'];
                    $message = $this->message(null);
                    $userList = CourseManager::get_user_list_from_course_code(
                        $this->course['code'],
                        $sessionId
                    );
                    if (!empty($userList)) {
                        foreach ($userList as $user) {
                            $messageSentTo[] = $user['user_id'];
                            MessageManager::send_message_simple(
                                $user['user_id'],
                                $subject,
                                $message,
                                $senderId,
                                false,
                                true
                            );
                        }
                    }
                }
            }
        }

        $this->logMailSent();
        $messageSentTo = array_unique($messageSentTo);

        return $messageSentTo;
    }

    /**
     * Store that emails where sent.
     */
    public function logMailSent()
    {
        $id = $this->announcement->getIid();
        $courseId = $this->course['real_id'];
        $table = Database::get_course_table(TABLE_ANNOUNCEMENT);
        $sql = "UPDATE $table SET
                email_sent = 1
                WHERE
                    c_id = $courseId AND
                    id = $id AND
                    session_id = {$this->session_id}
                ";
        Database::query($sql);
    }
}
