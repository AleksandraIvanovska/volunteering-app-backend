<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVolunteerEducationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('volunteer_education')) {
            Schema::create('volunteer_education', function (Blueprint $table) {
                $table->increments('id');
                $table->uuid('uuid')->index()->unique();
                $table->string('institution_name')->nullable();
                $table->string('degree_name')->nullable();
                $table->string('major')->nullable();
                $table->date('start_date')->nullable();
                $table->date('graduation_date')->nullable();
                $table->integer('volunteer_id');
                $table->timestamps();

                $table->foreign('volunteer_id')
                    ->references('id')
                    ->on('volunteers')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('volunteer_education');
    }
}
