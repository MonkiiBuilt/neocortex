<?php

namespace App\Http\Controllers\Api;

use App\Models\User;

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