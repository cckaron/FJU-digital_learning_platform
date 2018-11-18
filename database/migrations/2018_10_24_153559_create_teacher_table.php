<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeacherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {

            $table->integer('users_id')->unsigned()->primary();

            $table->string('remark');
            $table->string('status');

            $table->integer('courses_id')->unsigned()->nullable();
            $table->timestamps();

            $table->index('users_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('teachers');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }
}
