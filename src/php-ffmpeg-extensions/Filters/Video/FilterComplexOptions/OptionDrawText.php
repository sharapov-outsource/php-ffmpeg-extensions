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
use Sharapov\FFMpegExtensions\Coordinate\Point;
use Sharapov\FFMpegExtensions\Coordinate\TimeLine;
use Sharapov\FFMpegExtensions\Input\FileInterface;

/**
 * Class Text
 * @package Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions
 */
class OptionDrawText implements OptionsInterface
{
  protected $_fontFile;

  protected $_fontSize = 20;

  protected $_fontColor = '#000000';

  protected $_overlayText = 'Default text';

  protected $_coordinates;

  protected $_timeLine;

  protected $_boundingBox;

  protected $_textShadow;

  protected $_textBorder;

  /**
   * Set path to font file.
   *
   * @param $file
   *
   * @return $this
   */
  public function setFontFile(FileInterface $file)
  {
    $this->_fontFile = $file;

    return $this;
  }

  /**
   * Get path.
   * @return mixed
   */
  public function getFontFile()
  {
    return $this->_fontFile;
  }

  /**
   * Set font size.
   *
   * @param $size
   *
   * @return $this
   */
  public function setFontSize($size)
  {
    $this->_fontSize = (int)$size;

    return $this;
  }

  /**
   * Get font size.
   * @return int
   */
  public function getFontSize()
  {
    return $this->_fontSize;
  }

  /**
   * Set text to be overlapped.
   *
   * @param $text
   *
   * @return $this
   */
  public function setOverlayText($text)
  {
    $this->_overlayText = $text;

    return $this;
  }

  /**
   * Get text.
   * @return string
   */
  public function getOverlayText()
  {
    return $this->_overlayText;
  }

  /**
   * Set font color.
   *
   * @param     $color
   * @param int $transparent
   *
   * @return $this
   */
  public function setFontColor($color, $transparent = 1)
  {
    if ($transparent > 1 || $transparent < 0) {
      throw new InvalidArgumentException('Invalid value of transparent. Should be integer or float value from 0 to 1');
    }

    $this->_fontColor = $color . '@' . $transparent;

    return $this;
  }

  /**
   * Get font color.
   * @return string
   */
  public function getFontColor()
  {
    return $this->_fontColor;
  }

  /**
   * Set _coordinates object.
   *
   * @param Point $point
   *
   * @return $this
   */
  public function setCoordinates(Point $point)
  {
    $this->_coordinates = $point;

    return $this;
  }

  /**
   * Return _coordinates object.
   * @return mixed
   */
  public function getCoordinates()
  {
    return $this->_coordinates;
  }

  /**
   * Set timeline object.
   *
   * @param TimeLine $timeLine
   *
   * @return $this
   */
  public function setTimeLine(TimeLine $timeLine)
  {
    $this->_timeLine = $timeLine;

    return $this;
  }

  /**
   * Return timeline object
   * @return mixed
   */
  public function getTimeLine()
  {
    return $this->_timeLine;
  }

  /**
   * The color to be used for drawing a shadow behind the drawn text.
   * The x and y offsets for the text shadow position with respect to the position of the text. They can be either
   * positive or negative values.
   *
   * @param     $color
   * @param int $x
   * @param int $y
   * @param int $transparent
   *
   * @return $this
   */
  public function setTextShadow($color, $x = 0, $y = 0, $transparent = 1)
  {
    if ($transparent > 1 || $transparent < 0) {
      throw new InvalidArgumentException('Invalid value of transparent. Should be integer or float value from 0 to 1');
    }

    if (!is_numeric($x) or !is_numeric($y)) {
      throw new InvalidArgumentException('Shadow X and Y should be either positive or negative values');
    }

    $this->_textShadow = [
        "shadowcolor='" . $color . "'@" . $transparent,
        "shadowx=" . $x,
        "shadowy=" . $y
    ];

    return $this;
  }

  /**
   * Return text shadow value.
   * @return mixed
   */
  public function getTextShadow()
  {
    return $this->_textShadow;
  }

  /**
   * Set the color to be used for drawing border around text.
   *
   * @param     $color
   * @param int $border
   * @param int $transparent
   *
   * @return $this
   */
  public function setTextBorder($color, $border = 2, $transparent = 1)
  {
    if ($transparent > 1 || $transparent < 0) {
      throw new InvalidArgumentException('Invalid value of transparent. Should be integer or float value from 0 to 1');
    }

    if (!is_integer($border)) {
      throw new InvalidArgumentException('Border width should be positive integer');
    }

    $this->_textBorder = [
        "bordercolor='" . $color . "'@" . $transparent,
        "borderw=" . $border
    ];

    return $this;
  }

  /**
   * Return text border value.
   * @return mixed
   */
  public function getTextBorder()
  {
    return $this->_textBorder;
  }

  /**
   * The color to be used for drawing box around text.
   *
   * @param     $color
   * @param int $border
   * @param int $transparent
   *
   * @return $this
   */
  public function setBoundingBox($color, $border = 10, $transparent = 1)
  {
    if ($transparent > 1 || $transparent < 0) {
      throw new InvalidArgumentException('Invalid value of transparent. Should be integer or float value from 0 to 1');
    }

    if (!is_integer($border)) {
      throw new InvalidArgumentException('Border width should be positive integer');
    }

    $this->_boundingBox = [
        "boxcolor='" . $color . "'@" . $transparent,
        "boxborderw=" . $border
    ];

    return $this;
  }

  /**
   * Return box color value.
   * @return mixed
   */
  public function getBoundingBox()
  {
    return $this->_boundingBox;
  }

  /**
   * Return command string.
   * @return string
   */
  public function getCommand()
  {
    $filterOptions = [
        "fontfile=" . $this->getFontFile()->getPath(),
        "text='" . $this->getOverlayText() . "'",
        "fontcolor='" . $this->getFontColor() . "'",
        "fontsize=" . $this->getFontSize(),
        "x=" . $this->getCoordinates()->getX(),
        "y=" . $this->getCoordinates()->getY()
    ];

    if ($this->_timeLine instanceof TimeLine) {
      $filterOptions[] = (string)$this->_timeLine;
    }

    // Bounding box
    if ($this->_boundingBox != null) {
      $filterOptions[] = "box=1:" . implode(":", $this->_boundingBox);
    }

    // Text shadow
    if ($this->_textShadow != null) {
      $filterOptions[] = implode(":", $this->_textShadow);
    }

    // Text border
    if ($this->_textBorder != null) {
      $filterOptions[] = implode(":", $this->_textBorder);
    }

    return "drawtext=" . implode(":", $filterOptions);
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
