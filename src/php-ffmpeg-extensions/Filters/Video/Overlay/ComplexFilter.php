<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Filters\Video\Overlay;

use FFMpeg\Filters\Video\VideoFilterInterface;
use Sharapov\FFMpegExtensions\Coordinate\Point;
use Sharapov\FFMpegExtensions\Coordinate\TimeLine;
use Sharapov\FFMpegExtensions\Coordinate\Dimension;
use FFMpeg\Exception\InvalidArgumentException;
use VideoCreator\Model\Filters\Audio\Amerge;

/**
 * Class ComplexFilter
 * @package Sharapov\FFMpegExtensions\Filters\Video\Overlay
 */
class ComplexFilter extends AbstractFilter implements VideoFilterInterface
{
  protected $_imageOverlay = [];

  protected $_textOverlay = [];

  protected $_boxOverlay = [];

  protected $_amerge;

  protected $_colorKeyFilter;

  protected $_inputs = [];

  /**
   * Set overlay object.
   *
   * @param \Sharapov\FFMpegExtensions\Filters\Video\Overlay\OverlayInterface $overlay
   *
   * @return $this
   */
  public function setOverlay(\Sharapov\FFMpegExtensions\Filters\Video\Overlay\OverlayInterface $overlay)
  {
    if ($overlay instanceof ColorKey) {

      if ($overlay->getImageFile()->getFile() == null and $overlay->getVideoFile()->getFile() == null) {
        throw new InvalidArgumentException('Filter "ColorKey" error: incorrect file path');
      }

      if (!$overlay->getDimensions() instanceof Dimension) {
        throw new InvalidArgumentException('Filter "ColorKey" error: incorrect dimensions');
      }

      $this->_colorKeyFilter = $overlay;
      if ($overlay->getImageFile() != null) {
        $this->_inputs[] = $overlay->getImageFile();
      }
      if ($overlay->getVideoFile() != null) {
        $this->_inputs[] = $overlay->getVideoFile();
      }

    } elseif ($overlay instanceof Image) {

      if ($overlay->getImageFile() == null) {
        throw new InvalidArgumentException('Filter "Image" error: incorrect path');
      }

      if (!$overlay->getDimensions() instanceof Dimension) {
        throw new InvalidArgumentException('Filter "Image" error: incorrect dimensions');
      }

      $this->_imageOverlay[] = $overlay;
      $this->_inputs[] = $overlay->getImageFile();
    } elseif ($overlay instanceof Text) {
      $this->_textOverlay[] = $overlay;
    } elseif ($overlay instanceof Box) {
      $this->_boxOverlay[] = $overlay;
    } elseif ($overlay instanceof Amerge) {
      $this->_amerge = $overlay;
      $this->_inputs[] = $overlay->getImageFile();
    } else {
      throw new InvalidArgumentException('Unsupported overlay requested. Only ColorKey, Image, Text, Box are supported.');
    }

    return $this;
  }

  /**
   * @param \FFMpeg\Media\Video           $video
   * @param \FFMpeg\Format\VideoInterface $format
   *
   * @return array
   */
  public function apply(\FFMpeg\Media\Video $video, \FFMpeg\Format\VideoInterface $format)
  {
    $filterOptions = [];
    // Compile color key command
    if ($this->_colorKeyFilter instanceof ColorKey) {
      $filterOptions[] = sprintf('[0:v]colorkey=%s[sck]', $this->_colorKeyFilter->getColor());
      // Color key background input is always the first stream
      $filterOptions[] = sprintf('[1:v]scale=%s[out1]', $this->_colorKeyFilter->getDimensions());
      $filterOptions[] = sprintf('[out1][sck]overlay%s', ((count($this->_imageOverlay) > 0 or count($this->_textOverlay) > 0 or $this->_amerge != null) ? '[out2]' : ''));

      $filterNumStart = 2;
    } else {
      $filterNumStart = 1;
    }

    // Compile other filters commands
    foreach ($this->_imageOverlay as $k => $filter) {
      $filterOptions[] = sprintf('[%s:v]scale=%s[s%s]', $filterNumStart, $filter->getDimensions(), $filterNumStart);
      if ($filterNumStart == 1) {
        $cmd = '[0:v]';
      } else {
        $cmd = sprintf('[out%s]', $filterNumStart);
      }
      $cmd .= sprintf('[s%s]overlay=', $filterNumStart);

      // Image position
      if ($filter->getCoordinates() instanceof Point) {
        $cmd .= $filter->getCoordinates();
      } else {
        $cmd .= "0:0";
      }

      // Image overlay timings
      if ($filter->getTimeLine() instanceof TimeLine) {
        $cmd .= sprintf(":enable='between(t,%s)'", $filter->getTimeLine());
      }

      if (isset($this->_imageOverlay[($k + 1)]) or count($this->_textOverlay) > 0 or $this->_amerge != null) {
        $cmd .= sprintf("[out%s]", ($filterNumStart + 1));
      }
      $filterOptions[] = $cmd;

      $filterNumStart++;
    }

    if ($this->_amerge != null) {
      $an = 2;
      foreach ($this->_inputs as $i => $input) {
        if($input->getFile() == $this->_amerge->getImageFile()) {
          $an = $i;
        }
      }
      if ($this->_colorKeyFilter instanceof ColorKey) {
        $an++;
      }
      $filterOptions[] = sprintf('[0:a]aformat=sample_fmts=fltp:sample_rates=44100:channel_layouts=stereo[a1],[%s:a]aformat=sample_fmts=fltp:sample_rates=44100:channel_layouts=stereo[a2],[a1][a2]amerge,pan=stereo|c0<c0+c2|c1<c1+c3[aout]', $an);
    }

    // Compile drawtext filters
    if (count($this->_textOverlay) > 0) {
      if ($filterNumStart == 1) {
        $cmd = '[0:v]';
      } else {
        $cmd = sprintf('[out%s]', $filterNumStart);
      }
      $cmd .= implode(",", $this->_textOverlay);

      if (count($this->_boxOverlay) > 0 or $this->_amerge != null) {
        $cmd .= sprintf("[out%s]", ($filterNumStart + 1));
      }
      $filterOptions[] = $cmd;
      $filterNumStart++;
    }

    // Compile drawbox filters
    if (count($this->_boxOverlay) > 0) {
      $filterOptions[] = sprintf("[out%s]%s", $filterNumStart, implode(",", $this->_boxOverlay));
    }

    $commands = [];

    foreach ($this->_inputs as $input) {
      $commands[] = '-i';
      $commands[] = $input->getFile();
    }

    // Cut 1/10 from the beginning of video
    $commands[] = '-ss';
    $commands[] = '0.1';
    $commands[] = '-filter_complex';
    $commands[] = implode(",", $filterOptions);
    if ($this->_amerge != null) {
      $commands[] = '-map';
      $commands[] = '[aout]';
      $commands[] = '-map';
      $commands[] = sprintf("[out%s]", $filterNumStart);
    }

    return $commands;
  }
}
