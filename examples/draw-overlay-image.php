<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 */

use \Sharapov\FFMpegExtensions\Input\File as InputFile;
use \Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;
use \Sharapov\FFMpegExtensions\Coordinate;
use \Alchemy\BinaryDriver\Exception\ExecutionFailureException;

require_once 'init.php';

// Open source video
$video = $ffmpeg->open(new InputFile('source/Coast - 1270.mp4'));

// Create complex filter collection
$options = new FilterComplexOptions\OptionsCollection();

// Create overlay option 1
$overlay1 = new FilterComplexOptions\OptionOverlay();
$overlay1
    // Set z-index property. Greater value is always in front
    ->setZIndex(160)
    // You can use fade-in and fade-out effects. Set time in seconds
    ->setFadeIn(2)
    ->setFadeOut(2)
    // Set image path
    ->setExtraInputStream(new InputFile('source/demo_video_720p_HD.mp4'))
    // Coordinates where the text should be rendered. Accepts positive integer or
    // constants "(w-tw)/2", "(h-th)/2" to handle auto-horizontal, auto-vertical values
    ->setCoordinates(new Coordinate\Point(23, 50))
    // Set image dimensions
    ->setDimensions(new Coordinate\Dimension(450, 200))
    // Set timings (start, stop) in seconds. Accepts float values as well
    ->setTimeLine(new Coordinate\TimeLine(1, 10));

// Pass option to the options collection
$options
    ->add($overlay1);

// Create overlay option 1
$overlay2 = new FilterComplexOptions\OptionOverlay();
$overlay2
    // Set z-index property. Greater value is always in front
    ->setZIndex(260)
    // You can use fade-in and fade-out effects. Set time in seconds
    ->setFadeIn(4)
    ->setFadeOut(4)
    // Set image path
    ->setExtraInputStream(new InputFile('source/bg_red.jpg'))
    // Coordinates where the text should be rendered. Accepts positive integer or
    // constants "(w-tw)/2", "(h-th)/2" to handle auto-horizontal, auto-vertical values
    ->setCoordinates(new Coordinate\Point(430, 400))
    // Set image dimensions
    ->setDimensions(new Coordinate\Dimension(450, 200))
    // Set timings (start, stop) in seconds. Accepts float values as well
    ->setTimeLine(new Coordinate\TimeLine(3, 13));

// Pass option to the options collection
$options
    ->add($overlay2);

// Apply filter options to video
$video
    ->filters()
    ->complex($options);

// Run render
$format = new \FFMpeg\Format\Video\X264('libmp3lame');
$format->on('progress', function($video, $format, $percentage) {
  echo "$percentage% transcoded\n";
});

try {
  $video
      ->save($format, 'output/output.mp4');
  print 'Done!';
} catch (ExecutionFailureException $e) {
  print $e->getMessage();
}