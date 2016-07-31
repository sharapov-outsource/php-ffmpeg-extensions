<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\Overlay;

/**
 * Class AbstractFilter
 * @package Sharapov\FFMpegExtensions\Filters\Video\Overlay
 */
abstract class AbstractFilter
{
  /**
   * Priority number
   * @var integer
   */
  protected $priority;

  /**
   * Filter constructor.
   *
   * @param int $priority
   */
  public function __construct($priority = 0)
  {
    $this->priority = (int)$priority;
  }

  /**
   * Returns the priority of the filter.
   * @return int
   */
  public function getPriority()
  {
    return $this->priority;
  }
}
