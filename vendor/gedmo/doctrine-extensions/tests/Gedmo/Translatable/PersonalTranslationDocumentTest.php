<?php

namespace Gedmo\Translatable;

use Tool\BaseTestCaseMongoODM;
use Doctrine\Common\EventManager;
use Translatable\Fixture\Document\Personal\Article;
use Translatable\Fixture\Document\Personal\ArticleTranslation;

/**
 * These are tests for translatable behavior
 *
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @link http://www.gediminasm.org
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class PersonalTranslationDocumentTest extends BaseTestCaseMongoODM
{
    const ARTICLE = 'Translatable\Fixture\Document\Personal\Article';
    const TRANSLATION = 'Translatable\Fixture\Document\Personal\ArticleTranslation';

    private $translatableListener;
    private $id;

    protected function setUp()
    {
        parent::setUp();

        $evm = new EventManager;
        $this->translatableListener = new TranslatableListener();
        $this->translatableListener->setDefaultLocale('en');
        $this->translatableListener->setTranslatableLocale('en');
        $evm->addEventSubscriber($this->translatableListener);

        $this->getMockDocumentManager($evm);
    }

    /**
     * @test
     */
    function shouldCreateTranslations()
    {
        $this->populate();
        $article = $this->dm->getRepository(self::ARTICLE)->find($this->id);
        $translations = $article->getTranslations();

        $this->assertCount(2, $translations);
    }

    /**
     * @test
     */
    function shouldTranslateTheRecord()
    {
        $this->populate();
        $this->translatableListener->setTranslatableLocale('lt');

        $article = $this->dm->getRepository(self::ARTICLE)->find($this->id);
        $this->assertEquals('lt', $article->getTitle());
    }

    private function populate()
    {
        $article = new Article;
        $article->setTitle('en');

        $this->dm->persist($article);
        $this->dm->flush();

        $this->id = $article->getId();

        $this->translatableListener->setTranslatableLocale('de');
        $article->setTitle('de');

        $ltTranslation = new ArticleTranslation;
        $ltTranslation
            ->setField('title')
            ->setContent('lt')
            ->setObject($article)
            ->setLocale('lt')
        ;
        $this->dm->persist($ltTranslation);
        $this->dm->persist($article);
        $this->dm->flush();
        $this->dm->clear();
    }
}