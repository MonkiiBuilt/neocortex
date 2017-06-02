<?php

namespace App\Models\Items;

use App\Libs\BOMWeatherStation;
use App\Models\Item;
use App\Models\Queue;
use Carbon\Carbon;

class Weather extends Item
{
    const WEATHER_UPDATE_MINUTES = 10;

    /**
     * The value of the item.type field which indicates this item class.
     *
     * @var string
     */
    protected static $singleTableType = 'weather';

    /**
     * Fetch up to date weather data and save it to this item's details.
     */
    public function refresh()
    {
        $freshData = static::fetchRemoteWeatherData();
        $this->fill(['details' => $freshData]);
    }


    /**
     * @return bool Returns true if stored weather data should be refreshed.
     */
    public function isOutOfDate()
    {
        if ($this->updated_at->lte(Carbon::now()->subMinutes(self::WEATHER_UPDATE_MINUTES))) {
            \Debugbar::debug("Weather is out of date");
            return true;
        }
        return false;

    }


    /**
     * The weather never gets old.
     *
     * @param $query
     * @return bool
     */
    public static function readyToRetireTypeQueryCondition($query) {
        // This method adds no conditions to check for weather items to retire,
        // but its existence causes weather items to be exempt from the default
        // retirement logic
    }


    /**
     * Fetch the current weather readings from BOM and instantiate a helper
     * object.
     *
     * @return array
     */
    public static function fetchRemoteWeatherData()
    {
        $dataFile = 'ftp://ftp.bom.gov.au/anon/gen/fwo/IDV60920.xml';
        $stationId = 95936;

        $weather = new BOMWeatherStation($dataFile, $stationId);

        // Return a basic array with readings and values
        return $weather->getLatestReadings();
    }


}