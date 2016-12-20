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

  private $_optionsCollection;

  /** @var integer */
  private $priority;

  /**
   * {@inheritdoc}
   */
  public function __construct(OptionsCollection $optionsCollection = null) {
    if($optionsCollection instanceof OptionsCollection) {
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
  public function apply(Video $video, VideoInterface $format)
  {
    $commands = ['-filter_complex'];

    // Place draw text
    $commands[] = (string)$this->_getDrawTextCommand();

    return $commands;
  }

  private function _getDrawTextCommand()
  {
    return new OptionsCollection(array_filter((array)$this->_optionsCollection->getIterator(), function (OptionsInterface $option) {
      if($option instanceof OptionDrawText) {
        return true;
      }
    }));
  }
}