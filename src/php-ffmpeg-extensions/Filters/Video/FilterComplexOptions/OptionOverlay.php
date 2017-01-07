<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use Sharapov\FFMpegExtensions\Coordinate\Point;
use Sharapov\FFMpegExtensions\Coordinate\TimeLine;
use Sharapov\FFMpegExtensions\Input\FileInterface;
use FFMpeg\Exception\InvalidArgumentException;

/**
 * Overlay filter option
 * @package Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions
 */
class OptionOverlay
    implements
    OptionInterface,
    OptionProbeInterface,
    OptionExtraInputStreamInterface
{
  use TimeLineTrait;
  use CoordinatesTrait;
  use DimensionsTrait;
  use ProbeTrait;
  use ExtraInputStreamTrait;
  use ZindexTrait;

  /**
   * Get input streams collection.
   *
   * @return \FFMpeg\FFProbe\DataMapping\StreamCollection
   */
  public function getProbeData()
  {
    return $this->getProbe()->streams($this->getExtraInputStream()->getPath());
  }

  /**
   * Returns command string.
   *
   * @return string
   */
  public function getCommand()
  {
    $cmd = sprintf('[%s][%s]overlay=', ':s3', ':s4');

    if ($this->getCoordinates() instanceof Point) {
      $cmd .= sprintf("%s", (string)$this->getCoordinates());
    } else {
      $cmd .= '0:0';
    }

    if ($this->getTimeLine() instanceof TimeLine) {
      $cmd .= sprintf(":%s", (string)$this->getTimeLine());
    }

    return sprintf("[%s]scale=%s[%s],%s[%s]", ':s1', (string)$this->getDimensions(), ':s2', $cmd, ':s5');
  }

  /**
   * Returns a command string.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->getCommand();
  }
}
