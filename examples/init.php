<?php
/**
 * @copyright Sharapov A. <alexander@sharapov.biz>
 * @link      http://www.sharapov.biz/
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License
 */

use \Monolog\Logger as MonologLogger;
use \Monolog\Handler\StreamHandler;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

chdir(dirname(__FILE__));

try {
  $logger = new MonologLogger('debug');
  $logger->pushHandler(new StreamHandler('../data/logs/render.log', MonologLogger::ERROR));
  $logger->pushHandler(new StreamHandler('../data/logs/info.log', MonologLogger::INFO));
} catch (\Exception $e) {
  die($e->getMessage());
}

$config = [
    'ffmpeg.binaries'  => './ffmpeg33',
    'ffprobe.binaries' => './ffprobe',
    'timeout'          => 3600, // The timeout for the underlying process
    'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
];

// Init ffmpeg library
$ffmpeg = \Sharapov\FFMpegExtensions\FFMpeg::create($config, $logger);

