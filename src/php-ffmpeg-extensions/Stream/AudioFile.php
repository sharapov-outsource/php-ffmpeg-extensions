<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Stream;

use FFMpeg\Exception\InvalidArgumentException;

/**
 * Class AudioFile
 * @package Sharapov\FFMpegExtensions\Stream
 */
class AudioFile extends File implements FileInterface
{
  public function setFile($file)
  {
    if (!file_exists($file)) {
      throw new InvalidArgumentException('Incorrect audio file path.');
    }

    $this->_file = $file;

    return $this;
  }
}