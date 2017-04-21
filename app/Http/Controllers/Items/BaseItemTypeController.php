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
class BaseItemTypeController extends Controller
{
    protected static $type = 'item';

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
     * @return int The 'weight' of a possible match, with 0 meaning "no match".
     */
    public static function matchURL($url) {
        return 0;
    }


    /**
     * If no URL match is found, a HEAD request will be made to the URL so that
     * type matching can be performed on HTTP headers, for example mime type.
     *
     * @param $headers
     * @return int The 'weight' of a possible match, with 0 meaning "no match".
     */
    public static function matchHeaders($headers) {
        return 0;
    }


    /**
     * Add or change properties within item->details before it's saved to the
     * database.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Item $item)
    {
        // Manipulate item->details before it is saved in the database
    }


    /**
     * @return string The item type referenced by the class being matched.
     */
    public static function getType() {
        return static::$type;
    }
}
