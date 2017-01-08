<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions;

use Sharapov\FFMpegExtensions\Coordinate\Point;
use Sharapov\FFMpegExtensions\Coordinate\TimeLine;
use Sharapov\FFMpegExtensions\Filters\ProbeInterface;
use Sharapov\FFMpegExtensions\Filters\ProbeTrait;
use Sharapov\FFMpegExtensions\Input\FileInterface;
use FFMpeg\Exception\InvalidArgumentException;

/**
 * Overlay filter option
 * @package Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions
 */
class OptionAudioFile
    implements
    OptionInterface,
    ProbeInterface
{
  use ProbeTrait;

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
