<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseAnnouncement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_announcement', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('courses_id')->unsigned()->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->integer('priority')->default('1'); // 0->置頂 , 1->一般
            $table->integer('status')->default('1'); // 0->not active , 1-> active, 2-> in review
            $table->timestamps();

            $table->foreign('courses_id')->references('id')->on('courses')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_announcement');
    }
}
