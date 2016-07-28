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
  private $_inputs = [];
  private static $_instance = null;

  private function __construct()
  {
    // Protected constructor to prevent creating a new instance of the *Singleton* via the `new` operator from outside of this class.
  }

  protected function __clone()
  {
    // Private clone method to prevent cloning of the instance of the *Singleton* instance.
  }

  private function __wakeup()
  {
    // Private unserialize method to prevent unserializing of the *Singleton* instance.
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

/*
  protected $priority;

  protected $inputs = [];

  public function __construct($priority = 0)
  {
    $this->priority = $priority;
  }

  public function getPriority()
  {
    return $this->priority;
  }

  public function attachFile($file)
  {
    if (!file_exists($file)) {
      throw new InvalidArgumentException('Path ' . $file . ' is incorrect');
    }
    $this->inputs[] = $file;

    return $this;
  }

  '/home/givmfull/public_html/php-ffmpeg-extensions/examples/../ffmpeg-static/ffmpeg' '-y' '-i' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/demo_video_720p_HD.mp4' '-i' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/intro_720p_muted.mp4' '-filter_complex' '[0:v:0] [0:a:0] [1:v:0] [1:a:0] concat=n=2:v=1:a=1' '-threads' '12' '-vcodec' 'libx264' '-acodec' 'libmp3lame' '-b:v' '1000k' '-refs' '6' '-coder' '1' '-sc_threshold' '40' '-flags' '+loop' '-me_range' '16' '-subq' '7' '-i_qfactor' '0.71' '-qcomp' '0.6' '-qdiff' '4' '-trellis' '1' '-b:a' '128k' '-pass' '1' '-passlogfile' '/tmp/ffmpeg-passes5798bb2010799dkuzf/pass-5798bb201088a' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/../export-concatenated1.mp4'

  '/home/givmfull/public_html/php-ffmpeg-extensions/examples/../ffmpeg-static/ffmpeg' '-y' '-i' "concat:/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/greenscreen-test.mp4|/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/intro_720p_sound.mp4" -c copy '/home/givmfull/public_html/php-ffmpeg-extensions/examples/../export-concatenated1.mp4'


  Concat

  /home/givmfull/public_html/php-ffmpeg-extensions/examples/../ffmpeg-static/ffmpeg -i /home/givmfull/public_html/php-ffmpeg-extensions/examples/source/ezvc-video-marketing-spokesperson-transp.mp4 -c copy -bsf:v h264_mp4toannexb -f mpegts /home/givmfull/public_html/php-ffmpeg-extensions/examples/source/intermediate1.ts

  /home/givmfull/public_html/php-ffmpeg-extensions/examples/../ffmpeg-static/ffmpeg -i /home/givmfull/public_html/php-ffmpeg-extensions/examples/source/intro_720p_muted.mp4 -c copy -bsf:v h264_mp4toannexb -f mpegts /home/givmfull/public_html/php-ffmpeg-extensions/examples/source/intermediate2.ts


  /home/givmfull/public_html/php-ffmpeg-extensions/examples/../ffmpeg-static/ffmpeg -i "concat:/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/intermediate1.ts|/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/intermediate2.ts" -c copy -bsf:a aac_adtstoasc /home/givmfull/public_html/php-ffmpeg-extensions/examples/output.mp4

  public function apply(Video $video, VideoInterface $format)
  {
    $commands = $filterOptions = [];

    foreach ($this->inputs as $input) {
      $commands[] = '-i';
      $commands[] = $input;
    }

    for ($i = 0; $i <= count($this->inputs); $i++) {
      $filterOptions[] = sprintf('[%s:v:0]', $i);
      $filterOptions[] = sprintf('[%s:a:0]', $i);
    }

    $filterOptions[] = sprintf('concat=n=%s:v=1:a=1', (count($this->inputs) + 1));
    $commands[] = '-filter_complex';
    $commands[] = implode(" ", $filterOptions);

    return $commands;
  }
*/
}
