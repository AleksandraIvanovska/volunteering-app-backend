<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('event_location')) {
            Schema::create('event_location', function (Blueprint $table) {
                $table->increments('id');
                $table->uuid('uuid')->index()->unique();
                $table->integer('event_id');
                $table->integer('location_id');
                $table->string('address');
                $table->boolean('show_map')->nullable();
                $table->decimal('longitude')->nullable();
                $table->decimal('latitude')->nullable();
                $table->string('postal_code')->nullable();
                $table->timestamps();

                $table->foreign('event_id')
                    ->references('id')
                    ->on('VolunteeringEvents');

                $table->foreign('location_id')
                    ->references('id')
                    ->on('cities');
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
        Schema::dropIfExists('event_location');
    }
}
