<?php

namespace App\Http\Controllers\Items;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaseItemController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function display()
    {
        return view('partials.item', ['item' => $this]);
    }

    /**
     * Show the item creation view.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('items.create');
    }


    /**
     * Save an item to the database
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        return redirect('');
    }


}
