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
 * Class File
 * @package Sharapov\FFMpegExtensions\Stream
 */
class File implements FileInterface
{
  protected $_file;

  public function __construct($file = null)
  {
    if (!is_null($file)) {
      $this->setFile($file);
    }
  }

  public function setFile($file)
  {
    if (!file_exists($file)) {
      throw new InvalidArgumentException('Incorrect file path.');
    }

    $this->_file = $file;

    return $this;
  }

  public function getFile()
  {
    return $this->_file;
  }
}