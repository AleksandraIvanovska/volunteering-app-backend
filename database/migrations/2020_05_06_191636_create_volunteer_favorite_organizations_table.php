<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVolunteerFavoriteOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('volunteer_favorite_organizations')) {
            Schema::create('volunteer_favorite_organizations', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('volunteer_id');
                $table->integer('organization_id');
                $table->timestamps();

                $table->foreign('volunteer_id')
                    ->references('id')
                    ->on('volunteers');
                $table->foreign('organization_id')
                    ->references('id')
                    ->on('organizations');
                $table->unique(['volunteer_id','organization_id']);

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
        Schema::dropIfExists('volunteer_favorite_organizations');
    }
}
