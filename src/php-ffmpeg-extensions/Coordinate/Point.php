<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 */

namespace Sharapov\FFMpegExtensions\Coordinate;

use FFMpeg\Exception\InvalidArgumentException;

class Point {
  const AUTO_VERTICAL = '(h-th)/2';
  const AUTO_HORIZONTAL = '(w-tw)/2';

  protected $x;
  protected $y;

  /**
   * Point constructor.
   *
   * @param mixed $x
   * @param mixed $y
   */
  public function __construct($x = self::AUTO_HORIZONTAL, $y = self::AUTO_VERTICAL) {
    if($x != self::AUTO_HORIZONTAL and !is_int($x)) {
      throw new InvalidArgumentException('X should be positive integer or "' . self::AUTO_HORIZONTAL . '". ' . $x . ' given.');
    }
    if($y != self::AUTO_VERTICAL and !is_int($y)) {
      throw new InvalidArgumentException('Y should be positive integer or "' . self::AUTO_VERTICAL . '". ' . $y . ' given.');
    }

    $this->x = $x;
    $this->y = $y;
  }

  /**
   * @return string
   */
  public function __toString() {
    return sprintf("%s:%s", $this->getX(), $this->getY());
  }

  /**
   * @return integer
   */
  public function getX() {
    return $this->x;
  }

  /**
   * @return integer
   */
  public function getY() {
    return $this->y;
  }
}
