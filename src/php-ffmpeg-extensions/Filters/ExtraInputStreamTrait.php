<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters;

use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\FFProbe\DataMapping\StreamCollection;
use Sharapov\FFMpegExtensions\Input\FileInterface;
use Sharapov\FFMpegExtensions\FFProbe;

trait ExtraInputStreamTrait {

  /**
   * @var FileInterface
   */
  protected $_extraInputStream;

  /**
   * @param FileInterface $file
   *
   * @return $this
   */
  public function setExtraInputStream( FileInterface $file ) {
    $this->_extraInputStream = $file;

    return $this;
  }

  /**
   * @return FileInterface
   * @throws InvalidArgumentException
   */
  public function getExtraInputStream() {
    if ( ! $this->_extraInputStream instanceof FileInterface ) {
      throw new InvalidArgumentException( 'Extra input stream for ' . __CLASS__ . ' is undefined.' );
    }

    return $this->_extraInputStream;
  }

  /**
   * @return FFProbe
   */
  public function getProbe() {
    return FFProbe::getInstance();
  }

  /**
   * Get probe data of extra input stream.
   *
   * @return StreamCollection
   */
  public function getProbeData() {
    return $this->getProbe()->streams( $this->getExtraInputStream()->getPath() );
  }
}