<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 */

namespace Sharapov\FFMpegExtensions\Filters\Audio;

use Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions\OptionAudioFile;
use Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions\OptionsCollection;
use Sharapov\FFMpegExtensions\Input\FileInterface;
use Sharapov\FFMpegExtensions\Media\Audio;

class AudioFilters extends \FFMpeg\Filters\Audio\AudioFilters {
  public function __construct(Audio $media) {
    parent::__construct($media);
  }

  /**
   * Converts stereo to mono.
   * @return $this
   */
  public function stereo2mono() {
    $this->media->addFilter(new MergeFilter(null, MergeFilter::STEREO_2_MONO));

    return $this;
  }

  /**
   * Converts mono audio to stereo. The audio file will be mixed as the right channel.
   *
   * @param \Sharapov\FFMpegExtensions\Input\FileInterface $file
   *
   * @return $this
   */
  public function mono2stereo(FileInterface $file) {
    $this->media->addFilter(new MergeFilter(new OptionsCollection([new OptionAudioFile($file)]), MergeFilter::MONO_2_STEREO));

    return $this;
  }

  /**
   * Converts multiple streams to stereo.
   * @return $this
   */
  public function all2stereo() {
    $this->media->addFilter(new MergeFilter(null, MergeFilter::ALL_2_STEREO));

    return $this;
  }

  /**
   * Combine two or more stereo inputs into one stereo output.
   *
   * @param \Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions\OptionsCollection $optionsCollection
   *
   * @return $this
   */
  public function combineStereos(OptionsCollection $optionsCollection) {
    $this->media->addFilter(new MergeFilter($optionsCollection, MergeFilter::MUX_STEREO));

    return $this;
  }

  public function replace() {
    return $this;
  }
}
