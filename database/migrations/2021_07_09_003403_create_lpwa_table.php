<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLpwaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lpwa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('dir');
            $table->string('wind');
            $table->string('gust');
            $table->string('hm');
            $table->string('tmp');
            $table->string('light');
            $table->string('pressure');
            $table->string('rain');
            $table->string('uv');
            $table->string('host');
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lpwa');
    }
}
