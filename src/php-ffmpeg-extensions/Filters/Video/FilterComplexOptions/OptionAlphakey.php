<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use Sharapov\FFMpegExtensions\Filters\ExtraInputStreamInterface;
use Sharapov\FFMpegExtensions\Filters\ExtraInputStreamTrait;

/**
 * Alpha layer filter option
 * @package Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions
 */
class OptionAlphakey
  implements
  OptionInterface,
  ExtraInputStreamInterface {
  use DimensionsTrait;
  use ExtraInputStreamTrait;
  use ZindexTrait;

  /**
   * Returns command string.
   *
   * @return string
   */
  public function getCommand() {
    return sprintf( "[%s]scale=%s[abg],[%s][abg]overlay[%s]", ':s1', (string) $this->getDimensions(), ':s2', ':s3' );
  }

  /**
   * Returns a command string.
   *
   * @return string
   */
  public function __toString() {
    return $this->getCommand();
  }
}