<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use Sharapov\FFMpegExtensions\Coordinate\Dimension;

trait DimensionsTrait {

  protected $_dimensions;

  /**
   * Returns coordinates object.
   *
   * @param Dimension $dimension
   *
   * @return mixed
   */
  public function setDimensions(Dimension $dimension)
  {
    $this->_dimensions = $dimension;

    return $this;
  }

  /**
   * Returns dimensions object.
   * @return mixed
   */
  public function getDimensions()
  {
    return $this->_dimensions;
  }
}