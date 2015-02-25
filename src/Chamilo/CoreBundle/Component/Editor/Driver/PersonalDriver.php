<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Component\Editor\Driver;

/**
 * Class PersonalDriver
 * @todo add more checks in upload/rm
 * @package Chamilo\CoreBundle\Component\Editor\Driver
 */
class PersonalDriver extends Driver
{
    public $name = 'PersonalDriver';

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        if ($this->allow()) {

            $userId = api_get_user_id();
            if (!empty($userId)) {

                // Adding user personal files
                $dir = \UserManager::get_user_picture_path_by_id(
                    $userId,
                    'system'
                );
                $dirWeb = \UserManager::get_user_picture_path_by_id(
                    $userId,
                    'web'
                );

                $driver = array(
                    'driver' => 'PersonalDriver',
                    'alias' => get_lang('MyFiles'),
                    'path' => $dir['dir'] . 'my_files',
                    'URL' => $dirWeb['dir'] . 'my_files',
                    'accessControl' => array($this, 'access')
                );

                return $driver;
            }
        }

        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function upload($fp, $dst, $name, $tmpname)
    {
        if ($this->allow()) {
            $this->setConnectorFromPlugin();

            return parent::upload($fp, $dst, $name, $tmpname);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rm($hash)
    {
        if ($this->allow()) {
            $this->setConnectorFromPlugin();

            return parent::rm($hash);
        }
    }

    /**
     * @return bool
     */
    public function allow()
    {
        //if ($this->connector->security->isGranted('IS_AUTHENTICATED_FULLY')) {
        return !api_is_anonymous();
    }
}
