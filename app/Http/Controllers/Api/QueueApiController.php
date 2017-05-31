<?php

namespace App\Http\Controllers\Api;

use App\Models\Items\Weather;
use App\Models\Queue;
use App\Models\User;
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

        // Keep the weather item up to date
        $this->refreshWeather($models);

\Debugbar::debug($models);

        // Encode the model data for json:api consumption
        $encoder = Encoder::instance($this->modelSchemaMappings, $this->encoderOptions);
        $encodedData = $encoder->encodeData($models);

        return response($encodedData)
            ->header('Content-Type', 'application/json');
    }

    /**
     * Check if a Weather item is in the collection, and if not create one.
     * If the Weather item needs to be updated, refresh it before adding
     * it to the list.
     * @param $models
     */
    protected function refreshWeather(&$models) {
        // Locate the weather item in the queue if there is one
        $weather = $this->pluckWeather($models);

        // Everything is up to date, moving on
        if (!is_null($weather) && !$weather->isOutOfDate()) {
            return;
        }

        // If there is no weather item, create one
        if (is_null($weather)) {
            // Whoever is #1 will own the weather
            $userNumberOne = User::all()->first();
            $weather = new Weather([
                'user_id' => $userNumberOne->id,
            ]);
        }

        // Refresh the data and save
        $weather->refresh();
        $weather->save();

        // Refresh the models collection to include the new item
        $models = Queue::allActive();
    }

    /**
     * Find the Weather item in a collection of Items,
     *
     * @param $models
     * @return Weather|null The plucked Weather object or null.
     */
    protected function pluckWeather($models) {
        // Locate the weather item in the collection if there is one
        $weather = null;
        foreach ($models as $queueEntry) {
            if (is_a($queueEntry->item, Weather::class)) {
                $weather = $queueEntry->item;
            }
        }
        return $weather;
    }
}