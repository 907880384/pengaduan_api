<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            // $table->text('title')->nullable()->default(null);
            $table->longText('messages')->nullable()->default(null);
            // $table->boolean('is_urgent')->nullable()->default(false);
            $table->boolean('is_finished')->nullable()->default(false);
            $table->boolean('is_assigned')->nullable()->default(false);
            $table->unsignedBigInteger('sender_id')->nullable()->default(null);
            $table->unsignedBigInteger('type_id')->nullable()->default(null);
            $table->dateTime('finished_at')->nullable()->default(null);
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
        Schema::dropIfExists('complaints');
    }
}
