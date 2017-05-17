<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
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
        $models = Queue::where('status', '!=', 'retired')
            ->orderBy('created_at')
//            ->take(10)
            ->get();
\Debugbar::debug($models);
        $encoder = Encoder::instance($this->modelSchemaMappings, $this->encoderOptions);
        $encodedData = $encoder->encodeData($models);

        return response($encodedData)
            ->header('Content-Type', 'application/json');
    }

}