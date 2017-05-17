<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Queue extends Model
{
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
}
