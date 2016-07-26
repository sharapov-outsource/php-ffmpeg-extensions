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

class Text implements OverlayInterface
{
  protected $fontFile;

  protected $fontSize = 20;

  protected $fontColor = '#000000';

  protected $overlayText = 'Default text';

  protected $coordinates;

  protected $timeLine;

  protected $boundingBox;

  protected $textShadow;

  protected $textBorder;

  /**
   * Set path to font file.
   * @param $file
   * @return $this
   */
  public function setFontFile($file)
  {
    if (!file_exists($file)) {
      throw new InvalidArgumentException('Incorrect font path.');
    }
    $this->fontFile = $file;
    return $this;
  }

  /**
   * Get path.
   * @return mixed
   */
  public function getFontFile()
  {
    return $this->fontFile;
  }

  /**
   * Set font size.
   * @param $size
   * @return $this
   */
  public function setFontSize($size)
  {
    $this->fontSize = (int)$size;
    return $this;
  }

  /**
   * Get font size.
   * @return int
   */
  public function getFontSize()
  {
    return $this->fontSize;
  }

  /**
   * Set text to be overlapped.
   * @param $text
   * @return $this
   */
  public function setOverlayText($text)
  {
    $this->overlayText = $text;
    return $this;
  }

  /**
   * Get text.
   * @return string
   */
  public function getOverlayText()
  {
    return $this->overlayText;
  }

  /**
   * Set font color.
   * @param $color
   * @param int $transparent
   * @return $this
   */
  public function setFontColor($color, $transparent = 1)
  {
    if ($transparent > 1 || $transparent < 0) {
      throw new InvalidArgumentException('Invalid value of transparent. Should be integer or float value from 0 to 1');
    }

    $this->fontColor = $color . '@' . $transparent;
    return $this;
  }

  /**
   * Get font color.
   * @return string
   */
  public function getFontColor()
  {
    return $this->fontColor;
  }

  /**
   * Set coordinates object.
   * @param \Sharapov\FFMpegExtensions\Coordinate\Point $point
   * @return $this
   */
  public function setCoordinates(\Sharapov\FFMpegExtensions\Coordinate\Point $point)
  {
    $this->coordinates = $point;
    return $this;
  }

  /**
   * Return coordinates object.
   * @return mixed
   */
  public function getCoordinates()
  {
    return $this->coordinates;
  }

  /**
   * Set timeline object.
   * @param \Sharapov\FFMpegExtensions\Coordinate\TimeLine $timeLine
   * @return $this
   */
  public function setTimeLine(\Sharapov\FFMpegExtensions\Coordinate\TimeLine $timeLine)
  {
    $this->timeLine = $timeLine;
    return $this;
  }

  /**
   * Return timeline object
   * @return mixed
   */
  public function getTimeLine()
  {
    return $this->timeLine;
  }

  /**
   * The color to be used for drawing a shadow behind the drawn text.
   * The x and y offsets for the text shadow position with respect to the position of the text. They can be either positive or negative values.
   * @param $color
   * @param int $x
   * @param int $y
   * @param int $transparent
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

    $this->textShadow = array(
        "shadowcolor='" . $color . "'@" . $transparent,
        "shadowx=" . $x,
        "shadowy=" . $y
    );
    return $this;
  }

  /**
   * Return text shadow value.
   * @return mixed
   */
  public function getTextShadow()
  {
    return $this->textShadow;
  }

  /**
   * Set the color to be used for drawing border around text.
   * @param $color
   * @param int $border
   * @param int $transparent
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

    $this->textBorder = array(
        "bordercolor='" . $color . "'@" . $transparent,
        "borderw=" . $border
    );
    return $this;
  }

  /**
   * Return text border value.
   * @return mixed
   */
  public function getTextBorder()
  {
    return $this->textBorder;
  }

  /**
   * The color to be used for drawing box around text.
   * @param $color
   * @param int $border
   * @param int $transparent
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

    $this->boundingBox = array(
        "boxcolor='" . $color . "'@" . $transparent,
        "boxborderw=" . $border
    );
    return $this;
  }

  /**
   * Return box color value.
   * @return mixed
   */
  public function getBoundingBox()
  {
    return $this->boundingBox;
  }

  /**
   * Return command string.
   * @return string
   */
  public function getCommand()
  {
    $filterOptions = array(
        "fontfile=" . $this->getFontFile(),
        "text='" . $this->getOverlayText() . "'",
        "fontcolor='" . $this->getFontColor() . "'",
        "fontsize=" . $this->getFontSize(),
        "x=" . $this->getCoordinates()->getX(),
        "y=" . $this->getCoordinates()->getY()
    );

    if ($this->timeLine instanceof \Sharapov\FFMpegExtensions\Coordinate\TimeLine) {
      $filterOptions[] = $this->timeLine->getCommand();
    }

    // Bounding box
    if ($this->boundingBox != null) {
      $filterOptions[] = "box=1:" . implode(":", $this->boundingBox);
    }

    // Text shadow
    if ($this->textShadow != null) {
      $filterOptions[] = implode(":", $this->textShadow);
    }

    // Text border
    if ($this->textBorder != null) {
      $filterOptions[] = implode(":", $this->textBorder);
    }

    return "drawtext=" . implode(":", $filterOptions);
  }

  /**
   * Return command string.
   * @return string
   */
  public function __toString()
  {
    return $this->getCommand();
  }

  /**
   * Not implemented for this class.
   * @throws InvalidArgumentException
   */
  public function getImageFile()
  {
    throw new InvalidArgumentException('Method getImageFile() is not implemented for this class');
  }

  /**
   * Not implemented for this class.
   * @throws InvalidArgumentException
   */
  public function setImageFile($file)
  {
    throw new InvalidArgumentException('Method setImageFile($file) is not implemented for this class');
  }
}
