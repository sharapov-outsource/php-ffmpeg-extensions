<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 */

namespace Sharapov\FFMpegExtensions\Coordinate;

use FFMpeg\Exception\InvalidArgumentException;

class Duration {
  protected $duration;

  /**
   * @param integer $duration
   *
   * @throws InvalidArgumentException when one of the parameters is invalid
   */
  public function __construct($duration) {
    if(!is_numeric($duration) || $duration < 0) {
      throw new InvalidArgumentException('Duration time should be positive integer or float value. ' . $duration . ' given.');
    }

    $this->duration = preg_replace('/,/', '.', $duration);
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->getCommand();
  }

  public function getCommand() {
    return implode(" ", ['-t', $this->getDuration()]);
  }

  /**
   * @return integer
   */
  public function getDuration() {
    return $this->duration;
  }
}
