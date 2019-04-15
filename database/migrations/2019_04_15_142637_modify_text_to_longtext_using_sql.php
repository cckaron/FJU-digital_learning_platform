<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTextToLongtextUsingSql extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('longtext_using_sql', function (Blueprint $table) {
            DB::statement('ALTER TABLE assignments MODIFY content  LONGTEXT;');
            DB::statement('ALTER TABLE course_announcement MODIFY content  LONGTEXT;');
            DB::statement('ALTER TABLE system_announcement MODIFY content  LONGTEXT;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('longtext_using_sql', function (Blueprint $table) {
            //
        });
    }
}
