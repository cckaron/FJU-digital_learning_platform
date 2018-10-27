<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('student', function (Blueprint $table) {

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('teacher_id')->references('user_id')->on('teacher')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('course')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student', function (Blueprint $table) {
            //
        });
    }
}
