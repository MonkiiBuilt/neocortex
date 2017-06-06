<?php

namespace App\Models\Items;

use App\Libs\BomWeather\CityForecast;
use App\Libs\BomWeather\WeatherStation;
use App\Models\Item;
use App\Models\Queue;
use Carbon\Carbon;

class Weather extends Item
{
    const WEATHER_UPDATE_MINUTES = 1;

    /**
     * The value of the item.type field which indicates this item class.
     *
     * @var string
     */
    protected static $singleTableType = 'weather';

    /**
     * When weather items are added to the queue they have a permanent status.
     *
     * @var string
     */
    public $initial_queue_status = Queue::STATUS_PERMANENT;

    /**
     * Fetch up to date weather data and save it to this item's details.
     */
    public function refresh()
    {
        $freshReadings = static::fetchRemoteWeatherReadings();
        $this->fill(['details' => $freshReadings]);
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
        // retirement logic. #science
    }


    /**
     * Fetch the current weather readings from BOM and instantiate a helper
     * object.
     *
     * @return array
     */
    public static function fetchRemoteWeatherReadings()
    {
        // TODO put these in a config?
        $stationRemoteXml = 'ftp://ftp.bom.gov.au/anon/gen/fwo/IDV60920.xml';
        $stationId = 95936;
        $readings = new WeatherStation($stationRemoteXml, $stationId);

        $forecastRemoteXml = 'ftp://ftp.bom.gov.au/anon/gen/fwo/IDV10753.xml';
        $areaCode = 'VIC_PT042';
        $forecast = new CityForecast($forecastRemoteXml, $areaCode);

        // Return a basic array with readings and values
        $weatherDetails = [
            'readings' => $readings->getLatestReadings(),
            'forecasts' => $forecast->getForecast(),
        ];

        return $weatherDetails;
    }

}