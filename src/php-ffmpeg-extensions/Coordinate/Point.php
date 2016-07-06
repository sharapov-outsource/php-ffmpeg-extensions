<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Coordinate;

use FFMpeg\Exception\InvalidArgumentException;

class Point
{
  protected $x;
  protected $y;

  /**
   * Point constructor.
   * @param integer $x
   * @param integer $y
   */
  public function __construct($x, $y)
  {
    if ( ! is_integer($x) || ! is_integer($y)) {
      throw new InvalidArgumentException('X and Y should be positive integer');
    }
    $this->x = $x;
    $this->y = $y;
  }

  /**
   * @return integer
   */
  public function getX()
  {
    return $this->x;
  }

  /**
   * @return integer
   */
  public function getY()
  {
    return $this->y;
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->getX() . ":" . $this->getY();
  }
}
