<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use Sharapov\FFMpegExtensions\Coordinate\Point;
use Sharapov\FFMpegExtensions\Coordinate\TimeLine;
use Sharapov\FFMpegExtensions\Input\FileInterface;
use FFMpeg\Exception\InvalidArgumentException;

/**
 * Chromakey filter option
 * @package Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions
 */
class OptionChromakey
    implements
    OptionInterface,
    OptionExtraInputStreamInterface
{
  use TimeLineTrait;
  use DimensionsTrait;
  use ProbeTrait;
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
    // [1:v]colorkey=0x<color>:<similarity>:<blend>[ckout];[0:v][ckout]overlay

    /*
     * $filterOptions[] = sprintf('[0:v]colorkey=%s[sck]', $this->_colorKeyFilter->getColor());
        // Color key background input is always the first stream
        $filterOptions[] = sprintf('[1:v]scale=%s[out1]', $this->_colorKeyFilter->getDimensions());
        $filterOptions[] = sprintf('[out1][sck]overlay%s', ((count($this->_imageOverlay) > 0 or count($this->_textOverlay) > 0 or $this->_amerge != null) ? '[out2]' : ''));
     */
    /*
        $cmd = sprintf('', ':s1', ':s2');

        if ($this->getCoordinates() instanceof Point) {
          $cmd .= sprintf("%s", (string)$this->getCoordinates());
        } else {
          $cmd .= '0:0';
        }

        if ($this->getTimeLine() instanceof TimeLine) {
          $cmd .= sprintf(":%s", (string)$this->getTimeLine());
        }
    */
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
