<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeachingAssistantCourse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ta_course', function (Blueprint $table) {
            $table->string('tas_id')->nullable();
            $table->integer('courses_id')->unsigned();
            $table->timestamps();

            $table->primary(['tas_id', 'courses_id']);

            $table->foreign('tas_id')
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
        Schema::dropIfExists('teaching_assistant_course');
    }
}
