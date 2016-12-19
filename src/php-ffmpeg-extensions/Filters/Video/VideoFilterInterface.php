<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video;

use FFMpeg\Filters\FilterInterface;
use FFMpeg\Format\VideoInterface;
use Sharapov\FFMpegExtensions\Media\Video;

interface VideoFilterInterface extends FilterInterface
{
  /**
   * Applies the filter on the the Video media given an format.
   *
   * @param Video          $video
   * @param VideoInterface $format
   *
   * @return array An array of arguments
   */
  public function apply(Video $video, VideoInterface $format);
}
