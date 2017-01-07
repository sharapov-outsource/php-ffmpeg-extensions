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
use Sharapov\FFMpegExtensions\Filters\ExtraInputStreamInterface;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionChromakey;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionDrawText;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionDrawBox;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionOverlay;
use Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionsCollection;
use Sharapov\FFMpegExtensions\Input\FileInterface;
use Sharapov\FFMpegExtensions\Media\Video;

class ComplexFilter implements ExtraInputStreamInterface, VideoFilterInterface
{
  private $_optionsCollection;

  private $_optionsPrepared;

  private $_extraInputStreams = [];

  /** @var integer */
  private $priority;

  /**
   * {@inheritdoc}
   */
  public function __construct(OptionsCollection $optionsCollection = null)
  {
    if ($optionsCollection instanceof OptionsCollection) {
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
  public function getExtraInputStreams()
  {
    return $this->_extraInputStreams;
  }

  /**
   * {@inheritdoc}
   */
  public function setExtraInputStream(FileInterface $file)
  {
    $this->_extraInputStreams[] = '-i';
    $this->_extraInputStreams[] = $file->getPath();
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
    $firstStreamId = '0:v';
    $lastStreamId  = $firstStreamId;
    $inputsMapping = $inputs = [];
    // Detect all additional inputs numbers
    for ($i = 0; $i <= $this
        ->getOptionsCollection()
        ->filterHasExtraInputs()
        ->count(); $i++
    ) {
      $inputsMapping[] = sprintf('%s:v', $i);
    }

    //$filterComplexOptions = [
    //  sprintf('[%s:v]', $sn)
    //];

    /*
    * '-i' '/home/ezmembersarea/public_html/app/video-templates/mov/personal-injury-attorney-male.mov'
    * '-i' '/home/ezmembersarea/public_html/app/library-templates/bg__581c9e4fcc123.png'
    * '-i' '/home/ezmembersarea/public_html/app//lower-thirds-graphics/90002-3_57f2a863ab2ab_57f2a863aba41.png'
    * '-i' '/home/ezmembersarea/public_html/app/video-templates/mov/personal-injury-attorney-male.mov'
    * '-filter_complex'
    * '[1:v]scale=1280:720[out1],[out1][0:v]overlay[out2],
     * [2:v]scale=1280:720[s2],[out2][s2]overlay[out3],
    * [out3]drawtext=fontfile=/home/ezmembersarea/public_html/app/assets/fonts/OpenSansBold/OpenSansBold.ttf:text='\''Profession\% sdfsdf'\'':fontcolor='\''#FFFFFF@1'\'':fontsize=38:x=30:y=574:enable='\''between(t,0,29)'\'',drawtext=fontfile=/home/ezmembersarea/public_html/app/assets/fonts/OpenSansBold/OpenSansBold.ttf:text='\''http\://website.com'\'':fontcolor='\''#FFFFFF@1'\'':fontsize=38:x=580:y=574:enable='\''between(t,0,29)'\''' '-map' '0:v' '-map' '3:a' '-threads' '12' '-vcodec' 'libx264' '-acodec' 'aac' '-b:v' '1000k' '-refs' '6' '-coder' '1' '-sc_threshold' '40' '-flags' '+loop' '-me_range' '16' '-subq' '7' '-i_qfactor' '0.71' '-qcomp' '0.6' '-qdiff' '4' '-trellis' '1' '-r' '25' '-to' '26.793' '-b:a' '128k' '-pass' '2' '-passlogfile' '/tmp/ffmpeg-passes585959f866965372mp/pass-585959f866a65' './data/tmp/main_282.mp4'
    */

    // Prepare overlay inputs
    $optionsOverlay = $this->getOptionsCollection()->sortByZindex();
    $imn = 1;
    $stm = 1;
    if ($optionsOverlay->count() > 0) {
      foreach ($optionsOverlay as $option) {
        if($option instanceof OptionDrawText) {

          $this->_optionsPrepared[] =
              str_replace([
                              ':s1', ':s2'
                          ], [
                              $lastStreamId,
                              's' . $stm
                          ], $option->getCommand());

          $lastStreamId = 's' . $stm;

          print 'DT='.$option->getCommand().'<br />';
        } elseif ($option instanceof OptionDrawBox) {

          $this->_optionsPrepared[] =
              str_replace([
                              ':s1', ':s2'
                          ], [
                              $lastStreamId,
                              's' . $stm
                          ], $option->getCommand());

          $lastStreamId = 's' . $stm;

          print 'DB='.$option->getCommand().'<br />';
        } elseif ($option instanceof OptionOverlay) {

          $this->_optionsPrepared[] =
              str_replace([
                              ':s1', ':s2', ':s3', ':s4', ':s5'
                          ], [
                  $inputsMapping[$imn],
                              't' . ($imn),
                  $lastStreamId,
                  't' . $imn,
                  's' . $stm
                          ], $option->getCommand());
          // We need to get a last stream id to apply next options in the correct order
          $lastStreamId = 's' . $stm;
          // Pass input paths to the separate array
          $this->setExtraInputStream($option->getExtraInputStream());

          $imn++;
          print 'OO='.$option->getCommand().'<br />';
        } elseif ($option instanceof OptionChromakey) {
          $this->_optionsPrepared[] =
              str_replace([
                              ':s1', ':s2', ':s3'
                          ], [
                              $lastStreamId,
                              $inputsMapping[$imn],
                              //$lastStreamId,
                              //'t' . $imn,
                              's' . $stm
                          ], $option->getCommand());
          // We need to get a last stream id to apply next options in the correct order
          $lastStreamId = 's' . $stm;
          // Pass input paths to the separate array
          $this->setExtraInputStream($option->getExtraInputStream());

          $imn++;
          print 'OO='.$option->getCommand().'<br />';
        } else {}


        $stm++;
        // Mark up them
        /*
        $this->_optionsPrepared[] =
            str_replace([
                            ':s1', ':s2', ':s3', ':s4', ':s5'
                        ], [
                            $streamMapping[$i],
                            's' . ($i + ($i * 1)),
                            ((count($this->_optionsPrepared) == 0) ? $firstStreamId : 's' . ($i + 1)),
                            's' . ($i + ($i * 1)),
                            ((count($this->_optionsPrepared) == 0) ? 's' . ($i + ($i * 2)) : 's' . ($i + 1 + ($i * 1)))
                        ], $option->getCommand());
        // We need to get a last stream id to apply next options in the correct order
        $lastStreamId = '[s' . ($i + 1 + ($i * 1)) . ']';
        // Pass input paths to the separate array
        $inputs[] = '-i';
        $inputs[] = $option->getOverlayInput()->getPath();
      */
        }
    }

    /* // '-refs' '6' '-coder' '1' '-sc_threshold' '40' '-flags' '+loop' '-me_range' '16' '-subq' '7' '-i_qfactor' '0.71' '-qcomp' '0.6' '-qdiff' '4' '-trellis' '1'
     *
    '/home/ezmembersarea/videoapp/app/module/RenderEngine/FFmpegStatic/ffmpeg' '-y' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/demo_video_720p_HD.mp4' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/intro_720p_muted.mp4' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/bg_green.jpeg' '-filter_complex' '[1:v]scale=120:60[t1],[0:v][t1]overlay=130:180[s1],[s1]drawtext=fontfile=/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/OpenSansRegular.ttf:text='\''Layer2'\'':fontcolor='\''#ffffff@1'\'':fontsize=20:x=130:y=160:box=1:boxcolor='\''000000'\''@1:boxborderw=10[s2],[2:v]scale=120:60[t2],[s2][t2]overlay=130:150[s3],[s3]drawtext=fontfile=/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/OpenSansRegular.ttf:text='\''Layer1'\'':fontcolor='\''#ffffff@1'\'':fontsize=20:x=130:y=150:box=1:boxcolor='\''red'\''@1:boxborderw=10' '-b:v' '1000k' '-b:a' '128k' '-pass' '1' '-passlogfile' '/tmp/ffmpeg-passes5866721d0c83d74tp0/pass-5866721d0c932' -map '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/output/output.mp4'
     *
     *
     *
     *
     *
     *
     *
     * '/home/ezmembersarea/videoapp/app/module/RenderEngine/FFmpegStatic/ffmpeg' '-y' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/demo_video_720p_HD.mp4' '-threads' '12' '-vcodec' 'libx264' '-acodec' 'libmp3lame' '-filter_complex' '[%si:v]scale=120:60[vOut%so],[vOut%so][%si:v]overlay:enable='\''between(t,1,6)'\''[vOut%so],drawtext=fontfile=/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/OpenSansRegular.ttf:text='\''http\://www.com This is the @ default text'\'':fontcolor='\''#ffffff@1'\'':fontsize=33:x=230:y=150:enable='\''between(t,1,6)'\''' '-map' '[vOut%so]' '-b:v' '1000k' '-refs' '6' '-coder' '1' '-sc_threshold' '40' '-flags' '+loop' '-me_range' '16' '-subq' '7' '-i_qfactor' '0.71' '-qcomp' '0.6' '-qdiff' '4' '-trellis' '1' '-b:a' '128k'
     */

    // Prepare draw text options
    //if ($optionsDrawText = $this->getOptionsCollection()->filter('DrawText') and $optionsDrawText->count() > 0) {
      //$this->_optionsPrepared[] = $lastStreamId.implode(',', $optionsDrawText->sortByZindex()->getArrayCopy());
    //}

    print '<pre>';
    //print_r();
    print '</pre>';

    print '<pre>';
    print_r($this->_optionsPrepared);
    print '</pre>';

    /*'/home/ezmembersarea/videoapp/app/module/RenderEngine/FFmpegStatic/ffmpeg' '-y' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/demo_video_720p_HD.mp4'  '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/bg_green.jpeg' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/intro_720p_muted.mp4' '-threads' '12' '-vcodec' 'libx264' '-acodec' 'libmp3lame' '-b:v' '1000k' '-refs' '6' '-coder' '1' '-sc_threshold' '40' '-flags' '+loop' '-me_range' '16' '-subq' '7' '-i_qfactor' '0.71' '-qcomp' '0.6' '-qdiff' '4' '-trellis' '1' '-b:a' '128k' output.mp4
*/


    if(count($this->_optionsPrepared) > 0) {
      $commands = array_merge($inputs, [
          '-filter_complex',
          rtrim(implode(',', $this->_optionsPrepared), '[' . $lastStreamId . ']')
      ]);
    } else {
      $commands = [];
    }

    print '<pre>';
    print_r($commands);
    print '</pre>';

    /*
    '/home/ezmembersarea/videoapp/app/module/RenderEngine/FFmpegStatic/ffmpeg' '-y' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/demo_video_720p_HD.mp4' '-filter_complex' '[0:v]drawtext=fontfile=/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/OpenSansRegular.ttf:text='\''default tex1t1'\'':fontsize=33:x=430:y=150,drawtext=fontfile=/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/OpenSansRegular.ttf:text='\''text'\'':fontsize=33:x=230:y=150' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/output/output.mp4'

    */


    //die;


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