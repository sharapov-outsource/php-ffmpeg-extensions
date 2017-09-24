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

/**
 * Colorkey filter option. Actually it's the same as OptionChromakey
 * @package Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions
 */
class OptionColorkey extends OptionChromakey
  implements
  OptionInterface,
  ExtraInputStreamInterface {
  /**
   * Returns command string.
   *
   * @return string
   */
  public function getCommand() {
    return sprintf( "[%s]colorkey=%s[clrky];[%s]scale=%s[bg],[bg][clrky]overlay[%s]", ':s1', $this->getColor(), ':s2', (string) $this->getDimensions(), ':s3' );
  }
}
