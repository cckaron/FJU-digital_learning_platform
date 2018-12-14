<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommonCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('common_courses', function (Blueprint $table) {

            $table->integer('id',1)->unsigned();
            $table->string('name');
            $table->integer('year');
            $table->integer('semester');
            $table->string('start_date');
            $table->string('end_date');
            $table->integer('status')->default(1);

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
        Schema::dropIfExists('files');
    }
}
