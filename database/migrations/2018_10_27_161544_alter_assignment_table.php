<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAssignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('assignment', function (Blueprint $table) {

            $table->foreign('course_id')
                ->references('id')->on('course')
                ->onDelete('cascade');
            $table->foreign('author_id')
                ->references('users_id')->on('student')
                ->onDelete('cascade');
            $table->foreign('corrector_id')
                ->references('users_id')->on('teacher')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignment', function (Blueprint $table) {
            //
        });
    }
}
