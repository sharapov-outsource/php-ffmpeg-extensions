<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Media;

use Alchemy\BinaryDriver\Exception\ExecutionFailureException;
use FFMpeg\Driver\FFMpegDriver;
use FFMpeg\FFProbe;
use FFMpeg\Filters\Audio\AudioFilters;
use FFMpeg\Format\FormatInterface;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Exception\RuntimeException;
use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Filters\Audio\AudioFilterInterface;
use FFMpeg\Filters\FilterInterface;
use FFMpeg\Format\ProgressableInterface;
use Sharapov\FFMpegExtensions\Stream\FileInterface;

class Audio extends \FFMpeg\Media\Audio
{
  protected $_file;

  public function __construct(FileInterface $file, FFMpegDriver $driver, FFProbe $ffprobe)
  {
    $this->_file = $file;
    parent::__construct($file->getFile(), $driver, $ffprobe);
  }

  public function getFileObject()
  {
    return $this->_file;
  }

  /**
   * {@inheritdoc}
   *
   * @return AudioFilters
   */
  public function filters()
  {
    return new AudioFilters($this);
  }

  /**
   * {@inheritdoc}
   *
   * @return Audio
   */
  public function addFilter(FilterInterface $filter)
  {
    if (!$filter instanceof AudioFilterInterface) {
      throw new InvalidArgumentException('Audio only accepts AudioFilterInterface filters');
    }

    $this->filters->add($filter);

    return $this;
  }

  /**
   * Exports the audio in the desired format, applies registered filters.
   *
   * @param FormatInterface $format
   * @param string          $outputPathfile
   *
   * @return Audio
   *
   * @throws RuntimeException
   */
  public function save(FormatInterface $format, $outputPathfile)
  {
    $listeners = null;

    if ($format instanceof ProgressableInterface) {
      $listeners = $format->createProgressListener($this, $this->ffprobe, 1, 1);
    }

    $commands = ['-y', '-i', $this->pathfile];

    $filters = clone $this->filters;
    $filters->add(new SimpleFilter($format->getExtraParams(), 10));

    if ($this->driver->getConfiguration()->has('ffmpeg.threads')) {
      $filters->add(new SimpleFilter(['-threads', $this->driver->getConfiguration()->get('ffmpeg.threads')]));
    }
    if (null !== $format->getAudioCodec()) {
      $filters->add(new SimpleFilter(['-acodec', $format->getAudioCodec()]));
    }

    foreach ($filters as $filter) {
      $commands = array_merge($commands, $filter->apply($this, $format));
    }

    if (null !== $format->getAudioKiloBitrate()) {
      $commands[] = '-b:a';
      $commands[] = $format->getAudioKiloBitrate() . 'k';
    }
    if (null !== $format->getAudioChannels()) {
      $commands[] = '-ac';
      $commands[] = $format->getAudioChannels();
    }
    $commands[] = $outputPathfile;

    try {
      $this->driver->command($commands, false, $listeners);
    } catch (ExecutionFailureException $e) {
      $this->cleanupTemporaryFile($outputPathfile);
      throw new RuntimeException('Encoding failed', $e->getCode(), $e);
    }

    return $this;
  }
}
