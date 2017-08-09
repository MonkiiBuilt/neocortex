<?php
/**
 * Created by PhpStorm.
 * User: bsly
 * Date: 21/06/2017
 * Time: 10:59 AM
 */

namespace App\Libs\ImageProcessing;

class Banderas
{
    /**
     * Throw these as Exception messages to notify user of errors
     */
    const MESSAGES = [
        'banderas.missing' => "You are missing the base Antonio Banderas GIF. Talk to your friendly local developer.",
        'banderas.error' => "Antonio Banderas is not happy! Try again."
    ];


    /**
     * Filename for the base Antonio Banderas GIF
     */
    const BASEFILE = "/uploads/processing/antonio_banderas.gif";


    /**
     * Each scene is made up of start and end keyframes describing position/size of overlay so
     * we can interpolate the between frames
     */
    const SCENES = [
        [
            ['i' => 31, 'x' => 115, 'y' => 19, 'w' => 467, 'h' => 297],
            ['i' => 42, 'x' => 107, 'y' => 19, 'w' => 475, 'h' => 300]
        ],[
            ['i' => 83,  'x' => 92, 'y' => 16, 'w' => 492, 'h' => 314],
            ['i' => 109, 'x' => 77, 'y' => 8,  'w' => 516, 'h' => 326]
        ]
    ];


    /**
     * Inserts JPG/JPEG as screenshot in Antonio Banderas reaction GIF.
     * Exports to new file and returns the url.
     *
     * @param  string
     * @return string
     */
    public static function apply($urlPath)
    {
        // See if we have the base Antonio Banderas GIF
        // if not then throw error
        $basePath = public_path() . self::BASEFILE;
        if (!file_exists($basePath)) {
            throw new \Exception(self::MESSAGES['banderas.missing']);
        }

        $overlayPath = public_path() . $urlPath;

        // Setup the Interpolator with the scenes/keyframes and image paths
        $oi = new OverlayInterpolator(self::SCENES, $basePath, $overlayPath);

        return $oi->getOutputPath();
    }

}