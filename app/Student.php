<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['users_id', 'courses_id', 'users_name', 'department', 'grade', 'class'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function teacher(){
        return $this->belongsToMany('App\Teacher');
    }

    public function course(){
        return $this
            ->belongsToMany('App\Course', 'student_course', 'students_id', 'courses_id', 'users_id', 'id');
    }

    public function assignment(){
        return $this->hasMany('App\Assignment');
    }
}
