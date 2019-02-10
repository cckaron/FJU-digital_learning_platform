<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected  $fillable = ['id', 'common_courses_id', 'name', 'class'];

    public function teacher(){
        return $this->hasMany('App\Teacher');
    }

    public function student(){
        return $this->belongsToMany('App\Student');
    }


    public function assignment(){
        return $this->hasMany('App\Assignment');
    }

    public function common_course(){
        return $this->belongsTo('App\common_course', 'common_courses_id');
    }

}
