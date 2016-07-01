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

  public function setFontFile($file)
  {
    if (!file_exists($file)) {
      throw new InvalidArgumentException('Incorrect font path.');
    }
    $this->fontFile = $file;
    return $this;
  }

  public function getFontFile()
  {
    return $this->fontFile;
  }

  public function setFontSize($size)
  {
    $this->fontSize = (int)$size;
    return $this;
  }

  public function getFontSize()
  {
    return $this->fontSize;
  }

  public function setOverlayText($text)
  {
    $this->overlayText = $text;
    return $this;
  }

  public function getOverlayText()
  {
    return $this->overlayText;
  }

  public function setFontColor($color, $transparent = 1)
  {
    if ($transparent > 1 || $transparent < 0) {
      throw new InvalidArgumentException('Invalid value of transparent. Should be integer or float value from 0 to 1');
    }

    $this->fontColor = $color . '@' . $transparent;
    return $this;
  }

  public function getFontColor()
  {
    return $this->fontColor;
  }

  public function setCoordinates(Point $point)
  {
    $this->coordinates = $point;
    return $this;
  }

  public function getCoordinates()
  {
    return $this->coordinates;
  }

  public function setTimeLine(TimeLine $timeLine)
  {
    $this->timeLine = $timeLine;
    return $this;
  }

  public function getTimeLine()
  {
    return $this->timeLine;
  }

  public function setTextShadow($color, $x = 2, $y = 2, $transparent = 1)
  {
    if ($transparent > 1 || $transparent < 0) {
      throw new InvalidArgumentException('Invalid value of transparent. Should be integer or float value from 0 to 1');
    }

    if ( ! is_numeric($x) or ! is_numeric($y)) {
      throw new InvalidArgumentException('Shadow X and Y should be either positive or negative values');
    }

    $this->textShadow = array(
        "shadowcolor='" . $color . "'@" . $transparent,
        "shadowx=" . $x,
        "shadowy=" . $y
    );
    return $this;
  }

  public function getTextShadow()
  {
    return $this->textShadow;
  }

  public function setTextBorder($color, $border = 2, $transparent = 1)
  {
    if ($transparent > 1 || $transparent < 0) {
      throw new InvalidArgumentException('Invalid value of transparent. Should be integer or float value from 0 to 1');
    }

    if ( ! is_integer($border)) {
      throw new InvalidArgumentException('Border width should be positive integer');
    }

    $this->textBorder = array(
        "bordercolor='" . $color . "'@" . $transparent,
        "borderw=" . $border
    );
    return $this;
  }

  public function getTextBorder()
  {
    return $this->textBorder;
  }

  public function setBoundingBox($color, $border = 10, $transparent = 1)
  {
    if ($transparent > 1 || $transparent < 0) {
      throw new InvalidArgumentException('Invalid value of transparent. Should be integer or float value from 0 to 1');
    }

    if ( ! is_integer($border)) {
      throw new InvalidArgumentException('Border width should be positive integer');
    }

    $this->boundingBox = array(
        "boxcolor='" . $color . "'@" . $transparent,
        "boxborderw=" . $border
    );
    return $this;
  }

  public function getBoundingBox()
  {
    return $this->boundingBox;
  }

  public function getStringParameters()
  {
    $params = array("drawtext=");

    if ($this->timeLine instanceof TimeLine) {
      $params[] = "enable='between(t," . $this->getTimeLine()->getStartTime() . "," . $this->getTimeLine()->getEndTime() . ")'";
    }

    $params[] = "fontfile=" . $this->getFontFile();
    $params[] = "text='" . $this->getOverlayText() . "'";
    $params[] = "fontcolor='" . $this->getFontColor() . "'";
    $params[] = "fontsize=" . $this->getFontSize();
    $params[] = "x=" . $this->getCoordinates()->getX();
    $params[] = "y=" . $this->getCoordinates()->getY();

    // Bounding box
    if ($this->boundingBox != null) {
      $params[] = "box=1:".implode(":", $this->boundingBox);
    }

    // Text shadow
    if ($this->textShadow != null) {
      $params[] = implode(":", $this->textShadow);
    }

    // Text border
    if ($this->textBorder != null) {
      $params[] = implode(":", $this->textBorder);
    }

    return implode(":", $params);
  }

  public function __toString()
  {
    return $this->getStringParameters();
  }
}
