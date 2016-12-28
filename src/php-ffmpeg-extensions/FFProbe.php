<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions;

use Doctrine\Common\Cache\Cache;
use FFMpeg\Driver\FFProbeDriver;

class FFProbe extends \FFMpeg\FFProbe
{
  private static $_instance;

  public function __construct(FFProbeDriver $ffprobe, Cache $cache)
  {
    parent::__construct($ffprobe, $cache);
    self::$_instance = $this;
  }

  public static function getInstance()
  {
    return self::$_instance;
  }
}
