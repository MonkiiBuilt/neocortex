<?php

namespace App\Http\Controllers;

use App\Libs\YouTube;
use App\Http\Requests\CreateFormRequest;
use Illuminate\Support\Facades\View;

use App\Http\Requests;
use App\Models\Item;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->route('home');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFormRequest $request)
    {
        $url = $request->input('url');
        $user = auth()->user();

        list($type, $details) = $this->parseItemUrl($url);

        if (empty($type) || empty($details)) {
            return redirect()->route('item.create')->withInput()->withErrors(['url' => CreateFormRequest::$messages['url.invalid']]);
        }

        $item = new Item([
            'user_id' => $user->id,
            'type' => $type,
            'details' => $details,
        ]);

        if ($item->save()) {
            return redirect()->route('item.show', ['item' => $item]);
        }

        return redirect('item.create')->with('errors', 'Could not parse URL');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Item::findOrFail($id);

        if (View::exists("items.show.{$item->type}")) {
            return view("items.show.{$item->type}", ['item' => $item]);
        }

        return view('errors.503');
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
    public function update(CreateFormRequest $request, $id)
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

    /**
     * @param $url
     * @return array
     */
    private function parseItemUrl($url) {
        // Parse the URL into components
        $url_domain = parse_url($url, PHP_URL_HOST);
        $url_path   = parse_url($url, PHP_URL_PATH);

        $type = $this->getItemTypeFromDomain($url_domain);

        if (!$type) {
            $type = $this->getItemTypeFromPath($url_path);
        }

        if ($type == "youtube") {
            $data = YouTube::getDataFromUrl($url);
        }
        else {
            $data = [
                'url' => $url
            ];
        }

        // Return a tuple with type and details specific to that type
        return [
            $type,
            $data
        ];
    }

    /**
     * @param $domain
     * @return string
     */
    private function getItemTypeFromDomain($domain) {
        switch ($domain) {
            case 'www.youtube.com':
                return 'youtube';

            case 'vimeo.com':
                return 'vimeo';

            case 'giphy.com':
            case 'imgur.com':
            case 'i.imgur.com':
                return 'image';
        }

        return null;
    }

    private function getItemTypeFromPath($path) {
        if (preg_match('/^.*\.(jpg|jpeg|png|gif)$/i', $path)) {
            return "image";
        }

        if (preg_match('/^.*\.(mp4|gifv)$/i', $path)) {
            return "video";
        }
    }

    // http://www.gifbin.com/bin/102013/1383326970_dog_in_space.gif
    // http://i.imgur.com/59D3ja0.gifv
}
