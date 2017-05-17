<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\QueueSchema;
use App\Models\Item;
use App\Models\Queue;
use App\Models\User;
use App\Models\Api\ItemSchema;
use App\Models\Api\UserSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Encoder\EncoderOptions;


class JsonApiController extends Controller
{
    protected $modelSchemaMappings = [
        Item::class => ItemSchema::class,
        Queue::class => QueueSchema::class,
        User::class => UserSchema::class,
    ];

    protected $encoderOptions;
    protected $defaultEncoderOptions = [
        'options' => JSON_PRETTY_PRINT,
        'urlPrefix' => '/api',
    ];

    public function __construct()
    {
        $this->encoderOptions = new EncoderOptions(
            $this->defaultEncoderOptions['options'],
            $this->defaultEncoderOptions['urlPrefix']
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = Model::findOrFail($id);

        $encoder = Encoder::instance($this->modelSchemaMappings, $this->encoderOptions);
        $encodedData = $encoder->encodeData($model);

        return response($encoder->encodeData($encodedData))
            ->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
