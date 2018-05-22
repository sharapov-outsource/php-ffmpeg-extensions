<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use Sharapov\FFMpegExtensions\Coordinate\Point;
use FFMpeg\Exception\InvalidArgumentException;

trait CoordinatesTrait {
  protected $_coordinates;

  /**
   * Returns coordinates object.
   * @return mixed
   */
  public function getCoordinates() {
    if(!$this->_coordinates instanceof Point) {
      throw new InvalidArgumentException('Coordinates are empty.');
    }

    return $this->_coordinates;
  }

  /**
   * Set coordinates object.
   *
   * @param Point $point
   *
   * @return $this
   */
  public function setCoordinates(Point $point) {
    $this->_coordinates = $point;

    return $this;
  }
}