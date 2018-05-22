<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 */

namespace Sharapov\FFMpegExtensions\Coordinate;

use FFMpeg\Exception\InvalidArgumentException;

/**
 * Dimension object, used for manipulating width and height couples
 */
class Dimension {
  const WIDTH_MAX = 'iw';
  const HEIGHT_MAX = 'ih';

  protected $width;
  protected $height;

  /**
   * @param integer $width
   * @param integer $height
   *
   * @throws InvalidArgumentException when one of the parameteres is invalid
   */
  public function __construct($width, $height) {
    if((!is_int($width) and $width != self::WIDTH_MAX) || (!is_int($height) and $width != self::HEIGHT_MAX)) {
      throw new InvalidArgumentException('Width and height should be positive integer or "' . self::WIDTH_MAX . '", "' . self::HEIGHT_MAX . '". ' . $width . ', ' . $height . ' given.');
    }

    $this->width = $width;
    $this->height = $height;
  }

  /**
   * @return string
   */
  public function __toString() {
    return sprintf("%s:%s", $this->getWidth(), $this->getHeight());
  }

  /**
   * Returns width.
   * @return integer
   */
  public function getWidth() {
    return $this->width;
  }

  /**
   * Returns height.
   * @return integer
   */
  public function getHeight() {
    return $this->height;
  }
}
