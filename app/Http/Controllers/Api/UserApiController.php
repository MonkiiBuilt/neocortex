<?php

namespace App\Http\Controllers\Api;

//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\View;


use App\Models\User;
use NilPortugues\Laravel5\JsonApi\Controller\JsonApiController;

class UserApiController extends JsonApiController
{
    /**
     * Return the Eloquent model that will be used
     * to model the JSON API resources.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getDataModel()
    {
        return new User();
    }

}