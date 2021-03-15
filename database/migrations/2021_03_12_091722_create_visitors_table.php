<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->string('no_identitas')->nullable();
            $table->unsignedBigInteger('tipe_identitas');
            $table->string('no_hp')->nullable();
            $table->longText('tujuan')->nullable();
            $table->longText('keterangan')->nullable();
            $table->boolean('selesai')->nullable()->default(false);
            $table->dateTime('time_masuk')->nullable()->default(null);
            $table->dateTime('time_keluar')->nullable()->default(null);
            $table->unsignedBigInteger('user_id')->nullable();
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
        Schema::dropIfExists('visitors');
    }
}
