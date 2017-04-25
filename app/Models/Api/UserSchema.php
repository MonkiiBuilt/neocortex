<?php
/**
 * Created by PhpStorm.
 * User: aethr
 * Date: 25/04/17
 * Time: 5:56 PM
 */

namespace App\Models\Api;

use Neomerx\JsonApi\Schema\SchemaProvider;

class UserSchema extends SchemaProvider
{
    protected $resourceType = 'user';

    public function getId($user)
    {
        /** @var User $user */
        return $user->id;
    }

    public function getAttributes($user)
    {
        /** @var User $user */
        return [
        ];
    }

}
