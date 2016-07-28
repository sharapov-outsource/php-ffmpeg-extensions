<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\Concatenation;

use FFMpeg\Exception\InvalidArgumentException;

class VideoFilter
{
  private static $_instance = null;
  private $_inputs = [];

  private function __construct()
  {
    // Protected constructor to prevent creating a new instance of the *Singleton* via the `new` operator from outside of this class.
  }

  static public function init()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }

    return self::$_instance;
  }

  public function join($file)
  {
    if (!file_exists($file)) {
      throw new InvalidArgumentException('Path ' . $file . ' is incorrect');
    }
    $this->_inputs[] = $file;

    return $this;
  }

  public function getFiles()
  {
    return $this->_inputs;
  }

  protected function __clone()
  {
    // Private clone method to prevent cloning of the instance of the *Singleton* instance.
  }

  private function __wakeup()
  {
    // Private unserialize method to prevent unserializing of the *Singleton* instance.
  }
}
