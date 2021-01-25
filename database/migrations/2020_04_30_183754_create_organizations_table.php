<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('organizations')) {
            Schema::create('organizations', function (Blueprint $table) {
                $table->increments('id');
                $table->uuid('uuid')->index()->unique();
                $table->string('name');
                $table->string('mission')->nullable();
                $table->string('description')->nullable();
                $table->integer('location_id')->nullable();
                $table->string('website')->nullable();
                $table->string('facebook')->nullable();
                $table->string('linkedIn')->nullable();
                $table->string('phone_number')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->integer('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('location_id')->references('id')->on('cities')->onDelete('cascade');
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
        Schema::dropIfExists('organizations');
    }
}
