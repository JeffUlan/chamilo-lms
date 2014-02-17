<?php

/*
 * This file is part of PHP-FFmpeg.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FFMpeg\Format;

interface AudioInterface extends FormatInterface
{
    /**
     * Gets the audio kiloBitrate value.
     *
     * @return integer
     */
    public function getAudioKiloBitrate();

    /**
     * Returns an array of extra parameters to add to ffmpeg commandline.
     *
     * @return array()
     */
    public function getExtraParams();

    /**
     * Returns the audio codec.
     *
     * @return string
     */
    public function getAudioCodec();

    /**
     * Returns the list of available audio codecs for this format.
     *
     * @return array
     */
    public function getAvailableAudioCodecs();
}
