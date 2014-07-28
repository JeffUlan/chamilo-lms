<?php

namespace ChamiloLMS\CourseBundle\Tool;

/**
 * Class Dropbox
 * @package ChamiloLMS\CourseBundle\Tool
 */
class Dropbox extends BaseTool
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Exercise';
    }

    /**
     * {@inheritdoc}
     */
    public function getLink()
    {
        return 'dropbox/index.php';
    }

    public function getTarget()
    {
        return '_self';
    }

    public function getCategory()
    {
        return 'authoring';
    }
}
