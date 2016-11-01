<?php

namespace App\Http\Controllers\Api;

//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\View;


use App\Models\Item;
use NilPortugues\Laravel5\JsonApi\Controller\JsonApiController;

class ItemApiController extends JsonApiController
{
    /**
     * Return the Eloquent model that will be used
     * to model the JSON API resources.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getDataModel()
    {
        return new Item();
    }

}