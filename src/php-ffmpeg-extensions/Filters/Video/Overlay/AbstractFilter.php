<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\Overlay;

use FFMpeg\Coordinate\Dimension;
use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Filters\Video\VideoFilterInterface;
use FFMpeg\Media\Video;
use FFMpeg\Format\VideoInterface;
use Sharapov\FFMpegExtensions\Filters\Video\Overlay\Image;
use Sharapov\FFMpegExtensions\Filters\Video\Overlay\OverlayInterface;
use Sharapov\FFMpegExtensions\Filters\Video\Overlay\Text;

abstract class AbstractFilter
{
  /**
   * Priority number
   * @var integer
   */
  protected $priority;

  /**
   * Filter constructor.
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
