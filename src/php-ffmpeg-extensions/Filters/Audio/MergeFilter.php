<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Audio;

use FFMpeg\Format\AudioInterface;
use Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions\OptionsCollection;
use Sharapov\FFMpegExtensions\Media\Audio;

class MergeFilter implements AudioFilterInterface
{
  private $_optionsCollection;

  /** @var integer */
  private $priority;

  /**
   * {@inheritdoc}
   */
  public function __construct(OptionsCollection $optionsCollection = null)
  {
    if ($optionsCollection instanceof OptionsCollection) {
      $this->setOptionsCollection($optionsCollection);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getOptionsCollection()
  {
    return $this->_optionsCollection;
  }

  /**
   * {@inheritdoc}
   */
  public function setOptionsCollection(OptionsCollection $optionsCollection)
  {
    $this->_optionsCollection = $optionsCollection;
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
  public function apply(Audio $audio, AudioInterface $format)
  {
    return array('-ac', 2, '-ar', $this->rate);
  }
}