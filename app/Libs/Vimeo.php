<?php
/**
 * Created by PhpStorm.
 * User: bsly
 * Date: 7/03/2017
 * Time: 4:28 PM
 */

namespace App\Libs;

class Vimeo
{
    /**
     * Return detail array from vimeo url
     *
     * @return array
     */
    public static function getDataFromUrl($url)
    {
        $url_path = parse_url($url, PHP_URL_PATH);
        $vid = (int) substr($url_path, 1);

        $json_url = 'https://vimeo.com/api/v2/video/' . $vid . '.xml';

        $ch = curl_init($json_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        $data = new \SimpleXmlElement($data, LIBXML_NOCDATA);

        if (!isset($data->video) || !isset($data->video->duration)) {
            return [];
        }

        return [
            'url' => $url,
            'vid_id' => $vid,
            'duration' => $data->video->duration * 1000
        ];
    }

}
