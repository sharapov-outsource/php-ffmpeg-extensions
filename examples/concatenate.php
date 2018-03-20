<?php
/**
 * Created by PhpStorm.
 * User: Sharapov A. <alexander@sharapov.biz>
 * Web: http://sharapov.biz
 * Date: 18.03.2018
 * Time: 23:46
 */


ini_set('display_errors', 1);
date_default_timezone_set('UTC');
require_once dirname(__FILE__) . '/../vendor/autoload.php';

// Init ffmpeg library
$ffmpeg = \Sharapov\FFMpegExtensions\FFMpeg::create([
                                                        'ffmpeg.binaries'  => dirname(__FILE__).'/../../../ffmpeg-static/ffmpeg-3.4.2-64bit-static/ffmpeg',
                                                        'ffprobe.binaries' => dirname(__FILE__).'/../../../ffmpeg-static/ffmpeg-3.4.2-64bit-static/ffprobe',
                                                        'timeout'          => 3600, // The timeout for the underlying process
                                                        'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
                                                    ]);

// $ffmpeg->open('source/ez/spokesperson-clip-01.mp4')

$collection = new \Sharapov\FFMpegExtensions\Media\VideoCollection();


$collection
  ->add($ffmpeg->open('source/ez/spokesperson-clip-01.mp4'))
  ->add($ffmpeg->open('source/ez/spokesperson-clip-02.mp4'));

$result = $ffmpeg->concatenate($collection);




