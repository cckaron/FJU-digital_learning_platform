<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('students', function (Blueprint $table) {

            $table->string('users_id')->primary();
            $table->string('users_name')->nullable();
            $table->string('department')->nullable();
            $table->string('grade')->nullable();
            $table->string('class')->nullable();
            $table->string('remark')->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('students');
    }
}
