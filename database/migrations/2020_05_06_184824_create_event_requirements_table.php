<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('event_requirements')) {
            Schema::create('event_requirements', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('event_id');
                $table->string('driving_license')->nullable();
                $table->integer('minimum_age')->nullable();
                $table->string('languages')->nullable();
                $table->string('orientation')->nullable();
                $table->boolean('background_check')->nullable();
                $table->string('other')->nullable();
                $table->timestamps();

                $table->foreign('event_id')
                    ->references('id')
                    ->on('volunteering_events');
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
        Schema::dropIfExists('event_requirements');
    }
}
