<?php
/**
 * Created by PhpStorm.
 * User: bsly
 * Date: 7/03/2017
 * Time: 4:28 PM
 */

namespace App\Models\Items;

use App\Models\Item;

class YouTube extends Item
{
    const YOUTUBE_API_KEY = "AIzaSyA5la06fkh5uTHSExulSNbTvQxV0cQO1ek";

    /**
     * The value of the item.type field which indicates this item class.
     *
     * @var string
     */
    protected static $singleTableType = 'youtube';

    /**
     * Given a URL, provide a weighted value indicating whether the URL is
     * likely to served by this Item type.
     *
     * Note: this method should only parse the URL for patterns, ItemType
     * controllers should avoid making a request to the URL. If no match is
     * found based on the URL, the system will make a HEAD request to te URL
     * and pass it to the matchHeaders method.
     *
     * @param $url
     * @return integer The 'weight' of a possible match, with 0 meaning "no match".
     */
    public static function matchByURL($url) {
        $url_domain = parse_url($url, PHP_URL_HOST);
        $youtubeDomains = array(
            'youtube.com',
            'www.youtube.com',
            'youtu.be',
        );
        if (in_array($url_domain, $youtubeDomains)) {
            return 10;
        }
        return 0;
    }


    /**
     * Gather additional details before saving to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        // If no url is provided, we can't save this type
        if (!isset($this->details['url'])) {
            throw new Exception();
        }

        // We need to extract the URL to query some data from youtube
        $url_query  = parse_url($this->details['url'], PHP_URL_QUERY);
        parse_str($url_query, $query);

        $vid  = $query['v'];
        $get  = file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=".$vid."&key=".self::YOUTUBE_API_KEY);
        $data = json_decode($get, true);

        if (!isset($data['items']) || !count($data['items'])) {

            throw new Exception();
        }

        $duration = 10000;
        foreach ($data['items'] as $vidTime)
        {
            $duration = self::durationToMilliseconds($vidTime['contentDetails']['duration']);
        }

        $this->details = array_merge($this->details, [
            'vid_id' => $vid,
            'duration' => $duration,
        ]);

        // Back to Eloquent Model->save to persist to db
        return parent::save($options);
    }


    public static function durationToMilliseconds($duration)
    {
        $start = new \DateTime('@0'); // Unix epoch
        $start->add(new \DateInterval($duration));

        return ($start->getTimestamp() * 1000);
    }

}
