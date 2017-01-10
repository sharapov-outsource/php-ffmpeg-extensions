<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use FFMpeg\Exception\InvalidArgumentException;

trait ZindexTrait
{
  protected $_zIndex = null;

  /**
   * Returns Z-Index coordinate.
   *
   * @return mixed
   */
  public function getZIndex()
  {
    return $this->_zIndex;
  }

  /**
   * Set Z-Index coordinate.
   *
   * The z-index property specifies the stack order of an element.
   * An element with greater stack order is always in front of an element with a lower stack order.
   * Should be positive integer more than 100.
   *
   * @param int $z
   *
   * @return $this
   */
  public function setZIndex($z)
  {
    if (!is_int($z) or $z < 100) {
      throw new InvalidArgumentException('Z-Index should be positive integer more than or equal 100. ' . $z . ' given.');
    }

    $this->_zIndex = $z;

    return $this;
  }
}