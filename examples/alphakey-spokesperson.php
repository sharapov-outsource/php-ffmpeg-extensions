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
$video = $ffmpeg->open(new InputFile('source/spokesperson-alpha.mov'));

// Initiate complex filter options collection
$options = new FilterComplexOptions\OptionsCollection();

// Create alphakey option
$alphaKey = new FilterComplexOptions\OptionAlphakey();
$alphaKey
    // Set input stream for alphakey
    ->setExtraInputStream(new InputFile('source/demo_video_720p_HD.mp4'))
    ->setDimensions(new Coordinate\Dimension(1280, 720))
    // Set video alphakey background duration to meet the main video duration
    ->setDuration(new Coordinate\Duration($video->getStreamDuration()));

$options
    ->add($alphaKey);

// Pass option to the options collection
$options
    ->add(((new FilterComplexOptions\OptionDrawText())
        // Set z-index property. Greater value is always in front
        ->setZIndex(160)
        // You can use fade-in and fade-out effects. Set time in seconds
        ->setFadeIn(1)
        ->setFadeOut(1)
        // Set font path
        ->setFontFile(new InputFile('source/arial.ttf'))
        // Set font color. Accepts transparency value as the second argument. Float value between 0 and 1.
        ->setFontColor('ffffff')
        // Set font size in pixels
        ->setFontSize(33)
        // Set text string
        ->setText('Spokesperson chromakey demonstration')
        // Coordinates where the text should be rendered. Accepts positive integer or
        // constants "(w-tw)/2", "(h-th)/2" to handle auto-horizontal, auto-vertical values
        ->setCoordinates(new Coordinate\Point(Coordinate\Point::AUTO_HORIZONTAL, 50))
        // Set timings (start, stop) in seconds. Accepts float values as well
        ->setTimeLine(new Coordinate\TimeLine(1, 6))
        // Set bounding box
        ->setBoundingBox('000000', 10, 0.5)));

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