<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('assets')) {
            Schema::create('assets', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->nullable();
                $table->uuid('uuid')->index()->unique();
                $table->string('type',50)->nullable();
                $table->string('path',50)->nullable();
                $table->string('asset_name',50)->nullable();
                $table->string('mime',50)->nullable();
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('assets');
    }
}
