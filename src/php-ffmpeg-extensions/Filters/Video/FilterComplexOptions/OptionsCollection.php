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

class OptionsCollection implements \Countable, \IteratorAggregate
{
  const TYPE_OVERLAY = 'Overlay';
  const TYPE_DRAWTEXT = 'DrawText';
  const TYPE_DRAWBOX = 'DrawBox';
  const TYPE_CHROMAKEY = 'Chromakey';

  private $_options;

  public function __construct(array $options = [])
  {
    $this->_options = array_values($options);
  }

  /**
   * Returns the first stream of the collection, null if the collection is
   * empty.
   *
   * @return null|OptionInterface
   */
  public function first()
  {
    $option = reset($this->_options);

    return $option ? : null;
  }

  /**
   * Adds an option to the collection.
   *
   * @param OptionInterface $option
   *
   * @return OptionsCollection
   */
  public function add(OptionInterface $option)
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
   * Returns options that has an extra input streams.
   * @return \ArrayIterator|\Traversable
   */
  public function filterHasExtraInputs()
  {
    return new OptionsCollection(array_filter((array)$this->getIterator(), function (OptionInterface $option) {
      if ($option instanceof OptionExtraInputStreamInterface) {
        return true;
      }
    }));
  }

  /**
   * Returns options filtered by type.
   *
   * @param string $typeName
   *
   * @return \ArrayIterator|\Traversable
   */
  public function filter($typeName)
  {
    switch ($typeName) {
      case self::TYPE_DRAWBOX:
      case self::TYPE_DRAWTEXT:
      case self::TYPE_OVERLAY:
      case self::TYPE_CHROMAKEY:
        return new OptionsCollection(array_filter((array)$this->getIterator(), [$this, '_filter' . ucfirst($typeName)]));
      default :
        throw new InvalidArgumentException('Invalid option type requested.');
    }
  }

  /**
   * {@inheritdoc}
   */
  private function _filterOverlay(OptionInterface $option)
  {
    if ($option instanceof OptionOverlay) {
      return true;
    }
  }

  /**
   * {@inheritdoc}
   */
  private function _filterDrawText(OptionInterface $option)
  {
    if ($option instanceof OptionDrawText) {
      return true;
    }
  }

  /**
   * {@inheritdoc}
   */
  private function _filterDrawBox(OptionInterface $option)
  {
    if ($option instanceof OptionDrawBox) {
      return true;
    }
  }

  /**
   * {@inheritdoc}
   */
  private function _filterChromakey(OptionInterface $option)
  {
    if ($option instanceof OptionChromakey) {
      return true;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator()
  {
    return new \ArrayIterator($this->_options);
  }

  /**
   * Returns options sorted by z-index.
   * @return \ArrayIterator|\Traversable
   */
  public function sortByZindex()
  {
    $optionsIterator = $this->getIterator();
    $optionsIterator->uasort(function ($a, $b) {
      return strnatcmp($a->getZIndex(), $b->getZIndex());
    });

    return $optionsIterator;
  }
}