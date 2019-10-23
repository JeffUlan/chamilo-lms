<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Hook;

use Chamilo\CoreBundle\Hook\Interfaces\HookUpdateUserEventInterface;
use Chamilo\CoreBundle\Hook\Interfaces\HookUpdateUserObserverInterface;

/**
 * Class HookUpdateUser.
 */
class HookUpdateUser extends HookEvent implements HookUpdateUserEventInterface
{
    /**
     * HookUpdateUser constructor.
     */
    public function __construct()
    {
        parent::__construct('HookUpdateUser');
    }

    /**
     * Update all the observers.
     *
     * @param int $type
     *
     * @return int
     */
    public function notifyUpdateUser($type)
    {
        $this->eventData['type'] = $type;

        /** @var HookUpdateUserObserverInterface $observer */
        foreach ($this->observers as $observer) {
            $observer->hookUpdateUser($this);
        }

        return 1;
    }
}
