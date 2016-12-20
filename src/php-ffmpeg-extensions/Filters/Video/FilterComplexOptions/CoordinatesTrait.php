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

trait CoordinatesTrait {

  protected $_coordinates;

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
    return $this->_coordinates;
  }
}