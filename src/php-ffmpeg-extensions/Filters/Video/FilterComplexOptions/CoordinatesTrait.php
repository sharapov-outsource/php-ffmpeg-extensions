<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use Sharapov\FFMpegExtensions\Coordinate\Point;
use FFMpeg\Exception\InvalidArgumentException;

trait CoordinatesTrait
{

  protected $_coordinates;

  protected $_zIndex = null;

  /**
   * Set coordinates object.
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
   * Returns coordinates object.
   * @return mixed
   */
  public function getCoordinates()
  {
    if (!$this->_coordinates instanceof Point) {
      throw new InvalidArgumentException('Coordinates are empty.');
    }

    return $this->_coordinates;
  }

  /**
   * Set z-index coordinate.
   *
   * The z-index property specifies the stack order of an element.
   * An element with greater stack order is always in front of an element with a lower stack order.
   *
   * @param int $z
   *
   * @return $this
   */
  public function setZIndex($z)
  {
    if (!is_int($z)) {
      throw new InvalidArgumentException('Z-Index should be positive integer. ' . $z . ' given.');
    }

    $this->_zIndex = $z;

    return $this;
  }

  /**
   * Returns z-index coordinate.
   * @return mixed
   */
  public function getZIndex()
  {
    return $this->_zIndex;
  }
}