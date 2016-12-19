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

/**
 * Dimension object, used for manipulating width and height couples
 */
class Dimension
{
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
  public function __construct($width, $height)
  {
    if (($width <= 0 and $width != self::WIDTH_MAX) || ($height <= 0 and $width != self::HEIGHT_MAX)) {
      throw new InvalidArgumentException('Width and height should be positive integer or "iw", "ih"');
    }

    $this->width = $width;
    $this->height = $height;
  }

  /**
   * Returns width.
   *
   * @return integer
   */
  public function getWidth()
  {
    return $this->width;
  }

  /**
   * Returns height.
   *
   * @return integer
   */
  public function getHeight()
  {
    return $this->height;
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->getWidth() . ":" . $this->getHeight();
  }
}
