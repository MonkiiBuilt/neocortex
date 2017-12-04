<?php

namespace App\Http\Controllers;

use App\Models\Queue;
//use Illuminate\Support\Facades\Redirect;

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
        $entries = Queue::allActive($columns = ['*'], $include_permanent = FALSE);

        $remaining_image_count = 0;
        foreach($entries as $entry) {
            if($entry->item->type == 'image') {
                $remaining_image_count++;
            }
        }

        return view('queue.index', ['entries' => $entries, 'remaining_image_count' => $remaining_image_count]);
    }

    public function destroy($id) {
      $entry = Queue::findOrFail($id);

      $entry->retire();
      $entry->save();

      flash('Item deleted');
      return redirect()->route('queue');

    }
}
