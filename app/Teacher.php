<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['users_id', 'remark', 'users_name', 'status'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function course(){
        return $this->belongsToMany('App\Course', 'teacher_course', 'teachers_id', 'courses_id', 'users_id', 'id');
    }

    public function student(){
        return $this->belongsToMany('App\Student');
    }

    public function assignment(){
        return $this->hasMany('App\Assignment');
    }
}
