<?php
/**
 * Doctrine ORM
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 */

namespace Doctrine\ORM\Tools\Pagination;

use Doctrine\ORM\Query\TreeWalkerAdapter;
use Doctrine\ORM\Query\AST\SelectStatement;
use Doctrine\ORM\Query\AST\SelectExpression;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\AST\AggregateExpression;

/**
 * Replaces the selectClause of the AST with a COUNT statement
 *
 * @category    DoctrineExtensions
 * @package     DoctrineExtensions\Paginate
 * @author      David Abdemoulaie <dave@hobodave.com>
 * @copyright   Copyright (c) 2010 David Abdemoulaie (http://hobodave.com/)
 * @license     http://hobodave.com/license.txt New BSD License
 */
class CountWalker extends TreeWalkerAdapter
{
    /**
     * Distinct mode hint name
     */
    const HINT_DISTINCT = 'doctrine_paginator.distinct';

    /**
     * Walks down a SelectStatement AST node, modifying it to retrieve a COUNT
     *
     * @param SelectStatement $AST
     * @return void
     */
    public function walkSelectStatement(SelectStatement $AST)
    {
        if ($AST->havingClause) {
            throw new \RuntimeException('Cannot count query that uses a HAVING clause. Use the output walkers for pagination');
        }

        $rootComponents = array();
        foreach ($this->_getQueryComponents() as $dqlAlias => $qComp) {
            $isParent = array_key_exists('parent', $qComp)
                && $qComp['parent'] === null
                && $qComp['nestingLevel'] == 0
            ;
            if ($isParent) {
                $rootComponents[] = array($dqlAlias => $qComp);
            }
        }
        if (count($rootComponents) > 1) {
            throw new \RuntimeException("Cannot count query which selects two FROM components, cannot make distinction");
        }
        $root                = reset($rootComponents);
        $parentName          = key($root);
        $parent              = current($root);
        $identifierFieldName = $parent['metadata']->getSingleIdentifierFieldName();

        $pathType = PathExpression::TYPE_STATE_FIELD;
        if (isset($parent['metadata']->associationMappings[$identifierFieldName])) {
            $pathType = PathExpression::TYPE_SINGLE_VALUED_ASSOCIATION;
        }

        $pathExpression = new PathExpression(
            PathExpression::TYPE_STATE_FIELD | PathExpression::TYPE_SINGLE_VALUED_ASSOCIATION, $parentName,
            $identifierFieldName
        );
        $pathExpression->type = $pathType;

        $distinct = $this->_getQuery()->getHint(self::HINT_DISTINCT);
        $AST->selectClause->selectExpressions = array(
            new SelectExpression(
                new AggregateExpression('count', $pathExpression, $distinct), null
            )
        );

        // ORDER BY is not needed, only increases query execution through unnecessary sorting.
        $AST->orderByClause = null;
    }
}

