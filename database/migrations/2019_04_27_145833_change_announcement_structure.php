<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAnnouncementStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //drop the old table
        Schema::dropIfExists('course_announcement');

        Schema::create('announcements', function (Blueprint $table) {

            $table->integer('id')->autoIncrement()->unsigned();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->integer('priority')->default('1'); // 0->置頂 , 1->一般
            $table->integer('status')->default('1'); // 0->not active , 1-> active, 2-> in review
            $table->timestamps();

            $table->index('id');
        });

        Schema::create('course_announcement', function (Blueprint $table) {
            $table->integer('announcements_id')->unsigned();
            $table->integer('courses_id')->unsigned();

            $table->primary(['announcements_id', 'courses_id']);
            $table->timestamps();
        });

        //alter
        Schema::table('course_announcement', function (Blueprint $table) {

            $table->foreign('announcements_id')
                ->references('id')->on('announcements')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->foreign('courses_id')
                ->references('id')->on('courses')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

        });

        //change percentage default
        Schema::table('assignments', function (Blueprint $table) {
            $table->double('percentage')->default(0)->change();
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
