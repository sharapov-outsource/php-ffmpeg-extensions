<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use Sharapov\FFMpegExtensions\Coordinate\TimeLine;

trait TimeLineTrait {

  protected $_timeLine;

  /**
   * Returns timeline object
   * @return mixed
   */
  public function getTimeLine() {
    return $this->_timeLine;
  }

  /**
   * Set timeline object.
   *
   * @param TimeLine $timeLine
   *
   * @return $this
   */
  public function setTimeLine( TimeLine $timeLine ) {
    $this->_timeLine = $timeLine;

    return $this;
  }
}