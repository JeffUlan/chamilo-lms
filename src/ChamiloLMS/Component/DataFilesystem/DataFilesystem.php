<?php
/* For licensing terms, see /license.txt */
namespace ChamiloLMS\Component\DataFilesystem;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console;
use Sunra\PhpSimple\HtmlDomParser;
use ChamiloLMS\Component\Editor\Connector;
use ChamiloLMS\Component\Editor\Driver\CourseDriver;
use MediaAlchemyst\Alchemyst;
use Unoconv\Unoconv;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @todo use Gaufrette to manage course files (some day)
 * @todo add security restrictions.
 * Class DataFilesystem
 * @package ChamiloLMS\Component\DataFilesystem
 */
class DataFilesystem
{
    /** @var array chamilo paths */
    private $paths;

    /** @var \Symfony\Component\Filesystem\Filesystem  */
    private $fs;

    /** @var Alchemyst  */
    private $converter;
    private $connector;

    /**
     * @param array $paths
     * @param Filesystem $filesystem
     * @param Connector $connector
     * @param Alchemyst $converter
     */
    public function __construct($paths, Filesystem $filesystem, Connector $connector, $converter = null)
    {
        $this->paths = $paths;
        $this->fs = $filesystem;
        $this->converter = $converter;
        $this->connector = $connector;
        $this->connector->setDriver('CourseDriver');
    }

    /**
     * Gets a file from the "data" folder
     * @param string $file
     * @return SplFileInfo
     * @throws \InvalidArgumentException
     */
    public function get($file)
    {
        $file = new SplFileInfo($this->paths['sys_data_path'].$file, null, null);
        if ($this->fs->exists($file)) {
            return $file;
        } else {
            throw new \InvalidArgumentException(
                sprintf(
                    'The file "%s" does not exists .',
                    $file
                )
            );
        }
    }

    /**
     * Gets a file from the data/courses/MATHS/document directory
     * @param $courseCode
     * @param $file
     * @return SplFileInfo
     */
    public function getCourseDocument($courseCode, $file)
    {
        $file = 'courses/'.$courseCode.'/document/'.$file;
        return $this->get($file);
    }

     /**
     * Gets a file from the data/courses/MATHS/scorm directory
     * @param $courseCode
     * @param $file
     * @return SplFileInfo
     */
    public function getCourseScormDocument($courseCode, $file)
    {
        $file = 'courses/'.$courseCode.'/scorm/'.$file;
        return $this->get($file);
    }

    /**
     * Gets a file from the data/courses/MATHS/document directory
     * @param $courseCode
     * @param $file
     * @return SplFileInfo
     */
    public function getCourseUploadFile($courseCode, $file)
    {
        $file = 'courses/'.$courseCode.'/upload/'.$file;
        return $this->get($file);
    }

    /**
     * Create folders
     * @param array $folderList
     * @param OutputInterface $output
     * @param string permissions
     */
    public function createFolders(array $folderList, OutputInterface $output = null, $folderPermissions = null)
    {
        if (empty($folderPermissions)) {
            $folderPermissions = api_get_permissions_for_new_directories();
        }

        if (!empty($folderList)) {
            foreach ($folderList as $folder) {
                if (!is_dir($folder)) {
                    $this->fs->mkdir($folder, $folderPermissions);
                    if ($output) {
                        $output->writeln("Folder <comment>'$folder'</comment> created");
                    }
                }
            }
        }
    }

    /**
     * @param array $folderList
     * @param OutputInterface $output
     */
    public function copyFolders(array $folderList, OutputInterface $output = null)
    {
        if (!empty($folderList)) {
            foreach ($folderList as $folderSource => $folderDestination) {
                $this->fs->mirror($folderSource, $folderDestination);
                $finder = new Finder();
                $files = $finder->files()->in($folderDestination);
                $this->fs->chmod($files, api_get_permissions_for_new_directories());
                if ($output) {
                    $output->writeln("Contents were copied from <comment>$folderSource</comment> to <comment>$folderDestination</comment>");
                }
            }
        }
    }

    /**
     * @return Finder
     */
    public function getStyleSheetFolders()
    {
        $finder = new Finder();
        $styleSheetFolder = $this->paths['root_sys'].'main/css';
        return $finder->directories()->depth('== 0')->in($styleSheetFolder);
    }

    /**
     * Creates a empty file inside the temp folder
     * @param string $fileName
     * @param string $extension
     * @return string
     */
    public function createTempFile($fileName = null, $extension = null)
    {
        if (empty($fileName)) {
            $fileName = mt_rand();
        }
        if (!empty($extension)) {
            $extension = ".$extension";
        }
        $filePath = $this->paths['sys_temp_path'].$fileName.$extension;
        $this->fs->touch($filePath);
        if ($this->fs->exists($filePath)) {
            return $filePath;
        }
        return null;
    }

    /**
     * Converts ../courses/ABC/document/file.jpg to
     * http://chamilo/courses/ABC/document/file.jpg
     * @param string $content
     * @return string
     */
    public function convertRelativeToAbsoluteUrl($content)
    {
        /** @var CourseDriver $courseDriver */
        $courseDriver = $this->connector->getDriver('CourseDriver');

        $dom = HtmlDomParser::str_get_html($content);
        //var_dump($this->connector->getDrivers());
        /** @var \simple_html_dom_node $image */
        foreach ($dom->find('img') as $image) {
            $image->src = str_replace(
                $courseDriver->getCourseDocumentRelativeWebPath(),
                $courseDriver->getCourseDocumentWebPath(),
                $image->src
            );
        }
        return $dom;
    }

    /**
     * Save string in a temp file.
     *
     * @param string $content
     * @param string $fileName
     * @param string $extension
     *
     * @return string file path
     */
    public function putContentInTempFile($content, $filename = null, $extension = null)
    {
        $file = $this->createTempFile($filename, $extension);
        if (!empty($file)) {
            $this->fs->dumpFile($file, $content);
            return $file;
        }
        return null;
    }

    /**
     * @param string $filePath
     * @param string $format
     * @return string
     */
    public function transcode($filePath, $format)
    {
        if ($this->fs->exists($filePath)) {
            $fileInfo = pathinfo($filePath);
            $fileName = $fileInfo['filename'];
            $newFilePath = str_replace(
                $fileInfo['basename'],
                $fileName.'.'.$format, $filePath
            );
            /** @var \MediaAlchemyst\DriversContainer $drivers */
            $drivers = $this->converter->getDrivers();
            $unoconv = $drivers['unoconv'];
            /** @var Unoconv $unoconv */
            //$drivers = $this->converter->turnInto($filePath, $newFilePath);
            $unoconv->transcode($filePath, $format, $newFilePath);
            if ($this->fs->exists($newFilePath)) {
                return $newFilePath;

            }
        }
        return false;
    }

}
