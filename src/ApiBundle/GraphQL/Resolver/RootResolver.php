<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\ApiBundle\GraphQL\Resolver;

use Chamilo\ApiBundle\GraphQL\ApiGraphQLTrait;
use Chamilo\CoreBundle\Entity\Course;
use Chamilo\UserBundle\Entity\User;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class RootResolver.
 *
 * @package Chamilo\ApiBundle\GraphQL\Resolver
 */
class RootResolver implements ResolverInterface, AliasedInterface, ContainerAwareInterface
{
    use ApiGraphQLTrait;

    /**
     * Returns methods aliases.
     *
     * For instance:
     * array('myMethod' => 'myAlias')
     *
     * @return array
     */
    public static function getAliases()
    {
        return [
            'resolverViewer' => 'viewer',
            'resolveCourse' => 'course',
        ];
    }

    /**
     * @param \ArrayObject $context
     *
     * @return User
     */
    public function resolverViewer(\ArrayObject $context)
    {
        $this->checkAuthorization($context);

        /** @var User $user */
        $user = $context->offsetGet('user');

        return $user;
    }

    /**
     * @param int          $courseId
     * @param \ArrayObject $context
     *
     * @return Course|null
     */
    public function resolveCourse($courseId, \ArrayObject $context)
    {
        $this->checkAuthorization($context);

        try {
            $course = $this->em->find('ChamiloCoreBundle:Course', $courseId);
        } catch (\Exception $e) {
            $course = null;
        }

        $context->offsetSet('course', $course);

        $this->protectCourseData($course, $context);

        return $course;
    }
}
