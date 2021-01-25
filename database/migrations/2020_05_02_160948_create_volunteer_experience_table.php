<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVolunteerExperienceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('volunteer_experience')) {
            Schema::create('volunteer_experience', function (Blueprint $table) {
                $table->increments('id');
                $table->uuid('uuid')->index()->unique();
                $table->string('job_title')->nullable();
                $table->string('company_name')->nullable();
                $table->integer('location_id')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->integer('volunteer_id');
                $table->timestamps();

                $table->foreign('volunteer_id')
                    ->references('id')
                    ->on('volunteers')
                    ->onDelete('cascade');

                $table->foreign('location_id')
                    ->references('id')
                    ->on('cities');
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
        Schema::dropIfExists('volunteer_experience');
    }
}
