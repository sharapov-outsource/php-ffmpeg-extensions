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
use FFMpeg\FFProbe;
use FFMpeg\Media\Audio;
use Sharapov\FFMpegExtensions\Media\Video;
use Sharapov\FFMpegExtensions\Stream\FileInterface;
use Alchemy\BinaryDriver\ConfigurationInterface;
use FFMpeg\Driver\FFMpegDriver;
use Psr\Log\LoggerInterface;

class FFMpeg
{
  /** @var FFMpegDriver */
  private $driver;
  /** @var FFProbe */
  private $ffprobe;

  public function __construct(FFMpegDriver $ffmpeg, FFProbe $ffprobe)
  {
    $this->driver = $ffmpeg;
    $this->ffprobe = $ffprobe;
  }

  /**
   * Sets FFProbe.
   *
   * @param FFProbe
   *
   * @return FFMpeg
   */
  public function setFFProbe(FFProbe $ffprobe)
  {
    $this->ffprobe = $ffprobe;

    return $this;
  }

  /**
   * Gets FFProbe.
   *
   * @return FFProbe
   */
  public function getFFProbe()
  {
    return $this->ffprobe;
  }

  /**
   * Sets the ffmpeg driver.
   *
   * @return FFMpeg
   */
  public function setFFMpegDriver(FFMpegDriver $ffmpeg)
  {
    $this->driver = $ffmpeg;

    return $this;
  }

  /**
   * Gets the ffmpeg driver.
   *
   * @return FFMpegDriver
   */
  public function getFFMpegDriver()
  {
    return $this->driver;
  }

  /**
   * Opens a file in order to be processed.
   *
   * @param \Sharapov\FFMpegExtensions\Stream\FileInterface $file
   *
   * @return Audio|Video
   *
   * @throws InvalidArgumentException
   */
  public function open(FileInterface $file)
  {
    if (null === $streams = $this->getFFProbe()->streams($file->getFile())) {
      throw new RuntimeException(sprintf('Unable to probe "%s".', $file->getFile()));
    }

    if (0 < count($streams->videos())) {
      return new Video($file, $this->getFFMpegDriver(), $this->getFFProbe());
    } elseif (0 < count($streams->audios())) {
      return new Audio($file->getFile(), $this->getFFMpegDriver(), $this->getFFProbe());
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
      $probe = FFProbe::create($configuration, $logger, null);
    }

    return new static(FFMpegDriver::create($logger, $configuration), $probe);
  }
}