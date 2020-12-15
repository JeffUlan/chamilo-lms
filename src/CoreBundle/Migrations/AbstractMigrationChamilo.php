<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations;

use Chamilo\CoreBundle\Entity\ResourceInterface;
use Chamilo\CoreBundle\Entity\ResourceLink;
use Chamilo\CoreBundle\Entity\SettingsCurrent;
use Chamilo\CoreBundle\Entity\SettingsOptions;
use Chamilo\CoreBundle\Entity\User;
use Chamilo\CoreBundle\Repository\Node\CourseRepository;
use Chamilo\CoreBundle\Repository\Node\UserRepository;
use Chamilo\CoreBundle\Repository\SessionRepository;
use Chamilo\CourseBundle\Repository\CDocumentRepository;
use Chamilo\CourseBundle\Repository\CGroupRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class AbstractMigrationChamilo.
 */
abstract class AbstractMigrationChamilo extends AbstractMigration implements ContainerAwareInterface
{
    public const BATCH_SIZE = 20;
    private $manager;
    private $container;

    public function setEntityManager(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    public function getAdmin(): User
    {
        $container = $this->getContainer();
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $userRepo = $container->get(UserRepository::class);

        $sql = 'SELECT id, user_id FROM admin ORDER BY id LIMIT 1';
        $result = $connection->executeQuery($sql);
        $adminRow = $result->fetchAssociative();
        $adminId = $adminRow['user_id'];

        return $userRepo->find($adminId);
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        if (empty($this->manager)) {
            //$params = $this->connection->getParams();
            /*
            $dbParams = [
                'driver' => 'pdo_mysql',
                'host' => $this->connection->getHost(),
                'user' => $this->connection->getUsername(),
                'password' => $this->connection->getPassword(),
                'dbname' => $this->connection->getDatabase(),
                'port' => $this->connection->getPort(),
            ];*/
            /*$database = new \Database();
            $database->connect(
                $params,
                __DIR__.'/../../',
                __DIR__.'/../../'
            );
            $this->manager = $database->getManager();*/
        }

        return $this->manager;
    }

    /**
     * Speeds up SettingsCurrent creation.
     *
     * @param string $variable            The variable itself
     * @param string $subKey              The subkey
     * @param string $type                The type of setting (text, radio, select, etc)
     * @param string $category            The category (Platform, User, etc)
     * @param string $selectedValue       The default value
     * @param string $title               The setting title string name
     * @param string $comment             The setting comment string name
     * @param string $scope               The scope
     * @param string $subKeyText          Text if there is a subKey
     * @param int    $accessUrl           What URL it is for
     * @param bool   $accessUrlChangeable Whether it can be changed on each url
     * @param bool   $accessUrlLocked     Whether the setting for the current URL is
     *                                    locked to the current value
     * @param array  $options             Optional array in case of a radio-type field,
     *                                    to insert options
     */
    public function addSettingCurrent(
        $variable,
        $subKey,
        $type,
        $category,
        $selectedValue,
        $title,
        $comment,
        $scope = '',
        $subKeyText = '',
        $accessUrl = 1,
        $accessUrlChangeable = false,
        $accessUrlLocked = true,
        $options = []
    ) {
        $setting = new SettingsCurrent();
        $setting
            ->setVariable($variable)
            ->setSubkey($subKey)
            ->setType($type)
            ->setCategory($category)
            ->setSelectedValue($selectedValue)
            ->setTitle($title)
            ->setComment($comment)
            ->setScope($scope)
            ->setSubkeytext($subKeyText)
            ->setUrl($accessUrl)
            ->setAccessUrlChangeable($accessUrlChangeable)
            ->setAccessUrlLocked($accessUrlLocked);

        $this->getEntityManager()->persist($setting);

        if (count($options) > 0) {
            foreach ($options as $option) {
                if (empty($option['text'])) {
                    if ('true' == $option['value']) {
                        $option['text'] = 'Yes';
                    } else {
                        $option['text'] = 'No';
                    }
                }

                $settingOption = new SettingsOptions();
                $settingOption
                    ->setVariable($variable)
                    ->setValue($option['value'])
                    ->setDisplayText($option['text']);

                $this->getEntityManager()->persist($settingOption);
            }
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @param string $variable
     */
    public function getConfigurationValue($variable)
    {
        global $_configuration;
        if (isset($_configuration[$variable])) {
            return $_configuration[$variable];
        }

        return false;
    }

    /**
     * Remove a setting completely.
     *
     * @param string $variable The setting variable name
     */
    public function removeSettingCurrent($variable)
    {
        //to be implemented
    }

    public function addLegacyFileToResource($filePath, $repo, $resource, $id, $fileName = '')
    {
        if (!is_dir($filePath)) {
            $class = get_class($resource);
            $documentPath = basename($filePath);
            if (file_exists($filePath)) {
                $mimeType = mime_content_type($filePath);
                if (empty($fileName)) {
                    $fileName = basename($documentPath);
                }
                $file = new UploadedFile($filePath, $fileName, $mimeType, null, true);
                if ($file) {
                    $repo->addFile($resource, $file);
                } else {
                    $this->warnIf(true, "Cannot migrate $class #$id path: $documentPath ");
                }
            } else {
                $this->warnIf(true, "Cannot migrate $class #'.$id.' file not found: $documentPath");
            }
        }
    }

    public function fixItemProperty($tool, $repo, $course, $admin, ResourceInterface $resource, $parent)
    {
        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');
        $em = $doctrine->getManager();
        /** @var Connection $connection */
        $connection = $em->getConnection();

        $documentRepo = $container->get(CDocumentRepository::class);
        $courseRepo = $container->get(CourseRepository::class);
        $sessionRepo = $container->get(SessionRepository::class);
        $groupRepo = $container->get(CGroupRepository::class);
        $userRepo = $container->get(UserRepository::class);
        $courseId = $course->getId();
        $id = $resource->getResourceIdentifier();

        $sql = "SELECT * FROM c_item_property
                WHERE tool = '$tool' AND c_id = $courseId AND ref = $id";
        $result = $connection->executeQuery($sql);
        $items = $result->fetchAllAssociative();

        // For some reason this document doesnt have a c_item_property value.
        if (empty($items)) {
            return false;
        }

        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');
        $em = $doctrine->getManager();
        $sessionRepo = $container->get(SessionRepository::class);
        $groupRepo = $container->get(CGroupRepository::class);
        $userRepo = $container->get(UserRepository::class);

        $resource->setParent($parent);
        $resourceNode = null;
        $userList = [];
        $groupList = [];
        $sessionList = [];
        foreach ($items as $item) {
            $visibility = $item['visibility'];
            $sessionId = $item['session_id'];
            $userId = $item['insert_user_id'];
            $groupId = $item['to_group_id'];

            $newVisibility = ResourceLink::VISIBILITY_PENDING;
            switch ($visibility) {
                case 0:
                    $newVisibility = ResourceLink::VISIBILITY_PENDING;
                    break;
                case 1:
                    $newVisibility = ResourceLink::VISIBILITY_PUBLISHED;
                    break;
                case 2:
                    $newVisibility = ResourceLink::VISIBILITY_DELETED;
                    break;
            }

            if (isset($userList[$userId])) {
                $user = $userList[$userId];
            } else {
                $user = $userList[$userId] = $userRepo->find($userId);
            }

            if (null === $user) {
                $user = $admin;
            }

            $session = null;
            if (!empty($sessionId)) {
                if (isset($sessionList[$sessionId])) {
                    $session = $sessionList[$sessionId];
                } else {
                    $session = $sessionList[$sessionId] = $sessionRepo->find($sessionId);
                }
            }

            $group = null;
            if (!empty($groupId)) {
                if (isset($groupList[$groupId])) {
                    $group = $groupList[$groupId];
                } else {
                    $group = $groupList[$groupId] = $groupRepo->find($groupId);
                }
            }

            if (null === $resourceNode) {
                $resourceNode = $repo->addResourceNode($resource, $user, $parent);
                $em->persist($resourceNode);
            }
            $resource->addCourseLink($course, $session, $group, $newVisibility);
            $em->persist($resource);
        }

        return true;
    }
}
