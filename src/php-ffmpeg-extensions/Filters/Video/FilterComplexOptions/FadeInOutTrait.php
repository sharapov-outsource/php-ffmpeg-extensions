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

trait FadeInOutTrait {

  protected $_fadeInSeconds;

  protected $_fadeOutSeconds;

  /**
   * Returns fade-in time in seconds.
   * @return mixed
   */
  public function getFadeIn() {
    return $this->_fadeInSeconds;
  }

  /**
   * Set fade-in time in seconds.
   *
   * @param mixed $seconds
   *
   * @return $this
   */
  public function setFadeIn( $seconds ) {
    if ( ! is_numeric( $seconds ) || $seconds < 0 ) {
      throw new InvalidArgumentException( 'Fade-in time should be positive integer or float value. ' . $seconds . ' given.' );
    }

    $this->_fadeInSeconds = preg_replace( '/,/', '.', $seconds );

    return $this;
  }

  /**
   * Returns fade-out time in seconds.
   * @return mixed
   */
  public function getFadeOut() {
    return $this->_fadeOutSeconds;
  }

  /**
   * Set fade-out time in seconds.
   *
   * @param mixed $seconds
   *
   * @return $this
   * @throws InvalidArgumentException
   */
  public function setFadeOut( $seconds ) {
    if ( ! is_numeric( $seconds ) || $seconds < 0 ) {
      throw new InvalidArgumentException( 'Fade-out time should be positive integer or float value. ' . $seconds . ' given.' );
    }

    $this->_fadeOutSeconds = preg_replace( '/,/', '.', $seconds );

    return $this;
  }
}