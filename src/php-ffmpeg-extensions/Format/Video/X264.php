<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Format\Video;

use FFMpeg\Exception\InvalidArgumentException;

/**
 * The X264 video format. Allows to change the number of passes.
 */
class X264 extends \FFMpeg\Format\Video\X264
{
  public $passes = 2;

  /**
   * {@inheritDoc}
   */
  public function setPasses($passes)
  {
    if( ! is_integer($passes)) {
      throw new InvalidArgumentException('The number of passes should be positive integer');
    }
    $this->passes = (int)$passes;
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getPasses()
  {
    return $this->passes;
  }
}