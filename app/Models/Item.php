<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
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

    /**
     * Get the user that owns the item.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
