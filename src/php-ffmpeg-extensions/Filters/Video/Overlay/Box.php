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
use Sharapov\FFMpegExtensions\Stream\FileInterface;

/**
 * Class Box
 * @package Sharapov\FFMpegExtensions\Filters\Video\Overlay
 */
class Box implements OverlayInterface
{
  protected $_color = 'black@0.4:t=max';

  protected $_dimensions;

  protected $_coordinates;

  protected $_timeLine;

  /**
   * Constructor. Set box color.
   *
   * @param        $color
   * @param float  $transparent
   * @param string $thickness
   */
  public function __construct($color = 'black', $transparent = 0.4, $thickness = 'max')
  {
    $this->setColor($color, $transparent, $thickness);
  }

  /**
   * Set box color.
   *
   * @param        $color
   * @param float  $transparent
   * @param string $thickness
   *
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
    $this->_color = $color . '@' . $transparent . ":t=" . $thickness;

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
   * Set _coordinates object.
   *
   * @param \Sharapov\FFMpegExtensions\Coordinate\Point $point
   *
   * @return $this
   */
  public function setCoordinates(\Sharapov\FFMpegExtensions\Coordinate\Point $point)
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

  public function setDimensions(\Sharapov\FFMpegExtensions\Coordinate\Dimension $dimension)
  {
    $this->_dimensions = $dimension;

    return $this;
  }

  public function getDimensions()
  {
    return $this->_dimensions;
  }

  public function setTimeLine(\Sharapov\FFMpegExtensions\Coordinate\TimeLine $_timeLine)
  {
    $this->_timeLine = $_timeLine;

    return $this;
  }

  public function getTimeLine()
  {
    return $this->_timeLine;
  }

  /**
   * Return command string.
   * @return string
   */
  public function getCommand()
  {
    $filterOptions = [
        "width="  => $this->getDimensions()->getWidth(),
        "height=" => $this->getDimensions()->getHeight(),
        "color="  => $this->getColor(),
        "x="      => $this->getCoordinates()->getX(),
        "y="      => $this->getCoordinates()->getY()
    ];

    if ($this->_timeLine instanceof TimeLine) {
      $filterOptions[] = $this->_timeLine->getCommand();
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
  public function setImageFile(\Sharapov\FFMpegExtensions\Stream\FileInterface $file)
  {
    throw new InvalidArgumentException('Method setImageFile(FileInterface $file) is not implemented for this class');
  }
}
