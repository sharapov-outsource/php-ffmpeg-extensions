<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

namespace Sharapov\FFMpegExtensions\Driver;

use Alchemy\BinaryDriver\Configuration;
use Alchemy\BinaryDriver\ConfigurationInterface;
use Alchemy\BinaryDriver\Exception\ExecutableNotFoundException as BinaryDriverExecutableNotFound;
use FFMpeg\Exception\ExecutableNotFoundException;
use Psr\Log\LoggerInterface;

class FFMpegDriver extends \FFMpeg\Driver\FFMpegDriver
{
  /**
   * Creates an FFMpegDriver.
   *
   * @param LoggerInterface     $logger
   * @param array|Configuration $configuration
   *
   * @return FFMpegDriver
   */
  public static function create(LoggerInterface $logger = null, $configuration = array())
  {
    if (!$configuration instanceof ConfigurationInterface) {
      $configuration = new Configuration($configuration);
    }

    $binaries = $configuration->get('ffmpeg.binaries', array('avconv', 'ffmpeg'));

    if (!$configuration->has('timeout')) {
      $configuration->set('timeout', 300);
    }

    try {
      return static::load($binaries, $logger, $configuration);
    } catch (BinaryDriverExecutableNotFound $e) {
      throw new ExecutableNotFoundException('Unable to load FFMpeg', $e->getCode(), $e);
    }
  }

  public function printCommand($command)
  {
    if (!is_array($command)) {
      $command = array($command);
    }

    return $this->factory->create($command)->getCommandLine();
  }
}