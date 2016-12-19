<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Media;

use FFMpeg\Driver\FFMpegDriver;
use Sharapov\FFMpegExtensions\FFProbe;
use Sharapov\FFMpegExtensions\Input\FileInterface;

trait MediaTypeTrait {

  private $_file;

  public function __construct(FileInterface $file, FFMpegDriver $driver, FFProbe $ffprobe)
  {
    $this->_file = $file;
    parent::__construct($file->getPath(), $driver, $ffprobe);
  }

  public function setFile(FileInterface $file) {
    $this->_file = $file;
  }

  public function getFile() {
    return $this->_file;
  }
}