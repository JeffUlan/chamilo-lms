<?php
/* For licensing terms, see /license.txt */

use Chamilo\UserBundle\Entity\User;

class InputUser extends HTML_QuickForm_input
{
    /** @var User */
    private $user = null;
    private $imageSize = 'small';
    private $subTitle = '';

    public function __construct($name, $label, $attributes = [])
    {
        if (isset($attributes['image_size'])) {
            $this->imageSize = $attributes['image_size'];
            unset($attributes['image_size']);
        }

        if (isset($attributes['sub_title'])) {
            $this->subTitle = $attributes['sub_title'];
            unset($attributes['sub_title']);
        }

        parent::__construct($name, $label, $attributes);

        $this->setType('hidden');
    }

    /**
     * @inheritDoc
     */
    public function setValue($value)
    {
        $this->user = !is_a($value, 'Chamilo\UserBundle\Entity\User')
            ? UserManager::getManager()->find($value)
            : $value;

        parent::setValue($this->user->getId());
    }

    public function toHtml()
    {
        if (!$this->user) {
            return '';
        }

        $userInfo = api_get_user_info($this->user->getId());
        $userPicture = isset($userInfo["avatar_{$this->imageSize}"])
            ? $userInfo["avatar_{$this->imageSize}"]
            : $userInfo["avatar"];

        if (!$this->subTitle) {
            $this->subTitle = $this->user->getUsername();
        }

        $html = parent::toHtml();
        $html .= '
            <div class="media">
                <div class="media-left">
                    <img src="'.$userPicture.'" alt="'.$this->user->getCompleteName().'">
                </div>
                <div class="media-body">
                    <h4 class="media-heading">'.$this->user->getCompleteName().'</h4>
                    '.$this->subTitle.'
                </div>
            </div>
        ';

        return $html;
    }
}
