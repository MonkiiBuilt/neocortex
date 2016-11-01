<?php

namespace App\Model\Api;

use App\Models\Item;
use NilPortugues\Api\Mappings\JsonApiMapping;

class ItemTransformer implements JsonApiMapping
{
    /**
     * Returns a string with the full class name, including namespace.
     *
     * @return string
     */
    public function getClass()
    {
        return Item::class;
    }

    /**
     * Returns a string representing the resource name
     * as it will be shown after the mapping.
     *
     * @return string
     */
    public function getAlias()
    {
        return 'item';
    }

    /**
     * Returns an array of properties that will be renamed.
     * Key is current property from the class.
     * Value is the property's alias name.
     *
     * @return array
     */
    public function getAliasedProperties()
    {
        return [
            'user_id' => 'user',
        ];
    }

    /**
     * List of properties in the class that will be  ignored by the mapping.
     *
     * @return array
     */
    public function getHideProperties()
    {
        return [

        ];
    }

    /**
     * Returns an array of properties that are used as an ID value.
     *
     * @return array
     */
    public function getIdProperties()
    {
        return ['id'];
    }

    /**
     * @return array
     */
    public function getRequiredProperties()
    {
        return [
            'details' => 'details'
        ];
    }

    /**
     * Returns a list of URLs. This urls must have placeholders
     * to be replaced with the getIdProperties() values.
     *
     * @return array
     */
    public function getUrls()
    {
        return [
            'self' => ['name' => 'item.show', 'as_id' => 'id'],
            'item' => ['name' => 'item.index'],
            'user' => ['name' => 'user.show',  'as_id' => 'user_id'],
        ];
    }

    /**
     * Returns an array containing the relationship mappings as an array.
     * Key for each relationship defined must match a property of the mapped class.
     *
     * @return array
     */
    public function getRelationships()
    {
        return [
//            'user_id' =>
        ];
    }
}