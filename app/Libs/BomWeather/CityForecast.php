<?php
/**
 * Created by PhpStorm.
 * User: aethr
 * Date: 31/05/17
 * Time: 1:05 PM
 */

namespace App\Libs\BomWeather;

class CityForecast
{

    const forecastIconCodes = [
        1 => 'sunny',
        2 => 'clear',
        3 => 'partly-cloudy',
        4 => 'cloudy',
        6 => 'haze',
        8 => 'light-rain',
        9 => 'wind',
        10 => 'flog',
        11 => 'showers',
        12 => 'rain',
        13 => 'dust',
        14 => 'frost',
        15 => 'snow',
        16 => 'storm',
        17 => 'light-showers',
        18 => 'heavy-showers',
        19 => 'tropicalcyclone',
    ];

    /**
     * CityForecast constructor.
     * @param $forecastXmlUrl string URL to an xml-formatted BOM weather forecast.
     */
    public function __construct($forecastXmlUrl, $areaAac)
    {
        $xml = simplexml_load_file($forecastXmlUrl);

        // Station was not found, throw Exception?
        $this->forecasts = [];
        foreach ($xml->forecast->area as $area) {
            if ((string)$area['aac'] == $areaAac) {
                $this->constructFromXml($area);
                break;
            }
        }
    }

    protected function constructFromXml($forecasts) {
        // Pull each forecast out of the XML object
        foreach ($forecasts as $forecast) {
            $index = (string) $forecast['index'];
            $this->forecasts[$index]['day'] = date('D', strtotime((string) $forecast['start-time-local']));
            foreach ($forecast->element as $element) {
                $this->forecasts[$index][(string) $element['type']] = (string) $element;
                if ((string) $element['type'] == 'forecast_icon_code') {
                    $this->forecasts[$index]['icon'] = self::forecastIconCodes[(string) $element];
                }
            }
        }
    }

    /**
     * Accessor for daily forecasts.
     * @return array
     */
    public function getForecast() {
        return $this->forecasts;
    }

}