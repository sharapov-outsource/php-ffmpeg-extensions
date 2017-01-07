<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use Sharapov\FFMpegExtensions\Input\FileInterface;

interface OptionExtraInputStreamInterface
{
  public function setExtraInputStream(FileInterface $file);
  public function getExtraInputStream();
}
