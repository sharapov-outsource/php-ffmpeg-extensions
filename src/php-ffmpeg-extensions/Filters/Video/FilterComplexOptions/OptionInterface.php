<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

interface OptionInterface {
  public function getCommand();

  public function getZIndex();

  public function __toString();
}
