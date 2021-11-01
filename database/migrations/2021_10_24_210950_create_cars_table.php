<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('name of car');
            $table->boolean('charging')->default(false)->comment('is the car charging');
            $table->integer('amps')->nullable()->comment('amps that car is charging at');
            $table->integer('soc')->nullable()->comment('current state of charge percentage');
            $table->string('teslafi_api_token')->nullable()->comment('api token from teslafi');
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
        Schema::dropIfExists('cars');
    }
}
