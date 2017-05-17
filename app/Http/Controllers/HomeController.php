<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
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

        // No items in the database, possibly a new installation?
        if (!count($items)) {
            $users = User::all();

            // If no items, and no users are present, we can assume a fresh
            // install, redirect the user to the registration page
            if (!count($users)) {
                return redirect()
                    ->route('register');

                // TODO add message support to base template
                //    ->with('message', 'Welcome to neocortex! Please start by creating a user.');
            }
        }

        return view('display', ['items' => $items]);
    }
}
