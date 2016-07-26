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

class SimpleFilter extends AbstractFilter implements VideoFilterInterface
{
  /**
   * Overlay objects array
   * @var array
   */
  protected $overlay = array();

  /**
   * Set overlay object.
   * @param \Sharapov\FFMpegExtensions\Filters\Video\Overlay\OverlayInterface $overlay
   * @return $this
   */
  public function setOverlay(OverlayInterface $overlay)
  {
    $this->overlay[] = $overlay;
    return $this;
  }

  /**
   * Set array of overlay objects.
   * @param array $overlays
   * @return $this
   */
  public function setOverlays(array $overlays)
  {
    foreach ($overlays as $overlay) {
      $this->setOverlay($overlay);
    }
    return $this;
  }

  /**
   * Get the array of overlays.
   * @return array
   */
  public function getOverlays()
  {
    return $this->overlay;
  }

  /**
   * Applies the filter on the the Video media given an format.
   *
   * @param Video $video
   * @param VideoInterface $format
   *
   * @return array An array of arguments
   */
  public function apply(Video $video, VideoInterface $format)
  {
    if (empty($this->overlay)) {
      throw new InvalidArgumentException('No overlay objects found');
    }
    return array(
        '-vf',
        implode(",", $this->overlay)
    );
  }
}
