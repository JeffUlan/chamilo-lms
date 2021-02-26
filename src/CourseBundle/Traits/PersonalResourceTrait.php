<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Traits;

/**
 * Trait ShowCourseResourcesInSessionTrait.
 */
trait PersonalResourceTrait
{
    protected bool $loadPersonalResources = true;

    public function loadPersonalResources(): bool
    {
        return $this->loadPersonalResources;
    }
}
