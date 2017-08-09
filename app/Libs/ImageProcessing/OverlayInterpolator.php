<?php
/**
 * Created by PhpStorm.
 * User: bsly
 * Date: 28/06/2017
 * Time: 5:44 PM
 */

namespace App\Libs\ImageProcessing;


class OverlayInterpolator
{
    /**
     * Throw these as Exception messages to notify user of errors
     */
    const MESSAGES = [
        'keyframes.missing' => "Error in Processing library. One of the scenes is missing a keyframe",
        'interpolation.error' => "Error in Processing library. Error interpolating keyframes"
    ];

    protected $scenes;
    protected $baseIM;
    protected $overlayIM;
    protected $outputPath;

    public function __construct($scenes, $basePath, $overlayPath)
    {
        // Extra memory for Imagick
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', '120');

        $this->scenes = $scenes;

        // TODO: Check if input image is too big and resize to manageable size
        // TODO:   that won't run out of ram or timeout?
        $this->overlayIM = new \Imagick($overlayPath);
        $this->overlayIM->modulateImage(100, 72, 100); // Desaturate a smidge for blending

        // Create a new imagick object and read in base GIF
        $this->baseIM = new \Imagick($basePath);

        $this->processFrames($overlayPath);

        // Write output to gif
        $overlayParts = pathinfo($overlayPath);
        $this->outputPath = '/uploads/' . $overlayParts['filename'] . '.gifv';
        $this->baseIM->writeImages(public_path() . $this->outputPath, true);
    }

    public function processFrames($overlayPath)
    {
        // Loop through the gif frames and add the overlay only to the
        //   frames specified in the $scenes array
        foreach ($this->baseIM as $frame) {
            $frameNum = $frame->getIteratorIndex();
            $sceneNum = $this->getSceneNum($frameNum);

            if ($this->overlayRequried($sceneNum)) {
                $transform = $this->getTransform($sceneNum, $frameNum);

                // Adjust size if required
                if ($this->overlayIM->getImageWidth()  != 2 * $transform['w'] ||
                    $this->overlayIM->getImageHeight() != 2 * $transform['h']) {
                    $this->overlayIM = new \Imagick($overlayPath);
                    $this->overlayIM->resizeImage(2 * $transform['w'], 2 * $transform['h'], \Imagick::FILTER_CATROM, 2, false);
                    $this->overlayIM->modulateImage(100, 72, 100); // Desaturate a smidge for blending
                }

                // Set overlay to position. Resizing allows some sub-pixel positioning.
                $frame->magnifyImage(); // Doubles size
                $frame->compositeImage($this->overlayIM, \Imagick::COMPOSITE_ATOP, 2 * $transform['x'], 2 * $transform['y']);
                $frame->minifyImage(); // Halves size
            }
        }
    }

    public function getSceneNum($frameNum)
    {
        // Check if frame is situated in any of the scenes
        $numScenes = count($this->scenes);
        for ($i = 0; $i < $numScenes; $i++) {
            $startFrame = $this->scenes[$i][0]['i'];
            $endFrame   = $this->scenes[$i][$numScenes-1]['i'];

            if ($frameNum >= $startFrame && $frameNum <= $endFrame) return $i;
        }

        return -1;
    }

    public function overlayRequried($sceneNum)
    {
        if ($sceneNum != -1) return true;
        return false;
    }

    /*
     * Interpolate the position and size values from the scenes given the frame number
     */
    public function getTransform($sceneNum, $frameNum)
    {
        // Get the keyframes for the relevant scene
        $keyframes = $this->scenes[$sceneNum];
        if (count($keyframes) < 2) {
            throw new \Exception(self::MESSAGES['keyframes.missing']);
        }

        try {
            $startFrame = $keyframes[0]['i'];
            $endFrame   = $keyframes[1]['i'];

            $diffFrames = $endFrame - $startFrame;
            $posFrame   = $frameNum - $startFrame;

            $pcPos = $posFrame / $diffFrames;

            $currX = $this->interpolateValue($keyframes[0]['x'], $keyframes[1]['x'], $pcPos);
            $currY = $this->interpolateValue($keyframes[0]['y'], $keyframes[1]['y'], $pcPos);
            $currWidth  = $this->interpolateValue($keyframes[0]['w'], $keyframes[1]['w'], $pcPos);
            $currHeight = $this->interpolateValue($keyframes[0]['h'], $keyframes[1]['h'], $pcPos);
        }
        catch (\Exception $e) {
            throw new \Exception(self::MESSAGES['interpolation.error']);
        }

        return ['x' => $currX, 'y' => $currY, 'w' => $currWidth, 'h' => $currHeight];
    }

    /*
     * Determine the value from the position between the start and end values
     */
    public function interpolateValue($startVal, $endVal, $pcPos)
    {
        $diffVal = $endVal  - $startVal;
        $currVal = $diffVal * $pcPos;

        return $startVal + $currVal;
    }

    public function getOutputPath()
    {
        return $this->outputPath;
    }

}