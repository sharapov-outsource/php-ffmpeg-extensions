<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\Overlay;

use Sharapov\FFMpegExtensions\Coordinate\Point;
use FFMpeg\Exception\InvalidArgumentException;
use Sharapov\FFMpegExtensions\Coordinate\Dimension;
use Sharapov\FFMpegExtensions\Coordinate\TimeLine;

// // /usr/local/bin/ffmpeg -i /home/givmfull/public_html/php-ffmpeg-extensions/examples/source/bg.png -i /home/givmfull/public_html/php-ffmpeg-extensions/examples/source/greenscreen-test.mp4  -filter_complex "[0:v]scale=640:240 [ovrl], [1:v][ovrl]overlay=23:35" /home/givmfull/public_html/php-ffmpeg-extensions/examples/output/chromakey.mp4

// '/usr/local/bin/ffmpeg' '-y' '-i' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/greenscreen-test.mp4' '-i' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/overlay_1.jpg' '-i' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/overlay_2.jpg' '-filter_complex' '[1:v]scale=320:240[s1],[0:v][s1]overlay=23:23[out2],[2:v]scale=120:100[s2],[out2][s2]overlay=230:230:enable='\''between(t,12,16)'\''' '-vcodec' 'libx264' '-acodec' 'libmp3lame'    '-pass' '1'  '/home/givmfull/public_html/php-ffmpeg-extensions/examples/../export-2.mp4'



class Image implements OverlayInterface
{
  protected $imageFile;

  protected $dimensions;

  protected $coordinates;

  protected $timeLine;

  public function __construct($file = null)
  {
    if($file != null) {
      $this->setImageFile($file);
    }
  }

  public function setImageFile($file)
  {
    if (!file_exists($file)) {
      throw new InvalidArgumentException('Incorrect image path.');
    }
    $this->imageFile = $file;
    return $this;
  }

  public function getImageFile()
  {
    return $this->imageFile;
  }

  public function setDimensions(Dimension $dimension)
  {
    $this->dimensions = $dimension;
    return $this;
  }

  public function getDimensions()
  {
    return $this->dimensions;
  }

  public function setCoordinates(Point $point)
  {
    $this->coordinates = $point;
    return $this;
  }

  public function getCoordinates()
  {
    return $this->coordinates;
  }

  public function setTimeLine(TimeLine $timeLine)
  {
    $this->timeLine = $timeLine;
    return $this;
  }

  public function getTimeLine()
  {
    return $this->timeLine;
  }

  public function getCommand()
  {
    return '';
  }

  public function __toString()
  {
    return $this->getCommand();
  }
}
