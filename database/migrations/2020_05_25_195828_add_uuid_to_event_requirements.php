<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUuidToEventRequirements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('event_requirements','uuid')) {
            Schema::table('event_requirements', function (Blueprint $table) {
                $table->uuid('uuid')->index()->unique()->after('id');
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
        Schema::table('event_requirements', function (Blueprint $table) {
            //
        });
    }
}
