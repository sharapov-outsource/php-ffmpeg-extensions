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

// Create simple overlay filter
$simpleFilter = new Sharapov\FFMpegExtensions\Filters\Video\Overlay\SimpleFilter();

// Create text overlay 1
$overlayText1 = new Sharapov\FFMpegExtensions\Filters\Video\Overlay\Text();
$overlayText1
    ->setFontFile(dirname(__FILE__).'/source/arial.ttf') // Set path to font file
    ->setFontColor('#ffffff') // Set font color
    ->setFontSize(33) // Set font size
    ->setOverlayText('This is the default text') // Set overlay text
    ->setCoordinates(new \Sharapov\FFMpegExtensions\Coordinate\Point(230, 150)) // Set coordinates
    ->setTimeLine(new \Sharapov\FFMpegExtensions\Coordinate\TimeLine(1, 6)); // Set timings (start, stop) in seconds

// Create text overlay 2
$overlayText2 = new Sharapov\FFMpegExtensions\Filters\Video\Overlay\Text();
$overlayText2
    ->setFontFile(dirname(__FILE__).'/source/arial.ttf') // Set path to font file
    ->setFontColor('#ffffff') // Set font color
    ->setFontSize(28) // Set font size
    ->setOverlayText('This is the default text 2') // Set overlay text
    ->setCoordinates(new \Sharapov\FFMpegExtensions\Coordinate\Point(230, 250)) // Set coordinates
    ->setTimeLine(new \Sharapov\FFMpegExtensions\Coordinate\TimeLine(8, 14)); // Set timings (start, stop) in seconds

// Create text overlay 3
$overlayText3 = new Sharapov\FFMpegExtensions\Filters\Video\Overlay\Text();
$overlayText3
    ->setFontFile(dirname(__FILE__).'/source/arial.ttf') // Set path to font file
    ->setFontColor('#ffffff') // Set font color
    ->setFontSize(38) // Set font size
    ->setOverlayText('This is the default text 3') // Set overlay text
    ->setCoordinates(new \Sharapov\FFMpegExtensions\Coordinate\Point(750, 550)) // Set coordinates
    ->setTimeLine(new \Sharapov\FFMpegExtensions\Coordinate\TimeLine(16, 20)); // Set timings (start, stop) in seconds

// Pass overlays to filter
$simpleFilter
    ->setOverlays([
        $overlayText1,
        $overlayText2,
        $overlayText3
    ]);

// Apply filter on video
$video
    ->addFilter($simpleFilter);

// Choose output format
$format = new \FFMpeg\Format\Video\X264('libmp3lame');
$format->on('progress', function ($video, $format, $percentage) {
  echo "$percentage % \n<br />";
});

// And render it
$video
    ->save($format, dirname(__FILE__).'/output/export-sample-text-overlay.mp4');