<?php

namespace App\Models;

use App\Models\Items\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    public static function allActive($columns = ['*'])
    {
        return (new static)->newQuery()
            ->where('status', static::STATUS_ACTIVE)
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
    public static function allReadyForRetirement($columns = ['*'])
    {
        return static::allActive()
            ->filter(function ($value, $key) {
                return $value->shouldRetire();
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
