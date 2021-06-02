<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 */

namespace Sharapov\FFMpegExtensions\Format;

interface VideoInterface extends \FFMpeg\Format\VideoInterface
{
    /**
     * Returns the Constant Rate Factor
     *
     * @return array
     */
    public function getConstantRateFactor();
}
