<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnergiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('energies', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('time')->comment('unix timestamp of reading time');
            $table->unsignedInteger('production')->comment('current energy production in watts');
            $table->unsignedInteger('consumption')->comment('current energy consumption in watts');
            $table->integer('available')->comment('available energy in watts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('energies');
    }
}
