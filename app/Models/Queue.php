<?php

namespace App\Models;

use App\Models\Items\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Queue extends Model
{
    const STATUS_ACTIVE = 'active';
    const STATUS_RETIRED = 'retired';
    const STATUS_PERMANENT = 'permanent';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'queue';

    // Minimum needed to save an item: owner, what type of item, and serialised details
    protected $fillable = [
        'item_id',
        'status',
        'views'
    ];

    /**
     * Get the user that owns the item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Should the item associated with this queue entry be retired.
     * @return bool Returns true if the item is ready for retirement.
     */
    public function shouldRetire() {
        \Log::debug("Checking item {$this->item->id} for retirement");
        return $this->item->shouldRetire($this);
    }

    /**
     * Mark an active item as retired;
     */
    public function retire() {
        $this->status = self::STATUS_RETIRED;
    }


    /**
     * Get all of the models from the database who have an active status but
     * should be retired.
     *
     * @param  array|mixed  $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function allActive($columns = ['*'], $include_permanent = TRUE)
    {

        $statues = $include_permanent ? [static::STATUS_ACTIVE, static::STATUS_PERMANENT] : [static::STATUS_ACTIVE];

        return (new static)->newQuery()
            ->whereIn('status', $statues)
            ->get(
                is_array($columns) ? $columns : func_get_args()
            );
    }


    /**
     * Get all of the models from the database who have an active status but
     * should be retired.
     *
     * @param  array|mixed  $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function allReadyToRetire()
    {
        // Basic query to fetch active items
        $readyToRetireQuery = DB::table('queue')
            ->join('items', 'queue.item_id', '=', 'items.id')
            ->where('queue.status', QUEUE::STATUS_ACTIVE)
            ->where(function ($query) {
                static::itemTimeRetirementQueryBuilder($query);
            });
        $results = $readyToRetireQuery->get(['queue.*']);

        // Passing ->get() values directly to hydrate no longer works in
        // Laravel 5 (for some reason), so manually reconstruct as array
        $toRetire = collect($results)->map(function($x){ return (array) $x; })->toArray();

        // Return a hydrated collection of Item/Image/etc objects
        return self::hydrate($toRetire);
    }

    public static function itemTimeRetirementQueryBuilder($itemRetireQuery) {

        $itemTypeMap = Item::getSingleTableTypeMap();

        // Find a class that matches the type attribute
        $itemTypesWithFilters = [];
        foreach ($itemTypeMap as $typeValue => $typeClass) {
            // If a match is found, return an instance of the matching class
            if (method_exists($typeClass, 'readyToRetireTypeQueryCondition')) {
                // Add a conditional to the query to find items that are ready
                // to retire based on item-specific criteria
                $itemRetireQuery->orWhere(function($query) use ($typeClass) {
                    $typeClass::readyToRetireTypeQueryCondition($query);
                });

                // Record items with specific filtering logic
                $itemTypesWithFilters[] = $typeValue;
            }
        }

        // Add a catch all retirement filter for item types without their own
        // specific logic
        $itemRetireQuery->orWhere(function ($query) use ($itemTypesWithFilters) {
            $query->whereNotIn('items.'. Item::getTypeField(), $itemTypesWithFilters);
            Item::readyToRetireQueryCondition($query);
        });
    }

    /**
     * Return a "faked" item with a pre-set image for when the queue is empty.
     * @return static
     */
    public static function fallbackQueueItem() {
        // Manually set the type as the STI lib won't catch this when it isnt
        // going in the db
        $fallbackItem = new Image([
            'type' => 'image',
            'details' => ['url' => 'http://www.gifbin.com/bin/102013/1383326970_dog_in_space.gif'],
        ]);
        $fallbackItem->id = -1; // No item should have this id

        // Fake a queue item and attack the image
        $fallbackQueue = new static([
            'status' => 'active',
            'views' => 0,
        ]);
        $fallbackQueue->item = $fallbackItem;

        return $fallbackQueue;
    }
}
