<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVolunteerEventInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('volunteer_event_invitations')) {
            Schema::create('volunteer_event_invitations', function (Blueprint $table) {
                $table->increments('id');
                $table->uuid('uuid')->index()->unique();
                $table->integer('volunteer_id');
                $table->integer('event_id');
                $table->integer('status_id');
                $table->string('status')->nullable();
                $table->timestamps();

                $table->foreign('status_id')
                    ->references('id')
                    ->on('resources');
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
        Schema::dropIfExists('volunteer_event_invitations');
    }
}
