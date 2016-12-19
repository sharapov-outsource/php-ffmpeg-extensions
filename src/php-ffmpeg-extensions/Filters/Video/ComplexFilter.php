<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video;

use FFMpeg\Format\VideoInterface;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionDrawText;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionsCollection;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionsInterface;
use Sharapov\FFMpegExtensions\Media\Video;

class ComplexFilter implements VideoFilterInterface {

  private $optionsCollection;

  /** @var integer */
  private $priority;

  /**
   * {@inheritdoc}
   */
  public function __construct(OptionsCollection $optionsCollection = null) {
    if($optionsCollection instanceof OptionsCollection) {
      $this->setOptions($optionsCollection);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getOptions()
  {
    return $this->optionsCollection;
  }

  /**
   * {@inheritdoc}
   */
  public function setOptions(OptionsCollection $optionsCollection)
  {
    $this->optionsCollection = $optionsCollection;
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
  public function apply(Video $video, VideoInterface $format)
  {
    $commands = ['-filter_complex'];

    // Place draw text
    $commands[] = (string)$this->_fetchDrawText();

    return $commands;
  }

  private function _fetchDrawText()
  {
    return new OptionsCollection(array_filter((array)$this->getOptions()->getIterator(), function (OptionsInterface $option) {
      if($option instanceof OptionDrawText) {
        return true;
      }
    }));
  }
}

/*
 "D:/Projects/videomachine2/ffmpeg/bin/ffmpeg.exe" "-y" "-i" "D:/Projects/php-ffmpeg-extensions/examples/source/demo_video_720p_HD.mp4"  "-filter_complex" "drawtext=text='This is the default text'" "-threads" "12" "-vcodec" "libx264" "-acodec" "libmp3lame" "-b:v" "1000k" "-refs" "6" "-coder" "1" "-sc_threshold" "40" "-flags" "+loop" "-me_range" "16" "-subq" "7" "-i_qfactor" "0.71" "-qcomp" "0.6" "-qdiff" "4" "-trellis" "1" "-b:a" "128k" "D:/Projects/php-ffmpeg-extensions/output.mp4"
 */