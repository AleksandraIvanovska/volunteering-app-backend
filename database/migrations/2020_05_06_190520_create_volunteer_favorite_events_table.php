<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVolunteerFavoriteEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('volunteer_favorite_events')) {
            Schema::create('volunteer_favorite_events', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('volunteer_id');
                $table->integer('event_id');
                $table->timestamps();

                $table->foreign('volunteer_id')
                    ->references('id')
                    ->on('volunteers');

                $table->foreign('event_id')
                    ->references('id')
                    ->on('volunteering_events');

                $table->unique(['volunteer_id','event_id']);
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
        Schema::dropIfExists('volunteer_favorite_events');
    }
}
