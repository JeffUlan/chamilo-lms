<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Component\Editor\CkEditor\Toolbar;

/**
 * AssessmentsIntroduction toolbar configuration
 * 
 * @package Chamilo\CoreBundle\Component\Editor\CkEditor\Toolbar
 */
class AssessmentsIntroduction extends Basic
{
    /**
     * @return mixed
     */
    public function getConfig()
    {
        if (api_get_setting('more_buttons_maximized_mode') != 'true') {
            $config['toolbar'] = $this->getNormalToolbar();
        } else {
            $config['toolbar_minToolbar'] = $this->getMinimizedToolbar();
        }

        return $config;
    }

    protected function getNormalToolbar()
    {
        return [
            ['Save', 'Maximize', '-', 'PasteFromWord', '-', 'Undo', 'Redo'],
            ['Link', 'Unlink', 'Anchor'],
            ['Image', 'Video', 'Flash', 'Oembed', 'Youtube', 'Audio'],
            ['Table', 'SpecialChar'],
            ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'TextColor', 'BGColor'],
            '/',
            ['Styles', 'Format', 'Font', 'FontSize'],
            ['Bold', 'Italic', 'Underline'],
            ['Subscript', 'Superscript', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
            ['Source']
        ];
    }

    protected function getMinimizedToolbar()
    {
        return [
            ['Save', 'NewPage', 'Templates', '-', 'PasteFromWord'],
            ['Undo', 'Redo'],
            ['Link', 'Image', 'Video', 'Flash', 'Audio', 'Table', 'Asciimath', 'Asciisvg'],
            ['BulletedList', 'NumberedList', 'HorizontalRule'],
            ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
            ['Format', 'Font', 'FontSize', 'Bold', 'Italic', 'Underline', 'TextColor', 'BGColor'],
            ['Toolbarswitch']
        ];
    }

}
