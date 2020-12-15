<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V200;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Chamilo\CoreBundle\Repository\Node\AccessUrlRepository;
use Chamilo\CoreBundle\Repository\Node\CourseRepository;
use Chamilo\CoreBundle\Repository\Node\UserRepository;
use Chamilo\CoreBundle\Repository\SessionRepository;
use Chamilo\CourseBundle\Entity\CCalendarEvent;
use Chamilo\CourseBundle\Entity\CCalendarEventAttachment;
use Chamilo\CourseBundle\Repository\CCalendarEventAttachmentRepository;
use Chamilo\CourseBundle\Repository\CCalendarEventRepository;
use Chamilo\CourseBundle\Repository\CGroupRepository;
use Chamilo\Kernel;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;

final class Version20201215072918 extends AbstractMigrationChamilo
{
    public function getDescription(): string
    {
        return 'Migrate c_calendar_event, calendar_event_attachment';
    }

    public function up(Schema $schema): void
    {
        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');
        $em = $doctrine->getManager();
        /** @var Connection $connection */
        $connection = $em->getConnection();

        $urlRepo = $container->get(AccessUrlRepository::class);
        $eventRepo = $container->get(CCalendarEventRepository::class);
        $eventAttachmentRepo = $container->get(CCalendarEventAttachmentRepository::class);
        $courseRepo = $container->get(CourseRepository::class);
        $sessionRepo = $container->get(SessionRepository::class);
        $groupRepo = $container->get(CGroupRepository::class);
        $userRepo = $container->get(UserRepository::class);

        /** @var Kernel $kernel */
        $kernel = $container->get('kernel');
        $rootPath = $kernel->getProjectDir();
        $admin = $this->getAdmin();
        $urls = $urlRepo->findAll();

        $q = $em->createQuery('SELECT c FROM Chamilo\CoreBundle\Entity\Course c');
        foreach ($q->toIterable() as $course) {
            $counter = 1;
            $courseId = $course->getId();
            $course = $courseRepo->find($courseId);

            $sql = "SELECT * FROM c_calendar_event WHERE c_id = $courseId
                    ORDER BY iid";
            $result = $connection->executeQuery($sql);
            $events = $result->fetchAllAssociative();
            foreach ($events as $eventData) {
                $id = $eventData['iid'];
                /** @var CCalendarEvent $event */
                $event = $eventRepo->find($id);
                if ($event->hasResourceNode()) {
                    continue;
                }
                $sql = "SELECT * FROM c_item_property
                        WHERE tool = 'calendar_event' AND c_id = $courseId AND ref = $id";
                $result = $connection->executeQuery($sql);
                $items = $result->fetchAllAssociative();

                // For some reason this event doesnt have a c_item_property value, then we added to the main course.
                if (empty($items)) {
                    $items[] = [
                        'visibility' => 1,
                        'insert_user_id' => $admin->getId(),
                        'to_group_id' => 0,
                        'session_id' => $eventData['session_id'],
                    ];
                    $this->fixItemProperty($eventRepo, $course, $admin, $event, $course, $items);
                    $em->persist($event);
                    $em->flush();
                    continue;
                }
                $parent = null;
                if (!empty($eventData['parent_event_id'])) {
                    $parent = $eventRepo->find($eventData['parent_event_id']);
                }
                if (null === $parent) {
                    $parent = $course;
                }
                $this->fixItemProperty($eventRepo, $course, $admin, $event, $parent, $items);
                $em->persist($event);
                $em->flush();
            }

            $sql = "SELECT * FROM c_calendar_event_attachment WHERE c_id = $courseId
                    ORDER BY iid";
            $result = $connection->executeQuery($sql);
            $attachments = $result->fetchAllAssociative();
            foreach ($attachments as $attachmentData) {
                $id = $attachmentData['iid'];
                $attachmentPath = $attachmentData['path'];
                $fileName = $attachmentData['filename'];
                /** @var CCalendarEventAttachment $attachment */
                $attachment = $eventAttachmentRepo->find($id);
                if ($attachment->hasResourceNode()) {
                    continue;
                }
                $sql = "SELECT * FROM c_item_property
                        WHERE tool = 'calendar_event_attachment' AND c_id = $courseId AND ref = $id";
                $result = $connection->executeQuery($sql);
                $items = $result->fetchAllAssociative();
                $parent = $attachment->getEvent();
                $this->fixItemProperty($eventAttachmentRepo, $course, $admin, $attachment, $parent, $items);
                $filePath = $rootPath.'/app/courses/'.$course->getDirectory().'/upload/calendar/'.$attachmentPath;
                $this->addLegacyFileToResource($filePath, $eventAttachmentRepo, $attachment, $id, $fileName);
                $em->persist($attachment);
                $em->flush();
            }
        }
    }

    public function down(Schema $schema): void
    {
    }
}
