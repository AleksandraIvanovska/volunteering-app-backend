<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVolunteersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('volunteers')) {
            Schema::create('volunteers', function (Blueprint $table) {
                $table->increments('id');
                $table->uuid('uuid')->index()->unique();
                $table->integer('user_id');
                $table->string('first_name');
                $table->string('middle_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('name');
                $table->string('photo')->nullable();
                $table->string('gender')->nullable();
                $table->integer('nationality_id')->nullable();
                $table->dateTime('dob')->nullable();
                $table->string('cv')->nullable();
                $table->string('facebook')->nullable();
                $table->string('twitter')->nullable();
                $table->string('linkedIn')->nullable();
                $table->string('skype')->nullable();
                $table->string('phone_number')->nullable();
                $table->string('my_causes')->nullable();
                $table->integer('location_id')->nullable();
                $table->text('skills')->nullable();
                $table->timestamps();
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                $table->foreign('nationality_id')
                    ->references('id')
                    ->on('countries');
                $table->foreign('location_id')
                    ->references('id')
                    ->on('cities')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('volunteers');
    }
}
