<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('student', function (Blueprint $table) {

            $table->integer('users_id',0,1)->nullable();
            $table->integer('teacher_id',0,1)->nullable();
            $table->integer('course_id',0,1)->nullable();
            $table->string('name');
            $table->string('department');
            $table->string('grade');
            $table->string('class');
            $table->string('remark');
            $table->string('status');
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
        Schema::dropIfExists('students');
    }
}
