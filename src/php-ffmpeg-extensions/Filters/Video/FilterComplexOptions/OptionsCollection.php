<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

class OptionsCollection implements \Countable, \IteratorAggregate
{
  private $_options;

  public function __construct(array $options = [])
  {
    $this->_options = array_values($options);
  }

  /**
   * Returns the first stream of the collection, null if the collection is
   * empty.
   *
   * @return null|OptionsInterface
   */
  public function first()
  {
    $option = reset($this->_options);

    return $option ? : null;
  }

  /**
   * Adds an option to the collection.
   *
   * @param OptionsInterface $option
   *
   * @return OptionsCollection
   */
  public function add(OptionsInterface $option)
  {
    $this->_options[] = $option;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function count()
  {
    return count($this->_options);
  }

  /**
   * Returns the array of contained options.
   *
   * @return array
   */
  public function all()
  {
    return $this->_options;
  }

  /**
   * Returns the imploded command string of contained options.
   *
   * @param string $separator
   *
   * @return array
   */
  //public function getCommand($separator = ',')
  //{
    //return implode($separator, $this->_options);
  //}

  /**
   * {@inheritdoc}
   */
  public function getIterator()
  {
    return new \ArrayIterator($this->_options);
  }

  /**
   * Returns the imploded command string of contained options.
   *
   * @param string $separator
   * @param bool $sortZIndex
   *
   * @return array
   */
  public function getCommand($separator = ',', $sortZIndex = true)
  {
    $optionsIterator = $this->getIterator();
    if($sortZIndex) {
      $optionsIterator->uasort(function ($a, $b) {
        return strnatcmp($a->getZIndex(), $b->getZIndex());
      });
    }
    return implode($separator, (array)$optionsIterator);
  }

  /**
   * Returns a command string.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->getCommand();
  }
}