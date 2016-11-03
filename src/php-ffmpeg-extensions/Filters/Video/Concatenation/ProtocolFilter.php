<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\Concatenation;

use Sharapov\FFMpegExtensions\Stream\FileInterface;

/**
 * Class ProtocolFilter
 * @package Sharapov\FFMpegExtensions\Filters\Video\Concatenation
 */
class ProtocolFilter
{
  private static $_instance = null;

  protected $_inputs = [];

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

  public function setInput(FileInterface $file)
  {
    array_push($this->_inputs, $file);

    return $this;
  }

  public function getInputs()
  {
    return $this->_inputs;
  }

  public function getInputsArray()
  {
    $inputs = [];
    foreach ($this->getInputs() as $input) {
      $inputs[] = $input->getFile();
    }
    return $inputs;
  }

  public function getCommand($commands = null)
  {
    if (is_null($commands)) {
      $commands = [];
    }
    if (!is_array($commands)) {
      $commands = [$commands];
    }

    $inputs = [];
    foreach ($this->getInputs() as $input) {
      $inputs[] = $input->getFile();
    }

    $commands[] = '-i';
    $commands[] = sprintf('concat:%s', implode("|", $inputs));

    return $commands;
  }

  /**
   * Return command string.
   * @return string
   */
  public function __toString()
  {
    return implode(" ", $this->getCommand());
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
