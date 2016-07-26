<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video;

use FFMpeg\Filters\Video\VideoFilterInterface;
use FFMpeg\Media\Video;
use FFMpeg\Format\VideoInterface;
use FFMpeg\Exception\InvalidArgumentException;

class FilterConcat implements VideoFilterInterface
{
  /** @var integer */
  protected $priority;

  protected $inputs = array();

  public function __construct($priority = 0)
  {
    $this->priority = $priority;
  }

  /**
   * {@inheritdoc}
   */
  public function getPriority()
  {
    return $this->priority;
  }

  /**
   * {@inheritdoc}
   */
  public function attachFile($file)
  {
    if (!file_exists($file)) {
      throw new InvalidArgumentException('Path '.$file.' is incorrect');
    }
    $this->inputs[] = $file;
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public function apply(Video $video, VideoInterface $format)
  {
    $commands = $filterOptions = array();

    foreach ($this->inputs as $input) {
      $commands[] = '-i';
      $commands[] = $input;
    }

    for ($i = 0; $i <= count($this->inputs); $i++) {
      $filterOptions[] = sprintf('[%s:v:0]', $i);
      $filterOptions[] = sprintf('[%s:a:0]', $i);
    }

    $filterOptions[] = sprintf('concat=n=%s:v=1:a=1', (count($this->inputs) + 1));
    $commands[] = '-filter_complex';
    $commands[] = implode(" ", $filterOptions);
    return $commands;
  }
}
