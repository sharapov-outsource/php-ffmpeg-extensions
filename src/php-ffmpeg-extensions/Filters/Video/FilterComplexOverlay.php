<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video;

use FFMpeg\Filters\Video\VideoFilterInterface;
use FFMpeg\Media\Video;
use FFMpeg\Format\VideoInterface;
use Sharapov\FFMpegExtensions\Coordinate\Point;
use Sharapov\FFMpegExtensions\Coordinate\TimeLine;
use Sharapov\FFMpegExtensions\Filters\Video\Overlay\ColorKey;
use Sharapov\FFMpegExtensions\Filters\Video\Overlay\Image;
use Sharapov\FFMpegExtensions\Filters\Video\Overlay\OverlayInterface;
use Sharapov\FFMpegExtensions\Coordinate\Dimension;
use FFMpeg\Exception\InvalidArgumentException;
use Sharapov\FFMpegExtensions\Filters\Video\Overlay\Text;
use Sharapov\FFMpegExtensions\Filters\Video\Overlay\Box;

class FilterComplexOverlay implements VideoFilterInterface
{
  /** @var integer */
  protected $priority;

  protected $imageOverlay = array();

  protected $textOverlay = array();

  protected $boxOverlay = array();

  protected $colorKeyFilter;

  protected $inputs = array();

  public function __construct($priority = 0)
  {
    $this->priority = $priority;
  }

  /**
   * {@inheritdoc}
   */
  public function getPriority()
  {
    return $this->priority;
  }

