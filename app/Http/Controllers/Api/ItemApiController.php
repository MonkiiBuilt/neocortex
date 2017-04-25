<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use Neomerx\JsonApi\Encoder\Encoder;

class ItemApiController extends JsonApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = Item::whereNull('deleted_at')
            ->orderBy('id')
            ->take(10)
            ->get();

        $encoder = Encoder::instance($this->modelSchemaMappings, $this->encoderOptions);
        $encodedData = $encoder->encodeData($models);

        return response($encodedData)
            ->header('Content-Type', 'application/json');
    }

}