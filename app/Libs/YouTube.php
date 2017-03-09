<?php
/**
 * Created by PhpStorm.
 * User: bsly
 * Date: 7/03/2017
 * Time: 4:28 PM
 */

namespace App\Libs;

class YouTube
{
    const YOUTUBE_API_KEY = "AIzaSyA5la06fkh5uTHSExulSNbTvQxV0cQO1ek";

    /**
     * Return detail array from youtube url
     *
     * @return array
     */
    public static function getDataFromUrl($url)
    {
        $url_query  = parse_url($url, PHP_URL_QUERY);
        parse_str($url_query, $query);

        $vid  = $query['v'];
        $get  = file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=".$vid."&key=".self::YOUTUBE_API_KEY);
        $data = json_decode($get, true);

        if (!isset($data['items']) || !count($data['items'])) {
            return [];
        }

        $duration = 10000;
        foreach ($data['items'] as $vidTime)
        {
            $duration = self::durationToMilliseconds($vidTime['contentDetails']['duration']);
        }

        return [
            'url' => $url,
            'vid_id' => $vid,
            'duration' => $duration
        ];
    }

    public static function durationToMilliseconds($duration)
    {
        $start = new \DateTime('@0'); // Unix epoch
        $start->add(new \DateInterval($duration));

        return ($start->getTimestamp() * 1000);
    }

}
