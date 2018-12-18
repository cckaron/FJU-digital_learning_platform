<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStudentAssignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_assignment', function (Blueprint $table) {

            $table->foreign('students_id')
                ->references('users_id')->on('students')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->foreign('assignments_id')
                ->references('id')->on('assignments')
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
