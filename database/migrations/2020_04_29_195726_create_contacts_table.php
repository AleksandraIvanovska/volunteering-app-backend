<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('contacts')) {
            Schema::create('contacts', function (Blueprint $table) {
                $table->increments('id');
                $table->uuid('uuid')->index()->unique();
                $table->string('first_name');
                $table->string('middle_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('name');
                $table->string('photo')->nullable();
                $table->string('phone_number')->nullable();
                $table->string('email')->nullable();
                $table->string('facebook')->nullable();
                $table->string('twitter')->nullable();
                $table->string('linkedIn')->nullable();
                $table->string('skype')->nullable();
                $table->timestamps();
                $table->integer('organization_id');

                $table->foreign('organization_id')
                    ->references('id')
                    ->on('organizations')
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
        Schema::dropIfExists('contacts');
    }
}
