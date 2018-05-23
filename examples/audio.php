<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 */

ini_set('display_errors', 1);
date_default_timezone_set('UTC');
require_once dirname(__FILE__) . '/../vendor/autoload.php';

$logger = new \Monolog\Logger('debug');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('render.log', \Monolog\Logger::ERROR));
$logger->pushHandler(new \Monolog\Handler\StreamHandler('info.log', \Monolog\Logger::INFO));

if($_SERVER['SERVER_NAME'] == 'ffmpeg.local') {

  $ffmpeg = \Sharapov\FFMpegExtensions\FFMpeg::create([
                                                          'ffmpeg.binaries'  => 'D:\Projects\videomachine2\ffmpeg\bin\ffmpeg.exe', // Path to FFMpeg
                                                          'ffprobe.binaries' => 'D:\Projects\videomachine2\ffmpeg\bin\ffprobe.exe', // Path to FFProbe
                                                          'timeout'          => 3600, // The timeout for the underlying process
                                                          'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
                                                      ], $logger);


} else {

  $ffmpeg = \Sharapov\FFMpegExtensions\FFMpeg::create([
                                                        //'ffmpeg.binaries'  => 'D:\Projects\videomachine2\ffmpeg\bin\ffmpeg.exe', // Path to FFMpeg
                                                        //'ffprobe.binaries' => 'D:\Projects\videomachine2\ffmpeg\bin\ffprobe.exe', // Path to FFProbe
                                                          'ffmpeg.binaries'  => '/home/ezmembersarea/videoapp/app/module/RenderEngine/FFmpegStatic/ffmpeg',
                                                          'ffprobe.binaries' => '/home/ezmembersarea/videoapp/app/module/RenderEngine/FFmpegStatic/ffprobe',
                                                          'timeout'          => 3600, // The timeout for the underlying process
                                                          'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
                                                      ], $logger);
}
$audio = $ffmpeg->open(new \Sharapov\FFMpegExtensions\Input\File(dirname(__FILE__) . '/source/LastResort.mp3'));

$options = new \Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions\OptionsCollection();
$file1 = new \Sharapov\FFMpegExtensions\Filters\Audio\MergeFilterOptions\OptionAudioFile(new \Sharapov\FFMpegExtensions\Input\File(dirname(__FILE__) . '/source/LastStand.mp3'));
$file1
    ->setVolumeLevel(0.2);
$options
    ->add($file1);

$audio
    ->filters()
    ->combineStereos($options);
//->stereo2mono();
//->mono2stereo(new \Sharapov\FFMpegExtensions\Input\File(dirname(__FILE__) . '/source/mono2.mp3'));
//->merge($filterOptions);

//print '<pre>';
//print_r($video);
//print '</pre>';

$format = new \FFMpeg\Format\Audio\Mp3();
$format->on('progress', function($video, $format, $percentage) {
  print $percentage . "\n";
});

$audio->save($format, dirname(__FILE__) . '/output/output.mp3');
die("\n<br />1");


/*
 *
'/home/ezmembersarea/videoapp/app/module/RenderEngine/FFmpegStatic/ffmpeg' '-y' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/demo_video_720p_HD.mp4' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/intro_720p_muted.mp4' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/bg_green.jpeg' '-filter_complex' '[1:v]scale=120:60[t1],[0:v][t1]overlay=130:180' '-b:v' '1000k' '-refs' '6' '-coder' '1' '-sc_threshold' '40' '-flags' '+loop' '-me_range' '16' '-subq' '7' '-i_qfactor' '0.71' '-qcomp' '0.6' '-qdiff' '4' '-trellis' '1' '-b:a' '128k' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/output/output.mp4'
 *
 *
 *
 *
 *
 *
 '/home/ezmembersarea/videoapp/app/module/RenderEngine/FFmpegStatic/ffmpeg' '-y' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/demo_video_720p_HD.mp4' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/bg_green.jpeg' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/intro_720p_muted.mp4' '-filter_complex' '[1:v]scale=120:60[s2],[0:v][s2]overlay=130:150[s3],[2:v]scale=120:60[s4],[s3][s4]overlay=130:180[s5],[s5]drawtext=fontfile=/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/OpenSansRegular.ttf:text='\''Layer2'\'':fontcolor='\''#ffffff@1'\'':fontsize=20:x=130:y=160:box=1:boxcolor='\''000000'\''@1:boxborderw=10,drawtext=fontfile=/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/OpenSansRegular.ttf:text='\''Layer1'\'':fontcolor='\''#ffffff@1'\'':fontsize=20:x=130:y=150:box=1:boxcolor='\''red'\''@1:boxborderw=10' '-b:v' '1000k' '-refs' '6' '-coder' '1' '-sc_threshold' '40' '-flags' '+loop' '-me_range' '16' '-subq' '7' '-i_qfactor' '0.71' '-qcomp' '0.6' '-qdiff' '4' '-trellis' '1' '-b:a' '128k'  '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/output/output.mp4'
 *
 *
 '/home/ezmembersarea/videoapp/app/module/RenderEngine/FFmpegStatic/ffmpeg' '-y' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/demo_video_720p_HD.mp4' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/intro_720p_muted.mp4' '-i' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/bg_green.jpeg' '-filter_complex' '[2:v]scale=120:60[s4],[0:v][s4]overlay=130:180[s6],[1:v]scale=120:60[s2],[s2][s2]overlay=130:150[s3],[s3]drawtext=fontfile=/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/OpenSansRegular.ttf:text='\''Layer2'\'':fontcolor='\''#ffffff@1'\'':fontsize=20:x=130:y=160:box=1:boxcolor='\''000000'\''@1:boxborderw=10,drawtext=fontfile=/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/source/OpenSansRegular.ttf:text='\''Layer1'\'':fontcolor='\''#ffffff@1'\'':fontsize=20:x=130:y=150:box=1:boxcolor='\''red'\''@1:boxborderw=10' '-b:v' '1000k' '-refs' '6' '-coder' '1' '-sc_threshold' '40' '-flags' '+loop' '-me_range' '16' '-subq' '7' '-i_qfactor' '0.71' '-qcomp' '0.6' '-qdiff' '4' '-trellis' '1' '-b:a' '128k' '/home/ezmembersarea/public_html/app/ffmpeg-ext/examples/output/output.mp4'
 *
 */

$video2 = $ffmpeg->open(new \Sharapov\FFMpegExtensions\Input\File(dirname(__FILE__) . '/source/EZSCtest1a.mov'));

$audio = $ffmpeg->open(new \Sharapov\FFMpegExtensions\Input\File(dirname(__FILE__) . '/source/demo-sound.mp3'));


print '<pre>';
print_r($video);
print '</pre>';

print '<pre>';
print_r($video2);
print '</pre>';

print '<pre>';
print_r($audio);
print '</pre>';