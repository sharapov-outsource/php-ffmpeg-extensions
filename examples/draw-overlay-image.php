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
$video = $ffmpeg->open(new \Sharapov\FFMpegExtensions\Input\File(dirname(__FILE__) . '/source/demo_video_720p_HD.mp4'));

// Create complex filter collection
$options = new \Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionsCollection();

// Create overlay option 1
$overlay1 = new \Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionOverlay();
$overlay1
    // Set z-index property. Greater value is always in front
    ->setZIndex(160)
    // You can use fade-in and fade-out effects. Set time in seconds
    ->setFadeIn(2)
    ->setFadeOut(2)
    // Set image path
    ->setExtraInputStream(new \Sharapov\FFMpegExtensions\Input\File(dirname(__FILE__) . '/source/demo_video_720p_HD.mp4'))
    // Coordinates where the text should be rendered. Accepts positive integer or
    // constants "(w-tw)/2", "(h-th)/2" to handle auto-horizontal, auto-vertical values
    ->setCoordinates(new \Sharapov\FFMpegExtensions\Coordinate\Point(23, 50))
    // Set image dimensions
    ->setDimensions(new \Sharapov\FFMpegExtensions\Coordinate\Dimension(450, 200))
    // Set timings (start, stop) in seconds. Accepts float values as well
    ->setTimeLine(new \Sharapov\FFMpegExtensions\Coordinate\TimeLine(1, 10));

// Pass option to the options collection
$options
    ->add($overlay1);

// Create overlay option 1
$overlay2 = new \Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionOverlay();
$overlay2
    // Set z-index property. Greater value is always in front
    ->setZIndex(260)
    // You can use fade-in and fade-out effects. Set time in seconds
    ->setFadeIn(4)
    ->setFadeOut(4)
    // Set image path
    ->setExtraInputStream(new \Sharapov\FFMpegExtensions\Input\File(dirname(__FILE__) . '/source/bg_red.jpg'))
    // Coordinates where the text should be rendered. Accepts positive integer or
    // constants "(w-tw)/2", "(h-th)/2" to handle auto-horizontal, auto-vertical values
    ->setCoordinates(new \Sharapov\FFMpegExtensions\Coordinate\Point(230, 200))
    // Set image dimensions
    ->setDimensions(new \Sharapov\FFMpegExtensions\Coordinate\Dimension(450, 200))
    // Set timings (start, stop) in seconds. Accepts float values as well
    ->setTimeLine(new \Sharapov\FFMpegExtensions\Coordinate\TimeLine(3, 13));

// Pass option to the options collection
$options
   ->add($overlay2);

// Apply filter options to video
$video
    ->filters()
    ->complex($options);

// Run render
$format = new \FFMpeg\Format\Video\X264('libmp3lame');
$format->on('progress', function ($video, $format, $percentage) {
  print 'Done: '.$percentage . "%\n";
});

$video
    ->save($format, dirname(__FILE__) . '/output/output.mp4');