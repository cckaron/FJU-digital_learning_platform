<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class student_teacher extends Model
{
    protected $fillable = ['teachers_id', 'students_id'];
}
