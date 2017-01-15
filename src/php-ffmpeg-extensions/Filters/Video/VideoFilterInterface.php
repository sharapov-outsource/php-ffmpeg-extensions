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
use Sharapov\FFMpegExtensions\Input\FileInterface;
use Sharapov\FFMpegExtensions\Media\Video;

interface VideoFilterInterface extends FilterInterface
{
  public function getExtraInputs();
  public function setExtraInput(FileInterface $input);
  public function apply(Video $video, VideoInterface $format);
}
