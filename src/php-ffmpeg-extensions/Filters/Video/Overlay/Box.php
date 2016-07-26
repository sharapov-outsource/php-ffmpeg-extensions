<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\Overlay;

use Sharapov\FFMpegExtensions\Coordinate\TimeLine;
use FFMpeg\Exception\InvalidArgumentException;

class Box implements OverlayInterface
{
  protected $color = 'black@0.4:t=max';

  protected $dimensions;

  protected $coordinates;

  protected $timeLine;

  /**
   * Constructor. Set box color.
   * @param $color
   * @param float $transparent
   * @param string $thickness
   */
  public function __construct($color = 'black', $transparent = 0.4, $thickness = 'max')
  {
    $this->setColor($color, $transparent, $thickness);
  }

  /**
   * Set box color.
   * @param $color
   * @param float $transparent
   * @param string $thickness
   * @return $this
   */
  public function setColor($color, $transparent = 0.4, $thickness = 'max')
  {
    if ($transparent > 1 || $transparent < 0) {
      throw new InvalidArgumentException('Invalid value of transparent. Should be integer or float value from 0 to 1');
    }

    if ($thickness <= 0 and $thickness != 'max') {
      throw new InvalidArgumentException('Invalid value of thickness. Should be positive integer or "max"');
    }
    $this->color = $color . '@' . $transparent . ":t=" . $thickness;
    return $this;
  }

  /**
   * Get box color.
   * @return string
   */
  public function getColor()
  {
    return $this->color;
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

  public function setDimensions(\Sharapov\FFMpegExtensions\Coordinate\Dimension $dimension)
  {
    $this->dimensions = $dimension;
    return $this;
  }

  public function getDimensions()
  {
    return $this->dimensions;
  }

  public function setTimeLine(\Sharapov\FFMpegExtensions\Coordinate\TimeLine $timeLine)
  {
    $this->timeLine = $timeLine;
    return $this;
  }

  public function getTimeLine()
  {
    return $this->timeLine;
  }

  /**
   * Return command string.
   * @return string
   */
  public function getCommand()
  {
    $filterOptions = array(
        "width=" => $this->getDimensions()->getWidth(),
        "height=" => $this->getDimensions()->getHeight(),
        "color=" => $this->getColor(),
        "x=" => $this->getCoordinates()->getX(),
        "y=" => $this->getCoordinates()->getY()
    );

    if ($this->timeLine instanceof TimeLine) {
      $filterOptions[] = $this->timeLine->getCommand();
    }
    return "drawbox=" . implode(":", $filterOptions);
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
