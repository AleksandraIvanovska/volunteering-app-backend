<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldsToText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_requirements', function (Blueprint $table) {
            $table->text('languages')->change();
            $table->text('other')->change();
        });

        Schema::table('volunteering_events', function (Blueprint $table) {
            $table->text('skills_needed')->change();
        });

        Schema::table('volunteers', function (Blueprint $table) {
            $table->text('skills')->change();
            $table->text('my_causes')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('text', function (Blueprint $table) {
            //
        });
    }
}
