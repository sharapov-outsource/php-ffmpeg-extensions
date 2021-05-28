# php-ffmpeg-extensions
An extensions library for the PHP FFMpeg (https://github.com/PHP-FFMpeg/PHP-FFMpeg)

Installation (via Composer):
============================

For composer installation, add:

```json
"require": {
    "sharapov/php-ffmpeg-extensions": "^0.2"
},
```

to your composer.json file and update your dependencies. Or you can run:

```sh
$ composer require sharapov/php-ffmpeg-extensions
```

Usage:
======

Now you can autoload or use the class via its namespace. Below are examples of how to use the library.

Draw texts and boxes
--------------------------

```php

// Init FFMpeg library
$ffmpeg = \Sharapov\FFMpegExtensions\FFMpeg::create(array(
    'ffmpeg.binaries'  => '/path/to/ffmpeg', // Path to FFMpeg
    'ffprobe.binaries' => '/path/to/ffprobe', // Path to FFProbe
    'timeout'          => 3600, // The timeout for the underlying process
    'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
));

// Open source video
$video = $ffmpeg->open( new InputFile( 'source/Coast - 1270.mp4' ) );

// Apply filter options to video
$video
  ->filters()
  ->complex( new OptionsCollection( [
                                      ( ( new FilterComplexOptions\OptionDrawText() )
                                        // Set z-index property. Greater value is always in front
                                        ->setZIndex( 160 )
                                        // You can use fade-in and fade-out effects. Set time in seconds
                                        ->setFadeIn( 1 )
                                        ->setFadeOut( 2 )
                                        // Set font path
                                        ->setFontFile( new InputFile( 'source/arial.ttf' ) )
                                        // Set font color. Accepts transparency value as the second argument. Float value between 0 and 1.
                                        ->setFontColor( '#ffffff' )
                                        // Set font size in pixels
                                        ->setFontSize( 33 )
                                        // Set text string
                                        ->setText( 'php-ffmpeg-extensions library' )
                                        // Coordinates where the text should be rendered. Accepts positive integer or
                                        // constants "(w-tw)/2", "(h-th)/2" to handle auto-horizontal, auto-vertical values
                                        ->setCoordinates( new Coordinate\Point( Coordinate\Point::AUTO_HORIZONTAL, 50 ) )
                                        // Set timings (start, stop) in seconds. Accepts float values as well
                                        ->setTimeLine( new Coordinate\TimeLine( 1, 20 ) ) ),

                                      ( ( new FilterComplexOptions\OptionDrawText() )
                                        ->setZIndex( 160 )
                                        ->setFadeIn( 1 )
                                        ->setFadeOut( 2 )
                                        ->setFontFile( new InputFile( 'source/arial.ttf' ) )
                                        ->setFontColor( '#ffffff' )
                                        ->setFontSize( 28 )
                                        ->setText( 'Sharapov A. (www.sharapov.biz)' )
                                        ->setCoordinates( new Coordinate\Point( 15, 600 ) )
                                        ->setTimeLine( new Coordinate\TimeLine( 2, 20 ) ) ),

                                      ( ( new FilterComplexOptions\OptionDrawBox() )
                                        ->setZIndex( 130 )
                                        ->setColor( '000000', 0.6 )
                                        ->setDimensions( new Coordinate\Dimension( Coordinate\Dimension::WIDTH_MAX, 60 ) )
                                        ->setCoordinates( new Coordinate\Point( 0, 580 ) ) ),

                                      ( ( new FilterComplexOptions\OptionDrawText() )
                                        ->setZIndex( 160 )
                                        ->setFadeIn( 1 )
                                        ->setFadeOut( 2 )
                                        ->setFontFile( new InputFile( 'source/arial.ttf' ) )
                                        ->setFontColor( '#ffffff' )
                                        ->setFontSize( 28 )
                                        ->setText( 'v2.0' )
                                        ->setCoordinates( new Coordinate\Point( 1200, 600 ) )
                                        ->setTimeLine( new Coordinate\TimeLine( 3, 20 ) ) )
                                    ] ) );

// Run render
$format = new \FFMpeg\Format\Video\X264( 'libmp3lame' );
$format->on( 'progress', function ( $video, $format, $percentage ) {
  echo "$percentage% transcoded\n";
} );

try {
  $video
    ->save( $format, 'output/output.mp4' );
  print 'Done!';
} catch ( ExecutionFailureException $e ) {
  print $e->getMessage();
}
```

You will find other examples in "/examples" folder.

Changelog
=========


Links
=====
[PHP FFMpeg Homepage](https://github.com/PHP-FFMpeg/PHP-FFMpeg)

[Composer](https://getcomposer.org/)

[GitHub](https://github.com/sharapov-outsource/php-ffmpeg-extensions)

[Packagist](https://packagist.org/packages/sharapov/php-ffmpeg-extensions)
