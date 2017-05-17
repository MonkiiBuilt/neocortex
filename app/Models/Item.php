<?php

namespace App\Models;

use App\Events\ItemCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'items';

    // Minimum needed to save an item: owner, what type of item, and serialised details
    protected $fillable = [
        'user_id',
        'type',
        'details'
    ];

    // When the item is loaded, make sure details are unserialised
    protected $casts = [
        'details' => 'array',
    ];

    protected $events = [
        'created' => ItemCreated::class,
    ];

    /**
     * Get the user that owns the item.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }


    /**
     * Get the item's queue entry if it has one.
     */
    public function queueEntry()
    {
        return $this->hasOne(Queue::class);
    }
}
