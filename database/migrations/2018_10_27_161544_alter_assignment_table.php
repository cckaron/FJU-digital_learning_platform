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

        Schema::table('assignments', function (Blueprint $table) {

            $table->foreign('courses_id')
                ->references('id')->on('courses')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->foreign('author_id')
                ->references('users_id')->on('students')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->foreign('corrector_id')
                ->references('users_id')->on('teachers')
                ->onUpdate('CASCADE')
                ->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignments', function (Blueprint $table) {
            //
        });
    }
}
