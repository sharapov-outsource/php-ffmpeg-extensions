<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use FFMpeg\Exception\InvalidArgumentException;
use Sharapov\FFMpegExtensions\Coordinate\TimeLine;
use Sharapov\FFMpegExtensions\Input\FileInterface;
use Sharapov\FFMpegExtensions\Media\Video;

/**
 * DrawText filter option
 * @package Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions
 */
class OptionDrawText implements OptionInterface {
  use TimeLineTrait;
  use FadeInOutTrait;
  use CoordinatesTrait;
  use ZindexTrait;

  protected $_fontFile;

  protected $_fontSize = 20;

  protected $_fontColor = '#000000';

  protected $_text = 'Default text';

  protected $_boundingBox;

  protected $_textShadow;

  protected $_textBorder;

  protected $_escapeSymbols = [
      ':' => '\:'
  ];

  /**
   * @var Video
   */
  protected $_video;

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
   * Get pairs to escape.
   * @return array
   */
  public function getEscapeSymbols() {
    return $this->_escapeSymbols;
  }

  /**
   * Set pairs to escape.
   *
   * @param array $pairs
   *
   * @return $this
   */
  public function setEscapeSymbols(array $pairs) {
    $this->_escapeSymbols = $pairs;

    return $this;
  }

  /**
   * Returns text shadow value.
   * @return mixed
   */
  public function getTextShadow() {
    return $this->_textShadow;
  }

  /**
   * The color to be used for drawing a shadow behind the drawn text.
   * The x and y offsets for the text shadow position with respect to the position of the text. They can be either
   * positive or negative values.
   *
   * @param     $color
   * @param int $x
   * @param int $y
   * @param int $transparency
   *
   * @return $this
   */
  public function setTextShadow($color, $x = 0, $y = 0, $transparency = 1) {
    if(!is_numeric($transparency) || $transparency < 0 || $transparency > 1) {
      throw new InvalidArgumentException('Transparency should be integer or float value from 0 to 1. ' . $transparency . ' given.');
    }

    if(!is_numeric($x) or !is_numeric($y)) {
      throw new InvalidArgumentException('Shadow X and Y should be either positive or negative values. ' . $x . ', ' . $y . ' given.');
    }

    $this->_textShadow = [
        "shadowcolor='" . $color . "'@" . $transparency,
        "shadowx=" . $x,
        "shadowy=" . $y
    ];

    return $this;
  }

  /**
   * Returns text border value.
   * @return mixed
   */
  public function getTextBorder() {
    return $this->_textBorder;
  }

  /**
   * Set the color to be used for drawing border around text.
   *
   * @param     $color
   * @param int $border
   * @param int $transparency
   *
   * @return $this
   */
  public function setTextBorder($color, $border = 2, $transparency = 1) {
    if(!is_numeric($transparency) || $transparency < 0 || $transparency > 1) {
      throw new InvalidArgumentException('Transparency should be integer or float value from 0 to 1. ' . $transparency . ' given.');
    }

    if(!is_integer($border)) {
      throw new InvalidArgumentException('Border should be positive integer. ' . $border . ' given.');
    }

    $this->_textBorder = [
        "bordercolor='" . $color . "'@" . $transparency,
        "borderw=" . $border
    ];

    return $this;
  }

  /**
   * Returns box color value.
   * @return mixed
   */
  public function getBoundingBox() {
    return $this->_boundingBox;
  }

