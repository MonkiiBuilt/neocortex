<?php

namespace App\Console\Commands;

use App\Models\Factories\ItemFactory;
use Illuminate\Console\Command;

class AddItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:url {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new Item to the queue by providing a URL';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Fetch plausible user
        $owner = User::all()->first();

        // Create an item from the URL and try to save it
        $item = ItemFactory::create([
            'user_id' => $owner->id,
            'details' => ['url' => $this->argument('url')]
        ]);
        $item->save();
    }
}
