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
$video = $ffmpeg->open(new InputFile('source/demo_video_720p_HD.mp4'));

// Apply filter options to video
$video
    ->filters()
    ->complex(
        new FilterComplexOptions\OptionsCollection([
                                                       ((new FilterComplexOptions\OptionOverlay())
                                                           // Set z-index property. Greater value is always in front
                                                           ->setZIndex(260)
                                                           // You can use fade-in and fade-out effects. Set time in seconds
                                                           ->setFadeIn(2)
                                                           ->setFadeOut(2)
                                                           // Set image path
                                                           ->setExtraInputStream(new InputFile('source/animated.gif'))
                                                           // Coordinates where the text should be rendered. Accepts positive integer or
                                                           // constants "(w-tw)/2", "(h-th)/2" to handle auto-horizontal, auto-vertical values
                                                           ->setCoordinates(new Coordinate\Point(230, 200))
                                                           // Set image dimensions
                                                           //->setDimensions(new Coordinate\Dimension(450, 200))
                                                           // Set timings (start, stop) in seconds. Accepts float values as well
                                                           ->setTimeLine(new Coordinate\TimeLine(3, 13)))
                                                   ]));

// Run render
$format = new Sharapov\FFMpegExtensions\Format\Video\X264('libmp3lame');
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

/*
 * -loop 1
 "ffmpeg-20170915-6743351-win64-static/bin/ffmpeg.exe" -y -i "source/demo_video_720p_HD.mp4" -ignore_loop 0 -i "source/animated.gif" -filter_complex "[1:v]format=yuva420p,scale=450:200,fade=t=in:st=0:d=4:alpha='1',fade=t=out:st=9:d=4:alpha='1'[t1],[0:v][t1]overlay=230:200:shortest='1':enable='between(t,3,13)'" -threads 12 -vcodec libx264 -acodec libmp3lame -b:v 1000k -refs 6 -coder 1 -sc_threshold 40 -flags +loop -me_range 16 -subq 7 -i_qfactor 0.71 -qcomp 0.6 -qdiff 4 -trellis 1 -b:a 128k "output/output.mp4"
 */
