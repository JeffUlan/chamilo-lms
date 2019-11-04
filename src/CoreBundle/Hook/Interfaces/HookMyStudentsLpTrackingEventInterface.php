<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Hook\Interfaces;

/**
 * Interface HookMyStudentsLpTrackingEventInterface.
 *
 * @package Chamilo\CoreBundle\Hook\Interfaces
 */
interface HookMyStudentsLpTrackingEventInterface extends HookEventInterface
{
    public function notifyTrackingHeader(): array;

    /**
     * @param int $lpId
     * @param int $studentId
     */
    public function notifyTrackingContent($lpId, $studentId): array;
}
