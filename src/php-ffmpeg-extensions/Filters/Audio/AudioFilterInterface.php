<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Audio;

use FFMpeg\Filters\FilterInterface;
use FFMpeg\Format\AudioInterface;
use Sharapov\FFMpegExtensions\Media\Audio;

interface AudioFilterInterface extends FilterInterface
{
  /**
   * Applies the filter on the the Audio media given an format.
   *
   * @param Audio          $audio
   * @param AudioInterface $format
   *
   * @return array An array of arguments
   */
  public function apply(Audio $audio, AudioInterface $format);
}
