<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\Overlay;

use FFMpeg\Exception\InvalidArgumentException;

/**
 * Class ColorKey
 * @package Sharapov\FFMpegExtensions\Filters\Video\Overlay
 */
class ColorKey implements OverlayInterface
{
  protected $_imageFile;

  protected $_videoFile;

  protected $_colorKey = '0x3BBD1E:0.6:0.3';

  protected $_dimensions;

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
   * @param       $color
   * @param float $similarity
   * @param float $blend
   *
   * @return $this
   */
  public function setColor($color, $similarity = 0.6, $blend = 0.3)
  {
    if ($similarity > 1 || $similarity < 0) {
      throw new InvalidArgumentException('Invalid value of similarity. Should be integer or float value from 0 to 1.');
    }

    if ($blend > 1 || $blend < 0) {
      throw new InvalidArgumentException('Invalid value of blend. Should be integer or float value from 0 to 1.');
    }

    $color = ltrim($color, '#');
    $color = str_pad($color, 6, 0, STR_PAD_RIGHT);
    if (!preg_match('/^[a-f0-9]{6}$/i', $color)) {
      throw new InvalidArgumentException('Invalid value of color. Should be hex color string.');
    }

    $this->_colorKey = '0x' . $color . ':' . $similarity . ':' . $blend;

    return $this;
  }

  /**
   * Get RGB colorspace.
   * @return string
   */
  public function getColor()
  {
    return $this->_colorKey;
  }

  /**
   * Set background image file.
   *
   * @param \Sharapov\FFMpegExtensions\Stream\FileInterface $file
   *
   * @return $this
   */
  public function setImageFile(\Sharapov\FFMpegExtensions\Stream\FileInterface $file)
  {
    $this->_imageFile = $file;

    return $this;
  }

  /**
   * Set background video file.
   *
   * @param \Sharapov\FFMpegExtensions\Stream\VideoFile $file
   *
   * @return $this
   */
  public function setVideoFile(\Sharapov\FFMpegExtensions\Stream\VideoFile $file)
  {
    $this->_videoFile = $file;

    return $this;
  }

  /**
   * Get background image file.
   * @return string
   */
  public function getImageFile()
  {
    return $this->_imageFile;
  }

  /**
   * Get background video file.
   * @return string
   */
  public function getVideoFile()
  {
    return $this->_videoFile;
  }

  /**
   * Set background _dimensions.
   *
   * @param \Sharapov\FFMpegExtensions\Coordinate\Dimension $dimension
   *
   * @return $this
   */
  public function setDimensions(\Sharapov\FFMpegExtensions\Coordinate\Dimension $dimension)
  {
    $this->_dimensions = $dimension;

    return $this;
  }

  /**
   * Get background _dimensions.
   * @return \Sharapov\FFMpegExtensions\Coordinate\Dimension
   */
  public function getDimensions()
  {
    return $this->_dimensions;
  }

  /**
   * Return command string.
   * @return string
   */
  public function getCommand()
  {
    return '';
  }

  /**
   * Return command string.
   * @return string
   */
  public function __toString()
  {
    return $this->getCommand();
  }
}
