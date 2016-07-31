<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

date_default_timezone_set('UTC');
require_once dirname(__FILE__) . '/../vendor/autoload.php';

// Init logger object
$logger = new \Monolog\Logger('ffmpeg');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('info.log', \Monolog\Logger::INFO));

// Init FFMpeg library
$ffmpeg = \Sharapov\FFMpegExtensions\FFMpeg::create(array(
    'ffmpeg.binaries' => dirname(__FILE__).'/ffmpeg-static/ffmpeg', // Path to FFMpeg
    'ffprobe.binaries' => dirname(__FILE__).'/ffmpeg-static/ffprobe', // Path to FFProbe
    'timeout'          => 3600, // The timeout for the underlying process
    'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
), $logger);

// Open first file
$video = $ffmpeg->open(new \Sharapov\FFMpegExtensions\Stream\VideoFile(dirname(__FILE__).'/source/demo_video_720p_HD.mp4'));

// Transcode mp4 to transport stream
$format = new \Sharapov\FFMpegExtensions\Format\Video\TransportStream();
$format->on('progress', function ($video, $format, $percentage) {
  echo "$percentage %\n";
});

// Save intermediate 1
$video
    ->save($format, dirname(__FILE__).'/source/im1.ts');

// Open second file
$video = $ffmpeg->open(new \Sharapov\FFMpegExtensions\Stream\VideoFile(dirname(__FILE__).'/source/intro_720p_muted.mp4'));

// Transcode mp4 to transport stream
$format = new \Sharapov\FFMpegExtensions\Format\Video\TransportStream();
$format->on('progress', function ($video, $format, $percentage) {
  echo "$percentage %\n";
});

// Save intermediate 2
$video
    ->save($format, dirname(__FILE__).'/source/im2.ts');

// Merge two files using concatProtocol
$video = $ffmpeg->open(new \Sharapov\FFMpegExtensions\Stream\VideoFile(dirname(__FILE__).'/source/im1.ts'));
$video
    ->concatProtocol()
    ->setInput(new \Sharapov\FFMpegExtensions\Stream\VideoFile(dirname(__FILE__).'/source/im2.ts'));

$video
    ->merge(dirname(__FILE__).'/output/export-concatProtocol.mp4');