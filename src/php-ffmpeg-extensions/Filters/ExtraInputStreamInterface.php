<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters;

use Sharapov\FFMpegExtensions\Input\FileInterface;

interface ExtraInputStreamInterface
{
  public function setExtraInputStream(FileInterface $file);
  public function getExtraInputStream();
}
