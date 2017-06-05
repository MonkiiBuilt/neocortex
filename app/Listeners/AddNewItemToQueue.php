<?php

namespace App\Listeners;

use App\Events\ItemCreated;
use App\Models\Queue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddNewItemToQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ItemCreated  $event
     * @return void
     */
    public function handle(ItemCreated $event)
    {
        // Add the new Item directly to the Queue (do not pass GO)
        $queueEntry = new Queue([
            'item_id' => $event->item->id,
            'status' => Queue::STATUS_ACTIVE,
            'views' => 0,
        ]);
        $queueEntry->save();
    }
}
