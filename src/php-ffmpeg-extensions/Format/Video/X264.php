<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Format\Video;

/**
 * The X264 video format
 */
class X264 extends \FFMpeg\Format\Video\X264
{
  public function __construct($audioCodec = 'libfaac', $videoCodec = 'libx264')
  {
    parent::__construct($audioCodec, $videoCodec);
  }

  /**
   * {@inheritDoc}
   */
  public function getAvailableAudioCodecs()
  {
    return ['libvo_aacenc', 'libfaac', 'libmp3lame', 'libfdk_aac', 'copy'];
  }

  /**
   * {@inheritDoc}
   */
  public function getPasses()
  {
    return 1;
  }
}