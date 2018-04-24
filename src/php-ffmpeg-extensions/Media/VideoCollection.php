<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Media;

/**
 * Video collection
 * @package Sharapov\FFMpegExtensions\Media
 */
class VideoCollection implements CollectionInterface, \Countable, \IteratorAggregate {
  private $_streams;

  public function __construct( array $options = [] ) {
    $this->_streams = array_values( $options );
  }

  /**
   * Returns the first video stream of the collection, null if the collection is
   * empty.
   *
   * @return null|Video
   */
  public function first() {
    $option = reset( $this->_streams );

    return $option ? : null;
  }

  /**
   * Adds a video stream to the collection.
   *
   * @param Video $stream
   *
   * @return VideoCollection
   */
  public function add( Video $stream ) {
    $this->_streams[] = $stream;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function count() {
    return count( $this->_streams );
  }

  /**
   * Returns the array of contained options.
   *
   * @return array
   */
  public function all() {
    return $this->_streams;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator() {
    return new \ArrayIterator( $this->_streams );
  }
}