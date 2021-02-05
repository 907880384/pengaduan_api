<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assigneds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complaint_id');
            $table->unsignedBigInteger('executor_id');
            $table->boolean('is_accepted')->nullable()->default(false);
            $table->longText('image')->nullable()->default(null);
            $table->longText('video')->nullable()->default(null);
            $table->longText('description')->nullable()->default(null);
            $table->dateTime('start_work')->nullable()->default(null);
            $table->dateTime('end_work')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->default(null);
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
        Schema::dropIfExists('assigneds');
    }
}
