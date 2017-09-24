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

$logger = new \Monolog\Logger('debug');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('render.log', \Monolog\Logger::ERROR));
$logger->pushHandler(new \Monolog\Handler\StreamHandler('info.log', \Monolog\Logger::INFO));

// Init ffmpeg library
$ffmpeg = \Sharapov\FFMpegExtensions\FFMpeg::create([
                                                        'ffmpeg.binaries'  => 'D:\Projects\php-ffmpeg-extensions\examples\ffmpeg-20170915-6743351-win64-static\bin\ffmpeg.exe',
                                                        'ffprobe.binaries' => 'D:\Projects\php-ffmpeg-extensions\examples\ffmpeg-20170915-6743351-win64-static\bin\ffprobe.exe',
                                                        'timeout'          => 3600, // The timeout for the underlying process
                                                        'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
                                                    ], $logger);

// Open source video
$video = $ffmpeg->open(new \Sharapov\FFMpegExtensions\Input\File(dirname(__FILE__) . '/source/Vault.mov'));

// Create complex filter collection
$options = new \Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionsCollection();

// Create alphakey option
$alphaKey = new \Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionAlphakey();
$alphaKey
    ->setExtraInputStream(new \Sharapov\FFMpegExtensions\Input\File(dirname(__FILE__). '/source/demo_video_720p_HD.mp4'))
    ->setDimensions(new \Sharapov\FFMpegExtensions\Coordinate\Dimension(1280, 720));

$options
    ->add($alphaKey);


// Create drawtext option 1
$text1 = new \Sharapov\FFMpegExtensions\Filters\Video\FilterComplexOptions\OptionDrawText();
$text1
  // Set z-index property. Greater value is always in front
  ->setZIndex(160)
  // You can use fade-in and fade-out effects. Set time in seconds
  ->setFadeIn(2)
  ->setFadeOut(2)
  // Set font path
  ->setFontFile(new \Sharapov\FFMpegExtensions\Input\File('source/calibri.ttf'))
  // Set font color. Accepts transparency value as the second argument. Float value between 0 and 1.
  ->setFontColor('#ffffff')
  // Set font size in pixels
  ->setFontSize(33)
  // Set text string
  ->setText('alphakey demonstration')
  // Coordinates where the text should be rendered. Accepts positive integer or
  // constants "(w-tw)/2", "(h-th)/2" to handle auto-horizontal, auto-vertical values
  ->setCoordinates(new \Sharapov\FFMpegExtensions\Coordinate\Point(\Sharapov\FFMpegExtensions\Coordinate\Point::AUTO_HORIZONTAL, 50))
  // Set timings (start, stop) in seconds. Accepts float values as well
  ->setTimeLine(new \Sharapov\FFMpegExtensions\Coordinate\TimeLine(6, 20));

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

// "D:/Projects/php-ffmpeg-extensions/examples/ffmpeg-20170915-6743351-win64-static/bin/ffmpeg.exe" "-y" "-i" "D:/Projects/php-ffmpeg-extensions/examples/source/Vault.mov" "-i" "D:/Projects/php-ffmpeg-extensions/examples/source/demo_video_720p_HD.mp4" "-filter_complex" "[0:v]scale=1280:720[abg],[1:v][abg]overlay[s1],[s1]drawtext=fontfile=D:/Projects/php-ffmpeg-extensions/examples/source/calibri.ttf:text='alphakey demonstration':fontcolor='ffffff@1':fontsize=33:x=(w-tw)/2:y=50:enable='between(t,6,20)',fade=t=in:st=0:d=2,fade=t=out:st=18:d=" "-threads" "12" "-vcodec" "libx264" "-acodec" "libmp3lame" "-b:v" "1000k" "-refs" "6" "-coder" "1" "-sc_threshold" "40" "-flags" "+loop" "-me_range" "16" "-subq" "7" "-i_qfactor" "0.71" "-qcomp" "0.6" "-qdiff" "4" "-trellis" "1" "-b:a" "128k" "D:/Projects/php-ffmpeg-extensions/examples/output/output.mp4"