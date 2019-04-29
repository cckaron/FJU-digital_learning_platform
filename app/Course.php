<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public $incrementing = false;

    protected  $fillable = ['id', 'common_courses_id','name', 'class'];

    public function teacher(){
        return $this->belongsToMany('App\Teacher', 'teacher_course', 'courses_id', 'teachers_id', 'id', 'users_id');
    }

    public function student(){
        return $this->belongsToMany('App\Student', 'student_course', 'courses_id', 'students_id', 'id', 'users_id');
    }


    public function assignment(){
        return $this->hasMany('App\Assignment', 'courses_id', 'id');
    }

    public function common_course(){
        return $this->belongsTo('App\common_course', 'common_courses_id');
    }

    public function announcement(){
        return $this->belongsToMany('App\Announcement', 'course_announcement', 'courses_id', 'announcements_id', 'id', 'id');
    }



}
