<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class teacher_assignment extends Model
{
    public $incrementing = false;
    protected $fillable = ['teachers_id', 'assignments_id', 'created_at', 'updated_at'];

}
