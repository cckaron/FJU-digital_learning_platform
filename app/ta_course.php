<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ta_course extends Model
{
    protected $table = 'ta_course';

    protected $fillable = ['tas_id', 'courses_id', 'created_at', 'updated_at'];

}
