<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video;

use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\FilterComplexOptionsRegistry;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionsCollection;
use Sharapov\FFMpegExtensions\Media\Video;

class VideoFilters extends \FFMpeg\Filters\Video\VideoFilters
{
  public function __construct(Video $media)
  {
    parent::__construct($media);
  }

  /**
   * Resizes a video to a given dimension.
   *
   * @param OptionsCollection $optionsCollection
   *
   * @return VideoFilters
   */
  public function complex(OptionsCollection $optionsCollection)
  {
    $this->media->addFilter(new ComplexFilter($optionsCollection));

    return $this;
  }
}