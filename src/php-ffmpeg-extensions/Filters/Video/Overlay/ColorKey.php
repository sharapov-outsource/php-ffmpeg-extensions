<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\Overlay;

use Sharapov\FFMpegExtensions\Coordinate\Point;
use FFMpeg\Exception\InvalidArgumentException;
use Sharapov\FFMpegExtensions\Coordinate\Dimension;
use Sharapov\FFMpegExtensions\Coordinate\TimeLine;

class ColorKey implements OverlayInterface
{
  protected $imageFile;

  protected $colorKey = '0x3BBD1E:0.3:0.2';

  protected $dimensions;

  /**
   * RGB colorspace color keying.
   *
   * Accepts the following options:
   * $color - The color which will be replaced with transparency.
   * $similarity - Similarity percentage with the key color.
   *               0.01 matches only the exact key color, while 1.0 matches everything.
   * $blend - Blend percentage.
   *          0.0 makes pixels either fully transparent, or not transparent at all.
   *
   * Higher values result in semi-transparent pixels, with a higher transparency the more similar
   * the pixels color is to the key color.
   *
   * @param $color
   * @param string $similarity
   * @param string $blend
   * @return $this
   */
  public function setColor($color, $similarity = '0.3', $blend = '0.2')
  {
    if ($similarity > 1 || $similarity < 0) {
      throw new InvalidArgumentException('Invalid value of similarity. Should be integer or float value from 0 to 1');
    }

    if ($blend > 1 || $blend < 0) {
      throw new InvalidArgumentException('Invalid value of similarity. Should be integer or float value from 0 to 1');
    }

    $this->colorKey = '0x' . $color . ':' . $similarity . ':' . $blend;
    return $this;
  }

  /**
   * Get RGB colorspace
   * @return string
   */
  public function getColor()
  {
    return $this->colorKey;
  }

  /**
   * Set background image file
   * @param $file
   * @return $this
   */
  public function setImageFile($file)
  {
    if (!file_exists($file)) {
      throw new InvalidArgumentException('Incorrect image path.');
    }
    $this->imageFile = $file;
    return $this;
  }

  /**
   * Get background image file
   * @return string
   */
  public function getImageFile()
  {
    return $this->imageFile;
  }

  /**
   * Set background dimensions
   * @param Dimension $dimension
   * @return $this
   */
  public function setDimensions(Dimension $dimension)
  {
    $this->dimensions = $dimension;
    return $this;
  }

  /**
   * Get background dimensions
   * @return Dimension
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }

  /**
   * Get string command
   * @return string
   */
  public function getCommand()
  {
    return '';
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->getCommand();
  }
}
