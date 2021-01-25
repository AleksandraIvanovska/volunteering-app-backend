<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveLocationFromEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('volunteering_events', 'location_id')) {
            Schema::table('volunteering_events', function (Blueprint $table) {
                $table->dropColumn('location_id');
                $table->dropColumn('address');
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
        Schema::table('volunteering_events', function (Blueprint $table) {
            //
        });
    }
}
