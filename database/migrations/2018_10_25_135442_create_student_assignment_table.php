<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentAssignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_assignment', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('students_id')->nullable();
            $table->integer('assignments_id')->unsigned();
            $table->double('score')->nullable();
            $table->text('remark')->nullable();
            $table->text('comment')->nullable();
            $table->integer('status')->default(1); //1->未繳交; 2->已繳交; 3->已批改; 4->開放補繳; 5->已補繳; 6->開放重繳; 7->已重繳
            $table->timestamps();

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
        Schema::dropIfExists('student_assignment');
    }
}
