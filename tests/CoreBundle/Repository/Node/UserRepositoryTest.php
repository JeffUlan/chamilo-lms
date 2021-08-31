<?php

declare(strict_types=1);

namespace Chamilo\Tests\CoreBundle\Repository\Node;

use Chamilo\CoreBundle\Entity\User;
use Chamilo\CoreBundle\Repository\Node\UserRepository;
use Chamilo\Tests\AbstractApiTest;
use Chamilo\Tests\ChamiloTestTrait;

class UserRepositoryTest extends AbstractApiTest
{
    use ChamiloTestTrait;

    public function testCount(): void
    {
        self::bootKernel();
        $count = self::getContainer()->get(UserRepository::class)->count([]);
        // Admin + anon (registered in the DataFixture\AccessUrlAdminFixtures.php)
        $this->assertSame(2, $count);
    }

    public function testCreateUser(): void
    {
        self::bootKernel();
        $student = $this->createUser('student');
        $userRepo = self::getContainer()->get(UserRepository::class);

        $count = $userRepo->count([]);
        // By default, there are 2 users: admin + anon.
        $this->assertSame(3, $count);
        $this->assertHasNoEntityViolations($student);

        $this->assertSame(1, \count($student->getRoles()));
        $this->assertTrue(\in_array('ROLE_USER', $student->getRoles(), true));

        $student->addRole('ROLE_STUDENT');
        $userRepo->update($student);

        $this->assertTrue($student->hasRole('ROLE_STUDENT'));
        $this->assertTrue($student->isEqualTo($student));

        $this->assertSame(2, \count($student->getRoles()));

        $student->addRole('ROLE_STUDENT');
        $userRepo->update($student);

        $this->assertTrue($student->isStudent());
        $this->assertSame(2, \count($student->getRoles()));

        $student->removeRole('ROLE_STUDENT');
        $userRepo->update($student);

        $this->assertSame(1, \count($student->getRoles()));

        $this->assertTrue($student->isAccountNonExpired());
        $this->assertTrue($student->isAccountNonLocked());
        $this->assertTrue($student->isActive());
        $this->assertTrue($student->isEnabled());
        $this->assertFalse($student->isAdmin());
        $this->assertFalse($student->isStudentBoss());
        $this->assertFalse($student->isSuperAdmin());
        $this->assertTrue($student->isCredentialsNonExpired());
    }

