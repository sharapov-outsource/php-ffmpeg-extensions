<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\Overlay;

use Sharapov\FFMpegExtensions\Coordinate\Dimension;
use FFMpeg\Coordinate\Point;
use Sharapov\FFMpegExtensions\Coordinate\TimeLine;
use FFMpeg\Exception\InvalidArgumentException;

class Box implements OverlayInterface
{
  protected $color = 'black@0.4:t=max';

  protected $dimensions;

  protected $coordinates;

  protected $timeLine;

  public function __construct($color = 'black', $transparent = 0.4, $thickness = 'max')
  {
    return $this->setColor($color, $transparent, $thickness);
  }

  public function setColor($color, $transparent = 0.4, $thickness = 'max')
  {
    if ($transparent > 1 || $transparent < 0) {
      throw new InvalidArgumentException('Invalid value of transparent. Should be integer or float value from 0 to 1');
    }

    if ($thickness <= 0 and $thickness != 'max') {
      throw new InvalidArgumentException('Invalid value of thickness. Should be positive integer or "max"');
    }
    $this->color = $color.'@'.$transparent.":t=".$thickness;
    return $this;
  }

  public function getColor()
  {
    return $this->color;
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

  public function setDimensions(Dimension $dimension)
  {
    $this->dimensions = $dimension;
    return $this;
  }

  public function getDimensions()
  {
    return $this->dimensions;
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

  public function getStringParameters()
  {
    $params = array("drawbox=");

    if($this->timeLine instanceof TimeLine) {
      $params[] = "enable='between(t,".$this->timeLine->getStartTime().",".$this->timeLine->getEndTime().")'";
    }

    $params[] = "width=".$this->getDimensions()->getWidth();
    $params[] = "height=".$this->getDimensions()->getHeight();
    $params[] = "color=".$this->getColor();
    $params[] = "x=".$this->getCoordinates()->getX();
    $params[] = "y=".$this->getCoordinates()->getY();

    return implode(":", $params);
  }

  public function __toString()
  {
    return $this->getStringParameters();
  }
}
