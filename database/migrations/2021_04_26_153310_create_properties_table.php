<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('state');
            $table->string('city');
            $table->string('street');
            $table->string('description');
            $table->double('price');
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->integer('beds');
            $table->integer('rooms');
            $table->string('status')->default('available');
            $table->string('video')->default('');
            $table->double('long')->default(0);
            $table->double('lat')->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('type_of_place_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('type_id')->references('id')->on('property_types')->cascadeOnDelete();
            $table->foreign('type_of_place_id')->references('id')->on('type_of_places')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
}
