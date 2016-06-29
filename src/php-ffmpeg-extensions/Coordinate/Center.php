<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Coordinate;

class Center extends Point
{
  const AUTO_VERTICAL = '(h-th)/2';
  const AUTO_HORIZONTAL = '(w-tw)/2';

  protected $x;
  protected $y;

  /**
   * Center constructor.
   * @param integer $x
   * @param integer $y
   */
  public function __construct($x = null, $y = null)
  {
    if (is_integer($x)) {
      $this->x = $x;
    } else {
      $this->x = self::AUTO_HORIZONTAL;
    }
    if (is_integer($y)) {
      $this->y = $y;
    } else {
      $this->y = self::AUTO_VERTICAL;
    }
  }
}
