<?php
/* For licensing terms, see /license.txt */
use \Chamilo\CoreBundle\Entity\SequenceResource;

/**
 * SequenceResourceManager class
 * Helper for SequenceResource
 *
 * @author Angel Fernando Quiroz Campos <angel.quiroz@beeznest.com>
 */
class SequenceResourceManager
{

    /**
     * Check if the ser has completed the requirements for the sequences
     * @param array $sequences The sequences
     * @param int $userId The user ID
     * @param int $type The type of sequence resource
     *
     * @return array
     */
    public static function checkRequirementsForUser(array $sequences, $userId, $type)
    {
        $sequenceList = [];

        switch ($type) {
            case SequenceResource::SESSION_TYPE:
                $sequenceList = self::checkSessionRequirementsForUser($sequences, $userId);
                break;
        }

        return $sequenceList;
    }

    /**
     * Check if the ser has completed the requirements for the session sequences
     * @param array $sequences The sequences
     * @param int $userId The user ID
     *
     * @return array
     */
    private static function checkSessionRequirementsForUser(array $sequences, $userId)
    {
        $sequenceList = [];

        $entityManager = Database::getManager();

        $gradebookCategoryRepo = $entityManager->getRepository(
            'ChamiloCoreBundle:GradebookCategory'
        );

        foreach ($sequences as $sequenceId => $sequence) {
            $item = [
                'name' => $sequence['name'],
                'requirements' => []
            ];

            foreach ($sequence['requirements'] as $sessionRequired) {
                $itemSession = [
                    'name' => $sessionRequired->getName(),
                    'status' => true
                ];

                $sessionsCourses = $sessionRequired->getCourses();

                foreach ($sessionsCourses as $sessionCourse) {
                    $course = $sessionCourse->getCourse();

                    $gradebooks = $gradebookCategoryRepo->findBy([
                        'courseCode' => $course->getCode(),
                        'sessionId' => $sessionRequired->getId(),
                        'isRequirement' => true
                    ]);

                    foreach ($gradebooks as $gradebook) {
                        $category = Category::createCategoryObjectFromEntity(
                            $gradebook
                        );

                        $itemSession['status'] = $itemSession['status'] && Category::userFinishedCourse(
                            $userId,
                            $category
                        );
                    }
                }

                $item['requirements'][$sessionRequired->getId()] = $itemSession;
            }

            $sequenceList[$sequenceId] = $item;
        }

        return $sequenceList;
    }

    /**
     * Check if at least one sequence are completed
     * @param array $sequences The sequences
     * @param int $type The type of sequence resource
     *
     * @return boolean
     */
    public static function checkSequenceAreCompleted(array $sequences)
    {
        foreach ($sequences as $secuence) {
            $status = true;

            foreach ($secuence['requirements'] as $item) {
                $status = $status && $item['status'];
            }

            if ($status) {
                return true;
            }
        }

        return false;
    }
}
