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
use Sharapov\FFMpegExtensions\Filters\ExtraInputStreamInterface;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionAlphakey;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionChromakey;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionDrawText;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionDrawBox;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionOverlay;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionsCollection;
use Sharapov\FFMpegExtensions\Input\FileInterface;
use Sharapov\FFMpegExtensions\Media\Video;

class ComplexFilter implements ExtraInputStreamInterface, VideoFilterInterface
{
  private $_optionsCollection;

  private $_optionsPrepared;

  private $_extraInputStreams = [];

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
  public function getExtraInputStreams()
  {
    return $this->_extraInputStreams;
  }

  /**
   * {@inheritdoc}
   */
  public function setExtraInputStream(FileInterface $file)
  {
    $this->_extraInputStreams[] = '-i';
    $this->_extraInputStreams[] = $file->getPath();
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
    $firstStreamId = '0:v';
    $lastStreamId  = $firstStreamId;
    $inputsMapping = $inputs = [];
    // Detect all additional inputs numbers
    for ($i = 0; $i <= $this
        ->getOptionsCollection()
        ->filterHasExtraInputs()
        ->count(); $i++
    ) {
      $inputsMapping[] = sprintf('%s:v', $i);
    }

    // Prepare overlay inputs
    $optionsOverlay = $this->getOptionsCollection()->sortByZindex();
    $imn = 1;
    $stm = 1;
    if ($optionsOverlay->count() > 0) {
      foreach ($optionsOverlay as $option) {
        if($option instanceof OptionDrawText) {

          $this->_optionsPrepared[] =
              str_replace([
                              ':s1', ':s2'
                          ], [
                              $lastStreamId,
                              's' . $stm
                          ], $option->getCommand());

          $lastStreamId = 's' . $stm;

        } elseif ($option instanceof OptionDrawBox) {

          $this->_optionsPrepared[] =
              str_replace([
                              ':s1', ':s2'
                          ], [
                              $lastStreamId,
                              's' . $stm
                          ], $option->getCommand());

          $lastStreamId = 's' . $stm;

        } elseif ($option instanceof OptionOverlay) {

          $this->_optionsPrepared[] =
              str_replace([
                              ':s1', ':s2', ':s3', ':s4', ':s5'
                          ], [
                  $inputsMapping[$imn],
                              't' . ($imn),
                  $lastStreamId,
                  't' . $imn,
                  's' . $stm
                          ], $option->getCommand());
          // We need to get a last stream id to apply next options in the correct order
          $lastStreamId = 's' . $stm;
          // Pass input paths to the separate array
          $this->setExtraInputStream($option->getExtraInputStream());

          $imn++;
        } elseif ($option instanceof OptionChromakey) {
          $this->_optionsPrepared[] =
              str_replace([
                              ':s1', ':s2', ':s3'
                          ], [
                              $lastStreamId,
                              $inputsMapping[$imn],
                              //$lastStreamId,
                              //'t' . $imn,
                              's' . $stm
                          ], $option->getCommand());
          // We need to get a last stream id to apply next options in the correct order
          $lastStreamId = 's' . $stm;
          // Pass input paths to the separate array
          $this->setExtraInputStream($option->getExtraInputStream());

          $imn++;
        } elseif ($option instanceof OptionAlphakey) {
          $this->_optionsPrepared[] =
              str_replace([
                              ':s1', ':s2', ':s3'
                          ], [
                              $lastStreamId,
                              $inputsMapping[$imn],
                            //$lastStreamId,
                            //'t' . $imn,
                              's' . $stm
                          ], $option->getCommand());
          // We need to get a last stream id to apply next options in the correct order
          $lastStreamId = 's' . $stm;
          // Pass input paths to the separate array
          $this->setExtraInputStream($option->getExtraInputStream());

          $imn++;
        } else {}

        $stm++;
        }
    }

    if(count($this->_optionsPrepared) > 0) {
      $commands = array_merge($inputs, [
          '-filter_complex',
          rtrim(implode(',', $this->_optionsPrepared), '[' . $lastStreamId . ']')
      ]);
    } else {
      $commands = [];
    }

    return $commands;
  }
}