<?php
/**
 * Created by PhpStorm.
 * User: aethr
 * Date: 25/04/17
 * Time: 5:56 PM
 */

namespace App\Models\Api;

use Neomerx\JsonApi\Schema\SchemaProvider;

class QueueSchema extends SchemaProvider
{
    protected $resourceType = 'item';

    public function getId($queueItem)
    {
        /** @var Item $item */
        return $queueItem->id;
    }

    public function getAttributes($queueItem)
    {
        /** @var Item $item */
        return [
            'item_type' => $queueItem->item->type,
            'item' => $queueItem->item,
        ];
    }

    public function getRelationships($queueItem, $isPrimary, array $includeList)
    {
        /** @var Item $item */
        return [
//            'item' => [
//                self::DATA => $queueItem->item->id
//            ],
        ];
    }
}
