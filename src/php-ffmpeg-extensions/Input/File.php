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

/**
 * Class File
 * @package Sharapov\FFMpegExtensions\Stream
 */
class File implements FileInterface {
  protected $_filePath;
  protected $_mimes = [
    'video/quicktime',
    'video/mpeg',
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
  ];

  public function __construct( $file = null ) {
    if ( ! is_null( $file ) ) {
      $this->setPath( $file );
    }
  }

  public function setPath( $file ) {
    if ( ! file_exists( $file ) or ! is_file( $file ) ) {
      throw new InvalidArgumentException( 'Incorrect file specified' );
    }

    $this->_filePath = $file;

    return $this;
  }

  public function getPath() {
    return $this->_filePath;
  }


}