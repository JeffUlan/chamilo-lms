<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Component\Editor\Driver;

/**
 * Class CourseDriver
 * @package Chamilo\CoreBundle\Component\Editor\Driver
 *
 */
class CourseDriver extends Driver
{
    public $name = 'CourseDriver';

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        if ($this->allow()) {
            //$translator = $this->connector->translator;
            //$code = $this->connector->course->getCode();
            $courseCode = $this->connector->course['code'];
            $alias = $courseCode.' '.get_lang('Documents');

            return array(
                'driver' => 'CourseDriver',
                'path' => $this->getCourseDocumentSysPath(),
                'URL' => $this->getCourseDocumentRelativeWebPath(),
                'accessControl' => array($this, 'access'),
                'alias' => $alias,
                'attributes' => array(
                    // Hide shared_folder
                    array(
                        'pattern' => '/shared_folder/',
                        'read' => false,
                        'write' => false,
                        'hidden' => true,
                        'locked' => false
                    ),
                )
            );
        }

        return array();
    }

    /**
     * This is the absolute document course path like
     * /var/www/portal/data/courses/XXX/document/
     * @return string
     */
    public function getCourseDocumentSysPath()
    {
        $url = null;
        if ($this->allow()) {
            //$directory = $this->connector->course->getDirectory();
            $directory = $this->connector->course['directory'];
            $dataPath = $this->connector->paths['sys_data_path'];
            $url = $dataPath.$directory.'/document/';
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getCourseDocumentRelativeWebPath()
    {
        $url = null;
        if ($this->allow()) {
            $directory = $this->connector->course['directory'];
            $url = api_get_path(REL_COURSE_PATH).$directory.'/document/';
        }

        return $url;
    }


    /**
     * @return string
     */
    public function getCourseDocumentWebPath()
    {
        $url = null;
        if ($this->allow()) {
            $directory = $this->connector->course->getDirectory();
            $url = api_get_path(WEB_COURSE_PATH).$directory.'/document/';
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function upload($fp, $dst, $name, $tmpname)
    {
        if ($this->allow()) {
            $this->setConnectorFromPlugin();

            // upload file by elfinder.
            $result = parent::upload($fp, $dst, $name, $tmpname);

            $name = $result['name'];
            $filtered = \URLify::filter($result['name'], 80);

            if (strcmp($name, $filtered) != 0) {
                /*$arg = array('target' => $file['hash'], 'name' => $filtered);
                $elFinder->exec('rename', $arg);*/
                $this->rename($result['hash'], $filtered);
            }

            $realPath = $this->realpath($result['hash']);

            if (!empty($realPath)) {
                // Getting file info
                //$info = $elFinder->exec('file', array('target' => $file['hash']));
                /** @var elFinderVolumeLocalFileSystem $volume */
                //$volume = $info['volume'];
                //$root = $volume->root();
                //var/www/chamilogits/data/courses/NEWONE/document
                $realPathRoot = $this->getCourseDocumentSysPath();

                // Removing course path
                $realPath = str_replace($realPathRoot, '/', $realPath);
                add_document(
                    $this->connector->course,
                    $realPath,
                    'file',
                    intval($result['size']),
                    $result['name']
                );
            }

            return $result;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function rm($hash)
    {
        // elfinder does not delete the file
        //parent::rm($hash);
        if ($this->allow()) {
            $this->setConnectorFromPlugin();

            $path = $this->decode($hash);
            $stat = $this->stat($path);
            $stat['realpath'] = $path;
            $this->removed[] = $stat;

            $realFilePath = $path;
            $coursePath = $this->getCourseDocumentSysPath();
            $filePath = str_replace($coursePath, '/', $realFilePath);

            \DocumentManager::delete_document(
                $this->connector->course,
                $filePath,
                $coursePath
            );

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function allow()
    {
        //if ($this->connector->security->isGranted('ROLE_ADMIN')) {
        $userId = api_get_user_id();
        return
            isset($this->connector->course) &&
            !empty($this->connector->course) &&
            !api_is_anonymous($userId, true)
        ;
    }
}