    public function testCreateAdmin(): void
    {
        self::bootKernel();
        $admin = $this->createUser('admin2');
        $userRepo = self::getContainer()->get(UserRepository::class);

        $this->assertHasNoEntityViolations($admin);
        $admin->addRole('ROLE_ADMIN');
        $userRepo->update($admin);

        $this->assertTrue($admin->isActive());
        $this->assertTrue($admin->isEnabled());
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isStudentBoss());
        $this->assertFalse($admin->isSuperAdmin());
        $this->assertTrue($admin->isCredentialsNonExpired());
    }

    public function testCreateUserSkipResourceNode(): void
    {
        $manager = $this->getManager();
        $userRepo = self::getContainer()->get(UserRepository::class);

        $user = (new User())
            ->setSkipResourceNode(true)
            ->setLastname('Doe')
            ->setFirstname('Joe')
            ->setUsername('admin2')
            ->setStatus(1)
            ->setPlainPassword('admin2')
            ->setEmail('admin@example.org')
            ->setOfficialCode('ADMIN')
            ->setCreatorId(1)
            ->addUserAsAdmin()//->addGroup($group)
        ;

        $manager->persist($user);

        $userRepo->updateUser($user);
        $userRepo->addUserToResourceNode($user->getId(), $user->getId());
        $manager->flush();

        //$this->assertTrue($user->isAdmin());
        //$this->assertTrue($user->isSuperAdmin());
        $this->assertSame(3, $userRepo->count([]));
    }

    public function testCreateUserWithApi(): void
    {
        $token = $this->getUserToken([]);
        $username = 'test';
        $this->createClientWithCredentials($token)->request(
            'POST',
            '/api/users',
            [
                'json' => [
                    'username' => $username,
                    'firstname' => 'test',
                    'lastname' => 'test',
                    'website' => '',
                    'biography' => '',
                    'locale' => 'en',
                    'plainPassword' => 'test',
                    'timezone' => 'Europe\Paris',
                    'email' => 'test@example.com',
                    //'expiresAt' => new \DateTime(),
                    'phone' => '123456',
                    'address' => 'Paris',
                    'roles' => [
                        'ROLE_USER',
                    ],
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            'username' => $username,
        ]);
    }

    public function testAddFriendToUser(): void
    {
        self::bootKernel();
        $em = self::getContainer()->get('doctrine')->getManager();

        $user = $this->createUser('user', 'user');
        $friend = $this->createUser('friend', 'friend');

        $userRepo = self::getContainer()->get(UserRepository::class);

        // user -> friend
        $user->addFriend($friend);
        $userRepo->update($user);

        $this->assertSame(1, $user->getFriends()->count());
        $this->assertSame('friend', $user->getFriends()->first()->getFriend()->getUsername());
        $this->assertSame(0, $user->getFriendsWithMe()->count());

        $em->clear();

        // Check friend
        $friend = $userRepo->find($friend->getId());
        $this->assertSame(1, $friend->getFriendsWithMe()->count());
        $this->assertSame(0, $friend->getFriends()->count());

        // another_friend -> user
        $anotherFriend = $this->createUser('anotherfriend', 'anotherfriend');
        $user = $userRepo->find($user->getId());

        $anotherFriend->addFriend($user);
        $userRepo->update($anotherFriend);

        $this->assertSame(1, $anotherFriend->getFriends()->count());
        $this->assertSame('user', $anotherFriend->getFriends()->first()->getFriend()->getUsername());
        $this->assertSame(0, $anotherFriend->getFriendsWithMe()->count());

        $em->clear();

        /** @var User $user */
        $user = $userRepo->find($user->getId());

        $this->assertSame(1, $user->getFriends()->count());
        $this->assertSame(1, $user->getFriendsWithMe()->count());

        $em->clear();

        // Delete friend
        $friend = $userRepo->find($friend->getId());
        $userRepo->delete($friend);

        $user = $userRepo->find($user->getId());
        $this->assertSame(0, $user->getFriends()->count());
        $this->assertSame(1, $user->getFriendsWithMe()->count());
    }

    public function testUpdateUserWithApi(): void
    {
        $user = $this->createUser('test');
        $token = $this->getUserToken([]);

        $this->createClientWithCredentials($token)->request(
            'PUT',
            '/api/users/'.$user->getId(),
            [
                'json' => [
                    'firstname' => 'updated',
                    'lastname' => 'updated',
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            'firstname' => 'updated',
            'lastname' => 'updated',
        ]);
    }

    public function testUserCreationAsStudent(): void
    {
        $this->createUser('pillo');
        $tokenTest = $this->getUserToken(
            [
                'username' => 'pillo',
                'password' => 'pillo',
            ]
        );

        // Try to create user.
        $username = 'test';
        $this->createClientWithCredentials($tokenTest)->request(
            'POST',
            '/api/users',
            [
                'json' => [
                    'username' => $username,
                    'firstname' => 'test',
                    'lastname' => 'test',
                    'website' => '',
                    'biography' => '',
                    'locale' => 'en',
                    'plainPassword' => 'test',
                    'timezone' => 'Europe\Paris',
                    'email' => 'test@example.com',
                    'phone' => '123456',
                    'address' => 'Paris',
                    'roles' => [
                        'ROLE_USER',
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(403);

        // Try to update admin!
        $this->createClientWithCredentials($tokenTest)->request(
            'PUT',
            '/api/users/1',
            [
                'json' => [
                    'firstname' => 'updated',
                    'lastname' => 'updated',
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(403);

        // Try to delete admin!
        $this->createClientWithCredentials($tokenTest)->request(
            'DELETE',
            '/api/users/1'
        );
        $this->assertResponseStatusCodeSame(403);
    }
}
