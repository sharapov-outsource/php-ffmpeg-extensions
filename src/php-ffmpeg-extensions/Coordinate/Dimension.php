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

  private $width;
  private $height;

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

  /*
  public static function calculateBoxHeightByFontSize($size, $padding = 5)
  {
      return $size + ($padding * 2);
  }

  public static function calculateBoxYByHeight($height, $y)
  {
      return $y - ($height / 5);
  }*/

  public static function calculateBoundingBoxDimensions($fontSize, $fontFile, $text)
  {
    if (!file_exists($fontFile)) {
      throw new InvalidArgumentException('Incorrect font path.');
    }

    if (!$dimensions = imagettfbbox($fontSize, 0, $fontFile, $text)) {
      throw new InvalidArgumentException('Could not calculate box dimensions');
    }

    return new Dimension((abs($dimensions[2]) - 82), (abs($dimensions[5]) + 5));
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
}
