<?php

namespace App\Models\Factories;

use App\Models\Item;
use App\Exceptions\UnknownItemTypeException;

class ItemFactory
{
    /**
     * Create a new Item
     * @param array $attributes
     * @return mixed
     */
    public static function create(array $attributes) {

        // Replace .gifv with .mp4 if possible...
        static::checkUrl($attributes['details']['url']);

        // If the provided attributes don't include a type, try to determine
        // the item type based on what's provided
        if (!Item::getTypeFrom($attributes)) {
            $type = Item::identifyItemType($attributes);
            // Set the correct type field based on the type that was found
            $attributes[Item::getTypeField()] = $type;
        }

        $itemTypeMap = Item::getSingleTableTypeMap();

        // Find a class that matches the type attribute
        foreach ($itemTypeMap as $typeValue => $typeClass) {
            // If a match is found, return an instance of the matching class
            if ($typeValue == $attributes[Item::getTypeField()]) {
                return new $typeClass($attributes);
            }
        }

        throw new UnknownItemTypeException();
    }

    /**
     * Gifv files often have mp4 files in the same location. Chrome prefers
     * the mp4s to gifv so swap em over if we can.
     *
     * @param $url
     */
    private static function checkUrl(&$url) {
        $path_info = pathinfo($url);
        if($path_info['extension'] == 'gifv') {
            $mp4_path = str_replace('.gifv', '.mp4', $url);
            if(static::checkPathExists($mp4_path)) {
                $url = $mp4_path;
            }
        }
    }

    /**
     * Use curl to check if a remote file exists.
     *
     * @param $path
     *
     * @return bool
     */
    private static function checkPathExists($path) {
        $ch = curl_init($path);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        $resp_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $resp_code == 200;
    }

    /**
     * Attempt to identify the item type based on the available attributes.
     * @param $attributes
     * @return array
     */
    public static function identifyItemType($attributes) {
        if (isset($attributes['details']['url'])) {
            // If item type can be determined by URL, return it
            $typeFromURL = static::identifyItemTypeByURL($attributes['details']['url']);
            if ($typeFromURL) {
                return $typeFromURL;
            }
        }
    }

    /**
     * Attempt to identify the item type based on URL, if one is provided.
     * @param $url
     * @return mixed|null
     */
    public static function identifyItemTypeByURL($url) {
        $matches = [];

        \Log::debug("Identifying item type at $url");
        foreach (static::getSingleTableTypeMap() as $itemType => $itemClass) {
            // Add possible matches
//            \Log::debug("checking {$itemType::$type}");
            $weight = $itemClass::matchByUrl($url);
            if ($weight > 0) {
                $matches[$weight] = $itemType;
            }
        }

        // If any matches were found, return the highest weight
        if (count($matches)) {
            // Sort the array with the highest weight first
            krsort($matches);
            // Return the item type with the highest weight
            return array_shift($matches);
        }

        // If no match was found, make a request for the URL and try to
        // classify it based on the returned headers
        $headers = self::requestUrlHeaders($url);
        foreach (static::getSingleTableTypeMap() as $itemType => $itemClass) {
            // Add possible matches
//            \Log::debug("checking {$itemType::$type}");
            $weight = $itemClass::matchByHeaders($headers);
            if ($weight > 0) {
                $matches[$weight] = $itemType;
            }
        }

        // If matches were found based on headers, return the highest weight
        if (count($matches)) {
            // Sort the array with the highest weight first
            krsort($matches);
            // Return the item type with the highest weight
            return array_shift($matches);
        }

        // No match was found, so no type identified
        return null;
    }

    private static function requestUrlHeaders($url) {
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);
//        curl_setopt ($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

        // Only execute a HEAD request
        curl_setopt($ch, CURLOPT_HEADER, true); // header will be at output
        curl_setopt($ch, CURLOPT_NOBODY, true);

        $content = curl_exec ($ch);
        curl_close ($ch);

        // Split cURL response string into headers array
        $response = explode("\n", $content);
        $status = array_shift($response);

        // TODO throw an exception for non-200 response codes

        // Parse the response string into an array of headers => values
        $headers = static::parseHeaders($response);

        return $headers;
    }

    /**
     * Split each header line into header name and value.
     *
     * @param $response
     * @return array
     */
    private static function parseHeaders($response) {
        // Parse the remaining lines into header and value
        $headers = [];
        foreach ($response as $headerString) {
            $headerParts = explode(": ",$headerString,2);

            // Any line that doesn't have a : isn't a well formed header
            if (count($headerParts) < 2) {
                continue;
            }
            $headers[trim($headerParts[0])] = trim($headerParts[1]);
        }

        // Return status and headers
        return $headers;
    }

}
