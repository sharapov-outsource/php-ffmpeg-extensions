# php-ffmpeg-extensions
An extensions library for the PHP FFMpeg (https://github.com/PHP-FFMpeg/PHP-FFMpeg)

Installation (via Composer):
============================

For composer installation, add:

```json
"require": {
    "sharapov/php-ffmpeg-extensions": "dev-master"
},
```

to your composer.json file and update your dependencies. Or you can run:

```sh
$ composer require sharapov/php-ffmpeg-extensions
```

Usage:
======

Now you can autoload or use the class via its namespace. Below are examples of how to use the library.

Render simple text overlay
--------------------------

```php
require_once dirname(__FILE__) . '/../vendor/autoload.php';

// Init FFMpeg library
$ffmpeg = \FFMpeg\FFMpeg::create(array(
    'ffmpeg.binaries'  => '/usr/local/bin/ffmpeg', // Path to FFMpeg
    'ffprobe.binaries' => '/usr/local/bin/ffprobe', // Path to FFProbe
    'timeout'          => 3600, // The timeout for the underlying process
    'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
));
$video = $ffmpeg->open(dirname(__FILE__).'/source/demo_video_720p_HD.mp4');

// Create draw overlay filter
$drawText = new Sharapov\FFMpegExtensions\Filters\Video\FilterSimpleOverlay();

// Create text overlay 1
$overlayText = new Sharapov\FFMpegExtensions\Filters\Video\Overlay\Text();
$overlayText
    ->setFontFile(dirname(__FILE__).'/source/arial.ttf') // Set path to font file
    ->setFontColor('#ffffff') // Set font color
    ->setFontSize(33) // Set font size
    ->setOverlayText('This is the default text') // Set overlay text
    ->setCoordinates(new \Sharapov\FFMpegExtensions\Coordinate\Point(230, 150)) // Set coordinates
    ->setTimeLine(new \Sharapov\FFMpegExtensions\Coordinate\TimeLine(1, 6)); // Set timings (start, stop) in seconds

// Pass text overlay to filter
$drawText
    ->setOverlay($overlayText);

// Create text overlay 2
$overlayText = new Sharapov\FFMpegExtensions\Filters\Video\Overlay\Text();
$overlayText
    ->setFontFile(dirname(__FILE__).'/source/arial.ttf') // Set path to font file
    ->setFontColor('#ffffff') // Set font color
    ->setFontSize(28) // Set font size
    ->setOverlayText('This is the default text 2') // Set overlay text
    ->setCoordinates(new \Sharapov\FFMpegExtensions\Coordinate\Point(230, 250)) // Set coordinates
    ->setTimeLine(new \Sharapov\FFMpegExtensions\Coordinate\TimeLine(8, 14)); // Set timings (start, stop) in seconds

// Pass text overlay to filter
$drawText
    ->setOverlay($overlayText);

// Create text overlay 3
$overlayText = new Sharapov\FFMpegExtensions\Filters\Video\Overlay\Text();
$overlayText
    ->setFontFile(dirname(__FILE__).'/source/arial.ttf') // Set path to font file
    ->setFontColor('#ffffff') // Set font color
    ->setFontSize(38) // Set font size
    ->setOverlayText('This is the default text 3') // Set overlay text
    ->setCoordinates(new \Sharapov\FFMpegExtensions\Coordinate\Point(750, 550)) // Set coordinates
    ->setTimeLine(new \Sharapov\FFMpegExtensions\Coordinate\TimeLine(16, 20)); // Set timings (start, stop) in seconds

// Pass text overlay to filter
$drawText
    ->setOverlay($overlayText);

// Apply overlay filter to video
$video
    ->addFilter($drawText);

// Choose output format
$format = new \FFMpeg\Format\Video\X264();
$format->on('progress', function ($video, $format, $percentage) {
  echo "$percentage %<br />";
});

// And render it
$video
    ->save($format, dirname(__FILE__).'/output/export-sample-text-overlay.mp4');
```

You will find other examples in "/examples" folder. 

Changelog
=========


Links
=====
[PHP FFMpeg Homepage](https://github.com/PHP-FFMpeg/PHP-FFMpeg)

[Composer](https://getcomposer.org/)

[GitHub](https://github.com/sharapovweb/php-ffmpeg-extensions)

[Packagist](https://packagist.org/packages/sharapov/php-ffmpeg-extensions)