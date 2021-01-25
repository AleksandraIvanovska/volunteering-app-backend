<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUuidToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        if (!Schema::hasColumn('uuid','volunteer_favorite_organizations')){
//            Schema::table('volunteer_favorite_organizations', function (Blueprint $table) {
//                $table->uuid('uuid')->index()->unique()->after('id');
//            });
//        }
//
//        if (!Schema::hasColumn('uuid','volunteer_favorite_events')){
//            Schema::table('volunteer_favorite_events', function (Blueprint $table) {
//                $table->uuid('uuid')->index()->unique()->after('id');
//            });
//        }
//
//        if (!Schema::hasColumn('uuid','volunteer_event_invitations')){
//            Schema::table('volunteer_event_invitations', function (Blueprint $table) {
//                $table->uuid('uuid')->index()->unique()->after('id');
//            });
//        }

        if (!Schema::hasColumn('uuid','volunteer_event_attendance')){
            Schema::table('volunteer_event_attendance', function (Blueprint $table) {
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
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
}
