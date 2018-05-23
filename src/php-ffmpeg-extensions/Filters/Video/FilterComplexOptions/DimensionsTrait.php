<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use Sharapov\FFMpegExtensions\Coordinate\Dimension;
use FFMpeg\Exception\InvalidArgumentException;

trait DimensionsTrait {

  protected $_dimensions;

  /**
   * Returns dimensions object.
   *
   * @param bool $returnDefault
   *
   * @return \Sharapov\FFMpegExtensions\Coordinate\Dimension|string
   */
  public function getDimensions($returnDefault = false) {
    return ($this->_dimensions instanceof Dimension) ? $this->_dimensions : (($returnDefault) ? : $this->getDefaultDimensions());
  }

  /**
   * Returns coordinates object.
   *
   * @param Dimension $dimension
   *
   * @return mixed
   */
  public function setDimensions(Dimension $dimension) {
    $this->_dimensions = $dimension;

    return $this;
  }

  /**
   * Gets default dimensions
   * @return string
   */
  public function getDefaultDimensions() {
    if($this->isImage()) {
      $dimensions = @getimagesize($this->getExtraInputStream()->getPath());
      if($dimensions) {
        return sprintf('%s:%s', $dimensions[0], $dimensions[1]);
      }
    } else {
      throw new InvalidArgumentException('Due to performance issues we cannot automatically detect non-image overlay dimensions. Please set them before encoding.');
    }

    return '0:0';
  }
}