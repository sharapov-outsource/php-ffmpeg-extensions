<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 */

namespace Sharapov\FFMpegExtensions\Media;

use Alchemy\BinaryDriver\Exception\ExecutionFailureException;
use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Exception\RuntimeException;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Format\AudioInterface;
use FFMpeg\Format\FormatInterface;
use FFMpeg\Format\ProgressableInterface;
use FFMpeg\Format\VideoInterface;
use Neutron\TemporaryFilesystem\Manager as FsManager;
use Sharapov\FFMpegExtensions\Filters\Video\VideoFilters;

class Video extends \FFMpeg\Media\Video {
  use MediaTypeTrait;

  /** @var string */
  private $preset;

  /** @var array */
  private $supportedPresets = [
    'ultrafast',
    'superfast',
    'veryfast',
    'faster',
    'fast',
    'medium',
    'slow',
    'slower',
    'veryslow'
  ];

  /**
   * {@inheritdoc}
   * @return VideoFilters
   */
  public function filters() {
    return new VideoFilters($this);
  }

  /**
   * Exports the video in the desired format, applies registered filters.
   *
   * @param FormatInterface $format
   * @param string $outputPathFile
   *
   * @return Video
   * @throws RuntimeException
   */
  public function save(FormatInterface $format, $outputPathFile) {
    $commands = ['-y', '-i', $this->pathfile];

    $filters = clone $this->filters;
    $extraFilters = [];

    foreach($filters as $filter) {
      // Video filter options must be attached after all the extra input streams
      if($filter instanceof \Sharapov\FFMpegExtensions\Filters\Video\VideoFilterInterface) {
        /** @var VideoInterface $format */
        $extraFilters = array_merge($extraFilters, $filter->apply($this, $format));
      }
      // If filter has extra input streams, we need to attach them into the command
      if(count($filter->getExtraInputs()) > 0) {
        $commands = array_merge($commands, $filter->getExtraInputs());
      }
    }

    $commands = array_merge($commands, $extraFilters);

    $filters->add(new SimpleFilter($format->getExtraParams(), 10));

    if($this->driver->getConfiguration()->has('ffmpeg.threads')) {
      $filters->add(new SimpleFilter(['-threads', $this->driver->getConfiguration()->get('ffmpeg.threads')]));
    }
    if($format instanceof VideoInterface) {
      if(null !== $format->getVideoCodec()) {
        $filters->add(new SimpleFilter(['-vcodec', $format->getVideoCodec()]));
      }
    }
    if($format instanceof AudioInterface) {
      if(null !== $format->getAudioCodec()) {
        $filters->add(new SimpleFilter(['-acodec', $format->getAudioCodec()]));
      }
    }

    foreach($filters as $filter) {
      if(!$filter instanceof \Sharapov\FFMpegExtensions\Filters\Video\VideoFilterInterface) {
        $commands = array_merge($commands, $filter->apply($this, $format));
      }
    }

    if($format instanceof VideoInterface) {
      $commands[] = '-b:v';
      $commands[] = $format->getKiloBitrate() . 'k';
      $commands[] = '-refs';
      $commands[] = '6';
      $commands[] = '-coder';
      $commands[] = '1';
      $commands[] = '-sc_threshold';
      $commands[] = '40';
      $commands[] = '-flags';
      $commands[] = '+loop';
      $commands[] = '-me_range';
      $commands[] = '16';
      $commands[] = '-subq';
      $commands[] = '7';
      $commands[] = '-i_qfactor';
      $commands[] = '0.71';
      $commands[] = '-qcomp';
      $commands[] = '0.6';
      $commands[] = '-qdiff';
      $commands[] = '4';
      $commands[] = '-trellis';
      $commands[] = '1';

      if($this->getPreset()) {
        $commands[] = '-preset';
        $commands[] = $this->getPreset();
      }
    }

    if($format instanceof AudioInterface) {
      if(null !== $format->getAudioKiloBitrate()) {
        $commands[] = '-b:a';
        $commands[] = $format->getAudioKiloBitrate() . 'k';
      }
      if(null !== $format->getAudioChannels()) {
        $commands[] = '-ac';
        $commands[] = $format->getAudioChannels();
      }
    }

    $fs = FsManager::create();
    $fsId = uniqid('ffmpeg-passes');
    $passPrefix = $fs->createTemporaryDirectory(0777, 50, $fsId) . '/' . uniqid('pass-');
    $passes = [];
    $totalPasses = $format->getPasses();

    if(1 > $totalPasses) {
      throw new InvalidArgumentException('Pass number should be a positive value.');
    }

    for($i = 1; $i <= $totalPasses; $i++) {
      $pass = $commands;

      if($totalPasses > 1) {
        $pass[] = '-pass';
        $pass[] = $i;
        $pass[] = '-passlogfile';
        $pass[] = $passPrefix;
      }

      $pass[] = $outputPathFile;

      $passes[] = $pass;
    }

    $failure = null;

    foreach($passes as $pass => $passCommands) {
      try {
        /** add listeners here */
        $listeners = null;

        if($format instanceof ProgressableInterface) {
          $listeners = $format->createProgressListener($this, $this->ffprobe, $pass + 1, $totalPasses);
        }

        $this->driver->command($passCommands, false, $listeners);
      } catch (ExecutionFailureException $e) {
        $failure = $e;
        break;
      }
    }

    $fs->clean($fsId);

    if(null !== $failure) {
      throw new RuntimeException('Encoding failed', $failure->getCode(), $failure);
    }

    return $this;
  }

  /**
   * @return mixed
   */
  public function getStreamDuration() {
    return $this->getStreams()->videos()->first()->get('duration');
  }

  /**
   * @param $preset
   */
  public function setPreset($preset) {
    $this->preset = $preset;
    if(!in_array($preset, $this->supportedPresets)) {
      throw new InvalidArgumentException('Preset type doesn\'t supported. Supported types: ' . implode(', ', $this->supportedPresets) . '.');
    }
  }

  /**
   * @return mixed
   */
  public function getPreset() {
    return $this->preset;
  }
}
