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

$collection = new \Sharapov\FFMpegExtensions\Media\VideoCollection();

$collection
  ->add( $ffmpeg->open( new \Sharapov\FFMpegExtensions\Input\File( getcwd() . '/examples/source/demo_video_720p_HD.mp4' ) ) )
  ->add( $ffmpeg->open( new \Sharapov\FFMpegExtensions\Input\File( getcwd() . '/examples/source/intro_720p_muted.mp4' ) ) );

$format = new \Sharapov\FFMpegExtensions\Format\Video\X264();
$format->on( 'progress', function ( $item, $format, $percentage ) {
  print 'Done: ' . $percentage . "%\n";
} );

$result = $ffmpeg->concatenate( $format, $collection, getcwd() . '/examples/merged.mp4' );




