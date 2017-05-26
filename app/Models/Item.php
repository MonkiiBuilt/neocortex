<?php

namespace App\Models;

use App\Events\ItemCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;

class Item extends Model
{
    use SoftDeletes;
    use SingleTableInheritanceTrait {
        newFromBuilder as newFromTraitBuilder; // We want to override this
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'items';

    /**
     * The column where item type is stored, used to infer single table
     * inheritance subclasses.
     *
     * @var string
     */
    protected static $singleTableTypeField = 'type';

    /**
     * Enumeration of all Item subtype classes.
     *
     * @var array
     */
    protected static $singleTableSubclasses = [
        Items\Image::class,
        Items\Video::class,
    ];

    // Minimum needed to save an item: owner, what type of item, and serialised details
    protected $fillable = [
        'user_id',
        'type',
        'details'
    ];

    // When the item is loaded, make sure details are unserialised
    protected $casts = [
        'details' => 'array',
    ];

    protected $events = [
        'created' => ItemCreated::class,
    ];

    /**
     * Get the user that owns the item.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }


    /**
     * Get the item's queue entry if it has one.
     */
    public function queueEntry()
    {
        return $this->hasOne(Queue::class);
    }


    /**
     * Fill the model with the provided attributes, and if no item type is
     * provided, try to determine it based on included attributes.
     *
     * @param  array  $attributes
     * @return $this
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function fill(array $attributes) {
        parent::fill($attributes);

        $typeField = static::$singleTableTypeField;
        if (!isset($this->$typeField)) {
            // There is something seriously wrong
        }

        // If the type attribute is not yet set, try to set it
        if (!$this->$typeField) {
            $matchedType = static::identifyItemType($attributes);
            if ($matchedType) {
                $this->$typeField = $matchedType;
            }
        }

        return $this;
    }


    /**
     * Given a URL, provide a weighted value indicating whether the URL is
     * likely to served by this Item type.
     *
     * Note: this method should only parse the URL for patterns, ItemType
     * controllers should avoid making a request to the URL. If no match is
     * found based on the URL, the system will make a HEAD request to te URL
     * and pass it to the matchHeaders method.
     *
     * @param $url
     * @return integer The 'weight' of a possible match, with 0 meaning "no match".
     */
    public static function matchByURL($url) { return 0; }


    /**
     * If no URL match is found, a HEAD request will be made to the URL so that
     * type matching can be performed on HTTP headers, for example mime type.
     *
     * @param $headers
     * @return integer The 'weight' of a possible match, with 0 meaning "no match".
     */
    public static function matchByHeaders($headers) { return 0; }


    /**
     * Attempt to identify the item type based on the available attributes.
     * @param $attributes
     * @return array
     */
    public static function identifyItemType($attributes) {
        if (isset($attributes['details']['url'])) {
            // If item type can be determined by URL, return it
            $typeFromURL = static::identifyItemTypeByURL($attributes['details']['url']);
            if ($typeFromURL) {
                return $typeFromURL;
            }
        }
    }

    /**
     * Attempt to identify the item type based on URL, if one is provided.
     * @param $url
     * @return mixed|null
     */
    public static function identifyItemTypeByURL($url) {
        $matches = [];

        \Log::debug("Identifying item type at $url");
        foreach (static::getSingleTableTypeMap() as $itemType => $itemClass) {
            // Add possible matches
//            \Log::debug("checking {$itemType::$type}");
            $weight = $itemClass::matchByUrl($url);
            if ($weight > 0) {
                $matches[$weight] = $itemType;
            }
        }

        // If any matches were found, return the highest weight
        if (count($matches)) {
            // Sort the array with the highest weight first
            krsort($matches);
            // Return the item type with the highest weight
            return array_shift($matches);
        }

        // If no match was found, make a request for the URL and try to
        // classify it based on the returned headers
        $headers = self::requestUrlHeaders($url);
        foreach (static::getSingleTableTypeMap() as $itemType => $itemClass) {
            // Add possible matches
//            \Log::debug("checking {$itemType::$type}");
            $weight = $itemClass::matchByHeaders($headers);
            if ($weight > 0) {
                $matches[$weight] = $itemType;
            }
        }

        // If matches were found based on headers, return the highest weight
        if (count($matches)) {
            // Sort the array with the highest weight first
            krsort($matches);
            // Return the item type with the highest weight
            return array_shift($matches);
        }

        // No match was found, so no type identified
        return null;
    }

    private static function requestUrlHeaders($url) {
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);
//        curl_setopt ($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

        // Only execute a HEAD request
        curl_setopt($ch, CURLOPT_HEADER, true); // header will be at output
        curl_setopt($ch, CURLOPT_NOBODY, true);

        $content = curl_exec ($ch);
        curl_close ($ch);

        // Split cURL response string into headers array
        $response = explode("\n", $content);
        $status = array_shift($response);

        // TODO throw an exception for non-200 response codes

        // Parse the response string into an array of headers => values
        $headers = static::parseHeaders($response);

        return $headers;
    }

    /**
     * Split each header line into header name and value.
     *
     * @param $response
     * @return array
     */
    private static function parseHeaders($response) {
        // Parse the remaining lines into header and value
        $headers = [];
        foreach ($response as $headerString) {
            $headerParts = explode(": ",$headerString,2);

            // Any line that doesn't have a : isn't a well formed header
            if (count($headerParts) < 2) {
                continue;
            }
            $headers[trim($headerParts[0])] = trim($headerParts[1]);
        }

        // Return status and headers
        return $headers;
    }

}
