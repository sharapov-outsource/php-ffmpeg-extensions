<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions;

use Sharapov\FFMpegExtensions\Filters\ExtraInputStreamInterface;
use Sharapov\FFMpegExtensions\Filters\ExtraInputStreamTrait;
use Sharapov\FFMpegExtensions\Input\FileInterface;

/**
 * Audiofile filter option
 * @package Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions
 */
class OptionAudioFile
    implements
    OptionInterface,
    ExtraInputStreamInterface
{
  use ExtraInputStreamTrait;

  public function __construct(FileInterface $file)
  {
    $this->setExtraInputStream($file);
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
