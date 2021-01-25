<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVolunteeringEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('volunteering_events')) {
            Schema::create('volunteering_events', function (Blueprint $table) {
                $table->increments('id');
                $table->uuid('uuid')->index()->unique();
                $table->string('title');
                $table->text('description');
                $table->integer('organization_id')->nullable();
                $table->integer('category_id')->nullable();
                $table->boolean('is_virtual')->nullable();
                $table->integer('location_id')->nullable();
                $table->string('address')->nullable();
                $table->boolean('ongoing')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->decimal('estimated_hours')->nullable();
                $table->decimal('average_hours_per_day')->nullable();
                $table->integer('duration_id')->nullable();
                $table->dateTime('deadline')->nullable();
                $table->integer('expired_id')->nullable();
                $table->integer('status_id')->nullable();
                $table->integer('volunteers_needed')->nullable();
                $table->integer('spaces_available')->nullable();
                $table->integer('great_for_id')->nullable();
                $table->integer('group_size')->nullable();
                $table->text('sleeping')->nullable();
                $table->text('food')->nullable();
                $table->text('transport')->nullable();
                $table->text('benefits')->nullable();
                $table->text('skills_needed')->nullable();
                $table->text('tags')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('organization_id')
                    ->references('id')
                    ->on('organizations');

                $table->foreign('category_id')
                    ->references('id')
                    ->on('categories');

                $table->foreign('location_id')
                    ->references('id')
                    ->on('cities');

                $table->foreign('duration_id')
                    ->references('id')
                    ->on('resources');

                $table->foreign('expired_id')
                    ->references('id')
                    ->on('resources');

                $table->foreign('status_id')
                    ->references('id')
                    ->on('resources');

                $table->foreign('great_for_id')
                    ->references('id')
                    ->on('resources');

                $table->foreign('group_size')
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
        Schema::dropIfExists('volunteering_events');
    }
}
