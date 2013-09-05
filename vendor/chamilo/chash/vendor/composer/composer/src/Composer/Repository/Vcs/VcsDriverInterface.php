<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Repository\Vcs;

use Composer\IO\IOInterface;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
interface VcsDriverInterface
{
    /**
     * Initializes the driver (git clone, svn checkout, fetch info etc)
     */
    public function initialize();

    /**
     * Return the composer.json file information
     *
     * @param  string $identifier Any identifier to a specific branch/tag/commit
     * @return array  containing all infos from the composer.json file
     */
    public function getComposerInformation($identifier);

    /**
     * Return the root identifier (trunk, master, default/tip ..)
     *
     * @return string Identifier
     */
    public function getRootIdentifier();

    /**
     * Return list of branches in the repository
     *
     * @return array Branch names as keys, identifiers as values
     */
    public function getBranches();

    /**
     * Return list of tags in the repository
     *
     * @return array Tag names as keys, identifiers as values
     */
    public function getTags();

    /**
     * @param  string $identifier Any identifier to a specific branch/tag/commit
     * @return array  With type, url reference and shasum keys.
     */
    public function getDist($identifier);

    /**
     * @param  string $identifier Any identifier to a specific branch/tag/commit
     * @return array  With type, url and reference keys.
     */
    public function getSource($identifier);

    /**
     * Return the URL of the repository
     *
     * @return string
     */
    public function getUrl();

    /**
     * Return true if the repository has a composer file for a given identifier,
     * false otherwise.
     *
     * @param  string  $identifier Any identifier to a specific branch/tag/commit
     * @return boolean Whether the repository has a composer file for a given identifier.
     */
    public function hasComposerFile($identifier);

    /**
     * Checks if this driver can handle a given url
     *
     * @param  IOInterface $io   IO instance
     * @param  string      $url
     * @param  bool        $deep unless true, only shallow checks (url matching typically) should be done
     * @return bool
     */
    public static function supports(IOInterface $io, $url, $deep = false);
}
