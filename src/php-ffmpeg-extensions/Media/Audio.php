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

use FFMpeg\Format\FormatInterface;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Exception\RuntimeException;
use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Filters\FilterInterface;
use FFMpeg\Format\ProgressableInterface;
use Sharapov\FFMpegExtensions\Filters\Audio\AudioFilterInterface;
use Sharapov\FFMpegExtensions\Filters\Audio\AudioFilters;

class Audio extends \FFMpeg\Media\Audio {
  use MediaTypeTrait;

  /**
   * {@inheritdoc}
   *
   * @return AudioFilters
   */
  public function filters() {
    return new AudioFilters( $this );
  }

  /**
   * {@inheritdoc}
   *
   * @return Audio
   */
  public function addFilter( FilterInterface $filter ) {
    if ( ! $filter instanceof AudioFilterInterface ) {
      throw new InvalidArgumentException( 'Audio only accepts AudioFilterInterface filters' );
    }

    $this->filters->add( $filter );

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
  public function save( FormatInterface $format, $outputPathfile ) {
    $commands = [ '-y', '-i', $this->pathfile ];

    $filters      = clone $this->filters;
    $extraFilters = [];

    foreach ( $filters as $filter ) {
      // Video filter options must be attached after all the extra input streams
      if ( $filter instanceof \Sharapov\FFMpegExtensions\Filters\Audio\AudioFilterInterface ) {
        /** @var \FFMpeg\Format\AudioInterface $format */
        $extraFilters = array_merge( $extraFilters, $filter->apply( $this, $format ) );
      }
      // If filter has extra input streams, we need to attach them into the command
      if ( count( $filter->getExtraInputs() ) > 0 ) {
        $commands = array_merge( $commands, $filter->getExtraInputs() );
      }
    }

    $commands = array_merge( $commands, $extraFilters );

    $filters->add( new SimpleFilter( $format->getExtraParams(), 10 ) );

    if ( $this->driver->getConfiguration()->has( 'ffmpeg.threads' ) ) {
      $filters->add( new SimpleFilter( [ '-threads', $this->driver->getConfiguration()->get( 'ffmpeg.threads' ) ] ) );
    }
    if ( null !== $format->getAudioCodec() ) {
      $filters->add( new SimpleFilter( [ '-acodec', $format->getAudioCodec() ] ) );
    }

    foreach ( $filters as $filter ) {
      if ( ! $filter instanceof \Sharapov\FFMpegExtensions\Filters\Audio\AudioFilterInterface ) {
        $commands = array_merge( $commands, $filter->apply( $this, $format ) );
      }
    }

    if ( null !== $format->getAudioKiloBitrate() ) {
      $commands[] = '-b:a';
      $commands[] = $format->getAudioKiloBitrate() . 'k';
    }
    if ( null !== $format->getAudioChannels() ) {
      $commands[] = '-ac';
      $commands[] = $format->getAudioChannels();
    }
    $commands[] = $outputPathfile;

    try {
      $listeners = null;

      if ( $format instanceof ProgressableInterface ) {
        $listeners = $format->createProgressListener( $this, $this->ffprobe, 1, 1 );
      }

      $this->driver->command( $commands, false, $listeners );
    } catch ( ExecutionFailureException $e ) {
      $this->cleanupTemporaryFile( $outputPathfile );
      throw new RuntimeException( 'Encoding failed', $e->getCode(), $e );
    }

    return $this;
  }
}
