<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use FFMpeg\Exception\InvalidArgumentException;
use Sharapov\FFMpegExtensions\Input\FileInterface;

trait ExtraInputStreamTrait {

  protected $_extraInputStream;

  /**
   * Set path to font file.
   *
   * @param $file
   *
   * @return $this
   */
  public function setExtraInputStream(FileInterface $file)
  {
    $this->_extraInputStream = $file;
    return $this;
  }

  /**
   * Get path.
   *
   * @return mixed
   */
  public function getExtraInputStream()
  {
    if (!$this->_extraInputStream instanceof FileInterface) {
      throw new InvalidArgumentException('Extra input stream for '.__CLASS__.' is undefined.');
    }

    return $this->_extraInputStream;
  }
}