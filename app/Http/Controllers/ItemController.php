<?php

namespace App\Http\Controllers;

use App\Libs\Vimeo;
use App\Libs\YouTube;
use App\Http\Requests\CreateFormRequest;
use App\Http\Requests\CreateRandomImageFormRequest;
use App\Models\Factories\ItemFactory;
use App\Models\Queue;
use App\Models\User;
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
        // Grab the options for the Upload Image processing
        $po = \App\Models\Items\Image::$processingOptions;

        // Get the server settings for maximum upload size
        $max = ini_get('upload_max_filesize');

        return view('items.create', ['processingOptions' => $po, 'maxUploadSize' => $max]);
    }

    /**
     * Store a newly created resource in storage. Process if requested.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFormRequest $request)
    {
        // Are we uploading?
        if ($request->input('btnUpload') !== null) {
            $url = $this->handleUpload();

            // Check if processing required
            if (!empty($request->input('processing'))) {
                try {
                    $filter = 'App\Libs\ImageProcessing\\' . $request->input('processing');
                    $url = $filter::apply($url);
                } catch (\Exception $e) {
                    return redirect()
                        ->route('item.create')
                        ->withInput()
                        ->withErrors(['upload' => $e->getMessage()]);
                }
            }
        }
        else {
            $url = $request->input('url');
        }

        $user = auth()->user();

        // Attempt to create a new item from the provided URL
        $item = ItemFactory::create([
            'user_id' => $user->id,
            'details' => ['url' => $url]
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

        return redirect()
            ->route('item.create')
            ->withInput()
            ->withErrors(['url' => CreateFormRequest::$messages['url.invalid']]);
    }

    /**
     * Store a random image in storage.
     *
     * @return string
     */
    public function handleUpload()
    {
        $urlPath    = "/uploads/";
        $targetPath = public_path() . $urlPath;
        if (!is_dir($targetPath)) mkdir($targetPath, 0755, true);

        // Create unique filename
        $targetParts = pathinfo($_FILES['upload']['name']);
        $targetName  = uniqid() . "." . $targetParts['extension'];

        // Check extension
        $ext = strtolower($targetParts['extension']);
        if ($ext != "jpg" && $ext != "jpeg" && $ext != "png" && $ext != "gif") {
            return redirect()
                ->route('item.create')
                ->withInput()
                ->withErrors(['upload' => CreateFormRequest::$messages['upload.notimage']]);
        }

        // Move file into correct position
        $targetFile  = $targetPath . $targetName;
        $moveSuccess = move_uploaded_file($_FILES['upload']['tmp_name'], $targetFile);
        if (!$moveSuccess || !filesize($targetFile)) {
            return redirect()
                ->route('item.create')
                ->withInput()
                ->withErrors(['upload' => CreateFormRequest::$messages['upload.error']]);
        }

        return $urlPath . $targetName;
    }

    /**
     * Store a random image in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function randomImage(CreateRandomImageFormRequest $request)
    {
        $url = $request->input('randImgUrl');

        if(empty($url)) {
            switch(rand(0,1)) {
                case 0:
                    $url = $this->randomImageUnsplash();
                    break;
                case 1:
                    $url = $this->randomImageReddit();
                    break;
            }
        }

        $user = auth()->user();

        // Attempt to create a new item from the provided URL
        $item = ItemFactory::create([
            'user_id'  => $user->id,
            'details'  => ['url' => $url],
            'type'     => 'image',
        ]);

        // If everything looks fine, redirect to home where the item should
        // have been added to the queue
        if ($item->save()) {
            return redirect()->route('home');
        }

        return redirect()
            ->route('item.create')
            ->withInput()
            ->withErrors(['url' => CreateFormRequest::$messages['url.invalid']]);
    }

    public function randomImageUrl() {
        switch(rand(0,1)) {
            case 0:
                $url = $this->randomImageUnsplash();
                break;
            case 1:
                $url = $this->randomImageReddit();
                break;
        }
        die($url);
    }

    /**
     * Return the URL of a random image from reddit pics
     *
     * @return string
     */
    private function randomImageReddit() {
        $reddit_url = 'https://www.reddit.com/r/pics/top/.json?sort=top&t=day';
        $data = file_get_contents($reddit_url);
        $data = json_decode($data);
        $num_posts = count($data->data->children);
        $child = $data->data->children[rand(0, $num_posts - 1)];
        return $child->data->preview->images[0]->source->url;
    }

    /**
     * Return the url of a random image from unsplash.
     * We store the image locally because otherwise it will be different on
     * every request.
     *
     * @return string
     */
    private function randomImageUnsplash() {
        $remote_url = 'https://unsplash.it/1280/720?random';
        $image = file_get_contents($remote_url);
        $local_name = 'unsplash-' . time() . '.jpg';
        \Storage::disk('public')->put('unsplash_images/' . $local_name, $image);
        return '/unsplash_images/' . $local_name;
    }

    public function history()
    {

        $tableHeader = [
            [
                'title' => 'Type',
                'field' => 'items.type',
            ],
            [
                'title' => 'Created At',
                'field' => 'items.created_at',
            ],
            [
                'title' => 'Created By',
                'field' => 'users.name',
            ],
            [
                'title' => 'Resurrect count',
                'field' => 'items.resurrect_count',
            ],
            [
                'title' => 'Preview',
            ],
            [
                'title' => 'Actions',
            ],
        ];

        $sortOrder = (!empty($_GET['sort-order']) && $_GET['sort-order'] != 'desc') ? 'asc' : 'desc';
        if(!empty($_GET['sort-by']) && in_array($_GET['sort-by'], array_keys($tableHeader))) {
            $sortBy = $_GET['sort-by'];
        } else {
            $sortBy = 'items.created_at';
        }

        $query = \DB::table('items')
            ->join('queue', 'queue.item_id', '=', 'items.id')
            ->join('users', 'items.user_id', '=', 'users.id')
            ->where(function($query){
                $query->whereNull('queue.status')
                    ->orWhere('queue.status', '<>', 'active')
                    ->orWhere('queue.status', '<>', 'active');
            })
            ->where('type', '<>', 'weather')
            ->orderBy($sortBy, $sortOrder);

        $filters = '';

        // Filter by user
        if(!empty($_GET['user'])) {
            $query->where('items.user_id', (int) $_GET['user']);
            $user = User::find($_GET['user']);
            $filters = '<a class="btn btn-danger btn-sm" href="/item/history">'. $user->name . ' x</a>';
        }

        if(!empty($filters)) {
            $filters = '<p><strong>Filters</strong><br>' . $filters . '</p>';
        }

        $items = $query->paginate(20);

        // Decode json
        foreach($items as &$item) {
            $item->details = (array) json_decode($item->details);
        }

        $this->addTableHeaderLinks($tableHeader, $sortBy, $sortOrder);

        return view('items.history', [
            'tableHeader'   => $tableHeader,
            'items'         => $items,
            'filters'       => $filters,
            'pagination'    => $items->appends($_GET)->render(),
        ]);
    }

    public function resurrect($id)
    {
        $queueItem = Queue::where('item_id', $id)->first();

        if(!$queueItem) {
            $queueItem = new Queue;
            $queueItem->item_id = $id;
            $queueItem->views = 0;
        }

        $queueItem->status = 'active';
        $queueItem->created_at = time();
        $queueItem->updated_at = time();
        $queueItem->save();

        // Increment the item resurrect count
        $item = Item::find($id);
        $item->resurrect_count++;
        $item->save();

        flash('Item resurrected');
        return redirect()->route('queue');
    }

    private function addTableHeaderLinks(&$tableHeader, $sortBy, $sortOrder, $base = '/item/history') {

        $reverseOrder = $sortOrder == 'desc' ? 'asc' : 'desc';

        foreach($tableHeader as $key => &$item) {

            $link = null;
            $class = 'sort-link';

            if(empty($item['field'])) {
                // No sorting
            } elseif($item['field'] == $sortBy) { // Current sort
                $link = $base . '?sort-by=' . $item['field'] . '&sort-order=' . $reverseOrder;
                $class .= ' sort-link-' . $sortOrder;
            } else { // Other sort
                $link = $base . '?sort-by=' . $item['field'];
            }

            if($link && !empty($_GET['user'])) {
                $link .= '&user=' . (int) $_GET['user'];
            }

            if($link) {
                $item['header'] = '<a href="' . $link . '"' . ($class ? ' class="' . $class . '"' : '') . '>' . $item['title'] . '</a>';
            } else {
                $item['header'] = '<span>' . $item['title'] . '</span>';
            }

        }

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
