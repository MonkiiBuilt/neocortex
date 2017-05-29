<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\Items\Image;
use App\Models\Queue;
use Barryvdh\Debugbar\Middleware\Debugbar;
use Neomerx\JsonApi\Encoder\Encoder;

class QueueApiController extends JsonApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Before loading the queue, check for queue entroes that should be
        // retired
        $entriesToRetire = Queue::allReadyForRetirement();
        foreach ($entriesToRetire as $tiredEntry) {
            $tiredEntry->retire();
            $tiredEntry->save();
        }

        // Fetch all queue entries that are still active
        $models = Queue::allActive();

        // If no queue items were found, return a placeholder that reminds
        // people to add great memes.
        if ($models->isEmpty()) {
            $models->push(Queue::fallbackQueueItem());
        }

\Debugbar::debug($models);

        // Encode the model data for json:api consumption
        $encoder = Encoder::instance($this->modelSchemaMappings, $this->encoderOptions);
        $encodedData = $encoder->encodeData($models);

        return response($encodedData)
            ->header('Content-Type', 'application/json');
    }

}