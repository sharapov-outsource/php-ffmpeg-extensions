<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\Overlay;

/**
 * Interface OverlayInterface
 * @package Sharapov\FFMpegExtensions\Filters\Video\Overlay
 */
interface OverlayInterface
{
  public function getCommand();

  public function getImageFile();

  public function setImageFile(\Sharapov\FFMpegExtensions\Stream\FileInterface $file);

  public function __toString();
}
