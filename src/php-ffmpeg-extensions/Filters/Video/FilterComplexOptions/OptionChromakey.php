<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use Sharapov\FFMpegExtensions\Filters\ExtraInputStreamInterface;
use Sharapov\FFMpegExtensions\Filters\ExtraInputStreamTrait;
use FFMpeg\Exception\InvalidArgumentException;

/**
 * Chromakey filter option
 * @package Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions
 */
class OptionChromakey
    implements
    OptionInterface,
    ExtraInputStreamInterface
{
  use TimeLineTrait;
  use DimensionsTrait;
  use ExtraInputStreamTrait;

  protected $_color = '0x3BBD1E:0.6:0.3';

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
    if (!is_numeric($similarity) || $similarity < 0 || $similarity > 1) {
      throw new InvalidArgumentException('Similarity should be integer or float value from 0 to 1. ' . $similarity . ' given.');
    }

    if (!is_numeric($blend) || $blend < 0 || $blend > 1) {
      throw new InvalidArgumentException('Blend should be integer or float value from 0 to 1. ' . $blend . ' given.');
    }

    $color = ltrim($color, '#');
    $color = str_pad($color, 6, 0, STR_PAD_RIGHT);
    if (!preg_match('/^[a-f0-9]{6}$/i', $color)) {
      throw new InvalidArgumentException('Invalid value of color. Should be hex color string.');
    }

    $this->_color = '0x' . $color . ':' . $similarity . ':' . $blend;

    return $this;
  }

  /**
   * Get RGB colorspace.
   * @return string
   */
  public function getColor()
  {
    return $this->_color;
  }

  /**
   * Returns command string.
   *
   * @return string
   */
  public function getCommand()
  {
    return sprintf("[%s]chromakey=%s[chromky];[%s]scale=%s[bg],[bg][chromky]overlay[%s]", ':s1', $this->getColor(), ':s2', (string)$this->getDimensions(), ':s3');
  }

  /**
   * Returns a command string.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->getCommand();
  }

  /**
   * Z-Index of chromakey option is always 0.
   *
   * @return int
   */
  public function getZIndex()
  {
    return 0;
  }
}
