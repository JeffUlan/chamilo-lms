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

namespace Composer\Repository\Pear;

use Composer\Util\RemoteFilesystem;

/**
 * PEAR Channel package reader.
 *
 * Reads channel packages info from and builds Package's
 *
 * @author Alexey Prilipko <palex@farpost.com>
 */
class ChannelReader extends BaseChannelReader
{
    /** @var array of ('xpath test' => 'rest implementation') */
    private $readerMap;

    public function __construct(RemoteFilesystem $rfs)
    {
        parent::__construct($rfs);

        $rest10reader = new ChannelRest10Reader($rfs);
        $rest11reader = new ChannelRest11Reader($rfs);

        $this->readerMap = array(
            'REST1.3' => $rest11reader,
            'REST1.2' => $rest11reader,
            'REST1.1' => $rest11reader,
            'REST1.0' => $rest10reader,
        );
    }

    /**
     * Reads PEAR channel through REST interface and builds list of packages
     *
     * @param $url string PEAR Channel url
     * @return ChannelInfo
     */
    public function read($url)
    {
        $xml = $this->requestXml($url, "/channel.xml");

        $channelName = (string) $xml->name;
        $channelSummary = (string) $xml->summary;
        $channelAlias = (string) $xml->suggestedalias;

        $supportedVersions = array_keys($this->readerMap);
        $selectedRestVersion = $this->selectRestVersion($xml, $supportedVersions);
        if (!$selectedRestVersion) {
            throw new \UnexpectedValueException(sprintf('PEAR repository %s does not supports any of %s protocols.', $url, implode(', ', $supportedVersions)));
        }

        $reader = $this->readerMap[$selectedRestVersion['version']];
        $packageDefinitions = $reader->read($selectedRestVersion['baseUrl']);

        return new ChannelInfo($channelName, $channelAlias, $packageDefinitions);
    }

    /**
     * Reads channel supported REST interfaces and selects one of them
     *
     * @param $channelXml \SimpleXMLElement
     * @param $supportedVersions string[] supported PEAR REST protocols
     * @return array|null hash with selected version and baseUrl
     */
    private function selectRestVersion($channelXml, $supportedVersions)
    {
        $channelXml->registerXPathNamespace('ns', self::CHANNEL_NS);

        foreach ($supportedVersions as $version) {
            $xpathTest = "ns:servers/ns:primary/ns:rest/ns:baseurl[@type='{$version}']";
            $testResult = $channelXml->xpath($xpathTest);
            if (count($testResult) > 0) {
                return array('version' => $version, 'baseUrl' => (string) $testResult[0]);
            }
        }

        return null;
    }
}
