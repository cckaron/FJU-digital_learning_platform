<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTextToLongtext extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //NOT WORK!!!!!!!!!!!!!!!!!!!!!!!
        Schema::table('assignments', function (Blueprint $table) {
            $table->longText('content')->change();
        });

        Schema::table('course_announcement', function (Blueprint $table) {
            $table->longText('content')->change();
        });

        Schema::table('system_announcement', function (Blueprint $table) {
            $table->longText('content')->change();
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
