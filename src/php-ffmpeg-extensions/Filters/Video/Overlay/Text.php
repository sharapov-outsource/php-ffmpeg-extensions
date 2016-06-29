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

  public function setBoundingBox(Box $box)
  {
    $this->boundingBox = $box;
    return $box;
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
    if ($this->boundingBox instanceof Box) {
      $this
          ->boundingBox
          ->setTimeLine($this->getTimeLine());

      // Set bounding box coordinates
      // Make sure we have 5px padding around the text
      $this
          ->boundingBox
          ->setCoordinates(
              new \FFMpeg\Coordinate\Point(
                  $this
                      ->getCoordinates()
                      ->getX() - 5,
                  $this
                      ->getCoordinates()
                      ->getY() - 5
              )
          );
      // Set bounding box dimensions
      $this
          ->boundingBox
          ->setDimensions(
              Dimension::calculateBoundingBoxDimensions(
                  $this->getFontSize(),
                  $this->getFontFile(),
                  $this->getOverlayText()
              )
          );
      return $this->boundingBox->getStringParameters() . ',' . implode(":", $params);
    }

    /*
     * if($this->box instanceof Box) {
      $this->box->setTimeLine($this->getTimeLine());

      $height = Dimension::calculateBoxHeightByFontSize($this->getFontSize());
      $y = Dimension::calculateBoxYByHeight($height, $this->getCoordinates()->getY());

      $this->box->setCoordinates(new \FFMpeg\Coordinate\Point(0, $y));
      // Set box dimensions, full width and auto height depending on font size
      $this->box->setDimensions(new Dimension(Dimension::WIDTH_MAX, $height));
      return $this->box->getStringParameters().','.implode(":", $params);
    }
     */

    return implode(":", $params);
  }

  public function __toString()
  {
    return $this->getStringParameters();
  }
}
