<?php

namespace Gedmo\Tree;

use Doctrine\Common\EventManager;
use Tool\BaseTestCaseORM;
use Doctrine\Common\Util\Debug;
use Tree\Fixture\Closure\Category;
use Tree\Fixture\Closure\News;
use Tree\Fixture\Closure\CategoryClosure;
use Tree\Fixture\Closure\CategoryWithoutLevel;
use Tree\Fixture\Closure\CategoryWithoutLevelClosure;

/**
 * These are tests for Tree behavior
 *
 * @author Gustavo Adrian <comfortablynumb84@gmail.com>
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @package Gedmo.Tree
 * @link http://www.gediminasm.org
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ClosureTreeTest extends BaseTestCaseORM
{
    const CATEGORY = "Tree\\Fixture\\Closure\\Category";
    const CLOSURE = "Tree\\Fixture\\Closure\\CategoryClosure";
    const PERSON = "Tree\\Fixture\\Closure\\Person";
    const USER = "Tree\\Fixture\\Closure\\User";
    const PERSON_CLOSURE = "Tree\\Fixture\\Closure\\PersonClosure";
    const NEWS = "Tree\\Fixture\\Closure\\News";
    const CATEGORY_WITHOUT_LEVEL = "Tree\\Fixture\\Closure\\CategoryWithoutLevel";
    const CATEGORY_WITHOUT_LEVEL_CLOSURE = "Tree\\Fixture\\Closure\\CategoryWithoutLevelClosure";

    protected $listener;

    protected function setUp()
    {
        parent::setUp();

        $this->listener = new TreeListener;

        $evm = new EventManager;
        $evm->addEventSubscriber($this->listener);

        $this->getMockSqliteEntityManager($evm);
        $this->populate();
    }

    /*public function testHeavyLoad()
    {
        $start = microtime(true);
        $dumpTime = function($start, $msg) {
            $took = microtime(true) - $start;
            $minutes = intval($took / 60); $seconds = $took % 60;
            echo sprintf("%s --> %02d:%02d", $msg, $minutes, $seconds) . PHP_EOL;
        };
        $repo = $this->em->getRepository(self::CATEGORY);
        $parent = null;
        $num = 800;
        for($i = 0; $i < 500; $i++) {
            $cat = new Category;
            $cat->setParent($parent);
            $cat->setTitle('cat'.$i);
            $this->em->persist($cat);
            // siblings
            $rnd = rand(0, 3);
            for ($j = 0; $j < $rnd; $j++) {
                $siblingCat = new Category;
                $siblingCat->setTitle('cat'.$i.$j);
                $siblingCat->setParent($cat);
                $this->em->persist($siblingCat);
            }
            $num += $rnd;
            $parent = $cat;
        }
        $this->em->flush();
        $dumpTime($start, $num.' - inserts took:');
        $start = microtime(true);
        // test moving
        $target = $repo->findOneByTitle('cat300');
        $dest = $repo->findOneByTitle('cat2000');
        $target->setParent($dest);

        $target2 = $repo->findOneByTitle('cat450');
        $dest2 = $repo->findOneByTitle('cat2500');
        $target2->setParent($dest2);

        $this->em->flush();
        $dumpTime($start, 'moving took:');
    }*/

    public function testClosureTree()
    {
        $repo = $this->em->getRepository(self::CATEGORY);
        $closureRepo = $this->em->getRepository(self::CLOSURE);

        $food = $repo->findOneByTitle('Food');
        $dql = 'SELECT c FROM '.self::CLOSURE.' c';
        $dql .= ' WHERE c.ancestor = :ancestor';
        $query = $this->em->createQuery($dql);
        $query->setParameter('ancestor', $food);

        $foodClosures = $query->getResult();
        $this->assertCount(12, $foodClosures);
        foreach ($foodClosures as $closure) {
            $descendant = $closure->getDescendant();
            if ($descendant === $food) {
                $this->assertEquals(0, $closure->getDepth());
                continue;
            }
            $descendantTitle = $descendant->getTitle();
            $query->setParameter('ancestor', $descendant);
            $descendantClosures = $query->getResult();
            switch ($descendantTitle) {
                case 'Fruits':
                    $this->assertCount(5, $descendantClosures);
                    $this->assertEquals(1, $closure->getDepth());
                    break;
                case 'Oranges':
                    $this->assertCount(1, $descendantClosures);
                    $this->assertEquals(2, $closure->getDepth());
                    break;
                case 'Berries':
                    $this->assertCount(2, $descendantClosures);
                    $this->assertEquals(2, $closure->getDepth());
                    break;
                case 'Vegitables':
                    $this->assertCount(3, $descendantClosures);
                    $this->assertEquals(1, $closure->getDepth());
                    break;
                case 'Milk':
                    $this->assertCount(3, $descendantClosures);
                    $this->assertEquals(1, $closure->getDepth());
                    break;
                case 'Cheese':
                    $this->assertCount(2, $descendantClosures);
                    $this->assertEquals(2, $closure->getDepth());
                    break;
                case 'Strawberries':
                    $this->assertCount(1, $descendantClosures);
                    $this->assertEquals(3, $closure->getDepth());
                    break;
            }
        }
    }

    public function testUpdateOfParent()
    {
        $repo = $this->em->getRepository(self::CATEGORY);
        $strawberries = $repo->findOneByTitle('Strawberries');
        $cheese = $repo->findOneByTitle('Cheese');

        $strawberries->setParent($cheese);
        $this->em->persist($strawberries);
        $this->em->flush();

        $dql = 'SELECT c FROM '.self::CLOSURE.' c';
        $dql .= ' WHERE c.descendant = :descendant';
        $query = $this->em->createQuery($dql);
        $query->setParameter('descendant', $strawberries);

        $closures = $query->getResult();
        $this->assertTrue($this->hasAncestor($closures, 'Cheese'));
        $this->assertTrue($this->hasAncestor($closures, 'Milk'));
        $this->assertTrue($this->hasAncestor($closures, 'Food'));
        $this->assertFalse($this->hasAncestor($closures, 'Berries'));
        $this->assertFalse($this->hasAncestor($closures, 'Fruits'));
    }

    public function testAnotherUpdateOfParent()
    {
        $repo = $this->em->getRepository(self::CATEGORY);
        $strawberries = $repo->findOneByTitle('Strawberries');

        $strawberries->setParent(null);
        $this->em->persist($strawberries);
        $this->em->flush();

        $dql = 'SELECT c FROM '.self::CLOSURE.' c';
        $dql .= ' WHERE c.descendant = :descendant';
        $query = $this->em->createQuery($dql);
        $query->setParameter('descendant', $strawberries);

        $closures = $query->getResult();
        $this->assertCount(1, $closures);
        $this->assertTrue($this->hasAncestor($closures, 'Strawberries'));
    }

    public function testBranchRemoval()
    {
        $repo = $this->em->getRepository(self::CATEGORY);
        $fruits = $repo->findOneByTitle('Fruits');

        $id = $fruits->getId();
        $this->em->remove($fruits);
        $this->em->flush();

        $dql = 'SELECT COUNT(c) FROM '.self::CLOSURE.' c';
        $dql .= ' JOIN c.descendant d';
        $dql .= ' JOIN c.ancestor a';
        $dql .= ' WHERE (a.id = :id OR d.id = :id)';
        $query = $this->em->createQuery($dql);
        $query->setParameter('id', $id);

        $this->assertEquals(0, $query->getSingleScalarResult());
        // pdo_sqlite will not cascade
    }

    /**
     * @expectedException Gedmo\Exception\UnexpectedValueException
     */
    public function testSettingParentToChild()
    {
        $repo = $this->em->getRepository(self::CATEGORY);
        $fruits = $repo->findOneByTitle('Fruits');
        $strawberries = $repo->findOneByTitle('Strawberries');

        $fruits->setParent($strawberries);
        $this->em->flush();
    }

    public function testIfEntityHasNotIncludedTreeLevelFieldThenDontProcessIt()
    {
        $listener = $this->getMock('Gedmo\Tree\TreeListener', array('getStrategy'));
        $strategy = $this->getMock('Gedmo\Tree\Strategy\ORM\Closure', array('setLevelFieldOnPendingNodes'), array($listener));
        $listener->expects($this->any())
            ->method('getStrategy')
            ->will($this->returnValue($strategy));

        $strategy->expects($this->never())
            ->method('setLevelFieldOnPendingNodes');

        $evm = $this->em->getEventManager();

        $evm->removeEventListener($this->listener->getSubscribedEvents(), $this->listener);
        $evm->addEventListener($this->listener->getSubscribedEvents(), $this->listener);

        $cat = new CategoryWithoutLevel();
        $cat->setTitle('Test');

        $this->em->persist($cat);
        $this->em->flush();
    }

    private function hasAncestor($closures, $name)
    {
        $result = false;
        foreach ($closures as $closure) {
            $ancestor = $closure->getAncestor();
            if ($ancestor->getTitle() === $name) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    protected function getUsedEntityFixtures()
    {
        return array(
            self::CATEGORY,
            self::CLOSURE,
            self::PERSON,
            self::PERSON_CLOSURE,
            self::USER,
            self::NEWS,
            self::CATEGORY_WITHOUT_LEVEL,
            self::CATEGORY_WITHOUT_LEVEL_CLOSURE
        );
    }

    private function populate()
    {
        $food = new Category;
        $food->setTitle("Food");
        $this->em->persist($food);

        $fruits = new Category;
        $fruits->setTitle('Fruits');
        $fruits->setParent($food);
        $this->em->persist($fruits);

        $oranges = new Category;
        $oranges->setTitle('Oranges');
        $oranges->setParent($fruits);
        $this->em->persist($oranges);

        $lemons = new Category;
        $lemons->setTitle('Lemons');
        $lemons->setParent($fruits);
        $this->em->persist($lemons);

        $berries = new Category;
        $berries->setTitle('Berries');
        $berries->setParent($fruits);
        $this->em->persist($berries);

        $strawberries = new Category;
        $strawberries->setTitle('Strawberries');
        $strawberries->setParent($berries);
        $this->em->persist($strawberries);

        $vegitables = new Category;
        $vegitables->setTitle('Vegitables');
        $vegitables->setParent($food);
        $this->em->persist($vegitables);

        $cabbages = new Category;
        $cabbages->setTitle('Cabbages');
        $cabbages->setParent($vegitables);
        $this->em->persist($cabbages);

        $carrots = new Category;
        $carrots->setTitle('Carrots');
        $carrots->setParent($vegitables);
        $this->em->persist($carrots);

        $milk = new Category;
        $milk->setTitle('Milk');
        $milk->setParent($food);
        $this->em->persist($milk);

        $cheese = new Category;
        $cheese->setTitle('Cheese');
        $cheese->setParent($milk);
        $this->em->persist($cheese);

        $mouldCheese = new Category;
        $mouldCheese->setTitle('Mould cheese');
        $mouldCheese->setParent($cheese);
        $this->em->persist($mouldCheese);

        $this->em->flush();
    }

    public function testCascadePersistTree()
    {
        $politics = new Category();
        $politics->setTitle('Politics');

        $news = new News('Lorem ipsum', $politics);
        $this->em->persist($news);
        $this->em->flush();

        $closure = $this->em->createQueryBuilder()
                    ->select('c')
                    ->from(self::CLOSURE, 'c')
                    ->where('c.ancestor = :ancestor')
                    ->setParameter('ancestor', $politics->getId())
                    ->getQuery()
                    ->getResult();

        $this->assertCount(1, $closure);
    }
}
