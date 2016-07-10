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
$ffmpeg = \FFMpeg\FFMpeg::create(array(
    'ffmpeg.binaries' => dirname(__FILE__).'/ffmpeg-static/ffmpeg', // Path to FFMpeg
    'ffprobe.binaries' => dirname(__FILE__).'/ffmpeg-static/ffprobe', // Path to FFProbe
    'timeout'          => 3600, // The timeout for the underlying process
    'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
), $logger);
$video = $ffmpeg->open(dirname(__FILE__).'/source/demo_video_720p_HD.mp4');

// Create draw overlay filter
$drawText = new Sharapov\FFMpegExtensions\Filters\Video\FilterSimpleOverlay();

// Create text overlay 1 with central alignment
$overlayText = new Sharapov\FFMpegExtensions\Filters\Video\Overlay\Text();
$overlayText
    ->setFontFile(dirname(__FILE__).'/source/arial.ttf') // Set path to font file
    ->setFontColor('#ffffff') // Set font color
    ->setFontSize(33) // Set font size
    ->setOverlayText('Central alignment') // Set overlay text
    ->setCoordinates(new \Sharapov\FFMpegExtensions\Coordinate\Point()) // Set coordinates
    ->setTimeLine(new \Sharapov\FFMpegExtensions\Coordinate\TimeLine(1, 6)); // Set timings (start, stop) in seconds

// Pass text overlay to filter
$drawText
    ->setOverlay($overlayText);

// Vertical alignment
$overlayText = new Sharapov\FFMpegExtensions\Filters\Video\Overlay\Text();
$overlayText
    ->setFontFile(dirname(__FILE__).'/source/arial.ttf') // Set path to font file
    ->setFontColor('#ffffff') // Set font color
    ->setFontSize(28) // Set font size
    ->setOverlayText('Vertical alignment with 50px left margin') // Set overlay text
    ->setCoordinates(
        new \Sharapov\FFMpegExtensions\Coordinate\Point(50, \Sharapov\FFMpegExtensions\Coordinate\Point::AUTO_VERTICAL)
    ) // Set coordinates
    ->setTimeLine(new \Sharapov\FFMpegExtensions\Coordinate\TimeLine(8, 14)); // Set timings (start, stop) in seconds

// Pass text overlay to filter
$drawText
    ->setOverlay($overlayText);

// Horizontal alignment
$overlayText = new Sharapov\FFMpegExtensions\Filters\Video\Overlay\Text();
$overlayText
    ->setFontFile(dirname(__FILE__).'/source/arial.ttf') // Set path to font file
    ->setFontColor('#ffffff') // Set font color
    ->setFontSize(38) // Set font size
    ->setOverlayText('Horizontal alignment with 200px top margin') // Set overlay text
    ->setCoordinates(
        new \Sharapov\FFMpegExtensions\Coordinate\Point(\Sharapov\FFMpegExtensions\Coordinate\Point::AUTO_HORIZONTAL, 200)
    ) // Set coordinates
    ->setTimeLine(new \Sharapov\FFMpegExtensions\Coordinate\TimeLine(16, 20)); // Set timings (start, stop) in seconds

// Pass text overlay to filter
$drawText
    ->setOverlay($overlayText);

// Apply overlay filter to video
$video
    ->addFilter($drawText);

// Choose output format
$format = new \FFMpeg\Format\Video\X264();
$format->on('progress', function ($video, $format, $percentage) {
  echo "$percentage %<br />";
});

// And render it
$video
    ->save($format, dirname(__FILE__).'/output/export-sample-text-overlay-centering.mp4');