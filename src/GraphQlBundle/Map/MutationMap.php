<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\GraphQlBundle\Map;

use Chamilo\CoreBundle\Entity\Course;
use Chamilo\GraphQlBundle\Traits\GraphQLTrait;
use Chamilo\UserBundle\Entity\User;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Error\UserError;
use Overblog\GraphQLBundle\Resolver\ResolverMap;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class MutationMap.
 *
 * @package Chamilo\GraphQlBundle\Map
 */
class MutationMap extends ResolverMap implements ContainerAwareInterface
{
    use GraphQLTrait;

    /**
     * @return array
     */
    protected function map()
    {
        return [
            'Mutation' => [
                self::RESOLVE_FIELD => function ($value, Argument $args, \ArrayObject $context, ResolveInfo $info) {
                    $method = 'resolve'.ucfirst($info->fieldName);

                    return $this->$method($args, $context);
                },
            ],
        ];
    }

    /**
     * @param Argument $args
     *
     * @return array
     */
    protected function resolveAuthenticate(Argument $args)
    {
        /** @var User $user */
        $user = $this->em->getRepository('ChamiloUserBundle:User')->findOneBy(['username' => $args['username']]);

        if (!$user) {
            throw new UserError($this->translator->trans('User not found.'));
        }

        $encoder = $this->container->get('security.password_encoder');
        $isValid = $encoder->isPasswordValid($user, $args['password']);

        if (!$isValid) {
            throw new UserError($this->translator->trans('Password is not valid.'));
        }

        return [
            'token' => $this->encodeToken($user),
        ];
    }

    /**
     * @param Argument $args
     *
     * @return array
     */
    protected function resolveViewerSendMessage(Argument $args)
    {
        $this->checkAuthorization();

        $currentUser = $this->getCurrentUser();
        $usersRepo = $this->em->getRepository('ChamiloUserBundle:User');
        $users = $usersRepo->findUsersToSendMessage($currentUser->getId());
        $receivers = array_filter(
            $args['receivers'],
            function ($receiverId) use ($users) {
                /** @var User $user */
                foreach ($users as $user) {
                    if ($user->getId() === (int) $receiverId) {
                        return true;
                    }
                }

                return false;
            }
        );

        $result = [];

        foreach ($receivers as $receiverId) {
            $messageId = \MessageManager::send_message(
                $receiverId,
                $args['subject'],
                $args['text'],
                [],
                [],
                0,
                0,
                0,
                0,
                $currentUser->getId()
            );

            $result[] = [
                'receiverId' => $receiverId,
                'sent' => (bool) $messageId,
            ];
        }

        return $result;
    }

    /**
     * @param Argument $args
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     *
     * @return Course
     */
    protected function resolveCreateCourse(Argument $args): ?Course
    {
        $this->checkAuthorization();

        $checker = $this->container->get('security.authorization_checker');

        if (false === $checker->isGranted('ROLE_ADMIN')) {
            throw new UserError($this->translator->trans('Not allowed'));
        }

        $course = $args['course'];
        $originalCourseIdName = $args['originalCourseIdName'];
        $originalCourseIdValue = $args['originalCourseIdValue'];

        $title = $course['title'];
        $categoryCode = !empty($course['categoryCode']) ? $course['categoryCode'] : null;
        $wantedCode = isset($course['wantedCode']) ? $course['wantedCode'] : null;
        $language = $course['language'];
        $visibility = isset($course['visibility']) ? $course['visibility'] : COURSE_VISIBILITY_OPEN_PLATFORM;
        $diskQuota = $course['diskQuota'] * 1024 * 1024;
        $allowSubscription = $course['allowSubscription'];
        $allowUnsubscription = $course['allowUnsubscription'];

        $courseInfo = \CourseManager::getCourseInfoFromOriginalId($originalCourseIdValue, $originalCourseIdName);

        if (!empty($courseInfo)) {
            if (0 !== (int) $courseInfo['visibility']) {
                /** @var Course $course */
                $course = $this->em->find('ChamiloCoreBundle:Course', $courseInfo['real_id']);
                $course
                    ->setCourseLanguage($language)
                    ->setTitle($title)
                    ->setCategoryCode($categoryCode)
                    //->setTutorName('')
                    ->setVisualCode($wantedCode ?: $courseInfo['code'])
                    ->setVisibility($visibility);

                $this->em->persist($course);
                $this->em->flush();

                return $course;
            }

            return null;
        }

        $currentUser = $this->getCurrentUser();

        $params = [
            'title' => $title,
            'wanted_code' => $wantedCode,
            'category_code' => $categoryCode,
            //'tutor_name',
            'course_language' => $language,
            'user_id' => $currentUser->getId(),
            'visibility' => $visibility,
            'disk_quota' => $diskQuota,
            'subscribe' => !empty($allowSubscription),
            'unsubscribe' => !empty($allowUnsubscription),
        ];

        $courseInfo = \CourseManager::create_course($params, $currentUser->getId());

        if (empty($courseInfo)) {
            return null;
        }

        \CourseManager::create_course_extra_field(
            $originalCourseIdName,
            \ExtraField::FIELD_TYPE_TEXT,
            $originalCourseIdName
        );

        \CourseManager::update_course_extra_field_value(
            $courseInfo['code'],
            $originalCourseIdName,
            $originalCourseIdValue
        );

        return $this->em->find('ChamiloCoreBundle:Course', $courseInfo['real_id']);
    }
}
