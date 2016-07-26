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
use FFMpeg\Media\Video;
use FFMpeg\Format\VideoInterface;
use Sharapov\FFMpegExtensions\Coordinate\Point;
use Sharapov\FFMpegExtensions\Coordinate\TimeLine;
use Sharapov\FFMpegExtensions\Coordinate\Dimension;
use FFMpeg\Exception\InvalidArgumentException;

class ComplexFilter extends AbstractFilter implements VideoFilterInterface
{
  protected $imageOverlay = array();

  protected $textOverlay = array();

  protected $boxOverlay = array();

  protected $colorKeyFilter;

  protected $inputs = array();

  /**
   * Set overlay object.
   * @param \Sharapov\FFMpegExtensions\Filters\Video\Overlay\OverlayInterface $overlay
   * @return $this
   */
  public function setOverlay(OverlayInterface $overlay)
  {
    if ($overlay instanceof ColorKey) {

      if ($overlay->getImageFile() == null) {
        throw new InvalidArgumentException('Filter "ColorKey" error: incorrect path');
      }

      if (!$overlay->getDimensions() instanceof Dimension) {
        throw new InvalidArgumentException('Filter "ColorKey" error: incorrect dimensions');
      }

      $this->colorKeyFilter = $overlay;
      $this->inputs[] = $overlay->getImageFile();

    } elseif ($overlay instanceof Image) {

      if ($overlay->getImageFile() == null) {
        throw new InvalidArgumentException('Filter "Image" error: incorrect path');
      }

      if (!$overlay->getDimensions() instanceof Dimension) {
        throw new InvalidArgumentException('Filter "Image" error: incorrect dimensions');
      }

      $this->imageOverlay[] = $overlay;
      $this->inputs[] = $overlay->getImageFile();
    } elseif ($overlay instanceof Text) {
      $this->textOverlay[] = $overlay;
    } elseif ($overlay instanceof Box) {
      $this->boxOverlay[] = $overlay;
    } else {
      throw new InvalidArgumentException('Unsupported overlay requested. Only ColorKey, Image, Text, Box are supported.');
    }
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public function apply(Video $video, VideoInterface $format)
  {
    $filterOptions = array();
    // Compile color key command
    if ($this->colorKeyFilter instanceof ColorKey) {
      $filterOptions[] = sprintf('[0:v]colorkey=%s[sck]', $this->colorKeyFilter->getColor());
      // Color key background input is always the first stream
      $filterOptions[] = sprintf('[1:v]scale=%s[out1]', $this->colorKeyFilter->getDimensions());
      $filterOptions[] = sprintf('[out1][sck]overlay%s', ((count($this->imageOverlay) > 0 or count($this->textOverlay) > 0) ? '[out2]' : ''));

      $filterNumStart = 2;
    } else {
      $filterNumStart = 1;
    }

    // Compile other filters commands
    foreach ($this->imageOverlay as $k => $filter) {
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

      if (isset($this->imageOverlay[($k + 1)]) or count($this->textOverlay) > 0) {
        $cmd .= sprintf("[out%s]", ($filterNumStart + 1));
      }
      $filterOptions[] = $cmd;

      $filterNumStart++;
    }

    // Compile drawtext filters
    if (count($this->textOverlay) > 0) {
      if ($filterNumStart == 1) {
        $cmd = '[0:v]';
      } else {
        $cmd = sprintf('[out%s]', $filterNumStart);
      }
      $cmd .= implode(",", $this->textOverlay);

      if (count($this->boxOverlay) > 0) {
        $cmd .= sprintf("[out%s]", ($filterNumStart + 1));
      }
      $filterOptions[] = $cmd;
      $filterNumStart++;
    }

    // Compile drawbox filters
    if (count($this->boxOverlay) > 0) {
      $filterOptions[] = sprintf("[out%s]%s", $filterNumStart, implode(",", $this->boxOverlay));
    }

    $commands = array();

    foreach ($this->inputs as $input) {
      $commands[] = '-i';
      $commands[] = $input;
    }

    $commands[] = '-filter_complex';
    $commands[] = implode(",", $filterOptions);
    return $commands;
  }
}
