<?php

namespace Gedmo\Translatable;

use Doctrine\Common\EventManager;
use Tool\BaseTestCaseORM;
use Translatable\Fixture\Article;
use Translatable\Fixture\Comment;

/**
 * These are tests for translatable behavior
 *
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @link http://www.gediminasm.org
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class TranslatableEntityDefaultTranslationTest extends BaseTestCaseORM
{
    const ARTICLE = 'Translatable\\Fixture\\Article';
    const COMMENT = 'Translatable\\Fixture\\Comment';
    const TRANSLATION = 'Gedmo\\Translatable\\Entity\\Translation';

    private $translatableListener;

    protected function setUp()
    {
        parent::setUp();

        $evm = new EventManager;
        $this->translatableListener = new TranslatableListener();
        $this->translatableListener->setTranslatableLocale('translatedLocale');
        $this->translatableListener->setDefaultLocale('defaultLocale');
        $evm->addEventSubscriber($this->translatableListener);

        $conn = array(
            'driver' => 'pdo_mysql',
            'host' => '127.0.0.1',
            'dbname' => 'test',
            'user' => 'root',
            'password' => 'nimda'
        );
        //$this->getMockCustomEntityManager($conn, $evm);
        $this->getMockSqliteEntityManager($evm);

        $this->repo = $this->em->getRepository(self::TRANSLATION);

    }



    // --- Tests for default translation overruling the translated entity
    //     property ------------------------------------------------------------



    function testTranslatedPropertyWithoutPersistingDefault()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( false );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title', 'defaultLocale'   , 'title defaultLocale'   )
            ->translate($entity, 'title', 'translatedLocale', 'title translatedLocale')
        ;
        $this->assertSame('title translatedLocale', $entity->getTitle());
    }

    function testTranslatedPropertyWithoutPersistingDefaultResorted()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( false );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title', 'translatedLocale', 'title translatedLocale')
            ->translate($entity, 'title', 'defaultLocale'   , 'title defaultLocale'   )
        ;
        $this->assertSame('title translatedLocale', $entity->getTitle());
    }

    function testTranslatedPropertyWithPersistingDefault()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( true );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title', 'defaultLocale'   , 'title defaultLocale'   )
            ->translate($entity, 'title', 'translatedLocale', 'title translatedLocale')
        ;
        $this->assertSame('title translatedLocale', $entity->getTitle());
    }

    function testTranslatedPropertyWithPersistingDefaultResorted()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( true );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title', 'translatedLocale', 'title translatedLocale')
            ->translate($entity, 'title', 'defaultLocale'   , 'title defaultLocale'   )
        ;
        $this->assertSame('title translatedLocale', $entity->getTitle());
    }



    // --- Tests for default translation making it into the entity's
    //     database row --------------------------------------------------------



    function testOnlyDefaultTranslationWithoutPersistingDefault()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( false );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title', 'defaultLocale'   , 'title defaultLocale'   )
        ;

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $trans = $this->repo->findTranslations($this->em->find(self::ARTICLE, $entity->getId()));
        $this->assertCount(0, $trans);

        $articles = $this->em->createQuery('SELECT a FROM ' . self::ARTICLE . ' a')->getArrayResult();
        $this->assertCount(1, $articles);
        $this->assertEquals('title defaultLocale', $articles[0]['title']);
    }

    function testOnlyDefaultTranslationWithPersistingDefault()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( true );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title', 'defaultLocale'   , 'title defaultLocale'   )
        ;

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $trans = $this->repo->findTranslations($this->em->find(self::ARTICLE, $entity->getId()));
        $this->assertCount(1, $trans);
        $this->assertSame('title defaultLocale', $trans['defaultLocale']['title']);

        $articles = $this->em->createQuery('SELECT a FROM ' . self::ARTICLE . ' a')->getArrayResult();
        $this->assertCount(1, $articles);
        $this->assertEquals('title defaultLocale', $articles[0]['title']);
    }

    function testUpdateTranslationInDefaultLocale()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( false );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title', 'defaultLocale'   , 'title defaultLocale'   )
            ->translate($entity, 'title', 'translatedLocale', 'title translatedLocale');

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $entity = $this->em->find(self::ARTICLE, 1);
        $entity->setTranslatableLocale('translatedLocale');
        $this->em->refresh($entity);

        $this->repo
             ->translate($entity, 'title', 'defaultLocale', 'update title defaultLocale');

        $this->em->flush();

        $qb = $this->em->createQueryBuilder('a');
        $qb->select('a')
           ->from(self::ARTICLE, 'a')
           ->where('a.id = 1');

        $fields = $qb->getQuery()->getArrayResult();

        $this->assertEquals( 'update title defaultLocale', $fields[0]['title']);
    }

    function testUpdateTranslationWithPersistingInDefaultLocale()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( true );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title', 'defaultLocale'   , 'title defaultLocale'   )
            ->translate($entity, 'title', 'translatedLocale', 'title translatedLocale');

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $entity = $this->em->find(self::ARTICLE, 1);
        $entity->setTranslatableLocale('translatedLocale');
        $this->em->refresh($entity);

        $this->repo
             ->translate($entity, 'title', 'defaultLocale', 'update title defaultLocale');

        $this->em->flush();

        $qb = $this->em->createQueryBuilder('a');
        $qb->select('a')
           ->from(self::ARTICLE, 'a')
           ->where('a.id = 1');

        $fields = $qb->getQuery()->getArrayResult();

        $this->assertEquals( 'update title defaultLocale', $fields[0]['title']);
    }

    /**
     * As this test does not provide a default translation, we assert
     * that a translated value is picked as default value
     */
    function testOnlyEntityTranslationWithoutPersistingDefault()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( false );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title', 'translatedLocale', 'title translatedLocale')
        ;

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $trans = $this->repo->findTranslations($this->em->find(self::ARTICLE, $entity->getId()));
        $this->assertCount(1, $trans);
        $this->assertSame('title translatedLocale', $trans['translatedLocale']['title']);

        $articles = $this->em->createQuery('SELECT a FROM ' . self::ARTICLE . ' a')->getArrayResult();
        $this->assertCount(1, $articles);
        $this->assertEquals('title translatedLocale', $articles[0]['title']);
    }

    /**
     * As this test does not provide a default translation, we assert
     * that a translated value is picked as default value
     */
    function testOnlyEntityTranslationWithPersistingDefault()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( true );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title', 'translatedLocale', 'title translatedLocale')
        ;

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $trans = $this->repo->findTranslations($this->em->find(self::ARTICLE, $entity->getId()));
        $this->assertCount(1, $trans);
        $this->assertSame('title translatedLocale', $trans['translatedLocale']['title']);

        $articles = $this->em->createQuery('SELECT a FROM ' . self::ARTICLE . ' a')->getArrayResult();
        $this->assertCount(1, $articles);
        $this->assertEquals('title translatedLocale', $articles[0]['title']);
    }

    function testDefaultAndEntityTranslationWithoutPersistingDefault()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( false );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title', 'defaultLocale'   , 'title defaultLocale'   )
            ->translate($entity, 'title', 'translatedLocale', 'title translatedLocale')
        ;

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $trans = $this->repo->findTranslations($this->em->find(self::ARTICLE, $entity->getId()));
        $this->assertCount(1, $trans);
        $this->assertSame('title translatedLocale', $trans['translatedLocale']['title']);

        $articles = $this->em->createQuery('SELECT a FROM ' . self::ARTICLE . ' a')->getArrayResult();
        $this->assertCount(1, $articles);
        $this->assertEquals('title defaultLocale', $articles[0]['title']);
    }

    function testDefaultAndEntityTranslationWithoutPersistingDefaultResorted()
    {

        $this->translatableListener->setPersistDefaultLocaleTranslation( false );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title', 'translatedLocale', 'title translatedLocale')
            ->translate($entity, 'title', 'defaultLocale'   , 'title defaultLocale'   )
        ;

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $trans = $this->repo->findTranslations($this->em->find(self::ARTICLE, $entity->getId()));
        $this->assertCount(1, $trans);
        $this->assertSame('title translatedLocale', $trans['translatedLocale']['title']);

        $articles = $this->em->createQuery('SELECT a FROM ' . self::ARTICLE . ' a')->getArrayResult();
        $this->assertCount(1, $articles);
        $this->assertEquals('title defaultLocale', $articles[0]['title']);
    }

    function testDefaultAndEntityTranslationWithPersistingDefault()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( true );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title', 'defaultLocale'   , 'title defaultLocale'   )
            ->translate($entity, 'title', 'translatedLocale', 'title translatedLocale')
        ;

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $trans = $this->repo->findTranslations($this->em->find(self::ARTICLE, $entity->getId()));
        $this->assertCount(2, $trans);
        $this->assertSame('title translatedLocale', $trans['translatedLocale']['title']);
        $this->assertSame('title defaultLocale', $trans['defaultLocale']['title']);

        $articles = $this->em->createQuery('SELECT a FROM ' . self::ARTICLE . ' a')->getArrayResult();
        $this->assertCount(1, $articles);
        $this->assertEquals('title defaultLocale', $articles[0]['title']);
    }

    function testDefaultAndEntityTranslationWithPersistingDefaultResorted()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( true );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title', 'translatedLocale', 'title translatedLocale')
            ->translate($entity, 'title', 'defaultLocale'   , 'title defaultLocale'   )
        ;

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $trans = $this->repo->findTranslations($this->em->find(self::ARTICLE, $entity->getId()));
        $this->assertCount(2, $trans);
        $this->assertSame('title translatedLocale', $trans['translatedLocale']['title']);
        $this->assertSame('title defaultLocale', $trans['defaultLocale']['title']);

        $articles = $this->em->createQuery('SELECT a FROM ' . self::ARTICLE . ' a')->getArrayResult();
        $this->assertCount(1, $articles);
        $this->assertEquals('title defaultLocale', $articles[0]['title']);
    }

    function testTwoFieldsWithoutPersistingDefault()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( false );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title'  , 'translatedLocale', 'title translatedLocale'  )
            ->translate($entity, 'title'  , 'defaultLocale'   , 'title defaultLocale'     )
            ->translate($entity, 'content', 'translatedLocale', 'content translatedLocale')
            ->translate($entity, 'content', 'defaultLocale'   , 'content defaultLocale'   )
        ;

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $trans = $this->repo->findTranslations($this->em->find(self::ARTICLE, $entity->getId()));
        $this->assertCount(1, $trans);
        $this->assertSame('title translatedLocale'  , $trans['translatedLocale']['title']);
        $this->assertSame('content translatedLocale', $trans['translatedLocale']['content']);

        $articles = $this->em->createQuery('SELECT a FROM ' . self::ARTICLE . ' a')->getArrayResult();
        $this->assertCount(1, $articles);
        $this->assertEquals('title defaultLocale'  , $articles[0]['title']  );
        $this->assertEquals('content defaultLocale', $articles[0]['content']);
    }

    function testTwoFieldsWithoutPersistingDefaultResorted()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( false );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title'  , 'defaultLocale'   , 'title defaultLocale'     )
            ->translate($entity, 'title'  , 'translatedLocale', 'title translatedLocale'  )
            ->translate($entity, 'content', 'defaultLocale'   , 'content defaultLocale'   )
            ->translate($entity, 'content', 'translatedLocale', 'content translatedLocale')
        ;

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $trans = $this->repo->findTranslations($this->em->find(self::ARTICLE, $entity->getId()));
        $this->assertCount(1, $trans);
        $this->assertSame('title translatedLocale'  , $trans['translatedLocale']['title']);
        $this->assertSame('content translatedLocale', $trans['translatedLocale']['content']);

        $articles = $this->em->createQuery('SELECT a FROM ' . self::ARTICLE . ' a')->getArrayResult();
        $this->assertCount(1, $articles);
        $this->assertEquals('title defaultLocale'  , $articles[0]['title']  );
        $this->assertEquals('content defaultLocale', $articles[0]['content']);
    }

    function testTwoFieldsWithPersistingDefault()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( true );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title'  , 'translatedLocale', 'title translatedLocale'  )
            ->translate($entity, 'title'  , 'defaultLocale'   , 'title defaultLocale'     )
            ->translate($entity, 'content', 'translatedLocale', 'content translatedLocale')
            ->translate($entity, 'content', 'defaultLocale'   , 'content defaultLocale'   )
        ;

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $trans = $this->repo->findTranslations($this->em->find(self::ARTICLE, $entity->getId()));
        $this->assertCount(2, $trans);
        $this->assertSame('title translatedLocale'  , $trans['translatedLocale']['title']);
        $this->assertSame('title defaultLocale'     , $trans['defaultLocale']['title']);
        $this->assertSame('content translatedLocale', $trans['translatedLocale']['content']);
        $this->assertSame('content defaultLocale'   , $trans['defaultLocale']['content']);

        $articles = $this->em->createQuery('SELECT a FROM ' . self::ARTICLE . ' a')->getArrayResult();
        $this->assertCount(1, $articles);
        $this->assertEquals('title defaultLocale'  , $articles[0]['title']  );
        $this->assertEquals('content defaultLocale', $articles[0]['content']);
    }

    function testTwoFieldsWithPersistingDefaultResorted()
    {
        $this->translatableListener->setPersistDefaultLocaleTranslation( true );
        $entity = new Article;
        $this->repo
            ->translate($entity, 'title'  , 'defaultLocale'   , 'title defaultLocale'     )
            ->translate($entity, 'title'  , 'translatedLocale', 'title translatedLocale'  )
            ->translate($entity, 'content', 'defaultLocale'   , 'content defaultLocale'   )
            ->translate($entity, 'content', 'translatedLocale', 'content translatedLocale')
        ;

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $trans = $this->repo->findTranslations($this->em->find(self::ARTICLE, $entity->getId()));
        $this->assertCount(2, $trans);
        $this->assertSame('title translatedLocale'  , $trans['translatedLocale']['title']);
        $this->assertSame('title defaultLocale'     , $trans['defaultLocale']['title']);
        $this->assertSame('content translatedLocale', $trans['translatedLocale']['content']);
        $this->assertSame('content defaultLocale'   , $trans['defaultLocale']['content']);

        $articles = $this->em->createQuery('SELECT a FROM ' . self::ARTICLE . ' a')->getArrayResult();
        $this->assertCount(1, $articles);
        $this->assertEquals('title defaultLocale'  , $articles[0]['title']  );
        $this->assertEquals('content defaultLocale', $articles[0]['content']);
    }



    // --- Fixture related methods ---------------------------------------------



    protected function getUsedEntityFixtures()
    {
        return array(
            self::ARTICLE,
            self::TRANSLATION
        );
    }
}
