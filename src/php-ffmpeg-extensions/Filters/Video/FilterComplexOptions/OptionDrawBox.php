<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use FFMpeg\Exception\InvalidArgumentException;
use Sharapov\FFMpegExtensions\Coordinate\TimeLine;
use Sharapov\FFMpegExtensions\Media\Video;

/**
 * DrawText filter option
 * @package Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions
 */
class OptionDrawBox implements OptionInterface {
  use TimeLineTrait;
  use CoordinatesTrait;
  use DimensionsTrait;
  use ZindexTrait;

  protected $_color = '000000@0.4:t=max';

  /**
   * @var Video
   */
  protected $_video;

  /**
   * Constructor. Set box color.
   *
   * @param        $color
   * @param float $transparency
   * @param string $thickness
   */
  public function __construct($color = '000000', $transparency = 0.4, $thickness = 'max') {
    $this->setColor($color, $transparency, $thickness);
  }

  /**
   * Set video stream
   *
   * @param Video $video
   *
   * @return $this
   */
  public function setVideoStream(Video $video) {
    $this->_video = $video;

    return $this;
  }

  /**
   * Returns a command string.
   * @return string
   */
  public function __toString() {
    return $this->getCommand();
  }

  /**
   * Returns command string.
   * @return string
   */
  public function getCommand() {
    // If specific timeline is not provided for the drawtext, we must apply drawtext for the whole video
    if(!$this->_timeLine instanceof TimeLine) {
      $this->setTimeLine(new TimeLine(0, $this->getVideoStream()->getStreamDuration()));
    }

    return sprintf("[%s]drawbox=%s[%s]", ':s1', implode(":", [
        "x=" . $this->getCoordinates()->getX(),
        "y=" . $this->getCoordinates()->getY(),
        "w=" . $this->getDimensions()->getWidth(),
        "h=" . $this->getDimensions()->getHeight(),
        "color='" . $this->getColor() . "'",
        $this->getTimeLine()->getCommand()
    ]), ':s2');
  }

  /**
   * @return Video
   */
  public function getVideoStream() {
    return $this->_video;
  }

  /**
   * Get box color.
   * @return string
   */
  public function getColor() {
    return $this->_color;
  }

  /**
   * Set box color.
   *
   * @param        $color
   * @param float $transparency
   * @param string $thickness
   *
   * @return $this
   */
  public function setColor($color, $transparency = 0.4, $thickness = 'max') {
    if(!is_numeric($transparency) || $transparency < 0 || $transparency > 1) {
      throw new InvalidArgumentException('Transparency should be integer or float value from 0 to 1. ' . $transparency . ' given.');
    }

    if($thickness != 'max' and !is_int($thickness)) {
      throw new InvalidArgumentException('Thickness should be positive integer or "max". ' . $thickness . ' given.');
    }

    $color = ltrim($color, '#');
    $color = str_pad($color, 6, 0, STR_PAD_RIGHT);
    if(!preg_match('/^[a-f0-9]{6}$/i', $color)) {
      throw new InvalidArgumentException('Color should be HEX string. ' . $color . ' given.');
    }

    $this->_color = $color . '@' . $transparency . ":t=" . $thickness;

    return $this;
  }
}
