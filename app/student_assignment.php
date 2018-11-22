<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class student_assignment extends Model
{
    protected $fillable = ['fileURL', 'students_id', 'score', 'status'];

}
