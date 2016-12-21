<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video;

use FFMpeg\Format\VideoInterface;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionDrawText;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionDrawBox;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionsCollection;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionInterface;
use Sharapov\FFMpegExtensions\Media\Video;

class ComplexFilter implements VideoFilterInterface {

  private $_optionsCollection;

  /** @var integer */
  private $priority;

  /**
   * {@inheritdoc}
   */
  public function __construct(OptionsCollection $optionsCollection = null) {
    if($optionsCollection instanceof OptionsCollection) {
      $this->setOptionsCollection($optionsCollection);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getOptionsCollection()
  {
    return $this->_optionsCollection;
  }

  /**
   * {@inheritdoc}
   */
  public function setOptionsCollection(OptionsCollection $optionsCollection)
  {
    $this->_optionsCollection = $optionsCollection;
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
  public function apply(Video $video, VideoInterface $format)
  {
    print '<pre>';
    print_r($video);
    print '</pre>';
    $commands = [
          '-filter_complex',
    ];

    $inputs = [

    ];

    $filterComplexOptions = [
       '[0:v]'
    ];

    if($optionsOverlay = $this->getOptionsCollection()->filter('Overlay') and $optionsOverlay->count() > 0) {
      foreach ($optionsOverlay as $option) {
        print '<pre>';
        print_r((string)$option);
        print '</pre>';
        $inputs[] = '-i';
        $inputs[] = $option->getOverlayInput()->getPath();
      }

    }

    if($optionsDrawText = $this->getOptionsCollection()->filter('DrawText') and $optionsOverlay->count() > 0) {
      foreach ($optionsDrawText as $option) {
        $filterComplexOptions[] = (string)$option;
      }
    }

    print '<pre>';
    print_r($inputs);
    print '</pre>';

    print '<pre>';
    print_r($filterComplexOptions);
    print '</pre>';


    //print_r($this->_optionsCollection->getCommand());

    /*
    '/home/ezmembersarea/videoapp/app/module/RenderEngine/FFmpegStatic/ffmpeg' '-y' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/demo_video_720p_HD.mp4' '-filter_complex' '[0:v]drawtext=fontfile=/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/OpenSansRegular.ttf:text='\''default tex1t1'\'':fontsize=33:x=430:y=150,drawtext=fontfile=/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/OpenSansRegular.ttf:text='\''text'\'':fontsize=33:x=230:y=150' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/output/output.mp4'

    */

    /*
     * '/home/ezmembersarea/videoapp/app/module/RenderEngine/FFmpegStatic/ffmpeg' '-y' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/demo_video_720p_HD.mp4' '-threads' '12' '-vcodec' 'libx264' '-acodec' 'libmp3lame' '-filter_complex' '[%si:v]scale=120:60[vOut%so],[vOut%so][%si:v]overlay:enable='\''between(t,1,6)'\''[vOut%so],drawtext=fontfile=/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/OpenSansRegular.ttf:text='\''http\://www.com This is the @ default text'\'':fontcolor='\''#ffffff@1'\'':fontsize=33:x=230:y=150:enable='\''between(t,1,6)'\''' '-map' '[vOut%so]' '-b:v' '1000k' '-refs' '6' '-coder' '1' '-sc_threshold' '40' '-flags' '+loop' '-me_range' '16' '-subq' '7' '-i_qfactor' '0.71' '-qcomp' '0.6' '-qdiff' '4' '-trellis' '1' '-b:a' '128k'
     */
    die;


    return $commands;
  }
/*
  private function _getDrawTextCommand()
  {
    return new OptionsCollection(array_filter((array)$this->_optionsCollection->getIterator(), function (OptionInterface $option) {
      if($option instanceof OptionDrawText) {
        return true;
      }
    }));
  }

  private function _getDrawBoxCommand()
  {
    return new OptionsCollection(array_filter((array)$this->_optionsCollection->getIterator(), function (OptionInterface $option) {
      if($option instanceof OptionDrawBox) {
        return true;
      }
    }));
  }*/
}
/*
'/home/ezmembersarea/videoapp/app/module/RenderEngine/FFmpegStatic/ffmpeg' '-y' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/demo_video_720p_HD.mp4' '-threads' '12' '-vcodec' 'libx264' '-acodec' 'libmp3lame' '-filter_complex' 'drawtext=fontfile=/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/arial.ttf:text='\''This is the default text'\'':fontcolor='\''#ffffff@1'\'':fontsize=33:x=230:y=150:enable='\''between(t,1,6)'\'',drawtext=fontfile=/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/arial.ttf:text='\''This is the default text 2'\'':fontcolor='\''#ffffff@1'\'':fontsize=33:x=130:y=450:enable='\''between(t,1,6)'\'',drawbox=34:67:#ffffff@0.4:t=max:230:450:enable='\''between(t,4,8)'\''' '-b:v' '1000k' '-refs' '6' '-coder' '1' '-sc_threshold' '40' '-flags' '+loop' '-me_range' '16' '-subq' '7' '-i_qfactor' '0.71' '-qcomp' '0.6' '-qdiff'*/