<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Coordinate;

use FFMpeg\Exception\InvalidArgumentException;

class TimeLine
{
  private $start;
  private $end;

  /**
   * @param integer $start
   * @param integer $end
   *
   * @throws InvalidArgumentException when one of the parameters is invalid
   */
  public function __construct($start, $end)
  {
    if ($start <= 0 || $end <= 0) {
      throw new InvalidArgumentException('Start and end time should be positive integer');
    }

    $this->start = (int)$start;
    $this->end = (int)$end;
  }

  /**
   * @return integer
   */
  public function getStartTime()
  {
    return $this->start;
  }

  /**
   * @return integer
   */
  public function getEndTime()
  {
    return $this->end;
  }
}
