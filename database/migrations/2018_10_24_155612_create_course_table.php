<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->string('name');
            $table->integer('year');
            $table->integer('semester');
            $table->string('start_date');
            $table->string('end_date');
            $table->integer('status');
            $table->timestamps();

            //need to add the 1 to many relationship for teacher_id

            $table->index('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course');
    }
}
