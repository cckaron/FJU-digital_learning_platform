<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTeacherCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teacher_course', function (Blueprint $table) {

            $table->foreign('teachers_id')
                ->references('users_id')->on('teachers')
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
