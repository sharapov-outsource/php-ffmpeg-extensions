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

class Audio extends \FFMpeg\Media\Audio
{
  private $_file;

  public function __construct(FileInterface $file, FFMpegDriver $driver, FFProbe $ffprobe)
  {
    $this->_file = $file;
    parent::__construct($file->getPath(), $driver, $ffprobe);
  }
}
