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
$video = $ffmpeg->open(new InputFile('source/Todd-version1-no-text-centered-am2.mov'));

// Initiate complex filter options collection
$options = new FilterComplexOptions\OptionsCollection();

// Create alphakey option
$alphaKey = new FilterComplexOptions\OptionAlphakey();
$alphaKey
    // Set input stream for alphakey
    ->setExtraInputStream(new InputFile('source/Coast - 1270.mp4'))
    ->setDimensions(new Coordinate\Dimension(1280, 720))
    // Set video alphakey background duration to meet the main video duration
    ->setDuration(new Coordinate\Duration($video->getStreamDuration()));

$options
    ->add($alphaKey);

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