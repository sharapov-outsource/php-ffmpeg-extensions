<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Stream;

/**
 * Class MetaData
 * @package Sharapov\FFMpegExtensions\Stream
 */
class MetaData implements \Countable, \IteratorAggregate
{
  private $_sorted;
  private $_meta = [];

  /**
   * Set value.
   *
   * @param $key
   * @param $value
   *
   * @return $this
   */
  public function add($key, $value)
  {
    $this->_meta[$key] = $value;
    $this->_sorted = null;

    return $this;
  }

  /**
   * Get values.
   *
   * @param null $key
   *
   * @return array|mixed|null
   */
  public function get($key = null)
  {
    if (is_null($key)) {
      return $this->_meta;
    }
    if (isset($this->_meta[$key])) {
      return $this->_meta[$key];
    }

    return null;
  }

  /**
   * {@inheritdoc}
   */
  public function count()
  {
    if (0 === count($this->_meta)) {
      return 0;
    }

    return count(call_user_func_array('array_merge', $this->_meta));
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator()
  {
    if (null === $this->_sorted) {
      if (0 === count($this->_meta)) {
        $this->_sorted = $this->_meta;
      } else {
        krsort($this->_meta);
        $this->_sorted = call_user_func_array('array_merge', $this->_meta);
      }
    }

    return new \ArrayIterator($this->_sorted);
  }
}