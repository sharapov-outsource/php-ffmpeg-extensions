<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;

use Sharapov\FFMpegExtensions\Coordinate\TimeLine;
use Sharapov\FFMpegExtensions\Input\FileInterface;

/**
 * Overlay filter option
 * @package Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions
 */
class OptionOverlay implements OptionInterface
{
  use TimeLineTrait;
  use CoordinatesTrait;
  use DimensionsTrait;

  protected $_overlayInput;

  /**
   * Set path to font file.
   *
   * @param $file
   *
   * @return $this
   */
  public function setOverlayInput(FileInterface $file)
  {
    $this->_overlayInput = $file;

    if (null === $streams = $this->getFFProbe()->streams($file->getPath())) {
      throw new RuntimeException(sprintf('Unable to probe "%s".', $file->getPath()));
    }

    return $this;
  }

  /**
   * Get path.
   *
   * @return mixed
   */
  public function getOverlayInput()
  {
    return $this->_overlayInput;
  }

  /**
   * Returns command string.
   *
   * @return string
   */
  public function getCommand()
  {
    $options = [
        sprintf("[%s]scale=%s[%s]", ':stream', (string)$this->getDimensions(), ':stream'),
    ];
    if ($this->getTimeLine() instanceof TimeLine) {
      $options[] = sprintf("[%s][%s]overlay:%s[%s]", ':stream', ':stream', (string)$this->getTimeLine(), ':stream');
    } else {
      $options[] = sprintf("[%s][%s]overlay[%s]", ':stream', ':stream', ':stream');
    }

    /*
     * '-i' '/home/ezmembersarea/public_html/app/video-templates/mov/personal-injury-attorney-male.mov'
     * '-i' '/home/ezmembersarea/public_html/app/library-templates/bg__581c9e4fcc123.png'
     * '-i' '/home/ezmembersarea/public_html/app//lower-thirds-graphics/90002-3_57f2a863ab2ab_57f2a863aba41.png'
     * '-i' '/home/ezmembersarea/public_html/app/video-templates/mov/personal-injury-attorney-male.mov'
     * '-filter_complex'
     * '[1:v]scale=1280:720[out1],[out1][0:v]overlay[out2],[2:v]scale=1280:720[s2],[out2][s2]overlay=0:0:enable='\''between(t,0,29)'\''[out3],
     * [out3]drawtext=fontfile=/home/ezmembersarea/public_html/app/assets/fonts/OpenSansBold/OpenSansBold.ttf:text='\''Profession\% sdfsdf'\'':fontcolor='\''#FFFFFF@1'\'':fontsize=38:x=30:y=574:enable='\''between(t,0,29)'\'',drawtext=fontfile=/home/ezmembersarea/public_html/app/assets/fonts/OpenSansBold/OpenSansBold.ttf:text='\''http\://website.com'\'':fontcolor='\''#FFFFFF@1'\'':fontsize=38:x=580:y=574:enable='\''between(t,0,29)'\''' '-map' '0:v' '-map' '3:a' '-threads' '12' '-vcodec' 'libx264' '-acodec' 'aac' '-b:v' '1000k' '-refs' '6' '-coder' '1' '-sc_threshold' '40' '-flags' '+loop' '-me_range' '16' '-subq' '7' '-i_qfactor' '0.71' '-qcomp' '0.6' '-qdiff' '4' '-trellis' '1' '-r' '25' '-to' '26.793' '-b:a' '128k' '-pass' '2' '-passlogfile' '/tmp/ffmpeg-passes585959f866965372mp/pass-585959f866a65' './data/tmp/main_282.mp4'
     */



    return implode(",", $options);
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
