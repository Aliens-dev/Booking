<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('wilayas') ||  Schema::hasTable('communes')) {
            return ;
        }

        /*
        Schema::create('wilayas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('arabic_name');
            $table->decimal('longitude', 9, 6);
            $table->decimal('latitude', 9, 6);
            $table->timestamps();
        });
        */

        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            $table->string('commune_name');
            $table->string('commune_arabic_name');
            $table->string('daira_name');
            $table->string('daira_arabic_name');
            $table->string('wilaya_name');
            $table->string('wilaya_arabic_name');
            $table->string('wilaya_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('communes');
        Schema::dropIfExists('wilayas');
    }
}
