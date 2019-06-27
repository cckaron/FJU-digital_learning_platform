<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyForeignTaCourse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ta_course', function (Blueprint $table) {
            $table->dropForeign('ta_course_tas_id_foreign');
            $table->dropForeign('ta_course_courses_id_foreign');
            $table->dropColumn('courses_id');
            $table->dropColumn('tas_id');
        });

        Schema::table('ta_course', function (Blueprint $table) {
            $table->string('tas_id')->nullable();
            $table->integer('courses_id')->unsigned();

            $table->primary(['tas_id', 'courses_id']);
            $table->foreign('tas_id')
                ->references('users_id')
                ->on('tas')
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
