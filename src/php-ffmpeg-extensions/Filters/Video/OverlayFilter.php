<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video;

use FFMpeg\Coordinate\Dimension;
use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Filters\Video\VideoFilterInterface;
use FFMpeg\Media\Video;
use FFMpeg\Format\VideoInterface;
use Sharapov\FFMpegExtensions\Filters\Video\Overlay\OverlayInterface;
use Sharapov\FFMpegExtensions\Filters\Video\Overlay\Text;

class OverlayFilter implements VideoFilterInterface
{
  /** @var integer */
  protected $priority;

  protected $overlay = array();

  public function __construct($priority = 0)
  {
    $this->priority = $priority;
  }

  /**
   * {@inheritdoc}
   */
  public function getPriority()
  {
    return $this->priority;
  }

  /**
   * {@inheritdoc}
   */
  public function setOverlay(OverlayInterface $overlay)
  {
    $this->overlay[] = $overlay;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function apply(Video $video, VideoInterface $format)
  {
    return array(
        '-vf',
        implode(",", $this->overlay)
    );
  }
}
