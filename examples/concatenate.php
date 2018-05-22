<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

use \Sharapov\FFMpegExtensions\Input\File as InputFile;
use \Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions;
use \Sharapov\FFMpegExtensions\Coordinate;
use \Alchemy\BinaryDriver\Exception\ExecutionFailureException;

require_once 'init.php';

$collection = new \Sharapov\FFMpegExtensions\Media\VideoCollection();
// Open source video
$video = $ffmpeg->open( new InputFile( 'source/demo_video_720p_HD.mp4' ) );

// Initiate complex filter options collection
$options = new FilterComplexOptions\OptionsCollection();

// Pass option to the options collection
$options
  ->add( ( ( new FilterComplexOptions\OptionDrawText() )
    // Set z-index property. Greater value is always in front
    ->setZIndex( 160 )
    // You can use fade-in and fade-out effects. Set time in seconds
    ->setFadeIn( 2 )
    ->setFadeOut( 2 )
    // Set font path
    ->setFontFile( new InputFile( 'source/arial.ttf' ) )
    // Set font color. Accepts transparency value as the second argument. Float value between 0 and 1.
    ->setFontColor( 'ffffff' )
    // Set font size in pixels
    ->setFontSize( 33 )
    // Set text string
    ->setText( 'Concatenation videos' )
    // Coordinates where the text should be rendered. Accepts positive integer or
    // constants "(w-tw)/2", "(h-th)/2" to handle auto-horizontal, auto-vertical values
    ->setCoordinates( new Coordinate\Point( Coordinate\Point::AUTO_HORIZONTAL, 50 ) )
    // Set timings (start, stop) in seconds. Accepts float values as well
    ->setTimeLine( new Coordinate\TimeLine( 2, 8 ) ) ) );

// Apply filter options to video
$video
  ->filters()
  ->complex( $options );


// Adding clips to the collection
$collection
  ->add( $video )
  ->add( $ffmpeg->open( new InputFile( 'source/Coast - 1270.mp4' ) ) );

// And do concatenation
$format = new \Sharapov\FFMpegExtensions\Format\Video\X264();
$format->on( 'progress', function ( $video, $format, $percentage ) {
  echo "$percentage% transcoded\n";
} );

try {
  $ffmpeg->concatenate( $format, $collection, 'output/merged.mp4' );
  print 'Done!';
} catch ( ExecutionFailureException $e ) {
  print $e->getMessage();
}