<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters;

interface ExtraInputStreamInterface
{
  public function getExtraInputStreams();
  public function setExtraInputStream($input);
}
