<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Driver;

class FFMpegDriver extends \FFMpeg\Driver\FFMpegDriver
{
  public function printCommand($command)
  {
    if (!is_array($command)) {
      $command = array($command);
    }

    return $this->factory->create($command)->getCommandLine();
  }
}