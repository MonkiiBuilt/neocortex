<?php

namespace App\Http\Controllers;

use App\Models\Item;

class QueueController extends Controller
{
    /**
     * Force Users to log in for all actions in this controller
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the current Queue with SoftDeleted items
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Item::withTrashed()->orderBy('id', 'DESC')->paginate(10);

        return view('queue.index', ['items' => $items]);
    }
}
