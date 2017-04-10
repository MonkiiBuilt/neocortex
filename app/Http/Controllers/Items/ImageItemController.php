<?php

namespace App\Http\Controllers\Items;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/** ItemTypeController is used to identify and manipulate a specific type of
 * item.
 *
 * Class BaseItemController
 * @package App\Http\Controllers\Items
 */
class ImageItemTypeController extends Controller
{
    protected $type = 'image';

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
    public static function matchURL($url) {
        $extensionMatch = preg_match('/\.(gif|jpg|png)/', $url);
        return $extensionMatch;
    }


    /**
     * If no URL match is found, a HEAD request will be made to the URL so that
     * type matching can be performed on HTTP headers, for example mime type.
     *
     * @param $response
     * @return integer The 'weight' of a possible match, with 0 meaning "no match".
     */
    public static function matchHeaders($response) {
        return 0;
    }


    /**
     * Save an item to the database
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Item $item)
    {
        // Manipulate the item before it is saved in the database
    }


}
