<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('event_asset')){
            Schema::create('event_asset', function (Blueprint $table) {
                $table->increments('id');
                $table->uuid('uuid')->index()->unique();
                $table->integer('event_id');
                $table->integer('asset_id');
                $table->timestamps();

                $table->foreign('event_id')
                    ->references('id')
                    ->on('volunteering_events')
                    ->onDelete('cascade');

                $table->foreign('asset_id')
                    ->references('id')
                    ->on('assets')
                    ->onDelete('cascade');

                $table->unique(['event_id','asset_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_asset');
    }
}
