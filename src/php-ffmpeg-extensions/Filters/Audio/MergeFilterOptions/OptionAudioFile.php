<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions;

use Sharapov\FFMpegExtensions\Filters\ExtraInputStreamInterface;
use Sharapov\FFMpegExtensions\Filters\ExtraInputStreamTrait;
use Sharapov\FFMpegExtensions\Input\FileInterface;
use FFMpeg\Exception\InvalidArgumentException;

/**
 * Audiofile filter option
 * @package Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions
 */
class OptionAudioFile
    implements
    OptionInterface,
    ExtraInputStreamInterface
{
  use ExtraInputStreamTrait;

  protected $_volumeLevel;

  public function __construct(FileInterface $file)
  {
    $this->setExtraInputStream($file);
  }

  /**
   * Set volume level.
   *
   * @return $this
   */
  public function setVolumeLevel($level)
  {
    if (!is_numeric($level) || $level < 0 || $level > 1) {
      throw new InvalidArgumentException('Volume level should be integer or float value from 0 to 1. ' . $level . ' given.');
    }

    $this->_volumeLevel = $level;

    return $this;
  }

  /**
   * Get volume level.
   *
   * @return string
   */
  public function getVolumeLevel()
  {
    return $this->_volumeLevel;
  }

  /**
   * Returns command string.
   *
   * @return string
   */
  public function getCommandMono2Stereo()
  {
    return sprintf("[%s][%s]amerge=inputs=2[%s]", ':s1', ':s2', ':s3');
  }

  /**
   * Returns command string.
   *
   * @return string
   */
  public function getCommandMuxStereo()
  {
    // [1:a]volume=1[a2][0:a][a2]amerge=inputs=2,pan=stereo|c0<c0+c2|c1<c1+c3

      //   '/home/ezmembersarea/videoapp/app/module/RenderEngine/FFmpegStatic/ffmpeg' '-y' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/LastResort.mp3' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/LastStand.mp3' '-filter_complex' '[1:a]volume=0.2[av];[0:a][av]amerge=inputs=2,pan=stereo:c0<c0+c2:c1<c1+c3' '-threads' '12' '-acodec' 'libmp3lame' '-b:a' '128k' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/output/output.mp3'

      // '/home/ezmembersarea/videoapp/app/module/RenderEngine/FFmpegStatic/ffmpeg' '-y' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/LastResort.mp3' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/LastStand.mp3' '-filter_complex' '[0:a]aformat=sample_fmts=fltp:sample_rates=44100:channel_layouts=stereo[av1];[1:a]aformat=sample_fmts=fltp:sample_rates=44100:channel_layouts=stereo,volume=0.2[av];[av1][av]amerge=inputs=2,pan=stereo|c0<c0+c2|c1<c1+c3' '-threads' '12' '-acodec' 'libmp3lame' '-b:a' '128k' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/output/output.mp3'


    if($this->_volumeLevel != null) {
      $volume = sprintf('aformat=sample_fmts=fltp:sample_rates=44100:channel_layouts=stereo,volume=%s[av];[av][%s]', $this->_volumeLevel, ':s2');
    } else {
      $volume = '[:s2]';
    }
    return sprintf("[%s]%samerge=inputs=2,pan=stereo|c0<c0+c2|c1<c1+c3[%s]", ':s1', $volume, ':s3');
  }

  public function getCommand()
  {
    return '';
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
