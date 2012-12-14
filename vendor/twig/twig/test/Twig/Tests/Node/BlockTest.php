<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Twig_Tests_Node_BlockTest extends Twig_Test_NodeTestCase
{
    /**
     * @covers Twig_Node_Block::__construct
     */
    public function testConstructor()
    {
        $body = new Twig_Node_Text('foo', 1);
        $node = new Twig_Node_Block('foo', $body, 1);

        $this->assertEquals($body, $node->getNode('body'));
        $this->assertEquals('foo', $node->getAttribute('name'));
    }

    /**
     * @covers Twig_Node_Block::compile
     * @dataProvider getTests
     */
    public function testCompile($node, $source, $environment = null)
    {
        parent::testCompile($node, $source, $environment);
    }

    public function getTests()
    {
        $body = new Twig_Node_Text('foo', 1);
        $node = new Twig_Node_Block('foo', $body, 1);

        return array(
            array($node, <<<EOF
// line 1
public function block_foo(\$context, array \$blocks = array())
{
    echo "foo";
}
EOF
            ),
        );
    }
}
