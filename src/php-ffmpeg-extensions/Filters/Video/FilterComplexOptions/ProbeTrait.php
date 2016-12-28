<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use Sharapov\FFMpegExtensions\FFProbe;

trait ProbeTrait {

  protected $_probe;

  final function __construct() {
    $this->_probe = FFProbe::getInstance();
  }

  /**
   * Returns FFProbe driver
   * @return FFProbe
   */
  public function getProbe()
  {
    return $this->_probe;
  }
}