<?php

namespace App\Models\Items;

use App\Models\Item;
use App\Models\Queue;
use Carbon\Carbon;

class Image extends Item
{
    const IMAGE_ACTIVE_MINUTES = 180;

    /**
     * The value of the item.type field which indicates this item class.
     *
     * @var string
     */
    protected static $singleTableType = 'image';

    /**
     * When items are first added to the queue they are created with this status.
     *
     * @var string
     */
    public $initial_queue_status = Queue::STATUS_PERMANENT;

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
        // Try to match based on file extension
        $extensionMatch = preg_match('/\.(gif|jpg|jpeg|png)/', $url);
        if ($extensionMatch) {
            return $extensionMatch;
        }
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
     * Returns true if an Item should be retired from the queue.
     *
     * @param Builder $query
     * Item.
     * @return bool
     */
    public static function readyToRetireTypeQueryCondition($query) {
        // Add a clause to an existing query that will find items that meet
        // the criteria for retirement
        $typeField = static::$singleTableTypeField;
        $typeValue = self::$singleTableType;

        // Only apply this logic to Image items
        $query->where('items.'. $typeField, $typeValue);

        // Item images live for a different amount of time
        $query->where('queue.created_at', '<', Carbon::now()->subMinutes(self::IMAGE_ACTIVE_MINUTES));

        // Do not let the last active Image be retired
        $lastActiveImage = \DB::table('queue')
                        ->join('items', 'queue.item_id', '=', 'items.id')
                        ->where('queue.status', QUEUE::STATUS_ACTIVE)
                        ->where('items.'. $typeField, $typeValue)
                        ->orderBy('queue.created_at', 'DESC')
                        ->limit(1)
                        ->pluck('queue.id');
        $query->where('queue.id', '<>', $lastActiveImage);
    }

}
