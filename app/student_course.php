<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class student_course extends Model
{
    protected $table = 'student_course';

    protected $fillable = ['students_id', 'courses_id', 'created_at', 'updated_at'];

}
