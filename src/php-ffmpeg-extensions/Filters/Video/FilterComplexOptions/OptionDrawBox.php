<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use FFMpeg\Exception\InvalidArgumentException;
use Sharapov\FFMpegExtensions\Coordinate\TimeLine;

/**
 * DrawText filter option
 * @package Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions
 */
class OptionDrawBox implements OptionInterface
{
  use TimeLineTrait;
  use FadeInOutTrait;
  use CoordinatesTrait;
  use DimensionsTrait;
  use ZindexTrait;

  protected $_color = '000000@0.4:t=max';

  /**
   * Constructor. Set box color.
   *
   * @param        $color
   * @param float  $transparency
   * @param string $thickness
   */
  public function __construct($color = '000000', $transparency = 0.4, $thickness = 'max')
  {
    $this->setColor($color, $transparency, $thickness);
  }

  /**
   * Set box color.
   *
   * @param        $color
   * @param float  $transparency
   * @param string $thickness
   *
   * @return $this
   */
  public function setColor($color, $transparency = 0.4, $thickness = 'max')
  {
    if (!is_numeric($transparency) || $transparency < 0 || $transparency > 1) {
      throw new InvalidArgumentException('Transparency should be integer or float value from 0 to 1. ' . $transparency . ' given.');
    }

    if ($thickness != 'max' and !is_int($thickness)) {
      throw new InvalidArgumentException('Thickness should be positive integer or "max". ' . $thickness . ' given.');
    }

    $color = ltrim($color, '#');
    $color = str_pad($color, 6, 0, STR_PAD_RIGHT);
    if (!preg_match('/^[a-f0-9]{6}$/i', $color)) {
      throw new InvalidArgumentException('Color should be HEX string. ' . $color . ' given.');
    }

    $this->_color = $color . '@' . $transparency . ":t=" . $thickness;

    return $this;
  }

  /**
   * Get box color.
   * @return string
   */
  public function getColor()
  {
    return $this->_color;
  }

  /**
   * Returns command string.
   * @return string
   */
  public function getCommand()
  {
    $options = [
        "x=" . $this->getCoordinates()->getX(),
        "y=" . $this->getCoordinates()->getY(),
        "w=" . $this->getDimensions()->getWidth(),
        "h=" . $this->getDimensions()->getHeight(),
        "color='" . $this->getColor() . "'"
    ];

    if ($this->_timeLine instanceof TimeLine) {
      $options[] = $this->_timeLine->getCommand();
    }

    if($this->_fadeInSeconds or $this->_fadeOutSeconds) {
      $fadeTime = [];
      if($this->_fadeInSeconds) {
        $fadeTime[] = sprintf("fade=t=in:st=0:d=%s", $this->_fadeInSeconds);
      }
      if($this->_fadeOutSeconds) {
        if ($this->getTimeLine() instanceof TimeLine) {
          // We have to calculate the starting point of fade out if we have the TimeLine object
          $fadeTime[] = sprintf("fade=t=out:st=%s:d=%s", ($this->getTimeLine()->getEndTime() - $this->_fadeOutSeconds), $this->_fadeOutSeconds);
        } else {
          // Otherwise we add {VIDEO_LENGTH} tag to calculate the starting point on the next step
          $fadeTime[] = sprintf("fade=t=out:st={VIDEO_LENGTH}:d=%s", $this->_fadeOutSeconds);
        }
      }
      $fadeTime = sprintf(",%s", implode(",", $fadeTime));
    } else {
      $fadeTime = '';
    }

    return sprintf("[%s]drawbox=%s%s[%s]", ':s1', implode(":", $options), $fadeTime, ':s2');
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
}
