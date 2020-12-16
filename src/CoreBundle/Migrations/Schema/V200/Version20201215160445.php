<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V200;

use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Chamilo\CoreBundle\Repository\Node\CourseRepository;
use Chamilo\CoreBundle\Repository\Node\UserRepository;
use Chamilo\CoreBundle\Repository\SessionRepository;
use Chamilo\CourseBundle\Entity\CForumCategory;
use Chamilo\CourseBundle\Entity\CForumForum;
use Chamilo\CourseBundle\Entity\CForumPost;
use Chamilo\CourseBundle\Entity\CForumThread;
use Chamilo\CourseBundle\Repository\CForumAttachmentRepository;
use Chamilo\CourseBundle\Repository\CForumCategoryRepository;
use Chamilo\CourseBundle\Repository\CForumForumRepository;
use Chamilo\CourseBundle\Repository\CForumPostRepository;
use Chamilo\CourseBundle\Repository\CForumThreadRepository;
use Chamilo\CourseBundle\Repository\CGroupRepository;
use Chamilo\Kernel;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;

final class Version20201215160445 extends AbstractMigrationChamilo
{
    public function getDescription(): string
    {
        return 'Migrate c_forum tables';
    }

    public function up(Schema $schema): void
    {
        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');
        $em = $doctrine->getManager();
        /** @var Connection $connection */
        $connection = $em->getConnection();

        $forumCategoryRepo = $container->get(CForumCategoryRepository::class);
        $forumRepo = $container->get(CForumForumRepository::class);
        $forumAttachmentRepo = $container->get(CForumAttachmentRepository::class);
        $forumThreadRepo = $container->get(CForumThreadRepository::class);
        $forumPostRepo = $container->get(CForumPostRepository::class);

        $courseRepo = $container->get(CourseRepository::class);
        $sessionRepo = $container->get(SessionRepository::class);
        $groupRepo = $container->get(CGroupRepository::class);
        $userRepo = $container->get(UserRepository::class);

        /** @var Kernel $kernel */
        $kernel = $container->get('kernel');
        $rootPath = $kernel->getProjectDir();

        $admin = $this->getAdmin();

        $q = $em->createQuery('SELECT c FROM Chamilo\CoreBundle\Entity\Course c');
        /** @var Course $course */
        foreach ($q->toIterable() as $course) {
            $courseId = $course->getId();
            $course = $courseRepo->find($courseId);

            // Categories.
            $sql = "SELECT * FROM c_forum_category WHERE c_id = $courseId
                    ORDER BY iid";
            $result = $connection->executeQuery($sql);
            $items = $result->fetchAllAssociative();
            foreach ($items as $itemData) {
                $id = $itemData['iid'];
                /** @var CForumCategory $resource */
                $resource = $forumCategoryRepo->find($id);
                if ($resource->hasResourceNode()) {
                    continue;
                }
                $result = $this->fixItemProperty(
                    'forum_category',
                    $forumCategoryRepo,
                    $course,
                    $admin,
                    $resource,
                    $course
                );

                if (false === $result) {
                    continue;
                }

                $em->persist($resource);
                $em->flush();
            }

            $em->flush();
            $em->clear();

            // Forums.
            $sql = "SELECT * FROM c_forum_forum WHERE c_id = $courseId
                    ORDER BY iid";
            $result = $connection->executeQuery($sql);
            $items = $result->fetchAllAssociative();
            foreach ($items as $itemData) {
                $id = $itemData['iid'];
                /** @var CForumForum $resource */
                $resource = $forumRepo->find($id);
                if ($resource->hasResourceNode()) {
                    continue;
                }

                $categoryId = $itemData['forum_category'];
                $parent = $forumCategoryRepo->find($categoryId);
                // Parent should not be null, because every forum must have a category, in this case use the course
                // as parent.
                if (null === $parent) {
                    $parent = $course;
                }

                $result = $this->fixItemProperty(
                    'forum',
                    $forumRepo,
                    $course,
                    $admin,
                    $resource,
                    $parent
                );

                $forumImage = $itemData['forum_image'];
                if (!empty($forumImage)) {
                    $filePath = $rootPath.'/app/courses/'.$course->getDirectory().'/upload/forum/images/'.$forumImage;
                    $this->addLegacyFileToResource($filePath, $forumRepo, $resource, $id, $forumImage);
                }

                if (false === $result) {
                    continue;
                }
                $em->persist($resource);
                $em->flush();
            }
            $em->flush();
            $em->clear();

            // Threads.
            $sql = "SELECT * FROM c_forum_thread WHERE c_id = $courseId
                    ORDER BY iid";
            $result = $connection->executeQuery($sql);
            $items = $result->fetchAllAssociative();
            foreach ($items as $itemData) {
                $id = $itemData['iid'];
                /** @var CForumThread $resource */
                $resource = $forumThreadRepo->find($id);
                if ($resource->hasResourceNode()) {
                    continue;
                }

                $forumId = $itemData['forum_id'];
                if (empty($forumId)) {
                    continue;
                }

                /** @var CForumForum $resource */
                $forum = $forumRepo->find($forumId);

                $result = $this->fixItemProperty(
                    'forum_thread',
                    $forumThreadRepo,
                    $course,
                    $admin,
                    $resource,
                    $forum
                );

                if (false === $result) {
                    continue;
                }

                $em->persist($resource);
                $em->flush();
            }

            $em->flush();
            $em->clear();

            // Posts.
            $sql = "SELECT * FROM c_forum_post WHERE c_id = $courseId
                    ORDER BY iid";
            $result = $connection->executeQuery($sql);
            $items = $result->fetchAllAssociative();
            foreach ($items as $itemData) {
                $id = $itemData['iid'];
                /** @var CForumPost $resource */
                $resource = $forumPostRepo->find($id);
                if ($resource->hasResourceNode()) {
                    continue;
                }

                $threadId = $itemData['thread_id'];
                if (empty($threadId)) {
                    continue;
                }

                /** @var CForumThread $resource */
                $thread = $forumThreadRepo->find($threadId);

                $result = $this->fixItemProperty(
                    'forum_thread',
                    $forumPostRepo,
                    $course,
                    $admin,
                    $resource,
                    $thread
                );

                if (false === $result) {
                    continue;
                }

                $em->persist($resource);
                $em->flush();
            }

            $em->flush();
            $em->clear();

            // Post attachments
            $sql = "SELECT * FROM c_forum_attachment WHERE c_id = $courseId
                    ORDER BY iid";
            $result = $connection->executeQuery($sql);
            $items = $result->fetchAllAssociative();
            foreach ($items as $itemData) {
                $id = $itemData['iid'];
                $postId = $itemData['post_id'];
                $path = $itemData['path'];
                $fileName = $itemData['filename'];

                /** @var CForumPost $resource */
                $post = $forumPostRepo->find($postId);
                if (null === $post) {
                    continue;
                }

                if (!empty($forumImage)) {
                    $filePath = $rootPath.'/app/courses/'.$course->getDirectory().'/upload/forum/'.$path;
                    $this->addLegacyFileToResource($filePath, $forumPostRepo, $post, $id, $fileName);
                    $em->persist($resource);
                    $em->flush();
                }
            }
            $em->flush();
            $em->clear();
        }
    }
}
