<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unsigned();
            $table->string('name');
            $table->integer('year');
            $table->integer('semester');
            $table->string('start_date');
            $table->string('end_date');
            $table->integer('status')->default(1);
            $table->timestamps();

            //need to add the 1 to many relationship for teacher_id

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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('courses');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }
}
