<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Audio;

use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Format\AudioInterface;
use Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions\OptionsCollection;
use Sharapov\FFMpegExtensions\Input\FileInterface;
use Sharapov\FFMpegExtensions\Media\Audio;

class MergeFilter implements AudioFilterInterface {
  const STEREO_2_MONO = 10;
  const MONO_2_STEREO = 20;
  const ALL_2_STEREO = 30;
  const MUX_STEREO = 40;

  /**
   * @var \Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions\OptionsCollection
   */
  private $_optionsCollection;

  private $_optionsPrepared;

  private $_extraInputs = [];

  private $_action;

  /** @var integer */
  private $priority;

  /**
   * MergeFilter constructor.
   *
   * @param \Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions\OptionsCollection|null $optionsCollection
   * @param int                                                                                $action
   */
  public function __construct( OptionsCollection $optionsCollection = null, $action = self::STEREO_2_MONO ) {
    if ( $optionsCollection instanceof OptionsCollection ) {
      $this->setOptionsCollection( $optionsCollection );
    }

    $this->setAction( $action );
  }

  /**
   * @return mixed
   */
  public function getAction() {
    return $this->_action;
  }

  /**
   * @param $action
   *
   * @return $this
   */
  public function setAction( $action ) {
    switch ( $action ) {
      case self::STEREO_2_MONO:
        $this->_action = $action;
        break;
      case self::MONO_2_STEREO:
        $this->_action = $action;
        break;
      case self::ALL_2_STEREO:
        $this->_action = $action;
        break;
      case self::MUX_STEREO:
        $this->_action = $action;
        break;
      default :
        throw new InvalidArgumentException( 'Invalid action type requested.' );
    }

    return $this;
  }

  /**
   * @return mixed
   */
  public function getOptionsCollection() {
    return $this->_optionsCollection;
  }

  /**
   * @param \Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions\OptionsCollection $optionsCollection
   *
   * @return $this
   */
  public function setOptionsCollection( OptionsCollection $optionsCollection ) {
    $this->_optionsCollection = $optionsCollection;

    return $this;
  }

  /**
   * @return array
   */
  public function getExtraInputs() {
    return $this->_extraInputs;
  }

  /**
   * @param \Sharapov\FFMpegExtensions\Input\FileInterface $file
   *
   * @return $this
   */
  public function setExtraInput( FileInterface $file ) {
    $this->_extraInputs[] = '-i';
    $this->_extraInputs[] = $file->getPath();

    return $this;
  }

  /**
   * @return int
   */
  public function getPriority() {
    return $this->priority;
  }

  /**
   * @param \Sharapov\FFMpegExtensions\Media\Audio $audio
   * @param \FFMpeg\Format\AudioInterface          $format
   *
   * @return array
   */
  public function apply( Audio $audio, AudioInterface $format ) {
    $commands     = $inputsMapping = $inputs = [];
    $lastStreamId = null;

    if ( $this->getOptionsCollection() ) {
      $firstStreamId = '0:a';
      $lastStreamId  = $firstStreamId;

      // Detect all additional inputs numbers
      for (
        $i = 0; $i <= $this
        ->getOptionsCollection()
        ->filterHasExtraInputs()
        ->count(); $i ++
      ) {
        $inputsMapping[] = sprintf( '%s:a', $i );
      }
    }

    switch ( $this->_action ) {
      case self::STEREO_2_MONO:
        $commands[] = '-ac';
        $commands[] = '1';
        break;
      case self::MONO_2_STEREO:
        $imn = 1;
        $stm = 1;
        if ( $this->getOptionsCollection() ) {
          foreach ( $this->getOptionsCollection() as $option ) {
            /** @var \Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions\OptionAudioFile $option */
            $this->_optionsPrepared[] =
              str_replace( [
                             ':s1',
                             ':s2',
                             ':s3'
                           ], [
                             $lastStreamId,
                             $inputsMapping[ $imn ],
                             's' . $stm,
                           ], $option->getCommandMono2Stereo() );

            $lastStreamId = 's' . $stm;
            $stm ++;

            // Pass input paths to the separate array
            $this->setExtraInput( $option->getExtraInputStream() );
          }
        }
        break;
      case self::ALL_2_STEREO:
        $commands[] = '-ac';
        $commands[] = '2';
        break;
      case self::MUX_STEREO:
        $imn = 1;
        $stm = 1;
        if ( $this->getOptionsCollection() ) {
          foreach ( $this->getOptionsCollection() as $option ) {
            /** @var \Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions\OptionAudioFile $option */
            $this->_optionsPrepared[] =
              str_replace( [
                             ':s1',
                             ':s2',
                             ':s3'
                           ], [
                             $inputsMapping[ $imn ],
                             $lastStreamId,
                             's' . $stm,
                           ], $option->getCommandMuxStereo() );

            $lastStreamId = 's' . $stm;
            $stm ++;

            // Pass input paths to the separate array
            $this->setExtraInput( $option->getExtraInputStream() );
          }
        }
        break;
      default :
    }

    if ( count( $this->_optionsPrepared ) > 0 ) {
      $commands = array_merge( $inputs, [
        '-filter_complex',
        rtrim( implode( ',', $this->_optionsPrepared ), '[' . $lastStreamId . ']' )
      ] );
    }

    print_r( $commands );

    return $commands;
  }
}