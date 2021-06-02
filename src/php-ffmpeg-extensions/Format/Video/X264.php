<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 */

namespace Sharapov\FFMpegExtensions\Format\Video;

/**
 * The X264 video format
 * Uses aac audio codec by default. libfaac is is considered non-free
 * and most of unix systems doesn't provide ffmpeg with it.
 * Also that's a crappy encoder, there are better alternatives.
 */
class X264 extends \FFMpeg\Format\Video\X264 {

  /** @var int */
  private $cbr;

  public function __construct($audioCodec = 'aac', $videoCodec = 'libx264') {
    parent::__construct($audioCodec, $videoCodec);
  }

  /**
   * @param $cbr
   *
   * @return $this
   */
  public function setConstantBitrate($cbr) {
    $this->cbr = $cbr;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getConstantBitrate()
  {
    return $this->cbr;
  }
}
