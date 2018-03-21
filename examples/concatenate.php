<?php
/**
 * Created by PhpStorm.
 * User: Sharapov A. <alexander@sharapov.biz>
 * Web: http://sharapov.biz
 * Date: 18.03.2018
 * Time: 23:46
 */

chdir( dirname( __DIR__ ) );

ini_set( 'display_errors', 1 );
date_default_timezone_set( 'UTC' );
require_once dirname( __FILE__ ) . '/../vendor/autoload.php';

// Init ffmpeg library
$ffmpeg = \Sharapov\FFMpegExtensions\FFMpeg::create( [
                                                       'ffmpeg.binaries'  => getcwd() . '/../../ffmpeg-static/ffmpeg-3.4.2-64bit-static/ffmpeg',
                                                       'ffprobe.binaries' => getcwd() . '/../../ffmpeg-static/ffmpeg-3.4.2-64bit-static/ffprobe',
                                                       'timeout'          => 3600,
                                                       // The timeout for the underlying process
                                                       'ffmpeg.threads'   => 12,
                                                       // The number of threads that FFMpeg should use
                                                     ] );

$clip1 = $ffmpeg->open( new \Sharapov\FFMpegExtensions\Input\File( getcwd() . '/examples/source/demo_video_720p_HD.mp4' ) );

// Create complex filter collection
$options = new \Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionsCollection();

// Create drawtext option 1
$text1 = new \Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionDrawText();
$text1
  // Set z-index property. Greater value is always in front
  ->setZIndex(160)
  // You can use fade-in and fade-out effects. Set time in seconds
  ->setFadeIn(2)
  ->setFadeOut(2)
  // Set font path
  ->setFontFile(new \Sharapov\FFMpegExtensions\Input\File(getcwd() . '/examples/source/calibri.ttf'))
  // Set font color. Accepts transparency value as the second argument. Float value between 0 and 1.
  ->setFontColor('#ffffff')
  // Set font size in pixels
  ->setFontSize(33)
  // Set text string
  ->setText('php-ffmpeg-extensions>library')
  // Coordinates where the text should be rendered. Accepts positive integer or
  // constants "(w-tw)/2", "(h-th)/2" to handle auto-horizontal, auto-vertical values
  ->setCoordinates(new \Sharapov\FFMpegExtensions\Coordinate\Point(\Sharapov\FFMpegExtensions\Coordinate\Point::AUTO_HORIZONTAL, 50))
  // Set timings (start, stop) in seconds. Accepts float values as well
  ->setTimeLine(new \Sharapov\FFMpegExtensions\Coordinate\TimeLine(1, 20));

// Pass option to the options collection
$options
  ->add($text1);

// Apply filter options to video
$clip1
  ->filters()
  ->complex($options);

$collection = new \Sharapov\FFMpegExtensions\Media\VideoCollection();

// Adding clips to the collection
$collection
  ->add( $clip1 )
  ->add( $ffmpeg->open( new \Sharapov\FFMpegExtensions\Input\File( getcwd() . '/examples/source/intro_720p_muted.mp4' ) ) );

// And do concatenation
$format = new \Sharapov\FFMpegExtensions\Format\Video\X264();
$format->on( 'progress', function ( $item, $format, $percentage ) {
  print 'Done: ' . $percentage . "%\n";
} );

$result = $ffmpeg->concatenate( $format, $collection, getcwd() . '/examples/merged.mp4' );




