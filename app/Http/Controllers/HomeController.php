<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Barryvdh\Debugbar\Facade as Debugbar;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Item::all();

        Debugbar::info($items);
        return view('display', ['items' => $items]);
    }
}
