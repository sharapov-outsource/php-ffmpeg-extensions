<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Format\Video;

use FFMpeg\Format\FormatInterface;
use FFMpeg\FFProbe;
use Evenement\EventEmitter;
use FFMpeg\Format\ProgressableInterface;
use FFMpeg\Media\MediaTypeInterface;
use FFMpeg\Format\ProgressListener\VideoProgressListener;

/**
 * Transport stream video handler.
 */
class TransportStream extends EventEmitter implements FormatInterface, ProgressableInterface
{
  /**
   * {@inheritDoc}
   */
  public function getPasses()
  {
    return 1;
  }

  /**
   * {@inheritdoc}
   */
  public function getExtraParams()
  {
    return [];
  }

  public function createProgressListener(MediaTypeInterface $media, FFProbe $ffprobe, $pass, $total)
  {
    $format = $this;
    $listeners = [new VideoProgressListener($ffprobe, $media->getPathfile(), $pass, $total)];

    foreach ($listeners as $listener) {
      $listener->on('progress', function () use ($format, $media) {
        $format->emit('progress', array_merge([$media, $format], func_get_args()));
      });
    }

    return $listeners;
  }
}