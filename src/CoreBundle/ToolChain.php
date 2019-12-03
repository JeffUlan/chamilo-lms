<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle;

use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Entity\Resource\ResourceType;
use Chamilo\CoreBundle\Entity\Tool;
use Chamilo\CoreBundle\Entity\ToolResourceRight;
use Chamilo\CoreBundle\Security\Authorization\Voter\ResourceNodeVoter;
use Chamilo\CoreBundle\Tool\AbstractTool;
use Chamilo\CourseBundle\Entity\CTool;
use Chamilo\SettingsBundle\Manager\SettingsManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;

/**
 * Class ToolChain.
 *
 * The course tools classes (agenda, blog, etc) are located in:
 *
 * src/Chamilo/CourseBundle/Tool
 *
 * All this classes are registered as a service with the tag "chamilo_core.tool" here:

 * src/Chamilo/CoreBundle/Resources/config/tools.yml
 *
 * The register process is made using the class ToolCompilerClass:
 *
 * src/Chamilo/CoreBundle/DependencyInjection/Compiler/ToolCompilerClass.php

 * The tool chain is just an array that includes all the tools registered in services.yml
 *
 * The tool chain is hook when a new course is created via a listener here:

 * src/Chamilo/CoreBundle/Entity/Listener/CourseListener.php

 * After a course is created this function is called: CourseListener::prePersist()
 * This function includes the called to the function "addToolsInCourse" inside the tool chain.

 * This allows to tools more easily. Steps:

 * 1. Create a new tool class here: src/Chamilo/CoreBundle/Tool
 * 2. Add the class as a service here: src/Chamilo/CoreBundle/Resources/config/tools.yml  (see examples there)
 * 3. Create a new course. When you create a new course the new tool will be created.
 */
class ToolChain
{
    /**
     * @var array
     */
    protected $tools;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->tools = [];
    }

    public function addTool(AbstractTool $tool): void
    {
        $this->tools[$tool->getName()] = $tool;
    }

    public function getTools(): array
    {
        return $this->tools;
    }

    public function createTools(ObjectManager $manager): void
    {
        $tools = $this->getTools();

        /** @var AbstractTool $tool */
        foreach ($tools as $tool) {
            $toolEntity = new Tool();
            $toolEntity
                ->setName($tool->getName())
            ;

            if ($tool->getAdmin() === 1) {
                // Only check ROLE_ADMIN
            } else {
                $this->setToolPermissions($toolEntity);
            }

            $manager->persist($toolEntity);

            $types = $tool->getResourceTypes();
            if (!empty($types)) {
                foreach ($types as $name => $data) {
                    $resourceType = new ResourceType();
                    $resourceType->setName($name);
                    $resourceType->setService($data['repository']);
                    $resourceType->setTool($toolEntity);
                    $manager->persist($resourceType);
                }
            }
            $manager->flush();
        }
    }

    public function updateTools(ObjectManager $manager): void
    {
        $tools = $this->getTools();

        /** @var AbstractTool $tool */
        foreach ($tools as $tool) {
            $toolEntity = new Tool();
            $toolEntity
                ->setName($tool->getName())
            ;

            if ($tool->getAdmin() === 1) {
                // Only check ROLE_ADMIN
            } else {
                $this->setToolPermissions($toolEntity);
            }

            $manager->persist($toolEntity);

            $types = $tool->getResourceTypes();
            if (!empty($types)) {
                foreach ($types as $name => $data) {
                    $resourceType = new ResourceType();
                    $resourceType->setName($name);
                    $resourceType->setService($data['entity']);
                    $resourceType->setTool($toolEntity);
                    $manager->persist($resourceType);
                }
            }

            $manager->flush();
        }
    }

    public function setToolPermissions(Tool $tool): void
    {
        $toolResourceRight = new ToolResourceRight();
        $toolResourceRight
            ->setRole('ROLE_TEACHER')
            ->setMask(ResourceNodeVoter::getEditorMask())
        ;

        $toolResourceRightReader = new ToolResourceRight();
        $toolResourceRightReader
            ->setRole('ROLE_STUDENT')
            ->setMask(ResourceNodeVoter::getReaderMask())
        ;

        $tool->addToolResourceRight($toolResourceRight);
        $tool->addToolResourceRight($toolResourceRightReader);
    }

    public function addToolsInCourse(Course $course, SettingsManager $settingsManager): Course
    {
        $tools = $this->getTools();

        $toolVisibility = $settingsManager->getSetting('course.active_tools_on_create');
        /** @var AbstractTool $tool */
        foreach ($tools as $tool) {
            $toolEntity = new CTool();
            $visibility = in_array($tool->getName(), $toolVisibility);

            $toolEntity
                ->setCourse($course)
                ->setImage($tool->getImage())
                ->setName($tool->getName())
                ->setVisibility($visibility)
                ->setLink($tool->getLink())
                ->setTarget($tool->getTarget())
                ->setCategory($tool->getCategory());

            $course->addTools($toolEntity);
        }

        return $course;
    }

    /**
     * @param string $name
     *
     * @return AbstractTool
     */
    public function getToolFromName($name)
    {
        $tools = $this->getTools();

        if (array_key_exists($name, $tools)) {
            return $tools[$name];
        }

        throw new InvalidArgumentException(sprintf("The Tool '%s' doesn't exist.", $name));
    }
}
