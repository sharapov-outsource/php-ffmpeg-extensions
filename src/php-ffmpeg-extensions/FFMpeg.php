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
use Alchemy\BinaryDriver\ConfigurationInterface;
use FFMpeg\Driver\FFMpegDriver;
use FFMpeg\FFProbe;
use Psr\Log\LoggerInterface;
use Sharapov\FFMpegExtensions\Input\FileInterface;
use Sharapov\FFMpegExtensions\Media\Audio;
use Sharapov\FFMpegExtensions\Media\Video;

class FFMpeg extends \FFMpeg\FFMpeg
{
  /**
   * Opens a file in order to be processed.
   *
   * @param $file
   *
   * @return Audio|Video
   *
   * @throws InvalidArgumentException
   */
  public function open($file)
  {
    if(!$file instanceof FileInterface) {
      throw new InvalidArgumentException('Input object must implement FileInterface');
    }

    if (null === $streams = $this->getFFProbe()->streams($file->getPath())) {
      throw new RuntimeException(sprintf('Unable to probe "%s".', $file->getPath()));
    }

    if (0 < count($streams->videos())) {
      return new Video($file, $this->getFFMpegDriver(), $this->getFFProbe());
    } elseif (0 < count($streams->audios())) {
      return new Video($file, $this->getFFMpegDriver(), $this->getFFProbe());
    }

    throw new InvalidArgumentException('Unable to detect file format, only audio and video supported');
  }
}