<?php
/**
 * Created by PhpStorm.
 * User: bsly
 * Date: 7/03/2017
 * Time: 4:28 PM
 */

namespace App\Models\Items;

use App\Models\Item;

class Vimeo extends Item
{
    /**
     * The value of the item.type field which indicates this item class.
     *
     * @var string
     */
    protected static $singleTableType = 'vimeo';


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
        $vimeoDomains = array(
            'vimeo.com',
        );
        if (in_array($url_domain, $vimeoDomains)) {
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

        $url_path = parse_url($this->details['url'], PHP_URL_PATH);
        $vid = (int) substr($url_path, 1);

        $json_url = 'https://vimeo.com/api/v2/video/' . $vid . '.xml';

        $ch = curl_init($json_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        $data = new \SimpleXmlElement($data, LIBXML_NOCDATA);

        if (!isset($data->video) || !isset($data->video->duration)) {
            throw new Exception();
        }

        $this->details = array_merge($this->details, [
            'vid_id' => $vid,
            'duration' => $data->video->duration * 1000
        ]);

        // Back to Eloquent Model->save to persist to db
        return parent::save($options);
    }

}
