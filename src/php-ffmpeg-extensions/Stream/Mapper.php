<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Stream;
use FFMpeg\Media\AbstractStreamableMedia;
use FFMpeg\Media\Audio;

/**
 * Stream Mapper
 * @package Sharapov\FFMpegExtensions\Stream
 */
class Mapper
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

  public function setInput(AbstractStreamableMedia $file)
  {
    array_push($this->_inputs, $file);

    return $this;
  }

  public function getInputs()
  {
    return $this->_inputs;
  }

  public function getCommand($commands = null)
  {
    if (is_null($commands)) {
      $commands = [];
    }
    if (!is_array($commands)) {
      $commands = [$commands];
    }

    // Put input files
    foreach ($this->getInputs() as $i => $input) {
      $commands[] = '-i';
      $commands[] = $input->getPathFile();
    }

    // Put command string
    foreach ($this->getInputs() as $i => $input) {
      if ($input instanceof \Sharapov\FFMpegExtensions\Media\Video) {
        $commands[] = '-map';
        $commands[] = sprintf("%s:v", $i);
        foreach ($input->getFileObject()->getMappedAudioStreams() as $audioStream) {
          $commands[] = '-map';
          $commands[] = sprintf('%s:a:%s', $i, $audioStream);
        }
      } elseif ($input instanceof \FFMpeg\Media\Audio) {
        $commands[] = '-map';
        $commands[] = sprintf("%s:a", $i);
      }

      // -metadata:s:a:0 language=eng

      print '<pre>';
      print_r($input->getFileObject()->getTitle());
      print '</pre>';
    }

    // Put metadata
    foreach ($this->getInputs() as $i => $input) {
     // if ($input instanceof \FFMpeg\Media\Audio) {
        if($meta = $input->getFileObject()->getMetadata() and $meta !== null) {
          foreach ($meta as $key => $value) {
            $commands[] = sprintf("-metadata:s:a:%s", $i);
            $commands[] = sprintf("%s=%s", $key, $value);
          }
        }


        /*if()

        $commands[] = ''*/
      //}
    }

    print '<pre>';
    print_r($commands);
    print '</pre>';

    //die;
    //  '/home/givmfull/public_html/php-ffmpeg-extensions/examples/../ffmpeg-static/ffmpeg' '-y' '-i' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/GREEN_SCREEN_TEST-marketing-spokesperson.mp4' '-map' '0:v' '-map' '0:a:0' '-i' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/forest_sound.mp3' '-map' '1:a' '-codec' 'copy' '-shortest' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/output/export-concatProtocol.mp4'

    /*
     * ffmpeg -i video.mkv -i audio.mp3 -map 0:v -map 0:a:0 -map 1:a \
-metadata:s:a:0 language=eng -metadata:s:a:1 language=sme -codec copy \
-shortest output.mkv
     */

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