  /**
   * The color to be used for drawing box around text.
   *
   * @param     $color
   * @param int $border
   * @param int $transparency
   *
   * @return $this
   */
  public function setBoundingBox($color, $border = 10, $transparency = 1) {
    if(!is_numeric($transparency) || $transparency < 0 || $transparency > 1) {
      throw new InvalidArgumentException('Transparency should be integer or float value from 0 to 1. ' . $transparency . ' given.');
    }

    if(!is_integer($border)) {
      throw new InvalidArgumentException('Border should be positive integer. ' . $border . ' given.');
    }

    $color = ltrim($color, '#');
    $color = str_pad($color, 6, 0, STR_PAD_RIGHT);
    if(!preg_match('/^[a-f0-9]{6}$/i', $color)) {
      throw new InvalidArgumentException('Color should be HEX string. ' . $color . ' given.');
    }

    $this->_boundingBox = [
        "boxcolor='" . $color . "'@" . $transparency,
        "boxborderw=" . $border
    ];

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
    $options = [
        "fontfile=" . $this->getFontFile()->getPath(),
        "text='" . $this->_escapeSymbols() . "'",
        "fontcolor='" . $this->getFontColor() . "'",
        "fontsize=" . $this->getFontSize(),
        "x=" . $this->getCoordinates()->getX(),
        "y=" . $this->getCoordinates()->getY()
    ];

    if($this->_fadeInSeconds == null) {
      $this->_fadeInSeconds = 0;
    }

    if($this->_fadeOutSeconds == null) {
      $this->_fadeOutSeconds = 0;
    }

    // If specific timeline is not provided for the drawtext, we must apply drawtext for the whole video
    if(!$this->_timeLine instanceof TimeLine) {
      $this->setTimeLine(new TimeLine(0, $this->getVideoStream()->getStreamDuration()));
    }

    /**
     * FadeIn and Out
     * t1 - fade in start time (in seconds)
     * t2 - fade in length (in seconds)
     * t3 - time to keep fully opaque (in seconds)
     * t4 - fade out length (in seconds)
     * More manuals
     * @link http://ffmpeg.shanewhite.co/
     * @link http://noizeramp.com/2016/10/19/ffmpeg-fade-in-out-expressions/
     */
    $fadeTime = sprintf(":alpha='if(lt(t,%s),0,if(lt(t,%s),(t-%s)/%s,if(lt(t,%s),1,if(lt(t,%s),(%s-(t-%s))/%s,0))))'", $this->getTimeLine()->getStartTime(), ($this->getTimeLine()->getStartTime() + $this->_fadeInSeconds), $this->getTimeLine()->getStartTime(), $this->_fadeInSeconds, ($this->getTimeLine()->getStartTime() + $this->_fadeInSeconds + $this->getTimeLine()->getEndTime()), ($this->getTimeLine()->getStartTime() + $this->_fadeInSeconds + $this->getTimeLine()->getEndTime() + $this->_fadeOutSeconds), $this->_fadeOutSeconds, ($this->getTimeLine()->getStartTime() + $this->_fadeInSeconds + $this->getTimeLine()->getEndTime()), $this->_fadeOutSeconds);

    // Bounding box
    if($this->_boundingBox != null) {
      $options[] = "box=1:" . implode(":", $this->_boundingBox);
    }

    // Text shadow
    if($this->_textShadow != null) {
      $options[] = implode(":", $this->_textShadow);
    }

    // Text border
    if($this->_textBorder != null) {
      $options[] = implode(":", $this->_textBorder);
    }

    return sprintf("[%s]drawtext=%s%s[%s]", ':s1', implode(":", $options), $fadeTime, ':s2');
  }

  /**
   * Get path.
   * @return mixed
   */
  public function getFontFile() {
    if(!$this->_fontFile instanceof FileInterface) {
      throw new InvalidArgumentException('Font is undefined.');
    }

    return $this->_fontFile;
  }

  /**
   * Set path to font file.
   *
   * @param $file
   *
   * @return $this
   */
  public function setFontFile(FileInterface $file) {
    $this->_fontFile = $file;

    return $this;
  }

  /**
   * Replaces special symbols.
   * @return string
   */
  private function _escapeSymbols() {
    return strtr($this->getText(), $this->_escapeSymbols);
  }

  /**
   * Get text.
   * @return string
   */
  public function getText() {
    return $this->_text;
  }

  /**
   * Set text to be overlapped.
   *
   * @param $text
   *
   * @return $this
   */
  public function setText($text) {
    $this->_text = $text;

    return $this;
  }

  /**
   * Get font color.
   * @return string
   */
  public function getFontColor() {
    return $this->_fontColor;
  }

  /**
   * Set font color.
   *
   * @param     $color
   * @param int $transparency
   *
   * @return $this
   */
  public function setFontColor($color, $transparency = 1) {
    if(!is_numeric($transparency) || $transparency < 0 || $transparency > 1) {
      throw new InvalidArgumentException('Transparency should be integer or float value from 0 to 1. ' . $transparency . ' given.');
    }

    $color = ltrim($color, '#');
    $color = str_pad($color, 6, 0, STR_PAD_RIGHT);
    if(!preg_match('/^[a-f0-9]{6}$/i', $color)) {
      throw new InvalidArgumentException('Color should be HEX string. ' . $color . ' given.');
    }

    $this->_fontColor = $color . '@' . $transparency;

    return $this;
  }

  /**
   * Get font size.
   * @return int
   */
  public function getFontSize() {
    return $this->_fontSize;
  }

  /**
   * Set font size.
   *
   * @param $size
   *
   * @return $this
   */
  public function setFontSize($size) {
    $this->_fontSize = (int)$size;

    return $this;
  }

  /**
   * @return Video
   */
  public function getVideoStream() {
    return $this->_video;
  }
}
