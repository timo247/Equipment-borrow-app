<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_user', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('equipment_id');
            $table->string('type');
            $table->dateTime('start');
            $table->dateTime('end')->nullable();
            $table->dateTime('start_validation')->nullable();
            $table->dateTime('end_validation')->nullable();
            $table->integer('start_validation_user_id')->nullable();
            $table->integer('end_validation_user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('equipment_id')->references('id')->on('equipments')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('start_validation_user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('start_validation_user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->unique(["id", "user_id", "equipment_id"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment_user');
    }
};
