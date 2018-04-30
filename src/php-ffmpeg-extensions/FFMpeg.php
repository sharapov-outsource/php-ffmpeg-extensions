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
use Alchemy\BinaryDriver\Exception\ExecutionFailureException;
use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Exception\RuntimeException;
use FFMpeg\Driver\FFMpegDriver;
use FFMpeg\Format\FormatInterface;
use Psr\Log\LoggerInterface;
use Sharapov\FFMpegExtensions\Input\FileInterface;
use Sharapov\FFMpegExtensions\Media\Audio;
use Sharapov\FFMpegExtensions\Media\CollectionInterface;
use Sharapov\FFMpegExtensions\Media\Video;

class FFMpeg {
  /** @var FFMpegDriver */
  private $_driver;
  /** @var FFProbe */
  private $_ffprobe;
  /** @var string */
  private $_tmpDir;
  /** @var array */
  private static $_mimes = [
    'video/quicktime',
    'video/mpeg',
    'video/mp4',
    'audio/mpeg3',
    'audio/x-mpeg-3',
    'audio/mpeg',
    'audio/wav',
    'audio/x-wav',
    'image/gif',
    'image/png',
    'image/bmp',
    'image/x-windows-bmp',
    'image/jpeg',
    'image/pjpeg',
    'application/x-font-ttf'
  ];

  /**
   * FFMpeg constructor
   *
   * @param FFMpegDriver $ffmpeg
   * @param FFProbe      $ffprobe
   */
  public function __construct( FFMpegDriver $ffmpeg, FFProbe $ffprobe ) {
    $this->_driver  = $ffmpeg;
    $this->_ffprobe = $ffprobe;
    $this->_tmpDir  = getcwd() . '/data/tmp/';
  }

  /**
   * Sets FFProbe
   *
   * @param FFProbe $ffprobe
   *
   * @return FFMpeg
   */
  public function setFFProbe( FFProbe $ffprobe ) {
    $this->_ffprobe = $ffprobe;

    return $this;
  }

  /**
   * Gets FFProbe
   *
   * @return FFProbe
   */
  public function getFFProbe() {
    return $this->_ffprobe;
  }

  /**
   * Sets FFMpeg driver
   *
   * @param FFMpegDriver $ffmpeg
   *
   * @return $this
   */
  public function setFFMpegDriver( FFMpegDriver $ffmpeg ) {
    $this->_driver = $ffmpeg;

    return $this;
  }

  /**
   * Gets the ffmpeg driver.
   *
   * @return FFMpegDriver
   */
  public function getFFMpegDriver() {
    return $this->_driver;
  }

  /**
   * Opens a file in order to be processed.
   *
   * @param $file
   *
   * @return Audio|Video
   *
   * @throws InvalidArgumentException
   * @throws RuntimeException
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
   * Concatenate two or more streams
   *
   * @param \Sharapov\FFMpegExtensions\Media\CollectionInterface $collection
   * @param string                                               $outputPathFile
   * @param FormatInterface                                      $format
   *
   * @return string
   */
  public function concatenate( FormatInterface $format, CollectionInterface $collection, $outputPathFile ) {
    if ( $collection->count() == 0 ) {
      throw new InvalidArgumentException( 'Collection is empty' );
    }

    /**
     * @see https://ffmpeg.org/ffmpeg-formats.html#concat
     * @see https://trac.ffmpeg.org/wiki/Concatenate
     */
    $sourcesFile = $this->_tmpDir . uniqid( 'ffmpeg-concat-' );
    // Set the content of this file
    $fileStream = @fopen( $sourcesFile, 'w' );

    if ( $fileStream === false ) {
      throw new ExecutionFailureException( 'Cannot open a temporary file.' );
    }

    $sources = [];
    // Pre-encode each clip in collection
    foreach ( $collection as $i => $item ) {
      if ( $item instanceof Video ) {
        $tmpFile = 'ffmpeg-' . uniqid( md5( time() ) . '-' ) . '.mp4';
        $item
          ->save( $format, $this->_tmpDir . $tmpFile );
        $sources[] = $this->_tmpDir . $tmpFile;
        fwrite( $fileStream, "file " . $tmpFile . "\n" );
      }
    }
    fclose( $fileStream );

    // Execute the command
    try {
      $this->_driver->command( [
                                 '-y',
                                 '-f',
                                 'concat',
                                 '-safe',
                                 '0',
                                 '-i',
                                 $sourcesFile,
                                 '-c',
                                 'copy',
                                 $outputPathFile
                               ] );
    } catch ( ExecutionFailureException $e ) {
      $this->_cleanupTemporaryFile( $outputPathFile );
      $this->_cleanupTemporaryFile( $sourcesFile );
      $this->_cleanupTemporaryFile( $sources );
      throw new RuntimeException( 'Unable to save concatenated video', $e->getCode(), $e );
    }

    $this->_cleanupTemporaryFile( $sources );
    $this->_cleanupTemporaryFile( $sourcesFile );

    return $outputPathFile;
  }

  /**
   * Cleanup temporary files
   *
   * @param $filename
   *
   * @return $this
   */
  private function _cleanupTemporaryFile( $filename ) {
    if ( is_array( $filename ) ) {
      foreach ( $filename as $file ) {
        $this->_cleanupTemporaryFile( $file );
      }
    } else {
      if ( file_exists( $filename ) && is_writable( $filename ) ) {
        unlink( $filename );
      }
    }

    return $this;
  }

  /**
   * Creates a new FFMpeg instance
   *
   * @param array|ConfigurationInterface $configuration
   * @param LoggerInterface              $logger
   * @param FFProbe                      $probe
   *
   * @return FFMpeg
   */
  public static function create( $configuration = [], LoggerInterface $logger = null, FFProbe $probe = null ) {
    if ( null === $probe ) {
      $probe = FFProbe::create( $configuration, $logger, null );
    }

    return new static( FFMpegDriver::create( $logger, $configuration ), $probe );
  }

  /**
   * Returns the list of supported mime types
   *
   * @return array
   */
  public static function getSupportedMimes() {
    return self::$_mimes;
  }
}