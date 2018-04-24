<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

ini_set('display_errors', 1);
date_default_timezone_set('UTC');
require_once dirname(__FILE__) . '/../vendor/autoload.php';

$logger = new \Monolog\Logger('debug');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('render.log', \Monolog\Logger::ERROR));
$logger->pushHandler(new \Monolog\Handler\StreamHandler('info.log', \Monolog\Logger::INFO));

// Init ffmpeg library
$ffmpeg = \Sharapov\FFMpegExtensions\FFMpeg::create([
                                                        'ffmpeg.binaries'  => 'D:\Projects\php-ffmpeg-extensions\examples\ffmpeg-20170915-6743351-win64-static\bin\ffmpeg.exe',
                                                        'ffprobe.binaries' => 'D:\Projects\php-ffmpeg-extensions\examples\ffmpeg-20170915-6743351-win64-static\bin\ffprobe.exe',
                                                        'timeout'          => 3600, // The timeout for the underlying process
                                                        'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
                                                    ], $logger);

// Open source video
$video = $ffmpeg->open(new \Sharapov\FFMpegExtensions\Input\File(dirname(__FILE__) . '/source/Todd-version1-no-text-centered-am2.mov'));

// Create complex filter collection
$options = new \Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionsCollection();

// Create alphakey option
$alphaKey = new \Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionAlphakey();
$alphaKey
    ->setExtraInputStream(new \Sharapov\FFMpegExtensions\Input\File(dirname(__FILE__) . '/source/Coast - 1270.mp4'))
    ->setDimensions(new \Sharapov\FFMpegExtensions\Coordinate\Dimension(1280, 720))
    // Set video alphakey background duration to meet the main video duration
    ->setDuration(new \Sharapov\FFMpegExtensions\Coordinate\Duration( $video->getStreamDuration()));

$options
    ->add($alphaKey);

// Apply filter options to video
$video
    ->filters()
    ->complex($options);

// Run render
$format = new \FFMpeg\Format\Video\X264('libmp3lame');
$format->on('progress', function ($video, $format, $percentage) {
  print 'Done: ' . $percentage . "%\n";
});

$video
    ->save($format, dirname(__FILE__) . '/output/output.mp4');