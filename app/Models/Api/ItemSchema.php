<?php
/**
 * Created by PhpStorm.
 * User: aethr
 * Date: 25/04/17
 * Time: 5:56 PM
 */

namespace App\Models\Api;

use Neomerx\JsonApi\Schema\SchemaProvider;

class ItemSchema extends SchemaProvider
{
    protected $resourceType = 'item';

    public function getId($item)
    {
        /** @var Item $item */
        return $item->id;
    }

    public function getAttributes($item)
    {
        /** @var Item $item */
        return [
            'item_type' => $item->type,
            'details' => $item->details,
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeList)
    {
        /** @var Item $item */
        return [
            'user' => [
                self::DATA => $item->user
            ],
        ];
    }
}
