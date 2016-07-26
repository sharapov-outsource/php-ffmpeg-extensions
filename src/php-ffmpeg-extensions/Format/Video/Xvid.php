<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Format\Video;

use FFMpeg\Format\Video\DefaultVideo;

/**
 * The XVID video format
 */
class Xvid extends DefaultVideo
{
  public $passes = 2;

  /** @var boolean */
  private $bframesSupport = true;

  public function __construct($audioCodec = 'libfaac', $videoCodec = 'libxvid')
  {
    $this
        ->setAudioCodec($audioCodec)
        ->setVideoCodec($videoCodec);
  }

  /**
   * {@inheritDoc}
   */
  public function supportBFrames()
  {
    return $this->bframesSupport;
  }

  /**
   * @param $support
   *
   * @return Xvid
   */
  public function setBFramesSupport($support)
  {
    $this->bframesSupport = $support;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getAvailableAudioCodecs()
  {
    return array('libvo_aacenc', 'libfaac', 'libmp3lame', 'libfdk_aac');
  }

  /**
   * {@inheritDoc}
   */
  public function getAvailableVideoCodecs()
  {
    return array('libxvid');
  }

  /**
   * {@inheritDoc}
   */
  public function setPasses($passes)
  {
    $this->passes = $passes;
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getPasses()
  {
    return $this->passes;
  }

  /**
   * @return int
   */
  public function getModulus()
  {
    return 2;
  }
}
