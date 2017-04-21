<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Item;
use App\Http\Controllers\Items\BaseItemTypeController;
use App\Http\Controllers\Items\ImageItemTypeController;
use App\Http\Controllers\Items\VideoItemTypeController;

class ItemTypeServiceProvider extends ServiceProvider
{
    // Keep a reference to all available ItemTypeControllers in case an item
    // needs to be classified
    protected static $itemTypeControllers = [
        ImageItemTypeController::class,
        VideoItemTypeController::class,
    ];


    /**
     * Add available ItemType classifiers to a static
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }


    public static function identifyItemType(Item $item) {
        $matches = [];
        $url = $item->details['url'];
        \Log::debug("Identifying item type at $url");
        foreach (self::$itemTypeControllers as $itemType) {
            // Add possible matches
//            \Log::debug("checking {$itemType::$type}");
            $weight = $itemType::matchUrl($url);
            if ($weight > 0) {
                $matches[$weight] = $itemType::getType();
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
        foreach (self::$itemTypeControllers as $itemType) {
            // Add possible matches
//            \Log::debug("checking {$itemType::$type}");
            $weight = $itemType::matchHeaders($headers);
            if ($weight > 0) {
                $matches[$weight] = $itemType::getType();
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
        return '';
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
        $headers = self::parseHeaders($response);

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
