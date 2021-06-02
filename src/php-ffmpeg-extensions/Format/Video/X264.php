<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 */

namespace Sharapov\FFMpegExtensions\Format\Video;

use Sharapov\FFMpegExtensions\Format\VideoInterface;

/**
 * The X264 video format
 * Uses aac audio codec by default. libfaac is is considered non-free
 * and most of unix systems doesn't provide ffmpeg with it.
 * Also that's a crappy encoder, there are better alternatives.
 */
class X264 extends \FFMpeg\Format\Video\X264 implements VideoInterface {

  /** @var int */
  private $crf;

  public function __construct($audioCodec = 'aac', $videoCodec = 'libx264') {
    parent::__construct($audioCodec, $videoCodec);
  }

  /**
   * @param $cbr
   *
   * @return $this
   */
  public function setConstantRateFactor($crf) {
    $this->crf = $crf;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getConstantRateFactor()
  {
    return $this->crf;
  }
}
