<?php

use App\Models\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queue', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->unsigned()->unique();
            $table->enum('status', [
                Queue::STATUS_ACTIVE,
                Queue::STATUS_PERMANENT,
                Queue::STATUS_RETIRED,
            ]);
            $table->integer('views')->unsigned();

            $table->timestamps();

            // Relationships
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('queue');
    }
}
