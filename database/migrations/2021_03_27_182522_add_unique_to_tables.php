<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('volunteer_event_invitations', function (Blueprint $table) {
            $table->unique(['volunteer_id', 'event_id']);
        });

        Schema::table('volunteer_event_attendance', function (Blueprint $table) {
            $table->unique(['volunteer_id', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('volunteer_event_invitations', function (Blueprint $table) {
            //
        });
    }
}
