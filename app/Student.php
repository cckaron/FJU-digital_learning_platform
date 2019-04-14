<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['users_id', 'courses_id', 'users_name', 'department', 'grade', 'class', 'remark', 'created_at', 'updated_at'];

    public function user(){
        return $this->belongsTo('App\User', 'users_id', 'id');
    }

    public function teacher(){
        return $this->belongsToMany('App\Teacher');
    }

    public function course(){
        return $this
            ->belongsToMany('App\Course', 'student_course', 'students_id', 'courses_id', 'users_id', 'id');
    }

    public function assignment(){
        return $this->belongsToMany('App\Assignment', 'student_assignment', 'students_id', 'assignments_id', 'users_id', 'id');
    }
}
