<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use Sharapov\FFMpegExtensions\Coordinate\Duration;
use Sharapov\FFMpegExtensions\Filters\ExtraInputStreamInterface;
use Sharapov\FFMpegExtensions\Filters\ExtraInputStreamTrait;

/**
 * Alpha layer filter option
 * @package Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions
 */
class OptionAlphakey
  implements
  OptionInterface,
  ExtraInputStreamInterface {
  use DimensionsTrait;
  use ExtraInputStreamTrait;
  use ZindexTrait;

  protected $_duration;

  /**
   * Returns command string.
   *
   * @return string
   */
  public function getCommand() {
    return sprintf( "[%s]scale=%s[abg],[%s][abg]overlay[%s]", ':s1', (string) $this->getDimensions(), ':s2', ':s3' );
  }

  /**
   * Returns duration object
   * @return mixed
   */
  public function getDuration() {
    return $this->_duration;
  }

  /**
   * Set duration object.
   *
   * @param Duration $duration
   *
   * @return $this
   */
  public function setDuration( Duration $duration ) {
    $this->_duration = $duration;

    return $this;
  }

  /**
   * Returns a command string.
   *
   * @return string
   */
  public function __toString() {
    return $this->getCommand();
  }
}