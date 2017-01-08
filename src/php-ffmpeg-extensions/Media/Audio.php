<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Media;

use Sharapov\FFMpegExtensions\Filters\Audio\AudioFilters;

class Audio extends \FFMpeg\Media\Audio
{
  use MediaTypeTrait;

  /**
   * {@inheritdoc}
   *
   * @return AudioFilters
   */
  public function filters()
  {
    return new AudioFilters($this);
  }
}
