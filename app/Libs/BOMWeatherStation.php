<?php
/**
 * Created by PhpStorm.
 * User: aethr
 * Date: 31/05/17
 * Time: 1:05 PM
 */

namespace App\Libs;

class BOMWeatherStation
{
    public function __construct($xmlSource, $stationId)
    {
        $xml = static::parseWeatherXML($xmlSource);
        $this->stationId = $stationId;

        foreach ($xml->observations->station as $station) {
            // Find the station in the list of stations provided in the XML
            if ($station['wmo-id'] == $this->stationId) {
                $this->constructFromStation($station);
            }
        }

        // Station was not found, throw Exception?
    }

    /**
     * @param $file
     * @return \SimpleXMLElement Weather XML data
     */
    protected static function parseWeatherXML($file)
    {
        if ($fileHandle = fopen($file, 'r')) {
            // Read the file into a string
            $xmlstr = '';
            while (!feof($fileHandle)) {
                $xmlstr .= fgets($fileHandle, 4096);
            }
            // Close the file when finished reading
            fclose($fileHandle);

            // Parse the XML from the file into a SimplXMLElement
            return new \SimpleXMLElement($xmlstr);
        }
    }

    protected function constructFromStation($station) {
        // Assign general properties from the station attributes
        $this->bomId = $station['bom-id'];
        $this->name = $station['stn-name'];
        $this->description = $station['description'];
        $this->type = $station['type'];
        $this->timezone = $station['tz'];

        // Location
        $this->height = $station['stn-height'];
        $this->lat = $station['lat'];
        $this->lon = $station['lon'];
        $this->districtId = $station['forecast-district-id'];

        $this->readings = [];
        foreach ($station->period as $period) {
            // Only take the newest reading
            if ($period["index"] == 0) {
                foreach ($period->level[0]->element as $measurement) {
                    // Some measurements have units, include this if available
                    if (isset($measurement['units'])) {
                        $this->readings[(string) $measurement['type']] = [
                            'units' => (string) $measurement['units'],
                            'value' => (string) $measurement
                        ];
                    } else {
                        $this->readings[(string) $measurement['type']] = (string) $measurement;
                    }

                }
            }
        }

    }

    public function getLatestReadings() {
        return $this->readings;
    }
}