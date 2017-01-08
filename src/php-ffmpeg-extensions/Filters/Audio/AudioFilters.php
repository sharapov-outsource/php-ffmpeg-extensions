<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Audio;

use Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions\OptionsCollection;
use Sharapov\FFMpegExtensions\Media\Audio;

class AudioFilters extends \FFMpeg\Filters\Audio\AudioFilters
{
  public function __construct(Audio $media)
  {
    parent::__construct($media);
  }

  public function merge(OptionsCollection $optionsCollection)
  {
    $this->media->addFilter(new MergeFilter($optionsCollection));

    return $this;
  }

  public function replace()
  {
    return $this;
  }
}