  /**
   * {@inheritdoc}
   */
  public function addFilter(OverlayInterface $filter)
  {
    if($filter instanceof ColorKey) {

      if($filter->getImageFile() == null) {
        throw new InvalidArgumentException('Filter "ColorKey" error: incorrect path');
      }

      if( ! $filter->getDimensions() instanceof Dimension) {
        throw new InvalidArgumentException('Filter "ColorKey" error: incorrect dimensions');
      }

      $this->colorKeyFilter = $filter;
      $this->inputs[] = $filter->getImageFile();
      
    } elseif ($filter instanceof Image) {

      if($filter->getImageFile() == null) {
        throw new InvalidArgumentException('Filter "Image" error: incorrect path');
      }

      if( ! $filter->getDimensions() instanceof Dimension) {
        throw new InvalidArgumentException('Filter "Image" error: incorrect dimensions');
      }

      $this->imageOverlay[] = $filter;
      $this->inputs[] = $filter->getImageFile();
    } elseif ($filter instanceof Text) {
      $this->textOverlay[] = $filter;
    } elseif ($filter instanceof Box) {
      $this->boxOverlay[] = $filter;
    } else {
      throw new InvalidArgumentException('Unsupported filter requested. Only ColorKey, Image, Text, Box are supported.');
    }
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public function apply(Video $video, VideoInterface $format)
  {
    $commandString = array();
    // Compile color key command
    if($this->colorKeyFilter instanceof ColorKey) {
      $commandString[] = sprintf('[0:v]colorkey=%s[sck]', $this->colorKeyFilter->getColor());
      // Color key background input is always the first stream
      $commandString[] = sprintf('[1:v]scale=%s[out1]', $this->colorKeyFilter->getDimensions());
      $commandString[] = sprintf('[out1][sck]overlay%s', ((count($this->imageOverlay) > 0 or count($this->textOverlay) > 0)?'[out2]':''));

      $filterNumStart = 2;
    } else {
      $filterNumStart = 1;
    }

    // Compile other filters commands
    foreach ($this->imageOverlay as $k => $filter) {
        $commandString[] = sprintf('[%s:v]scale=%s[s%s]', $filterNumStart, $filter->getDimensions(), $filterNumStart);
        if($filterNumStart == 1) {
          $cmd = '[0:v]';
        } else {
          $cmd = sprintf('[out%s]', $filterNumStart);
        }
        $cmd.= sprintf('[s%s]overlay=', $filterNumStart);

        // Image position
        if($filter->getCoordinates() instanceof Point) {
          $cmd.= $filter->getCoordinates();
        } else {
          $cmd.= "0:0";
        }

        // Image overlay timings
        if($filter->getTimeLine() instanceof TimeLine) {
          $cmd.= sprintf(":enable='between(t,%s)'", $filter->getTimeLine());
        }

        if (isset($this->imageOverlay[($k + 1)]) or count($this->textOverlay) > 0) {
          $cmd.= sprintf("[out%s]", ($filterNumStart + 1));
        }
        $commandString[] = $cmd;

      $filterNumStart++;
    }

    // Compile drawtext filters
    if(count($this->textOverlay) > 0) {
      $cmd = sprintf("[out%s]%s", $filterNumStart, implode(",",$this->textOverlay));
      if (count($this->boxOverlay) > 0) {
        $cmd.= sprintf("[out%s]", ($filterNumStart + 1));
      }
      $commandString[] = $cmd;
      $filterNumStart++;
    }

    // Compile drawbox filters
    if(count($this->boxOverlay) > 0) {
      $commandString[] = sprintf("[out%s]%s", $filterNumStart, implode(",",$this->boxOverlay));
    }
    
    print_r($commandString);

//    die;

    /*

     */

    /*
     *
   [0:v]colorkey=0x3BBD1E:0.3:0.2[sck]
   [1:v]scale=1280:720[s0]
    [2:v]scale=320:240[s2]
    [3:v]scale=120:100[s3]
   [s0][sck]overlay[so1]
   [so1][s3]overlay=23:23:enable='between(t,1,10)'[so3]
   [so2][s4]overlay=230:230:enable='between(t,12,16)'[so4]

 [0:v]colorkey=0x3BBD1E:0.3:0.2[sck]
    [1:v]scale=1280:720[out1]
    [2:v]scale=320:240[s2]
    [3:v]scale=120:100[s3]
    [out1][sck]overlay[out2]

   [out2][s2]overlay=23:23:enable='between(t,1,10)'[out3]

   [out3][s3]overlay=230:230:enable='between(t,12,16)'[out4]

     *
     * [0:v]colorkey=0x3BBD1E:0.3:0.2[in0],
     * [1:v]scale=1280:720[in1],
     * [2:v]scale=320:120[in2],
     * [3:v]scale=120:70[in3],
     * [in1][in0]overlay[out1],
     * [out1][in2]overlay=10:10:enable='between(t,1,2)'[out2],
     * [out2][in3]overlay=200:20:enable='between(t,5,8)'
     *
     *
     /usr/local/bin/ffmpeg
    -i source/greenscreen-test.mp4
    -i source/bg.png
    -i source/overlay_1.jpg
    -i source/overlay_2.jpg
    -filter_complex "
    [0:v]colorkey=0x3BBD1E:0.3:0.2[in0],
    [1:v]scale=1280:720[in1],
    [2:v]scale=320:120[in2],
    [3:v]scale=120:70[in3],
    [in1][in0]overlay[out1],
    [out1][in2]overlay=10:10:enable='between(t,1,2)'[out2],
    [out2][in3]overlay=200:20:enable='between(t,5,8)'"
    export-121.mp4


    '/usr/local/bin/ffmpeg' '-y'
    '-i' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/greenscreen-test.mp4'
    '-i' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/overlay_1.jpg'
    '-i' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/overlay_2.jpg'
    '-i' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/bg.png'
    '-filter_complex' '
    [0:v]colorkey=0x3BBD1E:0.3:0.2[sck],
    [1:v]scale=1280:720[out1],
    [out1][sck]overlay[out2],
    [2:v]scale=320:240[s2],
    [out2][s2]overlay=23:23:enable='\''between(t,1,10)'\''[out3],
    [3:v]scale=120:100[s3],
    [out3][s3]overlay=230:230:enable='\''between(t,12,16)'\''[out4]'

    '-threads' '12' '-vcodec' 'libx264' '-acodec' 'libmp3lame' '-b:v' '1000k' '-refs' '6' '-coder' '1' '-sc_threshold' '40' '-flags' '+loop' '-me_range' '16' '-subq' '7' '-i_qfactor' '0.71' '-qcomp' '0.6' '-qdiff' '4' '-trellis' '1' '-b:a' '128k' '-pass' '1' '-passlogfile' '/tmp/ffmpeg-passes577c0d848307b1fd16/pass-577c0d84831e2' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/../export-12.mp4' [] []

     */

    print_r($this->inputs);

    $commands = array();

    foreach ($this->inputs as $input) {
      $commands[] = '-i';
      $commands[] = $input;
    }

    $commands[] = '-filter_complex';
    $commands[] = implode(",", $commandString);
    return $commands;
  }
}
