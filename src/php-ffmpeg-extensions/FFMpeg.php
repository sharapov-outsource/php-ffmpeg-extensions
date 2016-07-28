<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions;

use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Exception\RuntimeException;
use FFMpeg\Media\Audio;
use FFMpeg\Media\Video;

class FFMpeg extends \FFMpeg\FFMpeg
{
  /**
   * Opens a file in order to be processed.
   *
   * @param string $pathfile A pathfile
   *
   * @return Audio|Video
   *
   * @throws InvalidArgumentException
   */
  public function open($pathfile)
  {
    if (null === $streams = $this->getFFProbe()->streams($pathfile)) {
      throw new RuntimeException(sprintf('Unable to probe "%s".', $pathfile));
    }

    if (0 < count($streams->videos())) {
      return new \Sharapov\FFMpegExtensions\Media\Video($pathfile, $this->getFFMpegDriver(), $this->getFFProbe());
    } elseif (0 < count($streams->audios())) {
      return new Audio($pathfile, $this->getFFMpegDriver(), $this->getFFProbe());
    }

    throw new InvalidArgumentException('Unable to detect file format, only audio and video supported');
  }
}