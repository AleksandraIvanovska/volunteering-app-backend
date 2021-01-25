<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVolunteerLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('volunteer_languages')) {
            Schema::create('volunteer_languages', function (Blueprint $table) {
                $table->increments('id');
                $table->uuid('uuid')->index()->unique();
                $table->integer('volunteer_id');
                $table->integer('language_id');
                $table->integer('level_id')->nullable();
                $table->timestamps();

                $table->foreign('volunteer_id')
                    ->references('id')
                    ->on('volunteers')
                    ->onDelete('cascade');

                $table->foreign('language_id')
                    ->references('id')
                    ->on('languages');

                $table->foreign('level_id')
                    ->references('id')
                    ->on('language_level');

                $table->unique(['volunteer_id','language_id','level_id']);
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
        Schema::dropIfExists('volunteer_languages');
    }
}
