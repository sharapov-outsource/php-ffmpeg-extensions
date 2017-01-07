<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

/**
 * Alpha layer filter option
 * @package Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions
 */
class OptionAlphakey
    implements
    OptionInterface,
    OptionProbeInterface,
    OptionExtraInputStreamInterface
{
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
    return sprintf("[%s]scale=%s[abg],[%s][abg]overlay[%s]", ':s1', (string)$this->getDimensions(), ':s2', ':s3');
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