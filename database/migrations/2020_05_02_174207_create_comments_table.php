<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('comments')) {
            Schema::create('comments', function (Blueprint $table) {
                $table->increments('id');
                $table->uuid('uuid')->index()->unique();
                $table->text('description');
                $table->integer('creator_id');
                $table->integer('user_id');
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('creator_id')
                    ->references('id')
                    ->on('users');

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users');


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
        Schema::dropIfExists('comments');
    }
}
