<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\GraphQlBundle\Traits;

use Chamilo\SettingsBundle\Manager\SettingsManager;
use Chamilo\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Firebase\JWT\JWT;
use Overblog\GraphQLBundle\Error\UserError;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Trait GraphQLTrait.
 *
 * @package Chamilo\GraphQlBundle\Traits
 */
trait GraphQLTrait
{
    use ContainerAwareTrait;

    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var SettingsManager
     */
    protected $settingsManager;

    /**
     * ApiGraphQLTrait constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->translator = $container->get('translator');
        $this->settingsManager = $container->get('chamilo.settings.manager');
    }

    /**
     * Check if the Authorization header was sent to decode the token and authenticate manually the user.
     */
    public function checkAuthorization()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $header = $request->headers->get('Authorization');
        $token = str_replace(['Bearer ', 'bearer '], '', $header);

        if (empty($token)) {
            throw new UserError($this->translator->trans('NotAllowed'));
        }

        $tokenData = $this->decodeToken($token);

        try {
            /** @var User $user */
            $user = $this->em->find('ChamiloUserBundle:User', $tokenData['user']);
        } catch (\Exception $e) {
            $user = null;
        }

        if (!$user) {
            throw new UserError($this->translator->trans('NotAllowed'));
        }

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->container->get('security.token_storage')->setToken($token);
        $this->container->get('session')->set('_security_main', serialize($token));
    }

    /**
     * @param User $user
     *
     * @return string
     */
    private function encodeToken(User $user): string
    {
        $secret = $this->container->getParameter('secret');
        $time = time();

        $payload = [
            'iat' => $time,
            'exp' => $time + (60 * 60 * 24),
            'data' => [
                'user' => $user->getId(),
            ],
        ];

        return JWT::encode($payload, $secret, 'HS384');
    }

    /**
     * @param string $token
     *
     * @return array
     */
    private function decodeToken($token): array
    {
        $secret = $this->container->getParameter('secret');

        try {
            $jwt = JWT::decode($token, $secret, ['HS384']);

            $data = (array) $jwt->data;

            return $data;
        } catch (\Exception $exception) {
            throw new UserError($exception->getMessage());
        }
    }

    /**
     * Throw a UserError if $user doesn't match with the current user.
     *
     * @param User $user User to compare with the context's user
     */
    private function protectCurrentUserData(User $user)
    {
        $currentUser = $this->getCurrentUser();

        if ($user->getId() === $currentUser->getId()) {
            return;
        }

        throw new UserError($this->translator->trans("The user info doesn't match."));
    }

    /**
     * Get the current logged user.
     *
     * @return User
     */
    private function getCurrentUser(): User
    {
        $token = $this->container->get('security.token_storage')->getToken();

        return $token->getUser();
    }
}
