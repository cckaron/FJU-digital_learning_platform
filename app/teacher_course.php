<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class teacher_course extends Model
{

    protected $table = 'teacher_course';

    protected $fillable = ['teachers_id', 'courses_id', 'created_at', 'updated_at'];

}
