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
    protected static $itemTypeControllers = [];


    /**
     * Add available ItemType classifiers to a static
     *
     * @return void
     */
    public function boot()
    {
        // Add any new ItemTypeControllers here
        $itemTypes = [
            ImageItemTypeController::class,
            VideoItemTypeController::class,
        ];

        foreach ($itemTypes as $controllerClass) {
            ItemTypeServiceProvider::registerItemType($controllerClass);
        }
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

    /**
     * @param $itemTypeController class
     */
    public static function registerItemType($itemTypeController) {
        self::$itemTypeControllers[] = $itemTypeController;
    }


    public static function identifyItemType(Item $item) {
        $matches = [];
        $url = $item->details['url'];
        \Log::debug($url);
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
            return array_shift($matches);
        }

        // If no match was found, make a request for the URL and try to
        // classify it based on the returned headers

        // No match was found, so no type identified
        return '';
    }
}
