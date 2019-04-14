<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeachingAssistant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tas', function (Blueprint $table) {
            $table->string('users_id')->primary();
            $table->string('users_name')->nullable();
            $table->string('department')->nullable();
            $table->string('grade')->nullable();
            $table->string('class')->nullable();
            $table->string('remark')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->index('users_id');

            $table->foreign('users_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('users_name')->references('name')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teaching_assistant');
    }
}
