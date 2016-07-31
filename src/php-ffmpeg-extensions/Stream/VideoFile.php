<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Stream;

use FFMpeg\Exception\InvalidArgumentException;

/**
 * Class VideoFile
 * @package Sharapov\FFMpegExtensions\Stream
 */
class VideoFile extends File implements FileInterface
{
  protected $_audioStreams = [];

  public function __construct($file = null, $audioStreams = null)
  {
    parent::__construct($file);

    if (!is_null($audioStreams)) {
      if (!is_array($audioStreams)) {
        $audioStreams = [$audioStreams];
      }
      foreach ($audioStreams as $audioStream) {
        $this->mapAudioStream($audioStream);
      }
    }
  }

  public function setFile($file)
  {
    if (!file_exists($file)) {
      throw new InvalidArgumentException('Incorrect video file path.');
    }

    $this->_file = $file;

    return $this;
  }

  public function mapAudioStream($number)
  {
    if (!is_integer($number)) {
      throw new InvalidArgumentException('Audio stream number should be positive integer');
    }

    if (!in_array($number, $this->_audioStreams)) {
      array_push($this->_audioStreams, $number);
    }

    return $this;
  }

  public function getMappedAudioStreams()
  {
    return $this->_audioStreams;
  }
}