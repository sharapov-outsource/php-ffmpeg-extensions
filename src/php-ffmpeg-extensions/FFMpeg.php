<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions;

use Alchemy\BinaryDriver\ConfigurationInterface;
use FFMpeg\Driver\FFMpegDriver;
use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Exception\RuntimeException;
use Psr\Log\LoggerInterface;
use Sharapov\FFMpegExtensions\Input\FileInterface;
use Sharapov\FFMpegExtensions\Media\Audio;
use Sharapov\FFMpegExtensions\Media\Video;

class FFMpeg extends \FFMpeg\FFMpeg
{
  public function __construct(FFMpegDriver $ffmpeg, FFProbe $ffprobe)
  {
    parent::__construct($ffmpeg, $ffprobe);
  }

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
      return new Audio($file, $this->getFFMpegDriver(), $this->getFFProbe());
    }

    throw new InvalidArgumentException('Unable to detect file format, only audio and video supported');
  }

  /**
   * Creates a new FFMpeg instance.
   *
   * @param array|ConfigurationInterface $configuration
   * @param LoggerInterface              $logger
   * @param FFProbe                      $probe
   *
   * @return FFMpeg
   */
  public static function create($configuration = array(), LoggerInterface $logger = null, FFProbe $probe = null)
  {
    if (null === $probe) {
      $probe = \Sharapov\FFMpegExtensions\FFProbe::create($configuration, $logger, null);
    }

    return new static(FFMpegDriver::create($logger, $configuration), $probe);
  }
}