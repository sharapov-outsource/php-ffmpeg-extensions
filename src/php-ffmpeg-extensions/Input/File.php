<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Input;

use FFMpeg\Exception\InvalidArgumentException;
use Sharapov\FFMpegExtensions\FFMpeg;

/**
 * Class File
 * @package Sharapov\FFMpegExtensions\Stream
 */
class File implements FileInterface {
  protected $_filePath;
  protected $_mimes;

  /**
   * File constructor.
   *
   * @param null $file
   */
  public function __construct( $file = null ) {
    $this->_mimes = FFMpeg::getSupportedMimes();
    if ( ! is_null( $file ) ) {
      $this->setPath( $file );
    }
  }

  /**
   * Sets a file
   *
   * @param $file
   *
   * @return $this
   * @throws InvalidArgumentException
   */
  public function setPath( $file ) {
    if ( ! file_exists( $file ) or ! is_file( $file ) ) {
      throw new InvalidArgumentException( sprintf( 'Incorrect file specified. %s given.', $file ) );
    }

    if ( ! $mime = mime_content_type( $file ) or ! in_array( $mime, $this->_mimes ) ) {
      throw new InvalidArgumentException( sprintf( 'File type is not supported. %s given. Run FFMpeg::getSupportedMimes() to find the list of supported mimes.', $mime ) );
    }

    $this->_filePath = $file;

    return $this;
  }

  /**
   * Gets a file path
   *
   * @return mixed
   */
  public function getPath() {
    return $this->_filePath;
  }
}