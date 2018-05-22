<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 */

namespace Sharapov\FFMpegExtensions\Coordinate;

use FFMpeg\Exception\InvalidArgumentException;

class TimeLine {
  protected $start;
  protected $end;

  /**
   * @param integer $start Starting timestamp in seconds
   * @param integer $end   Ending timestamp in seconds
   *
   * @throws InvalidArgumentException when one of the parameters is invalid
   */
  public function __construct($start, $end) {
    if(!is_numeric($start) || !is_numeric($end) || $start < 0 || $end < 0) {
      throw new InvalidArgumentException('Start and end time should be positive integer or float value. ' . $start . ', ' . $end . ' given.');
    }

    if($end <= $start) {
      throw new InvalidArgumentException('End time should not be less or equal to start. ' . $start . ', ' . $end . ' given.');
    }

    $this->start = preg_replace('/,/', '.', $start);
    $this->end = preg_replace('/,/', '.', $end);
  }

  /**
   * @return integer
   */
  public function getDuration() {
    return $this->end - $this->start;
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->getCommand();
  }

  public function getCommand() {
    return sprintf("enable='between(%s)'", implode(",", ['t', $this->getStartTime(), $this->getEndTime()]));
  }

  /**
   * @return integer
   */
  public function getStartTime() {
    return $this->start;
  }

  /**
   * @return integer
   */
  public function getEndTime() {
    return $this->end;
  }
}
