<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\Overlay;

// // /usr/local/bin/ffmpeg -i /home/givmfull/public_html/php-ffmpeg-extensions/examples/source/bg.png -i /home/givmfull/public_html/php-ffmpeg-extensions/examples/source/greenscreen-test.mp4  -filter_complex "[0:v]scale=640:240 [ovrl], [1:v][ovrl]overlay=23:35" /home/givmfull/public_html/php-ffmpeg-extensions/examples/output/chromakey.mp4

// '/usr/local/bin/ffmpeg' '-y' '-i' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/greenscreen-test.mp4' '-i' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/overlay_1.jpg' '-i' '/home/givmfull/public_html/php-ffmpeg-extensions/examples/source/overlay_2.jpg' '-filter_complex' '[1:v]scale=320:240[s1],[0:v][s1]overlay=23:23[out2],[2:v]scale=120:100[s2],[out2][s2]overlay=230:230:enable='\''between(t,12,16)'\''' '-vcodec' 'libx264' '-acodec' 'libmp3lame'    '-pass' '1'  '/home/givmfull/public_html/php-ffmpeg-extensions/examples/../export-2.mp4'

/**
 * Class Image
 * @package Sharapov\FFMpegExtensions\Filters\Video\Overlay
 */
class Image implements OverlayInterface
{
  protected $_imageFile;

  protected $_dimensions;

  protected $_coordinates;

  protected $_timeLine;

  /**
   * Image constructor.
   *
   * @param \Sharapov\FFMpegExtensions\Stream\FileInterface|null $file
   */
  public function __construct(\Sharapov\FFMpegExtensions\Stream\FileInterface $file = null)
  {
    if (!is_null($file)) {
      $this->setImageFile($file);
    }
  }

  /**
   * @param \Sharapov\FFMpegExtensions\Stream\FileInterface $file
   *
   * @return $this
   */
  public function setImageFile(\Sharapov\FFMpegExtensions\Stream\FileInterface $file)
  {
    $this->_imageFile = $file;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getImageFile()
  {
    return $this->_imageFile;
  }

  /**
   * @param \Sharapov\FFMpegExtensions\Coordinate\Dimension $dimension
   *
   * @return $this
   */
  public function setDimensions(\Sharapov\FFMpegExtensions\Coordinate\Dimension $dimension)
  {
    $this->_dimensions = $dimension;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getDimensions()
  {
    return $this->_dimensions;
  }

  /**
   * @param \Sharapov\FFMpegExtensions\Coordinate\Point $point
   *
   * @return $this
   */
  public function setCoordinates(\Sharapov\FFMpegExtensions\Coordinate\Point $point)
  {
    $this->_coordinates = $point;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getCoordinates()
  {
    return $this->_coordinates;
  }

  /**
   * @param \Sharapov\FFMpegExtensions\Coordinate\TimeLine $timeLine
   *
   * @return $this
   */
  public function setTimeLine(\Sharapov\FFMpegExtensions\Coordinate\TimeLine $timeLine)
  {
    $this->_timeLine = $timeLine;

    return $this;
  }

  /**
   * @return mixed
   */
  public function getTimeLine()
  {
    return $this->_timeLine;
  }

  /**
   * @return string
   */
  public function getCommand()
  {
    return '';
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->getCommand();
  }
}
