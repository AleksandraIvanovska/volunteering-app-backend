<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->increments('id');
                $table->uuid('uuid')->index()->unique();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->string('type')->nullable();
                $table->string('navigate_url')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->integer('source_id')->nullable();
                $table->string('source_table')->nullable();
                $table->integer('sender_id');

                $table->foreign('sender_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });


        }

        if (!Schema::hasTable('event_user')) {
            Schema::create('event_user', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->unsignedInteger('event_id');
                $table->boolean('is_read')->nullable()->default(false);
                $table->timestamp('read_time')->nullable();

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');


                $table->foreign('event_id')
                    ->references('id')
                    ->on('events')
                    ->onDelete('cascade');

                $table->timestamps();

                $table->unique(['user_id', 'event_id']);

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
        Schema::dropIfExists('event_user');
        Schema::dropIfExists('events');
    }
}
