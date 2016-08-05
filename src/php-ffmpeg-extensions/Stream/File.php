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
  protected $_metaData;
  protected $_file;

  public function __construct($file = null, $title = null)
  {
    $this->_metaData = new MetaData();
    if (!is_null($file)) {
      $this->setFile($file);
    }

    if (!is_null($title)) {
      $this->setTitle($title);
    }
  }

  public function setTitle($title)
  {
    $this->_metaData->add('title', $title);

    return $this;
  }

  public function getTitle()
  {
    return $this->_metaData->get('title');
  }

  public function getMetadata()
  {
    return $this->_metaData->get();
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