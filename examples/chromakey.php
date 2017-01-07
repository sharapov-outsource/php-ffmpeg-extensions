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

// Init ffmpeg library
$ffmpeg = \Sharapov\FFMpegExtensions\FFMpeg::create([
                                                        'ffmpeg.binaries'  => '/home/ezmembersarea/videoapp/app/module/RenderEngine/FFmpegStatic/ffmpeg',
                                                        'ffprobe.binaries' => '/home/ezmembersarea/videoapp/app/module/RenderEngine/FFmpegStatic/ffprobe',
                                                        'timeout'          => 3600, // The timeout for the underlying process
                                                        'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
                                                    ]);

// Open source video
$video = $ffmpeg->open(new \Sharapov\FFMpegExtensions\Input\File(dirname(__FILE__) . '/source/demo_video_720p_HD.mp4'));

// Create complex filter collection
$options = new \Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionsCollection();

// Create chromakey option
$colorKey = new \Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionColorkey();
$colorKey
    // Set background file path. Can be video or image.
    ->setExtraInputStream(new \Sharapov\FFMpegExtensions\Input\File(dirname(__FILE__). '/source/bg_red.jpg'))
    // Set colorspace that must be replaced with background
    ->setColor('F3F6C7', 0.1, 0.2)
    // Background dimensions
    ->setDimensions(new \Sharapov\FFMpegExtensions\Coordinate\Dimension(1280, 720));

$filterOptions
    ->add($colorKey);

// Create drawtext option 1 (more examples are in the file draw-texts-and-boxes.php)
$text1 = new \Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionDrawText();
$text1
    ->setZIndex(160)
    ->setFontFile(new \Sharapov\FFMpegExtensions\Input\File(dirname(__FILE__) . '/source/calibri.ttf'))
    ->setFontColor('#ffffff')
    ->setFontSize(33)
    ->setText('Chromakey example')
    ->setCoordinates(new \Sharapov\FFMpegExtensions\Coordinate\Point(\Sharapov\FFMpegExtensions\Coordinate\Point::AUTO_HORIZONTAL, 50))
    ->setTimeLine(new \Sharapov\FFMpegExtensions\Coordinate\TimeLine(1, 20));

// Pass option to the options collection
$options
    ->add($text1);

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