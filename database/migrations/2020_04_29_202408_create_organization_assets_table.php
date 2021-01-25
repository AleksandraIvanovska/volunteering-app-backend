<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('organization_asset')) {
            Schema::create('organization_asset', function (Blueprint $table) {
                $table->increments('id');
                $table->uuid('uuid')->index()->unique();
                $table->integer('organization_id');
                $table->integer('asset_id');
                $table->timestamps();

                $table->foreign('organization_id')
                    ->references('id')
                    ->on('organizations')
                    ->onDelete('cascade');

                $table->foreign('asset_id')
                    ->references('id')
                    ->on('assets')
                    ->onDelete('cascade');

                $table->unique(['organization_id','asset_id']);
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
        Schema::dropIfExists('organization_asset');
    }
}
