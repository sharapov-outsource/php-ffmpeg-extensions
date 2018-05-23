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
   *
   * @param bool $returnDefault
   *
   * @return \Sharapov\FFMpegExtensions\Coordinate\Point|string
   */
  public function getCoordinates($returnDefault = false) {
    return ($this->_coordinates instanceof Point) ? $this->_coordinates : (($returnDefault) ? : $this->getDefaultCoordinates());
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

  /**
   * Gets default coordinates
   * @return string
   */
  public function getDefaultCoordinates() {
    return '0:0';
  }
}