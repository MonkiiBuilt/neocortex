<?php

namespace App\Http\Controllers;

use App\Libs\Vimeo;
use App\Libs\YouTube;
use App\Http\Requests\CreateFormRequest;
use App\Http\Requests\CreateRandomImageFormRequest;
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
        // Grab the options for the Upload Image processing
        $po = \App\Models\Items\Image::$processingOptions;

        return view('items.create', ['processingOptions' => $po]);
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
        if (isset($_POST['btnUpload'])) {
            $url = $this->handleUpload();

            // Check if processing required
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
        // Check if file upladoed
        if (empty($_FILES)) {
            return redirect()
                ->route('item.create')
                ->withInput()
                ->withErrors(['upload' => CreateFormRequest::$messages['upload.toobig']]);
        }

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
