<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\Tests\CoreBundle\Repository;

use Chamilo\CoreBundle\Entity\ExtraField;
use Chamilo\CoreBundle\Entity\ExtraFieldRelTag;
use Chamilo\CoreBundle\Entity\Tag;
use Chamilo\CoreBundle\Entity\UserRelTag;
use Chamilo\CoreBundle\Repository\ExtraFieldRelTagRepository;
use Chamilo\CoreBundle\Repository\TagRepository;
use Chamilo\Tests\AbstractApiTest;
use Chamilo\Tests\ChamiloTestTrait;

class TagRepositoryTest extends AbstractApiTest
{
    use ChamiloTestTrait;

    public function testCreate(): void
    {
        $em = $this->getEntityManager();
        $repo = self::getContainer()->get(TagRepository::class);

        $extraField = (new ExtraField())
            ->setDisplayText('test')
            ->setVariable('test')
            ->setExtraFieldType(ExtraField::USER_FIELD_TYPE)
            ->setFieldType(\ExtraField::FIELD_TYPE_TAG)
        ;
        $em->persist($extraField);
        $em->flush();

        $tag = (new Tag())
            ->setTag('php')
            ->setCount(0)
            ->setField($extraField)
        ;

        $this->assertHasNoEntityViolations($tag);
        $em->persist($tag);
        $em->flush();

        $this->assertSame(1, $repo->count([]));

        $tags = $repo->findTagsByField('php', $extraField);
        $this->assertCount(1, $tags);
    }

    public function testCreateUserRelTag(): void
    {
        $em = $this->getEntityManager();
        $repo = self::getContainer()->get(TagRepository::class);

        $extraField = (new ExtraField())
            ->setDisplayText('test')
            ->setVariable('test')
            ->setExtraFieldType(ExtraField::USER_FIELD_TYPE)
            ->setFieldType(\ExtraField::FIELD_TYPE_TAG)
        ;
        $em->persist($extraField);
        $em->flush();

        $tag = (new Tag())
            ->setTag('php')
            ->setField($extraField)
        ;
        $em->persist($tag);
        $em->flush();

        $user = $this->createUser('test');

        $userRelTag = (new UserRelTag())
            ->setUser($user)
            ->setTag($tag)
        ;
        $em->persist($userRelTag);
        $em->flush();
        $em->clear();

        $this->assertNotNull($userRelTag->getId());

        $tags = $repo->getTagsByUser($extraField, $user);
        $this->assertCount(1, $tags);

        /** @var Tag $tag */
        $tag = $repo->findOneBy(['tag' => 'php']);
        $this->assertNotNull($tag);

        $this->assertSame(1, $tag->getUserRelTags()->count());
        $user = $this->getUser('test');
        $this->assertSame(1, $user->getUserRelTags()->count());
    }

    public function testCreateExtraFieldRelTag(): void
    {
        $em = $this->getEntityManager();
        $repo = self::getContainer()->get(TagRepository::class);
        $extraFieldRelTagRepo = self::getContainer()->get(ExtraFieldRelTagRepository::class);

        $extraField = (new ExtraField())
            ->setDisplayText('test')
            ->setVariable('test')
            ->setExtraFieldType(ExtraField::USER_FIELD_TYPE)
            ->setFieldType(\ExtraField::FIELD_TYPE_TAG)
        ;
        $em->persist($extraField);

        $tag = (new Tag())
            ->setTag('php')
            ->setField($extraField)
        ;
        $em->persist($tag);
        $em->flush();

        $course = $this->createCourse('course');
        $itemId = $course->getId();

        $extraFieldRelTag = (new ExtraFieldRelTag())
            ->setItemId($itemId)
            ->setField($extraField)
            ->setTag($tag)
        ;
        $em->persist($extraFieldRelTag);
        $em->flush();
        $em->clear();

        $this->assertNotNull($extraFieldRelTag->getId());
        $this->assertSame(1, $extraFieldRelTagRepo->count([]));

        $tags = $repo->getTagsByItem($extraField, $itemId);

        $this->assertCount(1, $tags);
        $this->assertInstanceOf(Tag::class, $tags[0]);
    }
}
