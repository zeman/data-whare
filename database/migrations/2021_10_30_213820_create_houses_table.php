<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->string('solar_type')->nullable()->default('enphase');
            $table->string('solar_ip')->nullable();
            $table->integer('watts_start')->default(1000);
            $table->integer('watts_below')->default(500);
            $table->integer('watts_buffer')->default(5);
            $table->integer('watts_stop')->default(-1000);
            $table->integer('amps_min')->default(1);
            $table->integer('amps_max')->default(32);
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
        Schema::dropIfExists('houses');
    }
}
