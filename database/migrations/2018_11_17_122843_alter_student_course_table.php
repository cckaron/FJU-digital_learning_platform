<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStudentCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_course', function (Blueprint $table) {

            $table->foreign('students_id')
                ->references('users_id')->on('students')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->foreign('courses_id')
                ->references('id')->on('courses')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
