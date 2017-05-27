<?php

namespace App\Http\Controllers;

use App\Libs\Vimeo;
use App\Libs\YouTube;
use App\Http\Requests\CreateFormRequest;
use App\Models\Factories\ItemFactory;
use App\Models\Items\Image;
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
     * @return \Illuminate\Support\Facades\View
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

        // Attempt to create a new item from the provided URL
        $item = ItemFactory::create([
            'user_id' => $user->id,
            'details'=>['url' => $url]
        ]);

        // If a valid item type couldn't be found, don't save to the db
        if (empty($item->type)) {
            return redirect()
                ->route('item.create')
                ->withInput()
                ->withErrors(['url' => CreateFormRequest::$messages['url.invalid']]);
        }

        // If everything looks fine, redirect to home where the item should
        // have been added to the queue
        if ($item->save()) {
            return redirect()->route('home');
        }

        return redirect('item.create')->with('errors', 'Could not parse URL');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Support\Facades\View
     */
    public function show($id)
    {
        $item = Item::findOrFail($id);

        if ($item && View::exists("items.show.{$item->type}")) {
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

}
