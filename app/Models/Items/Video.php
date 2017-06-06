<?php

namespace App\Models\Items;

use App\Models\Item;

class Video extends Item
{
    /**
     * The value of the item.type field which indicates this item class.
     *
     * @var string
     */
    protected static $singleTableType = 'video';


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
        $extensionMatch = preg_match('/\.(mp4|gifv|webm)/', $url);
        if ($extensionMatch) {
            return $extensionMatch;
        }

//        // If no matching file extension, look for other indicators
//        $url_domain = parse_url($url, PHP_URL_HOST);
//        $imageHostingDomains = array(
//            'giphy.com',
//            'imgur.com',
//            'i.imgur.com',
//            'gfycat.com',
//        );
//        if (in_array($url_domain, $imageHostingDomains)) {
//            return 10;
//        }

/*
        $data = YouTube::getDataFromUrl($url);
        $data = Vimeo::getDataFromUrl($url);
 */
        // No match, this doesn't appear to be a video
        return 0;
    }


    /**
     * If no URL match is found, a HEAD request will be made to the URL so that
     * type matching can be performed on HTTP headers, for example mime type.
     *
     * @param $headers
     * @return integer The 'weight' of a possible match, with 0 meaning "no match".
     */
    public static function matchByHeaders($headers) {
        return 0;
    }

    /**
     * Check if we can replace the gifv extension with mp4
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

        $details = $this->details;

        $path_info = pathinfo($details['url']);
        if($path_info['extension'] == 'gifv') {
            $mp4_path = str_replace('.gifv', '.mp4', $details['url']);
            if($this->checkPathExists($mp4_path)) {
                $details['url'] = $mp4_path;
                $this->details = $details;
            }
        }

        // Back to Eloquent Model->save to persist to db
        return parent::save($options);
    }

    /**
     * Use curl to check if a remote file exists.
     *
     * @param $path
     *
     * @return bool
     */
    private function checkPathExists($path) {
        $ch = curl_init($path);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        $resp_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $resp_code == 200;
    }


}
