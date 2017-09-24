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
use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Exception\RuntimeException;
use Psr\Log\LoggerInterface;
use FFMpeg\Driver\FFMpegDriver;
use Sharapov\FFMpegExtensions\Input\FileInterface;
use Sharapov\FFMpegExtensions\Media\Audio;
use Sharapov\FFMpegExtensions\Media\Video;

class FFMpeg {
  /** @var FFMpegDriver */
  private $driver;
  /** @var FFProbe */
  private $ffprobe;

  public function __construct( FFMpegDriver $ffmpeg, FFProbe $ffprobe ) {
    $this->driver  = $ffmpeg;
    $this->ffprobe = $ffprobe;
  }

  /**
   * Sets FFProbe.
   *
   * @param FFProbe
   *
   * @return FFMpeg
   */
  public function setFFProbe( FFProbe $ffprobe ) {
    $this->ffprobe = $ffprobe;

    return $this;
  }

  /**
   * Gets FFProbe.
   *
   * @return FFProbe
   */
  public function getFFProbe() {
    return $this->ffprobe;
  }

  /**
   * Sets the ffmpeg driver.
   *
   * @return FFMpeg
   */
  public function setFFMpegDriver( FFMpegDriver $ffmpeg ) {
    $this->driver = $ffmpeg;

    return $this;
  }

  /**
   * Gets the ffmpeg driver.
   *
   * @return FFMpegDriver
   */
  public function getFFMpegDriver() {
    return $this->driver;
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
  public function open( $file ) {
    if ( ! $file instanceof FileInterface ) {
      throw new InvalidArgumentException( 'Input object must implement FileInterface' );
    }

    if ( null === $streams = $this->getFFProbe()->streams( $file->getPath() ) ) {
      throw new RuntimeException( sprintf( 'Unable to probe "%s".', $file->getPath() ) );
    }

    if ( 0 < count( $streams->videos() ) ) {
      return new Video( $file, $this->getFFMpegDriver(), $this->getFFProbe() );
    } elseif ( 0 < count( $streams->audios() ) ) {
      return new Audio( $file, $this->getFFMpegDriver(), $this->getFFProbe() );
    }

    throw new InvalidArgumentException( 'Unable to detect file format, only audio and video supported' );
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
  public static function create( $configuration = [], LoggerInterface $logger = null, FFProbe $probe = null ) {
    if ( null === $probe ) {
      $probe = \Sharapov\FFMpegExtensions\FFProbe::create( $configuration, $logger, null );
    }

    return new static( FFMpegDriver::create( $logger, $configuration ), $probe );
  }
}