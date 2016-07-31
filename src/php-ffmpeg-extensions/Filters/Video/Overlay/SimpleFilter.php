<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\Overlay;

use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Filters\Video\VideoFilterInterface;
use FFMpeg\Media\Video;
use FFMpeg\Format\VideoInterface;

/**
 * Class SimpleFilter
 * @package Sharapov\FFMpegExtensions\Filters\Video\Overlay
 */
class SimpleFilter extends AbstractFilter implements VideoFilterInterface
{
  /**
   * Overlay objects array
   * @var array
   */
  protected $_overlay = [];

  /**
   * Set overlay object.
   *
   * @param \Sharapov\FFMpegExtensions\Filters\Video\Overlay\OverlayInterface $overlay
   *
   * @return $this
   */
  public function setOverlay(\Sharapov\FFMpegExtensions\Filters\Video\Overlay\OverlayInterface $overlay)
  {
    $this->_overlay[] = $overlay;

    return $this;
  }

  /**
   * Set array of overlay objects.
   *
   * @param array $overlays
   *
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
    return $this->_overlay;
  }

  /**
   * Applies the filter on the the Video media given an format.
   *
   * @param \FFMpeg\Media\Video           $video
   * @param \FFMpeg\Format\VideoInterface $format
   *
   * @return array An array of arguments
   */
  public function apply(\FFMpeg\Media\Video $video, \FFMpeg\Format\VideoInterface $format)
  {
    if (empty($this->_overlay)) {
      throw new InvalidArgumentException('No overlay objects found');
    }

    return [
        '-vf',
        implode(",", $this->_overlay)
    ];
  }
}
